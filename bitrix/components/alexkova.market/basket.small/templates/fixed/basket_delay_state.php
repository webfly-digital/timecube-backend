<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?php /**
 * @var array $arResult
 */ ?>
<i class="fa fa-shopping-cart"></i><br/>
<?php
$canBuyItems = isset($arResult["BASKET_ITEMS"]["CAN_BUY"]) && is_array($arResult["BASKET_ITEMS"]["CAN_BUY"])
    ? count($arResult["BASKET_ITEMS"]["CAN_BUY"])
    : 0;

$delayItems = isset($arResult["BASKET_ITEMS"]["DELAY"]) && is_array($arResult["BASKET_ITEMS"]["DELAY"])
    ? count($arResult["BASKET_ITEMS"]["DELAY"])
    : 0;

$basket_delay_cnt = $canBuyItems + $delayItems;
?>
<?= $basket_delay_cnt ?>
<!--<br /><span class="bxr-format-price"><?= $arResult["FORMAT_SUMM"] ?></span>-->