<?
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Application;

if($_COOKIE['cookieApply'] != 'Y'){
	Asset::getInstance()->addCss(SITE_TEMPLATE_PATH.'/include/cookies_politik/style.css');
	Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/include/cookies_politik/script.js');
}
?>

<?if($_COOKIE['cookieApply'] != 'Y'):?>
	<style id="scs-cookies-critical">.scs-cookies{position:fixed;left:20px;bottom:20px;z-index:999;display:grid;grid-template-columns:1fr 130px;gap:30px;width:760px;max-width:calc(100% - 40px);min-height:90px;padding:20px;box-sizing:border-box;border-radius:8px;background:#f7f7f7;box-shadow:0 4px 20px rgba(0,0,0,.25);font:14px/1.2 Arial,sans-serif}.scs-cookies__title{margin-bottom:10px;font-size:18px;font-weight:600}.scs-cookies__btn{display:flex;align-items:center;justify-content:center;width:100%;max-width:130px;height:50px;border:0;border-radius:4px;background:#667800;color:#fff;font:600 16px/1.2 Arial,sans-serif;text-align:center;text-transform:uppercase}@media(max-width:991px){.scs-cookies{grid-template-columns:1fr;gap:15px;min-height:145px}}@media(max-width:767px){.scs-cookies{font-size:12px}.scs-cookies__title{font-size:14px}.scs-cookies__btn{height:40px;font-size:14px}}</style>
	<div class="scs-cookies">
		<div>
			<div class="scs-cookies__title">
				<?$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					Array(
						"AREA_FILE_SHOW" => "file",
						"AREA_FILE_SUFFIX" => "inc",
						"EDIT_TEMPLATE" => "",
						"PATH" => "/include/cookies_politik/title.php"
					)
				);?>
			</div>

			<?$APPLICATION->IncludeComponent(
				"bitrix:main.include",
				"",
				Array(
					"AREA_FILE_SHOW" => "file",
					"AREA_FILE_SUFFIX" => "inc",
					"EDIT_TEMPLATE" => "",
					"PATH" => "/include/cookies_politik/text.php"
				)
			);?>
		</div>
	
		<button class="scs-cookies__btn">
			<?$APPLICATION->IncludeComponent(
				"bitrix:main.include",
				"",
				Array(
					"AREA_FILE_SHOW" => "file",
					"AREA_FILE_SUFFIX" => "inc",
					"EDIT_TEMPLATE" => "",
					"PATH" => "/include/cookies_politik/btn.php"
				)
			);?>
		</button>
	</div>
<?endif?>
