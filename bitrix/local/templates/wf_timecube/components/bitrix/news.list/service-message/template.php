<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
/*
Сервисное сообщение.
Закрывается и больше не открывается.
Можно в качестве ID повесить sessionId, тогда в случае закрытия, оно не будет открываться у пользователя до конца сессии.
*/
foreach($arResult["ITEMS"] as $arItem) {?>
<div class="wf-alert--info wf-alert" id="alert-<?=$arItem['ID']?>">
    <div class="wf-alert__content">
        <p><?=$arItem['PREVIEW_TEXT']?></p>
    </div>
    <button class="btn-close btn-transparent" aria-label="Закрыть уведомление"><span class="svg-icon icon-close"></span></button>
</div>
<?}
