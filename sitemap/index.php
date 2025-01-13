<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Карта сайта");
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
                        <h1 class="heading__title"><?=$APPLICATION->ShowTitle(true)?></h1>
                    </div>
                </div>
            </div>
            <div class="container-fluid">

    <div class="pf-main clearfix">

        <?$APPLICATION->IncludeComponent(
            "bitrix:menu",
            "footer",
            array(
                "ROOT_MENU_TYPE" => "top",
                "MENU_CACHE_TYPE" => "A",
                "MENU_CACHE_TIME" => "3600",
                "MENU_CACHE_USE_GROUPS" => "N",
                "MENU_CACHE_GET_VARS" => array(
                ),
                "MAX_LEVEL" => "1",
                "CHILD_MENU_TYPE" => "",
                "USE_EXT" => "Y",
                "DELAY" => "N",
                "ALLOW_MULTI_SELECT" => "N"
            ),
            false
        );?>
        <br>
        <h3>Каталог товаров:</h3>
        <?$APPLICATION->IncludeComponent(
            "bitrix:catalog.section.list",
            "tree",
            array(
                "COMPONENT_TEMPLATE" => "tree",
                "IBLOCK_TYPE" => WF_CATALOG_IBLOCK_TYPE,
                "IBLOCK_ID" => WF_CATALOG_IBLOCK_ID,
                "SECTION_ID" => '',
                "SECTION_CODE" => WF_CATALOG_ROOT,
                "COUNT_ELEMENTS" => "N",
                "TOP_DEPTH" => "2",
                "SECTION_FIELDS" => array(
                    0 => "",
                    1 => "",
                ),
                "SECTION_USER_FIELDS" => array(
                    0 => "",
                    1 => "",
                ),
                "SECTION_URL" => "",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "36000000",
                "CACHE_GROUPS" => "Y",
                "ADD_SECTIONS_CHAIN" => "Y"
            ),
            false
        );
        /*?>
        <div class="catalog-section-list">
            <ul>
                <li><a href="/shkatulki_dlya_ochkov/futlyary/">Футляры для очков</a></li>
                <li><a href="/shkatulki_dlya_ochkov/futlyary/kozhanye/">Кожаные футляры для очков</a></li>
                <li><a href="/shkatulki_dlya_ochkov/futlyary/muzhskie/">Мужские футляры для очков</a></li>
                <li><a href="/braslety_dlya_chasov/8-mm/">Браслеты для часов 8 мм</a></li>
                <li><a href="/braslety_dlya_chasov/20-mm/">Браслеты для часов 20 мм</a></li>
                <li><a href="/braslety_dlya_chasov/22-mm/">Браслеты для часов 22 мм</a></li>
                <li><a href="/braslety_dlya_chasov/26-mm/">Браслеты для часов 26 мм</a></li>
                <li><a href="/braslety_dlya_chasov/zhenskie/">Браслеты для женских часов</a></li>
                <li><a href="/remeshki_dlya_chasov_1/kozhanye/">Кожаные ремешки для часов</a></li>
                <li><a href="/remeshki_dlya_chasov_1/zastezhka-babochka/">Ремешки для часов с застежкой бабочкой</a></li>
                <li><a href="/remeshki_dlya_chasov_1/18-mm/">Ремешки для часов 18 мм</a></li>
                <li><a href="/remeshki_dlya_chasov_1/20-mm/">Ремешки для часов 20 мм</a></li>
                <li><a href="/remeshki_dlya_chasov_1/22-mm/">Ремешки для часов 22 мм</a></li>
                <li><a href="/remeshki_dlya_chasov_1/24-mm/">Ремешки для часов 24 мм</a></li>
            </ul>
        </div>
        */?>
    <br>
    <h3>Производители:</h3>
        <?
        $APPLICATION->IncludeComponent(
	"bitrix:news.list", 
	"sitemap",
	array(
		"IBLOCK_TYPE" => "news",
		"IBLOCK_ID" => WF_MANUFACTURERS_IBLOCK_ID,
		"NEWS_COUNT" => "50",
		"SORT_BY1" => "NAME",
		"SORT_ORDER1" => "ASC",
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
		"DISPLAY_DATE" => "N",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "N",
		"DISPLAY_PREVIEW_TEXT" => "N",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"PAGER_TEMPLATE" => ".default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
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
);
        ?>
    </div>

    </div>
    </section>
    </div>
<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>