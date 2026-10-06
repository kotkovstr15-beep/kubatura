<?php

//пример выборки дерева подразделов для раздела
//$rsParentSection = CIBlockSection::GetByID(ID_необходимой_секции);
//if ($arParentSection = $rsParentSection->GetNext())
//{
//    $arFilter = array('IBLOCK_ID' => $arParentSection['IBLOCK_ID'],'>LEFT_MARGIN' => $arParentSection['LEFT_MARGIN'],'<RIGHT_MARGIN' => $arParentSection['RIGHT_MARGIN'],'>DEPTH_LEVEL' => $arParentSection['DEPTH_LEVEL']); // выберет потомков без учета активности
//    $rsSect = CIBlockSection::GetList(array('left_margin' => 'asc'),$arFilter);
//    while ($arSect = $rsSect->GetNext())
//    {
//        // получаем подразделы
//    }
//}


$arFilter = Array('IBLOCK_ID' => 7, 'GLOBAL_ACTIVE' => 'Y');
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


function prepareUrlToClearFormat(string $url): string
{
    $url = str_replace(array('https://', 'http://'), '', $url);
    $url = preg_replace('/\s\s+/', '', $url);
    $url = preg_replace('/[\\/]$/', '', $url);


    return $url;

}