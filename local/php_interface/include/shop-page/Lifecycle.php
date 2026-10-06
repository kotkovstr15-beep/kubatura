<?php
namespace Kubatura\ShopPage;

/** Persistent, restartable jobs. No mass update in the admin request. */
final class Lifecycle
{
    private static $internal=false;
    public static function enabled() { return Service::config('cascade_enabled') && Service::config('redirects_ready'); }
    private static function db() { return \Bitrix\Main\Application::getConnection(); }
    public static function elementChanged(&$fields)
    {
        if(self::$internal || (isset($fields['RESULT']) && !$fields['RESULT'])) return;
        $id=isset($fields['ID'])?(int)$fields['ID']:0; if(!$id) return;
        $row=\CIBlockElement::GetList(array(),array('ID'=>$id),false,false,array('ID','IBLOCK_ID'))->Fetch();
        if(!$row) return;
        if((int)$row['IBLOCK_ID']===(int)Service::config('shops')) {
            \CIBlock::clearIblockTagCache(Service::config('catalog'));
            \CIBlock::clearIblockTagCache(Service::config('shops'));
            if(self::enabled()) self::enqueueShop($id);
        }
        elseif((int)$row['IBLOCK_ID']===(int)Service::config('catalog')) {
            if(!self::enabled()) return;
            // Explicit manual deactivation must not be undone on reopening.
            if(isset($fields['ACTIVE']) && $fields['ACTIVE']==='N') self::db()->queryExecute('DELETE FROM b_kubatura_shop_disabled WHERE PRODUCT_ID='.$id);
            self::enqueueProduct($id);
        }
    }
    public static function propertiesChanged($id,$iblockId=0)
    {
        if(self::$internal || !self::enabled()) return;
        if((int)$iblockId===(int)Service::config('catalog')) self::enqueueProduct((int)$id);
    }
    public static function enqueueShop($id)
    {
        $id=(int)$id;
        self::db()->queryExecute('INSERT INTO b_kubatura_shop_jobs (SHOP_ID,CURSOR_ID,VERSION) VALUES ('.$id.',0,1) ON DUPLICATE KEY UPDATE CURSOR_ID=0,VERSION=VERSION+1');
    }
    private static function enqueueProduct($id)
    {
        $id=(int)$id;
        self::db()->queryExecute('INSERT INTO b_kubatura_shop_product_jobs (PRODUCT_ID,VERSION) VALUES ('.$id.',1) ON DUPLICATE KEY UPDATE VERSION=VERSION+1');
    }
    public static function processProduct($id,$dryRun=false)
    {
        $id=(int)$id; $db=self::db();
        $rs=\CIBlockElement::GetList(array(),array('ID'=>$id,'IBLOCK_ID'=>Service::config('catalog')),false,false,array('ID','IBLOCK_ID','CODE','ACTIVE'));
        if(!$ob=$rs->GetNextElement(false,false)) {
            if(!$dryRun) $db->queryExecute('DELETE FROM b_kubatura_shop_disabled WHERE PRODUCT_ID='.$id);
            return 'missing';
        }
        $row=$ob->GetFields(); $p=$ob->GetProperties();
        $shops=Service::listValue(isset($p['SHOP']['VALUE'])?$p['SHOP']['VALUE']:null);
        $active=$shops ? Service::count(array('IBLOCK_ID'=>Service::config('shops'),'ID'=>$shops,'ACTIVE'=>'Y')) : 1;
        // Multiple sellers: keep product active while any linked seller is active.
        $marker=$db->query('SELECT PRODUCT_ID FROM b_kubatura_shop_disabled WHERE PRODUCT_ID='.$id)->fetch();
        $target=!$active && $row['ACTIVE']==='Y'?'N':($active && $marker?'Y':null);
        if($target===null) return 'unchanged';
        if($dryRun) return $target==='N'?'disable':'restore';
        // Ledger is persisted before mutation. A failed update is retried safely.
        if($target==='N') $db->queryExecute('INSERT IGNORE INTO b_kubatura_shop_disabled (PRODUCT_ID) VALUES ('.$id.')');
        self::$internal=true;
        try {
            $element=new \CIBlockElement();
            if(!$element->Update($id,array('ACTIVE'=>$target,'CODE'=>$row['CODE']))) throw new \RuntimeException($element->LAST_ERROR);
            if($target==='Y') $db->queryExecute('DELETE FROM b_kubatura_shop_disabled WHERE PRODUCT_ID='.$id);
            if(class_exists('\\Bitrix\\Iblock\\PropertyIndex\\Manager')) \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(Service::config('catalog'),$id);
            \CIBlock::clearIblockTagCache(Service::config('catalog'));
            \CIBlock::clearIblockTagCache(Service::config('shops'));
            \CEventLog::Add(array('SEVERITY'=>'INFO','AUDIT_TYPE_ID'=>'KUBATURA_SHOP_ACTIVITY','MODULE_ID'=>'iblock','ITEM_ID'=>$id,'DESCRIPTION'=>'ACTIVE='.$target));
        } finally { self::$internal=false; }
        return $target==='N'?'disabled':'restored';
    }
    public static function run()
    {
        if(!self::enabled()) return 'Kubatura\\ShopPage\\Lifecycle::agent();';
        \Bitrix\Main\Loader::includeModule('iblock'); $db=self::db();
        $lock=$db->query("SELECT GET_LOCK('kubatura_shop_page_worker',0) AS ACQUIRED")->fetch();
        if(empty($lock['ACQUIRED'])) return 'Kubatura\\ShopPage\\Lifecycle::agent();';
        try {
            $limit=max(1,min(500,(int)Service::config('batch_size')));
            $job=$db->query('SELECT * FROM b_kubatura_shop_jobs ORDER BY SHOP_ID LIMIT 1')->fetch();
            if($job) {
                $filter=array('IBLOCK_ID'=>Service::config('catalog'),'PROPERTY_SHOP'=>(int)$job['SHOP_ID'],'>ID'=>(int)$job['CURSOR_ID']);
                $rs=\CIBlockElement::GetList(array('ID'=>'ASC'),$filter,false,array('nTopCount'=>$limit),array('ID'));
                $cursor=(int)$job['CURSOR_ID']; $n=0;
                while($r=$rs->Fetch()) { self::enqueueProduct($r['ID']); $cursor=(int)$r['ID']; $n++; }
                $where='SHOP_ID='.(int)$job['SHOP_ID'].' AND VERSION='.(int)$job['VERSION'];
                if($n<$limit) $db->queryExecute('DELETE FROM b_kubatura_shop_jobs WHERE '.$where);
                else $db->queryExecute('UPDATE b_kubatura_shop_jobs SET CURSOR_ID='.$cursor.' WHERE '.$where);
            }
            $rs=$db->query('SELECT PRODUCT_ID,VERSION FROM b_kubatura_shop_product_jobs ORDER BY PRODUCT_ID LIMIT '.$limit);
            while($r=$rs->fetch()) {
                self::processProduct($r['PRODUCT_ID']);
                $db->queryExecute('DELETE FROM b_kubatura_shop_product_jobs WHERE PRODUCT_ID='.(int)$r['PRODUCT_ID'].' AND VERSION='.(int)$r['VERSION']);
            }
        } finally { $db->query("SELECT RELEASE_LOCK('kubatura_shop_page_worker')"); }
        return 'Kubatura\\ShopPage\\Lifecycle::agent();';
    }
    public static function agent()
    {
        try { return self::run(); }
        catch(\Throwable $e) { \CEventLog::Add(array('SEVERITY'=>'ERROR','AUDIT_TYPE_ID'=>'KUBATURA_SHOP_ACTIVITY','MODULE_ID'=>'iblock','DESCRIPTION'=>$e->getMessage())); return 'Kubatura\\ShopPage\\Lifecycle::agent();'; }
    }
}
