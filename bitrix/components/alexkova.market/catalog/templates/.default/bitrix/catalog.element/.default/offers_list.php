<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

    $bxr_use_links_sku = COption::GetOptionString("alexkova.market", "bxr_use_links_sku", "N");
?>
<table>
    <tbody>
        <?  foreach ($arResult["OFFERS"] as $key => $offer) :
            $propsStr = "";
            foreach($offer["PROPERTIES"] as $propCode => $arProp):
                $printValue = "";
                if (array_key_exists($propCode, $arResult["OFFERS_PROP"]) || in_array($arProp["CODE"], $arParams["~OFFERS_PROPERTY_CODE"])):
                    $sPropId = $arResult["SKU_PROPS"][$propCode]["XML_MAP"][$arProp["VALUE"]];
                    if ($arProp["PROPERTY_TYPE"] == "E" && strlen($arResult["SKU_PROPS"][$propCode]["VALUES"][$arProp["VALUE"]]["NAME"]) > 0) {
                        $printValue = $arProp["NAME"].": ".$arResult["SKU_PROPS"][$propCode]["VALUES"][$arProp["VALUE"]]["NAME"];
                    } else if ($arProp["PROPERTY_TYPE"] == "S" && strlen($arResult["SKU_PROPS"][$propCode]["VALUES"][$sPropId]["NAME"]) > 0) {
                        $printValue = $arProp["NAME"].": ".$arResult["SKU_PROPS"][$propCode]["VALUES"][$sPropId]["NAME"];
                    } else if ($arProp["PROPERTY_TYPE"] == "L" && $arProp["MULTIPLE"] == "Y" && $arProp["VALUE"]) {
                            $printValue = $arProp["NAME"].": ";
                            $valueCount = count($arProp["VALUE"])-1;
                            foreach ($arProp["VALUE"] as $key => $value)
                            {
                                $printValue .= $value;
                                if ($key!=$valueCount) $printValue .= ',';
                            }
                    } else if (strlen($arProp["VALUE"]) > 0) {
                            $printValue = $arProp["NAME"].": ".$arProp["VALUE"];
                    }

                        if(!empty($printValue))
                            $propsStr .= $printValue.", ";

                endif;
            endforeach;
            $propsStr = rtrim($propsStr, ", ");?>
        <tr data-offer-id="<?=$offer["ID"]?>" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
            <td class="basket-image first hidden-xs">
                <?
                    if (is_array($offer["PREVIEW_PICTURE"])) {
                        $src = $offer["PREVIEW_PICTURE"]["SRC"];
                    } elseif (intval($offer["PREVIEW_PICTURE"]) > 0) {
                        $src = CFile::GetPath($offer["PREVIEW_PICTURE"]);
                    } elseif (is_array($offer["DETAIL_PICTURE"])) {
                        $src = $offer["DETAIL_PICTURE"]["SRC"];
                    } elseif (intval($offer["DETAIL_PICTURE"]) > 0) {
                        $src = CFile::GetPath($offer["DETAIL_PICTURE"]);
                    } elseif ($offer["MORE_PHOTO"][0]["SRC"] && $offer["MORE_PHOTO"][0]["TYPE"] != "NO_PHOTO") {
                        $src = $offer["MORE_PHOTO"][0]["SRC"];
                    } elseif ($arResult["MORE_PHOTO"][0]["SRC"] && $arParams["SHOW_MAIN_INSTEAD_NF_SKU"] == "Y") {
                        $src = $arResult["MORE_PHOTO"][0]["SRC"];
                    } else {
                        $src = '/bitrix/tools/bxready/.default/no-image.png';
                    }
                    $ofLink = ($arParams["SHOW_OFFER_PIC_BYCLICK"] == "Y" || $bxr_use_links_sku == "Y") ? "<a href='" : "<span" ;
                    if ($arParams["SHOW_OFFER_PIC_BYCLICK"] == "Y") {
                      $ofLink .= $src."' ";
                    }else{
                      if ($bxr_use_links_sku == "Y") { $ofLink .= $offer["DETAIL_PAGE_URL"]."' "; }
                    }
                    $ofLink .= " class='bxr-offer-img-in-list";
                    $ofLink .= ($arParams["SHOW_OFFER_PIC_BYCLICK"] == "Y") ? " fancybox-Y' " : "' " ;
                    $ofLink .= ($offer["DETAIL_PAGE_URL"]) ?  "itemprop='url'" : "" ;
                    $ofLink .= ">";
                    $ofLink .= "<img src='".$src."' itemprop='image' alt='".$offer["NAME"]."'>";
                    $ofLink .= ($arParams["SHOW_OFFER_PIC_BYCLICK"] == "Y" || $bxr_use_links_sku == "Y") ? "</a>" : "</span>" ;
                    echo $ofLink;
                    ?>

            </td>
            <td class="basket-name">
                <?if($bxr_use_links_sku == "Y"):?>
                    <a href="<?=$offer["DETAIL_PAGE_URL"];?>" class="bxr-font-hover-light" itemprop="sku">
                        <?=$offer["NAME"]?>
                    </a>
                <?else:?>
                     <span class="bxr-font-hover-light-span" itemprop="sku">
                        <?=$offer["NAME"]?>
                    </span>
                <?endif;?>
                <div class="offers-display-props"><?=$propsStr?></div>
                <input type="hidden" value="<?=$propsStr?>" class="offers-props">
            </td>
            <td class="basket-price bxr-format-price hidden-xs">
                <div class="bxr-offer-price-wrap" data-item="<?=$offer["ID"]?>">
                    <?foreach($offer["PRICES"] as $priceCode => $arPrice):
                        if (in_array($arResult["CAT_PRICES"][$priceCode]["ID"], $arResult["PRICES_ALLOW"])):?>
                            <div class="bxr-market-item-price bxr-format-price <?if (!$priceNameShow || count($offer["PRICES"]) == 1) echo 'bxr-market-price-without-name'?>">
                                <?
                                //--price name--
				if ($priceNameShow && count($offer["PRICES"]) > 1) {?>
                                    <span class="bxr-market-price-name"><?=$arResult["CAT_PRICES"][$priceCode]["TITLE"]?></span>
                                <?}
                                //--next blocks has float right--
                                //--current price with all discounts--
                                ?>
                                <span class="bxr-market-current-price bxr-market-format-price"<?=($arItemIDs['PRICE'] > 0)?' id="offer_'.$offer["ID"].'_'.$arItemIDs['PRICE'].'"':''; ?>><?=CurrencyFormat($arPrice['DISCOUNT_VALUE'], $arPrice['CURRENCY'])?></span>
                                <? //--old price--
                                if ($boolDiscountShow && $arPrice['VALUE'] != $arPrice['DISCOUNT_VALUE']) {?>
                                    <br><span class="bxr-market-old-price hidden-xs"<?=($arItemIDs['OLD_PRICE'] > 0)?' id="offer_'.$offer["ID"].'_'.$arItemIDs['OLD_PRICE'].'"':''; ?>><?=$arPrice['PRINT_VALUE']?></span>
                                <?}?>
                                <div class="clearfix"></div>
                            </div>
                            <?if (!$priceNameShow || count($offer["PRICES"]) == 1) {?>
                                <div class="clearfix"></div>
                            <?}
                        endif;
                    endforeach;?>
                </div>
            <meta itemprop="price" content="<?=($arPrice['DISCOUNT_VALUE'])?$arPrice['DISCOUNT_VALUE']:0?>">
            <meta itemprop="priceCurrency" content="<?=($arPrice['CURRENCY'])?$arPrice['CURRENCY']:'RUB'?>">
            </td>
            <td class="basket-line-qty">
                 <span class="bxr-market-current-price bxr-market-format-price hidden-lg hidden-md hidden-sm" id="qty_<?=$arItemIDs['PRICE'].'_'.$offer["ID"];?>"><?=CurrencyFormat($arPrice['DISCOUNT_VALUE'], $arPrice['CURRENCY'])?></span>
                <div class="offers-btn-wrap" data-item="<?=$offer["ID"]?>">
                    <?if (($offer["CATALOG_QUANTITY"] > 0) || ($offer["CATALOG_CAN_BUY_ZERO"] == "Y")) {
						?><link itemprop="availability" href="http://schema.org/InStock" /><?
					} else {
						?><link itemprop="availability" href="http://schema.org/OutOfStock" /><?
					};
					if ($offer["CATALOG_QUANTITY"] <= 0 && $offer["CATALOG_CAN_BUY_ZERO"] == "N" || !$offer["PRICES"]) {
                        if($showSubscribeBtn) {?>
                            <div class="bxr-subscribe-wrap">
                                <?$APPLICATION->includeComponent('alexkova.market:catalog.product.subscribe','',
                                    array(
                                        'PRODUCT_ID' => $offer['ID'],
                                        'BUTTON_ID' => 'bxr-dofflist-'.$offer['ID'].'-subscribe',
                                        'BUTTON_CLASS' => 'bxr-color-button bxr-subscribe',
                                    ),
                                    $component, array('HIDE_ICONS' => 'Y')
                                );?>
                            </div>
                        <?} else {?>
                            <button class="bxr-color-button bxr-trade-request" value="<?=$offer["ID"]?>">
                                    <?if (strlen($arParams["MESS_BTN_REQUEST"]) > 0):?>
                                        <?=$arParams["MESS_BTN_REQUEST"]?>
                                    <?else:?>
                                        <?=GetMessage("REQUEST_BTN")?>
                                    <?endif;?>
                            </button>
                        <?}
                    } else {?>
                        <?
                            $qtyMax = ($offer["CATALOG_CAN_BUY_ZERO"] == "Y") ? 0 : $offer["CATALOG_QUANTITY"];

                            $ratio = 1;

                            if($ratio_settings == "own_prop" && strlen($bxr_ratio_prop_code)>0
                                    && isset($offer["PROPERTIES"][$bxr_ratio_prop_code]["VALUE"]) && is_numeric($offer["PROPERTIES"][$bxr_ratio_prop_code]["VALUE"]))
                                $ratio = $offer["PROPERTIES"][$bxr_ratio_prop_code]["VALUE"];
                            elseif($ratio_settings == "base")
                                $ratio = $offer["CATALOG_MEASURE_RATIO"];

                            $quantity = ($ratio > $offer["CATALOG_QUANTITY"] && $offer["CATALOG_QUANTITY"]>0) ? $offer["CATALOG_QUANTITY"] : $ratio;
                        ?>
                        <form class="bxr-basket-action bxr-basket-group bxr-currnet-torg">
                            <input type="button" class="bxr-quantity-button-minus hidden-xs" value="-" data-item="<?=$offer["ID"]?>" data-ratio="<?=$ratio;?>">
                            <input type="text" name="quantity" value="<?=$quantity;?>" class="bxr-quantity-text hidden-xs" data-item="<?=$offer["ID"]?>">
                            <input type="button" class="bxr-quantity-button-plus hidden-xs" value="+" data-item="<?=$offer["ID"]?>" data-ratio="<?=$ratio;?>" data-max="<?=$qtyMax?>">
                            <button class="bxr-color-button bxr-color-button-small-only-icon bxr-basket-add">
                                <span class="fa fa-shopping-cart"></span>
                            </button>
                            <input class="bxr-basket-item-id" type="hidden" name="item" value="<?=$offer["ID"]?>">
                            <input type="hidden" name="action" value="add">
                        </form>
                        <?if ($useOneClick):
                        // --one click buy block--?>
                        <div class="bxr-basket-action hidden-xs">
                            <button class="bxr-color-button bxr-one-click-buy" data-item="<?=$offer["ID"]?>">
                                <?if (strlen($arParams["USE_ONE_CLICK_TEXT"]) > 0):
                                    echo $arParams["USE_ONE_CLICK_TEXT"];
                                else:
                                    echo GetMessage("ONE_CLICK_BUY");
                                endif;?>
                            </button>
                        </div>
                        <?endif;?>
                        <div class="clearfix"></div>
                    <?}?>
                </div>
            </td>
        </tr>
        <?endforeach;?>
    </tbody>
</table>
<script>
$(document).on("mouseover", ".bxr-detail-offers tr", function() {
    trade_id = '<?=$arResult["ID"]?>';
    trade_name = '<?=$arResult["NAME"]?>';
    trade_link = '<?=$arResult["DETAIL_PAGE_URL"]?>';
    selectParams = $(this).find('input.offers-props').val();
    current_offer_id = $(this).data('offer-id');

    if(selectParams != undefined && selectParams !="") {
            formRequestMsg = "<?=GetMessage('OFFER_REQUEST_MSG')?>";
            formRequestMsg = formRequestMsg.replace("#PARAMS#", ": " + selectParams);
    }
    else {
        formRequestMsg = "<?=GetMessage('TRADE_REQUEST_MSG')?>";
        formRequestMsg = formRequestMsg.replace("#PARAMS#", "");
    }

    formRequestMsg = formRequestMsg.replace("#TRADE_NAME#", '<?=htmlspecialchars($arResult['NAME'], ENT_QUOTES, SITE_CHARSET)?>');
});
    <?if (isset($arParams["SHOW_OFFER_PIC_BYCLICK"]) && $arParams["SHOW_OFFER_PIC_BYCLICK"] == "Y" ):?>
        $("a.fancybox-Y").fancybox();
    <?endif;?>
</script>
