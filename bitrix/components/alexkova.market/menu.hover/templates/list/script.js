$(document).ready(function(){
    //$(".bxr-li-top-menu-parent:eq(1) > div").show();
    //$(".bxr-menuline > div").removeClass("hidden-sm hidden-xs");
  

    window.BXReady.Market.MenuHoverList =  {

        LG: 4,
        MD: 3,
        SM: 2,
        XS: 1,
        rScreen: 0,
        bIMG: "N",


        init: function() {
            var menu = $('.bxr-list-hover-menu');

            /*(menu.data("lg") != undefined && menu.data("lg")!="") ? this.LG = menu.data("lg") : this.LG=this.LG;
            (menu.data("md") != undefined && menu.data("md")!="") ? this.MD = menu.data("md") : this.MD=this.MD;
            (menu.data("sm") != undefined && menu.data("sm")!="") ? this.SM = menu.data("sm") : this.SM=this.SM;
            (menu.data("xs") != undefined && menu.data("xs")!="") ? this.XS = menu.data("xs") : this.XS=this.XS;
            (menu.data("bimg") != undefined) ? this.bIMG = menu.data("bimg") : this.bIMG=this.bIMG;*/
            

            var t = this;
            menu.each(function(indx, element){
                
                ($(element).data("lg") != undefined && $(element).data("lg")!="") ? t.LG = $(element).data("lg") : t.LG=t.LG;
                ($(element).data("md") != undefined && $(element).data("md")!="") ? t.MD = $(element).data("md") : t.MD=t.MD;
                ($(element).data("sm") != undefined && $(element).data("sm")!="") ? t.SM = $(element).data("sm") : t.SM=t.SM;
                ($(element).data("xs") != undefined && $(element).data("xs")!="") ? t.XS = $(element).data("xs") : t.XS=t.XS;
                ($(element).data("bimg") != undefined) ? t.bIMG = $(element).data("bimg") : t.bIMG=t.bIMG;
                
                //console.info();
                
                new_element = $(element);   
                if(t.bIMG != "N"  && $(element).data("bimgsrc") != undefined   && $(element).data("bimgsrc") !="") {
                    $(element).append("<div class='col-lg-3 col-md-3 col-sm-3 col-xs-3 bxr-element-big-image pull-" + t.bIMG + "'><img src=\""+$(element).data("bimgsrc")+"\"></div><div class='bxr-container-hover-menu col-lg-9 col-md-9 col-sm-9 col-xs-9'></div>");
                    new_element = new_element.find(".bxr-container-hover-menu");
               }
                
                for(i=0;i<4;i++) {
                    new_element.append("<div class='col-lg-"+(12/t.LG)+" col-md-"+(12/t.MD)+" col-sm-"+(12/t.SM)+" col-xs-"+(12/t.XS)+" columns-left'></div>");
                }
            });
            
            $(window).resize(function() {
                window.BXReady.Market.MenuHoverList.menu_columns();                
            });

            this.menu_columns();            
        },

        isScreen: function(){
            if($(window).outerWidth()>=1200)
                return "lg";

            if($(window).outerWidth()<1200 && $(window).outerWidth()>=992)
                return "md";

            if($(window).outerWidth()<992 && $(window).outerWidth()>=768)
                return "sm";

            if($(window).outerWidth()<768)
                return "xs";

            return "lg";
        },

        menu_columns: function(){
            var tiS = this.isScreen();
            
            if(this.rScreen == tiS)
                return false;

            $('.bxr-list-hover-menu .columns-left').html("");

            var menu = $('.bxr-list-hover-menu');
            var columns = this[tiS];

            menu.each(function(indx, element){
                var i=0;
                columns = $(element).data(tiS);
                $(element).find("> .bxr-element-hover-menu").each(function(indx2, element2){
                    if(i>=(columns) || i<0)
                        i = 0;

                    var div = $(element).find('div.columns-left:eq(' + i +')');
                    div.append($(element2).clone().addClass("show col-xs-12"));
                    ++i;  
                });
            });

            this.rScreen = tiS;
        }
    };

    
     $(document).ready(function(){
        window.BXReady.Market.MenuHoverList.init();
    });
});