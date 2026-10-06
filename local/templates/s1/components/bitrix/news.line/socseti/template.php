<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
	<?foreach($arResult["ITEMS"] as $arItem):?>
		<?
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
		?>
		<?
		$socialHtml = preg_replace_callback('#<a\b([^>]*)>#i', function ($match) {
			$attributes = $match[1];
			if (stripos($attributes, 'social-item') === false || stripos($attributes, 'aria-label=') !== false) {
				return $match[0];
			}
			preg_match('#href=["\']([^"\']+)#i', $attributes, $hrefMatch);
			$href = isset($hrefMatch[1]) ? $hrefMatch[1] : '';
			$label = 'Социальная сеть Кубатуры';
			foreach (array('ok.ru' => 'Одноклассники', 't.me' => 'Telegram', 'max.ru' => 'MAX', 'dzen.ru' => 'Дзен', 'vk.com' => 'ВКонтакте') as $host => $network) {
				if (stripos($href, $host) !== false) {
					$label = $network . ' Кубатуры';
					break;
				}
			}
			return '<a' . $attributes . ' aria-label="' . htmlspecialcharsbx($label) . '">';
		}, (string)$arItem["PREVIEW_TEXT"]);
		?>
		<span id="<?=$this->GetEditAreaId($arItem['ID']);?>"><?=$socialHtml?></span>
	<?endforeach;?>
