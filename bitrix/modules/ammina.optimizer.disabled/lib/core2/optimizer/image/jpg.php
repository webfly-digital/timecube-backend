<?

namespace Ammina\Optimizer\Core2\Optimizer\Image;

use Ammina\Optimizer\Core2\Application;
use Ammina\Optimizer\Core2\Driver\Image\AmminaImagick;
use Ammina\Optimizer\Core2\Driver\Image\AmminaJpegOptim;
use Ammina\Optimizer\Core2\Driver\Image\Imagick;
use Ammina\Optimizer\Core2\Driver\Image\JpegOptim;
use Ammina\Optimizer\Core2\Driver\Image\PhpGD;

class Jpg extends Base
{
	/**
	 * @var Imagick
	 */
	protected $oDriverImagick = NULL;
	/**
	 * @var JpegOptim
	 */
	protected $oDriverJpegOptim = NULL;
	/**
	 * @var AmminaImagick
	 */
	protected $oDriverAmminaImagick = NULL;
	/**
	 * @var AmminaJpegOptim
	 */
	protected $oDriverAmminaJpegOptim = NULL;
	/**
	 * @var PhpGD
	 */
	protected $oDriverPhpGD = NULL;

	function __construct()
	{
		$this->oDriverImagick = new Imagick();
		$this->oDriverJpegOptim = new JpegOptim();
		$this->oDriverAmminaImagick = new AmminaImagick();
		$this->oDriverAmminaJpegOptim = new AmminaJpegOptim();
		$this->oDriverPhpGD = new PhpGD();
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
			$strOptimizedFile = "/upload/ammina.optimizer/jpg/q" . intval($this->arOptions['options']['QUALITY']) . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".jpg";
			$strOptimizedFileWait = "/upload/ammina.optimizer/jpg/q" . intval($this->arOptions['options']['QUALITY']) . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".jpg.wait";
			$strOptimizedFileInfo = "/upload/ammina.optimizer/jpg/q" . intval($this->arOptions['options']['QUALITY']) . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".jpg.info";
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
						$strResultFile = $this->oDriverImagick->optimizeImageJpg($strFilePath, $strOptimizedFile, $strOptimizedFileInfo, $this->arOptions['options']['QUALITY'], $this->arOptions['options_variant']['phpimagick']);
					} elseif ($this->arOptions['options']['LIBRARY'] == "jpegoptim") {
						$strResultFile = $this->oDriverJpegOptim->optimizeImageJpg($strFilePath, $strOptimizedFile, $strOptimizedFileInfo, $this->arOptions['options']['QUALITY'], $this->arOptions['options_variant']['phpimagick']);
					} elseif ($this->arOptions['options']['LIBRARY'] == "amminaphpimagick") {
						$strResultFile = $this->oDriverAmminaImagick->optimizeImageJpg($strFilePath, $strOptimizedFile, $strOptimizedFileInfo, $this->arOptions['options']['QUALITY'], $this->arOptions['options_variant']['amminaphpimagick']);
					} elseif ($this->arOptions['options']['LIBRARY'] == "amminajpegoptim") {
						$strResultFile = $this->oDriverAmminaJpegOptim->optimizeImageJpg($strFilePath, $strOptimizedFile, $strOptimizedFileInfo, $this->arOptions['options']['QUALITY'], $this->arOptions['options_variant']['amminaphpimagick']);
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

	public function doConvertAndOptimizeImage($strFilePath, $arConvertOptions = array())
	{
		$strResult = $strFilePath;
		if ($this->arOptions['options']['ACTIVE'] == "Y" && amopt_strpos($strFilePath, '/upload/ammina.optimizer/') !== 0) {
			if (\CAmminaOptimizer::doMathPageToRules($this->arOptions['options']['EXCLUDE_FILES'], $strFilePath) && !\CAmminaOptimizer::doMathPageToRules($this->arOptions['options']['INCLUDE_FILES'], $strFilePath)) {
				return $strResult;
			}
			$arPathInfo = ammina_pathinfo($_SERVER['DOCUMENT_ROOT'] . $strFilePath);
			$arBasePathInfo = ammina_pathinfo($strFilePath);
			$strOptimizedFile = "/upload/ammina.optimizer/jpg/q" . intval($this->arOptions['options']['QUALITY']) . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".jpg";
			$strOptimizedFileWait = "/upload/ammina.optimizer/jpg/q" . intval($this->arOptions['options']['QUALITY']) . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".jpg.wait";
			$strOptimizedFileInfo = "/upload/ammina.optimizer/jpg/q" . intval($this->arOptions['options']['QUALITY']) . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".jpg.info";
			if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFile)) {
				$strConvertFileTmp = "/upload/ammina.optimizer.convert/tojpg/" . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".jpg";
				$strConvertFileTmpWait = "/upload/ammina.optimizer.convert/tojpg/" . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".jpg.wait";
				$strConvertFileTmpInfo = "/upload/ammina.optimizer.convert/tojpg/" . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".jpg.info";
				CheckDirPath(dirname($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFile) . "/");
				CheckDirPath(dirname($_SERVER['DOCUMENT_ROOT'] . $strConvertFileTmp) . "/");
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileWait) && file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileWait) >= (time() - 60)) {
					$strResult = $strFilePath;
				} else {
					\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileWait, time());
					if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strConvertFileTmp)) {
						$strResultFile = $strConvertFileTmp;
					} else {
						$strResultFile = $this->oDriverPhpGD->convertToJpg($strFilePath, $strConvertFileTmp, $strConvertFileTmpInfo, $arConvertOptions);
					}
					if ($strResultFile !== true && file_exists($_SERVER['DOCUMENT_ROOT'] . $strConvertFileTmp)) {
						@unlink($_SERVER['DOCUMENT_ROOT'] . $strConvertFileTmpWait);
						$strResult = $this->doOptimizeImage($strConvertFileTmp);
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