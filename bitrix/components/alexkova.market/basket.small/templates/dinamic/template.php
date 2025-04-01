<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
global $eMarketBasketData;
$_SESSION["BXR_BASKET_TEMPLATE"] = "dinamic";
?>
<?if (isset($_REQUEST['ajaxbuy']) && $_REQUEST['ajaxbuy'] == "yes"){$APPLICATION->RestartBuffer();}?>

<?if (isset($_REQUEST['ajaxbuy']) && $_REQUEST['ajaxbuy'] == "yes" && $_REQUEST["action"] == 'add'):?>
<?include('popup.php');?>
<?endif;?>




	<?if (!isset($_REQUEST['ajaxbuy']) || $_REQUEST['ajaxbuy'] != "yes"):?>
		<div id="bxr-basket-container">
		<div id="bxr-basket-row" class="basket-body-table-row bxr-basket-row-dinamic text-center">

				<?// Basket can by Info?>
				<a href="javascript:void(0);" class="bxr-basket-indicator bxr-indicator-basket bxr-font-hover-light bxr-basket-label" data-group="basket-group" data-child="bxr-basket-body" 
                                   title="<?=GetMessage("BASKET_TITLE")?>">
					<?include('basket_delay_state.php');?>
				</a>
				<?// End Basket can by Info?>
	<?else:?>
				<span id="bxr-basket-data" style="display: none;"><?=json_encode($eMarketBasketData)?></span>
	<?endif;?>



	<?
	$idDelay = "bxr-basket-body";
	if (isset($_REQUEST['ajaxbuy']) && $_REQUEST['ajaxbuy'] == "yes")
		$idDelay = 'basket-body-content';
	?>
	<div id="<?=$idDelay?>" class="basket-body-container" data-group="basket-group" data-state="hide">
		<?include('items_basket.php');?>
	</div>
	<div id="bxr-basket-body-mobile">
        <?include('items_basket_mobile.php');?>
	</div>


	<?if (!isset($_REQUEST['ajaxbuy']) || $_REQUEST['ajaxbuy'] != "yes"):?>


				<?// Basket delay Info?>
                        <a href="javascript:void(0);" data-group="basket-group" class="bxr-basket-indicator bxr-indicator-favor bxr-font-hover-light  bxr-favor-label"  data-child="bxr-favor-body"
                            title="<?=GetMessage("FAVOR_TITLE")?>">
					<?include('favor_state.php');?>
				</a>
				<?// End Basket delay Info?>
	<?endif;?>




	<?
$idDelay = "bxr-favor-body";
	if (isset($_REQUEST['ajaxbuy']) && $_REQUEST['ajaxbuy'] == "yes")
        $idDelay = 'favor-body-content';
	?>
	<div id="<?=$idDelay?>" class="basket-body-container" data-group="basket-group" data-state="hide">
<?include('items_favor.php');?>
	</div>
<div id="bxr-favor-body-mobile">
        <?include('items_favor_mobile.php');?>
	</div>

<?if (!isset($_REQUEST['ajaxbuy']) || $_REQUEST['ajaxbuy'] != "yes"):?>

			<?if ($arParams["USE_COMPARE"] == "Y"):?>


				<?if (substr_count($APPLICATION->GetCurPage(),SITE_DIR.'/catalog/compare.php') <= 0)
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


			<?endif;?>

		</div>
	<div style="display: none;" id="bxr-basket-content">
	</div></div>
	<?include('mobile_state.php')?>
<?
else:?>
	<div id="bxr-indicator-basket-new"><?include('basket_delay_state.php');?></div>
	<div id="bxr-indicator-delay-new"><?include('delay_state.php');?></div>
        <div id="bxr-indicator-favor-new"><?include('favor_state.php');?></div>
	<?die();
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