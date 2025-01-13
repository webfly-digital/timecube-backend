<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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

if (!isset($arParams['FILTER_VIEW_MODE']) || (string)$arParams['FILTER_VIEW_MODE'] == '')
    $arParams['FILTER_VIEW_MODE'] = 'VERTICAL';
$arParams['USE_FILTER'] = (isset($arParams['USE_FILTER']) && $arParams['USE_FILTER'] == 'Y' ? 'Y' : 'N');

$isVerticalFilter = ('Y' == $arParams['USE_FILTER'] && $arParams["FILTER_VIEW_MODE"] == "VERTICAL");
$isSidebar = ($arParams["SIDEBAR_SECTION_SHOW"] == "Y" && isset($arParams["SIDEBAR_PATH"]) && !empty($arParams["SIDEBAR_PATH"]));
$isSidebarLeft = isset($arParams['SIDEBAR_SECTION_POSITION']) && $arParams['SIDEBAR_SECTION_POSITION'] === 'left';
$isFilter = ($arParams['USE_FILTER'] == 'Y');

if ($isFilter) {
    $arFilter = [
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "ACTIVE" => "Y",
        "GLOBAL_ACTIVE" => "Y",
    ];
    if (0 < intval($arResult["VARIABLES"]["SECTION_ID"]))
        $arFilter["ID"] = $arResult["VARIABLES"]["SECTION_ID"];
    elseif ('' != $arResult["VARIABLES"]["SECTION_CODE"])
        $arFilter["=CODE"] = $arResult["VARIABLES"]["SECTION_CODE"];

    $obCache = new CPHPCache();
    if ($obCache->InitCache(36000, serialize($arFilter), "/iblock/catalog")) {
        $arCurSection = $obCache->GetVars();
    } elseif ($obCache->StartDataCache()) {
        $arCurSection = [];
        if (Loader::includeModule("iblock")) {
            $dbRes = CIBlockSection::GetList([], $arFilter, false, ['*', 'UF_*']);

            if (defined("BX_COMP_MANAGED_CACHE")) {
                global $CACHE_MANAGER;
                $CACHE_MANAGER->StartTagCache("/iblock/catalog");

                if ($arCurSection = $dbRes->Fetch())
                    $CACHE_MANAGER->RegisterTag("iblock_id_" . $arParams["IBLOCK_ID"]);

                $CACHE_MANAGER->EndTagCache();
            } else {
                if (!$arCurSection = $dbRes->Fetch())
                    $arCurSection = [];
            }


            // count elements
            $arCurSection['COUNT'] = CIBlockSection::GetSectionElementsCount($arCurSection['ID'], ["CNT_ACTIVE" => "Y"]);

            $groupCode = 'CATALOG_GROUP_' . WF_PRICE_ID;
            $priceCode = 'catalog_PRICE_' . WF_PRICE_ID;
            $priceCodeUC = 'CATALOG_PRICE_' . WF_PRICE_ID;
            $priceCodeNew = 'PRICE_' . WF_PRICE_ID;
            $priceFilter = [
                "IBLOCK_ID" => $arParams['IBLOCK_ID'],
                //"IBLOCK_SECTION_ID"=>$currentSection['ID'],
                "SECTION_ID" => $arCurSection['ID'],
                'INCLUDE_SUBSECTIONS' => 'Y',
                'ACTIVE' => 'Y'
            ];
            // get max price
            $resMax = CIBlockElement::GetList(
                [$priceCode => "desc"], $priceFilter,
                false, ['nTopCount' => 1], ["IBLOCK_ID", "ID", "NAME", $groupCode]
            );

            $obMax = $resMax->Fetch();
            $arCurSection['PRICE_MAX'] = $obMax[$priceCodeUC];

            // get min price
            $resMin = CIBlockElement::GetList(
                [$priceCode => "ASC"], $priceFilter,
                false, ['nTopCount' => 1], ["IBLOCK_ID", "ID", "NAME", $groupCode]
            );
            $obMin = $resMin->Fetch();
            $arCurSection['PRICE_MIN'] = $obMin[$priceCodeUC];

        }
        $obCache->EndDataCache($arCurSection);
    }
    if (!isset($arCurSection))
        $arCurSection = [];
}


include($_SERVER["DOCUMENT_ROOT"] . "/" . $this->GetFolder() . "/section_vertical.php");