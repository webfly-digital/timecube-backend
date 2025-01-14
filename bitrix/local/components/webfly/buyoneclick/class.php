<?php

use Bitrix\Main\Application;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Loader;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class WFBuyOneClick extends CBitrixComponent implements Controllerable
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
            'buyOneClick' => [
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

    public function buyOneClickAction($pid, $name, $email, $phone, $msg)
    {
        if (empty($pid) || empty($phone))
            throw new Bitrix\Main\ArgumentException('buyOneClickAction empty arguments');

        //return ['success' => true, 'pid'=>$pid];

        Bitrix\Main\Loader::includeModule("iblock");
        Bitrix\Main\Loader::includeModule("catalog");
        Bitrix\Main\Loader::includeModule("sale");
        // check product exist
        $product = CIBlockElement::GetList([], ['IBLOCK_ID' => WF_CATALOG_IBLOCK_ID, '=ID' => $pid, 'ACTIVE' => 'Y'], false, false, ['*'])->GetNext();

        if (empty($product)) {
            $arSKU = CCatalogSKU::GetInfoByProductIBlock(WF_CATALOG_IBLOCK_ID);
            if (is_array($arSKU)) { //Не знаю почему, но работает только так
                $SKU = CIBlockElement::GetList([], ["IBLOCK_ID" => $arSKU["PRODUCT_IBLOCK_ID"], '=ID' => $pid], false, false, ["ID", "IBLOCK_ID",'PROPERTY_*']);
                while($ar = $SKU->GetNext()) {
                }
            }

            if (empty($SKU))
                return ['success' => false, 'message' => 'Product not found'];
        }

        // create order
        $siteId = Bitrix\Main\Context::getCurrent()->getSite();
        $currencyCode = Bitrix\Currency\CurrencyManager::getBaseCurrency();
        global $USER;
        $userId = 1;
        if ($USER->IsAuthorized()) $userId = $USER->GetID();
        $userName = $name;
        $userEmail = $email;
        $userPhone = $phone;
        $userMessage = $msg;

        $order = Bitrix\Sale\Order::create($siteId, $userId);
        $order->setPersonTypeId(1);
        $order->setField('CURRENCY', $currencyCode);

        $basket = Bitrix\Sale\Basket::create($siteId);
        $item = $basket->createItem('catalog', $pid);

        $item->setFields([
            'QUANTITY' => 1,
            'CURRENCY' => $currencyCode,
            'LID' => $siteId,
            'PRODUCT_PROVIDER_CLASS' => '\CCatalogProductProvider',
        ]);
        $order->setBasket($basket);

        $shipmentCollection = $order->getShipmentCollection();
        $shipment = $shipmentCollection->createItem();
        $emptyDeliveryId = Bitrix\Sale\Delivery\Services\EmptyDeliveryService::getEmptyDeliveryServiceId();
        $service = Bitrix\Sale\Delivery\Services\Manager::getById($emptyDeliveryId);
        $shipment->setFields([
            'DELIVERY_ID' => $service['ID'],
            'DELIVERY_NAME' => $service['NAME'],
        ]);
        $shipmentItemCollection = $shipment->getShipmentItemCollection();
        $shipmentItem = $shipmentItemCollection->createItem($item);
        $shipmentItem->setQuantity($item->getQuantity());

//        $paymentCollection = $order->getPaymentCollection();
//        $payment = $paymentCollection->createItem();
//        $paySystemService = Bitrix\Sale\PaySystem\Manager::getObjectById(1);
//        $payment->setFields([
//            'PAY_SYSTEM_ID' => $paySystemService->getField("PAY_SYSTEM_ID"),
//            'PAY_SYSTEM_NAME' => $paySystemService->getField("NAME"),
//        ]);

        $propertyCollection = $order->getPropertyCollection();
        $phoneProp = $propertyCollection->getPhone();
        $phoneProp->setValue($userPhone);
        if (!empty($userName)) {
            $nameProp = $propertyCollection->getPayerName();
            $nameProp->setValue($userName);
        }
        if (!empty($userEmail)) {
            $emailProp = $propertyCollection->getUserEmail();
            $emailProp->setValue($userEmail);
        }

        $order->setField('USER_DESCRIPTION', $userMessage);
        $order->setField('COMMENTS', 'Заказ в один клик');

        $order->doFinalAction(true);
        $result = $order->save();
        $orderId = $order->getId();

        $serverName = COption::GetOptionString("main", "server_name");
        $emailFrom = COption::GetOptionString("main", "email_from");
        Bitrix\Main\Mail\Event::send([
            'EVENT_NAME' => 'WF_EVENTS',
            'MESSAGE_ID' => '86', // /bitrix/admin/message_edit.php?lang=ru&ID=86
            'C_FIELDS' => [
                'URL' => 'https://' . $serverName . $product['DETAIL_PAGE_URL'],
                'ORDER_ID' => $orderId,
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
        if (!Loader::includeModule('sale')) throw new Exception('sale module not found');
        return true;
    }
}
