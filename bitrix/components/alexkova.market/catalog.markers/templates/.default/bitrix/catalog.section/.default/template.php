<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Alexkova\Bxready\Draw;

if (!CModule::IncludeModule('alexkova.bxready')) return;

$this->setFrameMode(true);

$elementTemplate = ".default";

global $unicumID;
if ($unicumID<=0) {$unicumID = 1;} else {$unicumID++;}
if (isset($_REQUEST["bxr_ajax"]) && $_REQUEST["bxr_ajax"] == "yes"){
	$unicumID = "marc_".htmlspecialchars($_REQUEST["ID"]);
}

$arParams["UNICUM_ID"] = $unicumID;


$colToElem = array();
$bootstrapGridCount = $arParams["BXREADY_LIST_BOOTSTRAP_GRID_STYLE"];
if ($bootstrapGridCount>0){
	for($i=1; $i<=$bootstrapGridCount; $i++){
		if (($bootstrapGridCount % $i) == 0){
			$colToElem[$bootstrapGridCount / $i] = $i;
		}
	}
}

$addGridClass = '';

if ($arParams["BXREADY_LIST_BOOTSTRAP_GRID_STYLE"] == 10){
	$addGridClass = 'row10grid';
}



if (count($arResult["ITEMS"])>0):?>
	<div id="mc<?=htmlspecialchars($_REQUEST["ID"])?>" class="row bxr-list bxr-markers-list <?=$addGridClass?>" data-slider="<?=htmlspecialchars($_REQUEST["ID"])?>">
		<?if ($arParams["BXREADY_LIST_SLIDER"] == "Y") {?>
		<div id="sl_<?=htmlspecialchars($_REQUEST["ID"])?>" class="bxr-carousel markers-slick-animation">
			<?} else {
				if ($arParams["DISPLAY_TOP_PAGER"])
				{
					echo $arResult["NAV_STRING"];
				}
			}
			

			foreach ($arResult["ITEMS"] as $cell => $arItem):
				$arItem['EDIT_LINK'] = str_replace(array("ajax%2Fmarkers_tc.php", "ajax%2Fbestsellers_tc.php", "bxr_ajax%3Dyes"), "", $arItem['EDIT_LINK']);
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				$strMainID = $this->GetEditAreaId($arItem['ID']);

				$arElementDrawParams = array(
					"ELEMENT" => $arItem,
					"PARAMS" => $arParams
				);

                                // central manage mode
                                $module_id = "alexkova.market";
                                $managment_element_mode = COption::GetOptionString($module_id, "managment_element_mode", "N");
                                if ($managment_element_mode == "Y") {
                                    $ownOptElementLib = COption::GetOptionString($module_id, "own_list_element_type_".SITE_TEMPLATE_ID, $arParams["BXREADY_ELEMENT_DRAW"]);
                                    if (strlen($ownOptElementLib) > 0) {
                                        $arParams["BXREADY_ELEMENT_DRAW"] = trim($ownOptElementLib); 
                                    } else {
                                        $optElementLib = COption::GetOptionString($module_id, "list_element_type_".SITE_TEMPLATE_ID, $arParams["BXREADY_ELEMENT_DRAW"]);
                                        if (strlen($optElementLib) > 0) {
                                            $arParams["BXREADY_ELEMENT_DRAW"] = $optElementLib; 
                                        }
                                    }
                                }
                                ?>
				<div id="<?=$strMainID?>" class="t_<?=$unicumID?> col-lg-<?=$arParams["BXREADY_LIST_LG_CNT"]?> col-md-<?=$arParams["BXREADY_LIST_MD_CNT"]?> col-sm-<?=$arParams["BXREADY_LIST_SM_CNT"]?> col-xs-<?=$arParams["BXREADY_LIST_XS_CNT"]?>">
					<?
					Draw::getInstance($this)
						->showElement($arParams["BXREADY_ELEMENT_DRAW"], $arItem, $arParams);
					?>
				</div>
			<? endforeach; ?>
		</div>
		<?if ($arParams["BXREADY_LIST_SLIDER"] == "Y") {?>
	</div>
<?}
	else{
		if ($arParams["DISPLAY_BOTTOM_PAGER"])
		{
		 echo $arResult["NAV_STRING"]; 
		}
	}
	
endif;
