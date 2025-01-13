<div class="product-detail__sidebar-box">
    <h4 class="h4">Преимущества покупки</h4>
    <!--Преимущества покупки начало-->
    <ul class="list-unstyled">
        <? if ($arResult['PROPERTIES']['IS_DELIVERY_FREE']['VALUE'] == 'Да') { ?>
            <li class="mb-3">
                <div class="product-label-ext">
                    <a target="_blank" rel="nofollow" href="/actions/besplatnaya-dostavka-po-rf/" class="product-label product-label--gray">
                        <span class="product-label__icon svg-icon icon-delivery-truck"></span>
                        <span class="product-label__title">Бесплатно</span>
                        <span class="product-label__top">по России</span>
                    </a>
                    <div class="product-label-ext__caption">
                        <p><a target="_blank" rel="nofollow" href="/actions/besplatnaya-dostavka-po-rf/">Бесплатная доставка в регионы</a></p>
                    </div>
                </div>
            </li>
        <? } else if ($arResult['PROPERTIES']['IS_DELIVERY_FREE_MOS']['VALUE'] == 'Да') { ?>
            <li class="mb-3">
                <div class="product-label-ext">
                    <a target="_blank" rel="nofollow" href="/actions/besplatnaya-dostavka-po-msk/" class="product-label product-label--gray">
                        <span class="product-label__icon svg-icon icon-delivery-truck"></span>
                        <span class="product-label__title">Free</span>
                    </a>
                    <div class="product-label-ext__caption">
                        <p><a target="_blank" rel="nofollow" href="/actions/besplatnaya-dostavka-po-msk/">Бесплатная доставка по Москве и СПБ</a></p>
                    </div>
                </div>
            </li>
        <? } ?>
        <? if ($arResult['PROPERTIES']['IS_PACK_FREE']['VALUE'] == 'Да') { ?>
            <li class="mb-3">
                <div class="product-label-ext">
                    <a target="_blank" rel="nofollow" href="/actions/upakuy-i-podari/" class="product-label product-label--violet">
                        <span class="product-label__title">Упаковка бесплатно</span>
                    </a>
                    <div class="product-label-ext__caption">
                        <p><a target="_blank" rel="nofollow" href="/actions/upakuy-i-podari/">Подарочная упаковка бесплатно</a></p>
                    </div>
                </div>
            </li>
        <? } ?>
        <? if ($arResult['actionPenEnabled'] && $arResult['PROPERTIES']['PEN_IN_CASE']['VALUE'] == 'Да') { ?>
            <li class="mb-3">
                <div class="product-label-ext">
                    <a target="_blank" rel="nofollow" href="/actions/ruchka-v-podarok/" class="product-label product-label--gold"
                           title="Эксклюзивная ручка в подарок">
                        <span class="product-label__icon svg-icon icon-giftcard"></span>
                        <span class="product-label__title">Подарок</span>
                    </a>
                    <div class="product-label-ext__caption">
                        <p><a target="_blank" rel="nofollow" href="/actions/ruchka-v-podarok/">Эксклюзивная ручка в подарок</a></p>
                    </div>
                </div>
            </li>
        <? } ?>
        <? if ($arResult['actionMoetEnabled'] && $arResult['PROPERTIES']['MOET_IN_CASE']['VALUE'] == 'Да') { ?>
            <li class="mb-3">
                <div class="product-label-ext">
                    <a target="_blank" rel="nofollow" href="/actions/shampanskoe-mo-t-chandon-imperial-v-podarok/" class="product-label product-label--gold"
                           title="Шампанское Moet в подарок">
                        <span class="product-label__icon svg-icon icon-champagne"></span>
                        <span class="product-label__title">Подарок</span>
                    </a>
                    <div class="product-label-ext__caption">
                        <p><a target="_blank" rel="nofollow" href="/actions/shampanskoe-mo-t-chandon-imperial-v-podarok/">Шампанское Moet в подарок</a></p>
                    </div>
                </div>
            </li>
        <? } ?>
        <?
        if ($arResult['LABEL'] && !empty($arResult['LABEL_ARRAY_VALUE'])) {
            foreach ($arResult['LABEL_ARRAY_VALUE'] as $code => $value) {
                ?>
                <li class="mb-3 <?= (!isset($arParams['LABEL_PROP_MOBILE'][$code]) ? 'hidden-xs' : '') ?>">
                    <div class="product-label-ext">
                        <label class="product-label product-label--gray">
                            <span class="product-label__title"><?= $code ?></span>
                        </label>
                        <div class="product-label-ext__caption">
                            <p><?= $code . $value ?></p>
                        </div>
                    </div>
                </li>
                <?
            }
        }
        ?>
        <li class="mb-3">
            <div class="product-label-ext">
                <label class="product-label product-label--orange"><span
                        class="product-label__top">2й товар</span><span
                        class="product-label__digit">10%</span><span
                        class="product-label__title">Скидка</span>
                </label>
                <div class="product-label-ext__caption">
                    <p>Скидка 10% при заказе более 1 товара</p>
                </div>
            </div>
        </li>
        <li class="mb-3">
            <div class="product-label-ext">
                <a target="_blank" rel="nofollow" href="/actions/skidka-na-den-rozhdenie/" class="product-label product-label--yellow"
                       title="Скидка 10% в День рождения">
                    <span class="product-label__top">в День Рождения</span>
                    <span class="product-label__digit">10%</span>
                    <span class="product-label__title">скидка</span>
                </a>
                <div class="product-label-ext__caption">
                    <p><a target="_blank" rel="nofollow" href="/actions/skidka-na-den-rozhdenie/">В День Рождения скидка 10%</a></p>
                </div>
            </div>
        </li>
    </ul>
    <!--преимущества покупки конец-->
</div>