<?
$js_sku_props = CUtil::PhpToJSObject($arResult["SKU_PROPS_LIST"]);
$js_offers = CUtil::PhpToJSObject($arResult["OFFERS"]);
$offer_cnt = count($arResult["OFFERS"]);
?>

<script>
    var sku_props = <?=$js_sku_props?>;
    var offers = <?=$js_offers?>;
    var offer_cnt = <?=$offer_cnt?>;
    var default_price = "<?=$arResult['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?>";
    var formRequestMsg;
    var trade_name = "<?=$arResult['NAME']?>";
    var trade_id = "<?=$arResult['ID']?>";
    var trade_link = "<?=$arResult['DETAIL_PAGE_URL']?>";
    var current_offer_id = 0;
    var changeTitle = "<?=$arParams["CHANGE_TITLE_SKU"]?>";
    
    function declOfNum(number, titles)
    {
        cases = [2, 0, 1, 1, 1, 2];
        return titles[ (number%100>4 && number%100<20)? 2 : cases[(number%10<5)?number%10:5] ];
    }
    
    $(document).on("click", "#bxr-market-sku-select-wrap .sku-prop-value", function(event, isTriger) {

        sku_clicked = $(this).data("prop-code");
        if ($(this).hasClass("deactive")) {
            alert("<?=GetMessage('NO_OFFER_WITH_PARAMS')?>");
        } else {
            if (!$(this).hasClass("active"))  {
                $(this).closest("ul").find(".sku-prop-value").removeClass("active");
                $(this).addClass("active");
            } else {
                $(this).removeClass("active");
            }
            availOffer = [];
            buy_offers = [];
            prop_variants = [];
            index = 0;
            
            $.each(offers, function(offerId) {
                index++;
                curOffer = this;
                availOffer[offerId] = [];
                availOffer[offerId]["VAL"] = true;
                $(".sku-prop-value.active").each(function() {
                    curProp = this;
                    propCode = $(curProp).data("prop-code");
                    propType = $(curProp).data("prop-type");
                    valId = $(curProp).data("val-id");
                    valName = $(curProp).data("val-name");
                    valCode = $(curProp).data("val-code");
                    availOffer[offerId][propCode] = true;
                    if ( (propType == "E" && valId != curOffer["PROPERTIES"][propCode]["VALUE"]) || 
                         (propType == "L" && valName != curOffer["PROPERTIES"][propCode]["VALUE"]) || 
                         ( propType == "S" && valCode != curOffer["PROPERTIES"][propCode]["VALUE"]) 
                       ) 
                    {
                        availOffer[offerId][propCode] = false;
                        availOffer[offerId]["VAL"] = false;
                    }
                });
                if (index == offer_cnt) {
                    
                    $.each(availOffer, function(offerId) {
                        if (this["VAL"])
                        buy_offers.push(offerId);
                    });
                    if (buy_offers.length > 0) {
                        slickFilterStr = "";
                        $("#bxr-market-sku-select-wrap .sku-prop-value.active").each(function() {
                            slickFilterStr += '[data-'+$(this).data("prop-code").toLowerCase()+'="'+$(this).data("val-code")+'"]';
                        });
                        slickFilterStr += ':not([data-main="none"])';
                        <?if ($arParams["FILTER_SKU_PHOTO_FLEX"] == "Y") {?>     
                            if (slickFilterStr.length) {
                                <?if ($arParams["FILTER_SKU_PHOTO"] == "Y"
//                                        || $arParams["ADD_DETAIL_TO_SLIDER_SKU"] != "Y"
                                        ) {?>
                                    $('.ax-element-slider-nav').slick('slickFilter',slickFilterStr);
                                    $('.ax-element-slider-main').slick('slickFilter',slickFilterStr);
                                    $($('.ax-element-slider-nav .slick-nav:not(.slick-cloned)')[0]).trigger('click');
                                <?} else {?>
                                    $('.ax-element-slider-nav').slick('slickFilter','[data-main="default"]');
                                    $('.ax-element-slider-main').slick('slickFilter','[data-main="default"]');
                                    $($('.ax-element-slider-nav .slick-nav:not(.slick-cloned)'+slickFilterStr)[0]).trigger('click');
                                <?}?>
                            } else {
                                $('.ax-element-slider-nav').slick('slickFilter','[data-main="default"]');
                                $('.ax-element-slider-main').slick('slickFilter','[data-main="default"]');
                                $($('.ax-element-slider-nav .slick-nav:not(.slick-cloned)')[0]).trigger('click');
                            }
                        <?}?>
                        
                        if (changeTitle == 'Y') {
                            document.title = trade_name;
                            $('h1').html(trade_name);
                        }
                        
                        foundMsg = "<?=GetMessage("OFFERS_FOUND")?>";
                        countMsg = foundMsg.replace("#CNT#",buy_offers.length);
                        countMsg += " " + declOfNum(buy_offers.length, ['<?=GetMessage("OFFERS_FOUND_1")?>', '<?=GetMessage("OFFERS_FOUND_2")?>', '<?=GetMessage("OFFERS_FOUND_n")?>']);
                        watch = "";
                        <?if( $arParams["HIDE_OFFERS_LIST"] != 'Y') {?>
                        watch = "<a href='#offers' data-scroll='bxr-offers' class='bxr-offers-scroll text-link scroll-navigate'><?=GetMessage("LOOK_OFFERS")?></a>";
                        <?}?>
                        closeicon = '<a class="popup-window-close-icon" href="" style="right: 12px; top: 8px;"></a>';
                        $(".offers-cnt").html(closeicon+countMsg+watch);
                    } else {
                        foundMsg = "<?=GetMessage("NO_OFFER_WITH_PARAMS")?>";
                        selectParams = "";
                        $("#bxr-market-sku-select-wrap .sku-prop-value.active").each(function() {
                            selectParams += $(this).closest('div').data('prop-name');
                            selectParams += ': ';
                            selectParams += $(this).data('val-name');
                            selectParams += '<br>';
                        });
                        leaveRequest = "<?=GetMessage('LEAVE_REQUEST')?>";
                        formRequestMsg = "<?=GetMessage('OFFER_REQUEST_MSG')?>";
                        stringParams = selectParams.replace(/<br>/g,", ");
                        stringParams = stringParams.slice(0, -2);
                        
                        if (changeTitle == 'Y') {
                            document.title = trade_name;
                            $('h1').html(trade_name);
                        }
                        
                        if(selectParams != undefined && selectParams !="") {
                            notFoundMsg = foundMsg.replace("#PARAMS#",selectParams);
                            formRequestMsg = formRequestMsg.replace("#PARAMS#", ": " + stringParams);
                        }
                        else {
                            notFoundMsg = foundMsg.replace("#PARAMS#", "");
                            formRequestMsg = formRequestMsg.replace("#PARAMS#", "");
                        }
                        
                        
                        $('.ax-element-slider-nav').slick('slickFilter','[data-main="default"]');
                        $('.ax-element-slider-main').slick('slickFilter','[data-main="default"]');
                        $($('.ax-element-slider-nav .slick-nav:not(.slick-cloned)')[0]).trigger('click');
                        
                        formRequestMsg = formRequestMsg.replace("#TRADE_NAME#",'<?=  htmlspecialchars($arResult['NAME'],ENT_QUOTES, SITE_CHARSET)?>');
                        requestBtn = "<a href='javascript:void(0)' data-id='<?=$arResult["ID"]?>' data-trade='<?=$arResult["NAME"]?>' data-url='<?=$arResult["DETAIL_PAGE_URL"]?>' data-params='"+selectParams+"' class='bxr-trade-request' id='leave_request'><?=GetMessage("REQUEST_BTN")?></a>";
                        closeicon = '<a class="popup-window-close-icon" href="" style="right: 12px; top: 8px;"></a>';
                        $(".offers-cnt").html(closeicon+notFoundMsg+'<br>'+leaveRequest+requestBtn);
                    }
                    if(!isTriger)
                        $(".offers-cnt").show();
                    
                    if (buy_offers.length == 1) {
                        var id = buy_offers[0];
                        current_offer_id = id;
                        setBasketIds(id);
                        setSkuUrl(id);
                        BX.onCustomEvent('onCatalogStoreProductChange', [id]);
                        
                        selectParams = "";
                        $(".sku-prop-value.active").each(function() {
                            selectParams += $(this).closest('div').data('prop-name');
                            selectParams += ': ';
                            selectParams += $(this).data('val-name');
                            selectParams += '<br>';
                        });
                        leaveRequest = "<?=GetMessage('LEAVE_REQUEST')?>";
                        formRequestMsg = "<?=GetMessage('OFFER_REQUEST_MSG')?>";
                        stringParams = selectParams.replace(/<br>/g,", ");
                        stringParams = stringParams.slice(0, -2);
                        
                         if(selectParams != undefined && selectParams !="") {
                            notFoundMsg = foundMsg.replace("#PARAMS#",selectParams);
                            formRequestMsg = formRequestMsg.replace("#PARAMS#", ": " + stringParams);
                        }
                        else {
                            notFoundMsg = foundMsg.replace("#PARAMS#", "");
                            formRequestMsg = formRequestMsg.replace("#PARAMS#", "");
                        }
                        
                        formRequestMsg = formRequestMsg.replace("#TRADE_NAME#",'<?=htmlspecialchars($arResult['NAME'],ENT_QUOTES, SITE_CHARSET)?>');
                        
                        $('.bxr-detail-offers tr').removeClass('avail');
                        $('.bxr-detail-offers tr[data-offer-id="'+id+'"]').addClass('avail');
                        
                        $('.bxr-basket-action').show();
                        $('#bxr-market-price-wrap .bxr-product-price-wrap').hide();
                        $('#bxr-market-price-wrap .bxr-offer-price-wrap').hide();
                        $('#bxr-market-price-wrap .bxr-offer-price-wrap[data-item="'+id+'"]').show();   
                        $('#bxr-market-detail-basket-btn-wrap .offers-btn-wrap[data-item="'+id+'"] .bxr-subscribe-wrap').show();
        
                        if (changeTitle == 'Y') {
                            offerName = offers[id]["NAME"];
                            document.title = offerName;
                            $('h1').html(offerName);
                        }
                        if (offers[id]["MORE_PHOTO"]) {
                            <?if ($arParams["FILTER_SKU_PHOTO"] == "Y"
//                                    || $arParams["ADD_DETAIL_TO_SLIDER_SKU"] != "Y"
                                    ) {?>
                                $('.ax-element-slider-nav').slick('slickFilter','[data-item='+id+']');
                                $('.ax-element-slider-main').slick('slickFilter','[data-item='+id+']');
                                $($('.ax-element-slider-nav .slick-nav:not(.slick-cloned)')[0]).trigger('click');
                            <?} else {?>
                                $('.ax-element-slider-nav').slick('slickFilter','[data-main="default"]');
                                $('.ax-element-slider-main').slick('slickFilter','[data-main="default"]');
                                if ($('.ax-element-slider-nav .slick-nav:not(.slick-cloned)[data-item='+id+']').length)
                                    $($('.ax-element-slider-nav .slick-nav:not(.slick-cloned)[data-item='+id+']')[0]).trigger('click');
                                else
                                    $($('.ax-element-slider-nav .slick-nav:not(.slick-cloned)')[0]).trigger('click');    
                            <?}?>
                        }
                        
                        $('.bxr-offer-avail-wrap').hide();
                        $('.bxr-main-avail-wrap').hide();
                        $('.bxr-offer-avail-wrap[data-item="'+id+'"]').show();
                    } else {
                        current_offer_id = 0;
                        $('.bxr-detail-offers tr').removeClass('avail');
                        $('#bxr-market-price-wrap .bxr-offer-price-wrap').hide();
                        $('#bxr-market-price-wrap .bxr-product-price-wrap').show();
                        $('#bxr-market-detail-basket-btn-wrap .bxr-basket-action').hide();
                        $('#bxr-market-detail-basket-btn-wrap .bxr-subscribe-wrap').hide();
                        setSkuUrl(null);
                        
                        $('.bxr-offer-avail-wrap').hide();
                        $('.bxr-main-avail-wrap').show();
                    };
                    $('.sku-row').removeClass("avail");
                    $.each(buy_offers, function() {
                        $('.sku-row[data-sku-id="'+this+'"]').addClass("avail");
                    });
                    buyIndex = 0;
                    $.each(buy_offers, function() {
                        if (offer_cnt != buy_offers.length)
                            $('.bxr-detail-offers tr[data-offer-id="'+this+'"]').addClass('avail');
                        cur_buy_offer = this;
                        buyIndex++;
                        $.each(offers[cur_buy_offer]["PROPERTIES"], function() {
                            cur_prop = this;
                            if (cur_prop["CODE"] in sku_props) {
                                if (!$.isArray(prop_variants[cur_prop["CODE"]]))
                                    prop_variants[cur_prop["CODE"]] = [];
                                if (!$.isArray(prop_variants[cur_prop["CODE"]]["VAL"]))
                                    prop_variants[cur_prop["CODE"]]["VAL"] = [];
                                prop_variants[cur_prop["CODE"]]["TYPE"] = cur_prop["PROPERTY_TYPE"];
                                if ($.inArray(cur_prop["VALUE"], prop_variants[cur_prop["CODE"]]["VAL"]) < 0) {
                                    if (cur_prop["PROPERTY_TYPE"] == "E") {
                                        prop_variants[cur_prop["CODE"]]["VAL"].push(parseInt(cur_prop["VALUE"]));
                                    } else {
                                        prop_variants[cur_prop["CODE"]]["VAL"].push(cur_prop["VALUE"]);
                                    }
                                }
                            }
                        });
                        if (buyIndex == buy_offers.length) {
                            cur_prop_deactive_val_name = [];
                            $('.sku-prop-value.deactive[data-prop-code="'+sku_clicked+'"]').each(function() {
                                cur_prop_deactive_val_name.push($(this).data("val-name"));
                            });
                            $('.sku-prop-value').removeClass('deactive');
                            for (key in prop_variants) {
                                cur_prop_var = prop_variants[key];
                                propCode = cur_prop_var["TYPE"];
                                $('.sku-prop-value[data-prop-code="'+key+'"]').each(function() {
                                    propType = $(this).data("prop-type");
                                    valName = $(this).data("val-name");
                                    valCode = $(this).data("val-code");
                                    valId = $(this).data("val-id");
                                    if ( (propType == "E" && $.inArray(valId, cur_prop_var["VAL"]) < 0) || 
                                         (propType == "L" && $.inArray(valName, cur_prop_var["VAL"]) < 0) || 
                                         (propType == "S" && $.inArray(valCode, cur_prop_var["VAL"]) < 0) 
                                       ) 
                                    {
                                        //not exist props actions
                                    }
                                })
                            }
                        };
                    });
                }
            });
        }
    });
    
    $(document).on("click", ".bxr-sku-select-wrap", function(event) {
        if ($(event.target).closest(".bxr-sku-select-items").length)
            return;        
        $('.bxr-sku-select-items').toggle();
        $('.bxr-sku-select-wrap hr').toggle();
        event.stopPropagation();
    });
    
    $(document).on("click", ".bxr-sku-select-item", function() {
        var id = $(this).data("pid");
        var inner = $(this).find('.bxr-offers-props').html();
        current_offer_id = id;
        setBasketIds(id);
        setSkuUrl(id);
        BX.onCustomEvent('onCatalogStoreProductChange', [id]);
        
        $('.bxr-detail-offers tr').removeClass('avail');
        $('.bxr-detail-offers tr[data-offer-id="'+id+'"]').addClass('avail');

        $('.bxr-basket-action').show();
        $('#bxr-market-price-wrap .bxr-product-price-wrap').hide();
        $('#bxr-market-price-wrap .bxr-offer-price-wrap').hide();
        $('#bxr-market-price-wrap .bxr-offer-price-wrap[data-item="'+id+'"]').show();
        
        if (changeTitle == 'Y') {
            offerName = offers[id]["NAME"];
            document.title = offerName;
            $('h1').html(offerName);
        }
        
        if (offers[id]["MORE_PHOTO"]) {
            <?if ($arParams["FILTER_SKU_PHOTO"] == "Y"
//                    || $arParams["ADD_DETAIL_TO_SLIDER_SKU"] != "Y"
                    ) {?>
                $('.ax-element-slider-nav').slick('slickFilter','[data-item='+id+']');
                $('.ax-element-slider-main').slick('slickFilter','[data-item='+id+']');
                $($('.ax-element-slider-nav .slick-nav:not(.slick-cloned)')[0]).trigger('click');
            <?} else {?>
                $('.ax-element-slider-nav').slick('slickFilter','[data-main="default"]');
                $('.ax-element-slider-main').slick('slickFilter','[data-main="default"]');
                if ($('.ax-element-slider-nav .slick-nav:not(.slick-cloned)[data-item='+id+']').length)
                    $($('.ax-element-slider-nav .slick-nav:not(.slick-cloned)[data-item='+id+']')[0]).trigger('click');
                else
                    $($('.ax-element-slider-nav .slick-nav:not(.slick-cloned)')[0]).trigger('click');
            <?}?>
        }
        
        $('.bxr-offer-avail-wrap').hide();
        $('.bxr-main-avail-wrap').hide();
        $('.bxr-offer-avail-wrap[data-item="'+id+'"]').show();
        
        $(".bxr-sku-select-chosen-inner").html(inner);
        $('.bxr-sku-select-items').hide();
        
        selectParams = $('.bxr-sku-select-chosen-inner').find('.bxr-sku-prop-brackets').html();
        
        if(selectParams != undefined && selectParams !="") {
            formRequestMsg = "<?=GetMessage('OFFER_REQUEST_MSG')?>";
            formRequestMsg = formRequestMsg.replace("#PARAMS#", ": " + selectParams);
        }
        else {
            formRequestMsg = "<?=GetMessage('TRADE_REQUEST_MSG')?>";
            formRequestMsg = formRequestMsg.replace("#PARAMS#", "");
        }
                
        formRequestMsg = formRequestMsg.replace("#TRADE_NAME#",'<?=htmlspecialchars($arResult['NAME'],ENT_QUOTES, SITE_CHARSET)?>');
    });

    $(document).on("click", ".bxr-sku-icons-item", function(event, isTriger) {
        $(".bxr-sku-icons-item").find(".bxr-offers-ico").removeClass('bxr-border-color');
        $(this).find(".bxr-offers-ico").addClass('bxr-border-color');
        var id = $(this).data("pid");
        current_offer_id = id;
        setBasketIds(id);
        setSkuUrl(id);
        BX.onCustomEvent('onCatalogStoreProductChange', [id]);
        <?if( $arParams["HIDE_OFFERS_LIST"] != 'Y') {?>                        
            foundMsg = "<?=GetMessage("OFFERS_FOUND")?>";                        
            watch = "<a href='#offers' data-scroll='bxr-offers' class='bxr-offers-scroll text-link scroll-navigate'><?=GetMessage("LOOK_OFFERS_BIG")?></a>";
            watch += " <?=GetMessage("OFFERS_FOUND_1")?>";

            closeicon = '<a class="popup-window-close-icon" href="" style="right: 12px; top: 8px;"></a>';
            $(".offers-cnt").html(closeicon+watch);
            if(!isTriger)
                $(".offers-cnt").show();
        <?}?>
        
        $('.bxr-detail-offers tr').removeClass('avail');
        $('.bxr-detail-offers tr[data-offer-id="'+id+'"]').addClass('avail');

        $('.bxr-basket-action').show();
        $('#bxr-market-price-wrap .bxr-product-price-wrap').hide();
        $('#bxr-market-price-wrap .bxr-offer-price-wrap').hide();
        $('#bxr-market-price-wrap .bxr-offer-price-wrap[data-item="'+id+'"]').show();
        
        if (changeTitle == 'Y') {
            offerName = offers[id]["NAME"];
            document.title = offerName;
            $('h1').html(offerName);
        }

        if (offers[id]["MORE_PHOTO"]) {
            <?if ($arParams["FILTER_SKU_PHOTO"] == "Y"
//                    || $arParams["ADD_DETAIL_TO_SLIDER_SKU"] != "Y"
                    ) {?>
                $('.ax-element-slider-nav').slick('slickFilter','[data-item='+id+']');
                $('.ax-element-slider-main').slick('slickFilter','[data-item='+id+']');
                $($('.ax-element-slider-nav .slick-nav:not(.slick-cloned)')[0]).trigger('click');
            <?} else {?>
                $('.ax-element-slider-nav').slick('slickFilter','[data-main="default"]');
                $('.ax-element-slider-main').slick('slickFilter','[data-main="default"]');
                if ($('.ax-element-slider-nav .slick-nav:not(.slick-cloned)[data-item='+id+']').length)
                    $($('.ax-element-slider-nav .slick-nav:not(.slick-cloned)[data-item='+id+']')[0]).trigger('click');
                else
                    $($('.ax-element-slider-nav .slick-nav:not(.slick-cloned)')[0]).trigger('click');
            <?}?>
        }
        
        $('.bxr-offer-avail-wrap').hide();
        $('.bxr-main-avail-wrap').hide();
        $('.bxr-offer-avail-wrap[data-item="'+id+'"]').show();
                     

        selectParams = $(this).find('.bxr-sku-prop-brackets').html();
        
        if(selectParams != undefined && selectParams !="") {
            formRequestMsg = "<?=GetMessage('OFFER_REQUEST_MSG')?>";
            formRequestMsg = formRequestMsg.replace("#PARAMS#", ": +" + selectParams);
        }
        else {
            formRequestMsg = "<?=GetMessage('TRADE_REQUEST_MSG')?>";
            formRequestMsg = formRequestMsg.replace("#PARAMS#", "+");
        }
                
        formRequestMsg = formRequestMsg.replace("#TRADE_NAME#",'<?=htmlspecialchars($arResult['NAME'],ENT_QUOTES, SITE_CHARSET)?>');
        
    });
    
    $(document).on('click', '.bxr-offers-scroll', function() {
        $('.bxr-detail-tab').hide();
        $('.bxr-detail-tabs li').removeClass('active');
        $('.bxr-detail-tabs li[data-tab="offers"]').addClass('active');
        $('.bxr-detail-tab[data-tab="offers"]').show();
        window.BXReady.scrollTo('.bxr-detail-tab .avail');
        return false;
    });
    
    $(document).on('click', '.offers-cnt .popup-window-close-icon', function() {
        $('.offers-cnt').hide();
        return false;
    })
</script>
