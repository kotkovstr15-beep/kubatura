<?php
$iblockId = 7;
$arFilter = Array('IBLOCK_ID' => $iblockId, 'GLOBAL_ACTIVE' => 'Y');
$db_list = CIBlockSection::GetList(Array(), $arFilter, true);
$allSections = [];
while ($ar_result = $db_list->GetNext()) {
    $allSections[] = $ar_result;
}
$rootDirs = [];
foreach ($allSections as $key => $section) {
    if ($section['DEPTH_LEVEL'] !== '1') {
        continue;
    }
    $rootDirs[$section['ID']] = $section;
    $rootDirs[$section['ID']]['SUB_DIRS'] = [];
    unset($allSections[$key]);
}

foreach ($allSections as $key => $section) {
    if ($section['DEPTH_LEVEL'] !== '2') {
        continue;
    }
    $rootDirs[$section['IBLOCK_SECTION_ID']]['SUB_DIRS'][] = $section;
    unset($allSections[$key]);
}
$arResult['SECTIONS_TREE'] = $rootDirs;

$arResult['MATERIALY'] = getPropertyEnumByCode('MATERIALY');
$arResult['COLOR'] = getPropertyEnumByCode('COLOR');
$arResult['NAL'] = getPropertyEnumByCode('NAL');
$arResult['MANUFACTURER'] = getPropertyEnumByCode('MANUFACTURER');

function getPropertyEnumByCode(string $code, int $iblockId = 7): array
{
    $property_enums = CIBlockPropertyEnum::GetList(Array(), Array("IBLOCK_ID" => $iblockId, "CODE" => $code));
    $props = [];
    while ($enum_fields = $property_enums->GetNext()) {
        $props[$enum_fields["ID"]] = $enum_fields["VALUE"];
    }
    return $props;

}

function prepareUrlToClearFormat(string $url): string
{
    $url = str_replace(array('https://', 'http://'), '', $url);
    $url = preg_replace('/\s\s+/', '', $url);
    $url = preg_replace('/[\\/]$/', '', $url);


    return $url;

}
