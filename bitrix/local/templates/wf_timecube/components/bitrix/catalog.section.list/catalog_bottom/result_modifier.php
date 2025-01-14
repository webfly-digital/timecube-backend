<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

// get current section
$filter = ["IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE" => "Y", "GLOBAL_ACTIVE" => "Y"];
if (0 < intval($arParams['SECTION_ID']))
    $filter["ID"] = $arParams['SECTION_ID'];
else if ('' != $arParams['SECTION_CODE'])
    $filter["=CODE"] = $arParams['SECTION_CODE'];

$dbRes = CIBlockSection::GetList([], $filter, false, ['*', 'UF_*']);
$arCurSection = $dbRes->getNext();

//region linked
if (!empty($arCurSection)) {
    $sections = [];
    $filter = ['IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ACTIVE' => 'Y', '!=ID' => $arCurSection['ID']];
    if (!empty($arCurSection['UF_LINKED_SECTIONS'])) {
        // get linked
        $filter['=ID'] = $arCurSection['UF_LINKED_SECTIONS'];
    } else {
        if (!empty($arCurSection['IBLOCK_SECTION_ID'])) {
            // get nearest by parent filter
            $filter['=SECTION_ID'] = $arCurSection['IBLOCK_SECTION_ID'];
        } else {
            // get only root
            $filter['=DEPTH_LEVEL'] = '1';
        }
    }
    $select = ['ID', 'CODE', 'NAME', 'SECTION_PAGE_URL', 'DEPTH_LEVEL', 'UF_PICTURE'];
    $res = CIBlockSection::GetList(['SORT' => 'ASC'], $filter, false, $select, ['nPageSize' => 10]);
    while ($section = $res->GetNext()) {
        // get picture for section
        if (!empty($section['UF_PICTURE'])) {
            $section['IMG_SRC'] = CFile::GetPath($section['UF_PICTURE']);
        } else {
            // get from any element
            $elFilter = [
                'IBLOCK_ID' => $arParams['IBLOCK_ID'], 'SECTION_ID' => $section['ID'],
                'ACTIVE' => 'Y', 'INCLUDE_SUBSECTIONS' => 'Y', '!=DETAIL_PICTURE' => false, '=AVAILABLE' => 'Y'
            ];
            $el = CIBlockElement::GetList(['SORT' => 'ASC'], $elFilter, false, ["nPageSize" => 1], ['DETAIL_PICTURE'])->Fetch();
            $img = CFile::ResizeImageGet($el['DETAIL_PICTURE'], ['width' => 300, 'height' => 300]);
            $section['IMG_SRC'] = $img['src'];

        }
        // get seo props
        $ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($arParams["IBLOCK_ID"], $section["ID"]);
        if (!empty($ipropValues)) $section['IPROP'] = $ipropValues->getValues();

        $sections[] = $section;
    }
}
$arResult['SECTIONS'] = $sections;
//endregion linked