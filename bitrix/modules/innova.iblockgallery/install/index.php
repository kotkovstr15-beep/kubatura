<?
IncludeModuleLangFile(__FILE__);
Class innova_iblockgallery extends CModule
{
	const MODULE_ID = 'innova.iblockgallery';
	var $MODULE_ID = 'innova.iblockgallery'; 
	protected $tableName = 'innova_iblockgallery';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("innova.iblockgallery_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("innova.iblockgallery_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("innova.iblockgallery_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("innova.iblockgallery_PARTNER_URI");
	}

	function InstallDB($arParams = array())
	{
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		return true;
	}

	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles($arParams = array())
	{
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/components'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					CopyDirFiles($p.'/'.$item, $_SERVER['DOCUMENT_ROOT'].'/bitrix/components/'.$item, $ReWrite = True, $Recursive = True);
				}
				closedir($dir);
			}
		}
		return true;
	}

	function UnInstallFiles()
	{
		if (is_dir($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/innova/iblockgallery/')) { 
			\Bitrix\Main\IO\Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/innova/iblockgallery/');
		}
		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;
		$this->InstallFiles();
		RegisterModule(self::MODULE_ID);
	}

	function DoUninstall()
	{
		global $APPLICATION;
		UnRegisterModule(self::MODULE_ID);
		$this->UnInstallFiles();
	}
}
?>
