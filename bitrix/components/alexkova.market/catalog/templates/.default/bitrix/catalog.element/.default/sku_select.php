<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if ($arParams["OFFERS_VIEW"] == "CHOISE") {?>
    <div class="sku-choise-wrap">
        <?foreach ($arResult["SKU_PROPS_LIST"] as $code => $prop) {?>
            <div class="sku-prop" data-prop-id="<?=$prop["ID"]?>" data-prop-code="<?=$prop["CODE"]?>" data-prop-name="<?=$prop["NAME"]?>" data-prop-type="<?=$prop["PROPERTY_TYPE"]?>">
                <div class="sku-prop-name">
                    <?=GetMessage('CHOOSE').' '.mb_strtolower($prop["NAME"], LANG_CHARSET);?>:
                </div>
                <ul class="sku-prop-values-list">
                    <?foreach ($prop["VALUES"] as $valId => $propVal) {
                        $valueType = ($propVal["FILE"]) ? "prop-img-wrap" : "prop-text-wrap";
                        $addClass = ($arParams["SKU_PROPS_SHOW_TYPE"] == "rounded") ? "rounded" : "";?>
                        <li class="sku-prop-value <?=$valueType?> <?=$addClass?>" data-prop-code="<?=$code?>" data-prop-type="<?=$prop["PROPERTY_TYPE"]?>" data-val-id="<?=$propVal["ID"]?>" data-val-code="<?if(!empty($propVal["XML_ID"])) echo $propVal["XML_ID"]; else echo $propVal["NAME"];?>" data-val-name="<?=$propVal["NAME"]?>">
                            <?if ($propVal["FILE"]) {?>
                                <img src="<?=$propVal["FILE"]?>" alt="<?=$propVal["NAME"]?>" title="<?echo $prop["NAME"].": ".($propVal["DESCRIPTION"]?$propVal["DESCRIPTION"]:$propVal["NAME"])?>">
                            <?} else {
                                echo $propVal["NAME"];
                            }?>
                        </li>
                    <?}?>
                </ul>
            </div>
            <div class="clearfix"></div>
        <?}?>
        <div  class="offers-cnt"></div>
    </div>
<?}
if ($arParams["OFFERS_VIEW"] == "SELECT") {?>
    <div class="bxr-sku-select-wrap" data-pid="<?=$chosenColor["ID"]?>">
        <i class="fa fa-chevron-down"></i>
        <div class="bxr-sku-select-chosen-inner">
            <?=GetMessage('CHOISE_OFFER')?>
        </div>
        <hr>
        <?  
            global $firstScuSelect;
            $firstScuSelect = 0;
        ?>
        <ul class="bxr-sku-select-items">
            <?foreach($arResult["OFFERS"] as $offer):
                if (is_array($offer["PREVIEW_PICTURE"])) {
                    $src = $offer["PREVIEW_PICTURE"]["SRC"];
                } elseif (intval($offer["PREVIEW_PICTURE"]) > 0) {
                    $src = CFile::GetPath($offer["PREVIEW_PICTURE"]);
                } elseif (is_array($offer["DETAIL_PICTURE"])) {
                    $src = $offer["DETAIL_PICTURE"]["SRC"];
                } elseif (intval($offer["DETAIL_PICTURE"]) > 0) {
                    $src = CFile::GetPath($offer["DETAIL_PICTURE"]);
                } elseif ($offer["MORE_PHOTO"][0]["SRC"] && $offer["MORE_PHOTO"][0]["TYPE"] != "NO_PHOTO") {
                    $src = $offer["MORE_PHOTO"][0]["SRC"];
                } elseif ($arResult["MORE_PHOTO"][0]["SRC"] && $arParams["SHOW_MAIN_INSTEAD_NF_SKU"] == "Y") {
                    $src = $arResult["MORE_PHOTO"][0]["SRC"];
                } else {
                    $src = '/bitrix/tools/bxready/.default/no-image.png';
                }
            ?>   
                <li class="bxr-sku-select-item" data-pid="<?=$offer['ID']?>">
                    <div class="bxr-offers-ico">
                        <img src="<?=$src?>" alt="<?=$offer["NAME"]?>" title="<?=$offer["NAME"]?>">
                    </div>
                    <div class="bxr-offers-props">
                        <?$propsStr = "";
                        foreach($offer["PROPERTIES"] as $propCode => $arProp):
                            if (array_key_exists($propCode, $arResult["OFFERS_PROP"])): 
                                $sPropId = $arResult["SKU_PROPS"][$propCode]["XML_MAP"][$arProp["VALUE"]];
                                if ($arProp["PROPERTY_TYPE"] == "E") {
                                    $printValue = $arProp["NAME"].": ".$arResult["SKU_PROPS"][$propCode]["VALUES"][$arProp["VALUE"]]["NAME"];
                                } else if ($arProp["PROPERTY_TYPE"] == "S") {
                                    $printValue = $arProp["NAME"].": ".$arResult["SKU_PROPS"][$propCode]["VALUES"][$sPropId]["NAME"];
                                } else {
                                    $printValue = $arProp["NAME"].": ".$arProp["VALUE"];
                                }
                                
                                $propsStr .= $printValue.", ";
                            endif;
                        endforeach;
			$propStr = rtrim($propsStr, ', ');
			if (strlen($propStr) > 0) :
				$propStr = ' (<span class="bxr-sku-prop-brackets">'.$propStr.'</span>)';
			endif;
			?>
                        <span class="bxr-offer-props-name"><?=$offer["NAME"]?></span><?=$propStr?>
                    </div>
                    <div class="clearfix"></div>
                </li>
            <?endforeach;?>
        </ul>
    </div>
<?}
if ($arParams["OFFERS_VIEW"] == "ICONS") {?>
    <div class="bxr-sku-icons-items-block">
        <ul class="bxr-sku-icons-items">
            <?foreach($arResult["OFFERS"] as $offer):
                if (is_array($offer["PREVIEW_PICTURE"])) {
                    $src = $offer["PREVIEW_PICTURE"]["SRC"];
                } elseif (intval($offer["PREVIEW_PICTURE"]) > 0) {
                    $src = CFile::GetPath($offer["PREVIEW_PICTURE"]);
                } elseif (is_array($offer["DETAIL_PICTURE"])) {
                    $src = $offer["DETAIL_PICTURE"]["SRC"];
                } elseif (intval($offer["DETAIL_PICTURE"]) > 0) {
                    $src = CFile::GetPath($offer["DETAIL_PICTURE"]);
                } elseif ($offer["MORE_PHOTO"][0]["SRC"] && $offer["MORE_PHOTO"][0]["TYPE"] != "NO_PHOTO") {
                    $src = $offer["MORE_PHOTO"][0]["SRC"];
                } elseif ($arResult["MORE_PHOTO"][0]["SRC"] && $arParams["SHOW_MAIN_INSTEAD_NF_SKU"] == "Y") {
                    $src = $arResult["MORE_PHOTO"][0]["SRC"];
                } else {
                    $src = $elementDraw->getDefaultImage();
                }
                ?>
                <li class="bxr-sku-icons-item" data-pid="<?=$offer['ID']?>">
                    <div class="bxr-offers-ico">
                        <img src="<?=$src?>" alt="<?=$offer['NAME']?>" title="<?=$offer['NAME']?>">
                    </div>
                    <input type="hidden" value="<?=$offer['NAME']?>" class="bxr-icons-offer-name">
                    <div class="bxr-offers-props">
                        <?$propsStr = "";
                        foreach($offer["PROPERTIES"] as $propCode => $arProp):
                            if (array_key_exists($propCode, $arResult["OFFERS_PROP"])): 
                                $sPropId = $arResult["SKU_PROPS"][$propCode]["XML_MAP"][$arProp["VALUE"]];
                                if ($arProp["PROPERTY_TYPE"] == "E") {
                                    $printValue = $arProp["NAME"].": ".$arResult["SKU_PROPS"][$propCode]["VALUES"][$arProp["VALUE"]]["NAME"];
                                } else if ($arProp["PROPERTY_TYPE"] == "S") {
                                    $printValue = $arProp["NAME"].": ".$arResult["SKU_PROPS"][$propCode]["VALUES"][$sPropId]["NAME"];
                                } else {
                                    $printValue = $arProp["NAME"].": ".$arProp["VALUE"];
                                }
                                
                                $propsStr .= $printValue.", ";
                            endif;
                        endforeach;
			$propStr = rtrim($propsStr, ', ');
			if (strlen($propStr) > 0) :
				$propStr = ' (<span class="bxr-sku-prop-brackets">'.$propStr.'</span>)';
			endif;
			?>
                        <span class="bxr-offer-props-name"><?=$offer["NAME"]?></span><?=$propStr?>
                    </div>
                </li>
            <?endforeach;?>
        </ul>
        <div class="clearfix"></div>
        <div  class="offers-cnt"></div>
    </div>
<? }