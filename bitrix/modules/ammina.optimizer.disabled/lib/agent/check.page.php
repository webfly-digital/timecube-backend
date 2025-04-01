<?

namespace Ammina\Optimizer\Agent;

use Ammina\Optimizer\HistoryTable;
use Ammina\Optimizer\PageTable;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Web\HttpClient;

class CheckPage
{

	public static function doExecute($PAGE_ID, $bIsPeriod = false)
	{
		if (\CModule::IncludeModule("ammina.optimizer")) {
			$arPage = PageTable::getRowById($PAGE_ID);
			if ($arPage && $arPage['ACTIVE'] == "Y") {
				PageTable::update($arPage['ID'], array(
					"STATUS" => "P",
				));
				self::doRequestPage($arPage['PAGE_URL']);
				$strDesktopData = self::doRequestGooglePage($arPage['PAGE_URL'], "desktop");
				$strMobileData = self::doRequestGooglePage($arPage['PAGE_URL'], "mobile");
				if ($strDesktopData !== false && $strMobileData !== false) {
					$arDesktopData = json_decode($strDesktopData, true);
					$arMobileData = json_decode($strMobileData, true);
					HistoryTable::add(array(
						"PAGE_ID" => $arPage['ID'],
						"DATE_CHECK" => new DateTime(),
						"DESKTOP_DATA" => $arDesktopData,
						"MOBILE_DATA" => $arMobileData,
					));
					if (isset($arDesktopData['lighthouseResult']) && !empty($arDesktopData['lighthouseResult']) && isset($arDesktopData['lighthouseResult']) && !empty($arDesktopData['lighthouseResult'])) {
						$arOld = $arPage;
						unset($arOld['ID']);
						unset($arOld['PAGE_URL']);
						unset($arOld['ACTIVE']);
						unset($arOld['STATUS']);
						unset($arOld['DATE_CREATE']);
						unset($arOld['DATE_CHECK']);
						unset($arOld['OLD_DATA']);
						$arUpdate = array(
							"STATUS" => "C",
							"DATE_CHECK" => new DateTime(),
							"DESKTOP_PERFORMANCE" => intval($arDesktopData['lighthouseResult']['categories']['performance']['score'] * 100),
							"DESKTOP_ACCESSIBILITY" => intval($arDesktopData['lighthouseResult']['categories']['accessibility']['score'] * 100),
							"DESKTOP_BESTPRACTICES" => intval($arDesktopData['lighthouseResult']['categories']['best-practices']['score'] * 100),
							"DESKTOP_SEO" => intval($arDesktopData['lighthouseResult']['categories']['seo']['score'] * 100),
							"DESKTOP_PWA" => intval($arDesktopData['lighthouseResult']['categories']['pwa']['score'] * 100),
							"MOBILE_PERFORMANCE" => intval($arMobileData['lighthouseResult']['categories']['performance']['score'] * 100),
							"MOBILE_ACCESSIBILITY" => intval($arMobileData['lighthouseResult']['categories']['accessibility']['score'] * 100),
							"MOBILE_BESTPRACTICES" => intval($arMobileData['lighthouseResult']['categories']['best-practices']['score'] * 100),
							"MOBILE_SEO" => intval($arMobileData['lighthouseResult']['categories']['seo']['score'] * 100),
							"MOBILE_PWA" => intval($arMobileData['lighthouseResult']['categories']['pwa']['score'] * 100),
							"OLD_DATA" => $arOld,
						);
						PageTable::update($arPage['ID'], $arUpdate);
					} else {
						PageTable::update($arPage['ID'], array(
							"DATE_CHECK" => new DateTime(),
							"STATUS" => "E",
						));
					}
				} else {
					PageTable::update($arPage['ID'], array(
						"DATE_CHECK" => new DateTime(),
						"STATUS" => "E",
					));
				}
			}
		}
		if ($bIsPeriod) {
			return '\Ammina\Optimizer\Agent\CheckPage::doExecute(' . $PAGE_ID . ', true);';
		}
		return "";
	}

	protected static function doRequestGooglePage($strUrl, $strStrategy)
	{
		$bResult = true;
		$client = new HttpClient(array(
			'redirect' => true,
			'redirectMax' => 10,
			'disableSslVerification' => true,
			'socketTimeout' => 120,
			'streamTimeout' => 120,
		));
		$strResult = $client->get('https://www.googleapis.com/pagespeedonline/v5/runPagespeed?key=' . urlencode(\COption::GetOptionString("ammina.optimizer", "google_pagespeed_apikey", "")) . '&url=' . urlencode($strUrl) . '&category=accessibility&category=best-practices&category=performance&category=pwa&category=seo&locale=ru&strategy=' . urlencode($strStrategy));
		$status = intval($client->getStatus());
		if ($status != 200 && $status != 500) {
			return false;
		}
		return $strResult;
	}

	protected static function doRequestPage($strUrl)
	{
		$bResult = true;
		$client = new HttpClient(array(
			'redirect' => true,
			'redirectMax' => 10,
			'disableSslVerification' => true,
		));
		$strResult = $client->get($strUrl);
		$status = intval($client->getStatus());
		if ($status != 200) {
			return false;
		}
		return $strResult;
	}
}