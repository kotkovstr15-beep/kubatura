<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Личный кабинет");
global $USER;
if (!$USER->IsAuthorized()) {
    header("Location: /login");
}
$shopId = getShopIdByUserId($USER->GetID());
/** Арендатор */
if ($shopId > 0):?>
    <? $APPLICATION->IncludeComponent(
        "bitrix:news.detail",
        "personal.shop.info.show",
        array(
            "ACTIVE_DATE_FORMAT" => "d.m.Y",
            "ADD_ELEMENT_CHAIN" => "N",
            "ADD_SECTIONS_CHAIN" => "N",
            "AJAX_MODE" => "N",
            "AJAX_OPTION_ADDITIONAL" => "",
            "AJAX_OPTION_HISTORY" => "N",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "Y",
            "BROWSER_TITLE" => "-",
            "CACHE_GROUPS" => "Y",
            "CACHE_TIME" => "1",
            "CACHE_TYPE" => "N",
            "CHECK_DATES" => "Y",
            "DETAIL_URL" => "",
            "DISPLAY_BOTTOM_PAGER" => "N",
            "DISPLAY_DATE" => "Y",
            "DISPLAY_NAME" => "Y",
            "DISPLAY_PICTURE" => "Y",
            "DISPLAY_PREVIEW_TEXT" => "Y",
            "DISPLAY_TOP_PAGER" => "N",
            "ELEMENT_CODE" => "",
            "ELEMENT_ID" => $shopId,
            "FIELD_CODE" => array(
                "ID",
                "CODE",
                "XML_ID",
                "NAME",
                "TAGS",
                "SORT",
                "PREVIEW_TEXT",
                "PREVIEW_PICTURE",
                "DETAIL_TEXT",
                "DETAIL_PICTURE",
                "DATE_ACTIVE_FROM",
                "ACTIVE_FROM",
                "DATE_ACTIVE_TO",
                "ACTIVE_TO",
                "SHOW_COUNTER",
                "SHOW_COUNTER_START",
                "IBLOCK_TYPE_ID",
                "IBLOCK_ID",
                "IBLOCK_CODE",
                "IBLOCK_NAME",
                "IBLOCK_EXTERNAL_ID",
                "DATE_CREATE",
                "CREATED_BY",
                "CREATED_USER_NAME",
                "TIMESTAMP_X",
                "MODIFIED_BY",
                "USER_NAME",
                ""
            ),
            "FILTER_NAME" => "arrFilter",
            "IBLOCK_ID" => "6",
            "IBLOCK_TYPE" => "content",
            "IBLOCK_URL" => "",
            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
            "MESSAGE_404" => "",
            "META_DESCRIPTION" => "-",
            "META_KEYWORDS" => "-",
            "PAGER_BASE_LINK_ENABLE" => "N",
            "PAGER_SHOW_ALL" => "N",
            "PAGER_TEMPLATE" => ".default",
            "PAGER_TITLE" => "Страница",
            "PROPERTY_CODE" => array(
                "CATEGORY_FILTER",
                "CORPUS_FILTER",
                "CORPUS",
                "ETAG",
                "OWNER",
                "GO_HOME",
                "SECTION",
                "PHONE",
                "MAP_LINK",
                "SITE_LINK",
                "INSTA_LINK",
                "VK_LINK",
                ""
            ),
            "SET_BROWSER_TITLE" => "N",
            "SET_CANONICAL_URL" => "N",
            "SET_LAST_MODIFIED" => "N",
            "SET_META_DESCRIPTION" => "Y",
            "SET_META_KEYWORDS" => "Y",
            "SET_STATUS_404" => "N",
            "SET_TITLE" => "N",
            "SHOW_404" => "N",
            "STRICT_SECTION_CHECK" => "N",
            "USE_FILTER" => "Y",
            "USE_PERMISSIONS" => "N",
            "USE_SHARE" => "N"
        )
    ); ?>
<?php
/** Админ */
elseif ($USER->IsAdmin()): ?>
    <? $APPLICATION->IncludeComponent(
        "bitrix:main.include",
        "",
        array(
            "AREA_FILE_SHOW" => "file",
            "AREA_FILE_SUFFIX" => "inc",
            "EDIT_TEMPLATE" => "",
            "PATH" => "/include/personal.shop.isAdmin.php"
        )
    ); ?>
<?php
/** Не арендатор и не админ */
else: ?>
    <h2>К вам не привязан магазин.</h2>
<?php endif;
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>