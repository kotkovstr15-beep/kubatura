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
require_once $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/include/performance/webp.php';
?>
<div class="title">
    <h1 class="title__head">
        <span>Личный кабинет арендатора <?= $arResult['NAME'] ?></span>
    </h1>
</div>
<div class="cab-line">
    <div class="cab-line__grid">
        <a href="#" class="cab-line__item active">Информация о компании</a>
        <a href="products/" class="cab-line__item">Товары в каталоге</a>
    </div>
    <a href="/personal/products/create/" class="cab-line__btn"><span>Добавить товар</span></a>
</div>
<div class="container__full">
    <div class="post-grid post-grid_cab">
        <div class="post-img">
            <div class="post-gallery swiper-container">
                <div class="swiper-wrapper">
                    <?php foreach ($arResult['DISPLAY_PROPERTIES']['SLIDE']['FILE_VALUE'] as $slide) : ?>
                        <div class="post-gallery__item swiper-slide"
                             style="background-image: url(<?= $slide['SRC'] ?>);"></div>
                    <?php endforeach; ?>
                </div>
                <div class="post-gallery__nav">
                    <div class="swiper-nav">
                        <div class="swiper-button-prev"><i class="ic ic-prev"></i></div>
                        <div class="swiper-button-next"><i class="ic ic-next"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="post-info">
            <?php
            $pathToImg = $arResult['PROPERTIES']['LOGO_1']['SRC'] ??
                SITE_TEMPLATE_PATH . '/img/nologo.jpg';
            $pathToImg = kubaturaWebpUrl($pathToImg, 84);
            ?>
            <a href="#" class="post-info__img">
                <img src="<?=htmlspecialcharsbx($pathToImg)?>" loading="lazy" alt="<?=htmlspecialcharsbx($arResult['NAME'])?>">
            </a>
            <div class="post-info__main">
                <h5 class="post-info__head"><a href="#"><?= $arResult['NAME'] ?></a></h5>
                <div class="post-place">
                    <span>Корпус <?= $arResult['PROPERTIES']['CORPUS']['VALUE'] ?></span>
                    <span>Этаж <?= $arResult['PROPERTIES']['ETAG']['VALUE'] ?></span>
                    <span>Секция <?= $arResult['PROPERTIES']['SECTION']['VALUE'] ?></span>
                </div>
            </div>
        </div>
        <div class="post-content">
            <div class="post-title">
                <h2 class="post-title__head">
                    <span><?= $arResult['NAME'] ?></span>
                </h2>
            </div>
            <div class="shop-info">
                <div class="shop-info__grid">
                    <div class="post-place">
                        <span>Корпус <?= $arResult['PROPERTIES']['CORPUS']['VALUE'] ?></span>
                        <span>Этаж <?= $arResult['PROPERTIES']['ETAG']['VALUE'] ?></span>
                        <span>Секция <?= $arResult['PROPERTIES']['SECTION']['VALUE'] ?></span>
                    </div>
                </div>
            </div>
            <div class="phones phones_green">
                <i class="phones__icon ic ic-phone"></i>
                <?php foreach ($arResult['PROPERTIES']['PHONE']['VALUE'] as $phone): ?>
                    <a href="tel:<?= $phone ?>"><?= $phone ?></a><br>
                <?php endforeach; ?>
            </div>
            <ul class="social-list">
                <?php if (!empty($arResult['PROPERTIES']['SITE_LINK']['VALUE'])): ?>
                    <li><i class="ic ic-globe"></i>
                        <a target="_blank"
                           href="<?= $arResult['PROPERTIES']['SITE_LINK']['VALUE'] ?>"><?= prepareUrlToClearFormat($arResult['PROPERTIES']['SITE_LINK']['VALUE']) ?></a>
                    </li>
                <?php endif; ?>
                <?php if (!empty($arResult['PROPERTIES']['INSTA_LINK']['VALUE'])): ?>
                    <li><i class="ic ic-instagram"></i>
                        <a target="_blank"
                           href="<?= $arResult['PROPERTIES']['INSTA_LINK']['VALUE'] ?>"><?= prepareUrlToClearFormat($arResult['PROPERTIES']['INSTA_LINK']['VALUE']) ?></a>
                    </li>
                <?php endif; ?>
                <?php if (!empty($arResult['PROPERTIES']['VK_LINK']['VALUE'])): ?>
                    <li><i class="ic ic-vk"></i>
                        <a target="_blank"
                           href="<?= $arResult['PROPERTIES']['VK_LINK']['VALUE'] ?>"><?= prepareUrlToClearFormat($arResult['PROPERTIES']['VK_LINK']['VALUE']) ?></a>
                    </li>
                <?php endif; ?>
            </ul>
            <div class="content">
                <?php if (!empty($arResult['DETAIL_TEXT'])) {
                    echo $arResult['DETAIL_TEXT'];
                } elseif (!empty($arResult['PREVIEW_TEXT'])) {
                    echo $arResult['PREVIEW_TEXT'];
                } ?>
            </div>
        </div>
    </div>
</div>
