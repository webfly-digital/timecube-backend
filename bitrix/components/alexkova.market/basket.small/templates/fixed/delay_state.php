<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 */

?>
    <i class="fa fa-heart-o"></i>
    <br/><?= isset($arResult["BASKET_ITEMS"]["DELAY"]) && is_array($arResult["BASKET_ITEMS"]["DELAY"])
    ? count($arResult["BASKET_ITEMS"]["DELAY"])
    : 0 ?>