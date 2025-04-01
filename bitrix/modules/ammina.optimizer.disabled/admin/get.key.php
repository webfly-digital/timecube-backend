<?

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
Bitrix\Main\Loader::includeModule('ammina.optimizer');
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/ammina.optimizer/prolog.php");

Loc::loadMessages(__FILE__);

$isSavingOperation = ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["getkey"]) && $_REQUEST['getkey'] == "Y" && check_bitrix_sessid());

$arUserGroups = $USER->GetUserGroupArray();
$modulePermissions = $APPLICATION->GetGroupRight("ammina.optimizer");

if ($modulePermissions < "W") {
	$APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
}

if (CAmminaOptimizer::getTestPeriodInfo() == \Bitrix\Main\Loader::MODULE_DEMO_EXPIRED) {
	require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
	CAdminMessage::ShowMessage(array("MESSAGE" => Loc::getMessage("AMMINA_OPTIMIZER_SYS_MODULE_IS_DEMO_EXPIRED"), "HTML" => true));
	require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
	die();
}


$APPLICATION->SetTitle(Loc::getMessage("AMMINA_OPTIMIZER_PAGE_TITLE"));
//CUtil::InitJSCore();
CJSCore::Init(array("jquery2"));
$APPLICATION->SetAdditionalCSS("/bitrix/themes/.default/ammina.optimizer/css/regular.css");
$APPLICATION->SetAdditionalCSS("/bitrix/themes/.default/ammina.optimizer/css/solid.css");
$APPLICATION->SetAdditionalCSS("/bitrix/themes/.default/ammina.optimizer/css/fontawesome.css");
$bResultGetKey = false;
if ($isSavingOperation) {
	$errorMessage = "";
	$bResultGetKey = CAmminaOptimizer::getAmminaApiKey($_REQUEST['AFIELDS']['NAME'], $_REQUEST['AFIELDS']['EMAIL'], $_REQUEST['AFIELDS']['PHONE'], $errorMessage);
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

if ($bResultGetKey) {
	$admMessage = new CAdminMessage(
		array(
			"TYPE" => "OK",
			"MESSAGE" => Loc::getMessage("AMMINA_OPTIMIZER_GETKEY_OK"),
			"DETAILS" => Loc::getMessage("AMMINA_OPTIMIZER_GETKEY_OK2"),
			"HTML" => true,
		)
	);
	echo $admMessage->Show();
} else {
	if (!empty($errorMessage)) {
		$admMessage = new CAdminMessage($errorMessage);
		echo $admMessage->Show();
	}
	?>
    <script type="text/javascript">
        function amGetKeyResetError(el) {
            $(el).parents(".ammina-getgey-left-block:first").find(".ammina-getgey-left-block-error").remove();
            $(el).removeClass("error");
        }

        function amGetKeyShowError(el, mess) {
            $(el).parents(".ammina-getgey-left-block:first").append('<div class="ammina-getgey-left-block-error">' + mess + '</div>');
            $(el).addClass("error");
        }

        $(document).ready(function () {
            $("#am-getkey").click(function () {
                var form = $("form[name='ammina_get_key_form']");
                var fieldName = $("#AFIELDS_NAME");
                var fieldEmail = $("#AFIELDS_EMAIL");
                var fieldPhone = $("#AFIELDS_PHONE");
                var bIsError = false;
                amGetKeyResetError(fieldEmail);
                var regTest = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
                if (fieldEmail.val().length < 1) {
                    bIsError = true;
                    amGetKeyShowError(fieldEmail, '<?=Loc::getMessage("ammina.optimizer_INSTALLFORM_ERROR_EMAIL1")?>');
                } else if (!regTest.test(fieldEmail.val())) {
                    bIsError = true;
                    amGetKeyShowError(fieldEmail, '<?=Loc::getMessage("ammina.optimizer_INSTALLFORM_ERROR_EMAIL2")?>');
                }
                if (!bIsError) {
                    form.submit();
                }
            });
        });
    </script>
    <div class="aopt-getgey">
        <form method="POST" action="<?= $APPLICATION->GetCurPage() . "?lang=" . LANGUAGE_ID . GetFilterParams("filter_", false) ?>" name="ammina_get_key_form" id="ammina_get_key_form" enctype="multipart/form-data">
			<?= bitrix_sessid_post() ?>
            <input name="getkey" value="Y" type="hidden"/>
            <div class="ammina-getgey-form">
                <div class="ammina-getgey-wrapper">
                    <div class="ammina-getgey-left">
                        <div class="ammina-getgey-left-text">
							<?= Loc::getMessage("AMMINA_OPTIMIZER_FORM_TITLE") ?>
                        </div>
                        <div class="ammina-getgey-left-block">
                            <label for="AFIELDS_EMAIL"><?= Loc::getMessage("AMMINA_OPTIMIZER_FORM_FIELD_EMAIL") ?>
                                :</label>
                            <input type="text" name="AFIELDS[EMAIL]" id="AFIELDS_EMAIL" value="<?= $USER->GetEmail() ?>" placeholder="mail@example.ru"/>
                        </div>
                        <div class="ammina-getgey-left-block">
                            <label for="AFIELDS_NAME"><?= Loc::getMessage("AMMINA_OPTIMIZER_FORM_FIELD_NAME") ?>
                                :</label>
                            <input type="text" name="AFIELDS[NAME]" id="AFIELDS_NAME" value="<?= $USER->GetFullName() ?>" placeholder="<?= Loc::getMessage("ammina.optimizer_INSTALLFORM_FIELD_NAME_PLACEHOLDER") ?>"/>
                        </div>
                        <div class="ammina-getgey-left-block">
                            <label for="AFIELDS_PHONE"><?= Loc::getMessage("AMMINA_OPTIMIZER_FORM_FIELD_PHONE") ?>
                                :</label>
                            <input type="text" name="AFIELDS[PHONE]" id="AFIELDS_PHONE" value="" placeholder="+7 (495) 111-11-11"/>
                        </div>
                        <div class="ammina-getgey-left-note">
							<?= Loc::getMessage("AMMINA_OPTIMIZER_FORM_FIELDS_REQUIRED") ?>
                        </div>
                        <div class="ammina-getgey-left-text-rules">
							<?= Loc::getMessage("AMMINA_OPTIMIZER_FORM_RULES") ?>
                        </div>
                        <div class="ammina-getgey-left-text-rules">
							<?= Loc::getMessage("AMMINA_OPTIMIZER_FORM_RULES2") ?>
                        </div>
                        <div class="ammina-getgey-left-text2">
							<?= Loc::getMessage("AMMINA_OPTIMIZER_FORM_TITLE2") ?>
                        </div>
                    </div>
                </div>
            </div>
            <div style="clear: both;"></div>
            <input type="button" name="inst" value="<?= GetMessage("AMMINA_OPTIMIZER_FORM_BUTTON") ?>" id="am-getkey"/>
        </form>
    </div>
	<?
}
CAmminaOptimizer::showSupportForm();
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");