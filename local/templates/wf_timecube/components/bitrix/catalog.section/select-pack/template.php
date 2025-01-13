<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$this->setFrameMode(true);
?>
<!--Products popup-->
<div id="products-popup" class="wf-popup wf-popup--medium">
    <input type="hidden" name="productid" id="wf-productid" value="123">
    <div class="wf-popup-header">
        <p class="wf-popup__title">Выберите упаковку</p>
    </div>
    <div class="wf-popup-body">
        <div class="products-list products-list-simple">
            <?
            foreach ($arResult['ITEMS'] as $arItem) { ?>
                <div class="product-card product-card--simple">
                    <div class="product-card__inner">
                        <?/*global $USER;
                        if ($USER->isAdmin()) {
                            echo "<spoiler>";
                            echo "<pre>";
                            var_dump($arItem["ITEM_PRICES"][$arItem["ITEM_PRICE_SELECTED"]]["PRINT_PRICE"]);
                            echo "</pre>";
                            echo "</spoiler>";
                        }*/?>
                        <div class="product-card__pic product-card__row">
                            <img class="lozad" data-src="<?=$arItem['DETAIL_PICTURE']['SRC']?>" alt="">
                        </div>
                        <div class="product-card__details product-card__row">
                            <p class="product-card__title wf-product-item__name"><?=$arItem['NAME']?></p>
                        </div>
                        <div class="product-card__row product-card__row--centered">
                            <p class="product-card__price">
                                <span class="price-normally"><?=$arItem["ITEM_PRICES"][$arItem["ITEM_PRICE_SELECTED"]]["PRINT_PRICE"]?></span>
                                <noindex><span class="price-free">Бесплатно</span></noindex>
                            </p>
                        </div>
                        <div class="product-card__buttons">
                            <button class="btn btn-sm btn-primary wf-product-select-pack btn-a-large"
                                    data-productid="<?=$arItem['ID']?>">Выбрать</button>
                        </div>
                    </div>
                </div>
            <? } ?>
        </div>
    </div>
    <div class="wf-popup__footer">
    </div>
</div>
<!--/products popup-->