<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if ($arParams['USE_GIFTS_DETAIL'] == 'Y' && \Bitrix\Main\ModuleManager::isModuleInstalled("sale"))
{
    $APPLICATION->IncludeComponent("bitrix:sale.gift.product", "detail", array(
        'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
        'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
        'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE'],
        'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
        'SUBSCRIBE_URL_TEMPLATE' => $arResult['~SUBSCRIBE_URL_TEMPLATE'],
        'COMPARE_URL_TEMPLATE' => $arResult['~COMPARE_URL_TEMPLATE'],

        "SHOW_DISCOUNT_PERCENT" => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
        "SHOW_OLD_PRICE" => $arParams['GIFTS_SHOW_OLD_PRICE'],
        "PAGE_ELEMENT_COUNT" => $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],
        "LINE_ELEMENT_COUNT" => $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],
        "HIDE_BLOCK_TITLE" => $arParams['GIFTS_DETAIL_HIDE_BLOCK_TITLE'],
        "BLOCK_TITLE" => $arParams['GIFTS_DETAIL_BLOCK_TITLE'],
        "TEXT_LABEL_GIFT" => $arParams['GIFTS_DETAIL_TEXT_LABEL_GIFT'],
        "SHOW_NAME" => "Y",
        "SHOW_IMAGE" => "Y",
        "MESS_BTN_BUY" => $arParams['GIFTS_MESS_BTN_BUY'],

        "SHOW_PRODUCTS_{$arParams['IBLOCK_ID']}" => "Y",
        "HIDE_NOT_AVAILABLE" => $arParams["GIFTS_HIDE_NOT_AVAILABLE"],
        "PRODUCT_SUBSCRIPTION" => $arParams["PRODUCT_SUBSCRIPTION"],
        "MESS_BTN_DETAIL" => $arParams["MESS_BTN_DETAIL"],
        "MESS_BTN_SUBSCRIBE" => $arParams["MESS_BTN_SUBSCRIBE"],
        "TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
        "PRICE_CODE" => $arParams["PRICE_CODE"],
        "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
        "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
        "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
        "BASKET_URL" => $arParams["BASKET_URL"],
        "ADD_PROPERTIES_TO_BASKET" => $arParams["ADD_PROPERTIES_TO_BASKET"],
        "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
        "PARTIAL_PRODUCT_PROPERTIES" => $arParams["PARTIAL_PRODUCT_PROPERTIES"],
        "USE_PRODUCT_QUANTITY" => 'N',
        "OFFER_TREE_PROPS_{$arResult['OFFERS_IBLOCK']}" => $arParams['OFFER_TREE_PROPS'],
        "CART_PROPERTIES_{$arResult['OFFERS_IBLOCK']}" => $arParams['OFFERS_CART_PROPERTIES'],
        "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
        "POTENTIAL_PRODUCT_TO_BUY" => array(
        'ID' => isset($arResult['ID']) ? $arResult['ID'] : null,
        'MODULE' => isset($arResult['MODULE']) ? $arResult['MODULE'] : 'catalog',
        'PRODUCT_PROVIDER_CLASS' => isset($arResult['PRODUCT_PROVIDER_CLASS']) ? $arResult['PRODUCT_PROVIDER_CLASS'] : 'CCatalogProductProvider',
        'QUANTITY' => isset($arResult['QUANTITY']) ? $arResult['QUANTITY'] : null,
        'IBLOCK_ID' => isset($arResult['IBLOCK_ID']) ? $arResult['IBLOCK_ID'] : null,

        'PRIMARY_OFFER_ID' => isset($arResult['OFFERS'][0]['ID']) ? $arResult['OFFERS'][0]['ID'] : null,
        'SECTION' => array(
        'ID' => isset($arResult['SECTION']['ID']) ? $arResult['SECTION']['ID'] : null,
        'IBLOCK_ID' => isset($arResult['SECTION']['IBLOCK_ID']) ? $arResult['SECTION']['IBLOCK_ID'] : null,
        'LEFT_MARGIN' => isset($arResult['SECTION']['LEFT_MARGIN']) ? $arResult['SECTION']['LEFT_MARGIN'] : null,
        'RIGHT_MARGIN' => isset($arResult['SECTION']['RIGHT_MARGIN']) ? $arResult['SECTION']['RIGHT_MARGIN'] : null,
        ),
        )
    ), $component, array("HIDE_ICONS" => "Y"));
}
