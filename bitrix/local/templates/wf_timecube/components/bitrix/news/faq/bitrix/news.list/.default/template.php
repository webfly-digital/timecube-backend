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
<ul class="faq-list">
        <? foreach ($arResult["ITEMS"] as $arItem):
            $this->AddEditAction(
                $arItem['ID'],$arItem['EDIT_LINK'],
                CIBlock::GetArrayByID($arItem["IBLOCK_ID"],"ELEMENT_EDIT")
            );
            ?>
            <div class="faq-item" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
                <a href="<?=$arItem['DETAIL_PAGE_URL']?>">
                    <?=$arItem['NAME']?>
                </a>
            </div>
        <? endforeach; ?>
</ul>


