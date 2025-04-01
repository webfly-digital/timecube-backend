<?php

namespace Ammina\Optimizer\Helpers\Admin\Blocks;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class MonitoringSeo extends MonitoringBase
{

	public static function getEdit($arItem)
	{
		global $APPLICATION;
		$result = '';
		if (!isset($arItem['error'])) {
			$arMonitoringInfo = self::getMonitoringInfo($arItem['lighthouseResult']['categories']['seo'], $arItem);
			$result = self::getHtmlMonitoring($arMonitoringInfo);
		}
		return $result;
	}
}