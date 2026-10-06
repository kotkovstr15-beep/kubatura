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
?>12312
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
                        <select size="7">
                            <option>Напольные покрытия</option>
                            <option>Плитка</option>
                            <option>Двери</option>
                            <option>Окна</option>
                            <option>Потолки</option>
                            <option>Декоративно-отделочные материалы</option>
                            <option>Отделочный камень</option>
                        </select>
                    </div>
                    <div class="form-item__field">
                        <select size="7">
                            <option>Линолеум</option>
                            <option>ПВХ покрытия </option>
                            <option>Ламинат</option>
                            <option>Массивная доска</option>
                            <option>Террасная доска</option>
                            <option>Паркетная доска</option>
                            <option>Пробковые покрытия</option>
                            <option>Ковровые покрытия </option>
                            <option>Плинтусные и профильные системы</option>
                            <option>Специальные покрытия </option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-item">
                <p class="form-item__head">Название</p>
                <div class="form-item__field">
                    <input type="text" class="form-field">
                </div>
            </div>
            <div class="form-item form-item_medium">
                <p class="form-item__head">Символьный код</p>
                <div class="form-item__field">
                    <input type="text" class="form-field">
                </div>
            </div>
            <div class="form-item">
                <p class="form-item__head">Описание товара</p>
                <div class="form-item__field">
                    <textarea class="form-field" rows="5"></textarea>
                </div>
            </div>
            <div class="form-item form-item_little">
                <p class="form-item__head">Цена</p>
                <div class="form-item__field">
                    <div class="form-field-curr">
                        <input type="text" class="form-field" maxlength="10">
                        <span class="form-field-curr__text">₽</span>
                    </div>
                    <a href="#" class="form-item__add"><span></span> Добавить цену по акции</a>
                </div>
            </div>
            <div class="form-item form-item_little">
                <p class="form-item__head">Количество</p>
                <div class="form-item__field">
                    <input type="text" class="form-field">
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
                            <select>
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
                            <select data-search="true">
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
                            <input type="text" class="form-field">
                        </div>
                    </div>
                    <div class="form-item form-item_medium">
                        <p class="form-item__head">Назначение</p>
                        <div class="form-item__field">
                            <input type="text" class="form-field">
                        </div>
                    </div>
                    <div class="form-item form-item_little">
                        <p class="form-item__head">Гарантия</p>
                        <div class="form-item__field">
                            <div class="form-field-curr">
                                <input type="text" class="form-field" maxlength="3">
                                <span class="form-field-curr__text">мес</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-item form-item_medium">
                        <p class="form-item__head">Доставка</p>
                        <div class="form-item__field">
                            <textarea class="form-field" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="main-form__col main-form__col_specs">
                    <div class="form-item form-item_little">
                        <p class="form-item__head">Вес</p>
                        <div class="form-item__field">
                            <div class="form-field-curr">
                                <input type="text" class="form-field" maxlength="10">
                                <span class="form-field-curr__text">кг</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-item form-item_little">
                        <p class="form-item__head">Размер</p>
                        <div class="form-item__field">
                            <div class="form-field-curr">
                                <input type="text" class="form-field" maxlength="10">
                                <span class="form-field-curr__text">мм</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-item form-item_little">
                        <p class="form-item__head">Единица измерения</p>
                        <div class="form-item__field">
                            <input type="text" class="form-field">
                        </div>
                    </div>
                    <div class="form-item form-item_little">
                        <p class="form-item__head">Защитный слой</p>
                        <div class="form-item__field">
                            <div class="form-field-curr">
                                <input type="text" class="form-field" maxlength="10">
                                <span class="form-field-curr__text">мм</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-item form-item_little">
                        <p class="form-item__head">Количество</p>
                        <div class="form-item__field">
                            <div class="form-field-curr">
                                <input type="text" class="form-field" maxlength="10">
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
                        <input type="file" accept="image/*">
                        <div class="form-images__img"><span></span></div>
                    </label>
                </div>
            </div>
        </div>
        <div class="main-form__col">
            <button class="btn">Добавить товар в каталог</button>
        </div>
    </div>
</form>