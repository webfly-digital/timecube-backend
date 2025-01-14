<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

foreach ($arResult["BASKET"] as $key => $prod) {
    $item = CCatalogProduct::GetByID($prod['PRODUCT_ID']);
    if ($item) {
        $arResult["BASKET"][$key]['CATALOG_QUANTITY'] = $item['QUANTITY'];
    }
}
if (!empty($arResult["DELIVERY"]['ID'])) {
    $delivery = \Bitrix\Sale\Delivery\Services\Manager::getById($arResult["DELIVERY"]['ID']);
    $arResult['DELIVERY_NAME'] = $delivery['NAME'];
} else {
    $arResult['DELIVERY_NAME'] = $arResult["DELIVERY"]["NAME"];
}