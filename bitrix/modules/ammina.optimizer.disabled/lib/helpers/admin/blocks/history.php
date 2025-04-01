<?php

namespace Ammina\Optimizer\Helpers\Admin\Blocks;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class History
{

	public static function getEdit($arItem)
	{
		global $APPLICATION;
		$result = '
			<table border="0" cellspacing="0" cellpadding="0" width="100%" class="adm-detail-content-table edit-table">
				<tbody>
					' . ($arItem['ID'] > 0 ? '<tr>
						<td class="adm-detail-content-cell-l">ID:</td>
						<td class="adm-detail-content-cell-r">' . htmlspecialcharsbx($arItem['ID']) . '</td>
					</tr>' : '') . '
					<tr>
						<td class="adm-detail-content-cell-l" width="40%">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_PAGE_URL") . ':</td>
						<td class="adm-detail-content-cell-r">
						[<a href="/bitrix/admin/ammina.optimizer.page.edit.php?ID=' . $arItem['PAGE_ID'] . '">' . $arItem['PAGE_ID'] . '</a>] <a href="' . $arItem['PAGE_URL'] . '" target="_blank">' . $arItem['PAGE_URL'] . '</a>
						</td>
					</tr>
					<tr>
						<td class="adm-detail-content-cell-l">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_DATE_CHECK") . ':</td>
						<td class="adm-detail-content-cell-r">
							' . htmlspecialcharsbx($arItem['DATE_CHECK']) . '
						</td>
					</tr>
				</tbody>
			</table>';

		return $result;
	}
}