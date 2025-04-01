<div class="bxr-default-element">
    <a href="<?=$arItem['PROPERTIES']['PROMO_LINK']['VALUE']?>"<?echo ($arItem['PROPERTIES']['PROMO_LINK_OPEN_NEW']['VALUE']=='Y') ? ' target="_blank"' : ''?>>
            <div class="bxr-promo-image" 
                 alt="<?=$arItem['DETAIL_PICTURE']['ALT']?>" 
                 title="<?=$arItem['DETAIL_PICTURE']['TITLE']?>" 
                 style="background-image:url(<?=$arItem['DETAIL_PICTURE']['SRC']?>)">
            </div>
            
            <div class="bxr-promo-ribbon-info">
                <?if ($arItem['PROPERTIES']['PROMO_HIDE_NAME']['VALUE']!='Y'):?>
                    <span class="bxr-promo-ribbon-name" 
                          style='background-color:<? echo ($arItem['PROPERTIES']['NAME_BACK_COLOR']['VALUE']) ? $arItem['PROPERTIES']['NAME_BACK_COLOR']['VALUE'] : 'transparent';?>;
                                 color:<? echo ($arItem['PROPERTIES']['NAME_COLOR']['VALUE']) ? $arItem['PROPERTIES']['NAME_COLOR']['VALUE'] : '#fff';?>;'
                    >
                            <?=$arItem['NAME']?>
                    </span><br>
                <?endif;?>
                    
                <?if ($arItem['PREVIEW_TEXT']):?>
                    <span class="bxr-promo-ribbon-text" 
                          style='background-color:<? echo ($arItem['PROPERTIES']['TEXT_BACK_COLOR']['VALUE']) ? $arItem['PROPERTIES']['TEXT_BACK_COLOR']['VALUE'] : 'transparent';?>;
                                 color:<? echo ($arItem['PROPERTIES']['TEXT_COLOR']['VALUE']) ? $arItem['PROPERTIES']['TEXT_COLOR']['VALUE'] : '#fff';?>;'
                    >
                        <?=$arItem['PREVIEW_TEXT']?>
                    </span>
                <?endif;?>
            </div>
    </a>
</div>