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

//Такой же точно, как и на стартовой странице, с двумя изменениями:
// 1. Для main-slider-wrapper добавляется класс main-slider-wrapper--inner
// 2. Десктопные изображения должны браться из другого источника (они другой высоты).
// 3. Мобильные фотки по-прежнему, как и на главном слайдере.
// Вставлять это добро внутри каталога перед <h1 class="pagetitle"></h1>.
//
?>
<!--Main slider-->
<div class="main-slider-wrapper main-slider-wrapper--inner wide-content" id="main-slider">
    <div class="main-slider">
        <? foreach ($arResult["ITEMS"] as $arItem):
            $this->AddEditAction(
                $arItem['ID'],$arItem['EDIT_LINK'],
                CIBlock::GetArrayByID($arItem["IBLOCK_ID"],"ELEMENT_EDIT")
            );
            ?>
            <div class="main-slider__slide">
                <div class="action-item" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
                    <a href="<?=$arItem['DETAIL_PAGE_URL']?>">
                        <div class="action-item__pic action-item__pic--desktop" >
                            <img alt="<?=$arItem['NAME']?>" class="lozad"
                                 data-src="<?=$arItem['CATALOG_BANNER']?>" height="180px" weight="2180"  src="<?=$arItem['CATALOG_BANNER']?>">
                        </div>
                        <div class="action-item__pic action-item__pic--mobile">
                            <img alt="<?=$arItem['NAME']?>" class="lozad"
                                 data-src="<?=$arItem['DETAIL_PICTURE']['SRC']?>" height="240" weight="320" src="<?=$arItem['DETAIL_PICTURE']['SRC']?>">
                        </div>
                    </a>
                </div>
            </div>
        <? endforeach; ?>
    </div>
</div>
<!--Main slider ends-->



