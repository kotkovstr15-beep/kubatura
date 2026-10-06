<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Товары");
global $USER;
global $arrFilter;

$userShopId = getShopIdByUserId($USER->GetID());
if ($userShopId === 0){
    header('Location: /personal/',true,301);
}
$arrFilter = Array(
    "PROPERTY_SHOP" => $userShopId,
);

if (array_key_exists('sort', $_GET)) {
    $sortName = explode('-', $_GET['sort'])[0];
    $sortValue = explode('-', $_GET['sort'])[1];
}

?><?$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"personal.products.info.show",
	Array(
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"ADD_SECTIONS_CHAIN" => "N",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "N",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "1",
		"CACHE_TYPE" => "N",
		"CHECK_DATES" => "N",
		"DETAIL_URL" => "",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_DATE" => "N",
		"DISPLAY_NAME" => "N",
		"DISPLAY_PICTURE" => "N",
		"DISPLAY_PREVIEW_TEXT" => "N",
		"DISPLAY_TOP_PAGER" => "N",
		"FIELD_CODE" => array("ID","CODE","XML_ID","NAME","TAGS","SORT","PREVIEW_TEXT","PREVIEW_PICTURE","DETAIL_TEXT","DETAIL_PICTURE","DATE_ACTIVE_FROM","ACTIVE_FROM","DATE_ACTIVE_TO","ACTIVE_TO","SHOW_COUNTER","SHOW_COUNTER_START","IBLOCK_TYPE_ID","IBLOCK_ID","IBLOCK_CODE","IBLOCK_NAME","IBLOCK_EXTERNAL_ID","DATE_CREATE","CREATED_BY","CREATED_USER_NAME","TIMESTAMP_X","MODIFIED_BY","USER_NAME",""),
		"FILTER_NAME" => "arrFilter",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"IBLOCK_ID" => "7",
		"IBLOCK_TYPE" => "content",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"INCLUDE_SUBSECTIONS" => "N",
		"MESSAGE_404" => "",
		"NEWS_COUNT" => "30",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "1",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "show_more_shop_products",
		"PAGER_TITLE" => "Новости",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"PREVIEW_TRUNCATE_LEN" => "",
		"PROPERTY_CODE" => array("MATERIALY","VESY","SALE_PRICE","PRICE","FILTER_PRICE","NEW","LENGHT","FAVORABLE_PURCHASE","COLOR_SELECT","HEIGHT","GABARITY_SH_V","GABARITY","GABARITYSHD","WARRANTY","DELIVERY","UNIT_OF_MEASURE","ZACHITNYSLOY","KLASS","CODEID","KOLVOMKVYPAK","KOLVOPREDMETOV","USE","POKRYTIE","PRACTICALITY","RAZMER","RAZMER_MM","SALE","SOOTNOSHENIE_SORON","STYLE","DURABILITY","TIP","THICKNESS","FUNCTIONALITY","WIDTH_S","COLOR","MANUFACTURER","NAL","FEATURE","IS_EXPORT","MAGAZIN_ATR_ID","MAGAZIN_ATR_URL",""),
		"SET_BROWSER_TITLE" => "N",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_STATUS_404" => "N",
		"SET_TITLE" => "N",
		"SHOW_404" => "N",
		"SORT_BY1" => $sortName??"ID",
		"SORT_BY2" => "",
		"SORT_ORDER1" => $sortValue??"DESC",
		"SORT_ORDER2" => "",
		"STRICT_SECTION_CHECK" => "N",
		"USE_FILTER" => "Y"
	)
);?><?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>