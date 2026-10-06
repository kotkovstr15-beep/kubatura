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
<link href="//cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@3/dark.css" rel="stylesheet">
<div class="title">
    <h1 class="title__head">Редактирование информации о компании</h1>
</div>
<form class="main-form form" method="post" enctype="multipart/form-data" action="/ajax/update_shop.php">
    <input type="hidden" name="SHOP_ID" value="<?= getShopIdByUserId(CUser::GetID()) ?>">
    <div class="main-form__grid">
        <div class="main-form__col main-form__col_edit-main">
            <div class="main-form__title">
                <h3>Основная информация</h3>
            </div>
            <div class="form-item">
                <p class="form-item__head">Название компании</p>
                <div class="form-item__field">
                    <input type="text" name="NAME" value="<?= $arResult['NAME'] ?>" class="form-field">
                </div>
            </div>
            <div class="form-item">
                <p class="form-item__head">Описание компании</p>
                <div class="form-item__field">
                    <textarea class="form-field" name="DETAIL_TEXT"><?= $arResult['~DETAIL_TEXT'] ?></textarea>
                </div>
            </div>
            <div class="form-item form-item_small">
                <p class="form-item__head">Телефон</p>
                <div class="form-item__field">
                    <div class="phones_block">
                        <?php if (!empty($arResult['PROPERTIES']['PHONE']['VALUE'])) { ?>
                            <?php foreach ($arResult['PROPERTIES']['PHONE']['VALUE'] as $index => $tel) { ?>
                                <input type="text" name="PHONE[]" value="<?= $tel ?>" class="form-field phone_field">
                            <? } ?>
                        <? } else { ?>
                        <? } ?>
                    </div>
                    <a class="form-item__add phone_add"><span></span>Добавить телефон</a>
                </div>
            </div>
            <div class="form-item form-item_small">
                <p class="form-item__head">Веб-сайт</p>
                <div class="form-item__field">
                    <input type="text" name="SITE_LINK" value="<?= $arResult['PROPERTIES']['SITE_LINK']['VALUE'] ?>"
                           class="form-field">
                </div>
            </div>
            <div class="form-item form-item_small">
                <p class="form-item__head">Аккаунт Instagram</p>
                <div class="form-item__field">
                    <input type="text" name="INSTA_LINK" value="<?= $arResult['PROPERTIES']['INSTA_LINK']['VALUE'] ?>"
                           class="form-field">
                </div>
            </div>
            <div class="form-item form-item_small">
                <p class="form-item__head">Группа VK</p>
                <div class="form-item__field">
                    <input type="text" name="VK_LINK" value="<?= $arResult['PROPERTIES']['VK_LINK']['VALUE'] ?>"
                           class="form-field">
                </div>
            </div>
        </div>
        <div class="main-form__col main-form__col_edit-address">
            <div class="main-form__title">
                <h3>Местонахождение</h3>
            </div>
            <div class="form-item form-item_small">
                <p class="form-item__head">Корпус</p>
                <div class="form-item__field">
                    <input type="text" name="CORPUS" value="<?= $arResult['PROPERTIES']['CORPUS']['VALUE'] ?>"
                           class="form-field">
                </div>
            </div>
            <div class="form-item form-item_small">
                <p class="form-item__head">Этаж</p>
                <div class="form-item__field">
                    <input type="text" name="ETAG" value="<?= $arResult['PROPERTIES']['ETAG']['VALUE'] ?>"
                           class="form-field">
                </div>
            </div>
            <div class="form-item form-item_small">
                <p class="form-item__head">Секция</p>
                <div class="form-item__field">
                    <input type="text" name="SECTION" value="<?= $arResult['PROPERTIES']['SECTION']['VALUE'] ?>"
                           class="form-field">
                </div>
            </div>
        </div>
        <div class="main-form__col">
            <div class="main-form__title">
                <h3>Логотип</h3>
            </div>
            <div class="form-images">
                <div class="form-images__grid form-images__grid_logo">
                    <div class="form-images__item logo">
                        <?php
                        $pathToImg = $arResult['DETAIL_PICTURE']['SRC'] ??
                            SITE_TEMPLATE_PATH . '/img/nologo.jpg';
                        ?>
                        <input type="hidden" name="OLD_LOGO" value="<?= $arResult['DETAIL_PICTURE']['ID'] ?>">
                        <div class="form-images__img" style="background-image: url('<?= $pathToImg ?>');"></div>
                        <span class="form-images__close ic ic-close ic-close-logo" onclick="removeLogo(this)"></span>
                    </div>
                    <label id="add_photo_to_logo" class="form-images__item form-images__item_add">
                        <input id="add_new_logo" type="file" accept="image/*">
                        <span class="form-images__img" title="Заменить логотип">
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
                    <?php foreach ($arResult['PROPERTIES']['SLIDE']['VALUE'] as $slide) :
                        $arFile = CFile::GetFileArray($slide);
                        ?>
                        <div class="form-images__item">
                            <input type="hidden" name="OLD_SLIDE[]" value="<?= $arFile['ID'] ?>">
                            <div class="form-images__img" style="background-image: url('<?= $arFile['SRC'] ?>');"></div>
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
            <button class="btn send_shop_info">Сохранить изменения</button>
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
<script src="//cdn.jsdelivr.net/npm/sweetalert2@9/dist/sweetalert2.min.js"></script>
