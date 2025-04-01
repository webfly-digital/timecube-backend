$(document).ready(function(){
    
    window.BXReady.Market.MenuLeft =  {
        
        slideAnimation: 300,
          
        init: function(){           
            
            $(document).on('click', ".bxr-left-menu .fa-angle-right, .bxr-left-menu .fa-angle-down, .bxr-left-menu .bxr-hover-link", function(e){
                if ($(this).closest('li').hasClass('bxr-left-pt-with-children')) {
                    e.preventDefault();
                    window.BXReady.Market.MenuLeft.showMenuUl(this);
                    return false;
                }
            });
            
            $(document).on("click", ".bxr-catalog-link-text", function() {
                link = $(this).closest('a').href();
                location.href = link;
            })
            
            $(document).on("mouseover", ".bxr-left-pt-with-children", function() {
                window.BXReady.Market.MenuLeft.showMenuUl($(this).children('a'));
            })
            
            $(document).on("mouseout", ".bxr-left-pt-with-children", function() {
                $(this).removeClass('bxr-color');;
            })
            
            $(document).on("click", ".bxr-hover-fdesc", function() {
                desc = $('.bxr-left-panel-hover .bxr-hover-fdesc').prev('li.bxr-hover-desc');
                if ($(this).data("state") == "minimized") {
                    $(this).data("state", "maximized");
                    $(this).html('<b>'+$(this).data("min")+'</b><i class="fa fa-chevron-up chevron-show"></i>');
                    desc.css("max-height", "none");
                } else {
                    $(this).data("state", "minimized");
                    $(this).html('<b>'+$(this).data("max")+'</b><i class="fa fa-chevron-down chevron-show"></i>');
                    desc.css("max-height", "54px");
                }
            })
        },
                
        showMenuUl: function(e) {
//            :not(.bxr-left-menu-selected)
            $(e).closest("ul").find("li").removeClass('bxr-color');
            $(e).closest("li").addClass('bxr-color');
            hoverContent = $(e).closest("li").find('.bxr-hover-link-content').html();
            $('.bxr-left-panel-hover .bxr-content-block .scroll-content').html(hoverContent);
            
//            $(hoverBlock).show();
            $(hoverBlock).find('.bxr-content-block').show();
            $(hoverBlock).fadeTo(0,1);
            $(hoverBlock).css("width", hoverWidth+"px");
            hoverVisible = true;
            desc = $('.bxr-left-panel-hover .bxr-hover-desc>div');
            dHeight = parseInt(desc.css("height"));
            if (dHeight > 56) 
                $('.bxr-hover-fdesc').css("display", "table");
            else 
                $('.bxr-hover-fdesc').css("display", "none");
        }
    }

    $(document).ready(function(){
        window.BXReady.Market.MenuLeft.init();
    });
});