<?php
// CLI only: php local/tools/shop-page.php diagnose|install|reindex|dry-run SHOP_ID|enqueue SHOP_ID|work
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$_SERVER['DOCUMENT_ROOT']=realpath(__DIR__.'/../..');
$_SERVER['REQUEST_URI']='/local/tools/shop-page.php';
$_SERVER['REQUEST_METHOD']='CLI';
define('NO_KEEP_STATISTIC',true); define('NOT_CHECK_PERMISSIONS',true);
require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';
\Bitrix\Main\Loader::includeModule('iblock');
use Kubatura\ShopPage\Service as S;
use Kubatura\ShopPage\Lifecycle as L;
$command=isset($argv[1])?$argv[1]:'diagnose';
try {
    if($command==='diagnose') {
        $report=array('php'=>PHP_VERSION,'config'=>S::config(),'hours'=>S::detectProperty(S::config('shops'),'hours_property',array('часы работы','режим работы','время работы','часы работы салона')),'recommend'=>S::detectProperty(S::config('catalog'),'recommend_property',array('рекомендуем')),'sale'=>S::saleSource(),'properties'=>array(),'unmapped_categories'=>array(),'meta_length_exceptions'=>array());
        foreach(array(S::config('shops'),S::config('catalog')) as $iblock) foreach(S::properties($iblock) as $p) $report['properties'][$iblock][]=array_intersect_key($p,array_flip(array('ID','NAME','CODE','PROPERTY_TYPE','USER_TYPE','MULTIPLE','LINK_IBLOCK_ID')));
        $rs=CIBlockElement::GetList(array('ID'=>'ASC'),array('IBLOCK_ID'=>S::config('shops'),'ACTIVE'=>'Y'),false,false,array('ID','CODE'));
        while($r=$rs->Fetch()) {
            $shop=S::shop($r['CODE']); if(!$shop) continue;
            $categories=S::categories($shop); $best=''; $max=-1;
            foreach($categories as $c) {
                if(!$c['sections']) $report['unmapped_categories'][]=array('shop'=>$r['CODE'],'enum_id'=>$c['id'],'name'=>$c['name']);
                $count=$c['sections']?S::count(S::productFilter($shop['ID'],$c['sections'])):0;
                if($count>$max) { $best=$c['name']; $max=$count; }
            }
            $meta=S::meta($shop,$best); $length=mb_strlen($meta['description']);
            if($length<150 || $length>180) $report['meta_length_exceptions'][]=array('shop'=>$r['CODE'],'length'=>$length,'description'=>$meta['description']);
        }
        echo json_encode($report,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES).PHP_EOL;
    } elseif($command==='install') {
        $definitions=array(
            'DESC_MAIN'=>array('Общее описание','Что за бренд, чем занимается, сколько лет на рынке, чем салон отличается. 400–700 знаков, без заголовка.'),
            'DESC_ASSORT'=>array('Что есть в салоне','Товарные группы списком, а не отдельные позиции. Без цен и характеристик. 400–900 знаков.'),
            'DESC_HELP'=>array('Чем помогают','Услуги салона: подбор, замер, расчёт, доставка, монтаж, гарантия. 300–600 знаков.')
        );
        foreach($definitions as $code=>$d) {
            $existing=CIBlockProperty::GetList(array(),array('IBLOCK_ID'=>S::config('shops'),'CODE'=>$code))->Fetch();
            if($existing) {
                if($existing['PROPERTY_TYPE']!=='S' || $existing['USER_TYPE']!=='HTML' || $existing['MULTIPLE']!=='N') throw new RuntimeException('Incompatible existing property '.$code);
                echo 'Exists: '.$code.PHP_EOL; continue;
            }
            $p=new CIBlockProperty();
            if(!$p->Add(array('IBLOCK_ID'=>S::config('shops'),'CODE'=>$code,'NAME'=>$d[0],'HINT'=>$d[1],'ACTIVE'=>'Y','PROPERTY_TYPE'=>'S','USER_TYPE'=>'HTML','MULTIPLE'=>'N','SORT'=>700))) throw new RuntimeException($p->LAST_ERROR);
            echo 'Created: '.$code.PHP_EOL;
        }
        $properties=S::properties(S::config('catalog'));
        if(!isset($properties['MANUFACTURER'])) throw new RuntimeException('MANUFACTURER property missing');
        $property=$properties['MANUFACTURER'];
        if(!in_array($property['PROPERTY_TYPE'],array('L','S'),true)) throw new RuntimeException('Unsupported MANUFACTURER type; diagnose first');
        CIBlockSectionPropertyLink::Set(0,$property['ID'],array('SMART_FILTER'=>'Y','DISPLAY_TYPE'=>'F','DISPLAY_EXPANDED'=>'N','FILTER_HINT'=>''));
        $db=\Bitrix\Main\Application::getConnection();
        if(stripos(get_class($db),'mysql')===false) throw new RuntimeException('Queue migration requires MySQL/MariaDB');
        foreach(array(
            'b_kubatura_shop_jobs'=>'SHOP_ID INT NOT NULL PRIMARY KEY, CURSOR_ID INT NOT NULL DEFAULT 0, VERSION INT NOT NULL DEFAULT 1',
            'b_kubatura_shop_product_jobs'=>'PRODUCT_ID INT NOT NULL PRIMARY KEY, VERSION INT NOT NULL DEFAULT 1',
            'b_kubatura_shop_disabled'=>'PRODUCT_ID INT NOT NULL PRIMARY KEY'
        ) as $table=>$schema) if(!$db->isTableExists($table)) $db->queryExecute('CREATE TABLE '.$table.' ('.$schema.') ENGINE=InnoDB');
        $agent='Kubatura\\ShopPage\\Lifecycle::agent();';
        if(!CAgent::GetList(array(),array('NAME'=>$agent))->Fetch()) CAgent::AddAgent($agent,'','N',60,'','Y');
        CIBlock::clearIblockTagCache(S::config('catalog')); CIBlock::clearIblockTagCache(S::config('shops'));
        echo "Installed. Cascade stays disabled in config. Run reindex and diagnose.\n";
    } elseif($command==='reindex') {
        if(!class_exists('\\Bitrix\\Iblock\\PropertyIndex\\Manager')) throw new RuntimeException('Facet index API unavailable in this Bitrix version');
        $index=\Bitrix\Iblock\PropertyIndex\Manager::createIndexer(S::config('catalog'));
        if(!method_exists($index,'startIndex') || !method_exists($index,'continueIndex') || !method_exists($index,'endIndex')) throw new RuntimeException('Use native admin facet reindex for this Bitrix version');
        $index->startIndex(); $index->continueIndex(0); $index->endIndex();
        echo "Facet index rebuilt\n";
    } elseif($command==='dry-run') {
        $id=isset($argv[2])?(int)$argv[2]:0; if(!$id) throw new RuntimeException('Supply shop ID');
        $rs=CIBlockElement::GetList(array('ID'=>'ASC'),array('IBLOCK_ID'=>S::config('catalog'),'PROPERTY_SHOP'=>$id),false,false,array('ID'));
        $result=array(); while($r=$rs->Fetch()) { $state=L::processProduct($r['ID'],true); if(!isset($result[$state])) $result[$state]=0; $result[$state]++; }
        echo json_encode($result,JSON_PRETTY_PRINT).PHP_EOL;
    } elseif($command==='enqueue') {
        if(!L::enabled()) throw new RuntimeException('Both cascade_enabled and redirects_ready must be enabled after redirect verification');
        $id=isset($argv[2])?(int)$argv[2]:0; if(!$id) throw new RuntimeException('Supply shop ID');
        L::enqueueShop($id); echo "Queued\n";
    } elseif($command==='work') {
        if(!L::enabled()) throw new RuntimeException('Cascade disabled');
        L::run(); echo "Batch completed\n";
    } else throw new RuntimeException('Unknown command');
} catch(Throwable $e) { fwrite(STDERR,$e->getMessage().PHP_EOL); exit(1); }
