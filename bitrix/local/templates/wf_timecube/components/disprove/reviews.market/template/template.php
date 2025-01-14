<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use \Bitrix\Main\Localization\Loc;
$this->setFrameMode(true);
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
?>
<?
$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($navParams['NavNum']));
$containerName = 'container-'.$navParams['NavNum'];
$mainId = $this->GetEditAreaId(rand(0,2222222));
$obName = $templateData['JS_OBJ'] = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $mainId);
if (!empty($arResult['NAV_STRING_NEW']))
{
	$navParams =  array(
		'NavPageCount' => $arResult['NAV_STRING_NEW']->NavPageCount,
		'NavPageNomer' => $arResult['NAV_STRING_NEW']->NavPageNomer,
		'NavNum' => $arResult['NAV_STRING_NEW']->NavNum
	);
}
else
{
	$navParams = array(
		'NavPageCount' => 1,
		'NavPageNomer' => 1
	);
}
$showTopPager = $arParams['DISPLAY_TOP_PAGER'];
$showBottomPager = $arParams['DISPLAY_BOTTOM_PAGER'];
$showLazyLoad = $arParams['LAZY_LOAD'];

$arResult["SHOP_RATING_TEXT"] = [ 
    Loc::getMessage('DISPROVE_REVIEWSMARKET_UJASNYY_MAGAZIN'), 
    Loc::getMessage('DISPROVE_REVIEWSMARKET_PLOHOY_MAGAZIN'),
    Loc::getMessage('DISPROVE_REVIEWSMARKET_OBYCNYY_MAGAZIN'),
    Loc::getMessage('DISPROVE_REVIEWSMARKET_HOROSIY_MAGAZIN'),
    Loc::getMessage('DISPROVE_REVIEWSMARKET_OTLICNYY_MAGAZIN')
];
?>
<div class="dreview_block <?=$arParams["TEMPLATE_THEME"];?> opinions-block dr-new <? if($arParams["SHOW_PADDING"] == 'Y' || empty($arParams["SHOW_PADDING"]))echo 'padding';?>" data-entity="<?=$containerName?>">
<? if($arParams["SHOW_TITLE"] == 'Y' || empty($arParams["SHOW_TITLE"])):?>
<div class="dreview_h1_block">
    <div class="dreview_h1"><?=GetMessage("DISPROVE_REVIEWSMARKET_OTZYVY")?></div>
</div>
<? endif;?>
<?if ($arResult["SHOW_ADD"]=='Y') { ?>
<?
$APPLICATION->IncludeComponent(
	"disprove:reviews.market.add", 
	".default",
	array(
		"TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
		"STARS_TEXT" => $arResult["SHOP_RATING_TEXT"],
		"FORM"=>$arResult["form"],
		"COMPONENT_TEMPLATE" => ".default",
		"CACHE_TYPE" => "N",
		"CACHE_GROUPS" => "N",
		"COMPOSITE_FRAME_MODE" => "N"
	),
	false
);
?>
<?}
$http = ((CMain::IsHTTPS()) ? "https://" : "http://").$_SERVER["SERVER_NAME"];?>
<div class="d-coll_block rate-container">
    <div class="d-coll d-coll-50 overall-rate">
        <div class="d-coll d-coll-35">
            <p class="mobile-rate-value visible-xs"><?=Loc::getMessage('DISPROVE_REVIEWSMARKET_SUMMING_VALUE');?></p>
            <div class="rate-diagram"><?=$arResult["PERCENT"];?>
	        <svg><defs><radialGradient id="radial" cx="0.5" cy="0.5" r="0.6" fx="0.4" fy="0.4"><stop offset="0%" stop-color="#000000"></stop><stop offset="100%" stop-color="#ff9844"></stop></radialGradient></defs><circle r="54" cx="60" cy="60" style="stroke-dasharray: 397px 384px;"></circle><circle r="54" cx="60" cy="60" style="stroke-linecap: round;stroke-dasharray: <?=$arResult["DASH"];?>px 339px;"></circle></svg>
            </div>
        </div>
        <div class="d-coll d-coll-65">
          <div class="rate-block" itemprop="itemReviewed" itemscope itemtype="https://schema.org/Organization">
            <? if($arResult["scheme"]):?>
		    <div style="display: none;">
                <?if($arResult["scheme"]["company"]):?>
                <a itemprop="url" href="<?=$http;?>">
                    <span itemprop="name"><?=$arResult["scheme"]["company"];?></span>
                </a>
                <?endif;?>
                <div itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
                    <div itemprop="ratingValue"><?=$arResult["PERCENT"];?></div>
                    <span itemprop="reviewCount"><?=$arResult["COUNT"];?></span>
                </div>
                <?if($arResult["scheme"]["adress"]):?>
                <span itemprop="address"><?=$arResult["scheme"]["adress"];?></span>
                <?endif;?>
                <?if($arResult["scheme"]["phone"]):?>
                <span itemprop="telephone"><?=$arResult["scheme"]["phone"];?></span>
                <?endif;?>
            </div>
   		    <? endif;?> 
            <p class="title"><?=Loc::getMessage("DISPROVE_REVIEWSMARKET_SUMMING_VALUE");?>:</p>
            <div class="rate-value"><?=$arResult["PERCENT"];?></div>
            <div class="rate-stars">
              <div class="fill-stars" style="width: <?=$arResult["DASH2"]?>%"></div>
            </div>
          </div>
          <? if($arResult["INFO"]):?>
        	<div class="marks marks-filter-inline">
            	<ul class="marks-list">
			<? foreach($arResult["INFO_REVERSE"]["arInfo"] as $k=>$info):
                    if($info && $arResult["COUNT"]) {
                        $progress = round(100/($arResult["COUNT"] / $info));
                    }else{
                        $progress = 0;
                    }
			?>
                <!--noindex-->
                <li><?=$k;?>
                    <div class="mark-item" data-mark="<?=$k;?>" data-grade-filter="<?=$k;?>">
                        <div class="rate-bar-container">
                            <div class="rate-bar" style="width: <?=$progress;?>px;"></div>
                        </div>
                    	<a data-stars="<?=$APPLICATION->GetCurPageParam("STAR=".$k, array("STAR","PAGEN_1"));?>" <?if($info > 0):?>href="<?=$APPLICATION->GetCurPageParam("STAR=".$k, array("STAR","PAGEN_1"));?>"<?endif;?> class="" data-grade="<?=$k;?>"><?=DRM::getTermination($info);?></a>
                    </div>
                </li>
                <!--/noindex-->
            <? endforeach;?>
            	</ul>
            </div>
        <? endif;?>
        </div>
    </div>
    <? //GetMessage("DISPROVE_REVIEWSMARKET_STAR_FILTER_0"),
    $tabs = array(GetMessage("DISPROVE_REVIEWSMARKET_WHERE_TAB_1")); ?>
    <?/*?>
    <div class="d-coll d-coll-50 score-container">
      <p><?=GetMessage("DISPROVE_REVIEWSMARKET_WHERE");?></p>
      <div class="sources-container tab">
        <? foreach($tabs as $t=>$tab):?>
        <div class="d-btn active">
          <label>
            <input type="radio" name="source" value="<?=$t;?>">
            <span class="title-<?=$t;?>"><?=$tab;?></span>
          </label>
        </div>
        <? endforeach;?>
      </div>
    </div>
    <?*/?>
    <div class="d-coll d-coll-50 d-filters">
      <form action="/reviews/" method="POST">
        <div class="d-coll d-coll-45">
          <p><?=GetMessage("DISPROVE_REVIEWSMARKET_FILTER_STAR");?></p>
          <div class="dr-select-selection">
          	<div class="dr-select-selection-input dr-sort_start"><?=GetMessage("DISPROVE_REVIEWSMARKET_STAR_FILTER_".$arResult["SORT_STARS"]);?></div>
            <input type="" name="" value="" />
              <div class="dr-select_drop_down">
               <? $i = 0;
            	while ($i < 6) {
					$filter = ''; $active = '';
					if($arResult["SORT_STARS"]){
						if($arResult["SORT_STARS"] == $i) $active = "active";
					}else{
						if($i == 0) $active = "active";	
					}
					if($i > 0) {
						$filter = "STAR=".$i;
					}
					?>
                <div data-stars="<?=$APPLICATION->GetCurPageParam($filter, array("STAR","PAGEN_1"));?>" data-value="<?=$i;?>" class="dr-ajax-sort-star <?=$active;?>"><?=GetMessage("DISPROVE_REVIEWSMARKET_STAR_FILTER_".$i);?></div>
				<?
				$i++;
				}
				?>
              </div>
          </div>
        </div> 
        <div class="d-coll d-coll-45 d-col-r">
          <p><?=GetMessage("DISPROVE_REVIEWSMARKET_FILTER_SORT");?></p>
          <div class="dr-select-selection">
          	<div class="dr-select-selection-input dr-sort-date-input <? if($arResult["sort_date"] == 'asc' || $arResult["sort_popular"] == 'asc'){echo 'down';}else echo 'up';?>"><?if($arResult["sort_popular"]){echo GetMessage("DISPROVE_REVIEWSMARKET_SORT_POPULAR");}else echo GetMessage("DISPROVE_REVIEWSMARKET_SORT_DATE");?></div>
              <div class="dr-select_drop_down dr-sort-date dr-ajax-sort">
                <div data-stars="<?=$APPLICATION->GetCurPageParam("SORT_1=date&SORT_N=asc", array("SORT_1","SORT_N","STAR"));?>" class="up<? if($arResult["sort_date"] == "desc") echo ' active';?>"><?=GetMessage("DISPROVE_REVIEWSMARKET_SORT_DATE");?></div>
                <div data-stars="<?=$APPLICATION->GetCurPageParam("SORT_1=date&SORT_N=desc", array("SORT_1","SORT_N","STAR"));?>" class="down<? if($arResult["sort_date"] == "asc") echo ' active';?>"><?=GetMessage("DISPROVE_REVIEWSMARKET_SORT_DATE");?></div>
                <div data-stars="<?=$APPLICATION->GetCurPageParam("SORT_1=AGREE&SORT_N=asc", array("SORT_1","SORT_N","STAR"));?>" class="up<? if($arResult["sort_popular"] == "desc") echo ' active';?>"><?=GetMessage("DISPROVE_REVIEWSMARKET_SORT_POPULAR");?></div>
                <div data-stars="<?=$APPLICATION->GetCurPageParam("SORT_1=AGREE&SORT_N=desc", array("SORT_1","SORT_N","STAR"));?>" class="down<? if($arResult["sort_popular"] == "asc") echo ' active';?>"><?=GetMessage("DISPROVE_REVIEWSMARKET_SORT_POPULAR");?></div>
              </div>
          </div>
        </div>
      </form>
    </div>
	<?if($arResult["URL_SHOP"]):?>
	<div class="dr-link_ymarket">
		<!--noindex--><a rel="nofollow" class="d-btn" target="_blank" href="<?=$arResult["URL_SHOP"];?>"><span><?=GetMessage("DISPROVE_REVIEWSMARKET_NA_ANDEKS_MARKET_2")?></span></a><!--/noindex-->
	</div>
	<?endif;?>
</div>
<div class="dr-wait" style="display:none;"></div>
<div class="dreview_list_new dreview_ajax_inner" data-entity="items-row">
    <? 
	$datetime2 = date_create('now',new DateTimeZone('Europe/Moscow'));
	$show_moth = false;
		// https://yandex.ru/support/webmaster/supported-schemas/review-organization.xml
	if($arResult["ITEMS"]){
		foreach($arResult["ITEMS"] as $k=>$arItem):
		$datetime1 = date_create($arItem["date"]);
		$interval = date_diff($datetime1, $datetime2);
		if($arResult["NAV_STRING_NEW"]->NavPageNomer == 1 && !$show_moth){
			if($interval->m == 4){
	?>
	<div class="n-product-review-item__separator i-bem b-zone">
		<div class="n-product-review-item__separator-title"><?=GetMessage("DISPROVE_REVIEWS_DATE_3_MONTH");?></div>
	</div>
	<?
			$show_moth = true;
			}
		}?>
  	<div id="<?=$arItem["ID"];?>" data-id="<?=$arItem["ID"];?>" class="dr-item d-coll_block <? if($k < 1)echo 'yellow';?>" itemscope itemtype="https://schema.org/Review">
        <div style="display: none;" itemprop="name">
            <?if($arParams["DELETE_LINK"] !== "Y"):?>
        <a itemprop="url" href="<?=$http.''.$arResult["CURDIR"];?>#<?=$arItem["ID"];?>" itemprop="url">
            <?endif;?>
            <?=$arResult["scheme"]["company"];?>
            <?if($arParams["DELETE_LINK"] !== "Y"):?>
        </a>
            <?endif;?>
        </div> 
        <div class="d-coll dr-item-coll-l">
            <div class="marks-list" itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
            <? $i = 0;
            while ($i < 5) {
                ?>
              <div class="star-item <? if($i < $arItem["rating"]) echo 'active';?>"></div>
                <?
            $i++;
            }
			$mDate = explode(" ",$arItem["date"]);
            ?>
            <meta itemprop="ratingValue" content="<?=$arItem["rating"];?>" />
            <meta itemprop="bestRating" content="5" />
            </div>
			<? if($arItem["problem"] && $arItem["problem"] !== GetMessage("DISPROVE_REVIEWSMARKET_PROBLEM_NOT")):?>
			<div class="problems">
				<span class="drm_green_problem"></span> <?=$arItem["problem"];?>
				<br />
			</div>
			<?endif;?>
            <div class="dr-item_name" itemprop="author" itemscope itemtype="https://schema.org/Person">
				<?if($arItem["avatarUrl"]):?>
				<div class="dr-item_img">
					<img alt="<?=GetMessage("DRM_USER_TO");?> <?=$arItem["author"];?>" title="<?=GetMessage("DRM_USER_TO");?> <?=$arItem["author"];?>" src="<?=$arItem["avatarUrl"];?>" />
				</div>
				<?endif;?>
				<span itemprop="name"><?=$arItem["author"];?></span>
				<? if($arItem["info_grades"]):?> 
                <div class="dr-info_grades"> (<?=str_replace("#NUM#",$arItem["info_grades"],GetMessage("DISPROVE_REVIEWSMARKET_AUTHOR_NUM"));?> <?=DRM::getTermination($arItem["info_grades"]);?>) </div>
                <? endif;?>
            </div>
            <div class="dr-item_date"><meta itemprop="datePublished" content="<?=$mDate[0];?>" /> <? if($arItem["DISPLAY_ACTIVE_FROM"]){echo $arItem["DISPLAY_ACTIVE_FROM"];}else $arItem["date"];?> </div>
            <?if($arItem["region"]):?><div class="dreview_region"><?=GetMessage("DISPROVE_REVIEWSMARKET_CITY");?><?=$arItem["region"];?> </div><?endif;?>
			<? if($arItem["delivery"]):?>
                <div class="dr-item_buy"><?=GetMessage("DISPROVE_REVIEWSMARKET_SPOSOB");?>: <?=$arItem["delivery"];?></div>
            <? endif;?>
        </div>
    	<div class="d-coll dr-item-coll-r">
			<div class="dr-description" itemprop="reviewBody">
				<? if($arItem["pro"]):?>
                    <div class="dr-item_h2"><?=GetMessage("DISPROVE_REVIEWSMARKET_DOSTOINSTVA")?></div>
                    <p<?/* itemprop="pro"*/?>><?=$arItem["pro"];?></p>
                <? endif;?>
                <? if($arItem["contra"]):?>             
                    <div class="dr-item_h2"><?=GetMessage("DISPROVE_REVIEWSMARKET_NEDOSTATKI")?></div> 
                    <p<?/* itemprop="contra"*/?>><?=$arItem["contra"];?></p>
                <? endif;?>
                <? if($arItem["text"]):?>               
                    <div class="dr-item_h2"><?=GetMessage("DISPROVE_REVIEWSMARKET_KOMMENTARIY")?></div>
                    <p<?/* itemprop="reviewBody"*/?>><?=$arItem["text"];?></p>
                <? endif;?>
			</div>
            <? if($arItem["order_id"] && $arItem["BASKET"]):?>
                <br />
                <div class="dr-item_h2"><?=GetMessage("DISPROVE_REVIEWSMARKET_ORDER_LIST")?></div>
                <div class="dreview_order_id"><?=GetMessage("DISPROVE_REVIEWSMARKET_ORDER").''.$arItem["order_id"];?></div>
                <br />
                <? if($arParams["SHOW_ITEMS"] == 'Y' || empty($arParams["SHOW_ITEMS"])):?>
                <div class="dr-show_link"><?=GetMessage("DISPROVE_REVIEWSMARKET_SHOW_ORDER")?></div>
                <div class="dr-items_list d-hidden">
                <? if($arItem["BASKET"]):?>
                <div class="dr-basket d-coll_block">
                <? foreach($arItem["BASKET"] as $arBasket): ?>
                	<div class="dr-basket-item d-coll d-coll-35">
						<? if($arBasket["PICTURE"]["src"]):?>
                        <a title="<? echo $arBasket["NAME"]; ?>" class="dr-basket-img" href="<?=$arBasket["DETAIL_PAGE_URL"];?>">
                            <img data-src="<?=$arBasket["PICTURE"]["src"];?>" alt="<? echo $arBasket["NAME"]; ?>" src="/bitrix/components/disprove/reviews.market/images/placeholder.jpg" />
                        </a>
                        <? endif;?>
                        <div class="dr-basket-item-bottom">
                            <a href="<?=$arBasket["DETAIL_PAGE_URL"];?>">
                            <? echo $arBasket["NAME"]; ?>
                            </a>
                        </div>
                    </div>
                <? endforeach; ?>
                </div>
                <? endif;?>
                </div>
                <? endif;?>
            <? endif;?>
            <?
			//$votesum = 7 - rand(2,10);
			$votesum = 0; $vote_type = ''; $likeID = 0;
			if($arResult["LIKES"][$arItem["ID"]]["VALUE"]){
				$likeID = 1;
				$votesum = $arResult["LIKES"][$arItem["ID"]]["VALUE"];
			}
			if($votesum){
				if($votesum > 0){
					$vote_type = 'positive';
				}else{ 
					$vote_type = 'negative';
					$votesum = $votesum * (-1);
				}
			}
			?>		
			<?if($arResult["FACTS"][$arItem["id_market"]]):?>	
                <div class="dr-item_bottom">
                    <div class="dr-btn_comment_group">
                        <? if($arResult["COMMENTS"][$arItem["id_market"]]):?>
                            <div class="dr-comment_item_count dr-show_item_facts"><?=GetMessage("DISPROVE_REVIEWSMARKET_FACTS_TEXT")?></div>
                        <? else:?>
                            <div class="dr-comment_item_count"><?=GetMessage("DISPROVE_REVIEWSMARKET_FACTS_NONE")?></div>
                        <? endif;?>
                    </div>
                </div>
				<div class="dr-facts d-hidden">
                    <div class="drm_opinion__user-ratings">
                    <?foreach($arResult["FACTS"][$arItem["id_market"]] as $fact):?>
                        <div class="drm_user-rating drm_opinion__user-rating">
                            <div class="drm_user-rating__text"> 
                                <span class="drm_user-rating__name"><?=$fact["TITLE"];?></span> <span class="drm_user-rating__rating"><?=$fact["VALUE"];?></span> 
                            </div>
                            <div class="drm_user-rating__bar">
                                <div class="drm_user-rating__bar-filled" style="width: <?=(int)$fact["VALUE"]*20;?>%"></div>
                            </div>
                        </div>
                    <?endforeach;?>
                    </div>
				</div>
			<? endif;?> 
            <div class="dr-item_bottom">
                <div class="dr-btn_comment_group">
                	<? if($arResult["COMMENTS"][$arItem["id_market"]]):?>
                    	<div class="dr-comment_item_count dr-show_item_comments"><?=GetMessage("DISPROVE_REVIEWSMARKET_COMMENT_TEXT")?> (<?=count($arResult["COMMENTS"][$arItem["id_market"]]);?>)</div>
                    <? else:?>
                    	<div class="dr-comment_item_count"><?=GetMessage("DISPROVE_REVIEWSMARKET_COMMENTS_NONE")?></div>
                    <? endif;?>
                </div>
                <div class="dr-edit-container">
                    <div class="vote-container">
                      <div class="vote-widget-container <? if($arResult["LIKES"][$arItem["ID"]]["BANED"]) echo 'voted';?>">
                          <div class="vote-sum <?=$vote_type;?>"><?=$votesum;?></div>
                          <div class="vote-action <? if($likeID)if($arResult["LIKES"][$arItem["ID"]]["USER_TYPE"] == "POS") echo 'voted';?> positive">
                            <span class="vote-positive"></span>
                            <span class="vote-counter"><?=$arResult["LIKES"][$arItem["ID"]]["POS"]?></span>
                          </div>
                          <div class="vote-action <? if($likeID)if($arResult["LIKES"][$arItem["ID"]]["USER_TYPE"] == "NEG") echo 'voted';?> negative">
                            <span class="vote-negative"></span>
                            <span class="vote-counter"><?=$arResult["LIKES"][$arItem["ID"]]["NEG"]?></span>
                          </div>
                      </div>
                    </div>
                </div>
            </div>
            <? if($arResult["COMMENTS"][$arItem["id_market"]]):?><br />
            <div class="dr-comments d-hidden">
               <?
					if($arItem["anonymous"] == 'true'){
						$comment_user = GetMessage("DISPROVE_REVIEWSMARKET_USER_NONE");
					}else
						$comment_user = GetMessage("DISPROVE_REVIEWSMARKET_USER_ASK").$arItem["author"];
				?>
                <? foreach($arResult["COMMENTS"][$arItem["id_market"]] as $comment):?>
                    <div class="dr-comment">
                        <div class="dr-comment_date"><?=$comment["date"];?></div>
                        <div class="dr-comment_name"><? $comment["user"] == 0 ? print GetMessage("DISPROVE_REVIEWSMARKET_COMMENTS") : print $comment_user;?></div>
                        <p><?=$comment["body"];?></p>
                    </div>
                <? endforeach;?>
            </div>
            <? endif;?>
        </div>
    </div>
		<?
        endforeach;
    }
    ?>

</div>
    <?if($arParams["LOAD_ON_SCROLL"] == "Y"):?>
    <div rel="next" class="more-load" data-entity="lazy-container-1" <?if($navParams["NavPageNomer"] == $navParams["NavPageCount"]){echo 'style="display:none"';}?>>
        <button type="button" data-use="show-more-1" href="/catalog/podvesnye-svetilniki/?PAGEN_1=4" data-wrap="catalogue-list" data-item="catalogue-list-item_product"><?=Loc::getMessage('DYMARKET_BTN_SHOW_MORE')?></button>
    </div>
    <?endif;?>
    <?if($navParams && $arParams["DISPLAY_BOTTOM_PAGER"]):?>
    <div class="pagination_block" data-pagination-num="<?=$navParams["NavPageNomer"];?>" data-pagination-pages="<?=$navParams["NavPageCount"];?>">
        <?
        $APPLICATION->IncludeComponent('bitrix:system.pagenavigation', $arParams["PAGER_TEMPLATE"], array(
            "NAV_RESULT" => $arResult["NAV_STRING_NEW"],
            "SEF_MODE" => "Y",
            "SHOW_COUNT" => "N",
        ));
        ?>
    </div>
    <?endif;?>
</div>
<?
$arResult["ORIGINAL_PARAMS"]["PARENT_NAME"] = 'disprove:reviews.market';
$arResult["ORIGINAL_PARAMS"]["PARENT_TEMPLATE_NAME"] = $templateName;

$signer = new \Bitrix\Main\Security\Sign\Signer;
$signedTemplate = $signer->sign($templateName, 'reviews.market');
$signedParams = $signer->sign(base64_encode(serialize($arResult["ORIGINAL_PARAMS"])), 'reviews.market');

//pr($arParams["LAZY_LOAD"]);
//pr($arParams["LOAD_ON_SCROLL"]);

?>
<script>
document.addEventListener("readystatechange", function (e) {
    "complete" === e.target.readyState && setTimeout(function () {
       if (typeof (JSDisproveReviewsComponent) === "function") {
           var <?=$obName?> = new JSDisproveReviewsComponent({
                siteId: '<?=CUtil::JSEscape($component->getSiteId())?>',
                componentPath: '<?=CUtil::JSEscape($componentPath)?>',
                navParams: <?=CUtil::PhpToJSObject($navParams)?>,
                deferredLoad: false,  //* enable it for deferred load*//
                initiallyShowHeader: '<?=!empty($arResult['ITEM_ROWS'])?>',
                lazyLoad: !!'<?=($arParams['LAZY_LOAD'] === 'Y')?>',
                loadOnScroll: !!'<?=($arParams['LOAD_ON_SCROLL'] === 'Y')?>',
                template: '<?=CUtil::JSEscape($signedTemplate)?>',
                ajaxId: '<?=CUtil::JSEscape($arParams['AJAX_ID'])?>',
                parameters: '<?=CUtil::JSEscape($signedParams)?>',
                container: '<?=$containerName?>',
                sendText: '<?=Loc::getMessage('DISPROVE_REVIEWSMARKET_AJAX_SUCCESS')?>'
           });
       }
  }, 500)
}, {passive: !0});
</script>