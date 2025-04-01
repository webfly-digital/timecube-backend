<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?if (!empty($arResult)):?>
    <ul class="bxr-topline-menu">
        <?foreach($arResult as $arItem):
            $hiddenClass = ($arItem["PARAMS"]["hidden-md"] == "Y") ? "hidden-md" : "";
            if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
                continue;?>
            <?if($arItem["SELECTED"]):?>
                <li class="<?=$hiddenClass?>">
                    <a href="<?=$arItem["LINK"]?>" class="selected bxr-font-color-light"><?=$arItem["TEXT"]?></a>
                </li>
            <?else:?>
                <li class="<?=$hiddenClass?>">
                    <a href="<?=$arItem["LINK"]?>" class="bxr-font-color"><?=$arItem["TEXT"]?></a>
                </li>
            <?endif?>
        <?endforeach?>
    </ul>
    <div class="clearfix"></div>
<?endif?>