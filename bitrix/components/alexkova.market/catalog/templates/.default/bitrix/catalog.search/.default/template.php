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
$this->setFrameMode(false);
?>
<?
$arElements = $APPLICATION->IncludeComponent(
	"bitrix:search.page",
	".default",
	Array(
		"RESTART" => $arParams["RESTART"],
		"NO_WORD_LOGIC" => $arParams["NO_WORD_LOGIC"],
		"USE_LANGUAGE_GUESS" => $arParams["USE_LANGUAGE_GUESS"],
		"CHECK_DATES" => $arParams["CHECK_DATES"],
		"arrFILTER" => array("iblock_".$arParams["IBLOCK_TYPE"]),
		"arrFILTER_iblock_".$arParams["IBLOCK_TYPE"] => array($arParams["IBLOCK_ID"]),
		"USE_TITLE_RANK" => "N",
		"DEFAULT_SORT" => "rank",
		"FILTER_NAME" => "",
		"SHOW_WHERE" => "N",
		"arrWHERE" => array(),
		"SHOW_WHEN" => "N",
		"PAGE_RESULT_COUNT" => 50,
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => "",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "N",
	),
	$component
);
if (!empty($arElements) && is_array($arElements))
{

		if (strlen(COption::GetOptionString('alexkova.market', 'list_marker_type'))>0){
			$bxreadyMarkers = COption::GetOptionString('alexkova.market', 'list_marker_type');
		}else{
			$bxreadyMarkers = $arParams["BXREADY_LIST_MARKER_TYPE"];
		}

	$arDefaultResponsive = array(
		"LG" => 3,
		"MD" => 3,
		"SM" => 4,
		"XS" => 6
	);

	$elementLibrary = "system#ecommerce_v1";
	$arResponsiveParams = $arDefaultResponsive;

        $module_id = "alexkova.market";
        $managment_element_mode = COption::GetOptionString($module_id, "managment_element_mode", "N");

        if ($managment_element_mode == "Y") {
            $ownOptElementLib = COption::GetOptionString($module_id, "own_catalog_list_element_type_".SITE_TEMPLATE_ID, "ecommerce.v2.lite");
            if (strlen($ownOptElementLib) > 0) {
                $elementLibrary = trim($ownOptElementLib); 
            } else {
                $optElementLib = COption::GetOptionString($module_id, "catalog_list_element_type_".SITE_TEMPLATE_ID, "ecommerce.v2.lite");
                if (strlen($optElementLib) > 0) {
                    $elementLibrary = $optElementLib;
                } else {
                    $elementLibrary = "ecommerce.v2.lite";
                }
            }
            $arResponsiveParams["LG"] = COption::GetOptionString($module_id, "catalog_list_element_count_lg_".SITE_TEMPLATE_ID, 4);
            $arResponsiveParams["MD"] = COption::GetOptionString($module_id, "catalog_list_element_count_md_".SITE_TEMPLATE_ID, 3);
            $arResponsiveParams["SM"] = COption::GetOptionString($module_id, "catalog_list_element_count_sm_".SITE_TEMPLATE_ID, 2);
            $arResponsiveParams["XS"] = COption::GetOptionString($module_id, "catalog_list_element_count_xs_".SITE_TEMPLATE_ID, 1);
        } else {
            $elementLibrary = "ecommerce.v2.lite";
            $arResponsiveParams["LG"] = 3;
            $arResponsiveParams["MD"] = 4;
            $arResponsiveParams["SM"] = 6;
            $arResponsiveParams["XS"] = 6;
        }
        

		global $searchFilter;
		$searchFilter = array(
			"=ID" => $arElements,
		);
		$APPLICATION->IncludeComponent(
		"bxready:ecommerce.list",
		".default",
		array(
			"SHOW_CATALOG_QUANTITY_CNT" => $arParams["SHOW_CATALOG_QUANTITY_CNT"],
      "SHOW_CATALOG_QUANTITY" => $arParams["SHOW_CATALOG_QUANTITY"],
      "QTY_SHOW_TYPE" => $arParams["QTY_SHOW_TYPE"],
      "IN_STOCK" => $arParams["IN_STOCK"],
      "NOT_IN_STOCK" => $arParams["NOT_IN_STOCK"],
      "QTY_MANY_GOODS_INT" => $arParams["QTY_MANY_GOODS_INT"],
      "QTY_MANY_GOODS_TEXT" => $arParams["QTY_MANY_GOODS_TEXT"],
      "QTY_LESS_GOODS_TEXT" => $arParams["QTY_LESS_GOODS_TEXT"],
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			/*"ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
			"ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
			"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
			"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],*/
			"PAGE_ELEMENT_COUNT" => 50,
			"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
			"PROPERTY_CODE" => $arParams["PROPERTY_CODE"],
			"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
			"OFFERS_FIELD_CODE" => $arParams["OFFERS_FIELD_CODE"],
			"OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
			/*"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
			"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
			"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
			"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],*/
            "MY_SORT" => $arElements,
			"OFFERS_LIMIT" => $arParams["OFFERS_LIMIT"],
			"SECTION_URL" => $arParams["SECTION_URL"],
			"DETAIL_URL" => $arParams["DETAIL_URL"],
			"BASKET_URL" => $arParams["BASKET_URL"],
			"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
			"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
			"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
			"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
			"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"DISPLAY_COMPARE" => $arParams["DISPLAY_COMPARE"],
			"PRICE_CODE" => $arParams["PRICE_CODE"],
			"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
			"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
			"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
			"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
			"USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
			"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
			"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
			"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
			"CURRENCY_ID" => $arParams["CURRENCY_ID"],
			"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
			"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
			"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
			"PAGER_TITLE" => $arParams["PAGER_TITLE"],
			"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
			"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
			"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
			"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
			"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
			"FILTER_NAME" => "searchFilter",
			"SECTION_ID" => "",
			"SECTION_CODE" => "",
			"SECTION_USER_FIELDS" => array(),
			"INCLUDE_SUBSECTIONS" => "Y",
			"SHOW_ALL_WO_SECTION" => "Y",
			"META_KEYWORDS" => "",
			"META_DESCRIPTION" => "",
			"BROWSER_TITLE" => "",
			"ADD_SECTIONS_CHAIN" => "N",
			"SET_TITLE" => "N",
			"SET_STATUS_404" => "N",
			"CACHE_FILTER" => "N",
			"CACHE_GROUPS" => "N",

			'LABEL_PROP' => $arParams['LABEL_PROP'],
			'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
			'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],

			'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
			'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
			'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
			'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
			'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
			'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
			'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
			'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
			'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
			'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],

			'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
			"BXREADY_LIST_BOOTSTRAP_GRID_STYLE" => "12",
			"BXREADY_LIST_PAGE_BLOCK_TITLE" => "",
			"BXREADY_LIST_PAGE_BLOCK_TITLE_GLYPHICON" => "",
			"BXREADY_LIST_LG_CNT" => $arResponsiveParams["LG"],
			"BXREADY_LIST_MD_CNT" => $arResponsiveParams["MD"],
			"BXREADY_LIST_SM_CNT" => $arResponsiveParams["SM"],
			"BXREADY_LIST_XS_CNT" => $arResponsiveParams["XS"],
			"BXREADY_LIST_SLIDER" => "N",
			"BXREADY_ELEMENT_DRAW" => $elementLibrary,
			"BXREADY_LIST_VERTICAL_SLIDER_MODE" => "N",
			"BXREADY_LIST_HIDE_SLIDER_ARROWS" => "Y",
			"BXREADY_LIST_HIDE_MOBILE_SLIDER_ARROWS" => "N",
			"BXREADY_LIST_MARKER_TYPE" => $bxreadyMarkers,
                        "TILE_SHOW_PROPERTIES" => $arParams["TILE_SHOW_PROPERTIES"]

		),
		$component,
		array("HIDE_ICONS"=>"Y")
	);
}
else
{
	echo GetMessage("CT_BCSE_NOT_FOUND");
}
?>