<?

namespace Ammina\Optimizer\Workers\Cache;


use Ammina\Optimizer\StatTypesTable;

class Stat
{
	protected $NS = array();
	protected $startTime = false;
	protected $arUpdateData = array();
	protected $arErrors = array();
	protected $arCacheData = array();
	protected $bNextFileFinded = false;

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
			$bResult = $this->doStepFiles($_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/css/");
		} elseif ($this->NS['STEP'] == 2) {
			$bResult = $this->doStepFiles($_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/img/");
		} elseif ($this->NS['STEP'] == 3) {
			$bResult = $this->doStepFiles($_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/js/");
		} elseif ($this->NS['STEP'] == 4) {
			$bResult = $this->doStepFiles($_SERVER['DOCUMENT_ROOT'] . "/upload/ammina.optimizer/gif/");
		} elseif ($this->NS['STEP'] == 5) {
			$bResult = $this->doStepFiles($_SERVER['DOCUMENT_ROOT'] . "/upload/ammina.optimizer/gif-webp/");
		} elseif ($this->NS['STEP'] == 6) {
			$bResult = $this->doStepFiles($_SERVER['DOCUMENT_ROOT'] . "/upload/ammina.optimizer/jpeg-webp/");
		} elseif ($this->NS['STEP'] == 7) {
			$bResult = $this->doStepFiles($_SERVER['DOCUMENT_ROOT'] . "/upload/ammina.optimizer/jpg/");
		} elseif ($this->NS['STEP'] == 8) {
			$bResult = $this->doStepFiles($_SERVER['DOCUMENT_ROOT'] . "/upload/ammina.optimizer/jpg-webp/");
		} elseif ($this->NS['STEP'] == 9) {
			$bResult = $this->doStepFiles($_SERVER['DOCUMENT_ROOT'] . "/upload/ammina.optimizer/png/");
		} elseif ($this->NS['STEP'] == 10) {
			$bResult = $this->doStepFiles($_SERVER['DOCUMENT_ROOT'] . "/upload/ammina.optimizer/png-webp/");
		} elseif ($this->NS['STEP'] == 11) {
			$bResult = $this->doStepFiles($_SERVER['DOCUMENT_ROOT'] . "/upload/ammina.optimizer/svg/");
		} elseif ($this->NS['STEP'] == 12) {
			$bResult = $this->doStep12();
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

	/**
	 * Шаг 0: Очистка таблицы статистики
	 */
	protected function doStep0()
	{
		global $DB;
		if (!$DB->Query("TRUNCATE TABLE `" . StatTypesTable::getTableName() . "`;", true)) {
			$this->arErrors[] = Loc::getMessage("AMMINA_OPTIMIZER_ERROR_TABLE_TRUNCATE_STAT_TYPES");
			return false;
		}
		$this->NS['STEP'] = 1;
		return true;
	}

	/**
	 * Загрузка файлов
	 *
	 */
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
						$this->doCheckFileStat($strBaseDir . $strFile);
						$this->arUpdateData['NEXT_FILE'] = $strBaseDir . $strFile;
					}
				} else {
					$this->doCheckFileStat($strBaseDir . $strFile);
					$this->arUpdateData['NEXT_FILE'] = $strBaseDir . $strFile;
				}
			}
			if ($this->doEndTimeInterval() || $bResult === "Y") {
				return "Y";
			}
		}
		return $bResult;
	}

	protected function doCheckFileStat($strFileName)
	{
		$iFileSize = filesize($strFileName);
		$this->arUpdateData['STAT']['TOTAL']['COUNT']++;
		$this->arUpdateData['STAT']['TOTAL']['SIZE'] += $iFileSize;
		$arPathInfo = ammina_pathinfo($strFileName);
		$arPathInfo['extension'] = amopt_strtolower($arPathInfo['extension']);
		if (amopt_strpos($strFileName, $_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/css/") === 0) {
			$this->arUpdateData['STAT']['TOTAL_CSS']['COUNT']++;
			$this->arUpdateData['STAT']['TOTAL_CSS']['SIZE'] += $iFileSize;
			if (amopt_strpos($strFileName, "/atom/") !== false) {
				$this->arUpdateData['STAT']['TOTAL_CSS_ATOM']['COUNT']++;
				$this->arUpdateData['STAT']['TOTAL_CSS_ATOM']['SIZE'] += $iFileSize;
				if ($arPathInfo['extension'] == "css") {
					$this->arUpdateData['STAT']['TOTAL_CSS_ATOM_CSS']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_CSS_ATOM_CSS']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "info") {
					$this->arUpdateData['STAT']['TOTAL_CSS_ATOM_INFO']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_CSS_ATOM_INFO']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "rules") {
					$this->arUpdateData['STAT']['TOTAL_CSS_ATOM_RULES']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_CSS_ATOM_RULES']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "wait") {
					$this->arUpdateData['STAT']['TOTAL_CSS_ATOM_WAIT']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_CSS_ATOM_WAIT']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "resulturl") {
					$this->arUpdateData['STAT']['TOTAL_CSS_ATOM_RESULTURL']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_CSS_ATOM_RESULTURL']['SIZE'] += $iFileSize;
				}
			} elseif (amopt_strpos($strFileName, "/full/") !== false) {
				$this->arUpdateData['STAT']['TOTAL_CSS_FULL']['COUNT']++;
				$this->arUpdateData['STAT']['TOTAL_CSS_FULL']['SIZE'] += $iFileSize;
				if (amopt_strpos($strFileName, "/critical/") !== false) {
					$this->arUpdateData['STAT']['TOTAL_CSS_FULL_CRITICAL']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_CSS_FULL_CRITICAL']['SIZE'] += $iFileSize;
					if ($arPathInfo['extension'] == "fonts") {
						$this->arUpdateData['STAT']['TOTAL_CSS_FULL_CRITICAL_FONTS']['COUNT']++;
						$this->arUpdateData['STAT']['TOTAL_CSS_FULL_CRITICAL_FONTS']['SIZE'] += $iFileSize;
					} elseif ($arPathInfo['extension'] == "css") {
						if (amopt_strpos($arPathInfo['filename'], '.nc') !== false) {
							$this->arUpdateData['STAT']['TOTAL_CSS_FULL_CRITICAL_NC']['COUNT']++;
							$this->arUpdateData['STAT']['TOTAL_CSS_FULL_CRITICAL_NC']['SIZE'] += $iFileSize;
						} else {
							$this->arUpdateData['STAT']['TOTAL_CSS_FULL_CRITICAL_CSS']['COUNT']++;
							$this->arUpdateData['STAT']['TOTAL_CSS_FULL_CRITICAL_CSS']['SIZE'] += $iFileSize;
						}
					}
				} elseif (amopt_strpos($strFileName, "/font/") !== false) {
					$this->arUpdateData['STAT']['TOTAL_CSS_FULL_FONTS']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_CSS_FULL_FONTS']['SIZE'] += $iFileSize;
					if ($arPathInfo['extension'] == "css") {
						if (amopt_strpos($arPathInfo['filename'], '.nf') !== false) {
							$this->arUpdateData['STAT']['TOTAL_CSS_FULL_FONTS_NF']['COUNT']++;
							$this->arUpdateData['STAT']['TOTAL_CSS_FULL_FONTS_NF']['SIZE'] += $iFileSize;
						} else {
							$this->arUpdateData['STAT']['TOTAL_CSS_FULL_FONTS_CSS']['COUNT']++;
							$this->arUpdateData['STAT']['TOTAL_CSS_FULL_FONTS_CSS']['SIZE'] += $iFileSize;
						}
					}
				} else {
					if ($arPathInfo['extension'] == "css") {
						$this->arUpdateData['STAT']['TOTAL_CSS_FULL_CSS']['COUNT']++;
						$this->arUpdateData['STAT']['TOTAL_CSS_FULL_CSS']['SIZE'] += $iFileSize;
					} elseif ($arPathInfo['extension'] == "php" && amopt_strpos($arPathInfo['filename'], '.rules') !== false) {
						$this->arUpdateData['STAT']['TOTAL_CSS_FULL_RULES']['COUNT']++;
						$this->arUpdateData['STAT']['TOTAL_CSS_FULL_RULES']['SIZE'] += $iFileSize;
					}
				}
			}
		} elseif (amopt_strpos($strFileName, $_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/img/") === 0) {
			$this->arUpdateData['STAT']['TOTAL_CSS_IMG']['COUNT']++;
			$this->arUpdateData['STAT']['TOTAL_CSS_IMG']['SIZE'] += $iFileSize;
		} elseif (amopt_strpos($strFileName, $_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/js/") === 0) {
			$this->arUpdateData['STAT']['TOTAL_JS']['COUNT']++;
			$this->arUpdateData['STAT']['TOTAL_JS']['SIZE'] += $iFileSize;
			if (amopt_strpos($strFileName, "/atom/") !== false) {
				$this->arUpdateData['STAT']['TOTAL_JS_ATOM']['COUNT']++;
				$this->arUpdateData['STAT']['TOTAL_JS_ATOM']['SIZE'] += $iFileSize;
				if ($arPathInfo['extension'] == "js") {
					$this->arUpdateData['STAT']['TOTAL_JS_ATOM_JS']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_JS_ATOM_JS']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "info") {
					$this->arUpdateData['STAT']['TOTAL_JS_ATOM_INFO']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_JS_ATOM_INFO']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "wait") {
					$this->arUpdateData['STAT']['TOTAL_JS_ATOM_WAIT']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_JS_ATOM_WAIT']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "resulturl") {
					$this->arUpdateData['STAT']['TOTAL_JS_ATOM_RESULTURL']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_JS_ATOM_RESULTURL']['SIZE'] += $iFileSize;
				}
			} elseif (amopt_strpos($strFileName, "/full/") !== false) {
				$this->arUpdateData['STAT']['TOTAL_JS_FULL']['COUNT']++;
				$this->arUpdateData['STAT']['TOTAL_JS_FULL']['SIZE'] += $iFileSize;
				if ($arPathInfo['extension'] == "js") {
					$this->arUpdateData['STAT']['TOTAL_JS_FULL_JS']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_JS_FULL_JS']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "info") {
					$this->arUpdateData['STAT']['TOTAL_JS_FULL_INFO']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_JS_FULL_INFO']['SIZE'] += $iFileSize;
				}
			}
		} elseif (amopt_strpos($strFileName, $_SERVER['DOCUMENT_ROOT'] . "/upload/ammina.optimizer/") === 0) {
			$this->arUpdateData['STAT']['TOTAL_IMAGES']['COUNT']++;
			$this->arUpdateData['STAT']['TOTAL_IMAGES']['SIZE'] += $iFileSize;
			if (amopt_strpos($strFileName, $_SERVER['DOCUMENT_ROOT'] . "/upload/ammina.optimizer/gif/") === 0) {
				$this->arUpdateData['STAT']['TOTAL_IMAGES_GIF']['COUNT']++;
				$this->arUpdateData['STAT']['TOTAL_IMAGES_GIF']['SIZE'] += $iFileSize;
				if ($arPathInfo['extension'] == "gif") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_GIF_IMAGES']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_GIF_IMAGES']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "info") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_GIF_INFO']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_GIF_INFO']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "wait") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_GIF_WAIT']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_GIF_WAIT']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "resulturl") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_GIF_RESULTURL']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_GIF_RESULTURL']['SIZE'] += $iFileSize;
				}
			} elseif (amopt_strpos($strFileName, $_SERVER['DOCUMENT_ROOT'] . "/upload/ammina.optimizer/gif-webp/") === 0) {
				$this->arUpdateData['STAT']['TOTAL_IMAGES_GIFWEBP']['COUNT']++;
				$this->arUpdateData['STAT']['TOTAL_IMAGES_GIFWEBP']['SIZE'] += $iFileSize;
				if ($arPathInfo['extension'] == "webp") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_GIFWEBP_IMAGES']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_GIFWEBP_IMAGES']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "info") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_GIFWEBP_INFO']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_GIFWEBP_INFO']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "wait") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_GIFWEBP_WAIT']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_GIFWEBP_WAIT']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "resulturl") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_GIFWEBP_RESULTURL']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_GIFWEBP_RESULTURL']['SIZE'] += $iFileSize;
				}
			} elseif (amopt_strpos($strFileName, $_SERVER['DOCUMENT_ROOT'] . "/upload/ammina.optimizer/jpeg-webp/") === 0) {
				$this->arUpdateData['STAT']['TOTAL_IMAGES_JPEGWEBP']['COUNT']++;
				$this->arUpdateData['STAT']['TOTAL_IMAGES_JPEGWEBP']['SIZE'] += $iFileSize;
				if ($arPathInfo['extension'] == "webp") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_JPEGWEBP_IMAGES']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_JPEGWEBP_IMAGES']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "info") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_JPEGWEBP_INFO']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_JPEGWEBP_INFO']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "wait") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_JPEGWEBP_WAIT']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_JPEGWEBP_WAIT']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "resulturl") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_JPEGWEBP_RESULTURL']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_JPEGWEBP_RESULTURL']['SIZE'] += $iFileSize;
				}
			} elseif (amopt_strpos($strFileName, $_SERVER['DOCUMENT_ROOT'] . "/upload/ammina.optimizer/jpg/") === 0) {
				$this->arUpdateData['STAT']['TOTAL_IMAGES_JPG']['COUNT']++;
				$this->arUpdateData['STAT']['TOTAL_IMAGES_JPG']['SIZE'] += $iFileSize;
				if ($arPathInfo['extension'] == "jpg" || $arPathInfo['extension'] == "jpeg") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_JPG_IMAGES']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_JPG_IMAGES']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "info") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_JPG_INFO']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_JPG_INFO']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "wait") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_JPG_WAIT']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_JPG_WAIT']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "resulturl") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_JPG_RESULTURL']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_JPG_RESULTURL']['SIZE'] += $iFileSize;
				}
			} elseif (amopt_strpos($strFileName, $_SERVER['DOCUMENT_ROOT'] . "/upload/ammina.optimizer/jpg-webp/") === 0) {
				$this->arUpdateData['STAT']['TOTAL_IMAGES_JPGWEBP']['COUNT']++;
				$this->arUpdateData['STAT']['TOTAL_IMAGES_JPGWEBP']['SIZE'] += $iFileSize;
				if ($arPathInfo['extension'] == "webp") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_JPGWEBP_IMAGES']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_JPGWEBP_IMAGES']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "info") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_JPGWEBP_INFO']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_JPGWEBP_INFO']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "wait") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_JPGWEBP_WAIT']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_JPGWEBP_WAIT']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "resulturl") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_JPGWEBP_RESULTURL']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_JPGWEBP_RESULTURL']['SIZE'] += $iFileSize;
				}
			} elseif (amopt_strpos($strFileName, $_SERVER['DOCUMENT_ROOT'] . "/upload/ammina.optimizer/png/") === 0) {
				$this->arUpdateData['STAT']['TOTAL_IMAGES_PNG']['COUNT']++;
				$this->arUpdateData['STAT']['TOTAL_IMAGES_PNG']['SIZE'] += $iFileSize;
				if ($arPathInfo['extension'] == "png") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_PNG_IMAGES']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_PNG_IMAGES']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "info") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_PNG_INFO']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_PNG_INFO']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "wait") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_PNG_WAIT']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_PNG_WAIT']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "resulturl") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_PNG_RESULTURL']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_PNG_RESULTURL']['SIZE'] += $iFileSize;
				}
			} elseif (amopt_strpos($strFileName, $_SERVER['DOCUMENT_ROOT'] . "/upload/ammina.optimizer/png-webp/") === 0) {
				$this->arUpdateData['STAT']['TOTAL_IMAGES_PNGWEBP']['COUNT']++;
				$this->arUpdateData['STAT']['TOTAL_IMAGES_PNGWEBP']['SIZE'] += $iFileSize;
				if ($arPathInfo['extension'] == "webp") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_PNGWEBP_IMAGES']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_PNGWEBP_IMAGES']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "info") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_PNGWEBP_INFO']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_PNGWEBP_INFO']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "wait") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_PNGWEBP_WAIT']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_PNGWEBP_WAIT']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "resulturl") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_PNGWEBP_RESULTURL']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_PNGWEBP_RESULTURL']['SIZE'] += $iFileSize;
				}
			} elseif (amopt_strpos($strFileName, $_SERVER['DOCUMENT_ROOT'] . "/upload/ammina.optimizer/svg/") === 0) {
				$this->arUpdateData['STAT']['TOTAL_IMAGES_SVG']['COUNT']++;
				$this->arUpdateData['STAT']['TOTAL_IMAGES_SVG']['SIZE'] += $iFileSize;
				if ($arPathInfo['extension'] == "svg") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_SVG_IMAGES']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_SVG_IMAGES']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "info") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_SVG_INFO']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_SVG_INFO']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "wait") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_SVG_WAIT']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_SVG_WAIT']['SIZE'] += $iFileSize;
				} elseif ($arPathInfo['extension'] == "resulturl") {
					$this->arUpdateData['STAT']['TOTAL_IMAGES_SVG_RESULTURL']['COUNT']++;
					$this->arUpdateData['STAT']['TOTAL_IMAGES_SVG_RESULTURL']['SIZE'] += $iFileSize;
				}
			}
		}

	}

	protected function doStep12()
	{
		foreach ($this->arUpdateData['STAT'] as $k => $v) {
			$arFields = array(
				"TYPE" => $k,
				"TOTAL_COUNT" => $v['COUNT'],
				"TOTAL_SIZE" => $v['SIZE'],
			);
			StatTypesTable::add($arFields);
		}
		return true;
	}

}
