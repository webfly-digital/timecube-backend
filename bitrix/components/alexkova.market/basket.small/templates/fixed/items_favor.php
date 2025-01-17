<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="basket-body-title">
    <span class="basket-body-title-h"><?=GetMessage('FAVOR_TITLE')?></span>
    <div class="pull-right">
        <button class="btn btn-default bxr-close-basket bxr-corns">
            <span class="fa fa-power-off" aria-hidden="true"></span>
            <?=GetMessage('BASKET_CLOSE')?>
        </button>
    </div>
    <div class="clearfix"></div>
</div>
<?if (is_array($arResult["FAVOR_ITEMS"]) && count($arResult["FAVOR_ITEMS"])>0){?>
    <div class="basket-body-table">
        <table width="100%">
            <tr>
                <th class="first" width="54px">&nbsp;</th>
                <th><?=GetMessage('BASKET_TD_NAME')?></th>
                <th class="last" width="120px">&nbsp;</th>
            </tr>
            <?foreach($arResult["FAVOR_ITEMS"] as $arFavorItem):

                    $img = $arFavorItem["PICTURE"];
                    $img = (strlen($img)>0)
                            ? '<a href="'.$arFavorItem["URL"].'"
                                            style="background: url('.$img.') no-repeat center center;
                                            background-size: contain;
                                            " title="'.$arBasketItem["NAME"].'" alt="'.$arBasketItem["NAME"].'"></a>'
                            : "&nbsp;";
                    ?>
                    <tr>
                            <td class="basket-image first" width="54px">
                                    <?=$img?>
                            </td>
                            <td class="basket-name xs-hide"><a href="<?=$arFavorItem["URL"]?>" class="bxr-font-hover-light"><?=$arFavorItem["NAME"]?></a></td>
                            <td class="basket-action-row last" width="120px">
<!--                                <form class="bxr-basket-action bxr-basket-group bxr-currnet-torg" action="">
                                    <input type="hidden" name="quantity" value="1" data-item="</?=$arFavorItem["ID"]?>" tabindex="0">
                                    <button class="bxr-basket-add" tabindex="0">
                                            <span class="fa fa-shopping-cart"></span>
                                    </button>
                                    <input class="bxr-basket-item-id" type="hidden" name="item" value="</?=$arFavorItem["ID"]?>" tabindex="0">
                                    <input type="hidden" name="action" value="add" tabindex="0">
                                </form>-->
                                <form class="bxr-basket-action bxr-basket-group" action="">
                                    <button class="bxr-basket-favor-delete" data-item="<?=$arFavorItem["ID"]?>" tabindex="0" title="<?=GetMessage("SALE_DELETE")?>">
                                        <span class="fa fa-close"></span>
                                    </button>
                                    <input type="hidden" name="item" value="<?=$arFavorItem["ID"]?>" tabindex="0">
                                    <input type="hidden" name="action" value="favor" tabindex="0">
                                    <input type="hidden" name="favor" value="yes" tabindex="0">
                                </form>
                            </td>
                    </tr>
            <?endforeach;?>
        </table>
    </div>
<?}else{?>
    <p class="bxr-helper bg-info">
        <?=GetMessage('FAVOR_EMPTY')?>
    </p>
<?}?>