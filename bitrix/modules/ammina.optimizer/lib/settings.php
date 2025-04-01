<?

namespace Ammina\Optimizer;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Event;
use Bitrix\Main\ORM\EventResult;


class SettingsTable extends DataManager
{
	public static function getTableName()
	{
		return 'am_optimizer_settings';
	}

	public static function getMap()
	{
		$fieldsMap = array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
			),
			'SITE_ID' => array(
				'data_type' => 'string',
			),
			'TYPE' => array(
				'data_type' => 'string',
			),
			'SETTINGS' => array(
				'data_type' => 'string',
			),
		);

		return $fieldsMap;
	}

	public static function onBeforeUpdate(Event $event)
	{
		$result = new EventResult();
		$data = $event->getParameter("fields");
		$arUpdateFields = false;
		if (isset($data['SETTINGS'])) {
			$arUpdateFields['SETTINGS'] = serialize(self::doNormailizeSettings($data['SETTINGS']));
		}
		if (is_array($arUpdateFields)) {
			$result->modifyFields($arUpdateFields);
		}
		return $result;
	}

	public static function onBeforeAdd(Event $event)
	{
		$result = new EventResult();
		$data = $event->getParameter("fields");
		$arUpdateFields = false;
		if (isset($data['SETTINGS'])) {
			$arUpdateFields['SETTINGS'] = serialize(self::doNormailizeSettings($data['SETTINGS']));
		}
		if (is_array($arUpdateFields)) {
			$result->modifyFields($arUpdateFields);
		}
		return $result;
	}

	public static function getList(array $parameters = array())
	{
		$result = parent::getList($parameters);
		$result->setSerializedFields(array("SETTINGS"));
		return $result;
	}

	public static function getSettingsForEdit($strSite = 'all', $strType = 'a')
	{
		$arAllSites = array();
		$b = "LID";
		$o = "ASC";
		$rSites = \CSite::GetList($b, $o);
		while ($arSite = $rSites->Fetch()) {
			$arAllSites[$arSite['LID']] = $arSite['NAME'];
		}
		if (!isset($arAllSites[$strSite])) {
			$strSite = "all";
		}
		if (!in_array($strType, array("a", "d", "m"))) {
			$strType = "a";
		}
		$arFilterList = array();
		$arFilterList[] = array(
			"SITE_ID" => "all",
			"TYPE" => "a",
		);
		if ($strSite != "all") {
			$arFilterList[] = array(
				"SITE_ID" => $strSite,
				"TYPE" => "a",
			);
		}
		if ($strType != "a") {
			$arFilterList[] = array(
				"SITE_ID" => $strSite,
				"TYPE" => $strType,
			);
		}
		$arDefaultSettings = self::getDefaultSettings();
		$arResultSetting['MAIN'] = $arDefaultSettings;
		$arOldSettings = false;
		foreach ($arFilterList as $arFilter) {
			$arSettings = SettingsTable::getList(array(
				"filter" => $arFilter,
			))->Fetch();
			if ($arSettings) {
				$arOldSettings = $arSettings;
				$arResultSetting = self::mixedSettings($arResultSetting, $arSettings['SETTINGS']);
			} else {
				$arResultSetting = self::mixedSettings($arResultSetting, array("MAIN" => $arDefaultSettings));
			}
		}
		if ($arOldSettings) {
			unset($arOldSettings['SETTINGS']);
			$arResultSetting['DB_INFO'] = $arOldSettings;
		}
		return $arResultSetting;
	}

	public static function getSettings($strSite = SITE_ID, $strType = 'd')
	{
		global $USER;
		$bPreventClearCache = false;
		if (is_object($USER) && $USER->CanDoOperation('cache_control')) {
			if (isset($_GET['clear_cache']) && $_GET['clear_cache'] == "Y") {
				$bPreventClearCache = true;
			}
		}
		$strCacheFileName = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/cache/ammina.optimizer/settings/sites/" . md5($strSite . "_" . $strType) . ".txt";
		$arCacheData = false;
		if (file_exists($strCacheFileName)) {
			$arCacheData = @unserialize(file_get_contents($strCacheFileName));
			if (time() > $arCacheData['TTL'] || $bPreventClearCache) {
				@unlink($strCacheFileName);
				$arCacheData = false;
			}
		}
		if (is_array($arCacheData) && !empty($arCacheData)) {
			$arResultSetting = $arCacheData['DATA'];
		} else {
			$b = "LID";
			$o = "ASC";
			$arSite = \CSite::GetList($b, $o, array("LID" => $strSite))->Fetch();
			if (!$arSite) {
				$strSite = "all";
			}
			if (!in_array($strType, array("a", "d", "m"))) {
				$strType = "a";
			}
			$arFilterList = array();
			$arFilterList[] = array(
				"SITE_ID" => "all",
				"TYPE" => "a",
			);
			$arSettingsCheck = SettingsTable::getList(array(
				"filter" => array("SITE_ID" => $strSite, "TYPE" => array("a", $strType)),
			))->Fetch();
			if ($arSettingsCheck) {
				if ($strSite != "all") {
					$arFilterList[] = array(
						"SITE_ID" => $strSite,
						"TYPE" => "a",
					);
				}
				if ($strType != "a") {
					$arFilterList[] = array(
						"SITE_ID" => $strSite,
						"TYPE" => $strType,
					);
				}
			} else {
				if ($strType != "a") {
					$arFilterList[] = array(
						"SITE_ID" => "all",
						"TYPE" => $strType,
					);
				}
			}
			$bFindedSetting = false;
			$iSettingsId = false;
			$arDefaultSettings = self::getDefaultSettings();
			$arResultSetting['MAIN'] = $arDefaultSettings;
			foreach ($arFilterList as $arFilter) {
				$arSettings = SettingsTable::getList(array(
					"filter" => $arFilter,
				))->Fetch();
				if ($arSettings) {
					$bFindedSetting = true;
					$iSettingsId = $arSettings['ID'];
					$arResultSetting = self::mixedSettings($arResultSetting, $arSettings['SETTINGS']);
				}
			}
			if ($bFindedSetting) {
				$arResultSetting['SETTING_ID'] = $iSettingsId;
			} else {
				$arResultSetting = array();
			}
			CheckDirPath(dirname($strCacheFileName) . "/");
			$arCacheData = array(
				"TTL" => time() + 3600,
				"DATA" => $arResultSetting
			);
			file_put_contents($strCacheFileName, serialize($arCacheData));
		}
		return $arResultSetting;
	}

	public static function getDefaultSettings()
	{
		$arAllOptionsDescription = include($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/ammina.optimizer/option.descriptions.php");
		$arResult = array();
		foreach ($arAllOptionsDescription['category'] as $strCategory => &$arCategory) {
			$arResult['category'][$strCategory]['options']['ACTIVE'] = $arCategory['options']['ACTIVE']['DEFAULT'];
			foreach ($arCategory['groups'] as $strGroup => &$arGroup) {
				$arResult['category'][$strCategory]['groups'][$strGroup]['DEFAULT'] = "Y";
				foreach ($arGroup['options'] as $strOption => &$arOption) {
					$arResult['category'][$strCategory]['groups'][$strGroup]['options'][$strOption] = $arOption['DEFAULT'];
					if ($arOption['TYPE'] == "select.options") {
						foreach ($arOption['VARIANTS'] as $kVariant => $arVariant) {
							$arResult['category'][$strCategory]['groups'][$strGroup]['options_variant'][$strOption][$kVariant] = $arVariant['DEFAULT'];
						}
					}
				}
			}
		}
		return $arResult;
	}

	public static function mixedSettings($arParentSettings, $arSettings)
	{
		$arResult = array();
		$arResult['MAIN'] = self::mixedCategoriesSettings($arParentSettings['MAIN'], $arSettings['MAIN']);
		foreach ($arSettings['PAGES'] as $iPage => $arPage) {
			$arResult['PAGES'][$iPage] = self::mixedCategoriesSettings($arResult['MAIN'], $arPage);
			$arResult['PAGES'][$iPage]['page'] = $arPage['page'];
		}
		return $arResult;
	}

	public static function mixedCategoriesSettings($arParentSettings, $arSettings)
	{
		$arResult = array();
		foreach ($arParentSettings['category'] as $strCategory => $arCategory) {
			$arResult['category'][$strCategory]['options']['ACTIVE'] = $arSettings['category'][$strCategory]['options']['ACTIVE'];
			if ($arResult['category'][$strCategory]['options']['ACTIVE'] != "Y") {
				$arResult['category'][$strCategory]['options']['ACTIVE'] = "N";
			}
			foreach ($arCategory['groups'] as $strGroup => $arGroup) {
				if ($arSettings['category'][$strCategory]['groups'][$strGroup]['DEFAULT'] == "Y") {
					$arResult['category'][$strCategory]['groups'][$strGroup]['DEFAULT'] = "Y";
					$arResult['category'][$strCategory]['groups'][$strGroup]['options'] = $arGroup['options'];
					if (isset($arGroup['options_variant'])) {
						$arResult['category'][$strCategory]['groups'][$strGroup]['options_variant'] = $arGroup['options_variant'];
					}
				} else {
					$arResult['category'][$strCategory]['groups'][$strGroup]['DEFAULT'] = "N";
					$arResult['category'][$strCategory]['groups'][$strGroup]['options'] = array_replace_recursive($arGroup['options'], $arSettings['category'][$strCategory]['groups'][$strGroup]['options']);
					if (isset($arGroup['options_variant'])) {
						if (isset($arSettings['category'][$strCategory]['groups'][$strGroup]['options_variant']) && is_array($arSettings['category'][$strCategory]['groups'][$strGroup]['options_variant'])) {
							$arResult['category'][$strCategory]['groups'][$strGroup]['options_variant'] = array_replace_recursive($arGroup['options_variant'], $arSettings['category'][$strCategory]['groups'][$strGroup]['options_variant']);
						}
					}
				}
			}
		}
		return $arResult;
	}

	public static function doNormailizeSettings($arSettings)
	{
		foreach ($arSettings['MAIN']['category'] as $strCategory => $arCategory) {
			foreach ($arCategory['groups'] as $strGroup => $arGroup) {
				if ($arGroup['DEFAULT'] == "Y") {
					unset($arSettings['MAIN']['category'][$strCategory]['groups'][$strGroup]['options']);
				}
			}
		}
		$arNewPages = array();
		$iPageIndex = 1;
		foreach ($arSettings['PAGES'] as $iPage => $arPage) {
			if ($arPage['page']['DELETE'] != "Y" && !empty(trim($arPage['page']['PAGES']))) {
				foreach ($arPage['category'] as $strCategory => $arCategory) {
					foreach ($arCategory['groups'] as $strGroup => $arGroup) {
						if ($arGroup['DEFAULT'] == "Y") {
							unset($arPage['category'][$strCategory]['groups'][$strGroup]['options']);
						}
					}
				}
				$arNewPages[$iPageIndex] = $arPage;
				$iPageIndex++;
			}
		}
		$arSettings['PAGES'] = $arNewPages;
		return $arSettings;
	}

	public static function onAfterAdd(Event $event)
	{
		$result = new EventResult();
		self::cleanCache();
		return $result;
	}

	public static function onAfterUpdate(Event $event)
	{
		$result = new EventResult();
		self::cleanCache();
		return $result;
	}

	public static function onAfterDelete(Event $event)
	{
		$result = new EventResult();
		self::cleanCache();
		return $result;
	}

	public static function cleanCache() : void
	{
		$strCacheDir = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/cache/ammina.optimizer/settings/sites/";
		$arFiles = scandir($strCacheDir);
		foreach ($arFiles as $strFile) {
			if (in_array($strFile, array(".", ".."))) {
				continue;
			}
			if (is_file($strCacheDir . $strFile)) {
				unlink($strCacheDir . $strFile);
			}
		}
	}
}