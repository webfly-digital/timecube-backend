<?php

\Bitrix\Main\EventManager::getInstance()->addEventHandler('sale', 'onManagerCouponAdd', 'OnSetCouponListHandler');
//\Bitrix\Sale\DiscountCouponsManager::EVENT_ON_COUPON_ADD;
function OnSetCouponListHandler(\Bitrix\Main\Event $event)
{
    $params = $event->getParameters();
    $coupon = $params['COUPON'];
    global $COUPON_SET_HIT;
    if ($COUPON_SET_HIT !== true && in_array($coupon, ['CARD5', 'CARD10', 'CARD15']) ) {
        \Bitrix\Sale\DiscountCouponsManager::clear(true);
    }

    // catch personal card number and replace by coupon code

    $cardCoupon = null;
    if($coupon>=40000 && $coupon<=80000) $cardCoupon = 'CARD5';
    if($coupon>=10000 && $coupon<=10900) $cardCoupon = 'CARD10';
    if($coupon>=30000 && $coupon<=30500) $cardCoupon = 'CARD15';

    global $USER;
    if (!empty($cardCoupon)) {
        \Bitrix\Sale\DiscountCouponsManager::clear(true);
        $COUPON_SET_HIT = true;
        \Bitrix\Sale\DiscountCouponsManager::add($cardCoupon);
        if ($USER->IsAuthorized()) {
            $connection = \Bitrix\Main\Application::getConnection();
            $sqlHelper = $connection->getSqlHelper();
            $sqlCoupon = $sqlHelper->forSql($coupon);
            $uid = $USER->GetID();
            // save personal card number to ADMIN_NOTES user field
            $connection->queryExecute("UPDATE b_user SET ADMIN_NOTES = '$sqlCoupon' WHERE ID = '$uid';");
        }
    }
}