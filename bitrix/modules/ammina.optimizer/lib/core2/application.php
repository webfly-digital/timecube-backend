<?

namespace Ammina\Optimizer\Core2;

use Ammina\Optimizer\Core2\Optimizer\CSS;
use Ammina\Optimizer\Core2\Optimizer\Delay;
use Ammina\Optimizer\Core2\Optimizer\Html;
use Ammina\Optimizer\Core2\Optimizer\Image;
use Ammina\Optimizer\Core2\Optimizer\JS;
use Ammina\Optimizer\Core2\Parser\Base;
use Bitrix\Main\Composite\BufferArea;
use Bitrix\Main\Composite\Engine;
use Bitrix\Main\Composite\Helper;
use Bitrix\Main\Composite\Page;
use Bitrix\Main\Composite\StaticArea;
use Bitrix\Main\IO\Path;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Page\AssetMode;
use Bitrix\Main\Web\Json;
use CBXVirtualIo;
use CDiskQuota;
use CFile;
use COption;
use CTempFile;
use CUpdateClientPartner;
use CUtil;
use Imagick;

class Application
{
	const REQUEST_TYPE_UNKNOWN = 0;
	const REQUEST_TYPE_HTML = 1;
	const REQUEST_TYPE_AJAX = 2;
	const REQUEST_TYPE_JSON = 3;
	const REQUEST_TYPE_COMPONENT_AJAX = 4;
	const REQUEST_TYPE_COMPONENT_CACHE = 5;
	const REQUEST_TYPE_AUTOCOMPOSITE = 6;
	const REQUEST_TYPE_COMPOSITE = 7;

	/**
	 * @var Application
	 */
	protected static $_instance = null;

	protected $startTime = false;
	protected $endTime = false;
	protected $totalTime = false;

	protected $startTimeParsing = false;
	protected $endTimeParsing = false;
	protected $totalTimeParsing = false;

	protected $startTimeImageOptimize = false;
	protected $endTimeImageOptimize = false;
	protected $totalTimeImageOptimize = false;

	protected $startTimeCssOptimize = false;
	protected $endTimeCssOptimize = false;
	protected $totalTimeCssOptimize = false;

	protected $startTimeDelayOptimize = false;
	protected $endTimeDelayOptimize = false;
	protected $totalTimeDelayOptimize = false;

	protected $startTimeJsOptimize = false;
	protected $endTimeJsOptimize = false;
	protected $totalTimeJsOptimize = false;

	protected $startTimeHtmlOptimize = false;
	protected $endTimeHtmlOptimize = false;
	protected $totalTimeHtmlOptimize = false;

	protected $startTimeMakeHeadersLink = false;
	protected $endTimeMakeHeadersLink = false;
	protected $totalTimeMakeHeadersLink = false;

	protected $startTimeMakeHtml = false;
	protected $endTimeMakeHtml = false;
	protected $totalTimeMakeHtml = false;

	protected $startMemory = false;
	protected $endMemory = false;
	protected $totalMemory = false;
	protected $isMobileDevice = false;
	/**
	 * @var Base
	 */
	protected $oParser = null;
	/**
	 * @var \AMOPT_Mobile_Detect
	 */
	protected $oDetector = null;
	protected $arOptions = array();
	protected $iRequestType = self::REQUEST_TYPE_UNKNOWN;
	protected $strClearCache = false;
	/**
	 * @var CSS
	 */
	protected $oCSSOptimizer = null;
	/**
	 * @var JS
	 */
	protected $oJSOptimizer = null;
	/**
	 * @var Image
	 */
	protected $oImageOptimizer = null;
	/**
	 * @var Html
	 */
	protected $oHtmlOptimizer = null;
	/**
	 * @var Delay
	 */
	protected $oDelayOptimizer = null;
	protected $bSupportWebP = false;
	protected $bPreventSupportWebP = false;
	protected $strBrowser = "";
	protected $strBrowserVersion = "";
	public $arSupportFontTypes = array();
	protected $arSupportHeaders = array();

	protected $arRequestStack = array();
	protected $iMaxStackPackageImages = 200;
	protected $bMakeHeadersComposite = false;

	/**
	 * @return Application
	 */
	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	private function __construct()
	{
		global $APPLICATION;
		if (isset($_REQUEST['amopt_showstat'])) {
			if ($_REQUEST['amopt_showstat'] == "Y") {
				$_SESSION['AMOPT_SHOWSTAT'] = true;
			} else {
				unset($_SESSION['AMOPT_SHOWSTAT']);
			}
		}
		if (isset($_REQUEST['amopt_stop'])) {
			$secretKey = $this->getSecretKeyStop();
			if ($_REQUEST['amopt_stop'] == $secretKey) {
				$_SESSION['AMOPT_STOP'] = true;
			} else {
				unset($_SESSION['AMOPT_STOP']);
			}
		}
		$this->strClearCache = false;
		if (isset($_REQUEST['amopt_clear_cache'])) {
			if ($_REQUEST['amopt_clear_cache'] == "Y") {
				$this->strClearCache = true;
			} elseif ($_REQUEST['amopt_clear_cache'] == "css") {
				$this->strClearCache = "css";
			} elseif ($_REQUEST['amopt_clear_cache'] == "js") {
				$this->strClearCache = "js";
			} elseif ($_REQUEST['amopt_clear_cache'] == "image") {
				$this->strClearCache = "image";
			}
		}
		$this->oDetector = new \AMOPT_Mobile_Detect();
		if ($this->oDetector->isMobile() || $this->oDetector->isTablet()) {
			$this->arOptions = \Ammina\Optimizer\SettingsTable::getSettings(SITE_ID, "m");
			$this->isMobileDevice = true;
		} else {
			$this->arOptions = \Ammina\Optimizer\SettingsTable::getSettings(SITE_ID, "d");
			$this->isMobileDevice = false;
		}
		if (isset($this->arOptions['PAGES']) && !empty($this->arOptions['PAGES'])) {
			$bSetOptions = false;
			foreach ($this->arOptions['PAGES'] as $k => $arPage) {
				if ($arPage['page']['ACTIVE'] == "Y") {
					if (\CAmminaOptimizer::doMathPageToRules($arPage['page']['PAGES'], $APPLICATION->GetCurPage(true))) {
						$bSetOptions = true;
						$this->arOptions = $arPage;
						break;
					}
				}
			}
			if (!$bSetOptions) {
				$this->arOptions = $this->arOptions['MAIN'];
			}
		} else {
			$this->arOptions = $this->arOptions['MAIN'];
		}
		if (!empty($this->arOptions)) {
			if ($this->arOptions['category']['main']['options']['ACTIVE'] == "Y" && \CAmminaOptimizer::isAllowPageOptimize()) {
				if ($this->arOptions['category']['main']['groups']['other']['options']['DISABLE_MAIN_JOIN_CSS'] == "Y") {
					if (method_exists(Asset::getInstance(), "disableOptimizeCss")) {
						Asset::getInstance()->disableOptimizeCss();
					}
				}
				if ($this->arOptions['category']['main']['groups']['other']['options']['DISABLE_MAIN_JOIN_JS'] == "Y") {
					if (method_exists(Asset::getInstance(), "disableOptimizeJs")) {
						if (!in_array($this->arOptions['category']['js']['groups']['ext']['options']['JOIN_MODEL'], array("notjoin", "onlypreload"))) {
							Asset::getInstance()->disableOptimizeJs();
						}
					}
				}
				if ($this->arOptions['category']['main']['groups']['other']['options']['DISABLE_MAIN_MOVE_JS'] == "Y") {
					if (method_exists(Asset::getInstance(), "setJsToBody")) {
						Asset::getInstance()->setJsToBody(false);
					}
				}
				\CAmminaOptimizer::doCheckCDN();
			}
		}

		$this->oCSSOptimizer = new CSS($this->arOptions['category']['css']['groups']);
		$this->oJSOptimizer = new JS($this->arOptions['category']['js']['groups']);
		$this->oImageOptimizer = new Image($this->arOptions['category']['images']['groups']);
		$this->oHtmlOptimizer = new Html($this->arOptions['category']['html']['groups']);
		$this->oDelayOptimizer = new Delay($this->arOptions['category']['main']['groups']['delay']['options'],$this->arOptions['category']['main']['groups']['other']['options']['MOVE_JS_BXSTAT_TOP']==="Y");
		$this->doCheckBrowserFeatures();
		/*
		if ($this->bSupportWebP) {
			$strWebP = "iswebp";
			if (amopt_strpos($_SERVER["REQUEST_URI"], '?') === false) {
				$_SERVER["REQUEST_URI"] .= "?" . $strWebP;
			} else {
				$_SERVER["REQUEST_URI"] .= "&" . $strWebP;
			}
		}
		*/
	}

	public function getSecretKeyStop()
	{
		$secretKey = COption::GetOptionString("ammina.optimizer", "stopseckey", "");
		if (amopt_strlen($secretKey) <= 0) {
			$secretKey = randString(10);
			COption::SetOptionString("ammina.optimizer", "stopseckey", $secretKey);
		}
		return amopt_substr(md5(date("dmYH") . $secretKey), 0, 10);
	}

	public function doRemoveCompositeWebpFlag()
	{
		global $APPLICATION;
		foreach ($_GET as $k => $v) {
			if ($k == "iswebp") {
				unset($_GET[$k]);
			}
		}
		if (amopt_strpos($_SERVER["REQUEST_URI"], '&iswebp') !== false) {
			$_SERVER["REQUEST_URI"] = str_replace("&iswebp=0", "", $_SERVER["REQUEST_URI"]);
			$_SERVER["REQUEST_URI"] = str_replace("&iswebp=1", "", $_SERVER["REQUEST_URI"]);
			$_SERVER["REQUEST_URI"] = str_replace("&iswebp=", "", $_SERVER["REQUEST_URI"]);
			$_SERVER["REQUEST_URI"] = str_replace("&iswebp", "", $_SERVER["REQUEST_URI"]);
		}
		if (amopt_strpos($_SERVER["QUERY_STRING"], '&iswebp') !== false) {
			$_SERVER["QUERY_STRING"] = str_replace("&iswebp=0", "", $_SERVER["QUERY_STRING"]);
			$_SERVER["QUERY_STRING"] = str_replace("&iswebp=1", "", $_SERVER["QUERY_STRING"]);
			$_SERVER["QUERY_STRING"] = str_replace("&iswebp=", "", $_SERVER["QUERY_STRING"]);
			$_SERVER["QUERY_STRING"] = str_replace("&iswebp", "", $_SERVER["QUERY_STRING"]);
		}
		if (amopt_strpos($_SERVER["QUERY_STRING"], 'iswebp') !== false) {
			$_SERVER["QUERY_STRING"] = str_replace("iswebp=0", "", $_SERVER["QUERY_STRING"]);
			$_SERVER["QUERY_STRING"] = str_replace("iswebp=1", "", $_SERVER["QUERY_STRING"]);
			$_SERVER["QUERY_STRING"] = str_replace("iswebp=", "", $_SERVER["QUERY_STRING"]);
			$_SERVER["QUERY_STRING"] = str_replace("iswebp", "", $_SERVER["QUERY_STRING"]);
		}
		if (amopt_strpos($_SERVER["REQUEST_URI"], '?iswebp') !== false) {
			$_SERVER["REQUEST_URI"] = str_replace("?iswebp=0", "", $_SERVER["REQUEST_URI"]);
			$_SERVER["REQUEST_URI"] = str_replace("?iswebp=1", "", $_SERVER["REQUEST_URI"]);
			$_SERVER["REQUEST_URI"] = str_replace("?iswebp=", "", $_SERVER["REQUEST_URI"]);
			$_SERVER["REQUEST_URI"] = str_replace("?iswebp", "", $_SERVER["REQUEST_URI"]);
		}
		$APPLICATION->reinitPath();
		$server = \Bitrix\Main\Context::getCurrent()->getServer();
		$arServer = $server->toArray();
		$arServer['REQUEST_URI'] = $_SERVER["REQUEST_URI"];
		$server->set($arServer);
	}

	public function doSetCompositeWebpFlag()
	{
		global $APPLICATION;

		if ($this->bSupportWebP && $this->arOptions['category']['images']['groups']['webp_files']['options']['ACTIVE'] == "Y") {
			$strWebP = "iswebp";
			if (amopt_strpos($_SERVER["REQUEST_URI"], "&" . $strWebP) === false && strpos($_SERVER["REQUEST_URI"], "?" . $strWebP) === false) {
				if (amopt_strpos($_SERVER["REQUEST_URI"], '?') === false) {
					$_SERVER["REQUEST_URI"] .= "?" . $strWebP;
				} else {
					$_SERVER["REQUEST_URI"] .= "&" . $strWebP;
				}
			}
			if (!isset($_GET['iswebp'])) {
				$_GET['iswebp'] = false;
			}
			$APPLICATION->reinitPath();
			$server = \Bitrix\Main\Context::getCurrent()->getServer();
			$arServer = $server->toArray();
			$arServer['REQUEST_URI'] = $_SERVER["REQUEST_URI"];
			$server->set($arServer);

			$this->doReinitCompositePage();
		}
	}

	protected function doReinitCompositePage()
	{
		$reinitInstance = static function () {
			if (isset(static::$instance)) {
				static::$instance = null;
				$oInstance = self::getInstance();
			}
		};
		$bsetReinitInstance = \Closure::bind($reinitInstance, null, '\Bitrix\Main\Composite\Page');
		$bsetReinitInstance();
	}

	private function __clone()
	{
	}

	private function __wakeup()
	{
		throw new \Exception("Cannot unserialize a singleton.");
	}

	public function isAllowComposite()
	{
		return ($this->arOptions['category']['main']['groups']['request']['options']['ACTIVE_AUTOCOMPOSITE'] == "Y");
	}

	/**
	 * @return Base
	 */
	public function getParser()
	{
		return $this->oParser;
	}

	protected function fillRequestType(&$strContent)
	{
		$bFullJsonDetect = true;
		$bIsJsonFormat = false;
		$bIsHtmlFormat = false;

		$iStartTag = amopt_strpos($strContent, '<');
		if ($iStartTag !== false) {
			$sub = trim(amopt_substr($strContent, 0, $iStartTag));
			if (amopt_strlen($sub) <= 0) {
				$bFullJsonDetect = false;
				$bIsHtmlFormat = true;
			}
		}
		if ($bFullJsonDetect) {
			$arFullJson = @json_decode($strContent, true);
			if (is_object($arFullJson) || is_array($arFullJson)) {
				$bIsJsonFormat = true;
			}
		}
		$isRand = Helper::getAjaxRandom();
		if ($isRand !== false) {
			$isRand = true;
		}
		$strTestContent = amopt_strtoupper(amopt_substr($strContent, 0, 1000));

		if ($isRand && Helper::isAjaxRequest()) {
			$this->iRequestType = self::REQUEST_TYPE_AUTOCOMPOSITE;
		} elseif ($bIsJsonFormat) {
			$this->iRequestType = self::REQUEST_TYPE_JSON;
		} elseif (isset($_REQUEST['bxajaxid']) && amopt_strlen($_REQUEST['bxajaxid']) > 0) {
			$this->iRequestType = self::REQUEST_TYPE_COMPONENT_AJAX;
		} elseif (\Bitrix\Main\Application::getInstance()->getContext()->getRequest()->isAjaxRequest() || $_REQUEST['ajax'] == "yes" || $_REQUEST['ajax'] == "Y" || $_REQUEST['AJAX'] == "yes" || $_REQUEST['AJAX'] == "Y" || (defined("PUBLIC_AJAX_MODE") && PUBLIC_AJAX_MODE === true)) {
			$this->iRequestType = self::REQUEST_TYPE_AJAX;
		} elseif (amopt_strpos($strTestContent, '<!DOCTYPE') !== false || amopt_strpos($strTestContent, '<HTML') !== false) {
			$this->iRequestType = self::REQUEST_TYPE_HTML;
		}

		if ($this->iRequestType == self::REQUEST_TYPE_AUTOCOMPOSITE) {
			if (!$this->doCheckAutocompositeUpdateCache($strContent)) {
				$this->bMakeHeadersComposite = true;
			}
		} elseif ($this->iRequestType == self::REQUEST_TYPE_HTML) {
			if (Engine::getUseHTMLCache()) {
				$page = Page::getInstance();
				if ($page->isCacheable()) {
					$this->bMakeHeadersComposite = true;
				}
			}
		}
	}

	public function doCheckAutocompositeUpdateCache(&$strContent)
	{
		$bResult = false;
		/**
		 * @todo �������� ��������� ��� ��������� ��������
		 */
		//$dividedData = Engine::getDividedPageData($strContent);
		$htmlCacheChanged = false;
		if (Engine::getUseHTMLCache()) {
			$page = Page::getInstance();
			if ($page->isCacheable()) {
				$cacheExists = $page->exists();
				$rewriteCache = true;//$page->getMd5() !== $dividedData["md5"];
				if (Engine::getAutoUpdate() && Engine::getAutoUpdateTTL() > 0 && $cacheExists) {
					$mtime = $page->getLastModified();
					if ($mtime !== false && ($mtime + Engine::getAutoUpdateTTL()) > time()) {
						$rewriteCache = false;
					}
				}

				$invalidateCache = Engine::getAutoUpdate() === false && Engine::isInvalidationRequest();

				if (!$cacheExists || $rewriteCache || $invalidateCache) {
					$bResult = true;
				}
			}
		}
		return $bResult;
	}

	/**
	 * @param $strContent
	 *
	 * @throws \Bitrix\Main\SystemException
	 */
	public function EndContent(&$strContent)
	{
		$bAllowOptimize = true;
		if ($this->arOptions['category']['main']['options']['ACTIVE'] != "Y") {
			$bAllowOptimize = false;
		}
		if ($bAllowOptimize) {
			$this->fillRequestType($strContent);
		}
		$bUpdateCacheAutocomposite = false;
		if ($this->iRequestType == self::REQUEST_TYPE_AUTOCOMPOSITE) {
			$bUpdateCacheAutocomposite = $this->doCheckAutocompositeUpdateCache($strContent);
			if (!$bUpdateCacheAutocomposite) {
				return;
			}
		}
		$this->startTime = microtime(true);
		$this->startMemory = memory_get_usage();

		if ($bAllowOptimize) {
			//$this->fillRequestType($strContent);
			if ($this->iRequestType == self::REQUEST_TYPE_HTML || $bUpdateCacheAutocomposite) {
				if ($this->arOptions['category']['main']['groups']['request']['options']['ACTIVE_HTML'] != "Y") {
					$bAllowOptimize = false;
				}
			} elseif ($this->iRequestType == self::REQUEST_TYPE_JSON) {
				if ($this->arOptions['category']['main']['groups']['request']['options']['ACTIVE_JSON'] != "Y") {
					$bAllowOptimize = false;
				}
			} elseif ($this->iRequestType == self::REQUEST_TYPE_AJAX) {
				if ($this->arOptions['category']['main']['groups']['request']['options']['ACTIVE_AJAX'] != "Y") {
					$bAllowOptimize = false;
				}
			} elseif ($this->iRequestType == self::REQUEST_TYPE_COMPONENT_AJAX) {
				if ($this->arOptions['category']['main']['groups']['request']['options']['ACTIVE_COMPONENT_AJAX'] != "Y") {
					$bAllowOptimize = false;
				}
			} elseif ($this->iRequestType == self::REQUEST_TYPE_AUTOCOMPOSITE) {
				if ($this->arOptions['category']['main']['groups']['request']['options']['ACTIVE_AUTOCOMPOSITE'] != "Y") {
					$bAllowOptimize = false;
				}
			} else {
				$bAllowOptimize = false;
			}
		}
		$bAllowAutoHeaders = false;
		if ($bAllowOptimize) {
			$this->oParser = Base::createParser(
				$this->arOptions['category']['main']['groups']['parse']['options']['LIBRARY'],
				$this->arOptions['category']['main']['groups']['parse']['options']['CHECK_NOTVALID_START_TAG'] == "Y",
				array(
					"REPLACE_BULLET" => $this->arOptions['category']['main']['groups']['other']['options']['REPLACE_BULLET'] == "Y",
					"CHECK_NOTVALID_UTF8_SYMBOLS" => $this->arOptions['category']['main']['groups']['parse']['options']['CHECK_NOTVALID_UTF8_SYMBOLS'] == "Y",
					"REPLACE_HTML_ENTITY" => $this->arOptions['category']['main']['groups']['other']['options']['REPLACE_HTML_ENTITY']
				)
			);
			if ($this->iRequestType == self::REQUEST_TYPE_HTML || $bUpdateCacheAutocomposite) {
				$this->doOptimizeHtml($strContent);
				$bAllowAutoHeaders = true;
			} elseif ($this->iRequestType == self::REQUEST_TYPE_JSON) {
				$this->doOptimizeJson($strContent);
				$bAllowAutoHeaders = true;
			} elseif ($this->iRequestType == self::REQUEST_TYPE_COMPONENT_AJAX) {
				$this->doOptimizeAjaxComponent($strContent);
				$bAllowAutoHeaders = true;
			} elseif ($this->iRequestType == self::REQUEST_TYPE_AJAX) {
				$this->doOptimizeAjax($strContent);
				$bAllowAutoHeaders = true;
			} elseif ($this->iRequestType == self::REQUEST_TYPE_AUTOCOMPOSITE) {
				$this->doOptimizeAutocomposite($strContent);
				$bAllowAutoHeaders = false;
			}
		}
		$this->doSendStackRequest();
		if ($bAllowAutoHeaders) {
			$this->doMakeHeaders();
		}

		$this->endTime = microtime(true);
		$this->totalTime = $this->endTime - $this->startTime;
		$this->endMemory = memory_get_peak_usage();
		$this->totalMemory = $this->endMemory - $this->startMemory;
		if ($this->isShowStat()) {
			$strStatContent = $this->doShowStat();
			$strContent = str_ireplace("</body", $strStatContent . '<link rel="stylesheet" href="/bitrix/css/ammina.optimizer/public.css"></body', $strContent);
		}
	}

	protected function getAllHeaders()
	{
		$arResult = array(
			"preload" => array(),
			"prefetch" => array(),
			"preconnect" => array(),
			"auto" => array(),
		);
		if ($this->arOptions['category']['other']['options']['ACTIVE'] == "Y") {
			//css
			foreach ($this->oCSSOptimizer->arHeadersPreloadFiles as $arHeader) {
				if (amopt_strlen($arHeader['FILE']) > 0 && $arHeader['TYPE'] == "STYLE") {
					$arResult['auto'][] = array(
						"href" => $arHeader['FILE'],
						"as" => "style",
					);
				}
			}
			//js
			foreach ($this->oJSOptimizer->arHeadersPreloadFiles as $arHeader) {
				if (amopt_strlen($arHeader['FILE']) > 0 && $arHeader['TYPE'] == "SCRIPT") {
					$arResult['auto'][] = array(
						"href" => $arHeader['FILE'],
						"as" => "script",
					);
				}
			}
			//critical font
			foreach ($this->oCSSOptimizer->arCriticalFonts as $arFont) {
				foreach ($this->arSupportFontTypes as $strFontType) {
					if (isset($arFont['srcbytype'][$strFontType])) {
						foreach ($arFont['srcbytype'][$strFontType] as $strFile) {
							if (amopt_strlen($strFile) > 0) {
								$arResult['auto'][] = array(
									"href" => $strFile,
									"as" => "font",
									"crossorigin" => "crossorigin",
								);
							}
						}
						break;
					}
				}
			}
			foreach ($this->oImageOptimizer->arHeadersPreloadFiles as $arHeader) {
				if (amopt_strlen($arHeader['FILE']) > 0 && $arHeader['TYPE'] == "IMAGE") {
					$arResult['auto'][] = array(
						"href" => $arHeader['FILE'],
						"as" => "image",
					);
				}
			}

			if ($this->iRequestType != self::REQUEST_TYPE_AJAX && $this->iRequestType != self::REQUEST_TYPE_JSON && /*$this->iRequestType != self::REQUEST_TYPE_AUTOCOMPOSITE && */ $this->iRequestType != self::REQUEST_TYPE_COMPONENT_AJAX) {
				if ($this->arOptions['category']['other']['groups']['headers']['options']['ACTIVE_PRELOAD'] == "Y") {
					{
						$arPreload = explode("\n", $this->arOptions['category']['other']['groups']['headers']['options']['PRELOAD']);
						foreach ($arPreload as $val) {
							$val = str_replace('&amp;', "#AMPSYMBOL#", $val);
							$val = trim($val);
							if (amopt_strlen($val) > 0) {
								$arVal = explode(";", $val);
								foreach ($arVal as $k => $v) {
									$arVal[$k] = str_replace("#AMPSYMBOL#", '&amp;', $v);
								}
								$arResult['preload'][] = array(
									"href" => $arVal[0],
									"as" => $arVal[1],
									"crossorigin" => $arVal[2],
								);
							}
						}
					}
				}
				if ($this->arOptions['category']['other']['groups']['headers']['options']['ACTIVE_PREFETCH'] == "Y") {
					{
						$arPrefetch = explode("\n", $this->arOptions['category']['other']['groups']['headers']['options']['PREFETCH']);
						foreach ($arPrefetch as $val) {
							$val = str_replace('&amp;', "#AMPSYMBOL#", $val);
							$val = trim($val);
							if (amopt_strlen($val) > 0) {
								$arVal = explode(";", $val);
								foreach ($arVal as $k => $v) {
									$arVal[$k] = str_replace("#AMPSYMBOL#", '&amp;', $v);
								}
								$arResult['prefetch'][] = array(
									"href" => $arVal[0],
									"as" => $arVal[1],
									"crossorigin" => $arVal[2],
								);
							}
						}
					}
				}
				if ($this->arOptions['category']['other']['groups']['headers']['options']['ACTIVE_PRECONNECT'] == "Y") {
					{
						$arPreconnect = explode("\n", $this->arOptions['category']['other']['groups']['headers']['options']['PRECONNECT']);
						foreach ($arPreconnect as $val) {
							$val = str_replace('&amp;', "#AMPSYMBOL#", $val);
							$val = trim($val);
							if (amopt_strlen($val) > 0) {
								$arVal = explode(";", $val);
								foreach ($arVal as $k => $v) {
									$arVal[$k] = str_replace("#AMPSYMBOL#", '&amp;', $v);
								}
								$arResult['preconnect'][] = array(
									"href" => $arVal[0],
									"crossorigin" => "crossorigin"
								);
							}
						}
					}
				}
			}
			if ($this->arOptions['category']['other']['groups']['headers']['options']['ACTIVE_USERAGENTS'] == "Y") {
				if (!in_array("preload", $this->arSupportHeaders)) {
					$arResult['preload'] = array();
				}
				if (!in_array("prefetch", $this->arSupportHeaders)) {
					$arResult['prefetch'] = array();
				}
				if (in_array("preload", $this->arSupportHeaders) && in_array("prefetch", $this->arSupportHeaders)) {
					$arResult['prefetch'] = array();
				}
				if (!in_array("preconnect", $this->arSupportHeaders)) {
					$arResult['preconnect'] = array();
				}
			}

			foreach ($arResult as $k => $v) {
				$arNew = array();
				foreach ($v as $v1) {
					$arNew[md5(serialize($v1))] = $v1;
				}
				$arResult[$k] = array_values($arNew);
			}
		}

		return $arResult;
	}

	public function doMakeHeaders()
	{
		if (!(($this->arOptions['category']['other']['groups']['links']['options']['ONLY_COMPOSITE'] == "Y" && Engine::isEnabled()) || $this->arOptions['category']['other']['groups']['links']['options']['ONLY_COMPOSITE'] != "Y")) {
			$arHeaders = $this->getAllHeaders();
			$arFillHeaders = array();
			foreach ($arHeaders['auto'] as $arHeader) {
				$arCommand = array(
					"<" . $arHeader['href'] . ">",
				);
				if (in_array("preload", $this->arSupportHeaders)) {
					$arCommand[] = 'rel=preload';
				} else {
					$arCommand[] = 'rel=prefetch';
				}
				if (isset($arHeader['as'])) {
					$arCommand[] = 'as=' . $arHeader['as'];
				}
				if (isset($arHeader['crossorigin'])) {
					$arCommand[] = 'crossorigin';
				}
				$arFillHeaders[] = implode("; ", $arCommand);
			}
			foreach ($arHeaders['preload'] as $arHeader) {
				$arCommand = array(
					"<" . $arHeader['href'] . ">",
					'rel=preload',
				);
				if (isset($arHeader['as'])) {
					$arCommand[] = 'as=' . $arHeader['as'];
				}
				if (isset($arHeader['crossorigin'])) {
					$arCommand[] = 'crossorigin';
				}
				$arFillHeaders[] = implode("; ", $arCommand);
			}
			foreach ($arHeaders['prefetch'] as $arHeader) {
				$arCommand = array(
					"<" . $arHeader['href'] . ">",
					'rel=prefetch',
				);
				if (isset($arHeader['as'])) {
					$arCommand[] = 'as=' . $arHeader['as'];
				}
				if (isset($arHeader['crossorigin'])) {
					$arCommand[] = 'crossorigin';
				}
				$arFillHeaders[] = implode("; ", $arCommand);
			}
			foreach ($arHeaders['preconnect'] as $arHeader) {
				$arCommand = array(
					"<" . $arHeader['href'] . ">",
					'rel=preconnect',
					"crossorigin",
				);
				$arFillHeaders[] = implode("; ", $arCommand);
			}
			$arFillHeaders = array_unique($arFillHeaders);
			foreach ($arFillHeaders as $strHeader) {
				header("Link: " . $strHeader, false);
			}
			/*if ($this->bMakeHeadersComposite) {
				$strFileName = Path::convertRelativeToAbsolute(\Bitrix\Main\Application::getPersonalRoot() . "/html_pages" . Page::getInstance()->getCacheKey() . ".nginx." . microtime(true) . ".conf");
				$arContent = array();
				foreach ($arFillHeaders as $strHeader) {
					$arContent[] = 'add_header \'Link\' \'' . $strHeader . '\';';
				}
				\CAmminaOptimizer::SaveFileContent($strFileName, implode("\n", $arContent));
			}*/
		}
	}

	public function doMakeHeadersLink()
	{
		$isEngineEnabled = false;
		if (class_exists("Bitrix\\Main\\Composite\\Engine")) {
			$isEngineEnabled = Engine::isEnabled();
		}
		if ($this->arOptions['category']['other']['options']['ACTIVE'] == "Y" && $this->arOptions['category']['other']['groups']['links']['options']['DELETE_OLD_LINKS'] == "Y") {
			$this->oParser->doRemoveLinkPreloadPrefetch();
		}
		if ($this->arOptions['category']['other']['options']['ACTIVE'] == "Y" && $this->arOptions['category']['other']['groups']['links']['options']['SET_LINKS'] == "Y") {
			if (($this->arOptions['category']['other']['groups']['links']['options']['ONLY_COMPOSITE'] == "Y" && $isEngineEnabled) || $this->arOptions['category']['other']['groups']['links']['options']['ONLY_COMPOSITE'] != "Y") {
				$arHeaders = $this->getAllHeaders();

				if (!is_object($this->oParser)) {
					return;
				}

				if (is_array($arHeaders['auto'])) {
					$arHeaders['auto'] = array_reverse($arHeaders['auto'], true);
				}
				if (is_array($arHeaders['preload'])) {
					$arHeaders['preload'] = array_reverse($arHeaders['preload'], true);
				}
				if (is_array($arHeaders['prefetch'])) {
					$arHeaders['prefetch'] = array_reverse($arHeaders['prefetch'], true);
				}
				if (is_array($arHeaders['preconnect'])) {
					$arHeaders['preconnect'] = array_reverse($arHeaders['preconnect'], true);
				}

				foreach ($arHeaders['auto'] as $arHeader) {
					$arCommand = array(
						"href" => $arHeader['href'],
					);
					if ($this->arOptions['category']['other']['groups']['links']['options']['LINKS_TYPE'] == "preload") {
						$arCommand['rel'] = "preload";
					} else {
						$arCommand['rel'] = "prefetch";
					}
					if (isset($arHeader['as'])) {
						$arCommand['as'] = $arHeader['as'];
					}
					if (isset($arHeader['crossorigin'])) {
						$arCommand['crossorigin'] = "anonymous";
					}
					$arCommand['href'] = htmlspecialchars($arCommand['href']);
					$this->oParser->AddTag("//head[1]", "link", $arCommand, "", true);
				}
				if ($this->arOptions['category']['other']['groups']['links']['options']['LINKS_TYPE'] == "preload") {
					foreach ($arHeaders['preload'] as $arHeader) {
						$arCommand = array(
							"href" => $arHeader['href'],
							"rel" => 'preload',
						);
						if (isset($arHeader['as'])) {
							$arCommand['as'] = $arHeader['as'];
						}
						if (isset($arHeader['crossorigin'])) {
							$arCommand['crossorigin'] = "anonymous";
						}
						$arCommand['href'] = htmlspecialchars($arCommand['href']);
						$this->oParser->AddTag("//head[1]", "link", $arCommand, "", true);
					}
				} else {
					foreach ($arHeaders['prefetch'] as $arHeader) {
						$arCommand = array(
							"href" => $arHeader['href'],
							"rel" => 'prefetch',
						);
						if (isset($arHeader['as'])) {
							$arCommand["as"] = $arHeader['as'];
						}
						if (isset($arHeader['crossorigin'])) {
							$arCommand['crossorigin'] = "anonymous";
						}
						$arCommand['href'] = htmlspecialchars($arCommand['href']);
						$this->oParser->AddTag("//head[1]", "link", $arCommand, "", true);
					}
				}
				foreach ($arHeaders['preconnect'] as $arHeader) {
					$arCommand = array(
						"href" => $arHeader['href'],
						"rel" => 'preconnect',
						"crossorigin" => "anonymous"
					);
					$arCommand['href'] = htmlspecialchars($arCommand['href']);
					$this->oParser->AddTag("//head[1]", "link", $arCommand, "", true);
				}
			}
		}
	}

	public function doCheckComponentCacheData(&$strContent)
	{
		$arResult = array();
		$iNext = 0;
		$arDataContent = array();
		$strSeparator = '<!-- component.ammina.optimizer.';
		$iPos = amopt_strpos($strContent, $strSeparator);
		while ($iPos !== false) {
			$iEndTagPos = amopt_strpos($strContent, " -->", $iPos);
			$strIdent = amopt_substr($strContent, $iPos + amopt_strlen($strSeparator), $iEndTagPos - $iPos - amopt_strlen($strSeparator));
			$arExpl = explode($strSeparator . $strIdent . ' -->', $strContent, 3);
			$strBlockContent = $arExpl[1];
			$arTmp = explode('<!-- component.critical.component.ammina.optimizer.' . $strIdent . ' ', $strBlockContent);
			$arTmp = explode(' -->', $arTmp[1]);
			$arCritical = unserialize($arTmp[0]);
			if ($this->isSupportWebP()) {
				$arBlock = explode(' <!-- with-webp component.ammina.optimizer.' . $strIdent . ' -->', $strBlockContent);
			} else {
				$arBlock = explode(' <!-- no-webp component.ammina.optimizer.' . $strIdent . ' -->', $strBlockContent);
			}
			$arResult[$strIdent] = array(
				"CONTENT" => $arBlock[1],
				"CRITICAL" => $arCritical
			);
			$strBlockContent = $arResult[$strIdent]['CONTENT'];
			if (amopt_strpos($strBlockContent, $strSeparator) !== false) {
				$arBlockData = $this->doCheckComponentCacheData($strBlockContent);
				foreach ($arBlockData as $k1 => $v1) {
					$strBlockContent = str_replace('#COMPONENT_AMMINA_OPTIMIZER_' . $k1 . '#', $v1['CONTENT'], $strBlockContent);
					$arResult[$strIdent]['CRITICAL'] = array_merge_recursive($arResult[$strIdent]['CRITICAL'], $v1['CRITICAL']);
				}
				$arResult[$strIdent]['CONTENT'] = $strBlockContent;
			}

			$arScriptContent = array();
			$arContentBlock = explode("</script>", $arResult[$strIdent]['CONTENT']);
			foreach ($arContentBlock as $k => $v) {
				$iStart = amopt_strpos($v, '<script');
				if ($iStart !== false) {
					$iStartContent = amopt_strpos($v, '>', $iStart + 1);
					$strCont = amopt_substr($v, $iStart) . '</script>';
					$arScriptContent[] = trim($strCont);
					$arContentBlock[$k] = amopt_substr($v, 0, $iStart);
				}
			}
			$arResult[$strIdent]['CONTENT'] = implode("", $arContentBlock);

			$arDataContent[] = $arExpl[0];
			$arDataContent[] = '#COMPONENT_AMMINA_OPTIMIZER_' . $strIdent . '#' . implode("", $arScriptContent);
			$strContent = $arExpl[2];
			$iPos = amopt_strpos($strContent, $strSeparator);
		}
		$arDataContent[] = $strContent;
		$strContent = implode("", $arDataContent);
		return $arResult;
	}

	public function doOptimizeHtml(&$strContent)
	{
		$this->startTimeParsing = microtime(true);
		$this->doCheckNormalEncode($strContent);
		$bComponentCache = false;
		if (amopt_strpos($strContent, '<!-- component.ammina.optimizer.') !== false) {
			$bComponentCache = true;
			$arDataComponentCache = $this->doCheckComponentCacheData($strContent);
		}
		if (!is_object($this->oParser)) {
			return;
		}
		$this->oParser->doParse($strContent);
		$this->endTimeParsing = microtime(true);
		$this->totalTimeParsing = $this->endTimeParsing - $this->startTimeParsing;

		$this->startTimeImageOptimize = microtime(true);

		if ($this->arOptions['category']['images']['options']['ACTIVE'] == "Y") {
			$this->oImageOptimizer->doOptimize();
		}
		$this->endTimeImageOptimize = microtime(true);
		$this->totalTimeImageOptimize = $this->endTimeImageOptimize - $this->startTimeImageOptimize;

		$this->startTimeCssOptimize = microtime(true);
		if ($this->arOptions['category']['css']['options']['ACTIVE'] == "Y") {
			$arAppendCritical = array();
			if ($bComponentCache) {
				foreach ($arDataComponentCache as $k => $v) {
					$arAppendCritical = array_merge_recursive($arAppendCritical, $v['CRITICAL']);
				}
			}
			if (empty($arAppendCritical)) {
				$arAppendCritical = false;
			}
			$this->oCSSOptimizer->doOptimize($arAppendCritical);
		}
		$this->endTimeCssOptimize = microtime(true);
		$this->totalTimeCssOptimize = $this->endTimeCssOptimize - $this->startTimeCssOptimize;

		$this->startTimeDelayOptimize = microtime(true);
		$this->oDelayOptimizer->doOptimize();
		$this->endTimeDelayOptimize = microtime(true);
		$this->totalTimeDelayOptimize = $this->endTimeDelayOptimize - $this->startTimeDelayOptimize;

		$this->startTimeJsOptimize = microtime(true);
		if ($this->arOptions['category']['main']['groups']['other']['options']['MAKE_STATIC_ASPRO_SETTHEME'] == "Y") {
			$this->oJSOptimizer->doMakeStaticAsproSetTheme();
		}
		if ($this->arOptions['category']['main']['groups']['other']['options']['UNLOCK_SKIP_MOVE_JS_ASPRO'] == "Y") {
			$this->oJSOptimizer->doUnlockSkipMoveJsAspro();
		}
		if ($this->arOptions['category']['main']['groups']['other']['options']['UNLOCK_SKIP_MOVE_JS_ASPRO'] == "Y") {
			$this->oJSOptimizer->doUnlockSkipMoveJsAspro();
		}
		if ($this->arOptions['category']['main']['groups']['other']['options']['UNLOCK_SKIP_MOVE_JS_HEAD'] == "Y") {
			$this->oJSOptimizer->doUnlockSkipMoveJsHead();
		}
		if ($this->arOptions['category']['main']['groups']['other']['options']['UNLOCK_SKIP_MOVE_JS_BODY'] == "Y") {
			$this->oJSOptimizer->doUnlockSkipMoveJsBody();
		}
		if ($this->arOptions['category']['main']['groups']['other']['options']['CHECK_BXRAND_SCRIPT'] == "Y") {
			$this->oJSOptimizer->doUnlockMoveBxRandScript();
			if ($this->arOptions['category']['main']['groups']['other']['options']['MOVE_JS_BODY'] == "Y") {
				$this->oJSOptimizer->doMoveBxRandScriptToEndBody();
			}
		}
		if ($this->arOptions['category']['js']['options']['ACTIVE'] == "Y") {
			$bStoreScriptInSession = false;
			if ($this->arOptions['category']['main']['groups']['other']['options']['CHECK_COMPONENT_AJAX_JSSCRIPT_EXISTS'] == "Y") {
				$bStoreScriptInSession = true;
			}
			$this->oJSOptimizer->doOptimize($bStoreScriptInSession);
		}
		if ($this->arOptions['category']['main']['groups']['other']['options']['MOVE_JS_BODY'] == "Y") {
			Asset::getInstance()->setJsToBody(false);
			$this->oJSOptimizer->doMoveJsBodyEnd();
		}
		$this->endTimeJsOptimize = microtime(true);
		$this->totalTimeJsOptimize = $this->endTimeJsOptimize - $this->startTimeJsOptimize;

		$this->startTimeHtmlOptimize = microtime(true);
		if ($this->arOptions['category']['html']['options']['ACTIVE'] == "Y") {
			$this->oHtmlOptimizer->doOptimize();
		}
		$this->endTimeHtmlOptimize = microtime(true);
		$this->totalTimeHtmlOptimize = $this->endTimeHtmlOptimize - $this->startTimeHtmlOptimize;

		$this->startTimeMakeHeadersLink = microtime(true);
		$this->doMakeHeadersLink();
		$this->endTimeMakeHeadersLink = microtime(true);
		$this->totalTimeMakeHeadersLink = $this->endTimeMakeHeadersLink - $this->startTimeMakeHeadersLink;

		$this->startTimeMakeHtml = microtime(true);
		$strContent = $this->oParser->doSaveHTML();

		if ($bComponentCache) {
			foreach ($arDataComponentCache as $k => $v) {
				$strContent = str_replace("#COMPONENT_AMMINA_OPTIMIZER_" . $k . "#", $v['CONTENT'], $strContent);
			}
			/*
			$arContent = explode("#COMPONENT_AMMINA_OPTIMIZER_", $strContent);
			$arLinkIdent = array();
			foreach ($arContent as $k => $v) {
				if ($k > 0) {
					$arContent[$k] = explode("#", $v, 2);
					$arLinkIdent[$arContent[$k][0]] =& $arContent[$k][0];
				}
			}
			foreach ($arDataComponentCache as $k => $v) {
				if (isset($arLinkIdent[$k])) {
					$arLinkIdent[$k] = $v['CONTENT'];
				}
				//$strContent = str_replace("#COMPONENT_AMMINA_OPTIMIZER_" . $k . "#", $v['CONTENT'], $strContent);
			}
			$strNew = "";

			foreach ($arContent as $k => $v) {
				if (is_array($v)) {
					$strNew .= implode("", $v);
				} else {
					$strNew .= $v;
				}
			}

			$strContent =$strNew;*/
		}
		$this->endTimeMakeHtml = microtime(true);
		$this->totalTimeMakeHtml = $this->endTimeMakeHtml - $this->startTimeMakeHtml;
		if (Helper::isCompositeEnabled()) {
			$arAreas = StaticArea::getDynamicAreas();
			foreach ($arAreas as $k => $oArea) {
				$arCachedData = $oArea->getCachedData();
				if (isset($arCachedData['staticPart']) && amopt_strlen($arCachedData['staticPart']) > 0) {
					$arCachedData['staticPart'] = $this->doOptimizeCompositeData($arCachedData['staticPart']);
					$oArea->applyCachedData($arCachedData);
				}
			}
		}
	}

	public function doOptimizeAjax(&$strContent)
	{
		if (amopt_strpos($strContent, '<') !== false && amopt_strpos($strContent, '</') !== false) {
			$this->doCheckNormalEncode($strContent);
			$bComponentCache = false;
			if (amopt_strpos($strContent, '<!-- component.ammina.optimizer.') !== false) {
				$bComponentCache = true;
				$arDataComponentCache = $this->doCheckComponentCacheData($strContent);
			}
			if (!is_object($this->oParser)) {
				return;
			}
			$this->oParser->doParsePart($strContent);
			if ($this->arOptions['category']['images']['options']['ACTIVE'] == "Y") {
				$this->oImageOptimizer->doOptimizePart();
			}

			if ($this->arOptions['category']['css']['options']['ACTIVE'] == "Y") {
				$this->oCSSOptimizer->doOptimizePart();
			}

			if ($this->arOptions['category']['js']['options']['ACTIVE'] == "Y") {
				$this->oJSOptimizer->doOptimizePart();
			}

			if ($this->arOptions['category']['html']['options']['ACTIVE'] == "Y") {
				$this->oHtmlOptimizer->doOptimize();
			}

			$this->doMakeHeadersLink();

			$strContent = $this->oParser->doSaveHTMLPart();
			if ($bComponentCache) {
				foreach ($arDataComponentCache as $k => $v) {
					$strContent = str_replace("#COMPONENT_AMMINA_OPTIMIZER_" . $k . "#", $v['CONTENT'], $strContent);
				}
			}
		}
	}

	public function doStoreScriptsInSession($arLinks)
	{
		global $APPLICATION;
		$strPage = $APPLICATION->GetCurPageParam("", array("bxajaxid"));
		foreach ($arLinks as $k => $v) {
			$strFileName = $v;
			if (amopt_strpos($strFileName, '://') !== false || amopt_strpos($strFileName, '//') === 0) {
			} else {
				$strFileNameOriginal = $strFileName;
				$strFileName = explode("?", $strFileName);
				$strFileName = $strFileName[0];
			}
			$arLinks[$k] = $strFileName;
		}
		if (isset($_SESSION['AMOPT_PAGES'][$strPage])) {
			unset($_SESSION['AMOPT_PAGES'][$strPage]);
		}
		$_SESSION['AMOPT_PAGES'][$strPage] = $arLinks;
		if (count($_SESSION['AMOPT_PAGES']) > 50) {
			$ak = array_keys($_SESSION['AMOPT_PAGES']);
			foreach ($ak as $k) {
				unset($_SESSION['AMOPT_PAGES'][$ak[$k]]);
				if (count($_SESSION['AMOPT_PAGES']) <= 50) {
					break;
				}
			}
		}
	}

	public function doOptimizeAjaxComponent(&$strContent)
	{
		global $APPLICATION;
		$bRemoveScriptInSession = false;
		$arRemoveJSLinks = array();
		if ($this->arOptions['category']['main']['groups']['other']['options']['CHECK_COMPONENT_AJAX_JSSCRIPT_EXISTS'] == "Y") {
			$bRemoveScriptInSession = true;
			$strReferer = $_SERVER['HTTP_REFERER'];
			$ar = explode("/", $strReferer);
			unset($ar[0]);
			unset($ar[1]);
			$ar[2] = "";
			$strReferer = implode("/", $ar);
			if (isset($_SESSION['AMOPT_PAGES'][$strReferer])) {
				$arRemoveJSLinks = $_SESSION['AMOPT_PAGES'][$strReferer];
			}
		}
		if (amopt_strpos($strContent, '<') !== false && amopt_strpos($strContent, '</') !== false) {
			$this->doCheckNormalEncode($strContent);
			$bComponentCache = false;
			if (amopt_strpos($strContent, '<!-- component.ammina.optimizer.') !== false) {
				$bComponentCache = true;
				$arDataComponentCache = $this->doCheckComponentCacheData($strContent);
			}
			if (!is_object($this->oParser)) {
				return;
			}
			$this->oParser->doParsePart($strContent);

			if ($this->arOptions['category']['images']['options']['ACTIVE'] == "Y") {
				$this->oImageOptimizer->doOptimizePart();
			}

			//Check arAjaxPageData
			$bExistsArAjaxData = false;
			$strScriptPageDataContent = ob_get_contents();
			if (amopt_strpos($strScriptPageDataContent, 'var arAjaxPageData') !== false) {
				$arScriptPageContent = array(
					"",
					"",
					"",
				);
				$st1 = amopt_strpos($strScriptPageDataContent, "var arAjaxPageData = ");
				$st2 = amopt_strpos($strScriptPageDataContent, "parent.BX.ajax.UpdatePageData(");
				if ($st1 !== false && $st2 !== false) {
					$bExistsArAjaxData = true;
					ob_clean();
					$st3 = amopt_strpos($strScriptPageDataContent, '{', $st1);
					$st4 = amopt_strpos($strScriptPageDataContent, '};', $st3);
					$arScriptPageContent[0] = amopt_substr($strScriptPageDataContent, 0, $st3);
					$arScriptPageContent[1] = amopt_substr($strScriptPageDataContent, $st3, $st4 - $st3 + 1);
					$arScriptPageContent[2] = amopt_substr($strScriptPageDataContent, $st4 + 1);
					$arDataPageContent = ammina_JsObjectToPhp($arScriptPageContent[1], true);
					$arDataPageContent['TITLE'] = $this->JSUnEscape($arDataPageContent['TITLE']);
					$arDataPageContent['WINDOW_TITLE'] = $this->JSUnEscape($arDataPageContent['WINDOW_TITLE']);
					$arDataPageContent['NAV_CHAIN'] = $this->JSUnEscape($arDataPageContent['NAV_CHAIN']);
				}
			}

			if ($this->arOptions['category']['css']['options']['ACTIVE'] == "Y") {
				$this->oCSSOptimizer->doOptimizePart();
			}

			if ($this->arOptions['category']['js']['options']['ACTIVE'] == "Y") {
				/**
				 * @todo ����������� ����������� ����������
				 */
				$this->oJSOptimizer->doOptimizePart($bRemoveScriptInSession);
			}

			if ($this->arOptions['category']['html']['options']['ACTIVE'] == "Y") {
				$this->oHtmlOptimizer->doOptimize();
			}

			if ($bExistsArAjaxData) {
				$oTmpOptimizer = new CSS($this->arOptions);
				$strResultFile = $oTmpOptimizer->doOptimizeCssFilesArray($arDataPageContent['CSS']);
				if (amopt_strlen($strResultFile) > 0) {
					$arDataPageContent['CSS'] = array($strResultFile);
				}

				$oTmpOptimizer = new CSS($this->arOptions['category']['css']['groups']);
				$strResultFile = $oTmpOptimizer->doOptimizeCssFilesArray($arDataPageContent['CSS']);
				if (amopt_strlen($strResultFile) > 0) {
					$arDataPageContent['CSS'] = array($strResultFile);
				}
				unset($oTmpOptimizer);
				$oTmpOptimizer = new JS($this->arOptions['category']['js']['groups']);
				if ($bRemoveScriptInSession) {
					$this->doStoreScriptsInSession($arDataPageContent['SCRIPTS']);
					if (!empty($arRemoveJSLinks)) {
						foreach ($arDataPageContent['SCRIPTS'] as $k => $v) {
							$strFileName = $v;
							if (amopt_strpos($strFileName, '://') !== false || amopt_strpos($strFileName, '//') === 0) {
							} else {
								$strFileNameOriginal = $strFileName;
								$strFileName = explode("?", $strFileName);
								$strFileName = $strFileName[0];
							}
							if (in_array($strFileName, $arRemoveJSLinks)) {
								unset($arDataPageContent['SCRIPTS'][$k]);
							}
						}
					}
				}
				$strResultFile = $oTmpOptimizer->doOptimizeJsFilesArray($arDataPageContent['SCRIPTS']);
				if (amopt_strlen($strResultFile) > 0) {
					if (filesize($_SERVER['DOCUMENT_ROOT'] . $strResultFile) > 0) {
						$arDataPageContent['SCRIPTS'] = array($strResultFile);
					} else {
						$arDataPageContent['SCRIPTS'] = array();
					}
				}
				unset($oTmpOptimizer);

				$arScriptPageContent[1] = CUtil::PhpToJSObject($arDataPageContent);
				$strScriptPageDataContent = implode("", $arScriptPageContent);
				echo $strScriptPageDataContent;
			}

			$this->doMakeHeadersLink();

			$strContent = $this->oParser->doSaveHTMLPart();
			if ($bComponentCache) {
				foreach ($arDataComponentCache as $k => $v) {
					$strContent = str_replace("#COMPONENT_AMMINA_OPTIMIZER_" . $k . "#", $v['CONTENT'], $strContent);
				}
			}
		}
	}

	public function doOptimizeJson(&$strContent)
	{
		$arData = Json::decode($strContent);
		$arData = $this->doOptimizeJsonData($arData);
		$strContent = Json::encode($arData);
	}

	protected function doOptimizeJsonData($arData)
	{
		if (!is_object($this->oParser)) {
			return $arData;
		}
		if (is_array($arData)) {
			foreach ($arData as $k => $v) {
				$arData[$k] = $this->doOptimizeJsonData($v);
			}
		} else {
			if (amopt_strpos($arData, '<') !== false && amopt_strpos($arData, '</') !== false) {
				$this->doCheckNormalEncode($arData);
				$bComponentCache = false;
				if (amopt_strpos($arData, '<!-- component.ammina.optimizer.') !== false) {
					$bComponentCache = true;
					$arDataComponentCache = $this->doCheckComponentCacheData($arData);
				}
				$this->oParser->doParsePart($arData);
				if ($this->arOptions['category']['images']['options']['ACTIVE'] == "Y") {
					$this->oImageOptimizer->doOptimizePart();
				}

				if ($this->arOptions['category']['css']['options']['ACTIVE'] == "Y") {
					$this->oCSSOptimizer->doOptimizePart();
				}

				if ($this->arOptions['category']['js']['options']['ACTIVE'] == "Y") {
					$this->oJSOptimizer->doOptimizePart();
				}

				if ($this->arOptions['category']['html']['options']['ACTIVE'] == "Y") {
					$this->oHtmlOptimizer->doOptimize();
				}

				$this->doMakeHeadersLink();

				$arData = $this->oParser->doSaveHTMLPart();
				if ($bComponentCache) {
					foreach ($arDataComponentCache as $k => $v) {
						$arData = str_replace("#COMPONENT_AMMINA_OPTIMIZER_" . $k . "#", $v['CONTENT'], $arData);
					}
				}
			}
		}
		return $arData;
	}

	protected function doOptimizeCompositeData($arData)
	{
		if (!is_object($this->oParser)) {
			return $arData;
		}
		if (is_array($arData)) {
			foreach ($arData as $k => $v) {
				$arData[$k] = $this->doOptimizeCompositeData($v);
			}
		} else {
			if (amopt_strpos($arData, '<') !== false && amopt_strpos($arData, '</') !== false) {
				$this->doCheckNormalEncode($arData);
				$bComponentCache = false;
				if (amopt_strpos($arData, '<!-- component.ammina.optimizer.') !== false) {
					$bComponentCache = true;
					$arDataComponentCache = $this->doCheckComponentCacheData($arData);
				}
				$this->oParser->doParsePart($arData);
				if ($this->arOptions['category']['images']['options']['ACTIVE'] == "Y") {
					$this->oImageOptimizer->doOptimizePart();
				}

				if ($this->arOptions['category']['css']['options']['ACTIVE'] == "Y") {
					$this->oCSSOptimizer->doOptimizePart();
				}

				if ($this->arOptions['category']['js']['options']['ACTIVE'] == "Y") {
					$this->oJSOptimizer->doOptimizePart(true);
				}

				if ($this->arOptions['category']['html']['options']['ACTIVE'] == "Y") {
					$this->oHtmlOptimizer->doOptimize();
				}

				$this->doMakeHeadersLink();

				$arData = $this->oParser->doSaveHTMLPart();
				if ($bComponentCache) {
					foreach ($arDataComponentCache as $k => $v) {
						$arData = str_replace("#COMPONENT_AMMINA_OPTIMIZER_" . $k . "#", $v['CONTENT'], $arData);
					}
				}
			}
		}
		return $arData;
	}

	public static function JSUnEscape($s)
	{
		static $aSearch = array("\\\\", "\\'", '\\"', "\n", "\n", "\\n", "\\n", "*\\/", "<\\/");
		static $aReplace = array("\\", "'", "\"", "\n", "\n", "\n", "\n", "*/", "</");
		$val = str_replace($aSearch, $aReplace, $s);
		return $val;
	}

	public function doCheckNormalEncode(&$strContent)
	{
		if ($this->arOptions['category']['main']['groups']['parse']['options']['CHECK_ENCODING_UTF8'] == "Y") {
			if (defined("BX_UTF") && BX_UTF === true) {
				if (mb_check_encoding($strContent, "windows-1251") && !mb_check_encoding($strContent, "utf-8")) {
					$strContent = mb_convert_encoding($strContent, "utf-8");;
				}
			}
		}
	}

	public function doOptimizeAutocomposite($strContent)
	{
		$arData = ammina_JsObjectToPhp($strContent, true);
		$bAllowAutoHeaders = true;
		if (!empty($arData['dynamicBlocks'])) {
			foreach ($arData['dynamicBlocks'] as $k => $v) {
				//$arData['dynamicBlocks'][$k]['CONTENT'] = $this->JSUnEscape($arData['dynamicBlocks'][$k]['CONTENT']);
				if (amopt_strpos($arData['dynamicBlocks'][$k]['CONTENT'], '<!-- ammina.optimizer.stop.frame -->') !== false) {
					$arData['dynamicBlocks'][$k]['CONTENT'] = str_replace('<!-- ammina.optimizer.stop.frame -->', "", $arData['dynamicBlocks'][$k]['CONTENT']);
				} else {
					$arData['dynamicBlocks'][$k]['CONTENT'] = $this->doOptimizeCompositeData($this->JSUnEscape($arData['dynamicBlocks'][$k]['CONTENT']));
				}
			}
		}
		//$this->oCSSOptimizer = new CSS($this->arOptions['category']['css']['groups']);
		//$this->oJSOptimizer = new JS($this->arOptions['category']['js']['groups']);
		if (!empty($arData['css'])) {
			$tmpCSSOptimizer = new CSS($this->arOptions['category']['css']['groups']);
			$strResult = $tmpCSSOptimizer->doOptimizeCssFilesArray($arData['css']);
			$this->oCSSOptimizer->arHeadersPreloadFiles = array_merge($this->oCSSOptimizer->arHeadersPreloadFiles, $tmpCSSOptimizer->arHeadersPreloadFiles);
			$arData['css'] = array($strResult);
			$bAllowAutoHeaders = true;
		}
		if (!empty($arData['js'])) {
			$tmpJSOptimizer = new JS($this->arOptions['category']['js']['groups']);
			$strResult = $tmpJSOptimizer->doOptimizeJsFilesArray($arData['js']);
			$this->oJSOptimizer->arHeadersPreloadFiles = array_merge($this->oJSOptimizer->arHeadersPreloadFiles, $tmpJSOptimizer->arHeadersPreloadFiles);
			$arData['js'] = array($strResult);
			$bAllowAutoHeaders = true;
		}
		if (!empty($arData['additional_js'])) {
			$tmpJSOptimizer = new JS($this->arOptions['category']['additional_js']['groups']);
			$strResult = $tmpJSOptimizer->doOptimizeJsFilesArray($arData['additional_js']);
			$this->oJSOptimizer->arHeadersPreloadFiles = array_merge($this->oJSOptimizer->arHeadersPreloadFiles, $tmpJSOptimizer->arHeadersPreloadFiles);
			$arData['additional_js'] = array($strResult);
			$bAllowAutoHeaders = true;
		}
		$strContent = \CUtil::PhpToJSObject($arData);
		$this->doSendStackRequest();
		if ($bAllowAutoHeaders) {
			$this->doMakeHeaders();
		}
		return $strContent;
	}

	public function isClearCache($strType = "")
	{
		if ($this->strClearCache === true) {
			return true;
		} elseif (amopt_strlen($strType) >= 0) {
			if ($this->strClearCache == $strType) {
				return true;
			}
		}
		return false;
	}

	public function isShowStat()
	{
		return (isset($_SESSION['AMOPT_SHOWSTAT']) && $_SESSION['AMOPT_SHOWSTAT']);
	}

	public function doShowStat()
	{
		if ($this->iRequestType == self::REQUEST_TYPE_HTML) {
			ob_start();
			?>
			<div class="amoptpub">
				<p><strong><?= Loc::getMessage("AMMINA_OPTIMIZER_PANEL_STAT_TITLE") ?></strong></p>
				<p><?= Loc::getMessage("AMMINA_OPTIMIZER_PANEL_STAT_TIME", array("#TIME#" => round($this->totalTime, 5))) ?>
					, <?= Loc::getMessage("AMMINA_OPTIMIZER_PANEL_STAT_TIME_ALSO") ?></p>
				<ul>
					<li><?= Loc::getMessage("AMMINA_OPTIMIZER_PANEL_STAT_TIME_PARSING", array("#TIME#" => round($this->totalTimeParsing, 5))) ?></li>
					<li><?= Loc::getMessage("AMMINA_OPTIMIZER_PANEL_STAT_TIME_IMAGE_OPTIMIZE", array("#TIME#" => round($this->totalTimeImageOptimize, 5))) ?></li>
					<li><?= Loc::getMessage("AMMINA_OPTIMIZER_PANEL_STAT_TIME_CSS_OPTIMIZE", array("#TIME#" => round($this->totalTimeCssOptimize, 5))) ?></li>
					<li><?= Loc::getMessage("AMMINA_OPTIMIZER_PANEL_STAT_TIME_DELAY_OPTIMIZE", array("#TIME#" => round($this->totalTimeDelayOptimize, 5))) ?></li>
					<li><?= Loc::getMessage("AMMINA_OPTIMIZER_PANEL_STAT_TIME_JS_OPTIMIZE", array("#TIME#" => round($this->totalTimeJsOptimize, 5))) ?></li>
					<li><?= Loc::getMessage("AMMINA_OPTIMIZER_PANEL_STAT_TIME_HTML_OPTIMIZE", array("#TIME#" => round($this->totalTimeHtmlOptimize, 5))) ?></li>
					<li><?= Loc::getMessage("AMMINA_OPTIMIZER_PANEL_STAT_TIME_MAKE_HTML", array("#TIME#" => round($this->totalTimeMakeHtml, 5))) ?></li>
				</ul>
				<?/*<p>������: <?= $this->totalMemory ?></p>*/ ?>
			</div>
			<?
			$strContent = ob_get_contents();
			ob_end_clean();
			return $strContent;
		}
		return '';
	}

	public function isSupportWebP()
	{
		//return true;
		return ($this->bSupportWebP || $this->bPreventSupportWebP);
	}

	public function setPreventSupportWebP($bSupport)
	{
		$this->bPreventSupportWebP = $bSupport;
	}

	protected function doCheckBrowserFeatures()
	{
		/**
		 * �������� ������������ � ��������� ������ ������������ ����������
		 */
		if (amopt_strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false) {
			$bMainSupportWebp = true;
		}
		$bIsLightHouse = (amopt_strpos(amopt_strtolower($_SERVER['USER_AGENT']), 'lighthouse') !== false);
		$iOsVersion = str_replace("_", ".", $this->oDetector->version("iOS"));
		$imacOsVersion = str_replace("_", ".", $this->oDetector->version("macOS"));
		$arBrowsers = array(
			'IE',
			'Edge',
			'Firefox',
			'Chrome',
			'Safari',
			'Opera',
			'Opera Mini',
			'UCBrowser',
			'SamsungBrowser',
		);
		foreach ($arBrowsers as $browser) {
			$strVersion = $this->oDetector->version($browser);
			if ($browser == "Safari") {
				if (($this->oDetector->isiOS() || $this->oDetector->ismacOS()) && amopt_strlen($strVersion) > 0) {
					$this->strBrowser = $browser;
					$this->strBrowserVersion = $strVersion;
					break;
				}
			} elseif (amopt_strlen($strVersion) > 0) {
				$this->strBrowser = $browser;
				$this->strBrowserVersion = $strVersion;
				break;
			}
		}
		switch ($this->strBrowser) {
			case "IE":
				if (version_compare($this->strBrowserVersion, '9', '>=')) {
					$this->arSupportFontTypes[] = "woff";
				}
				if (version_compare($this->strBrowserVersion, '6', '>=')) {
					$this->arSupportFontTypes[] = "eot";
				}
				if (version_compare($this->strBrowserVersion, '11', '>=')) {
					$this->arSupportHeaders[] = "prefetch";
				}
				if (version_compare($this->strBrowserVersion, '10', '>=')) {
					$this->arSupportHeaders[] = "dns-prefetch";
				}
				break;
			case "Edge":
				if (version_compare($this->strBrowserVersion, '18', '>=')) {
					$this->bSupportWebP = true;
				}
				if (version_compare($this->strBrowserVersion, '14', '>=')) {
					$this->arSupportFontTypes[] = "woff2";
				}
				if (version_compare($this->strBrowserVersion, '12', '>=')) {
					$this->arSupportFontTypes[] = "woff";
				}
				if (version_compare($this->strBrowserVersion, '12', '>=')) {
					$this->arSupportFontTypes[] = "ttf";
					$this->arSupportFontTypes[] = "otf";
				}
				if (version_compare($this->strBrowserVersion, '76', '>=')) {
					$this->arSupportHeaders[] = "preload";
				}
				if (version_compare($this->strBrowserVersion, '12', '>=')) {
					$this->arSupportHeaders[] = "prefetch";
				}
				if (version_compare($this->strBrowserVersion, '12', '>=')) {
					$this->arSupportHeaders[] = "dns-prefetch";
				}
				if (version_compare($this->strBrowserVersion, '76', '>=')) {
					$this->arSupportHeaders[] = "preconnect";
				}
				break;
			case "Firefox":
				if (version_compare($this->strBrowserVersion, '65', '>=')) {
					$this->bSupportWebP = true;
				}
				if (version_compare($this->strBrowserVersion, '39', '>=')) {
					$this->arSupportFontTypes[] = "woff2";
				}
				if (version_compare($this->strBrowserVersion, '3.6', '>=')) {
					$this->arSupportFontTypes[] = "woff";
				}
				if (version_compare($this->strBrowserVersion, '3.5', '>=')) {
					$this->arSupportFontTypes[] = "ttf";
					$this->arSupportFontTypes[] = "otf";
				}
				if (version_compare($this->strBrowserVersion, '2', '>=')) {
					$this->arSupportHeaders[] = "prefetch";
				}
				if (version_compare($this->strBrowserVersion, '3.5', '>=')) {
					$this->arSupportHeaders[] = "dns-prefetch";
				}
				if (version_compare($this->strBrowserVersion, '57', '>=')) {
					//$this->arSupportHeaders[] = "preload";//���� ����� ��������� ��������. �� ��������� false ���������. ������� ��������� ���������
				}
				if (version_compare($this->strBrowserVersion, '39', '>=')) {
					$this->arSupportHeaders[] = "preconnect";
				}
				break;
			case "Chrome":
				if (version_compare($this->strBrowserVersion, '32', '>=')) {
					$this->bSupportWebP = true;
				}
				if (version_compare($this->strBrowserVersion, '36', '>=')) {
					$this->arSupportFontTypes[] = "woff2";
				}
				if (version_compare($this->strBrowserVersion, '5', '>=')) {
					$this->arSupportFontTypes[] = "woff";
				}
				if (version_compare($this->strBrowserVersion, '4', '>=')) {
					$this->arSupportFontTypes[] = "ttf";
					$this->arSupportFontTypes[] = "otf";
				}
				if (version_compare($this->strBrowserVersion, '50', '>=')) {
					$this->arSupportHeaders[] = "preload";
				}
				if (version_compare($this->strBrowserVersion, '8', '>=')) {
					$this->arSupportHeaders[] = "prefetch";
				}
				if (version_compare($this->strBrowserVersion, '4', '>=')) {
					$this->arSupportHeaders[] = "dns-prefetch";
				}
				if (version_compare($this->strBrowserVersion, '46', '>=')) {
					$this->arSupportHeaders[] = "preconnect";
				}
				break;
			case "Safari":
				$bMainSupportWebp = false;
				if (version_compare($this->strBrowserVersion, '14.0.1', '>=')) {
					$this->bSupportWebP = true;
				}
				if (version_compare($this->strBrowserVersion, '12', '>=')) {
					$this->arSupportFontTypes[] = "woff2";
				}
				if (version_compare($this->strBrowserVersion, '5.1', '>=')) {
					$this->arSupportFontTypes[] = "woff";
				}
				if (version_compare($this->strBrowserVersion, '3.1', '>=')) {
					$this->arSupportFontTypes[] = "ttf";
					$this->arSupportFontTypes[] = "otf";
				}
				if (version_compare($this->strBrowserVersion, '11.1', '>=')) {
					$this->arSupportHeaders[] = "preload";
				}
				if (version_compare($this->strBrowserVersion, '5', '>=')) {
					$this->arSupportHeaders[] = "dns-prefetch";
				}
				if (version_compare($this->strBrowserVersion, '11.1', '>=')) {
					$this->arSupportHeaders[] = "preconnect";
				}
				break;
			case "Opera":
				if (version_compare($this->strBrowserVersion, '19', '>=')) {
					$this->bSupportWebP = true;
				}
				if (version_compare($this->strBrowserVersion, '23', '>=')) {
					$this->arSupportFontTypes[] = "woff2";
				}
				if (version_compare($this->strBrowserVersion, '11.5', '>=')) {
					$this->arSupportFontTypes[] = "woff";
				}
				if (version_compare($this->strBrowserVersion, '10', '>=')) {
					$this->arSupportFontTypes[] = "ttf";
					$this->arSupportFontTypes[] = "otf";
				}
				if (version_compare($this->strBrowserVersion, '37', '>=')) {
					$this->arSupportHeaders[] = "preload";
				}
				if (version_compare($this->strBrowserVersion, '15', '>=')) {
					$this->arSupportHeaders[] = "prefetch";
				}
				if (version_compare($this->strBrowserVersion, '15', '>=')) {
					$this->arSupportHeaders[] = "dns-prefetch";
				}
				if (version_compare($this->strBrowserVersion, '33', '>=')) {
					$this->arSupportHeaders[] = "preconnect";
				}
				break;
			case "Opera Mini":
				if (version_compare($this->strBrowserVersion, '1', '>=')) {
					$this->bSupportWebP = true;
				}
				break;
			case "UCBrowser":
				if (version_compare($this->strBrowserVersion, '11.8', '>=')) {
					$this->bSupportWebP = true;
				}
				if (version_compare($this->strBrowserVersion, '11.8', '>=')) {
					$this->arSupportFontTypes[] = "woff";
				}
				if (version_compare($this->strBrowserVersion, '11.8', '>=')) {
					$this->arSupportFontTypes[] = "ttf";
					$this->arSupportFontTypes[] = "otf";
				}
				if (version_compare($this->strBrowserVersion, '11.8', '>=')) {
					$this->arSupportHeaders[] = "prefetch";
				}
				break;
			case "SamsungBrowser":
				if (version_compare($this->strBrowserVersion, '4', '>=')) {
					$this->bSupportWebP = true;
				}
				if (version_compare($this->strBrowserVersion, '4', '>=')) {
					$this->arSupportFontTypes[] = "woff2";
				}
				if (version_compare($this->strBrowserVersion, '4', '>=')) {
					$this->arSupportFontTypes[] = "woff";
				}
				if (version_compare($this->strBrowserVersion, '4', '>=')) {
					$this->arSupportFontTypes[] = "ttf";
					$this->arSupportFontTypes[] = "otf";
				}
				if (version_compare($this->strBrowserVersion, '5', '>=')) {
					$this->arSupportHeaders[] = "preload";
				}
				if (version_compare($this->strBrowserVersion, '4', '>=')) {
					$this->arSupportHeaders[] = "prefetch";
				}
				if (version_compare($this->strBrowserVersion, '5', '>=')) {
					$this->arSupportHeaders[] = "dns-prefetch";
				}
				if (version_compare($this->strBrowserVersion, '5', '>=')) {
					$this->arSupportHeaders[] = "preconnect";
				}
				break;
		}
		if ($this->strBrowser == "Safari") {
			if ($this->oDetector->ismacOS() && version_compare($imacOsVersion, '11', '<')) {
				$this->bSupportWebP = false;
			}
		} else {
			if ($this->oDetector->isiOS() && version_compare($iOsVersion, '14', '<')) {
				$this->bSupportWebP = false;
			}
		}
		/*if ($this->oDetector->isiOS()) {
			$this->bSupportWebP = false;
		}*/
		if ($bMainSupportWebp) {
			$this->bSupportWebP = true;
		}
		if ($bIsLightHouse) {
			$this->bSupportWebP = true;
		}
		if (empty($this->arSupportHeaders)) {
			$this->arSupportHeaders[] = "prefetch";
			$this->arSupportHeaders[] = "preconnect";
		}
		if (empty($this->arSupportFontTypes)) {
			$this->arSupportFontTypes[] = "woff";
			$this->arSupportFontTypes[] = "ttf";
			$this->arSupportFontTypes[] = "otf";
		}
	}

	/**
	 * @return CSS
	 */
	public function getCSSOptimizer()
	{
		return $this->oCSSOptimizer;
	}

	/**
	 * @return JS
	 */
	public function getJSOptimizer()
	{
		return $this->oJSOptimizer;
	}

	/**
	 * @return Image
	 */
	public function getImageOptimizer()
	{
		return $this->oImageOptimizer;
	}

	public function normalized_file_get_content($strFileName)
	{
		global $APPLICATION;
		$strContent = file_get_contents($strFileName);
		if (defined("BX_UTF") && BX_UTF === true) {
			if (amopt_strlen($strContent) > 0 && amopt_strlen(htmlspecialchars($strContent)) <= 0) {
				$strContent = $APPLICATION->ConvertCharset($strContent, "windows-1251", "utf-8");
			}
		}
		return $strContent;
	}

	public function doPushStackRequest($arRequest, $strTypeRequest, $strResultFilePath, $strFileNameResultUrl)
	{
		\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strFileNameResultUrl, "wait");
		$arFieldsRequest = array(
			"type_request" => $strTypeRequest,
			"request" => $arRequest,
			"result_file_path" => $strResultFilePath,
			"file_name_result_url" => $strFileNameResultUrl,
		);
		$this->arRequestStack[] = $arFieldsRequest;
		if (count($this->arRequestStack) >= $this->iMaxStackPackageImages) {
			$this->doSendStackRequest();
			$this->arRequestStack = array();
		}
		return true;
	}

	public function doSendStackRequest()
	{
		global $APPLICATION;
		if (!empty($this->arRequestStack)) {
			$arResult = $this->doSendStackRequestWorkServer();
			if ($arResult['status'] == "ok") {
				foreach ($this->arRequestStack as $kRequest => $vRequest) {
					$strFileNameResultUrl = $vRequest['file_name_result_url'];
					$strResultFilePath = $vRequest['result_file_path'];
					$arRequest = $vRequest['request'];
					$arCurrentResult = $arResult['ANSWER'][$kRequest];
					if ($arCurrentResult['status'] == "ok") {
						\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strFileNameResultUrl, $arCurrentResult['url']);
					} elseif (file_exists($_SERVER['DOCUMENT_ROOT'] . $strFileNameResultUrl) && file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strFileNameResultUrl) == "wait") {
						@unlink($_SERVER['DOCUMENT_ROOT'] . $strFileNameResultUrl);
					}
				}
			}
		}
	}

	public function doSendStackRequestWorkServer()
	{
		global $APPLICATION;
		if (!empty($this->arRequestStack)) {
			$arFields = array(
				"SYSTEM" => array(
					"HOST" => $_SERVER['HTTP_HOST'],
					"HTTPS" => $APPLICATION->IsHttps(),
				),
			);
			if (amopt_strlen($arFields['SYSTEM']['HOST']) <= 0) {
				$arFields['SYSTEM']['HOST'] = COption::GetOptionString("ammina.optimizer", "default_host", "");
				if (amopt_strpos($arFields['SYSTEM']['HOST'], ",") !== false) {
					$arFields['SYSTEM']['HOST'] = explode(",", $arFields['SYSTEM']['HOST']);
					foreach ($arFields['SYSTEM']['HOST'] as $val) {
						if (amopt_strpos($_SERVER['HTTP_HOST'], $val) !== false) {
							$arFields['SYSTEM']['HOST'] = $val;
							break;
						}
					}
				}
				$arFields['SYSTEM']['HTTPS'] = (COption::GetOptionString("ammina.optimizer", "default_ishttps", "N") == "Y" ? true : false);
			}
			if (amopt_strlen($arFields['SYSTEM']['HOST']) <= 0) {
				$arSite = \CSite::GetByID(SITE_ID)->Fetch();
				if ($arSite && amopt_strlen($arSite['DOMAIN']) > 0) {
					$arFields['SYSTEM']['HOST'] = $arSite['DOMAIN'];
					$arFields['SYSTEM']['HTTPS'] = (COption::GetOptionString("main", "mail_link_protocol", "http") == "https" ? true : false);
				}
			}
			if (amopt_strlen($arFields['SYSTEM']['HOST']) <= 0) {
				$arFields['SYSTEM']['HOST'] = COption::GetOptionString("main", "server_name", "");
				$arFields['SYSTEM']['HTTPS'] = (COption::GetOptionString("main", "mail_link_protocol", "http") == "https" ? true : false);
			}
			$strUrl = COption::GetOptionString("ammina.optimizer", "ammina_workurl", "");
			$urltime = COption::GetOptionString("ammina.optimizer", "ammina_workurltime", "");
			if (amopt_strlen($strUrl) <= 0 || $urltime <= 0 || $urltime < (time() - 3600)) {
				$arUrlResponse = \CAmminaOptimizer::doRequestAmminaServer("GET_WORK_SERVER");
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
				$arFields['version'] = "2.0";
				foreach ($this->arRequestStack as $kRequest => $vRequest) {
					$arRequest = $vRequest['request'];
					$arRequest['t'] = $vRequest['type_request'];
					$arFields["REQUESTS"][$kRequest] = $arRequest;
				}
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
		}
		return false;
	}

	public function doStartComponentTemplate()
	{
		if (COption::GetOptionString("ammina.optimizer", "disabled_edit", "Y") == "Y") {
			if ($_SESSION["SESS_INCLUDE_AREAS"] === true) {
				return;
			}
		}
		if ($this->arOptions['category']['main']['options']['ACTIVE'] == "Y" && $this->arOptions['category']['main']['groups']['request']['options']['ACTIVE_COMPONENT_CACHE'] == "Y") {
			ob_start();
		}
	}

	public function doEndComponentTemplate()
	{
		if (COption::GetOptionString("ammina.optimizer", "disabled_edit", "Y") == "Y") {
			if ($_SESSION["SESS_INCLUDE_AREAS"] === true) {
				return;
			}
		}
		if ($this->arOptions['category']['main']['options']['ACTIVE'] == "Y" && $this->arOptions['category']['main']['groups']['request']['options']['ACTIVE_COMPONENT_CACHE'] == "Y") {
			$strContent = ob_get_contents();
			ob_end_clean();
			$randArea = "component.ammina.optimizer." . microtime(true) . "." . randString(10);
			?>
			<!-- <?= $randArea ?> -->
			<?
			$this->oParser = Base::createParser(
				$this->arOptions['category']['main']['groups']['parse']['options']['LIBRARY'],
				$this->arOptions['category']['main']['groups']['parse']['options']['CHECK_NOTVALID_START_TAG'] == "Y",
				array(
					"REPLACE_BULLET" => $this->arOptions['category']['main']['groups']['other']['options']['REPLACE_BULLET'] == "Y",
					"CHECK_NOTVALID_UTF8_SYMBOLS" => $this->arOptions['category']['main']['groups']['parse']['options']['CHECK_NOTVALID_UTF8_SYMBOLS'] == "Y"
				)
			);
			$this->oParser->doParsePart($strContent);
			if ($this->arOptions['category']['images']['options']['ACTIVE'] == "Y") {
				$this->oImageOptimizer->doOptimizePart("Y");
			}

			if ($this->arOptions['category']['css']['options']['ACTIVE'] == "Y") {
				$this->oCSSOptimizer->doOptimizePart();
			}

			if ($this->arOptions['category']['js']['options']['ACTIVE'] == "Y") {
				$this->oJSOptimizer->doOptimizePart();
			}

			if ($this->arOptions['category']['html']['options']['ACTIVE'] == "Y") {
				$this->oHtmlOptimizer->doOptimize();
			}
			?>
			<!-- component.critical.<?= $randArea ?> <?= serialize($this->oCSSOptimizer->arPartAllUsedClasses) ?> -->
			<!-- with-webp <?= $randArea ?> -->
			<?
			echo $this->oParser->doSaveHTMLPart();
			?>
			<!-- with-webp <?= $randArea ?> -->
			<!-- no-webp <?= $randArea ?> -->
			<?
			$this->oParser->doParsePart($strContent);
			if ($this->arOptions['category']['images']['options']['ACTIVE'] == "Y") {
				$this->oImageOptimizer->doOptimizePart("N");
			}

			if ($this->arOptions['category']['css']['options']['ACTIVE'] == "Y") {
				$this->oCSSOptimizer->doOptimizePart();
			}

			if ($this->arOptions['category']['js']['options']['ACTIVE'] == "Y") {
				$this->oJSOptimizer->doOptimizePart();
			}

			if ($this->arOptions['category']['html']['options']['ACTIVE'] == "Y") {
				$this->oHtmlOptimizer->doOptimize();
			}

			echo $this->oParser->doSaveHTMLPart();
			?>
			<!-- no-webp <?= $randArea ?> -->
			<!-- <?= $randArea ?> -->
			<?
		}
	}

	public function doResetComponentTemplate()
	{
		if ($this->arOptions['category']['main']['options']['ACTIVE'] == "Y" && $this->arOptions['category']['main']['groups']['request']['options']['ACTIVE_COMPONENT_CACHE'] == "Y") {
			ob_end_flush();
		}
	}

	public function OnPrologComposite()
	{
		global $APPLICATION;
		if (Helper::getAjaxRandom() !== false && Helper::isAjaxRequest()) {
			if ($this->isAllowComposite()) {
				$strCurrentContent = ob_get_contents();
				$APPLICATION->buffer_man = true;
				ob_end_clean();
				$APPLICATION->buffered = false;
				$APPLICATION->buffer_man = false;
				ob_start(array("CAmminaOptimizer", "OnEndContentComposite"));
				ob_start(array(&$APPLICATION, "EndBufferContent"));
				echo $strCurrentContent;
			}
		}
	}
}
