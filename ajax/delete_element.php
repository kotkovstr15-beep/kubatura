<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

if (CModule::IncludeModule("iblock")) {

    $elementId = (int)$_POST['ELEMENT_ID'];
    $arSelect = Array("ID", "IBLOCK_ID", "PROPERTY_SHOP");
    $arFilter = Array("IBLOCK_ID" => 7, "ID" => $elementId, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize" => 1), $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
    }

    if (getShopIdByUserId($USER->GetID()) === (int)$arProps['SHOP']['VALUE']) {
        CIBlockElement::Delete($elementId);
        echo json_encode(['success' => true, 'message' => 'Товар удалён']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Недостаточно прав']);
    }
}



