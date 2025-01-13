<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
global $APPLICATION;
global $USER;

?>

<? if (CSite::InDir('/tablica_podzavoda_chasov/')) { ?>
    </div>
    </section>
    </div>
<? } ?>

</div>
<footer class="footer">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <div class="col-12 col-md-4 col-xl-3">
                <div class="footer-item">
                    <p class="footer-item__caption">2010 - <?= date('Y') ?> © Timecube.ru</p>
                </div>
                <? $APPLICATION->IncludeComponent("bitrix:main.include", "", [
                    "PATH" => SITE_DIR . "include/footer_info.php",
                    "AREA_FILE_SHOW" => "file", "EDIT_MODE" => "html",
                ], false, ['HIDE_ICONS' => 'N']
                ); ?>
                <!--noindex-->
                <div class="footer-item">
                    <div class="footer-item__content">
                        <p>Разработано в <a href="//webfly.ru" rel="nofollow" target="_blank">webfly.ru</a></p>
                    </div>
                </div>
                <!--/noindex-->
            </div>
            <div class="col-12 col-md-4 col-xl-3">
                <div class="footer-item spoiler">
                    <p class="footer-item__caption toggler">Каталог товаров</p>
                    <div class="footer-item__content content">
                        <? $APPLICATION->IncludeComponent("bitrix:catalog.section.list", "footer_menu",
                            [
                                "IBLOCK_TYPE" => WF_CATALOG_IBLOCK_TYPE,
                                "IBLOCK_ID" => WF_CATALOG_IBLOCK_ID,
                                "SECTION_ID" => "",
                                "SECTION_CODE" => WF_CATALOG_ROOT,
                                "COUNT_ELEMENTS" => "N",
                                "COUNT_ELEMENTS_FILTER" => "CNT_ACTIVE",
                                "TOP_DEPTH" => "1",
                                "SECTION_FIELDS" => [],
                                "SECTION_USER_FIELDS" => [],
                                "FILTER_NAME" => "sectionsFilter",
                                "SHOW_PARENT_NAME" => "N",
                                "SECTION_URL" => "",
                                "CACHE_TYPE" => "A",
                                "CACHE_TIME" => "36000000",
                                "CACHE_GROUPS" => "Y",
                                "CACHE_FILTER" => "Y",
                                "ADD_SECTIONS_CHAIN" => "N"
                            ], false, ["HIDE_ICONS" => "Y"]
                        ); ?>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 col-xl-3">
                <div class="footer-item spoiler">
                    <p class="footer-item__caption toggler">Покупателю</p>
                    <div class="footer-item__content content">
                        <? $APPLICATION->IncludeComponent(
                            "bitrix:menu",
                            "footer",
                            array(
                                "ROOT_MENU_TYPE" => "top",
                                "MENU_CACHE_TYPE" => "A",
                                "MENU_CACHE_TIME" => "3600",
                                "MENU_CACHE_USE_GROUPS" => "N",
                                "MENU_CACHE_GET_VARS" => array(),
                                "MAX_LEVEL" => "1",
                                "CHILD_MENU_TYPE" => "",
                                "USE_EXT" => "Y",
                                "DELAY" => "N",
                                "ALLOW_MULTI_SELECT" => "N"
                            ),
                            false
                        ); ?>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-xl-3">
                <div class="footer-item">
                    <p class="footer-item__caption">Контакты</p>
                    <div class="footer-item__content">
                        <div class="row">
                            <div class="col-12 col-md-4 col-xl-12">
                                <? $APPLICATION->IncludeComponent("bitrix:main.include", "", [
                                    "PATH" => SITE_DIR . "include/telephone.php",
                                    "AREA_FILE_SHOW" => "file", "EDIT_MODE" => "html",
                                ], false, ['HIDE_ICONS' => 'N']
                                ); ?>
                            </div>
                            <div class="col-12 col-md-4 col-xl-12">
                                <b>Адрес</b>
                                <? $APPLICATION->IncludeComponent("bitrix:main.include", "", [
                                    "PATH" => SITE_DIR . "include/address.php",
                                    "AREA_FILE_SHOW" => "file", "EDIT_MODE" => "html",
                                ], false, ['HIDE_ICONS' => 'N']
                                ); ?>
                            </div>
                            <div class="col-12 col-md-4 col-xl-12">
                                <b>Режим работы магазина</b>
                                <? $APPLICATION->IncludeComponent("bitrix:main.include", "", [
                                    "PATH" => SITE_DIR . "include/schedule.php",
                                    "AREA_FILE_SHOW" => "file", "EDIT_MODE" => "html",
                                ], false, ['HIDE_ICONS' => 'N']
                                ); ?>
                                <? $APPLICATION->IncludeComponent("bitrix:main.include", "", [
                                    "PATH" => SITE_DIR . "include/email.php",
                                    "AREA_FILE_SHOW" => "file", "EDIT_MODE" => "html",
                                ], false, ['HIDE_ICONS' => 'N']
                                ); ?>

                            </div>

                            <div class="col-12 col-md-12">
                                <? $APPLICATION->IncludeComponent("bitrix:main.include", "", [
                                    "PATH" => SITE_DIR . "include/socnet_sidebar.php",
                                    "AREA_FILE_SHOW" => "file", "EDIT_MODE" => "html",
                                ], false, ['HIDE_ICONS' => 'N']
                                ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <? $APPLICATION->IncludeComponent("bitrix:main.include", "", [
                "PATH" => SITE_DIR . "include/footer_note.php",
                "AREA_FILE_SHOW" => "file", "EDIT_MODE" => "html",
            ], false, ['HIDE_ICONS' => 'N']
            ); ?>
        </div>
    </div>
</footer>
</section>
</div> <!-- //bx-wrapper -->

<?
$asset = \Bitrix\Main\Page\Asset::getInstance();
$asset->addJs("/assets/js/vendors.js");
$asset->addJs("/assets/js/app.js");
$asset->addJs("/assets/js/nouislider.min.js");

//if (!$USER->isAdmin()) $asset->addString('<script src="//code.jivosite.com/widget/sQTM4Cg616" async></script>');
//$asset->addString('<script async src="https://www.googletagmanager.com/gtag/js?id=UA-27153558-1"></script>');
if (CSite::InDir('/product/')) $asset->addString('<script type="text/javascript" src="/assets/widget/widjet.js" id="ISDEKscript" charset="utf-8"></script>');
?>
<!--<noscript>-->
<!--    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-55TJDZD"-->
<!--            height="0" width="0" style="display:none;visibility:hidden"></iframe>-->
<!--</noscript>-->

<?
$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$uri = $request->getRequestedPageDirectory();
$requestList = implode(array_flip($request->getQueryList()->toArray()), ':');
if (strpos($requestList, 'PAGEN_') !== false) {
    $pagen = true;
}

$filter = false;
$sort = $request->getQuery("sort");
$view = $request->getQuery("view");
$available = $request->getQuery("available");
$wrongStr = explode('/', $uri);
if (strpos($uri, '/filter/') !== false && strpos($uri, 'apply') !== false) {
    $filter = true;
    $filterPos = strpos($uri, 'filter/');
    $uriNoFilter = substr($uri, 0, $filterPos);
}

if (Zverushki\Seofilter\configuration::get('requestUri')) {
    Bitrix\Main\Loader::includeModule('zverushki.seofilter');
    $urlSeo = \Zverushki\Seofilter\configuration::get('requestUri');
    $existSeo = false;
    $dbList = \Zverushki\Seofilter\Internals\SettingsTable::GetList([
        'order' => ['ID' => 'ASC'],
        'filter' => ['IBLOCK_ID' => 10, 'URL_CPU' => $urlSeo],
        'select' => ['ID'],
        'limit' => 1
    ]);
    while ($arResultSeo = $dbList->fetch()) {
        if ($arResultSeo) {
            $existSeo = true;
        }
    }
}

if ((!empty($requestList) && $APPLICATION->GetPageProperty("catalog") == 'Y') || $filter || $pagen) {
    if ((!empty($requestList) && $APPLICATION->GetPageProperty("catalog") == 'Y') || $pagen) {
        $APPLICATION->SetPageProperty("robots", "noindex, nofollow");
    } else {
        $APPLICATION->SetPageProperty("robots", "index, follow");
    }
    if ($filter && $uriNoFilter) {
        if (!$existSeo) $APPLICATION->AddHeadString('<link rel="canonical" href="https://timecube.ru' . $uriNoFilter . '">', true);
    } else {
        $APPLICATION->AddHeadString('<link rel="canonical" href="https://timecube.ru' . $uri . '/">', true);
    }
} elseif ((count($wrongStr) > 3 && $wrongStr[1] == 'product') || $pagen || strstr(CHTTP::GetLastStatus(), '404') || $wrongStr[1] == 'compare' || $wrongStr[1] == 'favorites') {
    $APPLICATION->SetPageProperty("robots", "noindex, nofollow");
} else {
    $APPLICATION->SetPageProperty("robots", "index, follow");
    if (!$existSeo) $APPLICATION->AddHeadString('<link rel="canonical" href="https://timecube.ru' . $uri . '/">', true);
}


$noIndexParams = ['action', 'bxajaxi', 'backurl'];
if (array_intersect($noIndexParams, $request->getQueryList()->toArray())) {
    $APPLICATION->SetPageProperty("robots", "noindex, nofollow");
    $APPLICATION->SetPageProperty("googlebot","noindex, nofollow");
    if (!defined("ERROR_404")) define("ERROR_404", "Y");
    \CHTTP::setStatus("404 Not Found");
}


?>
<? Webfly\Handlers\Counters::insert(); ?>
</body>
</html>