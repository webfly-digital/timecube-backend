<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (is_array($_SESSION["BXR_BESTSELLER_SETTINGS"])
	&& !empty($_SESSION["BXR_BESTSELLER_SETTINGS"])
	&& count($arResult["BESTSELLERS_ITEMS"])>0):

	$arParams = $_SESSION["BXR_BESTSELLER_SETTINGS"];

	$recomCacheIDs = $arResult["BESTSELLERS_ITEMS"];

	global $arrFilter;
	$arrFilter["ID"] = $recomCacheIDs;

	$APPLICATION->IncludeComponent(
		"bitrix:catalog.section",
		"",
		$arParams,
		$component,
		array('HIDE_ICONS' => 'Y')
	);
	


endif;