<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
?>

<div class="top">
    <?php if (!empty($arResult['PREVIEW_TEXT'])): ?>
        <div class="inner">
            <div class="left">
                <div class="subtitle"> <?= $arResult['PREVIEW_TEXT'] ?> </div>
            </div>
            <div class="right">
            </div>
        </div>
    <? endif; ?>
</div>
<div class="content">
    <div class="left">
        <?php if (!empty($arResult['DETAIL_PICTURE']['SRC'])): ?>
            <div class="img-main">
                <img src="<?= $arResult['DETAIL_PICTURE']['SRC'] ?>" alt="<?= $arResult['NAME'] ?>" title="<?= $arResult['NAME'] ?>">
            </div>
        <?php endif ?>
        <?= $arResult['~DETAIL_TEXT'] ?>
        <? if (!empty($arResult["DATE_ACTIVE_FROM"])) {
            $data = FormatDate('j F Y', MakeTimeStamp($arResult["DATE_ACTIVE_FROM"]));
            $dataOrg = FormatDate('Y-m-d', MakeTimeStamp($arResult["DATE_ACTIVE_FROM"]));
        } else {
            $data = FormatDate('j F Y', MakeTimeStamp($arResult["DATE_CREATE"]));
            $dataOrg = FormatDate('Y-m-d', MakeTimeStamp($arResult["DATE_CREATE"]));
        }
        ?>
        <div class="signature mt-3 mb-3">
            <div>
                <?= $data ?>
            </div>
            <div> Материал подготовлен компанией TimeCube</div>
        </div>
    </div>
    <div style="display:none;" itemscope itemtype="http://schema.org/Article">
        <meta property="og:url" content="<?= SITE_SERVER_NAME . $arResult['DETAIL_PAGE_URL'] ?>"/>
        <meta property="og:locale" content="ru_RU"/>
        <meta property="og:title" content="<?= $arResult["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"] ?>"/>
        <meta property="og:description" content="<?= $arResult["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"] ?>"/>
        <meta property="og:image" content="<?= SITE_SERVER_NAME . $arResult['DETAIL_PICTURE']['SRC'] ?>"/>
        <meta property="og:type" content="website"/>
        <meta property="og:locale" content="ru_RU"/>
        <meta property="og:site_name" content="timecube.ru"/>

        <meta itemprop="headline" content="<?= $arResult["NAME"] ?>">
        <meta itemprop="image" content="https://<?= SITE_SERVER_NAME . $arResult['DETAIL_PICTURE']['SRC'] ?>"/>
        <meta itemprop="description" content="<?= $arResult["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"] ?>"/>
        <time itemprop="datePublished" datetime="<?= $dataOrg ?>"></time>
        <meta itemprop="author" content="TimeCube"/>
        <div itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
            <div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
                <img itemprop="url" src="https://<?= SITE_SERVER_NAME ?>/assets/img/logo-timecube.svg"/>
            </div>
            <meta itemprop="name" content="Timecube.ru">
            <meta itemprop="url" content="https://<?= SITE_SERVER_NAME ?>">
        </div>
        <meta itemprop="dateModified" content="<?= FormatDate('Y-m-d', MakeTimeStamp($arResult["TIMESTAMP_X"])) ?>"/>
        <meta itemscope itemprop="mainEntityOfPage" itemType="https://schema.org/WebPage"
              itemid="https://<?= SITE_SERVER_NAME . $arResult['DETAIL_PAGE_URL'] ?>"/>
    </div>
</div>




