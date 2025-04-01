<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$params = array();
$formId = intval($_REQUEST["FORM_ID"]);
if (isset($_SESSION["ALEXKOVA.MARKET"]) &&
	isset($_SESSION["ALEXKOVA.MARKET"]['FORMS_PARAM']) &&
	isset($_SESSION["ALEXKOVA.MARKET"]["FORMS_PARAM"][$formId])
) {
	$params = unserialize($_SESSION["ALEXKOVA.MARKET"]["FORMS_PARAM"][$formId]);
}

if (!$_REQUEST["strIMessage"] && (!$formId || !is_array($params) || empty($params))) {
	die("Error form id");
}

$params["AJAX"] = 'Y';
$params["FIRST"] = htmlspecialcharsbx($_REQUEST["first"]);

if ($_REQUEST["TARGET_URL"]) {
	$params["TARGET_URL"] = htmlspecialcharsbx($_REQUEST["TARGET_URL"]);
}

$APPLICATION->IncludeComponent(
	"alexkova.market:iblock.element.add.form",
	"request_trade",
	$params,
	false
);