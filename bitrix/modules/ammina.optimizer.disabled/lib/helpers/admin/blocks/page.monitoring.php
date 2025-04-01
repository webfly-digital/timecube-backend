<?php

namespace Ammina\Optimizer\Helpers\Admin\Blocks;

use Ammina\Optimizer\HistoryTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class PageMonitoring
{

	public static function getEdit($arItem)
	{
		global $APPLICATION;
		$arHistory = false;
		$strMonitoringResult = '';
		if ($arItem['ID'] <= 0) {
			return '';
		}
		$arHistory = HistoryTable::getList(array(
			"filter" => array(
				"PAGE_ID" => $arItem['ID'],
			),
		))->fetch();

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
			if ($arItem[$strField] >= 90) {
				$strValue .= '<span style="color:#178239;font-weight:bold;font-size:1.2em;">' . $arItem[$strField] . '</span>';
			} elseif ($arItem[$strField] >= 50) {
				$strValue .= '<span style="color:#e67700;;font-weight:bold;font-size:1.2em;">' . $arItem[$strField] . '</span>';
			} else {
				$strValue .= '<span style="color:#c7221f;font-weight:bold;font-size:1.2em;">' . $arItem[$strField] . '</span>';
			}
			if ($arItem['OLD_DATA'][$strField] > 0) {
				if ($arItem[$strField] > $arItem['OLD_DATA'][$strField]) {
					$strValue .= '&nbsp;&nbsp;&nbsp;<span style="color:#178239;font-weight:bold;font-size:0.9em;">&uarr; ' . (intval($arItem[$strField] - $arItem['OLD_DATA'][$strField])) . '</span>';
				} elseif ($arItem[$strField] < $arItem['OLD_DATA'][$strField]) {
					$strValue .= '&nbsp;&nbsp;&nbsp;<span style="color:#c7221f;font-weight:bold;font-size:0.9em;">&darr; ' . (intval($arItem[$strField] - $arItem['OLD_DATA'][$strField])) . '</span>';
				} else {
					$strValue .= '&nbsp;&nbsp;&nbsp;<span style="color:rgba(0,0,0,0.54);font-weight:normal;font-size:0.9em;">+ 0</span>';
				}
			}
			$strMonitoringResult .= '<tr>
						<td class="adm-detail-content-cell-l">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_" . $strField) . ':</td>
						<td class="adm-detail-content-cell-r">' . $strValue . '</td>
					</tr>';
		}

		$result = '
			<table border="0" cellspacing="0" cellpadding="0" width="100%" class="adm-detail-content-table edit-table">
				<tbody>
					<tr>
						<td class="adm-detail-content-cell-l" width="40%">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_DATE_CHECK") . ':</td>
						<td class="adm-detail-content-cell-r">
							' . htmlspecialcharsbx($arItem['DATE_CHECK']) . '
						</td>
					</tr>
					<tr>
						<td class="adm-detail-content-cell-l">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_OLD_HISTORY") . ':</td>
						<td class="adm-detail-content-cell-r">
							<a href="/bitrix/admin/ammina.optimizer.history.edit.php?ID=' . $arHistory['ID'] . '">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_OLD_HISTORY_GO") . '</a>
						</td>
					</tr>
					' . $strMonitoringResult . '
				</tbody>
			</table>';

		return $result;
	}
}