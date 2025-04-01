<figure class="effect-apollo"
        style='background: <? echo ($arItem['PROPERTIES']['EFFECT_ADD_COLOR']['VALUE']) ? $arItem['PROPERTIES']['EFFECT_ADD_COLOR']['VALUE'] : 'transparent';?>;'
    >
        <div class="bxr-promo-image" 
                 alt="<?=$arItem['DETAIL_PICTURE']['ALT']?>" 
                 title="<?=$arItem['DETAIL_PICTURE']['TITLE']?>" 
                 style="background-image:url(<?=$arItem['DETAIL_PICTURE']['SRC']?>)">
        </div>
        <figcaption>
            <?if ($arItem['PROPERTIES']['PROMO_HIDE_NAME']['VALUE']!='Y'):?>
                <h2 style='background: <? echo ($arItem['PROPERTIES']['NAME_BACK_COLOR']['VALUE']) ? $arItem['PROPERTIES']['NAME_BACK_COLOR']['VALUE'] : 'transparent';?>;
                        color: <? echo ($arItem['PROPERTIES']['NAME_COLOR']['VALUE']) ? $arItem['PROPERTIES']['NAME_COLOR']['VALUE'] : '#fff';?>;'>
                        <?=$arItem['NAME']?>
                </h2>
            <?endif;?>
            <?if ($arItem['PREVIEW_TEXT']):?>
                <p style='background: <? echo ($arItem['PROPERTIES']['TEXT_BACK_COLOR']['VALUE']) ? $arItem['PROPERTIES']['TEXT_BACK_COLOR']['VALUE'] : 'transparent';?>;
                       color: <? echo ($arItem['PROPERTIES']['TEXT_COLOR']['VALUE']) ? $arItem['PROPERTIES']['TEXT_COLOR']['VALUE'] : '#fff';?>;'>
                        <?=$arItem['PREVIEW_TEXT']?>
                </p>
            <?endif;?>
            <a href="<?=$arItem['PROPERTIES']['PROMO_LINK']['VALUE']?>"<?echo ($arItem['PROPERTIES']['PROMO_LINK_OPEN_NEW']['VALUE']=='Y') ? ' target="_blank"' : ''?>>View more</a>
        </figcaption>   
</figure>

