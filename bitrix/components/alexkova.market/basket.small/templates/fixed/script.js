$(document).ready(function(){

    window.BXReady.Market.Basket = {

        ajaxUrl: '/ajax/basket_action.php',
        template: '',
        basketTopPosition: 230,
        delayWindow: false,
        precisionFactor: Math.pow(10, 6),
        
        list: {},

        newQty: function(item, qty){

            basket = this;

            if (basket.ajaxUrl.length <= 0) return;

            data ='action=newqty&template='+basket.template+'&quantity='+qty+'&item='+item+'&ajaxbuy=yes&rg='+Math.random();
            $.ajax({
                url: basket.ajaxUrl+'?'+data,
                success: function(data){

                    basket.refresh(true, data);
                    BXReady.closeAjaxShadow('bxr-basket-body-shadow');
                }
            });

        },
        
        changeQtyValue: function(qty, coef, maxQty, type) {
            if(type!=="inc" && type!=="dec" && type!=="arb") return false;
            if(isNaN(coef)) coef = 1;
            coef = Math.round(coef * basket.precisionFactor) / basket.precisionFactor;
            if(isNaN(qty) || qty<0) qty = coef;
            
            if(type === "inc") {
                newQty = qty + coef;
                newQty = Math.round(newQty * basket.precisionFactor) / basket.precisionFactor;
            } else if (type === "dec") {
                newQty = qty - coef;
                newQty = Math.round(newQty * basket.precisionFactor) / basket.precisionFactor;
            } else if (type === "arb") {
                newQty = (Math.ceil(qty/coef)) * coef;
                newQty = Math.round(newQty * basket.precisionFactor) / basket.precisionFactor;
            }
            
            if (maxQty>0 && newQty > maxQty)
                newQty = maxQty;
            if ((newQty < coef && coef<=maxQty) || newQty === 0)
                newQty = coef;
            if (newQty < coef && coef>maxQty)
                newQty = maxQty;
            
            return newQty;
        },

        addItem: function(form, delay, favor){

            basket = this;

            if (basket.ajaxUrl.length <= 0) return;

            data = form.serialize()+'&ajaxbuy=yes&template='+basket.template+'&rg='+Math.random();
            if (delay){
                valueDelay = form.children('input[name=delay]').val();

                if (valueDelay == 'yes'){
                    data = data + '&delay='+valueDelay;
                    form.children('input[name=delay]').val('no');
                }else{
                    form.children('input[name=delay]').val('yes');
                }

            }
            
            if (favor) {
                valueFavor = form.children('input[name=favor]').val();

                if (valueFavor == 'yes'){
                    data = data + '&favor='+valueFavor;
                    form.children('input[name=favor]').val('no');
                }else{
                    form.children('input[name=favor]').val('yes');
                }
            }
            
            notAllProp  = false;
            bProp = {                
                BASKET_PROPS: {}
            };
            if (!delay && !favor) {
                if ($('.bxr-bprop-required').length) {
                    $('.bxr-bprop-required').each(function() {                        
                        prop = $(this);
                        container = prop.closest('.bxr-bprop-value');
                        pVal = prop.val();
                        pCode = prop.data("code");
                        pName = prop.data("name");
                        pSort = prop.data("sort");
                        if (pVal == "false") {
                            container.addClass('wrong-bprop');
                            BXReady.closeAjaxShadow('body-shadow');
                            notAllProp = true;
                        };
                        bProp["BASKET_PROPS"][pCode] = {
                            NAME: encodeURIComponent(pName),
                            CODE: pCode,
                            SORT: pSort,
                            VALUE: encodeURIComponent(pVal),
                        };
                    })
                };
                
                $('.bxr-bprop-optional').each(function() {                        
                    prop = $(this);
                    pVal = prop.val();
                    pCode = prop.data("code");
                    pName = prop.data("name");
                    pSort = prop.data("sort");
                    bProp["BASKET_PROPS"][pCode] = {
                        NAME: encodeURIComponent(pName),
                        CODE: pCode,
                        SORT: pSort,
                        VALUE: encodeURIComponent(pVal),
                    };
                })
            }
            
            
            if (!notAllProp) {

            $.ajax({
                url: basket.ajaxUrl+'?'+data,
                    data: bProp,
                success: function(data){
                    basket.refresh(true, data);

                    if (basket.delayWindow != true && delay || favor){


                    }
                    else {
                        basket.setAnalyticsDataLayer(form, 'addToCart');

                        BXReady.closeAjaxShadow('body-shadow');

                        BXReady.basketPopup = BX.PopupWindowManager.create("basketPopup", null, {
                                autoHide: true,
                                //	zIndex: 0,
                                offsetLeft: 0,
                                offsetTop: 0,
                                overlay : true,
                                draggable: {restrict:true},
                                closeByEsc: true,
                                closeIcon: { right : "12px", top : "10px"},
                                titleBar: {content: BX.create("span", {html: "<div>"+BX.message('setItemAdded2BasketTitle')+"</div>"})},
                                content: '',
                                events: {
                                    onAfterPopupShow: function()
                                    {
                                        this.setContent(BX("bxr-basket-popup"));

                                        $(document).on(
                                            'click',
                                            '#continue-buy',
                                            function(){
                                                BXReady.basketPopup.close()
                                            }
                                        );
                                    }
                                }
                            });
                            BXReady.basketPopup.show();
                        }


                }
            });
            }
        },
        setAnalyticsDataLayer: function(form, action){

            let info = {}, item = {};
            item.name = form.children('input[name=name]').val();
            item.id = form.children('input[name=id]').val();
            item.price = form.children('input[name=price]').val();
            item.category = form.children('input[name=category]').val();
            item.brand = form.children('input[name=brand]').val();
            item.quantity = form.children('input[name=quantity]').val();

            switch (action)
            {
                case 'addToCart':
                    info = {
                        'event': 'addToCart',
                        'ecommerce': {
                            'currencyCode': 'RUB',
                            'add': {
                                'products': [{
                                    'name': item.name || '',
                                    'id': item.id || '',
                                    'price': item.price || 0,
                                    'brand': item.brand || '',
                                    'category': item.category || '',
                                    //'variant': item.variant || '',
                                    'quantity': item.quantity
                                }]
                            }
                        }
                    };
                    break;
            }

            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push(info);
        },
        deleteItem: function(item){
            basket=this;

            if (basket.ajaxUrl.length <= 0) return;

            data ='action=delete&template='+basket.template+'&item='+item+'&ajaxbuy=yes&rg='+Math.random();

            $.ajax({
                url: basket.ajaxUrl+'?'+data,
                success: function(data){

                    basket.refresh();
                    BXReady.closeAjaxShadow('bxr-basket-body-shadow');
                }
            });
        },

        delayItem: function(item){
            basket = this;
            if (basket.ajaxUrl.length <= 0) return;
            data ='action=delay&template='+basket.template+'&item='+item+'&ajaxbuy=yes&rg='+Math.random();

            $.ajax({
                url: basket.ajaxUrl+'?'+data,
                success: function(data){

                    BXReady.closeAjaxShadow('bxr-basket-body-shadow');
                    /*$('.icon-delay-transparent-notext').removeClass('ajax-shadow');
                     $('.icon-delay-transparent-notext').removeClass('ajax-shadow-r');*/
                    basket.refresh();
                }
            });
        },

        delayToCart: function(item){
            basket = this;
            if (basket.ajaxUrl.length <= 0) return;
            data ='action=back&template='+basket.template+'&item='+item+'&ajaxbuy=yes&rg='+Math.random();
            $.ajax({
                url: basket.ajaxUrl+'?'+data,
                success: function(data){
                    basket.refresh();
                    BXReady.closeAjaxShadow('bxr-basket-body-shadow');
                }
            });
        },

        animateShowIndicator: function(element,sClass){
            element.css('opacity', '0').addClass(sClass+'-active').animate({'opacity': '1'}, 1000, "easeOutExpo");
        },

        animateHideIndicator: function(sClass){
            this.css('opacity', '0');
            this.addClass('sClass')
        },

        refreshData: function(){
            if (delayClick)
                $('.tab-delay').click();
            else 
                $('.tab-basket').click();
            
            basket = this;

            $('#bxr-basket-body').html($('#basket-body-content').html());
            $('#bxr-delay-body').html($('#delay-body-content').html());
            $('#bxr-favor-body').html($('#favor-body-content').html());

            $('#bxr-basket-mobile-container').html($('#bxr-basket-content #bxr-basket-body-mobile').html());
            $('#bxr-favor-mobile-container').html($('#bxr-basket-content  #bxr-favor-body-mobile').html());



            basket.list = JSON.parse($('#bxr-basket-data').html());
            
            $('.bxr-indicator-item-favor').data("favor", 0);
            
            if (basket.list.FAVOR.length > 0){
                a = basket.list.FAVOR;
                
                $('.bxr-counter-favor').html(basket.list.FAVOR.length);
                basket.animateShowIndicator($('.bxr-counter-favor'),'bxr-counter');
                
                for (var i = 0; i < a.length; i++){
                    value = basket.list.FAVOR[a[i]];
                    $('.bxr-indicator-item-favor[data-item='+a[i]+']').each(function() {
                        if (!$(this).hasClass('bxr-indicator-item-active'))
                            basket.animateShowIndicator($(this),'bxr-indicator-item');
                    });
                    $('.bxr-indicator-item-favor[data-item='+a[i]+']').data("favor", 1);
                }
            }else{
                $('.bxr-counter-favor').removeClass('bxr-counter-active'); 
                $('.bxr-indicator-item-favor').removeClass('bxr-indicator-item-active');
            }
            
            $('.bxr-indicator-item-favor').each(function() {
                if ($(this).data('favor') == 0)
                    $(this).removeClass('bxr-indicator-item-active');
            });
            
            $('.bxr-indicator-item').data("basket", 0);
            if (basket.list.ITEMS.length > 0){
                a = basket.list.ITEMS;

                for (var i = 0; i < a.length; i++){
                    value = basket.list.ALL[a[i]];
                    
                    $('.bxr-indicator-item-basket[data-item='+a[i]+']').each(function() {
                        if (!$(this).hasClass('bxr-indicator-item-active'))
                            basket.animateShowIndicator($(this),'bxr-indicator-item');
                    });
                    $('.bxr-counter-item-basket[data-item='+a[i]+']').html(value);
                    $('.bxr-indicator-item-basket[data-item='+a[i]+']').data("basket", 1);
                }
            }else {
                $('.bxr-counter-item-basket').removeClass('bxr-indicator-item-active');
            }
            
            $('.bxr-indicator-item-basket').each(function() {
                if ($(this).data('basket') == 0)
                    $(this).removeClass('bxr-indicator-item-active');
            });

            if (Object.keys(basket.list.ALL).length > 0) {
                $('.bxr-counter-basket').html(Object.keys(basket.list.ALL).length);
                basket.animateShowIndicator($('.bxr-counter-basket'),'bxr-counter');
            } else {
                $('.bxr-counter-basket').html(0);
                $('.bxr-counter-basket').removeClass('bxr-counter-active');                
            }

            var panels = ['basket', 'delay', 'favor'];

            for (i=0; i<panels.length; i++){

                $('#bxr-basket-row .bxr-indicator-'+panels[i]).html($('#bxr-basket-content #bxr-indicator-'+panels[i]+'-new').html());
            }

            window.BXReady.Market.setPriceCents();
            basket.autoSetVertical();
        },

        refresh: function(notRequest, dataRefresh){
            basket = this;

            if (notRequest === true){
                $('#bxr-basket-content').html(dataRefresh);
                basket.refreshData();
            }else{
                if (basket.ajaxUrl.length <= 0) return;

                data = 'ajaxbuy=yes&template='+basket.template+'&rg='+Math.random();
                $.ajax({
                    url: basket.ajaxUrl+'?'+data,
                    success: function(data){

                        $('#bxr-basket-content').html(data);
                        basket.refreshData();
                    }
                });
            }

        },


        basketButtonsInit: function(){
            basket = this;
            $('form.bxr-basket-action').submit(function(){
                window.BXReady.showAjaxShadow('body','body-shadow');
                basket.addItem($(this));
                return false;
            });

            $(document).on(
                'click',
                'form.bxr-basket-action .bxr-basket-add',
                function(){
                    window.BXReady.showAjaxShadow('body','body-shadow');
                    basket.addItem($(this).parent('form'));
                    return false;
                }
            );

            $(document).on(
                'click',
                'form.bxr-basket-action .bxr-basket-delay',
                function(){
                    basket.addItem($(this).parent('form'), true);
                    return false;
                }
            );
    
            $(document).on(
                'click',
                'form.bxr-basket-action .bxr-basket-favor',
                function(){
                    basket.addItem($(this).parent('form'), false, true);
                    return false;
                }
            );
    
            $(document).on(
                'click',
                'form.bxr-basket-action .bxr-basket-favor-delete',
                function(){
                    basket.addItem($(this).parent('form'), false, true);
                    return false;
                }
            );

            $(document).on(
                'click',
                '#bxr-basket-body .icon-button-delete, #bxr-basket-mobile-container .icon-button-delete',
                function(){
                    itemID = $(this).data('item');
                    BXReady.showAjaxShadow('#bxr-basket-body','bxr-basket-body-shadow');
                    basket.deleteItem(itemID);
                }
            );

            $(document).on(
                'click',
                '#bxr-basket-body .icon-button-delete, #bxr-basket-mobile-container .icon-button-delete',
                function(){
                    itemID = $(this).data('item');
                    BXReady.showAjaxShadow('#bxr-delay-body','bxr-basket-body-shadow');
                    basket.deleteItem(itemID);
                }
            );

            $(document).on(
                'click',
                '#bxr-basket-body .icon-button-delay, #bxr-basket-mobile-container .icon-button-delay',
                function(){
                    itemID = $(this).data('item');
                    BXReady.showAjaxShadow('#bxr-basket-body','bxr-basket-body-shadow');
                    basket.delayItem(itemID);
                }
            );

            $(document).on(
                'click',
                '#bxr-basket-body .icon-button-cart, #bxr-basket-mobile-container .icon-button-cart, #bxr-favor-body .icon-button-cart, #bxr-favor-mobile-container .icon-button-cart',
                function(){
                    itemID = $(this).data('item');
                    BXReady.showAjaxShadow('#bxr-basket-body','bxr-basket-body-shadow');
                    basket.delayToCart(itemID);
                }
            );

            $(document).on(
                'click',
                '.bxr-basket-indicator',
                function(){

                    var panels = ['bxr-basket-body', 'bxr-delay-body', 'bxr-compare-body', 'bxr-favor-body'];

                    itemID = $(this).data('child');
                    for (i=0; i<panels.length; i++){

                        if (panels[i] != itemID){
                            $('#'+panels[i]).hide();
                            $('#'+panels[i]).parent().removeClass('active');
                        }
                    }
                    $('#'+itemID).css('opacity', '0');
                    $('#'+itemID).fadeToggle(200, function(){
                        basket.autoSetWindow($('#'+itemID));
                        $('#'+itemID).animate({'opacity': '1'},100,'');
                        if ($('#'+itemID).css('display') == 'block'){
                            $('#'+itemID).parent().addClass('active');
                        }else{
                            $('#'+itemID).parent().removeClass('active');
                        }
                    });
                }
            );

            $(document).on(
                'click',
                '.bxr-basket-tab',
                function() {
                    var tabCode = $(this).data('tab');
                    if (tabCode == 'delay') {
                        delayClick = true;
                    } else {
                        delayClick = false;
                    };
                    $('.bxr-basket-tab').removeClass('active');
                    $('.bxr-basket-tab[data-tab='+tabCode+']').addClass('active');
                    $('.bxr-basket-tab-content').removeClass('active');
                    $('.bxr-basket-tab-content[data-tab="'+tabCode+'"]').addClass('active');
                }
            );

            $(document).on(
                'click',
                '.bxr-quantity-button-minus',
                function(){
                    itemID = parseInt($(this).data('item'));
                    operation = $(this).data('operation');
                    coef = parseFloat($(this).data('ratio'));
                    if (!coef || isNaN(coef)) coef = 1;
                    maxQty = coef;

                    startQtyValue = parseFloat($(this).parent('.bxr-basket-group').children('.bxr-quantity-text[data-item='+itemID+']').val());
                    newQty = basket.changeQtyValue(startQtyValue, coef, 0, "dec");

                    if (operation === 'auto_save'){
                        BXReady.showAjaxShadow('#bxr-basket-body','bxr-basket-body-shadow');
                        basket.newQty(itemID, newQty)
                    }

                    $('.bxr-quantity-text[data-item='+itemID+']').val(newQty);
                }
            );

            $(document).on(
                'click',
                '.bxr-quantity-button-plus',
                function(){
                    itemID = parseInt($(this).data('item'));
                    operation= $(this).data('operation');
                    coef = parseFloat($(this).data('ratio'));
                    if (!coef || isNaN(coef)) coef = 1;

                    maxQty = parseFloat($(this).data('max'));
                    if (isNaN(maxQty)) maxQty = 0;

                    startQtyValue = parseFloat($(this).parent('.bxr-basket-group').children('.bxr-quantity-text[data-item='+itemID+']').val());
                    newQty = basket.changeQtyValue(startQtyValue, coef, maxQty, "inc");

                    if (operation === 'auto_save'){
                        BXReady.showAjaxShadow('#bxr-basket-body','bxr-basket-body-shadow');
                        basket.newQty(itemID, newQty);
                    }
                    
                    $('.bxr-quantity-text[data-item='+itemID+']').val(newQty);
                }
            );

            $(document).on(
                'focusout',
                '.bxr-quantity-text',
                function(){
                    itemID = parseInt($(this).data('item'));
                    operation= $(this).parent('.bxr-basket-group').children('.bxr-quantity-button-plus[data-item='+itemID+']').data('operation');
                    coef = parseFloat($(this).parent('.bxr-basket-group').children('.bxr-quantity-button-plus[data-item='+itemID+']').data('ratio'));
                    maxQty = parseFloat($(this).parent('.bxr-basket-group').children('.bxr-quantity-button-plus[data-item='+itemID+']').data('max'));
                    
                    if (isNaN(coef)) coef = 1;
                    if (isNaN(maxQty)) maxQty = 0;

                    startQtyValue = parseFloat($(this).val());
                    newQty = basket.changeQtyValue(startQtyValue, coef, maxQty, "arb");

                    if (operation === 'auto_save'){
                        basket.newQty(itemID, newQty);
                    }

                    $('.bxr-quantity-text[data-item='+itemID+']').val(newQty);
                }
            );

            $(document).mouseup(function (e) {
                var container = 'div.bxr-basket-row';
                var other = '#bxr-basket-body-shadow';
                var hideContainer = $('div[data-group=basket-group]');

                if ($(e.target).parents(container).length === 0 && $(other).has(e.target).length === 0){
                    hideContainer.hide();
                    $('#bxr-basket-row > div').removeClass('active');
                }
            });

            $(document).on(
                'click',
                '.bxr-close-basket',
                function(){
                    var hideContainer = $('div[data-group=basket-group]');
                    hideContainer.hide(200);
                    $('#bxr-basket-row > div').removeClass('active');
                }
            );

            $(document).scroll(function(){
                basket.autoSetVertical();
                basket.autoSetButtonPosition();
            });

            $(window).resize(function(){
                basket.autoSetVertical();
                basket.autoSetButtonPosition();
            });

        },

        autoSetVertical: function(){
            if ($('#bxr-basket-body').css('display') == 'block'){
                basket.autoSetWindow($('#bxr-basket-body'));
            }
            if ($('#bxr-delay-body').css('display') == 'block'){
                basket.autoSetWindow($('#bxr-delay-body'));
            }
            if ($('#bxr-compare-body').css('display') == 'block'){
                basket.autoSetWindow($('#bxr-compare-body'));
            }
        },

        autoSetButtonPosition: function(){
            windowHeight = $(window).innerHeight();

            if (basket.basketTopPosition > windowHeight - $("#bxr-basket-row").height()){
                $("#bxr-basket-row").css('top','90px');
            }else{
                $("#bxr-basket-row").css('top',basket.basketTopPosition+'px');
            }
        },

        autoSetWindow: function(bodyContainer){
            windowHeight = $(window).innerHeight();
            heightContainer = bodyContainer.height();

            flagOptimize = false;

            if (bodyContainer.children('.basket-body-table').each(function(){
                flagOptimize = true;
            }));

            if (flagOptimize){

                maxHeight = windowHeight-250;

                currentHeight = $(bodyContainer.children('.basket-body-table')).children('table').height();

                if (maxHeight>currentHeight) maxHeight = currentHeight;
                
                $(bodyContainer.children('.basket-body-table')).css({'max-height': maxHeight+'px', 'height':'auto'});
                heightContainer = bodyContainer.height();

                basketPositionTop = $('#bxr-basket-row').position().top;
                parentPositionTop = bodyContainer.parent().position().top - $('#bxr-basket-row').position().top;

                newPosition = parentPositionTop + basketPositionTop;
                if (heightContainer < 400){
                    newPosition = 90;
                }else{
                    newPosition = newPosition + 180;
                }
                bodyContainer.css('top', '-'+newPosition + 'px');

            }else{
                parentPositionTop = bodyContainer.parent().position().top - $('#bxr-basket-row').position().top;
                parentHeight =  bodyContainer.parent().height();
                containerHeight = bodyContainer.height();

                newPosition = Math.round((containerHeight - parentHeight)/2);
                if (newPosition>0){
                    bodyContainer.css('top', '-'+newPosition + 'px');
                }else{
                    bodyContainer.css('top', newPosition + 'px');
                }

            }
        },

        createMobileInfo: function(){
            $('#bxr-basket-mobile').html($('#bxr-mobile-content').html());
            $('#bxr-mobile-content').html('');
        },

        initMobile: function(){
            $(document).on(
                'click',
                '.bxr-counter-mobile',
                function(){
                    var panels = ['bxr-basket-mobile-container', 'bxr-favor-mobile-container'];

                    itemID = $(this).data('child');

                    for (i=0; i<panels.length; i++){

                        if (panels[i] != itemID){
                            $('#'+panels[i]).hide();
                        }else{
                            parentRow = $('#'+panels[i]).parents('div.row');
                            $('#'+panels[i]).width(parentRow.width()-30);

                            $('#'+panels[i]).slideToggle(200);

                        }
                    }

                }
            );

            $(document).on(
                'click',
                '.bxr-close-basket-mobile',
                function(){
                    var panels = ['bxr-basket-mobile-container', 'bxr-favor-mobile-container'];

                    itemID = $(this).data('child');

                    for (i=0; i<panels.length; i++){
                        $('#'+panels[i]).slideUp(200);
                    }

                }
            );


        },

        init: function(){

            this.createMobileInfo();
            this.basketButtonsInit();
            this.initMobile();
            this.refresh();
            this.autoSetButtonPosition();


        }

    };

});