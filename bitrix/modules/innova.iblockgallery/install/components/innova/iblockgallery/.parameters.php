<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arIBlocks=array();
$db_iblock = CIBlock::GetList(array("SORT"=>"ASC"), array());
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = "[".$arRes["ID"]."] ".$arRes["NAME"];

$arComponentParameters = array(
	"GROUPS" => array(),
	"PARAMETERS" => array(
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("INNOVA_IBLOCKGALLERY_IBLOCK_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '={$_REQUEST["ID"]}',
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH" => "Y",
		),
		"VIEW_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("INNOVA_IBLOCKGALLERY_VIEW_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => array('grid' => GetMessage("INNOVA_IBLOCKGALLERY_VIEW_TYPE_GRID"), 'grid_slider' => GetMessage("INNOVA_IBLOCKGALLERY_VIEW_TYPE_GRID_SLIDER")),
			"DEFAULT" => "grid",
		),
		"SCALE_HOVER" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("INNOVA_IBLOCKGALLERY_SCALE_HOVER"),
			"TYPE" => "LIST",
			"VALUES" => array('Y' => GetMessage("INNOVA_IBLOCKGALLERY_Y"), 'N' => GetMessage("INNOVA_IBLOCKGALLERY_N")),
			"DEFAULT" => "Y",
		),
		"ROTATE_HOVER" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("INNOVA_IBLOCKGALLERY_ROTATE_HOVER"),
			"TYPE" => "LIST",
			"VALUES" => array('Y' => GetMessage("INNOVA_IBLOCKGALLERY_Y"), 'N' => GetMessage("INNOVA_IBLOCKGALLERY_N")),
			"DEFAULT" => "Y",
		),
		"TITLE_HOVER" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("INNOVA_IBLOCKGALLERY_TITLE_HOVER"),
			"TYPE" => "LIST",
			"VALUES" => array('Y' => GetMessage("INNOVA_IBLOCKGALLERY_Y"), 'N' => GetMessage("INNOVA_IBLOCKGALLERY_N")),
			"DEFAULT" => "Y",
		),
		"DESCRIPTION" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("INNOVA_IBLOCKGALLERY_DESCRIPTION"),
			"TYPE" => "LIST",
			"VALUES" => array('Y' => GetMessage("INNOVA_IBLOCKGALLERY_Y"), 'N' => GetMessage("INNOVA_IBLOCKGALLERY_N")),
			"DEFAULT" => "Y",
		),
		"DESCRIPTION_MAX" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("INNOVA_IBLOCKGALLERY_DESCRIPTION_MAX"),
			"TYPE" => "STRING",
			"DEFAULT" => "200",
		),
		"COUNT_COLS" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("INNOVA_IBLOCKGALLERY_COUNT_COLS"),
			"TYPE" => "STRING",
			"DEFAULT" => "3",
		),
		"COUNT_COLS_MOB" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("INNOVA_IBLOCKGALLERY_COUNT_COLS_MOB"),
			"TYPE" => "STRING",
			"DEFAULT" => "2",
		),
		"COUNT_IMAGE_PAGE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("INNOVA_IBLOCKGALLERY_COUNT_IMAGE_PAGE"),
			"TYPE" => "STRING",
			"DEFAULT" => "6",
		),
		"COUNT_IMAGE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("INNOVA_IBLOCKGALLERY_COUNT_IMAGE"),
			"TYPE" => "STRING",
			"DEFAULT" => "18",
		),

		"CACHE_TYPE"  =>  array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("COMP_PROP_CACHE_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => array("A" => GetMessage("COMP_PROP_CACHE_TYPE_AUTO")." ".GetMessage("COMP_PARAM_CACHE_MAN"), "Y" => GetMessage("COMP_PROP_CACHE_TYPE_YES"), "N" => GetMessage("COMP_PROP_CACHE_TYPE_NO")),
			"DEFAULT" => "N",
			"ADDITIONAL_VALUES" => "N",
			"REFRESH" => "Y" 
		),
	),
);
?>