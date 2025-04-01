<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div id="bxr-mobile-content" style="display:none">
	<div class="bxr-counter-mobile bxr-counter-mobile-basket bxr-bg-hover" data-child="bxr-basket-mobile-container" title="<?=GetMessage("BASKET_TITLE")?>">
		<i class="fa fa-shopping-cart"></i>
		<span class="bxr-counter-basket">
                    <?$basket_delay_cnt = count($arResult["BASKET_ITEMS"]["CAN_BUY"]) + count($arResult["BASKET_ITEMS"]["DELAY"]);?>
                    <?=$basket_delay_cnt?>
		</span>
	</div>
	<div class="bxr-counter-mobile bxr-counter-mobile-favor bxr-bg-hover" data-child="bxr-favor-mobile-container" title="<?=GetMessage("FAVOR_TITLE")?>">
		<i class="fa fa-heart-o"></i>
		<span class="bxr-counter-favor">
                    <?=count($arResult["FAVOR_ITEMS"])?>
		</span>
	</div>
	<div id="bxr-basket-mobile-container" class="col-sm-12 col-xs-12 hidden-md hidden-lg">
	</div>
	<div id="bxr-favor-mobile-container" class="col-sm-12 col-xs-12 hidden-md  hidden-lg">
	</div>
</div>
