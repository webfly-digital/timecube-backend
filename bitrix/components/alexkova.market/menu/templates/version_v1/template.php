<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?if (!empty($arResult["TREE"])):?>
<?
    $classUl = "";
    $classLi = "";
    $classLiSelected = "";
    
    $bigMode = "N";
	$lightMode = "N";
    
    if (strripos($arParams['STYLE_MENU'], "_big") !== false) {
        $bigMode = "Y";
        $arParams['STYLE_MENU'] = str_replace("_big", "", $arParams['STYLE_MENU']);
    }

	if (strripos($arParams['STYLE_MENU'], "_lighten") !== false) {
		$lightMode = "Y";
		$arParams['STYLE_MENU'] = str_replace("_lighten", "", $arParams['STYLE_MENU']);
	}
 
    switch ($arParams['STYLE_MENU']) {
        case "colored_light": 
            $classLi = "bxr-children-color-hover";
            $classLiSelected = "bxr-children-color";
            break;
        case "colored_color": 
            $classUl = "bxr-color-flat";
            $classLi = "bxr-color-flat bxr-bg-hover-dark-flat";
            $classLiSelected = "bxr-color-dark-flat";
            break;
        case "colored_dark": 
            $classUl = "bxr-dark-flat";
            $classLi = "bxr-dark-flat bxr-bg-hover-flat";
            $classLiSelected = "bxr-color-flat";
            break;
    }
    
    $classLiParent = "";
    if(isset($arParams["TEMPLATE_MENU_HOVER"]) && $arParams["TEMPLATE_MENU_HOVER"]!="classic")
         $classLiParent = "bxr-li-top-menu-parent";

    if($bigMode == "Y")
        $classUl .= " bxr-big-menu ";

	if($lightMode == "Y")
		$classUl .= " bxr-light-menu ";
    
    if($arParams['STYLE_MENU']=="colored_light")
        $classUl .= " line-top ";    
?>
<?if($arParams["FULL_WIDTH"] == "Y"):?>
    <div <?if($arParams["FIXED_MENU"] == "Y") echo 'data-fixed="Y"';?> class="bxr-v-line_menu hidden-sm hidden-xs <?=$classUl;?> <?=$arParams["STYLE_MENU"];?>"><div class="container">
<?else:?>
    <div <?if($arParams["FIXED_MENU"] == "Y") echo 'data-fixed="Y"';?>  class="container hidden-sm hidden-xs bxr-v-line_menu <?=$arParams["STYLE_MENU"];?>">
<?endif;?>
            
<div class="row"><div class="col-sm-12"><nav>
    <ul data-style-menu="<?=$arParams['STYLE_MENU']?>" data-style-menu-hover="<?=$arParams['STYLE_MENU_HOVER']?>"  class="bxr-flex-menu  <?=$classUl;?> bxr-top-menu">
<?
        $previousLevel = 0;
        $flagFirst = true;
        $i = 0;

        foreach($arResult["TREE"] as $arItem):?>
            <?
                $isChildren = false;
                $glyphicon = "";
                if(isset($arItem["CHILDREN"])) {
                    $isChildren = true;
                    $glyphicon = '<span class="fa fa-angle-down"></span>';
                }
                ?>
            <li class="<?=$classLi . " " . $classLiParent;?> <?if($arItem['SELECTED'] == 1) echo $classLiSelected;?>">
                <a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"].$glyphicon;?></a>
                <?if($isChildren):?>
                    <?
                        $TemplateMenuHover = "classic";
                        if(isset($arParams["TEMPLATE_MENU_HOVER"]))
                            $TemplateMenuHover = $arParams["TEMPLATE_MENU_HOVER"];
                        
                        
                        $arParamsHoverMenu = array(
                            "PICTURE_SECTION" => $arParams['PICTURE_SECTION'],
                            "CACHE_TYPE" => $arParams['CACHE_TYPE'],
                            "CACHE_TIME" => $arParams['CACHE_TIME'],
                            "MENU_TREE" => $arItem["CHILDREN"],
                            "STYLE_MENU" => $arParams["STYLE_MENU"],
                            "STYLE_MENU_HOVER" => $arParams["STYLE_MENU_HOVER"],
                        );
                        
                        if(isset($arParams["TEMPLATE_MENU_HOVER"]) && $arParams["TEMPLATE_MENU_HOVER"]=="list") {
                            $arParamsHoverMenu["PICTURE_CATEGARIES"] = $arParams["PICTURE_CATEGARIES"];
                            $arParamsHoverMenu["HOVER_MENU_COL_LG"] = $arParams["HOVER_MENU_COL_LG"];
                            $arParamsHoverMenu["HOVER_MENU_COL_MD"] = $arParams["HOVER_MENU_COL_MD"];
                            $arParamsHoverMenu["HOVER_MENU_COL_SM"] = $arParams["HOVER_MENU_COL_SM"];
                            $arParamsHoverMenu["HOVER_MENU_COL_XS"] = $arParams["HOVER_MENU_COL_XS"];
                                              
                            if(isset($arItem["IMG"])) {
                                $arParamsHoverMenu["IMG"] = $arItem["IMG"]; 
                            }                            
                        }
                    ?>
                    <?$APPLICATION->IncludeComponent("alexkova.market:menu.hover", $TemplateMenuHover, $arParamsHoverMenu, false, array("HIDE_ICONS" => "Y"));?>
                <?endif;?>
            </li>
        <?endforeach;?>

        <?if($arParams['SEARCH_FORM'] == "Y"):?>
            <li class="other <?=$classLi;?>" id="bxr-flex-menu-li">&nbsp;</li>
            <li class="last li-visible <?=$classLi;?>" ><a href="#"><span class="fa fa-search"></span></a></li>
        <?else:?>
            <li class="other pull-right <?=$classLi;?>" id="bxr-flex-menu-li">&nbsp;</li>
        <?endif;?>
        <div class="clearfix"></div>
    </ul>
</nav></div></div>        
<?if($arParams["FULL_WIDTH"] == "Y"):?>
    </div></div>
<?else:?>
    </div>
<?endif;?>
<?endif?>
<?//if($arParams["FULL_WIDTH"] == "Y"):?>
    <div class="bxr-menu-search-line-container <?if($arParams["FULL_WIDTH"] == "Y") echo "bxr-menu-search-line-container-color"; ?>">
<?//endif;?>
        <div class="container">
            <div class="row">
                <?if($arParams['SEARCH_FORM'] == "Y"):?>
                    <div id="bxr-menu-search-line" class="col-md-12 hidden-xs hidden-sm">
                        <?$APPLICATION->IncludeComponent(
                            "alexkova.market:search.title", 
                            "menu",
                            $arParams,
                            false,
                            array("HIDE_ICONS" => "Y")
                        );?>
                    </div>
                <?endif;?>
            </div>
        </div>    
<?//if($arParams["FULL_WIDTH"] == "Y"):?>
    </div>
<?//endif;?>
<div class="container">
    <div class="row">
        <div class="col-sm-12 col-xs-12 hidden-lg hidden-md bxr-mobile-menu-button-container">
            <div class="bxr-color-flat">
                <div class="bxr-mobile-menu-text"><?if(isset($arParams["TEXT_MOBIL_MENU"])) echo $arParams["TEXT_MOBIL_MENU"];?></div>
                <div id="bxr-menuitem" class="bxr-mobile-menu-button pull-right"><i class="fa fa-bars"></i></div>
                <?if($arParams['SEARCH_FORM'] == "Y"):?>
                    <div id="bxr-menu-search-form" class="bxr-mobile-menu-button pull-right"><i class="fa fa-search"></i></div>
                <?endif;?>
            </div>
        </div>
    </div>
    <?if($arParams['SEARCH_FORM'] == "Y"):?>
    <div class="row">
        <div class="col-sm-12 col-xs-12 hidden-lg hidden-md bxs-search-mobil-menu">
        <?$APPLICATION->IncludeComponent(
            "bitrix:search.form", 
            "market",
            $arParams,
            false,
            array("HIDE_ICONS" => "Y")
        );?>
        </div>
    </div>
    <?endif;?>
    <div class="row">
        <div class="col-sm-12 col-xs-12 hidden-lg hidden-md" id="bxr-mobile-menu-container">
            <div id="bxr-mobile-menu-body"></div>
        </div>
    </div>
</div>