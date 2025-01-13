<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Google Merchant");
global $arrFilter;
$arrFilter['AVAILABLE'] = 'Y';
$arrFilter['!PROPERTY_XXX_SHOW_ON_TIMECUBE_RU'] = false;
?>
<?
$APPLICATION->IncludeComponent(
	"webfly:google.merchant", 
	".default", 
	array(
		"IBLOCK_TYPE" => "1c_catalog",
		"IBLOCK_ID_IN" => array(
			0 => "10",
		),
		"IBLOCK_ID_EX" => array(
			0 => "0",
		),
		"IBLOCK_SECTION" => array(
			0 => "87",
			1 => "88",
			2 => "90",
			3 => "91",
			4 => "92",
			5 => "97",
			6 => "98",
			7 => "100",
			8 => "101",
			9 => "102",
			10 => "103",
			11 => "104",
			12 => "105",
			13 => "108",
			14 => "117",
			15 => "122",
			16 => "138",
		),
		"SITE" => "timecube.ru",
		"COMPANY" => "Интернет-магазин timecube.ru",
		"FILTER_NAME" => "arrFilter",
		"MORE_PHOTO" => "YA_PICTURE",
		"CACHE_TYPE" => "N",
		"CACHE_TIME" => "86400",
		"CACHE_FILTER" => "N",
		"PRICE_CODE" => array(
			0 => "Розничная цена",
		),
		"IBLOCK_ORDER" => "N",
		"CURRENCY" => "RUR",
		"LOCAL_DELIVERY_COST" => "0",
		"COMPONENT_TEMPLATE" => ".default",
		"AGENT_CHECK" => "N",
		"IBLOCK_TYPE_LIST" => array(
			0 => "1c_catalog",
		),
		"SAVE_IN_FILE" => "Y",
		"IBLOCK_CATALOG" => "Y",
		"DO_NOT_INCLUDE_SUBSECTIONS" => "N",
		"IBLOCK_AS_CATEGORY" => "N",
		"BIG_CATALOG_PROP" => "",
		"CACHE_NON_MANAGED" => "N",
		"SKU_NAME" => "SKU_NAME",
		"SKU_PROPERTY" => "PROPERTY_CML2_LINK",
		"PRICE_ROUND" => "N",
		"CURRENCIES_CONVERT" => "NOT_CONVERT",
		"NAME_PROP" => "XXX_YML_MODEL_NAME",
		"DETAIL_TEXT_PRIORITET" => "Y",
		"DEVELOPER" => "MANUFACTUR",
		"MARKET_CATEGORY_CHECK" => "Y",
		"MARKET_CATEGORY_PROP" => "XXX_GOOGLE_PRODUCT_CATEGORY",
		"DISCOUNTS" => "PRICE_ONLY",
		"HTTPS_CHECK" => "Y",
		"USE_SITE" => "N",
		"FILTER_NAME_SKU" => "arrFilterSku",
		"GET_OVER_FIELDS_ANONCE" => "N",
		"PHOTO_CHECK" => "N",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"OLD_PRICE" => "N",
		"AVAILABLE_ALGORITHM" => "BITRIX_ALGORITHM",
		"MINIMUM_PRICE_ROUND" => "0",
		"TYPE_PRICE_ROUND" => "MATH",
		"ACCURACY_PRICE_ROUND" => "9",
		"GTIN" => "0"
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
