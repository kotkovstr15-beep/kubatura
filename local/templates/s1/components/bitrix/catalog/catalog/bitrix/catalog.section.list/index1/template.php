<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<div class="categories">
	<?foreach($arResult as $arItem):?>
							<div class="categories__col">
								<div class="category">
									<div class="category__main">
										<div class="zag_tx_4"><a href="<?=htmlspecialcharsbx(rtrim($arItem["SECTION_PAGE_URL"], '/').'/')?>" class="link-head"><?=$arItem["NAME"]?></a></div>
										<table class="category__specs">
										<?foreach($arItem["CHILDREN"] as $arChild):?>
											<tr>
												<td><a class="title" href="<?=htmlspecialcharsbx(rtrim($arChild["SECTION_PAGE_URL"], '/').'/')?>"><?=$arChild["NAME"]?></a></td>
												<td><a href="<?=htmlspecialcharsbx(rtrim($arChild["SECTION_PAGE_URL"], '/').'/')?>" rel="nofollow"><?=$arChild["ELEMENT_CNT"]?></a></td>
											</tr>
											<?endforeach;?>
										</table>
									</div>
									<a href="<?=htmlspecialcharsbx(rtrim($arItem["SECTION_PAGE_URL"], '/').'/')?>" class="category__img">
										<img src="<?=htmlspecialcharsbx($arItem["PICTURE"]['SRC'])?>" width="<?=(int)$arItem["PICTURE"]['WIDTH']?>" height="<?=(int)$arItem["PICTURE"]['HEIGHT']?>" loading="lazy" alt="<?=htmlspecialcharsbx($arItem["PICTURE"]['ALT'] ?: $arItem["NAME"])?>">
									</a>
								</div>
							</div>
	<?endforeach;?>
</div>
<?endif?>
<div class="sm-padding">
<? 
$dbResult = CIBlock::GetByID($arParams['IBLOCK_ID']); 
if($arIBlock = $dbResult->GetNext()) {
  echo $arIBlock['DESCRIPTION'];
}
?>
</div>
