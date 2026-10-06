<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */

use Bitrix\Main\Context,
	Bitrix\Main\Type\DateTime,
	Bitrix\Main\Loader,
	Bitrix\Main,
	Bitrix\Iblock;

	if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
	{
		$arrFilter = array();
	}
	else
	{
		$arrFilter = $GLOBALS[$arParams["FILTER_NAME"]];
		if(!is_array($arrFilter))
			$arrFilter = array();
	}

	$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"]=="Y";
	$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"]!="N";
	$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
	$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"]=="Y";
	$arParams["PAGER_TEMPLATE"] = 'show_more_shops';
	$arParams["PAGER_DESC_NUMBERING"] = $arParams["PAGER_DESC_NUMBERING"]=="Y";
	$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] = intval($arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]);
	$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"]=="Y";

	$arNavParams = array(
		"nPageSize" => 8,
		"bDescPageNumbering" => false,
		"bShowAll" => false,
	);
	$arNavigation = CDBResult::GetNavParams($arNavParams);
	$arParams["CACHE_TIME"] = 3600;
	$arParams["CACHE_TYPE"] = "A";
	$arParams["CACHE_GROUPS"] = "N";

	$pagerParameters = array();

if($this->startResultCache(false, array(false, false, $arNavigation, $arrFilter, $pagerParameters)))
{
	if(!Loader::includeModule("iblock"))
	{
		$this->abortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}

	$arResult["ITEMS"] = array();
	$arResult["ELEMENTS"] = array();
	$arFilter = array (
		"IBLOCK_ID" => 6,
		"IBLOCK_LID" => SITE_ID,
		"ACTIVE" => "Y",
		"!PROPERTY_GO_HOME"=>false,
	);

	$allElement = CIBlockElement::GetList(array("ID"=>"ASC"), $arFilter, false, false, array('ID'));
	$all = intval($allElement->SelectedRowsCount());

	$rsElement = CIBlockElement::GetList(array("SORT"=>"ASC"), $arFilter, false, $arNavParams, array());
	while($obElement = $rsElement->GetNextElement())
	{
		$arItem = $obElement->GetFields();
		$arItem["PROPERTIES"] = $obElement->GetProperties();

		$arResult["ITEMS"][] = $arItem;
		$arResult["ELEMENTS"][] = $arItem["ID"];
	}

	$navComponentParameters = array();
	$arResult["NAV_STRING"] = $rsElement->GetPageNavStringEx(
		$navComponentObject,
		$arParams["PAGER_TITLE"],
		$arParams["PAGER_TEMPLATE"],
		$arParams["PAGER_SHOW_ALWAYS"],
		$this,
		$navComponentParameters
	);
	$arResult["NAV_CACHED_DATA"] = null;
	$arResult["NAV_RESULT"] = $rsElement;
	$arResult["NAV_PARAM"] = $navComponentParameters;
	$arResult["ALL"] = $all;

	$this->includeComponentTemplate();
}

//$this->setResultCacheKeys(array("LAST_ITEM_IBLOCK_ID",));
