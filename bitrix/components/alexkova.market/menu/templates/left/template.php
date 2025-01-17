<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?if (!empty($arResult["TREE"])):?>
<?
    $classUl = "";
    $classUl2 = "";
    $classLi = "";
    $classLi2 = "";
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
            $classUl2 = "bxr-dark-flat-left-menu";
            $classLi = "bxr-children-color-hover";
            $classLi2 = "bxr-children-color-hover ";
            $classLiSelected = "bxr-color-flat";
            $classLiSelected2 = "bxr-children-color";
            $ico = "ico_color";
            break;
        case "colored_color":
            $classUl = "bxr-color-flat";
            $classUl2 = "bxr-color-light-flat";
            $classLi = "bxr-color-flat";
            $classLi2 = "bxr-color-light-flat";
            $classLiSelected = "bxr-color-dark-flat";
            $ico = "ico_light";
            break;
        case "colored_dark": 
            $classUl = "bxr-dark-flat";
            $classLi = "bxr-dark-flat";
            $classLi2 = "bxr-dark-light-flat";
            $classLiSelected = "bxr-color-flat";
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
    <ul  class="<?=$classUl;?> bxr-left-menu hidden-sm hidden-xs">
        <?if(isset($arParams["TITLE_MENU"]) && !empty($arParams["TITLE_MENU"])):?>
            <li class="<?=$classLiSelected;?> bxr-title-menu"><?=$arParams["TITLE_MENU"];?></li>
        <?endif;?>
<?
        foreach($arResult["TREE"] as $arItem):?>
            <?
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
                                    $glyphicon = '<span class="fa fa-angle-down"></span>';
                                    $classShow = "show";
                                }
                                break;
                            case "SHOW":
                                $glyphicon = '<span class="fa fa-angle-down"></span>';
                                $classShow = "show";
                                break;
                        }
                    }
                }
                
                $s_ico = "";
                $p_ico = "";
 
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
                    $p_ico = "bxr-padding-lv2";
                }

            ?>
            <li class="<?=$classLi;?> <?if($arItem['SELECTED'] == 1) echo "bxr-left-menu-selected " . $classLiSelected2;?>">
                <a href="<?=$arItem["LINK"]?>"><?=$s_ico;?><?=$arItem["TEXT"].$glyphicon;?></a>
                <?if($isChildren):?>
                    <ul  class="<?=$classUl2 . " " . $classShow . " " . $p_ico;?>">
                    <?foreach($arItem["CHILDREN"] as $arItemChildren):?>
                        <li class="<?=$classLi2;?> <?if($arItemChildren['SELECTED'] == 1) echo "bxr-left-menu-selected " . $classLiSelected2;?>">
                            <a href="<?=$arItemChildren["LINK"]?>"><?=$arItemChildren["TEXT"];?></a>
                        </li>
                    <?endforeach;?>
                    </ul>
                <?endif;?>
            </li>
        <?endforeach;?>
    </ul>
</nav>
<?endif;?>