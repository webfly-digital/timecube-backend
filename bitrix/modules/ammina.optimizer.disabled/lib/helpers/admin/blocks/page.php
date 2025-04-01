<?php

namespace Ammina\Optimizer\Helpers\Admin\Blocks;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Page
{
	/*
		public static function getScripts()
		{
			global $APPLICATION;
			\CJSCore::Init(array("jquery2"));
			\Bitrix\Main\Page\Asset::getInstance()->addJs("/bitrix/js/ammina.ip/admin/content.js");
			$APPLICATION->SetAdditionalCSS("/bitrix/themes/.default/ammina.ip.css");
			return '
				<script type="text/javascript">
					$(document).ready(function(){
						$("#FIELD_COUNTRY").amminaIpAdminBlockContent();
						$("#FIELD_REGION").amminaIpAdminBlockContent();
						$("#FIELD_CITY").amminaIpAdminBlockContent();
					});
				</script>
			';
		}*/

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
							<input type="text" class="adm-bus-input" name="FIELDS[PAGE_URL]" maxlength="255" id="FIELD_PAGE_URL" value="' . htmlspecialcharsbx($arItem['PAGE_URL']) . '" />
						</td>
					</tr>
					<tr>
						<td class="adm-detail-content-cell-l">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_ACTIVE") . ':</td>
						<td class="adm-detail-content-cell-r">
							<input type="hidden" name="FIELDS[ACTIVE]" value="N"/>
							<input type="checkbox" class="adm-bus-input" name="FIELDS[ACTIVE]" id="FIELD_ACTIVE" value="Y"' . ($arItem['ACTIVE'] == "Y" ? ' checked="checked"' : '') . ' />
						</td>
					</tr>
					' . ($arItem['ID'] > 0 ? '<tr>
						<td class="adm-detail-content-cell-l">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_STATUS") . ':</td>
						<td class="adm-detail-content-cell-r">
							' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_STATUS_" . $arItem['STATUS']) . '
						</td>
					</tr>
					<tr>
						<td class="adm-detail-content-cell-l">' . Loc::getMessage("AMMINA_OPTIMIZER_FIELD_DATE_CREATE") . ':</td>
						<td class="adm-detail-content-cell-r">
							' . htmlspecialcharsbx($arItem['DATE_CREATE']) . '
						</td>
					</tr>' : '') . '
				</tbody>
			</table>';

		return $result;
	}
}