<?php
use \Bitrix\Main\Localization\Loc;

$tabControl->BeginNextTab();
?>
<tr>
    <td colspan="2" align="left">
        <?= Loc::getMessage('LABEL_TITLE_HELP_BEGIN_TOP') ?>    
        <script data-b24-form="inline/39/pvtfei" data-skip-moving="true">
                (function(w,d,u){
                        var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/180000|0);
                        var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
                })(window,document,'https://cdn-ru.bitrix24.ru/b991925/crm/form/loader_39.js');
        </script>
    </td>
</tr>
    <tr>
        <td colspan="2" align="left">
            <p class="c-paragraph"><?= Loc::getMessage('LABEL_TITLE_HELP_BEGIN') ?>.</p>
            <?= Loc::getMessage('LABEL_TITLE_HELP_BEGIN_TEXT'); ?>                            
            <h2><?= Loc::getMessage('LABEL_TITLE_HELP_DONATE_SUPPORT'); ?></h2>
            <p><?= Loc::getMessage('LABEL_TITLE_HELP_DONATE_SUPPORT_TEXT'); ?></p>                        
        </td>
    </tr>