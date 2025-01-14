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
<div class="brands-grid">
    <? foreach ($arResult["ITEMS"] as $arItem):
    $this->AddEditAction(
        $arItem['ID'],$arItem['EDIT_LINK'],
        CIBlock::GetArrayByID($arItem["IBLOCK_ID"],"ELEMENT_EDIT")
    );
    ?>
    <div class="brand-card">
        <div class="brand-card__inner" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
            <div class="brand-card__picture">
                <a href="<?= $arItem['DETAIL_PAGE_URL'] ?>">
                <img alt="BrandName" class="lozad" data-src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>">
                </a>
            </div>
        </div>
    </div>
    <? endforeach; ?>
</div>

