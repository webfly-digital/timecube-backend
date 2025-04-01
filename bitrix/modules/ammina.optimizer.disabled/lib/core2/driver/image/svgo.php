<?

namespace Ammina\Optimizer\Core2\Driver\Image;

class Svgo extends Base
{
	public function optimizeImageSvg($strSourceFilePath, $strResultFilePath, $strResultInfoFilePath, $arOptions = array())
	{
		CheckDirPath(dirname($_SERVER['DOCUMENT_ROOT'] . $strResultFilePath) . "/");
		$strCommand = \COption::GetOptionString("ammina.optimizer", "lib_path_svgo", "svgo") . ' --disable=removeViewBox -o "' . $_SERVER['DOCUMENT_ROOT'] . $strResultFilePath . '" -i "' . $_SERVER['DOCUMENT_ROOT'] . $strSourceFilePath . '"';
		@exec($strCommand);
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strResultFilePath)) {
			$arInfo = array(
				"SOURCE" => $strSourceFilePath,
				"RESULT" => $strResultFilePath,
				"SOURCE_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strSourceFilePath),
				"RESULT_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strResultFilePath),
			);
			\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strResultInfoFilePath, serialize($arInfo));
			return $strResultFilePath;
		}
		return false;
	}
}