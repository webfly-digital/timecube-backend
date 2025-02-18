<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * @var array $arResult
 */
?>
	<i class="fa fa-heart-o"></i>
	<br /><?= !empty($arResult["FAVOR_ITEMS"]) && is_array($arResult["FAVOR_ITEMS"]) ? count($arResult["FAVOR_ITEMS"]) : 0 ?>