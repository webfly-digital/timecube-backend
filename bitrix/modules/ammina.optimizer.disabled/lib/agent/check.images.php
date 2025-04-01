<?

namespace Ammina\Optimizer\Agent;

use Ammina\Optimizer\Core2\Application;

class CheckImages
{
	static protected $arCurrentAgent = false;
	static protected $bSetEmptyNext = false;
	static protected $iEndTime = false;
	static protected $bNextFileFinded = false;
	static protected $arAllowDir = array();
	static protected $strNextFile = "";
	static protected $bIsOneStep = false;

	public static function doExecute()
	{
		Application::getInstance()->setPreventSupportWebP(true);
		$iMemory = intval(\COption::GetOptionString("ammina.optimizer", "preoptagent_memorylimit", ""));
		if ($iMemory > 0) {
			@ini_set("memory_limit", $iMemory . "M");
		}
		/**
		 * ѕровер€ем:
		 * 1. ≈сли выполнение в кроне, то провер€ем интервал запуска и выполн€ем за 1 шаг.
		 * 2. ≈сли запуск на хитах, то провер€ем интервал, включенность режима выполнени€ по шагам и разрешение выполнени€ по шагам
		 */
		self::$arCurrentAgent = \CAgent::GetList(array(), array(
			"NAME" => '\Ammina\Optimizer\Agent\CheckImages::doExecute();',
			"MODULE_ID" => "ammina.optimizer",
		))->Fetch();
		self::$arAllowDir = explode("\n", \COption::GetOptionString("ammina.optimizer", "preoptagent_dirs", implode("\n", array("/upload/", "/bitrix/templates/", "/local/"))));
		foreach (self::$arAllowDir as $k => $v) {
			self::$arAllowDir[$k] = trim(amopt_strtolower($v));
			if (amopt_strlen(self::$arAllowDir[$k]) <= 0) {
				unset(self::$arAllowDir[$k]);
			}
		}
		self::$strNextFile = \COption::GetOptionString("ammina.optimizer", "preoptagent_nextdir", "");
		self::$iEndTime = time() + \COption::GetOptionString("ammina.optimizer", "preoptagent_maxtime_step", 15);
		if (\COption::GetOptionString("ammina.optimizer", "preoptagent_emptyexec", "N") == "Y") {
			\COption::SetOptionString("ammina.optimizer", "preoptagent_emptyexec", "N");
			self::$strNextFile = "";
		} elseif (((defined("BX_CRONTAB") && BX_CRONTAB === true) || (defined("CHK_EVENT") && CHK_EVENT === true)) && (\COption::GetOptionString("ammina.optimizer", "preoptagent_onlycron", "N") == "Y")) {
			if (self::$arCurrentAgent) {
				\CAgent::Update(self::$arCurrentAgent['ID'], array("NEXT_EXEC" => ConvertTimeStamp(time() + 3600 * 12, "FULL")));
			}
			if (self::$arCurrentAgent['ID'] > 0 && self::$arCurrentAgent['AGENT_INTERVAL'] != \COption::GetOptionString("ammina.optimizer", "preoptagent_period", 120) * 60) {
				\CAgent::Update(self::$arCurrentAgent['ID'], array(
					"AGENT_INTERVAL" => \COption::GetOptionString("ammina.optimizer", "preoptagent_period", 120) * 60,
				));
				self::$bSetEmptyNext = true;
			}
			self::$bIsOneStep = true;
			self::doExecuteWithCron();
		} else {
			if (\COption::GetOptionString("ammina.optimizer", "preoptagent_onlycron", "N") != "Y") {
				if (self::$arCurrentAgent['ID'] > 0 && self::$arCurrentAgent['AGENT_INTERVAL'] != \COption::GetOptionString("ammina.optimizer", "preoptagent_period_steps", 30)) {
					\CAgent::Update(self::$arCurrentAgent['ID'], array(
						"AGENT_INTERVAL" => \COption::GetOptionString("ammina.optimizer", "preoptagent_period_steps", 30),
					));
					$GLOBALS['AOPT_SETAGENT_NEXT'][self::$arCurrentAgent['ID']] = ConvertTimeStamp(time() + \COption::GetOptionString("ammina.optimizer", "preoptagent_period_steps", 30), "FULL");
				}
				self::doExecuteWithHit();
			}
		}
		\COption::SetOptionString("ammina.optimizer", "preoptagent_nextdir", self::$strNextFile);
		\Ammina\Optimizer\Core2\AppBackground::getInstance()->doEndContent();
		Application::getInstance()->setPreventSupportWebP(false);
		return '\Ammina\Optimizer\Agent\CheckImages::doExecute();';
	}

	protected static function doExecuteWithCron()
	{

		\COption::SetOptionString("ammina.optimizer", "preoptagent_nextdir", "");
		$bResult = self::doCheckDir("/");
		if (self::$bSetEmptyNext) {
			\COption::SetOptionString("ammina.optimizer", "preoptagent_emptyexec", "Y");
		}
	}

	protected static function doExecuteWithHit()
	{
		$bResult = self::doCheckDir("/");
		if ($bResult === "Y") {
			return;
		}
		/*
				if ($nextId > 0) {
					$arFilter['>ID'] = $nextId;
				}
				$rElements = \CIBlockElement::GetList(array("ID" => "ASC"), $arFilter, false, false, array("ID"));
				while ($arElement = $rElements->Fetch()) {
					self::doCheckElementPrices($arElement['ID']);
					if (time() >= $endTime) {
						\COption::SetOptionString("ammina.optimizer", "preoptagent_nextId", $arElement['ID']);
						return;
					}
				}
				*/
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
				"AGENT_INTERVAL" => \COption::GetOptionString("ammina.optimizer", "preoptagent_period", 120) * 60,
			));
			$GLOBALS['AOPT_SETAGENT_NEXT'][self::$arCurrentAgent['ID']] = ConvertTimeStamp(time() + \COption::GetOptionString("ammina.optimizer", "preoptagent_period", 120) * 60, "FULL");
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
			if (amopt_strpos($strFullName, "/bitrix/modules") === 0 || strpos($strFullName, "/bitrix/tmp") === 0 || strpos($strFullName, "/bitrix/wizard") === 0 || strpos($strFullName, "/bitrix/ammina.cache") === 0 || strpos($strFullName, "/upload/ammina.optimizer") === 0) {
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

	protected static function doCheckFile($strFileName)
	{
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strFileName)) {
			$arPath = ammina_pathinfo($_SERVER['DOCUMENT_ROOT'] . $strFileName);
			$extension = amopt_strtolower($arPath['extension']);
			if (in_array($extension, array("jpg", "png", "jpeg", "svg", "gif"))) {
				if (self::$bIsOneStep)
				{
					\COption::SetOptionString("ammina.optimizer", "preoptagent_onestepcurfile", $strFileName);
				}
				\Ammina\Optimizer\Core2\AppBackground::getInstance()->doOptimizeImage($strFileName);
			}
		}
	}
}