<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Alexkova\Market\Core;

$BXReady = \Alexkova\Market\Core::getInstance();

$elementBlock = 'system#ecommerce_v1';
$elementList = 'system#ecommerce_v1_list';
$elementTable = 'system#ecommerce_v1_table';

$module_id = "alexkova.market";
global $bxreadyMarkers;

if (strlen(COption::GetOptionString('alexkova.market', 'list_marker_type'))>0)
        $bxreadyMarkers = COption::GetOptionString('alexkova.market', 'list_marker_type');
else
        $bxreadyMarkers = $arParams["BXREADY_LIST_MARKER_TYPE"];

// central manage mode
$managment_element_mode = COption::GetOptionString($module_id, "managment_element_mode", "N");
if ($managment_element_mode == "Y") 
{
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
} 
else 
{
    $elementLibrary = "ecommerce.v2.lite";
}

if ($isSidebar):?>
<div class="col-md-3 col-sm-4 pull-right hidden-xs">
	<div class="bx-sidebar-block">
	</div>
	<?$BXReady->showBannerPlace("LEFT");
       
        //bestsallers
        if (ModuleManager::isModuleInstalled("sale"))
	{
		$arRecomData = array();
		$recomCacheID = array('IBLOCK_ID' => $arParams['IBLOCK_ID']);
		$obCache = new CPHPCache();
		if ($obCache->InitCache(36000, serialize($recomCacheID), "/sale/bestsellers"))
		{
			$arRecomData = $obCache->GetVars();
		}
		elseif ($obCache->StartDataCache())
		{
			if (Loader::includeModule("catalog"))
			{
				$arSKU = CCatalogSKU::GetInfoByProductIBlock($arParams['IBLOCK_ID']);
				$arRecomData['OFFER_IBLOCK_ID'] = (!empty($arSKU) ? $arSKU['IBLOCK_ID'] : 0);
			}
			$obCache->EndDataCache($arRecomData);
		}
        };
    
        //viewed products
        if(CModule::IncludeModule('catalog')):
            $basketUserId = (int)CSaleBasket::GetBasketUserID(true);
            $siteId = Bitrix\Main\Application::getInstance()->getContext()->getSite();
            $viewedIterator = Bitrix\Catalog\CatalogViewedProductTable::GetList(array(
                    'select' => array('PRODUCT_ID', 'ELEMENT_ID'),
                    'filter' => array('FUSER_ID' => $basketUserId, 'SITE_ID' => $siteId, '!PRODUCT_ID' => $GLOBALS["CURRENT_ELEMENT_ID"]),
                    'order' => array('DATE_VISIT' => 'DESC'),
                    'limit' => 5
            ));

            $viewedProductIds = array();
            while ($viewedProduct = $viewedIterator->fetch())
                    $viewedProductIds[] = $viewedProduct["PRODUCT_ID"];
        endif;
    
        $blockSort = array(
            $arParams["VIEWED_PRODUCTS_SORT"] => "viewed",
            $arParams["BESTSALLERS_SORT"] => "bestsaller",
        );
        ksort($blockSort);
        
        foreach ($blockSort as $sortBlock => $blockType) {
            if ($blockType == "viewed") {
                if($viewedProductIds && (!isset($arParams['VIEWED_PRODUCTS_SHOW']) || $arParams['VIEWED_PRODUCTS_SHOW'] != 'N') 
                    && $arParams["VIEWED_PRODUCTS_WERE_SHOW"] == "left" && ModuleManager::isModuleInstalled("catalog")):
                    global $viewedFilter;
                    $viewedFilter = array(
                            "ID" => $viewedProductIds
                    );
                    include 'viewed_col.php';
                endif;
            } elseif ($blockType == "bestsaller") {
                if (!empty($arRecomData)) {
                    if ((!isset($arParams['USE_SALE_BESTSELLERS']) || $arParams['USE_SALE_BESTSELLERS'] != 'N') 
                            && $arParams['BESTSALLERS_WERE_SHOW'] == "left" && ModuleManager::isModuleInstalled("sale"))
                    {
                        include 'bestsaller_col.php';
                    }
                }
            }
        }
	if ($isAdditionalSideBar):
	$APPLICATION->IncludeComponent(
		"bitrix:main.include",
		"",
		Array(
			"AREA_FILE_SHOW" => "file",
			"PATH" => $arParams["SIDEBAR_PATH"],
			"AREA_FILE_RECURSIVE" => "N",
			"EDIT_MODE" => "html",
		),
		false,
		Array('HIDE_ICONS' => 'Y')
	);
	endif;?>
</div>
<?endif?>
<div class="<?=(($isSidebar) ? "col-md-9 col-sm-8" : "col-md-12 col-sm-12 col-xs-12")?>">
<h1><?$APPLICATION->ShowTitle('h1');?></h1>
<div class="row">
<?if ($isFilter && $showElementsFilters):
	$BXReady->showBannerPlace("CATALOG_TOP");?>
    <div class="col-xs-12"><div class="<?if(!isset($arParams["HIDE_FILTER_MOBILE"]) || $arParams["HIDE_FILTER_MOBILE"]=="Y") echo "hidden-xs";?>">
		<?$APPLICATION->IncludeComponent(
			"bitrix:catalog.smart.filter",
			"visual_horisontal",
			array(
				"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"SECTION_ID" => $arCurSection['ID'],
				"FILTER_NAME" => $arParams["FILTER_NAME"],
				"PRICE_CODE" => $arParams["PRICE_CODE"],
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"SAVE_IN_SESSION" => "N",
				"FILTER_VIEW_MODE" => $arParams["FILTER_VIEW_MODE"],
				"XML_EXPORT" => "Y",
				"SECTION_TITLE" => "NAME",
				"SECTION_DESCRIPTION" => "DESCRIPTION",
				'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
				"TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
				'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
				'CURRENCY_ID' => $arParams['CURRENCY_ID'],
				"SEF_MODE" => $arParams["SEF_MODE"],
				"SEF_RULE" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["smart_filter"],
				"SMART_FILTER_PATH" => $arResult["VARIABLES"]["SMART_FILTER_PATH"],
				"DISPLAY_ELEMENT_COUNT" => $arParams["DISPLAY_ELEMENT_COUNT"],
				"PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
				"FILTER_PRICE_CODE" => $arParams['FILTER_PRICE_CODE']
			),
			$component,
			array('HIDE_ICONS' => 'Y')
                );?></div>
<?endif;
	$APPLICATION->IncludeComponent(
		"bitrix:catalog.section.list",
		"row",
		array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
			"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			"COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
			"TOP_DEPTH" => 1,
			"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
			"VIEW_MODE" => $arParams["SECTIONS_VIEW_MODE"],
			"SHOW_PARENT_NAME" => $arParams["SECTIONS_SHOW_PARENT_NAME"],
			"HIDE_SECTION_NAME" => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"]) ? $arParams["SECTIONS_HIDE_SECTION_NAME"] : "N"),
			"ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : '')
		),
		$component,
		array("HIDE_ICONS" => "Y")
	);
        
        if(!empty($arRecomData) && $arParams['USE_GIFTS_SECTION'] === 'Y' && $arParams['GIFTS_PLACE'] === 'TOP')
        {
            $APPLICATION->IncludeComponent(
                "bitrix:sale.gift.section",
                "market",
                array(
                        "HIDE_NOT_AVAILABLE" => $arParams["GIFTS_HIDE_NOT_AVAILABLE"],
                        "PRODUCT_SUBSCRIPTION" => $arParams['PRODUCT_SUBSCRIPTION'],
                        "SHOW_NAME" => "Y",
                        "SHOW_IMAGE" => "Y",
                        "MESS_BTN_BUY" => $arParams['GIFTS_MESS_BTN_BUY'],
                        "MESS_BTN_DETAIL" => $arParams['MESS_BTN_DETAIL'],
                        "MESS_NOT_AVAILABLE" => $arParams['MESS_NOT_AVAILABLE'],
                        "MESS_BTN_SUBSCRIBE" => $arParams['MESS_BTN_SUBSCRIBE'],
                        "TEMPLATE_THEME" => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
                        "DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
                        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                        "CACHE_TIME" => $arParams["CACHE_TIME"],
                        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                        "FILTER_NAME" => $arParams["FILTER_NAME"],
                        "ORDER_FILTER_NAME" => "arOrderFilter",
                        "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
                        "PRICE_CODE" => $arParams["PRICE_CODE"],
                        "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
                        "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                        "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
                        "CURRENCY_ID" => $arParams["CURRENCY_ID"],
                        "BASKET_URL" => $arParams["BASKET_URL"],
                        "ACTION_VARIABLE" => (!empty($arParams["ACTION_VARIABLE"]) ? $arParams["ACTION_VARIABLE"] : "action").'_sgs',
                        "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                        "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                        "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
                        "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                        "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
                        "USE_PRODUCT_QUANTITY" => 'N',
                        "SHOW_PRODUCTS_".$arParams["IBLOCK_ID"] => "Y",
                        "OFFER_TREE_PROPS_".$arRecomData['OFFER_IBLOCK_ID'] => $arParams["OFFER_TREE_PROPS"],
                        "ADDITIONAL_PICT_PROP_".$arParams['IBLOCK_ID'] => $arParams['ADD_PICT_PROP'],
                        "ADDITIONAL_PICT_PROP_".$arRecomData['OFFER_IBLOCK_ID'] => $arParams['OFFER_ADD_PICT_PROP'],

                        "SHOW_DISCOUNT_PERCENT" => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
                        "SHOW_OLD_PRICE" => $arParams['GIFTS_SHOW_OLD_PRICE'],
                        "TEXT_LABEL_GIFT" => $arParams['GIFTS_SECTION_LIST_TEXT_LABEL_GIFT'],
                        "HIDE_BLOCK_TITLE" => $arParams['GIFTS_SECTION_LIST_HIDE_BLOCK_TITLE'],
                        "BLOCK_TITLE" => $arParams['GIFTS_SECTION_LIST_BLOCK_TITLE'],
                        "PAGE_ELEMENT_COUNT" => $arParams['GIFTS_SECTION_LIST_PAGE_ELEMENT_COUNT'],
                        "LINE_ELEMENT_COUNT" => $arParams['GIFTS_SECTION_LIST_PAGE_ELEMENT_COUNT'],

                        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                )
                + array(
                        "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
                        "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
                        "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                        "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
                ),
                $component,
                array("HIDE_ICONS" => "Y")
            );
        }
        
	
	$intSectionID = 0;
	$arDesc = explode("#STEXT#", $arCurSection["DESCRIPTION"]);
        $sectionDesc = $arDesc[0];
        $showSeo = ($arParams["SHOW_SECTION_SEO"] == "Y") ? true : false;
        $seoText = $arDesc[1];
	
                    
        if ($sectionDesc && $arParams["SHOW_SECTION_DESC"] == "top" && (!isset($_GET['PAGEN_1']) || intval($_GET['PAGEN_1'])<=1)):?>
            <div class="bxr-section-desc"><?=$sectionDesc?></div> 
        <?endif;
            
        if($showElementsFilters):
            $APPLICATION->IncludeComponent(
                    "alexkova.market:sort.panel", 
                    ".default", 
                    array(
                            "COMPONENT_TEMPLATE" => ".default",
                            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                            "THEME" =>  $arParams["THEME"],
                            "ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
                            "ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
                            "PAGE_ELEMENT_COUNT_SHOW" => $arParams["PAGE_ELEMENT_COUNT_SHOW"],
                            "PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
                            "PAGE_ELEMENT_COUNT_LIST" => $arParams["PAGE_ELEMENT_COUNT_LIST"],
                            "CATALOG_VIEW_SHOW" => $arParams["CATALOG_VIEW_SHOW"],
                            "DEFAULT_CATALOG_VIEW" => $arParams["DEFAULT_CATALOG_VIEW"],
                            "CATALOG_DEFAULT_SORT" => $arParams["CATALOG_DEFAULT_SORT"],
                            "CATALOG_DEFAULT_SORT_ORDER" =>  isset($arParams['CATALOG_DEFAULT_SORT_ORDER']) ? $arParams['CATALOG_DEFAULT_SORT_ORDER'] : 'desc',
                    ),
                    false,
                    array(
                            "HIDE_ICONS" => "Y"
                    )
            );
        endif;             
	
	if (isset($_SESSION["USER_SORTPANEL"]) && is_array($_SESSION["USER_SORTPANEL"]))
	{
		foreach ($_SESSION["USER_SORTPANEL"] as $cell=>$val)
		{
			$_REQUEST[$cell] = $val;
		}
	}

	global $arSortGlobal;
	$sort = $arSortGlobal["sort"];
	$sort_order = $arSortGlobal["sort_order"];
	$num = $arSortGlobal["num"];
	$view = $arSortGlobal["view"]; 

	$arDefaultResponsive = array(
		"LG" => 4,
		"MD" => 4,
		"SM" => 6,
		"XS" => 12
	);

	if(in_array($view,array('.default','list','table'))){
		switch ($view){
			case "list":
				$elementLibrary = $elementList;
				$arResponsiveParams = array(
					"LG" => 12,
					"MD" => 12,
					"SM" => 12,
					"XS" => 12
				);
				break;
			case "table":
				$elementLibrary = $elementTable;
				$arResponsiveParams = array(
					"LG" => 12,
					"MD" => 12,
					"SM" => 12,
					"XS" => 12
				);
				break;

			default:
				$elementLibrary = $elementBlock;
				$arResponsiveParams = $arDefaultResponsive;
				break;
		}
	}
	else{
		$elementLibrary = $elementBlock;
		$arResponsiveParams = $arDefaultResponsive;
	}	
        
        if ($managment_element_mode == "Y") {
            if ($elementLibrary == $elementBlock) {
                $ownOptElementLib = COption::GetOptionString($module_id, "own_catalog_list_element_type_".SITE_TEMPLATE_ID, $elementBlock);
                if (strlen($ownOptElementLib) > 0) {
                    $optElementLib = trim($ownOptElementLib); 
                } else {
                    $optElementLib = COption::GetOptionString($module_id, "catalog_list_element_type_".SITE_TEMPLATE_ID, $elementBlock);
                }
                $arResponsiveParams["LG"] = COption::GetOptionString($module_id, "catalog_list_element_count_lg_".SITE_TEMPLATE_ID, 4);
                $arResponsiveParams["MD"] = COption::GetOptionString($module_id, "catalog_list_element_count_md_".SITE_TEMPLATE_ID, 4);
                $arResponsiveParams["SM"] = COption::GetOptionString($module_id, "catalog_list_element_count_sm_".SITE_TEMPLATE_ID, 6);
                $arResponsiveParams["XS"] = COption::GetOptionString($module_id, "catalog_list_element_count_xs_".SITE_TEMPLATE_ID, 12);
            } elseif ($elementLibrary == $elementList) {
                $nameListOption = substr("own_catalog_list_element_type_list_".SITE_TEMPLATE_ID, 0,50);
                $ownOptElementLib = COption::GetOptionString($module_id, $nameListOption, $elementList);
                if (strlen($ownOptElementLib) > 0) {
                    $optElementLib = trim($ownOptElementLib); 
                } else {
                    $optElementLib = COption::GetOptionString($module_id, "catalog_list_element_type_list_".SITE_TEMPLATE_ID, $elementList);
                }
            } elseif ($elementLibrary == $elementTable) {
                $nameTableOption = substr("own_catalog_list_element_type_table_".SITE_TEMPLATE_ID, 0,50);
                $ownOptElementLib = COption::GetOptionString($module_id, $nameTableOption, $elementTable);
                if (strlen($ownOptElementLib) > 0) {
                    $optElementLib = trim($ownOptElementLib); 
                } else {
                    $optElementLib = COption::GetOptionString($module_id, "catalog_list_element_type_table_".SITE_TEMPLATE_ID, $elementTable);
                }
            };
            if (strlen($optElementLib) > 0)
                    $elementLibrary = $optElementLib;
        }
        
                    
	$intSectionID = $APPLICATION->IncludeComponent(
		"bxready:ecommerce.list",
		".default",
		array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"ELEMENT_SORT_FIELD" => $sort,
			"ELEMENT_SORT_ORDER" => $sort_order,
			"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
			"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
			"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
			"META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
			"META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
			"BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
			"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
			"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
			"BASKET_URL" => $arParams["BASKET_URL"],
			"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
			"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
			"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
			"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
			"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
			"FILTER_NAME" => $arParams["FILTER_NAME"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_FILTER" => $arParams["CACHE_FILTER"],
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			"SET_TITLE" => $arParams["SET_TITLE"],
			"MESSAGE_404" => $arParams["MESSAGE_404"],
			"SET_STATUS_404" => $arParams["SET_STATUS_404"],
			"SHOW_404" => $arParams["SHOW_404"],
			"FILE_404" => $arParams["FILE_404"],
			"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
			"PAGE_ELEMENT_COUNT" => $num,
			"LINE_ELEMENT_COUNT" => $num,
			"PRICE_CODE" => $arParams["PRICE_CODE"],
			"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
			"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

			"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
			"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
			"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
			"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
			"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],

			"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
			"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
			"PAGER_TITLE" => $arParams["PAGER_TITLE"],
			"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
			"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
			"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
			"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
			"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
			"PAGER_BASE_LINK_ENABLE" => $arParams["PAGER_BASE_LINK_ENABLE"],
			"PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
			"PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],

			"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
			"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
			"OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
			"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
			"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
			"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
			"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
//			"OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
                        "OFFERS_LIMIT" => 0,
			"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
			"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
			"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
			"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
			"USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
			'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
			'CURRENCY_ID' => $arParams['CURRENCY_ID'],
			'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],

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

			'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
			"ADD_SECTIONS_CHAIN" => "N",
			'ADD_TO_BASKET_ACTION' => $basketAction,
			'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
			'COMPARE_PATH' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare'],
			'BACKGROUND_IMAGE' => (isset($arParams['SECTION_BACKGROUND_IMAGE']) ? $arParams['SECTION_BACKGROUND_IMAGE'] : ''),
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
			"USE_VOTE_RATING" => $arParams["DETAIL_USE_VOTE_RATING"],
			"VOTE_DISPLAY_AS_RATING" => $arParams["DETAIL_VOTE_DISPLAY_AS_RATING"],
                        "SHOW_CATALOG_QUANTITY_CNT" => $arParams["SHOW_CATALOG_QUANTITY_CNT"],
                        "SHOW_CATALOG_QUANTITY" => $arParams["SHOW_CATALOG_QUANTITY"],
                        "QTY_SHOW_TYPE" => $arParams["QTY_SHOW_TYPE"],
                        "IN_STOCK" => $arParams["IN_STOCK"],
                        "NOT_IN_STOCK" => $arParams["NOT_IN_STOCK"],
                        "QTY_MANY_GOODS_INT" => $arParams["QTY_MANY_GOODS_INT"],
                        "QTY_MANY_GOODS_TEXT" => $arParams["QTY_MANY_GOODS_TEXT"],
                        "QTY_LESS_GOODS_TEXT" => $arParams["QTY_LESS_GOODS_TEXT"],
                        "OFFERS_VIEW" => $arParams["OFFERS_VIEW"],
                        "SKU_PROPS_SHOW_TYPE" => $arParams["SKU_PROPS_SHOW_TYPE"],
                        "PREVIEW_TRUNCATE_LEN" => $arParams["PREVIEW_TRUNCATE_LEN"],
                        "TILE_SHOW_PROPERTIES" => $arParams["TILE_SHOW_PROPERTIES"]
		),
		false,
		array("HIDE_ICONS" => "Y")
	);?>
		</div>
        <div style="clear:both;"></div>
        <?if(!empty($arRecomData) && $arParams['USE_GIFTS_SECTION'] === 'Y' && $arParams['GIFTS_PLACE'] === 'BOTTOM')
            {
                $APPLICATION->IncludeComponent(
                    "bitrix:sale.gift.section",
                    "market",
                    array(
                            "HIDE_NOT_AVAILABLE" => $arParams["GIFTS_HIDE_NOT_AVAILABLE"],
                            "PRODUCT_SUBSCRIPTION" => $arParams['PRODUCT_SUBSCRIPTION'],
                            "SHOW_NAME" => "Y",
                            "SHOW_IMAGE" => "Y",
                            "MESS_BTN_BUY" => $arParams['GIFTS_MESS_BTN_BUY'],
                            "MESS_BTN_DETAIL" => $arParams['MESS_BTN_DETAIL'],
                            "MESS_NOT_AVAILABLE" => $arParams['MESS_NOT_AVAILABLE'],
                            "MESS_BTN_SUBSCRIBE" => $arParams['MESS_BTN_SUBSCRIBE'],
                            "TEMPLATE_THEME" => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
                            "DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
                            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                            "CACHE_TIME" => $arParams["CACHE_TIME"],
                            "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                            "FILTER_NAME" => $arParams["FILTER_NAME"],
                            "ORDER_FILTER_NAME" => "arOrderFilter",
                            "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
                            "PRICE_CODE" => $arParams["PRICE_CODE"],
                            "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
                            "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                            "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
                            "CURRENCY_ID" => $arParams["CURRENCY_ID"],
                            "BASKET_URL" => $arParams["BASKET_URL"],
                            "ACTION_VARIABLE" => (!empty($arParams["ACTION_VARIABLE"]) ? $arParams["ACTION_VARIABLE"] : "action").'_sgs',
                            "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                            "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                            "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
                            "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                            "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
                            "USE_PRODUCT_QUANTITY" => 'N',
                            "SHOW_PRODUCTS_".$arParams["IBLOCK_ID"] => "Y",
                            "OFFER_TREE_PROPS_".$arRecomData['OFFER_IBLOCK_ID'] => $arParams["OFFER_TREE_PROPS"],
                            "ADDITIONAL_PICT_PROP_".$arParams['IBLOCK_ID'] => $arParams['ADD_PICT_PROP'],
                            "ADDITIONAL_PICT_PROP_".$arRecomData['OFFER_IBLOCK_ID'] => $arParams['OFFER_ADD_PICT_PROP'],

                            "SHOW_DISCOUNT_PERCENT" => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
                            "SHOW_OLD_PRICE" => $arParams['GIFTS_SHOW_OLD_PRICE'],
                            "TEXT_LABEL_GIFT" => $arParams['GIFTS_SECTION_LIST_TEXT_LABEL_GIFT'],
                            "HIDE_BLOCK_TITLE" => $arParams['GIFTS_SECTION_LIST_HIDE_BLOCK_TITLE'],
                            "BLOCK_TITLE" => $arParams['GIFTS_SECTION_LIST_BLOCK_TITLE'],
                            "PAGE_ELEMENT_COUNT" => $arParams['GIFTS_SECTION_LIST_PAGE_ELEMENT_COUNT'],
                            "LINE_ELEMENT_COUNT" => $arParams['GIFTS_SECTION_LIST_PAGE_ELEMENT_COUNT'],

                            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                    )
                    + array(
                            "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
                            "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
                            "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                            "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
                    ),
                    $component,
                    array("HIDE_ICONS" => "Y")
                );
            }
        
	
	$GLOBALS['CATALOG_CURRENT_SECTION_ID'] = $intSectionID;
	unset($basketAction);
	

        if ($sectionDesc && $arParams["SHOW_SECTION_DESC"] == "bottom" && (!isset($_GET['PAGEN_1']) || intval($_GET['PAGEN_1'])<=1)):?>
            <div class="bxr-section-desc"><?=$sectionDesc?></div> 
        <?endif;
    
        if (strlen($seoText)>0 && $showSeo && (!isset($_GET['PAGEN_1']) || intval($_GET['PAGEN_1'])<=1)):?>
            <div class="row"><div class="col-lg-12 tb20"><?=$seoText?></div></div>
        <?endif;
            
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
            $arResponsiveParams["MD"] = COption::GetOptionString($module_id, "catalog_list_element_count_md_".SITE_TEMPLATE_ID, 4);
            $arResponsiveParams["SM"] = COption::GetOptionString($module_id, "catalog_list_element_count_sm_".SITE_TEMPLATE_ID, 6);
            $arResponsiveParams["XS"] = COption::GetOptionString($module_id, "catalog_list_element_count_xs_".SITE_TEMPLATE_ID, 12);
        } else {
            $elementLibrary = "ecommerce.v2.lite";
            $arResponsiveParams["LG"] = 3;
            $arResponsiveParams["MD"] = 4;
            $arResponsiveParams["SM"] = 6;
            $arResponsiveParams["XS"] = 6;
        }
        foreach ($blockSort as $sortBlock => $blockType) {
            if ($blockType == "viewed") {
                if($viewedProductIds && (!isset($arParams['VIEWED_PRODUCTS_SHOW']) || $arParams['VIEWED_PRODUCTS_SHOW'] != 'N')
                    && $arParams["VIEWED_PRODUCTS_WERE_SHOW"] == "bottom" && ModuleManager::isModuleInstalled("catalog")):
                    global $viewedFilter;
                    $viewedFilter = array(
                            "ID" => $viewedProductIds
                    );
                    include 'viewed_row.php';
                endif;
            } elseif ($blockType == "bestsaller") {
                if (!empty($arRecomData)) {
                    if ((!isset($arParams['USE_SALE_BESTSELLERS']) || $arParams['USE_SALE_BESTSELLERS'] != 'N')
                            && $arParams['BESTSALLERS_WERE_SHOW'] == "bottom" && ModuleManager::isModuleInstalled("sale"))
                    {
                        include 'bestsaller_row.php';
                    }
                }
            }
        }
            
	
	if (ModuleManager::isModuleInstalled("sale"))
	{
		if (!empty($arRecomData))
		{
			if (!isset($arParams['USE_BIG_DATA']) || $arParams['USE_BIG_DATA'] != 'N')
			{
				
		?><div class="col-xs-12">
				<?$APPLICATION->IncludeComponent("bitrix:catalog.bigdata.products", ".default", array(
					"LINE_ELEMENT_COUNT" => $arParams["BIG_DATA_CNT"],
					"TEMPLATE_THEME" => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
					"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
					"BASKET_URL" => $arParams["BASKET_URL"],
					"ACTION_VARIABLE" => (!empty($arParams["ACTION_VARIABLE"]) ? $arParams["ACTION_VARIABLE"] : "action")."_cbdp",
					"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
					"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
					"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
					"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
					"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
					"SHOW_OLD_PRICE" => $arParams['SHOW_OLD_PRICE'],
					"SHOW_DISCOUNT_PERCENT" => $arParams['SHOW_DISCOUNT_PERCENT'],
					"PRICE_CODE" => $arParams["PRICE_CODE"],
					"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
					"PRODUCT_SUBSCRIPTION" => $arParams['PRODUCT_SUBSCRIPTION'],
					"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
					"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
					"SHOW_NAME" => "Y",
					"SHOW_IMAGE" => "Y",
					"MESS_BTN_BUY" => $arParams['MESS_BTN_BUY'],
					"MESS_BTN_DETAIL" => $arParams['MESS_BTN_DETAIL'],
					"MESS_BTN_SUBSCRIBE" => $arParams['MESS_BTN_SUBSCRIBE'],
					"MESS_NOT_AVAILABLE" => $arParams['MESS_NOT_AVAILABLE'],
					"PAGE_ELEMENT_COUNT" => $arParams["BIG_DATA_CNT"],
					"SHOW_FROM_SECTION" => "N",
					"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],
					"DEPTH" => "2",
					"CACHE_TYPE" => $arParams["CACHE_TYPE"],
					"CACHE_TIME" => $arParams["CACHE_TIME"],
					"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
					"SHOW_PRODUCTS_".$arParams["IBLOCK_ID"] => "Y",
					"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
					"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
					"CURRENCY_ID" => $arParams["CURRENCY_ID"],
                                        "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
                                        "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
					"SECTION_ELEMENT_ID" => "",
					"SECTION_ELEMENT_CODE" => "",
					"LABEL_PROP_".$arParams["IBLOCK_ID"] => $arParams['LABEL_PROP'],
					"PROPERTY_CODE_".$arParams["IBLOCK_ID"] => $arParams["LIST_PROPERTY_CODE"],
					"PROPERTY_CODE_".$arRecomData['OFFER_IBLOCK_ID'] => $arParams["LIST_OFFERS_PROPERTY_CODE"],
					"CART_PROPERTIES_".$arParams["IBLOCK_ID"] => $arParams["PRODUCT_PROPERTIES"],
					"CART_PROPERTIES_".$arRecomData['OFFER_IBLOCK_ID'] => $arParams["OFFERS_CART_PROPERTIES"],
					"ADDITIONAL_PICT_PROP_".$arParams["IBLOCK_ID"] => $arParams['ADD_PICT_PROP'],
					"ADDITIONAL_PICT_PROP_".$arRecomData['OFFER_IBLOCK_ID'] => $arParams['OFFER_ADD_PICT_PROP'],
					"OFFER_TREE_PROPS_".$arRecomData['OFFER_IBLOCK_ID'] => $arParams["OFFER_TREE_PROPS"],
					"RCM_TYPE" => (isset($arParams['BIG_DATA_RCM_TYPE']) ? $arParams['BIG_DATA_RCM_TYPE'] : ''),
					"BLOCK_TITLE" => $arParams["BIG_DATA_TITLE"],
					"BXREADY_LIST_LG_CNT" => $arResponsiveParams["LG"],
					"BXREADY_LIST_MD_CNT" => $arResponsiveParams["MD"],
					"BXREADY_LIST_SM_CNT" => $arResponsiveParams["SM"],
					"BXREADY_LIST_XS_CNT" => $arResponsiveParams["XS"],
					"BXREADY_ELEMENT_DRAW" => $elementLibrary,
					"USE_VOTE_RATING" => $arParams["DETAIL_USE_VOTE_RATING"],
					"VOTE_DISPLAY_AS_RATING" => $arParams["DETAIL_VOTE_DISPLAY_AS_RATING"],
					"SHOW_CATALOG_QUANTITY_CNT" => $arParams["SHOW_CATALOG_QUANTITY_CNT"],
					"SHOW_CATALOG_QUANTITY" => $arParams["SHOW_CATALOG_QUANTITY"],
					"QTY_SHOW_TYPE" => $arParams["QTY_SHOW_TYPE"],
					"IN_STOCK" => $arParams["IN_STOCK"],
					"NOT_IN_STOCK" => $arParams["NOT_IN_STOCK"],
					"QTY_MANY_GOODS_INT" => $arParams["QTY_MANY_GOODS_INT"],
					"QTY_MANY_GOODS_TEXT" => $arParams["QTY_MANY_GOODS_TEXT"],
					"QTY_LESS_GOODS_TEXT" => $arParams["QTY_LESS_GOODS_TEXT"],
					"OFFERS_VIEW" => $arParams["OFFERS_VIEW"],
				),
				$component,
				array("HIDE_ICONS" => "Y")
			);?>
		</div>
			<?
			}
		}
	}
	
		$BXReady->showBannerPlace("CATALOG_BOTTOM");?>
	</div>
</div>