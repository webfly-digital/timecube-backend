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
?>

    <div class="img-box">
        <div class="img"><div class="table-cell">
                <img src="<?=$arResult['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arResult['NAME']?>" title="<?=$arResult['NAME']?>">
            </div></div>
        <div class="m-country"><?=$arResult['PROPERTIES']['COUNTRY']['VALUE']?></div>
    </div>

