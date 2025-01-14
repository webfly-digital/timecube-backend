<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
$APPLICATION->SetAdditionalCSS('/local/templates/wf_timecube/components/bitrix/news/faq_new0721/style/style.css', true);
$this->addExternalJs('/local/templates/wf_timecube/components/bitrix/news/faq_new0721/script.js');
?>

<div class="lr-blog-container">
    <? $ElementID = $APPLICATION->IncludeComponent(
        "bitrix:news.detail",
        "",
        array(
            "DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
            "DISPLAY_NAME" => $arParams["DISPLAY_NAME"],
            "DISPLAY_PICTURE" => $arParams["DISPLAY_PICTURE"],
            "DISPLAY_PREVIEW_TEXT" => $arParams["DISPLAY_PREVIEW_TEXT"],
            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "FIELD_CODE" => $arParams["DETAIL_FIELD_CODE"],
            "PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
            "DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["detail"],
            "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
            "META_KEYWORDS" => $arParams["META_KEYWORDS"],
            "META_DESCRIPTION" => $arParams["META_DESCRIPTION"],
            "BROWSER_TITLE" => $arParams["BROWSER_TITLE"],
            "SET_CANONICAL_URL" => $arParams["DETAIL_SET_CANONICAL_URL"],
            "DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
            "SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
            "SET_TITLE" => $arParams["SET_TITLE"],
            "MESSAGE_404" => $arParams["MESSAGE_404"],
            "SET_STATUS_404" => $arParams["SET_STATUS_404"],
            "SHOW_404" => $arParams["SHOW_404"],
            "FILE_404" => $arParams["FILE_404"],
            "INCLUDE_IBLOCK_INTO_CHAIN" => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
            "ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
            "ACTIVE_DATE_FORMAT" => $arParams["DETAIL_ACTIVE_DATE_FORMAT"],
            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
            "CACHE_TIME" => $arParams["CACHE_TIME"],
            "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
            "USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
            "GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
            "DISPLAY_TOP_PAGER" => $arParams["DETAIL_DISPLAY_TOP_PAGER"],
            "DISPLAY_BOTTOM_PAGER" => $arParams["DETAIL_DISPLAY_BOTTOM_PAGER"],
            "PAGER_TITLE" => $arParams["DETAIL_PAGER_TITLE"],
            "PAGER_SHOW_ALWAYS" => "N",
            "PAGER_TEMPLATE" => $arParams["DETAIL_PAGER_TEMPLATE"],
            "PAGER_SHOW_ALL" => $arParams["DETAIL_PAGER_SHOW_ALL"],
            "CHECK_DATES" => $arParams["CHECK_DATES"],
            "ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
            "ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
            "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
            "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
            "IBLOCK_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["news"],
            "USE_SHARE" => $arParams["USE_SHARE"],
            "SHARE_HIDE" => $arParams["SHARE_HIDE"],
            "SHARE_TEMPLATE" => $arParams["SHARE_TEMPLATE"],
            "SHARE_HANDLERS" => $arParams["SHARE_HANDLERS"],
            "SHARE_SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
            "SHARE_SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
            "ADD_ELEMENT_CHAIN" => (isset($arParams["ADD_ELEMENT_CHAIN"]) ? $arParams["ADD_ELEMENT_CHAIN"] : ''),
            'STRICT_SECTION_CHECK' => (isset($arParams['STRICT_SECTION_CHECK']) ? $arParams['STRICT_SECTION_CHECK'] : ''),
        ),
        $component
    ); ?>
    <div class="content">
        <div class="right">
            <h3>Вас может заинтересовать</h3>
            <?
            global $aFilter;
            $aFilter = array("!ID" => $ElementID);
            $APPLICATION->IncludeComponent(
                "bitrix:news.list",
                "card",
                array(
                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                    "NEWS_COUNT" => 3,
                    "SORT_BY1" => $arParams["SORT_BY1"],
                    "SORT_ORDER1" => $arParams["SORT_ORDER1"],
                    "SORT_BY2" => $arParams["SORT_BY2"],
                    "SORT_ORDER2" => $arParams["SORT_ORDER2"],
                    "FIELD_CODE" => $arParams["LIST_FIELD_CODE"],
                    "PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
                    "DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
                    "SET_TITLE" => "N",
                    "SET_BROWSER_TITLE" => "N",
                    "SET_META_KEYWORDS" => "N",
                    "SET_META_DESCRIPTION" => "N",
                    "SET_LAST_MODIFIED" => "N",
                    "MESSAGE_404" => $arParams["MESSAGE_404"],
                    "SET_STATUS_404" => $arParams["SET_STATUS_404"],
                    "SHOW_404" => $arParams["SHOW_404"],
                    "FILE_404" => $arParams["FILE_404"],
                    "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                    "ADD_SECTIONS_CHAIN" => "N",
                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                    "CACHE_FILTER" => $arParams["CACHE_FILTER"],
                    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                    "DISPLAY_TOP_PAGER" => 'N',
                    "DISPLAY_BOTTOM_PAGER" => "N",
                    "PAGER_TITLE" => $arParams["PAGER_TITLE"],
                    "PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
                    "PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
                    "PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
                    "PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
                    "PAGER_BASE_LINK_ENABLE" => $arParams["PAGER_BASE_LINK_ENABLE"],
                    "PAGER_BASE_LINK" => $arParams["PAGER_BASE_LINK"],
                    "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
                    "DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
                    "DISPLAY_NAME" => "N",
                    "DISPLAY_PICTURE" => 'N',
                    "DISPLAY_PREVIEW_TEXT" => 'N',
                    "PREVIEW_TRUNCATE_LEN" => $arParams["PREVIEW_TRUNCATE_LEN"],
                    "ACTIVE_DATE_FORMAT" => $arParams["LIST_ACTIVE_DATE_FORMAT"],
                    "USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
                    "GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
                    "HIDE_LINK_WHEN_NO_DETAIL" => $arParams["HIDE_LINK_WHEN_NO_DETAIL"],
                    "CHECK_DATES" => $arParams["CHECK_DATES"],
                    "STRICT_SECTION_CHECK" => $arParams["STRICT_SECTION_CHECK"],

                    "PARENT_SECTION" => $arResult["VARIABLES"]["SECTION_ID"],
                    "PARENT_SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
                    "DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["detail"],
                    "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
                    "IBLOCK_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["news"],

                    "FILTER_NAME" => "aFilter"
                ),
                $component
            );
            ?>
        </div>
    </div>
</div>

<?php global $USER;
$res = CIBlockElement::GetList([], ["ID" => $ElementID, "IBLOCK_ID" => $arParams["IBLOCK_ID"]], false, [], ['PROPERTY_POPULAR_PRODUCT', 'PROPERTY_LINK']);
$link = '';

while ($ar_fields = $res->Fetch()) {
    $link = $ar_fields["PROPERTY_LINK_VALUE"];
    $arrIdProduct[] = $ar_fields["PROPERTY_POPULAR_PRODUCT_VALUE"];
}
?>
<?php if (!empty($arrIdProduct)):
    global $bFilter;
    $bFilter = array("ID" => $arrIdProduct, '!PROPERTY_XXX_SHOW_ON_TIMECUBE_RU' => false);
    ?>
    <div class="container-fluid">
        <h2>Популярные товары</h2>
        <?
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
            'FILTER_NAME' => 'bFilter',
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
            'PAGE_ELEMENT_COUNT' => '8',
            'LINE_ELEMENT_COUNT' => '8',
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
            'HIDE_NOT_AVAILABLE' => 'N',
            'HIDE_NOT_AVAILABLE_OFFERS' => 'N',
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
        $APPLICATION->IncludeComponent('bitrix:catalog.section', 'slider',
            $sectionParams,
            $component, ['HIDE_ICONS' => 'N']
        );
        ?>
    </div>
<?php endif ?>
<?php if (!empty($link)): ?>
    <div class="container-fluid">
        <a href="<?= $link ?>" class="btn btn-primary btn-fullwidth">Перейти к ассортименту</a>
    </div>
<?php endif ?>

