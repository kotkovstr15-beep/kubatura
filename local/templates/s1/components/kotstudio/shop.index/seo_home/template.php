<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

// Explicit home-page template. Selected as "seo_home" in /index.php.

use \Bitrix\Main\Localization\Loc;


/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

$this->setFrameMode(false);

require_once $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/include/performance/webp.php';

?><!-- shop.index template: seo_home --><?

if( $arResult["ITEMS"] && count( $arResult["ITEMS"] ) > 0 )
{

?>
				<section class="shops s-margin">
					<div class="container">
						<div class="title">
							<div class="title__grid">
								<div class="title__main">
									<h2 class="title__head">Магазины</h2>
								</div>
	<div class="title__btn2">

							<a href="/shops/" class="btn  btn_medium">Все магазины</a>
						</div>
								<div class="title__btn">
									<a href="/karta-tssr/" class="btn btn_medium"><i class="ic ic-map"></i> Карта</a>
								</div>
							</div>
						</div>

						<div class="shops__grid shops__slider" role="region" aria-label="Магазины Кубатуры — листайте вправо" tabindex="0">

							<?foreach($arResult["ITEMS"] as $arItem){
								$backgroundFile = null;
								$logoFile = null;
								$backgroundPictureId = !empty($arItem["PREVIEW_PICTURE"])
									? $arItem["PREVIEW_PICTURE"]
									: (!empty($arItem["DETAIL_PICTURE"]) ? $arItem["DETAIL_PICTURE"] : 0);
								if($backgroundPictureId) {
									$backgroundFile = CFile::ResizeImageGet($backgroundPictureId, array('width'=>660, 'height'=>420), BX_RESIZE_IMAGE_PROPORTIONAL, false, array(), false, 90);
									if(empty($backgroundFile['src']))
										$backgroundFile['src'] = CFile::GetPath($backgroundPictureId);
								}
								$hasBackground = !empty($backgroundFile['src']);
								?>
							<div class="shops__col">
								<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="shop-item<?=$hasBackground ? ' shop-item_has-bg' : ''?>">
									<?if($hasBackground):?>
									<img class="shop-item__bg" src="<?=htmlspecialcharsbx($backgroundFile['src'])?>" width="<?=(int)$backgroundFile['width']?>" height="<?=(int)$backgroundFile['height']?>" loading="lazy" alt="" aria-hidden="true">
									<?endif;?>
									<?
									if($arItem["PROPERTIES"]["LOGO_1"]["VALUE"]):
										$logoFile = CFile::ResizeImageGet($arItem["PROPERTIES"]["LOGO_1"]["VALUE"], array('width'=>440, 'height'=>200), BX_RESIZE_IMAGE_PROPORTIONAL, false, array(), false, 90);
										if(!$logoFile['src'])
											$logoFile['src'] = CFile::GetPath($arItem["PROPERTIES"]["LOGO_1"]["VALUE"]);
										$logoFile['src'] = kubaturaWebpUrl($logoFile['src'], 84);
									endif;
									?>
									<?if(!empty($logoFile['src'])):?>
									<div class="shop-item__img img-box">
										<img src="<?=htmlspecialcharsbx($logoFile['src'])?>" width="<?=(int)$logoFile['width']?>" height="<?=(int)$logoFile['height']?>" loading="lazy" alt="Магазин <?=htmlspecialcharsbx($arItem["NAME"])?> в Кубатуре, Самара">
									</div>
									<?endif;?>
									<div class="shop-item__place">
										<div class="shop-item__place-wrap">
										<?if($arItem["PROPERTIES"]["CORPUS"]["VALUE"]):?>
											<span>Корпус <?=$arItem["PROPERTIES"]["CORPUS"]["VALUE"]?></span>
										<?endif;?>
										<?if($arItem["PROPERTIES"]["ETAG"]["VALUE"]):?>
											<span>Этаж <?=$arItem["PROPERTIES"]["ETAG"]["VALUE"]?></span>
										<?endif;?>
										<?if($arItem["PROPERTIES"]["SECTION"]["VALUE"]):?>
											<span>Секция <?=$arItem["PROPERTIES"]["SECTION"]["VALUE"]?></span>
										<?endif;?>
										</div>
									</div>
									<div class="shop-item__action">
										<h3 class="zag_tx_5"><?=$arItem["NAME"]?></h3>
										<span class="shop-item__arrow"><i class="ic ic-next"></i></span>
									</div>
								</a>
							</div>
							<?
							}
							?>

						</div>

							<div class="title__btn2">
							
							<a href="/shops/" class="btn">Все магазины</a>
						</div>

						<?=$arResult["NAV_STRING"]?>
					</div>
				</section>
				<?
}
?>
