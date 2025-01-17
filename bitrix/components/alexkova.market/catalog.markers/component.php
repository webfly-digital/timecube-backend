<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arResult = array();

$templatePage = "";

if ($_REQUEST['bxr_ajax'] == 'yes'){
	$templatePage = 'get_list';

}

$tempMarkers = array("ACTION", "NEW", "RECCOMEND", "HIT");
foreach($tempMarkers as $cell){
	if (!isset($arParams["TAB_".$cell."_SETTING"]) == "Y"){
		$arParams["TAB_".$cell."_SETTING"] = "Y";
		$arParams["TAB_".$cell."_SORT"] = 100+$i;
		$i++;
	}
}

if (\Bitrix\Main\Loader::includeModule("iblock"))
{
	if (
		isset($_REQUEST["MARK"])
		&& strlen($_REQUEST["MARK"])>0
		&& is_array($_SESSION["MARKERS_SETTINGS"])
		&& !empty($_SESSION["MARKERS_SETTINGS"])
	){
		$arResult["MARK"] = $_REQUEST["MARK"];
		$templatePage = "get_list";
	}

	$arMarkers = array();
	$tempMarkers = array("ACTION", "NEW", "RECCOMEND", "HIT");
	foreach($tempMarkers as $cell){
		if ($arParams["TAB_".$cell."_SETTING"] == "Y"){
			$arMarkers[$arParams["TAB_".$cell."_SORT"]] = $cell;
		}
	}
	ksort($arMarkers);

	$arResult["MARKERS_LIST"] = $arMarkers;

	if (strlen(COption::GetOptionString('alexkova.market', 'list_marker_type'))>0){
		$arParams["BXREADY_LIST_MARKER_TYPE"] = COption::GetOptionString('alexkova.market', 'list_marker_type');
	}
}

$this->IncludeComponentTemplate($templatePage);

?>