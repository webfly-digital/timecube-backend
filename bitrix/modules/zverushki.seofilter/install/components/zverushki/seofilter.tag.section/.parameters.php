<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */

if(!CModule::IncludeModule("iblock"))
	return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(array("-"=>" "));

$arIBlocks=array();
$db_iblock = CIBlock::GetList(array("SORT"=>"ASC"), array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];

if($arCurrentValues["IBLOCK_TYPE"]):
	$arFilter = array('IBLOCK_ID' => $arCurrentValues["IBLOCK_ID"], "DEPTH_LEVEL" => 1);
	$rsSections = CIBlockSection::GetList(array('SORT' => 'ASC'), $arFilter);
	while ($arSection = $rsSections->Fetch())
	{
	    $arSections[$arSection["ID"]] = $arSection["NAME"];
	}
endif;

$arSorts = array("ASC" => GetMessage("ZVERUSHKI_T_IBLOCK_DESC_ASC"), "DESC" => GetMessage("ZVERUSHKI_T_IBLOCK_DESC_DESC"));
$arSortFields = array(
		"ID" => GetMessage("ZVERUSHKI_T_IBLOCK_DESC_FID"),
		"SORT" => GetMessage("ZVERUSHKI_T_IBLOCK_DESC_SORT"),
		"PAGE_SECTION_TITLE" => GetMessage("ZVERUSHKI_T_IBLOCK_DESC_FNAME"),
		"COUNT" => GetMessage("ZVERUSHKI_T_IBLOCK_DESC_COUNT")
	);

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

$arComponentParameters = array(
	"GROUPS" => array(
		"PROPLIST" => array(
			"NAME" => GetMessage("ZVERUSHKI_GROUP_PROPLIST")
		),
		"DOPS" => array(
			"NAME" => GetMessage("ZVERUSHKI_GROUP_DOPS")
		)
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ZVERUSHKI_T_IBLOCK_DESC_LIST_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "news",
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ZVERUSHKI_T_IBLOCK_DESC_LIST_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '={$_REQUEST["IBLOCK_ID"]}',
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH" => "Y",
		),
		"SECTION_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ZVERUSHKI_T_ELEMENT_DESC_LIST_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["ELEMENT_ID"]}',
		),
		"NEWS_COUNT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ZVERUSHKI_T_IBLOCK_DESC_LIST_CONT"),
			"TYPE" => "STRING",
			"DEFAULT" => "20",
		),
		"ITEMS_VISIBLE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ZVERUSHKI_T_IBLOCK_DESC_LIST_CONT_VISIBLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "20",
		),
		"SORT_BY1" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ZVERUSHKI_T_IBLOCK_DESC_IBORD1"),
			"TYPE" => "LIST",
			"DEFAULT" => "SORT",
			"VALUES" => $arSortFields,
			"ADDITIONAL_VALUES" => "Y",
		),
		"SORT_ORDER1" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ZVERUSHKI_T_IBLOCK_DESC_IBBY1"),
			"TYPE" => "LIST",
			"DEFAULT" => "DESC",
			"VALUES" => $arSorts,
			"ADDITIONAL_VALUES" => "Y",
		),
		"TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ZVERUSHKI_T_SECTION_FVIEW"),
			"TYPE" => "LIST",
			"DEFAULT" => "only",
			"VALUES" => array(
                        'only' => GetMessage("ZVERUSHKI_T_SECTION_FVIEW_VAL_ONLY"),
                        'only_child' => GetMessage("ZVERUSHKI_T_SECTION_FVIEW_VAL_ONLY_CHILD"),
                        'only_parent' => GetMessage("ZVERUSHKI_T_SECTION_FVIEW_VAL_ONLY_PARENT"),
                    ),
			"ADDITIONAL_VALUES" => "Y",
		),
		"IDENTIFIER" => array(
			"PARENT" => "DOPS",
			"NAME" => GetMessage("ZVERUSHKI_T_IBLOCK_IDENTIFIER"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
	),
);