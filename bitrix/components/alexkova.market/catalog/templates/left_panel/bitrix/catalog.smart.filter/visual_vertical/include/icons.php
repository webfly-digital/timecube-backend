<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$iconPropCodes = $iconPropCodes = array(
	"SPECIALOFFER",
	"NEWPRODUCT",
	"SALELEADER",
	"RECOMMENDED"
);;
$icons = array();
foreach ($arResult["ITEMS"] as $key => $arItem):
	if(!in_array($arItem["CODE"],$iconPropCodes))
		continue;
        if ($arItem["VALUES"] && count($arItem["VALUES"])>0)
                $icons[] = $arItem;
endforeach;

if(empty($icons))
	return;
?>
<div class="bx_filter_container bx_filter_parameters_box icon">
	<span class="bx_filter_container_modef"></span>
<?foreach ($icons as $arItem):
		if(empty($arItem["VALUES"]) || isset($arItem["PRICE"]))
			continue;

		switch($arItem["CODE"])
		{
			case 'SPECIALOFFER':
				$iconCode = 'sale';
				break;
			case 'NEWPRODUCT':
				$iconCode = 'new';
				break;
			case 'SALELEADER':
				$iconCode = 'hit';
				break;
			case 'RECOMMENDED':
				$iconCode = 'rec';
				break;
		}
		?>

			<?foreach($arItem["VALUES"] as $val => $ar):?>
				<div class="bxr-filter-marker" id="filter_icon_<?=$ar["CONTROL_ID"]?>">
					<div
						class="bxr-marker-<?=$iconCode?> <?=$ar["CHECKED"]?'active':''?>"
						title = "<?=GetMessage("FILTER_MESSAGE_".$arItem["CODE"]."_TITLE")?>"
						onclick="smartFilter.iconClick(this)"
						>
						<?=GetMessage("FILTER_MESSAGE_".$arItem["CODE"])?>
						</div>
					<input
						type="checkbox"
						value="<?echo $ar["HTML_VALUE"]?>"
						name="<?echo $ar["CONTROL_NAME"]?>"
						id="<?echo $ar["CONTROL_ID"]?>"
						<?echo $ar["CHECKED"]? 'checked="checked"': ''?>
						onclick="smartFilter.click(this)"
						/>
				</div>
			<?endforeach;?>
	<?
	endforeach;
	?>
	<div class="clear"></div>
	<div class="filter-separator" style="margin-top: 10px"></div>
</div>