<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

global $APPLICATION;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Context;

Loc::loadMessages(__FILE__);

require_once __DIR__ . '/include/performance/webp.php';
require_once __DIR__ . '/include/seo/redirect-events.php';
kubaturaSeoProcessRequestRedirects();
kubaturaRegisterWebpOutputHandler();

$asset = Asset::getInstance();
$isCriticalHome = CSite::InDir('/index.php');

// include file
// Preload the actual first hero image instead of competing, non-critical fonts.
if (CSite::InDir('/index.php') && \Bitrix\Main\Loader::includeModule('iblock')) {
    $heroResult = \CIBlockElement::GetList(
        array('SORT' => 'ASC', 'ID' => 'ASC'),
        array('IBLOCK_ID' => 4, 'ACTIVE' => 'Y', 'ACTIVE_DATE' => 'Y'),
        false,
        array('nTopCount' => 1),
        array('ID', 'DETAIL_PICTURE')
    );
    if ($heroRow = $heroResult->Fetch()) {
        $heroPicture = \CFile::GetFileArray((int)$heroRow['DETAIL_PICTURE']);
        if ($heroPicture) {
            $heroPreload = kubaturaPreparedImage($heroPicture, 1400, 788, 74, BX_RESIZE_IMAGE_EXACT);
            $heroPreloadMobile = kubaturaPreparedImage($heroPicture, 768, 700, 72, BX_RESIZE_IMAGE_EXACT);
            if (!empty($heroPreload['src'])) {
                $heroSrcset = !empty($heroPreloadMobile['src'])
                    ? htmlspecialcharsbx($heroPreloadMobile['src']) . ' 768w, ' . htmlspecialcharsbx($heroPreload['src']) . ' 1400w'
                    : '';
                $asset->addString('<link rel="preload" as="image" href="' . htmlspecialcharsbx($heroPreload['src']) . '"' . ($heroSrcset !== '' ? ' imagesrcset="' . $heroSrcset . '" imagesizes="100vw"' : '') . ' fetchpriority="high">');
            }
        }
    }
}

// jQuery must be available before Bitrix's own combined page/template
// scripts run (they call $()/jQuery() without waiting). Those combined
// caches (bitrix/cache/js/.../page_..._v1.js, template_..._v1.js) are
// not deferred, so loading jQuery deferred inside libs.min.js let them
// execute first and throw "$ is not defined". Load it synchronously
// instead, but AFTER the hero image/font preload hints above so those
// still get discovered and requested first. libs.min.js has been
// rebuilt without a bundled copy of jQuery (see tpl/gulpfile.js scripts
// task minus the jquery entry), so this is the only copy loaded now —
// no double download/parse.
$asset->addString('<script src="' . SITE_TEMPLATE_PATH . '/libs/jquery/jquery-2.2.3.min.js"></script>');

if ($isCriticalHome) {
    foreach (array('Regular', 'Black') as $fontWeightName) {
        $asset->addString('<link rel="preload" as="font" type="font/woff2" href="' . SITE_TEMPLATE_PATH . '/fonts/Montserrat-' . $fontWeightName . '/Montserrat-' . $fontWeightName . '.woff2" crossorigin>');
    }
    $criticalCssFile = __DIR__ . '/css/critical-home.css';
    if (is_file($criticalCssFile)) {
        $criticalCss = str_replace('</style', '<\/style', file_get_contents($criticalCssFile));
        $asset->addString('<style id="kubatura-critical-css">' . $criticalCss . '</style>');
    }

    foreach (array(
        SITE_TEMPLATE_PATH . '/css/icss.css',
        SITE_TEMPLATE_PATH . '/libs/icomoon/style.css',
        SITE_TEMPLATE_PATH . '/css/main.min.css',
        SITE_TEMPLATE_PATH . '/css/custom.css',
    ) as $deferredStylesheet) {
        $escapedStylesheet = htmlspecialcharsbx($deferredStylesheet);
        $asset->addString('<link rel="stylesheet" href="' . $escapedStylesheet . '" media="print" onload="this.onload=null;this.media=\'all\'"><noscript><link rel="stylesheet" href="' . $escapedStylesheet . '"></noscript>');
    }
} else {
    $asset->addCss(SITE_TEMPLATE_PATH . "/css/icss.css");
    $asset->addCss(SITE_TEMPLATE_PATH . "/libs/icomoon/style.css");
    $asset->addCss(SITE_TEMPLATE_PATH . "/css/main.min.css");
    $asset->addCss(SITE_TEMPLATE_PATH . "/css/custom.css");
    $asset->addCss(SITE_TEMPLATE_PATH . "/css/shop-page.css");
}
if (CSite::InDir('/personal/')) {
    $asset->addCss(SITE_TEMPLATE_PATH . "/css/sweetalert2.min.css");
}

if ($isCriticalHome) {
    $asset->addString('<script defer fetchpriority="low" src="' . SITE_TEMPLATE_PATH . '/js/libs.min.js"></script>');
} else {
    $asset->addJs(SITE_TEMPLATE_PATH . "/js/libs.min.js");
}
if (CSite::InDir('/karta-tssr/')) {
    $asset->addJs(SITE_TEMPLATE_PATH . "/js/svg-pan-zoom.js");
}

if (CSite::InDir('/personal/')) {
    $asset->addJs(SITE_TEMPLATE_PATH . "/js/sweetalert2.min.js");
}
foreach (array('jquery_cookie.min.js', 'base64.min.js', 'cart.js', 'common.js', 'custom.js') as $deferredScript) {
    $asset->addString('<script defer src="' . SITE_TEMPLATE_PATH . '/js/' . $deferredScript . '"></script>');
}

$asset->addString('<link rel="icon" href="' . SITE_TEMPLATE_PATH . '/img/favicon.svg" type="image/svg+xml">');
$asset->addString('<link rel="icon" href="' . SITE_TEMPLATE_PATH . '/img/favicon.png" type="image/png">');
// end include file

// page search
$isIndexPage = CSite::InDir('/index.php');

$isNewsDir = CSite::InDir('/all_news/');
$isADir = CSite::InDir('/eto-interesno/');
$isShops = CSite::InDir('/shops/');

$isCart = CSite::InDir('/cart/');
$isPlan = CSite::InDir('/karta-tssr/');

$isCatalog = CSite::InDir('/catalog/');
$isVp = CSite::InDir('/vygodnaya-pokupka/');
$isPage404 = defined("ERROR_404") && ERROR_404 == 'Y' ? true : false;
$isGonePage = defined("ERROR_410") && ERROR_410 == 'Y' ? true : false;

if ($isPage404) {
    $APPLICATION->SetTitle('Страница не найдена — Кубатура');
    $APPLICATION->SetPageProperty('title', 'Страница не найдена — Кубатура');
    $APPLICATION->SetPageProperty('description', 'Страница не найдена. Перейдите в каталог Кубатуры или позвоните нам: 8 (846) 229-55-95.');
}

$defaultPage = (!$isIndexPage && !$isNewsDir && !$isADir && !$isPage404 && !$isShops && !$isVp && !$isCatalog && !$isCart) ? true : false;
// Remember whether this header actually opened the generic page wrapper.
$kubaturaDefaultWrapperOpened = $defaultPage;
// end page search

// set css
$cssBody = "page-home";
if (!$isIndexPage) {
    $cssBody = "page-inner ";
}

if ($isPage404) {
    $cssBody .= "page-error";
}
// end set css


$request = Context::getCurrent()->getRequest();

$requestUri = $request->getRequestUri();
$queryPosition = strpos($requestUri, '?');

$canonicalPath = $queryPosition === false
    ? $requestUri
    : substr($requestUri, 0, $queryPosition);

if ($canonicalPath === '') {
    $canonicalPath = '/';
}

if ($canonicalPath[0] !== '/') {
    $canonicalPath = '/' . $canonicalPath;
}

$canonicalPath = preg_replace('#/+#', '/', $canonicalPath);

if (preg_match('#^/index(?:\.(?:html?|php))?$#i', $canonicalPath)) {
    $canonicalPath = '/';
}

if (strpos($canonicalPath, '/catalog/') === 0 && strpos($canonicalPath, '/filter/') !== false && isset($_GET['PAGEN_1']) && is_scalar($_GET['PAGEN_1']) && preg_match('/^[1-9][0-9]*$/D', (string)$_GET['PAGEN_1']) && (int)$_GET['PAGEN_1'] > 1) {
    $canonicalPath .= '?PAGEN_1=' . (int)$_GET['PAGEN_1'];
}
$host = $request->getHttpHost();
$host = preg_replace('/[\r\n\t]+/', '', $host);

$scheme = \CMain::IsHTTPS() ? 'https://' : 'http://';
if ($host !== '') {
    $canonicalUrl = $scheme . $host . $canonicalPath;
}
?><!DOCTYPE html>
<html xml:lang="<?= LANGUAGE_ID ?>" lang="<?= LANGUAGE_ID ?>" prefix="og: https://ogp.me/ns#">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <? $APPLICATION->ShowHead(); ?>

    <?php if (!empty($canonicalUrl)): ?>
        <link rel="canonical" href="<?=htmlspecialcharsbx($canonicalUrl)?>">
    <?php endif; ?>

    <?php if ($isPage404 || $isGonePage): ?>
        <?php if ($isPage404) { \CHTTP::SetStatus('404 Not Found'); } ?>
        <meta name="robots" content="noindex, nofollow">
    <?php endif; ?>

    <?php if ($isIndexPage):
        $schemaPhone = defined('KUBATURA_PHONE') ? KUBATURA_PHONE : \Bitrix\Main\Config\Option::get('main', 'kubatura_phone', '+7 (846) 229-55-95');
        $schemaOpen = defined('KUBATURA_OPEN_TIME') ? KUBATURA_OPEN_TIME : \Bitrix\Main\Config\Option::get('main', 'kubatura_open_time', '08:30');
        $schemaClose = defined('KUBATURA_CLOSE_TIME') ? KUBATURA_CLOSE_TIME : \Bitrix\Main\Config\Option::get('main', 'kubatura_close_time', '20:00');
        $phoneInclude = $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'include/phone.php';
        $hoursInclude = $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'include/working_hours.php';

        if (!defined('KUBATURA_PHONE') && is_file($phoneInclude)) {
            $phoneText = trim(strip_tags(file_get_contents($phoneInclude)));
            if ($phoneText !== '') {
                $schemaPhone = $phoneText;
            }
        }

        if (!defined('KUBATURA_OPEN_TIME') && !defined('KUBATURA_CLOSE_TIME') && is_file($hoursInclude)) {
            $hoursText = trim(strip_tags(file_get_contents($hoursInclude)));
            if (preg_match('/(\d{1,2}:\d{2})\D+(\d{1,2}:\d{2})/u', $hoursText, $hoursMatch)) {
                $schemaOpen = $hoursMatch[1];
                $schemaClose = $hoursMatch[2];
            }
        }

        $shoppingCenterSchema = array(
            '@context' => 'https://schema.org',
            '@type' => 'ShoppingCenter',
            'name' => 'Кубатура',
            'alternateName' => 'Центр дизайна интерьера и ремонта Кубатура',
            'url' => $scheme . $host . '/',
            'logo' => $scheme . $host . SITE_TEMPLATE_PATH . '/img/logo.png',
            'image' => $scheme . $host . SITE_TEMPLATE_PATH . '/img/logo.png',
            'description' => 'Кубатура — квартал дизайна и интерьера в Самаре.',
            'address' => array(
                '@type' => 'PostalAddress',
                'streetAddress' => 'ул. Красноармейская, 1',
                'addressLocality' => 'Самара',
                'addressRegion' => 'Самарская область',
                'addressCountry' => 'RU',
            ),
            'geo' => array('@type' => 'GeoCoordinates', 'latitude' => 53.19482428, 'longitude' => 50.09070396),
            'telephone' => $schemaPhone,
            'priceRange' => '$$',
            'openingHoursSpecification' => array(array(
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
                'opens' => $schemaOpen,
                'closes' => $schemaClose,
            )),
            'sameAs' => array('https://vk.com/csrkubatura'),
        );
    ?>
        <script type="application/ld+json"><?=json_encode($shoppingCenterSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)?></script>
    <?php endif; ?>

    <title><? $APPLICATION->ShowTitle(); ?></title>
</head>
<body class="<?= $cssBody ?>" id="top">
<? $APPLICATION->ShowPanel(); ?>
<div id="wrapper">
    <div id="wrapper__wrap">

        <nav id="menu" class="menu">
            <div class="menu__grid">
              <div class="menu__mob">
                  <div class="menu-mob-btns">
                      <a href="/karta-tssr/" class="menu-mob-btns__icon"><i class="ic ic-map"></i> Карта ЦСР</a>
                      <a href="/kak-dobratsya/" class="menu-mob-btns__icon"><i class="ic ic-route"></i> Контакты</a>
                  </div>
              </div>
                <div class="menu__mob menu-mob-contacts">
                    <p class="menu-mob-contacts__item"><? $APPLICATION->IncludeFile(SITE_DIR . "include/address.php",
                            Array(), Array("MODE" => "php")); ?></p>
                    <div class="menu-mob-contacts__item">
                        <div class="phone">
                            <? $APPLICATION->IncludeFile(SITE_DIR . "include/phone.php", Array(),
                                Array("MODE" => "php")); ?>
                            <p class="phone__descr"><? $APPLICATION->IncludeFile(SITE_DIR . "include/working_hours.php",
                                    Array(), Array("MODE" => "php")); ?></p>
                        </div>
                    </div>
                </div>

                <? $APPLICATION->IncludeFile(SITE_DIR . "include/favorable_purchase.php", Array(),
                    Array("MODE" => "php")); ?>
                <ul class="menu__list">
                    <? $APPLICATION->IncludeComponent(
                        "bitrix:menu",
                        "top",
                        array(
                            "COMPONENT_TEMPLATE" => "top",
                            "ROOT_MENU_TYPE" => "menu__list",
                            "MENU_CACHE_TYPE" => "N",
                            "MENU_CACHE_TIME" => "3600",
                            "MENU_CACHE_USE_GROUPS" => "N",
                            "MENU_CACHE_GET_VARS" => array(),
                            "MAX_LEVEL" => "1",
                            "CHILD_MENU_TYPE" => "menu__list",
                            "USE_EXT" => "N",
                            "DELAY" => "N",
                            "ALLOW_MULTI_SELECT" => "N"
                        ),
                        false
                    ); ?>
                </ul>
                <div class="menu__info">
                    <ul class="menu__list-2">
                        <? $APPLICATION->IncludeComponent(
                            "bitrix:menu",
                            "top",
                            array(
                                "COMPONENT_TEMPLATE" => "top",
                                "ROOT_MENU_TYPE" => "menu__list1",
                                "MENU_CACHE_TYPE" => "N",
                                "MENU_CACHE_TIME" => "3600",
                                "MENU_CACHE_USE_GROUPS" => "N",
                                "MENU_CACHE_GET_VARS" => array(),
                                "MAX_LEVEL" => "1",
                                "CHILD_MENU_TYPE" => "menu__list1",
                                "USE_EXT" => "N",
                                "DELAY" => "N",
                                "ALLOW_MULTI_SELECT" => "N"
                            ),
                            false
                        ); ?>
                    </ul>
                    <div class="menu__address menu__contact menu__pc">
                        <p><b><? $APPLICATION->IncludeFile(SITE_DIR . "include/address.php", Array(),
                                    Array("MODE" => "php")); ?></b></p>
                        <p><small><? $APPLICATION->IncludeFile(SITE_DIR . "include/working_hours.php", Array(),
                                    Array("MODE" => "php")); ?></small></p>
                        <a href="/kak-dobratsya/" class="btn btn_small"><i class="ic ic-route"></i> Контакты</a>
                    </div>
                    <div class="menu__contact">
                        <div class="phone menu__pc">
                            <? $APPLICATION->IncludeFile(SITE_DIR . "include/phone.php", Array(),
                                Array("MODE" => "php")); ?>
                        </div>
                        <div class="social">
                            <div class="social__wrap">
                                <? $APPLICATION->IncludeComponent("bitrix:news.line", "socseti", Array(
                                    "COMPONENT_TEMPLATE" => ".default",
                                    "IBLOCK_TYPE" => "content",    // Тип информационного блока
                                    "IBLOCKS" => array(    // Код информационного блока
                                        0 => "1",
                                    ),
                                    "NEWS_COUNT" => "20",    // Количество новостей на странице
                                    "FIELD_CODE" => array(    // Поля
                                        0 => "PREVIEW_TEXT",
                                        1 => "",
                                    ),
                                    "SORT_BY1" => "SORT",    // Поле для первой сортировки новостей
                                    "SORT_ORDER1" => "ASC",    // Направление для первой сортировки новостей
                                    "SORT_BY2" => "SORT",    // Поле для второй сортировки новостей
                                    "SORT_ORDER2" => "ASC",    // Направление для второй сортировки новостей
                                    "DETAIL_URL" => "",    // URL, ведущий на страницу с содержимым элемента раздела
                                    "CACHE_TYPE" => "A",    // Тип кеширования
                                    "CACHE_TIME" => "3600",    // Время кеширования (сек.)
                                    "CACHE_GROUPS" => "N",    // Учитывать права доступа
                                    "ACTIVE_DATE_FORMAT" => "d.m.Y",    // Формат показа даты
                                ),
                                    false
                                ); ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </nav>

        <? if ($isIndexPage): ?>

            <section class="banner">
                <div class="banner__wrap">

                    <? $APPLICATION->IncludeFile(SITE_DIR . "include/header.php", Array(), Array("MODE" => "php")); ?>

                    <nav class="cat-menu">
                        <div class="cat-menu-mob">
                            <div class="container">
                                <div class="cat-menu-mob__grid">
                                    <a href="/cart/" class="cart-btn">
                                        Корзина
                                        <span class="cart-btn__icon">
											<i class="ic ic-cart"></i>
											<span class="cart-btn__count">0</span>
										</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </nav>

                    <div class="container home-hero-title">
                        <h1 class="home-hero-title__text"><?$APPLICATION->ShowTitle(false);?></h1>
                    </div>

                    <? $APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"front-banner",
	array(
		"COMPONENT_TEMPLATE" => "front-banner",
		"IBLOCK_TYPE" => "content",
		"IBLOCK_ID" => "4",
		"NEWS_COUNT" => "20",
		"SORT_BY1" => "SORT",
		"SORT_ORDER1" => "ASC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"FILTER_NAME" => "",
		"FIELD_CODE" => array(
			0 => "NAME",
			1 => "PREVIEW_TEXT",
			2 => "PREVIEW_PICTURE",
			3 => "DETAIL_TEXT",
			4 => "DETAIL_PICTURE",
			5 => "",
		),
		"PROPERTY_CODE" => array(
			0 => "URL",
			1 => "TEXT_URL",
			2 => "",
		),
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "N",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "N",
		"PREVIEW_TRUNCATE_LEN" => "",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"SET_TITLE" => "N",
		"SET_BROWSER_TITLE" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_LAST_MODIFIED" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"INCLUDE_SUBSECTIONS" => "Y",
		"STRICT_SECTION_CHECK" => "N",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"PAGER_TEMPLATE" => ".default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => "Новости",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"SET_STATUS_404" => "N",
		"SHOW_404" => "N",
		"MESSAGE_404" => ""
	),
	false
); ?>
                </div>
            </section>
        <? else: ?>
            <? $APPLICATION->IncludeFile(SITE_DIR . "include/header.php", Array(), Array("MODE" => "php")); ?>

            <? if (!$isCatalog && !$isVp): ?>
                <nav class="cat-menu">
                    <div class="cat-menu-mob">
                        <div class="container">
                            <div class="cat-menu-mob__grid">
                                <a href="/cart/" class="cart-btn">
                                    Корзина
                                    <span class="cart-btn__icon">
										<i class="ic ic-cart"></i>
										<span class="cart-btn__count">0</span>
									</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </nav>
            <? endif; ?>

            <? if ($isCatalog): ?>

                <? /* bitrix:catalog.section.list */ ?>

                <? $APPLICATION->ShowViewContent('cat_menu'); ?>

            <? endif; ?>
            <? if ($isVp): ?>

                <? /* bitrix:catalog.section.list */ ?>

                <? $APPLICATION->ShowViewContent('cat_menu'); ?>

            <? endif; ?>


        <? endif; ?>

        <main id="wrapper__center">
            <? if ($defaultPage): ?>
            <section class="post s-inner s-inner_bg">
                <div class="container  <? if ($isPlan): ?>karta<? endif; ?>">

                    <? if (!$isPlan): ?>
                        <? $APPLICATION->IncludeComponent("bitrix:breadcrumb", "", Array(
                                "START_FROM" => "0",
                                "PATH" => "",
                                "SITE_ID" => "s1"
                            )
                        ); ?>
                    <? else: ?>
                        <? $APPLICATION->IncludeComponent(
                            "bitrix:menu",
                            "plantc",
                            array(
                                "COMPONENT_TEMPLATE" => "plantc",
                                "ROOT_MENU_TYPE" => "left",
                                "MENU_CACHE_TYPE" => "N",
                                "MENU_CACHE_TIME" => "3600",
                                "MENU_CACHE_USE_GROUPS" => "N",
                                "MENU_CACHE_GET_VARS" => array(),
                                "MAX_LEVEL" => "1",
                                "CHILD_MENU_TYPE" => "menu__list",
                                "USE_EXT" => "N",
                                "DELAY" => "N",
                                "ALLOW_MULTI_SELECT" => "N",
                                "MENU_THEME" => "site"
                            ),
                            false
                        ); ?>
                    <? endif; ?>

                    <div class="title">
                        <h1 class="title__head"><?$APPLICATION->ShowTitle(false);?></h1>
                    </div>

                    <div class="post-grid">
                        <div class="full l">
                            <? endif; ?>
