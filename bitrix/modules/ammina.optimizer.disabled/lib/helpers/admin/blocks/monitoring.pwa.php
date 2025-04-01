<?php

namespace Ammina\Optimizer\Helpers\Admin\Blocks;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class MonitoringPwa extends MonitoringBase
{

	public static function getEdit($arItem)
	{
		global $APPLICATION;
		$result = '';
		if (!isset($arItem['error'])) {
			$arMonitoringInfo=self::getMonitoringInfo($arItem['lighthouseResult']['categories']['pwa'], $arItem);
			$result = self::getHtmlMonitoring($arMonitoringInfo);
		}
		return $result;
	}
}