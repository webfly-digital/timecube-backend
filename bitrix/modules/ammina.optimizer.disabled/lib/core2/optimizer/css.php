<?

namespace Ammina\Optimizer\Core2\Optimizer;

use Ammina\Optimizer\Core2\Application;
use Ammina\Optimizer\Core2\Driver\Css\AmminaUglifyCss;
use Ammina\Optimizer\Core2\Driver\Css\AmminaYUICompressor;
use Ammina\Optimizer\Core2\Driver\Css\MatthiasMullie;
use Ammina\Optimizer\Core2\Driver\Css\PHPWee;
use Ammina\Optimizer\Core2\Driver\Css\UglifyCss;
use Ammina\Optimizer\Core2\Driver\Css\YUICompressor;
use Sabberworm\CSS\CSSList\AtRuleBlockList;
use Sabberworm\CSS\CSSList\CSSBlockList;
use Sabberworm\CSS\CSSList\Document;
use Sabberworm\CSS\CSSList\KeyFrame;
use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Parser;
use Sabberworm\CSS\Property\Import;
use Sabberworm\CSS\Property\Selector;
use Sabberworm\CSS\Rule\Rule;
use Sabberworm\CSS\RuleSet\AtRuleSet;
use Sabberworm\CSS\RuleSet\DeclarationBlock;
use Sabberworm\CSS\Settings;
use Sabberworm\CSS\Value\CSSFunction;
use Sabberworm\CSS\Value\CSSString;
use Sabberworm\CSS\Value\RuleValueList;
use Sabberworm\CSS\Value\URL;
use Sabberworm\CSS\Value\Value;

class CSS
{
	protected $arOptions = false;
	protected $strBaseIdent = "";
	public $arAllUsedClasses = array();
	public $arPartAllUsedClasses = array();
	protected $arAllCssLinks = array();
	protected $arAllCssStyle = array();
	protected $arInlineTypes = array(
		"png" => "image/png",
		"gif" => "image/gif",
		"jpg" => "image/jpeg",
		"jpeg" => "image/jpeg",
		"svg" => "image/svg+xml",
	);
	protected $strMainResultFile = false;
	protected $strMainCriticalResultFile = false;
	protected $strMainNonCriticalResultFile = false;
	protected $strMainFontResultFile = false;
	protected $strMainNonFontResultFile = false;
	protected $oldCriticalIdent = "";
	public $arHeadersPreloadFiles = array();
	public $oParserSettings = null;
	/**
	 * @var PHPWee
	 */
	//protected $oDriverPHPWee = null;
	/**
	 * @var MatthiasMullie
	 */
	//protected $oDriverMatthiasMullie = null;
	/**
	 * @var UglifyCss
	 */
	//protected $oDriverUglifyCSS = null;
	/**
	 * @var YUICompressor
	 */
	//protected $oDriverYUICompressor = null;
	/**
	 * @var AmminaUglifyCss
	 */
	//protected $oDriverAmminaUglifyCSS = null;
	/**
	 * @var AmminaYUICompressor
	 */
	//protected $oDriverAmminaYUICompressor = null;

	public $arCriticalFonts = array();

	public function __construct($arOptions)
	{
		/*$this->oDriverPHPWee = new PHPWee();
		$this->oDriverMatthiasMullie = new MatthiasMullie();
		$this->oDriverUglifyCSS = new UglifyCss();
		$this->oDriverYUICompressor = new YUICompressor();
		$this->oDriverAmminaUglifyCSS = new AmminaUglifyCss();
		$this->oDriverAmminaYUICompressor = new AmminaYUICompressor();*/
		$this->oParserSettings = Settings::create();
		if (!defined("BX_UTF") || BX_UTF !== true) {
			//$oParserSettings->withDefaultCharset((amopt_strlen(LANG_CHARSET) > 0 ? LANG_CHARSET : SITE_CHARSET));
			$this->oParserSettings->withMultibyteSupport(false);
		} else {
			//$oParserSettings->withDefaultCharset("UTF-8");
			//$this->oParserSettings->withMultibyteSupport(true);
		}
		$this->setOptions($arOptions);
	}

	public function setOptions($arOptions)
	{
		$this->arOptions = $arOptions;
		$this->strBaseIdent = $arOptions;
		unset($this->strBaseIdent['minify']['DEFAULT']);
		unset($this->strBaseIdent['external_css']['DEFAULT']);
		unset($this->strBaseIdent['images']['DEFAULT']);
		unset($this->strBaseIdent['fonts']['DEFAULT']);
		unset($this->strBaseIdent['critical']['DEFAULT']);
		unset($this->strBaseIdent['other']['DEFAULT']);
		unset($this->strBaseIdent['external_css']['options']['EXCLUDE']);
		unset($this->strBaseIdent['external_css']['options']['INCLUDE']);
		unset($this->strBaseIdent['other']['options']['EXCLUDE_FILES']);
		$this->strBaseIdent = md5(serialize($this->strBaseIdent));
	}

	public function doOptimizePart()
	{
		$this->arAllUsedClasses = array();
		$this->arAllCssLinks = array();
		$this->arAllCssStyle = array();
		$this->strMainResultFile = false;
		$this->strMainCriticalResultFile = false;
		$this->strMainNonCriticalResultFile = false;
		$this->strMainFontResultFile = false;
		$this->strMainNonFontResultFile = false;
		$this->oldCriticalIdent = "";
		$this->doOptimize();
		$this->arPartAllUsedClasses = $this->arAllUsedClasses;
		$this->arAllUsedClasses = array();
		$this->arAllCssLinks = array();
		$this->arAllCssStyle = array();
		$this->strMainResultFile = false;
		$this->strMainCriticalResultFile = false;
		$this->strMainNonCriticalResultFile = false;
		$this->strMainFontResultFile = false;
		$this->strMainNonFontResultFile = false;
		$this->oldCriticalIdent = "";
	}

	protected function doCheckIgnoredKeys($value, &$arListData)
	{
		if (strlen($value) > 0) {
			if (amopt_stripos($value, 'PART:') === 0) {
				$value = trim(amopt_substr($value, 5));
				if (strlen($value) > 0) {
					foreach ($arListData as $k1 => $v1) {
						if (amopt_stripos($k1, $value) !== false) {
							unset($arListData[$k1]);
						}
					}
				}
			} else {
				if (isset($arListData[$value])) {
					unset($arListData[$value]);
				}
			}
		}
	}

	protected function doCheckOnlyKeys($arOnlyKeys, &$arListData)
	{
		$arFullKeys = array();
		$arPartKeys = array();
		foreach ($arOnlyKeys as $k => $v) {
			if (amopt_stripos($v, 'PART:') === 0) {
				$v = trim(amopt_substr($v, 5));
				if (strlen($v) > 0) {
					$arPartKeys[] = $v;
				}
			} else {
				$arFullKeys[] = $v;
			}
		}
		if (count($arFullKeys) > 0 || count($arPartKeys) > 0) {
			foreach ($arListData as $k => $v) {
				$bFinded = false;
				if (in_array($k, $arFullKeys)) {
					$bFinded = true;
				} else {
					foreach ($arPartKeys as $checkPart) {
						if (amopt_stripos($k, $checkPart) !== false) {
							$bFinded = true;
							break;
						}
					}
				}
				if (!$bFinded) {
					unset($arListData[$k]);
				}
			}
		}
	}

	public function doOptimize($arAppendAllUsedClasses = false)
	{
		$this->arAllUsedClasses = Application::getInstance()->getParser()->getAllClassesAndIdent();

		$this->arAllUsedClasses['CLASSES_ALL'] = $this->arAllUsedClasses['CLASSES'];
		$this->arAllUsedClasses['IDENT_ALL'] = $this->arAllUsedClasses['IDENT'];

		if ($arAppendAllUsedClasses !== false && !empty($arAppendAllUsedClasses)) {
			$this->arAllUsedClasses = array_merge_recursive($this->arAllUsedClasses, $arAppendAllUsedClasses);
		}

		$arIgnoreClasses = $this->arOptions['critical']['options']['IGNORE_CLASS'];
		$arIgnoreClasses = str_replace("\n", ",", $arIgnoreClasses);
		$arIgnoreClasses = explode(",", $arIgnoreClasses);
		foreach ($arIgnoreClasses as $k => $v) {
			$v = trim(amopt_strtolower($v));
			$this->doCheckIgnoredKeys($v, $this->arAllUsedClasses["CLASSES"]);
		}
		$arOnlyClasses = $this->arOptions['critical']['options']['ONLY_CLASS'];
		$arOnlyClasses = str_replace("\n", ",", $arOnlyClasses);
		$arOnlyClasses = explode(",", $arOnlyClasses);
		foreach ($arOnlyClasses as $k => $v) {
			$arOnlyClasses[$k] = trim(amopt_strtolower($v));
			if (strlen($arOnlyClasses[$k]) <= 0) {
				unset($arOnlyClasses[$k]);
			}
		}
		$this->doCheckOnlyKeys($arOnlyClasses, $this->arAllUsedClasses["CLASSES"]);
		$arAddClasses = $this->arOptions['critical']['options']['ADD_CLASS'];
		$arAddClasses = str_replace("\n", ",", $arAddClasses);
		$arAddClasses = explode(",", $arAddClasses);
		foreach ($arAddClasses as $k => $v) {
			$v = trim(amopt_strtolower($v));
			if (amopt_strlen($v) > 0) {
				if (!isset($this->arAllUsedClasses["CLASSES"][$v])) {
					$this->arAllUsedClasses["CLASSES"][$v] = 1;
				}
			}
		}

		$arIgnoreIdent = $this->arOptions['critical']['options']['IGNORE_IDENT'];
		$arIgnoreIdent = str_replace("\n", ",", $arIgnoreIdent);
		$arIgnoreIdent = explode(",", $arIgnoreIdent);
		foreach ($arIgnoreIdent as $k => $v) {
			$v = trim(amopt_strtolower($v));
			$this->doCheckIgnoredKeys($v, $this->arAllUsedClasses["IDENT"]);
		}
		$arOnlyIdent = $this->arOptions['critical']['options']['ONLY_IDENT'];
		$arOnlyIdent = str_replace("\n", ",", $arOnlyIdent);
		$arOnlyIdent = explode(",", $arOnlyIdent);
		foreach ($arOnlyIdent as $k => $v) {
			$arOnlyIdent[$k] = trim(amopt_strtolower($v));
			if (amopt_strlen($arOnlyIdent[$k]) <= 0) {
				unset($arOnlyIdent[$k]);
			}
		}
		$this->doCheckOnlyKeys($arOnlyIdent, $this->arAllUsedClasses["IDENT"]);
		$arAddIdent = $this->arOptions['critical']['options']['ADD_IDENT'];
		$arAddIdent = str_replace("\n", ",", $arAddIdent);
		$arAddIdent = explode(",", $arAddIdent);
		foreach ($arAddIdent as $k => $v) {
			$v = trim(amopt_strtolower($v));
			if (amopt_strlen($v) > 0) {
				if (!isset($this->arAllUsedClasses["IDENT"][$v])) {
					$this->arAllUsedClasses["IDENT"][$v] = 1;
				}
			}
		}
		ksort($this->arAllUsedClasses["IDENT"], SORT_NATURAL);
		ksort($this->arAllUsedClasses["CLASSES"], SORT_NATURAL);

		if ($this->arOptions['outline_css']['options']['ACTIVE'] == "Y") {
			$strIncludeContent = trim($this->arOptions['outline_css']['options']['INCLUDE_CONTENT']);
			$strExcludeContent = trim($this->arOptions['outline_css']['options']['EXCLUDE_CONTENT']);
			$arAllCssStyle = Application::getInstance()->getParser()->getAllCssStyle();
			foreach ($arAllCssStyle as $iStyle => $arStyle) {
				$strContent = trim($arStyle['CONTENT']);
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
						$strCacheFile = "/bitrix/ammina.cache/css.outline/" . SITE_ID . "/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".css";
						if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) || Application::getInstance()->isClearCache("css")) {
							\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, '/* Ammina CSS ouline file */' . "\n" . $strContent);
						}
						if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile)) {
							if (filemtime($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) < (time() - 3600)) {
								@touch($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, time());
							}
							Application::getInstance()->getParser()->replaceCssStyleToLink($iStyle, $strCacheFile);
						}
					}
				}
			}
		}

		//Получить все link и style
		$this->arAllCssLinks = Application::getInstance()->getParser()->getAllCssLinks();

		$this->doOptimizeCssLinks();
		/**
		 * @todo Добавить оптимизацияю inline
		 */

		$this->strMainResultFile = $this->doMakeResultFile();
		if ($this->arOptions['other']['options']['INLINE_CSS'] == "Y" && filesize($_SERVER['DOCUMENT_ROOT'] . $this->strMainResultFile) <= $this->arOptions['other']['options']['MAX_SIZE_INLINE']) {
			foreach ($this->arAllCssLinks as $k => $arCssLink) {
				if (amopt_strlen($arCssLink['OPTIMIZE']) > 0) {
					Application::getInstance()->getParser()->removeCssLink($k);
				}
			}
			if ($this->arOptions['other']['options']['INLINE_BEFORE_BODY'] == "Y") {
				Application::getInstance()->getParser()->AddTag("//body[1]", "style", array(), file_get_contents($_SERVER['DOCUMENT_ROOT'] . $this->strMainResultFile));
			} else {
				Application::getInstance()->getParser()->AddTag("//head[1]", "style", array(), file_get_contents($_SERVER['DOCUMENT_ROOT'] . $this->strMainResultFile));
			}
		} else {
			if ($this->arOptions['critical']['options']['ACTIVE'] == "Y") {
				//Выделить весь критический код (в т.ч. шрифты). Разместить инлайн в хеад. Линк переместить в конец боди
				$this->oldCriticalIdent = "";
				$this->strMainCriticalResultFile = $this->doMakeCriticalFile();
				if ($this->arOptions['critical']['options']['USE_UNCRITICAL'] == "Y") {
					$this->strMainNonCriticalResultFile = $this->doMakeNonCriticalFile();
				}
				foreach ($this->arAllCssLinks as $k => $arCssLink) {
					if (amopt_strlen($arCssLink['OPTIMIZE']) > 0) {
						Application::getInstance()->getParser()->removeCssLink($k);
					}
				}
				Application::getInstance()->getParser()->AddTag("//head[1]", "style", array("data-critical" => "yes"), file_get_contents($_SERVER['DOCUMENT_ROOT'] . $this->strMainCriticalResultFile));
				$strCssFile = $this->strMainResultFile;
				if ($this->arOptions['critical']['options']['USE_UNCRITICAL'] == "Y") {
					$strCssFile = $this->strMainNonCriticalResultFile;
				}
				if ($this->arOptions['critical']['options']['MODE_FULL'] == "script") {
					$strScript = 'var aml=document.createElement(\'link\');aml.rel="stylesheet";aml.href="' . $strCssFile . '";document.body.appendChild(aml);';
					Application::getInstance()->getParser()->AddTag("//body[1]", "script", array("async" => null), $strScript);
				} elseif ($this->arOptions['critical']['options']['MODE_FULL'] == "scriptwait") {
					$strScript = 'window.setTimeout(function(){var aml=document.createElement(\'link\');aml.rel="stylesheet";aml.href="' . $strCssFile . '";document.body.appendChild(aml);},' . intval($this->arOptions['critical']['options']['WAIT_FULL']) . ');';
					//$strScript='$(document).ready(function(){$.ajax("'.$strCssFile.'",{cache:true,success:function(data){$("body").append($("<style>"+data+"</style>"));}})});';
					Application::getInstance()->getParser()->AddTag("//body[1]", "script", array("async" => null), $strScript);
				} else {
					Application::getInstance()->getParser()->AddTag("//body[1]", "link", array("rel" => "stylesheet", "href" => $strCssFile));
					//Application::getInstance()->getParser()->AddTag("//body[1]", "link", array("rel" => "prefetch", "as" => "style", "onload"=>"this.rel='stylesheet'", "href" => $strCssFile));
				}
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCssFile) && filesize($_SERVER['DOCUMENT_ROOT'] . $strCssFile) > 0) {
					$this->arHeadersPreloadFiles[] = array(
						"FILE" => $strCssFile,
						"TYPE" => "STYLE",
					);
				}
			} elseif ($this->arOptions['fonts']['options']['ACTIVE'] == "Y" && $this->arOptions['fonts']['options']['INLINE'] == "Y") {
				//Выделить шрифты. Разместить инлайн
				$this->strMainFontResultFile = $this->doMakeFontFile();
				$this->strMainNonFontResultFile = $this->doMakeNonFontFile();
				$isFillAttr = false;
				$iCssLinkIndex = false;
				foreach ($this->arAllCssLinks as $k => $arCssLink) {
					if (amopt_strlen($arCssLink['OPTIMIZE']) > 0) {
						if (!$isFillAttr) {
							Application::getInstance()->getParser()->setHrefForCssLink($k, $this->strMainNonFontResultFile);
							$isFillAttr = true;
							$iCssLinkIndex = $k;
						} else {
							Application::getInstance()->getParser()->removeCssLink($k);
						}
					}
				}
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . $this->strMainNonFontResultFile) && filesize($_SERVER['DOCUMENT_ROOT'] . $this->strMainNonFontResultFile) > 0) {
					$this->arHeadersPreloadFiles[] = array(
						"FILE" => $this->strMainNonFontResultFile,
						"TYPE" => "STYLE",
					);
				}
				//Application::getInstance()->getParser()->AddTagBeforeCssLink($iCssLinkIndex, "style", array(), file_get_contents($_SERVER['DOCUMENT_ROOT'] . $this->strMainFontResultFile));
			} else {
				//Только объединить файлы. Удалить лишние
				$isFillAttr = false;
				foreach ($this->arAllCssLinks as $k => $arCssLink) {
					if (amopt_strlen($arCssLink['OPTIMIZE']) > 0) {
						if (!$isFillAttr) {
							Application::getInstance()->getParser()->setHrefForCssLink($k, $this->strMainResultFile);
							$isFillAttr = true;
						} else {
							Application::getInstance()->getParser()->removeCssLink($k);
						}
					}
				}
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . $this->strMainResultFile) && filesize($_SERVER['DOCUMENT_ROOT'] . $this->strMainResultFile) > 0) {
					$this->arHeadersPreloadFiles[] = array(
						"FILE" => $this->strMainResultFile,
						"TYPE" => "STYLE",
					);
				}
			}
		}
		$this->doOptimizeBxCss();
		$this->doOptimizeMoveToEnd();
	}

	protected function doOptimizeBxCss()
	{
		if ($this->arOptions['other']['options']['CHECK_BX_CSS'] == "Y") {
			$arScriptList = Application::getInstance()->getParser()->getAllJsScriptWithBxLoadCss();
			if (!empty($arScriptList)) {
				foreach ($arScriptList as $index => $arScript) {
					$strLowerContent = amopt_strtolower($arScript['CONTENT']);
					$iStart = amopt_strpos($strLowerContent, 'bx.loadcss([');
					$iEnd = amopt_strpos($strLowerContent, '])', $iStart + 1);
					if ($iStart !== false && $iEnd !== false) {
						$strCssContentFull = amopt_substr($arScript['CONTENT'], $iStart, $iEnd - $iStart + 2);
						$arListCss = amopt_substr($strCssContentFull, 11, amopt_strlen($strCssContentFull) - 12);
						$arListCss = ammina_JsObjectToPhp($arListCss);
						$oTmpOptimizer = new CSS($this->arOptions);
						$strResultFile = $oTmpOptimizer->doOptimizeCssFilesArray($arListCss);
						if (amopt_strlen($strResultFile) > 0) {
							$strNewContent = 'BX.loadCSS(' . \CUtil::PhpToJSObject(array($strResultFile)) . ')';
							$arScript['CONTENT'] = str_replace($arScript['CONTENT'], $strCssContentFull, $strNewContent);
							Application::getInstance()->getParser()->setJsScriptWithBxLoadCssContent($index, $arScript['CONTENT']);
							if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strResultFile) && filesize($_SERVER['DOCUMENT_ROOT'] . $strResultFile) > 0) {
								$this->arHeadersPreloadFiles[] = array(
									"FILE" => $strResultFile,
									"TYPE" => "STYLE",
								);
							}
						}
					}
				}
			}
		}
	}

	protected function doOptimizeMoveToEnd()
	{
		if ($this->arOptions['other']['options']['MOVE_STYLE_BOTTOM'] == "Y") {
			$arAllCssStyle = Application::getInstance()->getParser()->getAllCssStyle();
			foreach ($arAllCssStyle as $k => $v) {
				if ($v['attributes']['data-critical'] != "yes") {
					Application::getInstance()->getParser()->AddTag("//body[1]", "style", $v['attributes'], $v['CONTENT']);
					Application::getInstance()->getParser()->removeCssStyle($k);
				}
			}
		}
	}

	public function doOptimizeCssFilesArray($arFiles)
	{
		$this->arAllCssLinks = array();
		foreach ($arFiles as $strFile) {
			$this->arAllCssLinks[] = array(
				"href" => $strFile,
				"OPTIMIZE" => $this->doOptimizeCssFile($strFile),
			);
		}
		$this->strMainResultFile = $this->doMakeResultFile();
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . $this->strMainResultFile) && filesize($_SERVER['DOCUMENT_ROOT'] . $this->strMainResultFile) > 0) {
			$this->arHeadersPreloadFiles[] = array(
				"FILE" => $this->strMainResultFile,
				"TYPE" => "STYLE",
			);
		}
		return $this->strMainResultFile;
	}

	protected function doOptimizeCssLinks()
	{
		foreach ($this->arAllCssLinks as $k => $arCssLink) {
			$this->arAllCssLinks[$k]['OPTIMIZE'] = $this->doOptimizeCssFile($arCssLink['href']);
		}
	}

	protected function doMakeResultFile()
	{
		global $APPLICATION;
		$arAllAtomFiles = array();
		foreach ($this->arAllCssLinks as $k => $arCssLink) {
			if (amopt_strlen($arCssLink['OPTIMIZE']) > 0 && file_exists($_SERVER['DOCUMENT_ROOT'] . $arCssLink['OPTIMIZE'])) {
				$arAllAtomFiles[$arCssLink['OPTIMIZE']] = array(
					"FILE" => $arCssLink['OPTIMIZE'],
					//"MKTIME" => filemtime($_SERVER['DOCUMENT_ROOT'] . $arCssLink['OPTIMIZE']),
					"SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $arCssLink['OPTIMIZE']),
				);
			}
		}
		ksort($arAllAtomFiles);

		$strIdent = md5(serialize($arAllAtomFiles) . $this->strBaseIdent);
		$strCacheFile = "/bitrix/ammina.cache/css/ammina.optimizer/" . SITE_ID . "/full/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".css";
		$strCacheFileCssRules = "/bitrix/ammina.cache/css/ammina.optimizer/" . SITE_ID . "/full/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".css.rules.php";

		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) || Application::getInstance()->isClearCache("css")) {
			$arAllRules = array();
			$strFullContent = "";
			foreach ($this->arAllCssLinks as $k => $arCssLink) {
				if (amopt_strlen($arCssLink['OPTIMIZE']) > 0) {
					$strFullContent .= file_get_contents($_SERVER['DOCUMENT_ROOT'] . $arCssLink['OPTIMIZE']);
					$arAllRules[] = unserialize(file_get_contents($_SERVER['DOCUMENT_ROOT'] . $arCssLink['OPTIMIZE'] . ".rules"));
				}
			}
			\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, $strFullContent);
			CheckDirPath($_SERVER['DOCUMENT_ROOT'] . $strCacheFileCssRules);
			\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFileCssRules, '<' . '? return ' . var_export($arAllRules, true) . ';');
		} else {
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) && filemtime($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) < (time() - 3600)) {
				@touch($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, time());
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFileCssRules)) {
					@touch($_SERVER['DOCUMENT_ROOT'] . $strCacheFileCssRules, time());
				}
			}
		}
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile)) {
			return $strCacheFile;
		}
	}

	protected function doMakeCriticalFile()
	{
		global $APPLICATION;
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $this->strMainResultFile . ".rules.php")) {
			return false;
		}
		$arIdentCritical = array(
			$this->strMainResultFile,
			array_keys($this->arAllUsedClasses['CLASSES']),
		);
		if ($this->arOptions['critical']['options']['USE_TAG_IDENT'] == "Y") {
			$arIdentCritical[] = array_keys($this->arAllUsedClasses['IDENT']);
		}

		$strIdent = md5(serialize($arIdentCritical));
		if ($this->arOptions['critical']['options']['MAX_CRITICAL_RECORD'] > 0) {
			$arExistCritical = array();
			$arScan1 = scandir($_SERVER['DOCUMENT_ROOT'] . dirname($this->strMainResultFile) . "/critical/");
			foreach ($arScan1 as $kScan1 => $vScan1) {
				if (in_array($vScan1, array(".", ".."))) {
					continue;
				}
				$arScan2 = scandir($_SERVER['DOCUMENT_ROOT'] . dirname($this->strMainResultFile) . "/critical/" . $vScan1 . "/");
				foreach ($arScan2 as $kScan2 => $vScan2) {
					if (in_array($vScan2, array(".", ".."))) {
						continue;
					}
					$arScan3 = scandir($_SERVER['DOCUMENT_ROOT'] . dirname($this->strMainResultFile) . "/critical/" . $vScan1 . "/" . $vScan2 . "/");
					foreach ($arScan3 as $kScan3 => $vScan3) {
						if (in_array($vScan3, array(".", ".."))) {
							continue;
						}
						$arPath = ammina_pathinfo($_SERVER['DOCUMENT_ROOT'] . dirname($this->strMainResultFile) . "/critical/" . $vScan1 . "/" . $vScan2 . "/" . $vScan3);
						if ($arPath['extension'] == "css" && amopt_strpos($arPath['filename'], ".") === false) {
							$arExistCritical[$arPath['filename']] = filemtime($_SERVER['DOCUMENT_ROOT'] . dirname($this->strMainResultFile) . "/critical/" . $vScan1 . "/" . $vScan2 . "/" . $vScan3);
						}
					}
				}
			}
			if (count($arExistCritical) >= $this->arOptions['critical']['options']['MAX_CRITICAL_RECORD']) {
				arsort($arExistCritical, SORT_NUMERIC);
				$ak = array_keys($arExistCritical);
				$strUseIdent = $ak[0];
				$strCacheFileInfo = dirname($this->strMainResultFile) . "/critical/" . amopt_substr($strUseIdent, 0, 2) . "/" . amopt_substr($strUseIdent, 0, 6) . "/" . $strIdent . ".css.other." . $strIdent . ".info";
				if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFileInfo)) {
					$arInfo = array(
						"ALL_CLASSES" => $this->arAllUsedClasses['CLASSES_ALL'],
						"ALL_IDENT" => array(),
						"USED_CLASSES" => $this->arAllUsedClasses['CLASSES'],
						"USED_IDENT" => array(),
					);
					if ($this->arOptions['critical']['options']['USE_TAG_IDENT'] == "Y") {
						$arInfo['ALL_IDENT'] = $this->arAllUsedClasses['IDENT_ALL'];
						$arInfo['USED_IDENT'] = $this->arAllUsedClasses['IDENT'];
					}
					\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFileInfo, serialize($arInfo));
				}
				$strIdent = $strUseIdent;
			}
		}
		$this->oldCriticalIdent = $strIdent;
		$strCacheFile = dirname($this->strMainResultFile) . "/critical/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".css";
		$strCacheFileInfo = dirname($this->strMainResultFile) . "/critical/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".css.info";
		$strCacheFileFonts = dirname($this->strMainResultFile) . "/critical/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".css.fonts";
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) || Application::getInstance()->isClearCache("css")) {
			$arAllRules = @include($_SERVER['DOCUMENT_ROOT'] . $this->strMainResultFile . ".rules.php");
			$strCtiticalContent = '';
			$arAllCriticalFontFace = array();
			$arAllFontFace = array();
			foreach ($arAllRules as &$arCurrentRules) {
				if (isset($arCurrentRules['INDEX']) && is_array($arCurrentRules['INDEX'])) {
					foreach ($arCurrentRules['INDEX'] as $k => $arRulesIndex) {
						$arKeys = explode(";", $k);
						$bAllExists = true;
						foreach ($arKeys as $curKey) {
							$curKey = amopt_strtolower(trim($curKey));
							if ($curKey == "*") {
								break;
							} elseif (amopt_substr($curKey, 0, 1) == ".") {
								if (!isset($this->arAllUsedClasses['CLASSES'][amopt_substr($curKey, 1)])) {
									$bAllExists = false;
									break;
								}
							} elseif (amopt_substr($curKey, 0, 1) == "#") {
								if (!isset($this->arAllUsedClasses['IDENT'][amopt_substr($curKey, 1)])) {
									$bAllExists = false;
									break;
								}
							}
						}
						if ($bAllExists) {
							foreach ($arRulesIndex as $index) {
								$arCurrentRules['RULES'][$index]['CRITICAL'] = true;
								if (isset($arCurrentRules['RULES'][$index]['FONTS'])) {
									$arAllCriticalFontFace[] = array(
										"FONT" => $arCurrentRules['RULES'][$index]['FONTS'],
										"FONT_STYLE" => $this->doNormalizeFontStyle($arCurrentRules['RULES'][$index]['FONT_STYLE']),
										"FONT_WEIGHT" => $this->doNormalizeFontWeight($arCurrentRules['RULES'][$index]['FONT_WEIGHT']),
									);
									$arWeightSyn = $this->getFontWeightSyn($arAllCriticalFontFace[count($arAllCriticalFontFace) - 1]['FONT_WEIGHT']);
									foreach ($arWeightSyn as $w) {
										$arAllCriticalFontFace[] = array(
											"FONT" => $arCurrentRules['RULES'][$index]['FONTS'],
											"FONT_STYLE" => $this->doNormalizeFontStyle($arCurrentRules['RULES'][$index]['FONT_STYLE']),
											"FONT_WEIGHT" => $w,
										);
									}
								}
							}
						}
					}
				}
				foreach ($arCurrentRules['RULES'] as &$arRule) {
					if ($arRule['TYPE'] != "DeclarationBlock") {
						$arRule['CRITICAL'] = true;
					}
				}
				$bCurrentMedia = false;
				$iCurrentMediaIndex = false;
				$arCurrentCritical = array();
				foreach ($arCurrentRules['RULES'] as &$arRule) {
					if ($arRule['CRITICAL']) {
						if ($arRule['ISMEDIA']) {
							if ($arRule['MEDIA_INDEX'] == $iCurrentMediaIndex) {
								$arCurrentCritical[] = $arRule['CONTENT'];
							} else {
								if ($bCurrentMedia && $iCurrentMediaIndex !== false) {
									$arCurrentCritical[] = "}";
								}
								$iCurrentMediaIndex = $arRule['MEDIA_INDEX'];
								$bCurrentMedia = true;
								$mediaPrefix = trim($arCurrentRules['MEDIA'][$iCurrentMediaIndex]['CONTENT']);
								$arCurrentCritical[] = amopt_substr($mediaPrefix, 0, amopt_strlen($mediaPrefix) - 1);
								$arCurrentCritical[] = $arRule['CONTENT'];
							}
						} else {
							if ($bCurrentMedia && $iCurrentMediaIndex !== false) {
								$arCurrentCritical[] = "}";
								$bCurrentMedia = false;
								$iCurrentMediaIndex = false;
							}
							$arCurrentCritical[] = $arRule['CONTENT'];
						}
					}
				}
				if ($bCurrentMedia && $iCurrentMediaIndex !== false) {
					$arCurrentCritical[] = "}";
				}
				if (isset($arCurrentRules['FONT_FACE']) && !empty($arCurrentRules['FONT_FACE'])) {
					$arAllFontFace = array_merge($arAllFontFace, $arCurrentRules['FONT_FACE']);
				}
				$strCtiticalContent .= implode("", $arCurrentCritical);
			}
			\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, $strCtiticalContent);
			$arRequiredCritical = explode(",", $this->arOptions['fonts']['options']['LINK_FONTS']);
			$arRequiredUnicodeRange = explode(",", $this->arOptions['fonts']['options']['UNICODE_RANGE']);
			$arRequiredUnicodeRange[] = '0000-00ff';
			$arRequiredUnicodeRange[] = '0410-044f';
			foreach ($arRequiredUnicodeRange as $k => $v) {
				if (amopt_strlen($v) <= 0) {
					unset($arRequiredUnicodeRange[$k]);
					continue;
				}
				$ar = explode("-", $v);
				if (!isset($ar[1])) {
					$ar[1] = $ar[0];
				}
				$arRequiredUnicodeRange[$k] = array(
					"MIN" => hexdec($ar[0]),
					"MAX" => hexdec($ar[1])
				);
			}
			foreach ($arAllFontFace as $k => $arFontFace) {
				if ($this->arOptions['fonts']['options']['LINK_ALL_FONTS'] == "Y") {
					if (isset($arFontFace['unicode-range'])) {
						foreach ($arAllFontFace[$k]['unicode-range-dec'] as $k1 => $v1) {
							foreach ($arRequiredUnicodeRange as $k2 => $v2) {
								if (\CAmminaOptimizer::in_range($v1['MIN'], $v1['MAX'], $v2['MIN'], $v2['MAX'])) {
									$arAllFontFace[$k]['CRITICAL'] = true;
									break(2);
								}
							}
						}
					} else {
						$arAllFontFace[$k]['CRITICAL'] = true;
					}
				} else {
					$arFontFace['weight_orig'] = $arFontFace['weight'];
					$arFontFace['weight'] = $this->_normalizedFontWeight($arFontFace['weight']);
					foreach ($arRequiredCritical as $strFont) {
						if (amopt_strtolower($strFont) == amopt_strtolower($arFontFace['family'])) {
							$arAllFontFace[$k]['CRITICAL'] = true;
							continue(2);
						}
					}
					foreach ($arAllCriticalFontFace as $k1 => $arCriticalFont) {
						$arCriticalFont['FONT_WEIGHT'] = $this->_normalizedFontWeight($arCriticalFont['FONT_WEIGHT']);
						$arCriticalFont['FONT_WEIGHT_orig'] = $arCriticalFont['FONT_WEIGHT'];
						foreach ($arCriticalFont['FONT'] as $strFont) {
							if (amopt_strtolower($strFont) == amopt_strtolower($arFontFace['family'])) {
								if ($arCriticalFont['FONT_STYLE'] == $arFontFace['style'] && $arCriticalFont['FONT_WEIGHT_orig'] == $arFontFace['weight_orig']) {
									$arAllFontFace[$k]['CRITICAL'] = true;
									continue(2);
								}
							}
						}
						foreach ($arCriticalFont['FONT'] as $strFont) {
							if (amopt_strtolower($strFont) == amopt_strtolower($arFontFace['family'])) {
								if ($arCriticalFont['FONT_STYLE'] == $arFontFace['style'] && $arCriticalFont['FONT_WEIGHT'] == $arFontFace['weight']) {
									$arAllFontFace[$k]['CRITICAL'] = true;
									continue(2);
								}
							}
						}
					}
				}
			}
			foreach ($arAllFontFace as $k => $arFontFace) {
				if (!$arAllFontFace[$k]['CRITICAL']) {
					unset($arAllFontFace[$k]);
				}
			}
			\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFileFonts, serialize($arAllFontFace));
			$arInfo = array(
				"ALL_CLASSES" => $this->arAllUsedClasses['CLASSES_ALL'],
				"ALL_IDENT" => array(),
				"USED_CLASSES" => $this->arAllUsedClasses['CLASSES'],
				"USED_IDENT" => array(),
			);
			if ($this->arOptions['critical']['options']['USE_TAG_IDENT'] == "Y") {
				$arInfo['ALL_IDENT'] = $this->arAllUsedClasses['IDENT_ALL'];
				$arInfo['USED_IDENT'] = $this->arAllUsedClasses['IDENT'];
			}
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
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFileFonts)) {
				if (!empty($this->arCriticalFonts)) {
					$this->arCriticalFonts = array_merge($this->arCriticalFonts, unserialize(file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strCacheFileFonts)));
				} else {
					$this->arCriticalFonts = unserialize(file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strCacheFileFonts));
				}
			}
			return $strCacheFile;
		}
	}

	protected function _normalizedFontWeight($strWeight)
	{
		static $arSyn = array(
			"thin" => "100",
			"hairline" => "100",
			"extralight" => "200",
			"ultralight" => "200",
			"light" => "300",
			"normal" => "400",
			"medium" => "500",
			"semibold" => "600",
			"demibold" => "600",
			"bold" => "700",
			"extrabold" => "800",
			"ultrabold" => "800",
			"black" => "900",
			"heavy" => "900",
			"extrablack" => "950",
			"ultrablack" => "950",
		);
		$strWeight = amopt_strtolower($strWeight);
		if (isset($arSyn[$strWeight])) {
			$strWeight = $arSyn[$strWeight];
		}
		if ($strWeight <= 500) {
			$strWeight = 400;
		} else {
			$strWeight = 700;
		}
		return $strWeight;
	}

	protected function doMakeNonCriticalFile()
	{
		global $APPLICATION;
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $this->strMainResultFile . ".rules.php")) {
			return false;
		}
		if (amopt_strlen($this->oldCriticalIdent) > 0) {
			$strIdent = $this->oldCriticalIdent;
		} else {
			$arIdentCritical = array(
				$this->strMainResultFile,
				array_keys($this->arAllUsedClasses['CLASSES']),
				//array_keys($this->arAllUsedClasses['IDENT']),
			);
			$strIdent = md5(serialize($arIdentCritical));
		}
		$strCacheFile = dirname($this->strMainResultFile) . "/critical/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".nc.css";
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) || Application::getInstance()->isClearCache("css")) {
			$arAllRules = @include($_SERVER['DOCUMENT_ROOT'] . $this->strMainResultFile . ".rules.php");
			$strCtiticalContent = '';
			foreach ($arAllRules as &$arCurrentRules) {
				foreach ($arCurrentRules['INDEX'] as $k => $arRulesIndex) {
					$arKeys = explode(";", $k);
					$bAllExists = true;
					foreach ($arKeys as $curKey) {
						$curKey = amopt_strtolower(trim($curKey));
						if ($curKey == "*") {
							break;
						} elseif (amopt_substr($curKey, 0, 1) == ".") {
							if (!isset($this->arAllUsedClasses['CLASSES'][amopt_substr($curKey, 1)])) {
								$bAllExists = false;
								break;
							}
						} elseif (amopt_substr($curKey, 0, 1) == "#") {
							if (!isset($this->arAllUsedClasses['IDENT'][amopt_substr($curKey, 1)])) {
								$bAllExists = false;
								break;
							}
						}
					}
					if ($bAllExists) {
						foreach ($arRulesIndex as $index) {
							$arCurrentRules['RULES'][$index]['CRITICAL'] = true;
						}
					}
				}

				foreach ($arCurrentRules['RULES'] as &$arRule) {
					if ($arRule['TYPE'] != "DeclarationBlock") {
						$arRule['CRITICAL'] = true;
					}
				}
				$bCurrentMedia = false;
				$iCurrentMediaIndex = false;
				$arCurrentCritical = array();
				foreach ($arCurrentRules['RULES'] as &$arRule) {
					if (!$arRule['CRITICAL']) {
						if ($arRule['ISMEDIA']) {
							if ($arRule['MEDIA_INDEX'] == $iCurrentMediaIndex) {
								$arCurrentCritical[] = $arRule['CONTENT'];
							} else {
								if ($bCurrentMedia && $iCurrentMediaIndex !== false) {
									$arCurrentCritical[] = "}";
								}
								$iCurrentMediaIndex = $arRule['MEDIA_INDEX'];
								$bCurrentMedia = true;
								$mediaPrefix = trim($arCurrentRules['MEDIA'][$iCurrentMediaIndex]['CONTENT']);
								$arCurrentCritical[] = amopt_substr($mediaPrefix, 0, amopt_strlen($mediaPrefix) - 1);
								$arCurrentCritical[] = $arRule['CONTENT'];
							}
						} else {
							if ($bCurrentMedia && $iCurrentMediaIndex !== false) {
								$arCurrentCritical[] = "}";
								$bCurrentMedia = false;
								$iCurrentMediaIndex = false;
							}
							$arCurrentCritical[] = $arRule['CONTENT'];
						}
					}
				}
				if ($bCurrentMedia && $iCurrentMediaIndex !== false) {
					$arCurrentCritical[] = "}";
				}
				$strCtiticalContent .= implode("", $arCurrentCritical);
				/*
				$bCurrentMedia = false;
				$iCurrentMediaIndex = false;
				$strCurrentMedia = '';
				foreach ($arCurrentRules['RULES'] as $arRule) {
					if ($arRule['ISMEDIA']) {
						if ($arRule['MEDIA_INDEX'] != $iCurrentMediaIndex) {
							if ($bCurrentMedia) {
								if (amopt_strlen($strCurrentMedia) > 0) {
									$mediaPrefix = trim($arCurrentRules['MEDIA'][$iCurrentMediaIndex]['CONTENT']);
									$strCtiticalContent .= amopt_substr($mediaPrefix, 0, amopt_strlen($mediaPrefix) - 1) . $strCurrentMedia . '}';
									$strCurrentMedia = '';
									$iCurrentMediaIndex = false;
									$bCurrentMedia = false;
								}
							}
						}
						if ($iCurrentMediaIndex === false) {
							$iCurrentMediaIndex = $arRule['MEDIA_INDEX'];
							$strCurrentMedia = "";
							$bCurrentMedia = true;
						}
						if ($arRule['TYPE'] == "DeclarationBlock") {
							if (!$arRule['CRITICAL']) {
								$strCurrentMedia .= $arRule['CONTENT'];
							}
						} else {
							//$strCurrentMedia .= $arRule['CONTENT'];
						}
					} else {
						if ($bCurrentMedia) {
							if (amopt_strlen($strCurrentMedia) > 0) {
								$mediaPrefix = trim($arCurrentRules['MEDIA'][$iCurrentMediaIndex]['CONTENT']);
								$strCtiticalContent .= amopt_substr($mediaPrefix, 0, amopt_strlen($mediaPrefix) - 1) . $strCurrentMedia . '}';
								$strCurrentMedia = '';
								$iCurrentMediaIndex = false;
								$bCurrentMedia = false;
							}
						}
						if ($arRule['TYPE'] == "DeclarationBlock") {
							if (!$arRule['CRITICAL']) {
								$strCtiticalContent .= $arRule['CONTENT'];
							}
						} else {
							//$strCtiticalContent .= $arRule['CONTENT'];
						}
					}
				}
				*/
			}
			\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, $strCtiticalContent);
		} else {
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) && filemtime($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) < (time() - 3600)) {
				@touch($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, time());
			}
		}
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile)) {
			return $strCacheFile;
		}
	}

	protected function doMakeFontFile()
	{
		global $APPLICATION;
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $this->strMainResultFile . ".rules.php")) {
			return false;
		}
		$arIdentFont = array(
			$this->strMainResultFile,
		);
		$strIdent = md5(serialize($arIdentFont));
		$strCacheFile = dirname($this->strMainResultFile) . "/font/" . $strIdent . ".css";
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) || Application::getInstance()->isClearCache("css")) {
			$arAllRules = @include($_SERVER['DOCUMENT_ROOT'] . $this->strMainResultFile . ".rules.php");
			$strFontContent = '';
			foreach ($arAllRules as &$arCurrentRules) {
				foreach ($arCurrentRules['RULES'] as &$arRule) {
					if ($arRule['TYPE'] == "RULE" && $arRule['SUBTYPE'] == "font-face") {
						$strFontContent .= $arRule['CONTENT'];
					}
				}
				\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, $strFontContent);
			}
		} else {
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) && filemtime($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) < (time() - 3600)) {
				@touch($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, time());
			}
		}
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile)) {
			return $strCacheFile;
		}
	}

	protected function doMakeNonFontFile()
	{
		global $APPLICATION;
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $this->strMainResultFile . ".rules.php")) {
			return false;
		}
		$arIdentFont = array(
			$this->strMainResultFile,
		);
		$strIdent = md5(serialize($arIdentFont));
		$strCacheFile = dirname($this->strMainResultFile) . "/font/" . $strIdent . ".nf.css";
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) || Application::getInstance()->isClearCache("css")) {
			$arAllRules = @include($_SERVER['DOCUMENT_ROOT'] . $this->strMainResultFile . ".rules.php");
			$strFontContent = '';
			foreach ($arAllRules as &$arCurrentRules) {
				$bCurrentMedia = false;
				$iCurrentMediaIndex = false;
				$strCurrentMedia = '';
				foreach ($arCurrentRules['RULES'] as &$arRule) {
					if ($arRule['ISMEDIA']) {
						if ($arRule['MEDIA_INDEX'] != $iCurrentMediaIndex) {
							if ($bCurrentMedia) {
								if (amopt_strlen($strCurrentMedia) > 0) {
									$mediaPrefix = trim($arCurrentRules['MEDIA'][$iCurrentMediaIndex]['CONTENT']);
									$strFontContent .= amopt_substr($mediaPrefix, 0, amopt_strlen($mediaPrefix) - 1) . $strCurrentMedia . '}';
									$strCurrentMedia = '';
									$iCurrentMediaIndex = false;
									$bCurrentMedia = false;
								}
							}
						}
						if ($iCurrentMediaIndex === false) {
							$iCurrentMediaIndex = $arRule['MEDIA_INDEX'];
							$strCurrentMedia = "";
							$bCurrentMedia = true;
						}
						if ($arRule['TYPE'] != "RULE" || $arRule['SUBTYPE'] != "font-face") {
							$strCurrentMedia .= $arRule['CONTENT'];
						}
					} else {
						if ($bCurrentMedia) {
							if (amopt_strlen($strCurrentMedia) > 0) {
								$mediaPrefix = trim($arCurrentRules['MEDIA'][$iCurrentMediaIndex]['CONTENT']);
								$strFontContent .= amopt_substr($mediaPrefix, 0, amopt_strlen($mediaPrefix) - 1) . $strCurrentMedia . '}';
								$strCurrentMedia = '';
								$iCurrentMediaIndex = false;
								$bCurrentMedia = false;
							}
						}
						if ($arRule['TYPE'] != "RULE" || $arRule['SUBTYPE'] != "font-face") {
							$strFontContent .= $arRule['CONTENT'];
						}
					}
				}
			}
			\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, $strFontContent);
		} else {
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) && filemtime($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) < (time() - 3600)) {
				@touch($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, time());
			}
		}
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile)) {
			return $strCacheFile;
		}
	}

	protected function doOptimizeCssFile($strFileName)
	{
		global $APPLICATION;
		if (amopt_strpos($strFileName, '://') !== false || amopt_strpos($strFileName, '//') === 0) {
			$strTmpFileName = $strFileName;
			if (\CAmminaOptimizer::checkRequestDomainUrl($strTmpFileName)) {
				$strFileName = $strTmpFileName;
			}
		}
		if (amopt_strpos($strFileName, '://') !== false || amopt_strpos($strFileName, '//') === 0) {
			return $this->doOptimizeRemoteCssFile($strFileName);
		} else {
			$strFileNameOriginal = $strFileName;
			$strFileName = explode("?", $strFileName);
			$strFileName = $strFileName[0];
			if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strFileName) && amopt_strpos($strFileName, '%') !== false) {
				$strFileName = urldecode($strFileName);
			}
			$strFileName = \CAmminaOptimizer::Rel2AbsUrl($APPLICATION->GetCurPage(true), $strFileName);
			if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strFileName)) {
				return false;
			}

			if (\CAmminaOptimizer::doMathPageToRules($this->arOptions['other']['options']['EXCLUDE_FILES'], $strFileName)) {
				return false;
			}

			if (amopt_strpos($strFileName, '/bitrix/ammina.cache/') === 0) {
				$strIdent = md5(
					serialize(
						array(
							"fileName" => $strFileNameOriginal,
							"size" => filesize($_SERVER['DOCUMENT_ROOT'] . $strFileName),
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
			$strCacheFile = "/bitrix/ammina.cache/css/ammina.optimizer/" . SITE_ID . "/atom/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".css";
			$strCacheFileTmp = "/bitrix/ammina.cache/css/ammina.optimizer/" . SITE_ID . "/atom/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".tmp.css";
			$strCacheFileCssRules = "/bitrix/ammina.cache/css/ammina.optimizer/" . SITE_ID . "/atom/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".css.rules";
			$strOptimizedFileWait = "/bitrix/ammina.cache/css/ammina.optimizer/" . SITE_ID . "/atom/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".css.wait";
			$strOptimizedFileInfo = "/bitrix/ammina.cache/css/ammina.optimizer/" . SITE_ID . "/atom/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".css.info";
			if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) || Application::getInstance()->isClearCache("css")) {
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileWait)) {
					@unlink($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileWait);
				}
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileInfo)) {
					@unlink($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileInfo);
				}
				$strContent = Application::getInstance()->normalized_file_get_content($_SERVER['DOCUMENT_ROOT'] . $strFileName);
				if ((!defined("BX_UTF") || BX_UTF !== true) && ($this->arOptions['other']['options']['DOUBLE_CONVERT_UTF8_FOR_WINDOWS_1251'] == "Y")) {
					$strContent = $APPLICATION->ConvertCharset($strContent, "WINDOWS-1251", "UTF-8");
				}
				$oCssParser = new Parser($strContent, $this->oParserSettings);
				$oCssDocument = $oCssParser->parse();
				$this->doNormalizeDocumentCharset($oCssDocument);
				$this->doOptimizeImport($oCssDocument, $strFileName);

				$this->doOptimizeCssUrl($oCssDocument, $strFileName);

				if ($this->arOptions['images']['options']['ACTIVE'] == "Y") {
					$this->doOptimizeCssImages($oCssDocument, $strFileName);
				}

				$arAllSelectorsInfo = array();
				$arCssContent = $oCssDocument->getContents();
				$indexRules = 1;
				$indexMedia = 1;
				foreach ($arCssContent as $oContent) {
					if ($oContent instanceof DeclarationBlock) {
						/**
						 * @var $oContent DeclarationBlock
						 */
						$arContentSelectors = $oContent->getSelectors();
						foreach ($arContentSelectors as $oContentSelector) {
							/**
							 * @var $oContentSelector Selector
							 */
							$arAtomSelector = $this->getAtomSelector($oContentSelector->getSelector());
							$arAllSelectorsInfo['INDEX'][amopt_strtolower(implode(";", $arAtomSelector))][] = $indexRules;
							//$arAllSelectorsInfo['INDEX'][amopt_strtolower(implode(";", $arAtomSelector) . ";")][] = $indexRules;
						}
						$arAllSelectorsInfo['RULES'][$indexRules] = array(
							"CONTENT" => $oContent->render($this->getOptimizeOutputFormat(true)),
							"TYPE" => "DeclarationBlock",
						);
						$oFontRules = $oContent->getRules("font-family");
						if (!empty($oFontRules)) {
							$oValFontRule = $oFontRules[0]->getValue();
							if ($oValFontRule instanceof RuleValueList) {
								$oFontNames = $oValFontRule->getListComponents();
								foreach ($oFontNames as $oFontName) {
									if ($oFontName instanceof CSSString) {
										$arAllSelectorsInfo['RULES'][$indexRules]['FONTS'][strval($oFontName->getString())] = strval($oFontName->getString());
									} else {
										$arAllSelectorsInfo['RULES'][$indexRules]['FONTS'][strval($oFontName)] = strval($oFontName);
									}
								}
							} else {
								$arAllSelectorsInfo['RULES'][$indexRules]['FONTS'][strval($oValFontRule)] = strval($oValFontRule);
							}
							$oFontStyle = $oContent->getRules("font-style");
							if (!empty($oFontStyle)) {
								$arAllSelectorsInfo['RULES'][$indexRules]['FONT_STYLE'] = strval($oFontStyle[0]->getValue());
							}
							$oFontWeight = $oContent->getRules("font-weight");
							if (!empty($oFontWeight)) {
								$arAllSelectorsInfo['RULES'][$indexRules]['FONT_WEIGHT'] = strval(strval($oFontWeight[0]->getValue()));
							}
						} else {
							$oFontRules = $oContent->getRules("font");
							if (!empty($oFontRules)) {
								$oValFontRule = $oFontRules[0]->getValue();
								if ($oValFontRule instanceof RuleValueList) {
									$oFontNames = $oValFontRule->getListComponents();
									if (is_array($oFontNames)) {
										if (isset($oFontNames[4])) {
											$oValFontRule = $oFontNames[4];
											if ($oValFontRule instanceof RuleValueList) {
												$oFontNames = $oValFontRule->getListComponents();
												foreach ($oFontNames as $oFontName) {
													if ($oFontName instanceof CSSString) {
														$arAllSelectorsInfo['RULES'][$indexRules]['FONTS'][strval($oFontName->getString())] = strval($oFontName->getString());
													} else {
														$arAllSelectorsInfo['RULES'][$indexRules]['FONTS'][strval($oFontName)] = strval($oFontName);
													}
												}
											} elseif ($oValFontRule instanceof CSSString) {
												$arAllSelectorsInfo['RULES'][$indexRules]['FONTS'][strval($oValFontRule->getString())] = strval($oValFontRule->getString());
											} elseif (is_string($oValFontRule) && amopt_strlen($oValFontRule) > 0) {
												$arAllSelectorsInfo['RULES'][$indexRules]['FONTS'][strval($oValFontRule)] = strval($oValFontRule);
											}
										}
									} elseif (is_string($oFontNames) && amopt_strlen($oFontNames) > 0) {
										$arAllSelectorsInfo['RULES'][$indexRules]['FONTS'][strval($oFontNames)] = strval($oFontNames);
									}
								} elseif (is_string($oValFontRule) && amopt_strlen($oValFontRule) > 0) {
									$arAllSelectorsInfo['RULES'][$indexRules]['FONTS'][strval($oValFontRule)] = strval($oValFontRule);
								}
							}
						}
						$indexRules++;
					} elseif ($oContent instanceof AtRuleSet) {
						/**
						 * @var $oContent AtRuleSet
						 * @var $oTmpContent AtRuleSet
						 */
						$strName = $oContent->atRuleName();
						$arFontFaceInfo = false;
						if (amopt_strtolower($strName) == "font-face") {
							$arFontFaceInfo = $this->doOptimizeFontFaceRule($oContent);
						}
						$arAllSelectorsInfo['RULES'][$indexRules] = array(
							"CONTENT" => $oContent->render($this->getOptimizeOutputFormat(true)),
							"TYPE" => "RULE",
							"SUBTYPE" => $strName,
						);
						$arAllSelectorsInfo['TYPE_INDEX'][$strName][] = $indexRules;
						if ($arFontFaceInfo !== false) {
							$arAllSelectorsInfo['FONT_FACE'][] = $arFontFaceInfo;
						}
						$indexRules++;
					} elseif ($oContent instanceof AtRuleBlockList && $oContent->atRuleName() == "media") {
						/**
						 * @var $oContent AtRuleBlockList
						 * @var $oTmpContent AtRuleBlockList
						 */
						$oTmpContent = clone $oContent;
						$arRules = $oTmpContent->getContents();
						foreach ($arRules as $oRule) {
							$oTmpContent->remove($oRule);
						}
						$arAllSelectorsInfo['MEDIA'][$indexMedia] = array(
							"CONTENT" => $oTmpContent->render($this->getOptimizeOutputFormat(true)),
						);
						$arBlockContent = $oContent->getContents();
						foreach ($arBlockContent as $oBlockContent) {
							if ($oBlockContent instanceof DeclarationBlock) {
								$arBlockContentSelectors = $oBlockContent->getSelectors();
								foreach ($arBlockContentSelectors as $oBlockContentSelector) {
									/**
									 * @var $oBlockContentSelector Selector
									 */
									$arBlockAtomSelector = $this->getAtomSelector($oBlockContentSelector->getSelector());
									$arAllSelectorsInfo['INDEX'][amopt_strtolower(implode(";", $arBlockAtomSelector))][] = $indexRules;
									//$arAllSelectorsInfo['INDEX'][amopt_strtolower(implode(";", $arBlockAtomSelector) . ";")][] = $indexRules;
								}
								$arAllSelectorsInfo['RULES'][$indexRules] = array(
									"CONTENT" => $oBlockContent->render($this->getOptimizeOutputFormat(true)),
									"TYPE" => "DeclarationBlock",
									"ISMEDIA" => true,
									"MEDIA_INDEX" => $indexMedia,
								);
								$indexRules++;
							} else {
								$arAllSelectorsInfo['RULES'][$indexRules] = array(
									"CONTENT" => $oBlockContent->render($this->getOptimizeOutputFormat(true)),
									"TYPE" => "OTHER",
									"ISMEDIA" => true,
									"MEDIA_INDEX" => $indexMedia,
								);
								$indexRules++;
							}
						}
						$indexMedia++;
					} elseif ($oContent instanceof AtRuleBlockList) {
						/**
						 * @var $oContent AtRuleBlockList
						 */
						$arAllSelectorsInfo['RULES'][$indexRules] = array(
							"CONTENT" => $oContent->render($this->getOptimizeOutputFormat(true)),
							"TYPE" => "RULE_BLOCK_LIST",
						);
						$arAllSelectorsInfo['TYPE_INDEX']['RULE_BLOCK_LIST'][] = $indexRules;
						$indexRules++;
					} elseif ($oContent instanceof KeyFrame) {
						/**
						 * @var $oContent KeyFrame
						 */
						$arAllSelectorsInfo['RULES'][$indexRules] = array(
							"CONTENT" => $oContent->render($this->getOptimizeOutputFormat(true)),
							"TYPE" => "KEYFRAME",
						);
						$arAllSelectorsInfo['TYPE_INDEX']['KEYFRAME'][] = $indexRules;
						$indexRules++;
					} else {
						$arAllSelectorsInfo['RULES'][$indexRules] = array(
							"CONTENT" => $oContent->render($this->getOptimizeOutputFormat(true)),
							"TYPE" => "UNKNOWN",
						);
						//$arAllSelectorsInfo['INDEX']["*;"][] = $indexRules;
						$arAllSelectorsInfo['INDEX']["*"][] = $indexRules;
						$indexRules++;
					}
				}
				$strContent = '/* Ammina CSS file original ' . $strFileName . ' */' . "\n" . $oCssDocument->render($this->getOptimizeOutputFormat(false, $strFileName));
				if ((!defined("BX_UTF") || BX_UTF !== true) && ($this->arOptions['other']['options']['DOUBLE_CONVERT_UTF8_FOR_WINDOWS_1251'] == "Y")) {
					$strContent = $APPLICATION->ConvertCharset($strContent, "UTF-8", "WINDOWS-1251");
				}
				\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, $strContent);
				\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFileCssRules, serialize($arAllSelectorsInfo));
			} else {
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) && filemtime($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) < (time() - 3600)) {
					@touch($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, time());
					if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFileCssRules)) {
						@touch($_SERVER['DOCUMENT_ROOT'] . $strCacheFileCssRules, time());
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

	protected function doOptimizeRemoteCssFile($strFileName)
	{
		global $APPLICATION;
		if (amopt_strpos($strFileName, '//fonts.googleapis.com/css') !== false) {
			return $this->doOptimizeGoogleFontCssFile($strFileName);
		} elseif ($this->arOptions['external_css']['options']['ACTIVE'] == "Y" && !\CAmminaOptimizer::isLocalDomainLink($strFileName)) {
			if (\CAmminaOptimizer::doMathPageToRules($this->arOptions['external_css']['options']['EXCLUDE'], $strFileName) && !\CAmminaOptimizer::doMathPageToRules($this->arOptions['external_css']['options']['INCLUDE'], $strFileName)) {
				return false;
			}
			$strIdent = md5($strFileName);
			$strCacheFile = "/bitrix/ammina.cache/css.remote/" . SITE_ID . "/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".css";
			if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) || Application::getInstance()->isClearCache("css")) {
				$strContent = \CAmminaOptimizer::doRequestPageRemote($strFileName, '', 10, 5);
				if (amopt_strlen($strContent) <= 0) {
					return false;
				}

				//Скачиваем все файлы
				$oCssParser = new Parser($strContent, $this->oParserSettings);
				$oCssDocument = $oCssParser->parse();
				$this->doNormalizeDocumentCharset($oCssDocument);

				$arAllValues = $oCssDocument->getAllValues();
				foreach ($arAllValues as $oValue) {
					if ($oValue instanceof URL) {
						/**
						 * @var $oValue URL
						 */
						$strUrl = $oValue->getURL()->getString();
						$strNewUrl = \CAmminaOptimizer::Rel2AbsUrl($strFileName, $strUrl);
						$strFileIdent = md5($strNewUrl);
						$arUrlFile = parse_url($strNewUrl);
						$arPathFile = ammina_pathinfo($arUrlFile['path']);
						$strCacheRemoteFile = "/upload/ammina.optremote/" . SITE_ID . "/" . amopt_strtolower($arPathFile['extension']) . "/" . amopt_substr($strFileIdent, 0, 2) . "/" . amopt_substr($strFileIdent, 0, 6) . "/" . $strFileIdent . "." . $arPathFile['extension'];

						if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheRemoteFile) || Application::getInstance()->isClearCache("css")) {
							$strContentFile = \CAmminaOptimizer::doRequestPageRemote($strNewUrl, '', 10, 5);
							if (amopt_strlen($strContentFile) > 0) {
								CheckDirPath(dirname($_SERVER['DOCUMENT_ROOT'] . $strCacheRemoteFile) . "/");
								\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheRemoteFile, $strContentFile);
							}
						}
						if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheRemoteFile)) {
							$oValue->getURL()->setString($strCacheRemoteFile);
						}
					}
				}

				$strContent = $oCssDocument->render(OutputFormat::createPretty());

				\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, '/* Ammina CSS remote file ' . $strFileName . ' */' . "\n" . $strContent);
			} else {
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) && filemtime($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) < (time() - 3600)) {
					@touch($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, time());
				}
			}
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile)) {
				$strResult = $this->doOptimizeCssFile($strCacheFile);
				return $strResult;
			}
		}
		return false;
	}

	protected function doOptimizeGoogleFontCssFile($strFileName)
	{
		global $APPLICATION;
		if ($this->arOptions['fonts']['options']['ACTIVE'] != "Y" || $this->arOptions['fonts']['options']['GOOGLE_FONTS'] != "Y") {
			return false;
		}
		if (amopt_strpos($strFileName, '&amp;') !== false) {
			$strFileName = str_replace('&amp;', '&', $strFileName);
		}
		if ($this->arOptions['fonts']['options']['GOOGLE_FONTS_TYPE'] == "ua") {
			$arFormats = Application::getInstance()->arSupportFontTypes;
			$strUA = "";
			if (in_array("woff2", $arFormats)) {
				$strUA = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:78.0) Gecko/20100101 Firefox/78.0";
			} elseif (in_array("woff", $arFormats)) {
				$strUA = "Mozilla/5.0 (Windows NT 6.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1";
			} elseif (in_array("svg", $arFormats)) {
				$strUA = "Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.10";
			} elseif (in_array("ttf", $arFormats)) {
				$strUA = "Opera/9.80 (Macintosh; Intel Mac OS X; U; en) Presto/2.2.15 Version/10.00";
			} elseif (in_array("eot", $arFormats)) {
				$strUA = "Mozilla/5.0 (MSIE 8.0; Windows NT 6.1; Trident/4.0)";
			}
			$strIdent = md5(
				serialize(
					array(
						"TYPE" => "ua",
						"UA" => $strUA,
						"FILE_NAME" => $strFileName
					)
				)
			);
			$strCacheFile = "/bitrix/ammina.cache/fonts/gf-ua/" . SITE_ID . "/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".css";
			if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) || Application::getInstance()->isClearCache("css")) {
				$strContent = \CAmminaOptimizer::doRequestPageRemote($strFileName, $strUA, 10, 5);
				if (amopt_strlen($strContent) <= 0) {
					return false;
				}
				\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, '/* Ammina CSS file Google Fonts ' . $strFileName . ' */' . "\n" . $strContent);
			} else {
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) && filemtime($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) < (time() - 3600)) {
					@touch($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, time());
				}
			}
//echo file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strCacheFile);
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile)) {
				$strResult = $this->doOptimizeCssFile($strCacheFile);
				return $strResult;
			}
		}
	}

	protected function getAtomSelector($strSelector)
	{
		if (amopt_strpos($strSelector, '(') !== false) {
			$s = amopt_strpos($strSelector, '(');
			$e = amopt_strpos($strSelector, ')', $s);
			$strSelector = amopt_substr($strSelector, 0, $s) . " " . amopt_strpos($strSelector, $e + 1);
		}
		$strSelector = str_replace(">", " ", $strSelector);
		$strSelector = str_replace("+", " ", $strSelector);
		$strSelector = str_replace("~", " ", $strSelector);
		$strSelector = str_replace(".", " .", $strSelector);
		$arSelectorsAtom = explode(" ", $strSelector);
		foreach ($arSelectorsAtom as $k => $v) {
			if (amopt_strpos($v, '[') !== false) {
				$v = amopt_substr($v, 0, amopt_strpos($v, '['));
			}
			if (amopt_strpos($v, ':') !== false) {
				$v = amopt_substr($v, 0, amopt_strpos($v, ':'));
			}
			$v = trim($v);
			if (amopt_strlen($v) <= 0) {
				unset($arSelectorsAtom[$k]);
				continue;
			} elseif (!in_array(amopt_substr($v, 0, 1), array(".", "#"))) {
				unset($arSelectorsAtom[$k]);
				continue;
			}
			$arSelectorsAtom[$k] = $v;
		}
		$arSelectorsAtom = array_values(array_unique($arSelectorsAtom));
		if (count($arSelectorsAtom) <= 0) {
			$arSelectorsAtom[] = "*";
		}
		return $arSelectorsAtom;
	}

	/**
	 * @param $oCssDocument Document
	 */
	protected function doOptimizeCssImages(&$oCssDocument, $strFileName)
	{
		$arAllValues = $oCssDocument->getAllValues();
		foreach ($arAllValues as $oValue) {
			if ($oValue instanceof URL) {
				/**
				 * @var $oValue URL
				 */
				$strUrl = $oValue->getURL()->getString();
				$strNewUrl = $strUrl;
				if ($this->arOptions['images']['options']['OPTIMIZE'] == "Y") {
					$strUrl = Application::getInstance()->getImageOptimizer()->doOptimizeImage($strUrl, false, $strFileName);
					if ($strUrl !== false) {
						$strNewUrl = $strUrl;
					}
				} else {
					if (amopt_strpos($strUrl, '://') !== false || amopt_strpos($strUrl, '//') === 0 || amopt_strpos($strUrl, 'data:') !== false) {
						/**
						 * @todo оптимизация удаленных
						 */
					} else {
						$strNewUrl = Rel2Abs(dirname($strFileName), $strUrl);
					}
				}

				$oValue->getURL()->setString($strNewUrl);
				if ($this->arOptions['images']['options']['INLINE'] == "Y") {
					$oValue->getURL()->setString($this->getImageInline($strNewUrl));
				}
			}
		}
	}

	/**
	 * @param $oCssDocument Document
	 * @param $strFileName
	 */
	protected function doOptimizeImport(&$oCssDocument, $strFileName)
	{
		$arAllValues = $oCssDocument->getAllValues();
		$arReplaceData = array();
		$i = 1;
		foreach ($arAllValues as $oValue) {
			if ($oValue instanceof Import) {
				/**
				 * @var $oValue Import
				 */
				$strUrl = $oValue->getLocation()->getURL()->getString();
				if (amopt_strpos($strUrl, '://') !== false || amopt_strpos($strUrl, '//') === 0 || amopt_strpos($strUrl, 'data:') !== false) {
				} else {
					$strNewUrl = Rel2Abs(dirname($strFileName), $strUrl);
					$strResultOptimize = $this->doOptimizeCssFile($strNewUrl);
					if (amopt_strlen($strResultOptimize) > 0 && file_exists($_SERVER['DOCUMENT_ROOT'] . $strResultOptimize)) {
						$oValue->getLocation()->getURL()->setString("#REPLACED_DATA_" . $i . "#");
						$arReplaceData[$i] = $strResultOptimize;
						$i++;
					}
				}
			}
		}
		if (!empty($arReplaceData)) {
			$strContent = $oCssDocument->render(OutputFormat::createCompact());
			foreach ($arReplaceData as $k => $v) {
				$strContent = str_replace('@import url("#REPLACED_DATA_' . $k . '#");', file_get_contents($_SERVER['DOCUMENT_ROOT'] . $v), $strContent);
			}
			$oCssParser = new Parser($strContent, $this->oParserSettings);
			$oCssDocument = $oCssParser->parse();
			$this->doNormalizeDocumentCharset($oCssDocument);
		}
	}

	protected function doOptimizeCssUrl(&$oCssDocument, $strFileName)
	{
		$arAllValues = $oCssDocument->getAllValues();
		foreach ($arAllValues as $oValue) {
			if ($oValue instanceof URL) {
				/**
				 * @var $oValue URL
				 */
				$strUrl = $oValue->getURL()->getString();
				if (amopt_strpos($strUrl, '://') !== false || amopt_strpos($strUrl, '//') === 0 || amopt_strpos($strUrl, 'data:') !== false) {
				} else {
					$strNewUrl = Rel2Abs(dirname($strFileName), $strUrl);
					$oValue->getURL()->setString($strNewUrl);
				}
			}
		}
	}

	protected function getImageInline($strFileName)
	{
		$strFullName = $_SERVER['DOCUMENT_ROOT'] . $strFileName;
		$iMaxFileSize = $this->arOptions['images']['options']['MAX_IMAGE_SIZE'];
		if (file_exists($strFullName) && filesize($strFullName) <= $iMaxFileSize) {
			$arPathInfo = ammina_pathinfo($strFullName);
			if (isset($this->arInlineTypes[amopt_strtolower($arPathInfo['extension'])])) {
				return 'data:' . $this->arInlineTypes[amopt_strtolower($arPathInfo['extension'])] . ';base64,' . base64_encode(file_get_contents($strFullName));
			}
		}
		return $strFileName;
	}

	/**
	 * @return OutputFormat
	 */
	protected function getOptimizeOutputFormat($bForCriticalData = false, $strOriginalFilePath = false)
	{
		if ($bForCriticalData) {
			return OutputFormat::createCompact();
		} elseif ($this->arOptions['minify']['options']['ACTIVE'] == "Y" && $this->arOptions['minify']['options']['LIBRARY'] == "sabberworm") {
			if ($strOriginalFilePath !== false) {
				if (\CAmminaOptimizer::doMathPageToRules($this->arOptions['minify']['options']['EXCLUDE'], $strOriginalFilePath) && !\CAmminaOptimizer::doMathPageToRules($this->arOptions['minify']['options']['INCLUDE'], $strOriginalFilePath)) {
					return OutputFormat::createPretty();
				}
			}
			return OutputFormat::createCompact();
		}
		return OutputFormat::createPretty();
	}

	protected function doCheckMinify($strFileName, $strCacheFile, $strCacheFileTmp, $strOptimizedFileWait, $strOptimizedFileInfo)
	{
		if ($this->arOptions['minify']['options']['ACTIVE'] == "Y") {
			//if ($this->arOptions['minify']['options']['LIBRARY'] == "sabberworm") {
			if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileInfo)) {
				$arInfo = array(
					"SOURCE" => $strFileName,
					"RESULT" => $strCacheFile,
					"SOURCE_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strFileName),
					"RESULT_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strCacheFile),
				);
				\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileInfo, serialize($arInfo));
			} else {
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileInfo) && filemtime($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileInfo) < (time() - 3600)) {
					@touch($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileInfo, time());
				}
			}
			/*} else {
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
						if ($this->arOptions['minify']['options']['LIBRARY'] == "phpwee") {
							$bResult = $this->oDriverPHPWee->optimizeCss($strFileName, $strCacheFile, $strCacheFileTmp, $strOptimizedFileInfo, $this->arOptions['minify']['options_variant']['phpwee']);
						} elseif ($this->arOptions['minify']['options']['LIBRARY'] == "matthiasmullie") {
							$bResult = $this->oDriverMatthiasMullie->optimizeCss($strFileName, $strCacheFile, $strCacheFileTmp, $strOptimizedFileInfo, $this->arOptions['minify']['options_variant']['matthiasmullie']);
						} elseif ($this->arOptions['minify']['options']['LIBRARY'] == "yuicompressor") {
							$bResult = $this->oDriverYUICompressor->optimizeCss($strFileName, $strCacheFile, $strCacheFileTmp, $strOptimizedFileInfo, $this->arOptions['minify']['options_variant']['yuicompressor']);
						} elseif ($this->arOptions['minify']['options']['LIBRARY'] == "uglifycss") {
							$bResult = $this->oDriverUglifyCSS->optimizeCss($strFileName, $strCacheFile, $strCacheFileTmp, $strOptimizedFileInfo, $this->arOptions['minify']['options_variant']['uglifycss']);
						} elseif ($this->arOptions['minify']['options']['LIBRARY'] == "amminayuicompressor") {
							$bResult = $this->oDriverAmminaYUICompressor->optimizeCss($strFileName, $strCacheFile, $strCacheFileTmp, $strOptimizedFileInfo, $this->arOptions['minify']['options_variant']['amminayuicompressor']);
						} elseif ($this->arOptions['minify']['options']['LIBRARY'] == "amminauglifycss") {
							$bResult = $this->oDriverAmminaUglifyCSS->optimizeCss($strFileName, $strCacheFile, $strCacheFileTmp, $strOptimizedFileInfo, $this->arOptions['minify']['options_variant']['amminauglifycss']);
						}
					}
					if ($bResult) {
						if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileWait)) {
							@unlink($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileWait);
						}
					}
				}
			}*/
		}
	}

	/**
	 * @param $oContent AtRuleSet
	 */
	protected function doOptimizeFontFaceRule(&$oContent)
	{
		$arResult = false;
		if ($this->arOptions['fonts']['options']['ACTIVE'] == "Y") {
			$arAllRules = $oContent->getRules();
			$oRuleFontDisplay = false;
			foreach ($arAllRules as &$oRule) {
				if ($oRule instanceof Rule) {
					/**
					 * @var $oRule Rule
					 */
					$strName = amopt_strtolower($oRule->getRule());
					if ($strName == "font-display") {
						$oRuleFontDisplay = $oRule;
					} elseif ($strName == "font-family") {
						$arResult['family'] = $oRule->getValue();
						if ($arResult['family'] instanceof CSSString) {
							$arResult['family'] = $arResult['family']->getString();
						} else {
							$arResult['family'] = strval($arResult['family']);
							if (in_array(amopt_substr($arResult['family'], 0, 1), array("'", '"'))) {
								$arResult['family'] = amopt_substr($arResult['family'], 1, amopt_strlen($arResult['family']) - 2);
							}
						}
					} elseif ($strName == "font-weight") {
						$arResult['weight'] = strval($oRule->getValue());
					} elseif ($strName == "font-style") {
						$arResult['style'] = strval($oRule->getValue());
					} elseif ($strName == "src") {
						$oValue = $oRule->getValue();
						if ($oValue instanceof URL) {
							$arResult['src'][] = $oValue->getURL()->getString();
						} elseif ($oValue instanceof RuleValueList) {
							$arComponents = $oValue->getListComponents();

							foreach ($arComponents as $oComponent) {
								if ($oComponent instanceof CSSFunction) {
									$sName2 = $oComponent->getName();
									if ($sName2 != "local") {
										continue;
									}
									$arComponents2 = $oComponent->getListComponents();
									foreach ($arComponents2 as $oComponent2) {
										if ($oComponent2 instanceof CSSString) {
											$arResult['src_local'][] = $oComponent2->getString();
										}
									}
								} elseif ($oComponent instanceof URL) {
									$arResult['src'][] = $oComponent->getURL()->getString();
								} elseif ($oComponent instanceof RuleValueList) {
									$arComponents2 = $oComponent->getListComponents();
									foreach ($arComponents2 as $oComponent2) {
										if ($oComponent2 instanceof CSSFunction) {
											$sName3 = $oComponent2->getName();
											if ($sName3 != "local") {
												continue;
											}
											$arComponents3 = $oComponent2->getListComponents();
											foreach ($arComponents3 as $oComponent3) {
												if ($oComponent3 instanceof CSSString) {
													$arResult['src_local'][] = $oComponent3->getString();
												}
											}
										} elseif ($oComponent2 instanceof URL) {
											$arResult['src'][] = $oComponent2->getURL()->getString();
										}
									}
								}
							}
						}
					} elseif ($strName == "unicode-range") {
						$oValue = $oRule->getValue();
						if ($oValue instanceof RuleValueList) {
							$arComponents = $oValue->getListComponents();
							foreach ($arComponents as $oComponent) {
								if (is_string($oComponent)) {
									$arResult['unicode-range'][] = strval($oComponent);
								}
							}
						} elseif (!is_object($oValue)) {
							$arResult['unicode-range'][] = strval($oValue);
						}
					}
				}
			}
			if ($this->arOptions['fonts']['options']['NORMALIZE_ORDER'] == "Y") {
				//$oContent->removeRule();
				foreach ($arAllRules as &$oRule) {
					if ($oRule instanceof Rule) {
						/**
						 * @var $oRule Rule
						 */
						$strName = amopt_strtolower($oRule->getRule());
						if ($strName == 'src') {
							$strContent = $oRule->render(new OutputFormat());
							$strContent = str_replace(",", ";src:", $strContent);
							$oCssParserSrc = new Parser('@font-face{' . $strContent . '}', $this->oParserSettings);
							$oCssDocumentSrc = $oCssParserSrc->parse();
							$arSrcRules = $oCssDocumentSrc->getContents()[0]->getRules();
							uasort($arSrcRules, function ($a, $b) {
								$arOrderFonts = array("woff2", "woff", "ttf", "otf", "svg", "eot");
								/**
								 * @var Rule $a ,$b
								 */
								$oVal1 = $a->getValue();
								$oVal2 = $b->getValue();
								if ($oVal1 instanceof CSSFunction) {
									return -1;
								}
								$strType1 = "";
								$strType2 = "";
								if ($oVal1 instanceof RuleValueList) {
									foreach ($oVal1->getListComponents() as $oComp) {
										if ($oComp instanceof URL) {
											$strType1 = $oComp->getURL()->getString();
										}
									}
								}
								if ($oVal2 instanceof RuleValueList) {
									foreach ($oVal2->getListComponents() as $oComp) {
										if ($oComp instanceof URL) {
											$strType2 = $oComp->getURL()->getString();
										}
									}
								}
								if (amopt_strlen($strType1) > 0 && amopt_strlen($strType2) > 0) {
									if (amopt_strpos($strType1, '?') !== false) {
										$strType1 = explode("?", $strType1);
										$strType1 = $strType1[0];
									}
									$arPathType1 = ammina_pathinfo($strType1);
									$strType1 = amopt_strtolower($arPathType1['extension']);
									if (amopt_strpos($strType2, '?') !== false) {
										$strType2 = explode("?", $strType2);
										$strType2 = $strType2[0];
									}
									$arPathType2 = ammina_pathinfo($strType2);
									$strType2 = amopt_strtolower($arPathType2['extension']);

									$indx1 = array_search($strType1, $arOrderFonts);
									$indx2 = array_search($strType2, $arOrderFonts);
									if ($indx1 !== false && $indx2 !== false) {
										return ($indx1 < $indx2 ? -11 : 1);
									}
								}
								return 0;
							});
							$oCssParserSrc = new Parser('@font-face{}', $this->oParserSettings);
							$oCssDocumentSrc = $oCssParserSrc->parse();
							foreach ($arSrcRules as $k => $v) {
								$oCssDocumentSrc->getContents()[0]->addRule($v);
							}
							$strNewContent = $oCssDocumentSrc->getContents()[0]->render(new OutputFormat());
							$strNewContent = str_replace(';src:', ',', $strNewContent);
							$oCssParserSrc = new Parser($strNewContent, $this->oParserSettings);
							$oCssDocumentSrc = $oCssParserSrc->parse();
							$arSrcRules = $oCssDocumentSrc->getContents()[0]->getRules();

							$oContent->removeRule($oRule);
							foreach ($arSrcRules as $k => $v) {
								$oContent->addRule($v);
							}
						}
					}
				}
			}
			if (isset($arResult['src'])) {
				foreach ($arResult['src'] as $k => $v) {
					if (amopt_strpos($v, '?') !== false) {
						$v = explode("?", $v);
						$v = $v[0];
					}
					$arPath = ammina_pathinfo($v);
					$arResult['srcbytype'][amopt_strtolower($arPath['extension'])][] = $arResult['src'][$k];
				}
			}
			if (isset($arResult['unicode-range'])) {
				foreach ($arResult['unicode-range'] as $k => $v) {
					$ar = explode("-", $v);
					if (!isset($ar[1])) {
						$ar[1] = $ar[0];
					}
					$arResult['unicode-range-dec'][$k] = array(
						"MIN" => hexdec($ar[0]),
						"MAX" => hexdec($ar[1]),
					);
				}
			}
			if ($this->arOptions['fonts']['options']['FONT_FACE'] != "none") {
				if ($oRuleFontDisplay !== false) {
					$oRuleFontDisplay->setValue($this->arOptions['fonts']['options']['FONT_FACE']);
				} else {
					$oNewRule = new Rule("font-display");
					$oNewRule->setValue($this->arOptions['fonts']['options']['FONT_FACE']);
					$oContent->addRule($oNewRule);
				}
			}
		}
		return $arResult;
	}

	protected function doNormalizeFontWeight($strWeight)
	{
		if (amopt_strlen($strWeight) <= 0) {
			$strWeight = "normal";
		}
		$strWeight = amopt_strtolower($strWeight);
		return $strWeight;
	}

	protected function doNormalizeFontStyle($strStyle)
	{
		if (amopt_strlen($strStyle) <= 0) {
			$strStyle = "normal";
		}
		$strStyle = amopt_strtolower($strStyle);
		return $strStyle;
	}

	protected function doNormalizeCharsetValue($strValue)
	{
		global $APPLICATION;
		if (!defined("BX_UTF") || BX_UTF !== true) {
			if (\Bitrix\Main\Text\Encoding::detectUtf8($strValue)) {
				//valid UTF-8 octet sequences
				//0xxxxxxx
				//110xxxxx 10xxxxxx
				//1110xxxx 10xxxxxx 10xxxxxx
				//11110xxx 10xxxxxx 10xxxxxx 10xxxxxx

				$arNewData = array();
				$arData = unpack("C*", $strValue);
				$index = 0;
				$bIsUtf = false;
				$cntUtf = 0;
				foreach ($arData as $byte) {
					if ($bIsUtf && $cntUtf <= 0) {
						$bIsUtf = false;
					}
					if (!$bIsUtf) {
						if (($byte & 0x80) == 0x00) {
							$arNewData[$index][] = $byte;
						} elseif (($byte & 0xE0) == 0xC0) {
							$bIsUtf = true;
							$cntUtf = 1;
							$arNewData[$index][] = $byte;
						} elseif (($byte & 0xF0) == 0xE0) {
							$bIsUtf = true;
							$cntUtf = 2;
							$arNewData[$index][] = $byte;
						} elseif (($byte & 0xF8) == 0xF0) {
							$bIsUtf = true;
							$cntUtf = 3;
							$arNewData[$index][] = $byte;
						} else {
							$arNewData[$index] = chr($byte);
						}
					} else {
						$arNewData[$index][] = $byte;
						$cntUtf--;
					}
				}
				$strValue = "";
				foreach ($arNewData as $k => $v) {
					if (is_array($v)) {
						$iNew = 0;
						if (count($v) == 4) {
							$iNew = (($v[0] & 0x07) << 18) | (($v[1] & 0x3F) << 12) | (($v[2] & 0x3F) << 6) | ($v[3] & 0x3F);
						} elseif (count($v) == 3) {
							$iNew = (($v[0] & 0x0F) << 12) | (($v[1] & 0x3F) << 6) | ($v[2] & 0x3F);
						} elseif (count($v) == 2) {
							$iNew = (($v[0] & 0x1F) << 3) | ($v[1] & 0x3F);
						} elseif (count($v) == 1) {
							$iNew = $v[0] & 0x7F;
						}
						$strValue .= "\\" . dechex($iNew);
					} else {
						$strValue .= $v;
					}
				}
			}
		}
		return $strValue;
	}

	/**
	 * @param $oDocument Document
	 */
	protected function doNormalizeDocumentCharset(&$oDocument)
	{
		if (!defined("BX_UTF") || BX_UTF !== true) {
			$arRuleSets = $oDocument->getAllRuleSets();
			foreach ($arRuleSets as $k => $oRuleSets) {
				if ($oRuleSets instanceof DeclarationBlock) {
					/**
					 * @var $oRuleSets DeclarationBlock
					 */
					$arRules = $oRuleSets->getRules();
					foreach ($arRules as $kRule => $oRule) {
						if ($oRule instanceof Rule) {
							if ($oRule->getRule() == "content") {
								$oValue = $oRule->getValue();
								if ($oValue instanceof CSSString) {
									$oValue->setString($this->doNormalizeCharsetValue($oValue->getString()));
									$oValue->notSlashe = true;
								} elseif ($oValue instanceof RuleValueList) {
								} else {
								}
							}
						}
					}
				}
			}
		}
	}

	protected function getFontWeightSyn($strWeight)
	{
		$arSyn = array(
			"100" => array("thin", "hairline"),
			"200" => array("extralight", "ultralight"),
			"300" => array("light"),
			"400" => array("normal"),
			"500" => array("medium"),
			"600" => array("semibold", "demibold"),
			"700" => array("bold"),
			"800" => array("extrabold", "ultrabold"),
			"900" => array("black", "heavy"),
			"950" => array("extrablack", "ultrablack"),
			"thin" => array("100", "hairline"),
			"hairline" => array("100", "thin"),
			"extralight" => array("200", "ultralight"),
			"ultralight" => array("200", "extralight"),
			"light" => array("300"),
			"normal" => array("400"),
			"medium" => array("500"),
			"semibold" => array("600", "demibold"),
			"demibold" => array("600", "semibold"),
			"bold" => array("700"),
			"extrabold" => array("800", "ultrabold"),
			"ultrabold" => array("800", "extrabold"),
			"black" => array("900", "heavy"),
			"heavy" => array("900", "black"),
			"extrablack" => array("950", "ultrablack"),
			"ultrablack" => array("950", "extrablack"),
		);
		return $arSyn[amopt_strtolower($strWeight)];
	}

}