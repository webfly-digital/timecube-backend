<?php

function priceIndexAgent()
{

    $res = CIBlockElement::GetList([], ['IBLOCK_ID' => WF_CATALOG_IBLOCK_ID], false, false,
        ['ID', 'NAME', 'PRICE_'.WF_PRICE_ID, 'PROPERTY_XXX_1C_PROD_DISCOUNT_PRC','PROPERTY_SORTPRICE']
    );

    $element = new CIBlockElement;
    while ($arElement = $res->Fetch()) {
        if ($arElement["PROPERTY_XXX_1C_PROD_DISCOUNT_PRC_VALUE"] > 0) {
            $discount = $arElement['PRICE_'.WF_PRICE_ID] / 100 * $arElement["PROPERTY_XXX_1C_PROD_DISCOUNT_PRC_VALUE"];
            $price = $arElement['PRICE_'.WF_PRICE_ID] - $discount;
        } else {
            $price = $arElement['PRICE_'.WF_PRICE_ID];
        }
        $price = intval($price);
        CIBlockElement::SetPropertyValuesEx($arElement['ID'], WF_CATALOG_IBLOCK_ID, ['SORTPRICE'=>$price]);
    }

    return "priceIndexAgent();";

}
