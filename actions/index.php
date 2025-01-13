<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Акции на сайте TimeCube");
$APPLICATION->SetPageProperty("description", "Акции в интернет-магазине аксессуаров для часов TimeCube.  Звоните: 8 800 775-25-76!");
$APPLICATION->SetTitle("Акции");
?><div class="three-columns" id="inner-page">
 <section class="three-columns__body">
	<div class="container-fluid breadcrumbs-wrapper">
		 <?$APPLICATION->IncludeComponent(
	"bitrix:breadcrumb",
	"catalog",
	Array(
		"PATH" => "",
		"SITE_ID" => "s1",
		"START_FROM" => "0"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?>
	</div>
	<div class="container-fluid">
		<div class="heading">
			<div class="heading__item">
				<h1 class="heading__title"><?=$APPLICATION->ShowTitle(true)?></h1>
			</div>
		</div>
	</div>
	<div class="container-fluid">
		 <?$APPLICATION->IncludeComponent(
	"bitrix:news",
	"actions",
	Array(
		"ADD_ELEMENT_CHAIN" => "Y",
		"ADD_SECTIONS_CHAIN" => "N",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "N",
		"BROWSER_TITLE" => "-",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"CHECK_DATES" => "Y",
		"DETAIL_ACTIVE_DATE_FORMAT" => "d.m.Y",
		"DETAIL_DISPLAY_BOTTOM_PAGER" => "N",
		"DETAIL_DISPLAY_TOP_PAGER" => "N",
		"DETAIL_FIELD_CODE" => array(0=>"PREVIEW_PICTURE",1=>"",),
		"DETAIL_PAGER_SHOW_ALL" => "N",
		"DETAIL_PAGER_TEMPLATE" => "",
		"DETAIL_PAGER_TITLE" => "",
		"DETAIL_PROPERTY_CODE" => array(0=>"",1=>"",),
		"DETAIL_SET_CANONICAL_URL" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"FILE_404" => "",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"IBLOCK_ID" => WF_ACTIONS_IBLOCK_ID,
		"IBLOCK_TYPE" => "news",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"LIST_ACTIVE_DATE_FORMAT" => "d.m.Y",
		"LIST_FIELD_CODE" => array(0=>"DETAIL_PICTURE",1=>"",),
		"LIST_PROPERTY_CODE" => array(0=>"",1=>"",),
		"META_DESCRIPTION" => "-",
		"META_KEYWORDS" => "-",
		"NEWS_COUNT" => "20",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "bootstrap_v4",
		"PAGER_TITLE" => "Акции",
		"PREVIEW_TRUNCATE_LEN" => "",
		"SEF_FOLDER" => "/actions/",
		"SEF_MODE" => "Y",
		"SEF_URL_TEMPLATES" => array("news"=>"","section"=>"","detail"=>"#ELEMENT_CODE#/",),
		"SET_LAST_MODIFIED" => "N",
		"SET_STATUS_404" => "Y",
		"SET_TITLE" => "Y",
		"SHOW_404" => "Y",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_BY2" => "SORT",
		"SORT_ORDER1" => "DESC",
		"SORT_ORDER2" => "ASC",
		"STRICT_SECTION_CHECK" => "N",
		"USE_CATEGORIES" => "N",
		"USE_FILTER" => "N",
		"USE_PERMISSIONS" => "N",
		"USE_RATING" => "N",
		"USE_RSS" => "N",
		"USE_SEARCH" => "N"
	)
);?>
	</div>
 </section>
	<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => SITE_DIR."include/inner_aside.php"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?>
</div><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php")?>