<?

namespace Ammina\Optimizer\Core2;

use Ammina\Optimizer\Core2\Optimizer\Image;

class AppBackground
{
	/**
	 * @var AppBackground
	 */
	protected static $_instance = NULL;
	protected $arImagesForOptimize = array();

	protected $arImageOptimizers = array();

	public static function isInstance()
	{
		if (self::$_instance === NULL) {
			return false;
		}
		return true;
	}

	/**
	 * @return AppBackground
	 */
	public static function getInstance()
	{
		if (self::$_instance === NULL) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	private function __construct()
	{
		global $APPLICATION;
		$arAllOptions = array();
		$b = "LID";
		$o = "ASC";
		$rSites = \CSite::GetList($b, $o);
		while ($arSite = $rSites->Fetch()) {
			$arOptions = \Ammina\Optimizer\SettingsTable::getSettings($arSite['LID'], "d");
			if ($arOptions['SETTING_ID'] > 0 && !isset($arAllOptions[$arOptions['SETTING_ID']])) {
				$arAllOptions[$arOptions['SETTING_ID']] = $arOptions;
			}
		}
		foreach ($arAllOptions as $k => $arOptions) {
			if ($arOptions['MAIN']['category']['images']['options']['ACTIVE'] == "Y") {
				$this->arImageOptimizers[] = new Image($arOptions['MAIN']['category']['images']['groups']);
			}
			if (isset($arOptions['PAGES']) && !empty($arOptions['PAGES'])) {
				foreach ($arOptions['PAGES'] as $k => $arPage) {
					if ($arPage['page']['ACTIVE'] == "Y") {
						if ($arPage['category']['images']['options']['ACTIVE'] == "Y") {
							$this->arImageOptimizers[] = new Image($arPage['category']['images']['groups']);
						}
					}
				}
			}
		}
	}

	private function __clone()
	{
	}

	private function __wakeup()
	{
		throw new \Exception("Cannot unserialize a singleton.");
	}

	public function doPushOptimizeImage($strImagePath)
	{
		$this->arImagesForOptimize[] = $strImagePath;
	}

	public function doOptimizeImage($strImagePath)
	{
		if (amopt_strlen($strImagePath) > 0 && file_exists($_SERVER['DOCUMENT_ROOT'] . $strImagePath)) {
			/**
			 * @var $oImageOptimizer Image
			 */
			foreach ($this->arImageOptimizers as $oImageOptimizer) {
				$oImageOptimizer->doOptimizeImage($strImagePath, true);
			}
		}
	}

	public function doEndContent()
	{
		if (!empty($this->arImagesForOptimize)) {
			foreach ($this->arImagesForOptimize as $k => $v) {
				$this->doOptimizeImage($v);
				unset($this->arImagesForOptimize[$k]);
			}
		}
		Application::getInstance()->doSendStackRequest();
	}
}