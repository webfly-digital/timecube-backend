<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

$this->setFrameMode(true);

//region init bx

$templateLibrary = array('popup', 'fx');
$currencyList = '';

if (!empty($arResult['CURRENCIES'])) {
    $templateLibrary[] = 'currency';
    $currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
}

$templateData = array(
    'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
    'TEMPLATE_LIBRARY' => $templateLibrary,
    'CURRENCIES' => $currencyList,
    'ITEM' => array(
        'ID' => $arResult['ID'],
        'IBLOCK_ID' => $arResult['IBLOCK_ID'],
        'OFFERS_SELECTED' => $arResult['OFFERS_SELECTED'],
        'JS_OFFERS' => $arResult['JS_OFFERS']
    )
);
unset($currencyList, $templateLibrary);

$mainId = $this->GetEditAreaId($arResult['ID']);
$itemIds = array(
    'ID' => $mainId,
    'DISCOUNT_PERCENT_ID' => $mainId . '_dsc_pict',
    'STICKER_ID' => $mainId . '_sticker',
    'BIG_SLIDER_ID' => $mainId . '_big_slider',
    'BIG_IMG_CONT_ID' => $mainId . '_bigimg_cont',
    'SLIDER_CONT_ID' => $mainId . '_slider_cont',
    'OLD_PRICE_ID' => $mainId . '_old_price',
    'PRICE_ID' => $mainId . '_price',
    'DISCOUNT_PRICE_ID' => $mainId . '_price_discount',
    'PRICE_TOTAL' => $mainId . '_price_total',
    'SLIDER_CONT_OF_ID' => $mainId . '_slider_cont_',
    'QUANTITY_ID' => $mainId . '_quantity',
    'QUANTITY_DOWN_ID' => $mainId . '_quant_down',
    'QUANTITY_UP_ID' => $mainId . '_quant_up',
    'QUANTITY_MEASURE' => $mainId . '_quant_measure',
    'QUANTITY_LIMIT' => $mainId . '_quant_limit',
    'BUY_LINK' => $mainId . '_buy_link',
    'ADD_BASKET_LINK' => $mainId . '_add_basket_link',
    'BASKET_ACTIONS_ID' => $mainId . '_basket_actions',
    'NOT_AVAILABLE_MESS' => $mainId . '_not_avail',
    'COMPARE_LINK' => $mainId . '_compare_link',
    'TREE_ID' => $mainId . '_skudiv',
    'DISPLAY_PROP_DIV' => $mainId . '_sku_prop',
    'DISPLAY_MAIN_PROP_DIV' => $mainId . '_main_sku_prop',
    'OFFER_GROUP' => $mainId . '_set_group_',
    'BASKET_PROP_DIV' => $mainId . '_basket_prop',
    'SUBSCRIBE_LINK' => $mainId . '_subscribe',
    'TABS_ID' => $mainId . '_tabs',
    'TAB_CONTAINERS_ID' => $mainId . '_tab_containers',
    'SMALL_CARD_PANEL_ID' => $mainId . '_small_card_panel',
    'TABS_PANEL_ID' => $mainId . '_tabs_panel'
);
$obName = $templateData['JS_OBJ'] = 'ob' . preg_replace('/[^a-zA-Z0-9_]/', 'x', $mainId);
$name = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'])
    ? $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
    : $arResult['NAME'];
$title = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'])
    ? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE']
    : $arResult['NAME'];
$alt = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'])
    ? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT']
    : $arResult['NAME'];

$haveOffers = !empty($arResult['OFFERS']);

if ($haveOffers) {
    $actualItem = isset($arResult['OFFERS'][$arResult['OFFERS_SELECTED']])
        ? $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]
        : reset($arResult['OFFERS']);
    $showSliderControls = false;

    foreach ($arResult['OFFERS'] as $offer) {
        if ($offer['MORE_PHOTO_COUNT'] > 1) {
            $showSliderControls = true;
            break;
        }
    }
} else {
    $actualItem = $arResult;
    $showSliderControls = $arResult['MORE_PHOTO_COUNT'] > 1;
}

$skuProps = array();

$price = $actualItem['ITEM_PRICES'][$actualItem['ITEM_PRICE_SELECTED']];
$measureRatio = $actualItem['ITEM_MEASURE_RATIOS'][$actualItem['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'];
$showDiscount = $price['PERCENT'] > 0;

$showDescription = !empty($arResult['PREVIEW_TEXT']) || !empty($arResult['DETAIL_TEXT']);
$showBuyBtn = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION']);
$buyButtonClassName = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION_PRIMARY']) ? 'btn-primary' : 'btn-link';
$showAddBtn = in_array('ADD', $arParams['ADD_TO_BASKET_ACTION']);
$showButtonClassName = in_array('ADD', $arParams['ADD_TO_BASKET_ACTION_PRIMARY']) ? 'btn-primary' : 'btn-link';
$showSubscribe = $arParams['PRODUCT_SUBSCRIPTION'] === 'Y' && ($arResult['PRODUCT']['SUBSCRIBE'] === 'Y' || $haveOffers);

$arParams['MESS_BTN_BUY'] = $arParams['MESS_BTN_BUY'] ?: Loc::getMessage('CT_BCE_CATALOG_BUY');
$arParams['MESS_BTN_ADD_TO_BASKET'] = $arParams['MESS_BTN_ADD_TO_BASKET'] ?: Loc::getMessage('CT_BCE_CATALOG_ADD');
$arParams['MESS_NOT_AVAILABLE'] = $arParams['MESS_NOT_AVAILABLE'] ?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE');
$arParams['MESS_BTN_COMPARE'] = $arParams['MESS_BTN_COMPARE'] ?: Loc::getMessage('CT_BCE_CATALOG_COMPARE');
$arParams['MESS_PRICE_RANGES_TITLE'] = $arParams['MESS_PRICE_RANGES_TITLE'] ?: Loc::getMessage('CT_BCE_CATALOG_PRICE_RANGES_TITLE');
$arParams['MESS_DESCRIPTION_TAB'] = $arParams['MESS_DESCRIPTION_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_DESCRIPTION_TAB');
$arParams['MESS_PROPERTIES_TAB'] = $arParams['MESS_PROPERTIES_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_PROPERTIES_TAB');
$arParams['MESS_COMMENTS_TAB'] = $arParams['MESS_COMMENTS_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_COMMENTS_TAB');
$arParams['MESS_SHOW_MAX_QUANTITY'] = $arParams['MESS_SHOW_MAX_QUANTITY'] ?: Loc::getMessage('CT_BCE_CATALOG_SHOW_MAX_QUANTITY');
$arParams['MESS_RELATIVE_QUANTITY_MANY'] = $arParams['MESS_RELATIVE_QUANTITY_MANY'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_MANY');
$arParams['MESS_RELATIVE_QUANTITY_FEW'] = $arParams['MESS_RELATIVE_QUANTITY_FEW'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_FEW');

$initalImageSrc = $actualItem['DETAIL_PICTURE']['SRC'] ? $actualItem['DETAIL_PICTURE']['SRC'] : $arResult['DETAIL_PICTURE']['SRC'];
//endregion
?>

    <!--                           Open Graph-->
    <div style="display:none;">
        <meta property="og:title" content="<?= $name ?>"/>
        <meta property="og:description" content="<?= $arResult['IPROPERTY_VALUES']['ELEMENT_META_DESCRIPTION'] ?>"/>
        <meta property="og:image" content="https://timecube.ru<?= $initalImageSrc ?>"/>
        <meta property="og:type" content="website"/>
        <meta property="og:locale" content="ru_RU"/>
        <meta property="og:site_name" content="timecube.ru"/>
        <meta property="og:url" content="https://<?= SITE_SERVER_NAME . $arResult['DETAIL_PAGE_URL'] ?>"/>
    </div>
    <!--                          end  Open Graph-->

    <div id="<?= $itemIds['ID'] ?>">

        <div class="product-detail">
            <div class="col-12 d-md-none">
                <div class="product-detail__top">
                    <h1 class="pagetitle"><?= $name ?></h1>
                    <div class="product-detail__meta mb-4">
                        <div class="product-detail__metasection">
                            <p><b>Артикул:</b> <?= $arResult['PROPERTIES']['ARTNUMBER']['VALUE'] ?></p>
                            <? if (!empty($arResult['MANUFACTUR'])) { ?>
                                <p><b>Производитель: </b>
                                    <span class="manufacturer-simple">
                                            <a href="<?= $arResult['MANUFACTUR']['DETAIL_PAGE_URL'] ?>">
                                                <span class="manufacturer__name"><?= $arResult['MANUFACTUR']['NAME'] ?></span>
                                            </a>
                                            <? if ($arResult['MANUFACTUR']['FLAG_URL']['VALUE'] != 'china') { ?>
                                                <span class="manufacturer-simple__country">
                                              <svg class="sprite-icon">
                                                <use xlink:href="/assets/img/sprite.svg#<?= $arResult['MANUFACTUR']['FLAG_URL']['VALUE'] ?>"></use>
                                              </svg>
                                            </span>
                                            <? } ?>
                                        </span>
                                </p>
                            <? } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="product-detail__visuals" data-entity="images-slider-block"
                 id="<?= $itemIds['BIG_SLIDER_ID'] ?>">
                <div class="product-gallery">
                    <div class="product-gallery__big">
                        <a class="big-zoom-preview MagicZoom" id="big-preview"
                           href="<?= $initalImageSrc ?>">
                            <img src="<?= $initalImageSrc ?>" alt="<?= $name ?>" title="<?= $name ?>">
                        </a>
                        <? if ($arResult['SHOW_3D']) { ?>
                            <div class="product-gallery__3d">
                                <div class="small-pic-block small-pic-block--lg small-pic-block--green">
                                    <a id="view3d-opener" href="#popup-3d" title="Посмотреть в 3d">
                                        <img loading="lazy" class="lozad" data-src="/assets/img/3d-thumb.png"
                                             alt="Посмотреть в 3d">
                                    </a>
                                </div>
                            </div>
                        <? } ?>
                    </div>
                </div>
                <div class="product-gallery__previews" id="gallery-previews" data-entity="images-container">
                    <?
                    if (!empty($actualItem['MORE_PHOTO'])) {
                        foreach ($actualItem['MORE_PHOTO'] as $key => $photo) {
                            $thumb = $arResult['THUMBS'][$photo['ID']];
                            ?>
                            <div class="product-detail__thumb-item" data-entity="image"
                                 data-id="<?= $photo['ID'] ?>">
                                <div class="pic-ratio pic-ratio-6by5">
                                    <a class="mz-thumb" data-zoom-id="big-preview"
                                       href="<?= $photo['SRC'] ?>" data-image="<?= $photo['SRC'] ?>">
                                        <img loading="lazy" <?= ($key == 0 ? '' : 'class="lozad"') ?>
                                             data-src="<?= $thumb['src'] ? $thumb['src'] : $photo['SRC'] ?>"
                                             alt="<?= $name ?>" title="<?= $name ?>"
                                             src="<?= $thumb['src'] ? $thumb['src'] : $photo['SRC'] ?>">
                                    </a>
                                </div>
                            </div>
                            <?
                        }
                    } ?>
                </div>
                <script>
                    var mzOptions = {};
                    mzOptions = {
                        textHoverZoomHint: 'Наведите для детального просмотра',
                        textClickZoomHint: 'Кликните для полноэкранного просмотра',
                        textExpandHint: 'Кликните, чтобы увеличить',
                        textBtnClose: 'Закрыть',
                        textBtnPrev: 'Назад',
                        textBtnNext: 'Вперёд',
                        onZoomReady: function () {
                            window.MZ_READY = true;
                        },
                        onUpdate: function () {
                        },
                        onZoomIn: function () {
                        },
                        onZoomOut: function () {
                        },
                        onExpandOpen: function () {
                        },
                        onExpandClose: function () {
                        }
                    };
                </script>
                <!--Конец превьюх-->
                <!--Конец галереи-->
                <div class="row product-detail__visuals-additional">
                    <div class="col-auto">
                        <div class="wf-popup wf-popup-medium wf-popup-nopadding" id="popup-3d">
                            <? if ($arResult['SHOW_3D']) { ?>
                                <div class="product-detail__3d-wrapper">
                                    <div id="the360block">
                                        <img src="<?= $arResult['PATH_3D'] . $arResult['COUNT_3D'] . '.jpg' ?>"
                                             alt="<?= $name ?>" title="<?= $name ?>"
                                             id="the360image"
                                             data-file="<?= $arResult['PATH_3D'] . $arResult['COUNT_3D'] . '.jpg' ?>">
                                    </div>
                                </div>
                            <? } ?>
                        </div>
                    </div>
                    <? include 'icons.php'; ?>
                </div>
            </div>

            <?
            $showOffersBlock = $haveOffers && !empty($arResult['OFFERS_PROP']);
            $mainBlockProperties = array_intersect_key($arResult['DISPLAY_PROPERTIES'], $arParams['MAIN_BLOCK_PROPERTY_CODE']);
            $showPropsBlock = !empty($mainBlockProperties) || $arResult['SHOW_OFFERS_PROPS'];
            $showBlockWithOffersAndProps = $showOffersBlock || $showPropsBlock;
            ?>

            <div class="product-detail__content">
                <div class="d-none d-md-block">
                    <!--Заголовок и базовая информация (повторяется в вёрстке дважды)-->
                    <div class="product-detail__top">
                        <p class="h1 pagetitle"><?= $name ?></p>
                        <div class="product-detail__meta mb-4">
                            <div class="product-detail__metasection">
                                <p><b>Артикул:</b> <?= $arResult['PROPERTIES']['ARTNUMBER']['VALUE'] ?></p>
                            </div>
                            <div class="product-detail__metasection">
                                <? if (!empty($arResult['MANUFACTUR'])) { ?>
                                    <p><b>Производитель: </b>
                                        <span class="manufacturer-simple">
                                            <a href="<?= $arResult['MANUFACTUR']['DETAIL_PAGE_URL'] ?>">
                                                <span class="manufacturer__name"><?= $arResult['MANUFACTUR']['NAME'] ?></span>
                                            </a>
                                            <? if ($arResult['MANUFACTUR']['FLAG_URL']['VALUE'] != 'china') { ?>
                                                <span class="manufacturer-simple__country">
                                              <svg class="sprite-icon">
                                                <use xlink:href="/assets/img/sprite.svg#<?= $arResult['MANUFACTUR']['FLAG_URL']['VALUE'] ?>"></use>
                                              </svg>
                                            </span>
                                            <? } ?>
                                        </span>
                                    </p>
                                <? } ?>
                            </div>

                        </div>
                    </div>
                    <!--заголовок и базовая информация конец-->
                </div>

                <div class="commerce-block-wrapper py-4 mb-4">
                    <div class="commerce-block" id="commerce-block">
                        <?


                        //region price

                        ?>
                        <div class="commerce-block__row commerce-block__prices">
                            <div class="commerce-block__prices-main">
                                <p class="commerce-block__prices-title"><?= ($showSubscribe & !$actualItem['CAN_BUY']) ? '' : 'Цена' ?></p>
                                <p class="commerce-block__prices-price" id="<?= $itemIds['PRICE_ID'] ?>">
                                    <?= ($showSubscribe & !$actualItem['CAN_BUY']) ? $arParams['MESS_NOT_AVAILABLE'] : $price['PRINT_RATIO_PRICE'] ?></p>
                                <? if ($arParams['SHOW_OLD_PRICE'] === 'Y') { ?>
                                    <? if ($showSubscribe & !$actualItem['CAN_BUY']): ?>
                                    <? else: ?>
                                        <p class="commerce-block__prices-oldprice"
                                           id="<?= $itemIds['OLD_PRICE_ID'] ?>" <?= ($showDiscount ? '' : 'style="display: none;"') ?>
                                        ><?= ($showDiscount ? $price['PRINT_RATIO_BASE_PRICE'] : '') ?></p>
                                        <div class="commerce-block__prices-economy-price"
                                             id="<?= $itemIds['DISCOUNT_PRICE_ID'] ?>"
                                            <?= (false ? '' : 'style="display: none;"') ?>><?
                                            if ($showDiscount) {
                                                echo Loc::getMessage('CT_BCE_CATALOG_ECONOMY_INFO2', array('#ECONOMY#' => $price['PRINT_RATIO_DISCOUNT']));
                                            } ?></div>
                                    <? endif; ?>
                                <? } ?>
                            </div>
                            <?
                            //алгоритм включения Чёрной пятницы/Новогодней распродажи, чтобы перекрашивались цены в другие цвета
                            global $USER;
                            $date = new DateTime();
                            $dateStartSale = new DateTime(START_SALE);
                            $dateEndSale = new DateTime(END_SALE);
                            $checkSale = false;
                            if (($dateStartSale <= $date && $date <= $dateEndSale)) {
                                $checkSale = true;
                            }
                            //                             $checkSale =  $USER->IsAdmin() ? true: false;
                            if ($checkSale == true) {
                                ?>
                                <? if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' && $actualItem['CAN_BUY']) { ?>
                                    <div class="commerce-block__price__label <?= NAME_SALE ?>">
                                        <? if ($haveOffers) { ?>
                                            <label class="product-label product-label--sale" style="display: none;">
                                                <span class="product-label__title"><?= LABEL_SALE_TOP ?></span>
                                                <span class="product-label__digit"
                                                      id="<?= $itemIds['DISCOUNT_PERCENT_ID'] ?>"></span>
                                                <span class="product-label__title"><?= LABEL_SALE_BOTTOM ?></span>
                                            </label>
                                        <? } else {
                                            if ($price['DISCOUNT'] > 0) { ?>
                                                <label class="product-label product-label--sale">
                                                    <span class="product-label__title"><?= LABEL_SALE_TOP ?></span>
                                                    <span class="product-label__digit"
                                                          id="<?= $itemIds['DISCOUNT_PERCENT_ID'] ?>"
                                                          title="<?= -$price['PERCENT'] ?>%"><?= -$price['PERCENT'] ?>%</span>
                                                    <span class="product-label__title"><?= LABEL_SALE_BOTTOM ?></span>
                                                </label>
                                                <?
                                            }
                                        } ?>
                                    </div>
                                <? } ?>
                            <? } else {
                                ?>
                                <? if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' && $actualItem['CAN_BUY']) { ?>
                                    <div class="commerce-block__price__label">
                                        <? if ($haveOffers) { ?>
                                            <label class="product-label product-label--sale" style="display: none;">
                                <span class="product-label__digit"
                                      id="<?= $itemIds['DISCOUNT_PERCENT_ID'] ?>"></span>
                                                <span class="product-label__title">Скидка</span>
                                            </label>
                                        <? } else {
                                            if ($price['DISCOUNT'] > 0) { ?>
                                                <label class="product-label product-label--sale">
                                <span class="product-label__digit"
                                      id="<?= $itemIds['DISCOUNT_PERCENT_ID'] ?>"
                                      title="<?= -$price['PERCENT'] ?>%"><?= -$price['PERCENT'] ?>%</span>
                                                    <span class="product-label__title">Скидка</span>
                                                </label>
                                                <?
                                            }
                                        } ?>
                                    </div>
                                <? }
                            } ?>
                        </div>
                        <?

                        //endregion price

                        //region quantity

                        if ($arParams['USE_PRODUCT_QUANTITY']) {
                            ?>
                            <div class="mb-3" <?= (!$actualItem['CAN_BUY'] ? ' style="display: none;"' : '') ?>
                                 data-entity="quantity-block">
                                <?
                                if (Loc::getMessage('CATALOG_QUANTITY')) {
                                    ?>
                                    <div class="product-item-detail-info-container-title text-center"><?= Loc::getMessage('CATALOG_QUANTITY') ?></div>
                                    <?
                                }
                                ?>

                                <div class="product-item-amount">
                                    <div class="product-item-amount-field-container">
                                                            <span class="product-item-amount-field-btn-minus no-select"
                                                                  id="<?= $itemIds['QUANTITY_DOWN_ID'] ?>"></span>
                                        <input class="product-item-amount-field"
                                               id="<?= $itemIds['QUANTITY_ID'] ?>"
                                               type="number"
                                               value="<?= $price['MIN_QUANTITY'] ?>">
                                        <span class="product-item-amount-field-btn-plus no-select"
                                              id="<?= $itemIds['QUANTITY_UP_ID'] ?>"></span>
                                        <span class="product-item-amount-description-container">
                                                <span id="<?= $itemIds['QUANTITY_MEASURE'] ?>"><?= $actualItem['ITEM_MEASURE']['TITLE'] ?></span>
                                                <span id="<?= $itemIds['PRICE_TOTAL'] ?>"></span>
                                            </span>
                                    </div>
                                </div>
                            </div>
                            <?
                        }

                        //endregion quantity

                        //region buy buttons
                        ?>
                        <div class="commerce-block__row commerce-block__buttons">

                            <div data-entity="main-button-container" id="<?= $itemIds['BASKET_ACTIONS_ID'] ?>"
                                 style="display: <?= ($actualItem['CAN_BUY'] ? 'inline' : 'none') ?>;">
                                <? if ($showAddBtn) { ?>
                                    <button class="btn btn-primary btn-fullwidth"
                                            id="<?= $itemIds['ADD_BASKET_LINK'] ?>"><?= $arParams['MESS_BTN_ADD_TO_BASKET'] ?></button>
                                    <?
                                }
                                if ($showBuyBtn) { ?>
                                    <button class="btn btn-primary btn-fullwidth"
                                            id="<?= $itemIds['BUY_LINK'] ?>"><?= $arParams['MESS_BTN_BUY'] ?></button>
                                <? } ?>
                            </div>
                            <? if ($showSubscribe) {
                                ?>
                                <div class="mb-3"><?
                                $APPLICATION->IncludeComponent(
                                    'bitrix:catalog.product.subscribe',
                                    '',
                                    [
                                        'CUSTOM_SITE_ID' => isset($arParams['CUSTOM_SITE_ID']) ? $arParams['CUSTOM_SITE_ID'] : null,
                                        'PRODUCT_ID' => $actualItem['ID'],
                                        'BUTTON_ID' => $itemIds['SUBSCRIBE_LINK'],
                                        'BUTTON_CLASS' => 'btn btn-primary btn-fullwidth',
                                        'DEFAULT_DISPLAY' => !$actualItem['CAN_BUY'],
                                        'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
                                    ],
                                    $component,
                                    ['HIDE_ICONS' => 'Y']
                                );
                                ?></div><?
                            } ?>
                            <div class="mb-3" id="<?= $itemIds['NOT_AVAILABLE_MESS'] ?>"
                                 style="display: none">
                                <a class="btn btn-primary btn-third product-item-detail-buy-button"
                                   href="javascript:void(0)"
                                   rel="nofollow"><?= $arParams['MESS_NOT_AVAILABLE'] ?></a>
                            </div>
                            <?

                            //endregion buy buttons

                            //region favorite, compare

                            ?>
                            <div class="commerce-block__controls">
                                <div class="row align-items-center justify-content-center">
                                    <div class="col-auto">
                                        <div class="btn-animated-wrapper">
                                            <button class="btn btn-xs btn-third btn-animated btn-favourite"
                                                    onclick="addFavorite(event)"
                                                    data-entity="btn-fav" data-pid="<?= $arResult['ID'] ?>"><span
                                                        class="svg-icon icon-favorite"></span></button>
                                        </div>
                                    </div>
                                    <? if ($arParams['DISPLAY_COMPARE']) { ?>
                                        <div class="col-auto">
                                            <label class="btn-animated-wrapper" for="p-<?= $arResult['ID'] ?>">
                                                <input type="checkbox" data-entity="compare-checkbox"
                                                       id="p-<?= $arResult['ID'] ?>">
                                                <div class="btn btn-xs btn-third btn-animated btn-compare"
                                                     id="<?= $itemIds['COMPARE_LINK'] ?>"
                                                     title="<?= $arParams['MESS_BTN_COMPARE'] ?>">

                                                    <span class="svg-icon icon-compare"></span>
                                                </div>
                                            </label>
                                        </div>
                                    <? } ?>
                                    <? if ($actualItem['CAN_BUY']): ?>
                                        <div class="col-auto">
                                            <? $APPLICATION->IncludeComponent('webfly:buyoneclick', '',
                                                ['PID' => $arResult['ID']], $this->getComponent(), ['HIDE_ICONS' => 'Y']) ?>
                                        </div>
                                    <? endif; ?>
                                </div>
                            </div>
                        </div>
                        <?

                        //endregion favorite, compare

                        ?>
                    </div>
                </div>
                <div class="row product-detail__columns">
                    <div class="col-12">
                        <? if ($showBlockWithOffersAndProps && $showOffersBlock) { ?>
                            <div class="product-detail__datasection">
                                <!--sku properties-->
                                <?

                                // region SKU props

                                ?>
                                <div class="product-scu-wrapper" id="<?= $itemIds['TREE_ID'] ?>">
                                    <?
                                    if ($showBlockWithOffersAndProps && $showOffersBlock) {

                                        foreach ($arResult['SKU_PROPS'] as $skuProperty) {
                                            if (!isset($arResult['OFFERS_PROP'][$skuProperty['CODE']]))
                                                continue;

                                            $propertyId = $skuProperty['ID'];
                                            $skuProps[] = array(
                                                'ID' => $propertyId,
                                                'SHOW_MODE' => $skuProperty['SHOW_MODE'],
                                                'VALUES' => $skuProperty['VALUES'],
                                                'VALUES_COUNT' => $skuProperty['VALUES_COUNT']
                                            );
                                            ?>
                                            <div data-entity="sku-line-block">
                                                <div class="product-item-scu-container-title"><?= htmlspecialcharsEx($skuProperty['NAME']) ?></div>
                                                <div class="product-item-scu-container">
                                                    <div class="product-item-scu-block">
                                                        <div class="product-item-scu-list">
                                                            <ul class="product-item-scu-item-list">
                                                                <?
                                                                foreach ($skuProperty['VALUES'] as &$value) {
                                                                    $value['NAME'] = htmlspecialcharsbx($value['NAME']);

                                                                    if ($skuProperty['SHOW_MODE'] === 'PICT') {
                                                                        ?>
                                                                        <li class="product-item-scu-item-color-container"
                                                                            title="<?= $value['NAME'] ?>"
                                                                            data-treevalue="<?= $propertyId ?>_<?= $value['ID'] ?>"
                                                                            data-onevalue="<?= $value['ID'] ?>">
                                                                            <div class="product-item-scu-item-color-block">
                                                                                <div class="product-item-scu-item-color"
                                                                                     title="<?= $value['NAME'] ?>"
                                                                                     style="background-image: url('<?= $value['PICT']['SRC'] ?>');">
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                        <?
                                                                    } else {
                                                                        ?>
                                                                        <li class="product-item-scu-item-text-container"
                                                                            title="<?= $value['NAME'] ?>"
                                                                            data-treevalue="<?= $propertyId ?>_<?= $value['ID'] ?>"
                                                                            data-onevalue="<?= $value['ID'] ?>">
                                                                            <div class="product-item-scu-item-text-block">
                                                                                <div class="product-item-scu-item-text"><?= $value['NAME'] ?></div>
                                                                            </div>
                                                                        </li>
                                                                        <?
                                                                    }
                                                                }
                                                                ?>
                                                            </ul>
                                                            <div style="clear: both;"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?
                                        }

                                    }
                                    ?>
                                </div>
                                <?

                                // endregion SKU props

                                ?>
                            </div>
                        <? } ?>

                        <? if ($showBlockWithOffersAndProps) { ?>
                            <div class="product-detail__datasection">
                                <? if ($showPropsBlock) {
                                    if (!empty($mainBlockProperties)) { ?>
                                        <ul class="list-unstyled">
                                            <?
                                            foreach ($mainBlockProperties as $property) {
                                                ?>
                                                <li>
                                                    <span class="product-item-detail-properties-name text-muted"><?= $property['NAME'] ?></span>
                                                    <span class="product-item-detail-properties-dots"></span>
                                                    <span class="product-item-detail-properties-value"><?= (is_array($property['DISPLAY_VALUE'])
                                                            ? implode(' / ', $property['DISPLAY_VALUE'])
                                                            : $property['DISPLAY_VALUE']) ?>
                                                </span>
                                                </li>
                                                <?
                                            }
                                            ?>
                                        </ul>
                                        <?
                                    }
                                    if ($arResult['SHOW_OFFERS_PROPS']) {
                                        ?>
                                        <ul class="list-unstyled"
                                            id="<?= $itemIds['DISPLAY_MAIN_PROP_DIV'] ?>"></ul>
                                        <?
                                    }
                                } ?>
                            </div>
                        <? } ?>
                        <? if (!empty ($arResult["PREVIEW_TEXT"])) { ?>
                            <div class="product-detail__datasection">
                                <div class="product-detail__description readmore-wrapper">
                                    <div class="product-detail__description-content readmore"
                                         id="product-description" data-rows="22">
                                        <?= $arResult['PREVIEW_TEXT'] ?>
                                    </div>
                                </div>
                            </div>
                        <? } ?>
                        <div class="product-detail__datasection">
                            <div class="product-detail__meta">
                                <?

                                //region quantityLimit

                                if ($arParams['SHOW_MAX_QUANTITY'] !== 'N') {
                                    if ($haveOffers) {
                                        ?>
                                        <div class="product-detail__metasection">
                                            <div class="availability">
                                            <span class="availability__scale">
                                                <svg class="sprite-icon">
                                                  <use xlink:href="/assets/img/sprite.svg#a-medium"></use>
                                                </svg>
                                            </span>
                                                <div id="<?= $itemIds['QUANTITY_LIMIT'] ?>" style="display: none;">
                                                <span class="availability__caption"
                                                      data-entity="quantity-limit-value"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?
                                    } else {
                                        if (
                                            $measureRatio
                                            && (float)$actualItem['PRODUCT']['QUANTITY'] > 0
                                            && $actualItem['CHECK_QUANTITY']
                                        ) {

                                            $svgParam = 'high';
                                            if ((float)$actualItem['PRODUCT']['QUANTITY'] / $measureRatio < $arParams['RELATIVE_QUANTITY_FACTOR'])
                                                $svgParam = 'medium';
                                            if ((float)$actualItem['PRODUCT']['QUANTITY'] / $measureRatio == 1)
                                                $svgParam = 'low';

                                            ?>
                                            <div class="product-detail__metasection">
                                                <div class="availability">
                                                            <span class="availability__scale">
                                                            <svg class="sprite-icon">
                                                              <use xlink:href="/assets/img/sprite.svg#a-<?= $svgParam ?>"></use>
                                                            </svg>
                                                            </span>
                                                    <div id="<?= $itemIds['QUANTITY_LIMIT'] ?>">
                                                        <span class="availability__caption"
                                                              data-entity="quantity-limit-value">
                                                <?
                                                if ($arParams['SHOW_MAX_QUANTITY'] === 'M') {
                                                    if ((float)$actualItem['PRODUCT']['QUANTITY'] / $measureRatio >= $arParams['RELATIVE_QUANTITY_FACTOR']) {
                                                        echo $arParams['MESS_RELATIVE_QUANTITY_MANY'];
                                                    } else {
                                                        echo $arParams['MESS_RELATIVE_QUANTITY_FEW'];
                                                    }
                                                } else {
                                                    echo $actualItem['PRODUCT']['QUANTITY'] . ' ' . $actualItem['ITEM_MEASURE']['TITLE'];
                                                }
                                                ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <?
                                        }
                                    }
                                }

                                //endregion quantityLimit

                                ?>
                                <? if ($arResult["PROPERTIES"]["XXX_WARRANTY"]["VALUE"]) { ?>
                                    <div class="product-detail__metasection">
                                        <p><b>Гарантия:</b> <span
                                                    class="nobr"><?= $arResult["PROPERTIES"]["XXX_WARRANTY"]["VALUE"] ?></span>
                                        </p>
                                    </div>
                                <? } ?>
                                <?
                                $compare = ($arResult['PROPERTIES']['WIDTH']['VALUE'] && $arResult['PROPERTIES']['DEPTH']['VALUE'] && $arResult['PROPERTIES']['HEIGHT']['VALUE']);
                                $compare = false;
                                if ($compare) {
                                    ?>
                                    <div class="product-detail__metasection">
                                        <p><a class="mfp-inline-link" href="#understand-size">Понять размер</a></p>
                                        <div class="wf-popup mfp-hide" id="understand-size">
                                            <p class="wf-popup__title">Понять размер</p>
                                            <div class="wf-popup__body">
                                                <p>Кликните на предмет, с которым хотите сравнить.</p>
                                                <ul class="list-inline">
                                                    <li><a class="n1" href="#" onclick="compareSize(0); return false;">Мяч</a>
                                                    </li>
                                                    <li><a class="n2" href="#" onclick="compareSize(1); return false;">Coke</a>
                                                    </li>
                                                    <li><a class="n3" href="#" onclick="compareSize(2); return false;">Пачка
                                                            сигарет</a></li>
                                                </ul>

                                                <div class="pic">
                                                    <img id="compare-size-img"
                                                         src="/assets/compare.php?h=<?= $arResult['PROPERTIES']['HEIGHT']['VALUE'] ?>&w=<?= $arResult['PROPERTIES']['WIDTH']['VALUE'] ?>&d=<?= $arResult['PROPERTIES']['DEPTH']['VALUE'] ?>&n=0"/>
                                                </div>
                                                <p class="caption-gray">
                                                    Эта программа создана для того, чтобы посетитель сайта мог понять
                                                    реальный размер товара, путем сравнения его с предметом, размеры
                                                    которого ему хорошо известны: мяч, пачка сигарет, бутылка Coke.<br/>
                                                    Просто кликните на пачку сигарет, на мяч, или на бутылку, и
                                                    программа построит Вам пропорцию между товаром и выбранным
                                                    предметом.<br/>
                                                    Timecube &mdash; все для Вашего удобства!
                                                </p>

                                            </div>
                                        </div>
                                    </div>
                                <? } ?>
                            </div>
                        </div>
                        <hr>
                        <div class="product-detail__datasection">
                            <ul class="list-inline">
                                <li><b>Категории:</b></li>
                                <? foreach ($arResult['NAV_CHAIN'] as $key => $section) { ?>
                                    <li><a href="<?= $section['SECTION_PAGE_URL'] ?>"><?= $section['NAME'] ?></a></li>
                                <? } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <? //region meta, jsParams            ?>
            <div style="display: none" itemscope itemtype="http://schema.org/Product">
                <img itemprop="image" src="https://timecube.ru<?= $initalImageSrc ?>"/>
                <span itemprop="name"><?= $name ?></span>
                <span itemprop="description"><?= $arResult['IPROPERTY_VALUES']['ELEMENT_META_DESCRIPTION'] ? $arResult['IPROPERTY_VALUES']['ELEMENT_META_DESCRIPTION'] : $arResult['PREVIEW_TEXT'] ?></span>
                <span itemprop="category">https://timecube.ru<?= $arResult['CATEGORY_PATH'] ?></span>
                <span itemprop="model"><?= $arResult['PROPERTIES']['CML2_ARTICLE']['VALUE'] ?></span>
                <span itemprop="brand"><?= $arResult["MANUFACTUR"]['NAME'] ?></span>
                <span itemprop="color"><?= $arResult['PROPERTIES']["EXTERIOR_COLOR"]['VALUE'] ?></span>
                <span itemprop="material"><?= $arResult['PROPERTIES']["BODY_MATERIAL"]['VALUE'] ?: $arResult['PROPERTIES']["BRC_MATERIAL"]['VALUE'] ?: $arResult['PROPERTIES']["COVER_MATERIAL"]['VALUE'] ?></span>

                <div itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">
                    <meta itemprop="bestRating" content="5">
                    <meta itemprop="worstRating" content="1">
                    <meta itemprop="ratingValue" content="5">
                    <meta itemprop="ratingCount" content="5">
                </div>
                <? if ($haveOffers) {
                    foreach ($arResult['JS_OFFERS'] as $offer) {
                        $currentOffersList = array();
                        if (!empty($offer['TREE']) && is_array($offer['TREE'])) {
                            foreach ($offer['TREE'] as $propName => $skuId) {
                                $propId = (int)substr($propName, 5);

                                foreach ($skuProps as $prop) {
                                    if ($prop['ID'] == $propId) {
                                        foreach ($prop['VALUES'] as $propId => $propValue) {
                                            if ($propId == $skuId) {
                                                $currentOffersList[] = $propValue['NAME'];
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $offerPrice = $offer['ITEM_PRICES'][$offer['ITEM_PRICE_SELECTED']];
                        ?>
                        <span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                                <span itemprop="sku"><?= htmlspecialcharsbx(implode('/', $currentOffersList)) ?></span>
                                <span itemprop="price"><?= $offerPrice['RATIO_PRICE'] ?></span>
                                <span itemprop="priceCurrency"><?= $offerPrice['CURRENCY'] ?></span>
                                <link itemprop="availability"
                                      href="http://schema.org/<?= ($offer['CAN_BUY'] ? 'InStock' : 'OutOfStock') ?>"/>
		            </span>
                        <?
                    }
                    unset($offerPrice, $currentOffersList);
                } else { ?>
                    <span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                            <span itemprop="price"><?= $price['RATIO_PRICE'] ?></span>
                            <span itemprop="priceCurrency"><?= $price['CURRENCY'] ?></span>
                            <link itemprop="availability"
                                  href="http://schema.org/<?= ($actualItem['CAN_BUY'] ? 'InStock' : 'OutOfStock') ?>"/>
		        </span>
                <? } ?>
            </div>


            <?
            if ($haveOffers) {
                $offerIds = array();
                $offerCodes = array();

                $useRatio = $arParams['USE_RATIO_IN_RANGES'] === 'Y';

                foreach ($arResult['JS_OFFERS'] as $ind => &$jsOffer) {
                    $offerIds[] = (int)$jsOffer['ID'];
                    $offerCodes[] = $jsOffer['CODE'];

                    $fullOffer = $arResult['OFFERS'][$ind];
                    $measureName = $fullOffer['ITEM_MEASURE']['TITLE'];

                    $strAllProps = '';
                    $strMainProps = '';
                    $strPriceRangesRatio = '';
                    $strPriceRanges = '';

                    if ($arResult['SHOW_OFFERS_PROPS']) {
                        if (!empty($jsOffer['DISPLAY_PROPERTIES'])) {
                            foreach ($jsOffer['DISPLAY_PROPERTIES'] as $property) {
                                $current = '<li class="product-item-detail-properties-item">
						<span class="product-item-detail-properties-name">' . $property['NAME'] . '</span>
						<span class="product-item-detail-properties-dots"></span>
						<span class="product-item-detail-properties-value">' . (
                                    is_array($property['VALUE'])
                                        ? implode(' / ', $property['VALUE'])
                                        : $property['VALUE']
                                    ) . '</span></li>';
                                $strAllProps .= $current;

                                if (isset($arParams['MAIN_BLOCK_OFFERS_PROPERTY_CODE'][$property['CODE']])) {
                                    $strMainProps .= $current;
                                }
                            }

                            unset($current);
                        }
                    }

                    if ($arParams['USE_PRICE_COUNT'] && count($jsOffer['ITEM_QUANTITY_RANGES']) > 1) {
                        $strPriceRangesRatio = '(' . Loc::getMessage(
                                'CT_BCE_CATALOG_RATIO_PRICE',
                                array('#RATIO#' => ($useRatio
                                        ? $fullOffer['ITEM_MEASURE_RATIOS'][$fullOffer['ITEM_MEASURE_RATIO_SELECTED']]['RATIO']
                                        : '1'
                                    ) . ' ' . $measureName)
                            ) . ')';

                        foreach ($jsOffer['ITEM_QUANTITY_RANGES'] as $range) {
                            if ($range['HASH'] !== 'ZERO-INF') {
                                $itemPrice = false;

                                foreach ($jsOffer['ITEM_PRICES'] as $itemPrice) {
                                    if ($itemPrice['QUANTITY_HASH'] === $range['HASH']) {
                                        break;
                                    }
                                }

                                if ($itemPrice) {
                                    $strPriceRanges .= '<dt>' . Loc::getMessage(
                                            'CT_BCE_CATALOG_RANGE_FROM',
                                            array('#FROM#' => $range['SORT_FROM'] . ' ' . $measureName)
                                        ) . ' ';

                                    if (is_infinite($range['SORT_TO'])) {
                                        $strPriceRanges .= Loc::getMessage('CT_BCE_CATALOG_RANGE_MORE');
                                    } else {
                                        $strPriceRanges .= Loc::getMessage(
                                            'CT_BCE_CATALOG_RANGE_TO',
                                            array('#TO#' => $range['SORT_TO'] . ' ' . $measureName)
                                        );
                                    }

                                    $strPriceRanges .= '</dt><dd>' . ($useRatio ? $itemPrice['PRINT_RATIO_PRICE'] : $itemPrice['PRINT_PRICE']) . '</dd>';
                                }
                            }
                        }

                        unset($range, $itemPrice);
                    }

                    $jsOffer['DISPLAY_PROPERTIES'] = $strAllProps;
                    $jsOffer['DISPLAY_PROPERTIES_MAIN_BLOCK'] = $strMainProps;
                    $jsOffer['PRICE_RANGES_RATIO_HTML'] = $strPriceRangesRatio;
                    $jsOffer['PRICE_RANGES_HTML'] = $strPriceRanges;
                }

                $templateData['OFFER_IDS'] = $offerIds;
                $templateData['OFFER_CODES'] = $offerCodes;

                unset($jsOffer, $strAllProps, $strMainProps, $strPriceRanges, $strPriceRangesRatio, $useRatio);

                $jsParams = array(
                    'CONFIG' => array(
                        'USE_CATALOG' => $arResult['CATALOG'],
                        'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
                        'SHOW_PRICE' => true,
                        'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'] === 'Y',
                        'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'] === 'Y',
                        'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
                        'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
                        'SHOW_SKU_PROPS' => $arResult['SHOW_OFFERS_PROPS'],
                        'OFFER_GROUP' => $arResult['OFFER_GROUP'],
                        'MAIN_PICTURE_MODE' => $arParams['DETAIL_PICTURE_MODE'],
                        'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
                        'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'] === 'Y',
                        'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
                        'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'],
                        'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
                        'USE_STICKERS' => true,
                        'USE_SUBSCRIBE' => $showSubscribe,
                        'SHOW_SLIDER' => $arParams['SHOW_SLIDER'],
                        'SLIDER_INTERVAL' => $arParams['SLIDER_INTERVAL'],
                        'ALT' => $alt,
                        'TITLE' => $title,
                        'MAGNIFIER_ZOOM_PERCENT' => 200,
                        'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
                        'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
                        'BRAND_PROPERTY' => !empty($arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']])
                            ? $arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]['DISPLAY_VALUE']
                            : null
                    ),
                    'PRODUCT_TYPE' => $arResult['PRODUCT']['TYPE'],
                    'VISUAL' => $itemIds,
                    'DEFAULT_PICTURE' => array(
                        'PREVIEW_PICTURE' => $arResult['DEFAULT_PICTURE'],
                        'DETAIL_PICTURE' => $arResult['DEFAULT_PICTURE']
                    ),
                    'PRODUCT' => array(
                        'ID' => $arResult['ID'],
                        'ACTIVE' => $arResult['ACTIVE'],
                        'NAME' => $arResult['~NAME'],   //дописываем в название артикул
                        'CATEGORY' => $arResult['PROPERTIES']['XXX_GOOGLE_PRODUCT_CATEGORY']["VALUE"]
                    ),
                    'BASKET' => array(
                        'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
                        'BASKET_URL' => $arParams['BASKET_URL'],
                        'SKU_PROPS' => $arResult['OFFERS_PROP_CODES'],
                        'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
                        'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE']
                    ),
                    'OFFERS' => $arResult['JS_OFFERS'],
                    'OFFER_SELECTED' => $arResult['OFFERS_SELECTED'],
                    'TREE_PROPS' => $skuProps
                );
            } else {
                $emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
                if ($arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y' && !$emptyProductProperties) {
                    ?>
                    <div id="<?= $itemIds['BASKET_PROP_DIV'] ?>" style="display: none;">
                        <?
                        if (!empty($arResult['PRODUCT_PROPERTIES_FILL'])) {
                            foreach ($arResult['PRODUCT_PROPERTIES_FILL'] as $propId => $propInfo) {
                                ?>
                                <input type="hidden"
                                       name="<?= $arParams['PRODUCT_PROPS_VARIABLE'] ?>[<?= $propId ?>]"
                                       value="<?= htmlspecialcharsbx($propInfo['ID']) ?>">
                                <?
                                unset($arResult['PRODUCT_PROPERTIES'][$propId]);
                            }
                        }

                        $emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
                        if (!$emptyProductProperties) {
                            ?>
                            <table>
                                <?
                                foreach ($arResult['PRODUCT_PROPERTIES'] as $propId => $propInfo) {
                                    ?>
                                    <tr>
                                        <td><?= $arResult['PROPERTIES'][$propId]['NAME'] ?></td>
                                        <td>
                                            <?
                                            if (
                                                $arResult['PROPERTIES'][$propId]['PROPERTY_TYPE'] === 'L'
                                                && $arResult['PROPERTIES'][$propId]['LIST_TYPE'] === 'C'
                                            ) {
                                                foreach ($propInfo['VALUES'] as $valueId => $value) {
                                                    ?>
                                                    <label>
                                                        <input type="radio"
                                                               name="<?= $arParams['PRODUCT_PROPS_VARIABLE'] ?>[<?= $propId ?>]"
                                                               value="<?= $valueId ?>" <?= ($valueId == $propInfo['SELECTED'] ? '"checked"' : '') ?>>
                                                        <?= $value ?>
                                                    </label>
                                                    <br>
                                                    <?
                                                }
                                            } else {
                                                ?>
                                                <select name="<?= $arParams['PRODUCT_PROPS_VARIABLE'] ?>[<?= $propId ?>]">
                                                    <?
                                                    foreach ($propInfo['VALUES'] as $valueId => $value) {
                                                        ?>
                                                        <option value="<?= $valueId ?>" <?= ($valueId == $propInfo['SELECTED'] ? '"selected"' : '') ?>>
                                                            <?= $value ?>
                                                        </option>
                                                        <?
                                                    }
                                                    ?>
                                                </select>
                                                <?
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?
                                }
                                ?>
                            </table>
                            <?
                        }
                        ?>
                    </div>
                    <?
                }

                $jsParams = array(
                    'CONFIG' => array(
                        'USE_CATALOG' => $arResult['CATALOG'],
                        'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
                        'SHOW_PRICE' => !empty($arResult['ITEM_PRICES']),
                        'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'] === 'Y',
                        'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'] === 'Y',
                        'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
                        'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
                        'MAIN_PICTURE_MODE' => $arParams['DETAIL_PICTURE_MODE'],
                        'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
                        'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'] === 'Y',
                        'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
                        'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'],
                        'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
                        'USE_STICKERS' => true,
                        'USE_SUBSCRIBE' => $showSubscribe,
                        'SHOW_SLIDER' => $arParams['SHOW_SLIDER'],
                        'SLIDER_INTERVAL' => $arParams['SLIDER_INTERVAL'],
                        'ALT' => $alt,
                        'TITLE' => $title,
                        'MAGNIFIER_ZOOM_PERCENT' => 200,
                        'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
                        'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
                        'BRAND_PROPERTY' => !empty($arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']])
                            ? $arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]['DISPLAY_VALUE']
                            : null
                    ),
                    'VISUAL' => $itemIds,
                    'PRODUCT_TYPE' => $arResult['PRODUCT']['TYPE'],
                    'PRODUCT' => array(
                        'ID' => $arResult['ID'],
                        'ACTIVE' => $arResult['ACTIVE'],
                        'PICT' => reset($arResult['MORE_PHOTO']),
                        'NAME' => $name,//$arResult['~NAME'],//дописываем в название артикул
                        'SUBSCRIPTION' => true,
                        'ITEM_PRICE_MODE' => $arResult['ITEM_PRICE_MODE'],
                        'ITEM_PRICES' => $arResult['ITEM_PRICES'],
                        'ITEM_PRICE_SELECTED' => $arResult['ITEM_PRICE_SELECTED'],
                        'ITEM_QUANTITY_RANGES' => $arResult['ITEM_QUANTITY_RANGES'],
                        'ITEM_QUANTITY_RANGE_SELECTED' => $arResult['ITEM_QUANTITY_RANGE_SELECTED'],
                        'ITEM_MEASURE_RATIOS' => $arResult['ITEM_MEASURE_RATIOS'],
                        'ITEM_MEASURE_RATIO_SELECTED' => $arResult['ITEM_MEASURE_RATIO_SELECTED'],
                        'SLIDER_COUNT' => $arResult['MORE_PHOTO_COUNT'],
                        'SLIDER' => $arResult['MORE_PHOTO'],
                        'CAN_BUY' => $arResult['CAN_BUY'],
                        'CHECK_QUANTITY' => $arResult['CHECK_QUANTITY'],
                        'QUANTITY_FLOAT' => is_float($arResult['ITEM_MEASURE_RATIOS'][$arResult['ITEM_MEASURE_RATIO_SELECTED']]['RATIO']),
                        'MAX_QUANTITY' => $arResult['PRODUCT']['QUANTITY'],
                        'STEP_QUANTITY' => $arResult['ITEM_MEASURE_RATIOS'][$arResult['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'],
                        'CATEGORY' => $arResult['PROPERTIES']['XXX_GOOGLE_PRODUCT_CATEGORY']["VALUE"]
                    ),
                    'BASKET' => array(
                        'ADD_PROPS' => $arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y',
                        'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
                        'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE'],
                        'EMPTY_PROPS' => $emptyProductProperties,
                        'BASKET_URL' => $arParams['BASKET_URL'],
                        'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
                        'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE']
                    )
                );

                unset($emptyProductProperties);
            }

            if ($arParams['DISPLAY_COMPARE']) {
                $jsParams['COMPARE'] = array(
                    'COMPARE_URL_TEMPLATE' => $arResult['~COMPARE_URL_TEMPLATE'],
                    'COMPARE_DELETE_URL_TEMPLATE' => $arResult['~COMPARE_DELETE_URL_TEMPLATE'],
                    'COMPARE_PATH' => $arParams['COMPARE_PATH']
                );
            }

            //endregion meta, jsParams

            ?>
        </div>

        <? $this->SetViewTarget("detail_aside"); ?>

        <?

        // region labels

        ?>
        <div class="product-detail__datasection" id="<?= $itemIds['STICKER_ID'] ?>">
            <? include 'labels.php' ?>
        </div>
        <?

        // endregion labels

        ?>
        <div class="three-columns__sticky">
            <!--customers info accordion begin-->
            <? $APPLICATION->IncludeComponent("bitrix:main.include", "",
                ["AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/detail_customer_info.php"],
                $component, ['HIDE_ICONS' => 'N']
            ); ?>
            <!--customers info accordion end-->
            <div class="mt-4"></div>
        </div>
        <? $this->EndViewTarget(); ?>
    </div>

<? if (!empty($arResult['PROPERTIES']["VIDEO"]['VALUE'])) {
    $videoUrl = $arResult['PROPERTIES']["VIDEO"]['VALUE'];
    $videoUrl = str_replace(["https://youtu.be/", "https://www.youtube.com/watch?v="], "https://www.youtube.com/embed/", $videoUrl);
    $date = new DateTime($arResult["TIMESTAMP_X"]);
    $newDate = $date->format('c');
    ?>

    <? if ($videoUrl) { ?>
        <div itemscope  itemtype="https://schema.org/VideoObject" style="display: none">
            <a itemprop="url" href="<?= $videoUrl ?>"></a>
            <span itemprop="name"><?= $name ?></span>
            <span itemprop="description"><?= strip_tags($arResult['DETAIL_TEXT']) ?></span>
            <span itemprop="duration">PT3M58S</span>
            <meta itemprop="isFamilyFriendly" content="true">
            <span itemprop="uploadDate"><?= $newDate ?></span>
            <span itemprop="thumbnail" itemscope itemtype="http://schema.org/ImageObject">
                <img itemprop="contentUrl" src="https://timecube.ru<?= $initalImageSrc ?>">
            </span>
        </div>
    <? } ?>
    <section class="py-4">
        <div class="heading" data-entity="header">
            <div class="heading__item">
                <h3 class="heading__title">Видео</h3>
            </div>
            <div class="heading__item"><a class="heading__link"></a></div>
        </div>
        <div class="row">
            <div class="col-12 col-md-9 col-xl-8">
                <div class="embed-responsive embed-responsive-16by9 embed-frame" id='video-youtube'
                     data-video="<?= $videoUrl ?>">
                    <iframe class="embed-responsive-item"
                            frameborder="0"
                            allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </section>
<? } ?>


    <section class="py-4">
        <div class="heading" data-entity="header">
            <div class="heading__item">
                <h3 class="heading__title">Характеристики</h3>
            </div>
            <div class="heading__item"><a class="heading__link"></a></div>
        </div>
        <div class="row justify-content-between">
            <div class="col-12 col-md-9 col-xl-8">
                <div class="props-table-wrapper" data-entity="tab-container" data-value="properties">
                    <? if (!empty($arResult['DISPLAY_PROPERTIES']) || $arResult['SHOW_OFFERS_PROPS']) { ?>
                        <div class="props-table">
                            <? if (!empty($arResult['DISPLAY_PROPERTIES'])) {
                                foreach ($arResult['DISPLAY_PROPERTIES'] as $key => $property) {
                                    if ($property != $arResult['DISPLAY_PROPERTIES']["XXX_GOOGLE_PRODUCT_CATEGORY"]) {
                                        ?>
                                        <div class="props-table__row">
                                        <div class="props-table__propname props-table__col">
                                            <span><?= $property['NAME'] ?></span></div>
                                        <div class="props-table__propvalue props-table__col"><span><?= (
                                                is_array($property['DISPLAY_VALUE'])
                                                    ? implode(' / ', $property['DISPLAY_VALUE'])
                                                    : $property['DISPLAY_VALUE']
                                                ) ?></span></div>
                                        </div><?
                                    }
                                }
                                unset($property);
                            }

                            if ($arResult['SHOW_OFFERS_PROPS']) { ?>
                                <ul class="list-unstyled" id="<?= $itemIds['DISPLAY_PROP_DIV'] ?>"></ul>
                            <? } ?>
                        </div>
                    <? } ?>
                </div>
            </div>

            <aside class="col-12 col-md-3">
                <div class="product-detail__eac">
                    <svg class="sprite-icon">
                        <use xlink:href="/assets/img/sprite.svg#eac"></use>
                    </svg>
                    <p class="caption-gray">Товар сертифицирован в&nbsp;соответствии с&nbsp;Техническим Регламентом
                        Таможенного Союза (EAC)</p>
                </div>
            </aside>

        </div>
    </section>
<? if (!empty ($arResult["DETAIL_TEXT"])) { ?>
    <section class="py-4">
        <div class="heading" data-entity="header">
            <div class="heading__item">
                <h3 class="heading__title">Описание</h3>
            </div>
            <div class="heading__item"><a class="heading__link"></a></div>
        </div>
        <div class="row justify-content-between">
            <div class="col-12 col-md-9 col-xl-8">
                <div class="text-content ml-0 detail-text">
                    <?= $arResult['DETAIL_TEXT'] ?>
                </div>
            </div>
        </div>
    </section>
<? } ?>
<? if (!empty ($arResult["MANUFACTUR"])) { ?>

    <section class="py-4">
        <div class="heading">
            <div class="heading__item">
                <h3 class="heading__title">О производителе <strong><?= $arResult["MANUFACTUR"]['NAME'] ?></strong></h3>
            </div>
            <div class="heading__item"><a class="heading__link"></a></div>
        </div>
        <div class="row justify-content-between">
            <div class="col-12 col-md-9 col-xl-8">
                <div class="text-content readmore-wrapper ml-0">
                    <div class="readmore">
                        <p><?= nl2br($arResult["MANUFACTUR"]['DETAIL_TEXT']) ?></p>
                    </div>
                </div>
            </div>
            <aside class="col-12 col-md-3">
                <div class="product-detail__brand">
                    <img class="lozad" loading="lazy" data-src="<?= $arResult["MANUFACTUR"]['IMAGE_SRC'] ?>"
                         alt="<?= $arResult["MANUFACTUR"]['NAME'] ?>" title="<?= $arResult["MANUFACTUR"]['NAME'] ?>"
                         src="<?= $arResult["MANUFACTUR"]['IMAGE_SRC'] ?>">
                </div>
            </aside>
        </div>
    </section>

<? } ?>
<? if (!empty($arResult['PROPERTIES']["BEHAVIOURS_FULL"]['VALUE']['TEXT'])) { ?>
    <section class="py-4">
        <div class="heading" data-entity="header">
            <div class="heading__item">
                <h3 class="heading__title">Инструкция</h3>
            </div>
            <div class="heading__item"><a class="heading__link"></a></div>
        </div>
        <div class="row">
            <div class="col-12 col-md-9 col-xl-8">
                <div class="text-content readmore-wrapper">
                    <div class="readmore">
                        <?
                        echo str_replace('http://', 'https://', $arResult['PROPERTIES']["BEHAVIOURS_FULL"]['~VALUE']['TEXT']);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<? } ?>
<?
$weight = $arResult['PRODUCT']['WEIGHT'] ? $arResult['PRODUCT']['WEIGHT'] / 1000 : 1;
$length = $arResult['PRODUCT']['LENGTH'] ? $arResult['PRODUCT']['LENGTH'] / 10 : 40;
$height = $arResult['PRODUCT']['HEIGHT'] ? $arResult['PRODUCT']['HEIGHT'] / 10 : 20;
$width = $arResult['PRODUCT']['WIDTH'] ? $arResult['PRODUCT']['WIDTH'] / 10 : 30;
?>
    <section class="py-4" id="anchorSdek">
        <div class="heading">
            <div class="heading__item">
                <h3 class="heading__title">Расчёт доставки (предварительный)</h3>
            </div>
        </div>
        <div class="row justify-content-between">
            <div class="col-12 col-md-9 col-xl-8">
                <div>Для расчёта стоимости и сроков доставки <b>введите ваш город в строке поиска.</b> Многие товары из
                    нашего каталога доставляются бесплатно. Ищите предложения со стикерами &quot;Бесплатно по России&quot;
                    и &quot;Бесплатно по Москве и Санкт-Петербургу.&quot;
                </div>
                <div id="showSdek" data-weight=<?= $weight ?> data-length=<?= $length ?>
                     data-height=<?= $height ?> data-width=<?= $width ?>>
                    <div class='detail-info-sdek'>
                        <div class='detail-info-sdek-text'>Стоимость самовывоза из пункта выдачи СДЭК <span
                                    class="cost"><span id='delPrice'>...</span> руб.</span></div>
                        <div class='detail-info-sdek-text'> Стоимость курьерской доставки СДЭК <span class="cost"><span
                                        id='delPriceCour'>...</span> руб.</span></div>
                        <div class='detail-info-sdek-text'>Срок доставки, в днях<span class="cost"><span id='delTime'>...</span></span>
                        </div>
                    </div>
                    <div id="forpvz" style="height:600px;">
                        <div class="heading__title">Загрузка...</div>
                    </div>
                    <div>
                        *Точная стоимость доставки будет рассчитана в момент оформления заказа.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        BX.message({
            ECONOMY_INFO_MESSAGE: '<?=GetMessageJS('CT_BCE_CATALOG_ECONOMY_INFO2')?>',
            TITLE_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_TITLE_ERROR')?>',
            TITLE_BASKET_PROPS: '<?=GetMessageJS('CT_BCE_CATALOG_TITLE_BASKET_PROPS')?>',
            BASKET_UNKNOWN_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_BASKET_UNKNOWN_ERROR')?>',
            BTN_SEND_PROPS: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_SEND_PROPS')?>',
            BTN_MESSAGE_BASKET_REDIRECT: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_BASKET_REDIRECT')?>',
            BTN_MESSAGE_CLOSE: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE')?>',
            BTN_MESSAGE_CLOSE_POPUP: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE_POPUP')?>',
            TITLE_SUCCESSFUL: '<?=GetMessageJS('CT_BCE_CATALOG_ADD_TO_BASKET_OK')?>',
            COMPARE_MESSAGE_OK: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_OK')?>',
            COMPARE_UNKNOWN_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_UNKNOWN_ERROR')?>',
            COMPARE_TITLE: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_TITLE')?>',
            BTN_MESSAGE_COMPARE_REDIRECT: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_COMPARE_REDIRECT')?>',
            PRODUCT_GIFT_LABEL: '<?=GetMessageJS('CT_BCE_CATALOG_PRODUCT_GIFT_LABEL')?>',
            PRICE_TOTAL_PREFIX: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_PRICE_TOTAL_PREFIX')?>',
            RELATIVE_QUANTITY_MANY: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_MANY'])?>',
            RELATIVE_QUANTITY_FEW: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_FEW'])?>',
            SITE_ID: '<?=CUtil::JSEscape($component->getSiteId())?>'
        });

        var <?=$obName?> = new JCCatalogElement(<?=CUtil::PhpToJSObject($jsParams, false, true)?>);
    </script>
<?
unset($actualItem, $itemIds, $jsParams);