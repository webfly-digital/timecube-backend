<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!CModule::IncludeModule('alexkova.market') || !CModule::IncludeModule("sale"))  return;

$arResult = array();

CModule::IncludeModule('sale');
$newBasketUserID = CSaleBasket::GetBasketUserID(true);

if($newBasketUserID>0){
    $rsBaskets = CSaleBasket::GetList(
        array("ID" => "ASC"),
        array("FUSER_ID" => $newBasketUserID, "LID" => SITE_ID, "ORDER_ID" => "NULL"),
        false,
        false,
        array("ID", "NAME", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE", "CURRENCY", "DISCOUNT_PRICE")
    );
    $arPrice = 0;
    $tmpCurrency = "";
    while ($arItem = $rsBaskets->GetNext())
    {
        if (CSaleBasketHelper::isSetItem($arItem))
            continue;

        if ($arItem["CAN_BUY"] == "Y" && $arItem["DELAY"] == "N"){
            $arBasketItems["CAN_BUY"][] = $arItem;
        }
        $tmpCurrency = $arItem["CURRENCY"];
    }
}

$arOrder = array(
        'SITE_ID' => SITE_ID,
        'USER_ID' => $GLOBALS["USER"]->GetID(),
        'ORDER_PRICE' => $arPrice,
        'BASKET_ITEMS' => $arBasketItems['CAN_BUY']
);

$arOptions = array('N');
$arErrors = array();

CSaleDiscount::DoProcessOrder($arOrder, $arOptions, $arErrors);

if (!empty($arOrder['BASKET_ITEMS']))
{
    $arOrder['ORDER_PRICE'] = 0;
    foreach ($arOrder['BASKET_ITEMS'] as $key => $arItem) 
    {
        $arOrder['BASKET_ITEMS'][$key]["FORMAT_PRICE"] = SaleFormatCurrency($arItem["PRICE"], $arItem["CURRENCY"]);
        $arOrder['BASKET_ITEMS'][$key]["SUMM"] = $arItem["QUANTITY"] * $arItem["PRICE"];
        $arOrder['BASKET_ITEMS'][$key]["FORMAT_SUMM"] = SaleFormatCurrency($arOrder['BASKET_ITEMS'][$key]["SUMM"], $arItem["CURRENCY"]);

        if ($arItem["CAN_BUY"] == "Y" && $arItem["DELAY"] == "N")
                $arOrder['ORDER_PRICE'] += $arItem["PRICE"]*$arItem["QUANTITY"];
    }
}

$arResult["CURRENCY_FORMAT"] = CCurrencyLang::GetFormatDescription($tmpCurrency);
$arResult["CURRENCY_FORMAT"] = rtrim($arResult["CURRENCY_FORMAT"]["FORMAT_STRING"], '.');
$arResult["MIN_ORDER_PRICE"] = COption::GetOptionString("alexkova.market", "bxr_min_order_price");
$arResult["MIN_ORDER_PRICE_FORMATED"] = str_replace('#', $arResult["MIN_ORDER_PRICE"], $arResult["CURRENCY_FORMAT"]);
$arResult["ADD_PRICE"] = round($arResult["MIN_ORDER_PRICE"] - $arOrder["ORDER_PRICE"], 2);
$arResult["ADD_PRICE_FORMATED"] = str_replace('#', $arResult["ADD_PRICE"], $arResult["CURRENCY_FORMAT"]);
$optionMsg = COption::GetOptionString("alexkova.market", "bxr_min_order_price_msg");
$arResult["MIN_ORDER_PRICE_MSG"] = ($optionMsg != "") ? $optionMsg : GetMessage("MIN_ORDER_PRICE_MSG_DEF");
$arResult["MIN_ORDER_PRICE_MSG_FLAGS"] = $arResult["MIN_ORDER_PRICE_MSG"];
$arResult["MIN_ORDER_PRICE_MSG"] = str_replace("#MIN_ORDER_PRICE#", $arResult["MIN_ORDER_PRICE_FORMATED"], $arResult["MIN_ORDER_PRICE_MSG"]);
$arResult["MIN_ORDER_PRICE_MSG"] = str_replace("#ADD_ORDER_PRICE#", $arResult["ADD_PRICE_FORMATED"], $arResult["MIN_ORDER_PRICE_MSG"]);

if ($arResult["ADD_PRICE"] > 0 && !$_REQUEST["ORDER_ID"]) 
    $this->IncludeComponentTemplate();

return $arResult;