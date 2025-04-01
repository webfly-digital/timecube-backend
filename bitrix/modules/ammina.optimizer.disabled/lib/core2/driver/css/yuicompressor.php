<?

namespace Ammina\Optimizer\Core2\Driver\Css;

class YUICompressor extends Base
{
	public function optimizeCss($strOriginalFilePath, $strResultFilePath, $strTmpResultFilePath, $strResultInfoFilePath, $arOptions = array())
	{
		global $APPLICATION;
		$bResult = false;
		$strCommand = \COption::GetOptionString("ammina.optimizer", "lib_path_yuicompressor", "yuicompressor") . " --type css -o \"" . $_SERVER['DOCUMENT_ROOT'] . $strTmpResultFilePath . "\" \"" . $_SERVER['DOCUMENT_ROOT'] . $strResultFilePath . "\"";
		@exec($strCommand);
		$strContent = "";
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strTmpResultFilePath)) {
			$strContent = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strTmpResultFilePath);
			@unlink($_SERVER['DOCUMENT_ROOT'] . $strTmpResultFilePath);
		}
		if (amopt_strlen(trim($strContent)) > 0) {
			\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strResultFilePath, "\n" . '/* Ammina CSS file original ' . $strOriginalFilePath . ' */' . "\n" . $strContent);
			$arInfo = array(
				"SOURCE" => $strOriginalFilePath,
				"RESULT" => $strResultFilePath,
				"SOURCE_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strOriginalFilePath),
				"RESULT_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strResultFilePath),
			);
			\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strResultInfoFilePath, serialize($arInfo));
			$bResult = true;
		}
		return $bResult;
	}
}