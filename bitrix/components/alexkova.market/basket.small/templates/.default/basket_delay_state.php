<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

	<i class="fa fa-shopping-cart"></i>
        <?$basket_delay_cnt = count($arResult["BASKET_ITEMS"]["CAN_BUY"]) + count($arResult["BASKET_ITEMS"]["DELAY"]);?>
	<?=$basket_delay_cnt?>
<!--<br /><span class="bxr-format-price"><?=$arResult["FORMAT_SUMM"]?></span>-->
