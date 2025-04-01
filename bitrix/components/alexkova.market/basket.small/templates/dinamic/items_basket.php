<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="basket-body-title">
    <span class="basket-body-title-h bxr-basket-tab tab-basket active" data-tab="buy"><?=GetMessage('BASKET_TITLE')?><span class="bxr-basket-cnt"> (<?=count($arResult["BASKET_ITEMS"]["CAN_BUY"])?>)</span></span>
    <span class="basket-body-title-h bxr-basket-tab tab-delay" data-tab="delay"><?=GetMessage('DELAY_TITLE')?><span class="bxr-basket-cnt"> (<?=count($arResult["BASKET_ITEMS"]["DELAY"])?>)</span></span>
    <div class="pull-right">
        <button class="btn btn-default bxr-close-basket bxr-corns">
            <span class="fa fa-power-off" aria-hidden="true"></span>
            <?=GetMessage('BASKET_CLOSE')?>
        </button>
    </div>
</div>
<div class="clearfix"></div>

<input type="hidden" id="currency-format" value="<?=$arResult["CURRENCY_FORMAT"]?>">
<input type="hidden" id="min-order-price" value="<?=$arResult["MIN_ORDER_PRICE"]?>">
<input type="hidden" id="min-order-price-msg" value="<?=$arResult["MIN_ORDER_PRICE_MSG_FLAGS"]?>">
<div class="min-order-price-notify" <?if ($arResult["SUMM"] >= $arResult["MIN_ORDER_PRICE"]) {?>style="display: none;"<?}?>><?=$arResult["MIN_ORDER_PRICE_MSG"]?></div>

<div class="bxr-basket-tab-content active" data-tab="buy">
    <?include('items_can_by.php');?>
</div>
<div class="bxr-basket-tab-content" data-tab="delay">
    <?include('items_delay.php');?>
</div>
<div class="icon-close"></div>