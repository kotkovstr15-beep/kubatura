<?
use Bitrix\Main\Loader;

global $DBType;

$arClasses=array(
	'BFLazyLoad' => 'lib/BFLazyLoad.php',
);
CModule::AddAutoloadClasses("brainforce.lazyload",$arClasses);
