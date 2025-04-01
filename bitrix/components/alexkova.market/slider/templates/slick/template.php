<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->createFrame()->begin('...'); ?>
<?if (count($arResult["ITEMS"])<=0) return;?>
    <div class="bxr-slider <? if(isset($arParams['SLIDER_FULL_SCREEN']) && $arParams['SLIDER_FULL_SCREEN']=="N") {echo "container";} ?>" accesskey="" style="position: relative">
    <?foreach ($arResult["ITEMS"] as $key => $item):?>
        <?
            $target = "";
            ($item["PROPERTIES"]["NEW_TAB"]["VALUE"] == "Y") ? $target = "target='_blank'" : $target = "target='_self'";

            if ($item["PROPERTIES"]["LOCATION"]["VALUE"] != ""):
                $locationParts = explode(";", $item["PROPERTIES"]["LOCATION"]["VALUE"]);
                $location = $locationParts[0];
            else:
                $location = "left";
            endif;
        ?>

        <div class="container <?=$location;?>" style="background: url('<?=$item["DETAIL_PICTURE"]["SRC"]?>');"
            alt="<?=$item["NAME"]?>" title="<?=$item["NAME"]?>"><div class="row">
            <?
                $link = "/";
                if(isset($item["PROPERTIES"]["LINK"]["VALUE"][0]))
                    $link = $item["PROPERTIES"]["LINK"]["VALUE"][0];
            ?> 
            <?if ($item["PROPERTIES"]["SLIDER_LINK"]["VALUE"] == "Y"):?>
                <a <?=$target;?> href="<?=$link;?>">
            <?endif;?><div class="col-md-12">
                
            <div class="container">                    
                <div class="row">
                <?                       
                    if ($item["PROPERTIES"]["TITLE_COLOR"]["VALUE"] != "")
                        $title_color = "#" . $item["PROPERTIES"]["TITLE_COLOR"]["VALUE"];
                    else
                       $title_color = "fff";

                    if ($item["PROPERTIES"]["TEXT_COLOR"]["VALUE"] != "")
                        $text_color = "#" . $item["PROPERTIES"]["TEXT_COLOR"]["VALUE"];
                    else
                       $text_color = "fff";
                ?>
                <?if(isset($item["PREVIEW_PICTURE"]["SRC"])):?>
                    <div class="col-md-6 col-sm-5 hidden-xs pull-<?=$location?>">
                        <img class="img-responsive" alt="<?=$item["PREVIEW_PICTURE"]["ALT"]?>" src="<?=$item["PREVIEW_PICTURE"]["SRC"]?>"  alt="<?=$item["NAME"]?>"  data-bgfit="cover" data-bgposition="center center" data-bgrepeat="no-repeat">
                    </div>
                    <div class="col-md-6 col-sm-7 col-xs-12 slick-banner-content <?=$location?>">
                <?else:?>
                    <div class="col-md-12 slick-banner-content <?=$location?>">    
                <?endif;?><div class="bxr-table-cell">
                        <h2 style="color: <?=$title_color?>" ><?=$item["NAME"]?></h2>
                        <?if ($item["PROPERTIES"]["SHOW_PREVIEW_TEXT"]["VALUE"] == "Y"):?>
                            <p style="color: <?=$text_color?>"><?=$item["PREVIEW_TEXT"]?></p>
                        <? endif;?>
                            <div class="slick-buttons">
                                <?if (is_array($item["PROPERTIES"]["LINK"]["VALUE"])) 
                                    foreach ($item["PROPERTIES"]["LINK"]["VALUE"] as $k => $link): ?>
                                        <?if(empty($item["PROPERTIES"]["LINK"]["DESCRIPTION"][$k]))continue;?>
                                        <div class="modern-card-buttons">
                                            <?if ($item["PROPERTIES"]["SLIDER_LINK"]["VALUE"] != "Y"):?>
                                                <a <?=$target;?> href="<?=$link;?>" class="bxr-slider-button bxr-color-flat bxr-bg-hover-dark-flat"><?=$item["PROPERTIES"]["LINK"]["DESCRIPTION"][$k];?></a>
                                            <?else:?>
                                                <span class="bxr-slider-button bxr-color-flat bxr-bg-hover-dark-flat"><?=$item["PROPERTIES"]["LINK"]["DESCRIPTION"][$k];?></span>
                                            <? endif;?>
                                        </div>
                                    <?endforeach;?>
                            </div>
                    </div></div>
                </div></div></div>
            <?if ($item["PROPERTIES"]["SLIDER_LINK"]["VALUE"] == "Y"):?>
                </a>
            <?endif;?>
        </div></div>
    <?endforeach;?>
</div>
<script>
    window.BXReady.Market.SlickSliderTop = {
        init: function(){
            $('.bxr-slider').slick({
                slidesToShow: 1,
                dots: true,
                <?
                    $fade = "false";
                    if($arParams['SLIDER_FADE']=="Y")
                        $fade = "true";
                ?>
                fade: <?=$fade;?>,
                <? 
                    $speed = 1500;
                    if(isset($arParams['SLIDER_SPEED']) && is_numeric($arParams['SLIDER_SPEED']))
                        $speed = $arParams['SLIDER_SPEED'];
                ?>
                speed: <?=$speed;?>,
                <? 
                    $autoplaySpeed = 3000;
                    if(isset($arParams['SLIDER_AUTOPLAY_SPEED']) && is_numeric($arParams['SLIDER_AUTOPLAY_SPEED']))
                        $autoplaySpeed = $arParams['SLIDER_AUTOPLAY_SPEED'];
                ?>
                autoplaySpeed: <?=$autoplaySpeed;?>,
                <?
                   $autoplay = "false";
                   if($arParams['SLIDER_AUTOPLAY']=="Y")
                        $autoplay = "true";
                ?>
                autoplay: <?=$autoplay;?>,
                nextArrow: '<button type="button" class="slick-next bxr-bg-hover-dark-flat"><i class="fa fa-angle-right"></i></button>',
                prevArrow: '<button type="button" class="slick-prev bxr-bg-hover-dark-flat"><i class="fa fa-angle-left"></i></button>',
            });

            $('.bxr-slider').css("visibility", "visible");
        }
    }

    $(document).ready(function(){
        BXReady.Market.SlickSliderTop.init();
    });
</script>



