<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * Enforces the catalog URL policy after the Bitrix SEF router has resolved
 * whether the current request is a section or an element.
 */
function kubaturaEnforceCatalogTrailingSlash($isDetailPage)
{
    $method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
    if ($method !== 'GET' && $method !== 'HEAD') {
        return;
    }

    $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
    $questionPosition = strpos($requestUri, '?');
    $path = $questionPosition === false ? $requestUri : substr($requestUri, 0, $questionPosition);
    $query = $questionPosition === false ? '' : substr($requestUri, $questionPosition);

    if ($path === '' || $path === '/') {
        return;
    }

    $targetPath = $isDetailPage ? rtrim($path, '/') : rtrim($path, '/') . '/';
    if ($targetPath !== $path) {
        LocalRedirect($targetPath . $query, true, '301 Moved Permanently');
    }
}

/** Returns the matching inactive element, or null when the product is active/missing. */
function kubaturaFindInactiveProduct(array $parameters, array $variables)
{
    $filter = array('IBLOCK_ID' => $parameters['IBLOCK_ID']);

    if (!empty($variables['ELEMENT_ID'])) {
        $filter['ID'] = (int)$variables['ELEMENT_ID'];
    } elseif (!empty($variables['ELEMENT_CODE'])) {
        $filter['CODE'] = $variables['ELEMENT_CODE'];
    } else {
        return null;
    }

    $result = \CIBlockElement::GetList(
        array(),
        $filter,
        false,
        array('nTopCount' => 1),
        array('ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'NAME', 'ACTIVE')
    );
    $product = $result->Fetch();

    return $product && $product['ACTIVE'] === 'N' ? $product : null;
}

/**
 * A permanently discontinued product must be marked in the CMS. Supported
 * property codes are PERMANENTLY_REMOVED, GONE_410 and HTTP_STATUS. The first
 * two accept Y/1/YES/ДА/410; HTTP_STATUS accepts 410.
 */
function kubaturaInactiveProductIsGone(array $product)
{
    if (defined('KUBATURA_INACTIVE_PRODUCTS_ARE_GONE') && KUBATURA_INACTIVE_PRODUCTS_ARE_GONE === true) {
        return true;
    }

    if (empty($product['ID']) || empty($product['IBLOCK_ID'])) {
        return false;
    }

    $propertyCodes = defined('KUBATURA_GONE_PROPERTY_CODE') && KUBATURA_GONE_PROPERTY_CODE
        ? array(KUBATURA_GONE_PROPERTY_CODE)
        : array('PERMANENTLY_REMOVED', 'GONE_410', 'HTTP_STATUS');
    $goneValues = array('Y', 'YES', '1', '410', 'ДА', 'Да', 'да', 'GONE');

    foreach ($propertyCodes as $propertyCode) {
        $propertyResult = \CIBlockElement::GetProperty(
            (int)$product['IBLOCK_ID'],
            (int)$product['ID'],
            array(),
            array('CODE' => $propertyCode)
        );
        while ($property = $propertyResult->Fetch()) {
            $value = strtoupper(trim(isset($property['VALUE']) ? (string)$property['VALUE'] : ''));
            $enumValue = strtoupper(trim(isset($property['VALUE_ENUM']) ? (string)$property['VALUE_ENUM'] : ''));
            if (in_array($value, $goneValues, true) || in_array($enumValue, $goneValues, true)) {
                return true;
            }
        }
    }

    return false;
}

function kubaturaInactiveProductParentUrl(array $product)
{
    if (!empty($product['IBLOCK_SECTION_ID'])) {
        $sectionResult = \CIBlockSection::GetList(
            array(),
            array('ID' => (int)$product['IBLOCK_SECTION_ID']),
            false,
            array('ID', 'SECTION_PAGE_URL')
        );
        if ($section = $sectionResult->GetNext()) {
            if (!empty($section['SECTION_PAGE_URL'])) {
                return rtrim($section['SECTION_PAGE_URL'], '/') . '/';
            }
        }
    }

    $requestPath = parse_url(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/catalog/', PHP_URL_PATH);
    $parentPath = dirname(rtrim((string)$requestPath, '/'));
    return rtrim($parentPath === '.' ? '/catalog/' : $parentPath, '/') . '/';
}

/** Redirects an ordinarily disabled product to its parent catalog category. */
function kubaturaRedirectInactiveProduct(array $product)
{
    $target = kubaturaInactiveProductParentUrl($product);
    LocalRedirect($target, true, '301 Moved Permanently');
}

/**
 * Outputs a real 410 response for a permanently removed product.
 * The product must be inactive and explicitly marked with the configured
 * permanent-removal property. An ordinary inactive product redirects to its
 * parent category instead.
 */
function kubaturaRenderGoneProduct(array $product)
{
    global $APPLICATION;

    if (!defined('ERROR_410')) {
        define('ERROR_410', 'Y');
    }

    if (function_exists('http_response_code')) {
        http_response_code(410);
    }

    \CHTTP::SetStatus('410 Gone');
    \Bitrix\Main\Context::getCurrent()->getResponse()->setStatus('410 Gone');

    $APPLICATION->SetTitle('Товар снят с продажи');
    $APPLICATION->SetPageProperty('title', 'Товар снят с продажи — Кубатура');
    $APPLICATION->SetPageProperty('robots', 'noindex, nofollow');

    AddEventHandler('main', 'OnEndBufferContent', 'kubaturaInjectGoneRobotsMeta');

    $goneProductName = isset($product['NAME']) ? $product['NAME'] : '';
    $APPLICATION->IncludeFile(
        SITE_TEMPLATE_PATH . '/include/seo/410-content.php',
        array('PRODUCT_NAME' => $goneProductName),
        array('MODE' => 'php')
    );
}

/** Adds robots metadata because the product state is known only after <head> was rendered. */
function kubaturaInjectGoneRobotsMeta(&$content)
{
    if (stripos($content, 'name="robots"') !== false) {
        return;
    }

    $content = preg_replace(
        '#</head>#i',
        "    <meta name=\"robots\" content=\"noindex, nofollow\">\n</head>",
        $content,
        1
    );
}
