<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();/**
 * @var array $arResult
 * @var array $arParams
 */
?>
<?php if (!empty($arResult["BASKET_ITEMS"]["DELAY"]) && is_array($arResult["BASKET_ITEMS"]["DELAY"])) { ?>
    <div class="basket-body-table">
        <?php foreach($arResult["BASKET_ITEMS"]["DELAY"] as $arBasketItem):

                $img = $arBasketItem["PICTURE"];
                $img = (strlen($img)>0)
                        ? '<a href="'.$arBasketItem["URL"].'"
                                        style="background: url('.$img.') no-repeat center center;
                                        background-size: contain;
                                        " title="'.$arBasketItem["NAME"].'" alt="'.$arBasketItem["NAME"].'"></a>'
                        : "&nbsp;";
                ?>
                <div class="basket-body-table-row">
                        <table width="100%" class="bxr-table-row-action ">
                                <tr>
                                        <td class="basket-image first">
                                                <?=$img?>
                                        </td>
                                        <td class="basket-name xs-hide">
                                                <a href="<?=$arBasketItem["URL"]?>" class="bxr-font-hover-light"><?=$arBasketItem["NAME"]?></a>
                                            <?php foreach ($arBasketItem["PROPS"] as $prop) {?>
                                                    <div class="bxr-bsmall-prop"><?=$prop["NAME"]?>: <?=$prop["VALUE"]?></div>
                                            <?php }?>
                                                <b class="basket-price"><?=$arBasketItem["FORMAT_PRICE"]?></b>
                                        </td>
                                        <td class="basket-action last">
                                                <button id="button-delay-<?=$arBasketItem["ID"]?>" class="icon-button-cart" value="" data-item="<?=$arBasketItem["ID"]?>" title="<?=GetMessage("SALE_ADD_TO_BASKET")?>">
                                                        <span class="fa fa-shopping-cart" aria-hidden="true"></span>
                                                </button>
                                                <button id="button-delay-<?=$arBasketItem["ID"]?>" class="icon-button-delete" value="" data-item="<?=$arBasketItem["ID"]?>" title="<?=GetMessage("SALE_DELETE")?>">
                                                        <span class="fa fa-close" aria-hidden="true"></span>
                                                </button>

                                        </td>
                                </tr>
                        </table>
                </div>
        <?php endforeach;?>
    </div>

    <div class="basket-body-title">
        <div class="pull-right">
            <button class="btn btn-default bxr-close-basket-mobile bxr-corns">
                <span class="fa fa-power-off" aria-hidden="true"></span>
                <?=GetMessage('BASKET_CLOSE')?>
            </button>
        </div>
    </div>

<?php }else{?>
    <p class="bxr-helper bg-info">
        <?=GetMessage('BASKET_DELAY_EMPTY')?>
    </p>
<?php }?>
<div class="icon-close"></div>