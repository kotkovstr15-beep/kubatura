<?php
require_once __DIR__.'/Service.php';
require_once __DIR__.'/routes.php';
require_once __DIR__.'/Lifecycle.php';

// Invalidate page caches after edits/imports; live shop pages do not cache data.
AddEventHandler('iblock','OnAfterIBlockElementUpdate',array('Kubatura\\ShopPage\\Lifecycle','elementChanged'));
AddEventHandler('iblock','OnAfterIBlockElementAdd',array('Kubatura\\ShopPage\\Lifecycle','elementChanged'));
AddEventHandler('iblock','OnAfterIBlockElementSetPropertyValues',array('Kubatura\\ShopPage\\Lifecycle','propertiesChanged'));
AddEventHandler('iblock','OnAfterIBlockElementSetPropertyValuesEx',array('Kubatura\\ShopPage\\Lifecycle','propertiesChanged'));
