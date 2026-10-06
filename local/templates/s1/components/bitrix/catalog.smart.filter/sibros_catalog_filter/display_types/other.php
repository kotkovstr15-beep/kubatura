<?php
/** @var string $key */
/** @var array $arItem */
?>
    <div class="bx-filter-parameters-box filter-main__col" data-active="false">
        <span class="bx-filter-container-modef"></span>
        <div class="bx-filter-parameters-box-title filter-item special_mark_to_box"
             onclick="smartFilter.hideFilterProps(this)">
            <div class="bx-filter-parameters-box-hint filter-item__toggle">
                <span><?= $arItem["NAME"] ?></span>
            </div>
        </div>
        <div class="bx-filter-block filter-item__dropdown" data-role="bx_filter_block">
            <div class="row bx-filter-parameters-box-container filter-item__content">
                <div class="filter-item__options">
					<? $arCur = (is_array($arItem["VALUE"])) ? current($arItem["VALUE"]) : null;
                    foreach ($arItem["VALUES"] as $val => $ar): ?>
                        <label style="width: 100%;"
                               class="form-option form-option_checkbox"
                               data-role="label_<?= $ar["CONTROL_ID"] ?>"
                               for="<?= $ar["CONTROL_ID"] ?>">
                            <input
                                    data-code="<?= $arItem["CODE"] ?>"
                                    type="checkbox"
                                    data-text="<?= $ar['VALUE']; ?>"
                                    value="<?= $ar["HTML_VALUE"] ?>"
                                    name="<?= $ar["CONTROL_NAME"] ?>"
                                    id="<?= $ar["CONTROL_ID"] ?>"
                                <?= $ar["CHECKED"] ? 'checked="checked"' : '' ?>
                                    onclick="smartFilter.click(this)"/>
                            <span class=""
                                  title="<?= $ar["VALUE"] ?>">
                                <?= $ar["VALUE"] ?>
                                <?php /* if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])): ?><span data-role="count_<?= $ar["CONTROL_ID"] ?>">&nbsp;(<?= $ar["ELEMENT_COUNT"] ?>)</span><?php endif; */ ?>
                            </span>
                        </label><br>
                        <? /*                         <span class=""
                                  title="<?= $ar["VALUE"] ?>">
                                <?= $ar["VALUE"] ?>
                                <?php if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($ar["ELEMENT_COUNT"])): ?>&nbsp;(
                                    <span data-role="count_<?= $ar["CONTROL_ID"] ?>">
                                                                <? echo $ar["ELEMENT_COUNT"]; ?>
                                                            </span>)
                                <? endif; ?>
                                                    </span>)*/
                        ?>
                    <? endforeach; ?>
                </div>
            </div>
            <div style="clear: both"></div>
        </div>
        <a class="filter-main__reset" data-code="<?= $arItem['CODE'] ?>"><i class="ic ic-close"></i>Сбросить</a>
    </div>