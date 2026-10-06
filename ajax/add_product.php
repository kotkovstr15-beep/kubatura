<?php
/** @noinspection SpellCheckingInspection */
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

global $USER;

$response = ['success', 'error'];
if ((int)$_POST['SHOP_ID'] !== getShopIdByUserId($USER->GetID())) {
    $response['error'] = 'Указанный при отправке id магазина не соответствует привязанному id для пользователя!';
    die(json_encode($response));
}
if (CModule::IncludeModule('iblock')) {
    $el = new CIBlockElement;
    $slidesDoBeforeCreate = [];
    $slides = [];
    if (!empty($_FILES['SLIDES'])) {
        foreach ($_FILES['SLIDES'] as $key => $prop) {
            foreach ($prop as $index => $field) {
                $slidesDoBeforeCreate[$index][$key] = $field;
            }
        }
    }
    foreach ($slidesDoBeforeCreate as $slide) {

        $fid = CFile::SaveFile($slide, 'iblock');
        $slides[] = CFile::MakeFileArray($fid);
    }
    if (!empty($_FILES['DETAIL_FILE'])) {
        $arIMAGE = $_FILES['DETAIL_FILE'];
        $fid = CFile::SaveFile($arIMAGE, 'iblock');
        $detailPhoto = CFile::MakeFileArray($fid);
    }
    unset($arIMAGE);
    if (!empty($_FILES['PREVIEW_FILE'])) {
        $arIMAGE = $_FILES['PREVIEW_FILE'];
        $fid = CFile::SaveFile($arIMAGE, 'iblock');
        $previewPhoto = CFile::MakeFileArray($fid);
    }
    if (!empty($detailPhoto) && empty($previewPhoto)) {
        $previewPhoto = $detailPhoto;
    }
    unset($arIMAGE);

    $PROP = array();
    $PROP['SHOP'] = $_POST['SHOP_ID'];
    $PROP['PRICE'] = $_POST['PRICE'];
    $PROP['FILTER_PRICE'] = (int)$_POST['PRICE'];
    $PROP['SALE_PRICE'] = $_POST['SALE_PRICE'];
    $PROP['COLOR'] = $_POST['COLOR'];
    $PROP['MATERIALY'] = $_POST['MATERIALY'];
    $PROP['MANUFACTURER'] = $_POST['MANUFACTURER'];
    $PROP['USE'] = $_POST['USE'];
    $PROP['WARRANTY'] = $_POST['WARRANTY'];
    $PROP['DELIVERY'] = $_POST['DELIVERY'];
    $PROP['VESY'] = $_POST['VESY'];
    $PROP['KOLVOMKVYPAK '] = $_POST['KOLVOMKVYPAK '];
    $PROP['ZACHITNYSLOY'] = $_POST['ZACHITNYSLOY'];
    $PROP['MORE_PHOTO'] = $slides;

    $code = slugTranslit($_POST['NAME']);
    $arLoadProductArray = Array(
        'MODIFIED_BY' => $USER->GetID(), // элемент изменен текущим пользователем
        'IBLOCK_SECTION_ID' => $_POST['SUB_CATEGORY'],          // элемент лежит в корне раздела
        'IBLOCK_ID' => 7,
        'PROPERTY_VALUES' => $PROP,
        'NAME' => $_POST['NAME'],
        'ACTIVE' => 'Y',            // активен
        'CODE' => $code,
        'DETAIL_TEXT' => $_POST['DETAIL_TEXT'],
        "DETAIL_PICTURE" => $detailPhoto,
        "PREVIEW_PICTURE" => $previewPhoto,
    );

    if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
        $response['success'] = 'New ID: ' . $PRODUCT_ID;
    } else {
        $response['error'] = 'Error: ' . $el->LAST_ERROR;
    }
}
die(json_encode($response));

function slugTranslit(string $name): string
{
    $arParams = array(
        "max_len" => "100", // обрезаем символьный код до 100 символов
        "change_case" => "L", // приводим к нижнему регистру
        "replace_space" => "-", // меняем пробелы на тире
        "replace_other" => "-", // меняем плохие символы на тире
        "delete_repeat_replace" => "true", // удаляем повторяющиеся тире
        "use_google" => "false", // отключаем использование google
    );
    return Cutil::translit($name, "ru", $arParams);
}
