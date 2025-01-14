<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */

    foreach ($arResult["ITEMS"] as $key => $arItem) {
        $arResult['ITEMS'][$key]['CATALOG_BANNER'] = CFile::GetPath($arItem['PROPERTIES']['CATALOG_BANNER']["VALUE"]);
    }
