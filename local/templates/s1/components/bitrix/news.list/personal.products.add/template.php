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
?>
<div class="title">
    <h1 class="title__head">Добавление нового товара</h1>
</div>
<form class="main-form form">
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
                <p class="form-item__head">Название</p>
                <div class="form-item__field">
                    <input name="NAME" type="text" class="form-field">
                </div>
            </div>
            <div class="form-item form-item_medium">
                <p class="form-item__head">Символьный код</p>
                <div class="form-item__field">
                    <input name="CODE" type="text" class="form-field">
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
                    <a href="#" class="form-item__add"><span></span> Добавить цену по акции</a>
                </div>
            </div>
            <div class="form-item form-item_little">
                <p class="form-item__head">Цена по акции</p>
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
                            <select name="COLOR">
                                <option>Белый</option>
                                <option>Черный</option>
                                <option>Красный</option>
                                <option>Синий</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-item form-item_medium">
                        <p class="form-item__head">Материал</p>
                        <div class="form-item__field">
                            <select name="MATERIALY" data-search="true">
                                <option>Материал 1</option>
                                <option>Материал 2</option>
                                <option>Материал 3</option>
                                <option>Материал 4</option>
                                <option>Материал 1</option>
                                <option>Материал 2</option>
                                <option>Материал 3</option>
                                <option>Материал 4</option>
                                <option>Материал 1</option>
                                <option>Материал 2</option>
                                <option>Материал 3</option>
                                <option>Материал 4</option>
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
                            <input name="" type="text" class="form-field">
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
                                <input name="RAZMER_MM" type="text" class="form-field" maxlength="10">
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
                <div class="form-images__grid">
                    <div class="form-images__item">
                        <div class="form-images__img" style="background-image: url('img/post/post-img-2.jpg');"></div>
                        <p class="form-images__img-descr">Основное фото</p>
                        <span class="form-images__close ic ic-close"></span>
                    </div>
                    <label class="form-images__item form-images__item_add">
                        <input name="" type="file" accept="image/*">
                        <div class="form-images__img"><span></span></div>
                    </label>
                </div>
            </div>
        </div>
        <div class="main-form__col">
            <button class="btn send_shop_info">Добавить товар в каталог</button>
        </div>
    </div>
</form>
<div class="preloader">
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
