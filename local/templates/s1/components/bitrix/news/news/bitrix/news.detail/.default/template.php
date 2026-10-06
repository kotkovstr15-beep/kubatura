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
?> <?if(is_array($arResult["DETAIL_PICTURE"])):?>
<div class="post-img">
	<div class="post-img__wrap" style="background-image: url('&lt;span id=" bxid143965189"="" title="Код PHP: &lt;?=$arResult[&quot;DETAIL_PICTURE&quot;][&quot;SRC&quot;]?&gt;">
		 <?=$arResult["DETAIL_PICTURE"]["SRC"]?><span class="bxhtmled-surrogate-inner"><span class="bxhtmled-right-side-item-icon"></span><span class="bxhtmled-comp-lable" unselectable="on" spellcheck="false">Код PHP</span></span>');"&gt;
	</div>
</div>
 <?endif;?> <?if(!empty($arResult["PROPERTIES"]["SHOW_SHOP_INFO"]["VALUE"]) && !empty($arResult["PROPERTIES"]["SHOP_INFO"]["VALUE"])):?><?
									
									\Bitrix\Main\Loader::includeModule('iblock');
									
									$arSort = array('ID' => 'ASC');
									$arFilter = array('=ID' => $arResult["PROPERTIES"]["SHOP_INFO"]["VALUE"], 'IBLOCK_ID' => $arResult["PROPERTIES"]["SHOP_INFO"]['LINK_IBLOCK_ID']);
									$arSelect = array();
									$res = CIBlockElement::getList($arSort, $arFilter, false, array('nTopCount' => 1), $arSelect);
									if($ob = $res->GetNextElement()):
										$arFields = $ob->GetFields();
										$arProps = $ob->GetProperties();
								?>
<div class="post-info">
	 <?if($arProps['LOGO_2']["VALUE"]):?><?
										$file = CFile::ResizeImageGet($arProps['LOGO_2']["VALUE"], array('width'=>220, 'height'=>220), BX_RESIZE_IMAGE_PROPORTIONAL, false, array(), false, 100);
										if(!$file['src'])
											$file['src'] = CFile::GetPath($arProps['LOGO_2']["VALUE"]);
									?> <a href="<?=$arFields['DETAIL_PAGE_URL']?>" class="post-info__img"> <img src="<?=$file['src']?>" alt=""> </a>
	<?endif;?>
	<div class="post-info__main">
		<h5 class="post-info__head"><a href="<?=$arFields['DETAIL_PAGE_URL']?>"><?=$arFields['NAME']?></a></h5>
		<div class="post-place">
			 <?if($arProps['CORPUS']["VALUE"]):?> <span>Корпус <?=$arProps['CORPUS']["VALUE"]?></span>
			<?endif;?> <?if($arProps['ETAG']["VALUE"]):?> <span>Этаж <?=$arProps['ETAG']["VALUE"]?></span>
			<?endif;?> <?if($arProps['SECTION']["VALUE"]):?> <span>Секция <?=$arProps['SECTION']["VALUE"]?></span>
			<?endif;?>
		</div>
		 <?if($arProps['SECTION']["VALUE"]):?> <a href="&lt;?=$arProps['MAP_LINK'][" value"]?="">" data-bx-app-ex-href="<?=$arProps['MAP_LINK']["VALUE"]?> " class="btn btn_little"&gt;<i class="ic ic-map"></i> На карте</a>
		<?endif;?>
	</div>
	<div class="social">
		<div class="social__wrap">
			 <?if($arProps['SITE_LINK']["VALUE"]):?> <a href="&lt;?=$arProps['SITE_LINK'][" value"]?="">" data-bx-app-ex-href="<?=$arProps['SITE_LINK']["VALUE"]?> " class="social-item ic ic-globe"&gt;</a>
			<?endif;?> <?if($arProps['INSTA_LINK']["VALUE"]):?> <a href="&lt;?=$arProps['INSTA_LINK'][" value"]?="">" data-bx-app-ex-href="<?=$arProps['INSTA_LINK']["VALUE"]?> " class="social-item ic ic-instagram"&gt;</a>
			<?endif;?> <?if($arProps['VK_LINK']["VALUE"]):?> <a href="&lt;?=$arProps['VK_LINK'][" value"]?="">" data-bx-app-ex-href="<?=$arProps['VK_LINK']["VALUE"]?> " class="social-item ic ic-vk"&gt;</a>
			<?endif;?>
		</div>
	</div>
</div>
 <?endif;?> <?endif;?>
<div class="post-content">
	<div class="post-title">
		<p class="post-title__date">
			 <?=$arResult["DISPLAY_ACTIVE_FROM"]?>
		</p>
		<h1 class="post-title__head"><?=$arResult["NAME"]?></h1>
	</div>
	<div class="content">
		 <?=$arResult["DETAIL_TEXT"]?>
	</div>
</div>
<?/*<div class="news-detail">
	<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["DETAIL_PICTURE"])):?>
		<img
			class="detail_picture"
			border="0"
			src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>"
			width="<?=$arResult["DETAIL_PICTURE"]["WIDTH"]?>"
			height="<?=$arResult["DETAIL_PICTURE"]["HEIGHT"]?>"
			alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>"
			title="<?=$arResult["DETAIL_PICTURE"]["TITLE"]?>"
			/>
	<?endif?>
	<?if($arParams["DISPLAY_DATE"]!="N" && $arResult["DISPLAY_ACTIVE_FROM"]):?>
		<span class="news-date-time"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></span>
	<?endif;?>
	<?if($arParams["DISPLAY_NAME"]!="N" && $arResult["NAME"]):?>
		<h3><?=$arResult["NAME"]?></h3>
	<?endif;?>
	<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arResult["FIELDS"]["PREVIEW_TEXT"]):?>
		<p><?=$arResult["FIELDS"]["PREVIEW_TEXT"];unset($arResult["FIELDS"]["PREVIEW_TEXT"]);?></p>
	<?endif;?>
	<?if($arResult["NAV_RESULT"]):?>
		<?if($arParams["DISPLAY_TOP_PAGER"]):?><?=$arResult["NAV_STRING"]?><br /><?endif;?>
		<?echo $arResult["NAV_TEXT"];?>
		<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?><br /><?=$arResult["NAV_STRING"]?><?endif;?>
	<?elseif(strlen($arResult["DETAIL_TEXT"])>0):?>
		<?echo $arResult["DETAIL_TEXT"];?>
	<?else:?>
		<?echo $arResult["PREVIEW_TEXT"];?>
	<?endif?>
	<div style="clear:both"></div>
	<br />
	<?foreach($arResult["FIELDS"] as $code=>$value):
		if ('PREVIEW_PICTURE' == $code || 'DETAIL_PICTURE' == $code)
		{
			?><?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?
			if (!empty($value) && is_array($value))
			{
				?><img border="0" src="<?=$value["SRC"]?>" width="<?=$value["WIDTH"]?>" height="<?=$value["HEIGHT"]?>"><?
			}
		}
		else
		{
			?><?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?=$value;?><?
		}
		?><br />
	<?endforeach;
	foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>

		<?=$arProperty["NAME"]?>:&nbsp;
		<?if(is_array($arProperty["DISPLAY_VALUE"])):?>
			<?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
		<?else:?>
			<?=$arProperty["DISPLAY_VALUE"];?>
		<?endif?>
		<br />
	<?endforeach;
	if(array_key_exists("USE_SHARE", $arParams) && $arParams["USE_SHARE"] == "Y")
	{
		?>
		<div class="news-detail-share">
			<noindex>
			<?
			$APPLICATION->IncludeComponent("bitrix:main.share", "", array(
					"HANDLERS" => $arParams["SHARE_HANDLERS"],
					"PAGE_URL" => $arResult["~DETAIL_PAGE_URL"],
					"PAGE_TITLE" => $arResult["~NAME"],
					"SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
					"SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
					"HIDE" => $arParams["SHARE_HIDE"],
				),
				$component,
				array("HIDE_ICONS" => "Y")
			);
			?>
			</noindex>
		</div>
		

<?
	}
	?>
</div>*/?>