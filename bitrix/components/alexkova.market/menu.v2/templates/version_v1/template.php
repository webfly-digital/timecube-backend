<?
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
    if (empty($arResult["TREE"])) return;
?>
<?$this->setFrameMode(true);?>
<?
    $classUl = "";
    $classLi = "";
    $classLiSelected = "";
    
    $bigMode = "N";
    $lightMode = "N"; 
           
    switch ($arParams['FONT_MENU']) {
        case "ligth"    :   $lightMode = "Y"; break;
        case "big"      :   $bigMode = "Y"; break;
    }
     
    switch ($arParams['COLOR_MENU']) {
        case "light": 
            $classLi = "bxr-children-color-hover";
            $classLiSelected = "bxr-children-color";
            $arColor = "ico_dark";
            break;
        case "color": 
            $classUl = "bxr-color-flat";
            $classLi = "bxr-color-flat bxr-bg-hover-dark-flat";
            $arColor = "ico_light";
            $classLiSelected = "bxr-color-dark-flat";
            break;
        case "dark": 
            $classUl = "bxr-dark-flat";
            $classLi = "bxr-dark-flat bxr-bg-hover-flat";
            $classLiSelected = "bxr-color-flat";
            $arColor = "ico_light";
            break;
        default:
            $classLi = "bxr-children-color-hover";
            $classLiSelected = "bxr-children-color";
    }
    
    $arColor = $arParams['ICO_TOP_MENU_COLOR_1'];
    $arColorH = $arParams['ICO_TOP_MENU_COLOR_2'];
     
    $classLiParent =  "bxr-li-top-menu-parent-";

    if($bigMode == "Y")
        $classUl .= " bxr-big-menu ";

    if($lightMode == "Y")
        $classUl .= " bxr-light-menu ";
    
    if($arParams['COLOR_MENU']=="light")
        $classUl .= " line-top "; 
    
?>
<?if($arParams["FULL_WIDTH"] == "Y"):?>
    <div class="bxr-v-line_menu <?=$classUl;?> colored_<?=$arParams["COLOR_MENU"];?>"><div class="container bxr-built-menu-container">
<?else:?>
    <div class="container bxr-built-menu-container bxr-v-line_menu colored_<?=$arParams["COLOR_MENU"];?>">
<?endif;?>
<div class="row">
    <div class="col-sm-12"><nav>
    <ul 
        data-stretch="<?=(empty($arParams['STRETCH_MENU']) || $arParams['STRETCH_MENU'] == "Y") ? "Y" : "N" ;?>"
        data-style-menu="<?=$arParams['COLOR_MENU']?>"
        data-style-menu-hover="<?=$arParams['COLOR_MENU']?>"
        class="bxr-flex-menu <?=$classUl;?> bxr-top-menu bxr-ident-items-<?=$arParams["INDENT_ITEMS_MENU"]?>" >
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
                
                        
                $s_ico = $s_ico_h = $icoClass = "";
                if(isset($arParams["ICO_TOP_MENU"]) && $arParams["ICO_TOP_MENU"]!="N" ){ 
                    
                    if($arColor!=$arColorH) {
                        if(isset($arItem["ico_".$arColorH]) && !empty($arItem["ico_".$arColorH])) {
                            if(is_numeric($arItem["ico_".$arColorH])) {
                                $img = CFile::ResizeImageGet($arItem["ico_".$arColorH], array('width'=>16, 'height'=>16), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                            }
                            else {
                               $img['src'] = $arItem["ico_".$arColorH];                   
                            }
                            $s_ico_h = "<img class='bxr-ico-menu-top-hover' src='" . $img['src'] . "' alt='".$arItem["TEXT"]."'>";
                        }
                        elseif(!empty($arItem["ico_font"])){
                            $s_ico_h = "<i class='bxr-ico-menu-top-hover bxr-font-".$arParams['ICO_TOP_MENU_COLOR_2']." fa fa-fw " . $arItem["ico_font"] . "' ></i>";
                        }
                        elseif($arParams["ICO_TOP_MENU"]=="ICO_DEFAULT") {
                            //$s_ico_h = CFile::ShowImage(SITE_TEMPLATE_PATH. "/images/menu/default_ico_" . $arColorH . ".png", 16, 16, "class=bxr-ico-menu-top-hover", "", false);
                            $s_ico_h = "<i class='bxr-ico-menu-top-hover bxr-font-".$arParams['ICO_TOP_MENU_COLOR_2']." fa fa-fw fa-cloud' ></i>";
                        }
                    }
                    
                    $icoClass = "";
                    if(!empty($s_ico_h))
                          $icoClass = "bxr-ico-menu-top-default";
                    
                    if(isset($arItem["ico_".$arColor]) && !empty($arItem["ico_".$arColor])) {
                        if(is_numeric($arItem["ico_".$arColor])) {
                            $img = CFile::ResizeImageGet($arItem["ico_".$arColor], array('width'=>16, 'height'=>16), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                        }
                        else {
                           $img['src'] = $arItem["ico_".$arColor];                   
                        }
                        $s_ico = "<img class='".$icoClass."'  src='" . $img['src'] . "' alt='".$arItem["TEXT"]."'>";
                    }
                    elseif(!empty($arItem["ico_font"])){
                        $s_ico = "<i class='".$icoClass." fa bxr-font-".$arParams['ICO_TOP_MENU_COLOR_1']." fa-fw " . $arItem["ico_font"] . "' ></i>";
                    }
                    elseif($arParams["ICO_TOP_MENU"]=="ICO_DEFAULT") {                        
                        //$s_ico = CFile::ShowImage(SITE_TEMPLATE_PATH. "/images/menu/default_ico_" . $arColor . ".png", 16, 16, "class=".$icoClass, "", false);
                        $s_ico = "<i class='".$icoClass." fa bxr-font-".$arParams['ICO_TOP_MENU_COLOR_1']." fa-fw fa-cloud' ></i>";
                    }
                    
                }
                
                    $TemplateMenuHover = $arParams["TEMPLATE_MENU_HOVER"];
                    if(!empty($arItem["hover"]))
                        $TemplateMenuHover = $arItem["hover"];
                ?>
            <li <?if($arItem['SELECTED'] == 1) echo "data-selected='1' "?> class="<?=$classLi . " " . $classLiParent.$TemplateMenuHover;?> <?if($arItem['SELECTED'] == 1) echo $classLiSelected;?>">
                <a href="<?=$arItem["LINK"]?>"><span class="bxr-ico-top-menu"><?=$s_ico.$s_ico_h;?></span><?=$arItem["TEXT"]?><?=$glyphicon;?></a>
                <?if($isChildren):?>
                    <?                       
                        $arParamsHoverMenu = array(
                            "PICTURE_SECTION" => $arParams['PICTURE_SECTION'],
                            "ICO_HOVER_MENU_COLOR_1" => $arParams['ICO_HOVER_MENU_COLOR_1'],
                            "ICO_HOVER_MENU_COLOR_2" => $arParams['ICO_HOVER_MENU_COLOR_2'],
                            "CACHE_TYPE" => $arParams['CACHE_TYPE'],
                            "CACHE_TIME" => $arParams['CACHE_TIME'],
                            "MENU_TREE" => $arItem["CHILDREN"],
                            "COLOR_MENU" => $arParams["COLOR_MENU"],
                            "STYLE_MENU_HOVER" => $arParams["STYLE_MENU_HOVER"],
                            "PICTURE_CATEGARIES" =>$arParams["PICTURE_CATEGARIES"],
                            "HOVER_MENU_COL_LG" => $arParams["HOVER_MENU_COL_LG"],
                            "HOVER_MENU_COL_MD" => $arParams["HOVER_MENU_COL_MD"],
                            "HOVER_MENU_COL_SM" => $arParams["HOVER_MENU_COL_SM"],
                            "HOVER_MENU_COL_XS" => $arParams["HOVER_MENU_COL_XS"]                        
                        );   
                                              
                        if($TemplateMenuHover=="list")
                            $arParamsHoverMenu["PICTURE_SECTION"] = $arParams['PICTURE_SECTION_HOVER_LIST'];
                                              
                        if(isset($arItem["IMG"])) {
                            $arParamsHoverMenu["IMG"] = $arItem["IMG"]; 
                        }
                    ?>
                    <?$APPLICATION->IncludeComponent("alexkova.market:menu.hover", $TemplateMenuHover, $arParamsHoverMenu, false, array("HIDE_ICONS" => "Y"));?>
                <?endif;?>
            </li>
        <?endforeach;?>

            <li class="bxr-flex-menu-other other <?=$classLi;?>">&nbsp;</li>
        <?if($arParams['SEARCH_FORM'] == "Y"):?>
            <li class="search li-visible <?=$classLi;?>" ><a href="#"><span class="fa fa-search"></span></a></li>        

        <?endif;?>
        <div class="clearfix"></div>
    </ul>
</nav></div></div>        
<?if($arParams["FULL_WIDTH"] == "Y"):?>
    </div></div>
<?else:?>
    </div>
<?endif;?>
<?php
    global $INDEX_MENU;    
    $arParams["INPUT_ID"] .= "-".$INDEX_MENU;
    $arParams["CONTAINER_ID"] .= "-".$INDEX_MENU;
?>
<div class="bxr-menu-search-line-container <?if($arParams["FULL_WIDTH"] == "Y") echo "bxr-menu-search-line-container-color"; ?>"><div class="container bxr-built-menu-container">
    <?if($arParams['SEARCH_FORM'] == "Y"):?>
        <div class="bxr-menu-search-line col-md-12">
            <?$APPLICATION->IncludeComponent(
                "alexkova.business:search.title", 
                "",
                $arParams,
                false,
                array("HIDE_ICONS" => "Y")
            );?>
        </div>
    <?endif;?>
</div></div>