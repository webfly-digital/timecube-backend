<?php
$eventManager = \Bitrix\Main\EventManager::getInstance();

// Registration Antispam
$eventManager->AddEventHandler('main', 'OnBeforeUserRegister', 'wfRegisterAntispam');
function wfRegisterAntispam(&$arFields)
{
    // local\templates\wf_timecube\components\bitrix\system.auth.registration\flat\template.php
    $request = \Bitrix\Main\Context::getCurrent()->getRequest();
    if ($request->getPost('wf_check') !== md5('wf_check_secret_1687184' . bitrix_sessid())) {
        global $APPLICATION;
        $APPLICATION->ThrowException('Ошибка регистрации. You shell not pass!');
        return false;
    }
    return true;
}

// Email as Login
$eventManager->addEventHandler('main', 'OnBeforeUserRegister', 'wfEmailAsLogin');
$eventManager->addEventHandler('main', 'OnBeforeUserUpdate', 'wfEmailAsLogin');
$eventManager->addEventHandler('main', 'OnBeforeUserAdd', 'wfEmailAsLogin');
function wfEmailAsLogin(&$arFields)
{
    if ($arFields['ID'] != '1') {
        $arFields['LOGIN'] = $arFields['EMAIL'];
    }
}