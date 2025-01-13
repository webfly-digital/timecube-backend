<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Читать блог на сайте Timecube. Статьи о часах, шкатулок с автоподзаводом, интересные факты и другая информация в нашем интернет-магазине");
$APPLICATION->SetTitle("Блог компании Timecube");
$APPLICATION->SetPageProperty("title", "Блог и полезная информация компании Timecube");

$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/components/bitrix/news/faq_new0721/style/style.css', true);
?>


    <div class="container-fluid breadcrumbs-wrapper">
        <?
        $APPLICATION->IncludeComponent("bitrix:breadcrumb", "catalog",
            ["START_FROM" => "0", "PATH" => "", "SITE_ID" => "s1"],
            false, ["HIDE_ICONS" => "N"]
        );
        ?>
    </div>


    <div class="container-fluid">
        <div class="heading">
            <div class="heading__item">
                <h1 class="heading__title"><? $APPLICATION->ShowTitle(false) ?></h1>
            </div>
        </div>
    </div>
    <div class="container-fluid">
<?
    $APPLICATION->IncludeComponent(
	"bitrix:news", 
	"faq_new0721", 
	array(
		"COMPONENT_TEMPLATE" => "faq_new0721",
		"IBLOCK_TYPE" => "news",
		"IBLOCK_ID" => "40",
		"NEWS_COUNT" => "40",
		"USE_SEARCH" => "N",
		"USE_RSS" => "N",
		"USE_RATING" => "N",
		"USE_CATEGORIES" => "N",
		"USE_FILTER" => "N",
		"SORT_BY1" => "ID",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "ACTIVE_FROM",
		"SORT_ORDER2" => "ASC",
		"CHECK_DATES" => "Y",
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/blog/",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"SET_LAST_MODIFIED" => "N",
		"SET_TITLE" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "Y",
		"ADD_ELEMENT_CHAIN" => "Y",
		"USE_PERMISSIONS" => "N",
		"STRICT_SECTION_CHECK" => "Y",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"USE_SHARE" => "N",
		"PREVIEW_TRUNCATE_LEN" => "",
		"LIST_ACTIVE_DATE_FORMAT" => "d.m.Y",
		"LIST_FIELD_CODE" => array(
			0 => "DATE_ACTIVE_FROM",
			1 => "ACTIVE_FROM",
			2 => "DATE_ACTIVE_TO",
			3 => "ACTIVE_TO",
			4 => "DATE_CREATE",
			5 => "",
		),
		"LIST_PROPERTY_CODE" => array(
			0 => "POPULAR_PRODUCT",
			1 => "LINK",
			2 => "",
		),
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"DISPLAY_NAME" => "Y",
		"META_KEYWORDS" => "-",
		"META_DESCRIPTION" => "-",
		"BROWSER_TITLE" => "-",
		"DETAIL_SET_CANONICAL_URL" => "N",
		"DETAIL_ACTIVE_DATE_FORMAT" => "d.m.Y",
		"DETAIL_FIELD_CODE" => array(
			0 => "PREVIEW_TEXT",
			1 => "PREVIEW_PICTURE",
			2 => "DETAIL_TEXT",
			3 => "DETAIL_PICTURE",
			4 => "DATE_ACTIVE_FROM",
			5 => "ACTIVE_FROM",
			6 => "DATE_ACTIVE_TO",
			7 => "ACTIVE_TO",
			8 => "DATE_CREATE",
			9 => "",
		),
		"DETAIL_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"DETAIL_DISPLAY_TOP_PAGER" => "N",
		"DETAIL_DISPLAY_BOTTOM_PAGER" => "N",
		"DETAIL_PAGER_TITLE" => "Страница",
		"DETAIL_PAGER_TEMPLATE" => "",
		"DETAIL_PAGER_SHOW_ALL" => "N",
		"PAGER_TEMPLATE" => "bootstrap_v4",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Статьи",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"SET_STATUS_404" => "Y",
		"SHOW_404" => "N",
		"MESSAGE_404" => "",
		"SEF_URL_TEMPLATES" => array(
			"news" => "/blog/",
			"section" => "#SECTION_CODE#/",
			"detail" => "#SECTION_CODE#/#ELEMENT_CODE#/",
		)
	),
	false
); ?>
    </div>

    <? /*$APPLICATION->IncludeComponent("bitrix:main.include", "",
            ["AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/inner_aside.php"], false, ['HIDE_ICONS' => 'Y']
        );*/ ?>

    <? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>