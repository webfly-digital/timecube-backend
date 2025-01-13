<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Timecube – интернет-магазин аксессуаров и шкатулок для часов и украшений в Москве");
?>
    <!--                           Open Graph-->
    <div style="display:none;">
        <meta property="og:title" content="<?$APPLICATION->ShowTitle(false)?>"/>
        <meta property="og:description" content="<?$APPLICATION->ShowProperty("description")?>"/>
        <meta property="og:image" content="https://timecube.ru/upload/ammina.optimizer/jpg-webp/q80/upload/iblock/c38/c38e5175afaa4a2a68e353af5d8e9ab7.webp"/>
        <meta property="og:type" content="website"/>
        <meta property="og:url" content="https://<?=SITE_SERVER_NAME."/"?>"/>
        <meta property="og:locale" content="ru_RU"/>
        <meta property="og:site_name" content="timecube.ru"/>
    </div>
    <!--                          end  Open Graph-->
        <div class="container-fluid">
            <? $APPLICATION->IncludeComponent(
	"bitrix:news.list", 
	"actions_slider", 
	array(
		"IBLOCK_TYPE" => "news",
		"IBLOCK_ID" => WF_ACTIONS_IBLOCK_ID,
		"NEWS_COUNT" => "20",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"FILTER_NAME" => "",
		"FIELD_CODE" => array(
			0 => "DETAIL_PICTURE",
			1 => "",
		),
		"PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "N",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"PREVIEW_TRUNCATE_LEN" => "",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"SET_TITLE" => "N",
		"SET_BROWSER_TITLE" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_LAST_MODIFIED" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"INCLUDE_SUBSECTIONS" => "N",
		"STRICT_SECTION_CHECK" => "N",
		"PAGER_TEMPLATE" => ".default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => "",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"SET_STATUS_404" => "N",
		"SHOW_404" => "N",
		"MESSAGE_404" => "",
		"COMPONENT_TEMPLATE" => "actions_slider"
	),
	false
);

            $APPLICATION->IncludeComponent("bitrix:main.include", "",
                ["AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/utp.php"],
                false, ['HIDE_ICONS' => 'N']
            );

            $sectionParams = [
                'IBLOCK_ID' => WF_CATALOG_IBLOCK_ID,
                'IBLOCK_TYPE' => WF_CATALOG_IBLOCK_TYPE,
                'ELEMENT_SORT_FIELD' => 'catalog_PRICE_2',
                'ELEMENT_SORT_ORDER' => 'asc',
                'ELEMENT_SORT_FIELD2' => 'SORT',
                'ELEMENT_SORT_ORDER2' => 'desc',
                'PROPERTY_CODE' => [ 'NEWPRODUCT', 'SALELEADER', 'SPECIALOFFER'],
                'PROPERTY_CODE_MOBILE' => [],
                'META_KEYWORDS' => '-',
                'META_DESCRIPTION' => '-',
                'BROWSER_TITLE' => '-',
                "SET_BROWSER_TITLE" => "N",
                "SET_META_DESCRIPTION" => "N",
                "SET_META_KEYWORDS" => 'N',
                'SET_LAST_MODIFIED' => 'N',
                'INCLUDE_SUBSECTIONS' => 'Y',
                'BASKET_URL' => '/personal/cart/',
                'ACTION_VARIABLE' => 'action',
                'PRODUCT_ID_VARIABLE' => 'id',
                'SECTION_ID_VARIABLE' => 'SECTION_ID',
                'PRODUCT_QUANTITY_VARIABLE' => 'quantity',
                'PRODUCT_PROPS_VARIABLE' => 'prop',
                'FILTER_NAME' => 'arrFilter',
                'CACHE_TYPE' => 'A',
                'CACHE_TIME' => '36000000',
                'CACHE_FILTER' => 'Y',
                'CACHE_GROUPS' => 'Y',
                'SET_TITLE' => 'N',
                'MESSAGE_404' => '',
                'SET_STATUS_404' => 'N',
                'SHOW_404' => 'N',
                'FILE_404' => '',
                'DISPLAY_COMPARE' => 'Y',
                'PAGE_ELEMENT_COUNT' => '6',
                'LINE_ELEMENT_COUNT' => '6',
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
                'PAGER_TEMPLATE' => '',
                'PAGER_DESC_NUMBERING' => 'N',
                'PAGER_DESC_NUMBERING_CACHE_TIME' => '36000000',
                'PAGER_SHOW_ALL' => 'Y',
                'PAGER_BASE_LINK_ENABLE' => 'N',
                'LAZY_LOAD' => 'Y',
                'LOAD_ON_SCROLL' => 'N',
                'OFFERS_CART_PROPERTIES' =>
                    [
                        0 => 'SIZES_SHOES',
                        1 => 'SIZES_CLOTHES',
                        2 => 'COLOR_REF',
                    ],
                'OFFERS_FIELD_CODE' =>
                    [
                        0 => 'NAME',
                        1 => 'PREVIEW_PICTURE',
                        2 => 'DETAIL_PICTURE',
                    ],
                'OFFERS_PROPERTY_CODE' =>
                    [
                        0 => 'SIZES_SHOES',
                        1 => 'SIZES_CLOTHES',
                        2 => 'COLOR_REF',
                        3 => 'MORE_PHOTO',
                        4 => 'ARTNUMBER',
                    ],
                'OFFERS_SORT_FIELD' => 'sort',
                'OFFERS_SORT_ORDER' => 'desc',
                'OFFERS_SORT_FIELD2' => 'id',
                'OFFERS_SORT_ORDER2' => 'desc',
                'OFFERS_LIMIT' => '0',
                'SECTION_CODE' => WF_CATALOG_ROOT,
                'SECTION_URL' => '/#SECTION_CODE#/',
                'DETAIL_URL' => '/product/#ELEMENT_CODE#/',
                'USE_MAIN_ELEMENT_SECTION' => 'Y',
                'CONVERT_CURRENCY' => 'N',
                'HIDE_NOT_AVAILABLE' => 'Y',
                'HIDE_NOT_AVAILABLE_OFFERS' => 'Y',
                'LABEL_PROP' =>
                    [
                        'NEWPRODUCT',
                        'SALELEADER',
                        'SPECIALOFFER',
                    ],
                'LABEL_PROP_MOBILE' =>
                    [],
                'LABEL_PROP_POSITION' => 'top-left',
                'ADD_PICT_PROP' => 'MORE_PHOTO',
                'PRODUCT_DISPLAY_MODE' => 'Y',
                'PRODUCT_BLOCKS_ORDER' => 'price,props,sku,quantityLimit,quantity,buttons',
                'ENLARGE_PRODUCT' => 'STRICT',
                'ENLARGE_PROP' => '',
                'OFFER_ADD_PICT_PROP' => 'MORE_PHOTO',
                'OFFER_TREE_PROPS' =>
                    [
                        'SIZES_SHOES',
                        'SIZES_CLOTHES',
                        'COLOR_REF',
                    ],
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
                'DISABLE_INIT_JS_IN_COMPONENT' => 'Y',
            ];

            // region HITS

            ?>
            <section class="mt-5">
                <h1 class="main-heading text-center">TIMECUBE - аксессуары для часов</h1>
                <div class="heading">
                    <div class="heading__item">
                        <h3 class="heading__title">Хиты продаж</h3>
                    </div>
                </div>
                <?
                global $arrFilter;
                $arrFilter = ['!PROPERTY_HIT' => false, '!PROPERTY_XXX_SHOW_ON_TIMECUBE_RU'=> false];
                $APPLICATION->includeComponent('bitrix:catalog.section', 'slider',
                    $sectionParams,
                    false, ['HIDE_ICONS' => 'Y']
                );
                ?>
            </section>
            <?

            // endregion HITS

            // region DISCOUNT

            ?>
            <section class="mt-5">
                <div class="heading">
                    <div class="heading__item">
                        <h3 class="heading__title">Дисконт</h3>
                    </div>
                </div>
                <?
                global $arrFilter;
                $arrFilter = ['!PROPERTY_XXX_SHOW_ON_TIMECUBE_RU'=> false];
                $sectionParams['SECTION_CODE'] = 'diskont';
                $APPLICATION->includeComponent('bitrix:catalog.section', 'slider',
                    $sectionParams,
                    false, ['HIDE_ICONS' => 'Y']
                );
                ?>
            </section>
            <?

            // endregion DISCOUNT

            // region NEW

            ?>
            <section class="mt-5">
                <div class="heading">
                    <div class="heading__item">
                        <h3 class="heading__title">Новинки</h3>
                    </div>
                </div>
                <?
                global $arrFilter;
                $arrFilter = ['SECTION_ID' => [75], '!PROPERTY_XXX_SHOW_ON_TIMECUBE_RU'=> false];
                $replaceParams = [
                    "ELEMENT_SORT_FIELD" => "CREATED_DATE",
                    "ELEMENT_SORT_ORDER" => "DESC",
                    "ELEMENT_SORT_FIELD2" => "SORT",
                    "ELEMENT_SORT_ORDER2" => "ASC"
                ];
                $APPLICATION->includeComponent('bitrix:catalog.section', 'slider',
                    array_replace($sectionParams, $replaceParams),
                    false, ['HIDE_ICONS' => 'Y']
                );
                ?>
            </section>
            <?

            // endregion NEW

            // region BRANDS

            ?>
            <section class="mt-5" id="brands-section">
                <div class="heading">
                    <div class="heading__item">
                        <h3 class="heading__title">Популярные производители</h3>
                    </div>
                    <div class="heading__item"><a class="heading__link" href="/manufacturers/">Все производители</a>
                    </div>
                </div>
                <?$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"brands",
	array(
		"COMPONENT_TEMPLATE" => "brands",
		"IBLOCK_TYPE" => "news",
		"IBLOCK_ID" => WF_MANUFACTURERS_IBLOCK_ID,
		"NEWS_COUNT" => "10",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"FILTER_NAME" => "",
		"FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"PREVIEW_TRUNCATE_LEN" => "",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"SET_TITLE" => "N",
		"SET_BROWSER_TITLE" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_LAST_MODIFIED" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
		"ADD_SECTIONS_CHAIN" => "Y",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"INCLUDE_SUBSECTIONS" => "Y",
		"STRICT_SECTION_CHECK" => "N",
		"PAGER_TEMPLATE" => ".default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Новости",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"SET_STATUS_404" => "N",
		"SHOW_404" => "N",
		"MESSAGE_404" => ""
	),
	false
);?>
            </section>
            <?

            // endregion BRANDS

            // region SUBSCRIBE

            $APPLICATION->IncludeComponent(
                "bitrix:sender.subscribe",
                ".default",
                array(
                    "SET_TITLE" => "N",
                    "COMPONENT_TEMPLATE" => ".default",
                    "USE_PERSONALIZATION" => "Y",
                    "CONFIRMATION" => "N",
                    "HIDE_MAILINGS" => "N",
                    "SHOW_HIDDEN" => "N",
                    "USER_CONSENT" => "Y",
                    "USER_CONSENT_ID" => "1",
                    "USER_CONSENT_IS_CHECKED" => "Y",
                    "USER_CONSENT_IS_LOADED" => "N",
                    "AJAX_MODE" => "Y",
                    "AJAX_OPTION_JUMP" => "N",
                    "AJAX_OPTION_STYLE" => "N",
                    "AJAX_OPTION_HISTORY" => "N",
                    "AJAX_OPTION_ADDITIONAL" => "",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "3600"
                ),
                false
            );

            // endregion SUBSCRIBE

            ?>
        </div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>