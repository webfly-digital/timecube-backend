<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)    die();
$this->setFrameMode(true);
?>
    <div class="blog-page">
        <section>
            <div class="blog-nav">
                <?php foreach ($arResult["ITEMS"] as $key => $sect): ?>
                    <a href="#<?= $key ?>" class="item">
                        <div class="title"><?= $sect["SECTION"]["NAME"] ?></div>
                        <div class="count"><?= $sect["SECTION"]["ELEMENT_CNT"] ?> статей</div>
                    </a>
                <?php endforeach ?>
            </div>
        </section>

        <?php foreach ($arResult["ITEMS"] as $key => $sect): ?>
            <section>
                <h2 id="<?= $key ?>"><?= $sect["SECTION"]["NAME"] ?></h2>
                <div class="cards-grid">
                    <? foreach ($sect["ITEMS"] as $item): ?>
                        <? if (!empty($item["DATE_ACTIVE_FROM"]))
                            $data = FormatDate('j f Y', MakeTimeStamp($item["DATE_ACTIVE_FROM"]));
                        else
                            $data = FormatDate('j f Y', MakeTimeStamp($item["DATE_CREATE"]));
                        ?>
                        <a href="<?= $item["DETAIL_PAGE_URL"] ?>" class="item">
                            <div class="img">
                                <img src="<?= $item["PICTURE"]["src"] ?>" alt="<?= $item["NAME"] ?> ">
                            </div>
                            <div class="info">
                                <span><?= $data ?></span>
                                <div class="title"><?= $item["NAME"] ?> </div>
                                <div class="link">Читать далее</div>
                            </div>
                        </a>
                    <?php endforeach ?>
                    <a href="<?= $sect["SECTION"]["CODE"] ?>/" class="button"> Смотреть еще </a>
                </div>
            </section>

        <?php endforeach ?>
    </div>
<? /*if ($arParams["LINK_PRICE"] == 'Y'): ?>
    <section class="section">
        <div class="container">
            <ul class="nav-main nav-main--compact">
                <? foreach ($arResult['ITEMS'] as $items): ?>
                    <li class="nav-main__item"><a class="nav-main__link plugin-slowscroll"
                                                  href="#sect-prices-<?= $items['SECTION']['ID'] ?>">
                            <span class="nav-main__link-caption"><?= $items['SECTION']['NAME'] ?> </span></a>
                    </li>
                <? endforeach; ?>
            </ul>
        </div>
    </section>
<? else: ?>
    <div class="price-list">
        <? foreach ($arResult['ITEMS'] as $items): ?>
            <section class="section-detail fw-slider-wrapper" id="sect-prices-<?= $items['SECTION']['ID'] ?>">
                <div class="container">
                    <div class="row">
                        <aside class="col-12 col-lg-4">
                            <div class="section-detail__heading">
                                <p class="section-detail__title"><?= $items['SECTION']['NAME'] ?></p>
                            </div>
                            <? if (!empty( $items['SECTION']['DETAIL_PICTURE'] )): ?>
                                <img src='<?= $items['SECTION']['IMG'] ?>' alt='<?= $items['SECTION']['NAME'] ?>'>
                            <? endif; ?>
                        </aside>
                        <section class="col-12 col-lg-8">
                            <div class="price-group">
                                <div class="price-group__prices">

                                    <?
                                    foreach ($items["ITEMS"] as $key => $el):
                                        $el['IS_DISCOUNT'] = !empty($el["PROPERTY_PRICE_DISCOUNT_VALUE"]) && $el["PROPERTY_PRICE_DISCOUNT_VALUE"] < $el["PROPERTY_PRICE_WITHOUT_DISCOUNT_VALUE"];
                                        if ($el['IS_DISCOUNT']) {
                                            $el['DISCOUNT_PERCENT'] = 100 - round((preg_replace('/[^0-9]+/', '', $el["PROPERTY_PRICE_DISCOUNT_VALUE"]) * 100 / preg_replace('/[^0-9]+/', '', $el["PROPERTY_PRICE_WITHOUT_DISCOUNT_VALUE"])), 0);
                                        }

                                        ?>
                                        <div class="price-item">
                                            <div class="price-item__main">
                                                <div class="price-item__content">
                                                    <p class="price-item__title"><?= $el['NAME'] ?></p>
                                                    <? if (!empty($el["PREVIEW_TEXT"])): ?>
                                                        <p class="price-item__subtitle"><?= $el["PREVIEW_TEXT"] ?></p>
                                                    <? endif ?>
                                                    <ul class="price-item__meta">
                                                        <? if (!empty($el["PROPERTY_NUMBER_OF_VIZIT_VALUE"])): ?>
                                                            <li><span class="small-icon-block"><span
                                                                            class="svg-icon icon-user-walk"></span><span
                                                                            class="small-icon-block__caption"><?= $el["PROPERTY_NUMBER_OF_VIZIT_VALUE"] ?></span></span>
                                                            </li>
                                                        <? endif ?>
                                                        <? if (!empty($el["PROPERTY_LASTING_VALUE"])): ?>
                                                            <li><span class="small-icon-block"><span
                                                                            class="svg-icon icon-clock"></span><span
                                                                            class="small-icon-block__caption"><?= $el["PROPERTY_LASTING_VALUE"] ?></span></span>
                                                            </li>
                                                        <? endif ?>
                                                    </ul>
                                                </div>
                                                <div class="price-item__discount">
                                                    <? if ($el['IS_DISCOUNT']): ?>
                                                        <div class="price-item__inner"><span
                                                                    class="percent-label percent-label--lg"><?= $el['DISCOUNT_PERCENT'] ?>
                                                                %</span></div>
                                                    <? endif ?>
                                                </div>

                                                <div class="price-item__prices">
                                                    <div class="price-block price-block--md">
                                                        <? if ($el['IS_DISCOUNT']): ?>
                                                            <p class="price-block__price price-block__price--old"> <?= $el["PROPERTY_PRICE_WITHOUT_DISCOUNT_VALUE"] ?>
                                                                ₽</p>
                                                        <? endif ?>
                                                        <p class="price-block__price"><?= $el['IS_DISCOUNT'] ? $el["PROPERTY_PRICE_DISCOUNT_VALUE"] : $el["PROPERTY_PRICE_WITHOUT_DISCOUNT_VALUE"] ?>
                                                            ₽</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <? if (!empty($el["ADDITIONAL_CONDITIONS"])): ?>
                                                <div class="price-item__additional">
                                                    <p class="price-item__additional-caption"><a class="collapsed"
                                                                                                 href="#price-add-p-1-p-1-<?= $key ?>"
                                                                                                 data-toggle="collapse">Устанавливаются
                                                            дополнительно</a></p>
                                                    <div class="price-item__additional-panel collapse"
                                                         id="price-add-p-1-p-1-<?= $key ?>">
                                                        <div class="price-item__additional-panel-body"></div>
                                                        <ul class="list-unstyled">
                                                            <? foreach ($el["ADDITIONAL_CONDITIONS"] as $conditionId): ?>
                                                                <li>
                                                                    <div class="simple-price-row">
                                                                        <span class="simple-price-row__title"><?= $arResult['CONDITIONS'][$conditionId]['NAME'] ?></span><span
                                                                                class="simple-price-row__price"><?= $arResult['CONDITIONS'][$conditionId]['PROPERTY_PRICE_VALUE'] ?>
                                                                            ₽</span></div>
                                                                </li>
                                                            <? endforeach; ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            <? endif; ?>
                                        </div>
                                    <? endforeach; ?>
                                </div>
                                <? if (!empty($items['SECTION']['UF_QUESTION']) && !empty($items['SECTION']['UF_ASK'])): ?>
                                    <div class="price-group__help">
                                        <p class="price-group__help-question dynamic-inline-popover"
                                           data-toggle="popover"
                                           data-placement="top"><?= $items['SECTION']['UF_QUESTION'] ?></p>
                                        <div class="popover-new-body">
                                            <div class="popover-new-body__title popover-header"><?= $items['SECTION']['UF_HINT_TITLE'] ?></div>
                                            <div class="popover-new-body__content popover-body"><?= $items['SECTION']['UF_ASK'] ?></div>
                                        </div>
                                    </div>
                                <? endif ?>
                            </div>
                        </section>
                    </div>
                </div>
            </section>
        <? endforeach; ?>
    </div>
<? endif*/ ?>