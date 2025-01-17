<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
use Alexkova\Market\Core;
$BXReady = \Alexkova\Market\Core::getInstance();
$this->addExternalCss("/bitrix/css/main/bootstrap.css");
?>

<?$BXReady->showBannerPlace("CATALOG_TOP");?>

<?
if (isset($arResult["VARIABLES"]["SECTION_CODE"]) && strlen($arResult["VARIABLES"]["SECTION_CODE"])>0)
{
    global $arrFilter;
    $arrFilter["PROPERTY_MANUFACTURER"] = strval($arResult["VARIABLES"]["SECTION_CODE"]);

    echo "Set Concretical Pro";
}
?>

<?$APPLICATION->IncludeComponent(
    "alexkova.market:catalog.brandblock",
    "brandpage",
    array(
            "IBLOCK_TYPE" => "catalog",
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "ELEMENT_ID" => $arResult["ELEMENT_ID"],
            "ELEMENT_CODE" => $arResult["ELEMENT_CODE"],
            "PROP_CODE" => "MANUFACTURER",
            "WIDTH" => "150",
            "HEIGHT" => "80",
            "WIDTH_SMALL" => "150",
            "HEIGHT_SMALL" => "80",
            "CACHE_TYPE" => "N",
            "CACHE_TIME" => "3600",
            "CACHE_GROUPS" => "Y"
    ),
    false,
    array('HIDE_ICONS'=>"Y")
);?>

<?$BXReady->showBannerPlace("CATALOG_BOTTOM");?>

