<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Alexkova\Bxready\Core;

if (intval($arParams["IBLOCK_ID"])<=0){
	return false;
}

if (!CModule::IncludeModule('alexkova.bxready')) return false;

$this->IncludeComponentTemplate();