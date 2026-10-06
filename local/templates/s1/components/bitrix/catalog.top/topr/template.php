<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
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
?>

	<?if(count($arResult["ITEMS"]) > 0): foreach($arResult["ITEMS"] as $arItem):
							$ob = false;
							if($arItem["PROPERTIES"]['SHOP']['VALUE'])
							{
									\Bitrix\Main\Loader::includeModule('iblock');

									$arSort = array('ID' => 'ASC');
									$arFilter = array('=ID' => $arItem["PROPERTIES"]["SHOP"]["VALUE"], 'IBLOCK_ID' => $arItem["PROPERTIES"]["SHOP"]['LINK_IBLOCK_ID']);
									$arSelect = array();
									$res = CIBlockElement::getList($arSort, $arFilter, false, array('nTopCount' => 1), $arSelect);
									if($ob = $res->GetNextElement()):
										$arFields = $ob->GetFields();
										$arProps = $ob->GetProperties();
									endif;
							}

							?>
						<?php $productImage = kubaturaPreparedImage($arItem["PREVIEW_PICTURE"], 520, 360, 76); $productImageMobile = kubaturaPreparedImage($arItem["PREVIEW_PICTURE"], 420, 360, 74); $productAlt = trim($arItem['NAME'] . ' ' . (isset($arFields['NAME']) ? $arFields['NAME'] : '')); ?>
						<div class="swiper-slide">
								<div class="similar-item">
									<div class="labels">
										<?if($arItem["PROPERTIES"]['NEW']['VALUE']):?>
										<div class="label label_blue">
											<div class="label__wrap">Новинка</div>
										</div>
										<?endif;?>
										<?if($arItem["PROPERTIES"]['SALE']['VALUE']):?>
										<div class="label">
											<div class="label__wrap"><?=$arItem["PROPERTIES"]['SALE']['VALUE']?></div>
										</div>
										<?endif;?>
									</div>
									<a href="<?=htmlspecialcharsbx(rtrim($arItem["DETAIL_PAGE_URL"], '/'))?>" class="similar-item__img">
										<div>
											<img src="<?=htmlspecialcharsbx($productImage['src'])?>" srcset="<?=htmlspecialcharsbx($productImageMobile['src'])?> 420w, <?=htmlspecialcharsbx($productImage['src'])?> 520w" sizes="(max-width:575px) 100vw, 520px" width="<?=(int)$productImage['width']?>" height="<?=(int)$productImage['height']?>" loading="lazy" fetchpriority="low" decoding="async" alt="<?=htmlspecialcharsbx($productAlt)?>">
										</div>
									</a>
									<div class="similar-item__content">
											<?if($ob):?>
										<p class="similar-item__category"><a href="<?=$arFields["DETAIL_PAGE_URL"]?>"><?=$arFields["NAME"]?></a></p>
											<?endif;?>
											<h3 class="zag_tx_tv_5">
												<span class="similar-item__head-wrap">
											<a href="<?=htmlspecialcharsbx(rtrim($arItem["DETAIL_PAGE_URL"], '/'))?>" class="link-head"><?=$arItem["NAME"]?></a>
												</span>
											</h3>
										<div class="price">
											<? if($arItem["PROPERTIES"]['SALE_PRICE']['VALUE']):?><p class="price__old"><?=$arItem["PROPERTIES"]['SALE_PRICE']['VALUE'] ? $arItem["PROPERTIES"]['SALE_PRICE']['VALUE'] ." ₽": "&nbsp;"?></p>
											<?endif;?><p class="price__new"><?=$arItem["PROPERTIES"]['PRICE']['VALUE'] ? $arItem["PROPERTIES"]['PRICE']['VALUE'] ." ₽": "&nbsp;"?></p>
										</div>
									</div>
								</div>
							</div>
<?endforeach;	endif;?>
