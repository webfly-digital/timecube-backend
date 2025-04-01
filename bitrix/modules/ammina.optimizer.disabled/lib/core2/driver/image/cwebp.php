<?

namespace Ammina\Optimizer\Core2\Driver\Image;

class CWebP extends Base
{
	public function optimizeImageWebP($strSourceFilePath, $strResultFilePath, $strResultInfoFilePath, $iQuality, $arOptions = array())
	{
		CheckDirPath(dirname($_SERVER['DOCUMENT_ROOT'] . $strResultFilePath) . "/");
		$arPath = ammina_pathinfo($_SERVER['DOCUMENT_ROOT'] . $strSourceFilePath);
		if (in_array(amopt_strtolower($arPath['extension']), array("jpg", "jpeg", "png"))) {
			$strCommand = \COption::GetOptionString("ammina.optimizer", "lib_path_cwebp", "cwebp") . ' -q ' . $iQuality . ' "' . $_SERVER['DOCUMENT_ROOT'] . $strSourceFilePath . '" -o "' . $_SERVER['DOCUMENT_ROOT'] . $strResultFilePath . '"';
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
		} elseif (in_array(amopt_strtolower($arPath['extension']), array("gif"))) {
			$strCommand = \COption::GetOptionString("ammina.optimizer", "lib_path_gif2webp", "gif2webp") . ' -q ' . $iQuality . ' "' . $_SERVER['DOCUMENT_ROOT'] . $strSourceFilePath . '" -o "' . $_SERVER['DOCUMENT_ROOT'] . $strResultFilePath . '"';
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