<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */
/** @global CMain $APPLICATION */

if (!empty($arResult['ITEMS']))
{
	?><div<?=$arParams['IDENTIFIER'] ? ' id="'.$arParams['IDENTIFIER'].'"' : ''?> class="zverushki-tags"><?
	foreach ($arResult['ITEMS'] as $key => $item) {
		?><a class="btn btn-default btn-xs" href="<? echo $item['URL_CPU']?>" title="<? echo $item['PAGE_TITLE']?>"><? echo $item['PAGE_TITLE']?> (<? echo $item['COUNT']?>)</a><?
	}?></div><?
}