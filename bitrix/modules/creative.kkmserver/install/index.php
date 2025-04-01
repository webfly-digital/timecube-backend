<?
/*ToDo:Для отображения кассы в Z-отчетах в /bitrix/modules/sale/admin/cashbox_zreport.php нужно убрать ограничение по обработчикам - удалить во всех местах , '%HANDLER' => '\\Bitrix\\Sale\\Cashbox\\CashboxBitrix'*/

use Bitrix\Main\EventManager,
    Bitrix\Main\IO,
    Bitrix\Main\Application,
    Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

Class creative_kkmserver extends CModule
{
    var $MODULE_ID = "creative.kkmserver";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;

    function creative_kkmserver()
    {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path . "/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->MODULE_NAME = Loc::getMessage('CREATIVE_KKMSERVER_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('CREATIVE_KKMSERVER_MODULE_DESCRIPTION');
        $this->PARTNER_NAME = Loc::getMessage('CREATIVE_KKMSERVER_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('CREATIVE_KKMSERVER_PARTNER_URI');
    }

    function InstallFiles()
    {
        global $DOCUMENT_ROOT;
        CopyDirFiles(
            $DOCUMENT_ROOT . "/bitrix/modules/" . $this->MODULE_ID . "/install/components/",
            $DOCUMENT_ROOT . "/bitrix/components/",
            true,
            true
        );

        $file = new IO\File(Application::getDocumentRoot() . "/kkmserver/index.php");
        $file->putContents('<?
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
global $USER;
if($USER->IsAdmin()) {
        require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
}?>
<?$APPLICATION->IncludeComponent(
    "creative:creative.kkmserver",
    "",
    Array(
        "CACHE_TYPE" => "N",
        "COMPOSITE_FRAME_MODE" => "N",
        "COMPOSITE_FRAME_TYPE" => "DYNAMIC_WITHOUT_STUB",
        "LOGIN" => "' . str_replace('"', '\"', $_REQUEST['kkmserver_login']) . '",
        "PASSWORD" => "' . str_replace('"', '\"', $_REQUEST['kkmserver_password']) . '"
    ),
    false
);?>
<? if($USER->IsAdmin()) {
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
} else {
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
}?>');
        return true;
    }

    function UnInstallFiles()
    {
        if (CModule::IncludeModule("creative.kkmserver") && CModule::IncludeModule('main') && CModule::IncludeModule('sale')) {
            new creativeKkmserver();
            \Bitrix\Sale\Cashbox\cashboxCreativeKkmserver::deleteCashboxList();
        }
        DeleteDirFilesEx("/bitrix/components/creative/creative.kkmserver");
        DeleteDirFilesEx("/kkmserver");
        return true;
    }

    function InstallEvents()
    {
        EventManager::getInstance()->registerEventHandler(
            "sale",
            "OnGetCustomCashboxHandlers",
            $this->MODULE_ID,
            "creativeKkmserver",
            "customCashboxHandlers"
        );
        EventManager::getInstance()->registerEventHandler(
            "main",
            "OnProlog",
            $this->MODULE_ID,
            "creativeKkmserver",
            "AddModuleOnAjaxSettings"
        );
        return false;
    }

    function UnInstallEvents()
    {
        EventManager::getInstance()->unRegisterEventHandler(
            "sale",
            "OnGetCustomCashboxHandlers",
            $this->MODULE_ID,
            "creativeKkmserver",
            "customCashboxHandlers"
        );
        EventManager::getInstance()->unRegisterEventHandler(
            "main",
            "OnProlog",
            $this->MODULE_ID,
            "creativeKkmserver",
            "AddModuleOnAjaxSettings"
        );
        return false;
    }

    function DoInstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION, $step;
        $step = intval($step);
        if ($step < 2) {
            $APPLICATION->IncludeAdminFile(Loc::getMessage('CREATIVE_KKMSERVER_INSTALL_MODULE'), $DOCUMENT_ROOT . "/bitrix/modules/" . $this->MODULE_ID . "/install/step1.php");
        } else {
            $this->InstallFiles();
            $this->InstallEvents();
            RegisterModule($this->MODULE_ID);
            $APPLICATION->IncludeAdminFile(Loc::getMessage('CREATIVE_KKMSERVER_INSTALL_MODULE'), $DOCUMENT_ROOT . "/bitrix/modules/" . $this->MODULE_ID . "/install/step2.php");
        }
    }

    function DoUninstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->UnInstallFiles();
        $this->UnInstallEvents();
        UnRegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile(Loc::getMessage('CREATIVE_KKMSERVER_UNINSTALL_MODULE'), $DOCUMENT_ROOT . "/bitrix/modules/" . $this->MODULE_ID . "/install/unstep.php");
    }
}

?>