<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Импорт");

global $USER;
if (!$USER->IsAdmin()){
    header('Location: /personal/',true,301);
}
?>
<?$APPLICATION->IncludeComponent(
    "sibros:product.import",
    "",
    Array(
        "CACHE_FILTER" => "N",
        "CACHE_GROUPS" => "Y",
        "CACHE_TIME" => "1",
        "CACHE_TYPE" => "N",
        "IBLOCK_ID" => "7",
        "IBLOCK_TYPE" => "content",
    )
);?>
<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>