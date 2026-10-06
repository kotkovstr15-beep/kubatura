<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
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
<div class="title">
    <h1 class="title__head">
        <span>Личный кабинет арендатора <?= $arResult['SHOP']['FIELDS']['NAME'] ?></span>
    </h1>
    <div class="filter">
        <div class="filter__top">
            <a href="/personal/products/create/" class="btn">Добавить товар</a>
        </div>
        <div class="filter__sort">
            <div class="sort-box">
                <p class="sort-box__head">Сортировать</p>
                <select onchange="prepareSort(this)" class="sort-box__select" data-search="false">
                    <option <? if ($_GET['sort'] === 'date_create-desc') echo 'selected' ?>
                            value="sort=date_create-desc">по дате добавления
                    </option>
                    <option <? if ($_GET['sort'] === 'timestamp_x-desc') echo 'selected' ?>
                            value="sort=timestamp_x-desc">по дате изменения
                    </option>
                    <option <? if ($_GET['sort'] === 'PROPERTY_FILTER_PRICE-asc') echo 'selected' ?>
                            value="sort=PROPERTY_FILTER_PRICE-asc">по возрастанию цены
                    </option>
                    <option <? if ($_GET['sort'] === 'PROPERTY_FILTER_PRICE-desc') echo 'selected' ?>
                            value="sort=PROPERTY_FILTER_PRICE-desc">по убыванию цены
                    </option>
                    <option <? if ($_GET['sort'] === 'name-asc') echo 'selected' ?> value="sort=name-asc">по алфавиту
                    </option>
                </select>
            </div>
        </div>
    </div>
    <div class="similar__grid">
        <?php //начало
        foreach ($arResult['ITEMS'] as $key => $item) :
            $fields = $item['FIELDS'];
            $properties = $item['PROPERTIES'];
            ?>
            <div class="similar__col">
                <div class="similar-item similar-item_cab">
                    <div class="labels">
                        <?php if (!empty($properties['NEW']['VALUE'])): ?>
                            <div class="label label_blue">
                                <div class="label__wrap">
                                    <span><?= $properties['NEW']['NAME'] ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($properties['SALE']['VALUE'])): ?>
                            <div class="label">
                                <div class="label__wrap"><?= $properties['SALE']['VALUE'] ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <a href="<?= '/personal/products/edit/?ID=' . $fields['ID'] ?>" class="similar-item__img">
                        <?php if (!empty($fields['DETAIL_PICTURE']['SRC'])): ?>
                            <div><img src="<?= htmlspecialcharsbx($fields['DETAIL_PICTURE']['SRC']) ?>"
                                      loading="lazy" alt="<?= htmlspecialcharsbx($fields['NAME']) ?>"></div>
                        <?php endif; ?>
                    </a>
                    <div class="similar-item__content">
                        <p class="similar-item__category">
                            <span><?= $fields['SECTION_NAME'] ?></span>
                        </p>
                        <h5 class="similar-item__head">
                            <span class="similar-item__head-wrap">
                                <a href="<?= '/personal/products/edit/?ID=' . $fields['ID'] ?>"
                                   class="link-head">
                                    <span><?= $fields['NAME'] ?></span>
                                </a>
                            </span>
                        </h5>
                        <div class="price">
                            <p class="price__old"><?= $properties['SALE_PRICE']['VALUE'] ?></p>
                            <p class="price__new"><?= $properties['PRICE']['VALUE'] ?></p>
                        </div>
                    </div>
                    <div class="similar-item__action">
                        <div class="similar-item__action-grid">
                            <a href="<?= '/personal/products/edit/?ID=' . $fields['ID'] ?>"
                               class="btn btn_small change">Изменить
                                товар</a>
                            <a href="#" class="btn btn_small btn_red remove" data-element-id="<?= $fields['ID'] ?>">удалить</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?>
        <br/><?= $arResult["NAV_STRING"] ?>
    <?php endif; ?>
</div>
<div class="preloader">
    <svg class="preloader__image" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
        <path fill="currentColor"
              d="M304 48c0 26.51-21.49 48-48 48s-48-21.49-48-48 21.49-48 48-48 48 21.49 48 48zm-48 368c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48zm208-208c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48zM96 256c0-26.51-21.49-48-48-48S0 229.49 0 256s21.49 48 48 48 48-21.49 48-48zm12.922 99.078c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48c0-26.509-21.491-48-48-48zm294.156 0c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48c0-26.509-21.49-48-48-48zM108.922 60.922c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.491-48-48-48z">
        </path>
    </svg>
</div>
