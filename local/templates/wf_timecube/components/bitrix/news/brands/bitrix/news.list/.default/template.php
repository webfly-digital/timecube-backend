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

<div class="text-content">
    <?foreach($arResult["ITEMS"] as $arItem):
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	//$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
    <div class="media-card-h" id="<?=$this->GetEditAreaId($arItem['ID']);?>"><a name="<?=$arItem["CODE"]?>"></a>
        <div class="media-card-h__pic size-sm centered">
            <a href="<?=$arItem['DETAIL_PAGE_URL']?>" rel="nofollow">
                <img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>">
            </a>
        </div>
        <div class="media-card-h__content">
            <div class="m-country"><?=$arItem['PROPERTIES']['COUNTRY']['VALUE']?></div>
            <p class="media-card-h__title big"><a href="<?=$arItem['DETAIL_PAGE_URL']?>" rel="nofollow"><?=$arItem["NAME"]?></a></p>
            <p><strong>«<?=$arItem["NAME"]?>»</strong> <?=$arItem["DETAIL_TEXT"]?></p>
            <p><a href="<?=$arItem['DETAIL_PAGE_URL']?>" rel="nofollow" title="Все товары <?=$arItem['NAME']?>">Подробнее</a></p>
        </div>
    </div>
<?endforeach;?>
</div>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
