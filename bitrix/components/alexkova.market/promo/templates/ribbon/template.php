<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
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

$arParams['SET_TITLE'] = 'N';
$arParams["SET_BROWSER_TITLE"] = 'N';
$arParams["SET_META_DESCRIPTION"] = 'N';
$arParams["SET_META_KEYWORDS"] = 'N';
$arParams["ADD_SECTIONS_CHAIN"] = 'N';
$arParams["INCLUDE_IBLOCK_INTO_CHAIN"] = 'N';

$APPLICATION->IncludeComponent(
        "bitrix:news.list", 
        "", 
        $arParams, 
        $component
);

