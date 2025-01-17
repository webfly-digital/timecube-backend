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
<div class="col-lg-3 col-md-3 hidden-sm hidden-xs">
    <?if (isset($arParams["SHOW_LEFT_MENU"]) && $arParams["SHOW_LEFT_MENU"]=="Y"):
	$APPLICATION->IncludeComponent(
		"alexkova.market:menu",
		isset($arParams["LEFT_MENU_TEMPLATE"]) ? $arParams["LEFT_MENU_TEMPLATE"] : "left",
		array(
                    "ROOT_MENU_TYPE" => isset($arParams["ROOT_MENU_TYPE"]) ? $arParams["ROOT_MENU_TYPE"] : "left",
                    "MAX_LEVEL" => isset($arParams["MAX_LEVEL"]) ? $arParams["MAX_LEVEL"] : "2",
                    "CHILD_MENU_TYPE" => isset($arParams["CHILD_MENU_TYPE"]) ? $arParams["CHILD_MENU_TYPE"] : "left",
                    "USE_EXT" => isset($arParams["USE_EXT"]) ? $arParams["USE_EXT"] : "Y",
                    "DELAY" => isset($arParams["DELAY"]) ? $arParams["DELAY"] : "N",
                    "TITLE_MENU" => isset($arParams["TITLE_MENU"]) ? $arParams["TITLE_MENU"] : "",                    
                    "STYLE_MENU" => isset($arParams["STYLE_MENU"]) ? $arParams["STYLE_MENU"] : "colored_light", 			
                    "PICTURE_SECTION" => isset($arParams["PICTURE_SECTION"]) ? $arParams["PICTURE_SECTION"] : "N", 
                    "STYLE_MENU_HOVER" => isset($arParams["STYLE_MENU_HOVER"]) ? $arParams["STYLE_MENU_HOVER"] : "colored_light",
                    "PICTURE_SECTION_HOVER" => isset($arParams["PICTURE_SECTION_HOVER"]) ? $arParams["PICTURE_SECTION_HOVER"] : "N",
                    "PICTURE_CATEGARIES" => isset($arParams["PICTURE_CATEGARIES"]) ? $arParams["PICTURE_CATEGARIES"] : "N",
                    "HOVER_MENU_COL_LG" => isset($arParams["HOVER_MENU_COL_LG"]) ? $arParams["HOVER_MENU_COL_LG"] : "2",
                    "HOVER_MENU_COL_MD" => isset($arParams["HOVER_MENU_COL_MD"]) ? $arParams["HOVER_MENU_COL_MD"] : "2",
                    "HOVER_MENU_COL_SM" => isset($arParams["HOVER_MENU_COL_SM"]) ? $arParams["HOVER_MENU_COL_SM"] : "2",
                    "HOVER_MENU_COL_XS" => isset($arParams["HOVER_MENU_COL_XS"]) ? $arParams["HOVER_MENU_COL_XS"] : "2",
                    "SUBMENU" => isset($arParams["SUBMENU"]) ? $arParams["SUBMENU"] : "ACTIVE_SHOW",
                    "HOVER_TEMPLATE" => isset($arParams["HOVER_TEMPLATE"]) ? $arParams["HOVER_TEMPLATE"] : "classic",
                    "MENU_CACHE_TYPE" => $arParams["CACHE_TYPE"],
                    "MENU_CACHE_TIME" => $arParams["CACHE_TIME"],
                    "MENU_CACHE_USE_GROUPS" => $arParams["CACHE_GROUPS"],
                    "MENU_CACHE_GET_VARS" => array(
                    ),
                    "SHOW_TREE" => "Y",
		),
		false,
                array("HIDE_ICONS" => "Y")
	);
    endif;

	$BXReady->showBannerPlace("LEFT");

?></div>
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
	<?$BXReady->showBannerPlace("CATALOG_TOP");?>
	<h1><?$APPLICATION->ShowTitle(false);?></h1>
	<?$APPLICATION->IncludeComponent(
		"bitrix:catalog.section.list",
		"catalog_index",
		array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			"COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
			"TOP_DEPTH" => $arParams["SECTION_TOP_DEPTH"],
			"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
			"VIEW_MODE" => $arParams["SECTIONS_MAIN_VIEW_MODE"],
			"SHOW_PARENT_NAME" => $arParams["SECTIONS_SHOW_PARENT_NAME"],
			"HIDE_SECTION_NAME" => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"]) ? $arParams["SECTIONS_HIDE_SECTION_NAME"] : "N"),
			"ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : ''),
                        "SECTIONS_SHOW_DESCRIPTION" => (isset($arParams["SECTIONS_SHOW_DESCRIPTION"]) ? $arParams["SECTIONS_SHOW_DESCRIPTION"] : 'Y')
		),
		$component,
		array("HIDE_ICONS" => "Y")
	);
	
	$BXReady->showBannerPlace("CATALOG_BOTTOM");?>
</div>