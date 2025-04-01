<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?if (!empty($arResult)):?>
<nav>
    <ul class="bxr-service-menu">
        <?foreach($arResult as $arItem):
            if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
                continue;?>
            <?if($arItem["SELECTED"]):?>
                <li>
                    <a href="<?=$arItem["LINK"]?>" class="selected bxr-font-color-light"><?=$arItem["TEXT"]?></a>
                </li>
            <?else:?>
                <li>
                    <a href="<?=$arItem["LINK"]?>" class="bxr-font-color"><?=$arItem["TEXT"]?></a>
                </li>
            <?endif?>
        <?endforeach?>
        <div class="clearfix"></div>
    </ul>
</nav>
<?endif?>