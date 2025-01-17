<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
foreach($arResult["SHOW_PROPERTIES"] as $cell=>$val){
	$arResult["SHOW_PROPERTIES"][$cell]["COUNT_VALUE"] = count($arResult["ITEMS"]);
}

foreach($arResult["ITEMS"] as $cell=>$val){
	foreach($val["PROPERTIES"] as $cell2=>$val2){
		if (!$val2["VALUE"])
			$arResult["SHOW_PROPERTIES"][$cell2]["COUNT_VALUE"] -= 1;
	}

}

foreach($arResult["SHOW_PROPERTIES"] as $cell=>$val){
	if ($arResult["SHOW_PROPERTIES"][$cell]["COUNT_VALUE"]<=0) {
		unset($arResult["SHOW_PROPERTIES"][$cell]);
		unset($arResult["DISPLAY_PROPERTIES"][$cell]);
	}
}
?>

<h1><?$APPLICATION->ShowTitle(false)?></h1>

<div class="row bxr-compare-list">
<div class="col-xs-12">



	<?if($arResult["DIFFERENT"]):
		?><a class="bxr-color-button" href="<?=htmlspecialcharsbx($APPLICATION->GetCurPageParam("DIFFERENT=N",array("DIFFERENT")))?>" rel="nofollow"><?=GetMessage("CATALOG_ALL_CHARACTERISTICS")?></a><?
	else:
		?><span class="bxr-no-button"><?=GetMessage("CATALOG_ALL_CHARACTERISTICS")?></span><?
	endif;

	if(!$arResult["DIFFERENT"]):
		?><a class="bxr-color-button" href="<?=htmlspecialcharsbx($APPLICATION->GetCurPageParam("DIFFERENT=Y",array("DIFFERENT")))?>" rel="nofollow"><?=GetMessage("CATALOG_ONLY_DIFFERENT")?></a><?
	else:
		?><span class="bxr-no-button"><?=GetMessage("CATALOG_ONLY_DIFFERENT")?></span>
	<?endif?>

	<table>

		<?$widthTD = 100/count($arResult["ITEMS"]);?>
		<tr>
			<td>&nbsp;</td><td>&nbsp;</td>
			<?foreach($arResult["ITEMS"] as $arElement):?>
				<td style="width: <?=$widthTD?>%" class="name"><?=$arElement["NAME"]?></td>
			<?endforeach?>
		</tr>
		<tr>
			<td>&nbsp;</td><td>&nbsp;</td>
			<?foreach($arResult["ITEMS"] as $arElement):?>
				<td>
					<?
					if(empty( $arElement["PREVIEW_PICTURE"]["SRC"] ) && empty( $arElement["DETAIL_PICTURE"]["SRC"] )){
						$img = '<a class="image-icon-200 test2" href="'.$arElement["DETAIL_PAGE_URL"].'"><img src="'.$this->GetFolder().'/images/no-image.png"></a>';
					}elseif(empty( $arElement["PREVIEW_PICTURE"]["SRC"] )){
						$img = '<a class="image-icon-200 test1" href="'.$arElement["DETAIL_PAGE_URL"].'"><img src="'.$arElement["DETAIL_PICTURE"]["SRC"].'"></a>';
					}else{
						$img = '<a class="image-icon-200 test3" href="'.$arElement["DETAIL_PAGE_URL"].'"><img src="'.$arElement["PREVIEW_PICTURE"]["SRC"].'"></a>';
					}
					?>
					<?=$img?>
				</td>
			<?endforeach?>
		</tr>
		<?foreach($arResult["ITEMS"][0]["PRICES"] as $code=>$arPrice):?>
			<?if($arPrice["CAN_ACCESS"]):?>
				<tr>
					<td valign="top" nowrap><?=$arResult["PRICES"][$code]["TITLE"]?></td><td>&nbsp;</td>
					<?foreach($arResult["ITEMS"] as $arElement):?>
						<td valign="top">
							<?if($arElement["PRICES"][$code]["CAN_ACCESS"]):?>
								<b><?=$arElement["PRICES"][$code]["PRINT_DISCOUNT_VALUE"]?></b>
							<?endif;?>
						</td>
					<?endforeach?>
				</tr>
			<?endif;?>
		<?endforeach;?>
		<tr>
			<td>&nbsp;</td><td>&nbsp;</td>
			<?foreach($arResult["ITEMS"] as $arElement):?>
				<td>
					<a class="bxr-color-button bxr-color-button-small" href="<?=$APPLICATION->GetCurPage()?>?action=DELETE_FROM_COMPARE_RESULT&IBLOCK_ID=<?=$arParams["IBLOCK_ID"]?>&ID[]=<?=$arElement["ID"]?>">
						<?=GetMessage('CATALOG_REMOVE')?>
					</a>
				</td>
			<?endforeach?>
		</tr>

		<?foreach($arResult["SHOW_PROPERTIES"] as $code=>$field):
			$arCompare = Array();
			foreach($arResult["ITEMS"] as $arElement)
			{
				$arPropertyValue = $arElement["DISPLAY_PROPERTIES"][$code]["VALUE"];
				if(is_array($arPropertyValue))
				{
					sort($arPropertyValue);
					$arPropertyValue = implode(" / ", $arPropertyValue);
				}
				$arCompare[] = $arPropertyValue;
			}
			$diff = (count(array_unique($arCompare)) > 1 ? true : false);
			if($diff || !$arResult["DIFFERENT"]):?>
			<tr>
				<td class="property-name"><?=$field["NAME"]?></td>
				<td>
					<a class="icon-button-delete" href="<?=$APPLICATION->GetCurPage()?>?action=DELETE_FEATURE&IBLOCK_ID=<?=$arParams["IBLOCK_ID"]?>&pr_code[]=<?=$field["CODE"]?>">
						<i class="fa fa-ban"></i>
					</a>
				</td>
                <?foreach($arResult["ITEMS"] as $arElement):?>
                    <td>
                        <?if(is_array($arElement["PROPERTIES"][$code]["VALUE"])) $arElement["PROPERTIES"][$code]["VALUE"] = implode(', ',$arElement["PROPERTIES"][$code]["VALUE"]);?>
                        <?if ($code === "MANUFACTURER"):?>
                            <?=$arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]?>
                        <?elseif ($arElement["PROPERTIES"][$code]["USER_TYPE"] === "HTML"):?>
                            <?=$arElement["PROPERTIES"][$code]["~VALUE"]["TEXT"]?>
                        <?else:?>
                            <?if (is_array($arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])):?>
                                <?foreach($arElement["DISPLAY_PROPERTIES"][$code]["DESCRIPTION"] as $propKey => $propDescription):?>
                                    <p><?=($arElement["DISPLAY_PROPERTIES"][$code]['WITH_DESCRIPTION'] == "Y" ? "<b>".$propDescription." : </b>" : "")?><?=$arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"][$propKey]?></p>
                                <?endforeach;?>
                            <?else:?>
                                <?=$arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]?>
                            <?endif?>
                        <?endif?>
                    </td>
                <?endforeach;?>
			</tr>
			<?endif?>
		<?endforeach;?>
	</table>
	<br />
	<?if(!empty($arResult["DELETED_PROPERTIES"]) || !empty($arResult["DELETED_OFFER_FIELDS"]) || !empty($arResult["DELETED_OFFER_PROPS"])):?>
		<noindex><p>
				<?=GetMessage("CATALOG_REMOVED_FEATURES")?>:
				<?foreach($arResult["DELETED_PROPERTIES"] as $arProperty):?>
					<a class="bxr-color-button bxr-color-button-small" href="<?=htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=ADD_FEATURE&pr_code=".$arProperty["CODE"],array("op_code","of_code","pr_code","action")))?>" rel="nofollow"><?=$arProperty["NAME"]?></a>
				<?endforeach?>
				<?foreach($arResult["DELETED_OFFER_FIELDS"] as $code):?>
					<a class="bxr-color-button bxr-color-button-small" href="<?=htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=ADD_FEATURE&of_code=".$code,array("op_code","of_code","pr_code","action")))?>" rel="nofollow"><?=GetMessage("IBLOCK_FIELD_".$code)?></a>
				<?endforeach?>
				<?foreach($arResult["DELETED_OFFER_PROPERTIES"] as $arProperty):?>
					<a class="bxr-color-button bxr-color-button-small" href="<?=htmlspecialcharsbx($APPLICATION->GetCurPageParam("action=ADD_FEATURE&op_code=".$arProperty["CODE"],array("op_code","of_code","pr_code","action")))?>" rel="nofollow"><?=$arProperty["NAME"]?></a>
				<?endforeach?>
			</p></noindex>
	<?endif?>

</div></div>
<div class="clearfix"></div>
