<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?    
    if(!empty($arResult["PROPERTIES"]["VIDEO"]["VALUE"]))
        $arResult["PROPERTIES"]["VIDEO"]["VALUE"] = array_merge($arResult["PROPERTIES"]["VIDEO"]["~VALUE"], $arResult["PROPERTIES"]["UF_VIDEO"]);
    else
       $arResult["PROPERTIES"]["VIDEO"]["VALUE"] =  $arResult["PROPERTIES"]["UF_VIDEO"];
?>
<?
    switch($arParams["VIDEO_PLAYER"]){
        case "BITRIX": include_once 'video_bitrix.php';break;
        case "MEJ": include_once 'video_mediaelementjs.php'; break;
        case "IFRAME": include_once 'video_iframe.php'; break;
        default: include_once 'video_mediaelementjs.php'; break;
    }    
?>
