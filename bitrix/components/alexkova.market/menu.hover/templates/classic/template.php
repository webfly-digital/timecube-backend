<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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

    $arResult["INCLUDE"] = array();

    if(file_exists($_SERVER["DOCUMENT_ROOT"].$templateFolder."/style.css"))
        $arResult["INCLUDE"]["CSS"] = $templateFolder."/style.css";

    if(file_exists($_SERVER["DOCUMENT_ROOT"].$templateFolder."/script.js"))
        $arResult["INCLUDE"]["JS"] = $templateFolder."/script.js";

    $this->__component->SetResultCacheKeys(array("INCLUDE"));

?>
<?php
    if(!function_exists('bxr_classic_build_tree')) {
        function bxr_classic_build_tree($mas, $ico = "N", $arColor = array("li" => "", "li_selected" => "", "ico" => "ico_color")) {

            if(!is_array($mas))
                return false;

            $s_result = "<ul>";

            foreach($mas as $k => $v) {
                $s_ico = "";

                if($ico != "N") {
                    if(isset($v[$arColor['ico']]) && !empty($v[$arColor['ico']])) {
                        if(is_numeric($v[$arColor['ico']])) {
                            $img = CFile::ResizeImageGet($v[$arColor['ico']], array('width'=>16, 'height'=>16), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                        }
                        else {
                           $img['src'] = $v[$arColor['ico']];
                        }
                        $s_ico = "<img alt='".$v["TEXT"]."' class='bxr-ico-menu' src='" . $img['src'] . "'>";
                    }
                    elseif($ico == "ICO_DEFAULT") {
                        if($arColor['ico'] == "ico_color")
                            $arColor['ico'] = "ico_dark";

                        $img['src'] = SITE_TEMPLATE_PATH. "/images/menu/default_" . $arColor['ico'] . ".png";
                        $s_ico = "<img alt='".$v["TEXT"]."' class='bxr-ico-menu' src='" . $img['src'] . "'>";
                    }
                    else {
                        $s_ico = "<span class='hover-not-ico'>&nbsp;</span>";
                    }
                }

                $li_class = $arColor['li'];
                if($v['SELECTED'] == 1)
                    $li_class .= " ". $arColor['li_selected'];

                if(isset($v['CHILDREN']))
                    $s_result .= "<li class='" . $li_class . "' >" .
                                    "<a class='sub-item' href='" . $v['LINK'] . "'>". $s_ico . $v['TEXT'] . "<i class='fa fa-angle-right'></i></a>"
                                        . bxr_classic_build_tree($v['CHILDREN'], $ico, $arColor).
                                "</li>";
                else
                    $s_result .= "<li class='" . $li_class. "' >".
                                    "<a  href='" . $v['LINK'] . "'>" . $s_ico . $v['TEXT'] . "</a>" .
                                "</li>";
            }

            $s_result .= "</ul>";
            return $s_result;
        }
    }
?>
<div class="bxr-classic_hover_menu <?php if($arParams['STYLE_MENU'] == "colored_light") {echo " menu-arrow-top";} ?><?php if($arParams['STYLE_MENU_HOVER'] == "colored_color") {echo " bxr-classic-hover-menu-color";} ?>">
    <?php
        $isIco = "N";
        if(!empty($arParams['PICTURE_SECTION']))
             $isIco = $arParams['PICTURE_SECTION'];

        $arColorParams = array(
            "li" => "bxr-bg-hover-flat",
            "li_selected" => "bxr-color-flat",
            "ico" => "ico_color"
        );

        if(isset($arParams["STYLE_MENU_HOVER"]) && !empty($arParams["STYLE_MENU_HOVER"])) {
            switch ($arParams["STYLE_MENU_HOVER"]) {
                case "colored_color":
                    $arColorParams = array(
                        "li" => "bxr-color-flat bxr-bg-hover-dark-flat",
                        "li_selected" => "bxr-color-dark-flat",
                        "ico" => "ico_light"
                    );
                    break;
            }
        }

        $tree = bxr_classic_build_tree($arParams['MENU_TREE'], $isIco, $arColorParams);
        echo $tree;
    ?>
</div>