<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
if (empty($arResult['SECTIONS'])) return;
?>
<section class="mt-4" id="subsections">
    <div class="heading">
        <div class="heading__item">
            <h3 class="heading__title">Смотрите также</h3>
        </div>
        <div class="heading__item"><a class="heading__link"></a></div>
    </div>
    <div class="products-slider-wrapper wide-content">
        <div class="products-slider products-slider--simple">
            <?foreach ($arResult['SECTIONS'] as $section) {
                $name = $section['IPROP']['SECTION_PAGE_TITLE'] ? $section['IPROP']['SECTION_PAGE_TITLE'] : $section['NAME'];
                ?>
            <div class="product-card product-card--simple">
                <div class="product-card__inner">
                    <div class="product-card__pic product-card__row">
                    <a href="<?=$section['SECTION_PAGE_URL']?>">
                    <img class="lozad" data-src="<?=$section['IMG_SRC']?>" src="<?=$section['IMG_SRC']?>" alt="<?=$name?>"  title="<?=$name?>">
                    </a>
                    </div>
                    <div class="product-card__details product-card__row">
                    <p class="product-card__title"><a href="<?=$section['SECTION_PAGE_URL']?>"><?=$name?></a></p>
                    </div>
                </div>
            </div>
            <?}?>
        </div>
    </div>
</section>
