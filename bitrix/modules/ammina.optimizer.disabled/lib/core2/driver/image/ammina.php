<?

namespace Ammina\Optimizer\Core2\Driver\Image;

use Ammina\Optimizer\Core2\Application;

class Ammina extends Base
{
	protected $driverName = "";

	function sendRequest($arRequest, $strResultFilePath, $strFileNameResultUrl)
	{
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strFileNameResultUrl)) {
			$bEndTime = false;
			$fmtime = filemtime($_SERVER['DOCUMENT_ROOT'] . $strFileNameResultUrl);
			if ($fmtime < (time() - 600)) {
				$bEndTime = true;
			}
			$strResultUrl = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strFileNameResultUrl);
			if ($strResultUrl != "wait") {
				$bResult = \CAmminaOptimizer::doRequestWorkServerResultFile($strResultUrl, $strResultFilePath, $strFileNameResultUrl);
				if ($bResult) {
					$this->doMakeResultInfo($arRequest['FUNCTION_PARAMETERS']['SOURCE_FILE_PATH'], $arRequest['FUNCTION_PARAMETERS']['RESULT_FILE_PATH'], $arRequest['FUNCTION_PARAMETERS']['RESULT_INFO_FILE_PATH']);
					return $strResultFilePath;
				} elseif (!$bEndTime) {
					return false;
				} else {
					@unlink($_SERVER['DOCUMENT_ROOT'] . $strFileNameResultUrl);
				}
			}
		}

		$arRequest['LIBTYPE'] = "image";
		return Application::getInstance()->doPushStackRequest($arRequest, "optimize", $strResultFilePath, $strFileNameResultUrl);
	}

	public function optimizeImageJpg($strSourceFilePath, $strResultFilePath, $strResultInfoFilePath, $iQuality, $arOptions = array())
	{
		$arRequest = array(
			"DRIVER" => $this->driverName,
			"GET_FILE" => $strSourceFilePath,
			"RESULT_FILE" => $strResultFilePath,
			"FUNCTION" => "optimizeImageJpg",
			"FUNCTION_PARAMETERS" => array(
				"SOURCE_FILE_PATH" => $strSourceFilePath,
				"RESULT_FILE_PATH" => $strResultFilePath,
				"RESULT_INFO_FILE_PATH" => $strResultInfoFilePath,
				"IQUALITY" => $iQuality,
				"OPTIONS" => $arOptions,
			),
		);
		return $this->sendRequest($arRequest, $strResultFilePath, $strResultFilePath . ".resulturl");
	}

	public function optimizeImagePng($strSourceFilePath, $strResultFilePath, $strResultInfoFilePath, $iQuality, $arOptions = array())
	{
		$arRequest = array(
			"DRIVER" => $this->driverName,
			"GET_FILE" => $strSourceFilePath,
			"RESULT_FILE" => $strResultFilePath,
			"FUNCTION" => "optimizeImagePng",
			"FUNCTION_PARAMETERS" => array(
				"SOURCE_FILE_PATH" => $strSourceFilePath,
				"RESULT_FILE_PATH" => $strResultFilePath,
				"RESULT_INFO_FILE_PATH" => $strResultInfoFilePath,
				"IQUALITY" => $iQuality,
				"OPTIONS" => $arOptions,
			),
		);
		return $this->sendRequest($arRequest, $strResultFilePath, $strResultFilePath . ".resulturl");
	}

	public function optimizeImageGif($strSourceFilePath, $strResultFilePath, $strResultInfoFilePath, $iQuality, $arOptions = array())
	{
		$arRequest = array(
			"DRIVER" => $this->driverName,
			"GET_FILE" => $strSourceFilePath,
			"RESULT_FILE" => $strResultFilePath,
			"FUNCTION" => "optimizeImageGif",
			"FUNCTION_PARAMETERS" => array(
				"SOURCE_FILE_PATH" => $strSourceFilePath,
				"RESULT_FILE_PATH" => $strResultFilePath,
				"RESULT_INFO_FILE_PATH" => $strResultInfoFilePath,
				"IQUALITY" => $iQuality,
				"OPTIONS" => $arOptions,
			),
		);
		return $this->sendRequest($arRequest, $strResultFilePath, $strResultFilePath . ".resulturl");
	}

	public function optimizeImageSvg($strSourceFilePath, $strResultFilePath, $strResultInfoFilePath, $arOptions = array())
	{
		$arRequest = array(
			"DRIVER" => $this->driverName,
			"GET_FILE" => $strSourceFilePath,
			"RESULT_FILE" => $strResultFilePath,
			"FUNCTION" => "optimizeImageSvg",
			"FUNCTION_PARAMETERS" => array(
				"SOURCE_FILE_PATH" => $strSourceFilePath,
				"RESULT_FILE_PATH" => $strResultFilePath,
				"RESULT_INFO_FILE_PATH" => $strResultInfoFilePath,
				"OPTIONS" => $arOptions,
			),
		);
		return $this->sendRequest($arRequest, $strResultFilePath, $strResultFilePath . ".resulturl");
	}

	public function optimizeImageWebP($strSourceFilePath, $strResultFilePath, $strResultInfoFilePath, $iQuality, $arOptions = array())
	{
		$arRequest = array(
			"DRIVER" => $this->driverName,
			"GET_FILE" => $strSourceFilePath,
			"RESULT_FILE" => $strResultFilePath,
			"FUNCTION" => "optimizeImageWebP",
			"FUNCTION_PARAMETERS" => array(
				"SOURCE_FILE_PATH" => $strSourceFilePath,
				"RESULT_FILE_PATH" => $strResultFilePath,
				"RESULT_INFO_FILE_PATH" => $strResultInfoFilePath,
				"IQUALITY" => $iQuality,
				"OPTIONS" => $arOptions,
			),
		);
		return $this->sendRequest($arRequest, $strResultFilePath, $strResultFilePath . ".resulturl");
	}

	public function doMakeResultInfo($strSourceFileName, $strResultFileName, $strOptimizedFileInfo)
	{
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileInfo)) {
			$arInfo = array(
				"SOURCE" => $strSourceFileName,
				"RESULT" => $strResultFileName,
				"SOURCE_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strSourceFileName),
				"RESULT_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strResultFileName),
			);
			\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strOptimizedFileInfo, serialize($arInfo));
		}
	}
}