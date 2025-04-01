<?

use Bitrix\Main\Loader;
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

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_REQUEST["UpdateFiles"] == "Y") {
	if (!isset($INTERVAL))
		$INTERVAL = 20;
	else
		$INTERVAL = intval($INTERVAL);

	@set_time_limit(0);

	$start_time = time();

	$arErrors = array();
	$arMessages = array();

	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_js.php");

	if (array_key_exists("NS", $_POST) && is_array($_POST["NS"])) {
		$NS = $_POST["NS"];
	} else {
		$NS = array(
			"INTERVAL" => $_REQUEST['INTERVAL'],
			"START_TIME" => time(),
		);

		if ($NS['INTERVAL'] <= 0) {
			$NS['INTERVAL'] = 20;
		}
		$_SESSION['AMMINA_OPTIMIZER_CHECK_FILES_DATA'] = array(
			"STAT" => array(
				"FILES_SCAN" => 0,
				"ORIGINAL" => array(
					"TOTAL" => 0,
					"CSS" => 0,
					"JS" => 0,
					"IMAGE" => 0,
				),
				"OPTIMIZED" => array(
					"TOTAL" => 0,
					"CSS" => 0,
					"JS" => 0,
					"IMAGE" => 0,
				),
			),
			"NEXT_FILE" => "",
		);
	}

	$obUpdateFiles = new \Ammina\Optimizer\Workers\Cache\Files($NS, $start_time);
	if (!check_bitrix_sessid()) {
		$arErrors[] = Loc::getMessage("AMMINA_OPTIMIZER_ACCESS_DENIED");
	} else {
		$obUpdateFiles->doUpdateProcess($_SESSION['AMMINA_OPTIMIZER_CHECK_FILES_DATA']);
		$NS = $obUpdateFiles->getNSData();
		$_SESSION['AMMINA_OPTIMIZER_CHECK_FILES_DATA'] = $obUpdateFiles->getUpdateData();
		$arErrors = array_merge($arErrors, $obUpdateFiles->getErrors());
	}
	?>
	<script>
		CloseWaitWindow();
	</script>
	<?

	foreach ($arErrors as $strError)
		CAdminMessage::ShowMessage($strError);
	foreach ($arMessages as $strMessage)
		CAdminMessage::ShowMessage(array("MESSAGE" => $strMessage, "TYPE" => "OK"));
	if (count($arErrors) == 0) {
		$strStat = Loc::getMessage("AMMINA_OPTIMIZER_FILES_UPDATE_DETAIL", array(
			"#FILES_SCAN#" => $_SESSION['AMMINA_OPTIMIZER_CHECK_FILES_DATA']['STAT']['FILES_SCAN'],
			"#ORIGINAL_TOTAL#" => $_SESSION['AMMINA_OPTIMIZER_CHECK_FILES_DATA']['STAT']['ORIGINAL']['TOTAL'],
			"#ORIGINAL_CSS#" => $_SESSION['AMMINA_OPTIMIZER_CHECK_FILES_DATA']['STAT']['ORIGINAL']['CSS'],
			"#ORIGINAL_JS#" => $_SESSION['AMMINA_OPTIMIZER_CHECK_FILES_DATA']['STAT']['ORIGINAL']['JS'],
			"#ORIGINAL_IMAGE#" => $_SESSION['AMMINA_OPTIMIZER_CHECK_FILES_DATA']['STAT']['ORIGINAL']['IMAGE'],
			"#OPTIMIZED_TOTAL#" => $_SESSION['AMMINA_OPTIMIZER_CHECK_FILES_DATA']['STAT']['OPTIMIZED']['TOTAL'],
			"#OPTIMIZED_CSS#" => $_SESSION['AMMINA_OPTIMIZER_CHECK_FILES_DATA']['STAT']['OPTIMIZED']['CSS'],
			"#OPTIMIZED_JS#" => $_SESSION['AMMINA_OPTIMIZER_CHECK_FILES_DATA']['STAT']['OPTIMIZED']['JS'],
			"#OPTIMIZED_IMAGE#" => $_SESSION['AMMINA_OPTIMIZER_CHECK_FILES_DATA']['STAT']['OPTIMIZED']['IMAGE'],
		));
		$strStatComplete = Loc::getMessage("AMMINA_OPTIMIZER_FILES_UPDATE_DETAIL_COMPLETE", array(
			"#FILES_SCAN#" => $_SESSION['AMMINA_OPTIMIZER_CHECK_FILES_DATA']['STAT']['FILES_SCAN'],
			"#ORIGINAL_TOTAL#" => $_SESSION['AMMINA_OPTIMIZER_CHECK_FILES_DATA']['STAT']['ORIGINAL']['TOTAL'],
			"#ORIGINAL_CSS#" => $_SESSION['AMMINA_OPTIMIZER_CHECK_FILES_DATA']['STAT']['ORIGINAL']['CSS'],
			"#ORIGINAL_JS#" => $_SESSION['AMMINA_OPTIMIZER_CHECK_FILES_DATA']['STAT']['ORIGINAL']['JS'],
			"#ORIGINAL_IMAGE#" => $_SESSION['AMMINA_OPTIMIZER_CHECK_FILES_DATA']['STAT']['ORIGINAL']['IMAGE'],
			"#OPTIMIZED_TOTAL#" => $_SESSION['AMMINA_OPTIMIZER_CHECK_FILES_DATA']['STAT']['OPTIMIZED']['TOTAL'],
			"#OPTIMIZED_CSS#" => $_SESSION['AMMINA_OPTIMIZER_CHECK_FILES_DATA']['STAT']['OPTIMIZED']['CSS'],
			"#OPTIMIZED_JS#" => $_SESSION['AMMINA_OPTIMIZER_CHECK_FILES_DATA']['STAT']['OPTIMIZED']['JS'],
			"#OPTIMIZED_IMAGE#" => $_SESSION['AMMINA_OPTIMIZER_CHECK_FILES_DATA']['STAT']['OPTIMIZED']['IMAGE'],
		));
		$progressItems = array();
		if ($NS['STEP'] < 5) {
			if ($NS['STEP'] < 2) {
				if ($NS['STEP'] == 1) {
					$progressItems[] = $strStat;
				}
				if (amopt_strlen($_SESSION['AMMINA_OPTIMIZER_CHECK_FILES_DATA']['NEXT_FILE']) > 0) {
					$progressItems[] = "<p>" . Loc::getMessage("AMMINA_OPTIMIZER_CHECK_FILES_STATUS_STEP_NEXT_FILE", array("#FILE#" => $_SESSION['AMMINA_OPTIMIZER_CHECK_FILES_DATA']['NEXT_FILE'])) . "</p>";
				}
				CAdminMessage::ShowMessage(array(
					"MESSAGE" => Loc::getMessage("AMMINA_OPTIMIZER_FILES_UPDATE_PROCESS"),
					"DETAILS" => implode("", $progressItems),
					"HTML" => true,
					"TYPE" => "PROGRESS",
				));
			} elseif ($NS['STEP'] == 2) {
				CAdminMessage::ShowMessage(array(
					"MESSAGE" => Loc::getMessage("AMMINA_OPTIMIZER_FILES_UPDATE_PROCESS"),
					"DETAILS" => Loc::getMessage("AMMINA_OPTIMIZER_FILES_UPDATE_PROCESS_STEP2", array("#CNT_PACKAGES#" => $_SESSION['AMMINA_OPTIMIZER_CHECK_FILES_DATA']['STEP2_CNT_PACKAGES'])),
					"HTML" => true,
					"TYPE" => "PROGRESS",
				));
			} elseif ($NS['STEP'] == 3) {
				CAdminMessage::ShowMessage(array(
					"MESSAGE" => Loc::getMessage("AMMINA_OPTIMIZER_FILES_UPDATE_PROCESS"),
					"DETAILS" => Loc::getMessage("AMMINA_OPTIMIZER_FILES_UPDATE_PROCESS_STEP3", array("#CNT_PACKAGES#" => $_SESSION['AMMINA_OPTIMIZER_CHECK_FILES_DATA']['STEP3_CNT_PACKAGES'])),
					"HTML" => true,
					"TYPE" => "PROGRESS",
				));
			} elseif ($NS['STEP'] == 4) {
				CAdminMessage::ShowMessage(array(
					"MESSAGE" => Loc::getMessage("AMMINA_OPTIMIZER_FILES_UPDATE_PROCESS"),
					"DETAILS" => Loc::getMessage("AMMINA_OPTIMIZER_FILES_UPDATE_PROCESS_STEP4"),
					"HTML" => true,
					"TYPE" => "PROGRESS",
				));
			}

			if ($NS["STEP"] >= 0) {
				?>
				<script type="text/javascript">
					DoUpdateNext(<?=CUtil::PhpToJSObject(array("NS" => $NS)) ?>);
				</script>
				<?
			}
		} else {
			CAdminMessage::ShowMessage(array(
				"MESSAGE" => Loc::getMessage("AMMINA_OPTIMIZER_FILES_UPDATE_COMPLETE"),
				"DETAILS" => $strStatComplete,
				"HTML" => true,
				"TYPE" => "PROGRESS",
			));
			?>
			<script type="text/javascript">
				EndUpdate();
			</script>
			<?
		}
	} else {
		?>
		<script type="text/javascript">
			EndUpdate();
		</script>
		<?
	}

	require($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/include/epilog_admin_js.php");
}
$sTableID = "tbl_ammina_optimizer_stat";

$oSort = new CAdminSorting($sTableID, "FILE_DATE", "desc");
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
		"id" => "TYPE",
		"name" => Loc::getMessage("AMMINA_OPTIMIZER_FILTER_TYPE"),
		"filterable" => "",
		"type" => "list",
		"params" => array("multiple" => "Y"),
		"items" => array(
			"CSS" => GetMessage("AMMINA_OPTIMIZER_FILTER_TYPE_CSS"),
			"JS" => GetMessage("AMMINA_OPTIMIZER_FILTER_TYPE_JS"),
			"IMAGE" => GetMessage("AMMINA_OPTIMIZER_FILTER_TYPE_IMAGE"),
		),
		"default" => true,
	),
	array(
		"id" => "FILE_NAME",
		"name" => Loc::getMessage("AMMINA_OPTIMIZER_FILTER_FILE_NAME"),
		"filterable" => "?",
		"quickSearch" => "?",
		"default" => false,
	),
	array(
		"id" => "FILE_EXTENSION",
		"name" => Loc::getMessage("AMMINA_OPTIMIZER_FILTER_FILE_EXTENSION"),
		"filterable" => "?",
		"quickSearch" => "?",
		"default" => false,
	),
	array(
		"id" => "FILE_DATE",
		"name" => Loc::getMessage("AMMINA_OPTIMIZER_FILTER_FILE_DATE"),
		"type" => "date",
		"filterable" => "",
		"default" => false,
	),
	array(
		"id" => "CNT_OPTIMIZED",
		"name" => Loc::getMessage("AMMINA_OPTIMIZER_FILTER_CNT_OPTIMIZED"),
		"filterable" => "",
		"type" => "number",
		"default" => false,
	),
	array(
		"id" => "OPTIMIZED.FILE_NAME",
		"name" => Loc::getMessage("AMMINA_OPTIMIZER_FILTER_OPTIMIZED_FILE_NAME"),
		"filterable" => "?",
		"quickSearch" => "?",
		"default" => false,
	),
	array(
		"id" => "OPTIMIZED.FILE_DATE",
		"name" => Loc::getMessage("AMMINA_OPTIMIZER_FILTER_OPTIMIZED_FILE_DATE"),
		"type" => "date",
		"filterable" => "",
		"default" => false,
	),
);

$arFilter = array();
$lAdmin->AddFilter($filterFields, $arFilter);

$arHeader = array(
	array(
		"id" => "ID",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_ID"),
		"sort" => "ID",
		"default" => true,
	),
	array(
		"id" => "TYPE",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_TYPE"),
		"sort" => "TYPE",
		"default" => true,
	),
	array(
		"id" => "FILE_NAME",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_FILE_NAME"),
		"sort" => "FILE_NAME",
		"default" => true,
	),
	array(
		"id" => "FILE_EXTENSION",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_FILE_EXTENSION"),
		"sort" => "FILE_EXTENSION",
		"default" => true,
	),
	array(
		"id" => "FILE_DATE",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_FILE_DATE"),
		"sort" => "FILE_DATE",
		"default" => true,
	),
	array(
		"id" => "FILE_SIZE",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_FILE_SIZE"),
		"sort" => "DATE_CHECK",
		"default" => true,
	),
	array(
		"id" => "CNT_OPTIMIZED",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_CNT_OPTIMIZED"),
		"sort" => "CNT_OPTIMIZED",
		"default" => true,
	),
	array(
		"id" => "DETAIL_INFO",
		"content" => Loc::getMessage("AMMINA_OPTIMIZER_FIELD_DETAIL_INFO"),
		"sort" => "",
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
	$totalCount = \Ammina\Optimizer\FilesOriginalsTable::getCount($getListParams['filter']);
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

$rsItems = \Ammina\Optimizer\FilesOriginalsTable::getList($getListParams);
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
	$row =& $lAdmin->AddRow($arData['ID'], $arData, '/bitrix/admin/ammina.optimizer.stat.files.view.php?ID=' . $arData['ID'] . '&lang=' . LANGUAGE_ID, Loc::getMessage("AMMINA_OPTIMIZER_RECORD_VIEW"));
	$row->AddViewField("ID", '<a href="/bitrix/admin/ammina.optimizer.stat.files.view.php?ID=' . $arData['ID'] . '&lang=' . LANGUAGE_ID . '">' . $arData['ID'] . '</a>');
	$strFileName = '<a href="' . $arData['FILE_NAME'] . '">' . $arData['FILE_NAME'] . '</a>';
	if ($arData['TYPE'] == "IMAGE") {
		$strFileName = '<a href="' . $arData['FILE_NAME'] . '" data-fancybox="gallery-' . $arData['ID'] . '" data-caption="' . $arData['FILE_NAME'] . ' (' . $arData['FILE_SIZE'] . ')">' . $arData['FILE_NAME'] . '<br/><img src="' . $arData['FILE_NAME'] . '" style="max-width:200px; max-height:200px;" /></a>';
	}
	$row->AddViewField("FILE_NAME", $strFileName);

	if (in_array("DETAIL_INFO", $lAdmin->arVisibleColumns)) {
		$rOptimized = \Ammina\Optimizer\FilesOptimizedTable::getList(array(
			"order" => array("ID" => "ASC"),
			"filter" => array("ORIGINAL_ID" => $arData['ID']),
		));
		$arOptimizedList = array();
		while ($arOptimized = $rOptimized->Fetch()) {
			$percent = 100 - round(($arOptimized['FILE_SIZE'] / $arData['FILE_SIZE']) * 100, 1);
			$strOptimizedFileName = '<a href="' . $arOptimized['FILE_NAME'] . '">' . $arOptimized['FILE_NAME'] . '</a>' . " (" . $arOptimized['FILE_SIZE'] . ", " . Loc::getMessage("AMMINA_OPTIMIZER_FILES_ECONOMY") . " " . $percent . "%)";
			if ($arData['TYPE'] == "IMAGE") {
				$strOptimizedFileName = '<a href="' . $arOptimized['FILE_NAME'] . '" data-fancybox="gallery-' . $arData['ID'] . '" data-caption="' . $arOptimized['FILE_NAME'] . ' (' . $arOptimized['FILE_SIZE'] . ', ' . Loc::getMessage("AMMINA_OPTIMIZER_FILES_ECONOMY") . " " . $percent . '%)">' . $arOptimized['FILE_NAME'] . ' (' . $arOptimized['FILE_SIZE'] . ', ' . Loc::getMessage("AMMINA_OPTIMIZER_FILES_ECONOMY") . " " . $percent . '%)<br/><img src="' . $arOptimized['FILE_NAME'] . '" style="max-width:200px; max-height:200px;" /></a>';
			}
			$arOptimizedList[] = $strOptimizedFileName;
		}
		$row->AddViewField("DETAIL_INFO", implode("<hr/>", $arOptimizedList));
	}
	$row->AddViewField("TYPE", Loc::getMessage("AMMINA_OPTIMIZER_FIELD_TYPE_" . amopt_strtoupper($arData['TYPE'])));

	$arActions = array();
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
			"TEXT" => Loc::getMessage("AMMINA_OPTIMIZER_UPDATE_START"),
			"ICON" => "btn_update_start",
			"ONCLICK" => "this.id='btn_update_start';StartUpdate();",
			"TITLE" => Loc::getMessage("AMMINA_OPTIMIZER_UPDATE_START_TITLE"),
		),
		array(
			"TEXT" => Loc::getMessage("AMMINA_OPTIMIZER_UPDATE_STOP"),
			"ICON" => "btn_update_stop",
			"ONCLICK" => "EndUpdate();",
			"TITLE" => Loc::getMessage("AMMINA_OPTIMIZER_UPDATE_STOP_TITLE"),
		),
	);

	$lAdmin->AddAdminContextMenu($aContext);

	$lAdmin->AddGroupActionTable(Array());
}

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(Loc::getMessage("AMMINA_OPTIMIZER_PAGE_TITLE"));

CUtil::InitJSCore();
CJSCore::Init(array("jquery3"));
\Bitrix\Main\Page\Asset::getInstance()->addString('<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />');
\Bitrix\Main\Page\Asset::getInstance()->addString('<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>');


require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

?>
	<div id="tbl_ammina_optimizer_result_div"></div>
	<script type="text/javascript">
		var running = false;
		var oldNS = '';

		function DoUpdateNext(NS) {
			var queryString = 'UpdateFiles=Y'
				+ '&lang=<?echo LANG?>'
				+ '&<?echo bitrix_sessid_get()?>';

			if (running) {
				ShowWaitWindow();
				BX.ajax.post(
					'ammina.optimizer.stat.files.php?' + queryString,
					NS,
					function (result) {
						document.getElementById('tbl_ammina_optimizer_result_div').innerHTML = result;
					}
				);
			}
		}

		function StartUpdate() {
			running = document.getElementById('btn_update_start').disabled = true;
			DoUpdateNext();
		}

		function EndUpdate() {
			running = document.getElementById('btn_update_start').disabled = false;
		}
	</script>
<?
$lAdmin->DisplayFilter($filterFields);

if (CAmminaOptimizer::getTestPeriodInfo() == \Bitrix\Main\Loader::MODULE_DEMO) {
	CAdminMessage::ShowMessage(array("MESSAGE" => Loc::getMessage("AMMINA_OPTIMIZER_SYS_MODULE_IS_DEMO"), "HTML" => true));
} elseif (CAmminaOptimizer::getTestPeriodInfo() == \Bitrix\Main\Loader::MODULE_DEMO_EXPIRED) {
	CAdminMessage::ShowMessage(array("MESSAGE" => Loc::getMessage("AMMINA_OPTIMIZER_SYS_MODULE_IS_DEMO_EXPIRED"), "HTML" => true));
}

$lAdmin->DisplayList();
CAmminaOptimizer::showSupportForm();
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
