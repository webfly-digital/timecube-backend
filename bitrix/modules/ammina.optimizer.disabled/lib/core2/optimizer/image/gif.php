<?

namespace Ammina\Optimizer\Core2\Optimizer\Image;

use Ammina\Optimizer\Core2\Application;
use Ammina\Optimizer\Core2\Driver\Image\AmminaGifSicle;
use Ammina\Optimizer\Core2\Driver\Image\AmminaImagick;
use Ammina\Optimizer\Core2\Driver\Image\GifSicle;
use Ammina\Optimizer\Core2\Driver\Image\Imagick;

class Gif extends Base
{
	/**
	 * @var Imagick
	 */
	protected $oDriverImagick = NULL;
	/**
	 * @var GifSicle
	 */
	protected $oDriverGifSicle = NULL;
	/**
	 * @var AmminaImagick
	 */
	protected $oDriverAmminaImagick = NULL;
	/**
	 * @var AmminaGifSicle
	 */
	protected $oDriverAmminaGifSicle = NULL;

	function __construct()
	{
		$this->oDriverImagick = new Imagick();
		$this->oDriverGifSicle = new GifSicle();
		$this->oDriverAmminaImagick = new AmminaImagick();
		$this->oDriverAmminaGifSicle = new AmminaGifSicle();
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
			$strOptimizedFile = "/upload/ammina.optimizer/gif/q" . intval($this->arOptions['options']['QUALITY']) . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".gif";
			$strOptimizedFileWait = "/upload/ammina.optimizer/gif/q" . intval($this->arOptions['options']['QUALITY']) . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".gif.wait";
			$strOptimizedFileInfo = "/upload/ammina.optimizer/gif/q" . intval($this->arOptions['options']['QUALITY']) . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".gif.info";
			CheckDirPath(dirname($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFile) . "/");
			if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFile) || Application::getInstance()->isClearCache("image") || filesize($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFile) <= 0) {
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileWait) && file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileWait) >= (time() - 60)) {
					$strResult = $strFilePath;
				} else {
					\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileWait, time());
					if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFile)) {
						@unlink($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFile);
					}
					$strResultFile = false;
					if ($this->arOptions['options']['LIBRARY'] == "phpimagick") {
						$strResultFile = $this->oDriverImagick->optimizeImageGif($strFilePath, $strOptimizedFile, $strOptimizedFileInfo, $this->arOptions['options']['QUALITY'], $this->arOptions['options_variant']['phpimagick']);
					} elseif ($this->arOptions['options']['LIBRARY'] == "gifsicle") {
						$strResultFile = $this->oDriverGifSicle->optimizeImageGif($strFilePath, $strOptimizedFile, $strOptimizedFileInfo, $this->arOptions['options']['QUALITY'], $this->arOptions['options_variant']['gifsicle']);
					} elseif ($this->arOptions['options']['LIBRARY'] == "amminaphpimagick") {
						$strResultFile = $this->oDriverAmminaImagick->optimizeImageGif($strFilePath, $strOptimizedFile, $strOptimizedFileInfo, $this->arOptions['options']['QUALITY'], $this->arOptions['options_variant']['amminaphpimagick']);
					} elseif ($this->arOptions['options']['LIBRARY'] == "amminagifsicle") {
						$strResultFile = $this->oDriverAmminaGifSicle->optimizeImageGif($strFilePath, $strOptimizedFile, $strOptimizedFileInfo, $this->arOptions['options']['QUALITY'], $this->arOptions['options_variant']['amminagifsicle']);
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