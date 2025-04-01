<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

$this->setFrameMode(true);

use Alexkova\Bxready\Draw;

if (count($arResult['ITEMS'])>0){
?>
<h2><?=$arParams["BLOCK_TITLE"]?></h2>
<div class="row bxr-list">
    <div class="clearfix"></div>
    <?
    $module_id = "alexkova.market";
    $managment_element_mode = COption::GetOptionString($module_id, "managment_element_mode", "N");
    
    if ($managment_element_mode == "Y") {
        $ownOptElementLib = COption::GetOptionString($module_id, "own_catalog_list_element_type_".SITE_TEMPLATE_ID, "ecommerce.v2.lite");
        if (strlen($ownOptElementLib) > 0) {
            $elementLibrary = trim($ownOptElementLib); 
        } else {
            $optElementLib = COption::GetOptionString($module_id, "catalog_list_element_type_".SITE_TEMPLATE_ID, "ecommerce.v2.lite");
            if (strlen($optElementLib) > 0) {
                $elementLibrary = $optElementLib;
            } else {
                $elementLibrary =  "ecommerce.v2.lite";
            }
        }
        $arResponsiveParams["LG"] = COption::GetOptionString($module_id, "catalog_list_element_count_lg_".SITE_TEMPLATE_ID, 4);
        $arResponsiveParams["MD"] = COption::GetOptionString($module_id, "catalog_list_element_count_md_".SITE_TEMPLATE_ID, 3);
        $arResponsiveParams["SM"] = COption::GetOptionString($module_id, "catalog_list_element_count_sm_".SITE_TEMPLATE_ID, 2);
        $arResponsiveParams["XS"] = COption::GetOptionString($module_id, "catalog_list_element_count_xs_".SITE_TEMPLATE_ID, 1);
    } else {
        $elementLibrary =  "ecommerce.v2.lite";
        $arResponsiveParams = array(
		"LG" => 3,
		"MD" => 3,
		"SM" => 4,
		"XS" => 6
	);
    }?>
    <?
    $elementDraw = \Alexkova\Bxready\Draw::getInstance($this);
    $elementDraw->setCurrentTemplate($this);
    global $unicumID;
    if ($unicumID<=0) {$unicumID = 1;} else {$unicumID++;}   

    foreach ($arResult["ITEMS"] as $key => $arItem) {        
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        $strMainID = $this->GetEditAreaId($arItem['ID']);?>
    
        <div id="<?=$strMainID?>" class="t_<?=$unicumID?> col-lg-<?=$arResponsiveParams["LG"]?> col-md-<?=$arResponsiveParams["MD"]?> col-sm-<?=$arResponsiveParams["SM"]?> col-xs-<?=$arResponsiveParams["XS"]?>">
            <?
            $elementDraw->showElement($elementLibrary, $arItem, $arParams);
            ?>
        </div>
    <?}?>
</div>
<?}?>
