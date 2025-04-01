<?

namespace Ammina\Optimizer\Workers\Cache;

use Ammina\Optimizer\FilesOptimizedTable;
use Ammina\Optimizer\FilesOriginalsTable;
use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Main\Type\DateTime;

class Files
{
	protected $NS = array();
	protected $startTime = false;
	protected $arUpdateData = array();
	protected $arErrors = array();
	protected $arCacheData = array();
	protected $bNextFileFinded = false;
	protected $iMaxFindedFilesPackage = 100;
	protected $arCurrentFindedOptimized = array();
	protected $arCurrentFindedOriginal = array();

	public function __construct($NS, $startTime = false)
	{
		if ($startTime === false) {
			$startTime = time();
		}
		$this->NS = $NS;
		$this->startTime = $startTime;
	}

	public function getErrors()
	{
		return $this->arErrors;
	}

	public function getNSData()
	{
		return $this->NS;
	}

	public function getUpdateData()
	{
		return $this->arUpdateData;
	}

	public function doUpdateProcess($arUpdateData)
	{
		$bResult = true;
		$this->arUpdateData = $arUpdateData;
		if ($this->NS['STEP'] <= 0) {
			return $this->doStep0();
		} elseif ($this->NS['STEP'] == 1) {
			$bResult = $this->doStepFiles($_SERVER['DOCUMENT_ROOT'] . "/");
			$this->flushCurrentFindedOptimized();
			$this->flushCurrentFindedOriginal();
		} elseif ($this->NS['STEP'] == 2) {
			$bResult = $this->doStep2();
		} elseif ($this->NS['STEP'] == 3) {
			$bResult = $this->doStep3();
		} elseif ($this->NS['STEP'] == 4) {
			$bResult = $this->doStep4();
		}
		if ($bResult === true) {
			$this->NS['STEP']++;
			$this->arUpdateData['NEXT_FILE'] = "";
			$bResult = true;
		} elseif ($bResult === "Y") {
			$bResult = true;
		}
		return $bResult;
	}

	protected function doEndTimeInterval()
	{
		if ((time() - $this->startTime) >= $this->NS['INTERVAL']) {
			return true;
		}
		return false;
	}

	protected function doStep0()
	{
		global $DB;
		if (!$DB->Query("TRUNCATE TABLE `" . FilesOriginalsTable::getTableName() . "`;", true)) {
			$this->arErrors[] = Loc::getMessage("AMMINA_OPTIMIZER_ERROR_TABLE_TRUNCATE_FILES_ORIGINALS");
			return false;
		}
		if (!$DB->Query("TRUNCATE TABLE `" . FilesOptimizedTable::getTableName() . "`;", true)) {
			$this->arErrors[] = Loc::getMessage("AMMINA_OPTIMIZER_ERROR_TABLE_TRUNCATE_FILES_OPTIMIZED");
			return false;
		}
		$this->clearFindedData();
		$this->NS['STEP'] = 1;
		return true;
	}

	protected function doStepFiles($strBaseDir)
	{
		$bResult = true;
		if (amopt_strlen($this->arUpdateData['NEXT_FILE']) <= 0) {
			$this->bNextFileFinded = true;
		}
		$arFiles = scandir($strBaseDir);
		asort($arFiles, SORT_ASC | SORT_NATURAL);
		foreach ($arFiles as $strFile) {
			if (in_array($strFile, array(".", ".."))) {
				continue;
			}
			$strFullName = $strBaseDir . $strFile;
			if (amopt_strpos($strFullName, $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules") === 0 || amopt_strpos($strFullName, $_SERVER['DOCUMENT_ROOT'] . "/bitrix/tmp") === 0 || amopt_strpos($strFullName, $_SERVER['DOCUMENT_ROOT'] . "/bitrix/wizard") === 0) {
				continue;
			}
			if (is_dir($strBaseDir . $strFile . "/") && file_exists($strBaseDir . $strFile . "/")) {
				if ($this->arUpdateData['NEXT_FILE'] != "") {
					if ($this->bNextFileFinded || amopt_strpos($this->arUpdateData['NEXT_FILE'], $strBaseDir . $strFile . "/") === 0) {
						$bResult = $this->doStepFiles($strBaseDir . $strFile . "/");
					} else {
						continue;
					}
				} else {
					$bResult = $this->doStepFiles($strBaseDir . $strFile . "/");
				}
			} elseif (is_file($strBaseDir . $strFile)) {
				if ($this->arUpdateData['NEXT_FILE'] != "") {
					if (!$this->bNextFileFinded) {
						if ($this->arUpdateData['NEXT_FILE'] == $strBaseDir . $strFile) {
							$this->bNextFileFinded = true;
						}
						continue;
					} else {
						$this->doMakeFilesStatCache($strBaseDir . $strFile);
						$this->arUpdateData['NEXT_FILE'] = $strBaseDir . $strFile;
					}
				} else {
					$this->doMakeFilesStatCache($strBaseDir . $strFile);
					$this->arUpdateData['NEXT_FILE'] = $strBaseDir . $strFile;
				}
			}
			if ($this->doEndTimeInterval() || $bResult === "Y") {
				return "Y";
			}
		}
		return $bResult;
	}

	protected function doStep2()
	{
		$arFiles = scandir($_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/check.files/original/");
		$this->arUpdateData['STEP2_CNT_PACKAGES'] = count($arFiles) - 2;
		foreach ($arFiles as $strFile) {
			if (in_array($strFile, array(".", ".."))) {
				continue;
			}
			$arData = unserialize(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/check.files/original/" . $strFile));
			foreach ($arData as $arFields) {
				$arFields['CNT_OPTIMIZED'] = 0;
				unset($arFields['FULL_FILE_NAME']);
				$oResult = FilesOriginalsTable::add($arFields);
			}
			@unlink($_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/check.files/original/" . $strFile);
			if ($this->doEndTimeInterval()) {
				$this->arUpdateData['STEP2_CNT_PACKAGES']--;
				return "Y";
			}
		}
		$this->arUpdateData['STEP2_CNT_PACKAGES']--;
		return true;
	}

	protected function doStep3()
	{
		$arFiles = scandir($_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/check.files/optimized/");
		$this->arUpdateData['STEP3_CNT_PACKAGES'] = count($arFiles) - 2;
		foreach ($arFiles as $strFile) {
			if (in_array($strFile, array(".", ".."))) {
				continue;
			}
			$arData = unserialize(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/check.files/optimized/" . $strFile));
			foreach ($arData as $arFields) {
				$strFullFileName = $arFields['FULL_FILE_NAME'];
				$arInfo = $arFields['INFO'];
				unset($arFields['FULL_FILE_NAME']);
				unset($arFields['INFO']);
				if (isset($arInfo['SOURCE']) && amopt_strlen($arInfo['SOURCE']) > 0) {
					$arOriginalFile = FilesOriginalsTable::getList(array(
						"filter" => array("FILE_NAME" => $arInfo['SOURCE'], "CHECK_HASH" => "Y"),
					))->fetch();
					if ($arOriginalFile) {
						$arFields['ORIGINAL_ID'] = $arOriginalFile['ID'];
					} else {
						$arOrigFields = array(
							"TYPE" => "U",
							"FILE_NAME" => $arInfo['SOURCE'],
							"FILE_DATE" => new DateTime(ConvertTimeStamp(filemtime($strFullFileName), "FULL")),
						);
						$oResult = FilesOriginalsTable::add($arOrigFields);
						if ($oResult->isSuccess()) {
							$arFields['ORIGINAL_ID'] = $oResult->getId();
						}
					}
				}
				$oResult = FilesOptimizedTable::add($arFields);
			}
			@unlink($_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/check.files/optimized/" . $strFile);
			if ($this->doEndTimeInterval()) {
				$this->arUpdateData['STEP3_CNT_PACKAGES']--;
				return "Y";
			}
		}
		$this->arUpdateData['STEP3_CNT_PACKAGES']--;
		return true;
	}

	protected function doStep4()
	{
		$arFilter = array();
		if (isset($this->arUpdateData['NEXT_ID'])) {
			$arFilter['>=ID'] = $this->arUpdateData['NEXT_ID'];
		}
		$rOriginals = FilesOriginalsTable::getList(array(
			"order" => array("ID" => "ASC"),
			"filter" => $arFilter,
			"select" => array(
				"ID",
				"CNT_OPTIMIZED",
			),
		));
		while ($arOriginals = $rOriginals->fetch()) {
			$arOptimized = FilesOptimizedTable::getList(array(
				"filter" => array("ORIGINAL_ID" => $arOriginals['ID']),
				"select" => array("CNT"),
				'runtime' => array(
					new ExpressionField('CNT', 'COUNT(*)'),
				),
			))->fetch();
			if ($arOptimized['CNT'] != $arOriginals['CNT_OPTIMIZED']) {
				FilesOriginalsTable::update($arOriginals['ID'], array(
					"CNT_OPTIMIZED" => $arOptimized['CNT'],
				));
			}
			$this->arUpdateData['NEXT_ID'] = $arOriginals['ID'] + 1;
			if ($this->doEndTimeInterval()) {
				return "Y";
			}
		}
		return true;
	}

	protected function doMakeFilesStatCache($strFileName)
	{
		$this->arUpdateData['STAT']['FILES_SCAN']++;
		$arPath = ammina_pathinfo($strFileName);
		$extension = amopt_strtolower($arPath['extension']);
		if (in_array($extension, array("jpg", "png", "jpeg", "webp", "svg", "gif", "css", "js"))) {
			if (amopt_strpos($strFileName, $_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/") === 0 || amopt_strpos($strFileName, $_SERVER['DOCUMENT_ROOT'] . "/upload/ammina.optimizer/") === 0) {
				$strType = "";
				$this->arUpdateData['STAT']['OPTIMIZED']['TOTAL']++;
				if (in_array($extension, array("jpg", "png", "jpeg", "webp", "svg", "gif"))) {
					$strType = "IMAGE";
				} elseif (in_array($extension, array("css"))) {
					$strType = "CSS";
				} elseif (in_array($extension, array("js"))) {
					$strType = "JS";
				}
				$this->arUpdateData['STAT']['OPTIMIZED'][$strType]++;
				$strInfo = $strFileName . ".info";
				$arInfo = array();
				if (file_exists($strInfo)) {
					$arInfo = unserialize(file_get_contents($strInfo));
					if (isset($arInfo['SOURCE']) && amopt_strlen($arInfo['SOURCE']) > 0) {
					}
				}
				$this->arCurrentFindedOptimized[] = array(
					"TYPE" => $strType,
					"FULL_FILE_NAME" => $strFileName,
					"FILE_NAME" => amopt_substr($strFileName, amopt_strlen($_SERVER['DOCUMENT_ROOT'])),
					"FILE_DATE" => new DateTime(ConvertTimeStamp(filemtime($strFileName), "FULL")),
					"FILE_SIZE" => filesize($strFileName),
					"INFO" => $arInfo,
				);
				if (count($this->arCurrentFindedOptimized) >= $this->iMaxFindedFilesPackage) {
					$this->flushCurrentFindedOptimized();
				}
			} else {
				$strType = "";
				$this->arUpdateData['STAT']['ORIGINAL']['TOTAL']++;
				if (in_array($extension, array("jpg", "png", "jpeg", "webp", "svg", "gif"))) {
					$strType = "IMAGE";
				} elseif (in_array($extension, array("css"))) {
					$strType = "CSS";
				} elseif (in_array($extension, array("js"))) {
					$strType = "JS";
				}
				$this->arUpdateData['STAT']['ORIGINAL'][$strType]++;
				$this->arCurrentFindedOriginal[] = array(
					"TYPE" => $strType,
					"FULL_FILE_NAME" => $strFileName,
					"FILE_NAME" => amopt_substr($strFileName, amopt_strlen($_SERVER['DOCUMENT_ROOT'])),
					"FILE_EXTENSION" => $extension,
					"FILE_DATE" => new DateTime(ConvertTimeStamp(filemtime($strFileName), "FULL")),
					"FILE_SIZE" => filesize($strFileName),
				);
				if (count($this->arCurrentFindedOriginal) >= $this->iMaxFindedFilesPackage) {
					$this->flushCurrentFindedOriginal();
				}
			}
		}
	}

	/*
		protected function doCheckFileStat($strFileName)
		{
			$this->arUpdateData['STAT']['FILES_SCAN']++;
			$arPath = pathinfo($strFileName);
			$extension = amopt_strtolower($arPath['extension']);
			if (in_array($extension, array("jpg", "png", "jpeg", "webp", "svg", "gif", "css", "js"))) {
				if (amopt_strpos($strFileName, $_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/") === 0 || amopt_strpos($strFileName, $_SERVER['DOCUMENT_ROOT'] . "/upload/ammina.optimizer/") === 0) {
					$strType = "";
					$this->arUpdateData['STAT']['OPTIMIZED']['TOTAL']++;
					if (in_array($extension, array("jpg", "png", "jpeg", "webp", "svg", "gif"))) {
						$strType = "IMAGE";
					} elseif (in_array($extension, array("css"))) {
						$strType = "CSS";
					} elseif (in_array($extension, array("js"))) {
						$strType = "JS";
					}
					$this->arUpdateData['STAT']['OPTIMIZED'][$strType]++;
					$strInfo = $strFileName . ".info";
					$arFields = array(
						"FILE_NAME" => amopt_substr($strFileName, amopt_strlen($_SERVER['DOCUMENT_ROOT'])),
						"FILE_DATE" => new DateTime(ConvertTimeStamp(filemtime($strFileName), "FULL")),
						"FILE_SIZE" => filesize($strFileName),
					);
					if (file_exists($strInfo)) {
						$arInfo = unserialize(file_get_contents($strInfo));
						if (isset($arInfo['SOURCE']) && amopt_strlen($arInfo['SOURCE']) > 0) {
							$arOriginalFile = FilesOriginalsTable::getList(array(
								"filter" => array("FILE_NAME" => $arInfo['SOURCE'], "CHECK_HASH" => "Y"),
							))->fetch();
							if ($arOriginalFile) {
								$arFields['ORIGINAL_ID'] = $arOriginalFile['ID'];
							} else {
								$arOrigFields = array(
									"TYPE" => "U",
									"FILE_NAME" => $arInfo['SOURCE'],
									"FILE_DATE" => new DateTime(ConvertTimeStamp(filemtime($strFileName), "FULL")),
								);
								$oResult = FilesOriginalsTable::add($arOrigFields);
								if ($oResult->isSuccess()) {
									$arFields['ORIGINAL_ID'] = $oResult->getId();
								}
							}
						}
					}
					$arFile = FilesOptimizedTable::getList(array(
						"filter" => array("FILE_NAME" => $arFields['FILE_NAME']),
					))->fetch();
					if ($arFile) {
						$oResult = FilesOptimizedTable::update($arFile['ID'], $arFields);
					} else {
						$oResult = FilesOptimizedTable::add($arFields);
					}
				} else {
					$strType = "";
					$this->arUpdateData['STAT']['ORIGINAL']['TOTAL']++;
					if (in_array($extension, array("jpg", "png", "jpeg", "webp", "svg", "gif"))) {
						$strType = "IMAGE";
					} elseif (in_array($extension, array("css"))) {
						$strType = "CSS";
					} elseif (in_array($extension, array("js"))) {
						$strType = "JS";
					}
					$this->arUpdateData['STAT']['ORIGINAL'][$strType]++;
					$arFields = array(
						"TYPE" => $strType,
						"FILE_NAME" => amopt_substr($strFileName, amopt_strlen($_SERVER['DOCUMENT_ROOT'])),
						"FILE_EXTENSION" => $extension,
						"FILE_DATE" => new DateTime(ConvertTimeStamp(filemtime($strFileName), "FULL")),
						"FILE_SIZE" => filesize($strFileName),
					);
					$arFile = FilesOriginalsTable::getList(array(
						"filter" => array("FILE_NAME" => $arFields['FILE_NAME'], "CHECK_HASH" => "Y"),
					))->fetch();
					if ($arFile) {
						$oResult = FilesOriginalsTable::update($arFile['ID'], $arFields);
					} else {
						$arFields['CNT_OPTIMIZED'] = 0;
						$oResult = FilesOriginalsTable::add($arFields);
					}
				}
			}
		}
	*/
	protected function flushCurrentFindedOptimized()
	{
		if (count($this->arCurrentFindedOptimized) > 0) {
			$strName = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/check.files/optimized/" . microtime(true) . "." . randString(10) . ".dat";
			CheckDirPath(dirname($strName) . "/");
			\CAmminaOptimizer::SaveFileContent($strName, serialize($this->arCurrentFindedOptimized));
			$this->arCurrentFindedOptimized = array();
		}
	}

	protected function flushCurrentFindedOriginal()
	{
		if (count($this->arCurrentFindedOriginal) > 0) {
			$strName = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/check.files/original/" . microtime(true) . "." . randString(10) . ".dat";
			CheckDirPath(dirname($strName) . "/");
			\CAmminaOptimizer::SaveFileContent($strName, serialize($this->arCurrentFindedOriginal));
			$this->arCurrentFindedOriginal = array();
		}
	}

	protected function clearFindedData()
	{
		$arFiles = scandir($_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/check.files/original/");
		foreach ($arFiles as $strFile) {
			if (in_array($strFile, array(".", ".."))) {
				continue;
			}
			@unlink($_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/check.files/original/" . $strFile);
		}
		$arFiles = scandir($_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/check.files/optimized/");
		foreach ($arFiles as $strFile) {
			if (in_array($strFile, array(".", ".."))) {
				continue;
			}
			@unlink($_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/check.files/optimized/" . $strFile);
		}
	}
}
