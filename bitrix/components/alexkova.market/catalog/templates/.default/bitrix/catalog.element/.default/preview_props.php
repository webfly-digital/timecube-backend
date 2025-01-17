<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();?>
<table class="bxr-props-table">
	<?foreach($arParams["PREVIEW_DETAIL_PROPERTY_CODE"] as $propertyCode):
		$arProperty = $arResult["DISPLAY_PREVIEW_PROPERTIES"][$propertyCode];
                if (!is_array($arProperty["DISPLAY_VALUE"]) && $arProperty["DISPLAY_VALUE"]) {?>
                        <tr itemprop="additionalProperty" itemscope itemtype="http://schema.org/PropertyValue">
                                <td class="bxr-props-name"><span itemprop="name"><?=(!empty($arProperty["DESCRIPTION"][0])) ? $arProperty["DESCRIPTION"][0] : $arProperty["NAME"];?></span></td>
                                <td class="bxr-props-data"><span itemprop="value"><?=$arProperty["DISPLAY_VALUE"]?></span></td>
                        </tr>
                <?} elseif (is_array($arProperty["DISPLAY_VALUE"]) && count($arProperty["DISPLAY_VALUE"] > 0)) {
                        
                        $withDesc = false;
                        foreach($arProperty["DESCRIPTION"] as $cell=>$val) {
                            if ($val) {
                                $withDesc = true;
                                break;
                            }
                        }
                        if ($withDesc) {?>
                            <tr>
                                    <td colspan="2" class="bxr-props-data bxr-props-data-group">
                                            <b><?=$arProperty["NAME"]?></b></td>
                            </tr>
                            <?foreach($arProperty["DISPLAY_VALUE"] as $cell=>$val){?>
                                    <tr itemprop="additionalProperty" itemscope itemtype="http://schema.org/PropertyValue">
                                            <td class="bxr-props-name no-bold"><span itemprop="name"><?=$arProperty["DESCRIPTION"][$cell]?></span></td>
                                            <td class="bxr-props-data"><span itemprop="value"><?=$val?></span></td>
                                    </tr>
                            <?}
                        } else {?>
                            <tr itemprop="additionalProperty" itemscope itemtype="http://schema.org/PropertyValue">
                                <td class="bxr-props-name"><span itemprop="name"><?=$arProperty["NAME"]?></span></td>
                                <td class="bxr-props-data"><span itemprop="value"><?=  implode(', ', $arProperty["DISPLAY_VALUE"])?></span></td>
                            </tr>
                        <?}
                }
        endforeach;?>
</table>