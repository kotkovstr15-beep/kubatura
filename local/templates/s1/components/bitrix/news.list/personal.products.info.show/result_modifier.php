<?php
global $USER;
//получаем все id групп
$elementsIds = [];
foreach ($arResult['ITEMS'] as $item) {
    $elementsIds[] = $item['ID'];
}
//получаем их группы
$groups = CIBlockElement::GetElementGroups($elementsIds, true);
$groupMainInfo = [];
while ($group = $groups->Fetch()) {
    $groupMainInfo[$group['ID']] = $group['NAME'];
}
//объединяем
foreach ($arResult['ITEMS'] as &$item) {
    $item['SECTION_NAME'] = $groupMainInfo[$item['IBLOCK_SECTION_ID']];
    $item['FIELDS']['SECTION_NAME'] = $groupMainInfo[$item['IBLOCK_SECTION_ID']];
}

//получаем информацию о магазине
$arResult['SHOP'] = getInfoAboutUserShop($USER->GetID());