<?

IncludeModuleLangFile(__FILE__);
use \Bitrix\Main\ModuleManager;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

Class delight_lazyloadlite extends CModule{
	const MODULE_ID = "delight.lazyloadlite";
    var $MODULE_ID = "delight.lazyloadlite";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $errors;

    function __construct(){
        $arModuleVersion = array();
        
        include __DIR__ . '/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)){
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
		
        $this->MODULE_NAME = Loc::getMessage('DELIGHT_LAZYLITE_INSTALL_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('DELIGHT_LAZYLITE_INSTALL_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = Loc::getMessage('DELIGHT_LAZYLITE_INSTALL_PARTNER_NAME');
        $this->PARTNER_URI = 'https://it-angels.ru/speed/?utm_source=modules&utm_medium=organic&utm_campaign=modules&utm_content=modules_list&utm_term=delight.lazyloadlite';
    }

    function DoInstall(){
        $this->InstallDB();
        $this->InstallEvents();
        $this->InstallFiles();
        \Bitrix\Main\ModuleManager::RegisterModule(static::MODULE_ID);
		RegisterModuleDependences("main", "OnEndBufferContent", static::MODULE_ID, "DelightLazyLoadLite", "DelightLazyLoadLiteOnEndBufferContentHandler", 130);
		\Bitrix\Main\Config\Option::set(static::MODULE_ID, "enabled", "Y");
		\Bitrix\Main\Config\Option::set(static::MODULE_ID, "limitation_image_url", "mc.yandex.ru\nvk.com\nfacebook.com\ngoogletagmanager.com\nwww.google-analytics.com\nmail.ru");
        return true;
    }

    function DoUninstall(){
        $this->UnInstallDB();
        $this->UnInstallEvents();
        $this->UnInstallFiles();
        \Bitrix\Main\ModuleManager::UnRegisterModule(static::MODULE_ID);
		UnRegisterModuleDependences("main", "OnEndBufferContent", static::MODULE_ID, "DelightLazyLoadLite", "DelightLazyLoadLiteOnEndBufferContentHandler");
		CAdminNotify::DeleteByModule(static::MODULE_ID);
        return true;
    }

    function InstallDB(){
		return true;
    }

    function UnInstallDB(){
		return true;
    }

    function InstallEvents(){
        return true;
    }

    function UnInstallEvents(){
        return true;
    }

    function InstallFiles(){
        return true;
    }

    function UnInstallFiles(){
        return true;
    }
}