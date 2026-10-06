<?php
use Bitrix\Main\Context;
use Bitrix\Main\EventManager;

global $USER;

function dump($d)
{
    global $USER;
    if ($USER->IsAdmin()) {
        echo "<pre style='font-size:10px'>";
        var_dump($d);
        echo "</pre>";
    }
}

require_once "include/functions.php";
require_once "include/defines.php";
require_once "include/hooks.php";
require_once "include/seo.php";
require_once "include/catalog-seo.php";
require_once __DIR__ . "/include/karta-tssr.php";
require_once __DIR__ . "/include/shop-page/bootstrap.php";
require_once __DIR__ . "/include/catalog-intro.php";
EventManager::getInstance()->addEventHandler('main', 'OnEndBufferContent', 'kubaturaInjectCatalogIntro');
EventManager::getInstance()->addEventHandler('main', 'OnEndBufferContent', 'Kubatura\\ShopPage\\shopPaginationLinks');
EventManager::getInstance()->addEventHandler('main', 'OnEndBufferContent', 'Kubatura\\ShopPage\\kubaturaFixDisabledCatalogBreadcrumbs');
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/templates/s1/include/seo/redirect-events.php';


AddEventHandler("iblock", "OnBeforeIBlockElementAdd", "OnBeforeIBlockElementUpdateCodeHandler");
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", "OnBeforeIBlockElementUpdateCodeHandler");
function OnBeforeIBlockElementUpdateCodeHandler(&$arFields)
{
	$translitParams = [
		"max_len" => "100",
		"change_case" => "L",
		"replace_space" => "-",
		"replace_other" => "-",
		"delete_repeat_replace" => "true",
		"use_google" => "false",
	];

	$arFields['CODE'] = CUtil::translit($arFields['CODE'], 'ru' , $translitParams);
}

EventManager::getInstance()->addEventHandler('main', 'OnEndBufferContent', 'minifyHtmlOutput');

/**
 * Минификация HTML на выдаче:
 * - убирает комментарии (кроме служебных для Bitrix composite/IE)
 * - убирает переносы и лишние пробелы
 */
function minifyHtmlOutput(&$content){
	global $USER;

	if (
		!is_string($content) ||
		$content === '' ||
		(is_object($USER) && $USER->IsAuthorized()) ||
		(defined('ADMIN_SECTION') && ADMIN_SECTION === true) ||
		(defined('PUBLIC_AJAX_MODE') && PUBLIC_AJAX_MODE === true)
	) {
		return;
	}

	$requestUri = isset($_SERVER['REQUEST_URI']) ? (string)$_SERVER['REQUEST_URI'] : '';

	if (
		strpos($requestUri, '/bitrix/admin/') === 0 ||
		strpos($requestUri, '/bitrix/tools/') === 0 ||
		strpos($requestUri, '/local/tools/') === 0
	) {
		return;
	}

	// Минифицируем только HTML-страницы
	if (stripos($content, '<html') === false && stripos($content, '<!doctype html') === false) {
		return;
	}

	// Не трогаем содержимое pre/textarea/script/style/code
	$parts = preg_split(
		'/(<(?:pre|textarea|script|style|code)\b[^>]*>.*?<\/(?:pre|textarea|script|style|code)>)/is',
		$content,
		-1,
		PREG_SPLIT_DELIM_CAPTURE
	);

	foreach ($parts as $i => $part) {
		if (preg_match('/^<(?:pre|textarea|script|style|code)\b/i', $part)) {
			continue;
		}

		// Удаляем HTML-комментарии, но сохраняем служебные комментарии Bitrix composite и условные IE
		$part = preg_replace(
			'/<!--(?!\\[if|<!\\[endif|\'start_frame_cache_|\'end_frame_cache_).*?-->/is',
			'',
			$part
		);

		// Удаляем лишние пробелы/переносы между тегами
		$part = preg_replace('/>\s+</s', '><', $part);

		// Сжимаем повторные пробелы
		$part = preg_replace('/\s{2,}/', ' ', $part);

		$parts[$i] = $part;
	}

	$content = trim(implode('', $parts));
}

// ─────────────────────────────────────────────────────────────────────────────
// SEO-редиректы (добавлено 16.08.2026)
//
// Выполняются прямо здесь, в глобальной области init.php, а НЕ через
// AddEventHandler("main", "OnBeforeProlog"): init.php подключается раньше,
// заголовки ещё не отправлены, и редирект срабатывает до всякой логики
// компонентов.
//
// Используется header(), а НЕ LocalRedirect(). LocalRedirect строит
// абсолютный адрес и подставляет в него порт из SERVER_PORT — за прокси это
// даёт "https://kubatura.su:443/...". Адрес с портом и без порта поисковые
// системы считают разными, в отчётах краулеров появляются лишние строки и
// удлиняются цепочки. Относительный Location допустим по стандарту и порт
// за собой не тянет — проверено 16.08.2026 на обоих правилах.
// ─────────────────────────────────────────────────────────────────────────────

$seoRedirectPath = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

if ($seoRedirectPath !== null && $seoRedirectPath !== "") {

	// 1. Карточки магазинов: старый формат /shops/код/ → /shops/код.html
	// Прежний адрес после смены формата перестал открывать карточку и отдавал
	// копию общего списка магазинов с кодом 200.
	if (preg_match("~^/shops/([^/.]+)/$~", $seoRedirectPath, $m)) {
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: /shops/" . $m[1] . ".html");
		exit;
	}

	// 2. Очистка умного фильтра: /<раздел>/filter/clear/apply/ → /<раздел>/
	// Кнопка «Очистить фильтр» вела на полный дубль раздела: тот же заголовок,
	// тот же товар, а canonical указывал сам на себя. Ссылка стоит в коде
	// каждого раздела каталога, поэтому робот обходил их все.
	// Для посетителя ничего не меняется: нажал «очистить» — получил 301 и попал
	// на чистый раздел, ровно туда, куда и хотел.
	if (preg_match("~^(/.+?)/filter/clear/apply/?$~", $seoRedirectPath, $m)) {
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: " . $m[1] . "/");
		exit;
	}

	// 3. Отключённые дубли товаров → основная карточка.
	// Список в отдельном файле: 341 правило, руками его не написать.
	// Загружаем только когда путь похож на карточку каталога — чтобы не
	// читать файл на каждый запрос к любой странице сайта.
	if (strpos($seoRedirectPath, "/catalog/") === 0) {
		static $dupRedirects = null;
		if ($dupRedirects === null) {
			$dupFile = __DIR__ . "/redirects-dubli.php";
			$dupRedirects = file_exists($dupFile) ? include $dupFile : [];
		}
		if (isset($dupRedirects[$seoRedirectPath])) {
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: " . $dupRedirects[$seoRedirectPath]);
			exit;
		}
	}


	// 4. Отключённые и несуществующие карточки → свой раздел.
	//
	// Магазин закрылся, новость сняли, статью убрали — адрес остаётся
	// в индексе и в чужих ссылках и отдаёт 404. Вместо тупика уводим
	// человека в раздел: там он найдёт другие магазины или свежие новости.
	//
	// Проверка идёт запросом к базе, а не по списку: состав меняется
	// постоянно, список пришлось бы поддерживать вручную.
	// Запрос лёгкий — поиск по индексированному полю, и выполняется
	// только для адресов вида /раздел/что-то, то есть на карточках.
	$sectionFallback = [
		"/shops/"         => 6,   // магазины
		"/all_news/"      => 5,   // новости и акции
		"/eto-interesno/" => 9,   // статьи
	];
	foreach ($sectionFallback as $prefix => $iblockId) {
		if (strpos($seoRedirectPath, $prefix) !== 0) continue;
		$tail = substr($seoRedirectPath, strlen($prefix));
		if ($tail === "" || strpos($tail, "/") !== false) break;  // сам раздел или вложенность
		$key = preg_replace('~\.html$~', '', $tail);
		if ($key === "") break;
		if (CModule::IncludeModule("iblock")) {
			// у магазинов адрес по символьному коду, у новостей — по ID
			$filter = ["IBLOCK_ID" => $iblockId, "ACTIVE" => "Y"];
			if (ctype_digit($key)) { $filter["ID"] = $key; } else { $filter["CODE"] = $key; }
			$found = CIBlockElement::GetList([], $filter, false, ["nTopCount" => 1], ["ID"])->GetNext();
			if (!$found) {
				header("HTTP/1.1 301 Moved Permanently");
				header("Location: " . $prefix);
				exit;
			}
		}
		break;
	}

}
