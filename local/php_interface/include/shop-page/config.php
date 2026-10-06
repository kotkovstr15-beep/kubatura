<?php
// Overrides must be made here after running local/tools/shop-page.php diagnose.
return array(
    'shops' => 6,
    'catalog' => 7,
    'category_sections' => array(), // CATEGORY_FILTER enum ID => array(section IDs).
    'intro_field' => 'UF_INTRO',
    'hours_property' => '',         // Auto-detected by exact property name if unique.
    'recommend_property' => '',     // Auto-detected by name «Рекомендуем» if unique.
    'sale_iblock' => 0,             // Auto-detected by name #Кубатура_sale.
    'sale_shop_property' => '',     // Auto-detected element link to iblock 6.
    'legacy_description_fallback' => true, // Preserve existing texts until editors migrate.
    'cascade_enabled' => false,
    'redirects_ready' => false,
    'batch_size' => 100,
);
