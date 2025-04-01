<?

use Bitrix\Main\Localization\Loc;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
Bitrix\Main\Loader::includeModule('ammina.optimizer');
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/ammina.optimizer/prolog.php");

Loc::loadMessages(__FILE__);

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

$sTableID = "tbl_ammina_optimizer_page";

$oSort = new CAdminSorting($sTableID, "PAGE_URL", "asc");
$arOrder = (amopt_strtoupper($by) === "ID" ? array($by => $order) : array($by => $order, "ID" => "ASC"));
$lAdmin = new CAdminUiList($sTableID, $oSort);

$filterFields = array(
	array(
		"id" => "ID",
		"name" => Loc::getMessage("AMMINA_OPTIMIZER_FILTER_ID"),
		"type" => "number",
		"filterable" => "",
	),
	array(
		"id" => "PAGE_URL",
		"name" => Loc::getMessage("AMMINA_OPTIMIZER_FILTER_PAGE_URL"),
		"filterable" => "?",
		"quickSearch" => "?",
		"default" => true,
	),
);

$arFilter = array();
$lAdmin->AddFilter($filterFields, $arFilter);

if ($lAdmin->EditAction()) {
	foreach ($FIELDS as $ID => $postFields) {
		$DB->StartTransaction();
		$ID = IntVal($ID);

		if (!$lAdmin->IsUpdated($ID))
			continue;

		$allowedFields = array(
			"ACTIVE",
			"PAGE_URL",
		);
		$arFields = array();
		foreach ($allowedFields as $fieldId) {
			if (array_key_exists($fieldId, $postFields))
				$arFields[$fieldId] = $postFields[$fieldId];
		}

		$oUpdate = \Ammina\Optimizer\PageTable::update($ID, $arFields);
		if (!$oUpdate->isSuccess()) {
			$lAdmin->AddUpdateError(Loc::getMessage("AMMINA_OPTIMIZER_UPDATE_ERROR", array("#ID#" => $ID, "#ERROR_TEXT#" => implode(", ", $oUpdate->getErrorMessages()))), $ID);
			$DB->Rollback();
		}
		$DB->Commit();
	}
}

if (($arID = $lAdmin->GroupAction()) && $modulePermissions >= "W") {
	if ($_REQUEST['action_target'] == 'selected') {
		$arID = Array();
		$dbResultList = \Ammina\Optimizer\PageTable::getList(array(
			"order" => $arOrder,
			"filter" => $arFilter,
			"select" => array("ID")));
		while ($arResult = $dbResultList->Fetch()) {
			$arID[] = $arResult['ID'];
		}
	}
	$deltaTime = 0;
	foreach ($arID as $ID) {
		if (amopt_strlen($ID) <= 0) {
			continue;
		}

		switch ($_REQUEST['action']) {
			case "delete":
				@set_time_limit(0);
				$arPageOld = \Ammina\Optimizer\PageTable::getList(array(
					"filter" => array("ID" => $ID),
					"select" => array("ID"),
				))->Fetch();
				$DB->StartTransaction();
				$bIsOk = true;
				$rHistory = \Ammina\Optimizer\HistoryTable::getList(array(
					"filter" => array("PAGE_ID" => $ID),
					"select" => array("ID"),
				));
				while ($arHistory = $rHistory->fetch()) {
					$rOperation = \Ammina\Optimizer\HistoryTable::delete($arHistory['ID']);
					if (!$rOperation->isSuccess()) {
						$bIsOk = false;
						$DB->Rollback();
						if ($ex = $APPLICATION->GetException()) {
							$lAdmin->AddGroupError($ex->GetString(), $ID);
						} else {
							$lAdmin->AddGroupError(Loc::getMessage("AMMINA_OPTIMIZER_DELETE_ERROR"), $ID);
						}
					}
				}
				if ($bIsOk) {
					$rOperation = \Ammina\Optimizer\PageTable::delete($ID);
					if (!$rOperation->isSuccess()) {
						$DB->Rollback();
						if ($ex = $APPLICATION->GetException()) {
							$lAdmin->AddGroupError($ex->GetString(), $ID);
						} else {
							$lAdmin->AddGroupError(Loc::getMessage("AMMINA_OPTIMIZER_DELETE_ERROR"), $ID);
						}
					}
					$DB->Commit();
				}
				break;
			case "activate":
			case "deactivate":
				$arFields = Array("ACTIVE" => ($_REQUEST['action'] == "activate" ? "Y" : "N"));
				$oResult = \Ammina\Optimizer\PageTable::update($ID, $arFields);
				if (!$oResult->isSuccess()) {
					$lAdmin->AddGroupError(GetMessage("AMMINA_OPTIMIZER_UPDATE_ERROR") . implode(", ", $oResult->getErrorMessages()), $ID);
				}
				break;
			case "check":
				\Ammina\Optimizer\PageTable::update($ID, array("STATUS" => "P"));
				$arAgent = CAgent::GetList(array(), array(
					"NAME" => '\Ammina\Optimizer\Agent\CheckPage::doExecute(' . $ID . ');',
					"MODULE_ID" => "ammina.optimizer",
				))->Fetch();
				if (!$arAgent) {
					$arFields = Array(
						"NAME" => '\Ammina\Optimizer\Agent\CheckPage::doExecute(' . $ID . ');',
						"MODULE_ID" => "ammina.optimizer",
						"ACTIVE" => "Y",
						"SORT" => 100,
						"IS_PERIOD" => "N",
						"AGENT_INTERVAL" => 3600 * 24,
						"USER_ID" => false,
						"NEXT_EXEC" => ConvertTimeStamp(time() + $deltaTime, "FULL"),
					);
					$deltaTime += 60;
					CAgent::Add($arFields);
				}
				break;
			case "monitoring1":
			case "monitoring7":
				$arAgent = CAgent::GetList(array(), array(
					"NAME" => '\Ammina\Optimizer\Agent\CheckPage::doExecute(' . $ID . ', true);',
					"MODULE_ID" => "ammina.optimizer",
				))->Fetch();
				$arFields = Array(
					"NAME" => '\Ammina\Optimizer\Agent\CheckPage::doExecute(' . $ID . ', true);',
					"MODULE_ID" => "ammina.optimizer",
					"ACTIVE" => "Y",
					"SORT" => 100,
					"IS_PERIOD" => "Y",
					"AGENT_INTERVAL" => 3600 * 24 * ($_REQUEST['action'] == "monitoring1" ? 1 : 7),
					"USER_ID" => false,
					"NEXT_EXEC" => ConvertTimeStamp(time() + $deltaTime, "FULL"),
				);
				if ($arAgent) {
					CAgent::Update($arAgent['ID'], $arFields);
				} else {
					CAgent::Add($arFields);
				}
				$deltaTime += 60;
				break;
			case "monitoringstop":
				$arAgent = CAgent::GetList(array(), array(
					"NAME" => '\Ammina\Optimizer\Agent\CheckPage::doExecute(' . $ID . ', true);',
					"MODULE_ID" => "ammina.optimizer",
				))->Fetch();
				if ($arAgent) {
					CAgent::Delete($arAgent['ID']);
				}
				break;
		}
	}
}


$arHeader = array(
	array(
		"id" => "ID",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_ID"),
		"sort" => "ID",
		"default" => true,
	),
	array(
		"id" => "PAGE_URL",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_PAGE_URL"),
		"sort" => "PAGE_URL",
		"default" => true,
	),
	array(
		"id" => "ACTIVE",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_ACTIVE"),
		"sort" => "ACTIVE",
		"default" => true,
	),
	array(
		"id" => "STATUS",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_STATUS"),
		"sort" => "STATUS",
		"default" => true,
	),
	array(
		"id" => "DATE_CREATE",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_DATE_CREATE"),
		"sort" => "DATE_CREATE",
		"default" => true,
	),
	array(
		"id" => "DATE_CHECK",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_DATE_CHECK"),
		"sort" => "DATE_CHECK",
		"default" => true,
	),
	array(
		"id" => "DESKTOP_PERFORMANCE",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_DESKTOP_PERFORMANCE"),
		"sort" => "DESKTOP_PERFORMANCE",
		"default" => true,
	),
	array(
		"id" => "DESKTOP_ACCESSIBILITY",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_DESKTOP_ACCESSIBILITY"),
		"sort" => "DESKTOP_ACCESSIBILITY",
		"default" => true,
	),
	array(
		"id" => "DESKTOP_BESTPRACTICES",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_DESKTOP_BESTPRACTICES"),
		"sort" => "DESKTOP_BESTPRACTICES",
		"default" => true,
	),
	array(
		"id" => "DESKTOP_SEO",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_DESKTOP_SEO"),
		"sort" => "DESKTOP_SEO",
		"default" => true,
	),
	array(
		"id" => "DESKTOP_PWA",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_DESKTOP_PWA"),
		"sort" => "DESKTOP_PWA",
		"default" => true,
	),
	array(
		"id" => "MOBILE_PERFORMANCE",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_MOBILE_PERFORMANCE"),
		"sort" => "MOBILE_PERFORMANCE",
		"default" => true,
	),
	array(
		"id" => "MOBILE_ACCESSIBILITY",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_MOBILE_ACCESSIBILITY"),
		"sort" => "MOBILE_ACCESSIBILITY",
		"default" => true,
	),
	array(
		"id" => "MOBILE_BESTPRACTICES",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_MOBILE_BESTPRACTICES"),
		"sort" => "MOBILE_BESTPRACTICES",
		"default" => true,
	),
	array(
		"id" => "MOBILE_SEO",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_MOBILE_SEO"),
		"sort" => "MOBILE_SEO",
		"default" => true,
	),
	array(
		"id" => "MOBILE_PWA",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_MOBILE_PWA"),
		"sort" => "MOBILE_PWA",
		"default" => true,
	),
);

$lAdmin->AddHeaders($arHeader);

$usePageNavigation = true;
$navyParams = array();
if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'excel') {
	$usePageNavigation = false;
} else {
	$navyParams = CDBResult::GetNavParams(CAdminUiResult::GetNavSize($sTableID));
	if ($navyParams['SHOW_ALL']) {
		$usePageNavigation = false;
	} else {
		$navyParams['PAGEN'] = (int)$navyParams['PAGEN'];
		$navyParams['SIZEN'] = (int)$navyParams['SIZEN'];
	}
}

$getListParams = array(
	"order" => $arOrder,
	"filter" => $arFilter,
	"select" => array("*"),
);

if ($usePageNavigation) {
	$getListParams['limit'] = $navyParams['SIZEN'];
	$getListParams['offset'] = $navyParams['SIZEN'] * ($navyParams['PAGEN'] - 1);
}
$totalCount = 0;
$totalPages = 0;
if ($usePageNavigation) {
	$totalCount = \Ammina\Optimizer\PageTable::getCount($getListParams['filter']);
	if ($totalCount > 0) {
		$totalPages = ceil($totalCount / $navyParams['SIZEN']);
		if ($navyParams['PAGEN'] > $totalPages)
			$navyParams['PAGEN'] = $totalPages;
	} else {
		$navyParams['PAGEN'] = 1;
	}
	$getListParams['limit'] = $navyParams['SIZEN'];
	$getListParams['offset'] = $navyParams['SIZEN'] * ($navyParams['PAGEN'] - 1);
}

$rsItems = \Ammina\Optimizer\PageTable::getList($getListParams);
$rsItems = new CAdminUiResult($rsItems, $sTableID);
if ($usePageNavigation) {
	$rsItems->NavStart($getListParams['limit'], $navyParams['SHOW_ALL'], $navyParams['PAGEN']);
	$rsItems->NavRecordCount = $totalCount;
	$rsItems->NavPageCount = $totalPages;
	$rsItems->NavPageNomer = $navyParams['PAGEN'];
} else {
	$rsItems->NavStart();
}
$lAdmin->SetNavigationParams($rsItems);

while ($arData = $rsItems->NavNext()) {
	$row =& $lAdmin->AddRow($arData['ID'], $arData, 'ammina.optimizer.page.edit.php?ID=' . $arData['ID'] . '&lang=' . LANGUAGE_ID, Loc::getMessage("AMMINA_OPTIMIZER_RECORD_EDIT"));
	$row->AddCheckField("ACTIVE");
	$row->AddInputField("PAGE_URL");
	$row->AddViewField("STATUS", Loc::getMessage("AMMINA_OPTIMIZER_FIELD_STATUS_" . $arData['STATUS']));
	$arDataFields = array(
		"DESKTOP_PERFORMANCE",
		"DESKTOP_ACCESSIBILITY",
		"DESKTOP_BESTPRACTICES",
		"DESKTOP_SEO",
		"DESKTOP_PWA",
		"MOBILE_PERFORMANCE",
		"MOBILE_ACCESSIBILITY",
		"MOBILE_BESTPRACTICES",
		"MOBILE_SEO",
		"MOBILE_PWA",
	);
	foreach ($arDataFields as $strField) {
		$strValue = '';
		if ($arData[$strField] >= 90) {
			$strValue .= '<span style="color:#178239;font-weight:bold;font-size:1.2em;">' . $arData[$strField] . '</span>';
		} elseif ($arData[$strField] >= 50) {
			$strValue .= '<span style="color:#e67700;;font-weight:bold;font-size:1.2em;">' . $arData[$strField] . '</span>';
		} else {
			$strValue .= '<span style="color:#c7221f;font-weight:bold;font-size:1.2em;">' . $arData[$strField] . '</span>';
		}
		if ($arData['OLD_DATA'][$strField] > 0) {
			if ($arData[$strField] > $arData['OLD_DATA'][$strField]) {
				$strValue .= '&nbsp;&nbsp;&nbsp;<span style="color:#178239;font-weight:bold;font-size:0.9em;">&uarr; ' . (intval($arData[$strField] - $arData['OLD_DATA'][$strField])) . '</span>';
			} elseif ($arData[$strField] < $arData['OLD_DATA'][$strField]) {
				$strValue .= '&nbsp;&nbsp;&nbsp;<span style="color:#c7221f;font-weight:bold;font-size:0.9em;">&darr; ' . (intval($arData[$strField] - $arData['OLD_DATA'][$strField])) . '</span>';
			} else {
				$strValue .= '&nbsp;&nbsp;&nbsp;<span style="color:rgba(0,0,0,0.54);font-weight:normal;font-size:0.9em;">+ 0</span>';
			}
		}
		$row->AddViewField($strField, $strValue);
	}
	$arActions = array();
	if ($modulePermissions >= "W") {
		$arActions[] = array(
			"ICON" => "edit",
			"TEXT" => Loc::getMessage("MAIN_ADMIN_MENU_EDIT"),
			"DEFAULT" => true,
			"ACTION" => $lAdmin->ActionRedirect("ammina.optimizer.page.edit.php?ID=" . $arData['ID'] . "&lang=" . LANGUAGE_ID),
		);
		$arActions[] = array(
			"SEPARATOR" => true,
		);
		$arActions[] = array(
			"TEXT" => Loc::getMessage("AMMINA_OPTIMIZER_ACTION_CHECK"),
			"DEFAULT" => true,
			"ACTION" => $lAdmin->ActionDoGroup($arData['ID'], "check"),
		);
		$arActions[] = array(
			"TEXT" => Loc::getMessage("AMMINA_OPTIMIZER_ACTION_MONITORING_DAY"),
			"DEFAULT" => true,
			"ACTION" => $lAdmin->ActionDoGroup($arData['ID'], "monitoring1"),
		);
		$arActions[] = array(
			"TEXT" => Loc::getMessage("AMMINA_OPTIMIZER_ACTION_MONITORING_WEEK"),
			"DEFAULT" => true,
			"ACTION" => $lAdmin->ActionDoGroup($arData['ID'], "monitoring7"),
		);
		$arActions[] = array(
			"TEXT" => Loc::getMessage("AMMINA_OPTIMIZER_ACTION_MONITORING_STOP"),
			"DEFAULT" => true,
			"ACTION" => $lAdmin->ActionDoGroup($arData['ID'], "monitoringstop"),
		);

		$arActions[] = array(
			"SEPARATOR" => true,
		);
		$arActions[] = array(
			"ICON" => "delete",
			"TEXT" => Loc::getMessage("AMMINA_OPTIMIZER_ACTION_DELETE"),
			"ACTION" => "if(confirm('" . Loc::getMessage('AMMINA_OPTIMIZER_ACTION_DELETE_CONFIRM') . "')) " . $lAdmin->ActionDoGroup($arData['ID'], "delete"),
		);

	}

	if (count($arActions) > 0) {
		$row->AddActions($arActions);
	}
}

$lAdmin->AddFooter(
	array(
		array("title" => Loc::getMessage("MAIN_ADMIN_LIST_SELECTED"), "value" => $rsItems->SelectedRowsCount()),
		array("counter" => true, "title" => Loc::getMessage("MAIN_ADMIN_LIST_CHECKED"), "value" => "0"),
	)
);

if ($modulePermissions >= "W") {
	$aContext = array(
		array(
			"TEXT" => Loc::getMessage("AMMINA_OPTIMIZER_NEW_RECORD"),
			"ICON" => "btn_new",
			"LINK" => "ammina.optimizer.page.edit.php?lang=" . LANGUAGE_ID,
			"TITLE" => Loc::getMessage("AMMINA_OPTIMIZER_NEW_RECORD_TITLE"),
		),
	);

	$lAdmin->AddAdminContextMenu($aContext);

	$lAdmin->AddGroupActionTable(Array(
		"edit" => true,
		"delete" => true,
		"activate" => Loc::getMessage("MAIN_ADMIN_LIST_ACTIVATE"),
		"deactivate" => Loc::getMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
		"check" => Loc::getMessage("AMMINA_OPTIMIZER_ACTION_CHECK"),
		"monitoring1" => Loc::getMessage("AMMINA_OPTIMIZER_ACTION_MONITORING_DAY"),
		"monitoring7" => Loc::getMessage("AMMINA_OPTIMIZER_ACTION_MONITORING_WEEK"),
		"monitoringstop" => Loc::getMessage("AMMINA_OPTIMIZER_ACTION_MONITORING_STOP"),
	));
}

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(Loc::getMessage("AMMINA_OPTIMIZER_PAGE_TITLE"));

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

$lAdmin->DisplayFilter($filterFields);

if (CAmminaOptimizer::getTestPeriodInfo() == \Bitrix\Main\Loader::MODULE_DEMO) {
	CAdminMessage::ShowMessage(array("MESSAGE" => Loc::getMessage("AMMINA_OPTIMIZER_SYS_MODULE_IS_DEMO"), "HTML" => true));
} elseif (CAmminaOptimizer::getTestPeriodInfo() == \Bitrix\Main\Loader::MODULE_DEMO_EXPIRED) {
	CAdminMessage::ShowMessage(array("MESSAGE" => Loc::getMessage("AMMINA_OPTIMIZER_SYS_MODULE_IS_DEMO_EXPIRED"), "HTML" => true));
}

$lAdmin->DisplayList();
CAmminaOptimizer::showSupportForm();
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
