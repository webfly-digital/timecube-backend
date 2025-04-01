<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!$arParams["PAGE_ELEMENT_COUNT_SHOW"]) {
	$arParams["PAGE_ELEMENT_COUNT_SHOW"] = "N";
}
if (!isset($arParams["THEME"])) {
	$arParams["THEME"] = "default";
}
if (!$arParams["CATALOG_VIEW_SHOW"]) {
	$arParams["CATALOG_VIEW_SHOW"] = "Y";
}
if (!$arParams["DEFAULT_CATALOG_VIEW"]) {
	$arParams["DEFAULT_CATALOG_VIEW"] = "TITLE";
}
if (!$arParams["PAGE_ELEMENT_COUNT"]) {
	$arParams["PAGE_ELEMENT_COUNT"] = 16;
}
if (!$arParams["PAGE_ELEMENT_COUNT_LIST"]) {
	$arParams["PAGE_ELEMENT_COUNT_LIST"] = array(16);
}
if (!$arParams["CATALOG_DEFAULT_SORT"]) {
	$arParams["CATALOG_DEFAULT_SORT"] = "NAME";
}
else 
    $arParams["CATALOG_DEFAULT_SORT"] = mb_strtoupper($arParams["CATALOG_DEFAULT_SORT"]);

if (empty($arResult["SORT_PROPS"])) {
    $arResult["SORT_PROPS"] = array(
            "NAME" => array("NAME", "asc", GetMessage("KZNC_SORT_NAME_NAME")),
            "PROPERTY_RATING" => array("PROPERTY_RATING", "asc", GetMessage("KZNC_SORT_RATING_NAME")),
            "PROPERTY_MINIMUM_PRICE" => array("PROPERTY_MINIMUM_PRICE", "asc", GetMessage("KZNC_SORT_PRICE_NAME")),
    );
}

asort($arParams["PAGE_ELEMENT_COUNT_LIST"]);

if (!function_exists('number_key')) {
	function number_key($array, $desired_key) {
		if (!isset($array[$desired_key])) {
			return false;
		}
		$i = 1;
		foreach ($array as $key => $value) {
			if ($key == $desired_key) {
				return $i;
			}else{
				$i++;
			}
		}
	}
}

function NumberWordEndingsEx($num, $arEnds = false) {
   $lang = LANGUAGE_ID;
   if ($arEnds===false) {
      if(strlen(GetMessage("KZNC_ITEM_NAME_ENDING")) > 0):
	$arEnds = explode(",", GetMessage("KZNC_ITEM_NAME_ENDING"));
      endif;
   }
   if ($lang=='ru') {
      if (strlen($num)>1 && substr($num, strlen($num)-2, 1)=='1') {
         return $arEnds[0];
      } else {
         $c = IntVal(substr($num, strlen($num)-1, 1));
         if ($c==0 || ($c>=5 && $c<=9)) {
            return $arEnds[1];
         } elseif ($c==1) {
            return $arEnds[2];
         } else {
            return $arEnds[3];
         }
      }
   } elseif ($lang=='en') {
      if (IntVal($num)>1) {
         return 's';
      }
      return '';
   } else {
      return '';
   }
}

global $arSortGlobal;

$sort = array_key_exists("sort", $_REQUEST) && array_key_exists(ToUpper($_REQUEST["sort"]), $arResult["SORT_PROPS"]) ? $arResult["SORT_PROPS"][ToUpper($_REQUEST["sort"])][0] : $arParams["CATALOG_DEFAULT_SORT"];
$sort_order = array_key_exists("order", $_REQUEST) && in_array(ToLower($_REQUEST["order"]), Array("asc", "desc")) ? ToLower($_REQUEST["order"]) : $arParams["CATALOG_DEFAULT_SORT_ORDER"];
$num = array_key_exists("num", $_REQUEST) ? $_REQUEST["num"] : $arParams["PAGE_ELEMENT_COUNT"];
$view = array_key_exists("view", $_REQUEST) ? $_REQUEST["view"] : strtolower($arParams["DEFAULT_CATALOG_VIEW"]);

$arSortGlobal = array(
	"sort" => $sort,
	"sort_order" => $sort_order,
	"num" => $num,
	"view" => $view,
);