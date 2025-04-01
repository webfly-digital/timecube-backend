<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 */

$arIDS = [];
$iblockID = 0;
if (count($arResult) > 0) {

    $arResult = [
        "ITEMS" => $arResult
    ];


    foreach ($arResult["ITEMS"] as $arElement) {
        $arIDS[] = $arElement["ID"];
        $iblockID = $arElement["IBLOCK_ID"];
    }

    $arFilter = [
        "ACTIVE" => "Y",
        "ID" => $arIDS,
        "IBLOCK_ID" => $iblockID
    ];

    $arSelect = ["ID", "DETAIL_PICTURE"];

    $res = CIblockElement::GetList([], $arFilter, false, false, $arSelect);
    while ($arFields = $res->Fetch()) {
        if ($arFields["DETAIL_PICTURE"] > 0) $arFields["DETAIL_PICTURE"] = CFile::GetFileArray($arFields["DETAIL_PICTURE"]);
        $arResult["DATA"][$arFields["ID"]] = $arFields;

    }
}

if (!empty($arResult["ITEMS"]) && is_array($arResult["ITEMS"])) {
    foreach ($arResult["ITEMS"] as $val) {
        $arResult["JSON"][$val["ID"]] = 1;
    }
}