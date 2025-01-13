<?
/** @global CMain $APPLICATION */
define('STOP_STATISTICS', true);
define('PUBLIC_AJAX_MODE', true);
define('NOT_CHECK_PERMISSIONS', true);

use Bitrix\Main,
	Bitrix\Catalog;

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

if ($_REQUEST["AddOpinionForm"])
{
	if (!CModule::IncludeModuleEx("disprove.reviewsmarket"))
	{
		$APPLICATION->RestartBuffer();
		header('Content-Type: application/json');
		echo Main\Web\Json::encode(array("STATUS" => "ERROR", "TEXT" => "SEARCHER"));
		die();
	}
	$_REQUEST["AddOpinionForm"]["rating"];
    $_REQUEST["AddOpinionForm"]["userName"];
    $_REQUEST["AddOpinionForm"]["city"];
    $_REQUEST["AddOpinionForm"]["pro"];
    $_REQUEST["AddOpinionForm"]["contra"];
    $_REQUEST["AddOpinionForm"]["text"];
    $_REQUEST["AddOpinionForm"]["period"];
    $_REQUEST["AddOpinionForm"]["date"] = date('Y.m.d H:i:s', strtotime(date("d.m.Y H:i:s")));
    $_REQUEST["AddOpinionForm"]["ìoderation"] = "N";
    $_REQUEST["AddOpinionForm"]["type"] = 1;
	$_REQUEST["AddOpinionForm"]["LID"] = SITE_ID;
	
	$mas = $_REQUEST["AddOpinionForm"];

	$ID = DRM::addReview($mas);
	if($IDd = $ID->Fetch()["id"]){
		$APPLICATION->RestartBuffer();
		header('Content-Type: application/json');
		echo Main\Web\Json::encode(array("STATUS" => "SUCCESS"));
	}else{
		$APPLICATION->RestartBuffer();
		header('Content-Type: application/json');
		echo Main\Web\Json::encode(array("STATUS" => "ERROR", "TEXT" => "UNDEFINED PRODUCT"));
	}
	die();
}