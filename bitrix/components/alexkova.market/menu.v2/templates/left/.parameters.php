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
    
    "SUBMENU" => array(
        "PARENT" => "MENU_BLOCKS",
        "NAME" => GetMessage("SUBMENU"),
        "TYPE" => "LIST",
        "VALUES" => $arSubmenu,
        "DEFAULT" => "ACTIVE_SHOW",
    )    
    
);

?>