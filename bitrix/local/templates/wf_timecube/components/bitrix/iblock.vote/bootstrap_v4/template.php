<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

CJSCore::Init(array("ajax"));

//Let's determine what value to display: rating or average ?
if ($arParams['DISPLAY_AS_RATING'] === 'vote_avg')
{
	if (
		!empty($arResult['PROPERTIES']['vote_count']['VALUE'])
		&& is_numeric($arResult['PROPERTIES']['vote_sum']['VALUE'])
		&& is_numeric($arResult['PROPERTIES']['vote_count']['VALUE'])
	)
	{
		$DISPLAY_VALUE = round($arResult['PROPERTIES']['vote_sum']['VALUE'] / $arResult['PROPERTIES']['vote_count']['VALUE'], 2);
	}
	else
	{
		$DISPLAY_VALUE = 0;
	}
}
else
{
	$DISPLAY_VALUE = $arResult["PROPERTIES"]["rating"]["VALUE"];
}
$voteContainerId = 'vote_'.$arResult["ID"];
$onclick = "JCFlatVote.do_vote(this, '".$voteContainerId."', ".$arResult["AJAX_PARAMS"].")";
?>
<!--если человек уже голосовал, то добавить класс voted к div.rating-->
<div class="rating <?if ($arParams["READ_ONLY"]!=="Y") echo 'voted'?> rating--active" id="<?= $voteContainerId?>">
    <ul class="rating__stars rating__stars--4">
        <?foreach ($arResult["VOTE_NAMES"] as $i => $name){
            $itemContainerId = $voteContainerId.'_'.$i;
            ?>
        <li class="svg-icon icon-star" data-star="<?=$i+1?>"
            id="<?echo $itemContainerId?>"
            title="<?echo $name?>"
            <?if (!$arResult["VOTED"] && $arParams["READ_ONLY"]!=="Y"){?>
                onmouseover="JCFlatVote.trace_vote(this, true);"
                onmouseout="JCFlatVote.trace_vote(this, false)"
                onclick="<?echo htmlspecialcharsbx($onclick);?>"
            <?}?>
        ></li>
        <?}?>
    </ul>
    <?if ($arParams["SHOW_RATING"] == "Y" && !empty($DISPLAY_VALUE)){?>
    <span class="rating__caption">(<?= $DISPLAY_VALUE?>)</span>
    <?}?>
</div>