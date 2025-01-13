<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Отзывы об интернет-магазине TimeCube");
$APPLICATION->SetPageProperty("description", "Отзывы покупателей об интернет-магазине аксессуаров для часов");
$APPLICATION->SetTitle("Отзывы");

$arr = [];
$getParamCompany = ['PAGEN_1', 'STAR',  'SORT_1'];
$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$list = $request->getQueryList();
if ($list) $arr = array_flip($list->toArray());

$blockProduct = '';
$blockCompany = 'style="display: none"';
if (array_intersect($getParamCompany, $arr)) {
    $blockCompany = '';
    $blockProduct = 'style="display: none"';
}

?>
    <div class="container-fluid breadcrumbs-wrapper">
        <? $APPLICATION->IncludeComponent(
            "bitrix:breadcrumb",
            "catalog",
            array(
                "PATH" => "",
                "SITE_ID" => "s1",
                "START_FROM" => "0"
            ),
            false,
            array(
                'HIDE_ICONS' => 'Y'
            )
        ); ?>
    </div>
    <div class="container-fluid">
        <div class="heading">
            <div class="heading__item">
                <h1 class="heading__title"><?= $APPLICATION->ShowTitle(false) ?></h1>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="blog-page">
            <section>
                <div class="blog-nav">
                    <div data-name="product-reviews" class="item">
                        <div class="title">Отзывы о товарах</div>
                        <div class="count">1300+</div>
                    </div>
                    <div data-name="product-company" class="item">
                        <div class="title">Отзывы о компании</div>
                        <div class="count">200+</div>
                    </div>
                </div>
            </section>
            <section class="py-4" data-entity="parent-container" data-block="product-reviews" <?= $blockProduct ?>>
                <div class="heading" data-entity="header">
                    <div class="heading__item">
                        <h3 class="heading__title">Отзывы о товарах</h3>
                    </div>
                </div>

                <div class="row justify-content-between">
                    <div class="col-12 col-md-8 col-xl-7">
                        <? $APPLICATION->IncludeComponent('webfly:reviews', '', [
                            'IBLOCK_TYPE' => WF_CATALOG_IBLOCK_TYPE,
                            'IBLOCK_ID' => WF_CATALOG_IBLOCK_ID,
                            'ELEMENT_ID' => '',
                            'PAGE_COUNT' => 10,
                            'PAGER_TEMPLATE' => 'reviews_new',
                            'REVIEWS_IBLOCK_TYPE' => 'services',
                            'REVIEWS_IBLOCK_ID' => WF_REVIEWS_IBLOCK_ID,
                            // ajax
                            "AJAX_MODE" => "Y",
                            "AJAX_OPTION_SHADOW" => "N",
                            "AJAX_OPTION_JUMP" => "N",
                            "AJAX_OPTION_STYLE" => "N",
                            "AJAX_OPTION_HISTORY" => "N",
                            "AJAX_OPTION_ADDITIONAL" => "wf",
                        ]); ?>
                    </div>
                    <!--                    <aside class="col-12 col-md-4">-->
                    <!--                        <div class="review-radio">-->
                    <!--                            <p class="review-filter--header">Сортировать по оценке</p>-->
                    <!--                            <div class="review-filter">-->
                    <!--                                <label for="firstID">-->
                    <!--                                    <input type="radio" name="REVIEW_SCORE" id="firstID">-->
                    <!--                                    <ul>-->
                    <!--                                        <li class="fill"></li>-->
                    <!--                                        <li class="fill"></li>-->
                    <!--                                        <li class="fill"></li>-->
                    <!--                                        <li class="fill"></li>-->
                    <!--                                        <li class="fill"></li>-->
                    <!--                                    </ul>-->
                    <!--                                    <span>26 отзывов</span>-->
                    <!--                                </label>-->
                    <!--                                <label for="secondID">-->
                    <!--                                    <input type="radio" name="REVIEW_SCORE" id="secondID">-->
                    <!--                                    <ul>-->
                    <!--                                        <li class="fill"></li>-->
                    <!--                                        <li class="fill"></li>-->
                    <!--                                        <li class="fill"></li>-->
                    <!--                                        <li class="fill"></li>-->
                    <!--                                        <li></li>-->
                    <!--                                    </ul>-->
                    <!--                                    <span>6 отзывов</span>-->
                    <!--                                </label>-->
                    <!--                                <label for="thirdID">-->
                    <!--                                    <input type="radio" name="REVIEW_SCORE" id="thirdID">-->
                    <!--                                    <ul>-->
                    <!--                                        <li class="fill"></li>-->
                    <!--                                        <li class="fill"></li>-->
                    <!--                                        <li class="fill"></li>-->
                    <!--                                        <li></li>-->
                    <!--                                        <li></li>-->
                    <!--                                    </ul>-->
                    <!--                                    <span>0 отзывов</span>-->
                    <!--                                </label>-->
                    <!--                                <label for="fourthID">-->
                    <!--                                    <input type="radio" name="REVIEW_SCORE" id="fourthID">-->
                    <!--                                    <ul>-->
                    <!--                                        <li class="fill"></li>-->
                    <!--                                        <li class="fill"></li>-->
                    <!--                                        <li></li>-->
                    <!--                                        <li></li>-->
                    <!--                                        <li></li>-->
                    <!--                                    </ul>-->
                    <!--                                    <span>1 отзывов</span>-->
                    <!--                                </label>-->
                    <!--                                <label for="fifthID">-->
                    <!--                                    <input type="radio" name="REVIEW_SCORE" id="fifthID">-->
                    <!--                                    <ul>-->
                    <!--                                        <li class="fill"></li>-->
                    <!--                                        <li></li>-->
                    <!--                                        <li></li>-->
                    <!--                                        <li></li>-->
                    <!--                                        <li></li>-->
                    <!--                                    </ul>-->
                    <!--                                    <span>1 отзывов</span>-->
                    <!--                                </label>-->
                    <!--                            </div>-->
                    <!--                        </div>-->
                    <!--                    </aside>-->
                </div>
            </section>
            <section data-block="product-company" <?= $blockCompany ?>>
                <div class="heading" data-entity="header">
                    <div class="heading__item">
                        <h3 class="heading__title">Отзывы о компании</h3>
                    </div>
                </div>
                <div class="row justify-content-between">
                    <? $APPLICATION->IncludeComponent(
	"disprove:reviews.market", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"REVIEWS_COUNT" => "20",
		"LAZY_LOAD" => "N",
		"LOAD_ON_SCROLL" => "Y",
		"SHOW_NO_MOD" => "N",
		"JQUERY_BIBL" => "N",
		"SHOW_DELETED" => "N",
		"SHOW_TITLE" => "N",
		"SHOW_FACTS" => "Y",
		"SHOW_PADDING" => "Y",
		"DELETE_LINK" => "N",
		"SHOW_ITEMS" => "Y",
		"ELEMENT_SORT_FIELD" => "date",
		"ELEMENT_SORT_ORDER" => "desc",
		"TEMPLATE_THEME" => "green",
		"SEF_MODE" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_GROUPS" => "Y",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"PAGER_TEMPLATE" => "bootstrap_v4",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Отзывы yandex.Market",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_BASE_LINK_ENABLE" => "N"
	),
	false
); ?>
                </div>
            </section>
        </div>
    </div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>