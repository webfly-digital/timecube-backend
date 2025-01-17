<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?if (!empty($arResult["TREE"])):?>
<?
    $classUl = "";
    $classLi = "";
    $classTopLi = "";
    $classLiSelected = "";
    $classLiSelected2 = "";
    $ico_1 = (!empty($arParams['ICO_LEFT_MENU_COLOR_1'])) ? $arParams['ICO_LEFT_MENU_COLOR_1'] : "dark";
    $ico_2 = (!empty($arParams['ICO_LEFT_MENU_COLOR_2'])) ? $arParams['ICO_LEFT_MENU_COLOR_2'] : "light";
    
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
            $classLi = "bxr-bg-hover-flat";
            $classTopLi = "bxr-color-dark-flat";
            $classLiSelected = "bxr-color-flat";
            $classLiSelected2 = "bxr-color-flat";
            break;
        case "colored_color":
            $classUl = "bxr-color-flat";
            $classTopLi = "bxr-color-dark-flat";
            $classLi = "bxr-color-flat bxr-bg-hover-light-flat";
            $classLiSelected = "bxr-color-light-flat";
            $classLiSelected2 = "bxr-color-light-flat";
            break;
        case "colored_dark": 
            $classUl = "bxr-dark-flat";
            $classTopLi = "bxr-dark-dark-flat";
            $classLi = "bxr-dark-flat bxr-bg-hover";
            $classLiSelected = "bxr-dark-light-flat";
            $classLiSelected2 = "bxr-color-flat";
            break;
    }

    if($bigMode == "Y")
        $classUl .= " bxr-big-menu ";

	if($lightMode == "Y")
		$classUl .= " bxr-light-menu ";
    
    if($arParams['STYLE_MENU']=="colored_light")
        $classUl .= " line-top ";
    
    $hoverLeft = false;
    if(isset($arParams['HOVER_SHOW_LEFT']) &&  $arParams['HOVER_SHOW_LEFT']=="Y")
        $hoverLeft = true;    
?>
<nav class="tb20-bottom">
    <ul  class="<?=$classUl;?> bxr-left-menu-hover hidden-sm hidden-xs">
        <?if(isset($arParams["TITLE_MENU"]) && !empty($arParams["TITLE_MENU"])):?>
            <li class="<?=$classTopLi;?> bxr-title-menu-hover"><?=$arParams["TITLE_MENU"];?></li>
        <?endif;?>
<?
        $section_has_children = false;
        if($hoverLeft) {
            foreach($arResult["TREE"] as $k => $arItem){
                if(isset($arItem["CHILDREN"])){
                    $classLi .= " bxr-hover-menu-right";
                    $section_has_children = true;
                    break;
                }
            }
        }

        foreach($arResult["TREE"] as $arItem):?>
            <?
                ++$i;
                $isChildren = false;
                $glyphicon_right = "";
                $glyphicon_left = '<span class="fa fa-circle-o"></span>';
                $classShow = "";
                
                if(isset($arItem["CHILDREN"])) {
                    $isChildren = true;
                    
                    if(!$hoverLeft)
                        $glyphicon_right = '<span class="fa fa-angle-right"></span>';
                    
                    if($hoverLeft)
                        $glyphicon_left = '<span class="fa fa-angle-left"></span>';
                }

                if(!$section_has_children)
                    $glyphicon_left = "";
                
                $s_ico = $s_ico_h = $icoClass = "";
                if(isset($arParams["PICTURE_SECTION"]) && $arParams["PICTURE_SECTION"] != "N") {
                    
                    if($ico_1!=$ico_2) {
                     
                        if(isset($arItem["ico_".$ico_2]) && !empty($arItem["ico_".$ico_2])) { 
                            if(is_numeric($arItem["ico_".$ico_2])) {
                                $img = CFile::ResizeImageGet($arItem["ico_".$ico_2], array('width'=>16, 'height'=>16), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                            }
                            else {
                               $img['src'] = $arItem[$ico];
                            }
                            $s_ico_h = "<img class='bxr-ico-menu-left-hover-hover'  src='" . $img['src'] . "' alt='".$arItem["TEXT"]."'>";
                        }
                        elseif(!empty($arItem["ico_font"])){
                            $s_ico_h = "<i class='bxr-ico-menu-left-hover-hover bxr-font-".$ico_2." fa fa-fw " . $arItem["ico_font"] . "' ></i>";
                        }
                        elseif($arParams["PICTURE_SECTION"] == "ICO_DEFAULT") {
                            $s_ico_h = "<i class='bxr-ico-menu-left-hover-hover bxr-font-".$ico_2." fa fa-fw fa-cloud' ></i>";
                        }
                        else {
                            $s_ico_h = "<span class='bxr-ico-menu-left-hover-hover hover-not-ico'>&nbsp;</span>";
                        }
                    }
                    
                    $icoClass = "";
                    if(!empty($s_ico_h))
                      $icoClass = "bxr-ico-menu-left-hover-default";
                    
                    if(isset($arItem["ico_".$ico_1]) && !empty($arItem["ico_".$ico_1])) {
                        if(is_numeric($arItem["ico_".$ico_1])) {
                            $img = CFile::ResizeImageGet($arItem["ico_".$ico_1], array('width'=>16, 'height'=>16), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                        }
                        else {
                           $img['src'] = $arItem[$ico];
                        }
                        $s_ico = "<img class='".$icoClass."' src='" . $img['src'] . "' alt='".$arItem["TEXT"]."'>";
                    }
                    elseif(!empty($arItem["ico_font"])){
                        $s_ico = "<i class='".$icoClass." bxr-font-".$ico_1." fa fa-fw " . $arItem["ico_font"] . "' ></i>";
                    }
                    elseif($arParams["PICTURE_SECTION"] == "ICO_DEFAULT") {
                        $s_ico = "<i class='".$icoClass." bxr-font-".$ico_1." fa fa-fw fa-cloud' ></i>";
                    }
                    else {
                        $s_ico = "<span class='".$icoClass." hover-not-ico'>&nbsp;</span>";
                    }
                                        
                }
            ?>
            <li <?if($arItem['SELECTED'] == 1) echo "data-selected='1' ";?>  class=" <?=$classLi;?><?if($arItem['SELECTED'] == 1) echo " " . $classLiSelected2;?>">
                <a href="<?=$arItem["LINK"]?>"><?=$glyphicon_left;?><span class='bxr-ico-left-hover-menu'><?=$s_ico . $s_ico_h; ?></span><?=$arItem["TEXT"].$glyphicon_right;?></a>
                <?
                    $hoverTemplate = "classic";
                    if(!empty($arParams["HOVER_TEMPLATE"]))
                        $hoverTemplate = $arParams["HOVER_TEMPLATE"];
                    
                     if(!empty($arItem["hover"]))
                        $hoverTemplate = $arItem["hover"];
                ?>
                <?if($isChildren):?>
                    <?$APPLICATION->IncludeComponent(
                        "alexkova.market:menu.hover", 
                        $hoverTemplate, 
                        array(
                                "PICTURE_SECTION" => $arParams['PICTURE_SECTION_HOVER'],
                                "PICTURE_CATEGARIES" => $arParams['PICTURE_CATEGARIES'],
                                "HOVER_MENU_COL_LG" => $arParams['HOVER_MENU_COL_LG'],
                                "HOVER_MENU_COL_MD" => $arParams['HOVER_MENU_COL_MD'],
                                "HOVER_MENU_COL_SM" => $arParams['HOVER_MENU_COL_SM'],
                                "HOVER_MENU_COL_XS" => $arParams['HOVER_MENU_COL_XS'],
                                "CACHE_TYPE" => $arParams['CACHE_TYPE'],
                                "CACHE_TIME" => $arParams['CACHE_TIME'],
                                "MENU_TREE" => $arItem["CHILDREN"],
                                "IMG" => $arItem["IMG"],
                                "ICO_HOVER_MENU_COLOR_1" => $arParams['ICO_LEFT_MENU_HOVER_COLOR_1'], // dark // color
                                "ICO_HOVER_MENU_COLOR_2" => $arParams['ICO_LEFT_MENU_HOVER_COLOR_2'],
                                "STYLE_MENU" => "",
                                "STYLE_MENU_HOVER" => $arParams["STYLE_MENU_HOVER"],
                        ),
                        false,
                        array("HIDE_ICONS" => "Y")
                    );?>
                <?endif;?>
            </li>
        <?endforeach;?>
    </ul>
</nav>
<?endif;?>