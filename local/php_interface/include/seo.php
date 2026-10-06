<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;

EventManager::getInstance()->addEventHandler('main', 'OnPageStart', 'kubaturaNormalizePublicUrl');
EventManager::getInstance()->addEventHandler('main', 'OnEndBufferContent', 'kubaturaFixLogoAlt');
EventManager::getInstance()->addEventHandler('main', 'OnEndBufferContent', 'kubaturaEnsureSingleHomeH1');

AddEventHandler('iblock', 'OnBeforeIBlockElementUpdate', 'kubaturaRememberRemovedProduct');
AddEventHandler('iblock', 'OnAfterIBlockElementUpdate', 'kubaturaSaveRemovedProductRedirect');
AddEventHandler('iblock', 'OnBeforeIBlockElementDelete', 'kubaturaRememberDeletedProduct');

/**
 * 301 без порта в адресе.
 *
 * ⚠️ ЗАЧЕМ ВМЕСТО LocalRedirect(). LocalRedirect строит АБСОЛЮТНЫЙ адрес
 * и подставляет порт из SERVER_PORT — за прокси это всегда 443, и в Location
 * уходит "https://kubatura.su:443/...". Для поисковых систем адрес с портом
 * и без порта — разные документы: вес уходит не на ту страницу, а в отчётах
 * краулеров появляются лишние строки и удлиняются цепочки переадресаций.
 * Проверено 26.08.2026: так отдавались все переадресации нормализации адресов.
 *
 * Относительный Location допустим по стандарту (RFC 7231) и порт за собой
 * не тянет. Если адрес всё же пришёл абсолютным — снимаем стандартный порт.
 *
 * session_write_close() перед выходом — чтобы сессия не потерялась:
 * LocalRedirect закрывал её сам, а голый exit этого не делает.
 */
function kubaturaRedirect301($target)
{
    $target = (string)$target;
    if ($target === '') {
        return;
    }

    // на случай абсолютного адреса в карте переадресаций
    $target = preg_replace('~^(https?://[^/:?#]+):(?:80|443)(?=[/?#]|$)~i', '$1', $target);

    if (!headers_sent()) {
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $target);
    }

    if (function_exists('session_write_close') && session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }

    exit;
}

/**
 * Адрес раздела, в котором лежит элемент. Запасной вариант — корень каталога.
 */
function kubaturaSectionUrlOf($iblockId, $sectionId)
{
    $sectionId = (int)$sectionId;
    if ($sectionId <= 0) {
        return '/catalog/';
    }

    // ⚠️ Раздел снятого товара может быть отключён сам — тогда увод в него
    // дал бы очередной 404. Поэтому поднимаемся по дереву до ближайшего
    // включённого: GLOBAL_ACTIVE учитывает и выключенных родителей.
    $chain = \CIBlockSection::GetNavChain((int)$iblockId, $sectionId, array('ID'), true);
    $ids = array();
    if (is_array($chain)) {
        foreach ($chain as $node) {
            $ids[] = (int)$node['ID'];
        }
    }
    if (!$ids) {
        $ids = array($sectionId);
    }

    $section = \CIBlockSection::GetList(
        array('DEPTH_LEVEL' => 'DESC'),
        array('IBLOCK_ID' => (int)$iblockId, 'ID' => $ids, 'GLOBAL_ACTIVE' => 'Y'),
        false,
        array('ID', 'SECTION_PAGE_URL', 'DEPTH_LEVEL')
    )->GetNext();

    return ($section && !empty($section['SECTION_PAGE_URL'])) ? $section['SECTION_PAGE_URL'] : '/catalog/';
}

/**
 * Normalizes duplicate home URLs, repeated slashes and catalog trailing slashes.
 * Product iblock IDs can be overridden in /bitrix/php_interface/dbconn.php:
 * define('KUBATURA_PRODUCT_IBLOCK_IDS', '7,11');
 */
function kubaturaNormalizePublicUrl()
{
    if (defined('ADMIN_SECTION') && ADMIN_SECTION === true) {
        return;
    }

    $method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper((string)$_SERVER['REQUEST_METHOD']) : 'GET';
    if (!in_array($method, array('GET', 'HEAD'), true)) {
        return;
    }

    $requestUri = isset($_SERVER['REQUEST_URI']) ? (string)$_SERVER['REQUEST_URI'] : '/';
    $path = parse_url($requestUri, PHP_URL_PATH);
    $query = parse_url($requestUri, PHP_URL_QUERY);
    $path = is_string($path) && $path !== '' ? $path : '/';
    $normalized = preg_replace('#/{2,}#', '/', $path);

    if (preg_match('#^/index(?:\.(?:html?|php))?/?$#i', $normalized)) {
        $normalized = '/';
    }

    $redirectMap = json_decode(Option::get('main', 'kubatura_seo_redirect_map', '{}'), true);
    $redirectKey = $normalized;
    if (is_array($redirectMap) && !isset($redirectMap[$redirectKey])) {
        $redirectKey = rtrim($normalized, '/');
    }
    if (is_array($redirectMap) && isset($redirectMap[$redirectKey])) {
        if ($redirectMap[$redirectKey] === '__GONE__') {
            define('ERROR_410', 'Y');
            \CHTTP::SetStatus('410 Gone');
            return;
        }

        kubaturaRedirect301($redirectMap[$redirectKey]);
    }

    if (preg_match('#^/(?:catalog|vygodnaya-pokupka)/.+#', $normalized)) {
        $lastSegment = basename(rtrim($normalized, '/'));
        $iblockIds = defined('KUBATURA_PRODUCT_IBLOCK_IDS')
            ? array_filter(array_map('intval', explode(',', KUBATURA_PRODUCT_IBLOCK_IDS)))
            : array(7, 11);

        if ($lastSegment !== '' && Loader::includeModule('iblock')) {
            // ⚠️ ACTIVE тянем осознанно, а сортируем по нему же: если один код
            // делят активная и снятая карточки, первой должна прийти активная
            // ('Y' > 'N' при сортировке по убыванию).
            $element = \CIBlockElement::GetList(
                array('ACTIVE' => 'DESC'),
                array('IBLOCK_ID' => $iblockIds, '=CODE' => $lastSegment),
                false,
                array('nTopCount' => 1),
                array('ID', 'IBLOCK_ID', 'ACTIVE', 'IBLOCK_SECTION_ID', 'DETAIL_PAGE_URL')
            // ⚠️ ИМЕННО GetNext, а не Fetch. Fetch отдаёт сырые поля, и
            // DETAIL_PAGE_URL приходит незаполненным — подстановку раздела
            // и кода делает только GetNext. На Fetch правило ниже молча
            // не срабатывало: адрес живого товара оставался 404.
            // Так же сделано в kubaturaRememberRemovedProduct ниже по файлу.
            )->GetNext(false, false);

            // ─────────────────────────────────────────────────────────────────
            // Товар ЖИВ, но запрошен по чужому адресу → уводим на настоящий.
            //
            // ЗАЧЕМ. В индексе остались короткие адреса без раздела:
            //     в индексе:  /catalog/potolochnaya-lyustra-vitaluce-v1541-0-3pl
            //     настоящий:  /catalog/svet/lyustry/potolochnaya-lyustra-vitaluce-v1541-0-3pl
            // Товар при этом в продаже. Без этой ветки короткий адрес попадал
            // под общую нормализацию (снимался слеш) и отдавал 404 — проверка
            // 26.08.2026: так вели себя все 30 «корневых» адресов из карты сайта.
            //
            // Адрес берётся из самой карточки, поэтому при переносе товара
            // в другой раздел правило продолжает работать без правок.
            //
            // ⚠️ Только для /catalog/: в /vygodnaya-pokupka/ товар выставлен
            // намеренно другой витриной, уводить оттуда в каталог нельзя.
            // ─────────────────────────────────────────────────────────────────
            if ($element
                && isset($element['ACTIVE']) && $element['ACTIVE'] === 'Y'
                && strpos($normalized, '/catalog/') === 0
                && !empty($element['DETAIL_PAGE_URL'])
            ) {
                $realPath = parse_url((string)$element['DETAIL_PAGE_URL'], PHP_URL_PATH);
                if (is_string($realPath) && $realPath !== ''
                    && rtrim($realPath, '/') !== rtrim($normalized, '/')
                ) {
                    kubaturaRedirect301($realPath . ($query !== null && $query !== '' ? '?' . $query : ''));
                }
            }

            // ─────────────────────────────────────────────────────────────────
            // Товар есть в базе, но снят с публикации → уводим в его раздел.
            //
            // ЗАЧЕМ. Без этой ветки снятый товар попадал под общее правило
            // нормализации: слеш убирался, посетитель уходил на адрес
            // отключённой карточки и получал 404. Проверка 26.08.2026: все 30
            // проверенных адресов снятых товаров заканчивались 404 после
            // лишнего прыжка 301.
            //
            // Карта kubatura_seo_redirect_map это не закрывала: она наполняется
            // обработчиком OnBeforeIBlockElementUpdate, то есть только для
            // товаров, снятых ПОСЛЕ внедрения механизма. Накопленные раньше
            // адреса (около 2800) в неё не попали и попасть не могли.
            //
            // Здесь же раздел берётся из самой карточки, поэтому правило
            // работает для всех снятых товаров сразу, без наполнения карты.
            // ─────────────────────────────────────────────────────────────────
            if ($element && isset($element['ACTIVE']) && $element['ACTIVE'] !== 'Y') {
                $gone = \CIBlockElement::GetProperty(
                    (int)$element['IBLOCK_ID'],
                    (int)$element['ID'],
                    array(),
                    array('CODE' => 'GONE_FOREVER')
                )->Fetch();

                // помечен как удалённый навсегда — честный 410, как и в карте
                if ($gone && strtoupper((string)$gone['VALUE']) === 'Y') {
                    define('ERROR_410', 'Y');
                    \CHTTP::SetStatus('410 Gone');
                    return;
                }

                kubaturaRedirect301(kubaturaSectionUrlOf($element['IBLOCK_ID'], $element['IBLOCK_SECTION_ID']));
            }

            $normalized = $element ? rtrim($normalized, '/') : rtrim($normalized, '/') . '/';
        }
    }

    if ($normalized !== $path) {
        $target = $normalized . ($query !== null && $query !== '' ? '?' . $query : '');
        kubaturaRedirect301($target);
    }
}

/** Adds the required alt even though the actual logo markup lives in /include/header.php. */
function kubaturaFixLogoAlt(&$content)
{
    if (!is_string($content) || stripos($content, '<img') === false) {
        return;
    }

    $content = preg_replace_callback(
        '#<img\b([^>]*(?:class=["\'][^"\']*(?:logo|header__logo)[^"\']*["\']|src=["\'][^"\']*/logo\.(?:png|jpe?g|webp|svg)["\'])[^>]*)>#i',
        static function ($match) {
            $attributes = preg_replace('#\s+alt\s*=\s*(["\']).*?\1#i', '', $match[1]);
            return '<img' . $attributes . ' alt="Кубатура, квартал дизайна и интерьера">';
        },
        $content
    );
}

/** Keeps the hero heading as the single H1 even if the editable SEO include contains another H1. */
function kubaturaEnsureSingleHomeH1(&$content)
{
    $path = parse_url(isset($_SERVER['REQUEST_URI']) ? (string)$_SERVER['REQUEST_URI'] : '/', PHP_URL_PATH);
    if ($path !== '/' && !preg_match('#^/index(?:\.(?:html?|php))?/?$#i', (string)$path)) {
        return;
    }

    $seen = 0;
    $content = preg_replace_callback(
        '#</?h1\b[^>]*>#i',
        static function ($match) use (&$seen) {
            if (stripos($match[0], '</h1') === 0) {
                return $seen > 1 ? preg_replace('#h1#i', 'h2', $match[0], 1) : $match[0];
            }

            $seen++;
            return $seen > 1 ? preg_replace('#h1#i', 'h2', $match[0], 1) : $match[0];
        },
        $content
    );
}

/** Captures the old URL before an active product is disabled. */
function kubaturaRememberRemovedProduct(&$fields)
{
    if (empty($fields['ID']) || !isset($fields['ACTIVE']) || $fields['ACTIVE'] !== 'N' || !Loader::includeModule('iblock')) {
        return;
    }

    $iblockIds = defined('KUBATURA_PRODUCT_IBLOCK_IDS')
        ? array_filter(array_map('intval', explode(',', KUBATURA_PRODUCT_IBLOCK_IDS)))
        : array(7, 11);
    $element = \CIBlockElement::GetList(
        array(),
        array('ID' => (int)$fields['ID'], 'IBLOCK_ID' => $iblockIds),
        false,
        array('nTopCount' => 1),
        array('ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'DETAIL_PAGE_URL')
    )->GetNext();

    if (!$element || empty($element['DETAIL_PAGE_URL'])) {
        return;
    }

    $gone = false;
    $property = \CIBlockElement::GetProperty((int)$element['IBLOCK_ID'], (int)$element['ID'], array(), array('CODE' => 'GONE_FOREVER'))->Fetch();
    if ($property && strtoupper((string)$property['VALUE']) === 'Y') {
        $gone = true;
    }

    $sectionUrl = '/catalog/';
    if (!empty($element['IBLOCK_SECTION_ID'])) {
        $section = \CIBlockSection::GetList(
            array(),
            array('ID' => (int)$element['IBLOCK_SECTION_ID']),
            false,
            array('ID', 'SECTION_PAGE_URL')
        )->GetNext();
        if ($section && !empty($section['SECTION_PAGE_URL'])) {
            $sectionUrl = $section['SECTION_PAGE_URL'];
        }
    }

    $GLOBALS['KUBATURA_PENDING_PRODUCT_REDIRECTS'][(int)$fields['ID']] = array(
        'from' => parse_url($element['DETAIL_PAGE_URL'], PHP_URL_PATH),
        'to' => $gone ? '__GONE__' : $sectionUrl,
    );
}

/** Persists redirect targets in a Bitrix option after the update succeeds. */
function kubaturaSaveRemovedProductRedirect(&$fields)
{
    $id = isset($fields['ID']) ? (int)$fields['ID'] : 0;
    if (!$id || empty($GLOBALS['KUBATURA_PENDING_PRODUCT_REDIRECTS'][$id])) {
        return;
    }

    $pending = $GLOBALS['KUBATURA_PENDING_PRODUCT_REDIRECTS'][$id];
    unset($GLOBALS['KUBATURA_PENDING_PRODUCT_REDIRECTS'][$id]);
    if (empty($pending['from'])) {
        return;
    }

    $map = json_decode(Option::get('main', 'kubatura_seo_redirect_map', '{}'), true);
    $map = is_array($map) ? $map : array();
    $map[$pending['from']] = $pending['to'];

    if (count($map) > 2000) {
        $map = array_slice($map, -2000, null, true);
    }

    Option::set('main', 'kubatura_seo_redirect_map', json_encode($map, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

/** Creates the same redirect record before a product is physically deleted. */
function kubaturaRememberDeletedProduct($id)
{
    $fields = array('ID' => (int)$id, 'ACTIVE' => 'N');
    kubaturaRememberRemovedProduct($fields);
    kubaturaSaveRemovedProductRedirect($fields);
    return true;
}