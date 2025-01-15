<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */
/** @global CMain $APPLICATION */

if (!empty($arResult['ITEMS']))
{
	$this->addExternalCss($templateFolder.'/style.css', true);
	?><link rel="stylesheet" type="text/css" href="<?=$templateFolder?>/style.css"><ul<?=$arParams['IDENTIFIER'] ? ' id="'.$arParams['IDENTIFIER'].'"' : ''?> class="zverushki-tags"><?
	foreach ($arResult['ITEMS'] as $key => $item) {
		?><li><a href="<? echo $item['URL_CPU']?>" title="<? echo $item['PAGE_TITLE']?>"><? echo $item['PAGE_TITLE']?></a></li><?
	}?></ul><?
}