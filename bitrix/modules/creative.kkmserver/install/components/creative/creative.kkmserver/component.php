<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
    use Bitrix\Main\Localization\Loc;
    global $USER, $APPLICATION;
    Loc::loadMessages(__FILE__);
    if (CModule::IncludeModule("creative.kkmserver") && CModule::IncludeModule('sale')) {
        $http_auth_confirm = !empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW']) && $_SERVER['PHP_AUTH_USER'] == $arParams['LOGIN'] && $_SERVER['PHP_AUTH_PW'] == $arParams['PASSWORD'];
        if (empty($http_auth_confirm) && !empty($arParams['LOGIN']) && !empty($arParams['PASSWORD']) && !$USER->IsAdmin()) {
            header('Status:401 Unauthorized');
            header('WWW-Authenticate:Basic realm="Bitrix KKM-server API"');
            echo Loc::getMessage('CREATIVE_KKMSERVER_NEED_AUTH');
        } else {
            $input_json = file_get_contents('php://input');
            if (empty($input_json)) {
                $arResult = $_POST;
            } else {
                $arResult = json_decode($input_json, true);
            }
            if ((isset($arResult['Command']) && $arResult['Command'] == 'GetCommand') || isset($_GET['kkmserver'])) {
                header('Content-type:application/json');
                $this->IncludeComponentTemplate();
            } else {
                echo 'Ok';
            }
        }
    }
?>