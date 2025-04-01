<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Alexkova\Bxready\Draw;

	$this->setFrameMode(true);

global $unicumID;
if ($unicumID<=0) {$unicumID = 1;} else {$unicumID++;}
if (isset($_REQUEST["bxr_ajax"]) && $_REQUEST["bxr_ajax"] == "yes"){
	$unicumID = "marc_".htmlspecialchars($_REQUEST["ID"]);
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
	$this->addExternalCss(SITE_TEMPLATE_PATH.'/library/bootstrap/css/grid10_colums.css', true);
}

$first = ' bxr-bestsellers-group-active';?>
<div id="bxr-markers-container">
	<?if (strlen($arParams["BXREADY_LIST_PAGE_BLOCK_TITLE"])>0):?>
		<h2 class="bxr-font-color"><?if(!empty($arParams["BXREADY_LIST_PAGE_BLOCK_TITLE_GLYPHICON"])) echo "<i class='fa ".$arParams["BXREADY_LIST_PAGE_BLOCK_TITLE_GLYPHICON"]."'></i>";?><?=$arParams["BXREADY_LIST_PAGE_BLOCK_TITLE"]?></h2>
	<?endif;

	$firstMarker = false?>
	<div class="row hidden-xs" id="bxr-markers">
		<div class="col-lg-12 lent">
			<?foreach($arResult["MARKERS_LIST"] as $cell):

				
				switch ($cell){
				case "ACTION":?>
					<div class="bxr-markers-group" id="marc_tabSPECIALOFFER" data-slide="SPECIALOFFER" data-type="markers"><?=GetMessage('SPECIALOFFER_BUTTON')?></div>
					<?
					if (!$firstMarker) $firstMarker = "SPECIALOFFER";
					break;
				case "NEW":?>
					<div class="bxr-markers-group" id="marc_tabNEWPRODUCT" data-slide="NEWPRODUCT" data-type="markers"><?=GetMessage('NEWPRODUCT_BUTTON')?></div>
					<?
					if (!$firstMarker) $firstMarker = "NEWPRODUCT";
					break;
				case "RECCOMEND":?>
					<div class="bxr-markers-group" id="marc_tabRECOMMENDED" data-slide="RECOMMENDED" data-type="markers"><?=GetMessage('RECOMMENDED_BUTTON')?></div>
					<?
					if (!$firstMarker) $firstMarker = "RECOMMENDED";
					break;
				case "HIT":?>
					<div class="bxr-markers-group" id="marc_tabSALELEADER" data-slide="SALELEADER" data-type="markers"><?=GetMessage('SALELEADER_BUTTON')?></div>
					<?
					if (!$firstMarker) $firstMarker = "SALELEADER";
					break;
				default ;
			}
			endforeach;?>
		</div></div><?

	foreach($arResult["MARKERS_LIST"] as $cell):

		
		switch ($cell){
		case "ACTION":?>
			<div class="col-xs-12 hidden-lg hidden-md hidden-sm">
				<div id="bxr-mobile-name-SPECIALOFFER"
					 class="bxr-marker-mobile-names bxr-color-button"
					 data-slide="SPECIALOFFER"
					 data-unicum="<?=$unicum?>">
					<?=GetMessage('SPECIALOFFER_BUTTON')?>
				</div></div>
				<div id="mark-panel-SPECIALOFFER" class="bxr-carousel"></div>
			<?
			break;
		case "NEW":?>
			<div class="col-xs-12 hidden-lg hidden-md hidden-sm">
				<div id="bxr-mobile-name-NEWPRODUCT"
					 class="bxr-marker-mobile-names bxr-color-button"
					 data-slide="NEWPRODUCT"
					 data-unicum="<?=$unicum?>">
					<?=GetMessage('NEWPRODUCT_BUTTON')?>
				</div></div>
			<div id="mark-panel-NEWPRODUCT" class="bxr-carousel"></div><?

			
			break;
		case "RECCOMEND":?>
			<div class="col-xs-12 hidden-lg hidden-md hidden-sm">
				<div id="bxr-mobile-name-RECOMMENDED"
					 class="bxr-marker-mobile-names bxr-color-button"
					 data-slide="RECOMMENDED"
					 data-unicum="<?=$unicum?>">
					<?=GetMessage('RECOMMENDED_BUTTON')?>
				</div></div>
			<div id="mark-panel-RECOMMENDED" class="bxr-carousel"></div>

			<?
			break;
		case "HIT":?>
			<div class="col-xs-12 hidden-lg hidden-md hidden-sm">
				<div id="bxr-mobile-name-SALELEADER"
					 class="bxr-marker-mobile-names bxr-color-button"
					 data-slide="SALELEADER"
					 data-unicum="<?=$unicum?>">
					<?=GetMessage('SALELEADER_BUTTON')?>
				</div></div>
			<div id="mark-panel-SALELEADER" class="bxr-carousel"></div>
			<?
			break;
		default ;
	}
	endforeach;?>
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

			function initMarkerSlider(unicumID){

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
							breakpoint: 767,
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

			BXReady.Market.markersAjaxUrl = '<?=SITE_DIR?>ajax/markers_tc.php';

			BXReady.Market.Markers = {

				iblockID: <?=$arParams["IBLOCK_ID"]?>,

				init: function(){



				},

				load: function(idC, type, mobileMode){

					if (BXReady.Market.markersAjaxUrl.length > 0 && BXReady.Market.Markers.iblockID > 0){

						if ($('#marc_tab'+idC).data('state') != 'load'){
							$('#marc_tab'+idC).data('state', 'load');

							targetUrl = BXReady.Market.markersAjaxUrl + '?bxr_ajax=yes&ID=' + idC+'&IBLOCK_ID='+BXReady.Market.Markers.iblockID+'&rmT='+Math.random();

							BXReady.showAjaxShadow('#bxr-markers-container','bxr-markers-shadow');



							$.ajax({
								url: targetUrl,
								success: function(data){


									$('#mark-panel-'+idC).html(data);



									$('.bxr-markers-list').css('display', 'none');
									$('#mc'+idC).css('display', 'block');

									<?if ($arParams["BXREADY_LIST_SLIDER"] == "Y"):?>
									initMarkerSlider(idC);
									BXReady.Market.Basket.refresh();
                                                                        if (typeof window.BXReady.Market.Compare == 'object')
                                                                            window.BXReady.Market.Compare.reload();
									<?endif;?>

									BXReady.closeAjaxShadow('bxr-markers-shadow');
									if (type == 'scroll') BXReady.scrollTo('#mark-panel-'+idC);

								}
							});
						}else{
							$('.bxr-markers-list').css('display', 'none');

							$('#mc'+idC).css('display', 'block');
							<?if ($arParams["BXREADY_LIST_SLIDER"] == "Y"):?>

							$('.markers-slick-animation').slick('destroy');
							initMarkerSlider($('#mc'+idC).data('slider'));
							<?endif;?>
						}


					}

					$('#mc'+idC).addClass('active');

					$('.bxr-markers-group').removeClass('bxr-markers-group-active');
					$('#marc_tab'+idC).addClass('bxr-markers-group-active');

				}


			};

			<?if ($arParams["BXREADY_LIST_SLIDER"] == "Y"):?>

			$(window).resize(function(){
				$('.bxr-markers-list').each(function(){
					if ($(window).width() > 766 && $('#sl_'+$(this).data('slider')).data('slideset') == 1)
						initMarkerSlider($(this).data('slider'));
				});
			});
			<?endif;?>

			$(document).on(
				'click',
				'#bxr-markers div.bxr-markers-group',
				function(){
					BXReady.Market.Markers.init();
					BXReady.Market.Markers.load($(this).data('slide'));
				}
			);

			$(document).on(
				'click',
				'#bxr-markers-container div.bxr-marker-mobile-names',
				function(){
					BXReady.Market.Markers.load($(this).data('slide'), 'scroll');
				}
			);

		}



		BXReady.Market.Markers.load('<?=$firstMarker?>');
	})
</script>

<div style="display:none;"><?
	Draw::getInstance($this)
		->showElement($arParams["BXREADY_ELEMENT_DRAW"], $arItem, $arParams);
?></div>