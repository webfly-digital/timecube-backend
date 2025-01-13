<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

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

?>

<div class="product-card__top">
    <div class="product-card__columns product-card__columns-centered">
        <div class="product-card__column">
            <span class="product-card__brand"><?=$arResult['MANUFACTUR']['NAME']?></span>
        </div>
    <?if ($arResult['MANUFACTUR']['FLAG_URL']['VALUE'] != 'china') {?>
        <div class="product-card__column">
            <span class="product-card__country"><?=$arResult['MANUFACTUR']['COUNTRY_RU']['VALUE']?>
                <svg class="sprite-icon">
                    <use xlink:href="/assets/img/sprite.svg#<?= $arResult['MANUFACTUR']['FLAG_URL']['VALUE'] ?>"></use>
                </svg>
            </span>
        </div>
    <?}?>
    </div>
</div>
<div class="product-card__pic product-card__row" data-entity="image-wrapper">
    <a href="<?=$item['DETAIL_PAGE_URL']?>">
    <?if (!empty($item['HOVER_PHOTO'])) {?>
        <img class="lozad main" id="<?= $itemIds['PICT'] ?>" alt='<?=$imgTitle?>' title="<?= $imgTitle ?>"  data-src="<?=$item['PREVIEW_PICTURE']['SRC']?>" src="<?=$item['PREVIEW_PICTURE']['SRC']?>">
        <img class="lozad hover" alt='<?=$imgTitle?>' data-src="<?=$item['HOVER_PHOTO']?>" src="<?=$item['HOVER_PHOTO']?>">
    <?} else {?>
        <img id="<?= $itemIds['PICT'] ?>" title="<?= $imgTitle ?>" class="lozad" data-src="<?= $item['PREVIEW_PICTURE']['SRC'] ?>" src="<?= $item['PREVIEW_PICTURE']['SRC'] ?>">
    <?}?>
    </a>
</div>
<div class="product-card__details product-card__row">
    <p class="product-card__title"><a href="<?=$item['DETAIL_PAGE_URL']?>"><?= $productTitle ?></a></p>
</div>

    <?

    // region price
    ?>
<?php if ($showSubscribe & !$actualItem['CAN_BUY']): //1&1=1?>
    <div class="product-card__columns product-card__row" data-entity="price-block">
        <div class="product-card__column">
            <div class="product-card__prices">
                <p class="caption-gray"></p>
                <p class="product-card__price">
                    <?= $arParams['MESS_NOT_AVAILABLE'] ?>
                </p>
                <p class="product-card__oldprice">
                </p>
            </div>
        </div>
        <div class="product-card__column">
        </div>
    </div>
<?php else:?>
    <div class="product-card__columns product-card__row" data-entity="price-block">
        <div class="product-card__column">
            <div class="product-card__prices <?= $price['RATIO_PRICE'] >= $price['RATIO_BASE_PRICE'] ? '' : 'product-card__prices--discount' ?>">
                <p class="caption-gray">Цена:</p>
                <p class="product-card__price" id="<?= $itemIds['PRICE'] ?>">
                    <? if (!empty($price)) {
                        if ($arParams['PRODUCT_DISPLAY_MODE'] === 'N' && $haveOffers) {
                            echo $price['PRINT_RATIO_PRICE'];
//                            echo Loc::getMessage('CT_BCI_TPL_MESS_PRICE_SIMPLE_MODE', [
//                                '#PRICE#' => $price['PRINT_RATIO_PRICE'],
//                                '#VALUE#' => $measureRatio,
//                                '#UNIT#' => $minOffer['ITEM_MEASURE']['TITLE']
//                            ]);
                        } else {
                            echo $price['PRINT_RATIO_PRICE'];
                        }
                    } ?>
                </p>
                <p class="product-card__oldprice" id="<?= $itemIds['PRICE_OLD'] ?>"
                    <?= ($price['RATIO_PRICE'] >= $price['RATIO_BASE_PRICE'] ? 'style="display: none;"' : '') ?>>
                    <?= $price['PRINT_RATIO_BASE_PRICE'] ?>
                </p>
            </div>
        </div>
        <div class="product-card__column">
            <?
            include 'labels.php';
            ?>
        </div>
    </div>
<?php endif;?>
    <?
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
            <?
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
            <?
        }
    }
    //endregion quantity

    //region buttons
    ?>
    <div class="product-card__buttons product-card__row" data-entity="buttons-block">
        <?
        if (!$haveOffers) {
            if ($actualItem['CAN_BUY']) {
                ?>
                <div class="product-card__button-wrapper btn-animated-wrapper wide"
                     id="<?= $itemIds['BASKET_ACTIONS'] ?>">
                    <button class="btn btn-sm btn-third btn-animated btn-cart"
                            title="<?=($arParams['ADD_TO_BASKET_ACTION'] === 'BUY' ? $arParams['MESS_BTN_BUY'] : $arParams['MESS_BTN_ADD_TO_BASKET'])?>"
                            id="<?= $itemIds['BUY_LINK'] ?>" rel="nofollow"><?
                        echo ($arParams['ADD_TO_BASKET_ACTION'] === 'BUY' ? $arParams['MESS_BTN_BUY'] : $arParams['MESS_BTN_ADD_TO_BASKET']);
                        ?></button>
                </div>
                <?
            } else {
                ?>
                <div class="product-item-button-container btn-animated-wrapper">
                    <?
                    if ($showSubscribe) {
                        $APPLICATION->IncludeComponent(
                            'bitrix:catalog.product.subscribe',
                            '',
                            array(
                                'PRODUCT_ID' => $actualItem['ID'],
                                'BUTTON_ID' => $itemIds['SUBSCRIBE_LINK'],
                                'BUTTON_CLASS' =>  'btn btn-primary ' . $buttonSizeClass.'btn btn-sm btn-third btn-animated btn-cart',
                                'DEFAULT_DISPLAY' => true,
                                'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
                            ),
                            $component,
                            array('HIDE_ICONS' => 'Y')
                        );
                    }
                    ?>  <? global $USER;
                if (true) {
                    ?>
                    <button class="btn btn-link btn-sm btn-third"
                            id="<?= $itemIds['NOT_AVAILABLE_MESS'] ?>" href="javascript:void(0)"
                            rel="nofollow">
                        <?= $arParams['MESS_NOT_AVAILABLE'] ?>
                    </button>
                    <?
                } else {?>
                    <button class="btn btn-link btn-sm btn-third"
                            id="<?= $itemIds['NOT_AVAILABLE_MESS'] ?>" href="javascript:void(0)"
                            rel="nofollow">
                        <?= $arParams['MESS_NOT_AVAILABLE'] ?>
                    </button>
                    <?}?>
                </div>
                <?
            }
        } else {
            if ($arParams['PRODUCT_DISPLAY_MODE'] === 'Y') {
                ?>
                <div class="product-card__button-wrapper btn-animated-wrapper wide">
                    <?
                    if ($showSubscribe) {
                        $APPLICATION->IncludeComponent(
                            'bitrix:catalog.product.subscribe',
                            '',
                            array(
                                'PRODUCT_ID' => $item['ID'],
                                'BUTTON_ID' => $itemIds['SUBSCRIBE_LINK'],
                                'BUTTON_CLASS' => 'btn btn-primary ' . $buttonSizeClass,
                                'DEFAULT_DISPLAY' => !$actualItem['CAN_BUY'],
                                'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
                            ),
                            $component,
                            array('HIDE_ICONS' => 'Y')
                        );
                    }
                    ?>
                    <button class="btn btn-sm btn-third btn-animated btn-cart" rel="nofollow"
                            title="<?= $arParams['MESS_NOT_AVAILABLE'] ?>"
                            id="<?= $itemIds['NOT_AVAILABLE_MESS'] ?>" href="javascript:void(0)"
                        <?= ($actualItem['CAN_BUY'] ? 'style="display: none;"' : '') ?>><?
                            echo $arParams['MESS_NOT_AVAILABLE'];
                        ?></button>
                    <div id="<?= $itemIds['BASKET_ACTIONS'] ?>" <?= ($actualItem['CAN_BUY'] ? '' : 'style="display: none;"') ?>>
                        <button class="btn btn-sm btn-third btn-animated btn-cart"
                                title="<?= ($arParams['ADD_TO_BASKET_ACTION'] === 'BUY' ? $arParams['MESS_BTN_BUY'] : $arParams['MESS_BTN_ADD_TO_BASKET']) ?>"
                                rel="nofollow" id="<?= $itemIds['BUY_LINK'] ?>"><?
                            echo ($arParams['ADD_TO_BASKET_ACTION'] === 'BUY' ? $arParams['MESS_BTN_BUY'] : $arParams['MESS_BTN_ADD_TO_BASKET']);
                            ?></button>
                    </div>
                </div>
                <?
            } else {
                ?>
                <div class="product-card__button-wrapper btn-animated-wrapper wide">
                    <a class="btn btn-sm btn-third btn-animated btn-cart" title="<?= $arParams['MESS_BTN_DETAIL'] ?>"
                            href="<?='/product/'.$item['CODE'].'/'?>"><?= $arParams['MESS_BTN_DETAIL'] ?></a>
                </div>
                <?
            }
        }

        if (
            $arParams['DISPLAY_COMPARE']
            && (!$haveOffers || $arParams['PRODUCT_DISPLAY_MODE'] === 'Y')
        ) {
            ?>
            <div class="product-card__button-wrapper btn-animated-wrapper" id="<?= $itemIds['COMPARE_LINK'] ?>">
                    <input type="checkbox" data-entity="compare-checkbox">
                <div  class="btn btn-sm btn-third btn-animated btn-compare"
                        aria-label="В сравнение">
                    <span class="svg-icon icon-compare"></span>
                </div>
            </div>
            <?
        }
        ?>
        <div class="product-card__button-wrapper btn-animated-wrapper">
            <button class="btn btn-sm btn-third btn-animated btn-favourite" title="В избранное"
                    aria-label="В избранное" data-entity="btn-fav" data-pid="<?=$item['ID']?>"
            onclick="addFavorite(event);">
                <span class="svg-icon icon-favorite"></span>
            </button>
        </div>
    </div>
    <?
    //endregion buttons

    //region offers
    if ($arParams['PRODUCT_DISPLAY_MODE'] === 'Y' && $haveOffers && !empty($item['OFFERS_PROP'])) {
        ?>
        <div class="product-card__row product-item-info-container product-item-hidden" id="<?= $itemIds['PROP_DIV'] ?>">
            <?
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
                                    <?
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
                            </div>
                        </div>
                    </div>
                </div>
                <?
            }
            ?>
        </div>
        <?
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

    //region availability

    if ($arParams['SHOW_MAX_QUANTITY'] !== 'N') {

        $svgParam = 'high';
        if ((float)$actualItem['PRODUCT']['QUANTITY'] / $measureRatio < $arParams['RELATIVE_QUANTITY_FACTOR'])
            $svgParam = 'medium';
        if ((float)$actualItem['PRODUCT']['QUANTITY'] / $measureRatio == 1)
            $svgParam = 'low';

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
                              <use xlink:href="/assets/img/sprite.svg#a-<?=$svgParam?>"></use>
                            </svg>
                        </span>
                        <span class="availability__caption" data-entity="quantity-limit-value"></span>
                    </div>
                </div>
</div>
                <?

            }
        }
        else {
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
                              <use xlink:href="/assets/img/sprite.svg#a-<?=$svgParam?>"></use>
                            </svg>
                        </span>
                        <span class="availability__caption" data-entity="quantity-limit-value">
                            <?
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
                <?
            }
        }
    }

    //endregion availability
