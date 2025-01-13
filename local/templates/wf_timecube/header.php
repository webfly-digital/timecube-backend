<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $APPLICATION;
global $USER;

IncludeTemplateLangFile($_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/" . SITE_TEMPLATE_ID . "/header.php");
CJSCore::Init(["fx"]);

//\Bitrix\Main\UI\Extension::load("ui.bootstrap4");

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$page = $request->getRequestedPage();
$dir = $request->getRequestedPageDirectory();
$isMainPage = $dir === '';
$bodyClass = 'bf-promo-theme';

$pathToRegister = '/login/?register=yes&backurl=' . urlencode($request->getRequestUri());
$pathToAuthorize = '/login/?login=yes&backurl=' . urlencode($request->getRequestUri());

$arrGet = $request->getQueryList()->toArray();
if(!empty($arrGet) && empty(array_values($arrGet)[0])) LocalRedirect($APPLICATION->getCurDir(), false, '301 Moved permanently');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title><? $APPLICATION->ShowTitle() ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="yandex-verification" content="675184e9f72d1821" />
    <link rel="preconnect" href="https://code.jivosite.com"/> 
    <link as="image" rel="preload" href="/assets/img/logo-timecube.svg"/>
    <!--        <link rel="preload"-->
    <!--              href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900&amp;display=swap&amp;subset=cyrillic"-->
    <!--              as="style" onload="this.onload=null;this.rel='stylesheet'">-->
    <?
    include_once('style-header.php');
    \Bitrix\Main\Page\Asset::getInstance()->addCss("/assets/css/app.css");
    \Bitrix\Main\Page\Asset::getInstance()->addCss("/assets/fonts/roboto/css.css");
    ?>
    <? $APPLICATION->ShowHead(); ?>
    <meta name="google-site-verification" content="VrcPF2ARot5dteXnWC3q9F9u_iUFnG2-xa1_KZZixjE" />
</head>
<body class="<?= $bodyClass . ($isMainPage ? ' main-layout' : ' inner-layout') ?> ">
<div id="panel"><? $APPLICATION->ShowPanel(); ?></div>
<div class="bx-wrapper page-wrapper">
    <div class="sidebar-controls">
        <button class="btn-rounded sidebar-control btn-primary" data-target="main-menu">
            <span class="btn-rounded__icon svg-icon icon-list"></span>
            <span class="btn-rounded__caption">Меню</span>
        </button>
        <? $APPLICATION->ShowViewContent('catalog_filter_button'); ?>
    </div>
    <?
    $sidebarView = '';
    if ($isMainPage) $sidebarView = 'sidebar--normal-view';
    if (!$isMainPage) $sidebarView = 'sidebar--compact-view';
    ?>
    <aside class="sidebar <?= $sidebarView ?>" id="sidebar">
        <div class="sidebar-top <?= $isMainPage ? 'd-none d-xl-block' : '' ?>" id="sidebar-top">
            <div class="container-fluid">
                <a class="logo" href="/" title="Timecube.ru"><img src="/assets/img/logo-timecube.svg" alt="Timecube.ru" title="Timecube.ru"></a>
            </div>
        </div>
        <div class="sidebar-slide sidebar-slide--expanded" id="main-menu">
            <button class="btn-rounded sidebar-control btn-primary btn-close" data-target="" style="top: 386px;"><span
                        class="btn-rounded__icon svg-icon icon-close"></span><span
                        class="btn-rounded__caption">Закрыть</span></button>
            <div class="sidebar-slide__content">
                <div class="container">
                    <div class="sidebar-slide__section d-xl-none">
                        <ul class="nav-top">
                            <? if ($USER->isAuthorized()) { ?>
                                <li><a class="link-green" href="/personal/">👤 <?= $USER->getEmail() ?></a></li>
                                <li><a class="link-green" href="/login/?logout=yes">Выйти</a></li>
                            <? } else { ?>
                                <li><a class="link-green mfp-inline-link" href="#auth">Войти</a></li>
                            <? } ?>
                        </ul>
                    </div>
                    <div class="sidebar-slide__section">
                        <!-- Mainmenu begin-->
                        <? $APPLICATION->IncludeComponent("bitrix:catalog.section.list", "side_menu",
                            [
                                "WF_CACHE_VAR" => $dir,
                                "COMPONENT_TEMPLATE" => "bootstrap_v4",
                                "IBLOCK_TYPE" => WF_CATALOG_IBLOCK_TYPE,
                                "IBLOCK_ID" => WF_CATALOG_IBLOCK_ID,
                                "SECTION_ID" => "",
                                "SECTION_CODE" => WF_CATALOG_ROOT,
                                "COUNT_ELEMENTS" => "N",
                                "COUNT_ELEMENTS_FILTER" => "CNT_ACTIVE",
                                "TOP_DEPTH" => "1",
                                "SECTION_FIELDS" => ['CODE'],
                                "SECTION_USER_FIELDS" => [],
                                "FILTER_NAME" => "sectionsFilter",
                                "SECTION_URL" => "",
                                "CACHE_TYPE" => "A",
                                "CACHE_TIME" => "36000000",
                                "CACHE_GROUPS" => "Y",
                                "CACHE_FILTER" => "Y",
                                "ADD_SECTIONS_CHAIN" => "N"
                            ], false, ["HIDE_ICONS" => "Y"]
                        ); ?>
                        <!-- Mainmenu end-->
                    </div>
                    <div class="sidebar-slide__section d-xl-none">
                        <!--Mobile About menu begin-->
                        <? $APPLICATION->IncludeComponent(
                            "bitrix:menu",
                            "top",
                            array(
                                "ROOT_MENU_TYPE" => "top",
                                "MENU_CACHE_TYPE" => "A",
                                "MENU_CACHE_TIME" => "3600",
                                "MENU_CACHE_USE_GROUPS" => "N",
                                "MENU_CACHE_GET_VARS" => array(),
                                "MAX_LEVEL" => "1",
                                "CHILD_MENU_TYPE" => "",
                                "USE_EXT" => "N",
                                "DELAY" => "N",
                                "ALLOW_MULTI_SELECT" => "N"
                            ),
                            false
                        ); ?>
                        <!--Mobile about menu end-->
                    </div>
                    <div class="sidebar-slide__section d-xl-none">
                        <!--Phone begin-->
                        <div class="phone-block">
                            <? $APPLICATION->IncludeComponent("bitrix:main.include", "",
                                ["AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/telephone.php"],
                                false, ['HIDE_ICONS' => 'Y']
                            ); ?>
                        </div>
                        <!--phone end-->
                    </div>
                    <div class="sidebar-slide__section d-xl-none">
                        <!-- Address begin-->
                        <div class="address-block">
                            <? $APPLICATION->IncludeComponent("bitrix:main.include", "",
                                ["AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/address.php"],
                                false, ['HIDE_ICONS' => 'Y']
                            ); ?>
                        </div>
                        <!-- Address end-->
                    </div>
                </div>
            </div>
        </div>
        <div class="sidebar-slide bg-light-gray d-xl-none" id="mobile-filters-menu">
            <button class="btn-rounded sidebar-control btn-secondary btn-close" data-target="" style="top: 386px;"><span
                        class="btn-rounded__icon svg-icon icon-close"></span><span
                        class="btn-rounded__caption">Закрыть</span></button>
            <div class="sidebar-slide__content">
                <div class="container">
                    <!--Сюда будут переноситься фильтры при ширине экрана < 1200-->
                </div>
            </div>
        </div>
    </aside>
    <section class="maincontent">
        <!-- Header begin-->
        <header class="header">

            <?
            $APPLICATION->IncludeComponent("bitrix:news.list", "service-message", array(
                "IBLOCK_ID" => "17",
                "IBLOCK_TYPE" => "services",
                "NEWS_COUNT" => "3",
                "SORT_BY1" => "ACTIVE_FROM",
                "SORT_ORDER1" => "DESC",
                "SORT_BY2" => "SORT",
                "SORT_ORDER2" => "ASC",
                "FILTER_NAME" => "",
                "FIELD_CODE" => [],
                "PROPERTY_CODE" => [],
                "CHECK_DATES" => "Y",
                "DETAIL_URL" => "",
                "AJAX_MODE" => "N",
                "AJAX_OPTION_JUMP" => "N",
                "AJAX_OPTION_STYLE" => "N",
                "AJAX_OPTION_HISTORY" => "N",
                "AJAX_OPTION_ADDITIONAL" => "",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "3600",
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
                "DISPLAY_NAME" => "N",
                "DISPLAY_PICTURE" => "N",
                "DISPLAY_PREVIEW_TEXT" => "N",
                "PAGER_TEMPLATE" => ".default",
                "DISPLAY_TOP_PAGER" => "N",
                "DISPLAY_BOTTOM_PAGER" => "N",
                "PAGER_TITLE" => "Новости",
                "PAGER_SHOW_ALWAYS" => "N",
                "PAGER_DESC_NUMBERING" => "N",
                "PAGER_DESC_NUMBERING_CACHE_TIME" => "3600",
                "PAGER_SHOW_ALL" => "N",
                "PAGER_BASE_LINK_ENABLE" => "N",
                "SET_STATUS_404" => "N",
                "SHOW_404" => "N",
                "MESSAGE_404" => "",
            ),
                false,
                [
                    "HIDE_ICONS" => "Y"
                ]
            );
            ?>

            <!--Mobile top Begin-->
            <nav class="mobile-top-header d-md-none">
                <div class="container-fluid">
                    <div class="mobile-top-header__row">
                        <div class="mobile-top-header__section">
                            <button class="search-opener mobile-top-header__control" aria-label="Поиск по сайту"><span
                                        class="svg-icon icon-search"></span></button>
                        </div>
                        <div class="mobile-top-header__section"><a class="mobile-top-header__control"
                                                                   href="tel:+78007752576"><span
                                        class="svg-icon icon-call"></span></a></div>
                        <div class="mobile-top-header__section"><a class="mobile-top-header__control" href="/compare/"
                                                                   aria-label="Сравнение"><span
                                        class="svg-icon icon-compare"></span></a></div>
                        <div class="mobile-top-header__section">
                            <a class="mobile-top-header__control" href="/favorites/" aria-label="Избранное">
                                <span class="svg-icon icon-favorite"></span>
                                <span class="mobile-top-header__control-counter" id="wf_favorites_counter_mobile"
                                      style="display: none"></span>
                            </a>
                        </div>
                        <div class="mobile-top-header__section">
                            <a class="mobile-top-header__control" href="/personal/cart/" aria-label="Корзина">
                                <span class="svg-icon icon-cart"></span>
                                <span class="mobile-top-header__control-counter" id="wf_basket_counter_mobile"
                                      style="display: none"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </nav>
            <!--Mobile top end-->
            <div class="container-fluid">
                <div class="header__top d-none d-xl-flex">
                    <div class="header__top-section">
                        <!--About menu begin-->
                        <? $APPLICATION->IncludeComponent(
                            "bitrix:menu",
                            "top",
                            array(
                                "ROOT_MENU_TYPE" => "top",
                                "MENU_CACHE_TYPE" => "A",
                                "MENU_CACHE_TIME" => "3600",
                                "MENU_CACHE_USE_GROUPS" => "N",
                                "MENU_CACHE_GET_VARS" => array(),
                                "MAX_LEVEL" => "1",
                                "CHILD_MENU_TYPE" => "",
                                "USE_EXT" => "N",
                                "DELAY" => "N",
                                "ALLOW_MULTI_SELECT" => "N"
                            ),
                            false
                        ); ?>
                        <!--about menu end-->
                    </div>
                    <div class="header__top-section">
                        <div class="row">
                            <div class="col-auto">
                                <ul class="nav-top">
                                    <? if ($USER->isAuthorized()) { ?>
                                        <li><a class="link-green" href="/personal/">👤 <?= $USER->getEmail() ?></a></li>
                                        <li><a class="link-green" href="/login/?logout=yes">Выйти</a></li>
                                    <? } else { ?>
                                        <li><a class="link-green mfp-inline-link" href="#auth">Войти</a></li>
                                    <? } ?>
                                </ul>
                                <div class="wf-popup wf-popup-medium" id="auth">
                                    <div class="wf-popup__body">
                                        <?
                                        if (!$USER->isAuthorized() || $USER->isAdmin()) {
                                            $APPLICATION->IncludeComponent(
                                                "bitrix:system.auth.authorize", "flat", [
                                                "SHOW_ERRORS" => "Y"
                                            ], false, ['HIDE_ICONS' => 'Y']
                                            );
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="header__bottom">
                    <div class="header__bottom-section header-logo"><a class="logo" href="/" title="Timecube.ru"><img
                                    height="33" width="200"
                                    src="/assets/img/logo-timecube.svg" alt="Timecube.ru"></a>
                    </div>
                    <div class="header__bottom-section d-none d-xl-block">
                        <!--Phone begin-->
                        <div class="phone-block">
                            <? $APPLICATION->IncludeComponent("bitrix:main.include", "",
                                ["AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/telephone.php"]
                            ); ?>
                        </div>
                        <!--phone end-->
                    </div>
                    <div class="header__bottom-section d-none d-xl-block">
                        <!-- Address begin-->
                        <div class="address-block">
                            <? $APPLICATION->IncludeComponent("bitrix:main.include", "",
                                ["AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/address.php"]
                            ); ?>
                        </div>
                        <!-- Address end-->
                    </div>
                    <div class="header__bottom-section d-none d-lg-block" id="header-search">
                        <div class="search-simple">
                            <form class="search-simple__form" action="/search/">
                                <div class="search-simple__controls">
                                    <!--сюда кроме этих инпутов ничего не добавлять, всё остальное снаружи этого блока-->
                                    <input class="search-simple__input" type="text" name="q" placeholder="Поиск"
                                           aria-label="Поиск по сайту">
                                    <button class="btn-transparent search-simple__btn"><span
                                                class="svg-icon icon-search"></span></button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="header__bottom-section d-none d-md-block">
                        <!--header controls starts-->
                        <div class="header__controls">
                            <div class="header__control-item">
                                <? $APPLICATION->IncludeComponent("bitrix:catalog.compare.list", "bootstrap_v4", [
                                    "IBLOCK_TYPE" => WF_CATALOG_IBLOCK_TYPE,
                                    "IBLOCK_ID" => WF_CATALOG_IBLOCK_ID,
                                    "NAME" => "",
                                    "DETAIL_URL" => "/#SECTION_CODE_PATH#/#ELEMENT_CODE#/",
                                    "COMPARE_URL" => "/compare/",
                                    "ACTION_VARIABLE" => "action",
                                    "PRODUCT_ID_VARIABLE" => "id",
                                    "POSITION_FIXED" => "N",
                                    "POSITION" => "",
                                    "AJAX_MODE" => "N",
                                    "AJAX_OPTION_JUMP" => "N",
                                    "AJAX_OPTION_STYLE" => "N",
                                    "AJAX_OPTION_HISTORY" => "N",
                                    "AJAX_OPTION_ADDITIONAL" => ""
                                ], false, ["HIDE_ICONS" => "Y"]
                                ); ?>
                            </div>
                            <div class="header__control-item">
                                <? $APPLICATION->IncludeComponent("webfly:favorites", "", [
                                ], false, ["HIDE_ICONS" => "Y"]
                                ); ?>
                            </div>
                            <div class="header__control-item">
                                <? $APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", "bootstrap_v4", [
                                    "PATH_TO_ORDER" => "/personal/order/make/",
                                    "PATH_TO_BASKET" => "/personal/cart/",
                                    "PATH_TO_PERSONAL" => "/personal/",
                                    "PATH_TO_PROFILE" => "/personal/profile/",
                                    "PATH_TO_REGISTER" => $pathToRegister,
                                    "PATH_TO_AUTHORIZE" => $pathToAuthorize,
                                    "SHOW_PERSONAL_LINK" => "N",
                                    "SHOW_NUM_PRODUCTS" => "Y",
                                    "SHOW_TOTAL_PRICE" => "N",
                                    "SHOW_PRODUCTS" => "N",
                                    "POSITION_FIXED" => "N",
                                    "SHOW_AUTHOR" => "N",
                                    "HIDE_ON_BASKET_PAGES" => "Y",
                                    "SHOW_EMPTY_VALUES" => "N",
                                    "SHOW_REGISTRATION" => "N",
                                    "SHOW_DELAY" => "N",
                                    "SHOW_NOTAVAIL" => "N",
                                    "SHOW_IMAGE" => "N",
                                    "SHOW_PRICE" => "Y",
                                    "SHOW_SUMMARY" => "N",
                                    "MAX_IMAGE_SIZE" => "70"
                                ], false, ["HIDE_ICONS" => "Y"]
                                ); ?>
                            </div>
                        </div>
                        <!--header controls ends-->
                    </div>
                    <div class="header__bottom-section d-lg-none">
                        <div class="mobile-controls">
                            <button class="btn-rounded sidebar-control btn-primary" data-target="main-menu">
                                <span class="btn-rounded__icon svg-icon icon-list"></span>
                                <span class="btn-rounded__caption">Меню</span>
                            </button>
                            <div class="mobile-controls__additional">
                                <? $APPLICATION->ShowViewContent('catalog_filter_button'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!--header end-->
        <div id="content">
            <? if (CSite::InDir('/tablica_podzavoda_chasov/')) { ?>
            <div class="three-columns" id="inner-page">
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
                        <div class="heading">
                            <div class="heading__item">
                                <h1 class="heading__title"><?= $APPLICATION->ShowTitle(false) ?></h1>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid">
                        <? } ?>
