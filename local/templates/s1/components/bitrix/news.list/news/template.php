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
<?
						// https://getbootstrap.ru/docs/v4-alpha/components/navs/
						// https://swiperjs.com/api/
						?>
						<?if(count($arResult["ITEMS"]) > 5):?>
						<div class="title__nav">
							<div class="nwn news-nav swiper-nav">
								<div class="swiper-button-prev"><i class="ic ic-prev"></i></div>
								<div class="swiper-button-next"><i class="ic ic-next"></i></div>
							</div>
						</div>
						<?endif;?>

					<div class="nw news__slider swiper-container">
					<div class="swiper-wrapper">
<?

$arItem = $arResult["ITEMS"][0];
unset($arResult["ITEMS"][0]);

$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
$newsImage = kubaturaPreparedImage($arItem["PREVIEW_PICTURE"], 560, 560, 74, BX_RESIZE_IMAGE_EXACT);
?>
								<div class="swiper-slide swiper-slide_big">
									<a href="<?echo $arItem["DETAIL_PAGE_URL"]?>" class="news-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
										<img class="news-item__image" src="<?=htmlspecialcharsbx($newsImage["src"])?>" width="<?=$newsImage["width"]?>" height="<?=$newsImage["height"]?>" loading="lazy" alt="<?=htmlspecialcharsbx($arItem["NAME"])?>">
										<div class="news-item__label">
										<?
										if($arItem["IBLOCK_SECTION_ID"] == 1):
										?>
										Новости
										<?endif;?>
										<?
										if($arItem["IBLOCK_SECTION_ID"] == 2):
										?>
										Акции
										<?endif;?>
										<div class="news-item__line"></div>
										</div>
										<div class="news-item__wrap">
											<div class="news-item__content">
												<p class="news-item__date"><?echo $arItem["DISPLAY_ACTIVE_FROM"]?></p>
												<h3 class="zag_tx_ns_2 news-item__head"><?echo $arItem["NAME"]?></h3>
											</div>
										</div>
									</a>
								</div>
<?

$arResult["ITEMS"] = array_chunk($arResult["ITEMS"], 2);

foreach($arResult["ITEMS"] as $Items):?>
	<div class="swiper-slide swiper-slide_small">
	<?foreach($Items as  $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	$newsImage = kubaturaPreparedImage($arItem["PREVIEW_PICTURE"], 520, 520, 74, BX_RESIZE_IMAGE_EXACT);
	?>
									<a href="<?echo $arItem["DETAIL_PAGE_URL"]?>" class="news-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
										<img class="news-item__image" src="<?=htmlspecialcharsbx($newsImage["src"])?>" width="<?=$newsImage["width"]?>" height="<?=$newsImage["height"]?>" loading="lazy" alt="<?=htmlspecialcharsbx($arItem["NAME"])?>">
										<div class="news-item__label">
										<?
										if($arItem["IBLOCK_SECTION_ID"] == 1):
										?>
										Новости
										<?endif;?>
										<?
										if($arItem["IBLOCK_SECTION_ID"] == 2):
										?>
										Акции
										<?endif;?>
										<div class="news-item__line"></div>
										</div>
										<div class="news-item__wrap">
											<div class="news-item__content">
												<p class="news-item__date"><?echo $arItem["DISPLAY_ACTIVE_FROM"]?></p>
												<h3 class="zag_tx_ns_2 news-item__head"><?echo $arItem["NAME"]?></h3>
											</div>
										</div>
									</a>
	<?endforeach;?>
	</div>
<?endforeach;?>
</div>
</div>
