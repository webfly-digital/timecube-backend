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

$sTableID = "tbl_ammina_optimizer_history";

$oSort = new CAdminSorting($sTableID, "DATE_CHECK", "desc");
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
		"id" => "PAGE.PAGE_URL",
		"name" => Loc::getMessage("AMMINA_OPTIMIZER_FILTER_PAGE_URL"),
		"filterable" => "?",
		"quickSearch" => "?",
		"default" => true,
	),
);

$arFilter = array();
$lAdmin->AddFilter($filterFields, $arFilter);

if (($arID = $lAdmin->GroupAction()) && $modulePermissions >= "W") {
	if ($_REQUEST['action_target'] == 'selected') {
		$arID = Array();
		$dbResultList = \Ammina\Optimizer\HistoryTable::getList(array(
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
				$DB->StartTransaction();
				$rOperation = \Ammina\Optimizer\HistoryTable::delete($ID);
				if (!$rOperation->isSuccess()) {
					$DB->Rollback();
					if ($ex = $APPLICATION->GetException()) {
						$lAdmin->AddGroupError($ex->GetString(), $ID);
					} else {
						$lAdmin->AddGroupError(Loc::getMessage("AMMINA_OPTIMIZER_DELETE_ERROR"), $ID);
					}
				}
				$DB->Commit();
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
		"id" => "DATE_CHECK",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_DATE_CHECK"),
		"sort" => "DATE_CHECK",
		"default" => true,
	),
	array(
		"id" => "DESKTOP_PERFORMANCE",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_DESKTOP_PERFORMANCE"),
		"default" => true,
	),
	array(
		"id" => "DESKTOP_ACCESSIBILITY",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_DESKTOP_ACCESSIBILITY"),
		"default" => true,
	),
	array(
		"id" => "DESKTOP_BESTPRACTICES",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_DESKTOP_BESTPRACTICES"),
		"default" => true,
	),
	array(
		"id" => "DESKTOP_SEO",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_DESKTOP_SEO"),
		"default" => true,
	),
	array(
		"id" => "DESKTOP_PWA",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_DESKTOP_PWA"),
		"default" => true,
	),
	array(
		"id" => "MOBILE_PERFORMANCE",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_MOBILE_PERFORMANCE"),
		"default" => true,
	),
	array(
		"id" => "MOBILE_ACCESSIBILITY",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_MOBILE_ACCESSIBILITY"),
		"default" => true,
	),
	array(
		"id" => "MOBILE_BESTPRACTICES",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_MOBILE_BESTPRACTICES"),
		"default" => true,
	),
	array(
		"id" => "MOBILE_SEO",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_MOBILE_SEO"),
		"default" => true,
	),
	array(
		"id" => "MOBILE_PWA",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_MOBILE_PWA"),
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
	"select" => array("*", "PAGE_URL" => "PAGE.PAGE_URL"),
);

if ($usePageNavigation) {
	$getListParams['limit'] = $navyParams['SIZEN'];
	$getListParams['offset'] = $navyParams['SIZEN'] * ($navyParams['PAGEN'] - 1);
}
$totalCount = 0;
$totalPages = 0;
if ($usePageNavigation) {
	$totalCount = \Ammina\Optimizer\HistoryTable::getCount($getListParams['filter']);
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

$rsItems = \Ammina\Optimizer\HistoryTable::getList($getListParams);
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
	$row =& $lAdmin->AddRow($arData['ID'], $arData, 'ammina.optimizer.history.edit.php?ID=' . $arData['ID'] . '&lang=' . LANGUAGE_ID, Loc::getMessage("AMMINA_OPTIMIZER_RECORD_EDIT"));

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
			$arData[$strField] = intval($arData['DESKTOP_DATA']['lighthouseResult']['categories'][$strMonitoringField]['score'] * 100);
		} else {
			$arData[$strField] = intval($arData['MOBILE_DATA']['lighthouseResult']['categories'][$strMonitoringField]['score'] * 100);
		}
		$strValue = '';
		if ($arData[$strField] >= 90) {
			$strValue .= '<span style="color:#178239;font-weight:bold;font-size:1.2em;">' . $arData[$strField] . '</span>';
		} elseif ($arData[$strField] >= 50) {
			$strValue .= '<span style="color:#e67700;;font-weight:bold;font-size:1.2em;">' . $arData[$strField] . '</span>';
		} else {
			$strValue .= '<span style="color:#c7221f;font-weight:bold;font-size:1.2em;">' . $arData[$strField] . '</span>';
		}
		$row->AddViewField($strField, $strValue);
	}

	$arActions = array();
	if ($modulePermissions >= "W") {
		$arActions[] = array(
			"ICON" => "edit",
			"TEXT" => Loc::getMessage("MAIN_ADMIN_MENU_EDIT"),
			"DEFAULT" => true,
			"ACTION" => $lAdmin->ActionRedirect("ammina.optimizer.history.edit.php?ID=" . $arData['ID'] . "&lang=" . LANGUAGE_ID),
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
	$aContext = array();

	$lAdmin->AddAdminContextMenu($aContext);

	$lAdmin->AddGroupActionTable(Array(
		"delete" => true,
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
