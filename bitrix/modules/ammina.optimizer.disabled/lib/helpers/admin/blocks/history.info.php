<?php

namespace Ammina\Optimizer\Helpers\Admin\Blocks;

use Ammina\Optimizer\HistoryTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class HistoryInfo
{

	public static function getEdit($arItem)
	{
		global $APPLICATION;
		$arHistory = false;
		$strMonitoringResult = '';
		if ($arItem['ID'] <= 0) {
			return '';
		}

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
			$strMonitoringResult .= '<tr>
						<td class="adm-detail-content-cell-l" width="40%">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_" . $strField) . ':</td>
						<td class="adm-detail-content-cell-r">' . $strValue . '</td>
					</tr>';
		}

		$result = '
			<table border="0" cellspacing="0" cellpadding="0" width="100%" class="adm-detail-content-table edit-table">
				<tbody>
					' . $strMonitoringResult . '
				</tbody>
			</table>';

		return $result;
	}
}