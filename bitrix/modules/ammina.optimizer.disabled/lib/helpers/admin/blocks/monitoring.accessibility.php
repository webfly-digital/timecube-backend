<?php

namespace Ammina\Optimizer\Helpers\Admin\Blocks;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class MonitoringAccessibility extends MonitoringBase
{

	public static function getEdit($arItem)
	{
		global $APPLICATION;
		$result = '';
		if (!isset($arItem['error'])) {
			$arMonitoringInfo = self::getMonitoringInfo($arItem['lighthouseResult']['categories']['accessibility'], $arItem);
			$result = self::getHtmlMonitoring($arMonitoringInfo);
		}
		return $result;
	}
}