<?

namespace Ammina\Optimizer\Core2\Optimizer\Image;

use Ammina\Optimizer\Core2\Application;
use Ammina\Optimizer\Core2\Driver\Image\AmminaSvgo;
use Ammina\Optimizer\Core2\Driver\Image\Svgo;

class Svg extends Base
{
	/**
	 * @var Svgo
	 */
	protected $oDriverSvgo = NULL;
	/**
	 * @var AmminaSvgo
	 */
	protected $oDriverAmminaSvgo = NULL;

	function __construct()
	{
		$this->oDriverSvgo = new Svgo();
		$this->oDriverAmminaSvgo = new AmminaSvgo();
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
			$strOptimizedFile = "/upload/ammina.optimizer/svg" . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".svg";
			$strOptimizedFileWait = "/upload/ammina.optimizer/svg" . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".svg.wait";
			$strOptimizedFileInfo = "/upload/ammina.optimizer/svg" . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".svg.info";
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
					if ($this->arOptions['options']['LIBRARY'] == "svgo") {
						$strResultFile = $this->oDriverSvgo->optimizeImageSvg($strFilePath, $strOptimizedFile, $strOptimizedFileInfo, $this->arOptions['options_variant']['svgo']);
					} elseif ($this->arOptions['options']['LIBRARY'] == "amminasvgo") {
						$strResultFile = $this->oDriverAmminaSvgo->optimizeImageSvg($strFilePath, $strOptimizedFile, $strOptimizedFileInfo, $this->arOptions['options_variant']['amminasvgo']);
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