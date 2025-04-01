<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
unset($arResult["COMBO"]);
$APPLICATION->RestartBuffer();
echo CUtil::PHPToJSObject($arResult, true);
?>