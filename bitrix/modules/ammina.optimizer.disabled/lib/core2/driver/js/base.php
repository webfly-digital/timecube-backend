<?

namespace Ammina\Optimizer\Core2\Driver\Js;

class Base
{
	public function optimizeJs($strOriginalFilePath, $strResultFilePath, $strTmpResultFilePath, $strResultInfoFilePath, $arOptions = array(), $bDoubleConvertEncoding = false)
	{
		return false;
	}

	protected function doNormalizeEncodinig($strFileName, $strEncodingFrom)
	{
		global $APPLICATION;
		$strResultFileName = amopt_substr($strFileName, 0, amopt_strlen($strFileName) - 3) . ".encoded.js";
		$strContent = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strFileName);
		$strContent = $APPLICATION->ConvertCharset($strContent, $strEncodingFrom, "utf-8");
		\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strResultFileName, $strContent);
		return $strResultFileName;
	}
}