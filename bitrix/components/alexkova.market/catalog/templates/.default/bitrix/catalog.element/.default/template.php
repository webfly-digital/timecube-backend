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
global $moreSettings;
$templateData = array(
	'TEMPLATE_THEME' => $this->GetFolder().'/themes/'.$arParams['TEMPLATE_THEME'].'/style.css',
	'TEMPLATE_CLASS' => 'bx_'.$arParams['TEMPLATE_THEME']
);

$strMainID = $this->GetEditAreaId($arResult['ID']);
$arItemIDs = array(
	'ID' => $strMainID,
	'PICT' => $strMainID.'_pict',
	'DISCOUNT_PICT_ID' => $strMainID.'_dsc_pict',
	'STICKER_ID' => $strMainID.'_sticker',
	'BIG_SLIDER_ID' => $strMainID.'_big_slider',
	'BIG_IMG_CONT_ID' => $strMainID.'_bigimg_cont',
	'SLIDER_CONT_ID' => $strMainID.'_slider_cont',
	'SLIDER_LIST' => $strMainID.'_slider_list',
	'SLIDER_LEFT' => $strMainID.'_slider_left',
	'SLIDER_RIGHT' => $strMainID.'_slider_right',
	'OLD_PRICE' => $strMainID.'_old_price',
	'PRICE' => $strMainID.'_price',
	'DISCOUNT_PRICE' => $strMainID.'_price_discount',
	'SLIDER_CONT_OF_ID' => $strMainID.'_slider_cont_',
	'SLIDER_LIST_OF_ID' => $strMainID.'_slider_list_',
	'SLIDER_LEFT_OF_ID' => $strMainID.'_slider_left_',
	'SLIDER_RIGHT_OF_ID' => $strMainID.'_slider_right_',
	'QUANTITY' => $strMainID.'_quantity',
	'QUANTITY_DOWN' => $strMainID.'_quant_down',
	'QUANTITY_UP' => $strMainID.'_quant_up',
	'QUANTITY_MEASURE' => $strMainID.'_quant_measure',
	'QUANTITY_LIMIT' => $strMainID.'_quant_limit',
	'BUY_LINK' => $strMainID.'_buy_link',
	'ADD_BASKET_LINK' => $strMainID.'_add_basket_link',
	'COMPARE_LINK' => $strMainID.'_compare_link',
	'PROP' => $strMainID.'_prop_',
	'PROP_DIV' => $strMainID.'_skudiv',
	'DISPLAY_PROP_DIV' => $strMainID.'_sku_prop',
	'OFFER_GROUP' => $strMainID.'_set_group_',
	'BASKET_PROP_DIV' => $strMainID.'_basket_prop',
        'SUBSCRIBE_LINK' => $strMainID.'_subscribe',
);
$strObName = 'ob'.preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);
$templateData['JS_OBJ'] = $strObName;

$strTitle = (
	isset($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]) && '' != $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]
	? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]
	: $arResult['NAME']
);
$strAlt = (
	isset($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]) && '' != $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]
	? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]
	: $arResult['NAME']
);

$useVoteRating = ('Y' == $arParams['USE_VOTE_RATING']);
$useReview = ('Y' == $arParams['USE_REVIEW']);
$storeAmount = ('Y' == $arParams['USE_STORE']);
$useCompare = ('Y' == $arParams['DISPLAY_COMPARE']);
$useFavorites = ('Y' == $arParams['USE_FAVORITES']);
$useShare = ('Y' == $arParams['USE_SHARE']);
$useOneClick = ('Y' == $arParams['USE_ONE_CLICK']);
$noTabs = ('Y' == $arParams['NO_TABS']);

$show_files = false;
if( isset($arParams["DETAIL_DISPLAY_SHOW_FILES"]) && $arParams["DETAIL_DISPLAY_SHOW_FILES"] == "Y" &&
 (!empty($arResult["PROPERTIES"]["FILES"]["VALUE"]) || !empty($arResult["PROPERTIES"]["UF_FILES"]))) {
    $show_files = true;
}

$show_video = false;
if(isset($arParams["DETAIL_DISPLAY_SHOW_VIDEO"]) && $arParams["DETAIL_DISPLAY_SHOW_VIDEO"] == "Y") {
    if(!empty($arResult["PROPERTIES"]["UF_VIDEO"]))
        $show_video = true;

    if(!empty($arResult["PROPERTIES"]["VIDEO"]["VALUE"]) && !empty($arResult["PROPERTIES"]["VIDEO"]["VALUE"][0])) 
        $show_video = true;
    else
        unset($arResult["PROPERTIES"]["VIDEO"]["VALUE"][0]);
}

foreach($arParams["PREVIEW_DETAIL_PROPERTY_CODE"] as $propertyCode)
{
    $arResult["DISPLAY_PREVIEW_PROPERTIES"][$propertyCode] = $arResult["DISPLAY_PROPERTIES"][$propertyCode];

    if ($arParams["HIDE_PREVIEW_PROPS_INLIST"] == 'Y'):
        unset($arResult["DISPLAY_PROPERTIES"][$propertyCode]);
    endif;
}

if ($arParams["ARTICLE_POSITION"] && $arParams["ARTICLE_POSITION"] != "none")
{
    $articlePropCode = ($arParams["ARTICLE_PROP_CODE"] && strlen($arParams["ARTICLE_PROP_CODE"])>0) ? $arParams["ARTICLE_PROP_CODE"] : 'CML2_ARTICLE';
    $articlePropName = $arResult["PROPERTIES"][$articlePropCode]["NAME"];
    $articleValue = $arResult["PROPERTIES"][$articlePropCode]["VALUE"];
    $articleBlock = '<div class="bxr-article-block '.$arParams["ARTICLE_POSITION"].'"><span class="bxr-article-name">'.$articlePropName.': '.'</span>';
    $articleBlock .= '<span class="bxr-article-value">'.$articleValue.'</span></div>';
}
?>

<?if ($arParams["ARTICLE_POSITION"] && $arParams["ARTICLE_POSITION"] == "after_name" && strlen($articleValue)>0):?>
    <?=$articleBlock?>
<?endif;?>

<?include_once 'top_tabs.php';?>

<div class="row">
    <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
        <?//--slider block, also includes manufacturer logo, sale icons and value--
        if (count($arResult["MORE_PHOTO"]) > 0):
            include_once 'slider.php';
        endif;?>
    </div>
    <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 bxr-detail-right">
        
        <?if ($arParams["ARTICLE_POSITION"] && $arParams["ARTICLE_POSITION"] == "right" && strlen($articleValue)>0):?>
            <?=$articleBlock?>
        <?endif;?>
        
        <?
        //rating block
        if ($useVoteRating)
        {?>
            <div class="bxr-rating-wrap"<?=($arResult["PROPERTIES"]["rating"]["VALUE"] > 0)?' itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating"':''?>>
		<?if($arResult["PROPERTIES"]["rating"]["VALUE"] > 0) :?>
		<meta itemprop="ratingValue" content="<?=$arResult["PROPERTIES"]["rating"]["VALUE"]?>">
		<meta itemprop="ratingCount" content="<?=$arResult["PROPERTIES"]["vote_count"]["VALUE"]?>">
            <?	endif;

		$APPLICATION->IncludeComponent(
                "bitrix:iblock.vote",
                "stars",
                array(
                        "IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
                        "IBLOCK_ID" => $arParams['IBLOCK_ID'],
                        "ELEMENT_ID" => $arResult['ID'],
                        "ELEMENT_CODE" => "",
                        "MAX_VOTE" => "5",
                        "VOTE_NAMES" => array("1", "2", "3", "4", "5"),
                        "SET_STATUS_404" => "N",
                        "DISPLAY_AS_RATING" => $arParams['VOTE_DISPLAY_AS_RATING'],
                        "CACHE_TYPE" => $arParams['CACHE_TYPE'],
                        "CACHE_TIME" => $arParams['CACHE_TIME']
                ),
                $component,
                array("HIDE_ICONS" => "Y")
            );?>
            </div>
        <?}
        include_once 'quantity.php';?>
        <div class="clearfix"></div>
        <?if ($arResult["PROPERTIES"]["CML2_ARTICLE"]["VALUE"] && false) {?>
            <div class="bxr-good-article">
                <?=GetMessage("BXR_ARTICLE_TITLE_DETAIL").": ".$arResult["PROPERTIES"]["CML2_ARTICLE"]["VALUE"]?> 
            </div>
        <?}?>
        <div class="clearfix"></div>
        <?
        //--prices block --
	?><div id="bxr-market-price-wrap" itemprop="offers" itemscope <?=(count($arResult["OFFERS"]) > 0)?'itemtype="http://schema.org/AggregateOffer"':'itemtype="http://schema.org/Offer"'?>>
			<?$includeAreaName = 'prices';
			include 'include_handler.php';?>
        </div>
	<?        
        //--sku-select block -->	
        if (count($arResult["OFFERS"]) > 0) {?>
            <div id="bxr-market-sku-select-wrap" class="tb20">
                <?  include_once 'sku_select.php';?>
            </div>
        <?}
        
        //--basket-btns block--
	?>
        <div id="bxr-market-detail-basket-btn-wrap">
            <?  include_once 'basket_btn.php';?>
        </div>
        <?
        //--gift notice block--
        if ($arParams['USE_GIFTS_DETAIL'] == 'Y' && $arParams['SHOW_GIFTS_DETAIL_NOTICE'] == 'Y' && \Bitrix\Main\ModuleManager::isModuleInstalled("sale")){?>
        <div class="bxr-gift-notice bxr-border-color">
            <div class="bxr-gift-notice-icon bxr-color-button">
                <span class="fa fa-gift"></span>
            </div>
            <div class="bxr-gift-notice-text">
                <a href="javascript:void(0);" class="bxr-gift-notice-main"><?=GetMessage("BXR_GIFT_NOTICE_TITLE")?></a>
                <span class="bxr-gift-notice-add"><?=(strlen($arParams["BXR_GIFT_NOTICE_TEXT"])>0) ? $arParams["BXR_GIFT_NOTICE_TEXT"] : GetMessage("BXR_GIFT_NOTICE_TEXT")?></span>
            </div> 
            <div class="clearfix"></div>
        </div>
        <?}
        ?>
		<?
        if ($arResult["PreviewBlockShow"]) {?>
            <div class="bxr-detail-preview-wrap">
				<?if ($arResult["PreviewTextShow"]) {
					$includeAreaName = 'anounce';
                    include 'include_handler.php';
                }
                if (count($arParams["PREVIEW_DETAIL_PROPERTY_CODE"]) > 0) {
					$includeAreaName = 'preview_props';
					include 'include_handler.php';
                }?>
            </div>
        <?}?>
    </div>
    <div class="clearfix"></div>
</div>
<div class="row tb20 <?if($noTabs) echo "bxr-detail-block-no-tabs";?>" id="bxr-detail-block-wrap">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <?
            if ($arResult['OFFER_GROUP']) 
                include_once 'set.php';
         
            if(!$noTabs)
                include_once 'tabs.php';
            
            if(is_array($arResult["TABS"]) && count($arResult["TABS"]) > 0):
                foreach ($arResult["TABS"] as $k => $tab):
                        if(isset($tab["epilog"]) && $tab["epilog"]) 
                                continue;
                    
                        if(is_array($tab["echo"]) && count($tab["echo"]) > 0)
                                foreach($tab["echo"] as $v) {
                                    echo $v;
                                }
                            
                        if(is_array($tab["include_top"]) && count($tab["include_top"]) > 0)
                                foreach($tab["include_top"] as $v) {
                                    include_once $v;
                                }
                            
                        if(empty($tab["name"]))
                                continue;
                        
                        if($k == "props" && count($arResult["DISPLAY_PROPERTIES"])===0)
                                continue;
                    ?>
                    <h3 class="bxr-detail-tab-mobile-title  hidden-lg hidden-md hidden-sm" data-tab="<?=$k?>"><?=$tab["name"]?></h3><hr class="section">
                    <div class="bxr-detail-tab <?=$tab["class"]?>"<?=(strlen($tab["id"]) > 0)?'id="'.$tab["id"].'"':''?> data-tab="<?=$k?>">
                        <?
                            if(is_array($tab["include"]) && count($tab["include"])>0)
                                    foreach($tab["include"] as $v) {
                                        include_once $v;
                                    }
                        ?>
                    </div>
            <?  endforeach;
            endif;