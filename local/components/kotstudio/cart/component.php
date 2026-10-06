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

use Bitrix\Main\Application;
use Bitrix\Main\Loader;

if (!Loader::includeModule("iblock")) {
    $this->abortResultCache();
    ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
    return;
}

$application = Application::getInstance();
$context = $application->getContext();
$request = $context->getRequest();

$CART_PRODUCT = $request->getCookie("CART_PRODUCT_CUSTOM");

if (!$CART_PRODUCT) {
    $CART_PRODUCT = array();
} else {
    $CART_PRODUCT = json_decode(base64_decode($CART_PRODUCT), true);
}

foreach ($CART_PRODUCT as $k => $v) {
    $v = (int)$v;
    if ($v > 0) {
        $CART_PRODUCT[$k] = $v;
    } else {
        unset($CART_PRODUCT[$k]);
    }
}

$arResult = array();
$arResult['ITEMS'] = array();
$arResult['SHOP'] = array();
setArResultItemsByIblockId($CART_PRODUCT, 7, $arResult);
setArResultItemsByIblockId($CART_PRODUCT, 11, $arResult);


if (count($arResult['ITEMS']) > 0) {
    foreach ($arResult['ITEMS'] as &$item) {
        $item['SHOP_INFO'] = findShopByShopId($item['SHOP'], $arResult['SHOP']);
    }
}
$this->includeComponentTemplate();


function setArResultItemsByIblockId(array $itemsId, int $iblockId, &$arResult): void
{
    $count = count($itemsId);
    if ($count > 0) {
        $arShopInfo = array();
        $arProduct = array();
        $arSort = array('ID' => 'ASC');
        $arFilter = array('ID' => $itemsId, 'IBLOCK_ID' => $iblockId, "ACTIVE" => "Y");
        $arSelect = array();
        $res = CIBlockElement::getList($arSort, $arFilter, false, false, $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arProps = $ob->GetProperties();

            if (in_array($arFields['ID'], $itemsId)) {
                $SHOP_LINK_IBLOCK_ID = $arProps['SHOP']['LINK_IBLOCK_ID'];
                $arProduct[$arFields['ID']] = array(
                    'ID' => $arFields['ID'],
                    'NAME' => $arFields['NAME'],
                    'IBLOCK_ID' => $arFields['IBLOCK_ID'],
                    'DETAIL_PAGE_URL' => $arFields['DETAIL_PAGE_URL'],
                    'DETAIL_PICTURE' => $arFields['DETAIL_PICTURE'],
                    'PREVIEW_PICTURE' => $arFields['PREVIEW_PICTURE'],
                    'SHOP' => $arProps['SHOP']['VALUE'],
                    'SHOP_LINK_IBLOCK_ID' => $arProps['SHOP']['LINK_IBLOCK_ID'],
                    'SALE_PRICE' => $arProps['SALE_PRICE']['VALUE'],
                    'PRICE' => $arProps['PRICE']['VALUE'],
                    'NEW' => $arProps['NEW']['VALUE'],
                    'SALE' => $arProps['SALE']['VALUE'],
                    'NAL' => $arProps['NAL']['VALUE'],
                );
            }
        }
        if (count($arProduct) > 0) {
            foreach ($arProduct as $item) {
                $arShopInfo[$item['SHOP']] = array();
            }
            $arSort = array('ID' => 'ASC');
            $arFilter = array('ID' => array_keys($arShopInfo), 'IBLOCK_ID' => $SHOP_LINK_IBLOCK_ID);
            $arSelect = array();
            $res = CIBlockElement::getList($arSort, $arFilter, false, false, $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arShop = $ob->GetFields();
                $arShopInfo[$arShop['ID']] = array(
                    'FIELD' => $arShop,
                    'PROP' => $ob->GetProperties()
                );
            }
        }
//        dump($arShopInfo);
        $arResult['ITEMS'] = array_merge($arResult['ITEMS'], $arProduct);
        $arResult['SHOP'] = array_merge($arResult['SHOP'], $arShopInfo);
    }
    return;
}

function findShopByShopId(string $shopId, $shops): array
{
    foreach ($shops as $key => $shop) {
        if ($shop['FIELD']['ID'] === $shopId) {
            return $shop;
        }
    }
    return [];
}
