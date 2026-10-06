<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
CJSCore::Init();
define('SITE_KEY', '6LfVZt0UAAAAAKIEFvEuuee9M-d1KM5IGesS07mM');
define('SECRET_KEY', '6LfVZt0UAAAAACd1XIU4sO67Gq2PRUaqogJUbVwK');

?>
<div class="bx-system-auth-form">
    <?php if ($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR']) {
        ShowMessage($arResult['ERROR_MESSAGE']);
    } ?>
    <?php if ($arResult["FORM_TYPE"] == "login"): ?>
        <form name="system_auth_form<?= $arResult["RND"] ?>" method="post" target="_top"
              action="<?= $arResult["AUTH_URL"] ?>">
            <? foreach ($arResult["POST"] as $key => $value): ?>
                <input type="hidden" name="<?= $key ?>" value="<?= $value ?>"/>
            <? endforeach ?>
            <input type="hidden" name="AUTH_FORM" value="Y"/>
            <input type="hidden" name="TYPE" value="AUTH"/>
            <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
            <table class="authorize_container" width="95%">
                <tr>
                    <td colspan="2">
                        <?= GetMessage("AUTH_LOGIN") ?>:<br/>
                        <input type="text" name="USER_LOGIN" maxlength="50" value="" size="17"/>
                        <script>
                            BX.ready(function () {
                                var loginCookie = BX.getCookie("<?=CUtil::JSEscape($arResult["~LOGIN_COOKIE_NAME"])?>");
                                if (loginCookie) {
                                    var form = document.forms["system_auth_form<?=$arResult["RND"]?>"];
                                    var loginInput = form.elements["USER_LOGIN"];
                                    loginInput.value = loginCookie;
                                }
                            });
                        </script>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <?= GetMessage("AUTH_PASSWORD") ?>:<br/>
                        <input type="password" name="USER_PASSWORD" maxlength="255" size="17" autocomplete="off"/>
                        <? if ($arResult["SECURE_AUTH"]): ?>
                            <span class="bx-auth-secure" id="bx_auth_secure<?= $arResult["RND"] ?>"
                                  title="<? echo GetMessage("AUTH_SECURE_NOTE") ?>" style="display:none">
					<div class="bx-auth-secure-icon"></div>
				</span>
                            <noscript>
				<span class="bx-auth-secure" title="<? echo GetMessage("AUTH_NONSECURE_NOTE") ?>">
					<div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
				</span>
                            </noscript>
                            <script type="text/javascript">
                                document.getElementById('bx_auth_secure<?=$arResult["RND"]?>').style.display = 'inline-block';
                            </script>
                        <? endif ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" id="auth_td">
                        <button id="check_captcha" name="Login"
                                class="btn btn_medium btn_auth">
                            <?= GetMessage("AUTH_LOGIN_BUTTON") ?>
                        </button>
                        <!--input type="submit" id="authorize" name="Login" class="btn btn_medium btn_auth_after"
                               value="<?/*= GetMessage("AUTH_LOGIN_BUTTON") */ ?>"/>-->
                        <label class="not_robot_label">
                            <span>Подтвердите, что вы человек.</span>
                            <input type="checkbox" name="not_robot">
                        </label>
                    </td>
                </tr>
                <? if ($arResult["AUTH_SERVICES"]): ?>
                    <tr>
                        <td colspan="2">
                            <div class="bx-auth-lbl"><?= GetMessage("socserv_as_user_form") ?></div>
                            <?
                            $APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "icons",
                                array(
                                    "AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
                                    "SUFFIX" => "form",
                                ),
                                $component,
                                array("HIDE_ICONS" => "Y")
                            );
                            ?>
                        </td>
                    </tr>
                <? endif ?>
            </table>
        </form>
        <?php if ($arResult["AUTH_SERVICES"]): ?>
            <?
            $APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "",
                array(
                    "AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
                    "AUTH_URL" => $arResult["AUTH_URL"],
                    "POST" => $arResult["POST"],
                    "POPUP" => "Y",
                    "SUFFIX" => "form",
                ),
                $component,
                array("HIDE_ICONS" => "Y")
            );
            ?>
        <? endif ?>
    <?php
    elseif ($arResult["FORM_TYPE"] == "otp"):
        ?>

        <form name="system_auth_form<?= $arResult["RND"] ?>" method="post" target="_top"
              action="<?= $arResult["AUTH_URL"] ?>">
            <? if ($arResult["BACKURL"] <> ''): ?>
                <input type="hidden" name="backurl" value="<?= $arResult["BACKURL"] ?>"/>
            <? endif ?>
            <input type="hidden" name="AUTH_FORM" value="Y"/>
            <input type="hidden" name="TYPE" value="OTP"/>
            <table width="95%">
                <tr>
                    <td colspan="2">
                        <? echo GetMessage("auth_form_comp_otp") ?><br/>
                        <input type="text" name="USER_OTP" maxlength="50" value="" size="17" autocomplete="off"/></td>
                </tr>
                <? if ($arResult["CAPTCHA_CODE"]): ?>
                    <tr>
                        <td colspan="2">
                            <? echo GetMessage("AUTH_CAPTCHA_PROMT") ?>:<br/>
                            <input type="hidden" name="captcha_sid" value="<? echo $arResult["CAPTCHA_CODE"] ?>"/>
                            <img src="/bitrix/tools/captcha.php?captcha_sid=<? echo $arResult["CAPTCHA_CODE"] ?>"
                                 width="180" height="40" alt="CAPTCHA"/><br/><br/>
                            <input type="text" name="captcha_word" maxlength="50" value=""/></td>
                    </tr>
                <? endif ?>
                <? if ($arResult["REMEMBER_OTP"] == "Y"): ?>
                    <tr>
                        <td valign="top"><input type="checkbox" id="OTP_REMEMBER_frm" name="OTP_REMEMBER" value="Y"/>
                        </td>
                        <td width="100%"><label for="OTP_REMEMBER_frm"
                                                title="<? echo GetMessage("auth_form_comp_otp_remember_title") ?>"><? echo GetMessage("auth_form_comp_otp_remember") ?></label>
                        </td>
                    </tr>
                <? endif ?>
                <tr>
                    <td colspan="2"><input type="submit" name="Login" value="<?= GetMessage("AUTH_LOGIN_BUTTON") ?>"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <noindex><a href="<?= $arResult["AUTH_LOGIN_URL"] ?>"
                                    rel="nofollow"><? echo GetMessage("auth_form_comp_auth") ?></a></noindex>
                        <br/></td>
                </tr>
            </table>
        </form>

    <?php
    else:
        ?>
        <form action="<?= $arResult["AUTH_URL"] ?>">
            <table width="95%">
                <tr>
                    <td align="center">
                        <?= $arResult["USER_NAME"] ?><br/>
                        [<?= $arResult["USER_LOGIN"] ?>]<br/>
                        <a href="<?= $arResult["PROFILE_URL"] ?>"
                           title="<?= GetMessage("AUTH_PROFILE") ?>"><?= GetMessage("AUTH_PROFILE") ?></a><br/>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <? foreach ($arResult["GET"] as $key => $value): ?>
                            <input type="hidden" name="<?= $key ?>" value="<?= $value ?>"/>
                        <? endforeach ?>
                        <input type="hidden" name="logout" value="yes"/>
                        <input type="submit" name="logout_butt" value="<?= GetMessage("AUTH_LOGOUT_BUTTON") ?>"/>
                    </td>
                </tr>
            </table>
        </form>
    <?php endif ?>
</div>
<div class="preloader preloader_hidden">
    <svg class="preloader__image" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
        <path fill="currentColor"
              d="M304 48c0 26.51-21.49 48-48 48s-48-21.49-48-48 21.49-48 48-48 48 21.49 48 48zm-48 368c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48zm208-208c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48zM96 256c0-26.51-21.49-48-48-48S0 229.49 0 256s21.49 48 48 48 48-21.49 48-48zm12.922 99.078c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48c0-26.509-21.491-48-48-48zm294.156 0c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48c0-26.509-21.49-48-48-48zM108.922 60.922c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.491-48-48-48z">
        </path>
    </svg>
</div>
<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallbackRecap&render=<?= SITE_KEY ?>" async
        defer></script>
<script>
    $(document).ready(function () {
        // $('.preloader').hide();
        // hidden($('.preloader'));
        hidden($('.not_robot_label'));
        // $('.preloader').css("display","none");
        // $('.not_robot_label').hide();
    });
    $('#check_captcha').on('click', function () {
        if($('input[name=USER_LOGIN]').val() === '')
            return false;
        if($('input[name=USER_PASSWORD]').val() === '')
            return false;
        $('.preloader').show();
        let captcha = {
            recaptchaResponse: $('#recaptchaResponse').val()
        };
        $.post(
            "/ajax/check_recaptcha.php",
            captcha,
            (data) => {
                const json = JSON.parse(data);
                $('.preloader').hide();
                $('#check_captcha').hide();
                $('#auth_td').append(
                    '<input type="submit" id="authorize" name="Login" class="btn btn_medium btn_auth"' +
                    'value="<?= GetMessage("AUTH_LOGIN_BUTTON") ?>"/>'
                );
                if (json.score > 0.4) {
                    $('#authorize').trigger('click');
                } else {
                    $('.not_robot_label').show();
                }
            });
        return false;
    });
    var onloadCallbackRecap = function () {
        grecaptcha.ready(function () {
            grecaptcha.execute("<?= SITE_KEY ?>", {action: 'contact_callback'}).then(function (token) {
                var recaptchaResponse = document.getElementById('recaptchaResponse');
                recaptchaResponse.value = token;
            });
        });
    };

    /**
     * @description this function to Safari and IE 9
     * @description hide element.
     * */
    function hidden(element) {
        $(element).css('display','none');

    }
</script>