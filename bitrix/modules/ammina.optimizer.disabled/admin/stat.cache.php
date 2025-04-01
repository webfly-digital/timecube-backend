<?

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
Bitrix\Main\Loader::includeModule('ammina.optimizer');
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/ammina.optimizer/prolog.php");

Loc::loadMessages(__FILE__);

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

if (!isset($INTERVAL))
	$INTERVAL = 30;
else
	$INTERVAL = intval($INTERVAL);

@set_time_limit(0);

$start_time = time();

$arErrors = array();
$arMessages = array();

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_REQUEST["UpdateStat"] == "Y") {
	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_js.php");

	if (array_key_exists("NS", $_POST) && is_array($_POST["NS"])) {
		$NS = $_POST["NS"];
	} else {
		$NS = array(
			"INTERVAL" => $_REQUEST['INTERVAL'],
			"START_TIME" => time(),
		);
		$_SESSION['AMMINA_OPTIMIZER_CHECK_CACHE_DATA'] = array(
			"STAT" => array(
				"TOTAL" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_CSS" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_CSS_ATOM" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_CSS_ATOM_CSS" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_CSS_ATOM_INFO" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_CSS_ATOM_RULES" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_CSS_ATOM_WAIT" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_CSS_ATOM_RESULTURL" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_CSS_FULL" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_CSS_FULL_CSS" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_CSS_FULL_RULES" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_CSS_FULL_CRITICAL" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_CSS_FULL_CRITICAL_CSS" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_CSS_FULL_CRITICAL_FONTS" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_CSS_FULL_CRITICAL_NC" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_CSS_FULL_FONTS" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_CSS_FULL_FONTS_CSS" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_CSS_FULL_FONTS_NF" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_CSS_IMG" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_JS" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_JS_ATOM" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_JS_ATOM_JS" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_JS_ATOM_INFO" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_JS_ATOM_WAIT" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_JS_ATOM_RESULTURL" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_JS_FULL" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_JS_FULL_JS" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_JS_FULL_INFO" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_GIF" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_GIF_IMAGES" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_GIF_INFO" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_GIF_WAIT" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_GIF_RESULTURL" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),

				"TOTAL_IMAGES_GIFWEBP" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_GIFWEBP_IMAGES" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_GIFWEBP_INFO" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_GIFWEBP_WAIT" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_GIFWEBP_RESULTURL" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),

				"TOTAL_IMAGES_JPEGWEBP" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_JPEGWEBP_IMAGES" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_JPEGWEBP_INFO" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_JPEGWEBP_WAIT" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_JPEGWEBP_RESULTURL" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),

				"TOTAL_IMAGES_JPG" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_JPG_IMAGES" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_JPG_INFO" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_JPG_WAIT" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_JPG_RESULTURL" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),

				"TOTAL_IMAGES_JPGWEBP" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_JPGWEBP_IMAGES" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_JPGWEBP_INFO" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_JPGWEBP_WAIT" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_JPGWEBP_RESULTURL" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),

				"TOTAL_IMAGES_PNG" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_PNG_IMAGES" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_PNG_INFO" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_PNG_WAIT" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_PNG_RESULTURL" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),

				"TOTAL_IMAGES_PNGWEBP" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_PNGWEBP_IMAGES" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_PNGWEBP_INFO" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_PNGWEBP_WAIT" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_PNGWEBP_RESULTURL" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),

				"TOTAL_IMAGES_PNGWEBP" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_PNGWEBP_IMAGES" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_PNGWEBP_INFO" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_PNGWEBP_WAIT" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_PNGWEBP_RESULTURL" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),

				"TOTAL_IMAGES_SVG" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_SVG_IMAGES" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_SVG_INFO" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_SVG_WAIT" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
				"TOTAL_IMAGES_SVG_RESULTURL" => array(
					"COUNT" => 0,
					"SIZE" => 0,
				),
			),
			"NEXT_FILE" => "",
		);
	}

	$obUpdateStat = new \Ammina\Optimizer\Workers\Cache\Stat($NS, $start_time);
	if (!check_bitrix_sessid()) {
		$arErrors[] = Loc::getMessage("AMMINA_OPTIMIZER_ACCESS_DENIED");
	} else {
		$obUpdateStat->doUpdateProcess($_SESSION['AMMINA_OPTIMIZER_CHECK_CACHE_DATA']);
		$NS = $obUpdateStat->getNSData();
		$_SESSION['AMMINA_OPTIMIZER_CHECK_CACHE_DATA'] = $obUpdateStat->getUpdateData();
		$arErrors = array_merge($arErrors, $obUpdateStat->getErrors());
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
		if ($NS['STEP'] < 13) {

			$progressItems = array(
				Loc::getMessage("AMMINA_OPTIMIZER_CHECK_STAT_STATUS_STEP_0"),
			);
			$progressTotal = 0;
			$progressValue = 0;
			if ($NS['STEP'] < 12) {
				$progressItems = array(
					Loc::getMessage("AMMINA_OPTIMIZER_CHECK_STAT_STATUS_STEP" . intval($NS['STEP'])),
				);
				if (amopt_strlen($_SESSION['AMMINA_OPTIMIZER_CHECK_CACHE_DATA']['NEXT_FILE']) > 0) {
					$progressItems[] = Loc::getMessage("AMMINA_OPTIMIZER_CHECK_STAT_STATUS_STEP_NEXT_FILE", array("#FILE#" => $_SESSION['AMMINA_OPTIMIZER_CHECK_CACHE_DATA']['NEXT_FILE']));
				}
			} elseif ($NS['STEP'] == 12) {
				$progressItems = array(
					Loc::getMessage("AMMINA_OPTIMIZER_CHECK_STAT_STATUS_STEP" . intval($NS['STEP'])),
				);
			}
			CAdminMessage::ShowMessage(array(
				"MESSAGE" => Loc::getMessage("AMMINA_OPTIMIZER_STAT_UPDATE_PROCESS"),
				"DETAILS" => "<p>" . implode("</p><p>", $progressItems) . "</p>",
				"HTML" => true,
				"TYPE" => "PROGRESS",
			));

			if ($NS["STEP"] >= 0) {
				?>
				<script type="text/javascript">
					DoStatNext(<?=CUtil::PhpToJSObject(array("NS" => $NS)) ?>);
				</script>
				<?
			}
		} else {
			CAdminMessage::ShowMessage(array(
				"MESSAGE" => Loc::getMessage("AMMINA_OPTIMIZER_STAT_UPDATE_COMPLETE"),
				"DETAILS" => "<p>" . Loc::getMessage("AMMINA_OPTIMIZER_STAT_UPDATE_COMPLETE_DETAIL") . "</p>",
				"HTML" => true,
				"TYPE" => "PROGRESS",
			));
			?>
			<script type="text/javascript">
				EndStat();
			</script>
			<?
		}
	} else {
		?>
		<script type="text/javascript">
			EndStat();
		</script>
		<?
	}

	require($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/include/epilog_admin_js.php");
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && $_REQUEST["Clear"] == "Y") {
	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_js.php");

	if (array_key_exists("NS", $_POST) && is_array($_POST["NS"])) {
		$NS = $_POST["NS"];
	} else {
		$NS = array(
			"INTERVAL" => $_REQUEST['INTERVAL'],
			"CLEAR_CSS" => $_REQUEST['CLEAR_CSS'],
			"CLEAR_JS" => $_REQUEST['CLEAR_JS'],
			"CLEAR_IMG" => $_REQUEST['CLEAR_IMG'],
			"START_TIME" => time(),
		);
		$arClearDirs = array();
		if ($NS['CLEAR_CSS'] == "Y") {
			$arClearDirs[] = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/css/";
			$arClearDirs[] = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/css.outline/";
			$arClearDirs[] = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/img/";
		}
		if ($NS['CLEAR_JS'] == "Y") {
			$arClearDirs[] = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/js/";
			$arClearDirs[] = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/js.remote/";
			$arClearDirs[] = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/js.outline/";
		}
		if ($NS['CLEAR_IMG'] == "Y") {
			$arClearDirs[] = $_SERVER['DOCUMENT_ROOT'] . "/upload/ammina.optimizer/";
		}
		$_SESSION['AMMINA_OPTIMIZER_CLEAR_CACHE_DATA'] = array(
			"CLEAR_DIRS" => $arClearDirs,
		);
	}
	$obClear = new \Ammina\Optimizer\Workers\Cache\Clear($NS, $start_time);
	if (!check_bitrix_sessid()) {
		$arErrors[] = Loc::getMessage("AMMINA_OPTIMIZER_ACCESS_DENIED");
	} else {
		$obClear->doClearProcess($_SESSION['AMMINA_OPTIMIZER_CLEAR_CACHE_DATA']);
		$NS = $obClear->getNSData();
		$_SESSION['AMMINA_OPTIMIZER_CLEAR_CACHE_DATA'] = $obClear->getClearData();
		$arErrors = array_merge($arErrors, $obClear->getErrors());
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
		if ($NS['STEP'] < 1) {
			CAdminMessage::ShowMessage(array(
				"MESSAGE" => Loc::getMessage("AMMINA_OPTIMIZER_STAT_CLEAR_PROCESS"),
				//"DETAILS" => "<p>" . implode("</p><p>", $progressItems) . "</p>",
				"HTML" => true,
				"TYPE" => "PROGRESS",
				//"PROGRESS_TOTAL" => $progressTotal,
				//"PROGRESS_VALUE" => $progressValue,
			));

			/*if ($NS["STEP"] >= 0) {
				?>
				<script type="text/javascript">
					DoClearNext(<?=CUtil::PhpToJSObject(array("NS" => $NS)) ?>);
				</script>
				<?
			}*/
		} else {
			CAdminMessage::ShowMessage(array(
				"MESSAGE" => Loc::getMessage("AMMINA_OPTIMIZER_STAT_CLEAR_COMPLETE"),
				//"DETAILS" => "<p>" . Loc::getMessage("AMMINA_OPTIMIZER_STAT_UPDATE_COMPLETE_DETAIL") . "</p>",
				"HTML" => true,
				"TYPE" => "PROGRESS",
			));
			?>
			<script type="text/javascript">
				EndClear();
			</script>
			<?
		}
	} else {
		?>
		<script type="text/javascript">
			EndClear();
		</script>
		<?
	}

	require($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/include/epilog_admin_js.php");
}

$arStatData = \Ammina\Optimizer\StatTypesTable::getList(array(
	"order" => array("ID" => "ASC"),
))->fetchAll();

$APPLICATION->SetTitle(Loc::getMessage("AMMINA_OPTIMIZER_PAGE_TITLE"));
//CUtil::InitJSCore();
//CJSCore::Init(array("jquery2"));
$APPLICATION->SetAdditionalCSS("/bitrix/themes/.default/ammina.optimizer/css/regular.css");
$APPLICATION->SetAdditionalCSS("/bitrix/themes/.default/ammina.optimizer/css/solid.css");
$APPLICATION->SetAdditionalCSS("/bitrix/themes/.default/ammina.optimizer/css/fontawesome.css");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

?>
	<div id="tbl_ammina_optimizer_result_div"></div>
<?
$aTabs = array(
	array(
		"DIV" => "edit1",
		"TAB" => GetMessage("AMMINA_OPTIMIZER_STAT_TAB"),
		"ICON" => "ammina_optimizer_tab_stat",
		"TITLE" => GetMessage("AMMINA_OPTIMIZER_STAT_TAB_TITLE"),
	),
	array(
		"DIV" => "edit2",
		"TAB" => GetMessage("AMMINA_OPTIMIZER_STAT_UPDATE_TAB"),
		"ICON" => "ammina_optimizer_tab_stat",
		"TITLE" => GetMessage("AMMINA_OPTIMIZER_STAT_UPDATE_TAB_TITLE"),
	),
	array(
		"DIV" => "edit3",
		"TAB" => GetMessage("AMMINA_OPTIMIZER_STAT_CLEAR_TAB"),
		"ICON" => "ammina_optimizer_tab_stat",
		"TITLE" => GetMessage("AMMINA_OPTIMIZER_STAT_CLEAR_TAB_TITLE"),
	),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs, true, true);
?>
	<script type="text/javascript">
		var running = false;
		var oldNS = '';

		function DoStatNext(NS) {
			var interval = parseInt(document.getElementById('INTERVAL_stat').value);
			var queryString = 'UpdateStat=Y'
				+ '&lang=<?echo LANG?>'
				+ '&<?echo bitrix_sessid_get()?>'
				+ '&INTERVAL=' + interval;

			if (running) {
				ShowWaitWindow();
				BX.ajax.post(
					'ammina.optimizer.stat.cache.php?' + queryString,
					NS,
					function (result) {
						document.getElementById('tbl_ammina_optimizer_result_div').innerHTML = result;
					}
				);
			}
		}

		function DoClearNext(NS) {
			var interval = parseInt(document.getElementById('INTERVAL_clear').value);
			var clearCss = (document.getElementById('CLEAR_CSS').checked ? "Y" : "N");
			var clearJs = (document.getElementById('CLEAR_JS').checked ? "Y" : "N");
			var clearImg = (document.getElementById('CLEAR_IMG').checked ? "Y" : "N");
			var queryString = 'Clear=Y'
				+ '&lang=<?echo LANG?>'
				+ '&<?echo bitrix_sessid_get()?>'
				+ '&INTERVAL=' + interval
				+ '&CLEAR_CSS=' + clearCss
				+ '&CLEAR_JS=' + clearJs
				+ '&CLEAR_IMG=' + clearImg;

			if (running) {
				ShowWaitWindow();
				BX.ajax.post(
					'ammina.optimizer.stat.cache.php?' + queryString,
					NS,
					function (result) {
						document.getElementById('tbl_ammina_optimizer_result_div').innerHTML = result;
					}
				);
			}
		}

		function StartStat() {
			running = document.getElementById('start_stat_button').disabled = true;
			document.getElementById('start_clear_button').disabled = true;
			document.getElementById('stop_clear_button').disabled = true;
			DoStatNext();
		}

		function EndStat() {
			running = document.getElementById('start_stat_button').disabled = false;
			document.getElementById('start_clear_button').disabled = false;
			document.getElementById('stop_clear_button').disabled = false;
		}

		function StartClear() {
			running = document.getElementById('start_clear_button').disabled = true;
			document.getElementById('start_stat_button').disabled = true;
			document.getElementById('stop_stat_button').disabled = true;
			DoClearNext();
		}

		function EndClear() {
			running = document.getElementById('start_clear_button').disabled = false;
			document.getElementById('start_stat_button').disabled = false;
			document.getElementById('stop_stat_button').disabled = false;
		}
	</script>
<?

$tabControl->Begin();
$tabControl->BeginNextTab();

?>
	<tr>
		<td colspan="2">
			<table class="amopt-stattable">
				<thead>
				<tr>
					<th>&nbsp;</th>
					<th><?= Loc::getMessage("AMMINA_OPTIMIZER_STAT_TABLE_HEAD_COUNT") ?></th>
					<th><?= Loc::getMessage("AMMINA_OPTIMIZER_STAT_TABLE_HEAD_SIZE") ?></th>
				</tr>
				</thead>
				<tbody>
				<?
				foreach ($arStatData as $arRow) {
					$arRow['LEVEL'] = count(explode("_", $arRow['TYPE']));
					$strValue = $arRow['TOTAL_SIZE'];
					$strType = Loc::getMessage("AMMINA_OPTIMIZER_TYPE_TEXT_BYTES_BYTE");
					if ($strValue > 1073741824) {
						$strValue = intval(($strValue / 1073741824) * 10) / 10;
						$strType = Loc::getMessage("AMMINA_OPTIMIZER_TYPE_TEXT_BYTES_GBYTE");
					} elseif ($strValue > 1048576) {
						$strValue = intval(($strValue / 1048576) * 10) / 10;
						$strType = Loc::getMessage("AMMINA_OPTIMIZER_TYPE_TEXT_BYTES_MBYTE");
					} elseif ($strValue > 1024) {
						$strValue = intval(($strValue / 1024) * 10) / 10;
						$strType = Loc::getMessage("AMMINA_OPTIMIZER_TYPE_TEXT_BYTES_KBYTE");
					}
					?>
					<tr class="amopt-stattable__level<?= $arRow['LEVEL'] ?>">
						<td><?= str_repeat("&nbsp;.&nbsp;", $arRow['LEVEL'] - 1) ?><?= Loc::getMessage("AMMINA_OPTIMIZER_STAT_TABLE_TYPE_" . $arRow['TYPE']) ?></td>
						<td><?= $arRow['TOTAL_COUNT'] ?></td>
						<td><?= $strValue ?> <?= $strType ?></td>
					</tr>
					<?
				}
				?>
				</tbody>
			</table>
		</td>
	</tr>
<?
$tabControl->BeginNextTab();
?>
	<tr>
		<td width="50%"><? echo GetMessage("AMMINA_OPTIMIZER_STAT_UPDATE_INTERVAL") ?>:</td>
		<td width="50%">
			<input type="text" id="INTERVAL_stat" name="INTERVAL_stat" size="5" value="<?= intval($INTERVAL) ?>"/>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: center;">
			<input type="button" id="start_stat_button" value="<? echo GetMessage("AMMINA_OPTIMIZER_STAT_UPDATE_BUTTON") ?>" onclick="StartStat();" class="adm-btn-save">
			<input type="button" id="stop_stat_button" value="<? echo GetMessage("AMMINA_OPTIMIZER_STAT_UPDATE_BUTTON_STOP") ?>" onclick="EndStat();">
		</td>
	</tr>
<?
$tabControl->BeginNextTab();
?>
	<tr>
		<td width="50%"><? echo GetMessage("AMMINA_OPTIMIZER_STAT_CLEAR_INTERVAL") ?>:</td>
		<td width="50%">
			<input type="text" id="INTERVAL_clear" name="INTERVAL_clear" size="5" value="<?= intval($INTERVAL) ?>"/>
		</td>
	</tr>
	<tr>
		<td width="50%"><? echo GetMessage("AMMINA_OPTIMIZER_STAT_CLEAR_CSS") ?>:</td>
		<td width="50%">
			<input type="checkbox" id="CLEAR_CSS" name="CLEAR_CSS" value="Y" checked/>
		</td>
	</tr>
	<tr>
		<td width="50%"><? echo GetMessage("AMMINA_OPTIMIZER_STAT_CLEAR_JS") ?>:</td>
		<td width="50%">
			<input type="checkbox" id="CLEAR_JS" name="CLEAR_JS" value="Y"/>
		</td>
	</tr>
	<tr>
		<td width="50%"><? echo GetMessage("AMMINA_OPTIMIZER_STAT_CLEAR_IMG") ?>:</td>
		<td width="50%">
			<input type="checkbox" id="CLEAR_IMG" name="CLEAR_IMG" value="Y"/>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: center;">
			<input type="button" id="start_clear_button" value="<? echo GetMessage("AMMINA_OPTIMIZER_STAT_CLEAR_BUTTON") ?>" onclick="StartClear();" class="adm-btn-save">
			<input type="button" id="stop_clear_button" value="<? echo GetMessage("AMMINA_OPTIMIZER_STAT_CLEAR_BUTTON_STOP") ?>" onclick="EndClear();">
		</td>
	</tr>
<?
$tabControl->End();

CAmminaOptimizer::showSupportForm();
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");