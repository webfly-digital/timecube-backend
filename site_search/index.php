<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Поиск");
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
						<h1 class="heading__title"><?=$APPLICATION->ShowTitle()?></h1>
					</div>
				</div>
			</div>
			<div class="container-fluid">
				<?$APPLICATION->IncludeComponent(
	"bitrix:search.page", 
	".default", 
	array(
		"RESTART" => "N",
		"CHECK_DATES" => "N",
		"USE_TITLE_RANK" => "N",
		"DEFAULT_SORT" => "rank",
		"arrFILTER" => array(
			0 => "iblock_news",
			1 => "iblock_services",
			2 => "iblock_1c_catalog",
		),
		"arrFILTER_main" => "",
		"arrFILTER_iblock_services" => array(
			0 => "all",
		),
		"arrFILTER_iblock_news" => array(
			0 => "all",
		),
		"arrFILTER_iblock_catalog" => array(
			0 => "all",
		),
		"SHOW_WHERE" => "N",
		"SHOW_WHEN" => "N",
		"PAGE_RESULT_COUNT" => "25",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_SHADOW" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Результаты поиска",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "arrows",
		"USE_SUGGEST" => "N",
		"SHOW_ITEM_TAGS" => "N",
		"SHOW_ITEM_DATE_CHANGE" => "N",
		"SHOW_ORDER_BY" => "N",
		"SHOW_TAGS_CLOUD" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"COMPONENT_TEMPLATE" => ".default",
		"NO_WORD_LOGIC" => "N",
		"FILTER_NAME" => "",
		"arrFILTER_iblock_1c_catalog" => array(
			0 => "all",
		),
		"USE_LANGUAGE_GUESS" => "Y",
		"SHOW_RATING" => "",
		"RATING_TYPE" => "",
		"PATH_TO_USER_PROFILE" => ""
	),
	false
);?>

			</div>
		</section>
        <?$APPLICATION->IncludeComponent("bitrix:main.include","",
            ["AREA_FILE_SHOW" => "file","PATH" => SITE_DIR."include/inner_aside.php"], false, ['HIDE_ICONS' => 'Y']
        );?>
	</div>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>