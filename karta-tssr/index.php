<?
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path === '/karta-tssr/index.php') { header('HTTP/1.1 301 Moved Permanently'); header('Location: /karta-tssr/'); exit; }
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Удобная навигация по торговым корпусам Центра строительства и ремонта Кубатура. Перейдите на страницу любого торгового корпуса, кликнув на плане");
$APPLICATION->SetPageProperty("keywords", "Кубатура, стройматериалы в Самаре, строительно-отделочные материалы, отделочные материалы, товары для ремонта, отделочные работы, центр строительства и ремонта, сантехника, доски, загородный дом, панели, краска, штукатурка, смеси, цемент, двери, паркет");
$APPLICATION->SetPageProperty("title", "Карта центра «Кубатура»");
$APPLICATION->SetTitle("Карта центра «Кубатура»");
?>
<?php kubaturaKartaIntro('index', 'Карта центра «Кубатура»'); ?>
<? $APPLICATION->IncludeFile(
                            $APPLICATION->GetTemplatePath("/include/plan.php"),
                            Array(),
                            Array("MODE"=>"php")
                        );?>
<?php kubaturaKartaShopList(''); ?>



<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
