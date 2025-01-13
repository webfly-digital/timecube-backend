<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Избранные товары");
$APPLICATION->SetPageProperty("description", "Избранные товары в интернет-магазине Timecube. Звоните: 8 800 775-25-76!");
$APPLICATION->SetTitle("Избранное");
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
                <div class="heading">
                    <div class="heading__item">
                        <h1 class="heading__title"><?=$APPLICATION->ShowTitle(false)?></h1>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <?
                CBitrixComponent::includeComponentClass('webfly:favorites');
                $fav = new WFFavorites();
                $list = $fav->getFavorites();
                if (!empty($list)) {
                    global $arrFilter;
                    $arrFilter = ['=ID' => $list];
                    $APPLICATION->includeComponent('bitrix:catalog.section', 'bootstrap_v4',
                        [
                            'IBLOCK_TYPE' => WF_CATALOG_IBLOCK_TYPE,
                            'IBLOCK_ID' => WF_CATALOG_IBLOCK_ID,
                            'ELEMENT_SORT_FIELD' => 'desc',
                            'ELEMENT_SORT_ORDER' => 'asc',
                            'ELEMENT_SORT_FIELD2' => 'id',
                            'ELEMENT_SORT_ORDER2' => 'desc',
                            'PROPERTY_CODE' => ['NEWPRODUCT','SALELEADER','SPECIALOFFER'],
                            'PROPERTY_CODE_MOBILE' => [],
                            'META_KEYWORDS' => 'UF_KEYWORDS',
                            'META_DESCRIPTION' => 'UF_META_DESCRIPTION',
                            'BROWSER_TITLE' => 'UF_BROWSER_TITLE',
                            'SET_LAST_MODIFIED' => 'N',
                            'INCLUDE_SUBSECTIONS' => 'Y',
                            'BASKET_URL' => '/personal/cart/',
                            'ACTION_VARIABLE' => 'action',
                            'PRODUCT_ID_VARIABLE' => 'id',
                            'SECTION_ID_VARIABLE' => 'SECTION_ID',
                            'PRODUCT_QUANTITY_VARIABLE' => 'quantity',
                            'PRODUCT_PROPS_VARIABLE' => 'prop',
                            'FILTER_NAME' => 'arrFilter',
                            'CACHE_TYPE' => 'N',
                            'CACHE_TIME' => '1',
                            'CACHE_FILTER' => 'N',
                            'CACHE_GROUPS' => 'N',
                            'SET_TITLE' => 'Y',
                            'MESSAGE_404' => '',
                            'SET_STATUS_404' => 'N',
                            'SHOW_404' => 'N',
                            'FILE_404' => '',
                            'DISPLAY_COMPARE' => 'Y',
                            'PAGE_ELEMENT_COUNT' => '15',
                            'LINE_ELEMENT_COUNT' => '3',
                            'PRICE_CODE' => [WF_CATALOG_PRICE_TYPE],
                            'USE_PRICE_COUNT' => 'N',
                            'SHOW_PRICE_COUNT' => '1',
                            'PRICE_VAT_INCLUDE' => 'Y',
                            'USE_PRODUCT_QUANTITY' => 'N',
                            'ADD_PROPERTIES_TO_BASKET' => 'Y',
                            'PARTIAL_PRODUCT_PROPERTIES' => 'N',
                            'PRODUCT_PROPERTIES' => '',
                            'DISPLAY_TOP_PAGER' => 'Y',
                            'DISPLAY_BOTTOM_PAGER' => 'Y',
                            'PAGER_TITLE' => 'Товары',
                            'PAGER_SHOW_ALWAYS' => 'Y',
                            'PAGER_TEMPLATE' => 'bootstrap_v4',
                            'PAGER_DESC_NUMBERING' => 'N',
                            'PAGER_DESC_NUMBERING_CACHE_TIME' => '36000000',
                            'PAGER_SHOW_ALL' => 'Y',
                            'PAGER_BASE_LINK_ENABLE' => 'N',
                            'PAGER_BASE_LINK' => NULL,
                            'PAGER_PARAMS_NAME' => NULL,
                            'LAZY_LOAD' => 'Y',
                            'MESS_BTN_LAZY_LOAD' => NULL,
                            'LOAD_ON_SCROLL' => 'N',
                            'OFFERS_CART_PROPERTIES' =>NULL,
                            'OFFERS_FIELD_CODE' =>
                                [
                                    0 => 'NAME',
                                    1 => 'PREVIEW_PICTURE',
                                    2 => 'DETAIL_PICTURE',
                                    3 => '',
                                ],
                            'OFFERS_PROPERTY_CODE' => NULL,
                            'OFFERS_SORT_FIELD' => 'sort',
                            'OFFERS_SORT_ORDER' => 'desc',
                            'OFFERS_SORT_FIELD2' => 'id',
                            'OFFERS_SORT_ORDER2' => 'desc',
                            'OFFERS_LIMIT' => '0',
                            'SECTION_ID' => NULL,
                            'SECTION_CODE' => '',
                            'SECTION_URL' => '/#SECTION_CODE#/',
                            'DETAIL_URL' => '/#SECTION_CODE#/#ELEMENT_CODE#/',
                            'USE_MAIN_ELEMENT_SECTION' => 'Y',
                            'CONVERT_CURRENCY' => 'N',
                            'CURRENCY_ID' => NULL,
                            'HIDE_NOT_AVAILABLE' => 'N',
                            'HIDE_NOT_AVAILABLE_OFFERS' => 'N',
                            'LABEL_PROP' =>
                                [
                                    0 => 'NEWPRODUCT',
                                    1 => 'SALELEADER',
                                    2 => 'SPECIALOFFER',
                                ],
                            'LABEL_PROP_MOBILE' => [],
                            'LABEL_PROP_POSITION' => 'top-left',
                            'ADD_PICT_PROP' => 'MORE_PHOTO',
                            'PRODUCT_DISPLAY_MODE' => 'Y',
                            'PRODUCT_BLOCKS_ORDER' => 'price,props,sku,quantityLimit,quantity,buttons',
                            'ENLARGE_PRODUCT' => 'STRICT',
                            'ENLARGE_PROP' => '',
                            'SHOW_SLIDER' => 'N',
                            'SLIDER_INTERVAL' => '',
                            'SLIDER_PROGRESS' => '',
                            'OFFER_ADD_PICT_PROP' => 'MORE_PHOTO',
                            'OFFER_TREE_PROPS' => NULL,
                            'PRODUCT_SUBSCRIPTION' => 'Y',
                            'SHOW_DISCOUNT_PERCENT' => 'Y',
                            'DISCOUNT_PERCENT_POSITION' => 'bottom-right',
                            'SHOW_OLD_PRICE' => 'Y',
                            'SHOW_MAX_QUANTITY' => 'N',
                            'MESS_SHOW_MAX_QUANTITY' => '',
                            'RELATIVE_QUANTITY_FACTOR' => '',
                            'MESS_RELATIVE_QUANTITY_MANY' => '',
                            'MESS_RELATIVE_QUANTITY_FEW' => '',
                            'MESS_BTN_BUY' => 'Купить',
                            'MESS_BTN_ADD_TO_BASKET' => 'В корзину',
                            'MESS_BTN_SUBSCRIBE' => 'Подписаться',
                            'MESS_BTN_DETAIL' => 'Подробнее',
                            'MESS_NOT_AVAILABLE' => 'Нет в наличии',
                            'MESS_BTN_COMPARE' => 'Сравнение',
                            'USE_ENHANCED_ECOMMERCE' => 'N',
                            'DATA_LAYER_NAME' => '',
                            'BRAND_PROPERTY' => '',
                            'TEMPLATE_THEME' => 'site',
                            'ADD_SECTIONS_CHAIN' => 'N',
                            'ADD_TO_BASKET_ACTION' => 'ADD',
                            'SHOW_CLOSE_POPUP' => 'N',
                            'COMPARE_PATH' => '/compare/',
                            'COMPARE_NAME' => 'CATALOG_COMPARE_LIST',
                            'USE_COMPARE_LIST' => 'Y',
                            'BACKGROUND_IMAGE' => 'UF_BACKGROUND_IMAGE',
                            'COMPATIBLE_MODE' => 'N',
                            'DISABLE_INIT_JS_IN_COMPONENT' => 'N',
                        ],
                        false, ['HIDE_ICONS' => 'Y']

                    );
                }
                ?>

            </div>
        </section>
        <?$APPLICATION->IncludeComponent("bitrix:main.include","",
            ["AREA_FILE_SHOW" => "file","PATH" => SITE_DIR."include/inner_aside.php"], false, ['HIDE_ICONS' => 'Y']
        );?>

    </div>

<?require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");