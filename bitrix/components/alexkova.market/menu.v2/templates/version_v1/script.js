$(document).ready(function(){
    
    window.BXReady.Market.Menu =  {

        init: function(){
            $('ul.bxr-flex-menu').each(function(){
                window.BXReady.Market.Menu.resize($(this));
                window.BXReady.Market.Menu.searchForm($(this));
            });

            $(window).resize(function() {
                $('ul.bxr-flex-menu').each(function(){
                    window.BXReady.Market.Menu.resize($(this));
                });
            });
        },

        showMenu: function(oneMenu) {
            oneMenu.css("visibility", "visible");
            oneMenu.css("overflow", "visible");
            oneMenu.data("visibility", "1");
        },

        resetChanges: function(oneMenu){
            oneMenu.find(">li").each(function(){
                $(this).css('display','block');
                $(this).removeClass("bxr-hover-menu-right");
                $(this).removeClass("bxr-last-element");
                if (!$(this).is('.other') && !$(this).is('.li-visible'))
                    $(this).css('width','auto');
            });

            oneMenu.find('.bxr-flex-menu-other').css('display', 'none');
        },
        
        howManyElementFit: function(oneMenu){
            var result = {};
            count = 0;
            sumWidth = 0;
            fullWidth = oneMenu.width();
            remaining = fullWidth;
           
            oneMenu.find('> li > a').each(function(){
                sumWidth += $(this).innerWidth();

                if (sumWidth<fullWidth) {
                    ++count;
                    remaining -= $(this).innerWidth();
                }
            });
            
            result.count = count;
            result.remaining = remaining;
            
            return result;
        },
        
        showOther: function(oneMenu){
            oneMenu.find('.bxr-flex-menu-other').css('display', 'block');
            oneMenu.find('.bxr-flex-menu-other').html('');
            
            addHTML = '<a href="#"><span class="fa fa-ellipsis-h"></span></a>';
            strAddUL = '<ul>';
                
            divMenu = "bxr-top-menu-other";
            if(oneMenu.data("style-menu") == "light")
                divMenu += " menu-arrow-top";
                
            liHover = "";
            switch (oneMenu.data("style-menu-hover")) {
                case "color": liHover = "bxr-color-flat bxr-bg-hover-dark-flat"; break;
                case "light": liHover = "bxr-children-color-hover"; break;
                case "dark": liHover = "bxr-dark-flat bxr-bg-hover-flat"; break;
            }
            
            liHoverSecected = "";
            switch (oneMenu.data("style-menu-hover")) {
                case "color": liHoverSecected = "bxr-color-dark-flat"; break;
                case "light": liHoverSecected = "bxr-children-color"; break;
                case "dark": liHoverSecected = "bxr-color-flat"; break;
            }
            
            var otherSelect = false;

            var i = 0;
            oneMenu.find(">li").not('.other').not('.li-visible').each(function(){
                if ($(this).data('visible') == 0) {
                    if($(this).attr("data-selected") == 1) {
                        strAddUL += '<li data-selected="1" class="l-2 ' + liHover + ' ' + liHoverSecected + '">'+$(this).children('a').get(0).outerHTML+'</li>';
                        otherSelect = true;
                    }
                    else
                        strAddUL += '<li class="l-2 ' + liHover + '">'+$(this).children('a').get(0).outerHTML+'</li>';
                    ++i;
                }
            });

            strAddUL += '</ul>';
            strAddUL = "<div class='" + divMenu + "'>"+strAddUL+"</div>";
                
            oneMenu.find('.bxr-flex-menu-other').html(addHTML+strAddUL);
            
            if(otherSelect)
                oneMenu.find('.bxr-flex-menu-other').addClass(liHoverSecected);
            else
                oneMenu.find('.bxr-flex-menu-other').removeClass(liHoverSecected);
                
            if(i == 0)
                oneMenu.find('.bxr-flex-menu-other').css('display', 'none');
        },
        
        hideNotFit: function(oneMenu){
            flagFull = false;
            howMany = window.BXReady.Market.Menu.howManyElementFit(oneMenu);
            li = oneMenu.find('>li').not(".other").not(".li-visible");
            liVisible = oneMenu.find('> li.li-visible');

            liVisible.each(function(indx, element){
                if(howMany.remaining > $(element).innerWidth()) {
                    howMany.remaining -= $(element).innerWidth();
                }
                else {
                    --howMany.count;
                    howMany.remaining += $(li[howMany.count]).innerWidth() - $(element).innerWidth();
                }
            });
            
            if(li.length>howMany.count && howMany.remaining<oneMenu.find('> li.other').innerWidth())
                --howMany.count;
            
            li.each(function(indx, element){
                if(indx < howMany.count){
                    $(element).data('visible', 1);
                    $(element).find(">a").data('visible', 1);
                }
                else {
                    if(!($(element).hasClass("li-visible"))) {
                        $(element).data('visible', 0);
                        $(element).find(">a").data('visible', 0);
                        $(element).css('display', 'none');
                        flagFull = true;
                    }
                }
            });
            
            li.filter(":visible").last().addClass("bxr-hover-menu-right");
            
            if(flagFull)
                window.BXReady.Market.Menu.showOther(oneMenu);
            
        },
        
        resizeWidth: function(oneMenu) {
            jsObj = oneMenu.get()[0];
            fullWidth = Math.floor(jsObj.getBoundingClientRect().width);
            li = oneMenu.find("> li:visible").not(".other").not(".li-visible");
            other = oneMenu.find('>li.other:visible, > li.li-visible:visible');
            widthOther = 0;
            widthLi = 0;
                        
            other.each(function(indx, element){
                widthOther += Math.ceil($(element).innerWidth());
            });
            
            li.each(function(indx, element){
                widthLi += Math.ceil($(element).innerWidth());
            });
            
            distributePX = fullWidth-widthLi-widthOther;          
            forWidthElements = Math.floor(distributePX/li.length);
            forWidthElement = fullWidth-widthOther;
            
            li.each(function(indx, element){
                $(element).width($(element).width() + forWidthElements + "px");
                forWidthElement -= $(element).innerWidth();
                
                if((li.length-1)==indx)
                    $(element).width($(element).width() + forWidthElement + "px");
            });
            
            oneMenu.find('>li').filter(":visible").last().addClass("bxr-last-element");
            
            window.BXReady.Market.Menu.showMenu(oneMenu);            
        },
        
        resize: function(oneMenu){

            oneMenu.css('width', '100%');
            
            var tWidth = window.outerWidth;            
            if(tWidth==0)
                tWidth =screen.width;
            
            if (tWidth <320 && oneMenu.css('display') != 'none') {
                return;
            }
            
            oldResize = oneMenu.data("resizeWidth");
            
            if(oldResize!=undefined && oldResize==oneMenu.width()) {
                return;
            }
        
            oneMenu.data("resizeWidth", oneMenu.width());            
            
            window.BXReady.Market.Menu.resetChanges(oneMenu);
            window.BXReady.Market.Menu.hideNotFit(oneMenu);
            
            if(oneMenu.data("stretch") == "Y")
                window.BXReady.Market.Menu.resizeWidth(oneMenu);
            else
                window.BXReady.Market.Menu.showMenu(oneMenu);
        },
        
        searchForm: function(oneMenu){
            obj = oneMenu.find('> li .fa-search').parents("a");
            
            liHoverSecected = "";
            switch (oneMenu.data("style-menu-hover")) {
                case "color": liHoverSecected = "bxr-color-dark-flat"; break;
                case "light": liHoverSecected = "bxr-children-color"; break;
                case "dark": liHoverSecected = "bxr-color-flat"; break;
            }
            
            obj.on('click', obj, function(){
                e = $(this).closest('.bxr-v-line_menu').next('.bxr-menu-search-line-container').find('.bxr-menu-search-line');

                if(e.is(":visible")) {
                    e.fadeOut();
                    oneMenu.find(">li.search").removeClass(liHoverSecected);
                }
                else {
                    e.fadeIn();
                    oneMenu.find(">li.search").addClass(liHoverSecected);
                }
                return false; 
            });            
        }
    }

    $(document).ready(function(){
        window.BXReady.Market.Menu.init();
    });
});