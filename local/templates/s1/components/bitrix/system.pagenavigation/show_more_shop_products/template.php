<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->createFrame()->begin("Загрузка навигации");
?>

<?if($arResult["NavPageCount"] > 1):?>

    <?if ($arResult["NavPageNomer"]+1 <= $arResult["nEndPage"]):?>
        <?
            $plus = $arResult["NavPageNomer"]+1;
            $url = $arResult["sUrlPathParams"] . "PAGEN_".$arResult["NavNum"]."=".$plus;
        ?>
		<div class="action-box">
			<a href="#" class="btn" data-url="<?=$url?>">Загрузить еще</a>
		</div>
    <?else:?>

        <!--<div class="load_more">
            Загружено все
        </div>-->

    <?endif?>

<?
if(!$arResult["NavShowAlways"])
{
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");
?>

<div class="pagination"><div class="pagination__wrap">

<?if($arResult["bDescPageNumbering"] === true):?>

<?else:?>

	<?if ($arResult["NavPageNomer"] > 1):?>

		<?if($arResult["bSavePage"]):?>
			<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>" class="pagination__prev"><i class="ic ic-prev"></i></a>
		<?else:?>
			<?if ($arResult["NavPageNomer"] > 2):?>
				<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>" class="pagination__prev"><i class="ic ic-prev"></i></a>
			<?else:?>
				<a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>" class="pagination__prev"><i class="ic ic-prev"></i></a>
			<?endif?>
		<?endif?>

	<?else:?>
		<a href="#" class="pagination__prev disable"><i class="ic ic-prev"></i></a>
	<?endif?>
	<ul class="pagination__list">
	<?
			if ($arResult["NavPageNomer"] > 1):
				if ($arResult["nStartPage"] > 1):
					if($arResult["bSavePage"]):
						?><li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=1" class="pagination__item">1</a></li><?
					else:
						?><li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>" class="pagination__item">1</a></li><?
					endif;

					if ($arResult["nStartPage"] > 2):
						?><li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=round($arResult["nStartPage"] / 2)?>" class="pagination__item">...</a></li><?
					endif;
				endif;
			endif;

			do
			{
				if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):
					?><li><span class="pagination__item active"><?=$arResult["nStartPage"]?></span></li><?
				elseif($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false):
					?><li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>" class="pagination__item"><?=$arResult["nStartPage"]?></a></li><?
				else:
					?><li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>" class="pagination__item"><?=$arResult["nStartPage"]?></a></li><?
				endif;
				$arResult["nStartPage"]++;
			}

			while($arResult["nStartPage"] <= $arResult["nEndPage"]);

			if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):
				if ($arResult["nEndPage"] < $arResult["NavPageCount"]):
					if ($arResult["nEndPage"] < ($arResult["NavPageCount"] - 1)):
						?><li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=round($arResult["nEndPage"] + ($arResult["NavPageCount"] - $arResult["nEndPage"]) / 2)?>" class="pagination__item">...</a></li><?
					endif;
					?><li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["NavPageCount"]?>" class="pagination__item"><?=$arResult["NavPageCount"]?></a></li><?
				endif;
			endif;
	?>
	</ul>

	<?if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):?>
		<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>" class="pagination__next"><i class="ic ic-next"></i></a>
	<?else:?>
		<a href="#" class="pagination__next disable"><i class="ic ic-next"></i></a>
	<?endif?>

<?endif?>

</div></div>

<?endif?>