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
					<div class="banner__main">
						<div class="banner__slider swiper-container swiper-nav-hidden">
							<div class="swiper-wrapper">
							<?$bannerIndex = 0; foreach($arResult["ITEMS"] as $arItem):?>
								<?
								$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
								$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
								$bannerImage = kubaturaPreparedImage($arItem["DETAIL_PICTURE"], 1400, 788, 74, BX_RESIZE_IMAGE_EXACT);
								$bannerImageMobile = kubaturaPreparedImage($arItem["DETAIL_PICTURE"], 768, 700, 72, BX_RESIZE_IMAGE_EXACT);
								$isFirstBanner = ($bannerIndex === 0);
								$bannerAlt = trim(strip_tags($arItem['PREVIEW_TEXT'] ?: $arItem['DETAIL_TEXT'] ?: $arItem['NAME']));
								?>
								<div class="banner-item swiper-slide">
									<?if($isFirstBanner):?>
									<img src="<?=htmlspecialcharsbx($bannerImage["src"])?>" srcset="<?=htmlspecialcharsbx($bannerImageMobile["src"])?> 768w, <?=htmlspecialcharsbx($bannerImage["src"])?> 1400w" sizes="100vw" width="<?=(int)$bannerImage["width"]?>" height="<?=(int)$bannerImage["height"]?>" loading="eager" fetchpriority="high" decoding="sync" alt="<?=htmlspecialcharsbx($bannerAlt)?>">
									<?else:?>
									<img class="swiper-lazy" data-src="<?=htmlspecialcharsbx($bannerImage["src"])?>" data-srcset="<?=htmlspecialcharsbx($bannerImageMobile["src"])?> 768w, <?=htmlspecialcharsbx($bannerImage["src"])?> 1400w" sizes="100vw" width="<?=(int)$bannerImage["width"]?>" height="<?=(int)$bannerImage["height"]?>" decoding="async" alt="<?=htmlspecialcharsbx($bannerAlt)?>">
									<noscript><img src="<?=htmlspecialcharsbx($bannerImageMobile["src"])?>" width="<?=(int)$bannerImageMobile["width"]?>" height="<?=(int)$bannerImageMobile["height"]?>" alt="<?=htmlspecialcharsbx($bannerAlt)?>"></noscript>
									<?endif;?>

									<div class="banner-item__wrapper">
										<div class="container">
											<div class="banner-item__wrap" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
												<div class="banner-item__title">
													<p class="h1"><?=$arItem["PREVIEW_TEXT"]?></p>
												</div>
												<div class="banner-item__content">
													<p class="h2"><?=$arItem["DETAIL_TEXT"]?></p>
													<?if($arItem["PROPERTIES"]["URL"]['VALUE']):?>
														<a href="<?=$arItem["PROPERTIES"]["URL"]['VALUE']?>" class="btn btn_big"><?=$arItem["PROPERTIES"]["TEXT_URL"]['VALUE']?></a>
													<?endif;?>
												</div>
											</div>
										</div>
									</div>
								</div>
								<?$bannerIndex++;?>
							<?endforeach;?>
							</div>
							<div class="swiper-button-prev"><i class="ic ic-prev"></i></div>
							<div class="swiper-button-next"><i class="ic ic-next"></i></div>
						</div>
						<div class="banner__thumbs swiper-container">
							<div class="swiper-wrapper">
							<?foreach($arResult["ITEMS"] as $arItem):?>
								<?$bannerThumb = kubaturaPreparedImage($arItem["PREVIEW_PICTURE"], 1000, 600, 90, BX_RESIZE_IMAGE_PROPORTIONAL); $bannerAlt = trim(strip_tags($arItem['PREVIEW_TEXT'] ?: $arItem['DETAIL_TEXT'] ?: $arItem['NAME']));?>
								<div class="banner-thumb swiper-slide">
									<img src="<?=htmlspecialcharsbx($bannerThumb["src"])?>" width="<?=(int)$bannerThumb["width"]?>" height="<?=(int)$bannerThumb["height"]?>" loading="lazy" decoding="async" alt="<?=htmlspecialcharsbx($bannerAlt)?>">
								</div>
							<?endforeach;?>
							</div>
						</div>
						<div class="banner__pagination">
							<div class="swiper-pagination"></div>
						</div>
					</div>
