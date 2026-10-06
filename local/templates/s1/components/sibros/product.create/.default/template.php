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

global $USER;
?>
<div class="title">
    <h1 class="title__head">Добавление нового товара</h1>
</div>
<form class="main-form form">
    <input type="hidden" name="SHOP_ID" value="<?= getShopIdByUserId($USER->GetID()) ?>">
    <div class="main-form__grid">
        <div class="main-form__col">
            <div class="main-form__title">
                <h3>Основные параметры</h3>
            </div>
            <div class="form-item">
                <p class="form-item__head">Категории</p>
                <div class="form-item__fields">
                    <div class="form-item__field">
                        <select name="CATEGORY" size="7">
                        </select>
                    </div>
                    <div class="form-item__field">
                        <select name="SUB_CATEGORY" size="7">
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-item">
                <p class="form-item__head">Название*</p>
                <div class="form-item__field">
                    <input name="NAME" type="text" class="form-field">
                </div>
            </div>
            <div class="main-form__col">
                <div class="main-form__title">
                    <h3>Детальная картинка*</h3>
                </div>
                <div class="form-images">
                    <div class="form-images__grid form-images__grid_detail">
                        <label id="add_photo_to_detail" class="form-images__item form-images__item_add">
                            <input id="add_new_detail" type="file" accept="image/*">
                            <span class="form-images__img" title="Заменить детальную картинку">
                            <span></span>
                        </span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="main-form__col">
                <div class="main-form__title">
                    <h3>Картинка для анонса</h3>
                </div>
                <div class="form-images">
                    <div class="form-images__grid form-images__grid_preview">
                        <label id="add_photo_to_preview" class="form-images__item form-images__item_add">
                            <input id="add_new_preview" type="file" accept="image/*">
                            <span class="form-images__img" title="Заменить превьюшку картинку">
                            <span></span>
                        </span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-item">
                <p class="form-item__head">Описание товара</p>
                <div class="form-item__field">
                    <textarea name="DETAIL_TEXT" class="form-field" rows="5"></textarea>
                </div>
            </div>
            <div class="form-item form-item_little">
                <p class="form-item__head">Цена</p>
                <div class="form-item__field">
                    <div class="form-field-curr">
                      <input name="PRICE" type="text" class="form-field" maxlength="10">

                        <span class="form-field-curr__text">₽</span>
                    </div>
                    <a class="form-item__add add_sale_price_field"><span></span> Добавить старую цену</a>
                </div>
            </div>
            <div class="form-item form-item_little sale_price_container">
                <p class="form-item__head">Старая цена</p>
                <div class="form-item__field">
                    <div class="form-field-curr">
                        <input name="SALE_PRICE" type="text" class="form-field" maxlength="10">
                        <span class="form-field-curr__text">₽</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="main-form__col">
            <div class="main-form__title">
                <h3>Характеристики</h3>
            </div>
            <div class="main-form__grid">
                <div class="main-form__col main-form__col_specs">
                    <div class="form-item form-item_medium">
                        <p class="form-item__head">Цвет</p>
                        <div class="form-item__field">
                            <select name="COLOR" data-search="true">
                              <option selected>Нет</option>
                                <?php foreach ($arResult['COLOR'] as $id => $color) : ?>
                                    <option value="<?= $id ?>"><?= $color ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-item form-item_medium">
                        <p class="form-item__head">Материал</p>
                        <div class="form-item__field">
                            <select name="MATERIALY" data-search="true">
                              <option selected>Нет</option>
                                <?php foreach ($arResult['MATERIALY'] as $id => $material) : ?>
                                    <option value="<?= $id ?>"><?= $material ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-item form-item_medium">
                        <p class="form-item__head">Производитель</p>
                        <div class="form-item__field">
                            <input name="MANUFACTURER" type="text" class="form-field">
                        </div>
                    </div>
                    
                    <div class="form-item form-item_medium">
                        <p class="form-item__head">Назначение</p>
                        <div class="form-item__field">
                            <input name="USE" type="text" class="form-field">
                        </div>
                    </div>
                    <div class="form-item form-item_little">
                        <p class="form-item__head">Гарантия</p>
                        <div class="form-item__field">
                            <div class="form-field-curr">
                                <input name="WARRANTY" type="text" class="form-field" maxlength="3">
                                <span class="form-field-curr__text">мес</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-item form-item_medium">
                        <p class="form-item__head">Доставка</p>
                        <div class="form-item__field">
                            <textarea name="DELIVERY" class="form-field" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="main-form__col main-form__col_specs">
                    <div class="form-item form-item_little">
                        <p class="form-item__head">Вес</p>
                        <div class="form-item__field">
                            <div class="form-field-curr">
                                <input name="VESY" type="text" class="form-field" maxlength="10">
                                <span class="form-field-curr__text">кг</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-item form-item_little">
                        <p class="form-item__head">Размер</p>
                        <div class="form-item__field">
                            <div class="form-field-curr">
                                <input name="RAZMER" type="text" class="form-field" maxlength="10">
                                <span class="form-field-curr__text">мм</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-item form-item_little">
                        <p class="form-item__head">Единица измерения</p>
                        <div class="form-item__field">
                            <input name="UNIT_OF_MEASURE" type="text" class="form-field">
                        </div>
                    </div>
                    <div class="form-item form-item_little">
                        <p class="form-item__head">Защитный слой</p>
                        <div class="form-item__field">
                            <div class="form-field-curr">
                                <input name="ZACHITNYSLOY" type="text" class="form-field" maxlength="10">
                                <span class="form-field-curr__text">мм</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-item form-item_little">
                        <p class="form-item__head">Количество</p>
                        <div class="form-item__field">
                            <div class="form-field-curr">
                                <input name="KOLVOMKVYPAK" type="text" class="form-field" maxlength="10">
                                <span class="form-field-curr__text">м<sup>2</sup>/упак</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="main-form__col">
            <div class="main-form__title">
                <h3>Фотографии</h3>
            </div>
            <div class="form-images form-images_photos">
                <div class="form-images__grid form-images__grid_slides">
                    <label id="add_photo_to_slider" class="form-images__item form-images__item_add">
                        <input id="add_new_slide" multiple="multiple" type="file" accept="image/*">
                        <span class="form-images__img" title="Загрузите одну или несколько фотографий">
                            <span></span>
                        </span>
                    </label>
                </div>
            </div>
        </div>
        <div class="main-form__col">
            <button class="btn send_product_info">Добавить товар в каталог</button>
        </div>
    </div>
</form>
<div class="preloader preloader_hidden">
    <svg class="preloader__image" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
        <path fill="currentColor"
              d="M304 48c0 26.51-21.49 48-48 48s-48-21.49-48-48 21.49-48 48-48 48 21.49 48 48zm-48 368c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48zm208-208c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48zM96 256c0-26.51-21.49-48-48-48S0 229.49 0 256s21.49 48 48 48 48-21.49 48-48zm12.922 99.078c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48c0-26.509-21.491-48-48-48zm294.156 0c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48c0-26.509-21.49-48-48-48zM108.922 60.922c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.491-48-48-48z">
        </path>
    </svg>
</div>
<script>
    DirsService
        .allDirs = <?= CUtil::PhpToJSObject($arResult['SECTIONS_TREE'])?>;
</script>
