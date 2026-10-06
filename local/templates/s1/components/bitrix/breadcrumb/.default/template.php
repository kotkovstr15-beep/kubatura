<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/**
 * @global CMain $APPLICATION
 */

global $APPLICATION;

//delayed function must return a string
if(empty($arResult))
	return "";

$strReturn = '';
if (\Bitrix\Main\Loader::includeModule('iblock')) {
    foreach ($arResult as &$crumb) {
        if (empty($crumb['LINK']) || strpos($crumb['LINK'], '/catalog/') !== 0) continue;
        $section=\Kubatura\ShopPage\Service::sectionByPath(parse_url($crumb['LINK'], PHP_URL_PATH));
        if ($section && $section['ACTIVE'] !== 'Y') $crumb['LINK']='';
    }
    unset($crumb);
}

$strReturn .= '<div class="breadcrumbs" itemprop="http://schema.org/breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList"><ul class="breadcrumbs__list">';

$itemSize = count($arResult);
for($index = 0; $index < $itemSize; $index++)
{
	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
	if($arResult[$index]["LINK"] <> "" && $index != $itemSize-1)
	{
		$strReturn .= '
			<li id="bx_breadcrumb_'.$index.'" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
				<a href="'.$arResult[$index]["LINK"].'" title="'.$title.'" itemprop="item">
					<span itemprop="name">'.$title.'</span>
				</a>
				<meta itemprop="position" content="'.($index + 1).'" />
			</li>
			';
	}
	else
	{
		$strReturn .= '
			<li>'.$title.'</li>
		';
	}
}

$strReturn .= '</ul></div>';

return $strReturn;
