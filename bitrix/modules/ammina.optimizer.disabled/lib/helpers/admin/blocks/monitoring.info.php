<?php

namespace Ammina\Optimizer\Helpers\Admin\Blocks;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class MonitoringInfo
{

	public static function getEdit($arItem)
	{
		global $APPLICATION;
		$result = '<table border="0" cellspacing="0" cellpadding="0" width="100%" class="adm-detail-content-table edit-table"><tbody>';
		if (isset($arItem['error'])) {
			$oErr = new \CAdminMessage($arItem['error']['message']);
			$result .= '
			<tr>
				<td>
				' . $oErr->Show() . '
				</td>
			</tr>';
		} else {
			$result .= '
			<tr>
				<td class="adm-detail-content-cell-l" width="40%">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_LHR_requestedUrl") . ':</td>
				<td class="adm-detail-content-cell-r">' . $arItem['lighthouseResult']['requestedUrl'] . '</td>
			</tr>
			<tr>
				<td class="adm-detail-content-cell-l" width="40%">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_LHR_finalUrl") . ':</td>
				<td class="adm-detail-content-cell-r">' . $arItem['lighthouseResult']['finalUrl'] . '</td>
			</tr>
			<tr>
				<td class="adm-detail-content-cell-l" width="40%">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_LHR_lighthouseVersion") . ':</td>
				<td class="adm-detail-content-cell-r">' . $arItem['lighthouseResult']['lighthouseVersion'] . '</td>
			</tr>
			<tr>
				<td class="adm-detail-content-cell-l" width="40%">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_LHR_userAgent") . ':</td>
				<td class="adm-detail-content-cell-r">' . $arItem['lighthouseResult']['userAgent'] . '</td>
			</tr>
			';
		}
		$result .= '</tbody></table>';

		return $result;
	}
}