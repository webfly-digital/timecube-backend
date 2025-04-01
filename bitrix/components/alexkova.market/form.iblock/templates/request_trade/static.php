<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent(
	"alexkova.market:iblock.element.add.form",
	"request_trade",
	$arParams,
	$component,
	array("HIDE_ICONS"=>"Y")
);?>