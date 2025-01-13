<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

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
<?   if ($item['PROPERTIES']['SHOW_YOUTUBE']["VALUE_XML_ID"] == "74871818d9ae45163be185c1318f2909") {
        ?>
        <div class="product-card__yt-icon"></div>
        <?}?>
<div class="product-card__pic product-card__row" data-entity="image-wrapper5">
    <a href="<?= $item['DETAIL_PAGE_URL'] ?>">
        <? if (!empty($item['HOVER_PHOTO'])) { ?>
            <img id="<?= $itemIds['PICT'] ?>" title="<?= $imgTitle ?>" class="lozad main" loading="lazy"
                 data-src="<?= $item['PREVIEW_PICTURE']['SRC'] ?>" src="<?= $item['PREVIEW_PICTURE']['SRC'] ?>"
                 alt="<?= $imgTitle ?>"/>
            <img class="lozad hover" data-src="<?= $item['HOVER_PHOTO'] ?>" src="<?= $item['HOVER_PHOTO'] ?>" loading="lazy"
                 alt="<?= $imgTitle ?>"/>
        <? } else { ?>
            <img id="<?= $itemIds['PICT'] ?>" title="<?= $imgTitle ?>" class="lozad" loading="lazy"
                 data-src="<?= $item['PREVIEW_PICTURE']['SRC'] ?> " src="<?= $item['PREVIEW_PICTURE']['SRC'] ?>"
                 alt="<?= $imgTitle ?>">
        <? } ?>
    </a>
</div>
<div class="product-card__details product-card__row">
    <p class="product-card__title">
        <a href="<?= $item['DETAIL_PAGE_URL'] ?>"><?= $productTitle ?></a>
    </p>
</div>

<?
if (!empty($arParams['PRODUCT_BLOCKS_ORDER'])) {
    foreach ($arParams['PRODUCT_BLOCKS_ORDER'] as $blockName) {
        switch ($blockName) {
            case 'price':
                ?>
                <div class="product-card__row product-card__row--centered" data-entity="price-block">
                    <div class="product-card__prices <?
                    echo(($price['RATIO_PRICE'] >= $price['RATIO_BASE_PRICE'] || !$actualItem['CAN_BUY']) ? '' : 'product-card__prices--discount')
                    ?> product-card__prices--single-line">
                        <p class="product-card__price" id="<?= $itemIds['PRICE'] ?>">
                            <? if (!empty($price)) {
                                if ($arParams['PRODUCT_DISPLAY_MODE'] === 'N' && $haveOffers) {
                                    echo Loc::getMessage('CT_BCI_TPL_MESS_PRICE_SIMPLE_MODE', [
                                        '#PRICE#' => $price['PRINT_RATIO_PRICE'],
//                                    '#VALUE#' => $measureRatio,
//                                    '#UNIT#' => $minOffer['ITEM_MEASURE']['TITLE']
                                    ]);

                                } else {
                                    echo ($showSubscribe & !$actualItem['CAN_BUY']) ? $arParams['MESS_NOT_AVAILABLE'] : $price['PRINT_RATIO_PRICE'];
                                }
                            } ?>
                        </p>
                        <? if ($showSubscribe & !$actualItem['CAN_BUY']):?>
                        <? else: ?>
                            <p class="product-card__oldprice" id="<?= $itemIds['PRICE_OLD'] ?>"
                                <?= ($price['RATIO_PRICE'] >= $price['RATIO_BASE_PRICE'] ? 'style="display: none;"' : '') ?>>
                                <?= $price['PRINT_RATIO_BASE_PRICE'] ?>
                            </p>
                        <? endif; ?>
                    </div>
                </div>
                <? break;

            case 'buttons':
                ?>
                <div class="product-card__buttons product-card__row product-card__row--centered"
                     data-entity="buttons-block">
                    <?
                    if (!$haveOffers) {
                        if ($actualItem['CAN_BUY']) {
                            ?>
                            <div class="product-card__button-wrapper btn-animated-wrapper"
                                 id="<?= $itemIds['BASKET_ACTIONS'] ?>">
                                <button class="btn btn-sm btn-third btn-animated btn-cart bnt-cart-simple"
                                        title="<?= ($arParams['ADD_TO_BASKET_ACTION'] === 'BUY' ? $arParams['MESS_BTN_BUY'] : $arParams['MESS_BTN_ADD_TO_BASKET']) ?>"
                                        id="<?= $itemIds['BUY_LINK'] ?>" rel="nofollow"><?
                                    //echo ($arParams['ADD_TO_BASKET_ACTION'] === 'BUY' ? $arParams['MESS_BTN_BUY'] : $arParams['MESS_BTN_ADD_TO_BASKET']);
                                    ?><span class="svg-icon icon-cart"></span></button>
                            </div>
                            <?
                        } else {
                            ?>

                            <div class="product-card__button-wrapper btn-animated-wrapper item-icon">
                                <?
                                if ($showSubscribe) {
                                    $APPLICATION->IncludeComponent(
                                        'bitrix:catalog.product.subscribe',
                                        '',
                                        array(
                                            'PRODUCT_ID' => $actualItem['ID'],
                                            'BUTTON_ID' => $itemIds['SUBSCRIBE_LINK'],
                                            'BUTTON_CLASS' => 'btn btn-primary btn btn-sm btn-third btn-animated btn-cart bnt-cart-simple icon',
                                            'DEFAULT_DISPLAY' => true,
                                            'MESS_BTN_SUBSCRIBE' => $arParams['~MESS_BTN_SUBSCRIBE'],
                                        ),
                                        $component,
                                        array('HIDE_ICONS' => 'Y')
                                    );
                                }
                                ?>
                                <button class="btn btn-link btn-third<?= $buttonSizeClass ?>"
                                        style="display: none"
                                        id="<?= $itemIds['NOT_AVAILABLE_MESS'] ?>" href="javascript:void(0)"
                                        rel="nofollow"><?= $arParams['MESS_NOT_AVAILABLE'] ?>
                                </button>
                            </div>

                            <?
                        }
                    } else {
                        if ($arParams['PRODUCT_DISPLAY_MODE'] === 'Y') {
                            ?>
                            <div class="product-card__button-wrapper btn-animated-wrapper">
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
                                <button class="btn btn-sm btn-third btn-animated btn-cart bnt-cart-simple"
                                        rel="nofollow"
                                        title="<?= $arParams['MESS_NOT_AVAILABLE'] ?>"
                                        id="<?= $itemIds['NOT_AVAILABLE_MESS'] ?>" href="javascript:void(0)"
                                    <?= ($actualItem['CAN_BUY'] ? 'style="display: none;"' : '') ?>><?
                                    echo $arParams['MESS_NOT_AVAILABLE'];
                                    ?></button>
                                <div id="<?= $itemIds['BASKET_ACTIONS'] ?>" <?= ($actualItem['CAN_BUY'] ? '' : 'style="display: none;"') ?>>
                                    <button class="btn btn-sm btn-third btn-animated btn-cart bnt-cart-simple"
                                            title="<?= ($arParams['ADD_TO_BASKET_ACTION'] === 'BUY' ? $arParams['MESS_BTN_BUY'] : $arParams['MESS_BTN_ADD_TO_BASKET']) ?>"
                                            rel="nofollow" id="<?= $itemIds['BUY_LINK'] ?>"><?
                                        //echo ($arParams['ADD_TO_BASKET_ACTION'] === 'BUY' ? $arParams['MESS_BTN_BUY'] : $arParams['MESS_BTN_ADD_TO_BASKET']);
                                        ?><span class="svg-icon icon-cart"></span></button>
                                </div>
                            </div>
                            <?
                        } else {
                            ?>
                            <div class="product-card__button-wrapper btn-animated-wrapper">
                                <a class="btn btn-sm btn-third btn-animated btn-cart bnt-cart-simple"
                                   title="<?= $arParams['MESS_BTN_DETAIL'] ?>"
                                   href="<?= $item['DETAIL_PAGE_URL'] ?>"><?= $arParams['MESS_BTN_DETAIL'] ?></a>
                            </div>
                            <?
                        }
                    }

                    if (
                        $arParams['DISPLAY_COMPARE']
                        && (!$haveOffers || $arParams['PRODUCT_DISPLAY_MODE'] === 'Y')
                    ) {
                        ?>
                        <div class="product-card__button-wrapper btn-animated-wrapper">
                            <button id="<?= $itemIds['COMPARE_LINK'] ?>"
                                    class="btn btn-sm btn-third btn-animated btn-compare" title="В сравнение"
                                    aria-label="В сравнение">
                                <?// <input type="checkbox" data-entity="compare-checkbox">?>
                                <span data-entity="compare-title">
                                        <span class="svg-icon icon-compare"></span>
                                    </span>
                            </button>
                        </div>
                        <?
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
                <?
                break;

        }
    }
}
?>

<div class="item-sku-block" style="display: none">
    <?
    if ($arParams['PRODUCT_DISPLAY_MODE'] === 'Y' && $haveOffers && !empty($item['OFFERS_PROP'])) {
        ?>
        <div class="product-item-info-container product-item-hidden" id="<?= $itemIds['PROP_DIV'] ?>">
            <?
            foreach ($arParams['SKU_PROPS'] as $skuProperty) {
                $propertyId = $skuProperty['ID'];
                $skuProperty['NAME'] = htmlspecialcharsbx($skuProperty['NAME']);
                if (!isset($item['SKU_TREE_VALUES'][$propertyId]))
                    continue;
                ?>
                <div data-entity="sku-block">
                    <div class="product-item-scu-container" data-entity="sku-line-block">
                        <div class="product-item-scu-block-title text-muted"><?= $skuProperty['NAME'] ?></div>
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
    ?>
</div>

