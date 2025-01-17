<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Bitrix\Iblock;

if (!Loader::includeModule('iblock'))
	return;

$arResult = array();

$arSortAPI = CIBlockParameters::GetElementSortFields(
	array('SHOWS', 'SORT', 'TIMESTAMP_X', 'NAME', 'ID', 'ACTIVE_FROM', 'ACTIVE_TO'),
	array('KEY_LOWERCASE' => 'Y')
);

if (Loader::includeModule('catalog'))
{
    $arSortAPI = array_merge($arSortAPI, CCatalogIBlockParameters::GetCatalogSortFields());
}

function setNewSort($arSort)
{
    global $APPLICATION;
    $arrayExclude = array();
    
    foreach($arSort as $cell=>$val)
    {
        $_SESSION["USER_SORTPANEL"][$cell] = $val;
    }
}

$arStripCode = array();
$arStripCodeSort = array();
foreach ($arParams["ELEMENT_SORT_FIELD"] as $k => $v):
    $v = str_replace("PROPERTY_", "", $v);
    $arStripCode[$k] = $v;
        
    if(strripos($v, "PROPERTYSORT_") !== false) {     
        $arStripCodeSort[$k] = $v;
        $v = str_replace("PROPERTYSORT_", "", $v);        
        $arStripCode[$k] = $v;
    }    
endforeach;

$propertyIterator = Iblock\PropertyTable::getList(array(
        'select' => array('ID', 'IBLOCK_ID', 'NAME', 'CODE', 'PROPERTY_TYPE', 'MULTIPLE', 'LINK_IBLOCK_ID', 'USER_TYPE'),
        'filter' => array('=IBLOCK_ID' => $arParams['IBLOCK_ID'], '=ACTIVE' => 'Y', "CODE" => $arStripCode),
        'order' => array('NAME' => 'ASC', 'SORT' => 'ASC')
));
$arProperty  = array();
while ($property = $propertyIterator->fetch())
{
        $propertyCode = (string)$property['CODE'];
        if ($propertyCode == '')
                $propertyCode = $property['ID'];
        
        if ($propertyCode == 'MINIMUM_PRICE')
                $property['NAME'] = GetMessage('PRICE_NAME');
        
        $arProperty["PROPERTYSORT_".$property['CODE']] = $property['NAME'];
        if(in_array(strtoupper("PROPERTY_".$propertyCode), $arParams["ELEMENT_SORT_FIELD"]))
            $arResult["SORT_PROPS"][strtoupper("PROPERTY_".$propertyCode)] = array("PROPERTY_".$propertyCode, 'asc', $property['NAME']);
}
if(count($arStripCodeSort)>0) {
    foreach ($arStripCodeSort as $k => $v) {
        if(in_array(strtoupper($v), $arParams["ELEMENT_SORT_FIELD"]))
            $arResult["SORT_PROPS"][strtoupper($v)] = array($v, 'asc', $arProperty[$v]);
    }
}

foreach ($arSortAPI as $k => $v) {
    if(in_array($k, $arParams["ELEMENT_SORT_FIELD"]))
        $arResult["SORT_PROPS"][mb_strtoupper($k)] = array(mb_strtoupper($k), "asc", $v);
}

$userValues = array();

if (isset($_REQUEST["sort"]) && isset($_REQUEST["order"]))
    setNewSort(array("sort"=>$_REQUEST["sort"], "order"=>$_REQUEST["order"]));
if (isset($_REQUEST["num"]))
    setNewSort(array("num"=>intval($_REQUEST["num"])));
if (isset($_REQUEST["view"]))
    setNewSort(array("view"=>$_REQUEST["view"]));

if (isset($_SESSION["USER_SORTPANEL"]) && is_array($_SESSION["USER_SORTPANEL"]) && count($_SESSION["USER_SORTPANEL"]>0))
{
    foreach ($_SESSION["USER_SORTPANEL"] as $cell=>$val)
    {
        $_REQUEST[$cell] = $val;
    }
}

$this->IncludeComponentTemplate();

?>