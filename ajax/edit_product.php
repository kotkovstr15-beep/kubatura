<?php
/** @noinspection SpellCheckingInspection */
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

global $USER;

$response = [
    'success' => '',
    'error' => ''
];
if ((int)$_POST['SHOP_ID'] !== getShopIdByUserId($USER->GetID())) {
    $response['error'] = 'Указанный при отправке id магазина не соответствует привязанному id для пользователя!';
    die(json_encode($response));
}
if (CModule::IncludeModule('iblock')) {
    $el = new CIBlockElement;

    $slidesNotPrepared = [];
    $slides = [];
    if (!empty($_FILES["SLIDES"])) {
        foreach ($_FILES["SLIDES"] as $key => $prop) {
            foreach ($prop as $index => $field) {
                $slidesNotPrepared[$index][$key] = $field;
            }
        }
    }
    if (!empty($_POST['OLD_SLIDE'])) {
        foreach ($_POST['OLD_SLIDE'] as $key => $slideId) {
            $slides[] = CFile::MakeFileArray($slideId);
        }
    }
    foreach ($slidesNotPrepared as $slide) {
        $fid = CFile::SaveFile($slide, "iblock");
        $slides[] = CFile::MakeFileArray($fid);
    }
    if (empty($slides)) {
        $slides = array('del' => 'Y');
    }

    if (!empty($_FILES["DETAIL_FILE"])) {
        $arIMAGE = $_FILES["DETAIL_FILE"];
        $fid = CFile::SaveFile($arIMAGE, "iblock");
        $detailPhoto = CFile::MakeFileArray($fid);
    } elseif ($_POST['OLD_DETAIL_PICTURE'] !== 'undefined') {
        $detailPhoto = CFile::MakeFileArray($_POST['OLD_DETAIL_PICTURE']);
    } else {
        $detailPhoto = array('del' => 'Y');
    }

    if (!empty($_FILES["PREVIEW_FILE"])) {
        $arIMAGE = $_FILES["PREVIEW_FILE"];
        $fid = CFile::SaveFile($arIMAGE, "iblock");
        $previewPhoto = CFile::MakeFileArray($fid);
    } elseif ($_POST['OLD_PREVIEW_PICTURE'] !== 'undefined') {
        $previewPhoto = CFile::MakeFileArray($_POST['OLD_DETAIL_PICTURE']);
    } else {
        $previewPhoto = array('del' => 'Y');
    }

    $arLoadProductArray = Array(
        "MODIFIED_BY" => $USER->GetID(), // элемент изменен текущим пользователем
        "IBLOCK_SECTION" => $_POST['SUB_CATEGORY'],          // элемент лежит в корне раздела
        "NAME" => $_POST['NAME'],
        "ACTIVE" => "Y",            // активен
        "DETAIL_TEXT" => $_POST['DETAIL_TEXT'],
        "DETAIL_PICTURE" => $detailPhoto,
        "PREVIEW_PICTURE" => $previewPhoto,
    );
    $ID = $_POST['ID'];
    $res = $el->Update($ID, $arLoadProductArray);

    $PROP = array();
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
    $PROP['RAZMER'] = $_POST['RAZMER'];
    $PROP['ZACHITNYSLOY'] = $_POST['ZACHITNYSLOY'];
    $PROP['UNIT_OF_MEASURE'] = $_POST['UNIT_OF_MEASURE'];
    $PROP['KOLVOMKVYPAK'] = $_POST['KOLVOMKVYPAK'];
    $PROP['MORE_PHOTO'] = $slides;


    CIBlockElement::SetPropertyValuesEx($ID, false, $PROP);
}
die(json_encode($response));
