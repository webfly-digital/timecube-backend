<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
/** @var array $arCurrentValues */
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

if (!Loader::includeModule('iblock')) return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(array("-"=>" "));

$arIBlocks=array();
$db_iblock = CIBlock::GetList(array("SORT"=>"ASC"), array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];

$arSections=array();
$arSections[] = GetMessage('IBLOCK_SECTION_NONE');
$arFilterSect = array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:""), "IBLOCK_ID" => ($arCurrentValues["IBLOCK_ID"]!="-"?$arCurrentValues["IBLOCK_ID"]:""));
$db_section = CIBlockSection::GetList(array(), $arFilterSect, false);
while($arResSect = $db_section->GetNext())
{
    $arSections[$arResSect["ID"]] = $arResSect["NAME"];
}

$arProperty_LNS = array();
$rsProp = CIBlockProperty::GetList(array("sort"=>"asc", "name"=>"asc"), array("ACTIVE"=>"Y", "IBLOCK_ID"=>(isset($arCurrentValues["IBLOCK_ID"])?$arCurrentValues["IBLOCK_ID"]:$arCurrentValues["ID"])));
while ($arr=$rsProp->Fetch())
{
	$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	if (in_array($arr["PROPERTY_TYPE"], array("L", "N", "S")))
	{
		$arProperty_LNS[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}
}

$displayType = array(
    'block' => GetMessage('PROMO_RIBBON_PARAMETERS_DISPLAY_TYPE_BLOCK'),
    'mono' => GetMessage('PROMO_RIBBON_PARAMETERS_DISPLAY_TYPE_MONOLITH')
);
$hoverEffect = array(
    'approx' => GetMessage('PROMO_RIBBON_PARAMETERS_HOVER_EFFECT_APPROXIMATION'),
    'reduction' => GetMessage('PROMO_RIBBON_PARAMETERS_HOVER_EFFECT_REDUCTION'),
    'chico' => GetMessage('PROMO_RIBBON_PARAMETERS_HOVER_EFFECT_CHICO'),
    'goliath' => GetMessage('PROMO_RIBBON_PARAMETERS_HOVER_EFFECT_GOLIATH'),
    'selena' => GetMessage('PROMO_RIBBON_PARAMETERS_HOVER_EFFECT_SELENA'),
    'apollo' => GetMessage('PROMO_RIBBON_PARAMETERS_HOVER_EFFECT_APOLLO'),
    'default' => GetMessage('PROMO_RIBBON_PARAMETERS_HOVER_EFFECT_DEFAULT')
);

$arTemplateParameters = array(
    "IBLOCK_TYPE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("IBLOCK_TYPE"),
            "TYPE" => "LIST",
            "VALUES" => $arTypesEx,
            "REFRESH" => "Y",
    ),
    "IBLOCK_ID" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("IBLOCK_IBLOCK"),
            "TYPE" => "LIST",
            "VALUES" => $arIBlocks,
            "REFRESH" => "Y",
    ),
    "PARENT_SECTION" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("IBLOCK_SECTION_ID"),
            "TYPE" => "LIST",
            "VALUES" => $arSections,
            "DEFAULT" => '',
            "REFRESH" => "Y"
    ),
    "INCLUDE_SUBSECTIONS" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("CP_BNL_INCLUDE_SUBSECTIONS"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
    ),
    'NEWS_COUNT' => array(
        'PARENT' => 'BASE',
        'NAME' => GetMessage('T_IBLOCK_DESC_LIST_CONT'),
        'TYPE' => 'STRING',
        'DEFAULT' => '10',
    ),
    "FIELD_CODE" => CIBlockParameters::GetFieldCode(GetMessage("IBLOCK_FIELD"), "DATA_SOURCE"),
    "PROPERTY_CODE" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("T_IBLOCK_PROPERTY"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arProperty_LNS,
            "ADDITIONAL_VALUES" => "Y",
    ),
    'DISPLAY_TYPE' => array(
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => GetMessage('PROMO_RIBBON_PARAMETERS_DISPLAY_TYPE'),
        'TYPE' => 'LIST',
        'VALUES' => $displayType,
        'DEFAULT' => '',
    ),
    'HOVER_EFFECT' => array(
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => GetMessage('PROMO_RIBBON_PARAMETERS_HOVER_EFFECT'),
        'TYPE' => 'LIST',
        'VALUES' => $hoverEffect,
        'DEFAULT' => '',
    )
);