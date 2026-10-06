<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("description",
    "Центр строительства и ремонта Кубатура - крупнейший в Самаре специализированный магазин");
$APPLICATION->SetPageProperty("keywords",
    "Кубатура, стройматериалы в Самаре, строительно-отделочные материалы, отделочные материалы, товары для ремонта, отделочные работы, центр строительства и ремонта, сантехника, доски, загородный дом, панели, краска, штукатурка, смеси, цемент, двери, паркет");
$APPLICATION->SetPageProperty("title", "Кубатура - Центр строительства и ремонта в Самаре");
$APPLICATION->SetTitle("Кубатура - Центр строительства и ремонта в Самаре");
?>
<?php
$arEventFields['USER_EMAIL'] = $_POST['USER_EMAIL'];
$itemsId = $_POST['ITEMS'];

$arResult = array();
$arResult['ITEMS'] = array();
$arResult['SHOP'] = array();

setArResultItemsByIblockId($itemsId, 7, $arResult);
setArResultItemsByIblockId($itemsId, 11, $arResult);
$arEventFields['ITEMS'] = render($arResult['ITEMS']);
CEvent::Send("SEND_PRODUCTS", SITE_ID, $arEventFields, 'N', 10);

function setArResultItemsByIblockId(array $itemsId, int $iblockId, &$arResult): void
{
    $count = count($itemsId);
    if ($count > 0) {
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
                    'DETAIL_PAGE_URL' => 'https://kubatura.su' . $arFields['DETAIL_PAGE_URL'],
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
            $arShopInfo = array();
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
        foreach ($arProduct as &$product) {
            $fileID = '';
            if ($product['PREVIEW_PICTURE']) {
                $fileID = $product['PREVIEW_PICTURE'];
            } else {
                if ($product['DETAIL_PICTURE']) {
                    $fileID = $product['DETAIL_PICTURE'];
                } else {
                    if ($arShopInfo[$product['SHOP']]) {
                        $fileID = $arShopInfo[$product['SHOP']]['PROP']['LOGO_2']['VALUE'];
                    }
                }
            }
            if (!empty($fileID)) {
                $file = CFile::ResizeImageGet($fileID, array('width' => 400, 'height' => 280),
                    BX_RESIZE_IMAGE_PROPORTIONAL, false, array(), false, 100);
                $product['SRC'] = 'https://kubatura.su/' . $file['src'];
            }
        }
        $arResult['ITEMS'] = array_merge($arResult['ITEMS'], $arProduct);
        $arResult['SHOP'] = array_merge($arResult['SHOP'], $arShopInfo);
    }
    return;
}

function render($items): string
{
    $body = '';
    foreach ($items as $item) {
        $attributeA = "<p><a href=\"" . $item['DETAIL_PAGE_URL'] . "\" class=\"cart-item__img\">" . $item['NAME'] . "</a></p>";
        $body .= $attributeA;
    }
    return $body;
}

function renderWithImg($items): string
{
    $all = '';
    $body = '';
    $head = '<span style="color:grey"><b>Товар</b></span><br><table cellpadding="1" cellspacing="1"><tbody>';
    $footer = '</tbody></table>';
    foreach ($items as $item) {
        $tr = "
<tr>
	<td>
		<div style=\"background-image: url('" . $item['SRC'] . "'); width:245px; height:170px; display:block; background-position: center; background-size: cover;\">
		</div>
	</td>
	<td style=\"width:400px;text-align:center\">
 <a href=\"" . $item['DETAIL_PAGE_URL'] . "\" class=\"cart-item__img\">" . $item['NAME'] . "</a>
	</td>
	<td>
	</td>
</tr>";
        $body .= $tr;
    }
    $all = $head . $body . $footer;
    return $all;
}

?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
