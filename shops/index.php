<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Каталог магазинов ЦентрадДизайна интерьера и ремонта Кубатура");
$APPLICATION->SetPageProperty("keywords", "Кубатура, стройматериалы в Самаре, строительно-отделочные материалы, отделочные материалы, товары для ремонта, отделочные работы, центр строительства и ремонта, сантехника, доски, загородный дом, панели, краска, штукатурка, смеси, цемент, двери, паркет");
$APPLICATION->SetPageProperty("title", "Магазины Центра дизайна интерьера и ремонта Кубатура");
$APPLICATION->SetTitle("Магазины Центра дизайна интерьера и ремонта Кубатура");
?><?php
// Детальная карточка магазина обслуживается отдельным серверным шаблоном,
// чтобы порядок блоков и SEO-мета не зависели от стандартного news.detail.
$shopPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (preg_match('~^/shops/([^/.]+)\.html$~', (string)$shopPath, $shopMatch)
    && \Bitrix\Main\Loader::includeModule('iblock')) {
    $shop = \Kubatura\ShopPage\Service::shop($shopMatch[1]);
    if ($shop) {
        $data = \Kubatura\ShopPage\Service::data($shop);
        require $_SERVER['DOCUMENT_ROOT'].'/local/php_interface/include/shop-page/view.php';
        require $_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php';
        return;
    }
}
if (\Bitrix\Main\Loader::includeModule('iblock') && \Kubatura\ShopPage\shopPaginationIsInvalid()) {
    \Kubatura\ShopPage\notFound();
    require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
    return;
}
?><?$APPLICATION->IncludeComponent(
	"bitrix:news", 
	"shops", 
	array(
		"ADD_ELEMENT_CHAIN" => "Y",
		"ADD_SECTIONS_CHAIN" => "N",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "N",
		"BROWSER_TITLE" => "-",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "N",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "N",
		"CHECK_DATES" => "Y",
		"COMPONENT_TEMPLATE" => "shops",
		"DETAIL_ACTIVE_DATE_FORMAT" => "d.m.Y",
		"DETAIL_DISPLAY_BOTTOM_PAGER" => "Y",
		"DETAIL_DISPLAY_TOP_PAGER" => "N",
		"DETAIL_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"DETAIL_PAGER_SHOW_ALL" => "Y",
		"DETAIL_PAGER_TEMPLATE" => "",
		"DETAIL_PAGER_TITLE" => "Страница",
		"DETAIL_PROPERTY_CODE" => array(
			0 => "CATEGORY_FILTER",
			1 => "CORPUS_FILTER",
			2 => "CORPUS",
			3 => "ETAG",
			4 => "SECTION",
			5 => "PHONE",
			6 => "MAP_LINK",
			7 => "SITE_LINK",
			8 => "INSTA_LINK",
			9 => "VK_LINK",
			10 => "ANALOG_SHOPS",
			11 => "",
		),
		"DETAIL_SET_CANONICAL_URL" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"FILTER_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"FILTER_NAME" => "",
		"FILTER_PROPERTY_CODE" => array(
			0 => "CATEGORY_FILTER",
			1 => "CORPUS_FILTER",
			2 => "",
		),
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"IBLOCK_ID" => "6",
		"IBLOCK_TYPE" => "content",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"LIST_ACTIVE_DATE_FORMAT" => "d.m.Y",
		"LIST_FIELD_CODE" => array(
			0 => "ID",
			1 => "NAME",
			2 => "PREVIEW_PICTURE",
			3 => "",
		),
		"LIST_PROPERTY_CODE" => array(
			0 => "CATEGORY_FILTER",
			1 => "CORPUS_FILTER",
			2 => "CORPUS",
			3 => "ETAG",
			4 => "SECTION",
			5 => "PROPERTY_LOGO_1",
			6 => "",
		),
		"MESSAGE_404" => "",
		"META_DESCRIPTION" => "-",
		"META_KEYWORDS" => "-",
		"NEWS_COUNT" => "20",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "show_more_shops",
		"PAGER_TITLE" => "Новости",
		"PREVIEW_TRUNCATE_LEN" => "",
		"SEF_MODE" => "Y",
		"SET_LAST_MODIFIED" => "N",
		"SET_STATUS_404" => "Y",
		"SET_TITLE" => "Y",
		"SHOW_404" => "N",
		"SORT_BY1" => "SORT",
		"SORT_BY2" => "SORT",
		"SORT_ORDER1" => "ASC",
		"SORT_ORDER2" => "ASC",
		"STRICT_SECTION_CHECK" => "Y",
		"USE_CATEGORIES" => "N",
		"USE_FILTER" => "Y",
		"USE_PERMISSIONS" => "N",
		"USE_RATING" => "N",
		"USE_RSS" => "N",
		"USE_SEARCH" => "N",
		"USE_SHARE" => "N",
		"SEF_FOLDER" => "/shops/",
		"SEF_URL_TEMPLATES" => array(
			"news" => "",
			"section" => "#SECTION_CODE#/",
			"detail"  => "#ELEMENT_CODE#.html",
		)
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
