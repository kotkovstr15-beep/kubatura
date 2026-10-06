<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    return;
}

/** Returns the JSON registry used for redirects left by deleted products. */
function kubaturaSeoRedirectRegistryFile()
{
    return rtrim((string)$_SERVER['DOCUMENT_ROOT'], '/\\') . '/upload/kubatura-seo-redirects.json';
}

function kubaturaSeoLoadRedirectRegistry()
{
    $file = kubaturaSeoRedirectRegistryFile();
    if (!is_file($file)) {
        return array();
    }

    $data = json_decode((string)@file_get_contents($file), true);
    return is_array($data) ? $data : array();
}

function kubaturaSeoSaveRedirectRegistry(array $registry)
{
    $file = kubaturaSeoRedirectRegistryFile();
    $directory = dirname($file);
    if (!is_dir($directory) && !@mkdir($directory, 0755, true) && !is_dir($directory)) {
        return false;
    }

    $json = json_encode($registry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    if ($json === false) {
        return false;
    }

    $temporary = @tempnam($directory, 'kubatura-seo-');
    if (!$temporary || @file_put_contents($temporary, $json, LOCK_EX) === false) {
        if ($temporary) {
            @unlink($temporary);
        }
        return false;
    }

    @chmod($temporary, 0644);
    if (!@rename($temporary, $file)) {
        @unlink($temporary);
        return false;
    }

    return true;
}

function kubaturaSeoNormalizePath($path, $trailingSlash = null)
{
    $path = '/' . ltrim((string)$path, '/');
    $path = preg_replace('#/{2,}#', '/', $path);

    if ($trailingSlash === true && $path !== '/') {
        $path = rtrim($path, '/') . '/';
    } elseif ($trailingSlash === false && $path !== '/') {
        $path = rtrim($path, '/');
    }

    return $path;
}

function kubaturaSeoAbsoluteUrl($path)
{
    $scheme = defined('KUBATURA_CANONICAL_SCHEME') ? strtolower((string)KUBATURA_CANONICAL_SCHEME) : 'https';
    $scheme = $scheme === 'http' ? 'http' : 'https';
    $host = defined('KUBATURA_CANONICAL_HOST') && KUBATURA_CANONICAL_HOST
        ? (string)KUBATURA_CANONICAL_HOST
        : 'kubatura.su';
    $host = preg_replace('/[^a-z0-9.:-]/i', '', $host);

    return $scheme . '://' . $host . kubaturaSeoNormalizePath($path);
}

/**
 * Normalizes duplicate slashes and index aliases, then applies stored product
 * redirects. It is safe to call both from OnPageStart and from header.php.
 */
function kubaturaSeoProcessRequestRedirects()
{
    static $processed = false;
    if ($processed) {
        return;
    }
    $processed = true;

    $method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
    if ($method !== 'GET' && $method !== 'HEAD') {
        return;
    }

    $requestUri = isset($_SERVER['REQUEST_URI']) ? (string)$_SERVER['REQUEST_URI'] : '/';
    $questionPosition = strpos($requestUri, '?');
    $path = $questionPosition === false ? $requestUri : substr($requestUri, 0, $questionPosition);
    $query = $questionPosition === false ? '' : substr($requestUri, $questionPosition);
    $normalizedPath = kubaturaSeoNormalizePath($path);

    if (preg_match('#^/index(?:\.(?:php|html?))?/?$#i', $normalizedPath)) {
        $normalizedPath = '/';
    }

    if ($normalizedPath !== $path) {
        LocalRedirect(kubaturaSeoAbsoluteUrl($normalizedPath) . $query, true, '301 Moved Permanently');
    }

    $registry = kubaturaSeoLoadRedirectRegistry();
    $registryKey = rtrim($normalizedPath, '/');
    if ($registryKey === '') {
        $registryKey = '/';
    }
    if (empty($registry[$registryKey]) || empty($registry[$registryKey]['target'])) {
        return;
    }

    $record = $registry[$registryKey];
    // An inactive or reactivated element is handled by the catalog router. The
    // stored redirect becomes active only after the element is actually deleted.
    if (!empty($record['element_id']) && \Bitrix\Main\Loader::includeModule('iblock')) {
        $existingElement = \CIBlockElement::GetByID((int)$record['element_id'])->Fetch();
        if ($existingElement) {
            return;
        }
    }

    LocalRedirect(kubaturaSeoAbsoluteUrl($record['target']) . $query, true, '301 Moved Permanently');
}

function kubaturaSeoResolveElementRedirect($elementId)
{
    if (!(int)$elementId || !\Bitrix\Main\Loader::includeModule('iblock')) {
        return null;
    }

    $result = \CIBlockElement::GetList(
        array(),
        array('ID' => (int)$elementId),
        false,
        array('nTopCount' => 1),
        array('ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'CODE', 'DETAIL_PAGE_URL')
    );
    $element = $result->GetNext();
    if (!$element) {
        return null;
    }

    $sectionUrl = '/catalog/';
    if (!empty($element['IBLOCK_SECTION_ID'])) {
        $sectionResult = \CIBlockSection::GetList(
            array(),
            array('ID' => (int)$element['IBLOCK_SECTION_ID']),
            false,
            array('ID', 'SECTION_PAGE_URL')
        );
        if ($section = $sectionResult->GetNext()) {
            if (!empty($section['SECTION_PAGE_URL'])) {
                $sectionUrl = $section['SECTION_PAGE_URL'];
            }
        }
    }

    $detailUrl = !empty($element['DETAIL_PAGE_URL']) ? $element['DETAIL_PAGE_URL'] : '';
    if ($detailUrl === '' && !empty($element['CODE'])) {
        $detailUrl = rtrim($sectionUrl, '/') . '/' . rawurlencode($element['CODE']);
    }
    if ($detailUrl === '') {
        return null;
    }

    $detailPath = parse_url($detailUrl, PHP_URL_PATH);
    $sectionPath = parse_url($sectionUrl, PHP_URL_PATH);
    if (!is_string($detailPath) || $detailPath === '') {
        return null;
    }
    if (!preg_match('#^/(?:catalog|vygodnaya-pokupka)/#i', kubaturaSeoNormalizePath($detailPath))) {
        return null;
    }
    if (!is_string($sectionPath) || $sectionPath === '') {
        $sectionPath = '/catalog/';
    }

    return array(
        'source' => kubaturaSeoNormalizePath($detailPath, false),
        'target' => kubaturaSeoNormalizePath($sectionPath, true),
        'element_id' => (int)$element['ID'],
        'created_at' => date('c'),
    );
}

function kubaturaSeoRememberElementRedirect($elementId)
{
    $record = kubaturaSeoResolveElementRedirect($elementId);
    if (!$record || $record['source'] === $record['target']) {
        return;
    }

    $registry = kubaturaSeoLoadRedirectRegistry();
    $registry[$record['source']] = $record;
    kubaturaSeoSaveRedirectRegistry($registry);
}

function kubaturaSeoBeforeElementDelete($elementId)
{
    kubaturaSeoRememberElementRedirect($elementId);
}

function kubaturaSeoBeforeElementUpdate(&$fields)
{
    if (!empty($fields['ID']) && isset($fields['ACTIVE']) && $fields['ACTIVE'] === 'N') {
        kubaturaSeoRememberElementRedirect((int)$fields['ID']);
    }
}

function kubaturaRegisterSeoRedirectHandlers()
{
    static $registered = false;
    if ($registered) {
        return;
    }
    $registered = true;

    AddEventHandler('main', 'OnPageStart', 'kubaturaSeoProcessRequestRedirects', 1);
    AddEventHandler('iblock', 'OnBeforeIBlockElementDelete', 'kubaturaSeoBeforeElementDelete');
    AddEventHandler('iblock', 'OnBeforeIBlockElementUpdate', 'kubaturaSeoBeforeElementUpdate');
}

kubaturaRegisterSeoRedirectHandlers();
