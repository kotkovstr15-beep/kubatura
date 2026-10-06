<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
	<?foreach($arResult as $arItem):?>
		<li><a href="<?=$arItem["LINK"]?>" class="menu__link"><?=$arItem["TEXT"]?></a></li>
	<?endforeach;?>
<?endif?>