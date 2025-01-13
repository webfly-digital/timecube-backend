<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogElementComponent $component
 */

//region Категории с сео тайтлами
$select = ['IBLOCK_ID','ID','CODE','NAME','SECTION_PAGE_URL','UF_BROWSER_TITLE'];
$navChain = CIBlockSection::GetNavChain($arParams['IBLOCK_ID'],$arResult['IBLOCK_SECTION_ID'],$select);
$arResult['NAV_CHAIN'] = [];
while ($nav = $navChain->GetNext()) {
    $ires = new \Bitrix\Iblock\InheritedProperty\SectionValues($nav['IBLOCK_ID'], $nav["ID"]);
    $ival = $ires->getValues();

    $nav['NAME'] = $ival["SECTION_PAGE_TITLE"] ? $ival["SECTION_PAGE_TITLE"] :$nav['NAME'];
    if ($nav['CODE'] != WF_CATALOG_ROOT)
        $arResult['NAV_CHAIN'][] = $nav;
}

//endregion Категории с сео тайтлами

//region О производителе

$prop = $arResult["PROPERTIES"]['MANUFACTUR'];
$res = \CIBlockElement::GetList([], ['IBLOCK_ID' => WF_MANUFACTURERS_IBLOCK_ID, 'CODE' => $prop['VALUE']], false, ['nTopCount' => 1], ['*']);
$dbEl = $res->GetNextElement();
if (!empty($dbEl)) {
    $arResult['MANUFACTUR'] = $dbEl->GetFields() + $dbEl->GetProperties();
    if (!empty($arResult['MANUFACTUR']['PREVIEW_PICTURE'])) {
        $arResult['MANUFACTUR']['IMAGE_SRC'] = \CFile::GetPath($arResult['MANUFACTUR']['PREVIEW_PICTURE']);
    }
}

//endregion О производителе

//region thumbnails

$thumbsById = [];
foreach ($arResult['MORE_PHOTO'] as $key => $photo) {
    $resized = CFile::ResizeImageGet($photo['ID'], ['width' => 120, 'height' => 120]);
    $arResult['MORE_PHOTO'][$key]['THUMB_SRC'] = $resized['src'];
    $thumbsById[$photo['ID']] = $resized;
}
$resized = CFile::ResizeImageGet($arResult['DETAIL_PICTURE']['ID'], ['width' => 120, 'height' => 120]);
$thumbsById[$arResult['DETAIL_PICTURE']['ID']] = $resized;
$arResult['THUMBS'] = $thumbsById;

//endregion thumbnails


//3D shit
$show3D = false;
//$arResult['PROPERTIES']['OLD_ID']['VALUE'] = '1002'; // test dummy
if (!empty($arResult['PROPERTIES']['OLD_ID']['VALUE'])) {
    $webPath3D = '/3D/'.$arResult['PROPERTIES']['OLD_ID']['VALUE'].'/';
    $arResult['PATH_3D'] = $webPath3D;
    $path3D = $_SERVER['DOCUMENT_ROOT'] . $webPath3D;

    if (is_dir($path3D)) {
        $count3D = 0;
        while(file_exists($path3D.($count3D+1).'.jpg')) $count3D++;
        if ($count3D > 1) $show3D = true;
        $arResult['COUNT_3D'] = $count3D;
    }
}
$arResult['SHOW_3D'] = $show3D;


//
// check actions iblock
//
$actionMoetEnabled = false; // action id 10324
$actionPenEnabled = false; // action id 10327

$res = \Bitrix\Iblock\ElementTable::getList([
    'select'=>['ID','NAME','ACTIVE'],
    'filter'=>[
        'IBLOCK_ID'=>WF_ACTIONS_IBLOCK_ID,
        'ID'=> ['10324','10327']
    ]
]);
$actions = [];
while ($a = $res->fetch()) {
    if ($a['ID'] == '10324') $actionMoetEnabled = ($a["ACTIVE"] == 'Y');
    if ($a['ID'] == '10327') $actionPenEnabled = ($a["ACTIVE"] == 'Y');
}

$arResult['actionMoetEnabled'] = $actionMoetEnabled;
$arResult['actionPenEnabled'] = $actionPenEnabled;

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();
