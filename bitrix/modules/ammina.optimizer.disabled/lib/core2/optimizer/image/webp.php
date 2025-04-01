<?

namespace Ammina\Optimizer\Core2\Optimizer\Image;

use Ammina\Optimizer\Core2\Application;
use Ammina\Optimizer\Core2\Driver\Image\AmminaCWebP;
use Ammina\Optimizer\Core2\Driver\Image\AmminaImagick;
use Ammina\Optimizer\Core2\Driver\Image\CWebP;
use Ammina\Optimizer\Core2\Driver\Image\Imagick;
use Ammina\Optimizer\Core2\Driver\Image\PhpGD;

class WebP extends Base
{
	/**
	 * @var PhpGD
	 */
	protected $oDriverPhpGD = NULL;
	/**
	 * @var Imagick
	 */
	protected $oDriverImagick = NULL;
	/**
	 * @var CWebP
	 */
	protected $oDriverCWebP = NULL;
	/**
	 * @var AmminaCWebP
	 */
	protected $oDriverAmminaCWebP = NULL;

	function __construct()
	{
		$this->oDriverPhpGD = new PhpGD();
		$this->oDriverImagick = new Imagick();
		$this->oDriverCWebP = new CWebP();
		$this->oDriverAmminaCWebP = new AmminaCWebP();
	}

	public function doOptimizeImage($strFilePath)
	{
		$strResult = $strFilePath;
		if ($this->arOptions['options']['ACTIVE'] == "Y" && (amopt_strpos($strFilePath, '/upload/ammina.optimizer/') !== 0 || amopt_strpos($strFilePath, '/upload/ammina.optimizer/lazy/') === 0)) {
			if (\CAmminaOptimizer::doMathPageToRules($this->arOptions['options']['EXCLUDE_FILES'], $strFilePath) && !\CAmminaOptimizer::doMathPageToRules($this->arOptions['options']['INCLUDE_FILES'], $strFilePath)) {
				return $strResult;
			}
			$arPathInfo = ammina_pathinfo($_SERVER['DOCUMENT_ROOT'] . $strFilePath);
			$arBasePathInfo = ammina_pathinfo($strFilePath);
			$ext = amopt_strtolower($arPathInfo['extension']);
			$strOptimizedFile = "/upload/ammina.optimizer/" . $ext . "-webp/q" . intval($this->arOptions['options']['QUALITY']) . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".webp";
			$strOptimizedFileWait = "/upload/ammina.optimizer/" . $ext . "-webp/q" . intval($this->arOptions['options']['QUALITY']) . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".webp.wait";
			$strOptimizedFileInfo = "/upload/ammina.optimizer/" . $ext . "-webp/q" . intval($this->arOptions['options']['QUALITY']) . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".webp.info";
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
					if ($this->arOptions['options']['LIBRARY'] == "phpgd") {
						$strResultFile = $this->oDriverPhpGD->optimizeImageWebP($strFilePath, $strOptimizedFile, $strOptimizedFileInfo, $this->arOptions['options']['QUALITY'], $this->arOptions['options_variant']['phpgd']);
					} elseif ($this->arOptions['options']['LIBRARY'] == "phpimagick") {
						//$strResultFile = $this->oDriverImagick->optimizeImageJpg($strFilePath, $strOptimizedFile, $strOptimizedFileInfo, $this->arOptions['options']['QUALITY'], $this->arOptions['options_variant']['phpimagick']);
					} elseif ($this->arOptions['options']['LIBRARY'] == "cwebp") {
						$strResultFile = $this->oDriverCWebP->optimizeImageWebP($strFilePath, $strOptimizedFile, $strOptimizedFileInfo, $this->arOptions['options']['QUALITY'], $this->arOptions['options_variant']['cwebp']);
					} elseif ($this->arOptions['options']['LIBRARY'] == "amminacwebp") {
						$strResultFile = $this->oDriverAmminaCWebP->optimizeImageWebP($strFilePath, $strOptimizedFile, $strOptimizedFileInfo, $this->arOptions['options']['QUALITY'], $this->arOptions['options_variant']['amminacwebp']);
					}
					if ($strResultFile !== true && file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFile) && filesize($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFile) > 0) {
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