<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->createFrame()->begin('sortpanel');
global $arSortGlobal;
$numShow = ('Y' == $arParams["PAGE_ELEMENT_COUNT_SHOW"]);
$viewShow = ('Y' == $arParams["CATALOG_VIEW_SHOW"]);
?>
<div class="col-xs-12<?=($arParams["THEME"] == 'default') ?' bxr-border-color':' bxr-color-flat'?> bxr-sort-panel">
        <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-5 text-xs-right text-md-left">
                <span class="hidden-sm hidden-lg hidden-md text-xs-right"><?=GetMessage("KZNC_SORT_BY")?></span>
                <?foreach ($arResult["SORT_PROPS"] as $key => $val):
                        $className = ($arSortGlobal["sort"] == $val[0]) ? ' active' : ' hidden-xs';
                        $icon = "";
                        if ($className){
                                $className .= ($arSortGlobal["sort_order"] == 'asc') ? ' asc' : ' desc';
                                $icon = ($arSortGlobal["sort_order"] == 'asc') ? '<i class="fa fa-arrow-up"></i>' : ' <i class="fa fa-arrow-down"></i>';
                        }

                        if (strlen($val[3])>0){
                                $className .= " ".$val[3];
                        }

                        $newSort = ($arSortGlobal["sort"] == $val[0]) ? ($arSortGlobal["sort_order"] == 'desc' ? 'asc' : 'desc') : $arAvailableSort[$key][1];
                        ?>
                        <a href="<?=$APPLICATION->GetCurPageParam('sort='.$key.'&order='.$newSort, array('sort', 'order'))?>"
                           class="bxr-sortbutton<?=$className?> <?if(number_key($arResult["SORT_PROPS"], $key) == count($arResult["SORT_PROPS"])) echo "last";?>" rel="nofollow">
                                <?=$val[2]?><?=($arSortGlobal["sort"] == $val[0])?$icon:''?>
                        </a>
                <?endforeach;?>
        </div>
        <?if ($numShow):?>
                <div class="col-xs-12 col-sm-<?=$viewShow?'6':'12'?> col-md-<?=$viewShow?'4':'7'?> text-right">
                        <span class="hidden-sm hidden-lg hidden-md"><?=GetMessage("KZNC_SORT_COUNT_NAME")?></span>
                        <? foreach ($arParams["PAGE_ELEMENT_COUNT_LIST"] as $val) :?>
                        <?if ($val > 0):?>
                        <a href="<?=$APPLICATION->GetCurPageParam('num='.$val,array('num'));?>" title="<?=GetMessage('KZNC_VIEW_BY')." ".$val." ".GetMessage('KZNC_ITEM_NAME').NumberWordEndingsEx($val)?>" class="bxr-view-mode<?=($arSortGlobal["num"] == $val) ? ' active' : '';?>">
                                <?=$val;?>
                        </a>
                        <?endif;?>
                        <?endforeach;?>
                </div>
        <?endif;?>
        <?if ($viewShow):?>
        <div class="col-xs-12 col-sm-<?=$numShow?'6':'12'?> col-md-<?=$numShow?'3':'7'?> text-right">
                <span class="hidden-sm hidden-lg hidden-md"><?=GetMessage("KZNC_SORT_VIEW_NAME")?></span>
                <a href="<?=$APPLICATION->GetCurPageParam('view=title',array('view'));?>" title="<?=GetMessage('KZNC_VIEW_PLITKA')?>" class="bxr-view-mode<?=($arSortGlobal['view'] == 'title' || !$arSortGlobal['view']) ? ' active' : '';?>">
                        <i class="fa fa-th"></i>
                </a>
                <a href="<?=$APPLICATION->GetCurPageParam('view=list',array('view'));?>" title="<?=GetMessage('KZNC_VIEW_LIST')?>" class="bxr-view-mode<?=($arSortGlobal['view'] == 'list') ? ' active' : '';?>">
                        <i class="fa fa-th-list"></i>
                </a>
                <a href="<?=$APPLICATION->GetCurPageParam('view=table',array('view'));?>" title="<?=GetMessage('KZNC_VIEW_TABLE')?>" class="bxr-view-mode<?=($arSortGlobal['view'] == 'table') ? ' active' : '';?>">
                        <i class="fa fa-align-justify"></i>
                </a>
        </div>
        <?endif;?>
        </div>
</div>