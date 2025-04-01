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
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
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
			"VIEW_MODE" => $arParams["SECTIONS_VIEW_MODE"],
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