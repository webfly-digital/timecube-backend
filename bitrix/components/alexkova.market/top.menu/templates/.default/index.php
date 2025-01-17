<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?//$this->setFrameMode(true);?>
<?$mainPage = str_replace("//","/", SITE_DIR.'/index.php');?>
<?
if(!isset($arParams['VARIANT_MENU']))
    $arParams['VARIANT_MENU'] = "version_v1";

if(!isset($arParams['STYLE_MENU']))
    $arParams['STYLE_MENU'] = "colored_color";

if(!isset($arParams['FULL_WIDTH']))
    $arParams['FULL_WIDTH'] = "Y";

if(!isset($arParams['SEARCH_FORM']))
    $arParams['SEARCH_FORM'] = "N";

if(!isset($arParams['PICTURE_SECTION']))
    $arParams['PICTURE_SECTION'] = "N";

if(!isset($arParams['STYLE_MENU_HOVER']))
    $arParams['STYLE_MENU_HOVER'] = "colored_light";

?>
<?$APPLICATION->IncludeComponent(
    "alexkova.market:menu",
    $arParams['VARIANT_MENU'],
    Array(
            "ROOT_MENU_TYPE" => "top",
            "MENU_CACHE_TYPE" => $arParams["CACHE_TYPE"],
            "MENU_CACHE_TIME" => $arParams["CACHE_TIME"],
            "MENU_CACHE_USE_GROUPS" => "Y",
            "MENU_CACHE_GET_VARS" => array(""),
            "MAX_LEVEL" => "2",
            "CHILD_MENU_TYPE" => "left",
            "USE_EXT" => "Y",
            "DELAY" => "N",
            "ALLOW_MULTI_SELECT" => "N",
            "FULL_WIDTH" => $arParams['FULL_WIDTH'],
            "VARIANT_MENU" => $arParams['VARIANT_MENU'],
            "STYLE_MENU" => $arParams['STYLE_MENU'],
            "SEARCH_FORM" => $arParams['SEARCH_FORM'],
            "STYLE_MENU_HOVER" => $arParams['STYLE_MENU_HOVER'],
            "PICTURE_SECTION" => $arParams['PICTURE_SECTION'],
            "SHOW_TREE" => "Y",
    ),
    $component,
    array("HIDE_ICONS" => "Y")
);?>
