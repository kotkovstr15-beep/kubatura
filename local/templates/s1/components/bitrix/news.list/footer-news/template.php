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

use Bitrix\Main\Loader;

	Loader::includeModule("iblock");
	$arSection = array();
	$rsSect = \CIBlockSection::GetList(
		array('ID' => 'ASC'),
		array(
			'IBLOCK_ID' => $arParams['IBLOCK_ID'],
		),
		false,
		array('ID','NAME')
	);
	while($arSect = $rsSect->Fetch())
	{
		$arSection[$arSect['ID']] = $arSect['NAME'];
	}


?>

						<div class="title">
							<h2 class="title__head">Новости</h2>
							<div class="title__grid">
								<div class="title__main">
									<div class="tabs">
										<div class="tabs__grid">
											<a href="<?=$arParams['IBLOCK_URL']?>" class="tabs__item tabs__item_medium active">Все</a>
											<a href="#" class="tabs__item tabs__item_medium">Новости</a>
											<a href="#" class="tabs__item tabs__item_medium">Акции</a>
										</div>
									</div>
								</div>
							</div>
						</div>

<div class="news-inn__grid">

<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	$newsImage = kubaturaPreparedImage($arItem["PREVIEW_PICTURE"], 520, 520, 74, BX_RESIZE_IMAGE_EXACT);
	?>
							<div class="news-inn__col" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
								<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="news-item">
									<img class="news-item__image" src="<?=htmlspecialcharsbx($newsImage["src"])?>" width="<?=$newsImage["width"]?>" height="<?=$newsImage["height"]?>" loading="lazy" alt="<?=htmlspecialcharsbx($arItem["NAME"])?>">
									<?if(array_key_exists($arItem["IBLOCK_SECTION_ID"],$arSection)):?>
									<div class="news-item__label"><?=$arSection[$arItem["IBLOCK_SECTION_ID"]]?> <div class="news-item__line"></div>
									<?endif;?>
									</div>
									<div class="news-item__wrap">
										<div class="news-item__content">
											<p class="news-item__date"><?echo $arItem["DISPLAY_ACTIVE_FROM"]?></p>
											<h3 class="news-item__head"><?echo $arItem["NAME"]?></h3>
										</div>
									</div>
								</a>
							</div>
<?endforeach;?>
</div>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?>
<?endif;?>
