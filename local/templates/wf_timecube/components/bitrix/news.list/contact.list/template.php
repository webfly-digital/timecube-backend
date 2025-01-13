<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
?>
<div class="contact-group contact-group--wide">
    <h2 class="contact-group__title">
        <?=$arResult["NAME"]?>
    </h2>
    <div class="contact-group__content">
        <div class="persons-list">
            <? if (!empty($arResult['ITEMS'])): ?>
                <? foreach ($arResult['ITEMS'] as $ITEM) { ?>
                    <div class="person">
                        <div class="person__photo">
                            <img alt="<?= $ITEM["PREVIEW_PICTURE"]["ALT"] ?>" src="null" class="lozad"
                                 data-src=" <?= $ITEM["PREVIEW_PICTURE"]["SRC"] ?>">
                        </div>
                        <div class="person__content">
                            <p class="person__name">
                                <?= $ITEM['NAME'] ?>
                            </p>
                            <p class="person__position">
                                <?= $ITEM["PROPERTIES"]["POSITION"]["VALUE"] ?>
                            </p>
                            <p class="person__phone">
                                <?= $ITEM["PROPERTIES"]["PHONE"]["VALUE"] ?>
                            </p>
                            <p class="person__email">
                                <a href="mailto:<?= $ITEM["PROPERTIES"]["EMAIL"]["VALUE"] ?>">
                                    <?= $ITEM["PROPERTIES"]["EMAIL"]["VALUE"] ?></a>
                            </p>
                        </div>
                    </div>
                <? } ?>
            <? endif; ?>
        </div>
    </div>
</div>