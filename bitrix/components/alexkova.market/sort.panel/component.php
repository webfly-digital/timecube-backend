<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Bitrix\Iblock;

/**
 * @var array $arParams
 */

if (!Loader::includeModule('iblock')) {
    return;
}

$arResult = [];
$arParams["ELEMENT_SORT_FIELD"] = isset($arParams["ELEMENT_SORT_FIELD"]) && is_array($arParams["ELEMENT_SORT_FIELD"]) ? $arParams["ELEMENT_SORT_FIELD"] : [];

$arSortAPI = CIBlockParameters::GetElementSortFields(
    ['SHOWS', 'SORT', 'TIMESTAMP_X', 'NAME', 'ID', 'ACTIVE_FROM', 'ACTIVE_TO'],
    ['KEY_LOWERCASE' => 'Y']
);

if (Loader::includeModule('catalog')) {
    $arSortAPI = array_merge($arSortAPI, CCatalogIBlockParameters::GetCatalogSortFields());
}

function setNewSort($arSort)
{
    global $APPLICATION;
    if (!isset($_SESSION["USER_SORTPANEL"])) {
        $_SESSION["USER_SORTPANEL"] = [];
    }

    foreach ($arSort as $cell => $val) {
        $_SESSION["USER_SORTPANEL"][$cell] = $val;
    }
}

$arStripCode = [];
$arStripCodeSort = [];

foreach ($arParams["ELEMENT_SORT_FIELD"] as $k => $v) {
    $v = str_replace("PROPERTY_", "", $v);
    $arStripCode[$k] = $v;

    if (strpos($v, "PROPERTYSORT_") !== false) {
        $arStripCodeSort[$k] = $v;
        $v = str_replace("PROPERTYSORT_", "", $v);
        $arStripCode[$k] = $v;
    }
}

$propertyIterator = Iblock\PropertyTable::getList([
    'select' => ['ID', 'IBLOCK_ID', 'NAME', 'CODE', 'PROPERTY_TYPE', 'MULTIPLE', 'LINK_IBLOCK_ID', 'USER_TYPE'],
    'filter' => ['=IBLOCK_ID' => $arParams['IBLOCK_ID'], '=ACTIVE' => 'Y', "CODE" => $arStripCode],
    'order' => ['NAME' => 'ASC', 'SORT' => 'ASC']
]);

$arProperty = [];
while ($property = $propertyIterator->fetch()) {
    $propertyCode = (string)$property['CODE'] ?: $property['ID'];

    if ($propertyCode === 'MINIMUM_PRICE') {
        $property['NAME'] = GetMessage('PRICE_NAME');
    }

    $arProperty["PROPERTYSORT_" . $property['CODE']] = $property['NAME'];
    if (in_array(strtoupper("PROPERTY_" . $propertyCode), $arParams["ELEMENT_SORT_FIELD"], true)) {
        $arResult["SORT_PROPS"][strtoupper("PROPERTY_" . $propertyCode)] = ["PROPERTY_" . $propertyCode, 'asc', $property['NAME']];
    }
}

foreach ($arStripCodeSort as $k => $v) {
    if (in_array(strtoupper($v), $arParams["ELEMENT_SORT_FIELD"], true)) {
        $arResult["SORT_PROPS"][strtoupper($v)] = [$v, 'asc', $arProperty[$v] ?? ''];
    }
}

foreach ($arSortAPI as $k => $v) {
    if (in_array($k, $arParams["ELEMENT_SORT_FIELD"], true)) {
        $arResult["SORT_PROPS"][mb_strtoupper($k)] = [mb_strtoupper($k), "asc", $v];
    }
}

$userValues = [];

if (!empty($_REQUEST["sort"]) && !empty($_REQUEST["order"])) {
    setNewSort(["sort" => $_REQUEST["sort"], "order" => $_REQUEST["order"]]);
}
if (!empty($_REQUEST["num"])) {
    setNewSort(["num" => intval($_REQUEST["num"])]);
}
if (!empty($_REQUEST["view"])) {
    setNewSort(["view" => $_REQUEST["view"]]);
}

if (!empty($_SESSION["USER_SORTPANEL"]) && is_array($_SESSION["USER_SORTPANEL"])) {
    foreach ($_SESSION["USER_SORTPANEL"] as $cell => $val) {
        $_REQUEST[$cell] = $val;
    }
}

$this->IncludeComponentTemplate();