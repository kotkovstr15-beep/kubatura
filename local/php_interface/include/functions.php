<?php

/**
 * Метод возвращает ID магазина к которому привязан пользователь.
 * @param $userId
 * @return int
 */
function getShopIdByUserId($userId): int
{
    try {
        if (CModule::IncludeModule('iblock')) {
            $arSelect = array('ID');
            $arFilter = array('IBLOCK_ID' => 6, 'PROPERTY_OWNER' => $userId);
            $res = CIBlockElement::GetList(array(), $arFilter, false, array('nPageSize' => 1), $arSelect);
            $element = $res->GetNextElement();
            if ($element) {
                $fields = $element->GetFields();
                return $fields['ID'];
            }
        }
        return 0;
    } catch (Throwable | Exception  $error) {
        return 0;
    }
}

/**
 * Метод возвращает всю информацию о магазине по ID пользователя.
 * @param int $userId
 * @return array
 */
function getInfoAboutUserShop(int $userId): array
{
    $shop = [
        'FIELDS' => [],
        'PROPERTIES' => []
    ];
    $shopsIblockID = 6;
    $shopId = getShopIdByUserId($userId);
    $arFilter = array('IBLOCK_ID' => $shopsIblockID, 'ID' => $shopId);
    $res = CIBlockElement::GetList(array(), $arFilter, false, array('nPageSize' => 1), array());
    $notPreparedObject = $res->GetNextElement();
    if (!$notPreparedObject) {
        return $shop;
    }
    $fields = $notPreparedObject->GetFields();
    $properties = $notPreparedObject->GetProperties();
    $shop['FIELDS'] = $fields;
    $shop['PROPERTIES'] = $properties;
    return $shop;
}

function dumpC($d)
{
    if (!empty($_COOKIE['kek'])) {
        echo "<pre style='font-size:10px'>";
        var_dump($d);
        echo "</pre>";
    }
}