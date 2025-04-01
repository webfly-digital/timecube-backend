<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */
/** @global CMain $APPLICATION */

if (!empty($arResult['ITEMS']))
{
	?><ul<?=$arParams['IDENTIFIER'] ? ' id="'.$arParams['IDENTIFIER'].'"' : ''
	?> class="zverushki-tags"><?
	$i = 0;
	foreach ($arResult['ITEMS'] as $key => $item) {
		$i++;
		?><li<?=($i > $arParams['ITEMS_VISIBLE'] ? ' class="zver-hide"' : '')?>><a href="<? echo $item['URL_CPU']?>" title="<? echo $item['PAGE_SECTION_TITLE']?>"><? echo $item['PAGE_SECTION_TITLE']?></a></li><?
	}
	if($arResult['HIDE']){
		?><li><a id="zver-show-list" href="javascript:void(0)" rel="nofollow">...</a></li><?
	}?></ul><?
}