<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<?
    if(!function_exists(bxr_classic_build_tree)) {
        function bxr_classic_build_tree($mas, $ico = "N", $arColor = array("li" => "", "li_selected" => "", "ico_1" => "color", "ico_2" => "light"), $lvl=1) { 
            
            if(!is_array($mas))
                return false;
           
            $s_result = "<ul>";
            
            $section_has_children = false;
            foreach($mas as $k => $v) {
                if(isset($v['CHILDREN'])) {
                    $section_has_children = true;
                    break;
                }
            }

            foreach($mas as $k => $v) {
                $s_ico = $s_ico_h = $icoClass = "";
                
                if($ico != "N") {
                    
                    if($arColor['ico_1']!=$arColor['ico_2']) {
                        if(isset($v["ico_".$arColor['ico_2']]) && !empty($v["ico_".$arColor['ico_2']])) {
                            if(is_numeric($v["ico_".$arColor['ico_2']])) {
                                $img = CFile::ResizeImageGet($v["ico_".$arColor['ico_2']], array('width'=>15, 'height'=>15), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                            }
                            else {
                               $img['src'] = $v["ico_".$arColor['ico_2']];                   
                            }
                            $s_ico_h = "<img class='bxr-ico-menu-hover-hover' src='" . $img['src'] . "'>";
                        }
                        elseif(!empty($v["ico_font"])){
                            $s_ico_h = "<i class='bxr-ico-menu-hover-hover bxr-font-".$arColor['ico_2']." fa fa-fw " . $v["ico_font"] . "' ></i>";
                        }
                        elseif($ico == "ICO_DEFAULT") {
                            //$s_ico_h = CFile::ShowImage(SITE_TEMPLATE_PATH. "/images/menu/default_ico_" . $arColor['ico_2'] . ".png", 15, 15, "class=bxr-ico-menu-hover-hover", "", false);
                            $s_ico_h = "<i class='bxr-ico-menu-hover-hover bxr-font-".$arColor['ico_2']." fa fa-fw fa-cloud' ></i>";
                        }
                        else {
                            //$s_ico_h = "<span class='hover-not-ico'>&nbsp;</span>";
                        }
                    }
       
                    $icoClass = "";
                    if(!empty($s_ico_h))
                          $icoClass = "bxr-ico-menu-hover-default";
                    
                    if(isset($v["ico_".$arColor['ico_1']]) && !empty($v["ico_".$arColor['ico_1']])) {
                        if(is_numeric($v["ico_".$arColor['ico_1']])) {
                            $img = CFile::ResizeImageGet($v["ico_".$arColor['ico_1']], array('width'=>15, 'height'=>15), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                        }
                        else {
                           $img['src'] = $v["ico_".$arColor['ico_1']];                   
                        }
                        $s_ico = "<img class='".$icoClass."' src='" . $img['src'] . "'>";
                    }
                    elseif(!empty($v["ico_font"])){
                        $s_ico = "<i class='".$icoClass." bxr-font-".$arColor['ico_1']." fa fa-fw " . $v["ico_font"] . "' ></i>";
                    }
                    elseif($ico == "ICO_DEFAULT") {
                        //$s_ico = CFile::ShowImage(SITE_TEMPLATE_PATH. "/images/menu/default_ico_" . $arColor['ico_1'] . ".png", 15, 15, "class=".$icoClass, "", false);
                        $s_ico = "<i class='".$icoClass." bxr-font-".$arColor['ico_1']." fa fa-fw fa-cloud' ></i>";
                    }
                    else {
                        //$s_ico = "<span class='hover-not-ico'>&nbsp;</span>";
                    }
                                        
            }
                                                
                $li_class = $arColor['li'];
                $isSelected = "";
                if($v['SELECTED'] == 1) {
                    $li_class .= " ". $arColor['li_selected'];
                    $isSelected = " data-selected='1' ";
                }
                
                $fa_left_o = "";
                $li_a_class = "class='margin-no'";
                if($section_has_children) {
                    $fa_left_o = "<i class='fa fa-circle-o'></i>";
                    $li_a_class  = "";
                }
                
                if(isset($v['CHILDREN']))
                    $s_result .= "<li ".$isSelected." class='" . $li_class . "' >" .
                                    "<a class='sub-item' href='" . $v['LINK'] . "'><i class='fa fa-angle-left'></i><span class='bxr-ico-hover-menu'>". $s_ico . $s_ico_h . "</span>" . $v['TEXT'] . "<i class='fa fa-angle-right'></i></a>"
                                        . bxr_classic_build_tree($v['CHILDREN'], $ico, $arColor, ($lvl+1)).
                                "</li>";
                else
                    $s_result .= "<li ".$isSelected." class='" . $li_class. "' >".
                                    "<a ".$li_a_class."  href='" . $v['LINK'] . "'>".$fa_left_o."</i><span class='bxr-ico-hover-menu'>". $s_ico . $s_ico_h . "</span>" . $v['TEXT'] . "</a>" . 
                                "</li>";
                
            }

            $s_result .= "</ul>";
            return $s_result;
        }
    }
?>
<div class="bxr-classic-hover-menu <?if($arParams['COLOR_MENU'] == "light") {echo " menu-arrow-top";} ?><?if($arParams['STYLE_MENU_HOVER'] == "colored_color") {echo " bxr-classic-hover-menu-color";} ?><?if($arParams['STYLE_MENU_HOVER'] == "colored_dark") {echo " bxr-classic-hover-menu-dark";} ?>">
    <?  
        $isIco = "N";
        if(!empty($arParams['PICTURE_SECTION']))
             $isIco = $arParams['PICTURE_SECTION'];
        
        $arColorParams = array(
            "li" => "bxr-bg-hover-flat",
            "li_selected" => "bxr-color-flat",
            "ico_1" => "dark",
            "ico_2" => "light",
        );

        if(isset($arParams["STYLE_MENU_HOVER"]) && !empty($arParams["STYLE_MENU_HOVER"])) {
            switch ($arParams["STYLE_MENU_HOVER"]) {
                case "colored_color":
                    $arColorParams = array(
                        "li" => "bxr-color-flat bxr-bg-hover-dark-flat",
                        "li_selected" => "bxr-color-dark-flat"
                    );
                    break;
                case "colored_dark":
                    $arColorParams = array(
                        "li" => "bxr-dark-flat bxr-bg-hover-flat",
                        "li_selected" => "bxr-color-flat bxr-bg-hover-flat"
                    );
                    break;
            }
        }
        
        if(!empty($arParams["ICO_HOVER_MENU_COLOR_1"]))
            $arColorParams["ico_1"] = $arParams["ICO_HOVER_MENU_COLOR_1"];
        
        if(!empty($arParams["ICO_HOVER_MENU_COLOR_2"]))
            $arColorParams["ico_2"] = $arParams["ICO_HOVER_MENU_COLOR_2"];

        $tree = bxr_classic_build_tree($arParams['MENU_TREE'], $isIco, $arColorParams, 1);       
        echo $tree;
    ?>
</div>