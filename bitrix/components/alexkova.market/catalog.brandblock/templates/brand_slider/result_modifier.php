<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if ($arResult["BRAND_BLOCKS"] && count($arResult["BRAND_BLOCKS"])>0 && $arParams["BRAND_SHUFFLE"]=="Y")
	shuffle($arResult["BRAND_BLOCKS"]);