<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
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
<select class="filter-select filter-select_catalog" data-search="false"
        onchange="document.location=this.options[this.selectedIndex].value">
    <?
    $s = false;
    foreach ($arResult['SECTIONS'] as &$arSection) {
        $attr = '';
        if ($arSection['ID'] == $arParams['CURENT_ID'] || $arSection['CODE'] == $arParams['CURENT_CODE']) {
            $attr = ' selected';
            $s = true;
        }
        ?>
        <option value='<?= htmlspecialcharsbx(rtrim($arSection['SECTION_PAGE_URL'], '/') . '/') ?>'<?= $attr ?>><?= $arSection['NAME'] ?></option><?
    } ?>
    <? if (!$s): ?>
        <option value='<?= htmlspecialcharsbx(rtrim($arResult['SECTION']['SECTION_PAGE_URL'], '/') . '/') ?>' selected
                disabled><?= $arResult['SECTION']['NAME'] ?></option><? endif; ?>
</select>
