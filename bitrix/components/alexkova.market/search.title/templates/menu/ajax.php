<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (empty($arResult["CATEGORIES"]))
	return;
?>
<div class="bx_searche bxr-title-search-result bx_searche_menu">
<?foreach($arResult["CATEGORIES"] as $category_id => $arCategory):?>
        <?if(!empty($arCategory["TITLE_PARAMS"])):?>
            <div class="bx_category_title"><?=$arCategory["TITLE"];?></div>
        <?endif;?>
	<?foreach($arCategory["ITEMS"] as $i => $arItem):?>
		<?if($category_id === "all"):?>
			<div class="bx_item_block" style="min-height:0">
				<div class="bx_img_element"></div>
				<div class="bx_item_element bx_item_element_menu "><hr></div>
			</div>
			<div class="bx_item_block bx_item_block_el  all_result">
				<div class="bx_img_element"></div>
				<div class="bx_item_element bx_item_element_menu">
					<span class="all_result_title"><a href="<?echo $arItem["URL"]?>"><?echo $arItem["NAME"]?></a></span>
				</div>
				<div style="clear:both;"></div>
			</div>
		<?elseif(isset($arResult["ELEMENTS"][$arItem["ITEM_ID"]])):
			$arElement = $arResult["ELEMENTS"][$arItem["ITEM_ID"]];?>
			<div class="bx_item_block bx_item_block_el">
				<?if (is_array($arElement["PICTURE"])):?>
				<div class="bx_img_element">
                                    <img align="left" src="<?echo $arElement["PICTURE"]["src"]?>" width="<?echo $arElement["PICTURE"]["width"]?>" height="<?echo $arElement["PICTURE"]["height"]?>">
                                </div>
				<?endif;?>
				<div class="bx_item_element bx_item_element_menu">
					<a href="<?echo $arItem["URL"]?>"><?echo $arItem["NAME"]?></a>
					<?
					foreach($arElement["PRICES"] as $code=>$arPrice)
					{
						if ($arPrice["MIN_PRICE"] != "Y")
							continue;

						if($arPrice["CAN_ACCESS"])
						{
							if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
								<div class="bx_price">
									<?=$arPrice["PRINT_DISCOUNT_VALUE"]?>
									<span class="old"><?=$arPrice["PRINT_VALUE"]?></span>
								</div>
							<?else:?>
								<div class="bx_price"><?=$arPrice["PRINT_VALUE"]?></div>
							<?endif;
						}
						if ($arPrice["MIN_PRICE"] == "Y")
							break;
					}
					?>
				</div>
				<div style="clear:both;"></div>
			</div>
		<?else:?>
			<div class="bx_item_block bx_item_block_el others_result">
				<div class="bx_img_element"></div>
				<div class="bx_item_element bx_item_element_menu">
					<a href="<?echo $arItem["URL"]?>"><?echo $arItem["NAME"]?></a>
				</div>
				<div style="clear:both;"></div>
			</div>
		<?endif;?>
	<?endforeach;?>
<?endforeach;?>
</div>