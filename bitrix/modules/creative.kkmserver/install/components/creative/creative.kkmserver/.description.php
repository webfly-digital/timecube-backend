<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
    die();
    use Bitrix\Main\Localization\Loc;
    Loc::loadMessages(__FILE__);
    $arComponentDescription = array(
        "NAME" => Loc::getMessage('CREATIVE_KKMSERVER_COMPONENT_NAME'),
        "DESCRIPTION" => Loc::getMessage('CREATIVE_KKMSERVER_COMPONENT_DESCRIPTION'),
        "ICON" => "/images/kkmserver.png",
        "CACHE_PATH" => "Y",
        "PATH" => array(
            "ID" => "creative",
            "NAME" => Loc::getMessage('CREATIVE_KKMSERVER_COMPONENT_PATH_NAME')
        ),
    );
?>