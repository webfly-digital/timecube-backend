<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
/**
 * @global CMain                 $APPLICATION
 * @global CUser                 $USER
 * @global CDatabase             $DB
 *
 * @var array                    $arParams
 * @var array                    $arResult
 *
 * @var CatalogSectionComponent  $component
 * @var CBitrixComponentTemplate $this
 *
 * @var string                   $templateName
 * @var string                   $componentPath
 * @var string                   $templateFolder
 *
 * @const SITE_TEMPLATE_PATH
 */

global $eMarketBasketData;
$_SESSION["BXR_BASKET_TEMPLATE"] = "fixed";
?>
<?php if (isset($_REQUEST['ajaxbuy']) && $_REQUEST['ajaxbuy'] == "yes"){$APPLICATION->RestartBuffer();}?>

<?php if (isset($_REQUEST['ajaxbuy']) && $_REQUEST['ajaxbuy'] == "yes" && $_REQUEST["action"] == 'add'):?>
    <?php include('popup.php');?>
<?php endif;?>

<?php if (!isset($_REQUEST['ajaxbuy']) || $_REQUEST['ajaxbuy'] != "yes"):?>
        <div id="bxr-basket-row" class="basket-body-table-row bxr-basket-row-fixed text-center">
                <div class="">
                    <?php // Basket can by Info?>
                        <a href="javascript:void(0);" class="bxr-basket-indicator bxr-indicator-basket bxr-font-hover-light" data-group="basket-group" data-child="bxr-basket-body"
                            title="<?=GetMessage("BASKET_TITLE")?>">
                            <?php include('basket_delay_state.php');?>
                        </a>
                    <?php // End Basket can by Info?>
                    <?php else:?>
                        <span id="bxr-basket-data" style="display: none;"><?=json_encode($eMarketBasketData)?></span>
                    <?php endif;?>

                    <?php
$idDelay = "bxr-basket-body";
if (isset($_REQUEST['ajaxbuy']) && $_REQUEST['ajaxbuy'] == "yes")
        $idDelay = 'basket-body-content';
?>
<div id="<?=$idDelay?>" class="basket-body-container" data-group="basket-group" data-state="hide">
    <?php include('items_basket.php');?>
</div>
<div id="bxr-basket-body-mobile">
    <?php include('items_basket_mobile.php');?>
</div>


                    <?php if (!isset($_REQUEST['ajaxbuy']) || $_REQUEST['ajaxbuy'] != "yes"):?>
                </div>
                <div>
                    <?php // Basket delay Info?>
                        <a href="javascript:void(0);" data-group="basket-group" class="bxr-basket-indicator bxr-indicator-favor bxr-font-hover-light"  data-child="bxr-favor-body"
                            title="<?=GetMessage("FAVOR_TITLE")?>">
                            <?php include('favor_state.php');?>
                        </a>
                    <?php // End Basket delay Info?>
                    <?php endif;?>




                    <?php
$idDelay = "bxr-favor-body";
if (isset($_REQUEST['ajaxbuy']) && $_REQUEST['ajaxbuy'] == "yes")
        $idDelay = 'favor-body-content';
?>
<div id="<?=$idDelay?>" class="basket-body-container" data-group="basket-group" data-state="hide">
    <?php include('items_favor.php');?>
</div>
<div id="bxr-favor-body-mobile">
    <?php include('items_favor_mobile.php');?>
</div>

                    <?php if (!isset($_REQUEST['ajaxbuy']) || $_REQUEST['ajaxbuy'] != "yes"):?>
</div>
            <?php if ($arParams["USE_COMPARE"] == "Y"):?>

			<div>
                <?php if (substr_count($APPLICATION->GetCurPage(),SITE_DIR.'/catalog/compare.php') <= 0)
					$APPLICATION->IncludeComponent(
						"alexkova.market:catalog.compare.list",
						".default",
						Array(
							"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
							"IBLOCK_ID" => $arParams["IBLOCK_ID"],
							"AJAX_MODE" => "N",
							"AJAX_OPTION_JUMP" => "N",
							"AJAX_OPTION_STYLE" => "Y",
							"AJAX_OPTION_HISTORY" => "N",
							"DETAIL_URL" => "",
							"COMPARE_URL" => SITE_DIR."catalog/compare.php",
							"NAME" => "CATALOG_COMPARE_LIST"
						),
						false,
						array('HIDE_ICONS'=>"Y")
					);?>
			</div>

            <?php endif; ?>

		</div>
	<div style="display: none;" id="bxr-basket-content">
	</div>
<?php include('mobile_state.php')?>
<?php
else:?>
	<div id="bxr-indicator-basket-new"><?php include('basket_delay_state.php');?></div>
	<div id="bxr-indicator-delay-new"><?php include('delay_state.php');?></div>
        <div id="bxr-indicator-favor-new"><?php include('favor_state.php');?></div>
    <?php die();
endif;
?>

<script>
    var delayClick = false;
	$(document).ready(function(){

		BX.message({
			setItemDelay2BasketTitle: '<?=GetMessage('BASKET_DELAY_OK_TITLE')?>',
			setItemAdded2BasketTitle: '<?=GetMessage('BASKET_ADD_OK_TITLE')?>'
		});

		BXR = window.BXReady.Market.Basket;
		BXR.ajaxUrl = '<?=SITE_DIR?>ajax/basket_action.php';
		BXR.template = 'fixed';
		BXR.init();

	});

</script>