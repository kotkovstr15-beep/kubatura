<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Редактировать товар");

$userShopId = getShopIdByUserId($USER->GetID());
if ($userShopId === 0){
    header('Location: /personal/',true,301);
}
?>
<?$APPLICATION->IncludeComponent(
	"sibros:product.edit",
	"",
	Array(
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "1",
		"CACHE_TYPE" => "N",
		"IBLOCK_ID" => "7",
		"IBLOCK_TYPE" => "content",
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>