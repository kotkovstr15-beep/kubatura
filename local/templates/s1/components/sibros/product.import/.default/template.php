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
<div class="main-form__grid">
    <div class="main-form__col">
        <div class="main-form__title">
            <h3>Импортировать товары</h3>
        </div>
        <div class="form-images form-images_photos mb-20">
            <h5 class="get_xml_title hidden_tick">Загрузите xml файл</h5>
            <div class="form-images__grid form-images__grid_slides">
                <label id="add_photo_to_slider" class="form-images__item form-images__item_add add_xml_to_import"
                       title="Загрузить файл">
                    <input id="add_xml_file" multiple="multiple" type="file" accept="application/simple-filter+xml/*">
                    <img class="xml_file_img" data-path="<?= $this->GetFolder()?>" src="<?= $this->GetFolder()?>/img/xml.png" alt="xml_icon">
                </label>
            </div>
        </div>
        <div class="form-item all_shops mb-20">
            <h5 class="select_shop_title hidden_tick">Выберите магазин</h5>
            <div class="form-item__field">
                <select name="SHOP" size="7">
                    <?php foreach ($arResult['SHOPS'] as $shop): ?>
                        <option value="<?= $shop['ID'] ?>"><?= $shop['NAME'] ?></option>
                    <? endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-item mb-20 all_categories_from_file_container">
            <h5 class="choose_categories_title">Категории</h5>
            <h6>Проверьте правильность автоподбора категорий, где нужно - исправьте.</h6>
            <div class="all_categories_from_file"></div>
        </div>
    </div>
    <div class="main-form__col">
        <button class="btn send_product_info">Импортировать</button>
    </div>
</div>
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