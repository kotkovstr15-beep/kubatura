<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

$APPLICATION->SetTitle('Контакты');
$APPLICATION->SetPageProperty('title', 'Контакты квартала дизайна и интерьера «Кубатура»');
$APPLICATION->SetPageProperty(
    'description',
    'Контакты квартала дизайна и интерьера «Кубатура»: Самара, ул. Красноармейская, 1, телефон 8 (846) 229-55-95.'
);

$schemaHost = isset($_SERVER['HTTP_HOST']) ? preg_replace('/[^a-z0-9.:-]/i', '', $_SERVER['HTTP_HOST']) : 'kubatura.su';
$schemaBase = (\CMain::IsHTTPS() ? 'https://' : 'http://') . ($schemaHost ?: 'kubatura.su');
$organizationId = $schemaBase . '/#organization';
$localBusinessId = $schemaBase . '/kontakty/#localbusiness';
$contactSchema = array(
    '@context' => 'https://schema.org',
    '@graph' => array(
        array(
            '@type' => 'Organization',
            '@id' => $organizationId,
            'name' => 'Кубатура',
            'alternateName' => 'Кубатура, квартал дизайна и интерьера',
            'url' => $schemaBase . '/',
            'logo' => $schemaBase . SITE_TEMPLATE_PATH . '/img/logo.png',
            'telephone' => '+7 (846) 229-55-95',
            'sameAs' => array('https://vk.com/csrkubatura'),
        ),
        array(
            '@type' => array('LocalBusiness', 'ShoppingCenter'),
            '@id' => $localBusinessId,
            'name' => 'Кубатура, квартал дизайна и интерьера',
            'url' => $schemaBase . '/kontakty/',
            'image' => $schemaBase . SITE_TEMPLATE_PATH . '/img/logo.png',
            'telephone' => '+7 (846) 229-55-95',
            'priceRange' => '$$',
            'parentOrganization' => array('@id' => $organizationId),
            'address' => array(
                '@type' => 'PostalAddress',
                'streetAddress' => 'ул. Красноармейская, 1',
                'addressLocality' => 'Самара',
                'addressRegion' => 'Самарская область',
                'postalCode' => '443010',
                'addressCountry' => 'RU',
            ),
            'geo' => array(
                '@type' => 'GeoCoordinates',
                'latitude' => 53.19482428,
                'longitude' => 50.09070396,
            ),
            'openingHoursSpecification' => array(array(
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
                'opens' => '08:30',
                'closes' => '20:00',
            )),
        ),
    ),
);
?>
<h1>Контакты</h1>
<div class="contacts-page">
    <p><strong>Адрес:</strong> Самара, ул. Красноармейская, 1</p>
    <p><strong>Телефон:</strong> <a href="tel:+78462295595">8 (846) 229-55-95</a></p>
    <p><strong>Режим работы:</strong> ежедневно, 8:30–20:00</p>
    <p><a class="btn" href="/kak-dobratsya/">Как добраться</a></p>
</div>
<script type="application/ld+json"><?=json_encode($contactSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)?></script>
<?php require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'; ?>
