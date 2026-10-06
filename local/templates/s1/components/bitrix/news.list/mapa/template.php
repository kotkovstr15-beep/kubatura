<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arItem */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
require_once $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/include/performance/webp.php';
$this->addExternalCss("/bitrix/css/main/bootstrap.css");
$this->addExternalCss("/bitrix/css/main/font-awesome.css");
$this->addExternalCss($this->GetFolder().'/themes/'.$arParams['TEMPLATE_THEME'].'/style.css');
?>
<div class="amap">
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>
<div class="row">
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	$arSlide = array();
$n=0;
foreach($arItem['PROPERTIES']['SLIDE']['VALUE'] as $key => $v) {
if($key==0){
	$file = CFile::ResizeImageGet($v, array('width'=>718, 'height'=>520), BX_RESIZE_IMAGE_PROPORTIONAL, true);
	if(!$file['src'])
		$file['src'] = CFile::GetPath($v);

	$arSlide[] = $file['src'];
}
}
?>
<div id="s<? $text=$arItem['DISPLAY_PROPERTIES']['SECTION']['DISPLAY_VALUE']; $format = preg_replace('/[^0-9]+/', '', $text); echo $format;?>" class="dn ower">
	<div class="mbox" >
		<a class="close r m40" href="#off">
		<svg width="22" height="23" viewBox="0 0 22 23" fill="none" xmlns="http://www.w3.org/2000/svg">
		<rect width="28.0668" height="3.00716" transform="matrix(0.706224 -0.707988 0.706224 0.707988 0.0390625 20.8662)" fill="#333333"/>
		<rect width="28.0668" height="3.00716" transform="matrix(0.707908 0.706304 -0.707908 0.706304 2.12891 1.0166)" fill="#333333"/>
		</svg>
	</a>


										<?if(count($arSlide)>0):?>
													<?foreach($arSlide as $key => $vs):?>
<?if($key==0):?>
													<div class="post-gallery__item " style="background-image: url('<?=$vs?>');"></div>
<?endif?>
													<?endforeach?>
										<?else:?>
<div class="post-gallery__item " style="background-image: url('/local/img/no-img.png');"></div>

<?endif;?>

										<div class="info">
											<?if($arItem["PROPERTIES"]["LOGO_2"]["VALUE"]):
											$file = CFile::ResizeImageGet($arItem["PROPERTIES"]["LOGO_2"]["VALUE"], array('width'=>220, 'height'=>220), BX_RESIZE_IMAGE_PROPORTIONAL, false, array(), false, 100);
											if(!$file['src'])
												$file['src'] = CFile::GetPath($arItem["PROPERTIES"]["LOGO_2"]["VALUE"]);
											$file['src'] = kubaturaWebpUrl($file['src'], 84);
											?>
											<a class="post-info__img m20b">
												<img src="<?=htmlspecialcharsbx($file['src'])?>" width="<?=(int)$file['width']?>" height="<?=(int)$file['height']?>" loading="lazy" alt="<?=htmlspecialcharsbx($arItem["NAME"])?>">
											</a>
											<?endif;?>
											<div class="post-info__main full l m20b">
												<h2 class="post-info__head"><?=$arItem["NAME"]?></h2>
												<div class="post-place full m20b l">
												<?if($arItem["PROPERTIES"]["CORPUS"]["VALUE"]):?>
													<span>Корпус<?=$arItem["PROPERTIES"]["CORPUS"]["VALUE"]?></span>
													<?else:?>
													<span>Торговые ряды</span>
												<?endif;?>
												<?if($arItem["PROPERTIES"]["ETAG"]["VALUE"]):?>
													<span>Этаж <?=$arItem["PROPERTIES"]["ETAG"]["VALUE"]?></span>
												<?endif;?>
												<?if($arItem["PROPERTIES"]["SECTION"]["VALUE"]):?>
													<span>Секция <?=$arItem["PROPERTIES"]["SECTION"]["VALUE"]?></span>
												<?endif;?>
												</div>
												<?if($arItem["PROPERTIES"]["MAP_LINK"]["VALUE"]):?>
													<a href="<?=$arItem["PROPERTIES"]["MAP_LINK"]["VALUE"]?>" class="btn btn_little"><i class="ic ic-map"></i> На карте</a>
												<?endif;?>
											</div>
										</div>
										<div class="post-content">


											<?if($arItem["PROPERTIES"]["PHONE"]["VALUE"]):?>
											<div class="phones full m20b l ">
												<i class="phones__icon ic ic-phone"></i>
												<?
												$arPhone = array();
												foreach($arItem["PROPERTIES"]["PHONE"]["VALUE"] as $v):
													$vv = preg_replace('/[^0-9]/', '', $v);
													$arPhone[] = '<a href="tel:'.$vv.'">'.$v.'</a>';
												endforeach;
												echo implode("<br />", $arPhone);?>
											</div>
											<?endif;?>

											<div class="post-action">
												<div class="post-action__grid">

													<div class="social">
														<div class="social__wrap">
															<?if($arItem["PROPERTIES"]['SITE_LINK']["VALUE"]):?>
																<a href="<?=$arItem["PROPERTIES"]['SITE_LINK']["VALUE"]?>" class="social-item ic ic-globe"></a>
															<?endif;?>
															<?if($arItem["PROPERTIES"]['INSTA_LINK']["VALUE"]):?>
																<a href="<?=$arItem["PROPERTIES"]['INSTA_LINK']["VALUE"]?>" class="social-item ic ic-instagram"></a>
															<?endif;?>
															<?if($arItem["PROPERTIES"]['VK_LINK']["VALUE"]):?>
																<a href="<?=$arItem["PROPERTIES"]['VK_LINK']["VALUE"]?>" class="social-item ic ic-vk"></a>
															<?endif;?>
														</div>
													</div>
														<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="btn">Страница магазина</a>
												</div>
											</div>
										</div>






	</div>
</div>
<?endforeach;?>
<style>
<?foreach($arResult["ITEMS"] as $arItem):?>
#s<? $text=$arItem['DISPLAY_PROPERTIES']['SECTION']['DISPLAY_VALUE']; $format = preg_replace('/[^0-9]+/', '', $text); echo $format;?>:target{display: block;}
<?endforeach;?>
.plan a:hover path{
	fill:#fffff0;
}
</style>
</div>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
</div>
