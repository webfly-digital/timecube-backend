<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 */
?>
<script id="basket-total-template" type="text/html">
    <div class="basket-checkout-container" data-entity="basket-checkout-aligner">
        <?
        if ($arParams['HIDE_COUPON'] !== 'Y') {
            ?>
            <div class="basket-coupon-section" style="flex:5;">
                <div class="basket-coupon-block-field" style="background-color:inherit;">
                    <div class="form">
                        <div class="form-group" style="margin-left: 0 ; margin-right:0; ">
                            <input type="text" class="form-control input-normally holder" id="" data-entity="basket-coupon-input" placeholder="№ карты или промокод" style="width: 55%; background-color: #EEEEEE; padding-left: 7px; padding-right:   7px">
                            <span class="btn btn-lg btn-secondary" style="position: inherit; margin-left: 3px;">
                                Применить
                            </span>
                        </div>
                    </div>
                </div>
            </div>                           
            <?
        }
        ?>
        <div class="basket-checkout-section pl-lg-4">
            <div class="basket-checkout-section-inner<?= (($arParams['HIDE_COUPON'] == 'Y') ? ' justify-content-between' : '') ?>">
                <div class="basket-checkout-block basket-checkout-block-total">
                    <div class="basket-checkout-block-total-inner">
                        <p class="caption-gray"><?= Loc::getMessage('SBB_TOTAL') ?>:</p>
                        <div class="basket-checkout-block-total-description">
                            {{#WEIGHT_FORMATED}}
                            <?= Loc::getMessage('SBB_WEIGHT') ?>: {{{WEIGHT_FORMATED}}}
                            {{#SHOW_VAT}}<br>{{/SHOW_VAT}}
                            {{/WEIGHT_FORMATED}}
                            {{#SHOW_VAT}}
                            <?= Loc::getMessage('SBB_VAT') ?>: {{{VAT_SUM_FORMATED}}}
                            {{/SHOW_VAT}}
                        </div>
                    </div>
                </div>

                <div class="basket-checkout-block basket-checkout-block-total-price">
                    <div class="basket-checkout-block-total-price-inner">
                        {{#DISCOUNT_PRICE_FORMATED}}
                        <div class="basket-coupon-block-total-price-old">
                            {{{PRICE_WITHOUT_DISCOUNT_FORMATED}}}
                        </div>
                        {{/DISCOUNT_PRICE_FORMATED}}

                        <div class="basket-coupon-block-total-price-current" data-entity="basket-total-price">
                            {{{PRICE_FORMATED}}}
                        </div>

                        {{#DISCOUNT_PRICE_FORMATED}}
                        <div class="basket-coupon-block-total-price-difference">
                            <?= Loc::getMessage('SBB_BASKET_ITEM_ECONOMY') ?>
                            <span style="white-space: nowrap;">{{{DISCOUNT_PRICE_FORMATED}}}</span>
                        </div>
                        {{/DISCOUNT_PRICE_FORMATED}}
                    </div>
                </div>

                <div class="basket-checkout-block basket-checkout-block-btn">
                    <button class="btn btn-lg btn-primary basket-btn-checkout{{#DISABLE_CHECKOUT}} disabled{{/DISABLE_CHECKOUT}}"
                            data-entity="basket-checkout-button">
                                <?= Loc::getMessage('SBB_ORDER') ?>
                    </button>
                </div>
            </div>
        </div>

        <?
        if ($arParams['HIDE_COUPON'] !== 'Y') {
            ?>
            <div class="basket-coupon-alert-section">
                <div class="basket-coupon-alert-inner">
                    {{#COUPON_LIST}}
                    <div class="basket-coupon-alert text-{{CLASS}}">
                        <span class="basket-coupon-text">  
                            <strong>{{COUPON}}</strong> - <?= Loc::getMessage('SBB_COUPON') ?> {{JS_CHECK_CODE}}
                            {{#DISCOUNT_NAME}}({{DISCOUNT_NAME}}){{/DISCOUNT_NAME}}
                        </span>
                        <span class="close-link" data-entity="basket-coupon-delete" data-coupon="{{COUPON}}">
                            <span class="svg-icon icon-close"></span>
                        </span>
                    </div>
                    {{/COUPON_LIST}}
                </div>
            </div>
            <?
        }
        ?>
    </div>
</script>