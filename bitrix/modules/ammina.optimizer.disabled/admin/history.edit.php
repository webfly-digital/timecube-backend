<?

use Bitrix\Main\Localization\Loc;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
Bitrix\Main\Loader::includeModule('ammina.optimizer');
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/ammina.optimizer/prolog.php");

Loc::loadMessages(__FILE__);
$ID = isset($_REQUEST["ID"]) ? intval($_REQUEST["ID"]) : 0;

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

$needFieldsRestore = $_SERVER["REQUEST_METHOD"] == "POST" && !$isSavingOperation;
$isNewItem = ($ID <= 0);
$arCurrentItem = false;
if ($ID > 0) {
	$arCurrentItem = \Ammina\Optimizer\HistoryTable::getList(array(
		"filter" => array(
			"ID" => $ID,
		),
		"select" => array("*", "PAGE_URL" => "PAGE.PAGE_URL"),
	))->fetch();
	if (!$arCurrentItem) {
		$isNewItem = false;
		$ID = false;
	} else {
		$arDataFields = array(
			"DESKTOP_PERFORMANCE" => "performance",
			"DESKTOP_ACCESSIBILITY" => "accessibility",
			"DESKTOP_BESTPRACTICES" => "best-practices",
			"DESKTOP_SEO" => "seo",
			"DESKTOP_PWA" => "pwa",
			"MOBILE_PERFORMANCE" => "performance",
			"MOBILE_ACCESSIBILITY" => "accessibility",
			"MOBILE_BESTPRACTICES" => "best-practices",
			"MOBILE_SEO" => "seo",
			"MOBILE_PWA" => "pwa",
		);
		foreach ($arDataFields as $strField => $strMonitoringField) {
			if (amopt_strpos($strField, 'DESKTOP') === 0) {
				$arCurrentItem[$strField] = intval($arCurrentItem['DESKTOP_DATA']['lighthouseResult']['categories'][$strMonitoringField]['score'] * 100);
			} else {
				$arCurrentItem[$strField] = intval($arCurrentItem['MOBILE_DATA']['lighthouseResult']['categories'][$strMonitoringField]['score'] * 100);
			}
		}
	}
}

$result = new \Bitrix\Main\Entity\Result();

$customTabber = new CAdminTabEngine("OnAdminAmminaOptimizerHistoryEdit");
$customDraggableBlocks = new CAdminDraggableBlockEngine('OnAdminAmminaOptimizerHistoryEditDraggable');
/*
if ($isSavingOperation) {

	$errorMessage = '';
	if (!$customTabber->Check()) {
		if ($ex = $APPLICATION->GetException())
			$errorMessage .= $ex->GetString();
		else
			$errorMessage .= "Custom tabber check unknown error!";

		$result->addError(new \Bitrix\Main\Entity\EntityError($errorMessage));
	}

	if (!$customDraggableBlocks->check()) {
		if ($ex = $APPLICATION->GetException())
			$errorMessage .= $ex->GetString();
		else
			$errorMessage .= "Custom draggable block check unknown error!";

		$result->addError(new \Bitrix\Main\Entity\EntityError($errorMessage));
	}

	if ($isNewItem) {
		$oTableResult = \Ammina\Optimizer\HiTable::add($_POST['FIELDS']);
		$ID = $oTableResult->getId();
	} else {
		$oTableResult = \Ammina\Optimizer\PageTable::update($ID, $_POST['FIELDS']);
	}
	if (!$oTableResult->isSuccess()) {
		$result->addErrors($oTableResult->getErrors());
	}
	if ($result->isSuccess()) {
		if (isset($_POST["save"])) {
			LocalRedirect("/bitrix/admin/ammina.optimizer.page.php?lang=" . LANGUAGE_ID . GetFilterParams("filter_", false));
		} else {
			LocalRedirect("/bitrix/admin/ammina.optimizer.page.edit.php?lang=" . LANGUAGE_ID . "&ID=" . $ID . GetFilterParams("filter_", false));
		}
	}
}
*/
if ($ID > 0) {
	$APPLICATION->SetTitle(Loc::getMessage("AMMINA_OPTIMIZER_PAGE_TITLE_EDIT"));
} else {
	$APPLICATION->SetTitle(Loc::getMessage("AMMINA_OPTIMIZER_PAGE_TITLE_ADD"));
}

CUtil::InitJSCore();

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
/*
Blocks\OrderBasket::getCatalogMeasures();
*/
// context menu
$aMenu = array();
$aMenu[] = array(
	"ICON" => "btn_list",
	"TEXT" => Loc::getMessage("AMMINA_OPTIMIZER_TO_LIST"),
	"TITLE" => Loc::getMessage("AMMINA_OPTIMIZER_TO_LIST_TITLE"),
	"LINK" => "/bitrix/admin/ammina.optimizer.history.php?lang=" . LANGUAGE_ID . GetFilterParams("filter_"),
);


$context = new CAdminContextMenu($aMenu);
$context->Show();

//errors
$errorMessage = "";

if (!$result->isSuccess())
	foreach ($result->getErrors() as $error) {
		$errorMessage .= $error->getMessage() . "<br>\n";
	}

if (!empty($errorMessage)) {
	$admMessage = new CAdminMessage($errorMessage);
	echo $admMessage->Show();
}
$formId = "AMMINA_OPTIMIZER_history_edit";

$aTabs = array(
	array("DIV" => "tab_ammina_history", "TAB" => Loc::getMessage("AMMINA_OPTIMIZER_TAB_HISTORY"), "SHOW_WRAP" => "N", "IS_DRAGGABLE" => "Y"),
	array("DIV" => "tab_ammina_desktop", "TAB" => Loc::getMessage("AMMINA_OPTIMIZER_TAB_DESKTOP"), "SHOW_WRAP" => "N", "IS_DRAGGABLE" => "Y"),
	array("DIV" => "tab_ammina_mobile", "TAB" => Loc::getMessage("AMMINA_OPTIMIZER_TAB_MOBILE"), "SHOW_WRAP" => "N", "IS_DRAGGABLE" => "Y"),
);
//echo \Ammina\Optimizer\Helpers\Admin\Blocks\Page::getScripts();
?>
<form method="POST" action="<?= $APPLICATION->GetCurPage() . "?lang=" . LANGUAGE_ID . GetFilterParams("filter_", false) ?>" name="<?= $formId ?>_form" id="<?= $formId ?>_form" enctype="multipart/form-data">
	<input type="hidden" name="ID" value="<?= $arCurrentItem['ID'] ?>"/>
	<?= bitrix_sessid_post() ?>
	<?
	$tabControl = new CAdminTabControlDrag($formId, $aTabs, $moduleId, false, true);
	$tabControl->AddTabs($customTabber);
	$tabControl->Begin();

	$tabControl->BeginNextTab();
	$defaultBlocksPage = array(
		"history",
		"historyinfo",
	);
	$blocksPage = $tabControl->getCurrentTabBlocksOrder($defaultBlocksPage);
	?>
	<tr>
		<td>
			<div style="position: relative; vertical-align: top">
				<? $tabControl->DraggableBlocksStart(); ?>
				<?
				foreach ($blocksPage as $blockCode) {
					$tabControl->DraggableBlockBegin(Loc::getMessage("AMMINA_OPTIMIZER_BLOCK_TITLE_" . amopt_strtoupper($blockCode)), $blockCode);
					switch ($blockCode) {
						case "history":
							echo \Ammina\Optimizer\Helpers\Admin\Blocks\History::getEdit($arCurrentItem);
							break;
						case "historyinfo":
							echo \Ammina\Optimizer\Helpers\Admin\Blocks\HistoryInfo::getEdit($arCurrentItem);
							break;
					}
					$tabControl->DraggableBlockEnd();
				}
				?>
			</div>
		</td>
	</tr>
	<?
	$tabControl->EndTab();
	$tabControl->BeginNextTab();
	$defaultBlocksPage = array(
		"desktop_info",
		"desktop_performance",
		"desktop_accessibility",
		"desktop_bestpractices",
		"desktop_seo",
		"desktop_pwa",
	);
	$blocksPage = $tabControl->getCurrentTabBlocksOrder($defaultBlocksPage);
	?>
	<tr>
		<td>
			<div style="position: relative; vertical-align: top">
				<? $tabControl->DraggableBlocksStart(); ?>
				<?
				foreach ($blocksPage as $blockCode) {
					$tabControl->DraggableBlockBegin(Loc::getMessage("AMMINA_OPTIMIZER_BLOCK_TITLE_" . amopt_strtoupper($blockCode)), $blockCode);
					switch ($blockCode) {
						case "desktop_info":
							echo \Ammina\Optimizer\Helpers\Admin\Blocks\MonitoringInfo::getEdit($arCurrentItem['DESKTOP_DATA']);
							break;
						case "desktop_performance":
							echo \Ammina\Optimizer\Helpers\Admin\Blocks\MonitoringPerformance::getEdit($arCurrentItem['DESKTOP_DATA']);
							break;
						case "desktop_accessibility":
							echo \Ammina\Optimizer\Helpers\Admin\Blocks\MonitoringAccessibility::getEdit($arCurrentItem['DESKTOP_DATA']);
							break;
						case "desktop_bestpractices":
							echo \Ammina\Optimizer\Helpers\Admin\Blocks\MonitoringBestPractices::getEdit($arCurrentItem['DESKTOP_DATA']);
							break;
						case "desktop_seo":
							echo \Ammina\Optimizer\Helpers\Admin\Blocks\MonitoringSeo::getEdit($arCurrentItem['DESKTOP_DATA']);
							break;
						case "desktop_pwa":
							echo \Ammina\Optimizer\Helpers\Admin\Blocks\MonitoringPwa::getEdit($arCurrentItem['DESKTOP_DATA']);
							break;
					}
					$tabControl->DraggableBlockEnd();
				}
				?>
			</div>
		</td>
	</tr>
	<?
	$tabControl->EndTab();
	$tabControl->BeginNextTab();
	$defaultBlocksPage = array(
		"mobile_info",
		"mobile_performance",
		"mobile_accessibility",
		"mobile_bestpractices",
		"mobile_seo",
		"mobile_pwa",
	);
	$blocksPage = $tabControl->getCurrentTabBlocksOrder($defaultBlocksPage);
	?>
	<tr>
		<td>
			<div style="position: relative; vertical-align: top">
				<? $tabControl->DraggableBlocksStart(); ?>
				<?
				foreach ($blocksPage as $blockCode) {
					$tabControl->DraggableBlockBegin(Loc::getMessage("AMMINA_OPTIMIZER_BLOCK_TITLE_" . amopt_strtoupper($blockCode)), $blockCode);
					switch ($blockCode) {
						case "mobile_info":
							echo \Ammina\Optimizer\Helpers\Admin\Blocks\MonitoringInfo::getEdit($arCurrentItem['MOBILE_DATA']);
							break;
						case "mobile_performance":
							echo \Ammina\Optimizer\Helpers\Admin\Blocks\MonitoringPerformance::getEdit($arCurrentItem['MOBILE_DATA']);
							break;
						case "mobile_accessibility":
							echo \Ammina\Optimizer\Helpers\Admin\Blocks\MonitoringAccessibility::getEdit($arCurrentItem['MOBILE_DATA']);
							break;
						case "mobile_bestpractices":
							echo \Ammina\Optimizer\Helpers\Admin\Blocks\MonitoringBestPractices::getEdit($arCurrentItem['MOBILE_DATA']);
							break;
						case "mobile_seo":
							echo \Ammina\Optimizer\Helpers\Admin\Blocks\MonitoringSeo::getEdit($arCurrentItem['MOBILE_DATA']);
							break;
						case "mobile_pwa":
							echo \Ammina\Optimizer\Helpers\Admin\Blocks\MonitoringPwa::getEdit($arCurrentItem['MOBILE_DATA']);
							break;
					}
					$tabControl->DraggableBlockEnd();
				}
				?>
			</div>
		</td>
	</tr>
<?
$tabControl->EndTab();

$tabControl->Buttons(
	array(
		"back_url" => "/bitrix/admin/ammina.optimizer.history.php?lang=" . LANGUAGE_ID . GetFilterParams("filter_"),
		"btnSave" => false,
		"btnApply" => false,
	)
);

$tabControl->End();
CAmminaOptimizer::showSupportForm();
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");