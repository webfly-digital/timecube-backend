<?

namespace Ammina\Optimizer\Core2\Optimizer;

use Ammina\Optimizer\Core2\Application;
use Ammina\Optimizer\Core2\Optimizer\Image\Gif;
use Ammina\Optimizer\Core2\Optimizer\Image\Jpg;
use Ammina\Optimizer\Core2\Optimizer\Image\Lazy;
use Ammina\Optimizer\Core2\Optimizer\Image\Png;
use Ammina\Optimizer\Core2\Optimizer\Image\Svg;
use Ammina\Optimizer\Core2\Optimizer\Image\WebP;
use Sabberworm\CSS\CSSList\Document;
use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Parser;
use Sabberworm\CSS\Value\URL;

class Image
{
	protected $arOptions = false;
	/**
	 * @var Jpg
	 */
	protected $oJpgImage = null;
	/**
	 * @var Png
	 */
	protected $oPngImage = null;
	/**
	 * @var Gif
	 */
	protected $oGifImage = null;
	/**
	 * @var Svg
	 */
	protected $oSvgImage = null;
	/**
	 * @var WebP
	 */
	public $oWebPImage = null;
	/**
	 * @var Lazy
	 */
	protected $oLazy = null;

	protected $arAllImage = array();
	public $arInlineTypes = array(
		"png" => "image/png",
		"gif" => "image/gif",
		"jpg" => "image/jpeg",
		"jpeg" => "image/jpeg",
		"svg" => "image/svg+xml",
	);
	public $arInlineTypesWebP = array(
		"png" => "image/png",
		"gif" => "image/gif",
		"jpg" => "image/jpeg",
		"jpeg" => "image/jpeg",
		"svg" => "image/svg+xml",
		"webp" => "image/webp",
	);
	public $arHeadersPreloadFiles = array();
	public $strBaseIdent = "";
	public $iCntPreloadImg = 0;

	public function __construct($arOptions)
	{
		$this->oJpgImage = new Jpg();
		$this->oPngImage = new Png();
		$this->oGifImage = new Gif();
		$this->oSvgImage = new Svg();
		$this->oWebPImage = new WebP();
		$this->oLazy = new Lazy($this);
		$this->setOptions($arOptions);
	}

	public function setOptions($arOptions)
	{
		$this->arOptions = $arOptions;
		$this->strBaseIdent = $arOptions;
		unset($this->strBaseIdent['jpg_files']['DEFAULT']);
		unset($this->strBaseIdent['png_files']['DEFAULT']);
		unset($this->strBaseIdent['gif_files']['DEFAULT']);
		unset($this->strBaseIdent['svg_files']['DEFAULT']);
		unset($this->strBaseIdent['webp_files']['DEFAULT']);
		unset($this->strBaseIdent['events']['DEFAULT']);
		unset($this->strBaseIdent['external_images']['DEFAULT']);
		unset($this->strBaseIdent['lazy']['DEFAULT']);
		unset($this->strBaseIdent['other']['DEFAULT']);

		unset($this->strBaseIdent['jpg_files']['options']['EXCLUDE_FILES']);
		unset($this->strBaseIdent['jpg_files']['options']['INCLUDE_FILES']);
		unset($this->strBaseIdent['jpg_files']['options']['EXCLUDE_WEBP_FILES']);
		unset($this->strBaseIdent['jpg_files']['options']['INCLUDE_WEBP_FILES']);

		unset($this->strBaseIdent['png_files']['options']['EXCLUDE_FILES']);
		unset($this->strBaseIdent['png_files']['options']['INCLUDE_FILES']);
		unset($this->strBaseIdent['png_files']['options']['EXCLUDE_WEBP_FILES']);
		unset($this->strBaseIdent['png_files']['options']['INCLUDE_WEBP_FILES']);
		unset($this->strBaseIdent['png_files']['options']['EXCLUDE_JPG_FILES']);
		unset($this->strBaseIdent['png_files']['options']['INCLUDE_JPG_FILES']);

		unset($this->strBaseIdent['gif_files']['options']['EXCLUDE_FILES']);
		unset($this->strBaseIdent['gif_files']['options']['INCLUDE_FILES']);

		unset($this->strBaseIdent['svg_files']['options']['EXCLUDE_FILES']);
		unset($this->strBaseIdent['svg_files']['options']['INCLUDE_FILES']);

		unset($this->strBaseIdent['webp_files']['options']['EXCLUDE_FILES']);
		unset($this->strBaseIdent['webp_files']['options']['INCLUDE_FILES']);

		unset($this->strBaseIdent['events']);

		unset($this->strBaseIdent['external_images']['options']['EXCLUDE']);
		unset($this->strBaseIdent['external_images']['options']['INCLUDE']);

		$this->strBaseIdent = md5(serialize($this->strBaseIdent));
		$this->oJpgImage->setOptions($this->arOptions['jpg_files']);
		$this->oPngImage->setOptions($this->arOptions['png_files']);
		$this->oGifImage->setOptions($this->arOptions['gif_files']);
		$this->oSvgImage->setOptions($this->arOptions['svg_files']);
		$this->oWebPImage->setOptions($this->arOptions['webp_files']);
		$this->oLazy->setOptions($this->arOptions['lazy']);
	}

	public function doOptimizePart($bPreventWebP = false)
	{
		$this->arAllImage = array();
		$this->doOptimize($bPreventWebP);
		$this->arAllImage = array();
	}

	public function doOptimize($bPreventWebP = false)
	{
		$this->oLazy->doMakeLazy();
		$arCheckTypes = array(
			"CHECK_IMG" => $this->arOptions['search_model']['options']['CHECK_IMG'] == "Y",
			"CHECK_SRCSET_IMG" => $this->arOptions['search_model']['options']['CHECK_SRCSET_IMG'] == "Y",
			"CHECK_BACKGROUND" => $this->arOptions['search_model']['options']['CHECK_BACKGROUND'] == "Y",
			"CHECK_DATA_SRC" => $this->arOptions['search_model']['options']['CHECK_DATA_SRC'] == "Y",
			"CHECK_ATTRIBUTES" => $this->arOptions['search_model']['options']['CHECK_ATTRIBUTES'] == "Y",
			"CHECK_ALL_OTHER" => $this->arOptions['search_model']['options']['CHECK_ALL_OTHER'] == "Y",
		);
		if ($arCheckTypes['CHECK_ATTRIBUTES']) {
			$arCheckTypes['CHECK_IMG'] = false;
			$arCheckTypes['CHECK_DATA_SRC'] = false;
		}
		$this->arAllImage = Application::getInstance()->getParser()->getAllImages($arCheckTypes);
		foreach ($this->arAllImage as $index => $arImage) {
			if ($arImage['TYPE'] == "SRCSET") {
				$bOptimized = false;
				$arV = explode(", ", $arImage['IMAGE']);
				foreach ($arV as $k => $v) {
					$arV1 = explode(" ", $v);
					foreach ($arV1 as $k1 => $v1) {
						if (Application::getInstance()->getParser()->isImage($v1)) {
							$newV1 = $this->doOptimizeImage($v1, true, false, $bPreventWebP, $arImage['ATTR']);
							if (amopt_strlen($newV1) > 0) {
								$arV1[$k1] = $newV1;
								$bOptimized = true;
							}
						}
					}
					$arV[$k] = implode(" ", $arV1);
				}
				if ($bOptimized) {
					Application::getInstance()->getParser()->setImageAttribute($index, implode(", ", $arV));
				}
			} elseif ($arImage['TYPE'] == "ATTRIBUTE") {
				$this->arAllImage[$index]['OPTIMIZE_FILE'] = $this->doOptimizeImage($arImage['IMAGE'], true, false, $bPreventWebP, $arImage['ATTR']);
				if (amopt_strlen($this->arAllImage[$index]['OPTIMIZE_FILE']) > 0) {
					Application::getInstance()->getParser()->setImageAttribute($index, $this->arAllImage[$index]['OPTIMIZE_FILE']);
				}
			} elseif ($arImage['TYPE'] == "STYLE") {
				$this->arAllImage[$index]['OPTIMIZE_STYLE'] = $this->doOptimizeStyle($arImage['STYLE'], $bPreventWebP);
				if (amopt_strlen($this->arAllImage[$index]['OPTIMIZE_STYLE']) > 0) {
					Application::getInstance()->getParser()->setImageAttribute($index, $this->arAllImage[$index]['OPTIMIZE_STYLE']);
				}
			} elseif ($arImage['TYPE'] == "SCRIPT") {
				$this->arAllImage[$index]['OPTIMIZE_SCRIPT'] = $this->doOptimizeScript($arImage['SCRIPT'], $bPreventWebP);
				if (amopt_strlen($this->arAllImage[$index]['OPTIMIZE_SCRIPT']) > 0) {
					Application::getInstance()->getParser()->setImageScriptContent($index, $this->arAllImage[$index]['OPTIMIZE_SCRIPT']);
				}
			}
		}
	}

	public function doOptimizeImage($strFilePath, $bAllowConvert = true, $strBaseBath = false, $bPreventWebP = false, $strFromAttr = '')
	{
		$strResult = $strFilePath;
		$strFilePath = $this->checkRemoteFile($strFilePath, $strBaseBath);
		if ($strFilePath === false) {
			return $strResult;
		}
		$bNotChangeOriginal = ($this->arOptions['other']['options']['NOT_CHANGE_ORIGINALS'] == "Y");
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strFilePath)) {
			$strFilePath = urldecode($strFilePath);
		}
		$arPathInfo = ammina_pathinfo($_SERVER['DOCUMENT_ROOT'] . $strFilePath);
		$arPathInfo['extension'] = amopt_strtolower($arPathInfo['extension']);
		if (!in_array($arPathInfo['extension'], array("jpg", "jpeg", "png", "gif", "svg"))) {
			return false;
		}
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strFilePath)) {
			return false;
		}
		if ($arPathInfo['extension'] == "jpg" || $arPathInfo['extension'] == "jpeg") {
			if (!$bNotChangeOriginal) {
				$strResult = $this->oJpgImage->doOptimizeImage($strFilePath);
			}
			if ($bAllowConvert && ($bPreventWebP == "Y" || ($bPreventWebP === false && Application::getInstance()->isSupportWebP() && $this->arOptions['jpg_files']['options']['CONVERT_WEBP'] == "Y"))) {
				if (!(\CAmminaOptimizer::doMathPageToRules($this->arOptions['jpg_files']['options']['EXCLUDE_WEBP_FILES'], $strFilePath) && !\CAmminaOptimizer::doMathPageToRules($this->arOptions['options']['INCLUDE_WEBP_FILES'], $strFilePath))) {
					$strResult = $this->oWebPImage->doOptimizeImage($strFilePath);
				}
			}
		} elseif ($arPathInfo['extension'] == "png") {
			if (!$bNotChangeOriginal) {
				$strResult = $this->oPngImage->doOptimizeImage($strFilePath);
			}
			if ($bAllowConvert) {
				if (!Application::getInstance()->isSupportWebP() && $this->arOptions['png_files']['options']['CONVERT_JPG'] == "Y") {
					if (!(\CAmminaOptimizer::doMathPageToRules($this->arOptions['png_files']['options']['EXCLUDE_JPG_FILES'], $strFilePath) && !\CAmminaOptimizer::doMathPageToRules($this->arOptions['options']['INCLUDE_JPG_FILES'], $strFilePath))) {
						$strResult = $this->oJpgImage->doConvertAndOptimizeImage(
							$strFilePath,
							array(
								"BG_COLOR" => $this->arOptions['png_files']['options']['CONVERT_JPG_BG'],
							)
						);
					}
				} elseif ($bPreventWebP == "Y" || ($bPreventWebP === false && Application::getInstance()->isSupportWebP() && $this->arOptions['png_files']['options']['CONVERT_WEBP'] == "Y")) {
					if (!(\CAmminaOptimizer::doMathPageToRules($this->arOptions['png_files']['options']['EXCLUDE_WEBP_FILES'], $strFilePath) && !\CAmminaOptimizer::doMathPageToRules($this->arOptions['options']['INCLUDE_WEBP_FILES'], $strFilePath))) {
						$strResult = $this->oWebPImage->doOptimizeImage($strFilePath);
					}
				}
			}
		} elseif ($arPathInfo['extension'] == "gif") {
			if (!$bNotChangeOriginal) {
				$strResult = $this->oGifImage->doOptimizeImage($strFilePath);
			}
			if ($bAllowConvert) {
				if (!Application::getInstance()->isSupportWebP() && $this->arOptions['gif_files']['options']['CONVERT_JPG'] == "Y") {
					if (!(\CAmminaOptimizer::doMathPageToRules($this->arOptions['gif_files']['options']['EXCLUDE_JPG_FILES'], $strFilePath) && !\CAmminaOptimizer::doMathPageToRules($this->arOptions['options']['INCLUDE_JPG_FILES'], $strFilePath))) {
						$strResult = $this->oJpgImage->doConvertAndOptimizeImage(
							$strFilePath,
							array(
								"BG_COLOR" => $this->arOptions['gif_files']['options']['CONVERT_JPG_BG'],
							)
						);
					}
				} elseif ($bPreventWebP == "Y" || ($bPreventWebP === false && Application::getInstance()->isSupportWebP() && $this->arOptions['gif_files']['options']['CONVERT_WEBP'] == "Y")) {
					if (!(\CAmminaOptimizer::doMathPageToRules($this->arOptions['gif_files']['options']['EXCLUDE_WEBP_FILES'], $strFilePath) && !\CAmminaOptimizer::doMathPageToRules($this->arOptions['options']['INCLUDE_WEBP_FILES'], $strFilePath))) {
						$strResult = $this->oWebPImage->doOptimizeImage($strFilePath);
					}
				}
			}
		} elseif ($arPathInfo['extension'] == "svg") {
			$strResult = $this->oSvgImage->doOptimizeImage($strFilePath);
		}
		if ($this->arOptions['other']['options']['PRELOAD_IMG'] == "Y") {
			if ($this->arOptions['other']['options']['PRELOAD_IMG_COUNT'] <= 0 || ($this->arOptions['other']['options']['PRELOAD_IMG_COUNT'] > 0 && $this->iCntPreloadImg < $this->arOptions['other']['options']['PRELOAD_IMG_COUNT'])) {
				$this->arHeadersPreloadFiles[] = array(
					"FILE" => $strResult,
					"TYPE" => "IMAGE",
				);
				$this->iCntPreloadImg++;
			}
		}
		if ($this->arOptions['other']['options']['INLINE_IMG'] == "Y") {
			if (strtoupper($strFromAttr) != "HREF") {
				$strResult = $this->getImageInline($strResult);
			}
		}
		return $strResult;
	}

	public function doOptimizeStyle($strStyle, $bPreventWebP = false)
	{
		global $APPLICATION;
		$arIdent = array(
			"STYLE" => $strStyle,
			"IDENT" => $this->strBaseIdent,
			"IS_WEBP" => ($bPreventWebP == "Y" || ($bPreventWebP === false && Application::getInstance()->isSupportWebP())),
		);
		$strIdent = md5(serialize($arIdent));
		$strCacheFile = "/bitrix/ammina.cache/img/ammina.optimizer/" . SITE_ID . "/style/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".css";
		$strContent = "";
		$isOptimizedImage = false;
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile)) {
			$strContent = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strCacheFile);
			if (amopt_strpos($strContent, '/upload/ammina.') !== false) {
				$isOptimizedImage = true;
			}
		}
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) || Application::getInstance()->isClearCache("image") || Application::getInstance()->isClearCache("css") || (!$isOptimizedImage && (time() - filemtime($_SERVER['DOCUMENT_ROOT'] . $strCacheFile)) > 300)) {
			$oCssParser = new Parser('.style{' . $strStyle . '}');
			$oCssDocument = $oCssParser->parse();
			$this->doOptimizeCssImages($oCssDocument, $APPLICATION->GetCurPage(true), $bPreventWebP);
			$strResult = trim($oCssDocument->render(OutputFormat::createCompact()));
			$strResult = amopt_substr($strResult, 7, amopt_strlen($strResult) - 8);
			\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, $strResult);
			return $strResult;
		} else {
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) && filemtime($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) < (time() - 3600)) {
				@touch($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, time());
			}
		}
		return $strContent;
	}

	public function doOptimizeScript($strScript, $bPreventWebP = false)
	{
		global $APPLICATION;
		$strTmpScript = $strScript;
		$strTmpScript = str_replace('"', "'", $strTmpScript);
		$arTmpScript = explode("'", $strTmpScript);
		$arReplace = array();
		foreach ($arTmpScript as $k => $v) {
			$arPathInfo = ammina_pathinfo($v);
			if (isset($arPathInfo['extension'])) {
				$arPathInfo['extension'] = amopt_strtolower($arPathInfo['extension']);
				if (isset($this->arInlineTypes[$arPathInfo['extension']])) {
					$strNewImage = $this->doOptimizeImage($v, true, false, $bPreventWebP);
					if ($strNewImage === false) {
						$strNewImage = $v;
					}
					if ($strNewImage != $v) {
						$arReplace[$v] = $strNewImage;
					}
				}
			}
		}
		if (!empty($arReplace)) {
			foreach ($arReplace as $k => $v) {
				$strScript = str_replace($k, $v, $strScript);
			}
		}
		return $strScript;
	}

	/**
	 * @param $oCssDocument Document
	 */
	protected function doOptimizeCssImages(&$oCssDocument, $strFileName, $bPreventWebP)
	{
		$arAllValues = $oCssDocument->getAllValues();
		foreach ($arAllValues as $oValue) {
			if ($oValue instanceof URL) {
				/**
				 * @var $oValue URL
				 */
				$strUrl = $oValue->getURL()->getString();
				$strNewUrl = $this->doOptimizeImage($strUrl, true, $strFileName, $bPreventWebP);
				if ($strNewUrl === false) {
					$strNewUrl = $strUrl;
				}
				$oValue->getURL()->setString($strNewUrl);
				if ($this->arOptions['other']['options']['INLINE_IMG'] == "Y") {
					$oValue->getURL()->setString($this->getImageInline($strNewUrl));
				}
			}
		}
	}

	protected function getImageInline($strFileName)
	{
		$strFullName = $_SERVER['DOCUMENT_ROOT'] . $strFileName;
		$iMaxFileSize = $this->arOptions['other']['options']['MAX_SIZE_INLINE'];
		if (file_exists($strFullName) && filesize($strFullName) <= $iMaxFileSize) {
			$arPathInfo = ammina_pathinfo($strFullName);
			$arAllowInlineTypes = explode(",", $this->arOptions['other']['options']['INLINE_TYPES']);
			$arPathInfo['extension'] = amopt_strtolower($arPathInfo['extension']);
			if (in_array($arPathInfo['extension'], $arAllowInlineTypes) && isset($this->arInlineTypesWebP[$arPathInfo['extension']])) {
				return 'data:' . $this->arInlineTypesWebP[$arPathInfo['extension']] . ';base64,' . base64_encode(file_get_contents($strFullName));
			}
		}
		return $strFileName;
	}

	protected function checkRemoteFile($strFileName, $strBasePath = false)
	{
		global $APPLICATION;
		if ($strBasePath === false) {
			$strBasePath = $APPLICATION->GetCurPage(true);
		}
		if (amopt_strpos($strFileName, 'data:') !== 0) {
			if (amopt_strpos($strFileName, '://') !== false || amopt_strpos($strFileName, '//') === 0) {
			} elseif (amopt_strpos($strBasePath, '://') !== false || amopt_strpos($strBasePath, '//') === 0) {
				$strFileName = \CAmminaOptimizer::Rel2AbsUrl($strBasePath, $strFileName);
			} else {
				$strFileName = Rel2Abs(dirname($strBasePath), $strFileName);
			}
		}

		if (amopt_strpos($strFileName, '://') !== false || amopt_strpos($strFileName, '//') === 0) {
			$strTmpFileName = $strFileName;
			if (\CAmminaOptimizer::checkRequestDomainUrl($strTmpFileName)) {
				$strFileName = $strTmpFileName;
				return $strFileName;
			}
		}

		if (amopt_strpos($strFileName, '://') !== false || amopt_strpos($strFileName, '//') === 0) {
			if ($this->arOptions['external_images']['options']['ACTIVE'] == "Y" && !\CAmminaOptimizer::isLocalDomainLink($strFileName)) {
				if (\CAmminaOptimizer::doMathPageToRules($this->arOptions['external_images']['options']['EXCLUDE'], $strFileName) && !\CAmminaOptimizer::doMathPageToRules($this->arOptions['external_images']['options']['INCLUDE'], $strFileName)) {
					return false;
				}
				$strIdent = md5($strFileName);
				$arUrl = parse_url($strFileName);
				$arPath = ammina_pathinfo($arUrl['path']);
				if (amopt_strlen($arPath['extension']) <= 0 || !in_array($arPath['extension'], array("jpg", "jpeg", "png", "gif", "svg"))) {
					return false;
				}
				$strCacheFile = "/upload/ammina.optremote/" . SITE_ID . "/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . "." . $arPath['extension'];
				if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) || Application::getInstance()->isClearCache("image")) {
					$strContent = \CAmminaOptimizer::doRequestPageRemote($strFileName, '', 10, 5);
					if (amopt_strlen($strContent) > 0) {
						\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, $strContent);
					}
				} else {
					if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) && filemtime($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) < (time() - 3600)) {
						@touch($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, time());
					}
				}
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile)) {
					return $strCacheFile;
				} else {
					return false;
				}
			}
		} elseif (amopt_strpos($strFileName, 'data:') === 0) {
			return false;
		}
		return $strFileName;
	}

}
