<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();


if(!CModule::IncludeModule("iblock"))
	return;

/*
if(!CModule::IncludeModule("alexkova.market"))
	return;
*/

$arPictureCategories = array("N" => GetMessage("PICTURE_CATEGARIES_N"), "LEFT" => GetMessage("PICTURE_CATEGARIES_LEFT"), "RIGHT" => GetMessage("PICTURE_CATEGARIES_RIGHT"));
$arPictureSection = array("N" => GetMessage("PICTURE_SECTION_N"), "ICO" => GetMessage("PICTURE_SECTION_ICO"), "PICTURE" => GetMessage("PICTURE_SECTION_PICTURE"));
$arViewSubsection = array("LINE" => GetMessage("VIEW_SUBSECTION_LINE"), "COLUMN" => GetMessage("VIEW_SUBSECTION_COLUMN"));

$arResolution = array();
for($i=1; $i<=12; $i++) {
    $arResolution[$i] = $i;
}


$arComponentParameters = array(
	"GROUPS" => array(
                "MENU_HOVER_BLOCKS" => array('NAME'=>GetMessage("MENU_HOVER_BLOCKS"), "SORT" => "100"),
	),
	"PARAMETERS" => array(
            
                "PICTURE_CATEGARIES" => array(
                    "PARENT" => "MENU_HOVER_BLOCKS",
                    "NAME" => GetMessage("PICTURE_CATEGARIES"),
                    "TYPE" => "LIST",
                    "VALUES" => $arPictureCategories,
                    "DEFAULT" => "N",
                ),
            
                "PICTURE_SECTION" => array(
                    "PARENT" => "MENU_HOVER_BLOCKS",
                    "NAME" => GetMessage("PICTURE_SECTION"),
                    "TYPE" => "LIST",
                    "VALUES" => $arPictureSection,
                    "DEFAULT" => "N",
                ),
            
                "VIEW_SUBSECTION" => array(
                    "PARENT" => "MENU_HOVER_BLOCKS",
                    "NAME" => GetMessage("VIEW_SUBSECTION"),
                    "TYPE" => "LIST",
                    "VALUES" => $arViewSubsection,
                    "DEFAULT" => "LINE",
                ),
            
                "MENU_HOVER_RESOLUTION_LG" => array(
                    "PARENT" => "MENU_HOVER_BLOCKS",
                    "NAME" => GetMessage("MENU_HOVER_RESOLUTION_LG"),
                    "TYPE" => "LIST",
                    "VALUES" => $arResolution,
                    "DEFAULT" => "1",
                ),
            
                "MENU_HOVER_RESOLUTION_MD" => array(
                    "PARENT" => "MENU_HOVER_BLOCKS",
                    "NAME" => GetMessage("MENU_HOVER_RESOLUTION_MD"),
                    "TYPE" => "LIST",
                    "VALUES" => $arResolution,
                    "DEFAULT" => "1",
                ),
            
                "MENU_HOVER_RESOLUTION_SM" => array(
                    "PARENT" => "MENU_HOVER_BLOCKS",
                    "NAME" => GetMessage("MENU_HOVER_RESOLUTION_SM"),
                    "TYPE" => "LIST",
                    "VALUES" => $arResolution,
                    "DEFAULT" => "1",
                ),
            
                "MENU_HOVER_RESOLUTION_XS" => array(
                    "PARENT" => "MENU_HOVER_BLOCKS",
                    "NAME" => GetMessage("MENU_HOVER_RESOLUTION_XS"),
                    "TYPE" => "LIST",
                    "VALUES" => $arResolution,
                    "DEFAULT" => "1",
                ),
             
		"CACHE_TIME"  =>  Array("DEFAULT"=>3600)
	),

);


?>