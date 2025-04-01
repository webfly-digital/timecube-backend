<?

namespace Ammina\Optimizer\Core2\Optimizer\Image;

use Ammina\Optimizer\Core2\Application;
use Ammina\Optimizer\Core2\Driver\Image\AmminaImagick;
use Ammina\Optimizer\Core2\Driver\Image\AmminaOptiPng;
use Ammina\Optimizer\Core2\Driver\Image\AmminaPngQuant;
use Ammina\Optimizer\Core2\Driver\Image\Imagick;
use Ammina\Optimizer\Core2\Driver\Image\OptiPng;
use Ammina\Optimizer\Core2\Driver\Image\PngQuant;

class Png extends Base
{
	/**
	 * @var Imagick
	 */
	protected $oDriverImagick = NULL;
	/**
	 * @var PngQuant
	 */
	protected $oDriverPngQuant = NULL;
	/**
	 * @var OptiPng
	 */
	protected $oDriverOptiPng = NULL;
	/**
	 * @var AmminaImagick
	 */
	protected $oDriverAmminaImagick = NULL;
	/**
	 * @var AmminaPngQuant
	 */
	protected $oDriverAmminaPngQuant = NULL;
	/**
	 * @var AmminaOptiPng
	 */
	protected $oDriverAmminaOptiPng = NULL;

	function __construct()
	{
		$this->oDriverImagick = new Imagick();
		$this->oDriverPngQuant = new PngQuant();
		$this->oDriverOptiPng = new OptiPng();
		$this->oDriverAmminaImagick = new AmminaImagick();
		$this->oDriverAmminaPngQuant = new AmminaPngQuant();
		$this->oDriverAmminaOptiPng = new AmminaOptiPng();
	}

	public function doOptimizeImage($strFilePath)
	{
		$strResult = $strFilePath;
		if ($this->arOptions['options']['ACTIVE'] == "Y" && amopt_strpos($strFilePath, '/upload/ammina.optimizer/') !== 0) {
			if (\CAmminaOptimizer::doMathPageToRules($this->arOptions['options']['EXCLUDE_FILES'], $strFilePath) && !\CAmminaOptimizer::doMathPageToRules($this->arOptions['options']['INCLUDE_FILES'], $strFilePath)) {
				return $strResult;
			}
			$arPathInfo = ammina_pathinfo($_SERVER['DOCUMENT_ROOT'] . $strFilePath);
			$arBasePathInfo = ammina_pathinfo($strFilePath);
			if ($this->arOptions['options']['LIBRARY'] == "phpimagick") {
				if (!isset($this->arOptions['options_variant']['phpimagick']['QUALITY'])) {
					$this->arOptions['options_variant']['phpimagick']['QUALITY'] = 95;
				}
				$this->arOptions['options']['QUALITY'] = $this->arOptions['options_variant']['phpimagick']['QUALITY'];
			} elseif ($this->arOptions['options']['LIBRARY'] == "pngquant") {
				if (!isset($this->arOptions['options_variant']['pngquant']['QUALITY'])) {
					$this->arOptions['options_variant']['pngquant']['QUALITY'] = 75;
				}
				$this->arOptions['options']['QUALITY'] = $this->arOptions['options_variant']['pngquant']['QUALITY'];
			} elseif ($this->arOptions['options']['LIBRARY'] == "optipng") {
				if (!isset($this->arOptions['options_variant']['optipng']['QUALITY'])) {
					$this->arOptions['options_variant']['optipng']['QUALITY'] = 7;
				}
				$this->arOptions['options']['QUALITY'] = $this->arOptions['options_variant']['optipng']['QUALITY'];
			} elseif ($this->arOptions['options']['LIBRARY'] == "amminaphpimagick") {
				if (!isset($this->arOptions['options_variant']['amminaphpimagick']['QUALITY'])) {
					$this->arOptions['options_variant']['amminaphpimagick']['QUALITY'] = 95;
				}
				$this->arOptions['options']['QUALITY'] = $this->arOptions['options_variant']['amminaphpimagick']['QUALITY'];
			} elseif ($this->arOptions['options']['LIBRARY'] == "amminapngquant") {
				if (!isset($this->arOptions['options_variant']['amminapngquant']['QUALITY'])) {
					$this->arOptions['options_variant']['amminapngquant']['QUALITY'] = 75;
				}
				$this->arOptions['options']['QUALITY'] = $this->arOptions['options_variant']['amminapngquant']['QUALITY'];
			} elseif ($this->arOptions['options']['LIBRARY'] == "amminaoptipng") {
				if (!isset($this->arOptions['options_variant']['amminaoptipng']['QUALITY'])) {
					$this->arOptions['options_variant']['amminaoptipng']['QUALITY'] = 7;
				}
				$this->arOptions['options']['QUALITY'] = $this->arOptions['options_variant']['amminaoptipng']['QUALITY'];
			}

			$strOptimizedFile = "/upload/ammina.optimizer/png/q" . intval($this->arOptions['options']['QUALITY']) . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".png";
			$strOptimizedFileWait = "/upload/ammina.optimizer/png/q" . intval($this->arOptions['options']['QUALITY']) . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".png.wait";
			$strOptimizedFileInfo = "/upload/ammina.optimizer/png/q" . intval($this->arOptions['options']['QUALITY']) . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".png.info";
			/*$strOptimizedWebPFile = "/upload/ammina.optimizer/webp/q" . intval($this->arOptions['options']['QUALITY']) . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".webp";
			$strOptimizedWebPFileWait = "/upload/ammina.optimizer/webp/q" . intval($this->arOptions['options']['QUALITY']) . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".webp.wait";
			$strOptimizedWebPFileInfo = "/upload/ammina.optimizer/webp/q" . intval($this->arOptions['options']['QUALITY']) . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".webp.info";
			*/
			if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFile) || Application::getInstance()->isClearCache("image") || filesize($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFile) <= 0) {
				CheckDirPath(dirname($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFile) . "/");
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileWait) && file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileWait) >= (time() - 60)) {
					$strResult = $strFilePath;
				} else {
					\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileWait, time());
					if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFile)) {
						@unlink($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFile);
					}
					$strResultFile = false;
					if ($this->arOptions['options']['LIBRARY'] == "phpimagick") {
						$strResultFile = $this->oDriverImagick->optimizeImagePng($strFilePath, $strOptimizedFile, $strOptimizedFileInfo, $this->arOptions['options']['QUALITY'], $this->arOptions['options_variant']['phpimagick']);
					} elseif ($this->arOptions['options']['LIBRARY'] == "pngquant") {
						$strResultFile = $this->oDriverPngQuant->optimizeImagePng($strFilePath, $strOptimizedFile, $strOptimizedFileInfo, $this->arOptions['options']['QUALITY'], $this->arOptions['options_variant']['pngquant']);
					} elseif ($this->arOptions['options']['LIBRARY'] == "optipng") {
						$strResultFile = $this->oDriverOptiPng->optimizeImagePng($strFilePath, $strOptimizedFile, $strOptimizedFileInfo, $this->arOptions['options']['QUALITY'], $this->arOptions['options_variant']['optipng']);
					} elseif ($this->arOptions['options']['LIBRARY'] == "amminaphpimagick") {
						$strResultFile = $this->oDriverAmminaImagick->optimizeImagePng($strFilePath, $strOptimizedFile, $strOptimizedFileInfo, $this->arOptions['options']['QUALITY'], $this->arOptions['options_variant']['amminaphpimagick']);
					} elseif ($this->arOptions['options']['LIBRARY'] == "amminapngquant") {
						$strResultFile = $this->oDriverAmminaPngQuant->optimizeImagePng($strFilePath, $strOptimizedFile, $strOptimizedFileInfo, $this->arOptions['options']['QUALITY'], $this->arOptions['options_variant']['amminapngquant']);
					} elseif ($this->arOptions['options']['LIBRARY'] == "amminaoptipng") {
						$strResultFile = $this->oDriverAmminaOptiPng->optimizeImagePng($strFilePath, $strOptimizedFile, $strOptimizedFileInfo, $this->arOptions['options']['QUALITY'], $this->arOptions['options_variant']['amminaoptipng']);
					}
					if ($strResultFile !== true && file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFile)) {
						@unlink($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileWait);
						$strResult = $strResultFile;
					}

				}
			} else {
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFile) && filemtime($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFile) < (time() - 3600)) {
					@touch($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFile, time());
					if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileWait)) {
						@touch($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileWait, time());
					}
					if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileInfo)) {
						@touch($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileInfo, time());
					}
				}
				$strResult = $strOptimizedFile;
			}
		}
		return $strResult;
	}
}
