<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
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

$url = $APPLICATION->GetCurUri();//301 редирект с большого регистра на маленький
$urlBrand = explode('/', $url);
if (ctype_alpha($urlBrand[2])) {
    if (!ctype_lower($urlBrand[2])) {
        $urlBrandNew = strtolower($url);
        LocalRedirect($urlBrandNew, true, 301);
    }
} else {
    $pattern = '/^[a-z_]+$/u';
    if (!preg_match($pattern, $urlBrand[2])) {
        $urlBrandNew = strtolower($url);
        LocalRedirect($urlBrandNew, true, 301);
    }
}
?>
<div class="text-content">
    <?php
    $ElementID = $APPLICATION->IncludeComponent(
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
        $component, ['HIDE_ICONS' => 'Y']
    );
    ?>


    <?
    $el = CIBlockElement::GetByID($ElementID)->Fetch();
    if (isset($_GET["PAGEN_3"]))
        $page = intval($_GET["PAGEN_3"]);
    if (isset($_GET["PAGEN_2"]))
        $page = intval($_GET["PAGEN_2"]);
    if (isset($_GET["PAGEN_1"]))
        $page = intval($_GET["PAGEN_1"]);
    if (empty($page) || $page === 1) {
        ?>
        <p><strong>«<?= $el["NAME"] ?>»</strong> <?= $el["DETAIL_TEXT"] ?></p>
    <? } ?>
</div>
<? //Получаем элементы иб=10, фильтруем и группируем их по производителю
$req = CIBlockElement::GetList(
    array(),
    array("PROPERTY_MANUFACTUR" => $el['CODE'], "IBLOCK_ID" => 10, '=AVAILABLE' => 'Y', 'PROPERTY_XXX_DONT_SHOW_ON_SITE' => false, 'ACTIVE' => 'Y'),
    $arGroupBy = array("IBLOCK_SECTION_ID"),
    );

while ($ob = $req->GetNext()) {
    $res[] = $ob;
}
//Получаем все разделы с данным производителем
foreach ($res as $item) {
    $FilterSect[] = $item['IBLOCK_SECTION_ID'];
}
$reqSect = CIBlockSection::GetList(
    array('sort' => 'asc'),
    array('ID' => $FilterSect, 'IBLOCK_ID' => WF_CATALOG_IBLOCK_ID, '!ID' => 86, 'ACTIVE' => 'Y'),
    false,
    array('ID', 'NAME', 'SECTION_PAGE_URL')
);
while ($arSect = $reqSect->GetNext()) {
    $resSect[] = $arSect;
}

//Получаем СEO заголовки для блока категорий и формируем ссылку с фильтром
foreach ($resSect as $key => $section) {
    $ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues(10, $section['ID']);
    $IPROPERTY[] = $ipropValues->getValues();
    /* $IPROPERTY[$key]['SECTION_PAGE_URL'] = $section['SECTION_PAGE_URL'] . 'filter/manufactur-is-' . strtolower($el['CODE']) . '/apply/';*/
    $IPROPERTY[$key]['SECTION_PAGE_URL'] = $section['SECTION_PAGE_URL'];
}
?>
<div class="product-detail__datasection">
    <ul class="list-inline">
        <li><b>Категории:</b></li>
        <? foreach ($IPROPERTY as $section) { ?>
            <li>
                <a href="<?= $section['SECTION_PAGE_URL'] ?>"><?= $section["SECTION_PAGE_TITLE"] ?: $section["ELEMENT_PAGE_TITLE"] ?></a>
            </li>
        <? } ?>
    </ul>
</div>
<h3>Товары:</h3>
<?
if ($el) {
    global $arrFilter;
    global $sort_field;
    global $sort_order;
    global $pages;

    $_GET['field'] = $_GET['field'] ? $_GET['field'] : $_COOKIE['field'];
    if ($_GET['field'])
        setcookie('field', $_GET['field'], time() + 99999999, '/');
    $_GET['order'] = $_GET['order'] ? $_GET['order'] : $_COOKIE['order'];
    if ($_GET['order'])
        setcookie('order', $_GET['order'], time() + 99999999, '/');

    $sort_field = array('catalog_price_' => 'цене', 'property_order_count' => 'популярности', 'date_create' => 'новинки', 'property_podzavod_num' => 'количеству часов');
    $sort_order = array('asc' => 'По возрастанию', 'desc' => 'По убыванию');
    $f = str_replace('catalog_price_', '', $_GET['field']);
    $f = str_replace((int)$f, '', $_GET['field']); //Удаляем цифру в ID цены
    $field = $sort_field[$f] ? $_GET['field'] : $arParams["ELEMENT_SORT_FIELD"];
    $order = $sort_order[$_GET['order']] ? $_GET['order'] : $arParams["ELEMENT_SORT_ORDER"];


    $arrFilter['=AVAILABLE'] = 'Y';
    $arrFilter['PROPERTY_XXX_DONT_SHOW_ON_SITE'] = false;
    $arrFilter['!SECTION_CODE'] = [WF_CATALOG_ROOT, WF_PACK_SECTION_CODE];
    $arrFilter['PROPERTY_MANUFACTUR'] = $el['CODE'];

    $APPLICATION->IncludeComponent(
        "bitrix:catalog.section",
        'bootstrap_v4',
        array(
            'ELEMENT_SORT_FIELD' => $field,
            'ELEMENT_SORT_ORDER' => $order,
            'ADD_PROPERTIES_TO_BASKET' => 'Y',
            'ADD_SECTIONS_CHAIN' => 'N',
            'IBLOCK_TYPE' => WF_CATALOG_IBLOCK_TYPE,
            'IBLOCK_ID' => WF_CATALOG_IBLOCK_ID,
            'PROPERTY_CODE' =>
                array(
                    0 => 'CML2_ARTICLE',
                    1 => 'MOET_IN_CASE',
                    2 => 'IS_PACK_FREE',
                    3 => 'IS_DELIVERY_FREE_MOS',
                    4 => 'IS_DELIVERY_FREE',
                    5 => 'PEN_IN_CASE',
                    6 => 'DISCOUNT',
                    7 => '',
                ),
            'META_KEYWORDS' => '',
            'META_DESCRIPTION' => '',
            'BROWSER_TITLE' => '',
            'INCLUDE_SUBSECTIONS' => 'Y',
            'BASKET_URL' => '/personal/cart/',
            'ADD_TO_BASKET_ACTION' => 'ADD',
            'PRODUCT_DISPLAY_MODE' => 'Y',
            'ACTION_VARIABLE' => 'action',
            'PRODUCT_ID_VARIABLE' => 'id',
            'SECTION_ID_VARIABLE' => 'SECTION_ID',
            'FILTER_NAME' => 'arrFilter',
            'DISPLAY_PANEL' => '',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => '86400',
            'CACHE_FILTER' => 'Y',
            'CACHE_GROUPS' => 'Y',
            'SET_TITLE' => 'N',
            'SET_STATUS_404' => 'Y',
            'DISPLAY_COMPARE' => 'Y',
            'PAGE_ELEMENT_COUNT' => $arResult['NEWS_COUNT'],
            'LINE_ELEMENT_COUNT' => '1',
            'PRICE_CODE' =>
                [WF_CATALOG_PRICE_TYPE],
            'USE_PRICE_COUNT' => 'N',
            'SHOW_PRICE_COUNT' => '1',
            'PRICE_VAT_INCLUDE' => 'Y',
            'DISPLAY_TOP_PAGER' => 'Y',
            'DISPLAY_BOTTOM_PAGER' => 'Y',
            'PAGER_TITLE' => 'Страницы',
            'PAGER_SHOW_ALWAYS' => 'N',
            'PAGER_TEMPLATE' => 'modern',
            'PAGER_DESC_NUMBERING' => 'N',
            'PAGER_DESC_NUMBERING_CACHE_TIME' => '36000',
            'PAGER_SHOW_ALL' => 'N',
            'OFFER_TREE_PROPS' =>
                array(
                    0 => 'WATCHSTRAPCOLOR',
                    1 => 'WATCHSIDEWIDTH',
                    2 => 'CLASPCOLOR',
                ),
            'OFFERS_CART_PROPERTIES' =>
                array(
                    0 => 'WATCHSTRAPCOLOR',
                    1 => 'WATCHSIDEWIDTH',
                    2 => 'CLASPCOLOR',
                ),
            'OFFERS_FIELD_CODE' =>
                array(
                    0 => '',
                    1 => '',
                ),
            'OFFERS_PROPERTY_CODE' =>
                array(
                    0 => 'WATCHSTRAPCOLOR',
                    1 => 'WATCHSIDEWIDTH',
                    2 => 'CLASPCOLOR',
                ),
            'OFFERS_SORT_FIELD' => 'sort',
            'OFFERS_SORT_ORDER' => 'asc',
            //'OFFERS_LIMIT' => '0',
            'SECTION_ID' => NULL,
            'SECTION_CODE' => NULL,
            'USE_MAIN_ELEMENT_SECTION' => 'Y',
            'SECTION_URL' => '/#SECTION_CODE#/',
            'DETAIL_URL' => '/product/#ELEMENT_CODE#/',
            'COMPOSITE_FRAME_MODE' => 'N',
        ),
        false, ['HIDE_ICONS' => 'Y']
    );
}
?>
<style>
    .pages.show {
        display: none;
    }
</style>
