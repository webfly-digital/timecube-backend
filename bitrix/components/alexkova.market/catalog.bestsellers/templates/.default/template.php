<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$this->setFrameMode(true);
use Alexkova\Bxready\Draw;

if (count($arResult["ITEMS"])>0):
	$this->setFrameMode(true);

	$unicumID = 0;
	if (isset($_REQUEST["bxr_ajax"]) && $_REQUEST["bxr_ajax"] == "yes"){
		$unicumID = 10000+intval($_REQUEST["ID"]);
	}

	$colToElem = array();
	$bootstrapGridCount = $arParams["BXREADY_LIST_BOOTSTRAP_GRID_STYLE"];
	if ($bootstrapGridCount>0){
		for($i=1; $i<=$bootstrapGridCount; $i++){
			if (($bootstrapGridCount % $i) == 0){
				$colToElem[$bootstrapGridCount / $i] = $i;
			}
		}
	}

	if ($arParams["BXREADY_LIST_BOOTSTRAP_GRID_STYLE"] == 10){
		$this->addExternalCss(SITE_TEMPLATE_PATH.'/library/bootstrap/css/grid10_column.css', true);
	}

	



	$first = ' bxr-bestsellers-group-active';?>
<div id="bxr-bestsellers-container">
	<?if (strlen($arParams["BXREADY_LIST_PAGE_BLOCK_TITLE"])>0):?>
		<h2 class="bxr-font-color"><?if(!empty($arParams["BXREADY_LIST_PAGE_BLOCK_TITLE_GLYPHICON"])) echo "<i class='fa ".$arParams["BXREADY_LIST_PAGE_BLOCK_TITLE_GLYPHICON"]."'></i>";?><?=$arParams["BXREADY_LIST_PAGE_BLOCK_TITLE"]?></h2>
	<?endif;?>
	<div class="row hidden-xs" id="bxr-bestsellers">
		<div class="col-lg-12 jcarousel-row">
		<div class="lent">
		<?foreach($arResult["ITEMS"] as $arItem):
			$arItem['EDIT_LINK'] = str_replace(array("%2Fajax%2Fbestsellers_tc.php", "bxr_ajax%3Dyes"), "", $arItem['EDIT_LINK']);				
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
			$strMainID = $this->GetEditAreaId($arItem['ID']);
			$unicum = 10000 + intval($arItem["ID"]);
			?>
			<div class="bxr-bestsellers-group<?=$first?>"
				id="rec_tab<?=$arItem["ID"]?>" data-slide="<?=$arItem["ID"]?>" data-type="group"
				data-unicum="<?=$unicum?>">
					<div><?=$arItem["NAME"]?> (<?=intval(count($arItem["PROPERTY_ITEMS_VALUE"]))?>)</div>
			</div>
			<?$first = '';
		endforeach;?>
		</div>
		</div>
	</div><?



	foreach($arResult["ITEMS"] as $arItem):?>
	<div class="col-xs-12 hidden-lg hidden-md hidden-sm">
	<div id="bxr-mobile-name-<?=$arItem["ID"]?>"
				 class="bxr-mobile-names bxr-color-button"
				   data-slide="<?=$arItem["ID"]?>"
				   data-unicum="<?=$unicum?>">
					<?=$arItem["NAME"]?> (<?=intval(count($arItem["PROPERTY_ITEMS_VALUE"]))?>)
			</div></div>
			<div id="best-panel-<?=$arItem["ID"]?>" class="bxr-carousel"></div>
		<?endforeach;?>
</div>
<script>
	$(document).ready(function(){

		if (typeof(BXReady.Market) == 'object'){

			function isTouchDevice() {
				try {
					document.createEvent('TouchEvent');
					return true;
				}
				catch(e) {
					return false;
				}
			};

			function initBestsellerSlider(unicumID){

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

				$(document).on('destroy', '#sl_'+unicumID, function(event, slick){
					$(this).data('slideset', 1);
				});

				$('#sl_'+unicumID).slick({
					<?if ($arParams["BXREADY_LIST_SLIDER_MARKERS"] == "Y") {?>
					dots: true,
					<?}?>
					infinite: <?=($arParams["BXREADY_LIST_HIDE_MOBILE_SLIDER_AUTOSCROLL"] == "Y") ? 'true' : 'false'?>,
					speed: 300,
					<?if ($arParams["VERTICAL_SLIDER_MODE"] == "Y") {?>
					vertical: true,
					<?}?>
                                        <?if ($arParams["BXREADY_LIST_HIDE_MOBILE_SLIDER_AUTOSCROLL"] == "Y") {?>
                                            autoplay: true,
                                            autoplaySpeed: <?=($arParams["BXREADY_LIST_HIDE_MOBILE_SLIDER_SCROLLSPEED"]) ? $arParams["BXREADY_LIST_HIDE_MOBILE_SLIDER_SCROLLSPEED"] : 2000?>,
					<?}?>    
					slidesToShow: <?=$colToElem[$arParams["BXREADY_LIST_LG_CNT"]]?>,
					slidesToScroll: 1,
					prevArrow: prevBtn,
					nextArrow: nextBtn,
					responsive: [
						{
							breakpoint: 768,
							settings: "unslick"

						},
						{
							breakpoint: 1199,
							settings: {
								slidesToShow: <?=$colToElem[$arParams["BXREADY_LIST_MD_CNT"]]?>,
								slidesToScroll: 1
							}
						},
						{
							breakpoint: 991,
							settings: {
								slidesToShow: <?=$colToElem[$arParams["BXREADY_LIST_SM_CNT"]]?>,
								slidesToScroll: 1
							}
						}
					]
				});

				$('#sl_'+unicumID).data('slideset', 2);
			}

			BXReady.Market.bestsellersAjaxUrl = '<?=SITE_DIR?>ajax/bestsellers_tc.php';

			BXReady.Market.Bestsellers = {
				iblockID: <?=$arParams["IBLOCK_ID"]?>,
				init: function(){
					prevBtn = '<button type="button" class="bxr-color-button slick-prev hidden-arrow"></button>';
					nextBtn = '<button type="button" class="bxr-color-button slick-next hidden-arrow"></button>';
					$('#bxr-bestsellers div.lent').slick({
						dots: false,
						infinite: false,
						speed: 300,
						slidesToShow: 4,
						centerMode: false,
						variableWidth: true,
						prevArrow: prevBtn,
						nextArrow: nextBtn
					});

				},
				load: function(idC, type, mobileMode){
					if (BXReady.Market.bestsellersAjaxUrl.length > 0 && BXReady.Market.Bestsellers.iblockID > 0){
						if ($('#rec_tab'+idC).data('state') != 'load'){
							$('#rec_tab'+idC).data('state', 'load');
							targetUrl = BXReady.Market.bestsellersAjaxUrl + '?bxr_ajax=yes&ID=' + idC+'&IBLOCK_ID='+BXReady.Market.Bestsellers.iblockID+'&rmT='+Math.random();
							BXReady.showAjaxShadow('#bxr-bestsellers-container','bxr-bestsellers-shadow');
							$.ajax({
								url: targetUrl,
								success: function(data){

									$('#best-panel-'+idC).html(data);
									$('.bxr-bestseller-list').css('display', 'none');
									$('#c'+idC).css('display', 'block');
									<?if ($arParams["BXREADY_LIST_SLIDER"] == "Y"):?>
									initBestsellerSlider($('#c'+idC).data('slider'));
									BXReady.Market.Basket.refresh();
                                                                        if (typeof window.BXReady.Market.Compare == 'object')
                                                                            window.BXReady.Market.Compare.reload();
									<?endif;?>
									BXReady.closeAjaxShadow('bxr-bestsellers-shadow');
									if (type == 'scroll') BXReady.scrollTo('#best-panel-'+idC);


								}
							});
						}else{
							$('.bxr-bestseller-list').css('display', 'none');
							$('#c'+idC).css('display', 'block');
							<?if ($arParams["BXREADY_LIST_SLIDER"] == "Y"):?>
                                                            $('.bestseller-slick-animation.slick-initialized').slick('destroy');
                                                            initBestsellerSlider($('#c'+idC).data('slider'));
							<?endif;?>
						}
					}
					$('#c'+idC).addClass('active');
					$('.bxr-bestsellers-group').removeClass('bxr-bestsellers-group-active');
					$('#rec_tab'+idC).addClass('bxr-bestsellers-group-active');
				}
			}

			$(document).on(
				'click',
				'#bxr-bestsellers div.bxr-bestsellers-group',
				function(){
					BXReady.Market.Bestsellers.load($(this).data('slide'),$(this).data('type'));
				}
			);

			$(document).on(
				'click',
				'#bxr-bestsellers-container div.bxr-mobile-names',
				function(){
					BXReady.Market.Bestsellers.load($(this).data('slide'), 'scroll');
				}
			);
			<?if ($arParams["BXREADY_LIST_SLIDER"] == "Y"):?>
			$(window).resize(function(){
				$('.bxr-bestseller-list').each(function(){
					if ($(window).width() > 766 && $('#sl_'+$(this).data('slider')).data('slideset') == 1)
						initBestsellerSlider($(this).data('slider'));
				});
			});
			<?endif;?>
		}
		startElement = $('#bxr-bestsellers div.bxr-bestsellers-group-active');
		activeBestseller = parseInt(startElement.data('slide'));

		if (activeBestseller > 0){
			BXReady.Market.Bestsellers.init();
			BXReady.Market.Bestsellers.load(startElement.data('slide'),startElement.data('type'));
		}
	})
</script>
	<div style="display:none;"><?
		Draw::getInstance($this)
			->showElement($arParams["BXREADY_ELEMENT_DRAW"], $arItem, $arParams);
		?></div>
<?endif;
