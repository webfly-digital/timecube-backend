<?php

namespace Ammina\Optimizer\Helpers\Admin\Blocks;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class StatFileOptimized
{

	public static function getView($arItem)
	{
		global $APPLICATION;

		$rOptimized = \Ammina\Optimizer\FilesOptimizedTable::getList(array(
			"order" => array("ID" => "ASC"),
			"filter" => array("ORIGINAL_ID" => $arItem['ID']),
		));
		$result = '
			<table border="0" cellspacing="0" cellpadding="0" width="100%" class="adm-detail-content-table edit-table">
				<tbody>';
		$index = 1;
		while ($arOptimized = $rOptimized->Fetch()) {
			$percent = 100 - round(($arOptimized['FILE_SIZE'] / $arItem['FILE_SIZE']) * 100, 1);

			$strFileContent = '<a href="' . $arItem['FILE_NAME'] . '">' . $arItem['FILE_NAME'] . '</a>';
			if ($arItem['TYPE'] == "IMAGE") {
				$strFileContent = '<a href="' . $arItem['FILE_NAME'] . '" target="_blank">' . $arItem['FILE_NAME'] . '<br/><img src="' . $arItem['FILE_NAME'] . '" style="max-width:400px; max-height:400px;" /></a>';
			}
			$result .= '<tr class="heading"><td colspan="2">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_NUMBER_FILE") . $index . '</td></tr>';

			$strOptimizedFileName = '<a href="' . $arOptimized['FILE_NAME'] . '">' . $arOptimized['FILE_NAME'] . '</a>';// . " (" . $arOptimized['FILE_SIZE'] . ", " . Loc::getMessage("AMMINA_OPTIMIZER_FILES_ECONOMY") . " " . $percent . "%)";
			if ($arItem['TYPE'] == "IMAGE") {
				$strOptimizedFileName = '<a href="' . $arOptimized['FILE_NAME'] . '" data-fancybox="gallery-' . $arItem['ID'] . '" data-caption="' . $arOptimized['FILE_NAME'] . ' (' . $arOptimized['FILE_SIZE'] . ', ' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_FILE_ECONOMY") . " " . $percent . '%)">' . $arOptimized['FILE_NAME'] . '<br/><img src="' . $arOptimized['FILE_NAME'] . '" style="max-width:400px; max-height:400px;" /></a>';
			}
			$result .= '
					<tr>
						<td class="adm-detail-content-cell-l" width="40%">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_ID") . ':</td>
						<td class="adm-detail-content-cell-r">
							' . htmlspecialcharsbx($arOptimized['ID']) . ' 
						</td>
					</tr>
					<tr>
						<td class="adm-detail-content-cell-l">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_FILE_NAME") . ':</td>
						<td class="adm-detail-content-cell-r">
							' . $strOptimizedFileName . '
						</td>
					</tr>
					<tr>
						<td class="adm-detail-content-cell-l">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_FILE_SIZE") . ':</td>
						<td class="adm-detail-content-cell-r">
							' . htmlspecialcharsbx($arOptimized['FILE_SIZE']) . '
						</td>
					</tr>
					<tr>
						<td class="adm-detail-content-cell-l">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_FILE_ECONOMY") . ':</td>
						<td class="adm-detail-content-cell-r">
							' . htmlspecialcharsbx($percent) . '%
						</td>
					</tr>
					<tr>
						<td class="adm-detail-content-cell-l">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_FILE_DATE") . ':</td>
						<td class="adm-detail-content-cell-r">
							' . htmlspecialcharsbx($arOptimized['FILE_DATE']) . '
						</td>
					</tr>';
			$index++;
		}
		$result .= '</tbody></table>';
		return $result;
	}


}