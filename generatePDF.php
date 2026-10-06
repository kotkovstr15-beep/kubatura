<?php
define("STOP_STATISTICS", true);
define('NO_AGENT_CHECK', true);

use Bitrix\Main\Application;
use Bitrix\Main\Loader;

//require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
require_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/tcpdf_min/tcpdf.php");

// для модификации шапки и подвала сайта, необходимо переписать соответствующие функции.
// для этого, создадим свой класс унаследовав его от TCPDF:

class MdfPDF extends TCPDF
{
    //Page header
    public function Header()
    {
        // Logo
        $headerdata = $this->getHeaderData();
        $this->Image($headerdata['logo'], 20, 10, $headerdata['logo_width'], '', 'JPG', '', 'T', false, 300, '', false,
            false, 0, false, false, false);
        // Set font
        $this->SetFont('dejavusans', '', 14);
        // Title
        /*
        // если нужно в верхнем колонтитуле вывести заголовок или какой-то текст, эти строки нужно раскомментировать
        // и состыковать
        $this->Cell(0, 15, $headerdata['title'], 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Cell(0, 15, $headerdata['string'], 0, false, 'C', 0, '', 0, false, 'M', 'M');
        */
        $this->SetTextColor(85, 85, 85); // цвет шрифта
        $html = "<br /><hr />"; // добавим линию, отделающую колонтитул от текста
        $this->writeHTML($html, true, false, true, false, '');
    }

    // Page footer
    public function Footer()
    {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('dejavusans', 'I', 8);
        // Page number
        $this->SetTextColor(85, 85, 85);
        $html = "<hr />";
        $this->writeHTML($html, true, false, true, false, '');
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 10, 'Страница ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0,
            '', 0, false, 'T', 'M');
    }
}

class pdfit
{
    /** @var MdfPDF $pdf */
    var $pdf;
    var $itemsID;
    var $siteInfo;
    var $siteLogo;
    var $order;
    var $arUserData;
    protected $items = [
        'ITEMS' => [],
        'SHOP' => []
    ];

    function __construct($itemsID, $siteLogo = false)
    {
        $this->itemsID = $itemsID; // идентификатор заказа для обработки
        $this->siteLogo = $siteLogo; // пусть к файлу с логотипом сайта
    }

    function process()
    {
        $this->getSiteInfo();
        $resultfile = $_SERVER['DOCUMENT_ROOT'] . '/upload/invoices/' . $this->siteInfo['SERVER_NAME'] .
            '_invoice_' . 12 . '.pdf';
        $this->setArResultItemsByIblockId(7);
        $this->setArResultItemsByIblockId(11);
        $this->initPdf();
        $this->GetHead();
        $this->pdf->Output($resultfile, 'I'); // выводим на экран
        $this->pdf->Output($resultfile, 'F'); // сохраняем файл по указанному пути
    }

    function getSiteInfo($siteId = SITE_ID)
    {
        if (empty($siteId)) {
            $siteId = "s1";
        }
        $arSite = false;
        $obCache = new CPHPCache();
        if ($obCache->InitCache(36000, 'site_' . $siteId, '/')) {
            $arSite = $obCache->GetVars();
        } elseif ($obCache->StartDataCache()) {
            $arSite = CSite::GetByID($siteId)->Fetch();
            $obCache->EndDataCache($arSite);
        }
        $this->siteInfo = $arSite;
    }

    function setArResultItemsByIblockId(int $iblockId): void
    {
        if (CModule::IncludeModule("iblock")) {
            $itemsId = $this->itemsID;
            $count = count($itemsId);
            if ($count > 0) {
                $arProduct = array();
                $arShopInfo = array();
                $arSort = array('ID' => 'ASC');
                $arFilter = array('ID' => $itemsId, 'IBLOCK_ID' => $iblockId, "ACTIVE" => "Y");
                $arSelect = array();
                $res = CIBlockElement::getList($arSort, $arFilter, false, false, $arSelect);
                while ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    $arProps = $ob->GetProperties();
                    if (in_array($arFields['ID'], $itemsId)) {
                        $sections = CIBlockSection::GetByID($arFields['IBLOCK_SECTION_ID']);
                        $section = $sections->GetNext();
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
                            'SECTION' => $section,
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
                }
                $this->items['ITEMS'] = array_merge($this->items['ITEMS'], $arProduct);
                $this->items['SHOP'] = array_merge($this->items['SHOP'], $arShopInfo);
            }
        }
        return;
    }

    function initPdf()
    {
        $this->pdf = new MdfPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $this->pdf->SetMargins(20, 20, 10);
        $this->pdf->SetAutoPageBreak(true, 20);
        if (is_array($this->siteInfo) && strlen($this->siteInfo['NAME']) > 0) {
            $this->pdf->SetAuthor($this->siteInfo['NAME']);
        }
        $this->setTitleByOrderID();
    }

    function setTitleByOrderID()
    {
        $title = 'Корзина покупателя';
        $this->pdf->SetTitle($title);
        $this->pdf->SetHeaderData($this->siteLogo, 30, $title);
    }

    function GetHead()
    {
        $this->pdf->AddPage();
        $headerdata = $this->pdf->getHeaderData();
        $this->pdf->SetFont('dejavusans', '', 12);
        $this->pdf->Write(0, 'Корзина покупателя', '', 0, 'C', true,
            0, false, false, 0);
        $this->renderBody();
        $this->pdf->SetFont('dejavusans', '', 10);
        $this->pdf->Write(0, "", '', 0, 'C', true,
            0, false, false, 0);
    }

    protected function renderBody(): void
    {
        $table = '';
        $tableHead = '<table border="1">';
        $tableBodyHeader = '<tr align="center"><th>Магазин</th><th>Наименование товара</th><th>Цена</th></tr>';
        $tableFooter = '</table>';
        $trItems = '';
        foreach ($this->items['ITEMS'] as $item) {
            $trItem = '';
            $shop = $this->findShopByShopId($item['SHOP']);
            $shopName = $shop['FIELD']['NAME'];
            $name = $item['NAME'];
            $price = $item['PRICE'];
            $trItem .= "<tr align=\"center\"><td height=\"35\"><span>$shopName</span></td><td>$name</td><td>$price<span>р.</span></td></tr>";
            $trItems .= $trItem;
        }
        $table .= $tableHead . $tableBodyHeader . $trItems . $tableFooter;
        $this->pdf->writeHTML("</br>");
        $this->pdf->writeHTML($table);
    }

    protected function findShopByShopId(string $shopId): array
    {
        foreach ($this->items['SHOP'] as $key => $shop) {
            if ($shop['FIELD']['ID'] === $shopId) {
                return $shop;
            }
        }
        return [];
    }

}

try {
    Loader::includeModule('iblock');
} catch (LoaderExceptionAlias $e) {
    ShowError('Ошибка при загрузке модуля(');
}

$application = Application::getInstance();
$context = $application->getContext();
$request = $context->getRequest();

$itemsID = [];
$CART_PRODUCT = $request->getCookie("CART_PRODUCT_CUSTOM");
if (!$CART_PRODUCT) {
    $CART_PRODUCT = array();
} else {
    $CART_PRODUCT = json_decode(base64_decode($CART_PRODUCT), true);
}
foreach ($CART_PRODUCT as $k => $v) {
    $v = (int)$v;
    if ($v > 0) {
        $itemsID[$k] = $v;
    } else {
        unset($CART_PRODUCT[$k]);
    }
}
unset($CART_PRODUCT);
if (count($itemsID) > 0) {
    $pdf = new pdfit($itemsID, $_SERVER['DOCUMENT_ROOT'] . '/upload/sitelogo.jpg');
    $pdf->process();
} else {
    ShowError('Корзина пуста!');
}