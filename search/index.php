<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

$APPLICATION->SetTitle('Поиск по сайту');
$APPLICATION->SetPageProperty('title', 'Поиск по сайту — Кубатура');
$APPLICATION->SetPageProperty('robots', 'noindex, follow');
?>
<section class="search-results s-inner">
    <div class="container">
        <h1>Поиск по сайту</h1>
        <?php $APPLICATION->IncludeComponent(
            'bitrix:search.page',
            'search',
            array(
                'CACHE_TYPE' => 'A',
                'CACHE_TIME' => '3600',
                'RESTART' => 'Y',
                'NO_WORD_LOGIC' => 'N',
                'USE_LANGUAGE_GUESS' => 'N',
                'CHECK_DATES' => 'Y',
                'DEFAULT_SORT' => 'rank',
                'FILTER_NAME' => '',
                'arrFILTER' => array('iblock_content', 'iblock_products', 'iblock_shops'),
                'arrFILTER_iblock_content' => array('all'),
                'arrFILTER_iblock_products' => array('all'),
                'arrFILTER_iblock_shops' => array('all'),
                'SHOW_WHERE' => 'N',
                'SHOW_WHEN' => 'N',
                'PAGE_RESULT_COUNT' => '30',
                'DISPLAY_TOP_PAGER' => 'N',
                'DISPLAY_BOTTOM_PAGER' => 'Y',
                'PAGER_TITLE' => 'Результаты поиска',
            ),
            false
        ); ?>
    </div>
</section>
<?php require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'; ?>

