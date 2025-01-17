<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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

$hoverEffectFile = ($arParams['HOVER_EFFECT']) ? $arParams['HOVER_EFFECT'] : 'default';
$displayTypeClass = ($arParams['DISPLAY_TYPE']) ? $arParams['DISPLAY_TYPE'] : 'block';
?>

<div class="row bxr-promo-ribbon bxr-promo-<?=$displayTypeClass?>">

    <?  foreach ($arResult['ITEMS'] as $arItem):  ?>
        <?
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        ?>
        <div id="<?=$this->GetEditAreaId($arItem['ID']);?>" 
             class="bxr-promo-element <?=$arItem['PROPERTIES']["LG_COL_COUNT"]['VALUE_XML_ID']?> <?=$arItem['PROPERTIES']["MD_COL_COUNT"]['VALUE_XML_ID']?> <?=$arItem['PROPERTIES']["SM_COL_COUNT"]['VALUE_XML_ID']?> <?=$arItem['PROPERTIES']["XS_COL_COUNT"]['VALUE_XML_ID']?>">
                <?
                    if (file_exists($_SERVER['DOCUMENT_ROOT'].$this->GetFolder().'/include/'.$hoverEffectFile.'.php') 
                        && $arItem['PROPERTIES']["PROMO_NO_EFFECT"]['VALUE']!='Y')
                    {
                            include ($_SERVER['DOCUMENT_ROOT'].$this->GetFolder().'/include/'.$hoverEffectFile.'.php');
                    }
                    else
                    {
                            include ($_SERVER['DOCUMENT_ROOT'].$this->GetFolder().'/include/default.php');
                            $this->addExternalCss($this->GetFolder().'/include/css/default.css');
                    }
                ?>
        </div>
    <?  endforeach; ?>

</div>

<?
    //подключение css
    if (file_exists($_SERVER['DOCUMENT_ROOT'].$this->GetFolder().'/include/css/'.$hoverEffectFile.'.css'))
        $this->addExternalCss($this->GetFolder().'/include/css/'.$hoverEffectFile.'.css');
    else
        $this->addExternalCss($this->GetFolder().'/include/css/default.css');
?>

