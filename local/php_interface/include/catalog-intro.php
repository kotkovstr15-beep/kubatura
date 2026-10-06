<?php
/** Server-rendered intro for catalog sections. It is deliberately omitted from smart-filter and paginated pages. */
function kubaturaCatalogIntroHtml($value)
{
    if (is_array($value)) $value = isset($value['TEXT']) ? $value['TEXT'] : '';
    $value = trim((string)$value);
    return $value === '' ? '' : $value;
}

function kubaturaCatalogIntroSection($path)
{
    $rs = \CIBlockSection::GetList(array('LEFT_MARGIN'=>'ASC'), array('IBLOCK_ID'=>7, 'ACTIVE'=>'Y'), false, array('ID','SECTION_PAGE_URL','UF_INTRO'));
    while ($section = $rs->GetNext(false, false)) {
        $sectionPath = rtrim(parse_url($section['SECTION_PAGE_URL'], PHP_URL_PATH), '/').'/';
        if ($sectionPath === rtrim($path, '/').'/') return kubaturaCatalogIntroHtml($section['UF_INTRO']);
    }
    return '';
}

function kubaturaInjectCatalogIntro(&$content)
{
    if (!is_string($content) || $content === '' || stripos($content, '<html') === false) return;
    $path = parse_url(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '', PHP_URL_PATH);
    if (strpos((string)$path, '/catalog/') !== 0 || preg_match('~/filter/|/apply/?$~', (string)$path)) return;
    if (isset($_GET['set_filter']) || (isset($_GET['PAGEN_1']) && (int)$_GET['PAGEN_1'] > 1)) return;
    $intro = kubaturaCatalogIntroSection($path);
    if ($intro === '' || strpos($content, 'data-kubatura-catalog-intro') !== false) return;
    $block = '<div class="catalog-intro" data-kubatura-catalog-intro="1">'.$intro.'</div>';
    $patterns = array(
        '/(<div[^>]+class="[^"]*(?:catalog-products|catalog-section-list|bx_catalog_item_container)[^"]*"[^>]*>)/i',
        '/(<div[^>]+class=\'[^\']*(?:catalog-products|catalog-section-list|bx_catalog_item_container)[^\']*\'[^>]*>)/i',
    );
    foreach ($patterns as $pattern) if (preg_match($pattern, $content)) { $content = preg_replace($pattern, $block.'$1', $content, 1); return; }
}
