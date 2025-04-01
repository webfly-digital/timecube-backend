<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
$arComponentParameters = array(
    "GROUPS" => array(),
    "PARAMETERS" => array(
        "LOGIN" => array(
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage('CREATIVE_KKMSERVER_LOGIN'),
            "TYPE" => "STRING",
            "MULTIPLE" => "N",
            "DEFAULT" => "kkmserver",
        ),
        "PASSWORD" => array(
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage('CREATIVE_KKMSERVER_PASSWORD'),
            "TYPE" => "STRING",
            "MULTIPLE" => "N",
            "DEFAULT" => "",
        ),
    ),
);
?>