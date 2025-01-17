<?
$usePriceCount = ('Y' == $arParams['USE_PRICE_COUNT']);
$boolDiscountShow = ('Y' == $arParams['SHOW_OLD_PRICE']);
$priceNameShow = ('Y' == $arParams['SHOW_PRICE_NAME']);
$showFrom = ('Y' == $arParams["OFFER_PRICE_SHOW_FROM"]);
$showMeasure = ('Y' == $arParams["SHOW_MEASURE"]);

if (($usePriceCount || count($arResult["PRICES"]) > 0) && count($arResult["OFFERS"]) <= 0){
    if ($usePriceCount) {
        if ($arParams["GROUP_PRICE_COUNT"] == "count") {
                foreach ($arResult["PRICE_MATRIX"]["ROWS"] as $itemRangeKey => $itemRange) {?>
                    <div class="bxr-market-item-price-group">
                        <?if (count($arResult["PRICE_MATRIX"]["ROWS"]) > 1) {?>
                            <div class="bxr-market-item-range-title arrow">
                                <?=GetMessage("BXR_DETAIL_BUY_RANGE").' '.$itemRange["QUANTITY_FROM"];if ($itemRange["QUANTITY_TO"] > 0) echo '-'.$itemRange["QUANTITY_TO"];
                                echo ' '.$arResult["CATALOG_MEASURE_NAME"];?>
                            </div>
                        <?}
                        foreach ($arResult["PRICE_MATRIX"]["MATRIX"] as $priceIndex => $price) {
                            if (in_array($priceIndex, $arResult["PRICES_ALLOW"])):?>
                                <div class="bxr-market-item-price bxr-format-price 
                                    <?if (count($arResult["PRICE_MATRIX"]["COLS"]) == 1 && count($arResult["PRICE_MATRIX"]["ROWS"]) == 1) echo "bxr-no-range-price bxr-no-range-no-names-price";
                                    if (count($arResult["PRICE_MATRIX"]["ROWS"]) == 1) echo "bxr-no-range-price";
                                    if (!$priceNameShow || count($arResult["PRICE_MATRIX"]["COLS"]) == 1) echo "bxr-no-range-no-names-price"?>">
                                    <? //--price name--
                                    if ($priceNameShow && count($arResult["PRICE_MATRIX"]["COLS"]) > 1) {?>
                                        <span class="bxr-market-price-name"><?=$arResult["PRICE_MATRIX"]["COLS"][$priceIndex]["NAME_LANG"]?></span>
                                    <?}
                                    //--next blocks has float right--
                                    //--current price with all discounts--
                                    ?><span class="bxr-market-current-price bxr-market-format-price" id="<? echo $arItemIDs['PRICE']; ?>"><?=CurrencyFormat($price[$itemRangeKey]['DISCOUNT_PRICE'],$price[$itemRangeKey]['CURRENCY']) ?><?=($showMeasure?GetMessage("CATALOG_MEASURE_DELIMITER").$arResult["CATALOG_MEASURE_RATIO"].$arResult["CATALOG_MEASURE_NAME"]:'')?></span>
                                    <?
									//--old price--
                                    if ($boolDiscountShow && $price[$itemRangeKey]['PRICE'] != $price[$itemRangeKey]['DISCOUNT_PRICE']) {?>
                                        <span class="bxr-market-old-price" id="<? echo $arItemIDs['OLD_PRICE']; ?>"><?=CurrencyFormat($price[$itemRangeKey]['PRICE'],$price[$itemRangeKey]['CURRENCY'])?></span>
                                    <?}?>
                                    <div class="clearfix"></div>
                                </div>
				<meta itemprop="price" content="<?=($price[$itemRangeKey]['DISCOUNT_PRICE'])?$price[$itemRangeKey]['DISCOUNT_PRICE']:0?>">
				<meta itemprop="priceCurrency" content="<?=($price[$itemRangeKey]['CURRENCY'])?$price[$itemRangeKey]['CURRENCY']:'RUB'?>">
                                <div class="clearfix"></div>
                            <?  endif;
                        }?>
                    </div>
            <?  }
        } else {
            foreach ($arResult["PRICE_MATRIX"]["MATRIX"] as $priceIndex => $price) {
                if (in_array($priceIndex, $arResult["PRICES_ALLOW"])):?>
                    <div class="bxr-market-item-price-group">
                        <?if (count($arResult["PRICE_MATRIX"]["MATRIX"]) > 1 && count($arResult["PRICE_MATRIX"]["ROWS"]) > 1) {?>
                            <div class="bxr-market-item-range-title arrow">
                                <?=$arResult["PRICE_MATRIX"]["COLS"][$priceIndex]["NAME_LANG"]?>
                            </div>
                        <?}
                          foreach ($price as $priceRangeIndex => $priceRange) {?>
                                <div class="bxr-market-item-price bxr-format-price <?if (count($arResult["PRICE_MATRIX"]["ROWS"]) == 1) echo "bxr-no-range-price"?>
                                    <?if (count($arResult["PRICE_MATRIX"]["ROWS"]) == 1 && (count($arResult["PRICE_MATRIX"]["MATRIX"]) == 1 || !$priceNameShow)) echo "bxr-no-range-no-names-price"?>">
                                    <!--price range-->
                                    <span class="bxr-market-price-name">
                                        <?if (count($arResult["PRICE_MATRIX"]["ROWS"]) > 1) {
                                            echo GetMessage("BXR_DETAIL_BUY_RANGE").' '.$arResult["PRICE_MATRIX"]["ROWS"][$priceRangeIndex]["QUANTITY_FROM"];if ($arResult["PRICE_MATRIX"]["ROWS"][$priceRangeIndex]["QUANTITY_TO"] > 0) echo '-'.$arResult["PRICE_MATRIX"]["ROWS"][$priceRangeIndex]["QUANTITY_TO"];
                                            echo ' '.$arResult["CATALOG_MEASURE_NAME"];
                                        } elseif (count($arResult["PRICE_MATRIX"]["MATRIX"]) > 1 && $priceNameShow) {
                                            echo $arResult["PRICE_MATRIX"]["COLS"][$priceIndex]["NAME_LANG"];
                                        }?>
                                    </span><?
                                    //--next blocks has float right--
                                    //--current price with all discounts--
                                    ?><span class="bxr-market-current-price bxr-market-format-price" id="<? echo $arItemIDs['PRICE'].'_'.$priceIndex; ?>"><?=CurrencyFormat($priceRange['DISCOUNT_PRICE'],$priceRange['CURRENCY'])?><?=($showMeasure?GetMessage("CATALOG_MEASURE_DELIMITER").$arResult["CATALOG_MEASURE_RATIO"].$arResult["CATALOG_MEASURE_NAME"]:'')?></span>
                                    <?//--old price--
                                    if ($boolDiscountShow && $priceRange['PRICE'] != $priceRange['DISCOUNT_PRICE']) {?>
                                        <span class="bxr-market-old-price" id="<? echo $arItemIDs['OLD_PRICE'].'_'.$priceIndex; ?>"><?=CurrencyFormat($priceRange['PRICE'],$priceRange['CURRENCY'])?></span>
                                    <?}?>
                                    <div class="clearfix"></div>
                                </div>
				<meta itemprop="price" content="<?=($priceRange['DISCOUNT_PRICE'])?$priceRange['DISCOUNT_PRICE']:0?>">
				<meta itemprop="priceCurrency" content="<?=($priceRange['CURRENCY'])?$priceRange['CURRENCY']:'RUB'?>">
                        <?}?>
                    </div>
            <?  endif;
          }
        }
    } else {
        foreach($arResult["PRICES"] as $priceCode => $arPrice):
            if (in_array($arResult["CAT_PRICES"][$priceCode]["ID"], $arResult["PRICES_ALLOW"])):?>
                <div class="bxr-market-item-price bxr-format-price <?if (!$priceNameShow || count($arResult["PRICES"]) == 1) echo 'bxr-market-price-without-name'?>">
                    <?//--price name--
                    if ($priceNameShow && count($arResult["PRICES"]) > 1) {?>
                        <span class="bxr-market-price-name"><?=$arResult["CAT_PRICES"][$priceCode]["TITLE"]?></span>
                    <?}
                    //--next blocks has float right--
                    //--current price with all discounts--
                    ?><span class="bxr-market-current-price bxr-market-format-price" id="<? echo $arItemIDs['PRICE']; ?>"><?=CurrencyFormat($arPrice['DISCOUNT_VALUE'],$arPrice["CURRENCY"]) ?><?=($showMeasure?GetMessage("CATALOG_MEASURE_DELIMITER").$arResult["CATALOG_MEASURE_RATIO"].$arResult["CATALOG_MEASURE_NAME"]:'')?></span>
                    <?//--old price--
                    if ($boolDiscountShow && $arPrice['VALUE'] != $arPrice['DISCOUNT_VALUE']) {?>
                        <span class="bxr-market-old-price" id="<? echo $arItemIDs['OLD_PRICE']; ?>"><?=$arPrice['PRINT_VALUE']?></span>
                    <?}?>
                    <div class="clearfix"></div>
                </div>
		<meta itemprop="price" content="<?=($arPrice['DISCOUNT_VALUE'])?$arPrice['DISCOUNT_VALUE']:0?>">
		<meta itemprop="priceCurrency" content="<?=($arPrice['CURRENCY'])?$arPrice['CURRENCY']:'RUB'?>">
                <?if (!$priceNameShow || count($arResult["PRICES"]) == 1) {?>
                    <div class="clearfix"></div>
                <?}
            endif;
        endforeach;
    }
 } elseif (count($arResult["OFFERS"]) > 0) {?>
    <div class="bxr-product-price-wrap">
	    <meta itemprop="lowPrice" content="<?=$arResult['PROPERTIES']['MINIMUM_PRICE']['VALUE']?>">
	    <meta itemprop="highPrice" content="<?=$arResult['PROPERTIES']['MAXIMUM_PRICE']['VALUE']?>">
	    <meta itemprop="priceCurrency" content="<?=$arResult['MIN_PRICE'][$arParams['PRICE_CODE'][0]]['CURRENCY']?>">
	    <meta itemprop="offerCount" content="<?=count($arResult["OFFERS"])?>">
        <?foreach($arResult["MIN_PRICE"] as $priceCode => $arPrice):
            if (in_array($arResult["CAT_PRICES"][$priceCode]["ID"], $arResult["PRICES_ALLOW"])):?>
                <div class="bxr-market-item-price bxr-format-price <?if (!$priceNameShow || count($arResult["MIN_PRICE"]) == 1) echo 'bxr-market-price-without-name'?>">
                    <!--price name-->
                    <?if ($priceNameShow && count($arResult["MIN_PRICE"]) > 1) {?>
                        <span class="bxr-market-price-name"><?=$arResult["CAT_PRICES"][$priceCode]["TITLE"]?></span>
                    <?}
                    //--next blocks has float right--
                    //--current price with all discounts--
                    ?><span class="bxr-market-current-price bxr-market-format-price" id="<? echo $arItemIDs['PRICE'].'_'.$priceCode; ?>"><?=CurrencyFormat($arPrice['DISCOUNT_VALUE'],$arPrice["CURRENCY"]) ?><?=($showMeasure?GetMessage("CATALOG_MEASURE_DELIMITER").$arResult["CATALOG_MEASURE_RATIO"].$arResult["CATALOG_MEASURE_NAME"]:'')?></span>
                    <?//--old price--
                    if ($boolDiscountShow && $arPrice['VALUE'] != $arPrice['DISCOUNT_VALUE']) {?>
                        <span class="bxr-market-old-price" id="<? echo $arItemIDs['OLD_PRICE'].'_'.$priceCode; ?>"><?=$arPrice['PRINT_VALUE']?></span>
                    <?}
                    if ($showFrom) {?>
                        <span class="bxr-market-from"><?=GetMessage('FROM')?></span>
                    <?}?>
                    <div class="clearfix"></div>
                </div>
                <?if (!$priceNameShow || count($arResult["MIN_PRICE"]) == 1) {?>
                    <div class="clearfix"></div>
                <?}
            endif;
        endforeach;?>
    </div>
    <?foreach($arResult["OFFERS"] as $offer):?>
        <div class="bxr-offer-price-wrap" data-item="<?=$offer["ID"]?>" style="display: none;">
            <?foreach($offer["PRICES"] as $priceCode => $arPrice):
                if (in_array($arResult["CAT_PRICES"][$priceCode]["ID"], $arResult["PRICES_ALLOW"])):?>
                    <div class="bxr-market-item-price bxr-format-price <?if (!$priceNameShow || count($offer["PRICES"]) == 1) echo 'bxr-market-price-without-name'?>">
                        <?//--price name--
                        if ($priceNameShow && count($offer["PRICES"]) > 1) {?>
                            <span class="bxr-market-price-name"><?=$arResult["CAT_PRICES"][$priceCode]["TITLE"]?></span>
                        <?}
                        //--next blocks has float right--
                        //--current price with all discounts--
                        ?><span class="bxr-market-current-price bxr-market-format-price" id="<? echo $arItemIDs['PRICE'].'_'.$offer["ID"]; ?>"><?=CurrencyFormat($arPrice['DISCOUNT_VALUE'],$arPrice["CURRENCY"]) ?><?=($showMeasure?GetMessage("CATALOG_MEASURE_DELIMITER").$offer["CATALOG_MEASURE_RATIO"].$offer["CATALOG_MEASURE_NAME"]:'')?></span>
                        <?//--old price--
                        if ($boolDiscountShow && $arPrice['VALUE'] != $arPrice['DISCOUNT_VALUE']) {?>
                            <span class="bxr-market-old-price" id="<? echo $arItemIDs['OLD_PRICE'].'_'.$offer["ID"]; ?>"><?=$arPrice['PRINT_VALUE']?></span>
                        <?}?>
                        <div class="clearfix"></div>
                    </div>
                    <?if (!$priceNameShow || count($offer["PRICES"]) == 1) {?>
                        <div class="clearfix"></div>
                    <?}
                endif;
            endforeach;?>
        </div>
    <?endforeach;
} else {?>
    <meta itemprop="price" content="0">
    <meta itemprop="priceCurrency" content="RUB">
<?}
        if (($arResult[CATALOG_QUANTITY] > 0) || ($arResult["CATALOG_CAN_BUY_ZERO"] == "Y")) {
	    ?><link itemprop="availability" href="http://schema.org/InStock" /><?
        } else {
            ?><link itemprop="availability" href="http://schema.org/OutOfStock" /><?
        };
?><script>
var google_tag_params = {
	ecomm_prodid: "<?=$arResult["ID"]?>",
	ecomm_pagetype: "product",
<?if(!count($arResult["OFFERS"])):?>
	ecomm_totalvalue: <?=$arPrice['DISCOUNT_VALUE']?>
<?endif;?>
};
</script>
