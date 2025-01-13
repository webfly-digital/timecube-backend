<?php
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

global $USER;
if ($USER->isAdmin()) {

global $APPLICATION;
$APPLICATION->includeComponent(
    "bitrix:sale.personal.order.detail.mail",
    "",
    Array(
        "ACTIVE_DATE_FORMAT" => "d.m.Y",
        "CACHE_TIME" => "3600",
        "CACHE_TYPE" => "A",
        "CUSTOM_SELECT_PROPS" => [0=>"PICTURE",1=>"NAME",2=>"DISCOUNT_PRICE_PERCENT_FORMATED",3=>"PRICE_FORMATED",4=>"QUANTITY",5=>"PROPERTY_CML2_ARTICLE",],
        "ID" => "52229",
        "PATH_TO_CANCEL" => "//\".SITE_SERVER_NAME.\"/personal/cancel/{#ORDER_ACCOUNT_NUMBER_ENCODE#}/?CANCEL=Y",
        "PATH_TO_LIST" => "//\".SITE_SERVER_NAME.\"/personal/orders/",
        "PATH_TO_PAYMENT" => "//\".SITE_SERVER_NAME.\"/personal/order/payment.php",
        "PICTURE_HEIGHT" => "180",
        "PICTURE_RESAMPLE_TYPE" => "1",
        "PICTURE_WIDTH" => "180",
        "PROP_1" => array(""),
        "PROP_2" => array(""),
        "SHOW_ORDER_BASE" => "Y",
        "SHOW_ORDER_BASKET" => "Y",
        "SHOW_ORDER_BUYER" => "N",
        "SHOW_ORDER_DELIVERY" => "Y",
        "SHOW_ORDER_PARAMS" => "N",
        "SHOW_ORDER_PAYMENT" => "Y",
        "SHOW_ORDER_SUM" => "Y",
        "SHOW_ORDER_USER" => "N"
    )
);
}

//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
