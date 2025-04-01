<?

namespace Ammina\Optimizer\Workers\Cache;

class Clear
{
	protected $NS = array();
	protected $startTime = false;
	protected $arClearData = array();
	protected $arErrors = array();
	protected $arCacheData = array();

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

	protected function doEndTimeInterval()
	{
		if ((time() - $this->startTime) >= $this->NS['INTERVAL']) {
			return true;
		}
		return false;
	}

	public function getClearData()
	{
		return $this->arClearData;
	}

	public function doClearProcess($arClearData)
	{
		$bResult = true;
		$this->arClearData = $arClearData;
		if ($this->NS['STEP'] <= 0) {
			foreach ($this->arClearData['CLEAR_DIRS'] as $k => $v) {
				$bResult = $this->doClearDir($v);
				if ($bResult === true) {
					unset($this->arClearData['CLEAR_DIRS'][$k]);
					$bResult = true;
				} elseif ($bResult === "Y") {
					$bResult = true;
					break;
				}
			}
			if ($bResult && empty($this->arClearData['CLEAR_DIRS'])) {
				$this->NS['STEP']++;
			}
		}
		return $bResult;
	}

	protected function doClearDir($strBaseDir)
	{
		$bResult = true;
		$arFiles = scandir($strBaseDir);
		foreach ($arFiles as $strFile) {
			if (in_array($strFile, array(".", ".."))) {
				continue;
			}
			if (is_dir($strBaseDir . $strFile . "/") && file_exists($strBaseDir . $strFile . "/")) {
				$bResult = $this->doClearDir($strBaseDir . $strFile . "/");
			} elseif (is_file($strBaseDir . $strFile)) {
				@unlink($strBaseDir . $strFile);
			}
			if ($this->doEndTimeInterval() || $bResult === "Y") {
				return "Y";
			}
		}
		if ($bResult === true) {
			@rmdir($strBaseDir);
		}
		return $bResult;
	}
}
