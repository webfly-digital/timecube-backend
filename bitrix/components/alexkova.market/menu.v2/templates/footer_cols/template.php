<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$this->setFrameMode(true);
$cols = intval($arParams["COLS"])?:1;
if(!function_exists('footerPrintMenuCol'))
{
	function footerPrintMenuCol($items, $options)
	{
		$maxLevel = intval($options["MAX_LEVEL"]);
		$addUlClass = $options["UL_CLASS"];
		?>
		<ul <?if($addUlClass) echo "class='{$addUlClass}'"?>>
			<?
			foreach($items as $item):
				if($maxLevel == 1 && $item["DEPTH_LEVEL"] > 1)
					continue;
				?>
				<?if($item["SELECTED"]):?>
				<li><a href="<?=$item["LINK"]?>" class="selected"><?=$item["TEXT"]?></a></li>
			<?else:?>
				<li><a href="<?=$item["LINK"]?>"><?=$item["TEXT"]?></a></li>
			<?endif?>

			<?endforeach?>

		</ul>
	<?}
}
?>
<?if (!empty($arResult)):
	if($cols > 1):
		$arrChunked = array_chunk($arResult, ceil(count($arResult) / $arParams["COLS"]));
                $adaptCols = 12 / $arParams["COLS"];
		foreach ($arrChunked as $key=>$items):?>
			<div class="col-lg-<?=$adaptCols?> col-md-<?=$adaptCols?> col-sm-12 col-xs-12 bxr-footer-col">
				<?
				$addUlClass = '';
				if($key == 0)
					$addUlClass = 'first';
				footerPrintMenuCol($items, array('MAX_LEVEL'=>$arParams["MAX_LEVEL"], "UL_CLASS"=>$addUlClass));?>
			</div>
		<?endforeach?>
	<?else:?>
		<div class="col-lg-<?=$adaptCols?> col-md-<?=$adaptCols?> col-sm-12 col-xs-12 bxr-footer-col">
			<?footerPrintMenuCol($arResult, array('MAX_LEVEL'=>$arParams["MAX_LEVEL"],"UL_CLASS"=>'first'));?>
		</div>
	<?endif;?>
<?endif?>