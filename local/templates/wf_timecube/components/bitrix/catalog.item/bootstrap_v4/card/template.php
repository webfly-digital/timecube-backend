<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $item
 * @var array $actualItem
 * @var array $minOffer
 * @var array $itemIds
 * @var array $price
 * @var array $measureRatio
 * @var bool $haveOffers
 * @var bool $showSubscribe
 * @var array $morePhoto
 * @var bool $showSlider
 * @var bool $itemHasDetailUrl
 * @var string $imgTitle
 * @var string $productTitle
 * @var string $buttonSizeClass
 * @var CatalogSectionComponent $component
 */
global $arResult;
?>
<?php
if ($item['PROPERTIES']['SHOW_YOUTUBE']["VALUE_XML_ID"] == "74871818d9ae45163be185c1318f2909") {
    ?>
    <div class="product-card__yt-icon"></div>
    <?php
} ?>
    <div class="product-card__top">
        <div class="product-card__columns product-card__columns-centered">
            <div class="product-card__column">
                <span class="product-card__brand"><?= $arResult['MANUFACTUR']['NAME'] ?></span>
            </div>
            <?php if ($arResult['MANUFACTUR']['FLAG_URL']['VALUE'] != 'china') { ?>
                <div class="product-card__column">
            <span class="product-card__country"><?= $arResult['MANUFACTUR']['COUNTRY_RU']['VALUE'] ?>
                <svg class="sprite-icon">
                    <use xlink:href="/assets/img/sprite.svg#<?= $arResult['MANUFACTUR']['FLAG_URL']['VALUE'] ?>"></use>
                </svg>
            </span>
                </div>
                <?php
            } ?>
        </div>
    </div>
    <div class="product-card__pic product-card__row" data-entity="image-wrapper">
        <a href="<?= $item['DETAIL_PAGE_URL'] ?>">
            <?php if (!empty($item['HOVER_PHOTO'])) { ?>
                <img class="lozad main" id="<?= $itemIds['PICT'] ?>" alt='<?= $imgTitle ?>' title="<?= $imgTitle ?>"
                     data-src="<?= $item['PREVIEW_PICTURE']['SRC'] ?>" src="<?= $item['PREVIEW_PICTURE']['SRC'] ?>">
                <img class="lozad hover" alt='<?= $imgTitle ?>'  title="<?= $imgTitle ?> data-src="<?= $item['HOVER_PHOTO'] ?>"
                     src="<?= $item['HOVER_PHOTO'] ?>">
            <?php } else { ?>
                <img id="<?= $itemIds['PICT'] ?>" title="<?= $imgTitle ?>" alt='<?= $imgTitle ?>' class="lozad"
                     data-src="<?= $item['PREVIEW_PICTURE']['SRC'] ?>" src="<?= $item['PREVIEW_PICTURE']['SRC'] ?>">
                <?php
            } ?>
        </a>
    </div>
    <div class="product-card__details product-card__row">
        <p class="product-card__title"><a href="<?= $item['DETAIL_PAGE_URL'] ?>"><?= $productTitle ?></a></p>
    </div>

<?php

// region price
?>
    <div class="product-card__columns product-card__row" data-entity="price-block">
        <div class="product-card__column">
            <div class="product-card__prices <?= ($price['RATIO_PRICE'] >= $price['RATIO_BASE_PRICE']) || !$actualItem['CAN_BUY'] ? '' : 'product-card__prices--discount' ?>">
                <p class="caption-gray"><?= ($showSubscribe & !$actualItem['CAN_BUY']) ? '' : 'Цена' ?></p>
                <p class="product-card__price <?= ($showSubscribe & !$actualItem['CAN_BUY']) ? 'not_available' : '' ?>"
                   id="<?= $itemIds['PRICE'] ?>">
                    <?php if (!empty($price)) {
                        if ($arParams['PRODUCT_DISPLAY_MODE'] === 'N' && $haveOffers) {
                            echo Loc::getMessage('CT_BCI_TPL_MESS_PRICE_SIMPLE_MODE', [
                                '#PRICE#' => $price['PRINT_RATIO_PRICE'],
//                                    '#VALUE#' => $measureRatio,
//                                    '#UNIT#' => $minOffer['ITEM_MEASURE']['TITLE']
                            ]);
//                            echo $price['PRINT_RATIO_PRICE'];
                        } else {
                            echo ($showSubscribe & !$actualItem['CAN_BUY']) ? $arParams['MESS_NOT_AVAILABLE'] : $price['PRINT_RATIO_PRICE'];
                        }
                    } else {
                        echo $arParams['MESS_NOT_AVAILABLE'];
                    } ?>
                </p>
                <?php if ($showSubscribe & !$actualItem['CAN_BUY']): ?>
                <?php else: ?>
                    <p class="product-card__oldprice" id="<?= $itemIds['PRICE_OLD'] ?>"
                        <?= ($price['RATIO_PRICE'] >= $price['RATIO_BASE_PRICE'] ? 'style="display: none;"' : '') ?>>
                        <?= $price['PRINT_RATIO_BASE_PRICE'] ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <?php if (!$actualItem['CAN_BUY']): ?>
        <?php else: ?>
            <div class="product-card__column">
                <?php
                include 'labels.php';
                ?>
            </div>
        <?php endif; ?>
    </div>
<?php
// endregion price

//region quantity
if (!$haveOffers) {
    if ($actualItem['CAN_BUY'] && $arParams['USE_PRODUCT_QUANTITY']) {
        ?>
        <div data-entity="quantity-block">
                <span class="product-item-amount-field-btn-minus no-select"
                      id="<?= $itemIds['QUANTITY_DOWN'] ?>"></span>
            <input class="product-item-amount-field" id="<?= $itemIds['QUANTITY'] ?>"
                   type="number" name="<?= $arParams['PRODUCT_QUANTITY_VARIABLE'] ?>"
                   value="<?= $measureRatio ?>">
            <span class="product-item-amount-field-btn-plus no-select"
                  id="<?= $itemIds['QUANTITY_UP'] ?>"></span>
            <span class="product-item-amount-description-container">
                    <span id="<?= $itemIds['QUANTITY_MEASURE'] ?>">
                        <?= $actualItem['ITEM_MEASURE']['TITLE'] ?>
                    </span>
                    <span id="<?= $itemIds['PRICE_TOTAL'] ?>"></span>
                </span>
        </div>
        <?php
    }
} elseif ($arParams['PRODUCT_DISPLAY_MODE'] === 'Y') {
    if ($arParams['USE_PRODUCT_QUANTITY']) {
        ?>
        <div data-entity="quantity-block">
                <span class="product-item-amount-field-btn-minus no-select"
                      id="<?= $itemIds['QUANTITY_DOWN'] ?>"></span>
            <input class="product-item-amount-field" id="<?= $itemIds['QUANTITY'] ?>"
                   type="number" name="<?= $arParams['PRODUCT_QUANTITY_VARIABLE'] ?>"
                   value="<?= $measureRatio ?>">
            <span class="product-item-amount-field-btn-plus no-select"
                  id="<?= $itemIds['QUANTITY_UP'] ?>"></span>
            <span class="product-item-amount-description-container">
                    <span id="<?= $itemIds['QUANTITY_MEASURE'] ?>"><?= $actualItem['ITEM_MEASURE']['TITLE'] ?></span>
                    <span id="<?= $itemIds['PRICE_TOTAL'] ?>"></span>
                </span>
        </div>
        <?php
    }
}
//endregion quantity
//region offers
if ($arParams['PRODUCT_DISPLAY_MODE'] === 'Y' && $haveOffers && !empty($item['OFFERS_PROP'])) {
    ?>
    <div class="product-card__row product-item-info-container product-item-hidden" id="<?= $itemIds['PROP_DIV'] ?>">
        <?php
        foreach ($arParams['SKU_PROPS'] as $skuProperty) {
            $propertyId = $skuProperty['ID'];
            $skuProperty['NAME'] = htmlspecialcharsbx($skuProperty['NAME']);
            if (!isset($item['SKU_TREE_VALUES'][$propertyId]))
                continue;
            ?>
            <div data-entity="sku-block" class="product-card__scu-block">
                <div class="product-item-scu-container" data-entity="sku-line-block">
                    <div class="product-item-scu-block-title product-card__scu-title"><?= $skuProperty['NAME'] ?>:</div>
                    <div class="product-item-scu-block">
                        <div class="product-item-scu-list">
                            <ul class="product-item-scu-item-list">
                                <?php
                                foreach ($skuProperty['VALUES'] as $value) {
                                    if (!isset($item['SKU_TREE_VALUES'][$propertyId][$value['ID']]))
                                        continue;

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
                                                     style="background-image: url('<?= $value['PICT']['SRC'] ?>');"></div>
                                            </div>
                                        </li>
                                        <?php
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
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
    foreach ($arParams['SKU_PROPS'] as $skuProperty) {
        if (!isset($item['OFFERS_PROP'][$skuProperty['CODE']]))
            continue;

        $skuProps[] = array(
            'ID' => $skuProperty['ID'],
            'SHOW_MODE' => $skuProperty['SHOW_MODE'],
            'VALUES' => $skuProperty['VALUES'],
            'VALUES_COUNT' => $skuProperty['VALUES_COUNT']
        );
    }

    unset($skuProperty, $value);

    if ($item['OFFERS_PROPS_DISPLAY']) {
        foreach ($item['JS_OFFERS'] as $keyOffer => $jsOffer) {
            $strProps = '';

            if (!empty($jsOffer['DISPLAY_PROPERTIES'])) {
                foreach ($jsOffer['DISPLAY_PROPERTIES'] as $displayProperty) {
                    $strProps .= '<dt>' . $displayProperty['NAME'] . '</dt><dd>'
                        . (is_array($displayProperty['VALUE'])
                            ? implode(' / ', $displayProperty['VALUE'])
                            : $displayProperty['VALUE'])
                        . '</dd>';
                }
            }

            $item['JS_OFFERS'][$keyOffer]['DISPLAY_PROPERTIES'] = $strProps;
        }
        unset($jsOffer, $strProps);
    }
}
//endregion offers

//region buttons
?>
    <div class="product-card__buttons product-card__row" data-entity="buttons-block">
        <?php
        if (!$haveOffers) {
            if ($actualItem['CAN_BUY']) {
                ?>
                <div class="product-card__button-wrapper btn-animated-wrapper wide"
                     id="<?= $itemIds['BASKET_ACTIONS'] ?>">
                    <button class="btn btn-sm btn-third btn-animated btn-cart"
                            title="<?= ($arParams['ADD_TO_BASKET_ACTION'] === 'BUY' ? $arParams['MESS_BTN_BUY'] : $arParams['MESS_BTN_ADD_TO_BASKET']) ?>"
                            id="<?= $itemIds['BUY_LINK'] ?>" rel="nofollow"><?php
                        echo($arParams['ADD_TO_BASKET_ACTION'] === 'BUY' ? $arParams['MESS_BTN_BUY'] : $arParams['MESS_BTN_ADD_TO_BASKET']);
                        ?></button>
                </div>
                <?php
            } else {
                ?>
                <div class="product-item-button-container btn-animated-wrapper">
                    <?php
                    if ($showSubscribe) {
                        $APPLICATION->IncludeComponent(
                            'bitrix:catalog.product.subscribe',
                            '',
                            array(
                                'PRODUCT_ID' => $actualItem['ID'],
                                'BUTTON_ID' => $itemIds['SUBSCRIBE_LINK'],
                                'BUTTON_CLASS' => 'btn btn-primary ' . 'btn btn-sm btn-third btn-animated btn-cart',
                                'DEFAULT_DISPLAY' => true,
                                'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
                            ),
                            $component,
                            array('HIDE_ICONS' => 'Y')
                        );
                    }
                    ?>
                    <button class="btn btn-link btn-sm btn-third" style="display: none"
                            id="<?= $itemIds['NOT_AVAILABLE_MESS'] ?>" href="javascript:void(0)"
                            rel="nofollow">
                        <?= $arParams['MESS_NOT_AVAILABLE'] ?>
                    </button>
                </div>
                <?php
            }
        } else {
            if ($arParams['PRODUCT_DISPLAY_MODE'] === 'Y') {
                ?>
                <div class="product-item-button-container btn-animated-wrapper">
                    <?php
                    if ($showSubscribe) {
                        $APPLICATION->IncludeComponent(
                            'bitrix:catalog.product.subscribe',
                            '',
                            array(
                                'PRODUCT_ID' => $item['ID'],
                                'BUTTON_ID' => $itemIds['SUBSCRIBE_LINK'],
                                'BUTTON_CLASS' => 'btn btn-primary ' . 'btn btn-sm btn-third btn-animated btn-cart',
                                'DEFAULT_DISPLAY' => !$actualItem['CAN_BUY'],
                                'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
                            ),
                            $component,
                            array('HIDE_ICONS' => 'Y')
                        );
                    }
                    ?>
                    <?php /*  <button class="btn btn-sm btn-third btn-animated btn-cart" rel="nofollow"
                            title="<?= $arParams['MESS_NOT_AVAILABLE'] ?>"
                            id="<?= $itemIds['NOT_AVAILABLE_MESS'] ?>" href="javascript:void(0)"
                        <?= ($actualItem['CAN_BUY'] ? 'style="display: none;"' : '') ?>><?
                        echo $arParams['MESS_NOT_AVAILABLE'];
                        ?>
                    </button>*/?>
                    <div id="<?= $itemIds['BASKET_ACTIONS'] ?>" <?= ($actualItem['CAN_BUY'] ? '' : 'style="display: none;"') ?>>
                        <button class="btn btn-sm btn-third btn-animated btn-cart"
                                title="<?= ($arParams['ADD_TO_BASKET_ACTION'] === 'BUY' ? $arParams['MESS_BTN_BUY'] : $arParams['MESS_BTN_ADD_TO_BASKET']) ?>"
                                rel="nofollow" id="<?= $itemIds['BUY_LINK'] ?>"><?php
                            echo($arParams['ADD_TO_BASKET_ACTION'] === 'BUY' ? $arParams['MESS_BTN_BUY'] : $arParams['MESS_BTN_ADD_TO_BASKET']);
                            ?></button>
                    </div>
                </div>
                <?php
            } else {
                ?>
                <div class="product-card__button-wrapper btn-animated-wrapper wide">
                    <a class="btn btn-sm btn-third btn-animated btn-cart" title="<?= $arParams['MESS_BTN_DETAIL'] ?>"
                       href="<?= $item['DETAIL_PAGE_URL'] ?>"><?= $arParams['MESS_BTN_DETAIL'] ?></a>
                </div>
                <?php
            }
        }

        if (
            $arParams['DISPLAY_COMPARE']
            && (!$haveOffers || $arParams['PRODUCT_DISPLAY_MODE'] === 'Y')
        ) {
            ?>
            <div class="product-card__button-wrapper btn-animated-wrapper" id="<?= $itemIds['COMPARE_LINK'] ?>">
                <input type="checkbox" data-entity="compare-checkbox">
                <div class="btn btn-sm btn-third btn-animated btn-compare"
                     aria-label="В сравнение">
                    <span class="svg-icon icon-compare"></span>
                </div>
            </div>
            <?php
        }
        ?>
        <div class="product-card__button-wrapper btn-animated-wrapper">
            <button class="btn btn-sm btn-third btn-animated btn-favourite" title="В избранное"
                    aria-label="В избранное" data-entity="btn-fav" data-pid="<?= $item['ID'] ?>"
                    onclick="addFavorite(event);">
                <span class="svg-icon icon-favorite"></span>
            </button>
        </div>
    </div>
<?php
//endregion buttons

//region availability

if ($arParams['SHOW_MAX_QUANTITY'] !== 'N') {

    $svgParam = 'high';

    if ($measureRatio > 0) {
        $quantityRatio = (float)$actualItem['PRODUCT']['QUANTITY'] / $measureRatio;

        if ($quantityRatio < $arParams['RELATIVE_QUANTITY_FACTOR']) {
            $svgParam = 'medium';
        }
        if ($quantityRatio == 1) {
            $svgParam = 'low';
        }
    }

    if ($haveOffers) {
        if ($arParams['PRODUCT_DISPLAY_MODE'] === 'Y') {

            ?>
            <div class="product-card__columns product-card__row product-card__row--bottom">
                <div class="product-card__column" id="<?= $itemIds['QUANTITY_LIMIT'] ?>"
                     style="display: none;"
                     data-entity="quantity-limit-block" title="<?= $arParams['MESS_SHOW_MAX_QUANTITY'] ?>">
                    <div class="availability availability--sm">
                        <span class="availability__scale">
                            <svg class="sprite-icon">
                              <use xlink:href="/assets/img/sprite.svg#a-<?= $svgParam ?>"></use>
                            </svg>
                        </span>
                        <span class="availability__caption" data-entity="quantity-limit-value"></span>
                    </div>
                </div>
            </div>
            <?php

        }
    } else {
        if (
            $measureRatio
            && (float)$actualItem['CATALOG_QUANTITY'] > 0
            && $actualItem['CATALOG_QUANTITY_TRACE'] === 'Y'
            && $actualItem['CATALOG_CAN_BUY_ZERO'] === 'N'
        ) {
            ?>
            <div class="product-card__columns product-card__row product-card__row--bottom">
                <div class="product-card__column"
                     id="<?= $itemIds['QUANTITY_LIMIT'] ?>" title="<?= $arParams['MESS_SHOW_MAX_QUANTITY'] ?>">
                    <div class="availability availability--sm">
                        <span class="availability__scale">
                            <svg class="sprite-icon">
                              <use xlink:href="/assets/img/sprite.svg#a-<?= $svgParam ?>"></use>
                            </svg>
                        </span>
                        <span class="availability__caption" data-entity="quantity-limit-value">
                            <?php
                            if ($arParams['SHOW_MAX_QUANTITY'] === 'M') {
                                if ((float)$actualItem['CATALOG_QUANTITY'] / $measureRatio >= $arParams['RELATIVE_QUANTITY_FACTOR']) {
                                    echo $arParams['MESS_RELATIVE_QUANTITY_MANY'];
                                } else {
                                    echo $arParams['MESS_RELATIVE_QUANTITY_FEW'];
                                }
                            } else {
                                echo $actualItem['CATALOG_QUANTITY'] . ' ' . $actualItem['ITEM_MEASURE']['TITLE'];
                            }
                            ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php
        }
    }
}

//endregion availability