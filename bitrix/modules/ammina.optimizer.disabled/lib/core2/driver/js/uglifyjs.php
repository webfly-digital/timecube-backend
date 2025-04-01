<?

namespace Ammina\Optimizer\Core2\Driver\Js;

class UglifyJs extends Base
{
	public function optimizeJs($strOriginalFilePath, $strResultFilePath, $strTmpResultFilePath, $strResultInfoFilePath, $arOptions = array(), $bDoubleConvertEncoding = false)
	{
		global $APPLICATION;
		$bResult = false;
		$strResultFilePathEncoded = $strResultFilePath;
		if ($bDoubleConvertEncoding !== false) {
			$strResultFilePathEncoded = $this->doNormalizeEncodinig($strResultFilePath, $bDoubleConvertEncoding);
		}
		$strCommand = \COption::GetOptionString("ammina.optimizer", "lib_path_uglifyjs", "uglifyjs") . " -m -o \"" . $_SERVER['DOCUMENT_ROOT'] . $strTmpResultFilePath . "\" \"" . $_SERVER['DOCUMENT_ROOT'] . $strResultFilePathEncoded . "\"";
		@exec($strCommand);
		$strContent = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strTmpResultFilePath);
		if ($bDoubleConvertEncoding !== false) {
			$strContent = $APPLICATION->ConvertCharset($strContent, "UTF-8", $bDoubleConvertEncoding);
			@unlink($_SERVER['DOCUMENT_ROOT'] . $strResultFilePathEncoded);
		}
		if (amopt_strlen(trim($strContent)) > 0) {
			\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strResultFilePath, "\n" . '/* Ammina JS file original ' . $strOriginalFilePath . ' */' . "\n" . $strContent);
			$arInfo = array(
				"SOURCE" => $strOriginalFilePath,
				"RESULT" => $strResultFilePath,
				"SOURCE_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strOriginalFilePath),
				"RESULT_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strResultFilePath),
			);
			\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strResultInfoFilePath, serialize($arInfo));
			$bResult = true;
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strTmpResultFilePath)) {
				@unlink($_SERVER['DOCUMENT_ROOT'] . $strTmpResultFilePath);
			}
		}
		return $bResult;
	}

}