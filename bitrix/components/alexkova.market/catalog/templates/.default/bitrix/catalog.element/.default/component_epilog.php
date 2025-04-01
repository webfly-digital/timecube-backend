<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */
global $MESS;
include_once(GetLangFileName(dirname(__FILE__) . '/lang/', '/template.php'));

global $APPLICATION;
global $moreSettings;

if ($arParams["ZOOM_ON"] == "Y")
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/js/zoomsl-3.0.js");

if(isset($arParams["DETAIL_DISPLAY_SHOW_VIDEO"]) && $arParams["DETAIL_DISPLAY_SHOW_VIDEO"] == "Y" && (!isset($arParams["VIDEO_PLAYER"]) || $arParams["VIDEO_PLAYER"] == "MEJ")){
    $APPLICATION->AddHeadScript($templateFolder."/js/mediaelement-and-player.min.js");
    $APPLICATION->SetAdditionalCSS($templateFolder . '/css/mediaelementplayer.min.css', true);
}

$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/slick/slick.js');
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/js/slick/slick.css', true);
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/fancybox/jquery.fancybox.pack.js');
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/js/fancybox/jquery.fancybox.css');

$useReview = ('Y' == $arParams['USE_REVIEW']);


if(!empty($_REQUEST["offer"]) && isset($arResult["OFFERS"][$_REQUEST["offer"]]))
    $arResult["FIRST_SKU_SELECT"] = $_REQUEST["offer"];

            if(is_array($arResult["TABS"]) && count($arResult["TABS"])>0):
                foreach ($arResult["TABS"] as $k => $tab):
                    if(isset($tab["epilog"]) && $tab["epilog"]):
                        
                            if(is_array($tab["echo"]) && count($tab["echo"])>0)
                            foreach($tab["echo"] as $v) {
                                echo $v;
                            }
                            
                            if(is_array($tab["include_top"]) && count($tab["include_top"])>0)
                                foreach($tab["include_top"] as $v) {
                                    include_once $v;
                                }
                                
                            if(empty($tab["name"]))
                                continue;
                        ?>
                        <h3 class="bxr-detail-tab-mobile-title  hidden-lg hidden-md hidden-sm"><?=$tab["name"]?></h3><hr class="section">
                        <div class="bxr-detail-tab bxr-detail-offers" data-tab="<?=$k?>">
                            <?
                                if(is_array($tab["include"]) && count($tab["include"])>0)
                                    foreach($tab["include"] as $v) {
                                          include_once $v;
                                    }
                            ?>
                        </div>
                    <?endif;
				endforeach;
            endif;
        ?>
    </div>
</div>
<?if ($arParams["ADDITIONAL_DETAIL_INFO"] && strlen($arParams["ADDITIONAL_DETAIL_INFO"])>0):?>
<div class="row" id="bxr-additional-info">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <?
        $APPLICATION->IncludeComponent(
                "bitrix:main.include",
                "",
                Array(
                        "AREA_FILE_SHOW" => "file",
                        "PATH" => $arParams["ADDITIONAL_DETAIL_INFO"],
                        "AREA_FILE_RECURSIVE" => "N",
                        "EDIT_MODE" => "html",
                ),
                false,
                Array('HIDE_ICONS' => 'Y')
        );
        ?>
    </div>
</div>
<?endif;?>
<?
$bxr_use_links_sku = COption::GetOptionString("alexkova.market", "bxr_use_links_sku", "N");
$bxr_select_first_sku = COption::GetOptionString("alexkova.market", "bxr_select_first_sku", "N");
$offerId = (intval($arParams["OFFER_ID"])) ? intval($arParams["OFFER_ID"]) : (($bxr_select_first_sku == "Y") ? $arResult["FIRST_SKU_SELECT"] : 0);
?>
<script>
    $(document).ready(function() {
        useSelectSku = false;
        useFirstSelectSku = false;
        
        <?if($bxr_use_links_sku == "Y"):?>
            useSelectSku = true; 
        <?endif;?>
            
        <?if($bxr_select_first_sku == "Y"):?>
            useFirstSelectSku = true; 
        <?endif;?>
        
        paramsSKU = [];
        <?foreach ($arResult["OFFERS"][$offerId]["PROPERTIES"] as $k => $params):?>
            <?if(!isset($arResult["SKU_PROPS_LIST"][$params["CODE"]])) continue;?>
            paramsSKU["<?=$k?>"] = [];
            paramsSKU["<?=$k?>"].id = "<?=$params["ID"]?>";
            paramsSKU["<?=$k?>"].val = "<?=$params["VALUE"]?>";
            paramsSKU["<?=$k?>"].url = "<?=$arResult["OFFERS"][$offerId]["DETAIL_PICTURE"]["SRC"]?>";
        <?endforeach;?>
        <?if ($offerId) {?>
            selectSKU("<?=$arResult["OFFERS_VIEW"];?>", "<?=$offerId?>", paramsSKU); 
        <?}?>
    });
</script>
<? if (is_array($arResult["PROPERTIES"][$arParams["LINK_PROPERTY_SID"]]["VALUE"]) && count($arResult["PROPERTIES"][$arParams["LINK_PROPERTY_SID"]]["VALUE"]) > 0) { 
    
    global $accessoriesFilter;
    $accessoriesFilter = array("ID" => $arResult["PROPERTIES"][$arParams["LINK_PROPERTY_SID"]]["VALUE"]);
    $elementBlock = 'system#ecommerce_v1';
    $intSectionID = 0;
    if (strlen(COption::GetOptionString('alexkova.market', 'list_marker_type')) > 0) {
        $bxreadyMarkers = COption::GetOptionString('alexkova.market', 'list_marker_type');
    } else {
        $bxreadyMarkers = $arParams["BXREADY_LIST_MARKER_TYPE"];
    };
    $arDefaultResponsive = array(
        "LG" => 3,
        "MD" => 3,
        "SM" => 4,
        "XS" => 6
    );

    $elementLibrary = $elementBlock;
    $arResponsiveParams = $arDefaultResponsive;
    
    
    $module_id = "alexkova.market";
    $managment_element_mode = COption::GetOptionString($module_id, "managment_element_mode", "N");
    
    if ($managment_element_mode == "Y") {
        $ownOptElementLib = COption::GetOptionString($module_id, "own_catalog_list_element_type_".SITE_TEMPLATE_ID, $elementBlock);
        if (strlen($ownOptElementLib) > 0) {
            $elementLibrary = trim($ownOptElementLib); 
        } else {
            $optElementLib = COption::GetOptionString($module_id, "catalog_list_element_type_".SITE_TEMPLATE_ID, $elementBlock);
            if (strlen($optElementLib) > 0) {
                $elementLibrary = $optElementLib;
            } else {
                $elementLibrary = $elementBlock;
            }
        }
        $arResponsiveParams["LG"] = COption::GetOptionString($module_id, "catalog_list_element_count_lg_".SITE_TEMPLATE_ID, 4);
        $arResponsiveParams["MD"] = COption::GetOptionString($module_id, "catalog_list_element_count_md_".SITE_TEMPLATE_ID, 3);
        $arResponsiveParams["SM"] = COption::GetOptionString($module_id, "catalog_list_element_count_sm_".SITE_TEMPLATE_ID, 2);
        $arResponsiveParams["XS"] = COption::GetOptionString($module_id, "catalog_list_element_count_xs_".SITE_TEMPLATE_ID, 1);
    }
    ?>
    <div class="row" id="bxr-accessories-block-wrap">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <?if(!empty($arResult["PROPERTIES"][$arParams["LINK_PROPERTY_SID"]]['NAME'])):?>
                <h2><?=$arResult["PROPERTIES"][$arParams["LINK_PROPERTY_SID"]]['NAME'];?></h2>
            <?endif;?>
            <?
            $APPLICATION->IncludeComponent(
                "bxready:ecommerce.list", ".default", array(
                    "PROP_NAME_FOR_BLOCK_TITLE" => "",
                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                    "ELEMENT_SORT_FIELD" => "id",
                    "ELEMENT_SORT_ORDER" => 'asc',
                    "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
                    "ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
                    "PROPERTY_CODE" => $arParams["PROPERTY_CODE"],
                    //"META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
                    //"META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
                   // "BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
                    "SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
                    "INCLUDE_SUBSECTIONS" => "Y",
                    "SHOW_ALL_WO_SECTION" => "Y",
                    "BASKET_URL" => $arParams["BASKET_URL"],
                    "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
                    "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                    "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                    "FILTER_NAME" => 'accessoriesFilter',
                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                    "CACHE_FILTER" => $arParams["CACHE_FILTER"],
                    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                    //"SET_TITLE" => $arParams["SET_TITLE"],
                    "MESSAGE_404" => $arParams["MESSAGE_404"],
                    "SET_STATUS_404" => $arParams["SET_STATUS_404"],
                    "SHOW_404" => $arParams["SHOW_404"],
                    "FILE_404" => $arParams["FILE_404"],
                    "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
                    "PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
                    "LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
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
                    "OFFERS_FIELD_CODE" => $arParams["OFFERS_FIELD_CODE"],
                    "OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
                    "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
                    "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
                    "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
                    "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
                    //			"OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
                    "OFFERS_LIMIT" => 0,
                    "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
                    "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
                    "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
                    //"DETAIL_URL" => $arResult["DETAIL_PAGE_URL"],
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
                    'COMPARE_PATH' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['compare'],
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
                    "VOTE_DISPLAY_AS_RATING" => "N",
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
                ), false, array("HIDE_ICONS" => "Y")
            );
            ?>
        </div>
    </div>
<? } 

Bitrix\Catalog\CatalogViewedProductTable::refresh($arResult['ID'], CSaleBasket::GetBasketUserID(true));
$GLOBALS["CURRENT_ELEMENT_ID"] = $arResult["ID"];

