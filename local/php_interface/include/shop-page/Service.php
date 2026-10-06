<?php
namespace Kubatura\ShopPage;

/** Storefront queries deliberately do not require active parent sections. */
final class Service
{
    private static $config;
    private static $sections;
    private static $shops;
    private static $properties = array();
    public static function config($key = null)
    {
        if (self::$config === null) self::$config = require __DIR__ . '/config.php';
        return $key === null ? self::$config : self::$config[$key];
    }
    public static function e($value) { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
    public static function listValue($value)
    {
        return array_values(array_filter(is_array($value) ? $value : array($value), static function ($v) { return $v !== '' && $v !== null && $v !== false; }));
    }
    public static function value(array $shop, $code)
    {
        return isset($shop['PROPERTIES'][$code]['VALUE']) ? $shop['PROPERTIES'][$code]['VALUE'] : '';
    }
    public static function propertyValues(array $shop, $code)
    {
        $value = self::value($shop, $code);
        if (!is_array($value)) return self::listValue($value);
        $out = array();
        foreach ($value as $item) {
            if (is_array($item) && isset($item['VALUE'])) $item = $item['VALUE'];
            if ($item !== '' && $item !== null && $item !== false) $out[] = $item;
        }
        return $out;
    }
    public static function name($name)
    {
        return preg_replace_callback('/[А-ЯЁа-яё]+/u', static function ($m) {
            return mb_convert_case($m[0], MB_CASE_TITLE, 'UTF-8');
        }, trim($name));
    }
    public static function phone($text)
    {
        $digits = preg_replace('/\D/', '', $text);
        if (strlen($digits) === 11 && ($digits[0] === '8' || $digits[0] === '7')) return '+7' . substr($digits, 1);
        if (strlen($digits) === 10) return '+7' . $digits;
        return '';
    }
    public static function url($url)
    {
        $url = trim((string)$url);
        return preg_match('~^(?:https?://[^\s]+|/(?!/)[^\s]*)$~i', $url) ? $url : '';
    }
    public static function words($count)
    {
        $n = abs((int)$count); $end = $n % 100;
        return $n . ' ' . (($end >= 11 && $end <= 14) ? 'товаров' : ($n % 10 === 1 ? 'товар' : (($n % 10 >= 2 && $n % 10 <= 4) ? 'товара' : 'товаров')));
    }
    public static function balance(array $groups, $limit = 12)
    {
        $out = array(); $positions = array_fill(0, count($groups), 0); $groups = array_values($groups);
        do {
            $added = false;
            foreach ($groups as $i => $group) {
                while (isset($group[$positions[$i]]) && isset($out[$group[$positions[$i]]])) $positions[$i]++;
                if (isset($group[$positions[$i]])) {
                    $id = (int)$group[$positions[$i]++]; $out[$id] = $id; $added = true;
                    if (count($out) >= $limit) return array_values($out);
                }
            }
        } while ($added);
        return array_values($out);
    }
    public static function address(array $shop)
    {
        $out = array();
        foreach (array('CORPUS' => 'корпус', 'ETAG' => 'этаж', 'SECTION' => 'секция') as $code => $label) {
            $value = implode(', ', self::listValue(self::value($shop, $code)));
            if (trim($value) !== '') $out[] = $label . ' ' . $value;
        }
        $text = implode(', ', $out);
        return $text === '' ? '' : mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
    }
    public static function meta(array $shop, $category)
    {
        $name = self::name($shop['NAME']);
        $category = mb_strtolower(mb_substr($category, 0, 1)) . mb_substr($category, 1);
        $suffix = ' в ЦДИР «Кубатура», Самара';
        $title = $name . ($category !== '' ? ' — ' . $category : '') . $suffix;
        if (mb_strlen($title) > 70) $title = $name . $suffix;
        if (mb_strlen($title) > 70) $title = self::shorten($name, 70 - mb_strlen($suffix)) . $suffix;
        $address = self::address($shop);
        $description = $name . $suffix . ($category !== '' ? ': ' . $category : '') . '. ' . ($address !== '' ? $address . '. ' : '') . 'Режим работы и схема центра.';
        // No invented facts to pad short descriptions; diagnostics report these cases.
        if (mb_strlen($description) > 180) {
            $description = $name . $suffix . '. ' . ($address !== '' ? $address . '. ' : '') . 'Режим работы и схема центра.';
            if (mb_strlen($description) > 180) $description = self::shorten($description, 180);
        }
        if (mb_strlen($description) < 150) {
            $description = rtrim($description, '. ') . '. Контакты и ассортимент магазина.';
            if (mb_strlen($description) > 180) $description = self::shorten($description, 180);
        }
        return array('h1' => $name . ($category !== '' ? ' — ' . $category : '') . ' в Самаре', 'title' => $title, 'description' => trim($description));
    }
    public static function shorten($text, $length)
    {
        if (mb_strlen($text) <= $length) return $text;
        return rtrim(mb_substr($text, 0, max(0, $length - 1)), " ,:;—-") . '…';
    }
    public static function properties($iblock)
    {
        if (!isset(self::$properties[$iblock])) {
            self::$properties[$iblock] = array();
            $rs = \CIBlockProperty::GetList(array('SORT' => 'ASC', 'ID' => 'ASC'), array('IBLOCK_ID' => $iblock));
            while ($p = $rs->Fetch()) self::$properties[$iblock][$p['CODE']] = $p;
        }
        return self::$properties[$iblock];
    }
    public static function detectProperty($iblock, $setting, array $names)
    {
        $configured = self::config($setting);
        if ($configured !== '') return $configured;
        $found = array();
        foreach (self::properties($iblock) as $p) if (in_array(mb_strtolower(trim($p['NAME'])), $names, true)) $found[] = $p['CODE'];
        return count($found) === 1 ? $found[0] : '';
    }
    public static function categories(array $shop)
    {
        $code = 'CATEGORY_FILTER';
        $props = self::properties(self::config('shops'));
        if (!isset($props[$code])) return array();
        $p = $props[$code]; $values = self::propertyValues($shop, $code); $out = array();
        $map = self::config('category_sections');
        foreach ($values as $value) {
            $id = (string)$value; $name = (string)$value;
            if ($p['PROPERTY_TYPE'] === 'L') {
                $enum = \CIBlockPropertyEnum::GetList(array(), array('PROPERTY_ID'=>(int)$p['ID'], 'ID'=>(int)$value))->Fetch();
                if ($enum) { $id = (string)$enum['ID']; $name = $enum['VALUE']; }
            } elseif ($p['PROPERTY_TYPE'] === 'E') {
                $section = \CIBlockSection::GetList(array(), array('ID'=>(int)$value, 'IBLOCK_ID'=>self::config('catalog')), false, array('ID','NAME'))->Fetch();
                if ($section) { $id = (string)$section['ID']; $name = $section['NAME']; }
            }
            $sections = isset($map[$id]) ? array_map('intval', (array)$map[$id]) : (isset($map[$name]) ? array_map('intval', (array)$map[$name]) : array());
            if (!$sections) {
                $sectionRs = \CIBlockSection::GetList(array('LEFT_MARGIN'=>'ASC'), array('IBLOCK_ID'=>self::config('catalog'), 'ACTIVE'=>'Y', array('LOGIC'=>'OR', '=NAME'=>$name, '=CODE'=>$name)), false, array('ID'));
                while ($sectionRow = $sectionRs->Fetch()) $sections[] = (int)$sectionRow['ID'];
            }
            $out[] = array('id'=>$id, 'name'=>$name, 'sections'=>array_values(array_filter($sections)));
        }
        return $out;
    }
    public static function profileSections(array $shop)
    {
        $sections = array();
        foreach (self::categories($shop) as $category) foreach ($category['sections'] as $id) $sections[(int)$id] = (int)$id;
        return array_values($sections);
    }
    public static function categoryName(array $shop)
    {
        $best = ''; $max = -1;
        foreach (self::categories($shop) as $category) {
            $count = $category['sections'] ? self::count(self::productFilter($shop['ID'], $category['sections'])) : 0;
            if ($count > $max) { $max = $count; $best = $category['name']; }
        }
        return $best;
    }
    public static function shop($code, $active = true)
    {
        $filter = array('IBLOCK_ID' => self::config('shops'), '=CODE' => $code);
        if ($active) { $filter['ACTIVE'] = 'Y'; $filter['ACTIVE_DATE'] = 'Y'; }
        $rs = \CIBlockElement::GetList(array(), $filter, false, array('nTopCount' => 1));
        if (!$ob = $rs->GetNextElement()) return null;
        $shop = $ob->GetFields(); $shop['PROPERTIES'] = $ob->GetProperties();
        return $shop;
    }
    public static function sections()
    {
        if (self::$sections === null) {
            self::$sections = array();
            $rs = \CIBlockSection::GetList(array('LEFT_MARGIN' => 'ASC'), array('IBLOCK_ID' => self::config('catalog')), false, array('ID','NAME','CODE','XML_ID','ACTIVE','LEFT_MARGIN','RIGHT_MARGIN','DEPTH_LEVEL','PICTURE','SECTION_PAGE_URL'));
            while ($row = $rs->GetNext(false, false)) self::$sections[(int)$row['ID']] = $row;
        }
        return self::$sections;
    }
    public static function sectionByPath($path)
    {
        foreach (self::sections() as $s) if (rtrim(parse_url($s['SECTION_PAGE_URL'], PHP_URL_PATH), '/') === rtrim($path, '/')) return $s;
        return null;
    }
    public static function productFilter($shopId = null, $sections = array())
    {
        $filter = array('IBLOCK_ID' => self::config('catalog'), 'ACTIVE' => 'Y', 'ACTIVE_DATE' => 'Y', 'CHECK_PERMISSIONS' => 'Y');
        if ($shopId !== null) $filter['PROPERTY_SHOP'] = (int)$shopId;
        if ($sections) { $filter['SECTION_ID'] = $sections; $filter['INCLUDE_SUBSECTIONS'] = 'Y'; }
        return $filter;
    }
    public static function count(array $filter)
    {
        return (int)\CIBlockElement::GetList(array(), $filter, array(), false, array('ID'));
    }
    public static function candidates(array $filter)
    {
        $sort = array('SORT' => 'ASC', 'DATE_CREATE' => 'DESC', 'ID' => 'DESC');
        $code = self::detectProperty(self::config('catalog'), 'recommend_property', array('рекомендуем'));
        $ids = array();
        foreach ($code ? array(true, false) : array(false) as $recommended) {
            $f = $filter;
            if ($code) $f[($recommended ? '!' : '') . 'PROPERTY_' . $code] = false;
            $rs = \CIBlockElement::GetList($sort, $f, false, array('nTopCount' => 12), array('ID'));
            while ($row = $rs->Fetch()) $ids[(int)$row['ID']] = (int)$row['ID'];
            if (count($ids) >= 12) break;
        }
        return array_slice(array_values($ids), 0, 12);
    }
    /** Select up to 12 products evenly across the shop's profile sections. */
    public static function balancedCandidates($shopId, array $sections, $limit = 12)
    {
        $groups = array();
        foreach ($sections as $sectionId) {
            $groups[] = self::candidates(self::productFilter($shopId, array((int)$sectionId)));
        }
        return self::balance($groups, $limit);
    }
    public static function products(array $ids)
    {
        if (!$ids) return array();
        $filter = self::productFilter(); $filter['ID'] = $ids;
        $rows = array(); $rs = \CIBlockElement::GetList(array(), $filter, false, false, array('ID','IBLOCK_ID','NAME','DETAIL_PAGE_URL','PREVIEW_PICTURE','DETAIL_PICTURE'));
        while ($r = $rs->GetNext(false, false)) {
            $r['PROPERTIES'] = array();
            $rows[(int)$r['ID']] = $r;
        }
        if ($rows) \CIBlockElement::GetPropertyValuesArray($rows, self::config('catalog'), array('ID' => array_keys($rows)), array('CODE' => array('PRICE','SALE_PRICE','NEW','SALE','SHOP')));
        $out = array(); foreach ($ids as $id) if (isset($rows[$id])) $out[] = $rows[$id];
        return $out;
    }
    public static function brands($shopId)
    {
        $props = self::properties(self::config('catalog'));
        if (!isset($props['MANUFACTURER'])) return array();
        $p = $props['MANUFACTURER']; $filter = self::productFilter($shopId); $filter['!PROPERTY_MANUFACTURER'] = false;
        $rs = \CIBlockElement::GetList(array(), $filter, array('PROPERTY_MANUFACTURER'), false, array('PROPERTY_MANUFACTURER'));
        $used = array(); while ($r = $rs->Fetch()) {
            if (isset($r['PROPERTY_MANUFACTURER_VALUE'])) $used[(string)$r['PROPERTY_MANUFACTURER_VALUE']] = true;
            if (isset($r['PROPERTY_MANUFACTURER_ENUM_ID'])) $used[(string)$r['PROPERTY_MANUFACTURER_ENUM_ID']] = true;
        }
        $out = array();
        if ($p['PROPERTY_TYPE'] === 'L') {
            $rs = \CIBlockPropertyEnum::GetList(array('SORT'=>'ASC','VALUE'=>'ASC'), array('PROPERTY_ID'=>$p['ID']));
            while ($r = $rs->Fetch()) if (isset($used[(string)$r['ID']]) || isset($used[(string)$r['VALUE']])) $out[] = array('name'=>$r['VALUE'], 'code'=>self::brandCode($r), 'value'=>$r['ID']);
        } elseif ($p['PROPERTY_TYPE'] === 'S') {
            foreach (array_keys($used) as $value) $out[] = array('name'=>$value, 'code'=>rawurlencode($value), 'value'=>$value);
        }
        return $out;
    }
    public static function brandCode(array $enum)
    {
        return rawurlencode($enum['XML_ID'] !== '' ? $enum['XML_ID'] : $enum['VALUE']);
    }
    public static function saleSource()
    {
        $id = (int)self::config('sale_iblock');
        if (!$id) {
            $rs = \CIBlock::GetList(array(), array('NAME' => '#Кубатура_sale', 'CHECK_PERMISSIONS'=>'N')); $matches = array();
            while ($r = $rs->Fetch()) if ($r['NAME'] === '#Кубатура_sale') $matches[] = (int)$r['ID'];
            if (count($matches) === 1) $id = $matches[0];
        }
        $code = self::config('sale_shop_property');
        if ($id && !$code) {
            $matches = array(); foreach (self::properties($id) as $p) if ($p['PROPERTY_TYPE'] === 'E' && (int)$p['LINK_IBLOCK_ID'] === (int)self::config('shops')) $matches[] = $p['CODE'];
            if (count($matches) === 1) $code = $matches[0];
        }
        return array($id, $code);
    }
    public static function sales($shopId)
    {
        list($id,$code) = self::saleSource(); if (!$id || !$code) return array();
        $rs = \CIBlockElement::GetList(array('SORT'=>'ASC','ACTIVE_TO'=>'ASC','ID'=>'DESC'), array('IBLOCK_ID'=>$id,'ACTIVE'=>'Y','ACTIVE_DATE'=>'Y','PROPERTY_'.$code=>$shopId,'CHECK_PERMISSIONS'=>'Y'), false, false, array('ID','IBLOCK_ID','NAME','DETAIL_PAGE_URL','PREVIEW_PICTURE','DETAIL_PICTURE','ACTIVE_TO'));
        $out = array(); while ($r = $rs->GetNext(false,false)) $out[]=$r; return $out;
    }
    /** Only explicitly selected shops are shown; an empty field hides the block. */
    public static function neighborSelection(array $shop)
    {
        $manual = array_values(array_unique(array_filter(array_map('intval', self::listValue(self::value($shop, 'ANALOG_SHOPS'))), static function($id) { return $id > 0; })));
        $shared = array(); foreach (self::categories($shop) as $category) $shared[] = $category['id'];
        return array('manual'=>$manual, 'shared'=>array_values(array_unique($shared)));
    }
    public static function neighbors(array $shop)
    {
        $selection = self::neighborSelection($shop);
        $manual = $selection['manual']; $ids = $selection['shared'];
        if (!$manual && !$ids) return array();
        $filter = array('IBLOCK_ID'=>self::config('shops'),'ACTIVE'=>'Y','ACTIVE_DATE'=>'Y','CHECK_PERMISSIONS'=>'Y','!ID'=>(int)$shop['ID']);
        if ($manual) $filter['ID']=$manual; else $filter['PROPERTY_CATEGORY_FILTER']=$ids;
        $out=array(); $rs=\CIBlockElement::GetList(array('SORT'=>'ASC','ID'=>'ASC'),$filter,false,false,array('ID','IBLOCK_ID','NAME','DETAIL_PAGE_URL'));
        while ($r=$rs->GetNext(false,false)) {
            // Bitrix otherwise replaces element fields with property values.
            $r['PROPERTIES'] = array();
            $out[(int)$r['ID']] = $r;
        }
        if (!$out) return array();
        \CIBlockElement::GetPropertyValuesArray($out,self::config('shops'),array('ID'=>array_keys($out)),array('CODE'=>array('LOGO_2','CORPUS','ETAG','SECTION')));
        $counts=array();
        if (!$manual) {
            $f=self::productFilter(); $f['PROPERTY_SHOP']=array_keys($out);
            $rs=\CIBlockElement::GetList(array(),$f,array('PROPERTY_SHOP'),false,array('PROPERTY_SHOP'));
            while ($r=$rs->Fetch()) $counts[(int)$r['PROPERTY_SHOP_VALUE']]=(int)$r['CNT'];
        }
        $order=array_flip($manual);
        uasort($out,static function($a,$b) use($manual,$order,$counts) {
            $av=$manual ? $order[$a['ID']] : -(isset($counts[$a['ID']])?$counts[$a['ID']]:0);
            $bv=$manual ? $order[$b['ID']] : -(isset($counts[$b['ID']])?$counts[$b['ID']]:0);
            return $av===$bv ? (int)$a['ID']-(int)$b['ID'] : ($av<$bv?-1:1);
        });
        return array_values($out);
    }
    public static function image($id, $w=240, $h=160)
    {
        $image = $id ? \CFile::ResizeImageGet((int)$id,array('width'=>$w,'height'=>$h),BX_RESIZE_IMAGE_PROPORTIONAL,true) : false;
        return $image && !empty($image['src']) ? $image['src'] : 'https://kubatura.su/upload/iblock/899/yxp6jz52w0m5sr5tefvg3ny8rlx5te7v.jpg';
    }
    public static function html($value)
    {
        $text=is_array($value)?(isset($value['TEXT'])?$value['TEXT']:''):(string)$value;
        if (trim(str_replace("\xc2\xa0",' ',html_entity_decode(strip_tags($text),ENT_QUOTES,'UTF-8'))) === '') return '';
        if (!is_array($value) || !isset($value['TYPE']) || strtolower($value['TYPE']) !== 'html') return nl2br(self::e($text));
        $sanitizer=new \CBXSanitizer(); $sanitizer->SetLevel(\CBXSanitizer::SECURE_LEVEL_HIGH);
        $sanitizer->AddTags(array('h3'=>array(),'ul'=>array(),'ol'=>array(),'li'=>array(),'p'=>array(),'br'=>array(),'strong'=>array(),'em'=>array()));
        $text = preg_replace('/<(\/?)(?:h[1-6])\b[^>]*>/i', '<$1h3>', $text);
        return $sanitizer->SanitizeHtml($text);
    }
    public static function data(array $shop)
    {
        $profileSections = self::profileSections($shop);
        $filter = $profileSections ? self::productFilter($shop['ID'], $profileSections) : array();
        $total = $profileSections ? self::count($filter) : 0;
        $links = array(); $categories = array(); $category = self::categoryName($shop);
        if ($total) {
            foreach (self::sections() as $sid => $section) {
                if ($profileSections && !in_array((int)$sid, $profileSections, true)) continue;
                $count = self::count(self::productFilter($shop['ID'], array($sid)));
                if (!$count) continue;
                $section['count'] = $count; $links[$sid] = $section;
                $categories[] = array('id'=>$sid, 'name'=>$section['NAME'], 'sections'=>array($sid), 'count'=>$count);
            }
        }
        $productIds = $profileSections ? self::balancedCandidates($shop['ID'], $profileSections, 12) : array();
        $products = $total ? self::products($productIds) : array();
        return array('shop'=>$shop,'meta'=>self::meta($shop,$category),'category'=>$category,'total'=>$total, 'categories'=>$categories,'links'=>$links,'products'=>$products, 'brands'=>self::brands($shop['ID']),'sales'=>self::sales($shop['ID']),'neighbors'=>self::neighbors($shop));
    }
    public static function shopLabel(array $shop)
    {
        $name=self::e($shop['NAME']);
        return isset($shop['ACTIVE']) && $shop['ACTIVE']==='Y' ? '<a href="'.self::e($shop['DETAIL_PAGE_URL']).'">'.$name.'</a>' : $name;
    }
}
