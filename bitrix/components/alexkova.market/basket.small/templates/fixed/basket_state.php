<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 */

?>
<i class="fa fa-shopping-cart"></i><br/>
<?php if (!empty($arResult["BASKET_ITEMS"]["CAN_BUY"]) && is_array($arResult["BASKET_ITEMS"]["CAN_BUY"])) { ?>
    <?= count($arResult["BASKET_ITEMS"]["CAN_BUY"]) ?>
<?php } ?>
<!--<br /><span class="bxr-format-price"><?= $arResult["FORMAT_SUMM"] ?></span>-->