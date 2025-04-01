<?

namespace Ammina\Optimizer\Core2\Optimizer\Image;


use Ammina\Optimizer\Core2\Application;
use Ammina\Optimizer\Core2\Driver\Image\Imagick;
use Ammina\Optimizer\Core2\Driver\Image\PhpGD;
use Ammina\Optimizer\Core2\Optimizer\Image;
use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Parser;
use Sabberworm\CSS\Value\URL;

class Lazy
{
	protected $arOptions = false;
	protected $arAllTags = array();
	/**
	 * @var Image
	 */
	protected $oImageOptimizer = NULL;
	protected $arAllowLibrary = array();
	/**
	 * @var Imagick
	 */
	protected $oDriverImagick = NULL;
	/**
	 * @var PhpGD
	 */
	protected $oDriverPhpGD = NULL;

	/**
	 * Lazy constructor.
	 *
	 * @param $oImageOptimizer Image
	 */
	public function __construct(&$oImageOptimizer)
	{
		$this->oImageOptimizer = $oImageOptimizer;
		$this->arAllowLibrary = \Ammina\Optimizer\Core2\LibAvailable::getCurrentCheckData();
		$this->oDriverImagick = new Imagick();
		$this->oDriverPhpGD = new PhpGD();
	}

	public function setOptions($arOptions)
	{
		$this->arOptions = $arOptions;
	}

	public function doMakeLazy()
	{
		if ($this->arOptions['options']['ACTIVE'] == "Y") {
			$this->arAllTags = Application::getInstance()->getParser()->getAllLazyTags($this->arOptions['options']['LAZY_CLASS'], $this->arOptions['options']['CHECK_ALL_IMG'] == "Y");
			foreach ($this->arAllTags as $index => $arTag) {
				if ($arTag['TYPE'] == "img") {
					$this->arAllTags[$index]['OPTIMIZE_FILE'] = $this->oImageOptimizer->doOptimizeImage($arTag['IMAGE']);
					if (amopt_strlen($this->arAllTags[$index]['OPTIMIZE_FILE']) > 0) {
						$this->arAllTags[$index]['LAZY_FILE'] = $this->doMakeLazyFile($this->arAllTags[$index]['IMAGE']);
						Application::getInstance()->getParser()->setLazyNodeAttribute($index, "data-src", $this->arAllTags[$index]['OPTIMIZE_FILE']);
						if (amopt_strlen($this->arAllTags[$index]['LAZY_FILE']) > 0) {
							Application::getInstance()->getParser()->setLazyAttribute($index, $this->arAllTags[$index]['LAZY_FILE']);
							Application::getInstance()->getParser()->addLazyClassAttribute($index, "ammina-lazy");
						}
					}
				} else {
					$this->arAllTags[$index]['OPTIMIZE_STYLE'] = $this->oImageOptimizer->doOptimizeStyle($arTag['STYLE']);
					if (amopt_strlen($this->arAllTags[$index]['OPTIMIZE_STYLE']) > 0) {
						$this->doMakeLazyStyle($index);
						if (amopt_strlen($this->arAllTags[$index]['OPTIMIZE_LAZY_STYLE']) > 0 && amopt_strlen($this->arAllTags[$index]['OPTIMIZE_MAIN_IMG']) > 0) {
							Application::getInstance()->getParser()->setLazyNodeAttribute($index, "data-background-image", $this->arAllTags[$index]['OPTIMIZE_MAIN_IMG']);
							Application::getInstance()->getParser()->setLazyAttribute($index, $this->arAllTags[$index]['OPTIMIZE_LAZY_STYLE']);
							Application::getInstance()->getParser()->addLazyClassAttribute($index, "ammina-lazy");
						}
					}
				}
			}
			if ($this->arOptions['options']['INCLUDE_JQUERY'] == "Y") {
				Application::getInstance()->getParser()->AddTag("//head[1]", "script", array(
					"src" => "/bitrix/js/ammina.optimizer/jquery-3.3.1.js?" . filemtime($_SERVER['DOCUMENT_ROOT'] . "/bitrix/js/ammina.optimizer/jquery-3.3.1.js"),
					"type" => "text/javascript",
				));
			}
			Application::getInstance()->getParser()->AddTag("//head[1]", "script", array(
				"src" => "/bitrix/js/ammina.optimizer/ammina.lazy.js?" . filemtime($_SERVER['DOCUMENT_ROOT'] . "/bitrix/js/ammina.optimizer/ammina.lazy.js"),
				"type" => "text/javascript",
			));
		}
	}

	public function doMakeLazyFile($strFileName)
	{
		$strResult = $this->doMakeLazyFileMainImg();
		if ($this->arOptions['options']['TYPE'] == "blurgrey") {
			$strResult = $this->doMakeLazyFileBlurGrey($strFileName);
		} elseif ($this->arOptions['options']['TYPE'] == "blur") {
			$strResult = $this->doMakeLazyFileBlur($strFileName);
		}
		if (amopt_strlen($strResult) > 0 && file_exists($_SERVER['DOCUMENT_ROOT'] . $strResult) && Application::getInstance()->isSupportWebP() && $this->arOptions['lazy']['options']['CONVERT_WEBP'] == "Y") {
			//if (!(\CAmminaOptimizer::doMathPageToRules($this->arOptions['jpg_files']['options']['EXCLUDE_WEBP_FILES'], $strFilePath) && !\CAmminaOptimizer::doMathPageToRules($this->arOptions['options']['INCLUDE_WEBP_FILES'], $strFilePath))) {
			$strResult = $this->oImageOptimizer->oWebPImage->doOptimizeImage($strResult);
			//}
		}
		if ($this->arOptions['options']['INLINE_IMG'] == "Y" && filesize($_SERVER['DOCUMENT_ROOT'] . $strResult) <= $this->arOptions['options']['MAX_SIZE_INLINE']) {
			$strFullName = $_SERVER['DOCUMENT_ROOT'] . $strResult;
			$arPathInfo = ammina_pathinfo($strFullName);
			$arPathInfo['extension'] = amopt_strtolower($arPathInfo['extension']);
			if (isset($this->oImageOptimizer->arInlineTypesWebP[$arPathInfo['extension']])) {
				return 'data:' . $this->oImageOptimizer->arInlineTypesWebP[$arPathInfo['extension']] . ';base64,' . base64_encode(file_get_contents($strFullName));
			}
		}
		return $strResult;
	}

	public function doMakeLazyFileBlur($strFileName)
	{
		$arBasePathInfo = ammina_pathinfo($strFileName);
		$arBasePathInfo['extension'] = amopt_strtolower($arBasePathInfo['extension']);
		if ($arBasePathInfo['extension'] == "png") {
			$strLazyFile = "/upload/ammina.optimizer/lazy/blur/png" . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".png";
		} elseif (in_array($arBasePathInfo['extension'], array("jpg", "jpeg"))) {
			$strLazyFile = "/upload/ammina.optimizer/lazy/blur/jpg" . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".jpg";
		} else {
			return $this->doMakeLazyFileMainImg();
		}
		$strLazyFileInfo = $strLazyFile . ".info";
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strLazyFile)) {
			CheckDirPath(dirname($_SERVER['DOCUMENT_ROOT'] . $strLazyFile) . "/");
			if ($this->arAllowLibrary['packages']['phpimagick']) {
				$this->oDriverImagick->doMakeLazyFileBlur($strFileName, $strLazyFile, $strLazyFileInfo);
			} elseif ($this->arAllowLibrary['packages']['phpgd']) {
				$this->oDriverPhpGD->doMakeLazyFileBlur($strFileName, $strLazyFile, $strLazyFileInfo);
			} else {
				return $this->doMakeLazyFileMainImg();
			}
		}
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strLazyFile)) {
			return $strLazyFile;
		}
		return false;
	}

	public function doMakeLazyFileBlurGrey($strFileName)
	{
		$arBasePathInfo = ammina_pathinfo($strFileName);
		$arBasePathInfo['extension'] = amopt_strtolower($arBasePathInfo['extension']);
		if ($arBasePathInfo['extension'] == "png") {
			$strLazyFile = "/upload/ammina.optimizer/lazy/blurgrey/png" . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".png";
		} elseif (in_array($arBasePathInfo['extension'], array("jpg", "jpeg"))) {
			$strLazyFile = "/upload/ammina.optimizer/lazy/blurgrey/jpg" . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".jpg";
		} else {
			return $this->doMakeLazyFileMainImg();
		}
		$strLazyFileInfo = $strLazyFile . ".info";
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strLazyFile)) {
			CheckDirPath(dirname($_SERVER['DOCUMENT_ROOT'] . $strLazyFile) . "/");
			if ($this->arAllowLibrary['packages']['phpimagick'] && false) {
				$this->oDriverImagick->doMakeLazyFileBlurGrey($strFileName, $strLazyFile, $strLazyFileInfo);
			} elseif ($this->arAllowLibrary['packages']['phpgd']) {
				$this->oDriverPhpGD->doMakeLazyFileBlurGrey($strFileName, $strLazyFile, $strLazyFileInfo);
			} else {
				return $this->doMakeLazyFileMainImg();
			}
		}
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strLazyFile)) {
			return $strLazyFile;
		}
		return false;
	}

	public function doMakeLazyFileMainImg()
	{
		if (amopt_strlen($this->arOptions['options']['MAINIMG']) > 0) {
			return $this->arOptions['options']['MAINIMG'];
		}
		return "/bitrix/images/ammina.optimizer/lazy.gif";
	}

	public function doMakeLazyStyle($index)
	{
		global $APPLICATION;
		$strStyle = $this->arAllTags[$index]['OPTIMIZE_STYLE'];
		$arIdent = array(
			"STYLE" => $strStyle,
			"IDENT" => $this->oImageOptimizer->strBaseIdent,
			"LAZY" => true,
			"TYPE" => $this->arOptions['options']['TYPE'],
		);
		$strIdent = md5(serialize($arIdent));
		$strCacheFile = "/bitrix/ammina.cache/img/ammina.optimizer/" . SITE_ID . "/lazy/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".css.txt";
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) || Application::getInstance()->isClearCache()) {
			CheckDirPath(dirname($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) . "/");
			$oCssParser = new Parser('.style{' . $strStyle . '}');
			$oCssDocument = $oCssParser->parse();

			$arAllValues = $oCssDocument->getAllValues();
			foreach ($arAllValues as $oValue) {
				if ($oValue instanceof URL) {
					/**
					 * @var $oValue URL
					 */
					$strUrl = $oValue->getURL()->getString();
					if (amopt_strpos($strUrl, '://') !== false || amopt_strpos($strUrl, '//') === 0 || amopt_strpos($strUrl, 'data:') !== false) {
						return;
					}
					$this->arAllTags[$index]['OPTIMIZE_MAIN_IMG'] = $strUrl;
					$strNewUrl = $this->doMakeLazyFile($strUrl);
					$oValue->getURL()->setString($strNewUrl);
				}
			}

			$strResult = trim($oCssDocument->render(OutputFormat::createCompact()));
			$strResult = amopt_substr($strResult, 7, amopt_strlen($strResult) - 8);
			$strResult = serialize(array(
				"STYLE" => $strResult,
				"IMG" => $this->arAllTags[$index]['OPTIMIZE_MAIN_IMG'],
			));
			\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, $strResult);
		} else {
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) && filemtime($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) < (time() - 3600)) {
				@touch($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, time());
			}
		}
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile)) {
			$arResult = unserialize(file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strCacheFile));
			$this->arAllTags[$index]['OPTIMIZE_LAZY_STYLE'] = $arResult['STYLE'];
			$this->arAllTags[$index]['OPTIMIZE_MAIN_IMG'] = $arResult['IMG'];
		}
	}

}