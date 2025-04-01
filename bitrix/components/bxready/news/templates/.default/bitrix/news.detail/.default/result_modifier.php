<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$productSlider = array();
if (is_array($arResult["DETAIL_PICTURE"])){
	$productSlider[] = $arResult["DETAIL_PICTURE"];
}

if (is_array($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"]) 
&& is_array($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"])
&& count($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"])>0){

	foreach($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as $val){
		$productSlider[] = CFile::GetFileArray($val);
	}
}
$arResult['IMAGES'] = $productSlider;

foreach($arResult["IMAGES"] as &$image){
	$tmb = CFile::ResizeImageGet($image["ID"], array('width'=>400, 'height'=>300));
	foreach ($tmb as $cell=>$val){
		$image["TMB"][strtoupper($cell)] = $val;
	}
}

$arResult["FILES"] = array();

if (isset($arResult["PROPERTIES"]["FILES"])
	&&
	is_array($arResult["PROPERTIES"]["FILES"]["VALUE"])
	&& count($arResult["PROPERTIES"]["FILES"]["VALUE"])>0){

	foreach($arResult["PROPERTIES"]["FILES"]["VALUE"] as $val){
		$tFile = CFile::GetFileArray($val);
		$arExt = explode(".", $tFile["FILE_NAME"]);
		if (in_array(strtolower($arExt[count($arExt)-1]), array('xls', 'xlsx', 'rar', 'pdf', 'doc', 'docx')))
			$tFile["EXTENTION"] = strtolower($arExt[count($arExt)-1]);
		else
			$tFile["EXTENTION"] = 'file';

		$arResult["FILES"][] = $tFile;
	}
}

$arResult["VIDEO"] = array();

if (isset($arResult["PROPERTIES"]["VIDEO"])
	&& is_array($arResult["PROPERTIES"]["VIDEO"]["VALUE"])
	&& count($arResult["PROPERTIES"]["VIDEO"]["VALUE"])>0){

	foreach($arResult["PROPERTIES"]["VIDEO"]["VALUE"] as $cell=>$val){
		$arResult["VIDEO"][] = array(
			"TITLE"=>$arResult["PROPERTIES"]["VIDEO"]["DESCRIPTION"][$cell],
			"LINK"=>$val
		);
	}
}

$arResult["LINKS"] = array();

$arResult["BUTTON"] = array(
    "SHOW_BUTTON" => $arParams["SHOW_REQUEST_BUTTON"],
    "BUTTON_ACTION" => $arParams["REQUEST_BUTTON_ACTION"],
    "BUTTON_LINK" => $arParams["REQUEST_BUTTON_LINK"],
    "BUTTON_TARGET" => $arParams["REQUEST_BUTTON_TARGET"],
    "BUTTON_JS_CLASS" => $arParams["REQUEST_BUTTON_JS_CLASS"],
    "BUTTON_TITLE" => $arParams["REQUEST_BUTTON_TITLE"],
);

if (isset($arResult["PROPERTIES"]["MORE_URLS"])
	&& is_array($arResult["PROPERTIES"]["MORE_URLS"]["VALUE"])
	&& count($arResult["PROPERTIES"]["MORE_URLS"]["VALUE"])>0){

	foreach($arResult["PROPERTIES"]["MORE_URLS"]["VALUE"] as $cell=>$val){
		$arResult["LINKS"][] = array(
			"TITLE"=>$arResult["PROPERTIES"]["MORE_URLS"]["DESCRIPTION"][$cell],
			"LINK"=>$val
		);
	}
}

if (isset($arResult["DISPLAY_PROPERTIES"]["MORE_PHOTO"]))
	unset($arResult["DISPLAY_PROPERTIES"]["MORE_PHOTO"]);
if (isset($arResult["DISPLAY_PROPERTIES"]["FILES"]))
	unset($arResult["DISPLAY_PROPERTIES"]["FILES"]);
if (isset($arResult["DISPLAY_PROPERTIES"]["OTHER_ELEMENTS"]))
	unset($arResult["DISPLAY_PROPERTIES"]["OTHER_ELEMENTS"]);
?>