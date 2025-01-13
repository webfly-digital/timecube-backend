<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/**
 * @var array $arResult
 * @var array $arParam
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if(!$arResult["NavShowAlways"])
{
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}

	$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
	$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");
    ?>
<!--<p class="catalog-menu__caption">--><?//=GetMessage("MAIN_UI_PAGINATION__PAGES")?><!--</p>-->
<div class="pagination-container catalog-menu__links">
<ul class="list-inline"><?
    if ($arResult["NavPageNomer"] > 1):
        if ($arResult["nStartPage"] > 1):
            if($arResult["bSavePage"]):
                ?><li class="page-item"><a class="page-link" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=1">1</a></li><?
            else:
                ?><li class="page-item"><a class="page-link" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>">1</a></li><?
            endif;

            if ($arResult["nStartPage"] > 2):
                ?><li class="page-item"><a class="page-link" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=round($arResult["nStartPage"] / 2)?>">...</a></li><?
            endif;
        endif;
    endif;

    do
    {
        if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):
            ?><li class="page-item active"><span class="page-link"><?=$arResult["nStartPage"]?></span></li><?
        elseif($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false):
            ?><li class="page-item"><a class="page-link" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=$arResult["nStartPage"]?></a></li><?
        else:
            ?><li class="page-item"><a class="page-link" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"><?=$arResult["nStartPage"]?></a></li><?
        endif;
        $arResult["nStartPage"]++;
    }

    while($arResult["nStartPage"] <= $arResult["nEndPage"]);

    if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):
        if ($arResult["nEndPage"] < $arResult["NavPageCount"]):
            if ($arResult["nEndPage"] < ($arResult["NavPageCount"] - 1)):
                ?><li class="page-item"><a class="page-link" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=round($arResult["nEndPage"] + ($arResult["NavPageCount"] - $arResult["nEndPage"]) / 2)?>">...</a></li><?
            endif;
            ?><li class="page-item"><a class="page-link" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["NavPageCount"]?>"><?=$arResult["NavPageCount"]?></a></li><?
        endif;
    endif;

    /* hide show all
    if ($arResult["bShowAll"]):
        if ($arResult["NavShowAll"]):
            ?><li class="page-item"><a class="page-link" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>SHOWALL_<?=$arResult["NavNum"]?>=0"><?=GetMessage("MAIN_UI_PAGINATION__PAGED")?></a></li><?
        else:
            ?><li class="page-item"><a class="page-link" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>SHOWALL_<?=$arResult["NavNum"]?>=1"><?=GetMessage("MAIN_UI_PAGINATION__ALL")?></a></li><?
        endif;
    endif;
    */
    ?>
    </ul>
</div>
