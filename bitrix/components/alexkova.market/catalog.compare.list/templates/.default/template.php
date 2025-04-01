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
if (isset($_REQUEST['ajaxbuy']) && $_REQUEST['ajaxbuy'] == "yes") {
    $APPLICATION->RestartBuffer();
} ?>

<?php

//$this->setFrameMode(true);
$containerId = "bxr-counter-compare-new";
$addClass = 'class="display:none"';

?>

<?php if (!isset($_REQUEST['ajaxbuy']) || $_REQUEST['ajaxbuy'] != "yes"): ?>
    <?php $containerId = "bxr-counter-compare"; ?>
    <?php $addClass = ''; ?>
<?php else: ?>
    <?php $containerId = "bxr-counter-compare-new"; ?>
<?php endif; ?>
<a href="javascript:void(0)" class="bxr-basket-indicator compare-button-group bxr-font-hover-light bxr-compare-label" id="<?= $containerId ?>" <?= $addClass ?> data-child="bxr-compare-body"
   title="<?= GetMessage("COMPARE_TITLE") ?>">
    <i class="fa fa-bar-chart"></i>
    <span class="bxr-basket-compare-title"><?= GetMessage('COMPARE_STATE_NAME') ?>:</span>
    <?php if (isset($_SESSION["BXR_BASKET_TEMPLATE"]) and (($_SESSION["BXR_BASKET_TEMPLATE"] == "fixed") || ($_SESSION["BXR_BASKET_TEMPLATE"] == "dinamic"))) echo "<br />"; ?><?= !empty($arResult["ITEMS"]) && is_array($arResult["ITEMS"]) ? count($arResult["ITEMS"]) : 0 ?>
</a>


<?php if (!isset($_REQUEST['ajaxbuy']) || $_REQUEST['ajaxbuy'] != "yes"): ?>
<div id="bxr-compare-body" class="basket-body-container" data-group="basket-group">
    <?php endif; ?>

    <div id="bxr-compare-jdata" style="display: none"><?= json_encode($arResult["JSON"]) ?></div>

    <?php if (!empty($arResult["ITEMS"]) && is_array($arResult["ITEMS"])) { ?>

        <div class="basket-body-title">
					<span class="basket-body-title-h">
						<!--<i class="fa fa-bar-chart"></i>-->  <?= GetMessage('COMPARE_TITLE') ?></span>
            <?php if (isset($_SESSION["BXR_BASKET_TEMPLATE"]) and (($_SESSION["BXR_BASKET_TEMPLATE"] == "fixed") || ($_SESSION["BXR_BASKET_TEMPLATE"] == "dinamic"))): ?>
                <div class="pull-right">
                    <button class="btn btn-default bxr-close-basket bxr-corns">
                        <span class="fa fa-power-off" aria-hidden="true"></span>
                        <?= GetMessage('BASKET_CLOSE') ?></button>
                </div>
            <?php else: ?>
                <div class="pull-right">
                    <a href="<?= $arParams["COMPARE_URL"] ?>" class="bxr-color-button">
                        <span class="fa fa-bar-chart" aria-hidden="true"></span>
                        <?= GetMessage('COMPARE_STATE_NAME') ?></a>
                </div>
            <?php endif; ?>

        </div>
        <div class="basket-body-table">
            <table width="100%">

                <?php foreach ($arResult["ITEMS"] as $arBasketItem):

                    $img = $arResult["DATA"][$arBasketItem["ID"]]["DETAIL_PICTURE"]["SRC"];

                    $img = (strlen($img) > 0)
                        ? '<a href="' . $arBasketItem["DETAIL_PAGE_URL"] . '"
													   style="background: url(' . $img . ') no-repeat center center;
													   background-size: contain;
													   " title="' . $arBasketItem["NAME"] . '" alt="' . $arBasketItem["NAME"] . '"></a>'
                        : "&nbsp;";
                    ?>
                    <tr>
                        <td class="basket-image first">
                            <?= $img ?>
                        </td>
                        <td class="basket-name xs-hide"><a href="<?= $arBasketItem["DETAIL_PAGE_URL"] ?>" class="bxr-font-hover-light"><?= $arBasketItem["NAME"] ?></a></td>
                        <td class="basket-action last">
                            <button id="button-delay-<?= $arBasketItem["ID"] ?>" class="compare-button-delete" value="" data-item="<?= $arBasketItem["ID"] ?>" title="<?= GetMessage("SALE_DELETE") ?>">
                                <span class="fa fa-close" aria-hidden="true"></span>
                            </button>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

        </div>
        <div class="basket-body-title">
            <?php if (isset($_SESSION["BXR_BASKET_TEMPLATE"]) and (($_SESSION["BXR_BASKET_TEMPLATE"] == "fixed") || ($_SESSION["BXR_BASKET_TEMPLATE"] == "dinamic"))): ?>
                <div class="pull-right">
                    <a href="<?= $arParams["COMPARE_URL"] ?>" class="bxr-color-button">
                        <span class="fa fa-bar-chart" aria-hidden="true"></span>
                        <?= GetMessage('COMPARE_STATE_NAME') ?></a>
                </div>
            <?php else: ?>
                <div class="pull-right">
                    <button class="btn btn-default bxr-close-basket bxr-corns">
                        <span class="fa fa-power-off" aria-hidden="true"></span>
                        <?= GetMessage('BASKET_CLOSE') ?></button>
                </div>
            <?php endif; ?>

        </div>

    <?php } else { ?>
        <p class="bxr-helper bg-info">
            <?= GetMessage('COMPARE_EMPTY') ?>
        </p>
    <?php } ?>
    <div class="icon-close"></div>
    <?php if (isset($_REQUEST['ajaxbuy']) && $_REQUEST['ajaxbuy'] == "yes"): ?>

        <?php die(); ?>
    <?php endif; ?>

</div>


<script>

    $(document).ready(function () {

        BXRCompare = window.BXReady.Market.Compare;
        BXRCompare.ajaxURL = '<?=SITE_DIR?>ajax/compare.php';
        BXRCompare.messList = '<?=GetMessage('CT_BCE_CATALOG_COMPARE_LIST')?>';
        BXRCompare.mess = '<?=GetMessage('CT_BCE_CATALOG_COMPARE')?>';
        BXRCompare.iblockID = '<?=$arParams['IBLOCK_ID']?>';
        BXRCompare.init();

    });

    //        window.onload = function()
    //        {
    //            window.BXReady.Market.Compare.reload();
    //        }
</script>