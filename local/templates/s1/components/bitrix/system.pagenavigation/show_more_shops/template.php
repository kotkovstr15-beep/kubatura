<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$pages=(int)$arResult['NavPageCount']; $page=(int)$arResult['NavPageNomer'];
$key='PAGEN_'.(int)$arResult['NavNum'];
$query=array(); parse_str(isset($arResult['NavQueryString'])?$arResult['NavQueryString']:'',$query);
unset($query[$key]);
$path=isset($arResult['sUrlPath'])?$arResult['sUrlPath']:'/shops/';
$pageUrl=static function($number) use($query,$key,$path) {
    if($number>1) $query[$key]=$number;
    return $path.($query?'?'.http_build_query($query,'','&',PHP_QUERY_RFC3986):'');
};
if($pages>1): ?>
<nav class="shop-pages pagination" aria-label="Страницы магазинов">
<?php for($i=1;$i<=$pages;$i++): ?>
<?php if($i===$page): ?><span class="pagination__item active" aria-current="page"><?=$i?></span>
<?php else: ?><a class="pagination__item" href="<?=htmlspecialcharsbx($pageUrl($i))?>"><?=$i?></a><?php endif ?>
<?php endfor ?></nav>
<?php if($page<$pages): ?><div class="action-box"><a class="btn" href="<?=htmlspecialcharsbx($pageUrl($page+1))?>" data-url="<?=htmlspecialcharsbx($pageUrl($page+1))?>">Загрузить еще</a></div><?php endif ?>
<?php endif ?>
