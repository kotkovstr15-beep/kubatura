<?php
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", "OnBeforeIBlockElementUpdateHandler");

function OnBeforeIBlockElementUpdateHandler(&$arFields)
{
    foreach ($arFields['PROPERTY_VALUES'][22] as $key => $value){
        $price =& $arFields['PROPERTY_VALUES'][22][$key]['VALUE'];
        $price = preg_replace("/\D/", "", $price);
    }
//    die(11);
}