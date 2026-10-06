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
<? if (!empty($arResult['ID'])) { ?>
    <div class="title">
        <h1 class="title__head">Редактирование товара [ID - <?= $_GET['ID'] ?>]</h1>
    </div>
    <form class="main-form form" method="post" enctype="multipart/form-data" action="/ajax/edit_product.php">
        <input type="hidden" name="ID" value="<?= $arResult['ID'] ?>">
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
                    <p class="form-item__head">Название</p>
                    <div class="form-item__field">
                        <input type="text" name="NAME" value="<?= $arResult['NAME'] ?>" class="form-field" required>
                    </div>
                </div>
                <div class="form-item">
                    <p class="form-item__head">Описание товара</p>
                    <div class="form-item__field">
                        <textarea name="DETAIL_TEXT" class="form-field" rows="5"
                                  required><?= $arResult['~DETAIL_TEXT'] ?></textarea>
                    </div>
                </div>
                <div class="form-item form-item_little">
                    <p class="form-item__head">Цена</p>
                    <div class="form-item__field">
                        <div class="form-field-curr">
                          <input name="PRICE" type="text"
                                 value="<?= str_replace(' ₽', '', $arResult['PROPERTIES']['PRICE']['VALUE']) ?>"
                                 class="form-field" maxlength="10">
                            <span class="form-field-curr__text">₽</span>
                        </div>
                        <? if (empty($arResult['PROPERTIES']['PRICE']['VALUE'])) { ?>
                            <a class="form-item__add add_sale_price_field"><span></span> Добавить старую цену</a>
                        <? } ?>
                    </div>
                </div>
                <div class="form-item form-item_little sale_price_container">
                    <p class="form-item__head">Старая цена</p>
                    <div class="form-item__field">
                        <div class="form-field-curr">
                          <input name="SALE_PRICE" type="text"
                                 value="<?= str_replace(' ₽', '', $arResult['PROPERTIES']['SALE_PRICE']['VALUE']) ?>"
                                 class="form-field" maxlength="10">

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
                                  <option >Нет</option>
                                    <?php foreach ($arResult['FORM_VALUES']['COLOR'] as $id => $color) : ?>
                                        <?
                                        $current = false;
                                        if ($arResult['PROPERTIES']['COLOR']['VALUE_ENUM_ID'] == $id) {
                                            $current = true;
                                        } ?>
                                        <option value="<?= $id ?>" <?= ($current ? 'selected=selected' : '') ?>><?= $color ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-item form-item_medium">
                            <p class="form-item__head">Материал</p>
                            <select name="MATERIALY" data-search="true">
                              <option >Нет</option>
                                <?php foreach ($arResult['FORM_VALUES']['MATERIALY'] as $id => $material) : ?>
                                    <?
                                    $current = false;
                                    if ($arResult['PROPERTIES']['MATERIALY']['VALUE_ENUM_ID'] == $id) {
                                        $current = true;
                                    } ?>
                                    <option value="<?= $id ?>" <?= ($current ? 'selected=selected' : '') ?>><?= $material ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-item form-item_medium">
                            <p class="form-item__head">Производитель</p>
                            <div class="form-item__field">
                                <input name="MANUFACTURER" type="text" value="<?= $arResult['PROPERTIES']['MANUFACTURER']['VALUE'] ?>"
                                       class="form-field">
                            </div>
                        </div>

                        <div class="form-item form-item_medium">
                            <p class="form-item__head">Назначение</p>
                            <div class="form-item__field">
                                <input name="USE" type="text" value="<?= $arResult['PROPERTIES']['USE']['VALUE'] ?>"
                                       class="form-field">
                            </div>
                        </div>
                        <div class="form-item form-item_little">
                            <p class="form-item__head">Гарантия</p>
                            <div class="form-item__field">
                                <div class="form-field-curr">
                                    <input name="WARRANTY" type="text" value="<?= str_replace(' мес', '',
                                        $arResult['PROPERTIES']['WARRANTY']['VALUE']) ?>" class="form-field"
                                           maxlength="3">
                                    <span class="form-field-curr__text">мес</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-item form-item_medium">
                            <p class="form-item__head">Доставка</p>
                            <div class="form-item__field">
                                <textarea name="DELIVERY" class="form-field"
                                          rows="3"><?= $arResult['PROPERTIES']['DELIVERY']['VALUE'] ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="main-form__col main-form__col_specs">
                        <div class="form-item form-item_little">
                            <p class="form-item__head">Вес</p>
                            <div class="form-item__field">
                                <div class="form-field-curr">
                                    <input name="VESY" t-ype="text" value="<?= str_replace(' кг', '',
                                        $arResult['PROPERTIES']['VESY']['VALUE']) ?>" class="form-field" maxlength="10">
                                    <span class="form-field-curr__text">кг</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-item form-item_little">
                            <p class="form-item__head">Размер</p>
                            <div class="form-item__field">
                                <div class="form-field-curr">
                                    <input name="RAZMER" type="text" value="<?= str_replace(' кг', '',
                                        $arResult['PROPERTIES']['RAZMER']['VALUE']) ?>" class="form-field"
                                           maxlength="10">
                                    <span class="form-field-curr__text">мм</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-item form-item_little">
                            <p class="form-item__head">Единица измерения</p>
                            <div class="form-item__field">
                                <input name="UNIT_OF_MEASURE" type="text"
                                       value="<?= $arResult['PROPERTIES']['UNIT_OF_MEASURE']['VALUE'] ?>"
                                       class="form-field">
                            </div>
                        </div>
                        <div class="form-item form-item_little">
                            <p class="form-item__head">Защитный слой</p>
                            <div class="form-item__field">
                                <div class="form-field-curr">
                                    <input name="ZACHITNYSLOY" type="text" value="<?= str_replace(' мм', '',
                                        $arResult['PROPERTIES']['ZACHITNYSLOY']['VALUE']) ?>" class="form-field"
                                           maxlength="10">
                                    <span class="form-field-curr__text">мм</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-item form-item_little">
                            <p class="form-item__head">Количество</p>
                            <div class="form-item__field">
                                <div class="form-field-curr">
                                    <input name="KOLVOMKVYPAK" type="text" value="<?= str_replace(' м.кв/упак', '',
                                        $arResult['PROPERTIES']['KOLVOMKVYPAK']['VALUE']) ?>" class="form-field"
                                           maxlength="10">
                                    <span class="form-field-curr__text">м<sup>2</sup>/упак</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="main-form__col">
                <div class="main-form__title">
                    <h3>Картинка для анонса</h3>
                </div>
                <div class="form-images">
                    <div class="form-images__grid form-images__grid_preview">
                        <div class="form-images__item preview">
                            <?php
                            $pathToImgPreview = $arResult['PREVIEW_PICTURE']['SRC'] ??
                                SITE_TEMPLATE_PATH . '/img/nologo.jpg';
                            ?>
                            <input type="hidden" name="OLD_PREVIEW_PICTURE"
                                   value="<?= $arResult['PREVIEW_PICTURE']['ID'] ?>">
                            <div class="form-images__img"
                                 style="background-image: url('<?= $pathToImgPreview ?>');"></div>
                            <span class="form-images__close ic ic-close ic-close-logo"
                                  onclick="removeLogo(this)"></span>
                        </div>
                        <label id="add_photo_to_preview" class="form-images__item form-images__item_add">
                            <input id="add_new_preview" type="file" accept="image/*">
                            <span class="form-images__img" title="Заменить картинку для анонса">
                            <span></span>
                        </span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="main-form__col">
                <div class="main-form__title">
                    <h3>Детальная картинка</h3>
                </div>
                <div class="form-images">
                    <div class="form-images__grid form-images__grid_detail">
                        <div class="form-images__item detail">
                            <?php
                            $pathToImgDetail = $arResult['DETAIL_PICTURE']['SRC'] ??
                                SITE_TEMPLATE_PATH . '/img/nologo.jpg';
                            ?>
                            <input type="hidden" name="OLD_DETAIL_PICTURE"
                                   value="<?= $arResult['DETAIL_PICTURE']['ID'] ?>">
                            <div class="form-images__img"
                                 style="background-image: url('<?= $pathToImgDetail ?>');"></div>
                            <span class="form-images__close ic ic-close ic-close-logo"
                                  onclick="removeLogo(this)"></span>
                        </div>
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
                    <h3>Фотографии</h3>
                </div>
                <div class="form-images form-images_photos">
                    <div class="form-images__grid form-images__grid_slides">
                        <?php foreach ($arResult['PROPERTIES']['MORE_PHOTO']['VALUE'] as $slide) :
                            $arFile = CFile::GetFileArray($slide);
                            ?>
                            <div class="form-images__item">
                                <input type="hidden" name="OLD_SLIDE[]" value="<?= $arFile['ID'] ?>">
                                <div class="form-images__img"
                                     style="background-image: url('<?= $arFile['SRC'] ?>');"></div>
                                <span onclick="removePhotoInSlider(this)"
                                      class="form-images__close ic ic-close"
                                      data-file-name="<?= $arFile['FILE_NAME'] ?>"></span>
                            </div>
                        <?php endforeach; ?>
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
                <button class="btn send_product_info">Сохранить изменения</button>
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
        DirsService.allDirs = <?= CUtil::PhpToJSObject($arResult['SECTIONS_TREE'])?>;
        DirsService.currentSectD1 = <?= CUtil::PhpToJSObject($arResult['CURRENT_SECTION_DEPTH_1'])?>;
        DirsService.currentSectD2 = <?= CUtil::PhpToJSObject($arResult['CURRENT_SECTION_DEPTH_2'])?>;
    </script>
<? } else { ?>
    Данный товар [ID - <?= $_GET['ID']; ?>] не принадлежит вашему магазину! Вы не можете его редактировать.
<? } ?>
