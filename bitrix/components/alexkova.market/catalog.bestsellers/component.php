<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arResult = array();

$BID = intval($_REQUEST["ID"])>0 ? intval($_REQUEST["ID"]): 0;

if ($_REQUEST['bxr_ajax'] == 'yes'){
	$templatePage = 'get_list';

}

if (\Bitrix\Main\Loader::includeModule("iblock"))
{

	if ($this->StartResultCache(false,array("ID"=>$BID))){

		if (strlen(COption::GetOptionString('alexkova.market', 'list_marker_type'))>0){
			$arParams["BXREADY_LIST_MARKER_TYPE"] = COption::GetOptionString('alexkova.market', 'list_marker_type');
		}


		if ($BID>0){
			$templatePage = "get_list";

			$arSelect = array("ID", "NAME", "PROPERTY_ITEMS");
			$arFilter = array(
				"ACTIVE"=>"Y",
				"ID"=>$BID,
				"IBLOCK_ID"=>$arParams["BESTSELLER_IBLOCK_ID"]
			);
			$res = CIblockElement::GetList(array(), $arFilter, false, false, $arSelect);
			if ($arFields = $res->Fetch()){

				if (is_array($arFields["PROPERTY_ITEMS_VALUE"]) && count($arFields["PROPERTY_ITEMS_VALUE"])>0){
					$arResult["BESTSELLERS_ITEMS"] = $arFields["PROPERTY_ITEMS_VALUE"];
				}
			}
		}else{
			$arSelect = array("ID", "NAME", "PROPERTY_ITEMS");
			$arFilter = array(
				"ACTIVE"=>"Y",
				"IBLOCK_ID"=>intval($arParams["BESTSELLER_IBLOCK_ID"]),
			);

			$res = CIblockElement::GetList(array("SORT"=>"ASC"), $arFilter, false, false, $arSelect);
			while ($arFields = $res->Fetch()){
				if (is_array($arFields["PROPERTY_ITEMS_VALUE"]) && count($arFields["PROPERTY_ITEMS_VALUE"])>0){
					$arResult["ITEMS"][] = $arFields;
				}
			}
		}

		if (count($arResult["ITEMS"])<=0 && count($arResult["BESTSELLERS_ITEMS"])<=0) $this->AbortResultCache();

		if ($BID == 0) $this->AbortResultCache();
	}
}

$this->IncludeComponentTemplate($templatePage);

?>