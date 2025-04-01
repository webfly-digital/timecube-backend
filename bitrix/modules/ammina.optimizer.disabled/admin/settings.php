<?

use Bitrix\Main\Localization\Loc;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
Bitrix\Main\Loader::includeModule('ammina.optimizer');
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/ammina.optimizer/prolog.php");

Loc::loadMessages(__FILE__);

$isSavingOperation = (
	$_SERVER["REQUEST_METHOD"] == "POST"
	&& (
		isset($_POST["apply"])
		|| isset($_POST["save"])
	)
	&& check_bitrix_sessid()
);

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

$arAllSites = array();
$rSites = CSite::GetList($b, $o);
while ($arSite = $rSites->Fetch()) {
	$arAllSites[$arSite['LID']] = $arSite['NAME'];
}
$strSite = $_REQUEST['site'];
if (!isset($arAllSites[$strSite])) {
	$strSite = "all";
}
$strType = $_REQUEST['type'];
if (!in_array($strType, array("a", "d", "m"))) {
	$strType = "a";
}
if ($strSite == "all") {
	if ($strType == "a") {
		$strTitle = Loc::getMessage("AMMINA_OPTIMIZER_TITLE_DEF_ALL");
	} elseif ($strType == "d") {
		$strTitle = Loc::getMessage("AMMINA_OPTIMIZER_TITLE_DEF_DESKTOP");
	} elseif ($strType == "m") {
		$strTitle = Loc::getMessage("AMMINA_OPTIMIZER_TITLE_DEF_MOBILE");
	}
} else {
	if ($strType == "a") {
		$strTitle = Loc::getMessage("AMMINA_OPTIMIZER_TITLE_SITE_ALL", array("#SITE_NAME#" => "[" . $strSite . "] " . $arAllSites[$strSite]));
	} elseif ($strType == "d") {
		$strTitle = Loc::getMessage("AMMINA_OPTIMIZER_TITLE_SITE_DESKTOP", array("#SITE_NAME#" => "[" . $strSite . "] " . $arAllSites[$strSite]));
	} elseif ($strType == "m") {
		$strTitle = Loc::getMessage("AMMINA_OPTIMIZER_TITLE_SITE_MOBILE", array("#SITE_NAME#" => "[" . $strSite . "] " . $arAllSites[$strSite]));
	}
}
$result = new \Bitrix\Main\Entity\Result();
if ($isSavingOperation) {

	$errorMessage = '';
	$arSettings = \Ammina\Optimizer\SettingsTable::getList(array(
		"filter" => array(
			"SITE_ID" => $strSite,
			"TYPE" => $strType,
		),
	))->Fetch();

	if ($arSettings) {
		$oTableResult = \Ammina\Optimizer\SettingsTable::update($arSettings['ID'], array(
			"SETTINGS" => $_POST['AMOPT'],
		));
	} else {
		$oTableResult = \Ammina\Optimizer\SettingsTable::add(array(
			"SITE_ID" => $strSite,
			"TYPE" => $strType,
			"SETTINGS" => $_POST['AMOPT'],
		));
	}

	if (!$oTableResult->isSuccess()) {
		$result->addErrors($oTableResult->getErrors());
	}

	if ($result->isSuccess()) {
		LocalRedirect("/bitrix/admin/ammina.optimizer.settings.php?lang=" . LANGUAGE_ID . "&site=" . $strSite . "&type=" . $strType);
	}
}

$APPLICATION->SetTitle($strTitle);
include_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/ammina.optimizer/option.show.settings.php");
$arAllOptionsDescription = include($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/ammina.optimizer/option.descriptions.php");
$arCurrentSettings = \Ammina\Optimizer\SettingsTable::getSettingsForEdit($strSite, $strType);

if (check_bitrix_sessid() && $_REQUEST['action'] == "delete" && $_REQUEST['ID'] > 0) {
	\Ammina\Optimizer\SettingsTable::delete($_REQUEST['ID']);
	LocalRedirect("/bitrix/admin/ammina.optimizer.settings.php?lang=" . LANGUAGE_ID . "&site=" . $strSite . "&type=" . $strType);
}

CUtil::InitJSCore();
CJSCore::Init(array("jquery2"));
\Bitrix\Main\Page\Asset::getInstance()->addJs("/bitrix/js/ammina.optimizer/admin/settings.js");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
$formId = "AMMINA_OPTIMIZER_page_settings";
if (($strSite != "all" || $strType != "a") && ($arCurrentSettings['DB_INFO']['SITE_ID'] != $strSite || $arCurrentSettings['DB_INFO']['TYPE'] != $strType)) {
	if ($arCurrentSettings['DB_INFO']['SITE_ID'] == "all") {
		if ($arCurrentSettings['DB_INFO']['TYPE'] == "a") {
			$strUseSettings = Loc::getMessage("AMMINA_OPTIMIZER_TITLE_DEF_ALL");
		} elseif ($arCurrentSettings['DB_INFO']['TYPE'] == "d") {
			$strUseSettings = Loc::getMessage("AMMINA_OPTIMIZER_TITLE_DEF_DESKTOP");
		} elseif ($arCurrentSettings['DB_INFO']['TYPE'] == "m") {
			$strUseSettings = Loc::getMessage("AMMINA_OPTIMIZER_TITLE_DEF_MOBILE");
		}
	} else {
		if ($arCurrentSettings['DB_INFO']['TYPE'] == "a") {
			$strUseSettings = Loc::getMessage("AMMINA_OPTIMIZER_TITLE_SITE_ALL", array("#SITE_NAME#" => "[" . $arCurrentSettings['DB_INFO']['SITE_ID'] . "] " . $arAllSites[$arCurrentSettings['DB_INFO']['SITE_ID']]));
		} elseif ($arCurrentSettings['DB_INFO']['TYPE'] == "d") {
			$strUseSettings = Loc::getMessage("AMMINA_OPTIMIZER_TITLE_SITE_DESKTOP", array("#SITE_NAME#" => "[" . $arCurrentSettings['DB_INFO']['SITE_ID'] . "] " . $arAllSites[$arCurrentSettings['DB_INFO']['SITE_ID']]));
		} elseif ($arCurrentSettings['DB_INFO']['TYPE'] == "m") {
			$strUseSettings = Loc::getMessage("AMMINA_OPTIMIZER_TITLE_SITE_MOBILE", array("#SITE_NAME#" => "[" . $arCurrentSettings['DB_INFO']['SITE_ID'] . "] " . $arAllSites[$arCurrentSettings['DB_INFO']['SITE_ID']]));
		} else {
			$strUseSettings = Loc::getMessage("AMMINA_OPTIMIZER_TITLE_DEFAULT");
		}
	}
	$strMessage = Loc::getMessage("AMMINA_OPTIMIZER_NO_SETTINGS_MESSAGE", array(
		"#CURRENTSETTINGS#" => $strTitle,
		"#LINK#" => "/bitrix/admin/ammina.optimizer.settings.php?lang=" . LANGUAGE_ID . "&site=" . (amopt_strlen($arCurrentSettings['DB_INFO']['SITE_ID']) > 0 ? $arCurrentSettings['DB_INFO']['SITE_ID'] : "all") . "&type=" . (strlen($arCurrentSettings['DB_INFO']['TYPE']) > 0 ? $arCurrentSettings['DB_INFO']['TYPE'] : "a"),
		"#USESETTINGS#" => $strUseSettings,
	));
	CAdminMessage::ShowMessage(array("MESSAGE" => $strMessage, "HTML" => true, "TYPE" => "OK"));
}

if ($arCurrentSettings['DB_INFO']['ID'] > 0 && $arCurrentSettings['DB_INFO']['SITE_ID'] == $strSite && $arCurrentSettings['DB_INFO']['TYPE'] == $strType) {
	$aMenu[] = array(
		"TEXT" => Loc::getMessage("AMMINA_OPTIMIZER_DELETE"),
		"TITLE" => Loc::getMessage("AMMINA_OPTIMIZER_DELETE_TITLE"),
		"LINK" => "javascript:if(confirm('" . GetMessage("AMMINA_OPTIMIZER_DELETE_CONFIRM") . "')) window.location='/bitrix/admin/ammina.optimizer.settings.php?site=" . $strSite . "&type=" . $strType . "&action=delete&ID=" . $arCurrentSettings['DB_INFO']['ID'] . "&" . bitrix_sessid_get() . "&lang=" . LANGUAGE_ID . "';",
		"ICON" => "btn_delete");
	$context = new CAdminContextMenu($aMenu);
	$context->Show();
}
$aTabs = array(
	array("DIV" => "tab_ammina", "TAB" => Loc::getMessage("AMMINA_OPTIMIZER_TAB_PAGE"), "TITLE" => Loc::getMessage("AMMINA_OPTIMIZER_TAB_PAGE"), "SHOW_WRAP" => "N", "IS_DRAGGABLE" => "N"),
);
$iPages = 1;
if (isset($arCurrentSettings['PAGES'])) {
	foreach ($arCurrentSettings['PAGES'] as $k => $arPagesInfo) {
		$aTabs[] = array("DIV" => "tab_ammina_pages_" . $iPages, "TAB" => (amopt_strlen($arPagesInfo['page']['NAME']) > 0 ? $arPagesInfo['page']['NAME'] : Loc::getMessage("AMMINA_OPTIMIZER_TAB_PAGE_PAGES", array("#ID#" => $iPages))), "TITLE" => (strlen($arPagesInfo['page']['NAME']) > 0 ? $arPagesInfo['page']['NAME'] : Loc::getMessage("AMMINA_OPTIMIZER_TAB_PAGE_PAGES", array("#ID#" => $iPages))), "SHOW_WRAP" => "N", "IS_DRAGGABLE" => "N");
		$iPages++;
	}
}
$aTabs[] = array("DIV" => "tab_ammina_pages_" . $iPages, "TAB" => Loc::getMessage("AMMINA_OPTIMIZER_TAB_PAGE_PAGES", array("#ID#" => $iPages)), "TITLE" => Loc::getMessage("AMMINA_OPTIMIZER_TAB_PAGE_PAGES", array("#ID#" => $iPages)), "SHOW_WRAP" => "N", "IS_DRAGGABLE" => "N");

?>
<form method="POST" action="<?= ($APPLICATION->GetCurPage() . "?lang=" . LANGUAGE_ID . "&site=" . $strSite . "&type=" . $strType) ?>" name="<?= $formId ?>_form" id="<?= $formId ?>_form" enctype="multipart/form-data">
<?= bitrix_sessid_post() ?>
<?
$tabControl = new CAdminTabControl($formId, $aTabs, "ammina.optimizer", true, true);
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
	<tr>
		<td>
			<div class="amopts">
				<? doAMOPTShowFormSettings($arAllOptionsDescription, $arCurrentSettings['MAIN'], "[MAIN]"); ?>
			</div>
		</td>
	</tr>
<?
$tabControl->EndTab();
$iPages = 1;
if (isset($arCurrentSettings['PAGES'])) {
	foreach ($arCurrentSettings['PAGES'] as $k => $arPagesInfo) {
		$tabControl->BeginNextTab();
		?>
		<tr>
			<td>
				<div class="amopts">
					<? doAMOPTShowFormPage($arPagesInfo, "[PAGES][" . $iPages . "]"); ?>
					<? doAMOPTShowFormSettings($arAllOptionsDescription, $arPagesInfo, "[PAGES][" . $iPages . "]", true); ?>
				</div>
			</td>
		</tr>
		<?
		$tabControl->EndTab();
		$iPages++;
	}
}
$tabControl->BeginNextTab();
?>
	<tr>
		<td>
			<div class="amopts">
				<? doAMOPTShowFormPage(array(), "[PAGES][" . $iPages . "]"); ?>
				<? doAMOPTShowFormSettings($arAllOptionsDescription, array(), "[PAGES][" . $iPages . "]", true); ?>
			</div>
		</td>
	</tr>
<?
$tabControl->EndTab();

$tabControl->Buttons(
	array(
		"back_url" => "/bitrix/admin/ammina.optimizer.settings.php?lang=" . LANGUAGE_ID . "&site=" . $strSite . "&type=" . $strType)
);

$tabControl->End();

CAmminaOptimizer::showSupportForm();
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");