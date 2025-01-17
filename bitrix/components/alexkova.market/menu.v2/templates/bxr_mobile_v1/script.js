(function(){

    jQuery.extend( jQuery.easing,
    {
        easeOutBounce: function (x, t, b, c, d) {
            if ((t/=d) < (1/2.75)) {
                return c*(7.5625*t*t) + b;
            } else if (t < (2/2.75)) {
                return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;
            } else if (t < (2.5/2.75)) {
                return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;
            } else {
                return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;
            }
        },
        easeOutElastic: function (x, t, b, c, d) {
            var s=1.70158;var p=0;var a=c;
            if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
            if (a < Math.abs(c)) { a=c; var s=p/4; }
            else var s = p/(2*Math.PI) * Math.asin (c/a);
            return a*Math.pow(2,-10*t) * Math.sin( (t*d-s)*(2*Math.PI)/p ) + c + b;
        },
        easeOutExpo: function (x, t, b, c, d) {
            return (t==d) ? b+c : c * (-Math.pow(2, -10 * t/d) + 1) + b;
        }
    });

    BXReadyMenu = {

        menuWidth: 240,
        menuLeft: 240,
        state: 'close',

        resize: function(){

            BXReadyMenu.menuWidth = $(window).width();
            BXReadyMenu.menuLeft = BXReadyMenu.menuWidth;

            $('#bxr-multilevel-menu').width(BXReadyMenu.menuLeft);
            $('.bxr-mobile-push-menu-content').width(BXReadyMenu.menuLeft);
            $('.bxr-mobile-push-menu #bxr-mobile-menu-body').width(BXReadyMenu.menuLeft);

        },

        init: function(){

            BXReadyMenu.resize();

            maxHeight = 0;
            $('.bxr-mobile-push-menu ul').each(function(){

                if ($(this).height()>maxHeight){
                    maxHeight = $(this).height();
                }
            });

            $('.bxr-mobile-push-menu-content').height($(document).height());

            $('.bxr-mobile-push-menu ul').height(maxHeight).width(BXReadyMenu.menuLeft);
            $('.bxr-mobile-push-menu-content').css('margin-left', '-' + BXReadyMenu.menuLeft+'px');
        },

        showChildren: function (parentId){

            menuItem = $('ul[data-parent='+parentId+']');
            menuItem.css({'position':'absolute','right':'-'+BXReadyMenu.menuLeft+'px','top':'0', 'display': 'block', 'z-index':5});
            menuItem.animate({'right':'0'}, 200, 'easeOutExpo');
        },

        closeChildren: function (parentId){
            menuItem = $('ul[data-parent='+parentId+']');
             menuItem.css({'z-index':0});
            menuItem.animate({'right':'-'+BXReadyMenu.menuWidth+'px'}, 200, 'easeOutExpo');
        },

        openMenu: function(){

            BXReadyMenu.init();

            //$('html').css({'width':$('html').width()+'px'});

            $('.bxr-mobile-push-menu .bxr-mobile-menu-button-close').css('display', 'block');
            $('.bxr-mobile-push-menu .bxr-mobile-menu-button-open').css('display', 'none');

            $('.bxr-mobile-push-menu-content').animate({'margin-left': '0px'}, 300, 'easeOutExpo');
            $('.bxr-mobile-push-menu').addClass('active');

            BXReadyMenu.state = 'open';
            BXReadyMenu.closeSlides('pull');
        },

        closeMenu: function(){
            $('.bxr-mobile-push-menu .bxr-mobile-menu-button-close').css('display', 'none');
            $('.bxr-mobile-push-menu .bxr-mobile-menu-button-open').css('display', 'block');


            $('.bxr-mobile-push-menu-content').animate({'margin-left':'-'+BXReadyMenu.menuLeft+'px'}, 300, 'easeOutExpo', function(){
                $('html').removeClass('bxr-mobile-menu-content');
                $('html').css({'width':'auto'});
                $('.bxr-mobile-push-menu').removeClass('active');
            });

            BXReadyMenu.state = 'close';

        },

        closeSlides: function(elementID){
            $('.bxr-mobile-slide').each(function(){
                if ($(this).attr('id') != elementID){
                    $(this).slideUp(100);
                }
            });

            if (elementID != 'pull'){
                BXReadyMenu.closeMenu();
            }

        },

        activateButton: function(button){

            target = $(button).data('target');

            $('.bxr-mobile-push-menu-header .bxr-mobile-menu-button').each(function(){

                if ($(this).data('target') != target){
                    $(this).removeClass('bxr-mobile-menu-button-active');
                    if (!$(this).hasClass('bxr-mobile-menu-button-close'))
						$(this).removeClass('bxr-color');
                }
            });

            if ($(button).hasClass('bxr-mobile-menu-button-active')){
                $(button).removeClass('bxr-mobile-menu-button-active');
                $(button).removeClass('bxr-color');
            }else{
                $(button).addClass('bxr-mobile-menu-button-active');
                $(button).addClass('bxr-color');
            }
        }
    }

    $(document).ready(function(){
        $(document).on(
            'click',
            '.bxr-mobile-push-menu .bxr-mobile-menu-button-open',
            function(){
                BXReadyMenu.openMenu();
            }
        );

        $(document).on(
            'click',
            '.bxr-mobile-push-menu .bxr-mobile-menu-button-close',
            function(){
                BXReadyMenu.closeMenu();

            }
        );

        $(document).on(
            'click',
            '.bxr-mobile-push-menu .bxr-mobile-menu-button-phone',
            function(){

                id = 'bxr-mobile-phone';
                BXReadyMenu.closeSlides(id);
                $('#'+id).slideToggle(400);

                BXReadyMenu.activateButton(this);

            }
        );

        $(document).on(
            'click',
            '.bxr-mobile-push-menu .bxr-mobile-menu-button-contacts',
            function(){
                id = 'bxr-mobile-contacts';
                BXReadyMenu.closeSlides(id);
                $('#'+id).slideToggle(400);

                BXReadyMenu.activateButton(this);
            }
        );

        $(document).on(
            'click',
            '.bxr-mobile-push-menu .bxr-mobile-menu-button-search',
            function(){
                id = 'bxr-mobile-search';
                BXReadyMenu.closeSlides(id);
                $('#'+id).slideToggle(400);

                BXReadyMenu.activateButton(this);
            }
        );

        $(document).on(
            'click',
            '.bxr-mobile-push-menu .bxr-mobile-menu-button-user',
            function(){
                id = 'bxr-mobile-user';
                BXReadyMenu.closeSlides(id);
                $('#'+id).slideToggle(400);

                BXReadyMenu.activateButton(this);

            }
        );

        $(document).on(
            'click',
            '.bxr-mobile-push-menu #bxr-multilevel-menu div.parent',
            function(){

                state = $(this).attr('menu-state');
                parentId = $(this).data('parent');
                BXReadyMenu.showChildren(parentId);
            }
        );

        $(document).on(
            'click',
            '.bxr-mobile-push-menu #bxr-multilevel-menu div.child',
            function(){

                state = $(this).attr('menu-state');
                parentId = $(this).data('parent');
                BXReadyMenu.closeChildren(parentId);

            }
        );

        $(window).resize(
            function(){

                if (BXReadyMenu.state == 'open') BXReadyMenu.resize();

            }
        );



    });
})( jQuery );




