<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$pictureID = (is_array($arResult["PRODUCT_INFO"]["DETAIL_PARENT"]) && $arResult["PRODUCT_INFO"]["DETAIL_PARENT"]["DETAIL_PICTURE"] != "")
	? $arResult["PRODUCT_INFO"]["DETAIL_PARENT"]["DETAIL_PICTURE"]
	: $arResult["PRODUCT_INFO"]["DETAIL_PICTURE"];
$pictureID = (!empty($arResult["CATALOG"][$arResult["PRODUCT_INFO"]["ID"]]["~DETAIL_PICTURE"]))
    ? $arResult["CATALOG"][$arResult["PRODUCT_INFO"]["ID"]]["~DETAIL_PICTURE"]
    : $pictureID;

if (empty($pictureID)) {
    $pictureID = $arResult["PRODUCT_INFO"]["PREVIEW_PICTURE"];
}

if ($pictureID>0){
    $metrics = array('width' => 220, 'height' => 220);
    $resizedImage = \CFile::ResizeImageGet($pictureID, $metrics, BX_RESIZE_IMAGE_PROPORTIONAL, true);
}
if (strlen($resizedImage["src"])>0)
    $URL = $resizedImage["src"];
else
    $URL = CFile::GetPath($pictureID);

$arResult["MIN_ORDER_PRICE"] = COption::GetOptionString("alexkova.market", "bxr_min_order_price");
$arResult["MIN_ORDER_PRICE_MSG"] = GetMessage('MIN_ORDER_MSG');
$arResult["ADD_ORDER_PRICE"] = $arResult["MIN_ORDER_PRICE"] - $arResult["SUMM"];
$arResult["ADD_ORDER_PRICE_MSG"] = GetMessage('ADD_ORDER_MSG');
$arResult["CURRENCY"] = CCurrency::GetBaseCurrency();
$arResult["CURRENCY_FORMAT"] = CCurrencyLang::GetFormatDescription($arResult["CURRENCY"]);
$arResult["CURRENCY_FORMAT"] = rtrim($arResult["CURRENCY_FORMAT"]["FORMAT_STRING"], '.');
$arResult["ADD_ORDER_PRICE_FORMATED"] = str_replace('#', $arResult["ADD_ORDER_PRICE"], $arResult["CURRENCY_FORMAT"]);
$arResult["MIN_ORDER_PRICE_FORMATED"] = str_replace('#', $arResult["MIN_ORDER_PRICE"], $arResult["CURRENCY_FORMAT"]);
$arResult["MIN_ORDER_PRICE_MSG"] = str_replace("#MIN_ORDER_PRICE#", $arResult["MIN_ORDER_PRICE_FORMATED"], $arResult["MIN_ORDER_PRICE_MSG"]);
$arResult["ADD_ORDER_PRICE_MSG"] = str_replace("#ADD_ORDER_PRICE#", $arResult["ADD_ORDER_PRICE_FORMATED"], $arResult["ADD_ORDER_PRICE_MSG"]);
?>

<? $addData = 'data-nshow="0"';?>
<?if (isset($_REQUEST['delay']) && ($_REQUEST['delay'] == "yes" || $_REQUEST['delay'] == "no") && $_REQUEST["action"] == 'add'):?>
	<? $addData = 'data-nshow="1"';?>
<?endif;?>

<div id="bxr-basket-popup" <?=$addData?>>
	<div id="basket-popup-product-image">
		<img src="<?=$URL?>" alt="<?=$arResult["PRODUCT_INFO"]["NAME"]?>"/>
	</div>
	<div id="basket-popup-product-name" class="basket-popup-name">
		<?=$arResult["PRODUCT_INFO"]["NAME"]?>
	</div>
	<?if ($arResult["ADD_ORDER_PRICE"] > 0) {?>
	<div class="min-order-price-notify" style="text-align:center;">
		<?=$arResult["MIN_ORDER_PRICE_MSG"];?>
		<br />
		<?=$arResult["ADD_ORDER_PRICE_MSG"];?>
	</div>
	<?}?>
	<div id="basket-popup-buttons">
		<button class="bxr-color-button bxr-corns" onclick="BXReady.basketPopup.close()">
			<span class="fa fa-undo" aria-hidden="true"></span>
			<?=GetMessage('BASKET_ADD_CONTINUE')?></button>
			<?if ($arResult["ADD_ORDER_PRICE"] < 0) {?>
		<a class="bxr-color-button  bxr-corns" href="<?=$arParams["PATH_TO_BASKET"]?>">
			<span class="fa fa-check-square-o" aria-hidden="true"></span>
			<?=GetMessage('BASKET_TO_ORDER')?></a>
			<?}?>
	</div>
</div>
