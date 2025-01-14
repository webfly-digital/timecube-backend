<?php

use Bitrix\Main\Application;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Loader;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class WFCallback extends CBitrixComponent implements Controllerable
{
    /**
     * Component constructor.
     * @param CBitrixComponent | null $component
     */
    var $connection;
    var $sqlHelper;

    public function __construct($component = null)
    {
        parent::__construct($component);
        $this->connection = Application::getConnection();
        $this->sqlHelper = $this->connection->getSqlHelper();
    }

    public function configureActions()
    {
        // Сбрасываем фильтры по-умолчанию (ActionFilter\Authentication и ActionFilter\HttpMethod)
        // Предустановленные фильтры находятся в папке /bitrix/modules/main/lib/engine/actionfilter/
        return [
            'callback' => [
                'prefilters' => [
                    //new ActionFilter\Authentication(),
                    new ActionFilter\HttpMethod([
                        //ActionFilter\HttpMethod::METHOD_GET,
                        ActionFilter\HttpMethod::METHOD_POST
                    ]),
                    new ActionFilter\Csrf(),
                ]
            ],
        ];
    }

    public function callbackAction($pid, $name, $email, $phone, $msg)
    {

        if (empty($pid) || empty($phone))
            throw new Bitrix\Main\ArgumentException('callbackAction empty arguments');

        //return ['success' => true, 'pid'=>$pid];

        \Bitrix\Main\Loader::includeModule("iblock");


        global $USER;
        $userId = 1;
        if ($USER->IsAuthorized()) $userId = $USER->GetID();
        $userName = $name;
        $userEmail = $email;
        $userPhone = $phone;
        $userMessage = $msg;

        $serverName = COption::GetOptionString("main", "server_name");
        $emailFrom = COption::GetOptionString("main", "email_from");
        Bitrix\Main\Mail\Event::send([
            'EVENT_NAME' => 'WF_EVENTS',
            'MESSAGE_ID' => '86', // /bitrix/admin/message_edit.php?lang=ru&ID=86
            'C_FIELDS' => [
                'URL'=> 'https://'.$serverName.$product['DETAIL_PAGE_URL'],
                'NAME' => $userName,
                'EMAIL' => $userEmail,
                'PHONE' => $userPhone,
                'MESSAGE' => $userMessage,
                'EMAIL_TO' => $emailFrom
                ],
            'LID' => $siteId,
        ]);

        return ['success' => $result->isSuccess(), 'pid' => $pid, 'oid' => $orderId];
    }

    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    public function executeComponent()
    {
        $this->_checkModules();
        $this->includeComponentTemplate();
    }

    private function _checkModules()
    {
        if (!Loader::includeModule('iblock')) throw new Exception('iblock module not found');
        return true;
    }
}
