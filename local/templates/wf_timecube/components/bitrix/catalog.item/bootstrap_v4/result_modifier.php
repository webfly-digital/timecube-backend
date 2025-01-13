<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogElementComponent $component
 */

//region О производителе

$prop = $arResult['ITEM']['PROPERTIES']['MANUFACTUR'];
$res = \CIBlockElement::GetList([], ['IBLOCK_ID' => WF_MANUFACTURERS_IBLOCK_ID, 'CODE' => $prop['VALUE']], false, ['nTopCount' => 1], ['*']);
$dbEl = $res->GetNextElement();
if (!empty($dbEl)) {
    $arResult['MANUFACTUR'] = $dbEl->GetFields() + $dbEl->GetProperties();
    if (!empty($arResult['MANUFACTUR']['PREVIEW_PICTURE'])) {
        $arResult['MANUFACTUR']['IMAGE_SRC'] = \CFile::GetPath($arResult['MANUFACTUR']['PREVIEW_PICTURE']);
    }
}

//endregion О производителе

//region Hover photo

$prop = $arResult['ITEM']['PROPERTIES']['MORE_PHOTO'];
if (is_array($prop['VALUE'])) {
    $hoverPhotoId = current($prop['VALUE']);
} else {
    $hoverPhotoId = $prop['VALUE'];
}
$hoverPhoto = CFile::ResizeImageGet($hoverPhotoId, ['width' => 350, 'height' => 350]);
$arResult['ITEM']['HOVER_PHOTO'] = $hoverPhoto['src'];

//endregion Hover photo

$previewThumb = CFile::ResizeImageGet($arResult['ITEM']['PREVIEW_PICTURE']['ID'], ['width' => 350, 'height' => 350]);
$arResult['ITEM']['PREVIEW_PICTURE']['SRC'] = $previewThumb['src'];


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
