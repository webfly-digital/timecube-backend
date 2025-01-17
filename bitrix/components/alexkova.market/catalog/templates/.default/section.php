<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
$this->setFrameMode(true);
$this->addExternalCss("/bitrix/css/main/bootstrap.css");

if (!isset($arParams['FILTER_VIEW_MODE']) || (string)$arParams['FILTER_VIEW_MODE'] == '')
	$arParams['FILTER_VIEW_MODE'] = 'VERTICAL';
$arParams['USE_FILTER'] = (isset($arParams['USE_FILTER']) && $arParams['USE_FILTER'] == 'Y' ? 'Y' : 'N');
$arParams['SHOW_LEFT_MENU'] = (isset($arParams['SHOW_LEFT_MENU']) && $arParams['SHOW_LEFT_MENU'] == 'Y' ? 'Y' : 'N');
if (!isset($arParams["SIDEBAR_SECTION_SHOW"])) $arParams["SIDEBAR_SECTION_SHOW"] = 'Y';

$isMenu = ($arParams['SHOW_LEFT_MENU'] == 'Y');
$isVerticalFilter = ('Y' == $arParams['USE_FILTER'] && $arParams["FILTER_VIEW_MODE"] == "VERTICAL");
$isSidebar = $arParams["SIDEBAR_SECTION_SHOW"] == "Y";
$isAdditionalSideBar = (isset($arParams["SIDEBAR_PATH"]) && !empty($arParams["SIDEBAR_PATH"]));
$isFilter = ($arParams['USE_FILTER'] == 'Y');

$arFilter = array(
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "ACTIVE" => "Y",
        "GLOBAL_ACTIVE" => "Y",
);
if (0 < intval($arResult["VARIABLES"]["SECTION_ID"]))
        $arFilter["ID"] = $arResult["VARIABLES"]["SECTION_ID"];
elseif ('' != $arResult["VARIABLES"]["SECTION_CODE"])
        $arFilter["=CODE"] = $arResult["VARIABLES"]["SECTION_CODE"];

$obCache = new CPHPCache();
if ($obCache->InitCache(36000, serialize($arFilter), "/iblock/catalog"))
{
        $arCurSection = $obCache->GetVars();
}
elseif ($obCache->StartDataCache())
{
        $arCurSection = array();
        if (Loader::includeModule("iblock"))
        {
            if($arParams["INCLUDE_SUBSECTIONS"]=="N")
                    $arFilter["ELEMENT_SUBSECTIONS"] = "N";
            $arFilter["CNT_ACTIVE"] = "Y";

            $dbRes = CIBlockSection::GetList(array(), $arFilter, true, array("ID", "NAME", "DESCRIPTION"));

            if(defined("BX_COMP_MANAGED_CACHE"))
            {
                global $CACHE_MANAGER;
                $CACHE_MANAGER->StartTagCache("/iblock/catalog");
                if ($arCurSection = $dbRes->Fetch())
                        $CACHE_MANAGER->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);
                $CACHE_MANAGER->EndTagCache();
            }
            else
            {
                if(!$arCurSection = $dbRes->Fetch())
                        $arCurSection = array();
            }
        }
        $obCache->EndDataCache($arCurSection);
}

if (!isset($arCurSection))
        $arCurSection = array();

if ($arCurSection["ELEMENT_CNT"]>0)
        $showElementsFilters = true;
else
        $showElementsFilters = false;

if ($isVerticalFilter)
	include($_SERVER["DOCUMENT_ROOT"]."/".$this->GetFolder()."/section_vertical.php");
else
	include($_SERVER["DOCUMENT_ROOT"]."/".$this->GetFolder()."/section_horizontal.php");

