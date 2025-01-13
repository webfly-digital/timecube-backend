<?php

namespace Webfly\Handlers;

use Bitrix\Main\Loader;

class Sale
{

    const FREE_CITIES = ['0000073738', '0000103664'];//МСК и СПБ

    /**
     * onSaleDeliveryServiceCalculate
     * Устанавливает бесплатную доставку
     * @param \Bitrix\Main\Event $event
     */
    function setFreeDelivery(\Bitrix\Main\Event $event)
    {
        Loader::includeModule('iblock');
        $shipment = $event->getParameter('SHIPMENT');
        $order = $shipment->getOrder();//получить заказ
        $propertyCollection = $order->getPropertyCollection();
        $locationProperty = $propertyCollection->getDeliveryLocation();
        if (!empty($locationProperty))
            $locPropValue = $locationProperty->getValue();
        $basket = $order->getBasket();
        $productsIDs = [];
        foreach ($basket as $item) {
            $productsIDs[] = $item->getProductId();
        }
        $baseResult = $event->getParameter('RESULT');//получить резалт
        $products = \CIblockElement::getList([], ['ID' => $productsIDs, 'IBLOCK_ID' => WF_CATALOG_IBLOCK_ID], false, false, ['ID', 'PROPERTY_IS_DELIVERY_FREE', 'PROPERTY_IS_DELIVERY_FREE_MOS']);
        while ($ob = $products->fetch()) {
            $freedelivery = ($locPropValue && $ob['PROPERTY_IS_DELIVERY_FREE_MOS_VALUE_ID'] && in_array($locPropValue, self::FREE_CITIES)) || $ob['PROPERTY_IS_DELIVERY_FREE_VALUE_ID'];
            if ($freedelivery) {
                $baseResult->setDeliveryPrice(0);//пцена в ноль
                $event->addResult(//вернуть
                    new \Bitrix\Main\EventResult(
                        \Bitrix\Main\EventResult::SUCCESS, array('RESULT' => $baseResult)
                    )
                );
                break;
            }
        }
    }

    function setFreeDeliveryCoupon(\Bitrix\Main\Event $event)
    {
        Loader::includeModule('iblock');
        $shipment = $event->getParameter('SHIPMENT');
        $baseResult = $event->getParameter('RESULT');//получить резалт
        $order = $shipment->getOrder();//получить заказ
        global $USER;
        if ($USER->IsAdmin()) {
            $listCoupon = $order->getDiscount()->getApplyResult();
            $checkCoupon = false;
            foreach ($listCoupon['COUPON_LIST'] as $itemCoupon) {
                if ($itemCoupon['COUPON'] == "CARD5" && $itemCoupon['APPLY'] == 'Y') {
                    $checkCoupon = true;
                }
            }
            $deliveryIds = $order->getDeliverySystemId();
            $checkDelivery = false;
            foreach ($deliveryIds as $itemDelivery) {
                if ($itemDelivery == 23 || $itemDelivery == 24) {
                    $checkDelivery = true;
                }
            }
            if ($checkCoupon == true && $checkDelivery == true) {
                $baseResult->setDeliveryPrice(0);//цена в ноль
                $event->addResult(//вернуть
                    new \Bitrix\Main\EventResult(
                        \Bitrix\Main\EventResult::SUCCESS, array('RESULT' => $baseResult)
                    )
                );
            }
        }
    }

    function OnSaleComponentOrderJsDataHeandler($order, &$arUserResult, $request, &$arParams, &$arResult)//, &$arDeliveryServiceAll, &$arPaySystemServiceAll)
    {
//        $paySystemResult = \Bitrix\Sale\PaySystem\Manager::getList(array('filter' => array('ACTIVE' => 'Y',)));
//        $allPaySystem = [];
//        while ($paySystem = $paySystemResult->fetch()) {
//            $dbRestriction = \Bitrix\Sale\Internals\ServiceRestrictionTable::getList(array(
//                'select' => array('PARAMS'),
//                'filter' => array(
//                    'SERVICE_ID' => $paySystem['ID'],
//                    'SERVICE_TYPE' => \Bitrix\Sale\Services\PaySystem\Restrictions\Manager::SERVICE_TYPE_PAYMENT
//                )
//            ));
//            while ($restriction = $dbRestriction->fetch()) {
//                if (!in_array('tg', $restriction['PARAMS']['SITE_ID'])) {
//                    $allPaySystem[$paySystem['ID']] = $paySystem;
//                    $allPaySystemID[$paySystem['ID']] = $paySystem['ID'];
//                }
//            }
//        }
//        foreach ($arResult['JS_DATA']['PAY_SYSTEM'] as $paySystem) {
//            $activePaySystemID[$paySystem['ID']] = $paySystem['ID'];
//        }
//
//        $newPaySystemID = array_diff($allPaySystemID, $activePaySystemID);
//        $newPaySystem = [];
//        foreach ($newPaySystemID as $paySystemID) {
//            $allPaySystem[$paySystemID]['DISABLED'] = 'Y';
//            $allPaySystem[$paySystemID]['PSA_LOGOTIP'] = \CFile::GetFileArray($allPaySystem[$paySystemID]['LOGOTIP']);
//            $newPaySystem[] = $allPaySystem[$paySystemID];
//        }
//        $arResult['JS_DATA']['PAY_SYSTEM'] = array_merge($arResult['JS_DATA']['PAY_SYSTEM'], $newPaySystem);
    }
}