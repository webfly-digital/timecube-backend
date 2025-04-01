<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$useBrands = ('Y' == $arParams['BRAND_USE']);
$elementDraw = \Alexkova\Bxready\Draw::getInstance($this);
$elementDraw->setMarkerCollection('circle_vertical');

$isDiscount = false;
if ($arParams["USE_PRICE_COUNT"]):
    foreach ($arResult['PRICE_MATRIX']['MATRIX'] as $matrix):
	if ($matrix[0]['DISCOUNT_PRICE']):
		$isDiscount = intval(100-$matrix[0]['DISCOUNT_PRICE']*100/$matrix[0]['PRICE']);
	endif;
    endforeach;
elseif (count($arResult["OFFERS"]) > 0) :
    $isDiscount = intval($arResult['MIN_PRICE'][$arParams['PRICE_CODE'][0]]['DISCOUNT_DIFF_PERCENT']);
else:
    $isDiscount = intval($arResult['MIN_PRICE']['DISCOUNT_DIFF_PERCENT']);
endif;
$markerGroup = array();
if ($arResult["PROPERTIES"]["RECOMMENDED"]["VALUE_XML_ID"] == "Y")
    $markerGroup["REC"] = true;
if ($arResult["PROPERTIES"]["NEWPRODUCT"]["VALUE_XML_ID"] == "Y")
    $markerGroup["NEW"] = true;
if ($arResult["PROPERTIES"]["SALELEADER"]["VALUE_XML_ID"] == "Y")
    $markerGroup["HIT"] = true;
if ($arResult["PROPERTIES"]["SPECIALOFFER"]["VALUE_XML_ID"] == "Y")
    $markerGroup["SALE"] = true;
if ($isDiscount != 0) {
    $markerGroup["SALE"] = true;
    $markerGroup["DISCOUNT"] = $isDiscount;
}

if (strlen(COption::GetOptionString('alexkova.market', 'list_marker_type')) > 0){
    $bxreadyMarkers = COption::GetOptionString('alexkova.market', 'list_marker_type');
} else {
    $bxreadyMarkers = $arParams["BXREADY_LIST_MARKER_TYPE"];
}

$elementDraw->setMarkerCollection($bxreadyMarkers);
?>
<div class="ax-element-slider">
    <?$elementDraw->showMarkerGroup($markerGroup);?>
    <div class="ax-element-slider-main">
        <?if (count($arResult["MORE_PHOTO"]) > 0) :
            foreach ($arResult["MORE_PHOTO"] as $key => $val) :
                $title = ($arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]) ? $arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $arResult["NAME"];
                $alt = ($arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]) ? $arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $arResult["NAME"];
                $imgTitle = ($key > 0) ? $title.'. '.GetMessage("PHOTO").' N'.($key+1) : $title;
                $imgAlt = ($key > 0) ? $alt.'. '.GetMessage("PHOTO").' N'.($key+1) : $alt;
                $dataAtr = "";
                foreach ($val["GROUP"] as $keyval => $value) {
                    if ($value) 
                        $dataAtr .= "data-".strtolower($keyval)."='".$value."' ";
                }
                ?>
                <a href="<?=$val["SRC"]?>" class="fancybox" data-rel="gallery" <?if ($key == 0) echo 'id="main-photo"'?>
                    data-item="<?=$val["ITEM_ID"]?>" <?=$dataAtr?> >
                    <img src="<?=$val["SRC"]?>" class="zoom-img" title="<?=$imgTitle?>" alt="<?=$imgAlt?>"
                         data-state="show" data-large="<?=$val["SRC"]?>" data-text-bottom="<?=$imgTitle?>" itemprop="image">
                </a>
            <?endforeach;
            if ($useBrands && $arResult["PROPERTIES"][$arParams["BRAND_PROP_CODE"][0]]["VALUE"]) :
				echo '<div class="brand-detail">';
                $APPLICATION->IncludeComponent("bitrix:catalog.brandblock", "element_detail", array(
                    "IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
                    "IBLOCK_ID" => $arParams['IBLOCK_ID'],
                    "ELEMENT_ID" => $arResult['ID'],
                    "ELEMENT_CODE" => "",
                    "PROP_CODE" => $arParams['BRAND_PROP_CODE'],
                    "CACHE_TYPE" => $arParams['CACHE_TYPE'],
                    "CACHE_TIME" => $arParams['CACHE_TIME'],
                    "CACHE_GROUPS" => $arParams['CACHE_GROUPS'],
                    "WIDTH" => "176",
                    "HEIGHT" => "76",
                    "WIDTH_SMALL" => "176",
                    "HEIGHT_SMALL" => "76", 
                    ),
                    $component,
                    array("HIDE_ICONS" => "Y")
                );
				echo '</div>';
            endif;
        else: ?>
            <div class="bxr-no-image-detail-wrap" id="main-photo">
                <img src="<?=$elementDraw->getDefaultImage()?>" alt="No photo">
            </div>
        <?endif;?>
    </div>
    <?if (count($arResult["MORE_PHOTO"]) > 1) :?>
        <div class="ax-element-slider-nav hidden-xs">
            <?foreach ($arResult["MORE_PHOTO"] as $key => $val):
                $title = ($arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]) ? $arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] : $arResult["NAME"];
                $alt = ($arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]) ? $arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $arResult["NAME"];
                $imgTitle = ($key > 0) ? $title.'. '.GetMessage("PHOTO").' N'.($key+1) : $title;
                $imgAlt = ($key > 0) ? $alt.'. '.GetMessage("PHOTO").' N'.($key+1) : $alt;
                $dataAtr = "";
                foreach ($val["GROUP"] as $keyval => $value) {
                    if ($value) 
                        $dataAtr .= "data-".strtolower($keyval)."='".$value."' ";
                }
            ?>
            <div class="slick-nav" data-item="<?=$val["ITEM_ID"]?>" <?=$dataAtr?>>
                <div class="slide-wrap <?if ($key == 0) echo 'first-slide'?>" data-item="<?=$val["ITEM_ID"]?>" <?if ($key == 0) echo 'id="main-photo-small"'?>>
                    <img src="<?=$val["SRC"]?>" title="<?=$imgTitle?>" alt="<?=$imgAlt?>" itemprop="image">
                </div>
            </div>
            <?endforeach;?>
        </div>
    <?endif;?>
</div>
<?if (count($arResult["MORE_PHOTO"]) > 0):?>
    <script>
    $("a.fancybox").fancybox();
    var sliderMain = $('.ax-element-slider-main').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        speed: 300,
        arrows: true,
        prevArrow: '<button type="button" class="bxr-color-button slick-prev"></button>',
        nextArrow: '<button type="button" class="bxr-color-button slick-next"></button>',
        fade: true,
        dots: false,
        infinite: true,
        cssEase: 'linear',
        asNavFor: '.ax-element-slider-nav',
        slide: 'a'
    });
    var sliderNav = $('.ax-element-slider-nav').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        speed: 300,
        arrows: true,
        prevArrow: '<button type="button" class="bxr-color-button slick-prev"></button>',
        nextArrow: '<button type="button" class="bxr-color-button slick-next"></button>',
        dots: true,
        infinite: true,
        centerMode: false,
        cssEase: 'linear',
        asNavFor: '.ax-element-slider-main',
        focusOnSelect: false,
        slide: 'div'
    });
    
    $('.ax-element-slider-main').on('afterChange', function(event, slick, currentSlide, nextSlide){
        data = $('.ax-element-slider-main').find('.slick-active').data('slick-index');
        $($('.ax-element-slider-nav .slick-track').children('.slick-slide')).css("border", "1px solid #f6f6f6");
        $($('.ax-element-slider-nav .slick-track').children('.slick-slide[data-slick-index='+data+']')).css("border", "1px solid #a8a8a8");  
    });

    $('.ax-element-slider-nav').on('afterChange', function(event, slick, currentSlide, nextSlide){
        $($('.ax-element-slider-nav .slick-nav')).css("border", "1px solid #f6f6f6");
        $('.ax-element-slider-nav .slick-nav.slick-current').css("border", "1px solid #a8a8a8");
    });

    $('.ax-element-slider-nav .slick-slide').on('click', function(e){
        sCnt = $('.slick-nav:not(.slick-cloned)').length - 1;        
        c = $('.slick-nav:not(.slick-cloned)[data-item='+$(this).data('item')+'][data-slick-index='+$(this).data('slick-index')+']').index();
        clone = $('.slick-nav.slick-cloned').length / 2;
        index = (c>0) ? c - clone : 0;
        gSlide = (index > sCnt) ? sCnt : index;
        setTimeout(function() {$('.ax-element-slider-nav').slick('slickGoTo', gSlide)}, 300);
        setTimeout(function() {$('.ax-element-slider-main').slick('slickGoTo', gSlide)}, 300);
    });

    $(document).ready(function() {
        $(".ax-element-slider-nav").children(".slick-prev").css("display", "none");
        $(".ax-element-slider-nav").children(".slick-next").css("display", "none");
        $(".ax-element-slider-main").children(".slick-prev").css("display", "none");
        $(".ax-element-slider-main").children(".slick-next").css("display", "none");
        setSlideSizes();
        $('.ax-element-slider-nav').slick('slickFilter','[data-main="default"]');
        $('.ax-element-slider-main').slick('slickFilter','[data-main="default"]');
        $($('.ax-element-slider-nav .slick-nav:not(.slick-cloned)')[0]).trigger('click');
    });

    $(document).on("mouseover", ".ax-element-slider-nav", function() {
        hideArrows(this, "block");
    });

    $(document).on("mouseover", ".ax-element-slider-main", function() {
        hideArrows(this, "block");
    });

    $(document).on("mouseout", ".ax-element-slider-nav", function() {
        hideArrows(this, "none");
    });

    $(document).on("mouseout", ".ax-element-slider-main", function() {
        hideArrows(this, "none");
    });

    $(window).resize(function() {
        setSlideSizes();
    });

    function hideArrows(elem, display) {
        $(elem).children(".slick-prev").css("display", display);
        $(elem).children(".slick-next").css("display", display);
    }

    function setSlideSizes() {
        border = 4;
        width = $('.ax-element-slider-nav .slick-list .slick-track .slick-slide').width();
        height = width + border;
        imgSize = width - 10;
        $('.ax-element-slider-nav .slick-list .slick-track .slick-slide').height(height);
        $('.ax-element-slider-nav .slick-list .slick-track .slick-slide .slide-wrap').css({'width': height, 'height': height, 'line-height': height+'px'})
    }

    <?if ($arParams["ZOOM_ON"] == "Y") {?>
        jQuery(function(){

            $(".zoom-img").imagezoomsl({
              zoomrange: [2.12, 12],
              magnifiersize: [300, 300],
              scrollspeedanimate: 10,
              loopspeedanimate: 5,
              cursorshadeborder: "1px solid black",
              magnifiereffectanimate: "slideIn",	
              magnifierborder: "1px solid #eee",
              zindex: 1000,
            });

            $(document).on("click", ".tracker", function() {
                $('.zoom-img').closest('a.slick-active').trigger("click");
            })
        });   
    <?}?>
    </script>
<?endif;