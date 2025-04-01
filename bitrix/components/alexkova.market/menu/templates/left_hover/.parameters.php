<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arStyleMenu = array(
    "colored_light" => GetMessage("LIGHT_STYLE_MENU"),
    //"colored_light_big" => GetMessage("LIGHT_STYLE_MENU_BIG"),
    "colored_dark" => GetMessage("DARK_STYLE_MENU"),
   // "colored_dark_big" => GetMessage("DARK_STYLE_MENU_BIG"),
    "colored_color" => GetMessage("COLOR_STYLE_MENU"),
   // "colored_color_big" => GetMessage("COLOR_STYLE_MENU_BIG"),
);


$arPictureSection = array("N" => GetMessage("PICTURE_SECTION_N"), "ICO" => GetMessage("PICTURE_SECTION_ICO"), "ICO_DEFAULT" => GetMessage("PICTURE_SECTION_ICO_DEFAULT"));
$arSubmenu = array("ACTIVE_SHOW" => GetMessage("SUBMENU_ACTIVE_SHOW"), "SHOW" => GetMessage("SUBMENU_SHOW"), "NOT_SHOW" => GetMessage("SUBMENU_NOT_SHOW"));
$arTemplateMenuHover = array("classic" => GetMessage("CLASSIC_HOVER_MENU"), "list" => GetMessage("LIST_HOVER_MENU"));

$arTemplateParameters = array(
    "TITLE_MENU" => array(
        "PARENT" => "MENU_BLOCKS",
        "NAME" => GetMessage("TITLE_MENU"),
        "TYPE" => "STRING ",
        "DEFAULT" => "",
    ),    
    
    "STYLE_MENU" => array(
        "PARENT" => "MENU_BLOCKS",
        "NAME" => GetMessage("STYLE_MENU"),
        "TYPE" => "LIST",
        "VALUES" => $arStyleMenu,
        "DEFAULT" => "colored_light",
    ),            
   
    "PICTURE_SECTION" => array(
        "PARENT" => "MENU_BLOCKS",
        "NAME" => GetMessage("PICTURE_SECTION"),
        "TYPE" => "LIST",
        "VALUES" => $arPictureSection,
        "DEFAULT" => "N",
    ),
    
    "HOVER_TEMPLATE" => array(
        "PARENT" => "MENU_HOVER_BLOCKS",
        "NAME" => GetMessage("TEMPLATE_MENU_HOVER"),
        "TYPE" => "LIST",
        "VALUES" => $arTemplateMenuHover,
        "DEFAULT" => "classic",
        "REFRESH" => "Y",
    ),    
      
    
);

    if(!isset($arCurrentValues["HOVER_TEMPLATE"]) || $arCurrentValues["HOVER_TEMPLATE"] == "classic") {
        $arStyleMenuHover = array(
            "colored_light" => GetMessage("LIGHT_STYLE_MENU"),
            "colored_color" => GetMessage("COLOR_STYLE_MENU"),
        );
        
        $arTemplateParameters["STYLE_MENU_HOVER"] = array(
            "PARENT" => "MENU_HOVER_BLOCKS",
            "NAME" => GetMessage("STYLE_MENU_HOVER"),
            "TYPE" => "LIST",
            "VALUES" => $arStyleMenuHover,
            "DEFAULT" => "colored_light",
        ); 
                
        $arTemplateParameters["PICTURE_SECTION_HOVER"] = array (
            "PARENT" => "MENU_HOVER_BLOCKS",
            "NAME" => GetMessage("PICTURE_SECTION_HOVER"),
            "TYPE" => "LIST",
            "VALUES" => $arPictureSection,
            "DEFAULT" => "N",
        );
        
    }
    elseif($arCurrentValues["HOVER_TEMPLATE"] == "list") {
        $arStyleMenuHover = array(
            "colored_light" => GetMessage("LIGHT_STYLE_MENU"),
        );

        $arTemplateParameters["PARAMETERS"]["STYLE_MENU_HOVER"] = array (
            "PARENT" => "MENU_HOVER_BLOCKS",
            "NAME" => GetMessage("STYLE_MENU_HOVER"),
            "TYPE" => "LIST",
            "VALUES" => $arStyleMenuHover,
            "DEFAULT" => "colored_light",
        );

        $arPictureSection["IMG"] = GetMessage("PICTURE_SECTION_PICTURE");
        $arTemplateParameters["PICTURE_SECTION_HOVER"] = array (
            "PARENT" => "MENU_HOVER_BLOCKS",
            "NAME" => GetMessage("PICTURE_SECTION_HOVER"),
            "TYPE" => "LIST",
            "VALUES" => $arPictureSection,
            "DEFAULT" => "N",
        );

        $arPictureCategories = array("N" => GetMessage("PICTURE_CATEGARIES_N"), "left" => GetMessage("PICTURE_CATEGARIES_LEFT"), "right" => GetMessage("PICTURE_CATEGARIES_RIGHT"));
        $arTemplateParameters["PICTURE_CATEGARIES"] = array(
            "PARENT" => "MENU_HOVER_BLOCKS",
            "NAME" => GetMessage("PICTURE_CATEGARIES"),
            "TYPE" => "LIST",
            "VALUES" => $arPictureCategories,
            "DEFAULT" => "N",
        );

        $arColHoverMenu = array("1" => "1", "2" => "2", "3" => "3", "4" => "4" );


        $arTemplateParameters["HOVER_MENU_COL_LG"] = array(
            "PARENT" => "MENU_HOVER_BLOCKS",
            "NAME" => GetMessage("HOVER_MENU_COL_LG"),
            "TYPE" => "LIST",
            "VALUES" => $arColHoverMenu,
            "DEFAULT" => "2",
        );

        $arTemplateParameters["HOVER_MENU_COL_MD"] = array(
            "PARENT" => "MENU_HOVER_BLOCKS",
            "NAME" => GetMessage("HOVER_MENU_COL_MD"),
            "TYPE" => "LIST",
            "VALUES" => $arColHoverMenu,
            "DEFAULT" => "2",
        );    

        $arTemplateParameters["HOVER_MENU_COL_SM"] = array(
            "PARENT" => "MENU_HOVER_BLOCKS",
            "NAME" => GetMessage("HOVER_MENU_COL_SM"),
            "TYPE" => "LIST",
            "VALUES" => $arColHoverMenu,
            "DEFAULT" => "1",
        );

        $arTemplateParameters["HOVER_MENU_COL_XS"] = array(
            "PARENT" => "MENU_HOVER_BLOCKS",
            "NAME" => GetMessage("HOVER_MENU_COL_XS"),
            "TYPE" => "LIST",
            "VALUES" => $arColHoverMenu,
            "DEFAULT" => "1",
        );
    }


?>