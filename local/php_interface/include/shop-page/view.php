<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Kubatura\ShopPage\Service as S;
$shop=$data['shop']; $meta=$data['meta']; $name=S::name($shop['NAME']);
$visibleNeighbors = array();
foreach (isset($data['neighbors']) ? $data['neighbors'] : array() as $item) {
    if (!empty($item['ID']) && !empty($item['NAME']) && trim((string)$item['NAME']) !== '' && !empty($item['DETAIL_PAGE_URL'])) {
        $visibleNeighbors[] = $item;
    }
}
$visibleProducts = array();
if (!empty($data['total']) && !empty($data['products'])) {
    foreach ($data['products'] as $item) {
        if (!empty($item['ID']) && trim((string)$item['NAME']) !== '' && !empty($item['DETAIL_PAGE_URL'])) {
            $visibleProducts[] = $item;
        }
    }
}
$primarySection = null;
if (!empty($data['links'])) $primarySection = reset($data['links']);
$primaryCategoryLink = $primarySection && !empty($primarySection['SECTION_PAGE_URL'])
    ? rtrim($primarySection['SECTION_PAGE_URL'], '/') . '/'
    : '';
$categoryTitle = !empty($data['category']) ? trim((string)$data['category']) : '';
$APPLICATION->SetTitle($meta['h1']);
$APPLICATION->SetPageProperty('title',$meta['title']);
$APPLICATION->SetPageProperty('description',$meta['description']);
$APPLICATION->SetPageProperty('canonical','/shops/'.S::e($shop['CODE']).'.html');
\Bitrix\Main\Page\Asset::getInstance()->addCss('/local/templates/s1/css/shop-page.css');
\Bitrix\Main\Page\Asset::getInstance()->addJs('/local/templates/s1/js/shop-page.js');
$hoursCode=S::detectProperty(S::config('shops'),'hours_property',array('часы работы','режим работы','время работы','часы работы салона'));
$hours=$hoursCode?implode(', ',S::listValue(S::value($shop,$hoursCode))):'';
?>
<article class="shop-page s-inner"><div class="container">
<nav class="breadcrumbs" aria-label="Хлебные крошки" itemscope itemtype="https://schema.org/BreadcrumbList"><ul class="breadcrumbs__list">
<?php foreach(array(array('/','Главная'),array('/shops/','Магазины'),array('',$name)) as $i=>$crumb): ?>
<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><?php if($crumb[0]): ?><a itemprop="item" href="<?=S::e($crumb[0])?>"><span itemprop="name"><?=S::e($crumb[1])?></span></a><?php else: ?><span itemprop="name"><?=S::e($crumb[1])?></span><?php endif ?><meta itemprop="position" content="<?=$i+1?>"></li>
<?php endforeach ?></ul></nav>
<h1><?=S::e($meta['h1'])?></h1>
<div class="shop-page__card">
<div class="shop-page__logo"><img src="<?=S::e(S::image(S::value($shop,'LOGO_2'),400,280))?>" width="176" height="116" alt="<?=S::e('Логотип магазина '.$name)?>"></div>
<div class="shop-page__contacts">
<?php
$metaRows = array();
foreach (array('CORPUS'=>'Корпус','ETAG'=>'Этаж','SECTION'=>'Секция') as $code=>$label) {
    $metaValue = implode(', ', S::listValue(S::value($shop, $code)));
    if (trim($metaValue) !== '') $metaRows[] = array($label, $metaValue);
}
if ($hours) $metaRows[] = array('Режим работы', $hours);
?>
<?php if($metaRows): ?><dl class="shop-page__meta">
<?php foreach($metaRows as $metaRow): ?><div><dt><?=S::e($metaRow[0])?></dt><dd><?=S::e($metaRow[1])?></dd></div><?php endforeach ?>
</dl><?php endif ?>
<?php foreach(S::listValue(S::value($shop,'PHONE')) as $phones): foreach(preg_split('/[;,\r\n]+/', $phones,-1,PREG_SPLIT_NO_EMPTY) as $phone): $phone=trim($phone); if($phone==='') continue; $tel=S::phone($phone); ?>
<?php if($tel): ?><a class="shop-page__tel" href="tel:<?=S::e($tel)?>"><?=S::e($phone)?></a><?php else: ?><span class="shop-page__tel shop-page__tel--plain"><?=S::e($phone)?></span><?php endif ?>
<?php endforeach; endforeach ?>
<?php
$primaryActions = array();
if($url=S::url(S::value($shop,'MAP_LINK'))) $primaryActions[] = array($url,'Показать на схеме центра','map');
if($visibleProducts) $primaryActions[] = array('#tov','Товары магазина','goods');
$socialLinks = array();
foreach(array('SITE_LINK'=>'сайт','VK_LINK'=>'VK') as $code=>$label) { if($url=S::url(S::value($shop,$code))) $socialLinks[] = array($url,$label); }
?>
<?php if($primaryActions): ?><div class="shop-page__actions">
<?php foreach($primaryActions as $action): ?><a class="shop-page__action shop-page__action--<?=S::e($action[2])?>" href="<?=S::e($action[0])?>"><?=S::e($action[1])?></a><?php endforeach ?>
</div><?php endif ?>
<?php if($socialLinks): ?><div class="shop-page__socials">
<?php foreach($socialLinks as $social): ?><a href="<?=S::e($social[0])?>" rel="noopener" aria-label="<?=S::e($social[1])?>"><span><?=S::e($social[1])?></span></a><?php endforeach ?>
</div><?php endif ?>
</div>
<?php $slides=S::listValue(S::value($shop,'SLIDE')); if($slides): $slidesCount=count($slides); ?><div class="shop-page__photos swiper-container" data-per-view="<?=min(3,$slidesCount)?>" aria-label="Фотографии салона"><div class="swiper-wrapper">
<?php foreach($slides as $i=>$slide): ?><div class="swiper-slide"><img src="<?=S::e(S::image($slide,1000,700))?>" width="1000" height="700" <?= $i ? 'loading="lazy"' : '' ?> alt="<?=S::e('Салон '.$name.', фото '.($i+1))?>"></div><?php endforeach ?>
</div><?php if($slidesCount>3): ?><button type="button" class="shop-page__photos-prev" data-photo-scroll="-1" aria-label="Предыдущее фото">←</button><button type="button" class="shop-page__photos-next" data-photo-scroll="1" aria-label="Следующее фото">→</button><?php endif ?></div><?php endif ?>
</div>
<?php
$hasDescriptions=false;
foreach(array('DESC_MAIN'=>'','DESC_ASSORT'=>'Что есть в салоне','DESC_HELP'=>'Чем помогают') as $code=>$heading):
    $raw=isset($shop['PROPERTIES'][$code]['~VALUE'])?$shop['PROPERTIES'][$code]['~VALUE']:S::value($shop,$code); $content=S::html($raw); if(!$content) continue; $hasDescriptions=true;
?>
<section class="shop-page__description"><?php if($heading): ?><h2><?=S::e($heading)?></h2><?php endif ?><div class="content"><?=$content?></div></section>
<?php endforeach;
if(!$hasDescriptions && S::config('legacy_description_fallback')):
    $legacy=S::html(array('TEXT'=>isset($shop['~DETAIL_TEXT'])?$shop['~DETAIL_TEXT']:$shop['DETAIL_TEXT'],'TYPE'=>$shop['DETAIL_TEXT_TYPE']));
    if($legacy): ?><div class="shop-page__description content"><?=$legacy?></div><?php endif;
endif;
if($data['total'] && $data['brands']): ?>
<section><h2>Бренды в салоне</h2><ul class="shop-page__brands">
<?php foreach($data['brands'] as $brand): ?>
<li><a href="<?=S::e('/catalog/filter/shop-is-'.rawurlencode($shop['CODE']).'/manufacturer-is-'.$brand['code'].'/apply/')?>"><?=S::e($brand['name'])?></a></li>
<?php endforeach ?></ul></section><?php endif ?>
<?php if($visibleProducts): ?>
<section id="tov"><div class="shop-page__heading"><h2>Товары магазина</h2><div class="shop-page__product-actions"><?php if($primaryCategoryLink && $categoryTitle): ?><a href="<?=S::e($primaryCategoryLink)?>">Все <?=S::e(mb_strtolower($categoryTitle, 'UTF-8'))?> →</a><?php endif ?><button type="button" data-product-scroll="-1" aria-label="Предыдущие товары">←</button><button type="button" data-product-scroll="1" aria-label="Следующие товары">→</button></div></div><div class="shop-page__products-viewport swiper-container"><div class="shop-page__products swiper-wrapper">
<?php foreach($visibleProducts as $product): ?><div class="swiper-slide"><?php include __DIR__.'/product-card.php'; ?></div><?php endforeach ?>
</div></div></section><?php endif ?>
<?php if($data['total']): ?>
<section><h2>Товары магазина в каталоге</h2><ul class="shop-page__links shop-page__links--catalog">
<?php foreach($data['links'] as $section): ?>
<li><a href="<?=S::e(rtrim($section['SECTION_PAGE_URL'],'/').'/filter/shop-is-'.rawurlencode($shop['CODE']).'/apply/')?>"><img src="<?=S::e(S::image($section['PICTURE']))?>" width="120" height="80" loading="lazy" alt="<?=S::e($section['NAME'])?>"><span><?=S::e($section['NAME'])?> — <?=S::words($section['count'])?></span></a></li>
<?php endforeach ?></ul><a class="shop-page__all-goods" href="<?=S::e('/catalog/filter/shop-is-'.rawurlencode($shop['CODE']).'/apply/')?>">Весь ассортимент магазина — <?=S::words($data['total'])?></a></section>
<?php endif ?>
<?php if($data['sales']): ?>
<section><div class="shop-page__heading"><h2>Акции салона</h2><a href="/vygodnaya-pokupka/">Все акции центра</a></div><ul class="shop-page__links shop-page__links--sales">
<?php foreach($data['sales'] as $sale): ?><li><a href="<?=S::e($sale['DETAIL_PAGE_URL'])?>"><img src="<?=S::e(S::image($sale['PREVIEW_PICTURE']?:$sale['DETAIL_PICTURE']))?>" width="120" height="80" loading="lazy" alt="<?=S::e($sale['NAME'])?>"><span><small><?=S::e($sale['ACTIVE_TO']?'До '.$sale['ACTIVE_TO']:'Постоянно')?></small><strong><?=S::e($sale['NAME'])?></strong></span></a></li><?php endforeach ?>
</ul></section><?php endif ?>
<?php if($visibleNeighbors): ?>
<section><div class="shop-page__heading"><h2>Другие магазины<?= $categoryTitle ? ' '.S::e(mb_strtolower($categoryTitle, 'UTF-8')) : '' ?></h2><div class="shop-page__controls"><button type="button" data-shop-scroll="-1" aria-label="Предыдущие магазины">←</button><button type="button" data-shop-scroll="1" aria-label="Следующие магазины">→</button></div></div>
<ul class="shop-page__neighbors" tabindex="0" aria-label="Другие магазины">
<?php foreach($visibleNeighbors as $neighbor): ?><li><a href="<?=S::e($neighbor['DETAIL_PAGE_URL'])?>"><img src="<?=S::e(S::image(S::value($neighbor,'LOGO_2'),400,280))?>" width="200" height="140" loading="lazy" alt="<?=S::e('Логотип магазина '.S::name($neighbor['NAME']))?>"><strong><?=S::e(S::name($neighbor['NAME']))?></strong><span><?=S::e(S::address($neighbor))?></span></a></li><?php endforeach ?>
</ul></section><?php endif ?>
</div></article>