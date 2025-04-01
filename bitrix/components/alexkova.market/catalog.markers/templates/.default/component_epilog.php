<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();


$_SESSION["BXR_MARKERS_SETTINGS"] = $arParams;
if ($arParams["BXREADY_LIST_SLIDER"] == "Y"):
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/slick/slick.js');
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/js/slick/slick.css', false);
endif;