<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?if (!empty($arResult["TREE"])):?>
<?
    $classUl = "";
    $classLi = "";
    $classLiSelected = "";
    $classLiSelected2 = "";
    $ico = "";
    
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
            $classLiSelected = "bxr-color-flat";
            $classLiSelected2 = "bxr-color-flat";
            $ico = "ico_color";
            break;
        case "colored_color":
            $classUl = "bxr-color-flat";
            $classLi = "bxr-color-flat bxr-bg-hover-dark-flat";
            $classLiSelected = "bxr-color-dark-flat";
            $classLiSelected2 = "bxr-color-dark-flat";
            $ico = "ico_light";
            break;
        case "colored_dark": 
            $classUl = "bxr-dark-flat";
            $classLi = "bxr-dark-flat bxr-bg-hover-dark-flat";
            $classLiSelected = "bxr-dark-light-flat";
            $classLiSelected2 = "bxr-color-flat";
            $ico = "ico_light";
            break;
    }
    

    if($bigMode == "Y")
        $classUl .= " bxr-big-menu ";

	if($lightMode == "Y")
		$classUl .= " bxr-light-menu ";
    
    if($arParams['STYLE_MENU']=="colored_light")
        $classUl .= " line-top ";    
?>

<nav>
    <ul  class="<?=$classUl;?> bxr-left-menu-hover hidden-sm hidden-xs">
        <?if(isset($arParams["TITLE_MENU"]) && !empty($arParams["TITLE_MENU"])):?>
            <li class="<?=$classLiSelected;?> bxr-title-menu-hover"><?=$arParams["TITLE_MENU"];?></li>
        <?endif;?>
<?
        foreach($arResult["TREE"] as $arItem):?>
            <?
                ++$i;
                $isChildren = false;
                $glyphicon = "";
                $classShow = "";
                
                if(isset($arItem["CHILDREN"])) {
                    
                    $isChildren = true;
                    $glyphicon = '<span class="fa fa-angle-right"></span>';
                    
                    if(isset($arParams["SUBMENU"])) {
                        switch ($arParams["SUBMENU"]) {
                            case "ACTIVE_SHOW": 
                                if($arItem['SELECTED'] == 1) {
                                    $glyphicon = '<span class="fa fa-angle-right"></span>';
                                    $classShow = "show";
                                }
                                break;
                            case "SHOW":
                                $glyphicon = '<span class="fa fa-angle-right"></span>';
                                $classShow = "show";
                                break;
                        }
                    }
                }
                
                $s_ico = "";
 
                if(isset($arParams["PICTURE_SECTION"]) && $arParams["PICTURE_SECTION"] != "N") {
                    if(isset($arItem[$ico]) && !empty($arItem[$ico])) {
                        if(is_numeric($arItem[$ico])) {
                            $img = CFile::ResizeImageGet($arItem[$ico], array('width'=>16, 'height'=>16), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                        }
                        else {
                           $img['src'] = $arItem[$ico];
                        }
                        $s_ico = "<img alt='".$arItem["TEXT"]."' class='bxr-ico-menu' src='" . $img['src'] . "'>";
                    }
                    elseif($arParams["PICTURE_SECTION"] == "ICO_DEFAULT") {
                        if($ico == "ico_color")
                            $ico = "ico_dark";
                                
                        $img['src'] = SITE_TEMPLATE_PATH. "/images/menu/default_" . $ico . ".png";                    
                        $s_ico = "<img alt='".$arItem["TEXT"]."' class='bxr-ico-menu' src='" . $img['src'] . "'>";                        
                    }
                    else {
                        $s_ico = "<span class='hover-not-ico'>&nbsp;</span>";
                    }
                }

            ?>
            <li class="<?=$classLi;?> <?if($arItem['SELECTED'] == 1) echo $classLiSelected2;?>">
                <a href="<?=$arItem["LINK"]?>"><?=$s_ico;?><?=$arItem["TEXT"].$glyphicon;?></a>
                <?
                    $hoverTemplate = "classic";
                    if(!empty($arParams["HOVER_TEMPLATE"]))
                        $hoverTemplate = $arParams["HOVER_TEMPLATE"];
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