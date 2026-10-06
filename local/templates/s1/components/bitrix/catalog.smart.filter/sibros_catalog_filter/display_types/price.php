<?php
/** @var string $key */
/** @var array $arItem */

$minPrice = $arItem["VALUES"]["MIN"]["HTML_VALUE"] ?? $arItem["VALUES"]["MIN"]["VALUE"];
$maxPrice = setMaxPrice($arItem);
$minPrice = (int)$minPrice;
?>
<div class="bx-filter-parameters-box filter-main__col">
    <span class="bx-filter-container-modef"></span>
    <div class="bx-filter-parameters-box-title special_mark_to_box filter-item"
         onclick="smartFilter.hideFilterProps(this)">
        <div class="bx-filter-parameters-box-hint filter-item__toggle">
            <span><?= $arItem["NAME"] ?></span>
        </div>
    </div>
    <div class="bx-filter-block filter-item__dropdown" data-role="bx_filter_block">
        <div class="row bx-filter-parameters-box-container filter-item__content">
			<? $arCur = (is_array($arItem["VALUE"])) ? current($arItem["VALUE"]) : null; ?>
            <div class="filter-price">
                <div class="filter-price__form">
                    <div class="filter-price__item">
                        <div class="filter-price__field">
                            <input
                                    data-code="<?= $arItem['CODE'] ?>"
                                    class="min-price"
                                    data-price="min"
                                    type="text"
                                    name="<?= $arItem["VALUES"]["MIN"]["CONTROL_NAME"] ?>"
                                    id="<?= $arItem["VALUES"]["MIN"]["CONTROL_ID"] ?>"
                                    value="<?= $minPrice ?>"
                                    size="5"
                                    pattern="\d*"
                                    onkeyup="smartFilter.keyup(this)"
                            />
                        </div>
                        <p class="filter-price__descr">₽</p>
                    </div>
                    <div class="filter-price__sep"></div>
                    <div class="filter-price__item">
                        <p class="filter-price__descr">₽</p>
                        <div class="filter-price__field">
                            <input
                                    data-code="<?= $arItem['CODE'] ?>"
                                    class="max-price"
                                    data-price="max"
                                    type="text"
                                    name="<?= $arItem["VALUES"]["MAX"]["CONTROL_NAME"] ?>"
                                    id="<?= $arItem["VALUES"]["MAX"]["CONTROL_ID"] ?>"
                                    value="<?= $maxPrice ?>"
                                    size="5"
                                    onkeyup="smartFilter.keyup(this)"
                                    pattern="\d*"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-10 col-xs-offset-1 bx-ui-slider-track-container">
                <div class="bx-ui-slider-track" id="drag_track_<?= $key ?>">
                    <?php
                    $precision = $arItem["DECIMALS"] ? $arItem["DECIMALS"] : 0;
                    $step = ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"]) / 4;
                    ?>
                    <div class="bx-ui-slider-pricebar-vd" style="left: 0;right: 0;"
                         id="colorUnavailableActive_<?= $key ?>"></div>
                    <div class="bx-ui-slider-pricebar-vn" style="left: 0;right: 0;"
                         id="colorAvailableInactive_<?= $key ?>"></div>
                    <div class="bx-ui-slider-pricebar-v" style="left: 0;right: 0;"
                         id="colorAvailableActive_<?= $key ?>"></div>
                    <div class="bx-ui-slider-range" id="drag_tracker_<?= $key ?>"
                         style="left: 0;right: 0;">
                    </div>
                    <a class="bx-ui-slider-handle left" style="left:0;"
                       href="javascript:void(0)" id="left_slider_<?= $key ?>"></a>
                    <a class="bx-ui-slider-handle right" style="right:0;"
                       href="javascript:void(0)" id="right_slider_<?= $key ?>"></a>
                </div>
            </div>
            <?php $arJsParams = array(
                "leftSlider" => 'left_slider_' . $key,
                "rightSlider" => 'right_slider_' . $key,
                "tracker" => "drag_tracker_" . $key,
                "trackerWrap" => "drag_track_" . $key,
                "minInputId" => $arItem["VALUES"]["MIN"]["CONTROL_ID"],
                "maxInputId" => $arItem["VALUES"]["MAX"]["CONTROL_ID"],
                "minPrice" => $arItem["VALUES"]["MIN"]["VALUE"],
                "maxPrice" => $arItem["VALUES"]["MAX"]["VALUE"],
                "curMinPrice" => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
                "curMaxPrice" => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
                "fltMinPrice" => intval($arItem["VALUES"]["MIN"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MIN"]["FILTERED_VALUE"] : $arItem["VALUES"]["MIN"]["VALUE"],
                "fltMaxPrice" => intval($arItem["VALUES"]["MAX"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MAX"]["FILTERED_VALUE"] : $arItem["VALUES"]["MAX"]["VALUE"],
                "precision" => $arItem["DECIMALS"] ? $arItem["DECIMALS"] : 0,
                "colorUnavailableActive" => 'colorUnavailableActive_' . $key,
                "colorAvailableActive" => 'colorAvailableActive_' . $key,
                "colorAvailableInactive" => 'colorAvailableInactive_' . $key,
            ); ?>
            <script type="text/javascript">
                var trackBarObject = {
                    key: <?=$key?>,
                    keyAttr: 'trackBar',
                    data: new BX.Iblock.SmartFilter(<?=CUtil::PhpToJSObject($arJsParams)?>),
                };
                BX.ready(function () {
                    window['trackBar<?=$key?>'] = new BX.Iblock.SmartFilter(<?=CUtil::PhpToJSObject($arJsParams)?>);
                });
            </script>
        </div>
        <div style="clear: both"></div>
    </div>
    <a class="filter-main__reset" data-code="<?= $arItem['CODE'] ?>"><i class="ic ic-close"></i>Сбросить</a>
</div>
<?php
function setMaxPrice($arItem)
{
    if (is_null($arItem["VALUES"]["MAX"]["HTML_VALUE"])) {
        $price = $arItem["VALUES"]["MAX"]["VALUE"];
    } elseif ((int)$arItem["VALUES"]["MAX"]["HTML_VALUE"] === 0) {
        $price = $arItem["VALUES"]["MAX"]["VALUE"];
    } else {
        $price = (int)$arItem["VALUES"]["MAX"]["HTML_VALUE"];
    }
    return $price;
}
