<?

IncludeModuleLangFile(__FILE__);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/classes/general/update_client.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/classes/general/update_client_partner.php");
include_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/ammina.optimizer/mbfunc.php");
class ammina_optimizer extends CModule
{

	const MODULE_ID = 'ammina.optimizer';

	var $MODULE_ID = 'ammina.optimizer';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__) . "/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("ammina.optimizer_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("ammina.optimizer_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("ammina.optimizer_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("ammina.optimizer_PARTNER_URI");
	}

	function InstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
		$errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/db/" . $DBType . "/install.sql");
		if (!empty($errors)) {
			$APPLICATION->ThrowException(implode("", $errors));
			return false;
		}
		RegisterModuleDependences('main', 'OnBeforeEndBufferContent', self::MODULE_ID, 'CAmminaOptimizer', 'OnBeforeEndBufferContent', 1);
		RegisterModuleDependences('main', 'OnBeforeEndBufferContent', self::MODULE_ID, 'CAmminaOptimizer', 'OnBeforeEndBufferContent2', 1000000);
		RegisterModuleDependences('main', 'OnEndBufferContent', self::MODULE_ID, 'CAmminaOptimizer', 'OnEndBufferContent', 1000);
		RegisterModuleDependences('main', 'OnProlog', self::MODULE_ID, 'CAmminaOptimizer', 'OnProlog');
		RegisterModuleDependences('main', 'OnPageStart', self::MODULE_ID, 'CAmminaOptimizer', 'OnPageStart');
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
		if (array_key_exists("savedata", $arParams) && $arParams["savedata"] != "Y") {
			$errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/db/" . $DBType . "/uninstall.sql");

			if (!empty($errors)) {
				$APPLICATION->ThrowException(implode("", $errors));
				return false;
			}

		}
		UnRegisterModuleDependences('main', 'OnBeforeEndBufferContent', self::MODULE_ID, 'CAmminaOptimizer', 'OnBeforeEndBufferContent2');
		UnRegisterModuleDependences('main', 'OnBeforeEndBufferContent', self::MODULE_ID, 'CAmminaOptimizer', 'OnBeforeEndBufferContent');
		UnRegisterModuleDependences('main', 'OnPageStart', self::MODULE_ID, 'CAmminaOptimizer', 'OnPageStart');
		UnRegisterModuleDependences('main', 'OnProlog', self::MODULE_ID, 'CAmminaOptimizer', 'OnProlog');
		UnRegisterModuleDependences('main', 'OnEndBufferContent', self::MODULE_ID, 'CAmminaOptimizer', 'OnEndBufferContent');
		return true;
	}

	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles($arParams = array())
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/admin", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin", true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/php_interface", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/php_interface", true);
		//CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/components", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components", true, true);
		//CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/js/ammina.ip", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/js/ammina.ip", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/themes/.default", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/themes/.default", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/css", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/css", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/js", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/js", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/images", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/images", true, true);

		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/admin", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin");
		//DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/components", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components");
		//DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/js/ammina.ip", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/js/ammina.ip");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/themes/.default");//css
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/css", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/css");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/js", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/js");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/images", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/images");

		return true;
	}

	function DoInstall()
	{
		global $APPLICATION, $step;
		CJSCore::Init(array("jquery2"));
		$step = intval($step);
		if ($step < 2) {
			$APPLICATION->IncludeAdminFile(GetMessage("ammina.optimizer_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/step1.php");
		} elseif ($step == 2) {
			if (amopt_strlen($_REQUEST['AFIELDS']['NAME']) <= 0 || amopt_strlen($_REQUEST['AFIELDS']['EMAIL']) <= 0 || amopt_strlen($_REQUEST['AFIELDS']['PHONE']) <= 0) {
				LocalRedirect($APPLICATION->GetCurPageParam("lang=" . LANG . "&install=Y&id=" . self::MODULE_ID));
			}
			$this->doSendRegData();
			$this->InstallFiles();
			$this->InstallDB();
			RegisterModule(self::MODULE_ID);
			$this->InstallQuickSettings();
			$GLOBALS["errors"] = $this->errors;
			$APPLICATION->IncludeAdminFile(GetMessage("ammina.optimizer_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/step2.php");
		}
	}

	function DoUninstall()
	{
		global $APPLICATION, $step;
		$step = IntVal($step);
		if ($step < 2) {
			$APPLICATION->IncludeAdminFile(GetMessage("ammina.optimizer_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/unstep1.php");
		} elseif ($step == 2) {
			UnRegisterModule(self::MODULE_ID);
			$this->UnInstallDB(array(
				"savedata" => $_REQUEST["savedata"],
			));
			$this->UnInstallFiles();
			$GLOBALS["errors"] = $this->errors;
			$APPLICATION->IncludeAdminFile(GetMessage("ammina.optimizer_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . self::MODULE_ID . "/install/unstep2.php");
		}
	}

	function doSendRegData()
	{
		$strHost = $_SERVER['SERVER_NAME'];
		if (amopt_strlen($strHost) <= 0) {
			$strHost = $_SERVER['HTTP_HOST'];
		}
		$strError = "";
		$arClient = CUpdateClient::GetUpdatesList($strError);
		$arFields = array(
			"LEAD_NAME" => $_REQUEST['AFIELDS']['NAME'],
			"LEAD_EMAIL" => $_REQUEST['AFIELDS']['EMAIL'],
			"LEAD_PHONE" => $_REQUEST['AFIELDS']['PHONE'],
			"LEAD_UF_CRM_1551200754705" => md5('BITRIX' . CUpdateClientPartner::GetLicenseKey() . 'LICENSE'),
			"LEAD_UF_CRM_1551200838989" => $arClient['CLIENT'][0]['@']['LICENSE'],
			"LEAD_UF_CRM_1551200902405" => $strHost,
			"LEAD_UF_CRM_1551200882741" => self::MODULE_ID,
			"LEAD_UF_CRM_1551200966047" => $arClient['CLIENT'][0]['@']['DATE_FROM'],
			"LEAD_UF_CRM_1551200977499" => $arClient['CLIENT'][0]['@']['DATE_TO'],
			"from" => $_SERVER['HTTP_REFERER'],
		);
		if (!defined("BX_UTF") || BX_UTF !== true) {

			$arFields = $GLOBALS['APPLICATION']->ConvertCharsetArray($arFields, (amopt_strlen(LANG_CHARSET) > 0 ? LANG_CHARSET : SITE_CHARSET), "UTF-8");
		}
		$strUrl = "https://www.ammina24.ru/pub/form/6_kontaktnye_dannye_po_modulyu_ammina_ip/dyvcqd/?form_code=6_kontaktnye_dannye_po_modulyu_ammina_ip&sec=dyvcqd";
		$oHttpClient = new \Bitrix\Main\Web\HttpClient(array(
			'redirect' => true,
			'redirectMax' => 10,
			'version' => '1.1',
			'disableSslVerification' => true,
			'waitResponse' => true,
			'socketTimeout' => 15,
			'streamTimeout' => 30,
			'charset' => "UTF-8",
		));
		$strResult1 = $oHttpClient->get("https://www.ammina24.ru/pub/form/6_kontaktnye_dannye_po_modulyu_ammina_ip/dyvcqd/");
		$status = $oHttpClient->getStatus();
		if ($status == 200) {
			$s1 = amopt_strpos($strResult1, "'bitrix_sessid':'");
			$s2 = amopt_strpos($strResult1, "'", $s1 + 17);
			$strSessId = amopt_substr($strResult1, $s1 + 17, $s2 - $s1 - 17);
			$arFields['sessid'] = $strSessId;
			$oCookie = $oHttpClient->getCookies();
			$oHttpClient->setCookies($oCookie->toArray());
			$oHttpClient->setHeader('Referer', 'https://www.ammina24.ru/pub/form/6_kontaktnye_dannye_po_modulyu_ammina_ip/dyvcqd/');
			$response = $oHttpClient->post($strUrl, $arFields);
			unset($oHttpClient);
		}

	}

	function InstallQuickSettings()
	{
		CModule::IncludeModule("ammina.optimizer");
		if ($_REQUEST['AFIELDS']['QUICK_SETTINGS'] == "Y") {
			CAmminaOptimizer::doCheckAmminaAPIKey();
			set_time_limit(600);
			$arCheckData = \Ammina\Optimizer\Core2\LibAvailable::doCheckLibrary();
			if ($_REQUEST['AFIELDS']['QUICK_SITES_ALL'] == "Y") {
				$arSettings = \Ammina\Optimizer\SettingsTable::getList(
					array(
						"filter" => array(
							"SITE_ID" => "all",
							"TYPE" => "a",
						),
					)
				)->Fetch();
				if (!$arSettings) {
					$arCurrentSettings = \Ammina\Optimizer\SettingsTable::getSettingsForEdit("all", "a");
					$arCurrentSettings['MAIN']['category']['main']['options']['ACTIVE'] = "Y";
					$arCurrentSettings['MAIN']['category']['css']['options']['ACTIVE'] = "Y";
					$arCurrentSettings['MAIN']['category']['js']['options']['ACTIVE'] = "Y";
					$arCurrentSettings['MAIN']['category']['images']['options']['ACTIVE'] = "Y";
					$arCurrentSettings['MAIN']['category']['other']['options']['ACTIVE'] = "Y";
					\Ammina\Optimizer\SettingsTable::add(
						array(
							"SITE_ID" => "all",
							"TYPE" => "a",
							"SETTINGS" => $arCurrentSettings
						)
					);
				}
			} elseif (isset($_REQUEST['AFIELDS']['QUICK_SITES']) && is_array($_REQUEST['AFIELDS']['QUICK_SITES'])) {
				$firstSite = false;
				foreach ($_REQUEST['AFIELDS']['QUICK_SITES'] as $siteId) {
					$arSettings = \Ammina\Optimizer\SettingsTable::getList(
						array(
							"filter" => array(
								"SITE_ID" => $siteId,
								"TYPE" => "a",
							),
						)
					)->Fetch();
					if (!$arSettings) {
						if ($firstSite === false) {
							$firstSite = $siteId;
						}
						$arCurrentSettings = \Ammina\Optimizer\SettingsTable::getSettingsForEdit($siteId, "a");
						$arCurrentSettings['MAIN']['category']['main']['options']['ACTIVE'] = "Y";
						$arCurrentSettings['MAIN']['category']['css']['options']['ACTIVE'] = "Y";
						$arCurrentSettings['MAIN']['category']['js']['options']['ACTIVE'] = "Y";
						$arCurrentSettings['MAIN']['category']['images']['options']['ACTIVE'] = "Y";
						$arCurrentSettings['MAIN']['category']['other']['options']['ACTIVE'] = "Y";
						\Ammina\Optimizer\SettingsTable::add(
							array(
								"SITE_ID" => $siteId,
								"TYPE" => "a",
								"SETTINGS" => $arCurrentSettings
							)
						);
					}
				}
				if ($firstSite === false) {
					foreach ($_REQUEST['AFIELDS']['QUICK_SITES'] as $siteId) {
						$firstSite = $siteId;
						break;
					}
				}
				if ($firstSite === false) {
					$firstSite = "all";
				}
			}
		}
	}
}

?>