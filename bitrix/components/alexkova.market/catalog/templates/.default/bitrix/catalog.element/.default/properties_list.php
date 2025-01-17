<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();?>
<?if ($arParams["PROPS_TAB_VIEW"] == "LIST") {?>
    <div class="bxr-props-block">
        <?foreach($arResult["DISPLAY_PROPERTIES"] as $key => $arProperty):
            if (!in_array($key, $arParams["PREVIEW_DETAIL_PROPERTY_CODE"]) || $arParams["HIDE_PREVIEW_PROPS_INLIST"] == 'N'):
                if (!is_array($arProperty["DISPLAY_VALUE"]) && $arProperty["DISPLAY_VALUE"]){?>
                        <div itemprop="additionalProperty" itemscope itemtype="http://schema.org/PropertyValue">
                                <h5 class="bxr-props-name">
                                    <b><span itemprop="name"><?=(!empty($arProperty["DESCRIPTION"][0])) ? $arProperty["DESCRIPTION"][0] : $arProperty["NAME"];?></span></b>
                                    <?if ($arProperty["HINT"]):?>
                                        <div class="item_title_hint_chint">
                                            <i id="item_title_hint_<?echo $arProperty["ID"]?>" class="fa fa-question-circle"></i>
                                            <div><?=$arProperty["HINT"]?></div>
                                        </div>              
                                    <?endif;?>
                                </h5>
                                <div class="bxr-props-data"><span itemprop="value"><?=$arProperty["DISPLAY_VALUE"]?></span></div>
                        </div>
                <?}elseif (is_array($arProperty["DISPLAY_VALUE"]) && count($arProperty["DISPLAY_VALUE"]>0)){

                        $withDesc = false;
                        foreach($arProperty["DESCRIPTION"] as $cell=>$val){
                            if ($val) {
                                $withDesc = true;
                                break;
                            }
                        }
                        if ($withDesc) {?>
                            <div>
                                <h5 class="bxr-props-data bxr-props-data-group">
                                    <b><?=$arProperty["NAME"]?></b>
                                    <?if ($arProperty["HINT"]):?>
                                        <div class="item_title_hint_chint">
                                            <i id="item_title_hint_<?echo $arProperty["ID"]?>" class="fa fa-question-circle"></i>
                                            <div><?=$arProperty["HINT"]?></div>
                                        </div>              
                                    <?endif;?>
                                </h5>
                            <?foreach($arProperty["DISPLAY_VALUE"] as $cell=>$val){?>
                                <div itemprop="additionalProperty" itemscope itemtype="http://schema.org/PropertyValue">
                                    <b><span class="bxr-props-name no-bold" itemprop="name"><?=$arProperty["DESCRIPTION"][$cell]?>: </span></b>
                                    <span class="bxr-props-data" itemprop="value"><?=$val?></span>
                                </div>
                            <?}?>
                            </div>
                        <?} else {?>
                            <div itemprop="additionalProperty" itemscope itemtype="http://schema.org/PropertyValue">
                                <h5 class="bxr-props-name">
                                    <b><span itemprop="name"><?=$arProperty["NAME"]?></span></b>
                                    <?if ($arProperty["HINT"]):?>
                                        <div class="item_title_hint_chint">
                                            <i id="item_title_hint_<?echo $arProperty["ID"]?>" class="fa fa-question-circle"></i>
                                            <div><?=$arProperty["HINT"]?></div>
                                        </div>              
                                    <?endif;?>
                                </h5>
                                <div class="bxr-props-data"><span itemprop="value"><?=  implode(', ', $arProperty["DISPLAY_VALUE"])?></span></div>
                            </div>
                        <?}
                }
            endif;
        endforeach;?>
    </div>
<?} else {?>
    <table class="bxr-props-table">
        <?foreach($arResult["DISPLAY_PROPERTIES"] as $key => $arProperty):
            if (!in_array($key, $arParams["PREVIEW_DETAIL_PROPERTY_CODE"]) || $arParams["HIDE_PREVIEW_PROPS_INLIST"] == 'N'):
                if (!is_array($arProperty["DISPLAY_VALUE"]) && $arProperty["DISPLAY_VALUE"]){?>
                        <tr itemprop="additionalProperty" itemscope itemtype="http://schema.org/PropertyValue">
                                <td class="bxr-props-name">
                                    <span itemprop="name"><?=(!empty($arProperty["DESCRIPTION"][0])) ? $arProperty["DESCRIPTION"][0] : $arProperty["NAME"];?></span>
                                    <?if ($arProperty["HINT"]):?>
                                        <div class="item_title_hint_chint">
                                            <i id="item_title_hint_<?echo $arProperty["ID"]?>" class="fa fa-question-circle"></i>
                                            <div><?=$arProperty["HINT"]?></div>
                                        </div>              
                                    <?endif;?>
                                </td>
                                <td class="bxr-props-data"><span itemprop="value"><?=$arProperty["DISPLAY_VALUE"]?></span></td>
                        </tr>
                <?}elseif (is_array($arProperty["DISPLAY_VALUE"]) && count($arProperty["DISPLAY_VALUE"]>0)){

                        $withDesc = false;
                        foreach($arProperty["DESCRIPTION"] as $cell=>$val){
                            if ($val) {
                                $withDesc = true;
                                break;
                            }
                        }
                        if ($withDesc) {?>
                            <tr>
                                    <td colspan="2" class="bxr-props-data bxr-props-data-group">
                                            <b><?=$arProperty["NAME"]?></b>
                                            <?if ($arProperty["HINT"]):?>
                                                <div class="item_title_hint_chint">
                                                    <i id="item_title_hint_<?echo $arProperty["ID"]?>" class="fa fa-question-circle"></i>
                                                    <div><?=$arProperty["HINT"]?></div>
                                                </div>              
                                            <?endif;?>
                                    </td>
                            </tr>
                            <?foreach($arProperty["DISPLAY_VALUE"] as $cell=>$val){?>
                                    <tr itemprop="additionalProperty" itemscope itemtype="http://schema.org/PropertyValue">
                                            <td class="bxr-props-name no-bold"><span itemprop="name"><?=$arProperty["DESCRIPTION"][$cell]?></span></td>
                                            <td class="bxr-props-data"><span itemprop="value"><?=$val?></span></td>
                                    </tr>
                            <?}
                        } else {?>
                            <tr itemprop="additionalProperty" itemscope itemtype="http://schema.org/PropertyValue">
                                <td class="bxr-props-name">
                                    <span itemprop="name"><?=$arProperty["NAME"]?></span>
                                    <?if ($arProperty["HINT"]):?>
                                        <div class="item_title_hint_chint">
                                            <i id="item_title_hint_<?echo $arProperty["ID"]?>" class="fa fa-question-circle"></i>
                                            <div><?=$arProperty["HINT"]?></div>
                                        </div>              
                                    <?endif;?>
                                </td>
                                <td class="bxr-props-data"><span itemprop="value"><?=  implode(', ', $arProperty["DISPLAY_VALUE"])?></span></td>
                            </tr>
                        <?}
                }
            endif;
        endforeach;?>
    </table>
<? } ?>
