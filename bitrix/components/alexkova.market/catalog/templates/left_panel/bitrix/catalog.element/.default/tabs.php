<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

    if(is_array($arResult["TABS"]) && count($arResult["TABS"])>0):?>
        <ul class="bxr-detail-tabs hidden-xxs"><?
            foreach ($arResult["TABS"] as $k => $tab):
                
                    if(empty($tab["name"]))
                        continue;
                    if($k == "props" && count($arResult["DISPLAY_PROPERTIES"])===0)
                            continue;
                ?><li data-tab="<?=$k?>"><?=$tab["name"]?></li><?
            endforeach;?>
        </ul>
		<div class="clearfix"></div><?
    endif;
