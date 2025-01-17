<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */

$arLocHoriz = array("rigth" => GetMessage("BUTTON_UP_HORIZONTALLY_RIGHT"), "left" => GetMessage("BUTTON_UP_HORIZONTALLY_LEFT"));

$arComponentParameters = array(
	"GROUPS" => array(),
	"PARAMETERS" => array(
		"LOCATION_HORIZONTALLY" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("LOCATION_HORIZONTALLY"),
			"TYPE" => "LIST",
			"VALUES" => $arLocHoriz,
			"DEFAULT" => "right",
		),
                "BUTTON_UP_HORIZONTALLY_INDENT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("BUTTON_UP_HORIZONTALLY_INDENT"),
			"TYPE" => "STRING",
			"DEFAULT" => "0",
		),
                "BUTTON_UP_VERTICAL_INDENT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("BUTTON_UP_VERTICAL_INDENT"),
			"TYPE" => "STRING",
			"DEFAULT" => "0",
		),
                "BUTTON_UP_TOP_SHOW" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("BUTTON_UP_TOP_SHOW"),
			"TYPE" => "STRING",
			"DEFAULT" => "150",
		),
                "BUTTON_UP_SPEED" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("BUTTON_UP_SPEED"),
			"TYPE" => "STRING",
			"DEFAULT" => "150",
		),
	),
);

