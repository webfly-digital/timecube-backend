<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */

foreach ($arResult["ITEMS"] as $key => $arItem)
{
    $propertyRes = CIBlockElement::GetProperty(
        $arItem["IBLOCK_ID"],
        $arItem["ID"],
        ["sort" => "asc"],
        ["CODE" => "HIDDEN_PROMO"]
    );

    if ($arProperty = $propertyRes->Fetch())
    {
        $arResult["ITEMS"][$key]["PROPERTIES"]["HIDDEN_PROMO"]["VALUE"]        = $arProperty["VALUE"];
        $arResult["ITEMS"][$key]["PROPERTIES"]["HIDDEN_PROMO"]["VALUE_XML_ID"] = $arProperty["VALUE_XML_ID"];
    }
}