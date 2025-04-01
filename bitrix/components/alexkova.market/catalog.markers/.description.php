<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("MARKET_BESTSELLERS_NAME"),
	"DESCRIPTION" => GetMessage("MARKET_BESTSELLERS_DESCRIPTION"),
	"ICON" => "/images/icon.gif",
	"COMPLEX" => "Y",
	"SORT" => 10,
	"PATH" => array(
		"ID" => "emarket",
		"NAME"=> GetMessage("MARKET_SECTION_DESCRIPTION"),
	),
);

?>