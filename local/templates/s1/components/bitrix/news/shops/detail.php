<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
\Bitrix\Main\Loader::includeModule('iblock');
$shop=\Kubatura\ShopPage\Service::shop($arResult['VARIABLES']['ELEMENT_CODE']);
if (!$shop) { \Kubatura\ShopPage\notFound(); return; }
$data=\Kubatura\ShopPage\Service::data($shop);
require $_SERVER['DOCUMENT_ROOT'].'/local/php_interface/include/shop-page/view.php';
