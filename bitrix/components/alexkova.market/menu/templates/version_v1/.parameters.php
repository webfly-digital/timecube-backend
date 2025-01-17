<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arStyleMenu = array(
    "colored_light" => GetMessage("LIGHT_STYLE_MENU"),
    "colored_light_big" => GetMessage("LIGHT_STYLE_MENU_BIG"),
    "colored_light_lighten" => GetMessage("LIGHT_STYLE_MENU_LIGHT"),
    "colored_dark" => GetMessage("DARK_STYLE_MENU"),
    "colored_dark_big" => GetMessage("DARK_STYLE_MENU_BIG"),
    "colored_dark_lighten" => GetMessage("DARK_STYLE_MENU_LIGHT"),
    "colored_color" => GetMessage("COLOR_STYLE_MENU"),
    "colored_color_big" => GetMessage("COLOR_STYLE_MENU_BIG"),
    "colored_color_lighten" => GetMessage("COLOR_STYLE_MENU_LIGHT"),
);

$arStyleMenuHover = array(
    "colored_light" => GetMessage("LIGHT_STYLE_MENU"),
    "colored_color" => GetMessage("COLOR_STYLE_MENU"),
);

$arPictureSection = array("N" => GetMessage("PICTURE_SECTION_N"), "ICO" => GetMessage("PICTURE_SECTION_ICO"), "ICO_DEFAULT" => GetMessage("PICTURE_SECTION_ICO_DEFAULT"));
$arViewSubsection = array("LINE" => GetMessage("VIEW_SUBSECTION_LINE"), "COLUMN" => GetMessage("VIEW_SUBSECTION_COLUMN"));
$arTemplateMenuHover = array("classic" => GetMessage("CLASSIC_HOVER_MENU"), "list" => GetMessage("LIST_HOVER_MENU"));

if($arCurrentValues["TEMPLATE_MENU_HOVER"] === "list") {
    $arPictureSection["IMG"] = GetMessage("PICTURE_SECTION_PICTURE");
    unset($arStyleMenuHover["colored_color"]);
}
    

$arTemplateParameters = array(
    "FULL_WIDTH" => array(
        "PARENT" => "MENU_BLOCKS",
        "NAME" => GetMessage("FULL_WIDTH"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y",
    ),

    "FIXED_MENU" => array(
        "PARENT" => "MENU_BLOCKS",
        "NAME" => GetMessage("FIXED_MENU"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y",
    ),            

    "STYLE_MENU" => array(
        "PARENT" => "MENU_BLOCKS",
        "NAME" => GetMessage("STYLE_MENU"),
        "TYPE" => "LIST",
        "VALUES" => $arStyleMenu,
        "DEFAULT" => "colored_light",
    ),
    
    "TEXT_MOBIL_MENU" => array(
        "PARENT" => "MENU_BLOCKS",
        "NAME" => GetMessage("TEXT_MOBIL_MENU"),
        "TYPE" => "STRING",
        "DEFAULT" => "",
    ),  
  
    "TEMPLATE_MENU_HOVER" => array(
        "PARENT" => "MENU_HOVER_BLOCKS",
        "NAME" => GetMessage("TEMPLATE_MENU_HOVER"),
        "TYPE" => "LIST",
        "VALUES" => $arTemplateMenuHover,
        "DEFAULT" => "classic",
        "REFRESH" => "Y",
    ),    

    "STYLE_MENU_HOVER" => array(
        "PARENT" => "MENU_HOVER_BLOCKS",
        "NAME" => GetMessage("STYLE_MENU"),
        "TYPE" => "LIST",
        "VALUES" => $arStyleMenuHover,
        "DEFAULT" => "colored_light",
    ),     

   
    "PICTURE_SECTION" => array(
        "PARENT" => "MENU_HOVER_BLOCKS",
        "NAME" => GetMessage("PICTURE_SECTION"),
        "TYPE" => "LIST",
        "VALUES" => $arPictureSection,
        "DEFAULT" => "N",
    ),
        
    "SEARCH_FORM" => array(
        "PARENT" => "MENU_SEARCH_FORM",
        "NAME" => GetMessage("SEARCH_FORM"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N",
        "REFRESH" => "Y",
    ),
    
);

if($arCurrentValues["TEMPLATE_MENU_HOVER"] === "list")
{    
    
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

if(!CModule::IncludeModule("search"))
	return;

if($arCurrentValues["SEARCH_FORM"] === "Y")
{   
    $arTemplateParametersSearch = array(
        "PAGE" => array(
                "PARENT" => "MENU_SEARCH_FORM",
                "NAME" => GetMessage("CP_BST_FORM_PAGE"),
                "TYPE" => "STRING",
                "DEFAULT" => "#SITE_DIR#search/index.php",
        ),
        "NUM_CATEGORIES" => array(
                "PARENT" => "MENU_SEARCH_FORM",
                "NAME" => GetMessage("CP_BST_NUM_CATEGORIES"),
                "TYPE" => "STRING",
                "DEFAULT" => "1",
                "REFRESH" => "Y",
        ),
        "TOP_COUNT" => array(
                "PARENT" => "MENU_SEARCH_FORM",
                "NAME" => GetMessage("CP_BST_TOP_COUNT"),
                "TYPE" => "STRING",
                "DEFAULT" => "5",
                "REFRESH" => "Y",
        ),
        "ORDER" => array(
                "PARENT" => "MENU_SEARCH_FORM",
                "NAME" => GetMessage("CP_BST_ORDER"),
                "TYPE" => "LIST",
                "DEFAULT" => "date",
                "VALUES" => array(
                        "date" => GetMessage("CP_BST_ORDER_BY_DATE"),
                        "rank" => GetMessage("CP_BST_ORDER_BY_RANK"),
                ),
        ),
        "USE_LANGUAGE_GUESS" => Array(
                "PARENT" => "MENU_SEARCH_FORM",
                "NAME" => GetMessage("CP_BST_USE_LANGUAGE_GUESS"),
                "TYPE" => "CHECKBOX",
                "DEFAULT" => "Y",
        ),
        "CHECK_DATES" => array(
                "PARENT" => "MENU_SEARCH_FORM",
                "NAME" => GetMessage("CP_BST_CHECK_DATES"),
                "TYPE" => "CHECKBOX",
                "DEFAULT" => "N",
        ),
        "SHOW_OTHERS" => array(
                "PARENT" => "MENU_SEARCH_FORM",
                "NAME" => GetMessage("CP_BST_SHOW_OTHERS"),
                "TYPE" => "CHECKBOX",
                "DEFAULT" => "N",
                "REFRESH" => "Y",
        ),
    );
    
    $arTemplateParameters = array_merge($arTemplateParameters, $arTemplateParametersSearch);
    
    if($arCurrentValues["SHOW_OTHERS"] === "Y")
    {
    
        $arTemplateParameters["CATEGORY_OTHERS_TITLE"] = array(
                "PARENT" => "MENU_SEARCH_FORM",
                "NAME" => GetMessage("CP_BST_CATEGORY_TITLE"),
                "TYPE" => "STRING",
        );        
    }
        
    /**/
    
    $arPrice = array();
    if(CModule::IncludeModule("catalog"))
    {
            $rsPrice=CCatalogGroup::GetList($v1="sort", $v2="asc");
            while($arr=$rsPrice->Fetch())
                    $arPrice[$arr["NAME"]] = "[".$arr["NAME"]."] ".$arr["NAME_LANG"];
    }

    $arTemplateParametersSearch = array(
	"SHOW_INPUT" => array(
		"NAME" => GetMessage("TP_BST_SHOW_INPUT"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"REFRESH" => "Y",
                "PARENT" => "MENU_SEARCH_FORM",
	),
	"INPUT_ID" => array(
		"NAME" => GetMessage("TP_BST_INPUT_ID"),
		"TYPE" => "STRING",
		"DEFAULT" => "title-search-input",
                "PARENT" => "MENU_SEARCH_FORM",
	),
	"CONTAINER_ID" => array(
		"NAME" => GetMessage("TP_BST_CONTAINER_ID"),
		"TYPE" => "STRING",
		"DEFAULT" => "title-search",
                "PARENT" => "MENU_SEARCH_FORM",
	),
	"PRICE_CODE" => array(
		"PARENT" => "MENU_SEARCH_FORM",
		"NAME" => GetMessage("TP_BST_PRICE_CODE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arPrice,
	),
	"PRICE_VAT_INCLUDE" => array(
		"PARENT" => "MENU_SEARCH_FORM",
		"NAME" => GetMessage("TP_BST_PRICE_VAT_INCLUDE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"PREVIEW_TRUNCATE_LEN" => array(
		"PARENT" => "MENU_SEARCH_FORM",
		"NAME" => GetMessage("TP_BST_PREVIEW_TRUNCATE_LEN"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	),
	"SHOW_PREVIEW" => array(
		"NAME" => GetMessage("TP_BST_SHOW_PREVIEW"),
		"TYPE" => "CHECKBOX",
                "PARENT" => "MENU_SEARCH_FORM",
		"DEFAULT" => "Y",
		//"REFRESH" => "Y",
	),
    );
    
    $arTemplateParameters = array_merge($arTemplateParameters, $arTemplateParametersSearch);

    /*if (isset($arCurrentValues['SHOW_PREVIEW']) && 'Y' == $arCurrentValues['SHOW_PREVIEW'])
    {
            $arTemplateParameters["PREVIEW_WIDTH"] = array(
                    "NAME" => GetMessage("TP_BST_PREVIEW_WIDTH"),
                    "TYPE" => "STRING",
                    "DEFAULT" => 75,
                    "PARENT" => "MENU_SEARCH_FORM",
            );
            $arTemplateParameters["PREVIEW_HEIGHT"] = array(
                    "NAME" => GetMessage("TP_BST_PREVIEW_HEIGHT"),
                    "TYPE" => "STRING",
                    "DEFAULT" => 75,
                    "PARENT" => "MENU_SEARCH_FORM",
            );
    }*/

    if (CModule::IncludeModule('catalog') && CModule::IncludeModule('currency'))
    {
            $arTemplateParameters['CONVERT_CURRENCY'] = array(
                    'PARENT' => 'PRICES',
                    'NAME' => GetMessage('TP_BST_CONVERT_CURRENCY'),
                    'TYPE' => 'CHECKBOX',
                    'DEFAULT' => 'N',
                    'REFRESH' => 'Y',
                    "PARENT" => "MENU_SEARCH_FORM",
            );

            if (isset($arCurrentValues['CONVERT_CURRENCY']) && 'Y' == $arCurrentValues['CONVERT_CURRENCY'])
            {
                    $arCurrencyList = array();
                    $rsCurrencies = CCurrency::GetList(($by = 'SORT'), ($order = 'ASC'));
                    while ($arCurrency = $rsCurrencies->Fetch())
                    {
                            $arCurrencyList[$arCurrency['CURRENCY']] = $arCurrency['CURRENCY'];
                    }
                    $arTemplateParameters['CURRENCY_ID'] = array(
                            'PARENT' => 'MENU_SEARCH_FORM',
                            'NAME' => GetMessage('TP_BST_CURRENCY_ID'),
                            'TYPE' => 'LIST',
                            'VALUES' => $arCurrencyList,
                            'DEFAULT' => CCurrency::GetBaseCurrency(),
                            "ADDITIONAL_VALUES" => "Y",
                    );
            }
    }
    
    /*$NUM_CATEGORIES = intval($arCurrentValues["NUM_CATEGORIES"]);
    if($NUM_CATEGORIES <= 0)
            $NUM_CATEGORIES = 1;

    for($i = 0; $i < $NUM_CATEGORIES; $i++)
    {
            $arTemplateParameters["CATEGORY_".$i."_TITLE"] = array(
                    //"PARENT" => "CATEGORY_".$i,
                    "NAME" => GetMessage("CP_BST_CATEGORY_TITLE"),
                    "TYPE" => "STRING",
                    'PARENT' => "CATEGORY_".$i,
            );
    }*/
    
}

?>