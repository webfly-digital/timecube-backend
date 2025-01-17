<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?if (!empty($arResult["TREE"])):?>
<?
function getIcon($item, $arColor, $pSection, $catalogEndIndex) {
    $s_ico = "";
    
    $icoClass = "bxr-ico-menu-left-default";
                    
    if(isset($item["ico_".$arColor]) && !empty($item["ico_".$arColor])) {
        if(is_numeric($item["ico_".$arColor])) {
            $img = CFile::ResizeImageGet($item["ico_".$arColor], array('width'=>40, 'height'=>40), BX_RESIZE_IMAGE_PROPORTIONAL, true);
        }
        else {
           $img['src'] = $item["ico_".$arColor];                   
        }
        $s_ico = "<span class='bxr-pt-img-cont'><img class='".$icoClass."'  src='" . $img['src'] . "' alt='".$item["TEXT"]."'></span>";
    }
    elseif(!empty($item["ico_font"])){
        $s_ico = "<i class='".$icoClass." fa bxr-font-".$arColor." fa-fw " . $item["ico_font"] . "' ></i>";
    } 
    elseif($pSection=="ICO_DEFAULT") {                        
        //$s_ico = CFile::ShowImage(SITE_TEMPLATE_PATH. "/images/menu/default_ico_" . $arColor . ".png", 16, 16, "class=".$icoClass, "", false);
        $s_ico = "<i class='".$icoClass." fa fa-fw fa-cloud' ></i>";                        
    }
    elseif ($catalogEndIndex === false) {
        $s_ico = "<i class='fa fa-nbsp'>&nbsp;</i>";
    }
    
    return $s_ico;
} 
    $classUl = "";
    $classUl2 = "";
    $classTopLi = "";
    $classLi = "";
    $classLi2 = "";
    $classLiSelected = "";
    $classLiSelected2 = "";
    $ico = "";
             
    switch ($arParams['STYLE_MENU']) {
        case "colored_light": 
            $classUl2 = "bxr-left-hover-menu";
            $classTopLi = "bxr-children-color-hover";
            $classLi = "bxr-children-color-hover";
            $classLi2 = "bxr-children-color-hover ";
            $classLiSelected = "bxr-font-color";
//            $classLiSelected2 = "bxr-children-color";
            $classTitlePt = "bxr-title-menu bxr-bg-hover-flat";
            $classCatalogPt = "bxr-catalog-menu bxr-bg-hover-flat";
            $arColor = "dark";
            break;
        case "colored_dark": 
            $classUl = "bxr-dark-flat-left-menu";
            $classUl2 = "bxr-dark-flaet-left-menu bxr-left-hover-menu";
            $classTopLi = "bxr-dark-dark-flat";
            $classLi = "bxr-dark-flat";
            $classLi2 = "bxr-dark-light-flat";
//            $classLiSelected = "bxr-font-color";
            $classTitlePt = "bxr-title-menu bxr-bg-hover-flat";
            $classCatalogPt = "bxr-catalog-menu bxr-bg-hover-flat";
            $arColor = "light";
            break;
    }
        
    $catalogEndIndex = false;
    $simpleMenuPt = $classCatalogPt;
?>

<nav>
    <ul  class="<?=$classUl;?> bxr-left-menu hidden-sm hidden-xs">
        <?foreach($arResult["TREE"] as $itemIndex => $arItem):?>
            <?
            if ($arItem["TEXT"] == "PHANTOM_PT") {
                $catalogEndIndex = $itemIndex;
                continue;
            }
            if ($catalogEndIndex !== false && $catalogEndIndex < $itemIndex)
                $simpleMenuPt = $classTitlePt;
            if ($itemIndex == 0) {?>
                <li class="<?=$classLiSelected?> <?=$classTitlePt?> no-link">
                    <a href="<?=SITE_DIR?>catalog/">
                        <span>
                            <span class="bxr-catalog-link-text">
                                <?=GetMessage("CATALOG_TITLE")?>
                            </span>
                        </span>
                    </a>
                </li>
            <?}?>
                <?$isChildren = false;
                $glyphicon = "";
                $classShow = "";
                
                if(isset($arItem["CHILDREN"])) {
                    
                    $isChildren = true;
                    $glyphicon = '<span class="fa fa-angle-right bxr-font-hover-light"></span>';
                }
                
                $s_ico = "";
                $p_ico = "";
 
                if(isset($arParams["PICTURE_SECTION"]) && $arParams["PICTURE_SECTION"] != "N") {
                    $s_ico = getIcon($arItem, $arColor, $arParams["PICTURE_SECTION"], $catalogEndIndex);
                }

            ?>
            <li class="<?if($arItem['SELECTED'] == 1) echo "bxr-left-menu-selected bxr-color " . $classLiSelected2;?> <?=$simpleMenuPt?> <?if ($catalogEndIndex !== false && $catalogEndIndex + 1 == $itemIndex) {?> bxr-not-catalog-start<?}?> <?if ($isChildren) {?> bxr-left-pt-with-children<?}?>">
                <a href="<?=$arItem["LINK"]?>">
                    <?=$s_ico;?>
                    <span>
                        <span class="bxr-catalog-link-text">
                            <?=$arItem["TEXT"];?>
                        </span>
                    </span>
                    <?=$glyphicon?>
                </a>
                <?if($isChildren):?>
                    <div class="bxr-hover-link-content">
                        <ul  class="<?=$classUl2 . " " . $classShow . " " . $p_ico;?>">
                            <li class="<?=$classLiSelected?> <?=$classTitlePt?> no-link">
                                <a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"];?></a>
                            </li>                          
                            <?
                            $pict = CFile::ResizeImageGet($arItem["PARAMS"]["PICTURE"], array('width'=>246, 'height'=>100), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                            if ($arParams["SUBMENU_PICTURE"] == "Y" && $pict['src']) {?>
                                <li class="bxr-hover-img">
                                    <img src="<?=$pict['src'];?>" alt="<?=$arItem["TEXT"];?>" title="<?=$arItem["TEXT"];?>">
                                </li>
                            <?}?>
                            <?if ($arParams["SUBMENU_DESCRIPTION"] == "Y" && $arItem["PARAMS"]["DESCRIPTION"]) {?>
                                <li class="bxr-hover-desc">
                                    <div>
                                        <?=$arItem["PARAMS"]["DESCRIPTION"];?>
                                    </div>
                                </li>
                                <p class="bxr-hover-fdesc bxr-font-color" data-state="minimized" data-min="<?=GetMessage("HIDE_FDESC")?>" data-max="<?=GetMessage("SHOW_FDESC")?>">
                                    <b><?=GetMessage("SHOW_FDESC")?></b>
                                    <i class="fa fa-chevron-down chevron-show"></i>
                                </p>
                            <?}?>
                            <?foreach($arItem["CHILDREN"] as $arItemChildren):
                                $s_ico = "";
                                if(isset($arParams["PICTURE_SECTION"]) && $arParams["SUBMENU_PICTURE_SECTION"] != "N") 
                                    $s_ico = getIcon($arItemChildren, $arColor, $arParams["SUBMENU_PICTURE_SECTION"], false);?>
                                <li class="<?if($arItemChildren['SELECTED'] == 1) echo "bxr-left-menu-selected bxr-color " . $classLiSelected2;?> <?=$classCatalogPt?>">
                                    <a href="<?=$arItemChildren["LINK"]?>">
                                        <?=$s_ico;?>
                                        <span>
                                            <span class="bxr-catalog-link-text">
                                                <?=$arItemChildren["TEXT"];?>
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            <?endforeach;?>
                        </ul>
                    </div>
                <?endif;?>
            </li>
        <?endforeach;?>
    </ul>
</nav>
<?endif;?>