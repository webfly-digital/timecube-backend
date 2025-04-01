<?

use Ammina\Optimizer\SettingsTable;
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
$arCurrentItem = false;

$result = new \Bitrix\Main\Entity\Result();

$customTabber = new CAdminTabEngine("OnAdminAmminaOptimizerPageEdit");
$customDraggableBlocks = new CAdminDraggableBlockEngine('OnAdminAmminaOptimizerPageEditDraggable');

if ($isSavingOperation) {
	$errorMessage = '';
	if (!$customTabber->Check()) {
		if ($ex = $APPLICATION->GetException()) {
			$errorMessage .= $ex->GetString();
		} else {
			$errorMessage .= "Custom tabber check unknown error!";
		}

		$result->addError(new \Bitrix\Main\Entity\EntityError($errorMessage));
	}

	if (!$customDraggableBlocks->check()) {
		if ($ex = $APPLICATION->GetException()) {
			$errorMessage .= $ex->GetString();
		} else {
			$errorMessage .= "Custom draggable block check unknown error!";
		}

		$result->addError(new \Bitrix\Main\Entity\EntityError($errorMessage));
	}
	set_time_limit(600);
	$arCheckData = \Ammina\Optimizer\Core2\LibAvailable::doCheckLibrary();
	if ($_REQUEST['FIELDS']['SITES_ALL'] == "Y") {
		$arSettings = SettingsTable::getList(
			array(
				"filter" => array(
					"SITE_ID" => "all",
					"TYPE" => "a",
				),
			)
		)->Fetch();
		if (!$arSettings) {
			$arCurrentSettings = \Ammina\Optimizer\SettingsTable::getSettingsForEdit("all", "a");
			$arCurrentSettings['MAIN']['category']['main']['options']['ACTIVE'] = "Y";
			$arCurrentSettings['MAIN']['category']['css']['options']['ACTIVE'] = "Y";
			$arCurrentSettings['MAIN']['category']['js']['options']['ACTIVE'] = "Y";
			$arCurrentSettings['MAIN']['category']['images']['options']['ACTIVE'] = "Y";
			$arCurrentSettings['MAIN']['category']['other']['options']['ACTIVE'] = "Y";
			\Ammina\Optimizer\SettingsTable::add(
				array(
					"SITE_ID" => "all",
					"TYPE" => "a",
					"SETTINGS" => $arCurrentSettings
				)
			);
		}
		LocalRedirect("/bitrix/admin/ammina.optimizer.settings.php?lang=" . LANGUAGE_ID . "&site=all&type=a");
	} elseif (isset($_REQUEST['FIELDS']['SITES']) && is_array($_REQUEST['FIELDS']['SITES'])) {
		$firstSite = false;
		foreach ($_REQUEST['FIELDS']['SITES'] as $siteId) {
			$arSettings = SettingsTable::getList(
				array(
					"filter" => array(
						"SITE_ID" => $siteId,
						"TYPE" => "a",
					),
				)
			)->Fetch();
			if (!$arSettings) {
				if ($firstSite === false) {
					$firstSite = $siteId;
				}
				$arCurrentSettings = \Ammina\Optimizer\SettingsTable::getSettingsForEdit($siteId, "a");
				$arCurrentSettings['MAIN']['category']['main']['options']['ACTIVE'] = "Y";
				$arCurrentSettings['MAIN']['category']['css']['options']['ACTIVE'] = "Y";
				$arCurrentSettings['MAIN']['category']['js']['options']['ACTIVE'] = "Y";
				$arCurrentSettings['MAIN']['category']['images']['options']['ACTIVE'] = "Y";
				$arCurrentSettings['MAIN']['category']['other']['options']['ACTIVE'] = "Y";
				\Ammina\Optimizer\SettingsTable::add(
					array(
						"SITE_ID" => $siteId,
						"TYPE" => "a",
						"SETTINGS" => $arCurrentSettings
					)
				);
			}
		}
		if ($firstSite === false) {
			foreach ($_REQUEST['FIELDS']['SITES'] as $siteId) {
				$firstSite = $siteId;
				break;
			}
		}
		if ($firstSite === false) {
			$firstSite = "all";
		}
		LocalRedirect("/bitrix/admin/ammina.optimizer.settings.php?lang=" . LANGUAGE_ID . "&site=" . $firstSite . "&type=a");
	}
}

$APPLICATION->SetTitle(Loc::getMessage("AMMINA_OPTIMIZER_PAGE_TITLE"));

CUtil::InitJSCore();

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
/*
Blocks\OrderBasket::getCatalogMeasures();
*/
// context menu
$aMenu = array();
$context = new CAdminContextMenu($aMenu);
$context->Show();

//errors
$errorMessage = "";

if (!$result->isSuccess()) {
	foreach ($result->getErrors() as $error) {
		$errorMessage .= $error->getMessage() . "<br>\n";
	}
}

if (!empty($errorMessage)) {
	$admMessage = new CAdminMessage($errorMessage);
	echo $admMessage->Show();
}

//prepare blocks order
$defaultBlocksPage = array(
	"quick_setting"
);

$formId = "AMMINA_OPTIMIZER_quick_setting";

$aTabs = array(
	array("DIV" => "tab_ammina", "TAB" => Loc::getMessage("AMMINA_OPTIMIZER_TAB_QUICK_SETTING"), "SHOW_WRAP" => "N", "IS_DRAGGABLE" => "Y"),
);
//echo \Ammina\Optimizer\Helpers\Admin\Blocks\Page::getScripts();
?>
<form method="POST" action="<?= $APPLICATION->GetCurPage() . "?lang=" . LANGUAGE_ID . GetFilterParams("filter_", false) ?>" name="<?= $formId ?>_form" id="<?= $formId ?>_form" enctype="multipart/form-data">
    <input type="hidden" name="ID" value="<?= $arCurrentItem['ID'] ?>"/>
	<?
	$tabControl = new CAdminTabControlDrag($formId, $aTabs, $moduleId, false, true);
	$tabControl->AddTabs($customTabber);
	$tabControl->Begin();

	$tabControl->BeginNextTab();
	$customFastNavItems = array();
	$customBlocksPage = array();
	$fastNavItems = array();

	foreach ($customDraggableBlocks->getBlocksBrief() as $blockId => $blockParams) {
		$defaultBlocksPage[] = $blockId;
		$customFastNavItems[$blockId] = $blockParams['TITLE'];
		$customBlocksPage[] = $blockId;
	}

	$blocksPage = $tabControl->getCurrentTabBlocksOrder($defaultBlocksPage);
	$customNewBlockIds = array_diff($customBlocksPage, $blocksPage);
	$blocksPage = array_merge($blocksPage, $customNewBlockIds);

	foreach ($blocksPage as $item) {
		if (isset($customFastNavItems[$item])) {
			$fastNavItems[$item] = $customFastNavItems[$item];
		} else {
			$fastNavItems[$item] = Loc::getMessage("AMMINA_OPTIMIZER_BLOCK_TITLE_" . toUpper($item));
		}
	}
	?>
    <tr>
        <td>
			<?= bitrix_sessid_post() ?>
            <div style="position: relative; vertical-align: top">
				<? $tabControl->DraggableBlocksStart(); ?>
				<?
				foreach ($blocksPage as $blockCode) {
					echo '<a id="' . $blockCode . '" class="adm-ammina-optimizer-fastnav-anchor"></a>';
					$tabControl->DraggableBlockBegin($fastNavItems[$blockCode], $blockCode);
					switch ($blockCode) {
						case "quick_setting":
							?>
                            <table border="0" cellspacing="0" cellpadding="0" width="100%" class="adm-detail-content-table edit-table">
                                <tbody>
                                <tr>
                                    <td class="adm-detail-content-cell-l" width="40%"><?= Loc::getMessage("AMMINA_OPTIMIZER_FIELD_SITES_ALL") ?>:</td>
                                    <td class="adm-detail-content-cell-r">
                                        <input type="hidden" name="FIELDS[SITES_ALL]" value="N"/>
                                        <input type="checkbox" class="adm-bus-input" name="FIELDS[SITES_ALL]" id="FIELD_SITES_ALL" value="Y" checked="checked"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="adm-detail-content-cell-l" width="40%"><?= Loc::getMessage("AMMINA_OPTIMIZER_FIELD_SITES_ONLY") ?>:</td>
                                    <td class="adm-detail-content-cell-r">
										<?
										$rSites = CSite::GetList($b, $o);
										while ($arSite = $rSites->Fetch()) {
											?>
                                            <label><input type="checkbox" class="adm-bus-input" name="FIELDS[SITES][]" id="FIELD_SITES_<?= $arSite['LID'] ?>" value="<?= $arSite['LID'] ?>"/> [<?= $arSite['LID'] ?>] <?= $arSite['NAME'] ?></label>
											<?
										}
										?>
                                    </td>
                                </tr>
								<?

								?>
                                </tbody>
                            </table>
							<?
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
	array()
);

$tabControl->End();
CAmminaOptimizer::showSupportForm();
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");