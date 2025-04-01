<?

namespace Ammina\Optimizer\Core2\Driver\Css;

class MatthiasMullie extends Base
{
	public function optimizeCss($strOriginalFilePath, $strResultFilePath, $strTmpResultFilePath, $strResultInfoFilePath, $arOptions = array())
	{
		global $APPLICATION;
		$bResult = false;
		$oMin = new \MatthiasMullie\Minify\CSS();
		$oMin->add(file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strResultFilePath));
		$strContent = $oMin->minify();
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
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strTmpResultFilePath)) {
				@unlink($_SERVER['DOCUMENT_ROOT'] . $strTmpResultFilePath);
			}
		}
		return $bResult;
	}
}