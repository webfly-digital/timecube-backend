<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @global CMain $APPLICATION
 * @var CBitrixComponent $component
 * @var array $arParams
 * @var array $arResult
 * @var array $arCurSection
 */

if (isset($arParams['USE_COMMON_SETTINGS_BASKET_POPUP']) && $arParams['USE_COMMON_SETTINGS_BASKET_POPUP'] == 'Y') {
    $basketAction = isset($arParams['COMMON_ADD_TO_BASKET_ACTION']) ? $arParams['COMMON_ADD_TO_BASKET_ACTION'] : '';
} else {
    $basketAction = isset($arParams['SECTION_ADD_TO_BASKET_ACTION']) ? $arParams['SECTION_ADD_TO_BASKET_ACTION'] : '';
}

global $arrFilter;
$arParams["FILTER_NAME"] = 'arrFilter';

$newFilter = [];
if ($APPLICATION->GetCurPage(false) != '/aksessuary_dlya_shkatulok_dlya_chasov_s_avtopodzavodom/') {
    $arrFilter['!IBLOCK_SECTION_ID'] = '99';
    //$arrFilter['!SECTION_ID'] = '142';
    //$arrFilter['!SECTION_CODE'] = ['aksessuary_dlya_shkatulok_dlya_chasov_s_avtopodzavodom'];
    //$arrFilter['!=IBLOCK_SECTION_CODE'] = 'aksessuary_dlya_shkatulok_dlya_chasov_s_avtopodzavodom';
    $newFilter['!IBLOCK_SECTION_ID'] = '99';
}

//$arrFilter['PROPERTY_XXX_DONT_SHOW_ON_SITE'] = false;
$arrFilter['!PROPERTY_XXX_SHOW_ON_TIMECUBE_RU'] = false;//овечает за вывод отваров на сайте
$catalog_filter_button = <<<HTML
<button class="btn-rounded sidebar-control btn-secondary" data-target="mobile-filters-menu">
	<span class="btn-rounded__icon svg-icon icon-filter"></span>
	<span class="btn-rounded__caption">Фильтр</span>
</button>
HTML;

$APPLICATION->AddViewContent("catalog_filter_button", $catalog_filter_button);
?>
<div class="three-columns">
    <? if ($arResult["VARIABLES"]["SECTION_CODE"] != WF_PACK_SECTION_CODE) { ?>
        <aside class="three-columns__sidebar">
            <!--Catalog filter begin-->
            <?
            //region Filter
            if ($isFilter): ?>
                <div class="catalog-filter" id="catalog-filter">
                    <p class="catalog-filter__title"><?= GetMessage("CT_FILTER_TITLE") ?></p>
                    <?
                    $APPLICATION->IncludeComponent("bitrix:catalog.smart.filter", "bootstrap_v4", array(
                        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                        "SECTION_ID" => $arCurSection['ID'],
                        "FILTER_NAME" => $arParams["FILTER_NAME"],
                        "PRICE_CODE" => $arParams["~PRICE_CODE"],
                        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                        "CACHE_TIME" => $arParams["CACHE_TIME"],
                        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                        "SAVE_IN_SESSION" => "N",
                        "FILTER_VIEW_MODE" => $arParams["FILTER_VIEW_MODE"],
                        "XML_EXPORT" => "N",
                        "SECTION_TITLE" => "NAME",
                        "SECTION_DESCRIPTION" => "DESCRIPTION",
                        'HIDE_NOT_AVAILABLE' => ($_GET['available'] == 'y') ? "Y" : "N",//$arParams['HIDE_NOT_AVAILABLE'],
                        "TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
                        'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                        'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                        "SEF_MODE" => $arParams["SEF_MODE"],
                        "SEF_RULE" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["smart_filter"],
                        "SMART_FILTER_PATH" => $arResult["VARIABLES"]["SMART_FILTER_PATH"],
                        "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
                        "INSTANT_RELOAD" => $arParams["INSTANT_RELOAD"],
                        "PREFILTER_NAME" => 'arrFilter',
                    ),
                        $component,
                        ['HIDE_ICONS' => 'Y']
                    );
                    ?>
                </div>
            <? endif
            //endregion
            ?>
            <!--Catalog filter end-->
            <?
            //region Sidebar
            if ($isSidebar): ?>
                <div class="d-none d-sm-block">
                    <? $APPLICATION->IncludeComponent("bitrix:main.include", "", array(
                        "AREA_FILE_SHOW" => "file",
                        "PATH" => $arParams["SIDEBAR_PATH"],
                        "AREA_FILE_RECURSIVE" => "N",
                        "EDIT_MODE" => "html",
                    ),
                        false,
                        array('HIDE_ICONS' => 'Y')
                    );
                    ?>
                </div>
            <? endif
            //endregion
            ?>
        </aside>
    <? } ?>
    <section class="three-columns__body">
        <div class="container-fluid breadcrumbs-wrapper">
            <?
            $APPLICATION->IncludeComponent("bitrix:breadcrumb", "catalog",
                ["START_FROM" => "0", "PATH" => "", "SITE_ID" => "s1"],
                false, ["HIDE_ICONS" => "Y"]
            );
            ?>
        </div>
        <div class="container-fluid">
            <? $APPLICATION->IncludeComponent(
                "bitrix:news.list",
                "actions_slider_catalog",
                [
                    "IBLOCK_TYPE" => "news",
                    "IBLOCK_ID" => WF_ACTIONS_IBLOCK_ID,
                    "NEWS_COUNT" => "20",
                    "SORT_BY1" => "ACTIVE_FROM",
                    "SORT_ORDER1" => "DESC",
                    "SORT_BY2" => "SORT",
                    "SORT_ORDER2" => "ASC",
                    "FILTER_NAME" => "",
                    "FIELD_CODE" => ["DETAIL_PICTURE"],
                    "PROPERTY_CODE" => ["CATALOG_BANNER"],
                    "CHECK_DATES" => "Y",
                    "AJAX_MODE" => "N",
                    "AJAX_OPTION_JUMP" => "N",
                    "AJAX_OPTION_STYLE" => "N",
                    "AJAX_OPTION_HISTORY" => "N",
                    "AJAX_OPTION_ADDITIONAL" => "",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "3600000",
                    "CACHE_FILTER" => "N",
                    "CACHE_GROUPS" => "Y",
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
                    "DISPLAY_TOP_PAGER" => "N",
                    "DISPLAY_BOTTOM_PAGER" => "N",
                    "PAGER_SHOW_ALWAYS" => "N",
                    "PAGER_DESC_NUMBERING" => "N",
                    "PAGER_SHOW_ALL" => "N",
                    "PAGER_BASE_LINK_ENABLE" => "N",
                    "SET_STATUS_404" => "N",
                    "SHOW_404" => "N",
                    "MESSAGE_404" => ""
                ],
                false, ['HIDE_ICONS' => 'Y']
            ); ?>
            <h1 class="pagetitle"><? $APPLICATION->ShowTitle(false) ?></h1>
            <?
            /*
                        //region Description
                        if (($arParams['HIDE_SECTION_DESCRIPTION'] !== 'Y') && !empty($arCurSection['DESCRIPTION'])) {
                            ?>
                            <div class="row mb-4">
                                <div class="col catalog-section-description">
                                    <p><?= $arCurSection['DESCRIPTION'] ?></p>
                                </div>
                            </div>
                            <?
                        }
                        //endregion
            */
            ?>
            <div class="catalog">
                <?

                // region TAGS
                if ($arResult["VARIABLES"]["SECTION_CODE"] != WF_PACK_SECTION_CODE) {
                    if ($_GET['dsc']) {
                        $arrFilter[] = ["!PROPERTY_XXX_1C_PROD_DISCOUNT_PRC" => false];
                    }
                    if ($_GET['pack']) {
                        $arrFilter[] = ["PROPERTY_IS_PACK_FREE_VALUE" => 'Да'];
                    }
                    if ($_GET['dlvr']) {
                        $arrFilter[] = ["LOGIC" => "OR",
                            "!PROPERTY_IS_DELIVERY_FREE" => false,
                            "!PROPERTY_IS_DELIVERY_FREE_MOS" => false];
                    }
                    if ($_GET['gft']) {
                        $arrFilter[] = ["LOGIC" => "OR",
                            "PROPERTY_PEN_IN_CASE" => 'Да',
                            "PROPERTY_MOET_IN_CASE" => 'Да'
                        ];
                    }
                    $f_params["ALL"] = [
                        'dsc' => ["NAME" => "dsc", "TITLE" => "Со скидкой"],
                        'dlvr' => ["NAME" => "dlvr", "TITLE" => "С бесплатной доставкой"],
                        'pack' => ["NAME" => "pack", "TITLE" => "С бесплатной упаковкой"],
                        'gft' => ["NAME" => "gft", "TITLE" => "С подарком"],
                    ];


                    $res = CIBlockSection::GetList([], ['IBLOCK_ID' => $arParams["IBLOCK_ID"], 'CODE' => $arResult["VARIABLES"]["SECTION_CODE"]]);
                    $section = $res->Fetch();
                    $chain = CIBlockSection::GetNavChain($arParams["IBLOCK_ID"], $section["ID"], array());
                    $arSectionPath = [];
                    while ($arSection = $chain->GetNext()) {
                        $arSectionPath[] = $arSection["CODE"];
                    }
                    array_shift($arSectionPath); //remove catalog-root

                    $base_section_code = "";
                    if (in_array($arResult["VARIABLES"]["SECTION_CODE"], $arSectionPath)) {
                        $base_section_code = $arSectionPath[0];
                    }

                    if ($base_section_code == "shkatulki-dlya-chasov-s-avtopodzavodom") {
                        $f_params[$base_section_code] = [
                            'dop' => ["NAME" => "dop", "TITLE" => "С отсеком для хранения", "PROP" => "DOP_OTSEK"],
                            "lcd" => ["NAME" => "lcd", "TITLE" => "С дисплеем", "PROP" => "LCD"],
                            "lock" => ["NAME" => "lock", "TITLE" => "С замочком", "PROP" => "ZAMOCHEK"],
                            "led" => ["NAME" => "led", "TITLE" => "С подсветкой", "PROP" => "PODSVETKA"],
                            "bat" => ["NAME" => "bat", "TITLE" => "С батарейками", "PROP" => "BATTERY"],
                        ];
                    }
                    if ($base_section_code == "shkatulki_dlya_chasov") {
                        $f_params[$base_section_code] = [
                            "glsc" => ["NAME" => "glsc", "TITLE" => "Со стеклянной крышкой", "PROP" => "GLASS_COVER"],
                            "lock" => ["NAME" => "lock", "TITLE" => "С замочком", "PROP" => "ZAMOCHEK"],
                            "hhld" => ["NAME" => "hhld", "TITLE" => "С увеличенной подушкой", "PROP" => "XXX_HIGH_HOLDER"],
                            // "trvl" => ["NAME" => "trvl", "TITLE" => "Для путешествий", "PROP" => "XXX_MOBILE_HUMIDOR"],
                        ];
                    }
                    if ($base_section_code == "shkatulki_dlya_ukrasheniy") {
                        $f_params[$base_section_code] = [
                            "lock" => ["NAME" => "lock", "TITLE" => "С замочком", "PROP" => "ZAMOCHEK"],
                            // "mirr" => ["NAME" => "mirr", "TITLE" => "С зеркальцем", "PROP" => "BOX_WITH_MIRROR"],
                        ];
                        // if ($arResult["VARIABLES"]["SECTION_CODE"] != "shkatulki_dlya_ukrasheniy_derevyannye")
                        //$f_params[$base_section_code][] = ["NAME" => "trvl", "TITLE" => "Для путешествий", "PROP" => "XXX_MOBILE_HUMIDOR"];
                        //$f_params[$base_section_code][] = ["NAME" => "glsc", "TITLE" => "Со стеклянной крышкой", "PROP" => "GLASS_COVER"];
                    }
                    if ($base_section_code == "khyumidory") {
                        $f_params[$base_section_code] = [
                            "hmdf" => ["NAME" => "hmdf", "TITLE" => "Увлажнитель", "PROP" => "XXX_CIGAR_HUMIDIFIER"],
                            "hdrm" => ["NAME" => "hdrm", "TITLE" => "Гидрометр", "PROP" => "XXX_CIGAR_HYGROMETER"],
                            "lock" => ["NAME" => "lock", "TITLE" => "С замочком", "PROP" => "ZAMOCHEK"],
                            "trvl" => ["NAME" => "trvl", "TITLE" => "Для путешествий", "PROP" => "XXX_MOBILE_HUMIDOR"],
                            "glsc" => ["NAME" => "glsc", "TITLE" => "Со стеклянной крышкой", "PROP" => "GLASS_COVER"],
                        ];
                    }

                    $f_paramsSectFilter = [];
                    foreach ($f_params[$base_section_code] as $tagParam) {
                        if ($_GET[$tagParam["NAME"]]) {
                            $arrFilter[] = ["!PROPERTY_" . $tagParam["PROP"] => false];
                        }
                        $f_paramsSectFilter[$tagParam["NAME"]] = ['PROP' => ["!PROPERTY_" . $tagParam["PROP"] => false]];
                    }


                    //тут делаем запросы, проверяем, что есть товары с данными свойствами, если товаров нет, то убираем фильтр с гет-параметром
                    $f_paramsAllFilter = [];
                    $f_paramsAllFilter = [
                        'dsc' => ["PROP" => ["!PROPERTY_XXX_1C_PROD_DISCOUNT_PRC" => false]],
                        'dlvr' => ["PROP" => ["PROPERTY_IS_PACK_FREE_VALUE" => 'Да']],
                        'pack' => ["PROP" => ["LOGIC" => "OR", "!PROPERTY_IS_DELIVERY_FREE" => false, "!PROPERTY_IS_DELIVERY_FREE_MOS" => false]],
                        'gft' => ["PROP" => ["LOGIC" => "OR", "PROPERTY_PEN_IN_CASE" => 'Да', "PROPERTY_MOET_IN_CASE" => 'Да']],
                    ];
                    $tagsParamsCheckFilter = array_merge($f_paramsAllFilter, $f_paramsSectFilter);
                    foreach ($tagsParamsCheckFilter as $key => $tagParam) {
                        $checkExistProductFilter = [];
                        $checkExistProductFilter = ["IBLOCK_ID" => $arParams["IBLOCK_ID"], "!PROPERTY_XXX_SHOW_ON_TIMECUBE_RU" => false,
                            'SECTION_ID' => $arCurSection['ID'], 'INCLUDE_SUBSECTIONS' => 'Y', 'ACTIVE' => 'Y', $tagParam["PROP"]];
                        $checkExistProductFilter = array_merge($checkExistProductFilter, $newFilter);
                        $rsElements = CIBlockElement::Getlist([], $checkExistProductFilter, false, ['nTopCount' => 1], ['ID'])->fetch();
                        if (empty($rsElements)) {
                            if ($f_params["ALL"][$key]) {
                                unset($f_params["ALL"][$key]);
                            }
                            if ($f_params[$base_section_code][$key]) {
                                unset($f_params[$base_section_code][$key]);
                            }
                        }
                    }
                    $tagsParams = array_merge($f_params["ALL"], $f_params[$base_section_code]);


                    $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
                    $sort = $request->get('sort');
                    $inDir = $request->getRequestedPageDirectory();
                    $pos = stripos($inDir, 'filter');
                    $showSeoSection = true; // флаг отображение Сео текста из раздела
                    if ($pos !== false) { // находимся на странице фильтра
                        $showSeoSection = false;
                    }

                    Bitrix\Main\Loader::includeModule('zverushki.seofilter');
                    $setFilter = [
                        'IBLOCK_ID' => $arParams["IBLOCK_ID"],
                        'SECTION_ID' => $section["ID"],
                        'ACTIVE' => 'Y',
                        'SVIEW' => 'Y',
                        '!TAG_SECTION_NAME' => false
                    ];
                    $dbList = \Zverushki\Seofilter\Internals\SettingsTable::GetList([
                        'order' => ['ID' => 'ASC'],
                        'filter' => $setFilter,
                        'select' => ['*'],
                        'limit' => 30
                    ]);
                    $arrSeoTag = [];
                    $requestUri = $requestUri = \Zverushki\Seofilter\configuration::get('requestUri');
                    while ($arResult2 = $dbList->fetch()) {
                        $arrSeoTag["TITLE"] = $arResult2["TAG_SECTION_NAME"];
                        $arrSeoTag["LINK"] = $arResult2["URL_CPU"];
                        $arrSeoTag["CHECKED"] = 'N';
                        if ($requestUri == $arResult2["URL_CPU"]) {
                            $showSeoSection = false;
                            $arrSeoTag["CHECKED"] = 'Y';
                            $idSeoText = $arResult2["ID"];
                        }
                        $arrSeoTags[] = $arrSeoTag;
                    }

                    if ($arrSeoTags) {
                        if (empty($tagsParams)) $tagsParams = [];
                        $tagsParams = array_merge($tagsParams, $arrSeoTags);
                    }
                    ?>
                    <!--tags-wrapper begin-->
                    <? if ($tagsParams): ?>
                        <div class="catalog-top">
                            <form id="tags-form" class="catalog-top__row tags-wrapper">
                                <ul class="tags-list">
                                    <?
                                    foreach ($tagsParams as $tagParam) { ?>
                                        <li class="tag-item"><label class="inline-checkbox">
                                                <?
                                                if (isset($_GET[$tagParam["NAME"]]) || $tagParam['CHECKED'] == 'Y')
                                                    $checked = 'checked';
                                                else
                                                    $checked = '';
                                                ?>
                                                <? if ($tagParam['LINK']): ?>
                                                    <a href="<? echo $tagParam['LINK'] ?>"
                                                       title="<? echo $tagParam['TITLE'] ?>" class="<?= $checked ?>">
                                                        <span class="inline-checkbox__content"><?= $tagParam['TITLE'] ?></span>
                                                    </a>
                                                <? else: ?>
                                                    <input data-parent="tags-form" type="checkbox"
                                                           name="<?= $tagParam["NAME"] ?>" <?= $checked ?>/>
                                                    <span class="inline-checkbox__content"><?= $tagParam["TITLE"] ?></span>
                                                <? endif; ?>
                                            </label>
                                        </li>
                                        <?
                                    } ?>
                                </ul>
                            </form>
                        </div>
                    <? endif ?>
                    <!--tags-wrapper end-->
                <? }
                // endregion TAGS

                $watches_num_prop = 'property_HRAN_NUM2';
                if ($base_section_code == 'shkatulki-dlya-chasov-s-avtopodzavodom') $watches_num_prop = 'property_PODZAVOD_NUM2';
                if ($base_section_code == 'shkatulki_dlya_ruchek') $watches_num_prop = 'property_XXX_PENS_QUANTITY';
                //if ($base_section_code == 'shkatulki_dlya_chasov') $watches_num_prop = 'PROPERTY_HRAN_NUM2';

                switch ($sort) {
                    case 'price':
                        $arParams["ELEMENT_SORT_FIELD"] = 'CATALOG_AVAILABLE';
                        $arParams["ELEMENT_SORT_ORDER"] = 'desc,nulls';
                        $arParams["ELEMENT_SORT_FIELD2"] = 'property_SORTPRICE';
                        $arParams["ELEMENT_SORT_ORDER2"] = 'asc';
                        break;
                    case 'price_desc':
                        $arParams["ELEMENT_SORT_FIELD"] = 'CATALOG_AVAILABLE';
                        $arParams["ELEMENT_SORT_ORDER"] = 'desc,nulls';
                        $arParams["ELEMENT_SORT_FIELD2"] = 'catalog_PRICE_2';
                        $arParams["ELEMENT_SORT_ORDER2"] = 'desc';
                        break;
                    case 'watches_asc':
                        $arParams["ELEMENT_SORT_FIELD"] = $watches_num_prop;
                        $arParams["ELEMENT_SORT_ORDER"] = 'asc,nulls';
                        $arParams["ELEMENT_SORT_FIELD2"] = 'catalog_PRICE_2';
                        $arParams["ELEMENT_SORT_ORDER2"] = 'asc';
                        break;
                    case 'watches_desc':
                        $arParams["ELEMENT_SORT_FIELD"] = $watches_num_prop;
                        $arParams["ELEMENT_SORT_ORDER"] = 'desc,nulls';
                        $arParams["ELEMENT_SORT_FIELD2"] = 'catalog_PRICE_2';
                        $arParams["ELEMENT_SORT_ORDER2"] = 'asc';
                        break;
                    case 'popular':
                        $arParams["ELEMENT_SORT_FIELD"] = 'CATALOG_AVAILABLE';
                        $arParams["ELEMENT_SORT_ORDER"] = 'desc,nulls';
                        $arParams["ELEMENT_SORT_FIELD2"] = 'SORT';
                        $arParams["ELEMENT_SORT_ORDER2"] = 'asc';
                        break;
                    default:
                        $arParams["ELEMENT_SORT_FIELD"] = 'CATALOG_AVAILABLE';
                        $arParams["ELEMENT_SORT_ORDER"] = 'desc,nulls';
                        $arParams["ELEMENT_SORT_FIELD2"] = 'catalog_PRICE_2';
                        $arParams["ELEMENT_SORT_ORDER2"] = 'asc';
                        break;
                }

                $showSortPanel = $arResult["VARIABLES"]["SECTION_CODE"] != WF_PACK_SECTION_CODE ? 'Y' : 'N';
                $showTopPager = $arResult["VARIABLES"]["SECTION_CODE"] != WF_PACK_SECTION_CODE ? $arParams["DISPLAY_TOP_PAGER"] : 'N';
                // region catalog.section

                $itemType = 'card';
                if ($_GET['view'] == 'list') $itemType = 'row';

                $intSectionID = $APPLICATION->IncludeComponent(
                    "bitrix:catalog.section",
                    "bootstrap_v4", $p = [
                    'ITEM_TYPE' => $itemType,
                    'SHOW_SORT_PANEL' => $showSortPanel,
                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                    "ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
                    "ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
                    "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
                    "ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
                    "PROPERTY_CODE" => (isset($arParams["LIST_PROPERTY_CODE"]) ? $arParams["LIST_PROPERTY_CODE"] : []),
                    "PROPERTY_CODE_MOBILE" => $arParams["LIST_PROPERTY_CODE_MOBILE"],
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
                    "MESSAGE_404" => $arParams["~MESSAGE_404"],
                    "SET_STATUS_404" => $arParams["SET_STATUS_404"],
                    "SHOW_404" => $arParams["SHOW_404"],
                    "FILE_404" => $arParams["FILE_404"],
                    "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
                    "PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
                    "LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
                    "PRICE_CODE" => $arParams["~PRICE_CODE"],
                    "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
                    "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

                    "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                    "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
                    "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
                    "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
                    "PRODUCT_PROPERTIES" => (isset($arParams["PRODUCT_PROPERTIES"]) ? $arParams["PRODUCT_PROPERTIES"] : []),

                    "DISPLAY_TOP_PAGER" => $showTopPager,
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
                    "LAZY_LOAD" => $arParams["LAZY_LOAD"],
                    "MESS_BTN_LAZY_LOAD" => $arParams["~MESS_BTN_LAZY_LOAD"],
                    "LOAD_ON_SCROLL" => $arParams["LOAD_ON_SCROLL"],

                    "OFFERS_CART_PROPERTIES" => (isset($arParams["OFFERS_CART_PROPERTIES"]) ? $arParams["OFFERS_CART_PROPERTIES"] : []),
                    "OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
                    "OFFERS_PROPERTY_CODE" => (isset($arParams["LIST_OFFERS_PROPERTY_CODE"]) ? $arParams["LIST_OFFERS_PROPERTY_CODE"] : []),
                    "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
                    "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
                    "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
                    "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
                    "OFFERS_LIMIT" => (isset($arParams["LIST_OFFERS_LIMIT"]) ? $arParams["LIST_OFFERS_LIMIT"] : 0),

                    "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
                    "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
                    "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
                    "DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
                    "USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
                    'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                    'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                    'HIDE_NOT_AVAILABLE' => ($_GET['available'] == 'y') ? "Y" : $arParams["HIDE_NOT_AVAILABLE"],
                    'HIDE_NOT_AVAILABLE_OFFERS' => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],

                    'LABEL_PROP' => $arParams['LABEL_PROP'],
                    'LABEL_PROP_MOBILE' => $arParams['LABEL_PROP_MOBILE'],
                    'LABEL_PROP_POSITION' => $arParams['LABEL_PROP_POSITION'],
                    'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
                    'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],
                    'PRODUCT_BLOCKS_ORDER' => $arParams['LIST_PRODUCT_BLOCKS_ORDER'],
                    'PRODUCT_ROW_VARIANTS' => $arParams['LIST_PRODUCT_ROW_VARIANTS'],
                    'ENLARGE_PRODUCT' => $arParams['LIST_ENLARGE_PRODUCT'],
                    'ENLARGE_PROP' => isset($arParams['LIST_ENLARGE_PROP']) ? $arParams['LIST_ENLARGE_PROP'] : '',
                    'SHOW_SLIDER' => $arParams['LIST_SHOW_SLIDER'],
                    'SLIDER_INTERVAL' => isset($arParams['LIST_SLIDER_INTERVAL']) ? $arParams['LIST_SLIDER_INTERVAL'] : '',
                    'SLIDER_PROGRESS' => isset($arParams['LIST_SLIDER_PROGRESS']) ? $arParams['LIST_SLIDER_PROGRESS'] : '',

                    'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
                    'OFFER_TREE_PROPS' => (isset($arParams['OFFER_TREE_PROPS']) ? $arParams['OFFER_TREE_PROPS'] : []),
                    'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
                    'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
                    'DISCOUNT_PERCENT_POSITION' => $arParams['DISCOUNT_PERCENT_POSITION'],
                    'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
                    'SHOW_MAX_QUANTITY' => 'N',
                    'MESS_SHOW_MAX_QUANTITY' => (isset($arParams['~MESS_SHOW_MAX_QUANTITY']) ? $arParams['~MESS_SHOW_MAX_QUANTITY'] : ''),
                    'RELATIVE_QUANTITY_FACTOR' => (isset($arParams['RELATIVE_QUANTITY_FACTOR']) ? $arParams['RELATIVE_QUANTITY_FACTOR'] : ''),
                    'MESS_RELATIVE_QUANTITY_MANY' => (isset($arParams['~MESS_RELATIVE_QUANTITY_MANY']) ? $arParams['~MESS_RELATIVE_QUANTITY_MANY'] : ''),
                    'MESS_RELATIVE_QUANTITY_FEW' => (isset($arParams['~MESS_RELATIVE_QUANTITY_FEW']) ? $arParams['~MESS_RELATIVE_QUANTITY_FEW'] : ''),
                    'MESS_BTN_BUY' => (isset($arParams['~MESS_BTN_BUY']) ? $arParams['~MESS_BTN_BUY'] : ''),
                    'MESS_BTN_ADD_TO_BASKET' => (isset($arParams['~MESS_BTN_ADD_TO_BASKET']) ? $arParams['~MESS_BTN_ADD_TO_BASKET'] : ''),
                    'MESS_BTN_SUBSCRIBE' => (isset($arParams['~MESS_BTN_SUBSCRIBE']) ? $arParams['~MESS_BTN_SUBSCRIBE'] : ''),
                    'MESS_BTN_DETAIL' => (isset($arParams['~MESS_BTN_DETAIL']) ? $arParams['~MESS_BTN_DETAIL'] : ''),
                    'MESS_NOT_AVAILABLE' => (isset($arParams['~MESS_NOT_AVAILABLE']) ? $arParams['~MESS_NOT_AVAILABLE'] : ''),
                    'MESS_BTN_COMPARE' => (isset($arParams['~MESS_BTN_COMPARE']) ? $arParams['~MESS_BTN_COMPARE'] : ''),

                    'USE_ENHANCED_ECOMMERCE' => (isset($arParams['USE_ENHANCED_ECOMMERCE']) ? $arParams['USE_ENHANCED_ECOMMERCE'] : ''),
                    'DATA_LAYER_NAME' => (isset($arParams['DATA_LAYER_NAME']) ? $arParams['DATA_LAYER_NAME'] : ''),
                    'BRAND_PROPERTY' => (isset($arParams['BRAND_PROPERTY']) ? $arParams['BRAND_PROPERTY'] : ''),

                    'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
                    "ADD_SECTIONS_CHAIN" => "N",
                    'ADD_TO_BASKET_ACTION' => $basketAction,
                    'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
                    'COMPARE_PATH' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['compare'],
                    'COMPARE_NAME' => $arParams['COMPARE_NAME'],
                    'USE_COMPARE_LIST' => 'Y',
                    'BACKGROUND_IMAGE' => (isset($arParams['SECTION_BACKGROUND_IMAGE']) ? $arParams['SECTION_BACKGROUND_IMAGE'] : ''),
                    'COMPATIBLE_MODE' => (isset($arParams['COMPATIBLE_MODE']) ? $arParams['COMPATIBLE_MODE'] : ''),
                    'DISABLE_INIT_JS_IN_COMPONENT' => (isset($arParams['DISABLE_INIT_JS_IN_COMPONENT']) ? $arParams['DISABLE_INIT_JS_IN_COMPONENT'] : '')
                ],
                    $component, ['HIDE_ICONS' => 'Y']
                );

                // endregion catalog.section

                $GLOBALS['CATALOG_CURRENT_SECTION_ID'] = $intSectionID;

                if ($idSeoText) {
                    $dbSeoText = \Zverushki\Seofilter\Internals\SeotagTable::GetList([
                        'order' => ['ID' => 'ASC'],
                        'filter' => ['SETTING_ID' => $idSeoText, '!SEO_DESCRIPTION' => false],
                        'select' => ['ID', 'SEO_DESCRIPTION'],
                        'limit' => 1
                    ])->fetch();
                    if ($dbSeoText['SEO_DESCRIPTION']) {
                        echo $dbSeoText['SEO_DESCRIPTION'];
                    }
                }
                if ($showSeoSection) {
                    // https://webfly24.ru/bitrix/admin/ticket_edit.php?ID=4413&lang=ru
                    $page = 1;
                    if (isset($_GET["PAGEN_3"])) $page = intval($_GET["PAGEN_3"]);
                    if (isset($_GET["PAGEN_2"])) $page = intval($_GET["PAGEN_2"]);
                    if (isset($_GET["PAGEN_1"])) $page = intval($_GET["PAGEN_1"]);
                    if (!empty($arCurSection['DESCRIPTION']) && $page < 2) {

                        $seoDescription = $arCurSection['DESCRIPTION'];
                        $pMin = $arCurSection['PRICE_MIN'] ? intval($arCurSection['PRICE_MIN']) : '';
                        $pMax = $arCurSection['PRICE_MAX'] ? intval($arCurSection['PRICE_MAX']) : '';
                        $countEl = $arCurSection['COUNT'] ? intval($arCurSection['COUNT']) : '';
                        $resDesc = str_replace(['#PRICE_MIN#', '#PRICE_MAX#', '#COUNT#'], [$pMin, $pMax, $countEl], $seoDescription);
                        $descHtml = htmlspecialcharsEx($resDesc);
                        $text = [];
                        if (preg_match_all('|&lt;.+&gt;(.*)&lt;/.+&gt;|Uis', $descHtml, $result)) {
                            foreach ($result[0] as $span_text)
                                if (strpos($span_text, ' ') !== false) {
                                    $text[] = $span_text;
                                }
                        }
                        if (!empty($text)) {
                            $firstParagraph = stristr($descHtml, $text[2], true);
                            $twoParagraph = stristr($descHtml, $text[2]);
                            echo htmlspecialcharsBack($firstParagraph);

                            ?>
                            <div class="small-seo-text readmore-wrapper mt-4">
                                <div class="readmore" data-rows="0" id="105">
                                    <?= htmlspecialcharsBack($twoParagraph);
                                    ?>
                                </div>
                            </div>
                            <?
                        } else {
                            echo str_replace(['#PRICE_MIN#', '#PRICE_MAX#', '#COUNT#'], [$pMin, $pMax, $countEl], $seoDescription);
                        }
                    } else {
                        ?>
                        <div class="small-seo-text readmore-wrapper mt-4">
                            <div class="readmore" data-rows="4" id="105"><?
                                $seoDescription = $arCurSection['DESCRIPTION'];
                                $pMin = $arCurSection['PRICE_MIN'] ? intval($arCurSection['PRICE_MIN']) : '';
                                $pMax = $arCurSection['PRICE_MAX'] ? intval($arCurSection['PRICE_MAX']) : '';
                                $countEl = $arCurSection['COUNT'] ? intval($arCurSection['COUNT']) : '';
                                echo str_replace(['#PRICE_MIN#', '#PRICE_MAX#', '#COUNT#'], [$pMin, $pMax, $countEl], $seoDescription);

                                ?>
                            </div>
                        </div>
                    <? }
                }

                $sectionListParams = [
                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                    "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
                    "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                    "COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
                    "TOP_DEPTH" => $arParams["SECTION_TOP_DEPTH"],
                    "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
                    "VIEW_MODE" => $arParams["SECTIONS_VIEW_MODE"],
                    "SHOW_PARENT_NAME" => $arParams["SECTIONS_SHOW_PARENT_NAME"],
                    "HIDE_SECTION_NAME" => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"]) ? $arParams["SECTIONS_HIDE_SECTION_NAME"] : "N"),
                    "ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : '')
                ];
                if ($sectionListParams["COUNT_ELEMENTS"] === "Y") {
                    $sectionListParams["COUNT_ELEMENTS_FILTER"] = "CNT_ACTIVE";
                    if ($arParams["HIDE_NOT_AVAILABLE"] == "Y") {
                        $sectionListParams["COUNT_ELEMENTS_FILTER"] = "CNT_AVAILABLE";
                    }
                }
                $APPLICATION->IncludeComponent("bitrix:catalog.section.list", "catalog_bottom",
                    $sectionListParams,
                    $component, ["HIDE_ICONS" => "Y"]
                );
                unset($sectionListParams);

                $iprop = new \Bitrix\Iblock\InheritedProperty\SectionValues($arParams["IBLOCK_ID"], $arCurSection["ID"]);
                $SEOValues = $iprop->getValues();
                $sectionTitle = $arCurSection['UF_BROWSER_TITLE'] ? $arCurSection['UF_BROWSER_TITLE'] : $arCurSection['NAME'];

                $resImgOG = CIBlockSection::GetByID($arCurSection["ID"]);
                if ($ar_res = $resImgOG->GetNext())
                    $sectionImage = CFile::GetPath($ar_res["PICTURE"]);
                if (empty($sectionImage)) {
                    $resImgOG = CIBlockElement::GetList(['sort' => 'asc'], ['IBLOCK_ID' => $arParams["IBLOCK_ID"], 'SECTION_CODE' => $arResult["VARIABLES"]["SECTION_CODE"]], false, ["nTopCount" => 1], ["DETAIL_PICTURE"]);
                    while ($ar_res = $resImgOG->Fetch()) {
                        $sectionImage = CFile::GetPath($ar_res["DETAIL_PICTURE"]);
                    }
                }
                $url = "/" . $arResult["VARIABLES"]["SECTION_CODE"] . "/";
                if (Zverushki\Seofilter\Filter\Seo::getSeoTag("META_TITLE")) $sectionTitle = Zverushki\Seofilter\Filter\Seo::getSeoTag("META_TITLE");
                if (Zverushki\Seofilter\Filter\Seo::getSeoTag("META_DESCRIPTION")) $SEOValues['SECTION_META_DESCRIPTION'] = Zverushki\Seofilter\Filter\Seo::getSeoTag("META_DESCRIPTION");
                if (Zverushki\Seofilter\configuration::get('requestUri')) $url = \Zverushki\Seofilter\configuration::get('requestUri');
                ?>
                <!--                           Open Graph-->
                <div style="display:none;">
                    <meta property="og:title" content="<?= $sectionTitle ?>"/>
                    <meta property="og:description" content="<?= $SEOValues['SECTION_META_DESCRIPTION'] ?>"/>
                    <meta property="og:image" content="https://<?= SITE_SERVER_NAME . $sectionImage ?>"/>
                    <meta property="og:type" content="website"/>
                    <meta property="og:url"
                          content="https://<?= SITE_SERVER_NAME . $url ?>"/>
                    <meta property="og:locale" content="ru_RU"/>
                    <meta property="og:site_name" content="timecube.ru"/>
                </div>
                <!--                          end  Open Graph-->
                <div itemscope itemtype="http://schema.org/Product">
                    <meta itemprop="name" content="<?= $sectionTitle ?>"/>
                    <meta itemprop="description" content="<?= $SEOValues['SECTION_META_DESCRIPTION'] ?>"/>
                    <div itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer">
                        <meta itemprop="lowPrice" content="<?= $arCurSection['PRICE_MIN'] ?>"/>
                        <meta itemprop="highPrice" content="<?= $arCurSection['PRICE_MAX'] ?>"/>
                        <meta itemprop="offerCount" content="<?= $arCurSection['COUNT'] ?>"/>
                        <meta itemprop="priceCurrency" content="RUB"/>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?
    $showAside = $arResult["VARIABLES"]["SECTION_CODE"] == WF_PACK_SECTION_CODE;
    if ($showAside) { ?>
        <? $APPLICATION->IncludeComponent("bitrix:main.include", "",
            ["AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/inner_aside.php"], false, ['HIDE_ICONS' => 'Y']
        ); ?>
    <? } ?>
</div>