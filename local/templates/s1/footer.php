<?php
if (!defined ("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
    die();
}

// page search
$isIndexPage = CSite::InDir('/index.php');

$isNewsDir = CSite::InDir('/all_news/');
$isADir = CSite::InDir('/eto-interesno/');

$isShops = CSite::InDir('/shops/');

$isCatalog = CSite::InDir('/catalog/');
$isCatalogi = CSite::InDir('/catalog/index.php');
$isVp = CSite::InDir('/vygodnaya-pokupka/');
$isCart = CSite::InDir('/cart/');
$isPlan = CSite::InDir('/karta-tssr/');

$isPage404 = (defined ("ERROR_404") && ERROR_404 == 'Y') ? true : false;
$defaultPage = (!$isIndexPage && !$isNewsDir && !$isADir && !$isPage404 && !$isShops && !$isCatalog && !$isVp && !$isCart && !$isPlan) ? true : false;?>
<?php if (isset($kubaturaDefaultWrapperOpened) ? $kubaturaDefaultWrapperOpened : $defaultPage): ?>
</div>
						</div>

					</div>

				</section>

<?php endif; ?>

<?// end page search

if($defaultPage) {
	?>


				<?$APPLICATION->IncludeComponent(
					"bitrix:news.list",
					"index",
					array(
						"COMPONENT_TEMPLATE" => ".default",
						"IBLOCK_TYPE" => "content",
						"IBLOCK_ID" => "5",
						"NEWS_COUNT" => "13",
						"SORT_BY1" => "ACTIVE_FROM",
						"SORT_ORDER1" => "DESC",
						"SORT_BY2" => "SORT",
						"SORT_ORDER2" => "ASC",
						"FILTER_NAME" => "",
						"FIELD_CODE" => array(
							0 => "",
							1 => "",
						),
						"PROPERTY_CODE" => array(
							0 => "",
							1 => "",
						),
						"CHECK_DATES" => "Y",
						"DETAIL_URL" => "",
						"AJAX_MODE" => "N",
						"AJAX_OPTION_JUMP" => "N",
						"AJAX_OPTION_STYLE" => "N",
						"AJAX_OPTION_HISTORY" => "N",
						"AJAX_OPTION_ADDITIONAL" => "",
						"CACHE_TYPE" => "A",
						"CACHE_TIME" => "36000000",
						"CACHE_FILTER" => "N",
						"CACHE_GROUPS" => "N",
						"PREVIEW_TRUNCATE_LEN" => "",
						"ACTIVE_DATE_FORMAT" => "j F",
						"SET_TITLE" => "N",
						"SET_BROWSER_TITLE" => "N",
						"SET_META_KEYWORDS" => "N",
						"SET_META_DESCRIPTION" => "N",
						"SET_LAST_MODIFIED" => "N",
						"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
						"ADD_SECTIONS_CHAIN" => "N",
						"HIDE_LINK_WHEN_NO_DETAIL" => "N",
						"PARENT_SECTION" => "",
						"PARENT_SECTION_CODE" => "",
						"INCLUDE_SUBSECTIONS" => "Y",
						"STRICT_SECTION_CHECK" => "N",
						"DISPLAY_DATE" => "Y",
						"DISPLAY_NAME" => "Y",
						"DISPLAY_PICTURE" => "Y",
						"DISPLAY_PREVIEW_TEXT" => "Y",
						"PAGER_TEMPLATE" => ".default",
						"DISPLAY_TOP_PAGER" => "N",
						"DISPLAY_BOTTOM_PAGER" => "Y",
						"PAGER_TITLE" => "Новости",
						"PAGER_SHOW_ALWAYS" => "N",
						"PAGER_DESC_NUMBERING" => "N",
						"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
						"PAGER_SHOW_ALL" => "N",
						"PAGER_BASE_LINK_ENABLE" => "N",
						"SET_STATUS_404" => "N",
						"SHOW_404" => "N",
						"MESSAGE_404" => ""
					),
					false
				);?>

	<?

}

?>
<?
if (CSite::InDir('/index.php')){
?>
<section class="main-about sm-padding">
<div class="container">
<?$APPLICATION->IncludeComponent(
  "bitrix:main.include",
  "",
  Array(
    "AREA_FILE_SHOW" => "page",
    "AREA_FILE_SUFFIX" => "inc",
    "EDIT_TEMPLATE" => ""
  )
);?>
<?

}

?>

</div>
</section>
			</main><!-- #wrapper__center -->
</div>
			<footer class="footer">
				<div class="container">

					<div class="footer__grid">
						<div class="footer__col">
							<div class="footer__address">
								<p><b><?$APPLICATION->IncludeFile(SITE_DIR."include/address.php", Array(), Array("MODE" => "php"));?></b></p>
								<a href="/kak-dobratsya/" class="btn btn_small"><i class="ic ic-route"></i> Контакты</a>
							</div>
						</div>
						<div class="footer__col">
							<div class="phone">
								<?$APPLICATION->IncludeFile(SITE_DIR."include/phone.php", Array(), Array("MODE" => "php"));?>
								<p class="phone__descr"><?$APPLICATION->IncludeFile(SITE_DIR."include/working_hours.php", Array(), Array("MODE" => "php"));?></p>
							</div>
						</div>
						<div class="footer__col">
							<div class="social">
								<div class="social__wrap">
								<?$APPLICATION->IncludeComponent("bitrix:news.line", "socseti", Array(
									"COMPONENT_TEMPLATE" => ".default",
										"IBLOCK_TYPE" => "content",	// Тип информационного блока
										"IBLOCKS" => array(	// Код информационного блока
											0 => "1",
										),
										"NEWS_COUNT" => "20",	// Количество новостей на странице
										"FIELD_CODE" => array(	// Поля
											0 => "PREVIEW_TEXT",
											1 => "",
										),
										"SORT_BY1" => "SORT",	// Поле для первой сортировки новостей
										"SORT_ORDER1" => "ASC",	// Направление для первой сортировки новостей
										"SORT_BY2" => "SORT",	// Поле для второй сортировки новостей
										"SORT_ORDER2" => "ASC",	// Направление для второй сортировки новостей
										"DETAIL_URL" => "",	// URL, ведущий на страницу с содержимым элемента раздела
										"CACHE_TYPE" => "A",	// Тип кеширования
										"CACHE_TIME" => "3600",	// Время кеширования (сек.)
										"CACHE_GROUPS" => "N",	// Учитывать права доступа
										"ACTIVE_DATE_FORMAT" => "d.m.Y",	// Формат показа даты
									),
									false
								);?>
								</div>
							</div>
						</div>
					</div>

					<div class="footer__bottom">
						<div class="footer__grid">
							<div class="footer__col">
								<?$APPLICATION->IncludeFile(SITE_DIR."include/footer_oferta.php", Array(), Array("MODE" => "php"));?>
							</div>
							<div class="footer__col">
								<p class="footer__copy"><?$APPLICATION->IncludeFile(SITE_DIR."include/footer_copyright.php", Array(), Array("MODE" => "php"));?></p>
							</div>
							<div class="footer__col">
								<div class="dev-box">
									<?$APPLICATION->IncludeFile(SITE_DIR."include/footer_dev-box.php", Array(), Array("MODE" => "php"));?>
								</div>
							</div>
						</div>
					</div>

				</div>
			</footer>


		</div><!-- #wrapper__wrap -->
	</div><!-- #wrapper -->
  <div id="search" class="full pfx dn">
    <span class="search p20 bg_r r">
      <img src="/local/img/ico/close.svg"/>
    </span>
    <div class="page ma">
      <?$APPLICATION->IncludeComponent(
	"bitrix:search.page",
	"search",
	array(
		"AJAX_MODE" => "Y",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"CHECK_DATES" => "Y",
		"DEFAULT_SORT" => "rank",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"FILTER_NAME" => "",
		"NO_WORD_LOGIC" => "N",
		"PAGER_SHOW_ALWAYS" => "Y",
		"PAGER_TEMPLATE" => "",
		"PAGER_TITLE" => "Результаты поиска",
		"PAGE_RESULT_COUNT" => "50",
		"PATH_TO_USER_PROFILE" => "",
		"RATING_TYPE" => "",
		"RESTART" => "Y",
		"SHOW_RATING" => "",
		"SHOW_WHEN" => "N",
		"SHOW_WHERE" => "N",
		"USE_LANGUAGE_GUESS" => "N",
		"USE_SUGGEST" => "Y",
		"USE_TITLE_RANK" => "Y",
		"arrFILTER" => array(
			0 => "iblock_content",
		),
		"arrWHERE" => "",
		"COMPONENT_TEMPLATE" => "search",
		"arrFILTER_iblock_products" => array(
			0 => "2",
			1 => "3",
		),
		"arrFILTER_iblock_shops" => array(
			0 => "6",
			1 => "all",
		),
		"arrFILTER_iblock_content" => array(
			0 => "all",
		)
	),
	false
);?>
  </div>
  </div>
	<div class="hidden"><div class="vcard">
 <div>
   <span class="category">Центр Дизайна Интерьера и Ремонта</span>
   <span class="fn org">Кубатура</span>
 </div>
 <div class="adr">
   <span class="locality">г. Самара</span>,
   <span class="street-address">ул. Красноармейская, д. 1</span>
 </div>
 <div>Телефон: <span class="tel">+7 (846) 229-55-95</span></div>
 <div>Мы работаем <span class="workhours">ежедневно с 8:30 до 20:00</span>
   <span class="url">
     <span class="value-title" title="https://kubatura.su"> </span>
   </span>
 </div>
</div></div>
  <!-- Analytics and retargeting load after interaction or during post-load idle time. -->
  <script>
  (function (window, document) {
      var loaded = false;

      window.dataLayer = window.dataLayer || [];
      window.gtag = window.gtag || function () { window.dataLayer.push(arguments); };
      window.ym = window.ym || function () { (window.ym.a = window.ym.a || []).push(arguments); };
      window.ym.l = window.ym.l || +new Date();

      function appendScript(src, onload) {
          var script = document.createElement('script');
          script.async = true;
          script.src = src;
          if (onload) script.onload = onload;
          document.head.appendChild(script);
      }

      function loadMarketingScripts() {
          if (loaded) return;
          loaded = true;

          window.gtag('js', new Date());
          window.gtag('config', 'UA-19846238-2');
          appendScript('https://www.googletagmanager.com/gtag/js?id=UA-19846238-2');

          window.ym(60722113, 'init', {
              clickmap: true,
              trackLinks: true,
              accurateTrackBounce: true
          });
          appendScript('https://mc.yandex.ru/metrika/tag.js');

          appendScript('https://vk.com/js/api/openapi.js?168', function () {
              if (window.VK && VK.Retargeting) {
                  VK.Retargeting.Init('VK-RTRG-824733-i0FQi');
                  VK.Retargeting.Hit();
              }
          });
      }

      ['pointerdown', 'touchstart', 'keydown', 'scroll'].forEach(function (eventName) {
          window.addEventListener(eventName, loadMarketingScripts, {once: true, passive: true});
      });

      window.addEventListener('load', function () {
          window.setTimeout(function () {
              if ('requestIdleCallback' in window) {
                  window.requestIdleCallback(loadMarketingScripts, {timeout: 2000});
              } else {
                  loadMarketingScripts();
              }
          }, 8000);
      }, {once: true});
  })(window, document);
  </script>

  <noscript><div><img src="https://mc.yandex.ru/watch/60722113" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
  <noscript><img src="https://vk.com/rtrg?p=VK-RTRG-824733-i0FQi" style="position:fixed; left:-999px;" alt="" /></noscript>


		<?$APPLICATION->IncludeFile(
			SITE_TEMPLATE_PATH.'/include/cookies_politik/template.php',
			array(),
			array(
				'MODE' => 'php',
				'SHOW_BORDER' => false
			)
		);?>

		<?
		$APPLICATION->IncludeFile(
			SITE_TEMPLATE_PATH.'/include/btn-up/template.php',
			array(),
			array(
				'MODE' => 'php',
				'SHOW_BORDER' => false
			)
		);
		?>
</body>

</html>
