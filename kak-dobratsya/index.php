<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

$APPLICATION->SetTitle('Контакты');
$APPLICATION->SetPageProperty('title', 'Контакты квартала дизайна и интерьера «Кубатура» в Самаре');
$APPLICATION->SetPageProperty(
    'description',
    'Квартал дизайна и интерьера «Кубатура»: Самара, Красноармейская, 1. Ежедневно с 8:30 до 20:00, парковка на 300 мест, телефон 8 (846) 229-55-95'
);

// ⚠️ HTTP_HOST за прокси приходит с портом — «kubatura.su:443».
// Порт обязательно отсекать: адрес с портом и без порта поисковые системы
// считают разными документами, и вся микроразметка уезжает на чужой адрес.
$schemaHost = isset($_SERVER['HTTP_HOST']) ? preg_replace('/[^a-z0-9.-]/i', '', preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST'])) : 'kubatura.su';
$schemaBase = (\CMain::IsHTTPS() ? 'https://' : 'http://') . ($schemaHost ?: 'kubatura.su');
$organizationId = $schemaBase . '/#organization';
$localBusinessId = $schemaBase . '/kak-dobratsya/#localbusiness';

$socialProfiles = array(
    'https://vk.com/csrkubatura',
    'https://ok.ru/kubatura',
    'https://t.me/kubatura_smr',
    'https://dzen.ru/id/67ecc776d05be6618876c440',
    'https://max.ru/join/x9TSk2La-FZ96uCooDWKLV6Oi5nmHezjYGqTqE_jqIM',
);

$contactSchema = array(
    '@context' => 'https://schema.org',
    '@graph' => array(
        array(
            '@type' => 'Organization',
            '@id' => $organizationId,
            'name' => 'Кубатура',
            'alternateName' => 'Кубатура, квартал дизайна и интерьера',
            'legalName' => 'Индивидуальный предприниматель Корнилов Андрей Анатольевич',
            'taxID' => '631908200731',
            'url' => $schemaBase . '/',
            'logo' => $schemaBase . SITE_TEMPLATE_PATH . '/img/logo.png',
            'telephone' => '+7 (846) 229-55-95',
            'sameAs' => $socialProfiles,
        ),
        array(
            '@type' => array('LocalBusiness', 'ShoppingCenter'),
            '@id' => $localBusinessId,
            'name' => 'Кубатура, квартал дизайна и интерьера',
            'url' => $schemaBase . '/kak-dobratsya/',
            'image' => $schemaBase . SITE_TEMPLATE_PATH . '/img/logo.png',
            'telephone' => '+7 (846) 229-55-95',
            'priceRange' => '$$',
            'parentOrganization' => array('@id' => $organizationId),
            'sameAs' => $socialProfiles,
            'hasMap' => 'https://yandex.ru/maps/?ll=50.090704%2C53.194824&z=17&text=' . rawurlencode('Самара, Красноармейская, 1'),
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
<style>
/* Страница контактов. Классы с префиксом kc- — чтобы не пересечься с шаблоном. */
.kc{--kc-green:#7AB648;--kc-green-l:#edf5e3;--kc-line:rgba(0,0,0,.1);--kc-muted:#6b6b6b;--kc-bg2:#f7f7f5;
    font-size:15px;line-height:1.6}
.kc__grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px}
.kc__card{border:1px solid var(--kc-line);border-radius:14px;padding:20px 24px;background:#fff}
.kc__label{font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:var(--kc-muted);margin-bottom:10px}
.kc__phone{font-size:26px;font-weight:600;letter-spacing:-.02em;text-decoration:none;color:inherit;display:block;line-height:1.2}
.kc__phone:hover{color:var(--kc-green)}
.kc__hours{font-size:13px;color:var(--kc-muted);margin-top:6px}
.kc__addr{font-size:16px;font-weight:500;margin:0 0 4px}
.kc__note{font-size:13px;color:var(--kc-muted);margin:0}
.kc__row{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px}
.kc__pill{display:inline-flex;align-items:center;gap:6px;background:var(--kc-bg2);border:1px solid var(--kc-line);
    border-radius:99px;padding:6px 13px;font-size:13px;font-weight:500;color:inherit;text-decoration:none}
.kc__pill:hover{border-color:var(--kc-green);color:var(--kc-green)}
.kc__field{margin-top:14px}
.kc__field:first-of-type{margin-top:0}
.kc__copy{display:flex;width:100%;align-items:center;justify-content:space-between;gap:12px;
    background:var(--kc-bg2);border:1px solid var(--kc-line);border-radius:10px;
    padding:11px 14px;cursor:pointer;text-align:left;font:inherit;color:inherit}
.kc__copy:hover{border-color:var(--kc-green);background:var(--kc-green-l)}
.kc__copy b{font-weight:500;font-size:15px;line-height:1.4}
.kc__copy small{display:block;font-size:12px;color:var(--kc-muted);margin-top:2px;font-weight:400}
.kc__copy svg{width:16px;height:16px;flex:none;stroke:var(--kc-muted);fill:none;stroke-width:2;
    stroke-linecap:round;stroke-linejoin:round}
.kc__copy:hover svg{stroke:var(--kc-green)}
.kc__copy._ok{border-color:var(--kc-green);background:var(--kc-green-l)}
.kc__copy._ok svg{stroke:#5a8f30}
.kc__coord{font-variant-numeric:tabular-nums}
.kc__map{border:1px solid var(--kc-line);border-radius:14px;overflow:hidden;height:340px;margin-bottom:20px}
.kc__map iframe{width:100%;height:100%;display:block;border:0}
.kc__transport{display:grid;grid-template-columns:repeat(3,1fr);border:1px solid var(--kc-line);
    border-radius:14px;overflow:hidden;margin-bottom:20px}
.kc__transport>div{padding:16px 20px;border-right:1px solid var(--kc-line)}
.kc__transport>div:last-child{border-right:none}
.kc__nums{font-size:15px;font-weight:500;line-height:1.7}
.kc__car{background:var(--kc-bg2);border:1px solid var(--kc-line);border-radius:14px;
    padding:20px 24px;margin-bottom:20px}
.kc__car p{margin:0}
.kc__h2{font-size:11px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:var(--kc-green);
    border-left:3px solid var(--kc-green);padding-left:10px;margin:0 0 12px}
.kc__cta{display:inline-flex;align-items:center;gap:8px;background:var(--kc-green);color:#fff;text-decoration:none;
    border-radius:10px;padding:12px 20px;font-weight:500}
.kc__cta:hover{background:#5a8f30;color:#fff}
@media (max-width:640px){.kc__grid{grid-template-columns:1fr}.kc__phone{font-size:22px}
    .kc__transport{grid-template-columns:1fr}
    .kc__transport>div{border-right:none;border-bottom:1px solid var(--kc-line)}
    .kc__transport>div:last-child{border-bottom:none}}
</style>

<div class="kc">

    <div class="kc__grid">
        <div class="kc__card">
            <div class="kc__label">Адрес</div>
            <div class="kc__field">
                <button class="kc__copy" type="button" data-copy="443010, г. Самара, ул. Красноармейская, 1">
                    <span>
                        <b>443010, Самара, ул. Красноармейская, 1</b>
                        <small>Вход со стороны Красноармейской</small>
                    </span>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                </button>
            </div>
            <div class="kc__field">
                <button class="kc__copy" type="button" data-copy="53.19482428, 50.09070396">
                    <span>
                        <b class="kc__coord">53.19482428, 50.09070396</b>
                        <small>Координаты для навигатора</small>
                    </span>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                </button>
            </div>
        </div>

        <div class="kc__card">
            <div class="kc__label">Телефон</div>
            <a class="kc__phone" href="tel:+78462295595">+7 (846) 229-55-95</a>
            <div class="kc__hours">Ежедневно с 8:30 до 20:00, без выходных</div>
            <div class="kc__label" style="margin-top:18px">Мы в соцсетях</div>
            <div class="kc__row">
                <a class="kc__pill" href="https://vk.com/csrkubatura" target="_blank" rel="noopener">ВКонтакте</a>
                <a class="kc__pill" href="https://ok.ru/kubatura" target="_blank" rel="noopener">Одноклассники</a>
                <a class="kc__pill" href="https://t.me/kubatura_smr" target="_blank" rel="noopener">Telegram</a>
                <a class="kc__pill" href="https://dzen.ru/id/67ecc776d05be6618876c440" target="_blank" rel="noopener">Дзен</a>
                <a class="kc__pill" href="https://max.ru/join/x9TSk2La-FZ96uCooDWKLV6Oi5nmHezjYGqTqE_jqIM" target="_blank" rel="noopener">Max</a>
            </div>
        </div>
    </div>


    <h2 class="kc__h2">Городской транспорт</h2>
    <div class="kc__transport">
        <div>
            <div class="kc__label">Автобусы</div>
            <div class="kc__nums">3 · 24 · 37 · 47 · 61</div>
        </div>
        <div>
            <div class="kc__label">Трамваи</div>
            <div class="kc__nums">3 · 15 · 16 · 20</div>
        </div>
        <div>
            <div class="kc__label">Маршрутные такси</div>
            <div class="kc__nums">46 · 92 · 247 · 261 · 295 · 297</div>
        </div>
    </div>

    <h2 class="kc__h2">На автомобиле</h2>
    <div class="kc__car">
        <p>Для удобства посетителей квартала дизайна и интерьера «Кубатура» оборудовано несколько
        парковочных зон. Парковка на 300 машиномест расположена со стороны Красноармейской
        и улицы Льва Толстого.</p>
    </div>

    <div class="kc__map">
        <iframe
            src="https://yandex.ru/map-widget/v1/?ll=50.090704%2C53.194824&amp;z=17&amp;pt=50.090704%2C53.194824%2Cpm2rdl&amp;text=%D0%A1%D0%B0%D0%BC%D0%B0%D1%80%D0%B0%2C%20%D0%9A%D1%80%D0%B0%D1%81%D0%BD%D0%BE%D0%B0%D1%80%D0%BC%D0%B5%D0%B9%D1%81%D0%BA%D0%B0%D1%8F%2C%201"
            loading="lazy"
            allowfullscreen
            referrerpolicy="no-referrer-when-downgrade"
            title="Кубатура на карте: Самара, ул. Красноармейская, 1"></iframe>
    </div>

    <div class="kc__card" style="margin-top:20px">
        <div class="kc__label">Реквизиты</div>
        <p class="kc__note">
            ИП Корнилов Андрей Анатольевич<br>
            Юридический адрес: 166004, Ненецкий автономный округ, г. Нарьян-Мар,
            ул. Меньшикова, д. 14, кв. 16<br>
            ИНН 631908200731<br>
            ОГРНИП 313631906500012
        </p>
    </div>

</div>

<script>
document.addEventListener('click', function (e) {
    var btn = e.target.closest('.kc__copy');
    if (!btn || !navigator.clipboard) return;
    navigator.clipboard.writeText(btn.getAttribute('data-copy')).then(function () {
        var icon = btn.querySelector('svg'), orig = icon.innerHTML;
        icon.innerHTML = '<polyline points="20 6 9 17 4 12"/>';
        btn.classList.add('_ok');
        setTimeout(function () { icon.innerHTML = orig; btn.classList.remove('_ok'); }, 2000);
    });
});
</script>

<script type="application/ld+json"><?=json_encode($contactSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)?></script>
<?php require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'; ?>
