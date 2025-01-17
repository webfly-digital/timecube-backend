<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Bitrix\Iblock;

if (!Loader::includeModule('iblock'))
	return;
$catalogIncluded = Loader::includeModule('catalog');

$iblockExists = (!empty($arCurrentValues['IBLOCK_ID']) && (int)$arCurrentValues['IBLOCK_ID'] > 0);

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$iblockFilter = (
	!empty($arCurrentValues['IBLOCK_TYPE'])
	? array('TYPE' => $arCurrentValues['IBLOCK_TYPE'], 'ACTIVE' => 'Y')
	: array('ACTIVE' => 'Y')
);
$rsIBlock = CIBlock::GetList(array('SORT' => 'ASC'), $iblockFilter);
while ($arr = $rsIBlock->Fetch())
	$arIBlock[$arr['ID']] = '['.$arr['ID'].'] '.$arr['NAME'];
unset($arr, $rsIBlock, $iblockFilter);

$arProperty = array();
$arProperty_N = array();
$arProperty_X = array();
$arProperty_F = array();
if ($iblockExists)
{
	$propertyIterator = Iblock\PropertyTable::getList(array(
		'select' => array('ID', 'IBLOCK_ID', 'NAME', 'CODE', 'PROPERTY_TYPE', 'MULTIPLE', 'LINK_IBLOCK_ID', 'USER_TYPE'),
		'filter' => array('=IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'], '=ACTIVE' => 'Y'),
		'order' => array('NAME' => 'ASC', 'SORT' => 'ASC')
	));
	while ($property = $propertyIterator->fetch())
	{
		$propertyCode = (string)$property['CODE'];

		if ($propertyCode == '') continue;
		if ($propertyCode == 'MINIMUM_PRICE') continue;

		$propertyName = '['.$propertyCode.'] '.$property['NAME'];
		$propertyCode = "PROPERTY_".$propertyCode;
		if ($property['PROPERTY_TYPE'] != Iblock\PropertyTable::TYPE_FILE)
		{
			$arProperty[$propertyCode] = $propertyName;

			if ($property['MULTIPLE'] == 'Y')
				$arProperty_X[$propertyCode] = $propertyName;
			elseif ($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_LIST)
				$arProperty_X[$propertyCode] = $propertyName;
			elseif ($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_ELEMENT && (int)$property['LINK_IBLOCK_ID'] > 0)
				$arProperty_X[$propertyCode] = $propertyName;
		}
		else
		{
			if ($property['MULTIPLE'] == 'N')
				$arProperty_F[$propertyCode] = $propertyName;
		}

		if ($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_NUMBER)
			$arProperty_N[$propertyCode] = $propertyName;
	}
	unset($propertyCode, $propertyName, $property, $propertyIterator);
}
$arProperty_LNS = $arProperty;

$arCatalogView = array("TITLE" => GetMessage("KZNC_VIEW_TITLE"), "LIST" => GetMessage("KZNC_VIEW_LIST"), "TABLE" => GetMessage("KZNC_VIEW_TABLE"));
$arPageCount = array(8 => 8, 16 => 16, 32 => 32);

$arSort["NAME"] = GetMessage("KZNC_SORT_NAME_NAME");
$arSort["PROPERTY_MINIMUM_PRICE"] = GetMessage("KZNC_SORT_PRICE_NAME");

$arThemesMessages = array(
	"default" => GetMessage("KZNC_THEME_DEFAULT"), 
	"solid" => GetMessage("KZNC_THEME_SOLID"), 
	);
$arThemes = array();
$dir = trim(preg_replace("'[\\\\/]+'", "/", dirname(__FILE__)."/templates/.default/themes/"));
if (is_dir($dir) && $directory = opendir($dir)):
  while (($file = readdir($directory)) !== false) :
    if ($file != "." && $file != ".." && is_dir($dir.$file))
      $arThemes[$file] = (!empty($arThemesMessages[$file]) ? $arThemesMessages[$file] : strtoupper(substr($file, 0, 1)).strtolower(substr($file, 1)));
  endwhile;
  closedir($directory);
endif;

$arProperty = array_merge($arProperty_LNS, $arSort);

$arCurrentSortFields = array();
foreach ($arCurrentValues["ELEMENT_SORT_FIELD"] as $val):
	if(array_key_exists($val, $arProperty))
		$arCurrentSortFields[$val] = $arProperty[$val];
endforeach;
$arComponentParameters = array(
	"GROUPS" => array(
		"SORT_PANEL_SETTINGS" => array(
			"NAME" => GetMessage("KZNC_IBLOCK_SORT_PANEL_SETTINGS"),
		),
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_IBLOCK"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"THEME" => array(
			"PARENT" => "SORT_PANEL_SETTINGS",
			"NAME" => GetMessage("KZNC_THEME_NAME"),
			"TYPE" => "LIST",
			"VALUES" => $arThemes,
			"MULTIPLE" => "N",
			"DEFAULT" => "default",
		),	
		"ELEMENT_SORT_FIELD" => array(
			"PARENT" => "SORT_PANEL_SETTINGS",
			"NAME" => GetMessage("IBLOCK_ELEMENT_SORT_FIELD"),
			"TYPE" => "LIST",
			"VALUES" => $arProperty,
			"MULTIPLE" => "Y",
			"DEFAULT" => "sort",
			"REFRESH" => "Y",
			"SIZE" => 10,
		),
		"CATALOG_DEFAULT_SORT" => array(
			"PARENT" => "SORT_PANEL_SETTINGS",
			"NAME" => GetMessage("KZNC_CATALOG_DEFAULT_SORT"),
			"TYPE" => "LIST",
			"DEFAULT" => "sort",
			"VALUES" => $arCurrentSortFields,
		),
		"PAGE_ELEMENT_COUNT_SHOW" => array(
			"PARENT" => "SORT_PANEL_SETTINGS",
			"NAME" => GetMessage("KZNC_PAGE_ELEMENT_COUNT_SHOW"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"REFRESH" => "Y",
		),
	),
);
if($arCurrentValues["PAGE_ELEMENT_COUNT_SHOW"]=="Y") {
	$arComponentParameters["PARAMETERS"]["PAGE_ELEMENT_COUNT"] = array(
			"PARENT" => "SORT_PANEL_SETTINGS",
			"NAME" => GetMessage("IBLOCK_PAGE_ELEMENT_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "16",
		);
	$arComponentParameters["PARAMETERS"]["PAGE_ELEMENT_COUNT_LIST"] = array(
			"PARENT" => "SORT_PANEL_SETTINGS",
			"NAME" => GetMessage("KZNC_PAGE_ELEMENT_COUNT_LIST"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arPageCount,
		);
}
$arComponentParameters["PARAMETERS"]["CATALOG_VIEW_SHOW"] = array(
		"PARENT" => "SORT_PANEL_SETTINGS",
		"NAME" => GetMessage("KZNC_CATALOG_VIEW_SHOW"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"REFRESH" => "Y",
	);
if($arCurrentValues["CATALOG_VIEW_SHOW"]=="Y") {
	$arComponentParameters["PARAMETERS"]["DEFAULT_CATALOG_VIEW"] = array(
			"PARENT" => "SORT_PANEL_SETTINGS",
			"NAME" => GetMessage("KZNC_DEFAULT_CATALOG_VIEW"),
			"TYPE" => "LIST",
			"VALUES" => $arCatalogView,
			"DEFAULT" => "TITLE",
		);
}
?>