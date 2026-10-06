<?php
if (!function_exists('kubaturaKartaBreadcrumbs')) {
    function kubaturaKartaBreadcrumbs($current)
    {
        $e = static function ($value) { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); };
        echo '<nav class="breadcrumbs" aria-label="Хлебные крошки" itemscope itemtype="https://schema.org/BreadcrumbList"><ul class="breadcrumbs__list">';
        $items = array(array('/', 'Главная'), array('/karta-tssr/', 'Навигация'), array('', $current));
        foreach ($items as $i => $item) {
            echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            if ($item[0]) echo '<a itemprop="item" href="'.$e($item[0]).'"><span itemprop="name">'.$e($item[1]).'</span></a>';
            else echo '<span itemprop="name">'.$e($item[1]).'</span>';
            echo '<meta itemprop="position" content="'.($i + 1).'" /></li>';
        }
        echo '</ul></nav>';
    }
}
if (!function_exists('kubaturaKartaIntro')) {
    function kubaturaKartaIntro($file, $title)
    {
        global $APPLICATION;
        kubaturaKartaBreadcrumbs($title);
        echo '<h1>'.htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'</h1>';
        $APPLICATION->IncludeFile('/include/karta-tssr/'.$file.'.php', array(), array('MODE'=>'html', 'NAME'=>'Вводный текст карты центра'));
    }
}
if (!function_exists('kubaturaKartaShopList')) {
    /**
     * Плоский список строк из значения свойства: скаляр, массив (множественное свойство) или вложенные массивы.
     */
    function kubaturaKartaFlat($value)
    {
        $out = array();
        foreach ((array)$value as $item) {
            if (is_array($item)) {
                $out = array_merge($out, kubaturaKartaFlat($item));
            } else {
                $item = trim((string)$item);
                if ($item !== '') $out[] = $item;
            }
        }
        return $out;
    }

    function kubaturaKartaShopList($filterValue)
    {
        if (!\Bitrix\Main\Loader::includeModule('iblock')) return;
        $filter = array('IBLOCK_ID'=>6, 'ACTIVE'=>'Y', 'ACTIVE_DATE'=>'Y');
        if ($filterValue !== '') $filter['PROPERTY_CORPUS_FILTER_VALUE'] = $filterValue;
        $rs = \CIBlockElement::GetList(array('SORT'=>'ASC','NAME'=>'ASC'), $filter, false, false, array('ID','NAME','CODE','PROPERTY_SECTION','PROPERTY_CATEGORY_FILTER'));
        // Fetch() отдаёт «сырые» значения (без экранирования Bitrix) — экранируем один раз при выводе.
        // Множественные свойства Bitrix может вернуть и массивом, и дублирующимися строками — собираем по ID.
        $rows = array();
        while ($row = $rs->Fetch()) {
            $id = (int)$row['ID'];
            if (!isset($rows[$id])) $rows[$id] = array('NAME'=>$row['NAME'], 'CODE'=>$row['CODE'], 'SECTION'=>array(), 'CATEGORY'=>array());
            $rows[$id]['SECTION'] = array_merge($rows[$id]['SECTION'], kubaturaKartaFlat(isset($row['PROPERTY_SECTION_VALUE']) ? $row['PROPERTY_SECTION_VALUE'] : ''));
            $rows[$id]['CATEGORY'] = array_merge($rows[$id]['CATEGORY'], kubaturaKartaFlat(isset($row['PROPERTY_CATEGORY_FILTER_VALUE']) ? $row['PROPERTY_CATEGORY_FILTER_VALUE'] : ''));
        }
        if (!$rows) return;
        $e = static function ($value) { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); };
        // Карта лежит в float-блоке фиксированной высоты, поэтому список сбрасывает обтекание (clear)
        // и несёт собственные стили: CSS магазинов на страницах карты не подключается.
        echo '<style>
.karta-shops{clear:both;position:relative;margin:48px 0 0;padding:0;font-size:16px;line-height:1.4;font-weight:400;text-transform:none;white-space:normal;color:#333}
.karta-shops h2{margin:0 0 20px;text-transform:none}
.karta-shops__list{list-style:none;margin:0;padding:0;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px 20px}
.karta-shops__list li{margin:0;padding:0;background:#fff;border-left:4px solid #0078bf;border-radius:0 4px 4px 0}
.karta-shops__list li:before{content:none}
.karta-shops__list a{display:block;height:100%;padding:12px 16px;color:#333;text-decoration:none;text-transform:none}
.karta-shops__list a:hover{background:#f2f2f2}
.karta-shops__name{display:block;font-weight:600;color:#0078bf;overflow-wrap:anywhere}
.karta-shops__meta{display:block;margin-top:4px;font-size:14px;color:#777}
@media (max-width:1199px){.karta-shops__list{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media (max-width:575px){.karta-shops{margin-top:32px}.karta-shops__list{grid-template-columns:1fr}}
</style>';
        echo '<section class="karta-shops"><h2>Магазины на плане</h2><ul class="karta-shops__list">';
        foreach ($rows as $row) {
            $parts = array();
            if ($row['SECTION']) $parts[] = 'Секция '.implode(', ', array_unique($row['SECTION']));
            if ($row['CATEGORY']) $parts[] = implode(', ', array_unique($row['CATEGORY']));
            echo '<li><a href="'.$e('/shops/'.rawurlencode($row['CODE']).'.html').'"><span class="karta-shops__name">'.$e($row['NAME']).'</span>'
                .($parts ? '<span class="karta-shops__meta">'.$e(implode(' · ', $parts)).'</span>' : '')
                .'</a></li>';
        }
        echo '</ul></section>';
    }
}