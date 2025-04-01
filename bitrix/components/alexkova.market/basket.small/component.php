<?
use Alexkova\Market\Basket;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!CModule::IncludeModule('alexkova.market')) return;
if (!CModule::IncludeModule("sale") || !CModule::IncludeModule("iblock") || !CModule::IncludeModule("catalog"))
{
	ShowError(GetMessage("SALE_MODULE_NOT_INSTALL"));
	return;
}

$arResult = array();

global $eMarketBasketData;
$eMarketBasketData = array(
	"DELAY"=>array(),
	"ITEMS"=>array(),
        "FAVOR"=>array(),
	"ALL"=>array()
);

$itemID = intval($_REQUEST["item"]);
if ($itemID>0){
        $provider = "";
        if(isset($arParams["PRODUCT_PROVIDER_CLASS"]) && !empty($arParams["PRODUCT_PROVIDER_CLASS"]))
            $provider = $arParams["PRODUCT_PROVIDER_CLASS"];

	switch($_REQUEST["action"]){
		case "add":

			$delayed = false;
			if (isset($_REQUEST["delay"]) && $_REQUEST["delay"]=="yes"){
				$delayed = true;
			}
                        if (!empty($_REQUEST["BASKET_PROPS"]) && is_array($_REQUEST["BASKET_PROPS"]))
                        {
                            foreach ($_REQUEST["BASKET_PROPS"] as $k => $arOneProductParams)
                            {
                                $_REQUEST["BASKET_PROPS"][$k]["NAME"] = iconv("UTF-8", LANG_CHARSET, urldecode($arOneProductParams["NAME"]));
                                $_REQUEST["BASKET_PROPS"][$k]["VALUE"] = iconv("UTF-8", LANG_CHARSET, urldecode($arOneProductParams["VALUE"]));
                            }
                        }
			Alexkova\Market\Basket::addToBasket($itemID, floatval($_REQUEST["quantity"]), $delayed, $_REQUEST["BASKET_PROPS"], $provider);
                        $arResult["PRODUCT_INFO"] = Alexkova\Market\Basket::getPorductInfoByProductId($itemID);
                        $arResult["PRODUCT_INFO"]["DELAY"] = $delayed;
			break;
		case "delete":
			echo CSaleBasket::Delete($itemID);
			break;
		case "delay":
			Alexkova\Market\Basket::delayItem($itemID, $provider);
			break;
                case "favor":
			$favorList = Alexkova\Market\Basket::favorItem($itemID);
			break;
		case "back":
			Alexkova\Market\Basket::toCart($itemID, $provider);
			break;
		case "newqty":
			Alexkova\Market\Basket::newQty($itemID, floatval($_REQUEST["quantity"]), $provider);
			break;
	}
}

if (!is_array($favorList) || empty($favorList))
    $favorList = Alexkova\Market\Basket::favorItem();

$arResult["UFAVOR"] = $favorList;

$arFavorList = explode("#", $favorList);
$curFavorList = array_diff($arFavorList, array(''));
if (count($curFavorList) > 0) {
    $filter = array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ID" => $curFavorList);
    $select = array("ID", "NAME", "CODE", "DETAIL_PICTURE", "DETAIL_PAGE_URL", "PREVIEW_PICTURE");
    $res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $filter, false, false, $select);
    while($arElement = $res->GetNext())
    {
        if ($arElement["DETAIL_PICTURE"])
            $picture = CFile::GetFileArray($arElement["DETAIL_PICTURE"]);
        else
            $picture = CFile::GetFileArray($arElement["PREVIEW_PICTURE"]);
        $arResult["FAVOR_ITEMS"][] = array(
            "ID" => $arElement["ID"],
            "NAME" => $arElement["NAME"],
            "PICTURE" => $picture["SRC"],
            "URL" => $arElement["DETAIL_PAGE_URL"]
                );
        $eMarketBasketData["FAVOR"][] = $arElement["ID"];
    }
}

$arParams["PATH_TO_BASKET"] = Trim($arParams["PATH_TO_BASKET"]);
$arParams["PATH_TO_ORDER"] = Trim($arParams["PATH_TO_ORDER"]);

$newBasketUserID = CSaleBasket::GetBasketUserID(true);

global $ratio_settings, $bxr_ratio_prop_code;
$ratio_settings = COption::GetOptionString('alexkova.market', "ratio_settings","none");
$bxr_ratio_prop_code = COption::GetOptionString('alexkova.market', "bxr_ratio_prop_code");

if($newBasketUserID>0){
	$rsBaskets = CSaleBasket::GetList(
		array("ID" => "ASC"),
		array("FUSER_ID" => $newBasketUserID, "LID" => SITE_ID, "ORDER_ID" => "NULL"),
		false,
		false,
		array(
			"ID", "NAME", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY",
                        "PRICE", "WEIGHT", "DETAIL_PAGE_URL", "NOTES", "CURRENCY", "VAT_RATE", "CATALOG_XML_ID",
                        "PRODUCT_XML_ID", "SUBSCRIBE", "DISCOUNT_PRICE", "PRODUCT_PROVIDER_CLASS", "TYPE", "SET_PARENT_ID"
		)
	);
	$arPrice = 0;
	$arDelayPrice = 0;
	$tmpCurrency = "";
        $allWeight = 0;
	$arTradeList = array();
	while ($arItem = $rsBaskets->GetNext())
	{
                if (CSaleBasketHelper::isSetItem($arItem))
                    continue;
                
		if (!isset($arTradeList[$arItem["PRODUCT_ID"]])){
			$arTradeList[$arItem["PRODUCT_ID"]] = CCatalogProduct::GetByIDEx($arItem["PRODUCT_ID"]);
			if ($arTradeList[$arItem["PRODUCT_ID"]]["DETAIL_PICTURE"]>0){
				$arTradeList[$arItem["PRODUCT_ID"]]["DETAIL_PICTURE"] = CFile::GetFileArray($arTradeList[$arItem["PRODUCT_ID"]]["DETAIL_PICTURE"]);
			}
		}

		if (is_array($arTradeList[$arItem["PRODUCT_ID"]]["DETAIL_PICTURE"]))
			$arItem["PICTURE"] = $arTradeList[$arItem["PRODUCT_ID"]]["DETAIL_PICTURE"]["SRC"];

		if (
			isset($arTradeList[$arItem["PRODUCT_ID"]]["PROPERTIES"]["CML2_LINK"]["VALUE"]) &&
			$arTradeList[$arItem["PRODUCT_ID"]]["PROPERTIES"]["CML2_LINK"]["VALUE"]>0
		){
			$arItem["PARENT"] = $arTradeList[$arItem["PRODUCT_ID"]]["PROPERTIES"]["CML2_LINK"]["VALUE"];
			if (!isset($arTradeList[$arTradeList[$arItem["PRODUCT_ID"]]["PROPERTIES"]["CML2_LINK"]["VALUE"]])){
				$arTradeList[$arTradeList[$arItem["PRODUCT_ID"]]["PROPERTIES"]["CML2_LINK"]["VALUE"]] = CCatalogProduct::GetByIDEx($arTradeList[$arItem["PRODUCT_ID"]]["PROPERTIES"]["CML2_LINK"]["VALUE"]);
				if ($arTradeList[$arTradeList[$arItem["PRODUCT_ID"]]["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_PICTURE"]>0){
					$arTradeList[$arTradeList[$arItem["PRODUCT_ID"]]["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_PICTURE"] = CFile::GetFileArray($arTradeList[$arTradeList[$arItem["PRODUCT_ID"]]["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_PICTURE"]);
				}
			}
			$arTradeList[$arItem["PRODUCT_ID"]]["PARENT"] = $arTradeList[$arItem["PRODUCT_ID"]]["PROPERTIES"]["CML2_LINK"]["VALUE"];
			if (!isset($arItem["PICTURE"]) && (is_array($arTradeList[$arTradeList[$arItem["PRODUCT_ID"]]["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_PICTURE"])))
				$arItem["PICTURE"] = $arTradeList[$arTradeList[$arItem["PRODUCT_ID"]]["PROPERTIES"]["CML2_LINK"]["VALUE"]]["DETAIL_PICTURE"]["SRC"];
		}
                
                $arItem = getMeasures(array($arItem));
                $arItem = getRatio($arItem);
                $arItem = current($arItem);

                $arItem["RATIO"] = 1;
                $arItem["START_QTY"] = 1;
                $prodQty = $arTradeList[$arItem["PRODUCT_ID"]]['PRODUCT']["QUANTITY"];
                
                if($ratio_settings == "own_prop" && strlen($bxr_ratio_prop_code)>0)
                    $ratioPropValue = $arTradeList[$arItem["PRODUCT_ID"]]["PROPERTIES"][$bxr_ratio_prop_code]["VALUE"];
                elseif($ratio_settings == "base")
                    $ratioPropValue = $arItem["MEASURE_RATIO"];
                
                if(isset($ratioPropValue) && is_numeric($ratioPropValue))
                {
                    $arItem["RATIO"] = $ratioPropValue;
                    $arItem["START_QTY"] = ($arItem["RATIO"] > $prodQty && $prodQty>0) ? $prodQty : $arItem["RATIO"];
                }
                $arItem["QTY_MAX"] = ($arTradeList[$arItem["PRODUCT_ID"]]['PRODUCT']['CAN_BUY_ZERO'] == "Y") ? 0 : $prodQty;

		$arItem["URL"] = $arItem["DETAIL_PAGE_URL"];
		if ($arItem["PARENT"]>0)
			$arItem["URL"] = $arTradeList[$arItem["PARENT"]]["DETAIL_PAGE_URL"];
                
                /*start basket props*/
                $propsIterator = CSaleBasket::GetPropsList(
                    array('SORT' => 'ASC', 'ID' => 'ASC'),
                    array('BASKET_ID' => $arItem["ID"])
                );
                while ($property = $propsIterator->GetNext())
                {
                    $property['CODE'] = (string)$property['CODE'];
                    if ($property['CODE'] == 'CATALOG.XML_ID' || $property['CODE'] == 'PRODUCT.XML_ID' || $property['CODE'] == 'SUM_OF_CHARGE')
                            continue;
                    $arItem['PROPS'][] = $property;
                }
                /*end basket props*/

		if ($arItem["CAN_BUY"] == "Y" && $arItem["DELAY"] == "N"){
			$arBasketItems["CAN_BUY"][] = $arItem;
                        $allWeight += ($arItem["WEIGHT"] * $arItem["QUANTITY"]);

			$eMarketBasketData["ITEMS"][] = $arItem["PRODUCT_ID"];
			$eMarketBasketData["ALL"][$arItem["PRODUCT_ID"]] = $arItem["QUANTITY"];
			$eMarketBasketData["BASKET"][$arItem["PRODUCT_ID"]] = $arItem["ID"];
                }
                elseif($arItem["CAN_BUY"] == "Y" && $arItem["DELAY"] == "Y")
		{
			$arBasketItems["DELAY"][] = $arItem;
			$arDelayPrice += $arItem["PRICE"]*$arItem["QUANTITY"];
			$eMarketBasketData["DELAY"][] = $arItem["PRODUCT_ID"];
			$eMarketBasketData["ALL"][$arItem["PRODUCT_ID"]] = $arItem["QUANTITY"];
			$eMarketBasketData["BASKET"][$arItem["PRODUCT_ID"]] = $arItem["ID"];
		}
		$tmpCurrency = $arItem["CURRENCY"];
	}
}

$arOrder = array(
        'SITE_ID' => SITE_ID,
        'USER_ID' => $GLOBALS["USER"]->GetID(),
        'ORDER_PRICE' => $arPrice,
        'ORDER_WEIGHT' => $allWeight,
        'BASKET_ITEMS' => $arBasketItems['CAN_BUY']
);

$arOptions = array(
        'COUNT_DISCOUNT_4_ALL_QUANTITY' => $arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] ? $arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] : 'N',
);
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

$arResult["BASKET_ITEMS"] = $arBasketItems;
$arResult["BASKET_ITEMS"]['CAN_BUY'] = $arOrder['BASKET_ITEMS'];
$arResult["CATALOG"] = $arTradeList;
$arResult["SUMM"] = $arOrder['ORDER_PRICE'];
$arResult["FORMAT_SUMM"] = SaleFormatCurrency($arResult["SUMM"], $tmpCurrency);
$arResult["DELAY_SUMM"] = $arDelayPrice;
$arResult["FORMAT_DELAY_SUMM"] = SaleFormatCurrency($arResult["DELAY_SUMM"], $tmpCurrency);

$arResult["CURRENCY_FORMAT"] = CCurrencyLang::GetFormatDescription($tmpCurrency);
$arResult["CURRENCY_FORMAT"] = rtrim($arResult["CURRENCY_FORMAT"]["FORMAT_STRING"], '.');

$arResult["MIN_ORDER_PRICE"] = COption::GetOptionString("alexkova.market", "bxr_min_order_price");
$arResult["MIN_ORDER_PRICE_FORMATED"] = str_replace('#', $arResult["MIN_ORDER_PRICE"], $arResult["CURRENCY_FORMAT"]);
$addPrice = round($arResult["MIN_ORDER_PRICE"] - $arResult["SUMM"], 2);
$arResult["ADD_PRICE_FORMATED"] = str_replace('#', $addPrice, $arResult["CURRENCY_FORMAT"]);

$optionMsg = COption::GetOptionString("alexkova.market", "bxr_min_order_price_msg");
$arResult["MIN_ORDER_PRICE_MSG"] = ($optionMsg != "") ? $optionMsg : GetMessage("MIN_ORDER_PRICE_MSG_DEF");
$arResult["MIN_ORDER_PRICE_MSG_FLAGS"] = $arResult["MIN_ORDER_PRICE_MSG"];
$arResult["MIN_ORDER_PRICE_MSG"] = str_replace("#MIN_ORDER_PRICE#", $arResult["MIN_ORDER_PRICE_FORMATED"], $arResult["MIN_ORDER_PRICE_MSG"]);
$arResult["MIN_ORDER_PRICE_MSG"] = str_replace("#ADD_ORDER_PRICE#", $arResult["ADD_PRICE_FORMATED"], $arResult["MIN_ORDER_PRICE_MSG"]);

$this->IncludeComponentTemplate($currentTemplate);
