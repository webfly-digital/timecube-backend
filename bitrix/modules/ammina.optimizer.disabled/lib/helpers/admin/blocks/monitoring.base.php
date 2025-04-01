<?php

namespace Ammina\Optimizer\Helpers\Admin\Blocks;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class MonitoringBase
{

	public static function getEdit($arItem)
	{
		$result = '';
		return $result;
	}

	public static function getMonitoringInfo($arMonitoringCategories, $arMonitoring)
	{
		$arResult = array(
			"groups" => array(
				"nogroup" => array(),
			),
			"audits" => array(),
			"finalUrl" => $arMonitoring['lighthouseResult']['finalUrl'],
		);
		foreach ($arMonitoringCategories['auditRefs'] as $k => $arAudit) {
			$arAudit['INFO'] = $arMonitoring['lighthouseResult']['audits'][$arAudit['id']];
			if (amopt_strlen($arAudit['group']) <= 0) {
				$arAudit['group'] = 'nogroup';
			}
			$arResult['groups'][$arAudit['group']] = $arMonitoring['lighthouseResult']['categoryGroups'][$arAudit['group']];
			$arResult['audits'][$arAudit['group']][$arAudit['id']] = $arAudit;
		}
		return $arResult;
	}

	public static function getHtmlMonitoring($arMonitoringInfo)
	{
		$strResult = '';
		foreach ($arMonitoringInfo['groups'] as $kGroup => $arGroup) {
			$strResult .= self::getHtmlMonitoringGroup($arMonitoringInfo, $kGroup);
		}
		return $strResult;
	}

	public static function getHtmlMonitoringGroup($arMonitoringInfo, $strGroup)
	{
		$arGroupInfo = $arMonitoringInfo['groups'][$strGroup];
		$strResult = "";
		if ((isset($arGroupInfo['title']) && amopt_strlen($arGroupInfo['title']) > 0) || (isset($arGroupInfo['description']) && amopt_strlen($arGroupInfo['description']) > 0)) {
			$strResult .= '<div class="amopt-groupheader-block">';
			if (isset($arGroupInfo['title']) && amopt_strlen($arGroupInfo['title']) > 0) {
				$strResult .= '<h2 class="bx-block-title amoptm-group-title">' . $arGroupInfo['title'] . '</h2>';
			}
			if (isset($arGroupInfo['description']) && amopt_strlen($arGroupInfo['description']) > 0) {
				$strResult .= '<p class="amoptm-group-description">' . $arGroupInfo['description'] . '</p>';
			}
			$strResult .= '</div>';
		}
		$strResult .= '<table border="0" cellspacing="0" cellpadding="0" width="100%" class="adm-detail-content-table edit-table"><tbody>';
		foreach ($arMonitoringInfo['audits'][$strGroup] as $kAudit => $arAudit) {
			$strResult .= self::getHtmlMonitoringAudit($arMonitoringInfo, $strGroup, $kAudit);
		}
		$strResult .= '</tbody></table>';
		return $strResult;
	}

	public static function getHtmlMonitoringAudit($arMonitoringInfo, $strGroup, $strAudit)
	{
		$arAuditInfo = $arMonitoringInfo['audits'][$strGroup][$strAudit];
		$strResult = "";
		if (!isset($arAuditInfo['INFO']['details'])) {
			$strResult .= self::getHtmlMonitoringAuditType1($arAuditInfo);
		} elseif (isset($arAuditInfo['INFO']['details']['type'])) {
			if ($arAuditInfo['INFO']['details']['type'] == "screenshot") {
				$strResult .= self::getHtmlMonitoringAuditTypeScreenshot($arAuditInfo);
			} elseif ($arAuditInfo['INFO']['details']['type'] == "filmstrip") {
				$strResult .= self::getHtmlMonitoringAuditTypeFilmStrip($arAuditInfo);
			} elseif ($arAuditInfo['INFO']['details']['type'] == "opportunity") {
				if (empty($arAuditInfo['INFO']['details']['items'])) {
					$strResult .= self::getHtmlMonitoringAuditType1($arAuditInfo);
				} else {
					$strResult .= self::getHtmlMonitoringAuditTypeOpportunity($arAuditInfo);
				}
			} elseif ($arAuditInfo['INFO']['details']['type'] == "table") {
				if (empty($arAuditInfo['INFO']['details']['items'])) {
					$strResult .= self::getHtmlMonitoringAuditType1($arAuditInfo);
				} else {
					$strResult .= self::getHtmlMonitoringAuditTypeTable($arAuditInfo);
				}
			} elseif ($arAuditInfo['INFO']['details']['type'] == "criticalrequestchain") {
				if (empty($arAuditInfo['INFO']['details']['chains'])) {
					$strResult .= self::getHtmlMonitoringAuditType1($arAuditInfo);
				} else {
					$strResult .= self::getHtmlMonitoringAuditTypeCriticalRequestChain($arAuditInfo, $arMonitoringInfo['finalUrl'], $arMonitoringInfo['audits']['nogroup']['network-requests']);
				}
			} else {
				/*
				ob_start();
				echo '<tr><td colspan="2"><pre>';
				print_r($arAuditInfo);
				echo '</pre></td></tr>';
				$cont = ob_get_contents();
				ob_end_clean();
				$strResult .= $cont;*/
			}

		} else {
			/*
			ob_start();
			echo "<tr><td>";
			echo "</td></tr>";
			$cont = ob_get_contents();
			ob_end_clean();
			$strResult .= $cont;*/
		}
		return $strResult;
	}

	public static function getHtmlMonitoringAuditType1($arAudit)
	{
		$strResult = "";
		$strExtClass = '';
		if (in_array($arAudit['INFO']['scoreDisplayMode'], array("binary", "numeric"))) {
			$strExtClass = ' amoptm-auditrow-';
			if ($arAudit['INFO']['score'] >= 0.9) {
				$strExtClass .= 'green';
			} elseif ($arAudit['INFO']['score'] >= 0.5) {
				$strExtClass .= 'orange';
			} else {
				$strExtClass .= 'red';
			}
		} elseif ($arAudit['INFO']['scoreDisplayMode'] == "notApplicable") {
			$strExtClass = ' amoptm-auditrow-green';
		}
		$strResult .= '<tr class="amoptm-auditrow' . $strExtClass . '">';
		if (amopt_strlen($arAudit['INFO']['displayValue']) > 0 || amopt_strlen($arAudit['INFO']['explanation']) > 0 || !empty($arAudit['INFO']['warnings'])) {
			$strResult .= '<td class="adm-detail-content-cell-l" width="40%">';
			if (amopt_strlen($arAudit['INFO']['title']) > 0) {
				$strResult .= '<p class="amoptm-title">' . self::getTranslateText($arAudit['INFO']['title']) . '</p>';
			}
			if (amopt_strlen($arAudit['INFO']['description']) > 0) {
				$strResult .= '<p class="amoptm-description">' . self::getTranslateText($arAudit['INFO']['description']) . '</p>';
			}
			$strResult .= '</td><td class="adm-detail-content-cell-r">';
			if (amopt_strlen($arAudit['INFO']['displayValue']) > 0) {
				$strResult .= '<p class="amoptm-display-value">' . self::getTranslateText($arAudit['INFO']['displayValue']) . '</p>';
			}
			if (isset($arAudit['INFO']['details'])) {
				if ($arAudit['INFO']['details']['overallSavingsMs'] > 0) {
					$strResult .= '<p class="amoptm-audit-overallsaving">' . Loc::getMessage("AMMINA_OPTIMIZER_MONITORING_UNIT_OVERALL_SAVING") . ": " . ($arAudit['INFO']['details']['overallSavingsMs'] > 100 ? round($arAudit['INFO']['details']['overallSavingsMs'] / 1000, 2) . " " . Loc::getMessage("AMMINA_OPTIMIZER_MONITORING_UNIT_SEC") : $arAudit['INFO']['details']['overallSavingsMs'] . " " . Loc::getMessage("AMMINA_OPTIMIZER_MONITORING_UNIT_MSEC")) . '</p>';
				}
				if ($arAudit['INFO']['details']['overallSavingsBytes'] > 0) {
					$strResult .= '<p class="amoptm-audit-overallsaving">' . Loc::getMessage("AMMINA_OPTIMIZER_MONITORING_UNIT_OVERALL_SAVING") . ": " . ($arAudit['INFO']['details']['overallSavingsBytes'] > 1024 ? round($arAudit['INFO']['details']['overallSavingsBytes'] / 1024, 2) . " " . Loc::getMessage("AMMINA_OPTIMIZER_MONITORING_UNIT_KB") : $arAudit['INFO']['details']['overallSavingsBytes'] . " " . Loc::getMessage("AMMINA_OPTIMIZER_MONITORING_UNIT_BYTES")) . '</p>';
				}
			}
			if (amopt_strlen($arAudit['INFO']['explanation']) > 0) {
				$strResult .= '<p class="amoptm-explanation">' . self::getTranslateText($arAudit['INFO']['explanation']) . '</p>';
			}
			if (!empty($arAudit['INFO']['warnings'])) {
				foreach ($arAudit['INFO']['warnings'] as $strWarning) {
					$strResult .= '<p class="amoptm-warnings">' . $strWarning . '</p>';
				}
			}
			$strResult .= '</td>';
		} else {
			$strResult .= '<td class="adm-detail-content-cell" colspan="2">';
			if (amopt_strlen($arAudit['INFO']['title']) > 0) {
				$strResult .= '<p class="amoptm-title">' . self::getTranslateText($arAudit['INFO']['title']) . '</p>';
			}
			if (amopt_strlen($arAudit['INFO']['description']) > 0) {
				$strResult .= '<p class="amoptm-description">' . self::getTranslateText($arAudit['INFO']['description']) . '</p>';
			}
			$strResult .= '</td>';
		}
		$strResult .= '</tr>';
		return $strResult;
	}

	public static function getHtmlMonitoringAuditTypeScreenshot($arAudit)
	{
		$strResult = "";
		$strResult .= '<tr class="amoptm-auditrow"><td class="adm-detail-content-cell-l" width="40%">';
		if (amopt_strlen($arAudit['INFO']['title']) > 0) {
			$strResult .= '<p class="amoptm-title">' . self::getTranslateText($arAudit['INFO']['title']) . '</p>';
		}
		if (amopt_strlen($arAudit['INFO']['description']) > 0) {
			$strResult .= '<p class="amoptm-description">' . self::getTranslateText($arAudit['INFO']['description']) . '</p>';
		}
		$strResult .= '</td><td class="adm-detail-content-cell-r"><img src="' . $arAudit['INFO']['details']['data'] . '" /></td></tr>';
		return $strResult;
	}

	public static function getHtmlMonitoringAuditTypeFilmStrip($arAudit)
	{
		$strResult = "";
		$strResult .= '<tr class="amoptm-auditrow amoptm-auditrow-nb"><td class="adm-detail-content-cell" colspan="2">';
		if (amopt_strlen($arAudit['INFO']['title']) > 0) {
			$strResult .= '<p class="amoptm-title">' . self::getTranslateText($arAudit['INFO']['title']) . '</p>';
		}
		if (amopt_strlen($arAudit['INFO']['description']) > 0) {
			$strResult .= '<p class="amoptm-description">' . self::getTranslateText($arAudit['INFO']['description']) . '</p>';
		}
		$strResult .= '</td></tr>';
		$strResult .= '<tr class="amoptm-auditrow"><td colspan="2" class="adm-detail-content-cell"><table align="center" width="100%"><tr>';
		foreach ($arAudit['INFO']['details']['items'] as $arItem) {
			$strResult .= '<td><img src="data:image/jpeg;base64,' . $arItem['data'] . '" /></td>';
		}
		$strResult .= '</tr><tr>';
		foreach ($arAudit['INFO']['details']['items'] as $arItem) {
			$strResult .= '<td>' . (amopt_strlen($arItem['timing']) > 0 ? $arItem['timing'] . " ms" : "") . '</td>';
		}
		$strResult .= '</tr></table></td></tr>';
		return $strResult;
	}

	public static function getHtmlMonitoringAuditTypeOpportunity($arAudit)
	{
		$strResult = "";
		$strExtClass = '';
		if (in_array($arAudit['INFO']['scoreDisplayMode'], array("binary", "numeric"))) {
			$strExtClass = ' amoptm-auditrow-';
			if ($arAudit['INFO']['score'] >= 0.9) {
				$strExtClass .= 'green';
			} elseif ($arAudit['INFO']['score'] >= 0.5) {
				$strExtClass .= 'orange';
			} else {
				$strExtClass .= 'red';
			}
		} elseif ($arAudit['INFO']['scoreDisplayMode'] == "notApplicable") {
			$strExtClass = ' amoptm-auditrow-green';
		}
		$strResult .= '<tr class="amoptm-auditrow' . $strExtClass . ' amoptm-auditrow-nb">';
		if (amopt_strlen($arAudit['INFO']['displayValue']) > 0 || amopt_strlen($arAudit['INFO']['explanation']) > 0 || !empty($arAudit['INFO']['warnings']) || $arAudit['INFO']['details']['overallSavingsMs'] > 0 || $arAudit['INFO']['details']['overallSavingsBytes'] > 0) {
			$strResult .= '<td class="adm-detail-content-cell-l" width="40%">';
			if (amopt_strlen($arAudit['INFO']['title']) > 0) {
				$strResult .= '<p class="amoptm-title">' . self::getTranslateText($arAudit['INFO']['title']) . '</p>';
			}
			if (amopt_strlen($arAudit['INFO']['description']) > 0) {
				$strResult .= '<p class="amoptm-description">' . self::getTranslateText($arAudit['INFO']['description']) . '</p>';
			}
			$strResult .= '</td><td class="adm-detail-content-cell-r">';
			if (amopt_strlen($arAudit['INFO']['displayValue']) > 0) {
				$strResult .= '<p class="amoptm-display-value">' . self::getTranslateText($arAudit['INFO']['displayValue']) . '</p>';
			}
			if ($arAudit['INFO']['details']['overallSavingsMs'] > 0) {
				$strResult .= '<p class="amoptm-audit-overallsaving">' . Loc::getMessage("AMMINA_OPTIMIZER_MONITORING_UNIT_OVERALL_SAVING") . ": " . ($arAudit['INFO']['details']['overallSavingsMs'] > 100 ? round($arAudit['INFO']['details']['overallSavingsMs'] / 1000, 2) . " " . Loc::getMessage("AMMINA_OPTIMIZER_MONITORING_UNIT_SEC") : $arAudit['INFO']['details']['overallSavingsMs'] . " " . Loc::getMessage("AMMINA_OPTIMIZER_MONITORING_UNIT_MSEC")) . '</p>';
			}
			if ($arAudit['INFO']['details']['overallSavingsBytes'] > 0) {
				$strResult .= '<p class="amoptm-audit-overallsaving">' . Loc::getMessage("AMMINA_OPTIMIZER_MONITORING_UNIT_OVERALL_SAVING") . ": " . ($arAudit['INFO']['details']['overallSavingsBytes'] > 1024 ? round($arAudit['INFO']['details']['overallSavingsBytes'] / 1024, 2) . " " . Loc::getMessage("AMMINA_OPTIMIZER_MONITORING_UNIT_KB") : $arAudit['INFO']['details']['overallSavingsBytes'] . " " . Loc::getMessage("AMMINA_OPTIMIZER_MONITORING_UNIT_BYTES")) . '</p>';
			}
			if (amopt_strlen($arAudit['INFO']['explanation']) > 0) {
				$strResult .= '<p class="amoptm-explanation">' . self::getTranslateText($arAudit['INFO']['explanation']) . '</p>';
			}
			if (!empty($arAudit['INFO']['warnings'])) {
				foreach ($arAudit['INFO']['warnings'] as $strWarning) {
					$strResult .= '<p class="amoptm-warnings">' . $strWarning . '</p>';
				}
			}
			$strResult .= '</td>';
		} else {
			$strResult .= '<td class="adm-detail-content-cell" colspan="2">';
			if (amopt_strlen($arAudit['INFO']['title']) > 0) {
				$strResult .= '<p class="amoptm-title">' . self::getTranslateText($arAudit['INFO']['title']) . '</p>';
			}
			if (amopt_strlen($arAudit['INFO']['description']) > 0) {
				$strResult .= '<p class="amoptm-description">' . self::getTranslateText($arAudit['INFO']['description']) . '</p>';
			}
			$strResult .= '</td>';
		}
		$strResult .= '</tr><tr class="amoptm-auditrow"><td class="adm-detail-content-cell" colspan="2"><table align="center" width="100%" class="amoptm-data-table">';
		$strResult .= self::getHtmlMonitoringAuditTypeTableDetail($arAudit);
		$strResult .= '</table>';
		return $strResult;
	}

	public static function getHtmlMonitoringAuditTypeTable($arAudit)
	{
		$strResult = "";
		$strExtClass = '';
		if (in_array($arAudit['INFO']['scoreDisplayMode'], array("binary", "numeric"))) {
			$strExtClass = ' amoptm-auditrow-';
			if ($arAudit['INFO']['score'] >= 0.9) {
				$strExtClass .= 'green';
			} elseif ($arAudit['INFO']['score'] >= 0.5) {
				$strExtClass .= 'orange';
			} else {
				$strExtClass .= 'red';
			}
		} elseif ($arAudit['INFO']['scoreDisplayMode'] == "notApplicable") {
			$strExtClass = ' amoptm-auditrow-green';
		}
		$strResult .= '<tr class="amoptm-auditrow' . $strExtClass . ' amoptm-auditrow-nb">';
		if (amopt_strlen($arAudit['INFO']['displayValue']) > 0 || amopt_strlen($arAudit['INFO']['explanation']) > 0 || !empty($arAudit['INFO']['warnings'])) {
			$strResult .= '<td class="adm-detail-content-cell-l" width="40%">';
			if (amopt_strlen($arAudit['INFO']['title']) > 0) {
				$strResult .= '<p class="amoptm-title">' . self::getTranslateText($arAudit['INFO']['title']) . '</p>';
			}
			if (amopt_strlen($arAudit['INFO']['description']) > 0) {
				$strResult .= '<p class="amoptm-description">' . self::getTranslateText($arAudit['INFO']['description']) . '</p>';
			}
			$strResult .= '</td><td class="adm-detail-content-cell-r">';
			if (amopt_strlen($arAudit['INFO']['displayValue']) > 0) {
				$strResult .= '<p class="amoptm-display-value">' . self::getTranslateText($arAudit['INFO']['displayValue']) . '</p>';
			}
			if (amopt_strlen($arAudit['INFO']['explanation']) > 0) {
				$strResult .= '<p class="amoptm-explanation">' . self::getTranslateText($arAudit['INFO']['explanation']) . '</p>';
			}
			if (!empty($arAudit['INFO']['warnings'])) {
				foreach ($arAudit['INFO']['warnings'] as $strWarning) {
					$strResult .= '<p class="amoptm-warnings">' . $strWarning . '</p>';
				}
			}
			$strResult .= '</td>';
		} else {
			$strResult .= '<td class="adm-detail-content-cell" colspan="2">';
			if (amopt_strlen($arAudit['INFO']['title']) > 0) {
				$strResult .= '<p class="amoptm-title">' . self::getTranslateText($arAudit['INFO']['title']) . '</p>';
			}
			if (amopt_strlen($arAudit['INFO']['description']) > 0) {
				$strResult .= '<p class="amoptm-description">' . self::getTranslateText($arAudit['INFO']['description']) . '</p>';
			}
			$strResult .= '</td>';
		}
		$strResult .= '</tr><tr class="amoptm-auditrow"><td class="adm-detail-content-cell" colspan="2"><table align="center" width="100%" class="amoptm-data-table">';
		$strResult .= self::getHtmlMonitoringAuditTypeTableDetail($arAudit);
		$strResult .= '</table>';

		return $strResult;
	}

	public static function getHtmlMonitoringAuditTypeTableDetail($arAudit)
	{
		$strResult = "";
		$strResult .= '<thead><tr class="heading">';
		$arHeaderByKey = array();
		foreach ($arAudit['INFO']['details']['headings'] as $arItem) {
			if (isset($arItem['itemType'])) {
				$arItem['type'] = $arItem['itemType'];
			} elseif (isset($arItem['valueType'])) {
				$arItem['type'] = $arItem['valueType'];
			}
			$arHeaderByKey[$arItem['key']] = $arItem;
		}
		foreach ($arHeaderByKey as $arItem) {
			$strResult .= '<td>' . htmlspecialcharsbx(amopt_strlen($arItem['text']) > 0 ? $arItem['text'] : $arItem['label']) . '</td>';
		}
		$strResult .= '</tr></thead><tbody>';
		foreach ($arAudit['INFO']['details']['items'] as $arItem) {
			$strResult .= '<tr>';
			foreach ($arHeaderByKey as $kHeader => $arHeader) {
				$strResult .= '<td>';
				if ($arHeader['type'] == 'url') {
					$strResult .= self::getHtmlMonitoringAuditTypeTableCellTypeUrl($arItem[$kHeader], $arHeader);
				} elseif ($arHeader['type'] == 'link') {
					$strResult .= self::getHtmlMonitoringAuditTypeTableCellTypeUrl($arItem[$kHeader], $arHeader);
				} elseif ($arHeader['type'] == 'ms' || $arHeader['valueType'] == 'timespanMs') {
					$strResult .= self::getHtmlMonitoringAuditTypeTableCellTypeMs($arItem[$kHeader], $arHeader);
				} elseif ($arHeader['type'] == 'bytes') {
					$strResult .= self::getHtmlMonitoringAuditTypeTableCellTypeBytes($arItem[$kHeader], $arHeader);
				} elseif ($arHeader['type'] == 'text') {
					$strResult .= self::getHtmlMonitoringAuditTypeTableCellTypeText($arItem[$kHeader], $arHeader);
				} elseif ($arHeader['type'] == 'numeric') {
					$strResult .= self::getHtmlMonitoringAuditTypeTableCellTypeNumeric($arItem[$kHeader], $arHeader);
				} elseif ($arHeader['type'] == 'node') {
					$strResult .= self::getHtmlMonitoringAuditTypeTableCellTypeNode($arItem[$kHeader], $arHeader);
				} elseif ($arHeader['type'] == 'code') {
					$strResult .= self::getHtmlMonitoringAuditTypeTableCellTypeCode($arItem[$kHeader], $arHeader);
				} else {
					$strResult .= "Unknown type: " . $arHeader['valueType'];
				}
				$strResult .= '</td>';
			}
			$strResult .= '</tr>';
		}
		$strResult .= '</body">';
		return $strResult;
	}

	public static function getHtmlMonitoringAuditTypeTableCellTypeUrl($strValue, $arHeader = array())
	{
		$strResult = "";
		$strText = $strValue;
		if (amopt_strlen($strText) > 40) {
			$strText = amopt_substr($strText, 0, 20) . "..." . amopt_substr($strText, amopt_strlen($strText) - 20);
		}
		$strResult .= '<a href="' . $strValue . '" title="' . htmlspecialcharsbx($strValue) . '">' . htmlspecialcharsbx($strText) . '</a>';
		return $strResult;
	}

	public static function getHtmlMonitoringAuditTypeTableCellTypeMs($strValue, $arHeader = array())
	{
		$strResult = "";
		$strResult .= htmlspecialcharsbx($strValue);
		return $strResult;
	}

	public static function getHtmlMonitoringAuditTypeTableCellTypeBytes($strValue, $arHeader = array())
	{
		$strResult = "";
		$strResult .= htmlspecialcharsbx($strValue);
		return $strResult;
	}

	public static function getHtmlMonitoringAuditTypeTableCellTypeText($strValue, $arHeader = array())
	{
		$strResult = "";
		$strResult .= htmlspecialcharsbx($strValue);
		return $strResult;
	}

	public static function getHtmlMonitoringAuditTypeTableCellTypeNumeric($strValue, $arHeader = array())
	{
		$strResult = "";
		$strResult .= htmlspecialcharsbx($strValue);
		return $strResult;
	}

	public static function getHtmlMonitoringAuditTypeTableCellTypeNode($arValue, $arHeader = array())
	{
		$strResult = "";
		if (amopt_strlen($arValue['selector']) > 0) {
			$strResult .= '<p class="amoptm-node-selector"><strong>' . Loc::getMessage("AMMINA_OPTIMIZER_MONITORING_NODE_SELECTOR") . ":</strong> " . htmlspecialcharsbx($arValue['selector']) . '</p>';
		}
		if (amopt_strlen($arValue['path']) > 0) {
			$strResult .= '<p class="amoptm-node-path"><strong>' . Loc::getMessage("AMMINA_OPTIMIZER_MONITORING_NODE_PATH") . ":</strong> " . htmlspecialcharsbx($arValue['path']) . '</p>';
		}
		if (amopt_strlen($arValue['snippet']) > 0) {
			$strResult .= '<p class="amoptm-node-snippet"><strong>' . Loc::getMessage("AMMINA_OPTIMIZER_MONITORING_NODE_SNIPPET") . ":</strong> " . nl2br(htmlspecialcharsbx($arValue['snippet'])) . '</p>';
		}
		if (amopt_strlen($arValue['explanation']) > 0) {
			$strResult .= '<p class="amoptm-node-explanation"><strong>' . Loc::getMessage("AMMINA_OPTIMIZER_MONITORING_NODE_EXPLANATION") . ":</strong> " . nl2br(htmlspecialcharsbx($arValue['explanation'])) . '</p>';
		}
		return $strResult;
	}

	public static function getHtmlMonitoringAuditTypeTableCellTypeCode($arValue, $arHeader = array())
	{
		$strResult = "";
		$strResult .= htmlspecialcharsbx($arValue['value']);
		return $strResult;
	}

	public static function getHtmlMonitoringAuditTypeCriticalRequestChain($arAudit, $strFinalUrl, $arAuditNetworkRequests)
	{
		$strResult = "";
		$strExtClass = '';
		if (in_array($arAudit['INFO']['scoreDisplayMode'], array("binary", "numeric"))) {
			$strExtClass = ' amoptm-auditrow-';
			if ($arAudit['INFO']['score'] >= 0.9) {
				$strExtClass .= 'green';
			} elseif ($arAudit['INFO']['score'] >= 0.5) {
				$strExtClass .= 'orange';
			} else {
				$strExtClass .= 'red';
			}
		} elseif ($arAudit['INFO']['scoreDisplayMode'] == "notApplicable") {
			$strExtClass = ' amoptm-auditrow-green';
		}
		$strResult .= '<tr class="amoptm-auditrow' . $strExtClass . ' amoptm-auditrow-nb">';
		$strResult .= '<td class="adm-detail-content-cell-l" width="40%">';
		if (amopt_strlen($arAudit['INFO']['title']) > 0) {
			$strResult .= '<p class="amoptm-title">' . self::getTranslateText($arAudit['INFO']['title']) . '</p>';
		}
		if (amopt_strlen($arAudit['INFO']['description']) > 0) {
			$strResult .= '<p class="amoptm-description">' . self::getTranslateText($arAudit['INFO']['description']) . '</p>';
		}
		$strResult .= '</td><td class="adm-detail-content-cell-r">';
		if (amopt_strlen($arAudit['INFO']['displayValue']) > 0) {
			$strResult .= '<p class="amoptm-display-value">' . self::getTranslateText($arAudit['INFO']['displayValue']) . '</p>';
		}
		if (amopt_strlen($arAudit['INFO']['explanation']) > 0) {
			$strResult .= '<p class="amoptm-explanation">' . self::getTranslateText($arAudit['INFO']['explanation']) . '</p>';
		}
		if (!empty($arAudit['INFO']['warnings'])) {
			foreach ($arAudit['INFO']['warnings'] as $strWarning) {
				$strResult .= '<p class="amoptm-warnings">' . $strWarning . '</p>';
			}
		}
		$strResult .= '<p class="amoptm-auditcriticalpathmax">' . Loc::getMessage("AMMINA_OPTIMIZER_MONITORING_MAX_CRITICAL_PATH") . ': <strong>' . round($arAudit['INFO']['details']['longestChain']['duration'], 0) . " " . Loc::getMessage("AMMINA_OPTIMIZER_MONITORING_UNIT_MSEC") . '</strong></p>';
		$strResult .= '</td></tr><tr class="amoptm-auditrow"><td class="adm-detail-content-cell" colspan="2"><table align="left" width="100%" class="amoptm-data-table"><tbody>';
		$arChainUrl = array();
		foreach ($arAudit['INFO']['details']['chains'] as $arChain) {
			$strResult .= self::getHtmlMonitoringAuditTypeCriticalRequestChainChild($arChain, $arChainUrl, false);
		}
		$strResult .= '</tbody></table>';
		$arMainUrl = parse_url($strFinalUrl);
		foreach ($arChainUrl as $k => $strUrl) {
			$arUrl = parse_url($strUrl);
			if ($arMainUrl['host'] == $arUrl['host']) {
				$arChainUrl[$k] = $arUrl['path'];
				if (amopt_strlen($arUrl['query']) > 0) {
					$arChainUrl[$k] .= '?' . $arUrl['query'];
				}
			}
		}
		$arFullChainUrl = array();
		foreach ($arAuditNetworkRequests['INFO']['details']['items'] as $arItem) {
			if (isset($arChainUrl[amopt_strtolower($arItem['url'])])) {
				$arFullChainUrl[] = array(
					"url" => $arChainUrl[amopt_strtolower($arItem['url'])],
					"mime" => $arItem['mimeType'],
					"type" => amopt_strtolower($arItem['resourceType']),
				);
				unset($arChainUrl[amopt_strtolower($arItem['url'])]);
			}
		}
		$arPreload = array();
		$arPreloadOther = array();
		foreach ($arFullChainUrl as $arUrl) {
			if (in_array($arUrl['type'], array("script"))) {
				$arPreload[] = '<' . $arUrl['url'] . '>; rel=preload; as=script';
			} elseif (in_array($arUrl['type'], array("stylesheet"))) {
				$arPreload[] = '<' . $arUrl['url'] . '>; rel=preload; as=style';
			} elseif (in_array($arUrl['type'], array("font"))) {
				$arPreload[] = '<' . $arUrl['url'] . '>; rel=preload; as=font; crossorigin';
			} else {
				$arPreloadOther[] = $arUrl['url'];
			}
		}
		foreach ($arChainUrl as $val) {
			$arPreloadOther[] = $val;
		}
		if (!empty($arPreload)) {
			$strResult .= '<p class="amoptm-auditcriticalpath-recomended">' . Loc::getMessage("AMMINA_OPTIMIZER_MONITORING_CRITICAL_PATH_RECOMENDED");
			$strResult .= '<textarea class="amoptm-auditcriticalpath-recomended-text">' . htmlspecialcharsbx(implode("\n", $arPreload)) . '</textarea></p>';
		}
		if (!empty($arPreloadOther)) {
			$strResult .= '<p class="amoptm-auditcriticalpath-recomended">' . Loc::getMessage("AMMINA_OPTIMIZER_MONITORING_CRITICAL_PATH_RECOMENDED_OTHER") . '</p>';
			$strResult .= '<textarea class="amoptm-auditcriticalpath-recomended-text">' . htmlspecialcharsbx(implode("\n", $arPreloadOther)) . '</textarea>';
		}
		return $strResult;
	}

	public static function getHtmlMonitoringAuditTypeCriticalRequestChainChild($arChain, &$arChainUrl, $bAddToChainArray)
	{
		$strResult = "";
		if ($bAddToChainArray) {
			$arChainUrl[amopt_strtolower($arChain['request']['url'])] = $arChain['request']['url'];
		}
		$strResult .= '<tr><td>' . self::getHtmlMonitoringAuditTypeTableCellTypeUrl($arChain['request']['url']) . ', <strong>' . round(($arChain['request']['endTime'] - $arChain['request']['startTime']) * 1000, 0) . ' ' . Loc::getMessage("AMMINA_OPTIMIZER_MONITORING_UNIT_MSEC") . ', ' . round($arChain['request']['transferSize'] / 1024, 2) . ' ' . Loc::getMessage("AMMINA_OPTIMIZER_MONITORING_UNIT_KB") . '</strong></td></tr>';
		if (isset($arChain['children']) && is_array($arChain['children'])) {
			$strResult .= '<tr><td><table align="left" width="100%" class="amoptm-data-table"><tbody>';
			foreach ($arChain['children'] as $arChain) {
				$strResult .= self::getHtmlMonitoringAuditTypeCriticalRequestChainChild($arChain, $arChainUrl, true);
			}
			$strResult .= '</tbody></table></td></tr>';
		}
		return $strResult;
	}

	public static function getTranslateText($strValue)
	{

		return htmlspecialcharsbx($strValue);
	}
}