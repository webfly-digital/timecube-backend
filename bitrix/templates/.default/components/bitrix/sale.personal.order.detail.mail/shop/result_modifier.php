<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
foreach ($arResult["BASKET"] as $key => $prod) {
    $src = CFile::GetPath($prod["PROPERTY_SS_DETAIL_PHOTO_VALUE"]);
    $arResult["BASKET"][$key]['PICTURE']['SRC'] =  $src;
    $item = CCatalogProduct::GetByID($prod['PRODUCT_ID']);
    if ($arResult["BASKET"][$key]['BASE_PRICE'] == $arResult["BASKET"][$key]['PRICE']) {
        $discountProp = CIBlockElement::GetProperty(WF_CATALOG_IBLOCK_ID, $prod['PRODUCT_ID'], [],['CODE'=>'XXX_1C_PROD_DISCOUNT_PRC'])->Fetch();
        if ($discountProp)
            if ($discountProp['VALUE']) {
                $arResult["BASKET"][$key]['BASE_PRICE'] = $arResult["BASKET"][$key]['PRICE'];
                $arResult["BASKET"][$key]['PRICE'] = round($arResult["BASKET"][$key]['PRICE'] / 100 * $discountProp['VALUE']);
                $discountProp = null;
            }
    }

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