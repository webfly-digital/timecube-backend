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

?>
<div class="actions-list">
    <?php foreach ($arResult["ITEMS"] as $arItem):
        // Если акция помечена как "Не публичная" (XML_ID = "Y"), пропускаем элемент
        if ($arItem['PROPERTIES']['HIDDEN_PROMO']['VALUE_XML_ID'] === 'Y') {
            continue;
        }

        $this->AddEditAction(
            $arItem['ID'], $arItem['EDIT_LINK'],
            CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT")
        );
        ?>
        <div class="action-item" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
            <a href="<?= $arItem['DETAIL_PAGE_URL'] ?>">
                <?php if (!empty($arItem['PREVIEW_PICTURE']['SRC'])) { ?>
                    <div class="action-item__pic action-item__pic--desktop">
                        <img class="lozad" data-src="<?= $arItem['PREVIEW_PICTURE']['SRC'] ?>" alt="<?= $arItem['NAME'] ?>">
                    </div>
                    <div class="action-item__pic action-item__pic--mobile">
                        <img class="lozad" data-src="<?= $arItem['DETAIL_PICTURE']['SRC'] ?>" alt="<?= $arItem['NAME'] ?>">
                    </div>
                <?php } else { ?>
                    <h4><?= $arItem['NAME']; ?></h4>
                <?php } ?>
            </a>
        </div>
    <?php endforeach; ?>
</div>