<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Alexkova\Market\Core;
use Alexkova\Bxready\Draw;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

$BXReady = \Alexkova\Market\Core::getInstance();

$elementBlock = 'system#ecommerce_v1';
$elementList = 'system#ecommerce_v1_list';
$elementTable = 'system#ecommerce_v1_table';

$module_id = "alexkova.market";

$arParams['USE_FILTER'] = (isset($arParams['USE_FILTER']) && $arParams['USE_FILTER'] == 'Y' ? 'Y' : 'N');
if (isset($arResult["VARIABLES"]["SECTION_CODE"]) && strlen($arResult["VARIABLES"]["SECTION_CODE"])>0){
	global $arrFilter;

        CModule::IncludeModule("highloadblock");
        
        $properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>$arParams['IBLOCK_ID'],"CODE"=>$arParams['DETAIL_BRAND_PROP_CODE'][0]));
        if($prop_fields = $properties->GetNext())
        {
            $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(
                                        array('filter' => array('=TABLE_NAME' => $prop_fields['USER_TYPE_SETTINGS']['TABLE_NAME']))
                                )->fetch();
            $hlblock_id = $hlblock_info['ID'];
        }
        $hlblock_id = ($hlblock_id) ? $hlblock_id : 2;
        
        if (!empty($hlblock)) {
            $entity = HL\HighloadBlockTable::compileEntity($hlblock);
            $entity_data_class = $entity->getDataClass();
            $entity_table_name = $hlblock['TABLE_NAME'];

            $filter = array("UF_LINK" => "/brands/".$arResult["VARIABLES"]["SECTION_CODE"]."/"); 

            $sTableID = 'tbl_'.$entity_table_name;
            $rsData = $entity_data_class::getList(array(
                    "select" => array('*'), 
                    "filter" => $filter,
                    "order" => array("UF_SORT"=>"ASC") 
            ));
            $rsData = new CDBResult($rsData, $sTableID);
            if ($arRes = $rsData->Fetch()){
                if ($arRes["UF_FILE"]>0){
                    $arCurSection["PICTURE"] = Alexkova\Bxready\Draw::prepareImage($arRes["UF_FILE"], array("width" => 200, "height" => 200));
                }
                $arCurSection["NAME"] = $arRes["UF_NAME"];
                $arCurSection["TITLE"] = $arRes["UF_TITLE"];
                $arCurSection["DESCRIPTION"] = $arRes["UF_DESCRIPTION"];
                $arCurSection["KEYWORDS"] = $arRes["UF_KEYWORDS"];
                $arCurSection["DESC"] = (strlen($arRes["UF_FULL_DESCRIPTION"]) > 0) ? $arRes["UF_FULL_DESCRIPTION"] : $arRes["UF_DESCRIPTION"];
                $xmlId = $arRes["UF_XML_ID"];
            }
        }
        
        $arrFilter["PROPERTY_".$arParams['DETAIL_BRAND_PROP_CODE'][0]] = strval($xmlId);
}
?>
<?
$arFilter = array(
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],
	"ACTIVE" => "Y",
	"GLOBAL_ACTIVE" => "Y",
);

if (!Loader::includeModule('highloadblock'))
{
	ShowError(GetMessage("IBLOCK_CBB_HLIBLOCK_NOT_INSTALLED"));
	return false;
}
?>
<?
if (strlen($arCurSection["NAME"])>0)
{
?>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <?if (isset($arCurSection))
    {
        $titleBrand = $arCurSection["NAME"];
        if (strlen($arParams["BRAND_PAGE_MASK"])>0){
                $titleBrand = str_replace('#BRAND_NAME#', $titleBrand, $arParams["BRAND_PAGE_MASK"]);
        }

        $APPLICATION->SetPageProperty('h1', $titleBrand);
        $APPLICATION->SetPageProperty('description', $titleBrand);
        $APPLICATION->SetTitle($titleBrand);
        $APPLICATION->AddChainItem($titleBrand);
        $APPLICATION->SetPageProperty('title', $arCurSection["TITLE"]);
        $APPLICATION->SetPageProperty('keywords', $arCurSection["KEYWORDS"]);
        $APPLICATION->SetPageProperty('description', $arCurSection["DESCRIPTION"]);
        ?>
        <?if ($arParams["SHOW_SECTION_DESC"] == "top" && (strlen($arCurSection["PICTURE"]['src'])>0 || $arCurSection["DESC"]) && (!isset($_GET['PAGEN_1']) || intval($_GET['PAGEN_1'])<=1)) {?>
            <div class="row">
                <div class="col-xs-12 tb20 brand-image">
                    <?if(strlen($arCurSection["PICTURE"]['src'])>0):?>
                            <img src="<?=$arCurSection["PICTURE"]['src']?>" align="right">
                    <?endif;?>
                    <div class="bxr-section-desc">
                        <?=$arCurSection["DESC"]?>
                    </div> 
                </div>
            </div>
        <?}?>
    <?}?>  
    <div class="row">
        <?$BXReady->showBannerPlace("CATALOG_TOP");?>
        <div class="col-xs-12">
            <?
            $intSectionID = 0;
            ?>
            <?
            if (strlen(COption::GetOptionString('alexkova.market', 'list_marker_type'))>0){
                    $bxreadyMarkers = COption::GetOptionString('alexkova.market', 'list_marker_type');
            }else{
                    $bxreadyMarkers = $arParams["BXREADY_LIST_MARKER_TYPE"];
            }

            // central manage mode
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
            } else {
                $elementLibrary = "ecommerce.v2.lite";
            }
            
            
$APPLICATION->IncludeComponent(
	"alexkova.market:sort.panel", 
	".default", 
	array(
                "COMPONENT_TEMPLATE" => ".default",
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"THEME" =>  $arParams["THEME"],
		"ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
		"PAGE_ELEMENT_COUNT_SHOW" => $arParams["PAGE_ELEMENT_COUNT_SHOW"],
		"PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
		"PAGE_ELEMENT_COUNT_LIST" => $arParams["PAGE_ELEMENT_COUNT_LIST"],
		"CATALOG_VIEW_SHOW" => $arParams["CATALOG_VIEW_SHOW"],
		"DEFAULT_CATALOG_VIEW" => $arParams["DEFAULT_CATALOG_VIEW"],
		"CATALOG_DEFAULT_SORT" => $arParams["CATALOG_DEFAULT_SORT"],
	),
	false,
	array(
		"HIDE_ICONS" => "Y"
	)
);

            if (isset($_SESSION["USER_SORTPANEL"]) && is_array($_SESSION["USER_SORTPANEL"]))
            {
                    foreach ($_SESSION["USER_SORTPANEL"] as $cell=>$val)
                    {
                            $_REQUEST[$cell] = $val;
                    }
            }

            $sort = "price";
            $sort_order = "asc";

            global $arSortGlobal;

            $sort = $arSortGlobal["sort"];
            $sort_order = $arSortGlobal["sort_order"];
            $num = $arSortGlobal["num"];

            $view = trim(strip_tags($_REQUEST["view"]));

            $arDefaultResponsive = array(
                    "LG" => 3,
                    "MD" => 3,
                    "SM" => 4,
                    "XS" => 6
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
                    $arResponsiveParams["MD"] = COption::GetOptionString($module_id, "catalog_list_element_count_md_".SITE_TEMPLATE_ID, 3);
                    $arResponsiveParams["SM"] = COption::GetOptionString($module_id, "catalog_list_element_count_sm_".SITE_TEMPLATE_ID, 2);
                    $arResponsiveParams["XS"] = COption::GetOptionString($module_id, "catalog_list_element_count_xs_".SITE_TEMPLATE_ID, 1);
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

            if (!isset($_GET["sort"])) {
                    $sort = $arParams["CATALOG_DEFAULT_SORT"];
                    $sort_order = $arParams["ELEMENT_SORT_ORDER"];
            }
            ?>

            <?$intSectionID = $APPLICATION->IncludeComponent(
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
                    "PAGE_ELEMENT_COUNT" => $num ? $num : $arParams["LINE_ELEMENT_COUNT"],
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
                    "OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
                    "OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
                    "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
                    "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
                    "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
                    "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
                    "OFFERS_LIMIT" => 0,
                    "SECTION_ID" => "",
                    "SECTION_CODE" => "",
                    "SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
                    "DETAIL_URL" => $arResult["URL_TEMPLATES"]["element"],
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
                    "BY_LINK" => "Y",
                    "SKU_PROPS_SHOW_TYPE" => $arParams["SKU_PROPS_SHOW_TYPE"],  
                ),
                $component,
                array("HIDE_ICONS" => "Y")
            );?>
        </div>
        <?
        $GLOBALS['CATALOG_CURRENT_SECTION_ID'] = $intSectionID;
        unset($basketAction);
        ?>
        <?$BXReady->showBannerPlace("CATALOG_BOTTOM");?>
    </div>
    <?if ($arParams["SHOW_SECTION_DESC"] == "bottom" && (strlen($arCurSection["PICTURE"]['src'])>0 || $arCurSection["DESC"]) && (!isset($_GET['PAGEN_1']) || intval($_GET['PAGEN_1'])<=1)) {?>
            <div class="row">
                <div class="col-xs-12 tb20">
                    <?if(strlen($arCurSection["PICTURE"]['src'])>0):?>
                            <img src="<?=$arCurSection["PICTURE"]['src']?>" align="right">
                    <?endif;?>
                    <div class="bxr-section-desc">
                        <?=$arCurSection["DESC"]?>
                    </div> 
                </div>
            </div>
        <?}?>
</div>
<?
}
else
{
    if ($arParams['SET_STATUS_404'] == 'Y')
    {
        CHTTP::SetStatus("404 Not Found");

        if ($arParams['SHOW_404'] == "Y")
        {
            if (strlen($arParams['FILE_404'])>0)
                $file404 = $arParams['FILE_404'];
            else
                $file404 = '/404.php';

            include($_SERVER['DOCUMENT_ROOT'].$file404);
        }
        else
        {
            if (strlen($arParams['MESSAGE_404'])>0){?>
                <div class="bxr-404-message"><?=$arParams['MESSAGE_404']?></div>
            <?}
        }
    }
}?>
