<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */
/** @global CMain $APPLICATION */

use Bitrix\Main\Localization\Loc;
use Alexkova\Bxready\Draw;

$elementDraw = \Alexkova\Bxready\Draw::getInstance($this);
$elementDraw->setCurrentTemplate($this);


$randomString = $this->randString();


$APPLICATION->setTitle(Loc::getMessage('CPSL_SUBSCRIBE_TITLE_NEW'));
if(!$arResult['USER_ID'] && !isset($arParams['GUEST_ACCESS'])):?>
	<?
	$contactTypeCount = count($arResult['CONTACT_TYPES']);
	$authStyle = 'display: block;';
	$identificationStyle = 'display: none;';
	if(!empty($_GET['result']))
	{
		$authStyle = 'display: none;';
		$identificationStyle = 'display: block;';
	}
	?>
	<div class="row">
		<div class="col-md-8 col-sm-7">
			<div class="alert alert-danger"><?=Loc::getMessage('CPSL_SUBSCRIBE_PAGE_TITLE_AUTHORIZE')?></div>
		</div>
		<? $authListGetParams = array(); ?>
		<div class="col-md-8 col-sm-7" id="catalog-subscriber-auth-form" style="<?=$authStyle?>">
			<?$APPLICATION->authForm('', false, false, 'N', false);?>
			<hr class="bxe-light">
		</div>
		<?$APPLICATION->setTitle(Loc::getMessage('CPSL_TITLE_PAGE_WHEN_ACCESSING'));?>
		<div id="catalog-subscriber-identification-form" style="<?=$identificationStyle?>">
		<div class="col-md-8 col-sm-7 catalog-subscriber-identification-form">
			<h4><?=Loc::getMessage('CPSL_HEADLINE_FORM_SEND_CODE')?></h4>
			<hr class="bxe-light">
			<form method="post">
				<?=bitrix_sessid_post()?>
				<input type="hidden" name="siteId" value="<?=SITE_ID?>">
				<?if($contactTypeCount > 1):?>
					<div class="form-group">
						<label for="contactType"><?=Loc::getMessage('CPSL_CONTACT_TYPE_SELECTION')?></label>
						<select id="contactType" class="form-control" name="contactType">
							<?foreach($arResult['CONTACT_TYPES'] as $contactTypeData):?>
								<option value="<?=intval($contactTypeData['ID'])?>">
									<?=htmlspecialcharsbx($contactTypeData['NAME'])?></option>
							<?endforeach;?>
						</select>
					</div>
				<?endif;?>
				<div class="form-group">
					<?
						$contactLable = Loc::getMessage('CPSL_CONTACT_TYPE_NAME');
						$contactTypeId = 0;
						if($contactTypeCount == 1)
						{
							$contactType = current($arResult['CONTACT_TYPES']);
							$contactLable = $contactType['NAME'];
							$contactTypeId = $contactType['ID'];
						}
					?>
					<label for="contactInput"><?=htmlspecialcharsbx($contactLable)?></label>
					<input type="text" class="form-control" name="userContact" id="contactInput">
					<input type="hidden" name="subscriberIdentification" value="Y">
					<?if($contactTypeId):?>
						<input type="hidden" name="contactType" value="<?=$contactTypeId?>">
					<?endif;?>
				</div>
				<button type="submit" class="btn btn-default"><?=Loc::getMessage('CPSL_BUTTON_SUBMIT_CODE')?></button>
			</form>
		</div>
		<div class="col-md-8 col-sm-7">
			<h4><?=Loc::getMessage('CPSL_HEADLINE_FORM_FOR_ACCESSING')?></h4>
			<hr class="bxe-light">
			<form method="post">
				<?=bitrix_sessid_post()?>
				<div class="form-group">
					<label for="contactInput"><?=htmlspecialcharsbx($contactLable)?></label>
					<input type="text" class="form-control" name="userContact" id="contactInput" value=
						"<?=!empty($_GET['contact']) ? htmlspecialcharsbx(urldecode($_GET['contact'])): ''?>">
				</div>
				<div class="form-group">
					<label for="token"><?=Loc::getMessage('CPSL_CODE_LABLE')?></label>
					<input type="text" class="form-control" name="subscribeToken" id="token">
					<input type="hidden" name="accessCodeVerification" value="Y">
				</div>
				<button type="submit" class="btn btn-default"><?=Loc::getMessage('CPSL_BUTTON_SUBMIT_ACCESS')?></button>
			</form>
		</div>
		</div>
	</div>
	<script type="text/javascript">
		BX.ready(function() {
			if(BX('cpsl-auth'))
			{
				BX.bind(BX('cpsl-auth'), 'click', BX.delegate(showAuthForm, this));
				BX.bind(BX('cpsl-identification'), 'click', BX.delegate(showAuthForm, this));
			}
			function showAuthForm()
			{
				var formType = BX.proxy_context.id.replace('cpsl-', '');
				var authForm = BX('catalog-subscriber-auth-form'),
					codeForm = BX('catalog-subscriber-identification-form');
				if(!authForm || !codeForm || !BX('catalog-subscriber-'+formType+'-form')) return;

				BX.style(authForm, 'display', 'none');
				BX.style(codeForm, 'display', 'none');
				BX.style(BX('catalog-subscriber-'+formType+'-form'), 'display', '');
			}
		});
	</script>
<?endif;

?>
<script type="text/javascript">
	BX.message({
		CPSL_MESS_BTN_DETAIL: '<?=('' != $arParams['MESS_BTN_DETAIL']
			? CUtil::JSEscape($arParams['MESS_BTN_DETAIL']) : GetMessageJS('CPSL_TPL_MESS_BTN_DETAIL'));?>',

		CPSL_MESS_NOT_AVAILABLE: '<?=('' != $arParams['MESS_BTN_DETAIL']
			? CUtil::JSEscape($arParams['MESS_BTN_DETAIL']) : GetMessageJS('CPSL_TPL_MESS_BTN_DETAIL'));?>',
		CPSL_BTN_MESSAGE_BASKET_REDIRECT: '<?=GetMessageJS('CPSL_CATALOG_BTN_MESSAGE_BASKET_REDIRECT');?>',
		CPSL_BASKET_URL: '<?=$arParams["BASKET_URL"];?>',
		CPSL_TITLE_ERROR: '<?=GetMessageJS('CPSL_CATALOG_TITLE_ERROR') ?>',
		CPSL_TITLE_BASKET_PROPS: '<?=GetMessageJS('CPSL_CATALOG_TITLE_BASKET_PROPS') ?>',
		CPSL_BASKET_UNKNOWN_ERROR: '<?=GetMessageJS('CPSL_CATALOG_BASKET_UNKNOWN_ERROR') ?>',
		CPSL_BTN_MESSAGE_SEND_PROPS: '<?=GetMessageJS('CPSL_CATALOG_BTN_MESSAGE_SEND_PROPS');?>',
		CPSL_BTN_MESSAGE_CLOSE: '<?=GetMessageJS('CPSL_CATALOG_BTN_MESSAGE_CLOSE') ?>',
		CPSL_STATUS_SUCCESS: '<?=GetMessageJS('CPSL_STATUS_SUCCESS');?>',
		CPSL_STATUS_ERROR: '<?=GetMessageJS('CPSL_STATUS_ERROR') ?>'
	});
</script>
<?

if(!empty($_GET['result']) && !empty($_GET['message']))
{
	$successNotify = strpos($_GET['result'], 'Ok') ? true : false;
	$postfix = $successNotify ? 'Ok' : 'Fail';
	$popupTitle = Loc::getMessage('CPSL_SUBSCRIBE_POPUP_TITLE_'.strtoupper(str_replace($postfix, '', $_GET['result'])));

	$arJSParams = array(
		'NOTIFY_USER' => true,
		'NOTIFY_POPUP_TITLE' => $popupTitle,
		'NOTIFY_SUCCESS' => $successNotify,
		'NOTIFY_MESSAGE' => urldecode($_GET['message']),
	);
	?>
	<script type="text/javascript">
		var <?='jaClass_'.$randomString;?> = new JCCatalogProductSubscribeList(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
	</script>
	<?
}

if (!empty($arResult['ITEMS']))
{
    global $unicumID;
    if ($unicumID<=0) {$unicumID = 1;} else {$unicumID++;}

    $arParams["UNICUM_ID"] = $unicumID;
    foreach ($arResult["ITEMS"] as $key => $arItem) {
        
        $strMainID = $this->GetEditAreaId($arItem['ID']);
        $arItemIDs = array(
                'ID' => $strMainID,
                'PICT' => $strMainID . '_pict',
                'SECOND_PICT' => $strMainID . '_secondpict',
                'MAIN_PROPS' => $strMainID . '_main_props',

                'QUANTITY' => $strMainID . '_quantity',
                'QUANTITY_DOWN' => $strMainID . '_quant_down',
                'QUANTITY_UP' => $strMainID . '_quant_up',
                'QUANTITY_MEASURE' => $strMainID . '_quant_measure',
                'BUY_LINK' => $strMainID . '_buy_link',
                'SUBSCRIBE_LINK' => $strMainID . '_subscribe',
                'SUBSCRIBE_DELETE_LINK' => $strMainID . '_delete_subscribe',

                'PRICE' => $strMainID . '_price',
                'DSC_PERC' => $strMainID . '_dsc_perc',
                'SECOND_DSC_PERC' => $strMainID . '_second_dsc_perc',

                'PROP_DIV' => $strMainID . '_sku_tree',
                'PROP' => $strMainID . '_prop_',
                'DISPLAY_PROP_DIV' => $strMainID . '_sku_prop',
                'BASKET_PROP_DIV' => $strMainID . '_basket_prop'
        );
        
        $strObName = 'ob' . preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);
        
        $arPrice = CCatalogProduct::GetOptimalPrice($arItem["ID"], 1, $USER->GetUserGroupArray(), 'N');
        $arItem["MIN_PRICE"] = array(
            "VALUE" => $arPrice["RESULT_PRICE"]["BASE_PRICE"],
            "DISCOUNT_VALUE" => $arPrice["DISCOUNT_PRICE"],
            "PRINT_VALUE" => CurrencyFormat($arPrice["RESULT_PRICE"]["BASE_PRICE"], $arPrice["RESULT_PRICE"]["CURRENCY"]),
            "PRINT_DISCOUNT_VALUE" => CurrencyFormat($arPrice["DISCOUNT_PRICE"], $arPrice["RESULT_PRICE"]["CURRENCY"])
        );
        foreach ($arItem["OFFERS"] as $keyOffer => $arOffer) {
            $arPrice = CCatalogProduct::GetOptimalPrice($arOffer["ID"], 1, $USER->GetUserGroupArray(), 'N');
            $arItem["OFFERS"][$keyOffer]["MIN_PRICE"]  = array(
                "VALUE" => $arPrice["RESULT_PRICE"]["BASE_PRICE"],
                "DISCOUNT_VALUE" => $arPrice["DISCOUNT_PRICE"],
                "PRINT_VALUE" => CurrencyFormat($arPrice["RESULT_PRICE"]["BASE_PRICE"], $arPrice["RESULT_PRICE"]["CURRENCY"]),
                "PRINT_DISCOUNT_VALUE" => CurrencyFormat($arPrice["DISCOUNT_PRICE"], $arPrice["RESULT_PRICE"]["CURRENCY"])
            );
        }
        $arParams["AREA_ID"] = $strMainID;?>
            <?if (count($arItem["OFFERS"]) <= 0) {
                $arJSParams = array(
                    'PRODUCT_TYPE' => $arItem['CATALOG_TYPE'],
                    'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
                    'SHOW_ADD_BASKET_BTN' => false,
                    'SHOW_BUY_BTN' => true,
                    'SHOW_ABSENT' => true,
                    'PRODUCT' => array(
                            'ID' => $arItem['ID'],
                            'NAME' => $arItem['~NAME'],
                            'PICT' => ('Y' == $arItem['SECOND_PICT']?$arItem['PREVIEW_PICTURE_SECOND']:$arItem['PREVIEW_PICTURE']),
                            'CAN_BUY' => $arItem["CAN_BUY"],
                            'SUBSCRIPTION' => ('Y' == $arItem['CATALOG_SUBSCRIPTION']),
                            'CHECK_QUANTITY' => $arItem['CHECK_QUANTITY'],
                            'MAX_QUANTITY' => $arItem['CATALOG_QUANTITY'],
                            'STEP_QUANTITY' => $arItem['CATALOG_MEASURE_RATIO'],
                            'QUANTITY_FLOAT' => is_double($arItem['CATALOG_MEASURE_RATIO']),
                            'ADD_URL' => $arItem['~ADD_URL'],
                            'SUBSCRIBE_URL' => $arItem['~SUBSCRIBE_URL'],
                            'LIST_SUBSCRIBE_ID' => $arParams['LIST_SUBSCRIPTIONS'],
                    ),
                    'BASKET' => array(
                            'ADD_PROPS' => ('Y' == $arParams['ADD_PROPERTIES_TO_BASKET']),
                            'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
                            'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE'],
                            'EMPTY_PROPS' => $emptyProductProperties
                    ),
                    'VISUAL' => array(
                            'ID' => $arItemIDs['ID'],
                            'PICT_ID' => ('Y' == $arItem['SECOND_PICT'] ? $arItemIDs['SECOND_PICT'] : $arItemIDs['PICT']),
                            'QUANTITY_ID' => $arItemIDs['QUANTITY'],
                            'QUANTITY_UP_ID' => $arItemIDs['QUANTITY_UP'],
                            'QUANTITY_DOWN_ID' => $arItemIDs['QUANTITY_DOWN'],
                            'PRICE_ID' => $arItemIDs['PRICE'],
                            'BUY_ID' => $arItemIDs['BUY_LINK'],
                            'BASKET_PROP_DIV' => $arItemIDs['BASKET_PROP_DIV'],
                            'DELETE_SUBSCRIBE_ID' => $arItemIDs['SUBSCRIBE_DELETE_LINK'],
                    ),
                    'LAST_ELEMENT' => $arItem['LAST_ELEMENT'],
                );?>
                <div id="<?=$strMainID?>" class="t_<?=$unicumID?> col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <?$elementDraw->showElement("unsubscribe", $arItem, $arParams);?>
                </div>
                <script type="text/javascript">
                    var <?=$strObName;?> = new JCCatalogProductSubscribeList(
                            <?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
                </script>
            <?} else {
                foreach ($arItem["OFFERS"] as $keyOffer => $arOffer) {
                    $strMainID = $this->GetEditAreaId($arOffer['ID']);
                    $arParams["AREA_ID"] = $strMainID;
                    $arItemIDs = array(
                        'ID' => $strMainID,
                        'PICT' => $strMainID . '_pict',
                        'SECOND_PICT' => $strMainID . '_secondpict',
                        'MAIN_PROPS' => $strMainID . '_main_props',

                        'QUANTITY' => $strMainID . '_quantity',
                        'QUANTITY_DOWN' => $strMainID . '_quant_down',
                        'QUANTITY_UP' => $strMainID . '_quant_up',
                        'QUANTITY_MEASURE' => $strMainID . '_quant_measure',
                        'BUY_LINK' => $strMainID . '_buy_link',
                        'SUBSCRIBE_LINK' => $strMainID . '_subscribe',
                        'SUBSCRIBE_DELETE_LINK' => $strMainID . '_delete_subscribe',

                        'PRICE' => $strMainID . '_price',
                        'DSC_PERC' => $strMainID . '_dsc_perc',
                        'SECOND_DSC_PERC' => $strMainID . '_second_dsc_perc',

                        'PROP_DIV' => $strMainID . '_sku_tree',
                        'PROP' => $strMainID . '_prop_',
                        'DISPLAY_PROP_DIV' => $strMainID . '_sku_prop',
                        'BASKET_PROP_DIV' => $strMainID . '_basket_prop'
                    );
                    $arOffer['DETAIL_PAGE_URL'] = $arItem['DETAIL_PAGE_URL'];
                    $strObName = 'ob' . preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);
                    $arJSParams = array(
                        'PRODUCT_TYPE' => 1,
                        'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
                        'SHOW_ADD_BASKET_BTN' => false,
                        'SHOW_BUY_BTN' => true,
                        'SHOW_ABSENT' => true,
                        'SHOW_SKU_PROPS' => $arOffer['OFFERS_PROPS_DISPLAY'],
                        'SECOND_PICT' => ($arParams['SHOW_IMAGE'] == "Y" ? $arOffer['SECOND_PICT'] : false),
                        'SHOW_OLD_PRICE' => ('Y' == $arParams['SHOW_OLD_PRICE']),
                        'SHOW_DISCOUNT_PERCENT' => ('Y' == $arParams['SHOW_DISCOUNT_PERCENT']),
                        'DEFAULT_PICTURE' => array(
                                'PICTURE' => $arOffer['PRODUCT_PREVIEW'],
                                'PICTURE_SECOND' => $arOffer['PRODUCT_PREVIEW_SECOND']
                        ),
                        'VISUAL' => array(
                                'ID' => $arItemIDs['ID'],
                                'PICT_ID' => $arItemIDs['PICT'],
                                'SECOND_PICT_ID' => $arItemIDs['SECOND_PICT'],
                                'QUANTITY_ID' => $arItemIDs['QUANTITY'],
                                'QUANTITY_UP_ID' => $arItemIDs['QUANTITY_UP'],
                                'QUANTITY_DOWN_ID' => $arItemIDs['QUANTITY_DOWN'],
                                'QUANTITY_MEASURE' => $arItemIDs['QUANTITY_MEASURE'],
                                'PRICE_ID' => $arItemIDs['PRICE'],
                                'TREE_ID' => $arItemIDs['PROP_DIV'],
                                'TREE_ITEM_ID' => $arItemIDs['PROP'],
                                'BUY_ID' => $arItemIDs['BUY_LINK'],
                                'ADD_BASKET_ID' => $arItemIDs['ADD_BASKET_ID'],
                                'DSC_PERC' => $arItemIDs['DSC_PERC'],
                                'SECOND_DSC_PERC' => $arItemIDs['SECOND_DSC_PERC'],
                                'DISPLAY_PROP_DIV' => $arItemIDs['DISPLAY_PROP_DIV'],
                                'DELETE_SUBSCRIBE_ID' => $arItemIDs['SUBSCRIBE_DELETE_LINK'],
                        ),
                        'BASKET' => array(
                                'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
                                'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE']
                        ),
                        'PRODUCT' => array(
                                'ID' => $arOffer['ID'],
                                'NAME' => $arOffer['~NAME'],
                                'LIST_SUBSCRIBE_ID' => $arParams['LIST_SUBSCRIPTIONS'],
                        ),
                        'LAST_ELEMENT' => $arItem['LAST_ELEMENT'],
                    );?>
                    <div id="<?=$strMainID?>" class="<?=$strObName;?> t_<?=$unicumID?> col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <?$elementDraw->showElement("unsubscribe", $arOffer, $arParams);?>
                    </div>
                    <script type="text/javascript">
			var <?=$strObName;?> = new JCCatalogProductSubscribeList(
				<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
                    </script>
            <?
                }
            }?>
    <?}?>
<?
}
else
{
	if(isset($arParams['GUEST_ACCESS'])):
		echo '<h3>'.Loc::getMessage('CPSL_SUBSCRIBE_NOT_FOUND').'</h3>';
	endif;
}
?>