<?php
/** @var string $key */
/** @var array $arItem */
?>
<div class="bx-filter-parameters-box filter-main__col" data-active="false">
    <label for="<?= $arItem["CODE"] ?>" hidden></label>
    <select
            id="<?= $arItem["CODE"] ?>"
            data-code="<?= $arItem["CODE"] ?>"
            onchange="smartFilter.click($(this).find('option:selected')[0]);" class="filter-select"
            data-placeholder="<?= $arItem["NAME"] ?>" data-search="true"
            data-search-placeholder="Поиск <?= $arItem["SEARCH_NAME"] ?>">
        <option></option>
        <? foreach ($arItem["VALUES"] as $val => $ar): ?>
            <option
                <? echo $ar["CHECKED"] ? 'selected="selected"' : '' ?>
                    value="<?= $ar["HTML_VALUE"] ?>"
                    data-text="<?= $ar['VALUE']; ?>"
                    name="<?= $ar["CONTROL_NAME"] ?>"
                    id="<?= $ar["CONTROL_ID"] ?>">
                <?= $ar['VALUE'] ?>
            </option>
        <? endforeach; ?>
    </select>
    <a class="filter-main__reset" data-code="<?= $arItem['CODE'] ?>"><i class="ic ic-close"></i>Сбросить</a>

</div>