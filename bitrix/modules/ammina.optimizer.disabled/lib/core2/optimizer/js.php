<?

namespace Ammina\Optimizer\Core2\Optimizer;

use Ammina\Optimizer\Core2\Application;
use Ammina\Optimizer\Core2\Driver\Js\AmminaBabelMinify;
use Ammina\Optimizer\Core2\Driver\Js\AmminaTerserJs;
use Ammina\Optimizer\Core2\Driver\Js\AmminaUglifyJs;
use Ammina\Optimizer\Core2\Driver\Js\AmminaUglifyJs2;
use Ammina\Optimizer\Core2\Driver\Js\AmminaYUICompressor;
use Ammina\Optimizer\Core2\Driver\Js\BabelMinify;
use Ammina\Optimizer\Core2\Driver\Js\MatthiasMullie;
use Ammina\Optimizer\Core2\Driver\Js\PHPWee;
use Ammina\Optimizer\Core2\Driver\Js\TerserJs;
use Ammina\Optimizer\Core2\Driver\Js\UglifyJs;
use Ammina\Optimizer\Core2\Driver\Js\UglifyJs2;
use Ammina\Optimizer\Core2\Driver\Js\YUICompressor;

class JS
{
	protected $arOptions = false;
	protected $strBaseIdent = "";
	public $arHeadersPreloadFiles = array();
	protected $arAllJSScript = array();
	protected $strMainResultFile = false;

	/**
	 * @var PHPWee
	 */
	//protected $oDriverPHPWee = null;
	/**
	 * @var MatthiasMullie
	 */
	//protected $oDriverMatthiasMullie = null;
	/**
	 * @var UglifyJs
	 */
	protected $oDriverUglifyJS = null;
	/**
	 * @var UglifyJs2
	 */
	protected $oDriverUglifyJS2 = null;
	/**
	 * @var TerserJs
	 */
	protected $oDriverTerserJS = null;
	/**
	 * @var BabelMinify
	 */
	protected $oDriverBabelMinify = null;
	/**
	 * @var YUICompressor
	 */
	//protected $oDriverYUICompressor = null;
	/**
	 * @var AmminaUglifyJs
	 */
	protected $oDriverAmminaUglifyJS = null;
	/**
	 * @var AmminaUglifyJs2
	 */
	protected $oDriverAmminaUglifyJS2 = null;
	/**
	 * @var AmminaTerserJs
	 */
	protected $oDriverAmminaTerserJS = null;
	/**
	 * @var AmminaBabelMinify
	 */
	protected $oDriverAmminaBabelMinify = null;
	/**
	 * @var AmminaYUICompressor
	 */
	//protected $oDriverAmminaYUICompressor = null;

	public function __construct($arOptions)
	{
		//$this->oDriverPHPWee = new PHPWee();
		//$this->oDriverMatthiasMullie = new MatthiasMullie();
		$this->oDriverUglifyJS = new UglifyJs();
		$this->oDriverUglifyJS2 = new UglifyJs2();
		$this->oDriverTerserJS = new TerserJs();
		$this->oDriverBabelMinify = new BabelMinify();
		//$this->oDriverYUICompressor = new YUICompressor();
		$this->oDriverAmminaUglifyJS = new AmminaUglifyJs();
		$this->oDriverAmminaUglifyJS2 = new AmminaUglifyJs2();
		$this->oDriverAmminaTerserJS = new AmminaTerserJs();
		$this->oDriverAmminaBabelMinify = new AmminaBabelMinify();
		//$this->oDriverAmminaYUICompressor = new AmminaYUICompressor();
		$this->setOptions($arOptions);
	}

	public function setOptions($arOptions)
	{
		$this->arOptions = $arOptions;
		$this->strBaseIdent = $arOptions;
		unset($this->strBaseIdent['minify']['DEFAULT']);
		unset($this->strBaseIdent['external_js']['DEFAULT']);
		unset($this->strBaseIdent['other']['DEFAULT']);
		unset($this->strBaseIdent['ext']['DEFAULT']);
		unset($this->strBaseIdent['external_js']['options']['EXCLUDE']);
		unset($this->strBaseIdent['external_js']['options']['INCLUDE']);
		unset($this->strBaseIdent['other']['options']['EXCLUDE_FILES']);
		$this->strBaseIdent = md5(serialize($this->strBaseIdent));
	}

	public function doOptimizePart($bIsComposite = false)
	{
		$this->arAllJSScript = array();
		$this->strMainResultFile = false;
		$this->doOptimize(false, $bIsComposite);
		$this->arAllJSScript = array();
		$this->strMainResultFile = false;
	}

	public function doOptimizeJsFilesArray($arFiles)
	{
		$this->arAllJSScript = array();
		foreach ($arFiles as $strFile) {
			$this->arAllJSScript[] = array(
				"src" => $strFile,
				"OPTIMIZE_FILE" => $this->doOptimizeJsFile($strFile),
			);
		}
		$this->strMainResultFile = $this->doMakeResultFile();
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . $this->strMainResultFile) && filesize($_SERVER['DOCUMENT_ROOT'] . $this->strMainResultFile) > 0) {
			$this->arHeadersPreloadFiles[] = array(
				"FILE" => $this->strMainResultFile,
				"TYPE" => "SCRIPT",
			);
		}
		return $this->strMainResultFile;
	}

	public function doUnlockMoveBxRandScript()
	{
		Application::getInstance()->getParser()->doUnlockMoveBxRandScript();
	}

	public function doMoveBxRandScriptToEndBody()
	{
		Application::getInstance()->getParser()->doMoveBxRandScriptToEndBody();
	}

	public function doUnlockSkipMoveJsAspro()
	{
		Application::getInstance()->getParser()->doUnlockSkipMoveJsAspro();
	}

	public function doMakeStaticAsproSetTheme(){
		Application::getInstance()->getParser()->doMakeStaticAsproSetTheme();
	}

	public function doUnlockSkipMoveJsHead()
	{
		Application::getInstance()->getParser()->doUnlockSkipMoveJsHead();
	}

	public function doUnlockSkipMoveJsBody()
	{
		Application::getInstance()->getParser()->doUnlockSkipMoveJsBody();
	}

	public function doSetDeferAttribute()
	{
		global $APPLICATION;
		if ($this->arOptions['ext']['options']['SET_DEFER'] == "Y") {
			$bAllow = true;
			if (amopt_strlen(trim($this->arOptions['ext']['options']['EXCLUDE_DEFER'])) > 0 && \CAmminaOptimizer::doMathPageToRules($this->arOptions['ext']['options']['EXCLUDE_DEFER'], $APPLICATION->GetCurPage())) {
				$bAllow = false;
			}
			if (amopt_strlen(trim($this->arOptions['ext']['options']['EXCLUDE_DEFER'])) > 0 && \CAmminaOptimizer::doMathPageToRules($this->arOptions['ext']['options']['INCLUDE_DEFER'], $APPLICATION->GetCurPage())) {
				$bAllow = true;
			}
			if (defined("AMMINA_OPTIMIZER_STOP_DEFER")) {
				if (AMMINA_OPTIMIZER_STOP_DEFER === "Y") {
					$bAllow = false;
				} elseif (AMMINA_OPTIMIZER_STOP_DEFER === "N") {
					$bAllow = true;
				}
			}
			if ($bAllow) {
				$this->arAllJSScript = Application::getInstance()->getParser()->getAllJsScript(true, true);
				foreach ($this->arAllJSScript as $k => $arJsLink) {
					Application::getInstance()->getParser()->setDeferForJsScript($k);
					/*
					if (amopt_strlen($arJsLink['CONTENT']) > 0) {
						Application::getInstance()->getParser()->setDeferForInlineJsScript($k, $arJsLink['CONTENT']);
					} else {
						Application::getInstance()->getParser()->setDeferForJsScript($k);
					}*/
				}
				Application::getInstance()->getParser()->doAppendDeferInitScript();
			}
		}
	}

	public function doOptimize($bStoreScriptInSession = false, $bIsComposite = false)
	{
		$this->__doOptimize($bStoreScriptInSession);
		$this->arAllJSScript = Application::getInstance()->getParser()->getAllJsScript(true, true);
		foreach ($this->arAllJSScript as $k => $arJsLink) {
			if (amopt_strlen($arJsLink['src']) > 0 && amopt_strpos($arJsLink['src'], 'data:') !== 0) {
				$this->arHeadersPreloadFiles[] = array(
					"FILE" => $arJsLink['src'],
					"TYPE" => "SCRIPT",
				);
			} elseif ($bIsComposite && amopt_strlen($arJsLink['CONTENT']) > 0 && amopt_strpos($arJsLink['CONTENT'], 'newElComposite') === false) {
				if ($this->arOptions['ext']['options']['COMPOSITE_MOVE_END'] == "Y") {
					if ($this->arOptions['ext']['options']['COMPOSITE_MOVE_TIMEOUT'] == "Y") {
						$content = 'window.addEventListener("onload",function(){window.setTimeout(function(){ var newElComposite = document.createElement(\'script\');newElComposite.textContent=' . \CUtil::PhpToJSObject($arJsLink['CONTENT']) . ';document.body.appendChild(newElComposite);},' . (intval($this->arOptions['ext']['options']['COMPOSITE_MOVE_TIMEOUT_VALUE'])) . ');});';
					} else {
						$content = 'window.addEventListener("onload",function(){var newElComposite = document.createElement(\'script\');newElComposite.textContent=' . \CUtil::PhpToJSObject($arJsLink['CONTENT']) . ';document.body.appendChild(newElComposite);});';
					}
					Application::getInstance()->getParser()->setContentForJsScript($k, $content);
				}
			}
		}
	}

	public function __doOptimize($bStoreScriptInSession = false)
	{
		global $APPLICATION;
		if (in_array($this->arOptions['ext']['options']['JOIN_MODEL'], array("notjoin", "onlypreload"))) {
			if ($this->arOptions['ext']['options']['JOIN_MODEL'] == "notjoin") {
				$this->arAllJSScript = Application::getInstance()->getParser()->getAllJsScript(true, true);
				$arAllLinks = $this->doOptimizeJsLinks();

				foreach ($this->arAllJSScript as $k => $arJsLink) {
					if (amopt_strlen($arJsLink['OPTIMIZE_FILE']) > 0) {
						Application::getInstance()->getParser()->setSrcForJsScript($k, $arJsLink['OPTIMIZE_FILE']);
					}
				}
			} else {
				//$this->arAllJSScript = Application::getInstance()->getParser()->getAllJsScript(true, true);
			}
			/*
			foreach ($this->arAllJSScript as $k => $arJsLink) {
				if (amopt_strlen($arJsLink['OPTIMIZE_FILE']) > 0) {
					$this->arHeadersPreloadFiles[] = array(
						"FILE" => $arJsLink['OPTIMIZE_FILE'],
						"TYPE" => "SCRIPT",
					);
				} elseif (amopt_strlen($arJsLink['src']) > 0) {
					$this->arHeadersPreloadFiles[] = array(
						"FILE" => $arJsLink['src'],
						"TYPE" => "SCRIPT",
					);
				}
			}*/
			$this->doSetDeferAttribute();
			return;
		}
		if ($this->arOptions['outline_js']['options']['ACTIVE'] == "Y") {
			$strIncludeContent = trim($this->arOptions['outline_js']['options']['INCLUDE_CONTENT']);
			$strExcludeContent = trim($this->arOptions['outline_js']['options']['EXCLUDE_CONTENT']);
			$this->arAllJSScript = Application::getInstance()->getParser()->getAllJsScript();
			foreach ($this->arAllJSScript as $iScript => $arScript) {
				$strContent = trim($arScript['CONTENT']);
				if (amopt_strpos($strContent, 'AMOPT DELAY SCRIPT') !== false) {
					continue;
				}
				if (amopt_strlen($strContent) > 0) {
					$bAllowOutline = true;
					if (amopt_strlen($strIncludeContent) > 0) {
						$bAllowOutline = \CAmminaOptimizer::doMathContentToRules($strIncludeContent, $strContent);
					}
					if ($bAllowOutline && amopt_strlen($strExcludeContent) > 0) {
						$bAllowOutline = !\CAmminaOptimizer::doMathContentToRules($strExcludeContent, $strContent);
					}
					if ($bAllowOutline) {
						$strIdent = md5($strContent);
						$strCacheFile = "/bitrix/ammina.cache/js.outline/" . SITE_ID . "/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".js";
						if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) || Application::getInstance()->isClearCache("js")) {
							\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, '/* Ammina JS ouline file */' . "\n" . $strContent);
						}
						if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile)) {
							if (filemtime($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) < (time() - 3600)) {
								@touch($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, time());
							}
							Application::getInstance()->getParser()->setSrcForJsOutlineScript($iScript, $strCacheFile);
						}
					}
				}
			}
		}

		//Получить все script
		$this->arAllJSScript = Application::getInstance()->getParser()->getAllJsScript(false, true);
		if ($this->arOptions['ext']['options']['CHECK_CORE_FILES'] == "Y") {
			$arMoved = array();
			$iMovedBefore = false;
			$bStart = false;
			foreach ($this->arAllJSScript as $i => $arScript) {
				if ($iMovedBefore === false && amopt_strlen(trim($arScript['src'])) > 0) {
					$iMovedBefore = $i;// + 1;
				}
				if ($bStart === false) {
					if (amopt_strpos($arScript['CONTENT'], 'if(!window.BX)window.BX={};') !== false) {
						$arMoved[] = $i;
						$bStart = true;
					}
				} elseif ($bStart === true) {
					if (isset($arScript['CONTENT']) && amopt_strlen($arScript['CONTENT']) > 0) {
						$arMoved[] = $i;
					} else {
						$bStart = 0;
					}
				} else {
					if ($iMovedBefore !== false) {
						//break;
						if (amopt_strpos($arScript['CONTENT'], '(window.BX||top.BX).message({') === 0) {
							$strEndPos = amopt_strpos($arScript['CONTENT'], '})');
							if ($strEndPos >= amopt_strlen($arScript['CONTENT']) - 4) {
								$arMoved[] = $i;
							}
						} elseif (amopt_strpos($arScript['CONTENT'], 'BX.message({') === 0) {
							$strEndPos = amopt_strpos($arScript['CONTENT'], '})');
							if ($strEndPos >= amopt_strlen($arScript['CONTENT']) - 4) {
								$arMoved[] = $i;
							}
						}
					}
				}
			}
			if (!empty($arMoved) && $iMovedBefore !== false) {
				foreach ($arMoved as $val) {
					//if ($val>$iMovedBefore) {
					Application::getInstance()->getParser()->moveJsScriptBefore($val, $iMovedBefore);
					//}
				}
				$this->arAllJSScript = Application::getInstance()->getParser()->getAllJsScript(false, true);
			}
		}

		$arAllLinks = $this->doOptimizeJsLinks();
		if ($bStoreScriptInSession) {
			Application::getInstance()->doStoreScriptsInSession($arAllLinks);
		}

		if ($this->arOptions['ext']['options']['JOIN_MODEL'] == "keeporder") {
			$arBundles = array();
			$bOldIsFile = false;
			foreach ($this->arAllJSScript as $k => $v) {
				if (isset($v['OPTIMIZE_FILE']) && amopt_strlen($v['OPTIMIZE_FILE']) > 0) {
					if ($bOldIsFile) {
						$arBundles[count($arBundles) - 1]['FILES'][$k] = $v;
					} else {
						$bOldIsFile = true;
						$arBundles[] = array('FILES' => array($k => $v));
					}
				} else {
					/*if (amopt_strlen($v['src']) > 0) {
						$this->arHeadersPreloadFiles[] = array(
							"FILE" => $v['src'],
							"TYPE" => "SCRIPT",
						);
					}*/
					$bOldIsFile = false;
				}
			}
			foreach ($arBundles as $k => $v) {
				if (count($v['FILES']) > 1) {
					$arBundles[$k]['BUNDLE'] = $this->doMakeBundleFile($v['FILES']);
					$bFirst = true;
					foreach ($v['FILES'] as $k1 => $v1) {
						if ($bFirst) {
							Application::getInstance()->getParser()->setSrcForJsScript($k1, $arBundles[$k]['BUNDLE']);
							$bFirst = false;
						} else {
							Application::getInstance()->getParser()->removeJsScript($k1);
						}
					}
				} else {
					$ak = array_keys($v['FILES']);
					$arBundles[$k]['BUNDLE'] = $v['FILES'][$ak[0]]['OPTIMIZE_FILE'];
					Application::getInstance()->getParser()->setSrcForJsScript($ak[0], $arBundles[$k]['BUNDLE']);
				}
				/*$this->arHeadersPreloadFiles[] = array(
					"FILE" => $arBundles[$k]['BUNDLE'],
					"TYPE" => "SCRIPT",
				);*/
			}
			$this->doSetDeferAttribute();
			return;
		}

		$this->strMainResultFile = $this->doMakeResultFile();

		if ($this->arOptions['other']['options']['INLINE_JS'] == "Y" && filesize($_SERVER['DOCUMENT_ROOT'] . $this->strMainResultFile) <= $this->arOptions['other']['options']['MAX_SIZE_INLINE']) {
			/*foreach ($this->arAllCssLinks as $k => $arCssLink) {
				if (amopt_strlen($arCssLink['OPTIMIZE']) > 0) {
					Application::getInstance()->getParser()->removeCssLink($k);
				}
			}
			if ($this->arOptions['other']['options']['INLINE_BEFORE_BODY'] == "Y") {
				Application::getInstance()->getParser()->AddTag("//body[1]", "style", array(), file_get_contents($_SERVER['DOCUMENT_ROOT'] . $this->strMainResultFile));
			} else {
				Application::getInstance()->getParser()->AddTag("//head[1]", "style", array(), file_get_contents($_SERVER['DOCUMENT_ROOT'] . $this->strMainResultFile));
			}
			*/
		} else {
			$isFillAttr = false;
			foreach ($this->arAllJSScript as $k => $arJsLink) {
				if (amopt_strlen($arJsLink['OPTIMIZE_FILE']) > 0) {
					if (!$isFillAttr) {
						Application::getInstance()->getParser()->setSrcForJsScript($k, $this->strMainResultFile);
						$isFillAttr = true;
					} else {
						Application::getInstance()->getParser()->removeJsScript($k);
					}
				}
			}
			/*if (file_exists($_SERVER['DOCUMENT_ROOT'] . $this->strMainResultFile) && filesize($_SERVER['DOCUMENT_ROOT'] . $this->strMainResultFile) > 0) {
				$this->arHeadersPreloadFiles[] = array(
					"FILE" => $this->strMainResultFile,
					"TYPE" => "SCRIPT",
				);
			}*/
		}
		$this->doSetDeferAttribute();
	}

	protected function doOptimizeJsLinks()
	{
		$arResult = array();
		foreach ($this->arAllJSScript as $k => $arJsLink) {
			if (isset($arJsLink['src']) && amopt_strlen($arJsLink['src']) > 0 && (!isset($arJsLink['type']) || $arJsLink['type'] == "text/javascript")) {
				if ((isset($arJsLink['extension']) && $arJsLink['extension'] == "js") || !isset($arJsLink['extension'])) {
					$this->arAllJSScript[$k]['OPTIMIZE_FILE'] = $this->doOptimizeJsFile($arJsLink['src']);
					$arResult[] = $arJsLink['src'];
				}
			}
			//$this->arAllCssLinks[$k]['OPTIMIZE'] = $this->doOptimizeCssFile($arCssLink['href']);
		}
		return $arResult;
	}

	protected function doOptimizeJsFile($strFileName)
	{
		global $APPLICATION;
		if (\CAmminaOptimizer::doMathPageToRules($this->arOptions['other']['options']['EXCLUDE_FILES'], $strFileName)) {
			return false;
		}
		if (amopt_strpos($strFileName, '://') !== false || amopt_strpos($strFileName, '//') === 0) {
			$strTmpFileName = $strFileName;
			if (\CAmminaOptimizer::checkRequestDomainUrl($strTmpFileName)) {
				$strFileName = $strTmpFileName;
			}
		}
		if (amopt_strpos($strFileName, '://') !== false || amopt_strpos($strFileName, '//') === 0) {
			return $this->doOptimizeRemoteJsFile($strFileName);
		} else {
			$strFileNameOriginal = $strFileName;
			$strFileName = explode("?", $strFileName);
			$strFileName = $strFileName[0];
			if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strFileName) && amopt_strpos($strFileName, '%') !== false) {
				$strFileName = urldecode($strFileName);
			}
			if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strFileName)) {
				return false;
			}
			if (amopt_strpos($strFileName, '/bitrix/ammina.cache/') === 0) {
				$strIdent = md5(
					serialize(
						array(
							"fileName" => $strFileNameOriginal,
							"mtime" => filesize($_SERVER['DOCUMENT_ROOT'] . $strFileName),
							"extCache" => $this->strBaseIdent,
						)
					)
				);
			} else {
				$strIdent = md5(
					serialize(
						array(
							"fileName" => $strFileNameOriginal,
							"mtime" => filemtime($_SERVER['DOCUMENT_ROOT'] . $strFileName),
							"extCache" => $this->strBaseIdent,
						)
					)
				);
			}
			$strCacheFile = "/bitrix/ammina.cache/js/ammina.optimizer/" . SITE_ID . "/atom/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".js";
			$strCacheFileTmp = "/bitrix/ammina.cache/js/ammina.optimizer/" . SITE_ID . "/atom/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".tmp.js";
			$strOptimizedFileWait = "/bitrix/ammina.cache/js/ammina.optimizer/" . SITE_ID . "/atom/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".js.wait";
			$strOptimizedFileInfo = "/bitrix/ammina.cache/js/ammina.optimizer/" . SITE_ID . "/atom/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".js.info";
			if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) || Application::getInstance()->isClearCache("js")) {
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileWait)) {
					@unlink($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileWait);
				}
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileInfo)) {
					@unlink($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileInfo);
				}
				$strContent = ';' . Application::getInstance()->normalized_file_get_content($_SERVER['DOCUMENT_ROOT'] . $strFileName) . ';';
				\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, '/* Ammina JS file original ' . $strFileName . ' */' . "\n" . $strContent);
			} else {
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) && filemtime($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) < (time() - 3600)) {
					@touch($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, time());
					if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileWait)) {
						@touch($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileWait, time());
					}
					if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileInfo)) {
						@touch($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileInfo, time());
					}
				}
			}
			$this->doCheckMinify($strFileName, $strCacheFile, $strCacheFileTmp, $strOptimizedFileWait, $strOptimizedFileInfo);

			if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile)) {
				return $strCacheFile;
			}
		}
	}

	protected function doOptimizeRemoteJsFile($strFileName)
	{
		global $APPLICATION;
		if ($this->arOptions['external_js']['options']['ACTIVE'] == "Y" && !\CAmminaOptimizer::isLocalDomainLink($strFileName)) {
			if (\CAmminaOptimizer::doMathPageToRules($this->arOptions['external_js']['options']['EXCLUDE'], $strFileName) && !\CAmminaOptimizer::doMathPageToRules($this->arOptions['external_js']['options']['INCLUDE'], $strFileName)) {
				return false;
			}
			$strIdent = md5($strFileName);
			$strCacheFile = "/bitrix/ammina.cache/js.remote/" . SITE_ID . "/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".js";
			if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) || Application::getInstance()->isClearCache("js")) {
				$strContent = \CAmminaOptimizer::doRequestPageRemote($strFileName, '', 10, 5);
				if (amopt_strlen($strContent) <= 0) {
					return false;
				}
				\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, '/* Ammina JS remote file ' . $strFileName . ' */' . "\n" . $strContent);
			}
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile)) {
				if (filemtime($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) < (time() - 3600)) {
					@touch($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, time());
				}
				$strResult = $this->doOptimizeJsFile($strCacheFile);
				return $strResult;
			}
		}
		return false;
	}

	protected function doCheckMinify($strFileName, $strCacheFile, $strCacheFileTmp, $strOptimizedFileWait, $strOptimizedFileInfo)
	{
		if ($this->arOptions['minify']['options']['ACTIVE'] == "Y") {
			$bMakeMinimized = true;
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileWait)) {
				if (file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileWait) >= (time() - 60)) {
					$bMakeMinimized = false;
				}
			} elseif (file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileInfo)) {
				$bMakeMinimized = false;
			}
			if ($bMakeMinimized) {
				if (\CAmminaOptimizer::doMathPageToRules($this->arOptions['minify']['options']['EXCLUDE'], $strFileName) && !\CAmminaOptimizer::doMathPageToRules($this->arOptions['minify']['options']['INCLUDE'], $strFileName)) {
					$bResult = true;
					if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileInfo)) {
						$arInfo = array(
							"SOURCE" => $strFileName,
							"RESULT" => $strCacheFile,
							"SOURCE_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strFileName),
							"RESULT_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strCacheFile),
						);
						\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileInfo, serialize($arInfo));
					}
				} else {
					\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileWait, time());
					$bResult = false;
					$bDoubleConvertEncoding = false;
					if (!defined("BX_UTF") || BX_UTF === true) {
						$bDoubleConvertEncoding = (amopt_strlen(LANG_CHARSET) > 0 ? LANG_CHARSET : SITE_CHARSET);
						if (amopt_strlen($bDoubleConvertEncoding) <= 0) {
							$bDoubleConvertEncoding = "WINDOWS-1251";
						}
					}
					/*if ($this->arOptions['minify']['options']['LIBRARY'] == "phpwee") {
						$bResult = $this->oDriverPHPWee->optimizeJs($strFileName, $strCacheFile, $strCacheFileTmp, $strOptimizedFileInfo, $this->arOptions['minify']['options_variant']['phpwee'], $bDoubleConvertEncoding);
					} elseif ($this->arOptions['minify']['options']['LIBRARY'] == "matthiasmullie") {
						$bResult = $this->oDriverMatthiasMullie->optimizeJs($strFileName, $strCacheFile, $strCacheFileTmp, $strOptimizedFileInfo, $this->arOptions['minify']['options_variant']['matthiasmullie'], $bDoubleConvertEncoding);
					} elseif ($this->arOptions['minify']['options']['LIBRARY'] == "yuicompressor") {
						$bResult = $this->oDriverYUICompressor->optimizeJs($strFileName, $strCacheFile, $strCacheFileTmp, $strOptimizedFileInfo, $this->arOptions['minify']['options_variant']['yuicompressor'], $bDoubleConvertEncoding);
					} else*/
					if ($this->arOptions['minify']['options']['LIBRARY'] == "uglifyjs") {
						$bResult = $this->oDriverUglifyJS->optimizeJs($strFileName, $strCacheFile, $strCacheFileTmp, $strOptimizedFileInfo, $this->arOptions['minify']['options_variant']['uglifyjs'], $bDoubleConvertEncoding);
					} elseif ($this->arOptions['minify']['options']['LIBRARY'] == "uglifyjs2") {
						$bResult = $this->oDriverUglifyJS2->optimizeJs($strFileName, $strCacheFile, $strCacheFileTmp, $strOptimizedFileInfo, $this->arOptions['minify']['options_variant']['uglifyjs2'], $bDoubleConvertEncoding);
					} elseif ($this->arOptions['minify']['options']['LIBRARY'] == "terserjs") {
						$bResult = $this->oDriverTerserJS->optimizeJs($strFileName, $strCacheFile, $strCacheFileTmp, $strOptimizedFileInfo, $this->arOptions['minify']['options_variant']['terserjs'], $bDoubleConvertEncoding);
					} elseif ($this->arOptions['minify']['options']['LIBRARY'] == "babelminify") {
						$bResult = $this->oDriverBabelMinify->optimizeJs($strFileName, $strCacheFile, $strCacheFileTmp, $strOptimizedFileInfo, $this->arOptions['minify']['options_variant']['babelminify'], $bDoubleConvertEncoding);
					} else/*if ($this->arOptions['minify']['options']['LIBRARY'] == "amminayuicompressor") {
						$bResult = $this->oDriverAmminaYUICompressor->optimizeJs($strFileName, $strCacheFile, $strCacheFileTmp, $strOptimizedFileInfo, $this->arOptions['minify']['options_variant']['amminayuicompressor'], $bDoubleConvertEncoding);
					} else*/ {
						if ($this->arOptions['minify']['options']['LIBRARY'] == "amminauglifyjs") {
							$bResult = $this->oDriverAmminaUglifyJS->optimizeJs($strFileName, $strCacheFile, $strCacheFileTmp, $strOptimizedFileInfo, $this->arOptions['minify']['options_variant']['amminauglifyjs'], $bDoubleConvertEncoding);
						} elseif ($this->arOptions['minify']['options']['LIBRARY'] == "amminauglifyjs2") {
							$bResult = $this->oDriverAmminaUglifyJS2->optimizeJs($strFileName, $strCacheFile, $strCacheFileTmp, $strOptimizedFileInfo, $this->arOptions['minify']['options_variant']['amminauglifyjs2'], $bDoubleConvertEncoding);
						} elseif ($this->arOptions['minify']['options']['LIBRARY'] == "amminaterserjs") {
							$bResult = $this->oDriverAmminaTerserJS->optimizeJs($strFileName, $strCacheFile, $strCacheFileTmp, $strOptimizedFileInfo, $this->arOptions['minify']['options_variant']['amminaterserjs'], $bDoubleConvertEncoding);
						} elseif ($this->arOptions['minify']['options']['LIBRARY'] == "amminababelminify") {
							$bResult = $this->oDriverAmminaBabelMinify->optimizeJs($strFileName, $strCacheFile, $strCacheFileTmp, $strOptimizedFileInfo, $this->arOptions['minify']['options_variant']['amminababelminify'], $bDoubleConvertEncoding);
						}
					}
				}
				if ($bResult) {
					if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileWait)) {
						@unlink($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileWait);
					}
				}
			}
		} else {
			$arInfo = array(
				"SOURCE" => $strFileName,
				"RESULT" => $strCacheFile,
				"SOURCE_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strFileName),
				"RESULT_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strCacheFile),
			);
			\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileInfo, serialize($arInfo));
		}
	}

	protected function doMakeResultFile()
	{
		global $APPLICATION;
		$arAllAtomFiles = array();
		foreach ($this->arAllJSScript as $k => $arJsScript) {
			if (amopt_strlen($arJsScript['OPTIMIZE_FILE']) > 0 && file_exists($_SERVER['DOCUMENT_ROOT'] . $arJsScript['OPTIMIZE_FILE'])) {
				$arAllAtomFiles[$arJsScript['OPTIMIZE_FILE']] = array(
					"FILE" => $arJsScript['OPTIMIZE_FILE'],
					//"MKTIME" => filemtime($_SERVER['DOCUMENT_ROOT'] . $arJsScript['OPTIMIZE_FILE']),
					"SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $arJsScript['OPTIMIZE_FILE']),
				);
			}
		}
		ksort($arAllAtomFiles);
		$strIdent = md5(serialize($arAllAtomFiles) . $this->strBaseIdent);
		$strCacheFile = "/bitrix/ammina.cache/js/ammina.optimizer/" . SITE_ID . "/full/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".js";
		$strCacheFileInfo = "/bitrix/ammina.cache/js/ammina.optimizer/" . SITE_ID . "/full/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".js.info";
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) || Application::getInstance()->isClearCache("js")) {
			$strFullContent = "";
			$arInfo = array(
				"ATOM_FILES" => array(),
				"SOURCE_FILES" => array(),
				"SOURCE_FILES_SIZE" => 0,
				"RESULT_FILE_SIZE" => 0,
			);
			foreach ($this->arAllJSScript as $k => $arJsScript) {
				if (amopt_strlen($arJsScript['OPTIMIZE_FILE']) > 0) {
					$strFullContent .= file_get_contents($_SERVER['DOCUMENT_ROOT'] . $arJsScript['OPTIMIZE_FILE']);
					$arAtomInfo = unserialize(file_get_contents($_SERVER['DOCUMENT_ROOT'] . $arJsScript['OPTIMIZE_FILE'] . ".info"));
					$arInfo['ATOM_FILES'][] = $arJsScript['OPTIMIZE_FILE'];
					$arInfo['SOURCE_FILES'][] = $arAtomInfo['SOURCE'];
					$arInfo['SOURCE_FILES_SIZE'] += $arAtomInfo['SOURCE_SIZE'];
				}
			}
			\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, $strFullContent);
			$arInfo['RESULT_FILE_SIZE'] = filesize($_SERVER['DOCUMENT_ROOT'] . $strCacheFile);
			\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFileInfo, serialize($arInfo));
		} else {
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) && filemtime($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) < (time() - 3600)) {
				@touch($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, time());
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFileInfo)) {
					@touch($_SERVER['DOCUMENT_ROOT'] . $strCacheFileInfo, time());
				}
			}
		}
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile)) {
			return $strCacheFile;
		}
	}

	protected function doMakeBundleFile($arFiles)
	{
		global $APPLICATION;
		$arAllAtomFiles = array();
		foreach ($arFiles as $k => $arJsScript) {
			if (amopt_strlen($arJsScript['OPTIMIZE_FILE']) > 0 && file_exists($_SERVER['DOCUMENT_ROOT'] . $arJsScript['OPTIMIZE_FILE'])) {
				$arAllAtomFiles[$arJsScript['OPTIMIZE_FILE']] = array(
					"FILE" => $arJsScript['OPTIMIZE_FILE'],
					//"MKTIME" => filemtime($_SERVER['DOCUMENT_ROOT'] . $arJsScript['OPTIMIZE_FILE']),
					"SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $arJsScript['OPTIMIZE_FILE']),
				);
			}
		}
		//ksort($arAllAtomFiles);
		$strIdent = md5(serialize($arAllAtomFiles) . $this->strBaseIdent);
		$strCacheFile = "/bitrix/ammina.cache/js/ammina.optimizer/" . SITE_ID . "/full/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".js";
		$strCacheFileInfo = "/bitrix/ammina.cache/js/ammina.optimizer/" . SITE_ID . "/full/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".js.info";
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) || Application::getInstance()->isClearCache("js")) {
			$strFullContent = "";
			$arInfo = array(
				"ATOM_FILES" => array(),
				"SOURCE_FILES" => array(),
				"SOURCE_FILES_SIZE" => 0,
				"RESULT_FILE_SIZE" => 0,
			);
			foreach ($arFiles as $k => $arJsScript) {
				if (amopt_strlen($arJsScript['OPTIMIZE_FILE']) > 0) {
					$strFullContent .= file_get_contents($_SERVER['DOCUMENT_ROOT'] . $arJsScript['OPTIMIZE_FILE']);
					$arAtomInfo = unserialize(file_get_contents($_SERVER['DOCUMENT_ROOT'] . $arJsScript['OPTIMIZE_FILE'] . ".info"));
					$arInfo['ATOM_FILES'][] = $arJsScript['OPTIMIZE_FILE'];
					$arInfo['SOURCE_FILES'][] = $arAtomInfo['SOURCE'];
					$arInfo['SOURCE_FILES_SIZE'] += $arAtomInfo['SOURCE_SIZE'];
				}
			}
			\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, $strFullContent);
			$arInfo['RESULT_FILE_SIZE'] = filesize($_SERVER['DOCUMENT_ROOT'] . $strCacheFile);
			\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFileInfo, serialize($arInfo));
		} else {
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) && filemtime($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) < (time() - 3600)) {
				@touch($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, time());
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFileInfo)) {
					@touch($_SERVER['DOCUMENT_ROOT'] . $strCacheFileInfo, time());
				}
			}
		}
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile)) {
			return $strCacheFile;
		}
	}

	public function doMoveJsBodyEnd()
	{
		Application::getInstance()->getParser()->moveJsToBodyEnd();
	}
}
