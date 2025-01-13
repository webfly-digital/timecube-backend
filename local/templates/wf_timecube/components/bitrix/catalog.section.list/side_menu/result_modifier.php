<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arFilter = [
    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
    'ACTIVE' => 'Y',
    'DEPTH_LEVEL' => 3
];
$rsSect = CIBlockSection::GetList(['SORT' => 'asc'],$arFilter);
$subs = [];
while ($sub = $rsSect->GetNext())
{
    if (empty($subs[$sub['IBLOCK_SECTION_ID']])) $subs[$sub['IBLOCK_SECTION_ID']] = [];
    $subs[$sub['IBLOCK_SECTION_ID']][] = $sub;
}


foreach ($arResult['SECTIONS'] as &$section) {
    $section['SUBS'] = $subs[$section['ID']];
}