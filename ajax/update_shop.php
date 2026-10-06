<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;

if (CModule::IncludeModule("iblock")) {

    $el = new CIBlockElement;

    if (!empty($_FILES["LOGO"])) {
        $arIMAGE = $_FILES["LOGO"];
        $fid = CFile::SaveFile($arIMAGE, "iblock");
        $logo = CFile::MakeFileArray($fid);
    } elseif ($_POST['OLD_LOGO'] !== 'undefined') {
        $logo = CFile::MakeFileArray($_POST['OLD_LOGO']);
    } else {
        $logo = array('del' => 'Y');
    }

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


    $arLoadProductArray = Array(
        "MODIFIED_BY" => $USER->GetID(), // элемент изменен текущим пользователем
        "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
        "NAME" => $_POST['NAME'],
        "ACTIVE" => "Y",            // активен
        "DETAIL_TEXT" => $_POST['DETAIL_TEXT'],
        "DETAIL_PICTURE" => $logo,
    );

    $SHOP_ID = $_POST['SHOP_ID'];
    $res = $el->Update($SHOP_ID, $arLoadProductArray);

    $PROP = array();
    $PROP['SITE_LINK'] = $_POST['SITE_LINK'];
    $PROP['INSTA_LINK'] = $_POST['INSTA_LINK'];
    $PROP['VK_LINK'] = $_POST['VK_LINK'];
    $PROP['CORPUS'] = $_POST['CORPUS'];
    $PROP['ETAG'] = $_POST['ETAG'];
    $PROP['SECTION'] = $_POST['SECTION'];
    $PROP['PHONE'] = $_POST['PHONE'];
    $PROP['SLIDE'] = $slides;

    CIBlockElement::SetPropertyValuesEx($SHOP_ID, false, $PROP);

}
echo json_encode(true);
