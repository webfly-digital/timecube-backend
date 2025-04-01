<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 */

?>
<div id="bxr-mobile-content" style="display:none">
    <div class="bxr-counter-mobile bxr-counter-mobile-basket bxr-bg-hover" data-child="bxr-basket-mobile-container" title="<?= GetMessage("BASKET_TITLE") ?>">
        <i class="fa fa-shopping-cart"></i>
        <span class="bxr-counter-basket">
                    <?php
                    $basket_delay_cnt =
                        (!empty($arResult["BASKET_ITEMS"]["CAN_BUY"]) && is_array($arResult["BASKET_ITEMS"]["CAN_BUY"]) ? count($arResult["BASKET_ITEMS"]["CAN_BUY"]) : 0) +
                        (!empty($arResult["BASKET_ITEMS"]["DELAY"]) && is_array($arResult["BASKET_ITEMS"]["DELAY"]) ? count($arResult["BASKET_ITEMS"]["DELAY"]) : 0);
                    ?>

                    <?= $basket_delay_cnt ?>
		</span>
    </div>
    <div class="bxr-counter-mobile bxr-counter-mobile-favor bxr-bg-hover" data-child="bxr-favor-mobile-container" title="<?= GetMessage("FAVOR_TITLE") ?>">
        <i class="fa fa-heart-o"></i>
        <span class="bxr-counter-favor">
                    <?= !empty($arResult["FAVOR_ITEMS"]) && is_array($arResult["FAVOR_ITEMS"]) ? count($arResult["FAVOR_ITEMS"]) : 0 ?>
		</span>
    </div>
    <div id="bxr-basket-mobile-container" class="col-sm-12 col-xs-12 hidden-md hidden-lg">
    </div>
    <div id="bxr-favor-mobile-container" class="col-sm-12 col-xs-12 hidden-md  hidden-lg">
    </div>
</div>