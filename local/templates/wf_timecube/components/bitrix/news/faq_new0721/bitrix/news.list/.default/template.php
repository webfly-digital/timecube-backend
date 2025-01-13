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

<div class="blog-page">
    <section>
        <div class="blog-nav">
            <?php foreach ($arResult["SECTION"]["LIST_SECTION"] as $sect): ?>
                <a href="/blog/<?=$sect["CODE"]?>/" class="item">
                    <div class="title"><?= $sect["NAME"] ?></div>
                    <div class="count"><?= $sect["ELEMENT_CNT"] ?> статей</div>
                </a>
            <?php endforeach ?>
        </div>
    </section>
    <section>
        <div class="cards-grid">
            <?php foreach ($arResult["ITEMS"] as $item):?>
                <? if (!empty($item["DATE_ACTIVE_FROM"]))
                    $data = FormatDate('j F Y', MakeTimeStamp($item["DATE_ACTIVE_FROM"]));
                else
                    $data = FormatDate('j F Y', MakeTimeStamp($item["DATE_CREATE"]));
                ?>
                <a href="<?= $item["DETAIL_PAGE_URL"] ?>" class="item">
                    <div class="img">
                        <img src="<?= CFile::GetPath($item["PREVIEW_PICTURE"]['ID']) ?>"  alt="<?= $item['NAME'] ?>" title="<?= $item['NAME'] ?>">
                    </div>
                    <div class="info">
                        <span><?= $data ?></span>
                        <div class="title"> <?= $item["NAME"] ?></div>
                        <div class="link">Читать далее</div>
                    </div>
                </a>
            <?php endforeach ?>
        </div>
    </section>
</div>
<? if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<? endif;?>

