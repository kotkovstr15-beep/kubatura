<?php
/*ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);*/

use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class DelightLazyLoadLite{
	const MODULE_ID = "delight.lazyloadlite";
	
	// Функция получает текущий URL
	public static function GetCurrentUrl(){
		return (CMain::IsHTTPS() ? "https" : "http")."://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
	}
	
	// Функция проверяет, соблюдены ли все условия для работы модуля
	public static function CanProcess(){
		if ((isset($_GET["nolazyloadlite"])) OR (strpos($_SERVER["REQUEST_URI"], '/bitrix/admin/') === 0)){
			return false;
		}
		if(\Bitrix\Main\Config\Option::get(static::MODULE_ID, "enabled") != "Y"){
			return false;
		}
		// Проверяем ограничение по URL
		$LimitationUrlParamsStr = \Bitrix\Main\Config\Option::get(static::MODULE_ID, "limitation_url");
		if(!empty($LimitationUrlParamsStr)){
			$LimitationUrlParamsArr = explode("|",str_replace(array("\r\n", "\r", "\n"),"|",$LimitationUrlParamsStr));
			$CurrentUrl = self::GetCurrentUrl();
			foreach($LimitationUrlParamsArr as $LimitationUrlParam){
				if(stripos($CurrentUrl, $LimitationUrlParam) !== false){
					return false;
				}
			}
		}
		return true;
	}
	
	// Функция обработки контента
	public static function DelightLazyLoadLiteOnEndBufferContentHandler(&$content)
	{
		if (self::CanProcess() === true){
			$DisabledClassesStr = \Bitrix\Main\Config\Option::get(static::MODULE_ID, "limitation_classes");
			$DisabledElementUrlStr = \Bitrix\Main\Config\Option::get(static::MODULE_ID, "limitation_image_url");
			$GLOBALS["DelightLazyloadLiteModuleSettings"] = array(
				"DisabledClassesArr"	=> explode("|",str_replace(array("\r\n", "\r", "\n"),"|",$DisabledClassesStr."\n")),
				"DisabledElementUrlArr" => explode("|",str_replace(array("\r\n", "\r", "\n"),"|",$DisabledElementUrlStr."\n"))
			);
			
			// Тестирование регулярки:
			//preg_match_all('/((?i:<img\b[\s]*[^>]*?[\s]+))(src[\s]*=[\s]*)(\"|\')([^?\'\"]+)(\"|\')([^?\>]+>)/x', $content, $output_array);
			//Replacement: $0 loading=$2lazy$2
			$regex = "/
				((?i:<img\b[\s]*[^>]*?[\s]+))
				(src[\s]*=[\s]*)
				(\"|\')															#open_quote
				([^?'\"]+)														#href body
				(\\3)															#close_quote
				([^?\>]*>)														#after_src
			/x";
			$content = preg_replace_callback($regex, array(
				"DelightLazyLoadLite",
				"filter_img",
			), $content);
		}
	}
	
	private static function filter_img($match)
	{
		$element = $match[1];
		$src_attr = $match[2];
		$open_quote = $match[3];
		$link = $match[4];
		$close_quote = $match[5];
		$after_src = $match[6];

		// Обработка ограничения по наличию атрибута loading
		preg_match_all('#\s+(?<key>[^\'"]+)\s*=\s*(["\'])(?<val>.*?)\2(?=[\s>])#is', $match[0], $tmp, PREG_SET_ORDER);
		$attrs = []; foreach ($tmp as $attr) $attrs[$attr['key']] = $attr['val'];
		if(isset($attrs["loading"])){
			return $match[0];
		}
		
		// Обработка ограничения по классам
		if(isset($attrs['class'])){
			$classes = explode(" ", $attrs['class']);
			$classes = array_diff($classes, array(0, null, ''));
			// теперь $classes - нормализованный массив классов тега

			// Не обрабатываем элементы с указанными в настройках классами
			foreach($GLOBALS["DelightLazyloadLiteModuleSettings"]["DisabledClassesArr"] as $DisabledClass){
				if ((!empty($DisabledClass)) AND (in_array($DisabledClass, $classes))){
					return $match[0];
				}
			}
		}
		
		// Обработка ограничения по наличию строки в URL изображения
		foreach($GLOBALS["DelightLazyloadLiteModuleSettings"]["DisabledElementUrlArr"] as $DisabledElementUrl){
			if ((!empty($DisabledElementUrl)) AND (strripos($link, $DisabledElementUrl) !== false)){
				return $match[0];
			}
		}
			
		return $element.$src_attr.$open_quote.$link.$close_quote." loading=".$open_quote."lazy".$close_quote." ".$after_src;
	}
}