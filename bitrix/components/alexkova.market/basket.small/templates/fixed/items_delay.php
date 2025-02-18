<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 * @var array $arParams
 */
?>

<?php if (!empty($arResult["BASKET_ITEMS"]["DELAY"]) && is_array($arResult["BASKET_ITEMS"]["DELAY"])) { ?>
    <div class="basket-body-table">
        <table width="100%">
            <tr>
                <th class="first">&nbsp;</th>
                <th><?= GetMessage('BASKET_TD_NAME') ?></th>
                <th><?= GetMessage('BASKET_TD_PRICE') ?></th>
                <th class="last">&nbsp;</th>
            </tr>
            <?php foreach ($arResult["BASKET_ITEMS"]["DELAY"] as $arBasketItem):

                $img = $arBasketItem["PICTURE"];
                $img = (strlen($img) > 0)
                    ? '<a href="' . $arBasketItem["URL"] . '"
                                            style="background: url(' . $img . ') no-repeat center center;
                                            background-size: contain;
                                            " title="' . $arBasketItem["NAME"] . '" alt="' . $arBasketItem["NAME"] . '"></a>'
                    : "&nbsp;";
                ?>
                <tr>
                    <td class="basket-image first">
                        <?= $img ?>
                    </td>
                    <td class="basket-name xs-hide">
                        <a href="<?= $arBasketItem["URL"] ?>" class="bxr-font-hover-light"><?= $arBasketItem["NAME"] ?></a>
                        <?php foreach ($arBasketItem["PROPS"] as $prop) { ?>
                            <div class="bxr-bsmall-prop"><?= $prop["NAME"] ?>: <?= $prop["VALUE"] ?></div>
                        <?php } ?>
                    </td>
                    <td class="basket-price bxr-format-price"><?= $arBasketItem["FORMAT_PRICE"] ?></td>
                    <td class="basket-action last">
                        <button id="button-delay-<?= $arBasketItem["ID"] ?>" class="icon-button-cart" value="" data-item="<?= $arBasketItem["ID"] ?>" title="<?= GetMessage("SALE_ADD_TO_BASKET") ?>">
                            <span class="fa fa-shopping-cart" aria-hidden="true"></span>
                        </button>
                        <button id="button-delay-<?= $arBasketItem["ID"] ?>" class="icon-button-delete" value="" data-item="<?= $arBasketItem["ID"] ?>" title="<?= GetMessage("SALE_DELETE") ?>">
                            <span class="fa fa-close" aria-hidden="true"></span>
                        </button>

                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <div class="basket-body-title">
        <div class="pull-right">
            <a href="<?= $arParams["PATH_TO_BASKET"] ?>" class="bxr-color-button">
                <span class="fa fa-shopping-cart" aria-hidden="true"></span>
                <?= GetMessage('SHOW_BASKET') ?>
            </a>
        </div>
    </div>


<?php } else { ?>
    <p class="bxr-helper bg-info">
        <?= GetMessage('BASKET_DELAY_EMPTY') ?>
    </p>
<?php } ?>
<div class="icon-close"></div>