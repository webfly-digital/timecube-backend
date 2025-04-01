<?
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
?>
<form action="<?echo $APPLICATION->GetCurPage()?>" id="form_creative_kkmserver">
    <?=bitrix_sessid_post()?>
    <input type="hidden" name="lang" value="<?echo LANG?>">
    <input type="hidden" name="id" value="creative.kkmserver">
    <input type="hidden" name="install" value="Y">
    <input type="hidden" name="step" value="2">
    <div class="adm-detail-block">
        <div class="adm-detail-content-wrap">
            <div class="adm-detail-content" style="display: block; padding:12px;">
                <div class="adm-detail-title"><?=Loc::getMessage('CREATIVE_KKMSERVER_KKMSERVER_SETTINGS')?></div>
                <div class="adm-detail-content-item-block">
                    <table cellpadding="3" cellspacing="0" border="0" width="0%">
                        <tr>
                            <td>&nbsp;</td>
                            <td>
                                <table cellpadding="3" cellspacing="0" border="0" width="0%">
                                    <tr>
                                        <td><p><?=Loc::getMessage('CREATIVE_KKMSERVER_KKMSERVER_LOGIN')?></p></td>
                                        <td><input type="input" name="kkmserver_login" value="kkmserver" size="40"></td>
                                    </tr>
                                    <tr>
                                        <td><p><?=Loc::getMessage('CREATIVE_KKMSERVER_KKMSERVER_PASSWORD')?></p></td>
                                        <td><input type="input" name="kkmserver_password" value="<?=md5(time().rand(0,time()))?>" size="40"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <br/>
                    <div id="kkmserver_submit">
                        <input type="submit" style="height:29px!important;" class="adm-btn adm-btn-save" id="kkmserver_submit_button" onclick="document.getElementById('kkmserver_submit_button').setAttribute('disabled','disabled'); document.getElementById('form_creative_kkmserver').submit();" name="inst" value="<?= GetMessage("MOD_INSTALL")?>"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>