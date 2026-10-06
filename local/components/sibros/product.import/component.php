<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */

$arParams["CACHE_TIME"] = 1;

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
if (strlen($arParams["IBLOCK_TYPE"]) <= 0) {
    $arParams["IBLOCK_TYPE"] = "news";
}
$arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);

$arSelect =  array(
    "ID",
    "IBLOCK_ID",
    "IBLOCK_SECTION_ID",
    "NAME",
    "ACTIVE_FROM",
    "TIMESTAMP_X",
    "DETAIL_PAGE_URL",
    "LIST_PAGE_URL",
    "DETAIL_TEXT",
    "DETAIL_TEXT_TYPE",
    "PREVIEW_TEXT",
    "PREVIEW_TEXT_TYPE",
    "PREVIEW_PICTURE",
    "DETAIL_PICTURE",
    "PROPERTY_*"
);
$arFilter = Array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ID"=>htmlspecialchars($_GET['ID']), "ACTIVE"=>"Y","PROPERTY_SHOP" => getShopIdByUserId($USER->GetID()));
$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
if($ob = $res->GetNextElement()){
    $arFields = $ob->GetFields();
    $arFields["PREVIEW_PICTURE"] = $arFields["~PREVIEW_PICTURE"] = CFile::GetFileArray($arFields["PREVIEW_PICTURE"]);
    $arFields["DETAIL_PICTURE"] = $arFields["~DETAIL_PICTURE"] = CFile::GetFileArray($arFields["DETAIL_PICTURE"]);

    $arResult = $arFields;

    $arProps = $ob->GetProperties();
    $arResult['PROPERTIES'] = $arProps;
}
$db_groups = CIBlockElement::GetElementGroups($arResult['ID'], true);
while($ar_group = $db_groups->Fetch()){
    $arResult['CURRENT_SECTION_DEPTH_2'] = $ar_group['ID'];
    $arResult['CURRENT_SECTION_DEPTH_1'] = $ar_group["IBLOCK_SECTION_ID"];
}

$arResult['FORM_VALUES']['MATERIALY'] = getPropertyEnumByCode('MATERIALY');
$arResult['FORM_VALUES']['COLOR'] = getPropertyEnumByCode('COLOR');
$arResult['FORM_VALUES']['MANUFACTURER'] = getPropertyEnumByCode('MANUFACTURER');

function getPropertyEnumByCode(string $code, int $iblockId = 7): array
{
    $property_enums = CIBlockPropertyEnum::GetList(Array(), Array("IBLOCK_ID" => $iblockId, "CODE" => $code));
    $props = [];
    while ($enum_fields = $property_enums->GetNext()) {
        $props[$enum_fields["ID"]] = $enum_fields["VALUE"];
    }
    return $props;

}
echo '<pre>';
   // var_dump($arResult);
echo '</pre>';
$this->includeComponentTemplate();