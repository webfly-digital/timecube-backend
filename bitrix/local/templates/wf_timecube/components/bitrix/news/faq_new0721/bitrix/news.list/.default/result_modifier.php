<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$sectionData = \CIblockSection::getList(['SORT' => 'asc'], ['IBLOCK_ID' => $arResult['ID'], 'ACTIVE' => 'Y'], true, ['ID', 'NAME', "LIST_PAGE_URL"], false);

while ($ob_s = $sectionData->fetch()) {
    $arResult["SECTION"]['LIST_SECTION'] [] = $ob_s;
}
