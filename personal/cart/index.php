<?
define("HIDE_SIDEBAR", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Корзина");
global $USER;

?>
    <div class="three-columns" id="inner-page">
        <section class="three-columns__body">
            <div class="container-fluid breadcrumbs-wrapper">
                <?
                $APPLICATION->IncludeComponent("bitrix:breadcrumb","catalog",
                    ["START_FROM" => "0","PATH" => "","SITE_ID" => "s1"],
                    false,["HIDE_ICONS" => "Y"]
                );
                ?>
            </div>
            <div class="container-fluid">
                <div class="heading mb-5">
                    <div class="heading__item">
                        <h1 class="heading__title"><?=$APPLICATION->ShowTitle()?></h1>
                    </div>
                    <div class="heading-item">
                        <a class="heading__link link-back" href="javascript:window.history.back();">Вернуться к покупкам</a>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
<?$APPLICATION->IncludeComponent(
	"bitrix:sale.basket.basket", 
	"bootstrap_v5", 
	array(
		"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
		"COLUMNS_LIST" => array(
			0 => "NAME",
			1 => "DISCOUNT",
			2 => "PRICE",
			3 => "QUANTITY",
			4 => "SUM",
			5 => "PROPS",
			6 => "DELETE",
			7 => "DELAY",
		),
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"PATH_TO_ORDER" => "/personal/order/make/",
		"HIDE_COUPON" => "N",
		"QUANTITY_FLOAT" => "N",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"TEMPLATE_THEME" => "site",
		"SET_TITLE" => "Y",
		"AJAX_OPTION_ADDITIONAL" => "",
		"OFFERS_PROPS" => array(
			0 => "SIZES_SHOES",
			1 => "SIZES_CLOTHES",
			2 => "COLOR_REF",
		),
		"DEFERRED_REFRESH" => "N",
		"USE_DYNAMIC_SCROLL" => "Y",
		"SHOW_FILTER" => "N",
		"SHOW_RESTORE" => "Y",
		"COLUMNS_LIST_EXT" => array(
			0 => "PREVIEW_PICTURE",
			1 => "DISCOUNT",
			2 => "PROPS",
			3 => "DELETE",
			4 => "DELAY",
			5 => "SUM",
		),
		"COLUMNS_LIST_MOBILE" => array(
			0 => "PREVIEW_PICTURE",
			1 => "DISCOUNT",
			2 => "DELETE",
			3 => "SUM",
		),
		"TOTAL_BLOCK_DISPLAY" => array(
			0 => "top",
		),
		"DISPLAY_MODE" => "extended",
		"PRICE_DISPLAY_MODE" => "Y",
		"SHOW_DISCOUNT_PERCENT" => "Y",
		"DISCOUNT_PERCENT_POSITION" => "bottom-right",
		"PRODUCT_BLOCKS_ORDER" => $USER->isAdmin()?"props,sku,columns":"sku,columns",
		"USE_PRICE_ANIMATION" => "Y",
		"LABEL_PROP" => array(
		),
		"USE_PREPAYMENT" => "N",
		"CORRECT_RATIO" => "Y",
		"AUTO_CALCULATION" => "Y",
		"ACTION_VARIABLE" => "basketAction",
		"COMPATIBLE_MODE" => "N",
		"EMPTY_BASKET_HINT_PATH" => "/",
		"ADDITIONAL_PICT_PROP_2" => "-",
		"ADDITIONAL_PICT_PROP_3" => "-",
		"BASKET_IMAGES_SCALING" => "adaptive",
		"USE_GIFTS" => "N",
		"USE_ENHANCED_ECOMMERCE" => "Y",
		"ADDITIONAL_PICT_PROP_9" => "-",
		"ADDITIONAL_PICT_PROP_10" => "-",
		"COMPONENT_TEMPLATE" => "bootstrap_v5",
		"ADDITIONAL_PICT_PROP_11" => "-",
		"DATA_LAYER_NAME" => "dataLayer",
		"BRAND_PROPERTY" => "PROPERTY_MANUFACTUR"
	),
	false
);?>

            </div>
        </section>
    </div>

<?$APPLICATION->IncludeComponent(
    "bitrix:catalog.section",
    "select-pack",
    array(
        "IBLOCK_TYPE" => WF_CATALOG_IBLOCK_TYPE,
        "IBLOCK_ID" => WF_CATALOG_IBLOCK_ID,
        "SECTION_ID" => '',
        "SECTION_CODE" => WF_PACK_SECTION_CODE,
        "SECTION_USER_FIELDS" => [],
        "FILTER_NAME" => "arrFilter",
        "INCLUDE_SUBSECTIONS" => "Y",
        "SHOW_ALL_WO_SECTION" => "N",
        "CUSTOM_FILTER" => "",
        "HIDE_NOT_AVAILABLE" => "N",
        "HIDE_NOT_AVAILABLE_OFFERS" => "N",
        "ELEMENT_SORT_FIELD" => "sort",
        "ELEMENT_SORT_ORDER" => "asc",
        "ELEMENT_SORT_FIELD2" => "id",
        "ELEMENT_SORT_ORDER2" => "desc",
        "PAGE_ELEMENT_COUNT" => "24",
        "LINE_ELEMENT_COUNT" => "3",
        "PROPERTY_CODE" => [],
        "BACKGROUND_IMAGE" => "-",
        "TEMPLATE_THEME" => "blue",
        "ADD_PICT_PROP" => "-",
        "LABEL_PROP" => "-",
        "PRODUCT_SUBSCRIPTION" => "N",
        "SHOW_DISCOUNT_PERCENT" => "N",
        "SHOW_OLD_PRICE" => "N",
        "SHOW_CLOSE_POPUP" => "N",
        "MESS_BTN_BUY" => "Купить",
        "MESS_BTN_ADD_TO_BASKET" => "В корзину",
        "MESS_BTN_SUBSCRIBE" => "Подписаться",
        "MESS_BTN_COMPARE" => "Сравнить",
        "MESS_BTN_DETAIL" => "Подробнее",
        "MESS_NOT_AVAILABLE" => "Нет в наличии",
        "SECTION_URL" => "",
        "DETAIL_URL" => "",
        "SECTION_ID_VARIABLE" => "SECTION_ID",
        "SEF_MODE" => "N",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "N",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "36000000",
        "CACHE_GROUPS" => "Y",
        "SET_TITLE" => "N",
        "SET_BROWSER_TITLE" => "N",
        "BROWSER_TITLE" => "-",
        "SET_META_KEYWORDS" => "Y",
        "META_KEYWORDS" => "-",
        "SET_META_DESCRIPTION" => "Y",
        "META_DESCRIPTION" => "-",
        "SET_LAST_MODIFIED" => "N",
        "USE_MAIN_ELEMENT_SECTION" => "N",
        "ADD_SECTIONS_CHAIN" => "N",
        "CACHE_FILTER" => "N",
        "ACTION_VARIABLE" => "action",
        "PRODUCT_ID_VARIABLE" => "id",
        "PRICE_CODE" => [WF_CATALOG_PRICE_TYPE],
        "USE_PRICE_COUNT" => "N",
        "SHOW_PRICE_COUNT" => "1",
        "PRICE_VAT_INCLUDE" => "Y",
        "CONVERT_CURRENCY" => "Y",
        "CURRENCY_ID" => "RUB",
        "BASKET_URL" => "/personal/basket.php",
        "USE_PRODUCT_QUANTITY" => "N",
        "PRODUCT_QUANTITY_VARIABLE" => "quantity",
        "ADD_PROPERTIES_TO_BASKET" => "Y",
        "PRODUCT_PROPS_VARIABLE" => "prop",
        "PARTIAL_PRODUCT_PROPERTIES" => "N",
        "PRODUCT_PROPERTIES" => [],
        "ADD_TO_BASKET_ACTION" => "ADD",
        "DISPLAY_COMPARE" => "N",
        "PAGER_TEMPLATE" => ".default",
        "DISPLAY_TOP_PAGER" => "N",
        "DISPLAY_BOTTOM_PAGER" => "N",
        "PAGER_TITLE" => "Товары",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "N",
        "PAGER_BASE_LINK_ENABLE" => "N",
        "SET_STATUS_404" => "N",
        "SHOW_404" => "N",
        "MESSAGE_404" => "",
        "COMPATIBLE_MODE" => "N",
        "DISABLE_INIT_JS_IN_COMPONENT" => "N"
    ),
    false, ['HIDE_ICONS'=>'Y']
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>