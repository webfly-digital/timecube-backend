<?
$labelsCount = 0;
if ($item['PROPERTIES']['IS_PACK_FREE']['VALUE'] == 'Да') $labelsCount++;
if ($item['PROPERTIES']['PEN_IN_CASE']['VALUE'] == 'Да') $labelsCount++;
if ($item['PROPERTIES']['IS_DELIVERY_FREE']['VALUE'] == 'Да' ||
    $item['PROPERTIES']['IS_DELIVERY_FREE_MOS']['VALUE'] == 'Да') $labelsCount++;

if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' && $price['PERCENT'] > 0) $labelsCount++;
$labelsClass = $labelsCount < 3 ?: 'product-labels--tripple';
?>
<div class="product-card__labels product-labels <?= $labelsClass ?>" id="<?= $itemIds['STICKER_ID'] ?>">
    <? if ($item['PROPERTIES']['IS_PACK_FREE']['VALUE'] == 'Да') { ?>
        <label class="product-label product-label--freepack" title="Подарочная коробка бесплатно">
            <a rel="nofollow" target="_blank" href="/actions/upakuy-i-podari/">
                <span class="product-label__title">Упаковка бесплатно</span>
            </a>
        </label>
    <? } ?>
    <? if ($arResult['actionPenEnabled'] && $item['PROPERTIES']['PEN_IN_CASE']['VALUE'] == 'Да') { ?>
        <label class="product-label product-label--gift" title="Ручка в футляре в подарок">
            <a rel="nofollow" target="_blank" href="/actions/ruchka-v-podarok/">
                <span class="product-label__icon svg-icon icon-giftcard"></span>
                <span class="product-label__title">Ручка в подарок</span>
            </a>
        </label>
    <? } ?>

    <? if ($item['PROPERTIES']['IS_DELIVERY_FREE']['VALUE'] == 'Да') { ?>
        <label class="product-label product-label--freedelivery" title="Бесплатная доставка по России">
            <a rel="nofollow" target="_blank" href="/actions/besplatnaya-dostavka-po-rf/">
                <span class="product-label__icon svg-icon icon-delivery-truck"></span>
                <span class="product-label__title">Бесплатно</span>
                <span class="product-label__top">по России</span>
            </a>
        </label>
    <? } ?>
    <? if ($item['PROPERTIES']['IS_DELIVERY_FREE_MOS']['VALUE'] == 'Да' && !($item['PROPERTIES']['IS_DELIVERY_FREE']['VALUE'] == 'Да')) { ?>
        <label class="product-label product-label--freedelivery" title="Бесплатная доставка по&nbsp;Мск и&nbsp;CПб">
            <a rel="nofollow" target="_blank" href="/actions/besplatnaya-dostavka-po-msk/">
                <span class="product-label__icon svg-icon icon-delivery-truck"></span>
                <span class="product-label__title">Бесплатно</span>
                <span class="product-label__top">по&nbsp;Мск и&nbsp;CПб</span>
            </a>
        </label>
    <? } ?>
    <?
    //алгоритм включения Чёрной пятницы/Новогодней распродажи, чтобы перекрашивались цены в другие цвета
    global $USER;
    $date = new DateTime();
    $dateStartSale = new DateTime(START_SALE);
    $dateEndSale = new DateTime(END_SALE);
    $checkSale = false;
    if (true) {
        if (($dateStartSale <= $date && $date <= $dateEndSale)) {
            $checkSale = true;
        }
    }
//    $checkSale =  $USER->IsAdmin() ? true: false;
    if ($checkSale == true) {
        ?>
        <label class="product-label product-label--sale <?=NAME_SALE?>"
            <?= ($price['PERCENT'] > 0 ? '' : 'style="display: none;"') ?>>
            <span class="product-label__title"><?= LABEL_SALE_TOP ?></span>
            <span class="product-label__digit" id="<?= $itemIds['DSC_PERC'] ?>"><?= -$price['PERCENT'] ?>%</span>
            <span class="product-label__title"><?= LABEL_SALE_BOTTOM ?></span>
        </label>
    <? } else {
        ?>
        <? if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y') { ?>
            <label class="product-label product-label--sale"
                <?= ($price['PERCENT'] > 0 ? '' : 'style="display: none;"') ?>>
                <span class="product-label__digit" id="<?= $itemIds['DSC_PERC'] ?>"><?= -$price['PERCENT'] ?>%</span>
                <span class="product-label__title">Скидка</span>
            </label>
            <?
        }
    } ?>
</div>
