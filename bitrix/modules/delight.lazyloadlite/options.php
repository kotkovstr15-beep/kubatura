<?
if (!$USER->IsAdmin())
    return;

\Bitrix\Main\UI\Extension::load("ui.hint");
IncludeModuleLangFile(__FILE__);

$MODULE_ID = "delight.lazyloadlite";

$arAllOptions = array(
    array(
		"name" => "enabled",
		"title" =>GetMessage("DELIGHT_LAZYLITE_SETTINGS_ENABLED"),
		"default_value" => "Y",
		"type" => array("checkbox", "Y"),
	),
	array(
		"name" => "limitation_classes",
		"title" =>GetMessage("DELIGHT_LAZYLITE_SETTINGS_LIMITATION_CLASSES"),
		"default_value" => "",
		"type" => array("textarea", 5, 50),
		"hint" => GetMessage("DELIGHT_LAZYLITE_SETTINGS_LIMITATION_CLASSES_HINT"),
		"attrs" => "",
	),
	array(
		"name" => "limitation_url",
		"title" =>GetMessage("DELIGHT_LAZYLITE_SETTINGS_LIMITATION_URL"),
		"default_value" => "",
		"type" => array("textarea", 5, 50),
		"hint" => GetMessage("DELIGHT_LAZYLITE_SETTINGS_LIMITATION_URL_HINT"),
		"attrs" => "",
	),
	array(
		"name" => "limitation_image_url",
		"title" =>GetMessage("DELIGHT_LAZYLITE_SETTINGS_LIMITATION_IMAGE_URL"),
		"default_value" => "mc.yandex.ru\nvk.com\nfacebook.com\ngoogletagmanager.com\nwww.google-analytics.com\nmail.ru",
		"type" => array("textarea", 5, 50),
		"hint" => GetMessage("DELIGHT_LAZYLITE_SETTINGS_LIMITATION_IMAGE_URL_HINT"),
		"attrs" => "",
	),
);
$aTabs = array(
    array("DIV" => "edit_main", "TAB" => GetMessage("DELIGHT_LAZYLITE_SETTINGS_MAIN_TAB_SET"), "TITLE" => GetMessage("DELIGHT_LAZYLITE_SETTINGS_MAIN_TAB_SET_TITLE")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);
if ($REQUEST_METHOD == "POST" && strlen($Update . $Apply . $RestoreDefaults) > 0 && check_bitrix_sessid()) {
    if (strlen($RestoreDefaults) > 0) {
        COption::RemoveOption($MODULE_ID);
    } else {
        foreach ($arAllOptions as $arOption) {
            $val = $_REQUEST[$arOption["name"]];
            if (isset($arOption["type"]) && isset($arOption["type"][0]) && $arOption["type"][0] == "checkbox" && $val != "Y")
                $val = "N";
            \Bitrix\Main\Config\Option::set($MODULE_ID, $arOption["name"], $val);
        }
    }

    if (strlen($Update) > 0 && strlen($_REQUEST["back_url_settings"]) > 0)
        LocalRedirect($_REQUEST["back_url_settings"]);
    else
        LocalRedirect($APPLICATION->GetCurPage() . "?mid=" . urlencode($mid) . "&lang=" . urlencode(LANGUAGE_ID) . "&back_url_settings=" . urlencode($_REQUEST["back_url_settings"]) . "&" . $tabControl->ActiveTabParam());
}

$tabControl->Begin();
?>
<form method="post" name="delight_lazyloadlite_settings" id="delight_lazyloadlite_settings" action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($mid) ?>&amp;lang=<? echo LANGUAGE_ID ?>">
	<? $tabControl->BeginNextTab();
	foreach ($arAllOptions as $arOption) {
		if (!is_array($arOption)) { ?>
			<tr class="heading">
				<td colspan="2"><?=$arOption?></td>
			</tr>
		<? } else {
			$val = \Bitrix\Main\Config\Option::get($MODULE_ID, $arOption["name"], $arOption["default_value"]);
			$type = $arOption["type"];
			?>
				<tr>
					<td width="40%" nowrap <? if ($type[0] == "textarea") echo 'class="adm-detail-valign-top"' ?>>
						<label for="<? echo htmlspecialcharsbx($arOption["name"]) ?>"><? echo $arOption["title"] ?>:</label>
						<? if(isset($arOption["hint"])){ ?>
							<span data-hint="<?=$arOption["hint"]?>" class="ui-hint"><span class="ui-hint-icon"></span></span>
						<? } ?>
					</td>
					<td width="60%">
						<? if ($type[0] == "checkbox"): ?>
							<input type="checkbox" id="<? echo htmlspecialcharsbx($arOption["name"]) ?>" name="<? echo htmlspecialcharsbx($arOption["name"]) ?>" value="Y"<? if ($val == "Y") echo" checked"; ?>>
						<? elseif ($type[0] == "text"): ?>
							<input type="text" size="<? echo $type[1] ?>" maxlength="255" value="<? echo htmlspecialcharsbx($val) ?>" name="<? echo htmlspecialcharsbx($arOption["name"]) ?>">
						<? elseif ($type[0] == "textarea"): ?>
							<textarea rows="<? echo $type[1] ?>" cols="<? echo $type[2] ?>" name="<? echo htmlspecialcharsbx($arOption["name"]) ?>" <? echo htmlspecialcharsbx($arOption["attrs"]) ?>><? echo htmlspecialcharsbx($val) ?></textarea>
						<? elseif ($type[0] == "selectbox"): ?>
							<select name="<? echo htmlspecialcharsbx($arOption["name"]) ?>">
								<? foreach ($type[1] as $t_key=>$t_val) { ?>
									<option value="<?=htmlspecialcharsbx($t_key);?>" <?=(htmlspecialcharsbx($val) == htmlspecialcharsbx($t_key)) ? "selected" : "" ?>>
										<?=htmlspecialcharsbx($t_val);?>
									</option>
								<? } ?>
							</select>
						<? endif ?>
					</td>
				</tr>
			<?
		}
	}
	?>
	<tr>
		<td colspan="2">
			<div id="module_ajax_data"></div>
		</td>
	</tr>
    <? $tabControl->Buttons(); ?>
		<input type="submit" name="Update" value="<?= GetMessage("DELIGHT_LAZYLITE_SETTINGS_SAVE") ?>" title="<?= GetMessage("DELIGHT_LAZYLITE_SETTINGS_SAVE") ?>" class="adm-btn-save">
		<input type="submit" name="Apply" value="<?= GetMessage("DELIGHT_LAZYLITE_SETTINGS_APPLY") ?>" title="<?= GetMessage("DELIGHT_LAZYLITE_SETTINGS_APPLY") ?>">
		<? if (strlen($_REQUEST["back_url_settings"]) > 0): ?>
			<input type="button" name="Cancel" value="<?= GetMessage("DELIGHT_LAZYLITE_SETTINGS_CANCEL") ?>" title="<?= GetMessage("DELIGHT_LAZYLITE_SETTINGS_CANCEL") ?>" onclick="window.location = '<? echo htmlspecialcharsbx(CUtil::addslashes($_REQUEST["back_url_settings"])) ?>'">
			<input type="hidden" name="back_url_settings" value="<?= htmlspecialcharsbx($_REQUEST["back_url_settings"]) ?>">
		<? endif ?>
		<input type="submit" name="RestoreDefaults" title="<? echo GetMessage("DELIGHT_LAZYLITE_RESTORE_DEFAULTS") ?>" OnClick="return confirm('<? echo AddSlashes(GetMessage("DELIGHT_LAZYLITE_RESTORE_DEFAULTS_WARNING")) ?>')" value="<? echo GetMessage("DELIGHT_LAZYLITE_RESTORE_DEFAULTS") ?>">
		<?= bitrix_sessid_post(); ?>
    <? $tabControl->End(); ?>
</form>
<script>
    BX.ready(function() {
        BX.UI.Hint.init(BX('delight_lazyloadlite_settings'));
		BX.ajax({
			url: "https://it-angels.ru/ajax/SpeedModulesAds.php",
			dataType: 'html',
			onsuccess: function(data){
				if(data){
					BX("module_ajax_data").insertAdjacentHTML('afterbegin', data);
					obLink = BX.findChild(BX("module_ajax_data"), {
							"tag" : "a",
							"class" : "utm_link"
						}, 
						true
					);
					obLink.setAttribute("href", obLink.getAttribute("href")+"&utm_term=<?=$MODULE_ID?>");
				}				
			}
		});
    });
	function FilterField(object){
		object.value = object.value.replace(/(?![\da-z-_\n])./i,'');
	}
</script>