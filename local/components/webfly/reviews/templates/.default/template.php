<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 *
 */

//\Bitrix\Main\Diag\Debug::dump($arParams);
//\Bitrix\Main\Diag\Debug::dump($arResult['USER_HAS_REVIEW']);
global $USER;
CJSCore::Init(["popup"]);

$obName = 'wf_' . preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($arParams['ELEMENT_ID']));
?>

<section class="py-4" data-entity="parent-container" id="<?= $obName ?>">
    <? if (!empty($arParams['ELEMENT_ID'])): ?>
    <div class="heading" data-entity="header">
        <div class="heading__item">
            <h3 class="heading__title">Отзывы</h3>
        </div>
    </div>
    <div class="row justify-content-between">
        <div class="col-12 col-md-9 col-xl-8">
            <? endif; ?>
            <!--Reviews list begin-->
            <div class="reviews-list">
                <? foreach ($arResult['ITEMS'] as $item) { ?>
                    <div class="review">
                        <div class="review__header">
                            <div class="review__header-col">
                                <span class="review__name"><?= $item['NAME'] ?></span>
                                <span class="review__date"><?= $item['DATE_CREATE'] ?></span>
                            </div>
                            <div class="review__header-col">
                                <div class="rating" data-product="<?= $item['ID'] ?>">
                                    <ul class="rating__stars rating__stars--<?= $item['RATE']['VALUE'] ?>">
                                        <li class="svg-icon icon-star" data-star="1"></li>
                                        <li class="svg-icon icon-star" data-star="2"></li>
                                        <li class="svg-icon icon-star" data-star="3"></li>
                                        <li class="svg-icon icon-star" data-star="4"></li>
                                        <li class="svg-icon icon-star" data-star="5"></li>
                                    </ul>
                                    <span class="rating__caption">Отлично</span>
                                </div>
                            </div>
                            <? if ($USER->isAdmin()) { ?>
                                <div class="review__header-col" style="background-color: lightyellow">
                                    <small><?= $item['ACTIVE'] == 'Y' ? 'активен' : 'не активен' ?>
                                        <a href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=<?= $arParams['REVIEWS_IBLOCK_ID'] ?>&type=<?= $arParams['REVIEWS_IBLOCK_TYPE'] ?>&ID=<?= $item['ID'] ?>&lang=ru">Изменить</a>
                                        (admin)
                                    </small>
                                </div>
                            <? } ?>
                        </div>
                        <? if (!empty($item['PRODUCT'])): ?>
                            <p class="review__link"><b>Товар:</b> <a href="<?= $item['PRODUCT']["DETAIL_PAGE_URL"] ?>"><?= $item['PRODUCT']["NAME"] ?></a></p>
                        <? endif; ?>
                        <div class="review__body">
                            <?= nl2br(trim($item['~PREVIEW_TEXT'])) ?>
                        </div>
                        <div class="review__footer">
                        </div>
                    </div>
                <? } ?>
                <?= $arResult["NAV_STRING"] ?>
            </div>
            <!--Reviews list end-->
            <? if (!empty($arParams['ELEMENT_ID'])): ?>
        </div>
    </div>

<? if (!$USER->isAuthorized()) { ?>
    <div class="reviews-actions">
        <a href="#auth" rel="nofollow" class="mfp-inline-link btn btn-md btn-gray">Авторизуйтесь, чтобы написать
            отзыв</a>
    </div>
<? } ?>
<? endif; ?>
    <script>
        <?
        if ($USER->isAdmin()) echo 'window.BXDEBUG = true;';

        $signer = new \Bitrix\Main\Security\Sign\Signer;
        $signedTemplate = $signer->sign($templateName, 'webfly.reviews');
        $signedParams = $signer->sign(base64_encode(serialize($arResult['ORIGINAL_PARAMETERS'])), 'webfly.reviews');
        ?>
        var <?=$obName?> = new WFReviewsComponent('<?=$obName?>', '<?=$signedTemplate?>', '<?=$signedParams?>');
    </script>
    <!--  trick for ajax reload. Bitrix will replace url of this link to automate ajax reload all component  -->
    <a data-entity="a-reload" rel="nofollow" style="display: none" href="<?= $APPLICATION->GetCurPage(false) ?>"></a>
</section>