<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

//\Bitrix\Main\Diag\Debug::dump($arParams);
//\Bitrix\Main\Diag\Debug::dump($arResult);
?>
<a class="icon-button" href="/favorites/">
    <span class="icon-button__content">
        <span class="icon-button__icon">
            <span class="svg-icon icon-heart-outline"></span>
        </span><span class="icon-button__caption">Избранное</span>
        <span class="icon-button__counter" <? if (empty($arResult['COUNT'])) echo 'style="display:none"' ?>
              id="wf_favorites_counter"><?= $arResult['COUNT'] ?></span>
    </span>
</a>
<script>
    var WF_FAVORITES = <?=json_encode($arResult['FAVORITES'])?>;
    var WF_FAVORITES_COUNTER = <?=intval($arResult['COUNT'])?>;
    updateCounter();
</script>
