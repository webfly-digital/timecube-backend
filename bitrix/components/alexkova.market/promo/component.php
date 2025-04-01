<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/** @var $this \Bxready\Promo */

if(!CModule::IncludeModule("iblock"))
{
	ShowError(GetMessage("CC_BCF_MODULE_NOT_INSTALLED"));
	return;
}
if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$cacheParams = array($arParams['COMPONENT_TEMPLATE']);

if($this->StartResultCache(false,$cacheParams))
{
        $this->includeComponentTemplate();
}