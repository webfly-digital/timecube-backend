<?
include_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/urlrewrite.php');

CHTTP::SetStatus("404 Not Found");
@define("ERROR_404", "Y");
define("HIDE_SIDEBAR", true);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("404 Not Found");
$APPLICATION->SetPageProperty("title", '404 Not Found');
$APPLICATION->SetPageProperty("description", 'Несуществующая страница');
?>
    <div class="bx-404-container">
        <div class="bx-404-block"><img src="<?= SITE_DIR ?>images/404.png" alt=""></div>
        <div class="bx-404-text-block">Неправильно набран адрес, <br>или такой страницы на сайте больше не существует.
        </div>
        <div class="">Вернитесь на <a href="<?= SITE_DIR ?>">главную</a> или воспользуйтесь картой сайта.</div>
        <div class="bx-maps-title">Карта сайта:</div>
    </div>

    <div class="three-columns" id="inner-page">
        <section class="three-columns__body">
            <div class="container-fluid">
                <div class="col-sm-offset-2 col-sm-4">
                    <div class="bx-map-title pt-4">Каталог</div>
                    <? $APPLICATION->IncludeComponent(
                        "bitrix:catalog.section.list",
                        "tree",
                        array(
                            "COMPONENT_TEMPLATE" => "tree",
                            "IBLOCK_TYPE" => WF_CATALOG_IBLOCK_TYPE,
                            "IBLOCK_ID" => WF_CATALOG_IBLOCK_ID,
                            "SECTION_ID" => $_REQUEST["SECTION_ID"],
                            "SECTION_CODE" => "",
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
                    ?>
                </div>
                <div class="col-sm-offset-1">
                    <div class="bx-map-title pt-4">О магазине</div>
                    <?
                    $APPLICATION->IncludeComponent(
                        "bitrix:main.map",
                        ".default",
                        array(
                            "CACHE_TYPE" => "A",
                            "CACHE_TIME" => "36000000",
                            "SET_TITLE" => "N",
                            "LEVEL" => "3",
                            "COL_NUM" => "2",
                            "SHOW_DESCRIPTION" => "Y",
                            "COMPONENT_TEMPLATE" => ".default"
                        ),
                        false
                    ); ?>
                </div>
            </div>
        </section>
    </div>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>