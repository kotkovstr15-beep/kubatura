<?php
namespace Kubatura\ShopPage;

function notFound()
{
    global $APPLICATION;
    if (!defined('ERROR_404')) define('ERROR_404','Y');
    \CHTTP::SetStatus('404 Not Found');
    $APPLICATION->SetTitle('Страница не найдена');
    $APPLICATION->SetPageProperty('title','Страница не найдена — Кубатура');
    $APPLICATION->SetPageProperty('robots','noindex, follow');
    echo '<section class="s-inner"><div class="container"><h1>Страница не найдена</h1><p><a href="/catalog/">Перейти в каталог</a></p></div></section>';
}

/** Dedicated server-rendered shop/brand facets. Other native filters remain native. */
function catalogRoute()
{
    global $APPLICATION;
    $path=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
    if (!preg_match('~^(/catalog(?:/.*?)?)/filter/(.+)/apply/$~D',$path,$match)) return false;
    $tokens=explode('/',$match[2]); $shop=null; $brand=null; $handled=false; $extra=false; $seen=array();
    foreach($tokens as $token) {
        if(preg_match('/^(shop|manufacturer)-is-(.+)$/D',$token,$m)) {
            $handled=true;
            if(isset($seen[$m[1]])) { notFound(); return true; } $seen[$m[1]]=true;
            $code=rawurldecode($m[2]);
            if($m[1]==='shop') {
                $shop=Service::shop($code);
                if(!$shop) { notFound(); return true; }
            } else {
                $props=Service::properties(Service::config('catalog'));
                if(!isset($props['MANUFACTURER'])) { notFound(); return true; }
                $p=$props['MANUFACTURER'];
                if($p['PROPERTY_TYPE']==='L') {
                    $rs=\CIBlockPropertyEnum::GetList(array(),array('PROPERTY_ID'=>$p['ID']));
                    while($r=$rs->Fetch()) if(rawurldecode(Service::brandCode($r))===$code) { $brand=array('value'=>$r['ID'],'name'=>$r['VALUE']); break; }
                } elseif($p['PROPERTY_TYPE']==='S') {
                    $f=Service::productFilter(); $f['=PROPERTY_MANUFACTURER']=$code;
                    if(Service::count($f)) $brand=array('value'=>$code,'name'=>$code);
                }
                if(!$brand) { notFound(); return true; }
            }
        } elseif (strpos($token,'shop-is-')===0 || strpos($token,'manufacturer-is-')===0) { notFound(); return true; }
        else $extra=true;
    }
    if(!$handled) return false;
    // Do not silently discard additional native criteria. Let native smart filter
    // handle them, with a mandatory shop constraint installed after its execution.
    if($extra) { $GLOBALS['KUBATURA_REQUIRED_FACET']=array('shop'=>$shop,'brand'=>$brand); return false; }
    $base=rtrim($match[1],'/').'/';
    $section=$base==='/catalog/'?null:Service::sectionByPath($base);
    if($base!=='/catalog/' && !$section) { notFound(); return true; }
    $filter=Service::productFilter($shop?$shop['ID']:null,$section?array((int)$section['ID']):array());
    if($brand) $filter['=PROPERTY_MANUFACTURER']=$brand['value'];
    $total=Service::count($filter); $size=30; $pages=max(1,(int)ceil($total/$size));
    $rawPage=isset($_GET['PAGEN_1'])?$_GET['PAGEN_1']:'1';
    if(!is_scalar($rawPage)||!preg_match('/^[1-9][0-9]*$/D',(string)$rawPage)||(float)$rawPage>$pages) { notFound(); return true; }
    $page=(int)$rawPage;
    $heading=($section?$section['NAME']:'Товары').($shop?' — '.Service::name($shop['NAME']):'').($brand?' — '.$brand['name']:'').' в Самаре';
    if(!$total && $section) $heading=$section['NAME'];
    $APPLICATION->SetTitle($heading); $APPLICATION->SetPageProperty('title',$heading); $APPLICATION->SetPageProperty('canonical',$path);
    $APPLICATION->SetPageProperty('description',$heading.'. Ассортимент в ЦДИР «Кубатура».');
    if(!$total) $APPLICATION->SetPageProperty('robots','noindex, follow');
    \Bitrix\Main\Page\Asset::getInstance()->addCss('/local/templates/s1/css/shop-page.css');
    echo '<section class="shop-page s-inner"><div class="container"><nav class="breadcrumbs"><a href="/">Главная</a> → <a href="/catalog/">Каталог</a>'.($section?' → <a href="'.Service::e($base).'">'.Service::e($section['NAME']).'</a>':'').'</nav><h1>'.Service::e($heading).'</h1>';
    if(!$total) echo '<p>'.($shop?($section?'В этом магазине нет товаров этой категории':'В этом магазине нет товаров'):'Нет товаров этого бренда в выбранной категории').'</p><a href="'.Service::e($base).'">'.($section?'Перейти в раздел без фильтра':'Перейти в каталог').'</a>';
    else {
        $rs=\CIBlockElement::GetList(array('SORT'=>'ASC','DATE_CREATE'=>'DESC','ID'=>'DESC'),$filter,false,array('nPageSize'=>$size,'iNumPage'=>$page,'bShowAll'=>false),array('ID'));
        $ids=array(); while($r=$rs->Fetch()) $ids[]=(int)$r['ID'];
        $products=Service::products($ids);
        echo '<div class="shop-page__products">';
        foreach($products as $product) include __DIR__.'/product-card.php';
        echo '</div><nav class="shop-pages" aria-label="Страницы товаров">';
        for($i=1;$i<=$pages;$i++) echo $i===$page?'<span aria-current="page">'.$i.'</span>':'<a href="'.Service::e($path.($i>1?'?PAGEN_1='.$i:'')).'">'.$i.'</a>';
        echo '</nav>';
    }
    echo '</div></section>'; return true;
}

function shopPaginationIsInvalid()
{
    $path=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
    if($path!=='/shops/' && $path!=='/shops/index.php') return false;
    if(!isset($_GET['PAGEN_1'])) return false;
    $value=$_GET['PAGEN_1'];
    if(!is_scalar($value)||!preg_match('/^[1-9][0-9]*$/D',(string)$value)) return true;
    $filter=array('IBLOCK_ID'=>Service::config('shops'),'ACTIVE'=>'Y','ACTIVE_DATE'=>'Y','CHECK_PERMISSIONS'=>'Y');
    $total=Service::count($filter);
    return (float)$value>max(1,ceil($total/20));
}

/** Add crawlable page links to the legacy "Load more" shops pager. */
function shopPaginationLinks(&$content)
{
    $path = parse_url(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '', PHP_URL_PATH);
    if ($path !== '/shops/' || !is_string($content) || stripos($content, 'data-url="/shops/?PAGEN_1=') === false) return;
    $total = Service::count(array('IBLOCK_ID'=>Service::config('shops'),'ACTIVE'=>'Y','ACTIVE_DATE'=>'Y','CHECK_PERMISSIONS'=>'Y'));
    $pages = max(1, (int)ceil($total / 20));
    if ($pages < 2 || stripos($content, 'class="shops-pagination"') !== false) return;
    $current = isset($_GET['PAGEN_1']) && preg_match('/^[1-9][0-9]*$/D', (string)$_GET['PAGEN_1']) ? (int)$_GET['PAGEN_1'] : 1;
    $html = '<nav class="shops-pagination" aria-label="Страницы магазинов">';
    for ($page=1; $page <= $pages; $page++) {
        if ($page === $current) $html .= '<span aria-current="page">'.$page.'</span>';
        else $html .= '<a href="/shops/'.($page > 1 ? '?PAGEN_1='.$page : '').'">'.$page.'</a>';
    }
    $html .= '</nav>';
    $content = preg_replace('/(<a\b[^>]*data-url="\/shops\/\?PAGEN_1=2"[^>]*>)/i', $html.'$1', $content, 1);
}

/** Disabled catalog sections remain text in product breadcrumbs, not dead links. */
function kubaturaFixDisabledCatalogBreadcrumbs(&$content)
{
    $path = parse_url(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '', PHP_URL_PATH);
    if (!is_string($content) || strpos((string)$path, '/catalog/') !== 0 || stripos($content, 'breadcrumbs') === false) return;
    $last = basename(rtrim((string)$path, '/'));
    if ($last === '' || !\Bitrix\Main\Loader::includeModule('iblock')) return;
    $element = \CIBlockElement::GetList(array(), array('IBLOCK_ID'=>Service::config('catalog'), '=CODE'=>$last), false, array('nTopCount'=>1), array('ID','IBLOCK_SECTION_ID'))->GetNext(false, false);
    if (!$element || empty($element['IBLOCK_SECTION_ID'])) return;
    $sections = array();
    $rs = \CIBlockSection::GetNavChain(Service::config('catalog'), (int)$element['IBLOCK_SECTION_ID'], array('ID','NAME','CODE','SECTION_PAGE_URL','ACTIVE'));
    while ($section = $rs->GetNext(false, false)) if ($section['ACTIVE'] !== 'Y') $sections[] = $section;
    foreach ($sections as $section) {
        $url = rtrim(parse_url($section['SECTION_PAGE_URL'], PHP_URL_PATH), '/') . '/';
        $pattern = '#<a\b([^>]*href=["\']'.preg_quote($url, '#').'["\'][^>]*)>(.*?)</a>#isu';
        $content = preg_replace($pattern, '$2', $content);
    }
}
