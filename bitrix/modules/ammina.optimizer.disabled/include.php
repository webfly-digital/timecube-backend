<?

use Ammina\Optimizer\Core2\AppBackground;
use Bitrix\Main\Composite\Helper;
use Bitrix\Main\Localization\Loc;

IncludeModuleLangFile(__FILE__);
include_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/ammina.optimizer/mbfunc.php");

class CAmminaOptimizer
{
	static protected $MODULE_TEST_PERIOD = false;
	static protected $oCssObject = false;
	static protected $oJsObject = false;
	static protected $oImgObject = false;

	function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
	{
		return;
	}

	static function isTestPeriodEnd()
	{
		if (self::$MODULE_TEST_PERIOD === false) {
			self::$MODULE_TEST_PERIOD = \Bitrix\Main\Loader::includeSharewareModule("ammina.optimizer");
		}
		if (self::$MODULE_TEST_PERIOD == \Bitrix\Main\Loader::MODULE_DEMO_EXPIRED) {
			return true;
		}
		return false;
	}

	static function getTestPeriodInfo()
	{
		if (self::$MODULE_TEST_PERIOD === false) {
			self::$MODULE_TEST_PERIOD = \Bitrix\Main\Loader::includeSharewareModule("ammina.optimizer");
		}
		return self::$MODULE_TEST_PERIOD;
	}

	static public function isAllowPageOptimize()
	{
		global $APPLICATION;
		if (self::isTestPeriodEnd()) {
			return false;
		}
		if (isset($_SESSION['AMOPT_STOP']) && ($_SESSION['AMOPT_STOP'] === true)) {
			return false;
		}
		if (defined('AMMINA_OPTIMIZER_STOP') && AMMINA_OPTIMIZER_STOP === true) {
			return false;
		}
		if ((defined("BX_CRONTAB") && BX_CRONTAB === true) || (defined("CHK_EVENT") && CHK_EVENT === true)) {
			return false;
		}
		if (\CAmminaOptimizer::doMathPageToRules(
			array(
				"/bitrix/admin/",
				"/bitrix/services/",
				"/bitrix/activities/",
				"/bitrix/gadgets/",
				"/bitrix/panel/",
				"/bitrix/tools/",
				"/bitrix/wizards/",
				"/bitrix/components/bitrix/sender.",
				"/bitrix/components/bitrix/report.",
				"/bitrix/components/bitrix/rest.",
				"/bitrix/components/bitrix/b24connector.",
				"/bitrix/components/bitrix/bitrixcloud.",
				"/bitrix/components/bitrix/bitrixcloud.",
				"/bitrix/components/bitrix/ui.",
			),
			$APPLICATION->GetCurPage()
		)) {
			return false;
		}
		$arDisabledPages = explode("\n", COption::GetOptionString("ammina.optimizer", "disabled_pages", ""));
		foreach ($arDisabledPages as $k => $v) {
			$arDisabledPages[$k] = trim($v);
			if (amopt_strlen($arDisabledPages[$k]) <= 0) {
				unset($arDisabledPages[$k]);
			}
		}
		$arDisabledPages = array_values($arDisabledPages);
		if (count($arDisabledPages) > 0) {
			if (\CAmminaOptimizer::doMathPageToRules($arDisabledPages, $APPLICATION->GetCurPage())) {
				return false;
			}
		}
		if (COption::GetOptionString("ammina.optimizer", "disabled_edit", "Y") == "Y") {
			if ($_SESSION["SESS_INCLUDE_AREAS"] === true) {
				return false;
			}
		}
		return true;
	}

	static public function OnBeforeEndBufferContent()
	{
		if (!self::isAllowPageOptimize()) {
			return;
		}
		if (Helper::isCompositeEnabled()) {
			\Ammina\Optimizer\Core2\Application::getInstance()->doSetCompositeWebpFlag();
		}
	}

	static public function OnBeforeEndBufferContent2()
	{
		if (!self::isAllowPageOptimize()) {
			return;
		}
		\Ammina\Optimizer\Core2\Application::getInstance()->doRemoveCompositeWebpFlag();
	}

	static public function OnEndBufferContent(&$content)
	{
		global $APPLICATION;
		if (self::isTestPeriodEnd()) {
			return false;
		}
		if ($_SESSION['AMOPT_STOP'] === true) {
			return;
		}
		if (defined('AMMINA_OPTIMIZER_STOP') && AMMINA_OPTIMIZER_STOP === true) {
			return;
		}
		if (AppBackground::isInstance()) {
			AppBackground::getInstance()->doEndContent();
		}
		if ((defined("BX_CRONTAB") && BX_CRONTAB === true) || (defined("CHK_EVENT") && CHK_EVENT === true)) {
			return;
		}
		if (\CAmminaOptimizer::doMathPageToRules(
			array(
				"/bitrix/admin/",
				"/bitrix/services/",
				"/bitrix/activities/",
				"/bitrix/gadgets/",
				"/bitrix/panel/",
				"/bitrix/tools/",
				"/bitrix/wizards/",
				"/bitrix/components/bitrix/sender.",
				"/bitrix/components/bitrix/report.",
				"/bitrix/components/bitrix/rest.",
				"/bitrix/components/bitrix/b24connector.",
				"/bitrix/components/bitrix/bitrixcloud.",
				"/bitrix/components/bitrix/bitrixcloud.",
				"/bitrix/components/bitrix/ui.",
			),
			$APPLICATION->GetCurPage()
		)) {
			return;
		}
		$arDisabledPages = explode("\n", COption::GetOptionString("ammina.optimizer", "disabled_pages", ""));
		foreach ($arDisabledPages as $k => $v) {
			$arDisabledPages[$k] = trim($v);
			if (amopt_strlen($arDisabledPages[$k]) <= 0) {
				unset($arDisabledPages[$k]);
			}
		}
		$arDisabledPages = array_values($arDisabledPages);
		if (count($arDisabledPages) > 0) {
			if (\CAmminaOptimizer::doMathPageToRules($arDisabledPages, $APPLICATION->GetCurPage())) {
				return;
			}
		}
		if (COption::GetOptionString("ammina.optimizer", "disabled_edit", "Y") == "Y") {
			if ($_SESSION["SESS_INCLUDE_AREAS"] === true) {
				return;
			}
		}
		$AMMINA_OPTIMIZER_APP = \Ammina\Optimizer\Core2\Application::getInstance();
		//$AMMINA_OPTIMIZER_APP->doSetCompositeWebpFlag();
		$AMMINA_OPTIMIZER_APP->EndContent($content);
		return;
	}

	static public function OnEndContentComposite($strContent)
	{
		global $APPLICATION;
		if (!self::isAllowPageOptimize()) {
			return $strContent;
		}
		//\Ammina\Optimizer\Core2\Application::getInstance()->doSetCompositeWebpFlag();
		$strContent = \Ammina\Optimizer\Core2\Application::getInstance()->doOptimizeAutocomposite($strContent);
		return $strContent;
	}

	static public function OnPageStart()
	{
		global $APPLICATION;
		\Ammina\Optimizer\Core2\Application::getInstance()->doRemoveCompositeWebpFlag();
	}

	static public function doCheckAmminaAPIKey()
	{
		global $USER;
		if ($USER->IsAdmin()) {
			if (amopt_strlen(COption::GetOptionString("ammina.optimizer", "ammina_apikey", "")) <= 0) {
				$errorMessage = "";
				self::getAmminaApiKey($USER->GetFullName(), $USER->GetEmail(), "", $errorMessage);
			}

			$nextTimePing = COption::GetOptionInt("ammina.optimizer", "pt", time());
			if ($nextTimePing > (time() + 3600 * 24 * 14)) {
				$nextTimePing = time();
			}
			if ($nextTimePing <= time()) {
				include_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/classes/general/update_client.php");
				include_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/classes/general/update_client_partner.php");
				$arClient = CUpdateClient::GetUpdatesList($strError);
				$arSendData = array(
					"HTTP_HOST" => $_SERVER['HTTP_HOST'],
					"EMAIL" => $USER->GetEmail(),
					"MODULE" => "ammina.optimizer",
					"PERIOD" => self::getTestPeriodInfo(),
					"MAIN_HASH" => md5('BITRIX' . CUpdateClientPartner::GetLicenseKey() . 'LICENSE'),
					"EXT_HASH" => md5(CUpdateClientPartner::GetLicenseKey()),
					"LTYPE" => $arClient['CLIENT'][0]['@']['LICENSE'],
					"LFROM" => $arClient['CLIENT'][0]['@']['DATE_FROM'],
					"LTO" => $arClient['CLIENT'][0]['@']['DATE_TO'],
				);
				if (!defined("BX_UTF") || BX_UTF !== true) {
					$arSendData = $GLOBALS['APPLICATION']->ConvertCharsetArray($arSendData, (amopt_strlen(LANG_CHARSET) > 0 ? LANG_CHARSET : SITE_CHARSET), "UTF-8");
				}
				$oHttpClient = new \Bitrix\Main\Web\HttpClient(
					array(
						'redirect' => true,
						'redirectMax' => 10,
						'version' => '1.1',
						'disableSslVerification' => true,
						'waitResponse' => true,
						'socketTimeout' => 10,
						'streamTimeout' => 10,
						'charset' => "UTF-8",
					)
				);
				$response = $oHttpClient->post("https://www.fastbitrix.ru/rq/rq.php", $arSendData);
				$status = $oHttpClient->getStatus();
				if ($status == 200) {
					if (self::getTestPeriodInfo() == \Bitrix\Main\Loader::MODULE_DEMO) {
						COption::SetOptionInt("ammina.optimizer", "pt", time() + 3600 * 24 * 3);
					} else {
						COption::SetOptionInt("ammina.optimizer", "pt", time() + 3600 * 24 * 7);
					}
				} else {
					COption::SetOptionInt("ammina.optimizer", "pt", time() + 3600);
				}
			}
		}
	}

	static public function OnProlog()
	{
		global $APPLICATION;
		self::doCheckAmminaAPIKey();
		\Ammina\Optimizer\Core2\Application::getInstance()->OnPrologComposite();

		if (isset($GLOBALS['AOPT_SETAGENT_NEXT']) && is_array($GLOBALS['AOPT_SETAGENT_NEXT'])) {
			foreach ($GLOBALS['AOPT_SETAGENT_NEXT'] as $k => $v) {
				\CAgent::Update(
					$k,
					array(
						"NEXT_EXEC" => $v,
					)
				);
			}
		}
		if (amopt_strpos($APPLICATION->GetCurPage(), '/bitrix/admin/') === 0) {
			$APPLICATION->SetAdditionalCSS("/bitrix/themes/.default/ammina.optimizer.css");
		} else {
			header("x-ammina-module: optimizer" . (in_array(self::getTestPeriodInfo(), array(\Bitrix\Main\Loader::MODULE_DEMO, \Bitrix\Main\Loader::MODULE_DEMO_EXPIRED)) ? ", demo" : ""), false);
			if (self::isTestPeriodEnd()) {
				return false;
			}
			$AMMINA_OPTIMIZER_APP = \Ammina\Optimizer\Core2\Application::getInstance();
			if ($APPLICATION->GetGroupRight("ammina.optimizer") >= "W") {
				$APPLICATION->SetAdditionalCSS("/bitrix/themes/.default/ammina.optimizer.pub.css");
				$arMenu[] = array(
					"TEXT" => Loc::getMessage("AMMINA_OPTIMIZER_PANEL_BUTTON_SHOWSTAT"),
					"TITLE" => Loc::getMessage("AMMINA_OPTIMIZER_PANEL_BUTTON_SHOWSTAT_TITLE"),
					"CHECKED" => $AMMINA_OPTIMIZER_APP->isShowStat(),
					"ACTION" => "jsUtils.Redirect([], '" . CUtil::addslashes($APPLICATION->GetCurPageParam("amopt_showstat=" . ($AMMINA_OPTIMIZER_APP->isShowStat() ? "N" : "Y"), array("amopt_clear_cache", "amopt_showstat"))) . "');",
					"HK_ID" => "amopt_top_panel_showstat",
				);
				$arMenu[] = array("SEPARATOR" => true);
				$arMenu[] = array(
					"TEXT" => Loc::getMessage("AMMINA_OPTIMIZER_PANEL_BUTTON_CLEAR_CSS"),
					"TITLE" => Loc::getMessage("AMMINA_OPTIMIZER_PANEL_BUTTON_CLEAR_CSS_TITLE"),
					"ACTION" => "jsUtils.Redirect([], '" . CUtil::addslashes($APPLICATION->GetCurPageParam("amopt_clear_cache=css", array("amopt_clear_cache", "amopt_showstat"))) . "');",
					"HK_ID" => "amopt_top_panel_clear_css",
				);
				$arMenu[] = array(
					"TEXT" => Loc::getMessage("AMMINA_OPTIMIZER_PANEL_BUTTON_CLEAR_JS"),
					"TITLE" => Loc::getMessage("AMMINA_OPTIMIZER_PANEL_BUTTON_CLEAR_JS_TITLE"),
					"ACTION" => "jsUtils.Redirect([], '" . CUtil::addslashes($APPLICATION->GetCurPageParam("amopt_clear_cache=js", array("amopt_clear_cache", "amopt_showstat"))) . "');",
					"HK_ID" => "amopt_top_panel_clear_js",
				);
				$arMenu[] = array(
					"TEXT" => Loc::getMessage("AMMINA_OPTIMIZER_PANEL_BUTTON_CLEAR_IMAGE"),
					"TITLE" => Loc::getMessage("AMMINA_OPTIMIZER_PANEL_BUTTON_CLEAR_IMAGE_TITLE"),
					"ACTION" => "jsUtils.Redirect([], '" . CUtil::addslashes($APPLICATION->GetCurPageParam("amopt_clear_cache=image", array("amopt_clear_cache", "amopt_showstat"))) . "');",
					"HK_ID" => "amopt_top_panel_clear_image",
				);
				$arMenu[] = array("SEPARATOR" => true);
				$arMenu[] = array(
					"TEXT" => Loc::getMessage("AMMINA_OPTIMIZER_PANEL_BUTTON_STOP"),
					"TITLE" => Loc::getMessage("AMMINA_OPTIMIZER_PANEL_BUTTON_STOP_TITLE"),
					"CHECKED" => $_SESSION['AMOPT_STOP'],
					"ACTION" => "jsUtils.Redirect([], '" . CUtil::addslashes($APPLICATION->GetCurPageParam("amopt_stop=" . ($_SESSION['AMOPT_STOP'] ? "N" : \Ammina\Optimizer\Core2\Application::getInstance()->getSecretKeyStop()), array("amopt_clear_cache", "amopt_showstat"))) . "');",
					"HK_ID" => "amopt_top_panel_stop",
				);
				$APPLICATION->AddPanelButton(
					array(
						"HREF" => $APPLICATION->GetCurPageParam("amopt_clear_cache=Y", array("amopt_clear_cache")),
						"TYPE" => "BIG",
						"ICON" => "bx-panel-clear-cache-icon",
						"TEXT" => Loc::getMessage("AMMINA_OPTIMIZER_PANEL_BUTTON_CLEAR"),
						"ALT" => Loc::getMessage("AMMINA_OPTIMIZER_PANEL_BUTTON_CLEAR"),
						"MAIN_SORT" => "3000",
						"SORT" => 10,
						"MENU" => $arMenu,
						"HK_ID" => "amopt_top_panel_clear_cache",
						/*"HINT" => array(
							"TITLE" => GetMessage("top_panel_cache_new_tooltip_title"),
							"TEXT" => GetMessage("top_panel_cache_new_tooltip"),
						),*/
					)
				);
			}
		}
		if (COption::GetOptionString("ammina.optimizer", "autoredirect_webp", "N") == 'Y') {
			if (!\CAmminaOptimizer::doMathPageToRules(
					array(
						"/bitrix/admin/",
						"/bitrix/services/",
						"/bitrix/activities/",
						"/bitrix/gadgets/",
						"/bitrix/panel/",
						"/bitrix/tools/",
						"/bitrix/wizards/",
						"/bitrix/components/bitrix/sender.",
						"/bitrix/components/bitrix/report.",
						"/bitrix/components/bitrix/rest.",
						"/bitrix/components/bitrix/b24connector.",
						"/bitrix/components/bitrix/bitrixcloud.",
						"/bitrix/components/bitrix/bitrixcloud.",
						"/bitrix/components/bitrix/ui.",
					),
					$APPLICATION->GetCurPage()
				) && $_SERVER['REQUEST_METHOD'] != "POST") {
				if (amopt_strpos($_SERVER['HTTP_X_ORIGINAL_QUERY'], '&iswebp') !== false || amopt_strpos($_SERVER['HTTP_X_ORIGINAL_QUERY'], 'iswebp') === 0) {
					LocalRedirect($APPLICATION->GetCurPageParam("", array("iswebp")), "301 Moved Permanently");
				}
			}
		}
	}

	static public function doMathPageToRules($strRules, $strPage)
	{
		if (is_array($strRules)) {
			$arRules = $strRules;
		} else {
			$arRules = explode("\n", $strRules);
		}
		foreach ($arRules as $strRule) {
			$strRule = trim($strRule);
			if (amopt_strlen($strRule) > 0) {
				if (amopt_strpos($strRule, 'PREG:') === 0) {
					$strRule = trim(amopt_substr($strRule, 5));
					$strPattern = "/" . $strRule . "/ui";
					$aMatch = array();
					if (preg_match($strPattern, $strPage, $aMatch)) {
						return true;
					}
				} elseif (amopt_strpos($strRule, 'PART:') === 0) {
					$strRule = trim(amopt_substr($strRule, 5));
					if (amopt_stripos($strPage, $strRule) !== false) {
						return true;
					}
				} else {
					if (amopt_stripos($strPage, $strRule) === 0) {
						return true;
					}
				}
			}
		}
		return false;
	}

	static public function doMathContentToRules($strRules, $strContent)
	{
		if (is_array($strRules)) {
			$arRules = $strRules;
		} else {
			$arRules = explode("\n", $strRules);
		}
		foreach ($arRules as $strRule) {
			$strRule = trim($strRule);
			if (amopt_strlen($strRule) > 0) {
				if (amopt_strpos($strRule, 'PREG:') === 0) {
					$strRule = trim(amopt_substr($strRule, 5));
					$strPattern = "/" . $strRule . "/ui";
					$aMatch = array();
					if (preg_match($strPattern, $strContent, $aMatch)) {
						return true;
					}
				} else {
					if (amopt_stripos($strContent, $strRule) !== false) {
						return true;
					}
				}
			}
		}
		return false;
	}

	static public function clearCacheFiles()
	{
		self::doRmDir($_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/css/ammina.optimizer");
		self::doRmDir($_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/js/ammina.optimizer");
	}

	static public function doRmDir($strPath)
	{
		if (file_exists($strPath) && is_dir($strPath)) {
			$arFiles = scandir($strPath);
			foreach ($arFiles as $strFile) {
				if (in_array($strFile, array(".", ".."))) {
					continue;
				}
				$strFullName = $strPath . "/" . $strFile;
				if (is_dir($strFullName)) {
					self::doRmDir($strFullName);
					@rmdir($strFullName);
				} else {
					@unlink($strFullName);
				}
			}
		}
	}

	static public function doRecursiveOptimizeCss(&$arJson)
	{
		if (is_array($arJson)) {
			foreach ($arJson as $k => &$v) {
				self::doRecursiveOptimizeCss($arJson[$k]);
			}
		} else {
			if (self::$oCssObject === false) {
				self::$oCssObject = new \Ammina\Optimizer\Core\Css();
			}
			self::$oCssObject->doOptimize($arJson);
		}
	}

	static public function doRecursiveOptimizeJs(&$arJson)
	{
		if (is_array($arJson)) {
			foreach ($arJson as $k => &$v) {
				self::doRecursiveOptimizeJs($arJson[$k]);
			}
		} else {
			if (self::$oJsObject === false) {
				self::$oJsObject = new \Ammina\Optimizer\Core\Js();
			}
			self::$oJsObject->doOptimize($arJson);
		}
	}

	static public function doRecursiveOptimizeImg(&$arJson)
	{
		if (is_array($arJson)) {
			foreach ($arJson as $k => &$v) {
				self::doRecursiveOptimizeImg($arJson[$k]);
			}
		} else {
			if (self::$oImgObject === false) {
				self::$oImgObject = new \Ammina\Optimizer\Core\Img();
			}
			self::$oImgObject->doOptimize($arJson);
		}
	}

	static public function doRequestPageRemote($strUrl, $strUserAgent = '', $iTimeout = 60, $iConnectTimeout = 15)
	{
		global $APPLICATION;
		if (!function_exists("curl_init")) {
			return false;
		}
		$strResult = false;
		if (amopt_strpos($strUrl, '//') === 0) {
			if ($APPLICATION->IsHttps()) {
				$strUrl = 'https:' . $strUrl;
			} else {
				$strUrl = 'http:' . $strUrl;
			}
		}
		$arInfo = false;
		if (function_exists("curl_init")) {
			$oCurl = curl_init($strUrl);
			if ($oCurl) {
				curl_setopt($oCurl, CURLOPT_AUTOREFERER, true);
				curl_setopt($oCurl, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, $iConnectTimeout);
				curl_setopt($oCurl, CURLOPT_TIMEOUT, $iTimeout);
				curl_setopt($oCurl, CURLOPT_MAXREDIRS, 30);
				curl_setopt($oCurl, CURLOPT_ENCODING, "gzip");
				curl_setopt($oCurl, CURLOPT_HEADER, false);
				if (amopt_strlen($strUserAgent) > 0) {
					curl_setopt($oCurl, CURLOPT_USERAGENT, $strUserAgent);
				}
				$iStartTime = microtime(true);
				$strResult = curl_exec($oCurl);
				$iTotalTime = microtime(true) - $iStartTime;
				$arInfo = curl_getinfo($oCurl);
				curl_close($oCurl);
			}
		} else {
			$oHttpClient = new \Bitrix\Main\Web\HttpClient(
				array(
					'redirect' => true,
					'redirectMax' => 30,
					'version' => '1.1',
					'disableSslVerification' => true,
					'waitResponse' => true,
					'socketTimeout' => $iTimeout,
					'streamTimeout' => $iTimeout,
					'charset' => "UTF-8",
					'compress' => true
				)
			);
			if (amopt_strlen($strUserAgent) > 0) {
				$oHttpClient->setHeader("User-Agent", $strUserAgent);
			}
			$iStartTime = microtime(true);
			$strResult = $oHttpClient->get($strUrl);
			$iTotalTime = microtime(true) - $iStartTime;
			$status = intval($oHttpClient->getStatus());
			$arInfo['http_code'] = $status;
		}
		if ($arInfo) {
			$bLogErrorRequest = COption::GetOptionString("ammina.optimizer", "log_error_requests", "Y") == "Y";
			$bLogSlowRequest = COption::GetOptionString("ammina.optimizer", "log_slow_requests", "Y") == "Y";
			if ($arInfo['http_code'] != "200") {
				$strResult = false;
				if ($bLogErrorRequest) {
					CheckDirPath($_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina/ammina.optimizer/log/");
					file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina/ammina.optimizer/log/error_" . date("Y-m-d") . ".log", date("d.m.Y H:i:s") . ': Error request ' . $strUrl . ', Status: ' . $arInfo['http_code'] . ", Page: " . ('http' . ($APPLICATION->IsHTTPS() ? "s" : "") . "://" . $_SERVER['HTTP_HOST'] . $APPLICATION->GetCurPageParam()) . "\n", FILE_APPEND);
				}
			}
			if ($iTotalTime > 2 && $bLogSlowRequest) {
				CheckDirPath($_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina/ammina.optimizer/log/");
				file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina/ammina.optimizer/log/slow_" . date("Y-m-d") . ".log", date("d.m.Y H:i:s") . ': Slow request ' . $strUrl . ', Time: ' . $iTotalTime . "s, Page: " . ('http' . ($APPLICATION->IsHTTPS() ? "s" : "") . "://" . $_SERVER['HTTP_HOST'] . $APPLICATION->GetCurPageParam()) . "\n", FILE_APPEND);
			}
		}
		return $strResult;
	}

	static public function checkRequestDomainUrl(&$strUrl)
	{
		global $APPLICATION;
		$bResult = false;
		$arUrl = parse_url($strUrl);
		$arUrl2 = parse_url('http' . ($APPLICATION->IsHTTPS() ? "s" : "") . '://' . $_SERVER['HTTP_HOST'] . '/');
		if ($arUrl['host'] == $arUrl2['host']) {
			$arPathInfo = pathinfo($arUrl['path']);
			if (in_array(amopt_strtolower($arPathInfo['extension']), array("jpg", "jpeg", "png", "gif", "webp", "css", "js", "woff2", "woff", "ttf", "otf", "eot", "svg"))) {
				$strUrl = $arUrl['path'];
				$bResult = true;
			}
		}
		return $bResult;
	}

	/*
		static public function gunzip($zipped)
		{
			$offset = 0;
			return gzinflate(amopt_substr($zipped, $offset + 10));
			if (amopt_substr($zipped, 0, 2) == "\x1f\x8b")
				$offset = 2;
			if (amopt_substr($zipped, $offset, 1) == "\x08") {
				return gzinflate(amopt_substr($zipped, $offset + 8));
			}
			return false;
		}
	*/
	static public function Rel2AbsUrl($strBaseUrl, $strPath)
	{
		$arUrlBase = parse_url($strBaseUrl);
		$arUrlFile = parse_url($strPath);
		$strResult = "";
		$arNew = array();
		if (amopt_strlen($arUrlFile['scheme']) > 0) {
			$arNew['scheme'] = $arUrlFile['scheme'];
			$arNew['host'] = $arUrlFile['host'];
			$arNew['path'] = $arUrlFile['path'];
			$arNew['query'] = $arUrlFile['query'];
			$arNew['fragment'] = $arUrlFile['fragment'];
		} else {
			$arNew['scheme'] = $arUrlBase['scheme'];
			if (amopt_strlen($arUrlFile['host']) > 0) {
				$arNew['host'] = $arUrlFile['host'];
				$arNew['path'] = $arUrlFile['path'];
				$arNew['query'] = $arUrlFile['query'];
				$arNew['fragment'] = $arUrlFile['fragment'];
			} else {
				$arNew['host'] = $arUrlBase['host'];
				if (amopt_strlen($arUrlFile['path']) > 0) {
					$arPath = ammina_pathinfo($arUrlBase['path']);
					$strPathDir = $arPath['dirname'] . "/";
					if (amopt_strlen($arPath['extension']) <= 0) {
						$strPathDir = $arUrlBase['path'];
					}
					$arNew['path'] = Rel2Abs($strPathDir, $arUrlFile['path']);
					$arNew['query'] = $arUrlFile['query'];
					$arNew['fragment'] = $arUrlFile['fragment'];
				} else {
					$arNew['path'] = $arUrlBase['path'];
					if (amopt_strlen($arUrlFile['query']) > 0) {
						$arNew['query'] = $arUrlFile['query'];
						$arNew['fragment'] = $arUrlFile['fragment'];
					} else {
						$arNew['query'] = $arUrlBase['query'];
						if (amopt_strlen($arUrlFile['fragment']) > 0) {
							$arNew['fragment'] = $arUrlFile['fragment'];
						} else {
							$arNew['fragment'] = $arUrlBase['fragment'];
						}
					}
				}
			}
		}

		if (amopt_strlen($arNew['scheme']) > 0) {
			$strResult .= $arNew['scheme'] . "://";
		}
		if (amopt_strlen($arNew['host']) > 0) {
			$strResult .= $arNew['host'];
		}
		if (amopt_strlen($arNew['path']) > 0) {
			$strResult .= $arNew['path'];
		}
		if (amopt_strlen($arNew['query']) > 0) {
			$strResult .= "?" . $arNew['query'];
		}
		if (amopt_strlen($arNew['fragment']) > 0) {
			$strResult .= "#" . $arNew['fragment'];
		}

		return $strResult;
	}

	public static function SaveFileContent($abs_path, $strContent)
	{
		CheckDirPath(dirname($abs_path) . "/");
		file_put_contents($abs_path, $strContent);
		@chmod($abs_path, BX_FILE_PERMISSIONS);
	}

	public static function showSupportForm()
	{
		if (COption::GetOptionString("ammina.optimizer", "show_support_form", "Y") == "Y") {
			$strCacheFileName = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/cache/ammina/support/ammina_support.txt";
			$arCacheData = false;
			if (file_exists($strCacheFileName)) {
				$arCacheData = @unserialize(file_get_contents($strCacheFileName));
				if (time() > $arCacheData['TTL']) {
					@unlink($strCacheFileName);
					$arCacheData = false;
				}
			}
			if (is_array($arCacheData) && !empty($arCacheData)) {
				$strCode = $arCacheData['DATA'];
			} else {
				$client = new \Bitrix\Main\Web\HttpClient(
					array(
						'redirect' => true,
						'redirectMax' => 10,
						'socketTimeout' => 15,
						'streamTimeout' => 15,
						'disableSslVerification' => true,
					)
				);
				$strCode = $client->get("https://www.ammina24.ru/upload/support.widget.txt");
				$status = intval($client->getStatus());
				if ($status != 200) {
					$strCode = "";
				}
				CheckDirPath(dirname($strCacheFileName) . "/");
				$arCacheData = array(
					"TTL" => time() + 3600,
					"DATA" => $strCode
				);
				file_put_contents($strCacheFileName, serialize($arCacheData));
			}
			if (amopt_strlen($strCode) > 0) {
				echo $strCode;
			}
		}
	}

	public static function getExtOptionsForFiles($type = "css")
	{
		$arCacheOptions = array(
			"css" => array("css_fontface", "css_minify_active", "css_minify_type", "css_incimages", "css_incimages_maxsize", "css_files_no_optimize", "css_files_no_minimize", "css_path_yuicompressor", "css_path_uglifycss", "css_remote_active", "css_remote_disable_links", "css_remote_googlefonts_type"),
			"js" => array("js_minify_active", "js_minify_type", "js_files_no_optimize", "js_files_no_minimize", "js_bxcore_files_active", "js_path_uglifyjs", "js_path_yuicompressor", "js_path_uglifyjs2", "js_path_terserjs", "js_path_babelminify", "js_remote_active", "js_remote_disable_links"),
		);
		$arCache = array();
		foreach ($arCacheOptions[$type] as $val) {
			$arCache[$val] = COption::GetOptionString("ammina.optimizer", $val, "");
		}
		return serialize($arCache);
	}

	public static function doPreLoadHeaders($strLink, &$strContent)
	{
		$lastStatus = CHTTP::GetLastStatus();
		if (amopt_strlen($lastStatus) <= 0 || amopt_strtoupper($lastStatus) == "200 OK") {
			header("Link: " . $strLink, false);
			if (COption::GetOptionString("ammina.optimizer", "header_link_to_html", "Y") == "Y") {
				CAmminaOptimizer::doCheckLinkToHtml($strLink, $strContent);
			}
			if (COption::GetOptionString("ammina.optimizer", "header_prefetch", "Y") == "Y") {
				$strFetch = str_replace('rel=preload;', 'rel=prefetch;', $strLink);
				header("Link: " . $strFetch, false);
				if (COption::GetOptionString("ammina.optimizer", "header_link_to_html", "Y") == "Y") {
					CAmminaOptimizer::doCheckLinkToHtml($strFetch, $strContent);
				}
			}
		}
	}

	public static function doCheckLinkToHtml($strLink, &$strContent)
	{
		$arData = explode(";", $strLink);
		$arLinkData = array();
		foreach ($arData as $val) {
			$val = trim($val);
			if (amopt_substr($val, 0, 1) == '<') {
				$arLinkData[] = 'href="' . amopt_substr($val, 1, strlen($val) - 2) . '"';
			} elseif (amopt_strpos($val, 'rel=') === 0) {
				$arLinkData[] = 'rel="' . amopt_substr($val, 4) . '"';
			} elseif (amopt_strpos($val, 'as=') === 0) {
				$arLinkData[] = 'as="' . amopt_substr($val, 3) . '"';
			} elseif (amopt_strpos($val, 'crossorigin') === 0) {
				$arLinkData[] = 'crossorigin="anonymous"';
			}
		}
		if (!empty($arLinkData)) {
			$strNewLink = '<link ' . implode(" ", $arLinkData) . ' />';
			$strContent = str_replace('</head>', $strNewLink . "\n" . "</head>", $strContent);
		}
	}

	/**
	 * @param $strTypeRequest (GET_WORK_SERVER)
	 * @param $arFields
	 *
	 * @return mixed
	 * @throws \Bitrix\Main\ArgumentOutOfRangeException
	 */
	public static function doRequestAmminaServer($strTypeRequest, $arFields = array())
	{
		$arFields['k'] = COption::GetOptionString("ammina.optimizer", "ammina_apikey", "");
		$lk = COption::GetOptionString("ammina.optimizer", "ammina_lkey", "");
		$lktime = COption::GetOptionString("ammina.optimizer", "ammina_lkeytime", "");
		if (amopt_strlen($lk) <= 0 || $lktime <= 0 || $lktime < (time() - 3600)) {
			include_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/classes/general/update_client.php");
			include_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/classes/general/update_client_partner.php");
			$lk = md5('BITRIX' . CUpdateClientPartner::GetLicenseKey() . 'LICENSE');
			COption::SetOptionString("ammina.optimizer", "ammina_lkey", $lk);
			COption::SetOptionString("ammina.optimizer", "ammina_lkeytime", time());
		}
		$arFields['l'] = $lk;
		$arFields['t'] = $strTypeRequest;
		if (!defined("BX_UTF") || BX_UTF !== true) {
			$arFields = $GLOBALS['APPLICATION']->ConvertCharsetArray($arFields, (amopt_strlen(LANG_CHARSET) > 0 ? LANG_CHARSET : SITE_CHARSET), "UTF-8");
		}
		$strUrl = "https://www.fastbitrix.ru/api/api.php";
		$oHttpClient = new \Bitrix\Main\Web\HttpClient(
			array(
				'redirect' => true,
				'redirectMax' => 10,
				'version' => '1.1',
				'disableSslVerification' => true,
				'waitResponse' => true,
				'socketTimeout' => 15,
				'streamTimeout' => 30,
				'charset' => "UTF-8",
			)
		);
		$response = $oHttpClient->post($strUrl, $arFields);
		$arResponse = json_decode($response, true);
		if (!defined("BX_UTF") || BX_UTF !== true) {
			$arResponse = $GLOBALS['APPLICATION']->ConvertCharsetArray($arResponse, "UTF-8", (amopt_strlen(LANG_CHARSET) > 0 ? LANG_CHARSET : SITE_CHARSET));
		}
		return $arResponse;
	}

	/**
	 * @param $strTypeRequest
	 * @param $arFields
	 */
	public static function doRequestWorkServer($strTypeRequest, $arFields = array())
	{
		global $APPLICATION;
		$arFields['SYSTEM'] = array(
			"HOST" => $_SERVER['HTTP_HOST'],
			"HTTPS" => $APPLICATION->IsHttps(),
		);
		$strUrl = COption::GetOptionString("ammina.optimizer", "ammina_workurl", "");
		$urltime = COption::GetOptionString("ammina.optimizer", "ammina_workurltime", "");
		if (amopt_strlen($strUrl) <= 0 || $urltime <= 0 || $urltime < (time() - 3600)) {
			$arUrlResponse = self::doRequestAmminaServer("GET_WORK_SERVER");
			if ($arUrlResponse['status'] == "ok") {
				$strUrl = $arUrlResponse['url'];
				$urltime = time();
				COption::SetOptionString("ammina.optimizer", "ammina_workurl", $strUrl);
				COption::SetOptionString("ammina.optimizer", "ammina_workurltime", $urltime);
			}
		}
		if (amopt_strlen($strUrl) > 0) {
			$arFields['k'] = COption::GetOptionString("ammina.optimizer", "ammina_apikey", "");
			$lk = COption::GetOptionString("ammina.optimizer", "ammina_lkey", "");
			$lktime = COption::GetOptionString("ammina.optimizer", "ammina_lkeytime", "");
			if (amopt_strlen($lk) <= 0 || $lktime <= 0 || $lktime < (time() - 3600)) {
				include_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/classes/general/update_client.php");
				include_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/classes/general/update_client_partner.php");
				$lk = md5('BITRIX' . CUpdateClientPartner::GetLicenseKey() . 'LICENSE');
				COption::SetOptionString("ammina.optimizer", "ammina_lkey", $lk);
				COption::SetOptionString("ammina.optimizer", "ammina_lkeytime", time());
			}
			$arFields['l'] = $lk;
			$arFields['t'] = $strTypeRequest;
			if (!defined("BX_UTF") || BX_UTF !== true) {
				$arFields = $GLOBALS['APPLICATION']->ConvertCharsetArray($arFields, (amopt_strlen(LANG_CHARSET) > 0 ? LANG_CHARSET : SITE_CHARSET), "UTF-8");
			}
			$oHttpClient = new \Bitrix\Main\Web\HttpClient(
				array(
					'redirect' => true,
					'redirectMax' => 10,
					'version' => '1.1',
					'disableSslVerification' => true,
					'waitResponse' => true,
					'socketTimeout' => 15,
					'streamTimeout' => 30,
					'charset' => "UTF-8",
				)
			);
			$response = $oHttpClient->post($strUrl, $arFields);
			$arResponse = json_decode($response, true);
			if (!defined("BX_UTF") || BX_UTF !== true) {
				$arResponse = $GLOBALS['APPLICATION']->ConvertCharsetArray($arResponse, "UTF-8", (amopt_strlen(LANG_CHARSET) > 0 ? LANG_CHARSET : SITE_CHARSET));
			}
			return $arResponse;
		}
		return false;
	}

	public static function doRequestWorkServerResultFile($strFileUrl, $strFileResult, $strFileNameFileUrl)
	{
		$oHttpClient = new \Bitrix\Main\Web\HttpClient(
			array(
				'redirect' => true,
				'redirectMax' => 10,
				'version' => '1.1',
				'disableSslVerification' => true,
				'waitResponse' => true,
				'socketTimeout' => 15,
				'streamTimeout' => 30,
				'charset' => "UTF-8",
			)
		);
		$response = $oHttpClient->get($strFileUrl);
		$status = intval($oHttpClient->getStatus());
		if ($status != 200) {
			return false;
		}
		CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strFileResult, $response);
		@chmod($_SERVER['DOCUMENT_ROOT'] . $strFileResult, BX_FILE_PERMISSIONS);
		@unlink($_SERVER["DOCUMENT_ROOT"] . $strFileNameFileUrl);
		return true;
	}

	public static function OnFileSave(&$arFile, $strFileName, $strSavePath, $bForceMD5, $bSkipExt, $dirAdd)
	{
		$arPath = ammina_pathinfo($strFileName);
		if (in_array(amopt_strtolower($arPath['extension']), array("jpg", "jpeg", "png", "gif", "svg"))) {
			$upload_dir = COption::GetOptionString("main", "upload_dir", "upload");
			$io = CBXVirtualIo::GetInstance();
			if ($bForceMD5 != true && COption::GetOptionString("main", "save_original_file_name", "N") == "Y") {
				$dir_add = $dirAdd;
				if ($dir_add == '') {
					$i = 0;
					while (true) {
						$dir_add = amopt_substr(md5(uniqid("", true)), 0, 3);
						if (!$io->FileExists($_SERVER["DOCUMENT_ROOT"] . "/" . $upload_dir . "/" . $strSavePath . "/" . $dir_add . "/" . $strFileName)) {
							break;
						}
						if ($i >= 25) {
							$j = 0;
							while (true) {
								$dir_add = amopt_substr(md5(mt_rand()), 0, 3) . "/" . amopt_substr(md5(mt_rand()), 0, 3);
								if (!$io->FileExists($_SERVER["DOCUMENT_ROOT"] . "/" . $upload_dir . "/" . $strSavePath . "/" . $dir_add . "/" . $strFileName)) {
									break;
								}
								if ($j >= 25) {
									$dir_add = amopt_substr(md5(mt_rand()), 0, 3) . "/" . md5(mt_rand());
									break;
								}
								$j++;
							}
							break;
						}
						$i++;
					}
				}
				if (amopt_substr($strSavePath, -1, 1) <> "/") {
					$strSavePath .= "/" . $dir_add;
				} else {
					$strSavePath .= $dir_add . "/";
				}
			} else {
				$strFileExt = ($bSkipExt == true || ($ext = GetFileExtension($strFileName)) == '' ? '' : "." . $ext);
				while (true) {
					if (amopt_substr($strSavePath, -1, 1) <> "/") {
						$strSavePath .= "/" . amopt_substr($strFileName, 0, 3);
					} else {
						$strSavePath .= amopt_substr($strFileName, 0, 3) . "/";
					}

					if (!$io->FileExists($_SERVER["DOCUMENT_ROOT"] . "/" . $upload_dir . "/" . $strSavePath . "/" . $strFileName)) {
						break;
					}

					//try the new name
					$strFileName = md5(uniqid("", true)) . $strFileExt;
				}
			}

			$arFile["SUBDIR"] = $strSavePath;
			$arFile["FILE_NAME"] = $strFileName;
			$strDirName = $_SERVER["DOCUMENT_ROOT"] . "/" . $upload_dir . "/" . $strSavePath . "/";
			$strDbFileNameX = $strDirName . $strFileName;
			$strPhysicalFileNameX = $io->GetPhysicalName($strDbFileNameX);

			CheckDirPath($strDirName);

			if (is_set($arFile, "content")) {
				$f = fopen($strPhysicalFileNameX, "w");
				if (!$f) {
					return false;
				}
				if (fwrite($f, $arFile["content"]) === false) {
					return false;
				}
				fclose($f);
			} elseif (
				!copy($arFile["tmp_name"], $strPhysicalFileNameX)
				&& !move_uploaded_file($arFile["tmp_name"], $strPhysicalFileNameX)
			) {
				CFile::DoDelete($arFile["old_file"]);
				return false;
			}

			if (isset($arFile["old_file"])) {
				CFile::DoDelete($arFile["old_file"]);
			}

			@chmod($strPhysicalFileNameX, BX_FILE_PERMISSIONS);

			//flash is not an image
			$flashEnabled = !CFile::IsImage($arFile["ORIGINAL_NAME"], $arFile["type"]);

			$imgArray = CFile::GetImageSize($strDbFileNameX, false, $flashEnabled);

			if (is_array($imgArray)) {
				$arFile["WIDTH"] = $imgArray[0];
				$arFile["HEIGHT"] = $imgArray[1];

				if ($imgArray[2] == IMAGETYPE_JPEG) {
					$exifData = CFile::ExtractImageExif($strPhysicalFileNameX);
					if ($exifData && isset($exifData['Orientation'])) {
						//swap width and height
						if ($exifData['Orientation'] >= 5 && $exifData['Orientation'] <= 8) {
							$arFile["WIDTH"] = $imgArray[1];
							$arFile["HEIGHT"] = $imgArray[0];
						}

						$properlyOriented = CFile::ImageHandleOrientation($exifData['Orientation'], $io->GetPhysicalName($strDbFileNameX));
						if ($properlyOriented) {
							$jpgQuality = intval(COption::GetOptionString('main', 'image_resize_quality', '95'));
							if ($jpgQuality <= 0 || $jpgQuality > 100) {
								$jpgQuality = 95;
							}

							imagejpeg($properlyOriented, $strPhysicalFileNameX, $jpgQuality);
							clearstatcache(true, $strPhysicalFileNameX);
						}

						$arFile['size'] = filesize($strPhysicalFileNameX);
					}
				}
			} else {
				$arFile["WIDTH"] = 0;
				$arFile["HEIGHT"] = 0;
			}
			$strFullFileName = "/" . $upload_dir . "/" . $arFile["SUBDIR"] . "/" . $arFile["FILE_NAME"];
			\Ammina\Optimizer\Core2\AppBackground::getInstance()->doOptimizeImage($strFullFileName);
			return true;
		}
	}

	public static function OnAfterResizeImage($file, $arSizeParams, &$callbackData, &$cacheImageFile, &$cacheImageFileTmp, &$arImageSize)
	{
		$strFullFileName = $_SERVER["DOCUMENT_ROOT"] . $cacheImageFile;
		$arPath = ammina_pathinfo($strFullFileName);
		if (in_array(amopt_strtolower($arPath['extension']), array("jpg", "jpeg", "png", "gif", "svg"))) {
			\Ammina\Optimizer\Core2\AppBackground::getInstance()->doOptimizeImage($cacheImageFile);
		}
	}

	public static function doCheckNotify()
	{
		global $APPLICATION;
		if (amopt_strpos($APPLICATION->GetCurPage(), "/bitrix/admin/") !== 0) {
			return;
		}
		$nextTime = intval(COption::GetOptionInt("ammina", "notify_next_time", 0));
		if ($nextTime <= 0 || $nextTime <= time()) {
			$client = new \Bitrix\Main\Web\HttpClient(
				array(
					'redirect' => true,
					'redirectMax' => 10,
					'socketTimeout' => 15,
					'streamTimeout' => 15,
					'disableSslVerification' => true,
				)
			);
			$strCode = $client->get("https://www.ammina.ru/local/notify/old.txt");
			$iOldNumber = false;
			$status = intval($client->getStatus());
			if ($status == 200) {
				$iOldNumber = intval($strCode);
			}
			if ($iOldNumber > 0) {
				$iFromNumber = intval(COption::GetOptionInt("ammina", "notify_old", 0));
				$arShowed = array();
				if ($iFromNumber <= 0) {
					$strContent = $client->get("https://www.ammina.ru/local/notify/req.json");
					$status = intval($client->getStatus());
					if ($status == 200) {
						$arData = @\Bitrix\Main\Web\Json::decode($strContent);
						foreach ($arData as $id) {
							$arShowed[] = $id;
							self::doShowNotify($id);
						}
					}
					$iFromNumber = $iOldNumber - 1;
				}
				for ($i = $iFromNumber + 1; $i <= $iOldNumber; $i++) {
					if (!in_array($i, $arShowed)) {
						self::doShowNotify($i);
					}
				}
				COption::SetOptionInt("ammina", "notify_old", $iOldNumber);
			}
		}
		COption::SetOptionInt("ammina", "notify_next_time", time() + 10800);
	}

	public static function doShowNotify($ID)
	{
		$client = new \Bitrix\Main\Web\HttpClient(
			array(
				'redirect' => true,
				'redirectMax' => 10,
				'socketTimeout' => 15,
				'streamTimeout' => 15,
				'disableSslVerification' => true,
			)
		);
		$strContent = $client->get("https://www.ammina.ru/local/notify/" . $ID . ".json");
		$status = intval($client->getStatus());
		if ($status == 200) {
			$arData = @\Bitrix\Main\Web\Json::decode($strContent);
			if ($arData['ID'] > 0 && amopt_strlen($arData['TITLE']) > 0 && amopt_strlen($arData['TEXT']) > 0) {
				$bAllowNotify = true;
				if (isset($arData['MODULE']) && !empty($arData['MODULE']) && is_array($arData['MODULE']) && count($arData['MODULE']) > 0) {
					$bAllowNotify = false;
					foreach ($arData['MODULE'] as $mod) {
						if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/" . $mod . "/")) {
							$bAllowNotify = true;
						}
					}
				}
				if (isset($arData['NOTMODULE']) && !empty($arData['NOTMODULE']) && is_array($arData['NOTMODULE']) && count($arData['NOTMODULE']) > 0) {
					$bAllowNotify = false;
					foreach ($arData['NOTMODULE'] as $mod) {
						if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/" . $mod . "/")) {
							$bAllowNotify = false;
						}
					}
				}
				if ($bAllowNotify) {
					\CAdminNotify::Add(
						array(
							//'MODULE_ID' => "",
							//'TAG' => "",
							'MESSAGE' => '<b>' . $arData['TITLE'] . '</b><br>' . $arData['TEXT'],
							'ENABLE_CLOSE' => "Y",
							'PUBLIC_SECTION' => "N",
							'NOTIFY_TYPE' => "M"
						)
					);
				}
			}
		}
	}

	public static function getAmminaApiKey($strName, $strEmail, $strPhone, &$errorMessage)
	{
		include_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/classes/general/update_client.php");
		include_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/classes/general/update_client_partner.php");

		$errorMessage = '';

		$strHost = $_SERVER['SERVER_NAME'];
		if (amopt_strlen($strHost) <= 0) {
			$strHost = $_SERVER['HTTP_HOST'];
		}
		$strError = "";
		$arClient = CUpdateClient::GetUpdatesList($strError);

		$arFields = array(
			"NAME" => $strName,
			"EMAIL" => $strEmail,
			"PHONE" => $strPhone,
			"LICENSE" => md5('BITRIX' . CUpdateClientPartner::GetLicenseKey() . 'LICENSE'),
			"LICENSE_TYPE" => $arClient['CLIENT'][0]['@']['LICENSE'],
			"HOST" => $strHost,
			"LICENSE_DATE_FROM" => $arClient['CLIENT'][0]['@']['DATE_FROM'],
			"LICENSE_DATE_TO" => $arClient['CLIENT'][0]['@']['DATE_TO'],
			//"MODULE_DEMO" => self::MODULE_ID,
			"from" => $_SERVER['HTTP_REFERER'],
		);
		if (!defined("BX_UTF") || BX_UTF !== true) {
			$arFields = $GLOBALS['APPLICATION']->ConvertCharsetArray($arFields, (amopt_strlen(LANG_CHARSET) > 0 ? LANG_CHARSET : SITE_CHARSET), "UTF-8");
		}
		$strUrl = "https://www.fastbitrix.ru/api/getkey.php";
		$oHttpClient = new \Bitrix\Main\Web\HttpClient(
			array(
				'redirect' => true,
				'redirectMax' => 10,
				'version' => '1.1',
				'disableSslVerification' => true,
				'waitResponse' => true,
				'socketTimeout' => 15,
				'streamTimeout' => 30,
				'charset' => "UTF-8",
			)
		);
		$response = $oHttpClient->post($strUrl, $arFields);
		$arResponse = json_decode($response, true);
		if ($arResponse['status'] == "ok") {
			$bResultGetKey = true;
			COption::SetOptionString("ammina.optimizer", "ammina_apikey", $arResponse['key']);
		} else {
			$bResultGetKey = false;
			$errorMessage = $arResponse['message'];
		}
		return $bResultGetKey;
	}

	public static function doCheckCDN()
	{
		//Проверка CDN
		$con = \Bitrix\Main\Application::getConnection();
		$sqlHelper = $con->getSqlHelper();
		$res = $con->query(
			"SELECT * " .
			"FROM b_module_to_module " .
			"WHERE FROM_MODULE_ID='main'" .
			"	AND MESSAGE_ID='OnEndBufferContent' " .
			"	AND TO_MODULE_ID='bitrixcloud' " .
			"	AND TO_CLASS='CBitrixCloudCDN' " .
			"	AND TO_METHOD='OnEndBufferContent'"
		);
		if ($ar = $res->fetch()) {
			if ($ar['SORT'] != 1100) {
				UnRegisterModuleDependences("main", "OnEndBufferContent", "bitrixcloud", "CBitrixCloudCDN", "OnEndBufferContent");
				RegisterModuleDependences("main", "OnEndBufferContent", "bitrixcloud", "CBitrixCloudCDN", "OnEndBufferContent", 1100);
			}
		}
	}

	public static function in_range($a1, $b1, $a2, $b2)
	{
		if ($a1 > $b1) {
			$tmp = $b1;
			$b1 = $a1;
			$a1 = $tmp;
		}
		if ($a2 > $b2) {
			$tmp = $b2;
			$b2 = $a2;
			$a2 = $tmp;
		}
		if ($b2 < $a1 || $a2 > $b1) {
			return false;
		}
		return true;
	}

	public static function getLocalDomains()
	{
		$oCache = new CPHPCache();
		$arDomains = array();
		if ($oCache->InitCache(600, 'ammina_local_domains', 'ammina.optimizer')) {
			$res = $oCache->GetVars();
			$arDomains = $res['DOMAINS'];
		}
		if ($oCache->StartDataCache()) {
			$arCheckDomains = array();
			$strHost = $_SERVER['HTTP_HOST'];
			if (amopt_strpos($strHost, ':') !== false) {
				$strHost = explode(":", $strHost);
				$strHost = $strHost[0];
			}
			$arCheckDomains[] = $strHost;
			$strDomain = COption::GetOptionString("main", "server_name", "");
			if (strlen($strDomain) > 0) {
				$arCheckDomains[] = $strDomain;
			}
			$rSites = CSite::GetList($b, $o, array());
			while ($arSite = $rSites->Fetch()) {
				if (amopt_strlen($arSite['SERVER_NAME']) > 0) {
					$arCheckDomains[] = $arSite['SERVER_NAME'];
				}
				$ar = explode("\n", $arSite['DOMAINS']);
				foreach ($ar as $val) {
					$val = trim($val);
					if (amopt_strlen($val) > 0) {
						$arCheckDomains[] = $val;
					}
				}
			}
			foreach ($arCheckDomains as $domain) {
				$arDomains[amopt_strtolower($domain)] = $domain;
				if (amopt_strpos($domain, 'www.') === 0) {
					$domain = amopt_substr($domain, 4);
					$arDomains[amopt_strtolower($domain)] = $domain;
				} else {
					$arDomains["www." . amopt_strtolower($domain)] = "www." . $domain;
				}
			}

			$oCache->EndDataCache(
				array(
					"DOMAINS" => $arDomains,
				)
			);
		}
		return $arDomains;
	}

	public static function isLocalDomainLink($strLink)
	{
		$arLocalDomains = self::getLocalDomains();
		$arUrl = parse_url($strLink);
		if (isset($arUrl['host']) && amopt_strlen($arUrl['host']) > 0) {
			if (isset($arLocalDomains[amopt_strtolower($arUrl['host'])])) {
				return true;
			}
		}
		return false;
	}
}

CAmminaOptimizer::doCheckNotify();
/*
function amopt_substr_count($haystack, $needle, $offset = null, $length = null)
{
	if (function_exists('mb_strlen') && ((int)ini_get('mbstring.func_overload') & 2)) {
		//$mbIntEnc = NULL;
		//$mbIntEnc = mb_internal_encoding();
		//mb_internal_encoding('8bit');
		if (!is_null($offset) || !is_null($length)) {
			$checkString = substr($haystack, $offset, $length);
			$result = substr_count($checkString, $needle);
		} else {
			$result = substr_count($haystack, $needle);
		}
		//if ($mbIntEnc !== NULL) {
		//	mb_internal_encoding($mbIntEnc);
		//}
		return $result;
	} else {
		return substr_count($haystack, $needle, $offset, $length);
	}
}
*/
CModule::AddAutoloadClasses(
	"ammina.optimizer",
	array(
		"Ammina\\Optimizer\\Core\\Base" => "lib/core/base.php",
		"Ammina\\Optimizer\\Core\\Css" => "lib/core/css.php",
		"Ammina\\Optimizer\\Core\\Js" => "lib/core/js.php",
		"Ammina\\Optimizer\\Core\\Html" => "lib/core/html.php",
		"Ammina\\Optimizer\\Core\\Img" => "lib/core/img.php",

		"Ammina\\Optimizer\\PageTable" => "lib/page.php",
		"Ammina\\Optimizer\\HistoryTable" => "lib/history.php",
		"Ammina\\Optimizer\\SettingsTable" => "lib/settings.php",
		"Ammina\\Optimizer\\StatTypesTable" => "lib/stat.types.php",
		"Ammina\\Optimizer\\FilesOptimizedTable" => "lib/files.optimized.php",
		"Ammina\\Optimizer\\FilesOriginalsTable" => "lib/files.original.php",

		"Ammina\\Optimizer\\Agent\\CheckPage" => "lib/agent/check.page.php",
		"Ammina\\Optimizer\\Agent\\CheckImages" => "lib/agent/check.images.php",
		"Ammina\\Optimizer\\Agent\\CheckCache" => "lib/agent/check.cache.php",

		"Ammina\\Optimizer\\Workers\\Cache\\Stat" => "lib/workers/cache/stat.php",
		"Ammina\\Optimizer\\Workers\\Cache\\Clear" => "lib/workers/cache/clear.php",
		"Ammina\\Optimizer\\Workers\\Cache\\Files" => "lib/workers/cache/files.php",

		"Ammina\\Optimizer\\Helpers\\Admin\\Blocks\\Page" => "lib/helpers/admin/blocks/page.php",
		"Ammina\\Optimizer\\Helpers\\Admin\\Blocks\\PageMonitoring" => "lib/helpers/admin/blocks/page.monitoring.php",
		"Ammina\\Optimizer\\Helpers\\Admin\\Blocks\\History" => "lib/helpers/admin/blocks/history.php",
		"Ammina\\Optimizer\\Helpers\\Admin\\Blocks\\HistoryInfo" => "lib/helpers/admin/blocks/history.info.php",
		"Ammina\\Optimizer\\Helpers\\Admin\\Blocks\\MonitoringInfo" => "lib/helpers/admin/blocks/monitoring.info.php",
		"Ammina\\Optimizer\\Helpers\\Admin\\Blocks\\MonitoringBase" => "lib/helpers/admin/blocks/monitoring.base.php",
		"Ammina\\Optimizer\\Helpers\\Admin\\Blocks\\MonitoringPerformance" => "lib/helpers/admin/blocks/monitoring.performance.php",
		"Ammina\\Optimizer\\Helpers\\Admin\\Blocks\\MonitoringAccessibility" => "lib/helpers/admin/blocks/monitoring.accessibility.php",
		"Ammina\\Optimizer\\Helpers\\Admin\\Blocks\\MonitoringBestPractices" => "lib/helpers/admin/blocks/monitoring.best.practices.php",
		"Ammina\\Optimizer\\Helpers\\Admin\\Blocks\\MonitoringSeo" => "lib/helpers/admin/blocks/monitoring.seo.php",
		"Ammina\\Optimizer\\Helpers\\Admin\\Blocks\\MonitoringPwa" => "lib/helpers/admin/blocks/monitoring.pwa.php",
		"Ammina\\Optimizer\\Helpers\\Admin\\Blocks\\StatFileOptimized" => "lib/helpers/admin/blocks/stat.file.optimized.php",
		"Ammina\\Optimizer\\Helpers\\Admin\\Blocks\\StatFileOriginal" => "lib/helpers/admin/blocks/stat.file.original.php",

		"Psr\\Cache\\InvalidArgumentException" => "lib/ext/psr/InvalidArgumentException.php",
		"Psr\\Cache\\CacheException" => "lib/ext/psr/CacheException.php",
		"Psr\\Cache\\CacheItemInterface" => "lib/ext/psr/CacheItemInterface.php",
		"Psr\\Cache\\CacheItemPoolInterface" => "lib/ext/psr/CacheItemPoolInterface.php",
		"Psr\\Log\\AbstractLogger" => "lib/ext/psr/Log/AbstractLogger.php",
		"Psr\\Log\\InvalidArgumentException" => "lib/ext/psr/Log/InvalidArgumentException.php",
		"Psr\\Log\\LoggerAwareInterface" => "lib/ext/psr/Log/LoggerAwareInterface.php",
		"Psr\\Log\\LoggerAwareTrait" => "lib/ext/psr/Log/LoggerAwareTrait.php",
		"Psr\\Log\\LoggerInterface" => "lib/ext/psr/Log/LoggerInterface.php",
		"Psr\\Log\\LoggerTrait" => "lib/ext/psr/Log/LoggerTrait.php",
		"Psr\\Log\\LogLevel" => "lib/ext/psr/Log/LogLevel.php",
		"Psr\\Log\\NullLogger" => "lib/ext/psr/Log/NullLogger.php",

		"ImageOptimizer\\Optimizer" => "lib/ext/image-optimizer/Optimizer.php",
		"ImageOptimizer\\Command" => "lib/ext/image-optimizer/Command.php",
		"ImageOptimizer\\CommandOptimizer" => "lib/ext/image-optimizer/CommandOptimizer.php",
		"ImageOptimizer\\OptimizerFactory" => "lib/ext/image-optimizer/OptimizerFactory.php",
		"ImageOptimizer\\SmartOptimizer" => "lib/ext/image-optimizer/SmartOptimizer.php",
		"ImageOptimizer\\SuppressErrorOptimizer" => "lib/ext/image-optimizer/SuppressErrorOptimizer.php",
		"ImageOptimizer\\ChainOptimizer" => "lib/ext/image-optimizer/ChainOptimizer.php",
		"ImageOptimizer\\Exception\\CommandNotFound" => "lib/ext/image-optimizer/Exception/CommandNotFound.php",
		"ImageOptimizer\\Exception\\Exception" => "lib/ext/image-optimizer/Exception/Exception.php",
		"ImageOptimizer\\TypeGuesser\\ExtensionTypeGuesser" => "lib/ext/image-optimizer/TypeGuesser/ExtensionTypeGuesser.php",
		"ImageOptimizer\\TypeGuesser\\GdTypeGuesser" => "lib/ext/image-optimizer/TypeGuesser/GdTypeGuesser.php",
		"ImageOptimizer\\TypeGuesser\\SmartTypeGuesser" => "lib/ext/image-optimizer/TypeGuesser/SmartTypeGuesser.php",
		"ImageOptimizer\\TypeGuesser\\TypeGuesser" => "lib/ext/image-optimizer/TypeGuesser/TypeGuesser.php",

		"Symfony\\Component\\OptionsResolver\\Options" => "lib/ext/symphony/OptionsResolver/Options.php",
		"Symfony\\Component\\OptionsResolver\\OptionsResolver" => "lib/ext/symphony/OptionsResolver/OptionsResolver.php",
		"Symfony\\Component\\OptionsResolver\\Exception\ExceptionInterface" => "lib/ext/symphony/OptionsResolver/Exception/ExceptionInterface.php",
		"Symfony\\Component\\OptionsResolver\\Exception\AccessException" => "lib/ext/symphony/OptionsResolver/Exception/AccessException.php",
		"Symfony\\Component\\OptionsResolver\\Exception\InvalidArgumentException" => "lib/ext/symphony/OptionsResolver/Exception/InvalidArgumentException.php",
		"Symfony\\Component\\OptionsResolver\\Exception\InvalidOptionsException" => "lib/ext/symphony/OptionsResolver/Exception/InvalidOptionsException.php",
		"Symfony\\Component\\OptionsResolver\\Exception\MissingOptionsException" => "lib/ext/symphony/OptionsResolver/Exception/MissingOptionsException.php",
		"Symfony\\Component\\OptionsResolver\\Exception\NoSuchOptionException" => "lib/ext/symphony/OptionsResolver/Exception/NoSuchOptionException.php",
		"Symfony\\Component\\OptionsResolver\\Exception\OptionDefinitionException" => "lib/ext/symphony/OptionsResolver/Exception/OptionDefinitionException.php",
		"Symfony\\Component\\OptionsResolver\\Exception\UndefinedOptionsException" => "lib/ext/symphony/OptionsResolver/Exception/UndefinedOptionsException.php",

		"Symfony\\Component\\Process\\ExecutableFinder" => "lib/ext/symphony/Process/ExecutableFinder.php",
		"Symfony\\Component\\Process\\InputStream" => "lib/ext/symphony/Process/InputStream.php",
		"Symfony\\Component\\Process\\PhpExecutableFinder" => "lib/ext/symphony/Process/PhpExecutableFinder.php",
		"Symfony\\Component\\Process\\PhpProcess" => "lib/ext/symphony/Process/PhpProcess.php",
		"Symfony\\Component\\Process\\Process" => "lib/ext/symphony/Process/Process.php",
		"Symfony\\Component\\Process\\ProcessUtils" => "lib/ext/symphony/Process/ProcessUtils.php",
		"Symfony\\Component\\Process\\Exception\ExceptionInterface" => "lib/ext/symphony/Process/Exception/ExceptionInterface.php",
		"Symfony\\Component\\Process\\Exception\InvalidArgumentException" => "lib/ext/symphony/Process/Exception/InvalidArgumentException.php",
		"Symfony\\Component\\Process\\Exception\LogicException" => "lib/ext/symphony/Process/Exception/LogicException.php",
		"Symfony\\Component\\Process\\Exception\ProcessFailedException" => "lib/ext/symphony/Process/Exception/ProcessFailedException.php",
		"Symfony\\Component\\Process\\Exception\ProcessSignaledException" => "lib/ext/symphony/Process/Exception/ProcessSignaledException.php",
		"Symfony\\Component\\Process\\Exception\ProcessTimedOutException" => "lib/ext/symphony/Process/Exception/ProcessTimedOutException.php",
		"Symfony\\Component\\Process\\Exception\RuntimeException" => "lib/ext/symphony/Process/Exception/RuntimeException.php",
		"Symfony\\Component\\Process\\Pipes\PipesInterface" => "lib/ext/symphony/Process/Pipes/PipesInterface.php",
		"Symfony\\Component\\Process\\Pipes\AbstractPipes" => "lib/ext/symphony/Process/Pipes/AbstractPipes.php",
		"Symfony\\Component\\Process\\Pipes\UnixPipes" => "lib/ext/symphony/Process/Pipes/UnixPipes.php",
		"Symfony\\Component\\Process\\Pipes\WindowsPipes" => "lib/ext/symphony/Process/Pipes/WindowsPipes.php",

		"AMOPT_Mobile_Detect" => "lib/ext/mobiledetect/Mobile_Detect.php",

		"Sabberworm\\CSS\\OutputFormat" => "lib/ext/Sabberworm/CSS/OutputFormat.php",
		"Sabberworm\\CSS\\Parser" => "lib/ext/Sabberworm/CSS/Parser.php",
		"Sabberworm\\CSS\\Renderable" => "lib/ext/Sabberworm/CSS/Renderable.php",
		"Sabberworm\\CSS\\Settings" => "lib/ext/Sabberworm/CSS/Settings.php",
		"Sabberworm\\CSS\\Comment\\Comment" => "lib/ext/Sabberworm/CSS/Comment/Comment.php",
		"Sabberworm\\CSS\\Comment\\Commentable" => "lib/ext/Sabberworm/CSS/Comment/Commentable.php",
		"Sabberworm\\CSS\\CSSList\\AtRuleBlockList" => "lib/ext/Sabberworm/CSS/CSSList/AtRuleBlockList.php",
		"Sabberworm\\CSS\\CSSList\\CSSBlockList" => "lib/ext/Sabberworm/CSS/CSSList/CSSBlockList.php",
		"Sabberworm\\CSS\\CSSList\\CSSList" => "lib/ext/Sabberworm/CSS/CSSList/CSSList.php",
		"Sabberworm\\CSS\\CSSList\\Document" => "lib/ext/Sabberworm/CSS/CSSList/Document.php",
		"Sabberworm\\CSS\\CSSList\\KeyFrame" => "lib/ext/Sabberworm/CSS/CSSList/KeyFrame.php",
		"Sabberworm\\CSS\\Parsing\\OutputException" => "lib/ext/Sabberworm/CSS/Parsing/OutputException.php",
		"Sabberworm\\CSS\\Parsing\\ParserState" => "lib/ext/Sabberworm/CSS/Parsing/ParserState.php",
		"Sabberworm\\CSS\\Parsing\\SourceException" => "lib/ext/Sabberworm/CSS/Parsing/SourceException.php",
		"Sabberworm\\CSS\\Parsing\\UnexpectedTokenException" => "lib/ext/Sabberworm/CSS/Parsing/UnexpectedTokenException.php",
		"Sabberworm\\CSS\\Parsing\\UnexpectedEOFException" => "lib/ext/Sabberworm/CSS/Parsing/UnexpectedEOFException.php",
		"Sabberworm\\CSS\\Property\\AtRule" => "lib/ext/Sabberworm/CSS/Property/AtRule.php",
		"Sabberworm\\CSS\\Property\\Charset" => "lib/ext/Sabberworm/CSS/Property/Charset.php",
		"Sabberworm\\CSS\\Property\\CSSNamespace" => "lib/ext/Sabberworm/CSS/Property/CSSNamespace.php",
		"Sabberworm\\CSS\\Property\\Import" => "lib/ext/Sabberworm/CSS/Property/Import.php",
		"Sabberworm\\CSS\\Property\\Selector" => "lib/ext/Sabberworm/CSS/Property/Selector.php",
		"Sabberworm\\CSS\\Property\\KeyframeSelector" => "lib/ext/Sabberworm/CSS/Property/KeyframeSelector.php",
		"Sabberworm\\CSS\\Rule\\Rule" => "lib/ext/Sabberworm/CSS/Rule/Rule.php",
		"Sabberworm\\CSS\\RuleSet\\AtRuleSet" => "lib/ext/Sabberworm/CSS/RuleSet/AtRuleSet.php",
		"Sabberworm\\CSS\\RuleSet\\DeclarationBlock" => "lib/ext/Sabberworm/CSS/RuleSet/DeclarationBlock.php",
		"Sabberworm\\CSS\\RuleSet\\RuleSet" => "lib/ext/Sabberworm/CSS/RuleSet/RuleSet.php",
		"Sabberworm\\CSS\\Value\\CalcFunction" => "lib/ext/Sabberworm/CSS/Value/CalcFunction.php",
		"Sabberworm\\CSS\\Value\\CalcRuleValueList" => "lib/ext/Sabberworm/CSS/Value/CalcRuleValueList.php",
		"Sabberworm\\CSS\\Value\\Color" => "lib/ext/Sabberworm/CSS/Value/Color.php",
		"Sabberworm\\CSS\\Value\\CSSFunction" => "lib/ext/Sabberworm/CSS/Value/CSSFunction.php",
		"Sabberworm\\CSS\\Value\\CSSString" => "lib/ext/Sabberworm/CSS/Value/CSSString.php",
		"Sabberworm\\CSS\\Value\\LineName" => "lib/ext/Sabberworm/CSS/Value/LineName.php",
		"Sabberworm\\CSS\\Value\\PrimitiveValue" => "lib/ext/Sabberworm/CSS/Value/PrimitiveValue.php",
		"Sabberworm\\CSS\\Value\\RuleValueList" => "lib/ext/Sabberworm/CSS/Value/RuleValueList.php",
		"Sabberworm\\CSS\\Value\\Size" => "lib/ext/Sabberworm/CSS/Value/Size.php",
		"Sabberworm\\CSS\\Value\\URL" => "lib/ext/Sabberworm/CSS/Value/URL.php",
		"Sabberworm\\CSS\\Value\\Value" => "lib/ext/Sabberworm/CSS/Value/Value.php",
		"Sabberworm\\CSS\\Value\\ValueList" => "lib/ext/Sabberworm/CSS/Value/ValueList.php",
	)
);

CModule::AddAutoloadClasses(
	"ammina.optimizer",
	array(
		"Ammina\\Optimizer\\Core2\\Settings" => "lib/core2/settings.php",
		"Ammina\\Optimizer\\Core2\\LibAvailable" => "lib/core2/lib.available.php",
		"Ammina\\Optimizer\\Core2\\Application" => "lib/core2/application.php",
		"Ammina\\Optimizer\\Core2\\AppBackground" => "lib/core2/appbackground.php",
		"Ammina\\Optimizer\\Core2\\Parser\\Base" => "lib/core2/parser/base.php",
		"Ammina\\Optimizer\\Core2\\Parser\\PHPParser" => "lib/core2/parser/phpparser.php",
		"Ammina\\Optimizer\\Core2\\Parser\\DOMParser" => "lib/core2/parser/domparser.php",
		"Ammina\\Optimizer\\Core2\\Optimizer\\CSS" => "lib/core2/optimizer/css.php",
		"Ammina\\Optimizer\\Core2\\Optimizer\\JS" => "lib/core2/optimizer/js.php",
		"Ammina\\Optimizer\\Core2\\Optimizer\\Image" => "lib/core2/optimizer/image.php",
		"Ammina\\Optimizer\\Core2\\Optimizer\\Html" => "lib/core2/optimizer/html.php",
		"Ammina\\Optimizer\\Core2\\Optimizer\\Delay" => "lib/core2/optimizer/delay.php",
		"Ammina\\Optimizer\\Core2\\Optimizer\\Image\\Base" => "lib/core2/optimizer/image/base.php",
		"Ammina\\Optimizer\\Core2\\Optimizer\\Image\\Jpg" => "lib/core2/optimizer/image/jpg.php",
		"Ammina\\Optimizer\\Core2\\Optimizer\\Image\\Png" => "lib/core2/optimizer/image/png.php",
		"Ammina\\Optimizer\\Core2\\Optimizer\\Image\\Gif" => "lib/core2/optimizer/image/gif.php",
		"Ammina\\Optimizer\\Core2\\Optimizer\\Image\\Svg" => "lib/core2/optimizer/image/svg.php",
		"Ammina\\Optimizer\\Core2\\Optimizer\\Image\\WebP" => "lib/core2/optimizer/image/webp.php",
		"Ammina\\Optimizer\\Core2\\Optimizer\\Image\\Lazy" => "lib/core2/optimizer/image/lazy.php",
	)
);

CModule::AddAutoloadClasses(
	"ammina.optimizer",
	array(
		"Ammina\\Optimizer\\Core2\\Driver\\Css\\Base" => "lib/core2/driver/css/base.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Css\\PHPWee" => "lib/core2/driver/css/phpwee.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Css\\MatthiasMullie" => "lib/core2/driver/css/matthiasmullie.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Css\\UglifyCss" => "lib/core2/driver/css/uglifycss.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Css\\YUICompressor" => "lib/core2/driver/css/yuicompressor.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Css\\Ammina" => "lib/core2/driver/css/ammina.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Css\\AmminaUglifyCss" => "lib/core2/driver/css/amminauglifycss.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Css\\AmminaYUICompressor" => "lib/core2/driver/css/amminayuicompressor.php",

		"Ammina\\Optimizer\\Core2\\Driver\\Image\\Base" => "lib/core2/driver/image/base.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Image\\Imagick" => "lib/core2/driver/image/imagick.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Image\\JpegOptim" => "lib/core2/driver/image/jpegoptim.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Image\\PngQuant" => "lib/core2/driver/image/pngquant.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Image\\OptiPng" => "lib/core2/driver/image/optipng.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Image\\GifSicle" => "lib/core2/driver/image/gifsicle.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Image\\Svgo" => "lib/core2/driver/image/svgo.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Image\\PhpGD" => "lib/core2/driver/image/phpgd.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Image\\CWebP" => "lib/core2/driver/image/cwebp.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Image\\Ammina" => "lib/core2/driver/image/ammina.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Image\\AmminaImagick" => "lib/core2/driver/image/amminaimagick.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Image\\AmminaJpegOptim" => "lib/core2/driver/image/amminajpegoptim.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Image\\AmminaPngQuant" => "lib/core2/driver/image/amminapngquant.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Image\\AmminaOptiPng" => "lib/core2/driver/image/amminaoptipng.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Image\\AmminaGifSicle" => "lib/core2/driver/image/amminagifsicle.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Image\\AmminaSvgo" => "lib/core2/driver/image/amminasvgo.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Image\\AmminaCWebP" => "lib/core2/driver/image/amminacwebp.php",

		"Ammina\\Optimizer\\Core2\\Driver\\Js\\Base" => "lib/core2/driver/js/base.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Js\\PHPWee" => "lib/core2/driver/js/phpwee.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Js\\MatthiasMullie" => "lib/core2/driver/js/matthiasmullie.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Js\\UglifyJs" => "lib/core2/driver/js/uglifyjs.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Js\\UglifyJs2" => "lib/core2/driver/js/uglifyjs2.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Js\\TerserJs" => "lib/core2/driver/js/terserjs.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Js\\BabelMinify" => "lib/core2/driver/js/babelminify.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Js\\YUICompressor" => "lib/core2/driver/js/yuicompressor.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Js\\Ammina" => "lib/core2/driver/js/ammina.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Js\\AmminaUglifyJs" => "lib/core2/driver/js/amminauglifyjs.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Js\\AmminaUglifyJs2" => "lib/core2/driver/js/amminauglifyjs2.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Js\\AmminaTerserJs" => "lib/core2/driver/js/amminaterserjs.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Js\\AmminaBabelMinify" => "lib/core2/driver/js/amminababelminify.php",
		"Ammina\\Optimizer\\Core2\\Driver\\Js\\AmminaYUICompressor" => "lib/core2/driver/js/amminayuicompressor.php",
	)
);

if (!class_exists('Bitrix\\Main\\ORM\\Event', true)) {
	class_alias('Bitrix\\Main\\Entity\\Event', 'Bitrix\\Main\\ORM\\Event');
	class_alias('Bitrix\\Main\\Entity\\EventResult', 'Bitrix\\Main\\ORM\\EventResult');
	class_alias('Bitrix\\Main\\Entity\\DataManager', 'Bitrix\\Main\\ORM\\Data\\DataManager');
	class_alias('Bitrix\\Main\\Entity\\Result', 'Bitrix\\Main\\ORM\\Data\\Result');
	class_alias('Bitrix\\Main\\Entity\\AddResult', 'Bitrix\\Main\\ORM\\Data\\AddResult');
	class_alias('Bitrix\\Main\\Entity\\UpdateResult', 'Bitrix\\Main\\ORM\\Data\UpdateResult');
	class_alias('Bitrix\\Main\\Entity\\DeleteResult', 'Bitrix\\Main\\ORM\\Data\\DeleteResult');
	class_alias('Bitrix\\Main\\Entity\\ExpressionField', 'Bitrix\\Main\\ORM\\Fields\\ExpressionField');
}
if (!class_exists('Bitrix\\Main\\Composite\\Engine', true)) {
	class_alias("Bitrix\\Main\\Page\\Frame", "Bitrix\\Main\\Composite\\Engine");
}
if (!class_exists('Bitrix\\Main\\Composite\\Helper', true)) {
	class_alias("CHTMLPagesCache", "Bitrix\\Main\\Composite\\Helper");
}
if (!class_exists('Bitrix\\Main\\Composite\\StaticArea', true)) {
	class_alias("Bitrix\\Main\\Page\\FrameStatic", "Bitrix\\Main\\Composite\\StaticArea");
}
if (!class_exists('Bitrix\\Main\\Composite\\Page', true)) {
	class_alias("Bitrix\\Main\\Data\\StaticHtmlCache", "Bitrix\\Main\\Composite\\Page");
}

if (!function_exists("myPrint")) {
	function myPrint(&$Var, $bIsHtmlSpecialChars = true, $strFileName = false, $bAppend = false)
	{
		if (defined("AMMINA_CRON_UNIQ_IDENT") && $strFileName === false) {
			$bIsHtmlSpecialChars = false;
		}
		if ($strFileName) {
			ob_start();
		}
		echo '<pre style="text-align:left;background-color:#222222;color:#ffffff;font-size:11px;">';
		if ($bIsHtmlSpecialChars) {
			echo htmlspecialchars(print_r($Var, true));
		} else {
			print_r($Var);
		}
		echo '</pre>';
		if ($strFileName) {
			$c = ob_get_contents();
			ob_end_clean();
			if ($bAppend) {
				file_put_contents($strFileName, file_get_contents($strFileName) . "\n" . $c);
			} else {
				file_put_contents($strFileName, $c);
			}
		}
	}
}

if (!function_exists("amminainclassxpath")) {
	function amminainclassxpath($str, $substr)
	{
		if (amopt_stripos($str, $substr) !== false) {
			return "Y";
		}
		return "N";
	}
}

if (!function_exists("ammina_pathinfo")) {
	function ammina_pathinfo($path, $options = null)
	{
		if (COption::GetOptionString("ammina.optimizer", "use_ammina_pathinfo", "Y") == "Y") {
			$arResult = array(
				"dirname" => false,
				"basename" => false,
				"extension" => false,
				"filename" => false,
			);
			$ar = explode("/", $path);
			$old = array_pop($ar);
			if (amopt_strlen($old) <= 0) {
				$old = array_pop($ar);
			}
			$arResult['basename'] = $old;
			if (amopt_strlen($path) <= 0) {
				$arResult['dirname'] = "";
			} elseif ($path == "/") {
				$arResult['dirname'] = "/";
			} elseif (count($ar) <= 0) {
				$arResult['dirname'] = ".";
			} elseif (count($ar) == 1) {
				$ar[] = "";
				$arResult['dirname'] = implode("/", $ar);
			} else {
				$arResult['dirname'] = implode("/", $ar);
			}
			$arFile = explode(".", $arResult['basename']);
			if (count($arFile) > 1) {
				$arResult['extension'] = $arFile[count($arFile) - 1];
				unset($arFile[count($arFile) - 1]);
			}
			$arResult['filename'] = implode(".", $arFile);
		} else {
			$arResult = pathinfo($path);
		}
		if (!is_null($options)) {
			if (!($options & PATHINFO_DIRNAME)) {
				unset($arResult['dirname']);
			}
			if (!($options & PATHINFO_BASENAME)) {
				unset($arResult['basename']);
			}
			if (!($options & PATHINFO_EXTENSION)) {
				unset($arResult['extension']);
			}
			if (!($options & PATHINFO_FILENAME)) {
				unset($arResult['filename']);
			}
			if (count($arResult) == 1) {
				$ak = array_keys($arResult);
				$arResult = $arResult[$ak[0]];
			}
		}
		return $arResult;
	}
}

if (!function_exists("ammina_JsObjectToPhp")) {
	function ammina_JsObjectToPhp($data, $bSkipNative = false)
	{
		global $APPLICATION;
		$arResult = array();
		if (function_exists('json_decode') && COption::GetOptionString("ammina.optimizer", "use_json_decode", "Y") == "Y") {
			$data = str_replace("\t", "\\t", $data);
			$arTest = explode("'", $data);
			$arNewTest = array();
			foreach ($arTest as $k => $v) {
				if ($k > 0) {
					$iPrev = count($arNewTest) - 1;
					$iLengthPrev = amopt_strlen($arNewTest[$iPrev]);
					$strOldCharPrev = amopt_substr($arNewTest[$iPrev], $iLengthPrev - 1, 1);
					if ($strOldCharPrev == "\\") {
						$arNewTest[$iPrev] = amopt_substr($arNewTest[$iPrev], 0, $iLengthPrev - 1) . "'" . $v;
					} else {
						$arNewTest[] = $v;
					}
				} else {
					$arNewTest[] = $v;
				}
			}
			$data = implode("\"", $arNewTest);
			if (!defined("BX_UTF") || BX_UTF !== true) {
				$data = $APPLICATION->ConvertCharset($data, (amopt_strlen(LANG_CHARSET) > 0 ? LANG_CHARSET : SITE_CHARSET), "UTF-8");
			}
			$arResult = json_decode($data, true);
			if (!defined("BX_UTF") || BX_UTF !== true) {
				$arResult = $APPLICATION->ConvertCharsetArray($arResult, "UTF-8", (amopt_strlen(LANG_CHARSET) > 0 ? LANG_CHARSET : SITE_CHARSET));
			}
		} else {
			$arResult = \CUtil::JsObjectToPhp($data, $bSkipNative);
		}
		return $arResult;
	}
}

?>