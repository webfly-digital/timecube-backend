<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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

$bgm = current($arResult["ITEMS"])['PREVIEW_PICTURE']['SRC'];
$bg = current($arResult["ITEMS"])['DETAIL_PICTURE']['SRC'];
?>
<style>
    @media (min-width: 480px) {
        .action-item__pic--mobile {
            display: none;
        }
    }

    @media (max-width: 479px) {
        .action-item__pic--desktop {
            display: none;
        }
    }
</style>
<!--Main slider-->
<div class="main-slider-wrapper wide-content" id="main-slider">
    <div class="main-slider">
        <?php
        $first = true;
        foreach ($arResult["ITEMS"] as $arItem):

            // Если акция помечена как "Не публичная" (XML_ID = "Y"), пропускаем элемент
            if ($arItem['PROPERTIES']['HIDDEN_PROMO']['VALUE_XML_ID'] === 'Y') {
                continue;
            }

            $this->AddEditAction(
                $arItem['ID'], $arItem['EDIT_LINK'],
                CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT")
            ); ?>
            <div class="main-slider__slide" <?php if (!$first) echo 'style="display:none"' ?>>
                <div class="action-item" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
                    <a href="<?= $arItem['DETAIL_PAGE_URL'] ?>">
                        <div class="action-item__pic action-item__pic--desktop">
                            <img <? php/*loading="lazy" alt="<?=$arItem['NAME']?>" class="lozad" data-src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>"*/
                            ?> alt="<?= $arItem['PREVIEW_PICTURE']['ALT'] ?>" title="<?= $arItem['PREVIEW_PICTURE']['TITLE'] ?>"
                               src="<?= $arItem['PREVIEW_PICTURE']['SRC'] ?>">
                        </div>
                        <div class="action-item__pic action-item__pic--mobile">
                            <img <? php/*loading="lazy" class="lozad"  data-src="<?=$arItem['DETAIL_PICTURE']['SRC']?>"*/
                            ?> alt="<?= $arItem['DETAIL_PICTURE']['ALT'] ?>" title="<?= $arItem['DETAIL_PICTURE']['TITLE'] ?>"
                               src="<?= $arItem['DETAIL_PICTURE']['SRC'] ?>">
                        </div>
                    </a>
                </div>
            </div>
            <?php
            $first = false;
        endforeach; ?>
    </div>
</div>

<!--Main slider ends-->