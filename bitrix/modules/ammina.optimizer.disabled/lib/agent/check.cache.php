<?

namespace Ammina\Optimizer\Agent;

use Ammina\Optimizer\Core2\Application;

class CheckCache
{
	static protected $arCurrentAgent = false;
	static protected $bSetEmptyNext = false;
	static protected $iEndTime = false;
	static protected $bNextFileFinded = false;
	static protected $arAllowDir = array();
	static protected $strNextFile = "";
	static protected $bIsOneStep = false;
	static protected $iMaxTimeDeleteCss = false;
	static protected $iMaxTimeDeleteJs = false;
	static protected $iMaxTimeDeleteImage = false;

	public static function doExecute()
	{
		if (defined("BX_UTF") && BX_UTF == 'true') {
			setlocale(LC_ALL, 'ru_RU.utf8');
		}
		$iMemory = intval(\COption::GetOptionString("ammina.optimizer", "clearcacheagent_memorylimit", ""));
		if ($iMemory > 0) {
			@ini_set("memory_limit", $iMemory . "M");
		}
		self::$iMaxTimeDeleteCss = time() - \COption::GetOptionString("ammina.optimizer", "clearcacheagent_ttl_css", 3) * 3600 * 24;
		self::$iMaxTimeDeleteJs = time() - \COption::GetOptionString("ammina.optimizer", "clearcacheagent_ttl_js", 14) * 3600 * 24;
		self::$iMaxTimeDeleteImage = time() - \COption::GetOptionString("ammina.optimizer", "clearcacheagent_ttl_image", 30) * 3600 * 24;

		/**
		 * ѕровер€ем:
		 * 1. ≈сли выполнение в кроне, то провер€ем интервал запуска и выполн€ем за 1 шаг.
		 * 2. ≈сли запуск на хитах, то провер€ем интервал, включенность режима выполнени€ по шагам и разрешение выполнени€ по шагам
		 */
		self::$arCurrentAgent = \CAgent::GetList(array(), array(
			"NAME" => '\Ammina\Optimizer\Agent\CheckCache::doExecute();',
			"MODULE_ID" => "ammina.optimizer",
		))->Fetch();
		self::$arAllowDir = array(
			"/bitrix/ammina.cache/",
			"/upload/ammina.optimizer/",
			"/upload/ammina.optremote/",
		);
		self::$strNextFile = \COption::GetOptionString("ammina.optimizer", "clearcacheagent_nextdir", "");
		self::$iEndTime = time() + \COption::GetOptionString("ammina.optimizer", "clearcacheagent_maxtime_step", 15);
		if (\COption::GetOptionString("ammina.optimizer", "clearcacheagent_emptyexec", "N") == "Y") {
			\COption::SetOptionString("ammina.optimizer", "clearcacheagent_emptyexec", "N");
			self::$strNextFile = "";
		} elseif (((defined("BX_CRONTAB") && BX_CRONTAB === true) || (defined("CHK_EVENT") && CHK_EVENT === true)) && (\COption::GetOptionString("ammina.optimizer", "clearcacheagent_onlycron", "N") == "Y")) {
			if (self::$arCurrentAgent) {
				\CAgent::Update(self::$arCurrentAgent['ID'], array("NEXT_EXEC" => ConvertTimeStamp(time() + 3600 * 12, "FULL")));
			}
			if (self::$arCurrentAgent['ID'] > 0 && self::$arCurrentAgent['AGENT_INTERVAL'] != \COption::GetOptionString("ammina.optimizer", "clearcacheagent_period", 120) * 60) {
				\CAgent::Update(self::$arCurrentAgent['ID'], array(
					"AGENT_INTERVAL" => \COption::GetOptionString("ammina.optimizer", "clearcacheagent_period", 120) * 60,
				));
				self::$bSetEmptyNext = true;
			}
			self::$bIsOneStep = true;
			self::doExecuteWithCron();
		} else {
			if (\COption::GetOptionString("ammina.optimizer", "clearcacheagent_onlycron", "N") != "Y") {
				if (self::$arCurrentAgent['ID'] > 0 && self::$arCurrentAgent['AGENT_INTERVAL'] != \COption::GetOptionString("ammina.optimizer", "clearcacheagent_period_steps", 30)) {
					\CAgent::Update(self::$arCurrentAgent['ID'], array(
						"AGENT_INTERVAL" => \COption::GetOptionString("ammina.optimizer", "clearcacheagent_period_steps", 30),
					));
					$GLOBALS['AOPT_SETAGENT_NEXT'][self::$arCurrentAgent['ID']] = ConvertTimeStamp(time() + \COption::GetOptionString("ammina.optimizer", "clearcacheagent_period_steps", 30), "FULL");
				}
				self::doExecuteWithHit();
			}
		}
		\COption::SetOptionString("ammina.optimizer", "clearcacheagent_nextdir", self::$strNextFile);
		return '\Ammina\Optimizer\Agent\CheckCache::doExecute();';
	}

	protected static function doExecuteWithCron()
	{

		\COption::SetOptionString("ammina.optimizer", "clearcacheagent_nextdir", "");
		$bResult = self::doCheckDir("/");
		if (self::$bSetEmptyNext) {
			\COption::SetOptionString("ammina.optimizer", "clearcacheagent_emptyexec", "Y");
		}
	}

	protected static function doExecuteWithHit()
	{
		$bResult = self::doCheckDir("/");
		if ($bResult === "Y") {
			return;
		}
		if (amopt_strlen(self::$strNextFile) > 0 && !self::$bNextFileFinded) {
			$ar = explode("/", self::$strNextFile);
			if (amopt_strlen($ar[count($ar) - 1]) > 0) {
				unset($ar[count($ar) - 1]);
			} else {
				unset($ar[count($ar) - 1]);
				if (count($ar) > 0) {
					unset($ar[count($ar) - 1]);
				}
			}
			self::$strNextFile = implode("/", $ar) . "/";
		} else {
			self::$strNextFile = "";
		}
		if (self::$arCurrentAgent['ID'] > 0) {
			\CAgent::Update(self::$arCurrentAgent['ID'], array(
				"AGENT_INTERVAL" => \COption::GetOptionString("ammina.optimizer", "clearcacheagent_period", 1440) * 60,
			));
			$GLOBALS['AOPT_SETAGENT_NEXT'][self::$arCurrentAgent['ID']] = ConvertTimeStamp(time() + \COption::GetOptionString("ammina.optimizer", "clearcacheagent_period", 1440) * 60, "FULL");
		}
	}

	protected static function isAllowDir($strCheckDir)
	{
		foreach (self::$arAllowDir as $strAllowDir) {
			if (amopt_strlen($strAllowDir) <= amopt_strlen($strCheckDir)) {
				if (amopt_strpos($strCheckDir, $strAllowDir) === 0) {
					return true;
				}
			} else {
				if (amopt_strpos($strAllowDir, $strCheckDir) === 0) {
					return true;
				}
			}
		}
		return false;
	}

	protected static function doCheckDir($strBaseDir)
	{
		$bResult = true;
		if ((amopt_strlen(self::$strNextFile) <= 0) || ($strBaseDir == self::$strNextFile)) {
			self::$bNextFileFinded = true;
		}
		if (!self::isAllowDir($strBaseDir)) {
			return $bResult;
		}
		$arFiles = scandir($_SERVER['DOCUMENT_ROOT'] . $strBaseDir);
		asort($arFiles, SORT_ASC | SORT_NATURAL);
		foreach ($arFiles as $strFile) {
			if (in_array($strFile, array(".", ".."))) {
				continue;
			}
			$strFullName = $strBaseDir . $strFile;
			if (amopt_strpos($strFullName, "/bitrix/modules") === 0 || amopt_strpos($strFullName, "/bitrix/tmp") === 0 || amopt_strpos($strFullName, "/bitrix/wizard") === 0) {
				continue;
			}
			if (is_dir($_SERVER['DOCUMENT_ROOT'] . $strBaseDir . $strFile . "/") && file_exists($_SERVER['DOCUMENT_ROOT'] . $strBaseDir . $strFile . "/")) {
				if (!self::isAllowDir($strFullName . "/")) {
					continue;
				}
				if (self::$strNextFile != "") {
					if (self::$bNextFileFinded || amopt_strpos(self::$strNextFile, $strBaseDir . $strFile . "/") === 0) {
						$bResult = self::doCheckDir($strBaseDir . $strFile . "/");
					} else {
						continue;
					}
				} else {
					$bResult = self::doCheckDir($strBaseDir . $strFile . "/");
				}
				self::doCheckEmptyDirRemove($strBaseDir . $strFile . "/");
			} elseif (is_file($_SERVER['DOCUMENT_ROOT'] . $strBaseDir . $strFile)) {
				if (self::$strNextFile != "") {
					if (!self::$bNextFileFinded) {
						if (self::$strNextFile == $strBaseDir . $strFile) {
							self::$bNextFileFinded = true;
						}
						continue;
					} else {
						self::doCheckFile($strBaseDir . $strFile);
						self::$strNextFile = $strBaseDir . $strFile;
					}
				} else {
					self::doCheckFile($strBaseDir . $strFile);
					self::$strNextFile = $strBaseDir . $strFile;
				}
			}
			if (!self::$bIsOneStep && (time() >= self::$iEndTime || $bResult === "Y")) {
				return "Y";
			}
		}
		return $bResult;
	}

	public static function doCheckFile($strFileName)
	{
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strFileName)) {
			$arPath = pathinfo($_SERVER['DOCUMENT_ROOT'] . $strFileName);
			if ($arPath['extension'] == "info") {
				return;
			}
			$iCheckTime = false;
			$bIsImageCheckExists = false;
			if (amopt_strpos($strFileName, '/upload/ammina.optimizer') === 0) {
				$iCheckTime = self::$iMaxTimeDeleteImage;
				$bIsImageCheckExists = true;
			} elseif (amopt_strpos($strFileName, '/upload/ammina.optremote') === 0) {
				$iCheckTime = self::$iMaxTimeDeleteImage;
			} elseif (amopt_strpos($strFileName, '/bitrix/ammina.cache/css') === 0 || amopt_strpos($strFileName, '/bitrix/ammina.cache/img') === 0) {
				$iCheckTime = self::$iMaxTimeDeleteCss;
			} elseif (amopt_strpos($strFileName, '/bitrix/ammina.cache/js') === 0) {
				$iCheckTime = self::$iMaxTimeDeleteJs;
			}
			if ($bIsImageCheckExists) {
				$arOriginalFile = explode("/", $strFileName);
				if (amopt_strtolower($arOriginalFile[1]) == "upload" && amopt_strtolower($arOriginalFile[2]) == "ammina.optimizer") {
					unset($arOriginalFile[1]);
					unset($arOriginalFile[2]);
					if ($arOriginalFile[3] != "svg") {
						unset($arOriginalFile[4]);
					}
					$strOriginalExtension = $arOriginalFile[3];
					if (strpos($arOriginalFile[3], '-') !== false) {
						$ar = explode("-", $arOriginalFile[3]);
						$strOriginalExtension = $ar[0];
					}
					unset($arOriginalFile[3]);
					$strOriginalFile = implode("/", $arOriginalFile);
					$arPath = pathinfo($strOriginalFile);
					$strOriginalFile = $arPath['dirname'] . "/" . $arPath['filename'] . "." . $strOriginalExtension;
					if (strlen($strOriginalFile) > 0 && !file_exists($_SERVER['DOCUMENT_ROOT'] . $strOriginalFile)) {
						$iCheckTime = false;
						@unlink($_SERVER['DOCUMENT_ROOT'] . $strFileName);
						@unlink($_SERVER['DOCUMENT_ROOT'] . $strFileName . ".info");
					}
				}
			}
			if ($iCheckTime !== false) {
				$t = filemtime($_SERVER['DOCUMENT_ROOT'] . $strFileName);
				if ($t < $iCheckTime) {
					@unlink($_SERVER['DOCUMENT_ROOT'] . $strFileName);
				}
			}
			/*
			$arPath = ammina_pathinfo($_SERVER['DOCUMENT_ROOT'] . $strFileName);
			$extension = amopt_strtolower($arPath['extension']);
			if (in_array($extension, array("jpg", "png", "jpeg", "svg", "gif"))) {
				if (self::$bIsOneStep) {
					\COption::SetOptionString("ammina.optimizer", "preoptagent_onestepcurfile", $strFileName);
				}
				\Ammina\Optimizer\Core2\AppBackground::getInstance()->doOptimizeImage($strFileName);
			}*/
		}
	}

	protected static function doCheckEmptyDirRemove($strBaseDir)
	{
		$iCntFiles = 0;
		$arFiles = scandir($_SERVER['DOCUMENT_ROOT'] . $strBaseDir);
		foreach ($arFiles as $strFile) {
			if (in_array($strFile, array(".", ".."))) {
				continue;
			}
			$iCntFiles++;
		}
		if ($iCntFiles <= 0) {
			@rmdir($_SERVER['DOCUMENT_ROOT'] . $strBaseDir);
		}
	}
}
