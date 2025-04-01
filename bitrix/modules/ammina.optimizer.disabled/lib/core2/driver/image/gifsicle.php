<?

namespace Ammina\Optimizer\Core2\Driver\Image;

class GifSicle extends Base
{
	public function optimizeImageGif($strSourceFilePath, $strResultFilePath, $strResultInfoFilePath, $iQuality, $arOptions = array())
	{
		CheckDirPath(dirname($_SERVER['DOCUMENT_ROOT'] . $strResultFilePath) . "/");
		$arPath = ammina_pathinfo($_SERVER['DOCUMENT_ROOT'] . $strSourceFilePath);
		if (in_array(amopt_strtolower($arPath['extension']), array("gif"))) {
			$strCommand = \COption::GetOptionString("ammina.optimizer", "lib_path_gifsicle", "gifsicle") . ' --optimize=' . $iQuality . ' --output "' . $_SERVER['DOCUMENT_ROOT'] . $strResultFilePath . '" "' . $_SERVER['DOCUMENT_ROOT'] . $strSourceFilePath . '"';
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
		}
		return false;
	}
}