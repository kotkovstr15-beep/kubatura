<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
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

$templateData = array(
    'TEMPLATE_THEME' => $this->GetFolder() . '/themes/' . $arParams['TEMPLATE_THEME'] . '/colors.css',
    'TEMPLATE_CLASS' => 'bx-' . $arParams['TEMPLATE_THEME']
);

if (isset($templateData['TEMPLATE_THEME'])) {
    $this->addExternalCss($templateData['TEMPLATE_THEME']);
}
$this->addExternalCss("/bitrix/css/main/bootstrap.css");
$this->addExternalCss("/bitrix/css/main/font-awesome.css");

?>
<div class="bx-filter filter <?= $templateData["TEMPLATE_CLASS"] ?>">
    <div class="bx-filter-section container-fluid">
        <div class="filter__top">
            <button class="filter__toggle btn btn_toggle">
                <i class="ic ic-chevron-bottom"></i>
                <span><?= GetMessage("CUSTOM_CT_BCSF_FILTER_TITLE") ?></span>
            </button>
        </div>
        <div class="filter__wrap">
            <form id="main_filter_form" name="<? echo $arResult["FILTER_NAME"] . "_form" ?>"
                  action="<? echo $arResult["FORM_ACTION"] ?>"
                  method="get" class="smartfilter filter-main">
                <? foreach ($arResult["HIDDEN"] as $arItem): ?>
                    <input type="hidden" name="<? echo $arItem["CONTROL_NAME"] ?>" id="<? echo $arItem["CONTROL_ID"] ?>"
                           value="<? echo $arItem["HTML_VALUE"] ?>"/>
                <? endforeach; ?>
                <div class="row filter-main__grid">
                    <? include "display_types/catalog.php"; ?>
                    <? foreach ($arResult["ITEMS"] as $key => $arItem):
                        if (empty($arItem["VALUES"]) || isset($arItem["PRICE"])) {
                            continue;
                        }
                        if ($arItem["DISPLAY_TYPE"] == "A" && ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0)) {
                            continue;
                        }
                        if ($arItem['CODE'] === 'FILTER_PRICE') {
                            $arItem["NAME"] = GetMessage('CUSTOM_CT_BCSF_FILTER_PRICE');
                        }
                        if ($arItem["DISPLAY_TYPE"] === 'P') {
                            include "display_types/dropdown.php";
                            continue;
                        }
                        if ($arItem["DISPLAY_TYPE"] === 'A') {
                            include "display_types/price.php";
                            continue;
                        }
                        include "display_types/other.php";
                        ?>
                    <? endforeach; ?>
                </div>
                <div class="row" style="display: none">
                    <div class="col-xs-12 bx-filter-button-box">
                        <div class="bx-filter-block">
                            <div class="bx-filter-parameters-box-container">
                                <input
                                        class="btn btn-themes"
                                        type="submit"
                                        id="set_filter"
                                        name="set_filter"
                                        value="<?= GetMessage("CT_BCSF_SET_FILTER") ?>"
                                />
                                <input
                                        class="btn btn-link"
                                        type="submit"
                                        id="del_filter"
                                        name="del_filter"
                                        value="<?= GetMessage("CT_BCSF_DEL_FILTER") ?>"
                                />
                                <div class="bx-filter-popup-result <? if ($arParams["FILTER_VIEW_MODE"] == "VERTICAL") echo $arParams["POPUP_POSITION"] ?>"
                                     id="modef" <? if (!isset($arResult["ELEMENT_COUNT"])) {
                                    echo 'style="display:none"';
                                } ?> style="display: inline-block;">
                                    <? echo GetMessage("CT_BCSF_FILTER_COUNT",
                                        array("#ELEMENT_COUNT#" => '<span id="modef_num">' . intval($arResult["ELEMENT_COUNT"]) . '</span>')); ?>
                                    <span class="arrow"></span>
                                    <br/>
                                    <a href="<? echo $arResult["FILTER_URL"] ?>"
                                       target=""><? echo GetMessage("CT_BCSF_FILTER_SHOW") ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clb"></div>
                <div class="filter-main__action">
                    <div class="filter-main__action-grid">
                        <a onclick="smartFilter.clearFilter()" class="filter-main__reset-all">Сбросить всё <i
                                    class="ic ic-close"></i></a>
                    </div>
                </div>
            </form>
        </div>
        <div class="filter__sort">
            <div class="sort-box">
                <p class="sort-box__head">Сортировать</p>
                <select onchange="prepareSort(this)" class="sort-box__select" data-search="false">
                    <option <? if ($_GET['sort'] === 'PROPERTY_NEW-desc') echo 'selected' ?>
                            value="sort=PROPERTY_NEW-desc">новинки
                    </option>
                    <option <? if ($_GET['sort'] === 'PROPERTY_PRICE-asc') echo 'selected' ?>
                            value="sort=PROPERTY_PRICE-asc">по возрастанию цены
                    </option>
                    <option <? if ($_GET['sort'] === 'PROPERTY_PRICE-desc') echo 'selected' ?>
                            value="sort=PROPERTY_PRICE-desc">по убыванию цены
                    </option>
                    <option <? if ($_GET['sort'] === 'name-asc') echo 'selected' ?> value="sort=name-asc">по алфавиту
                    </option>
                </select>
            </div>
        </div>
        <div class="filter-choosed">
            <div class="filter-choosed__grid">
                <div class="filter-choosed__col">
                    <div class="filter-choosed__items"></div>
                </div>
                <div class="filter-choosed__col">
                    <a onclick="smartFilter.clearFilter()" class="btn btn_2 btn_bd">Сбросить всё</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var smartFilter = new JCSmartFilter('<?echo CUtil::JSEscape($arResult["FORM_ACTION"])?>', '<?=CUtil::JSEscape($arParams["FILTER_VIEW_MODE"])?>', <?=CUtil::PhpToJSObject($arResult["JS_FILTER_PARAMS"])?>);
    customInit(smartFilter);
    var lastUrl = location.href;
    var isItem = false;
    setInterval(() => {
        var newUrl = location.href;
        if (newUrl !== lastUrl) {
            if (!newUrl.match('filter')) {
                isItem = true;
            }
        }else{
            if(isItem){
                localStorage.removeItem('lastItemCode');
                isItem=false;
                location.reload();
            }
        }
    }, 100)
</script>