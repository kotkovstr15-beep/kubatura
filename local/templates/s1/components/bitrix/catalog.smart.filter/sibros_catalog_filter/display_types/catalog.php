<div class="filter-main__col">
    <?
    \Bitrix\Main\Loader::includeModule('iblock');
    $section_id = $arParams['SECTION_ID'];
    $section_code = '';
    $arFilter = array("IBLOCK_ID" => $arParams["IBLOCK_ID"]);
    if ($section_id) {
        $arFilter["ID"] = $section_id;
    } else {
        if ($arResult["VARIABLES"]["SECTION_CODE"]) {
            $arFilter["CODE"] = $arResult["VARIABLES"]["SECTION_CODE"];
        }
    }
    $db_list = CIBlockSection::GetList(
        Array('ID' => 'ASC'),
        $arFilter,
        false,
        array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "CODE")
    );
    if ($arItem = $db_list->Fetch()) {
        if ($arItem['IBLOCK_SECTION_ID']) {
            $section_id = $arItem['IBLOCK_SECTION_ID'];
            $section_code = '';
        }
    }
    ?>
    <? $APPLICATION->IncludeComponent(
        "bitrix:catalog.section.list",
        "sibros_catalog_menu",
        array(
            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "SECTION_ID" => $section_id,//$arResult["VARIABLES"]["SECTION_ID"],
            "SECTION_CODE" => $section_code,//$arResult["VARIABLES"]["SECTION_CODE"],
            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
            "CACHE_TIME" => $arParams["CACHE_TIME"],
            "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
            "COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
            "TOP_DEPTH" => $arParams["SECTION_TOP_DEPTH"],
            "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
            "VIEW_MODE" => $arParams["SECTIONS_VIEW_MODE"],
            "SHOW_PARENT_NAME" => $arParams["SECTIONS_SHOW_PARENT_NAME"],
            "HIDE_SECTION_NAME" => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"]) ? $arParams["SECTIONS_HIDE_SECTION_NAME"] : "N"),
            "ADD_SECTIONS_CHAIN" => "Y",
            'CURENT_ID' => $arResult["VARIABLES"]["SECTION_ID"],
            'CURENT_CODE' => $arResult["VARIABLES"]["SECTION_CODE"],
        ),
        $component,
        array("HIDE_ICONS" => "Y")
    ); ?>
</div>
