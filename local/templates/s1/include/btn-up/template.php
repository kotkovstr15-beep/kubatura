<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Page\Asset;

Asset::getInstance()->addCss(SITE_TEMPLATE_PATH.'/include/btn-up/style.css');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/include/btn-up/script.js');
?>

<button class="scs-btn-up" onclick="scrollToSection('#top')">
	<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M11.1118 4.16699L16.6673 10.0003M16.6673 10.0003L11.1118 15.8337M16.6673 10.0003L3.33398 10.0003" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
	</svg>
</button>