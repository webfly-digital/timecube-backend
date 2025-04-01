<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */
/** @global CMain $APPLICATION */

if (!empty($arResult['ITEMS']))
{
	?><div<?=$arParams['IDENTIFIER'] ? ' id="'.$arParams['IDENTIFIER'].'"' : ''?> class="zverushki-tags"><?
	$i = 0;
	foreach ($arResult['ITEMS'] as $key => $item) {
		$i++;
		?><a class="btn btn-default btn-xs<?=($i > $arParams['ITEMS_VISIBLE'] ? ' zver-hide' : '')?>" href="<? echo $item['URL_CPU']?>"
             title="<? echo $item['PAGE_SECTION_TITLE']?>"><? echo $item['PAGE_SECTION_TITLE']?> (<? echo $item['COUNT']?>)</a><?
	}

	if($arResult['HIDE']){
		?><a id="zver-show-buttons-count" class="btn btn-default btn-xs" href="javascript:void(0)" rel="nofollow">...</a><?
	}?></div><?
}