<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Список покупок");
?>
    <section class="cart s-inner">
        <div class="container">
            <div class="breadcrumbs">
                <? $APPLICATION->IncludeComponent("bitrix:breadcrumb", "", Array(
                        "START_FROM" => "0",
                        "PATH" => "",
                        "SITE_ID" => "s1"
                    )
                ); ?>
            </div>
            <div class="title">
                <h1 class="title__head">Список покупок</h1>
            </div>
            <? $APPLICATION->IncludeComponent("kotstudio:cart", "", Array()
            ); ?>
        </div>
    </section>
    <div class='js_viewed_product_box' data-path="<?= SITE_TEMPLATE_PATH ?>"></div>
<?php
if (!empty($_COOKIE['kek'])) {
}
?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>