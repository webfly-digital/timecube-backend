<?php

namespace Ammina\Optimizer\Helpers\Admin\Blocks;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class StatFileOriginal
{

	public static function getView($arItem)
	{
		global $APPLICATION;

		$strFileContent = '<a href="' . $arItem['FILE_NAME'] . '">' . $arItem['FILE_NAME'] . '</a>';
		if ($arItem['TYPE'] == "IMAGE") {
			$strFileContent = '<a href="' . $arItem['FILE_NAME'] . '" data-fancybox="gallery-' . $arItem['ID'] . '" data-caption="' . $arItem['FILE_NAME'] . ' (' . $arItem['FILE_SIZE'] . ')">' . $arItem['FILE_NAME'] . '<br/><img src="' . $arItem['FILE_NAME'] . '" style="max-width:400px; max-height:400px;" /></a>';
		}
		$result = '
			<table border="0" cellspacing="0" cellpadding="0" width="100%" class="adm-detail-content-table edit-table">
				<tbody>
					<tr>
						<td class="adm-detail-content-cell-l" width="40%">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_ID") . ':</td>
						<td class="adm-detail-content-cell-r">
							' . htmlspecialcharsbx($arItem['ID']) . '
						</td>
					</tr>
					<tr>
						<td class="adm-detail-content-cell-l">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_FILE_TYPE") . ':</td>
						<td class="adm-detail-content-cell-r">
							' . htmlspecialcharsbx($arItem['TYPE']) . '
						</td>
					</tr>
					<tr>
						<td class="adm-detail-content-cell-l">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_FILE_NAME") . ':</td>
						<td class="adm-detail-content-cell-r">
							' . $strFileContent . '
						</td>
					</tr>
					<tr>
						<td class="adm-detail-content-cell-l">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_FILE_EXTENSION") . ':</td>
						<td class="adm-detail-content-cell-r">
							' . htmlspecialcharsbx($arItem['FILE_EXTENSION']) . '
						</td>
					</tr>
					<tr>
						<td class="adm-detail-content-cell-l">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_FILE_DATE") . ':</td>
						<td class="adm-detail-content-cell-r">
							' . htmlspecialcharsbx($arItem['FILE_DATE']) . '
						</td>
					</tr>
					<tr>
						<td class="adm-detail-content-cell-l">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_FILE_SIZE") . ':</td>
						<td class="adm-detail-content-cell-r">
							' . htmlspecialcharsbx($arItem['FILE_SIZE']) . '
						</td>
					</tr>
					<tr>
						<td class="adm-detail-content-cell-l">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_CNT_OPTIMIZED") . ':</td>
						<td class="adm-detail-content-cell-r">
							' . htmlspecialcharsbx($arItem['CNT_OPTIMIZED']) . '
						</td>
					</tr>
				</tbody>
			</table>';

		return $result;
	}
}