<?php

\Bitrix\Main\EventManager::getInstance()
    ->addEventHandler('sale', 'OnSaleOrderSaved', 'OnSaleOrderSavedHandler');
function OnSaleOrderSavedHandler(\Bitrix\Main\Event $event)
{
    /** @var \Bitrix\Sale\Order $order */
    $order = $event->getParameter("ENTITY");
    //$oldValues = $event->getParameter("VALUES");
    $isNew = $event->getParameter("IS_NEW");

    // personal card number saved OnSetCouponList event to ADMIN_NOTES user field
    // OnSetCouponListHandler include/coupons.php
    // now, get from ADMIN_NOTES and save to COUPON order property

    if ($isNew) {
        $couponList = \Bitrix\Sale\Internals\OrderCouponsTable::getList([
            'select' => ['COUPON'], 'filter' => ['=ORDER_ID' => $order->getId()]
        ]);
        $coupon = $couponList->fetch();
        global $USER;
        if (!empty($coupon) && $USER->IsAuthorized()) {

            $connection = \Bitrix\Main\Application::getConnection();
            $sqlHelper = $connection->getSqlHelper();
            $uid = $USER->GetID();
            $user = $connection->query("SELECT ADMIN_NOTES FROM b_user WHERE ID = '$uid';")->fetch();
            if (!empty($user))
                if (!empty($user['ADMIN_NOTES'])) {
                    $orderPropertyCollection = $order->getPropertyCollection();
                    $orderPropValue = $orderPropertyCollection->getItemByOrderPropertyId(24); // fiz
                    if (!empty($orderPropValue)) {
                        $orderPropValue->setValue($user['ADMIN_NOTES']);
                        $orderPropertyCollection->save();
                    }
                    $orderPropValue = $orderPropertyCollection->getItemByOrderPropertyId(25); // ur
                    if (!empty($orderPropValue)) {
                        $orderPropValue->setValue($user['ADMIN_NOTES']);
                        $orderPropertyCollection->save();
                    }
                }
        }

    }
}


// https://webfly24.ru/bitrix/admin/ticket_edit.php?ID=4359&lang=ru
// передача срока доставки в почтовые шаблоны
// local/templates/timecube_mail/components/bitrix/sale.personal.order.detail.mail/.default/template.php
// Передача #DELIVERY_TIME# не требуется, т.к. вывод делает sale.personal.order.detail.mail
//
//\Bitrix\Main\EventManager::getInstance()
//    ->addEventHandler('main', 'OnBeforeEventAdd', 'OnBeforeEventAddHandler');
//function OnBeforeEventAddHandler($event, $lid, &$arFields, $message_id)
//{
//    if ($event == 'SALE_NEW_ORDER') {
//        $orderId = $arFields['ORDER_REAL_ID'];
//        $order = \Bitrix\Sale\Order::load($orderId);
//        $sc = $order->getShipmentCollection();
//        /** @var \Bitrix\Sale\Shipment $shipment */
//        foreach ($sc as $shipment) {
//            if (!$shipment->isSystem()) {
//                $params = $shipment->getField('PARAMS');
//                if ($params)
//                    if ($params["DELIVERY_TIME"])
//                        $arFields['DELIVERY_TIME'] = $params["DELIVERY_TIME"];
//            }
//        }
//    }
//}

\Bitrix\Main\EventManager::getInstance()
    ->addEventHandler('sale', 'onSaleDeliveryServiceCalculate', 'onSaleDeliveryServiceCalculateHandler');
function onSaleDeliveryServiceCalculateHandler(\Bitrix\Main\Event $event)
{
    /** @var \Bitrix\Sale\Delivery\CalculationResult $baseResult */
    $baseResult = $event->getParameter('RESULT');
    $shipment = $event->getParameter('SHIPMENT');
    /** @var \Bitrix\Sale\Shipment $shipment */
    $order = $shipment->getOrder();

    $params = ['DELIVERY_TIME' => $baseResult->getPeriodDescription()];
    $shipment->setField('PARAMS', $params);
}
