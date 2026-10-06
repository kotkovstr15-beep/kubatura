<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$props=$arResult['PROPERTIES'];
$value=static function($code,$fallback='')use($props){if(!isset($props[$code]['VALUE'])||$props[$code]['VALUE']===''||$props[$code]['VALUE']===null)return $fallback;$v=$props[$code]['VALUE'];return is_array($v)?implode(', ',array_filter($v)):$v;};
$first=static function($code)use($props){if(!isset($props[$code]['VALUE'])||$props[$code]['VALUE']===''||$props[$code]['VALUE']===null)return '';return is_array($props[$code]['VALUE'])?reset($props[$code]['VALUE']):$props[$code]['VALUE'];};
$category=$first('CATEGORY_FILTER');
$title=$arResult['NAME'].($category?' — '.$category.' в Самаре':'');
$logoId=$first('LOGO_2')?:$first('LOGO_1');
$logo=$logoId?CFile::ResizeImageGet($logoId,array('width'=>260,'height'=>180),BX_RESIZE_IMAGE_PROPORTIONAL,true):false;
$slides=array();
foreach((array)($props['SLIDE']['VALUE']??array()) as $slideId){$slide=CFile::ResizeImageGet($slideId,array('width'=>520,'height'=>320),BX_RESIZE_IMAGE_PROPORTIONAL,true);if($slide&&$slide['src'])$slides[]=$slide['src'];}
if(!$slides&&$arResult['PREVIEW_PICTURE']['SRC'])$slides[]=$arResult['PREVIEW_PICTURE']['SRC'];
$phoneHref=static function($phone){$digits=preg_replace('/[^0-9]/','',(string)$phone);if(strlen($digits)===11&&$digits[0]==='8')$digits='7'.substr($digits,1);return '+'.$digits;};
$phones=isset($props['PHONE']['VALUE'])?(array)$props['PHONE']['VALUE']:array();
$description=trim((string)$arResult['DETAIL_TEXT']);
?>
<section class="shop-page">
	<div class="container">
		<div class="shop-page__breadcrumbs"><a href="/">Главная</a><span>/</span><a href="/shops/">Магазины</a><span>/</span><b><?=htmlspecialcharsbx($arResult['NAME'])?></b></div>
		<h1 class="shop-page__title"><?=htmlspecialcharsbx($title)?></h1>

		<div class="shop-page__hero">
			<aside class="shop-page__aside">
				<div class="shop-page__logo"><?if($logo&&$logo['src']):?><img src="<?=htmlspecialcharsbx($logo['src'])?>" alt="<?=htmlspecialcharsbx($arResult['NAME'])?>"><?else:?><span>Логотип магазина</span><?endif;?></div>
				<div class="shop-page__facts">
					<?if($value('CORPUS')):?><span>Корпус <?=$value('CORPUS')?></span><?endif;?>
					<?if($value('ETAG')):?><span>этаж <?=$value('ETAG')?></span><?endif;?>
					<?if($value('SECTION')):?><span>секция <?=$value('SECTION')?></span><?endif;?>
				</div>
				<?foreach($phones as $phone):if(!$phone)continue;?><a class="shop-page__phone" href="tel:<?=$phoneHref($phone)?>"><?=htmlspecialcharsbx($phone)?></a><?endforeach;?>
				<div class="shop-page__actions"><?if($value('MAP_LINK')):?><a href="<?=htmlspecialcharsbx($value('MAP_LINK'))?>">Показать на схеме центра</a><?endif;?><a href="#tov">Товары магазина</a></div>
				<div class="shop-page__socials"><?if($value('SITE_LINK')):?><a href="<?=htmlspecialcharsbx($value('SITE_LINK'))?>">Сайт магазина</a><?endif;?><?if($value('VK_LINK')):?><a href="<?=htmlspecialcharsbx($value('VK_LINK'))?>">ВКонтакте</a><?endif;?></div>
			</aside>
			<div class="shop-page__gallery"<?if(count($slides)>1):?> data-shop-gallery<?endif;?>><?foreach($slides as $slide):?><img src="<?=htmlspecialcharsbx($slide)?>" alt="" loading="lazy"><?endforeach;?></div>
		</div>

		<?if($description):?><section class="shop-page__description"><div class="content"><?=$description?></div></section><?endif;?>

		<section id="tov" class="shop-page__section shop-page__products-section">
			<div class="shop-page__section-head"><h2>Товары магазина</h2><a href="/catalog/">Все товары магазина →</a><div class="shop-page__arrows"><button type="button" data-product-scroll="-1" aria-label="Назад">←</button><button type="button" data-product-scroll="1" aria-label="Вперёд">→</button></div></div>
			<div class="shop-page__products-viewport"><?$APPLICATION->IncludeComponent("bitrix:catalog.link.list","tovar-m",array("ELEMENT_ID"=>$arResult['ID'],"IBLOCK_ID"=>"7","IBLOCK_TYPE"=>"content","LINK_PROPERTY_SID"=>"SHOP","PAGE_ELEMENT_COUNT"=>"30","DISPLAY_BOTTOM_PAGER"=>"N","CACHE_TYPE"=>"A","CACHE_TIME"=>"300","SET_TITLE"=>"N","FILTER_NAME"=>"arrFilter"),false);?></div>
		</section>

		<?if($arResult['PROPERTIES']['ANALOG_SHOPS']['VALUE']):?><section class="shop-page__section"><div class="shop-page__section-head"><h2>Другие магазины напольных покрытий</h2><div class="shop-page__arrows"><button type="button" data-shop-scroll="-1" aria-label="Назад">←</button><button type="button" data-shop-scroll="1" aria-label="Вперёд">→</button></div></div><div class="shop-page__neighbors">
			<?$res=CIBlockElement::GetList(array('ID'=>'ASC'),array('=ID'=>(array)$arResult['PROPERTIES']['ANALOG_SHOPS']['VALUE'],'IBLOCK_ID'=>$arResult['PROPERTIES']['ANALOG_SHOPS']['LINK_IBLOCK_ID'],'ACTIVE'=>'Y'),false,false,array('ID','NAME','DETAIL_PAGE_URL','PROPERTY_LOGO_1','PROPERTY_CORPUS','PROPERTY_ETAG'));while($item=$res->GetNext()):$nlogo=$item['PROPERTY_LOGO_1_VALUE']?CFile::ResizeImageGet($item['PROPERTY_LOGO_1_VALUE'],array('width'=>220,'height'=>120),BX_RESIZE_IMAGE_PROPORTIONAL,true):false;?>
			<a class="shop-page__neighbor" href="<?=htmlspecialcharsbx($item['DETAIL_PAGE_URL'])?>"><span class="shop-page__neighbor-logo"><?if($nlogo&&$nlogo['src']):?><img src="<?=htmlspecialcharsbx($nlogo['src'])?>" alt=""><?else:?>Логотип<?endif;?></span><b><?=htmlspecialcharsbx($item['NAME'])?></b><small>Корпус <?=$item['PROPERTY_CORPUS_VALUE']?>, этаж <?=$item['PROPERTY_ETAG_VALUE']?></small></a>
			<?endwhile;?></div></section><?endif;?>
	</div>
</section>
