<?php
define('ERROR_404', 'Y');
define('STATISTIC_SKIP_ACTIVITY_CHECK', true);

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

global $APPLICATION;
$APPLICATION->SetTitle('Страница не найдена — Кубатура');
$APPLICATION->SetPageProperty('title', 'Страница не найдена — Кубатура');
$APPLICATION->SetPageProperty(
    'description',
    'Страница не найдена. Перейдите в каталог Кубатуры или позвоните нам: 8 (846) 229-55-95.'
);

include $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/include/seo/404-content.php';

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
