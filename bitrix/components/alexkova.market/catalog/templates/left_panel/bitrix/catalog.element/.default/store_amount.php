<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->IncludeComponent(
        "bitrix:catalog.store.amount", 
        "bxr-market", 
        array(
                "PER_PAGE" => "10",
                "USE_STORE_PHONE" => $arParams["PHONE"],
                "SCHEDULE" => $arParams["STORE_DETAIL"]["USE_STORE_SCHEDULE"],
                "USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
                "MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
                "ELEMENT_ID" => $arResult["ID"],
                "STORE_PATH"  =>  "/company/store/#store_id#/",
                "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                "CACHE_TIME" => $arParams["CACHE_TIME"],
                "STORES" => $arParams["STORES"],
                "SHOW_EMPTY_STORE" => $arParams["SHOW_EMPTY_STORE"],
                "SHOW_GENERAL_STORE_INFORMATION" => $arParams["SHOW_GENERAL_STORE_INFORMATION"],
                "FIELDS" => $arParams["FIELDS"],
                "USER_FIELDS" => $arParams["USER_FIELDS"],
                "MAIN_TITLE" => $arParams["MAIN_TITLE"]
        ),
        $component,
        array("HIDE_ICONS" => "Y")
);
