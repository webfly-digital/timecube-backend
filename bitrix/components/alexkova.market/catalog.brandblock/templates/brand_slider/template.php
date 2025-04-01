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
use Alexkova\Bxready\Draw;

$elementDraw = \Alexkova\Bxready\Draw::getInstance($this);
$elementDraw->setCurrentTemplate($this);

if (!CModule::IncludeModule('alexkova.bxready')) return;

$this->setFrameMode(true);

if (empty($arResult["BRAND_BLOCKS"]))
	return;

global $unicumID;
if ($unicumID<=0) {$unicumID = 1;} else {$unicumID++;}

$arParams["UNICUM_ID"] = $unicumID;

$colToElem = array(
	"LG" => 5,
	"MD" => 4,
	"SM" => 3,
	"XS" => 2
);

?>
<div class="container">
	<div id="c<?=intval($_REQUEST["ID"])?>" class="row bxr-list bxr-brands-list" data-slider="<?=$unicumID?>" >

	<div id="sl_<?=$unicumID?>">

<?
foreach ($arResult["BRAND_BLOCKS"] as $cell => $arItem):

	$arItem["PREVIEW_PICTURE"] = $arItem["PICT"];
	$arItem["DETAIL_PAGE_URL"] = strlen($arItem["LINK"])>0 ? SITE_DIR.ltrim($arItem["LINK"], '/'): '';
	$arParams["BXREADY_ELEMENT_DRAW"] = 'system#brand.list.v1';
        if($arItem["ACTIVE"]){
	?>
	<div class="t_<?=$unicumID?> col-lg-<?=$colToElem["LG"]?> col-md-<?=$colToElem["MD"]?> col-sm-<?=$colToElem["SM"]?> col-xs-<?=$colToElem["XS"]?>">
		<?
		$elementDraw = Draw::getInstance($this);
		$elementDraw->showElement($arParams["BXREADY_ELEMENT_DRAW"], $arItem, $arParams);
		?>
	</div>
        <?}?>
<? endforeach; ?>
	</div>
	</div>
</div>
	<script>
		 function isTouchDevice() {
		 try {
		 document.createEvent('TouchEvent');
		 return true;
		 }
		 catch(e) {
		 return false;
		 }
		 }
		<?if ($arParams["HIDE_SLIDER_ARROWS"] == "Y" || !isset($arParams["HIDE_SLIDER_ARROWS"])) {?>
		 if (!isTouchDevice()) {
		 prevBtn = '<button type="button" class="bxr-color-button slick-prev hidden-arrow"></button>';
		 nextBtn = '<button type="button" class="bxr-color-button slick-next hidden-arrow"></button>';
		 }
		<?} else {?>
		 if (!isTouchDevice()) {
		 prevBtn = '<button type="button" class="bxr-color-button slick-prev"></button>';
		 nextBtn = '<button type="button" class="bxr-color-button slick-next"></button>';
		 }
		<?}?>
			<?if ($arParams["HIDE_MOBILE_SLIDER_ARROWS"] == "Y") {?>
		 if (isTouchDevice()) {
		 prevBtn = '<button type="button" class="bxr-color-button slick-prev hidden-arrow"></button>';
		 nextBtn = '<button type="button" class="bxr-color-button slick-next hidden-arrow"></button>';
		 }
		<?} else {?>
		 if (isTouchDevice()) {
		 prevBtn = '<button type="button" class="bxr-color-button slick-prev"></button>';
		 nextBtn = '<button type="button" class="bxr-color-button slick-next"></button>';
		 }
		<?}?>

		 $('#sl_'+<?=$unicumID?>).slick({

		 dots: false,
		 infinite: true,
		 speed: 300,
		 slidesToShow: <?=$colToElem["LG"]?>,
		 slidesToScroll: 1,
		 prevArrow: prevBtn,
		 nextArrow: nextBtn,
		 responsive: [
		 {
		 breakpoint: 1199,
		 settings: {
		 slidesToShow: <?=$colToElem["LG"]?>,
		 slidesToScroll: 1
		 }
		 },
		 {
		 breakpoint: 991,
		 settings: {
		 slidesToShow: <?=$colToElem["MD"]?>,
		 slidesToScroll: 1
		 }
		 },
		 {
		 breakpoint: 767,
		 settings: {
		 slidesToShow: <?=$colToElem["SM"]?>,
		 slidesToScroll: 1
		 }
		},
			 {
				 breakpoint: 360,
				 settings: {
					 slidesToShow: <?=$colToElem["XS"]?>,
					 slidesToScroll: 1
				 }
			 }
		]
		});

	</script>
<?
$this->addExternalJS(SITE_TEMPLATE_PATH.'/js/slick/slick.js');
$this->addExternalCss(SITE_TEMPLATE_PATH.'/js/slick/slick.css', false);
//echo "<pre>"; print_r($arResult); echo "</pre>";