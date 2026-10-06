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
?>
							<div class="filter">
								<div class="filter__top">
									<div class="filter__top-grid">
										<button class="filter__toggle btn btn_toggle"><i class="ic ic-chevron-bottom"></i> Категории</button>
										<div class="filter-selected">
											<ul class="filter-selected__list">
												<!--<li class="filter-selected__item">Плитка <a href="#" class="filter-selected__del"><i class="ic ic-close"></i></a></li>-->
											</ul>
										</div>
									</div>
								</div>
								<div class="filter__wrap">

									<div class="filter-cats">
										<div class="filter-cats__grid" data-select='<?=$arResult["ITEMS"]["PROPERTY_5"]["INPUT"]?>'>

											<a href="<?=$arParams["IBLOCK_URL"]?>" class="filter-cats__item all active">Все</a>
											<!--<a href="#" class="filter-cats__item active">Плитка</a>-->

										</div>
									</div>

								</div>
							</div>

							<div class="title__grid">
								<div class="title__main">
									<div class="tabs">
										<div class="tabs__grid" data-select='<?=$arResult["ITEMS"]["PROPERTY_6"]["INPUT"]?>'>
											<a href="#" class="tabs__item tabs__item_small active">Все</a>
											<!--<a href="#" class="tabs__item">1 КОРПУС 1 ЭТАЖ</a>-->
										</div>
									</div>
								</div>
							</div>

<?/*<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get" hidden>
	<?foreach($arResult["ITEMS"] as $arItem):
		if(array_key_exists("HIDDEN", $arItem)):
			echo $arItem["INPUT"];
		endif;
	endforeach;?>
	<table class="data-table" cellspacing="0" cellpadding="2">
	<thead>
		<tr>
			<td colspan="2" align="center"><?=GetMessage("IBLOCK_FILTER_TITLE")?></td>
		</tr>
	</thead>
	<tbody>
		<?foreach($arResult["ITEMS"] as $arItem):?>
			<?if(!array_key_exists("HIDDEN", $arItem)):?>
				<tr>
					<td valign="top"><?=$arItem["NAME"]?>:</td>
					<td valign="top"><?=$arItem["INPUT"]?></td>
				</tr>
			<?endif?>
		<?endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2">
				<input type="submit" name="set_filter" value="<?=GetMessage("IBLOCK_SET_FILTER")?>" /><input type="hidden" name="set_filter" value="Y" />&nbsp;&nbsp;<input type="submit" name="del_filter" value="<?=GetMessage("IBLOCK_DEL_FILTER")?>" /></td>
		</tr>
	</tfoot>
	</table>
</form>*/?>