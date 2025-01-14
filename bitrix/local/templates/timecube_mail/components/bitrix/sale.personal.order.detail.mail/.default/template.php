<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
foreach ($arResult["ORDER_PROPS"] as $prop) {

    if ($prop["CODE"] == 'PHONE') $user_phone = $prop['VALUE'];
    if ($prop["CODE"] == 'EMAIL') $user_email = $prop['VALUE'];
}
$user_name = $arResult['USER_NAME'];
?>
<?if(strlen($arResult["ERROR_MESSAGE"])):?>
    <?=ShowError($arResult["ERROR_MESSAGE"]);?>
<?else:?>
    <?if($arParams["SHOW_ORDER_BASE"]=='Y' || $arParams["SHOW_ORDER_USER"]=='Y' || $arParams["SHOW_ORDER_PARAMS"]=='Y' || $arParams["SHOW_ORDER_BUYER"]=='Y' || $arParams["SHOW_ORDER_DELIVERY"]=='Y' || $arParams["SHOW_ORDER_PAYMENT"]=='Y'):?>
        <table cellpadding="0" cellspacing="0" width="600" align="center">
            <tbody><tr>
                <td width="240" align="left" valign="top" style="padding-bottom: 8px; padding-top: 12px; padding-left: 50px; padding-right: 10px;">
                    <span style="font-size: 15px; line-height: 20px; color: #000000; font-family: Tahoma, Arial;">
                        <strong>Ваш заказ номер:</strong>
                        <a target="_blank" href="//<?=SITE_SERVER_NAME?>" style="color: #71852a; text-decoration: none;"><span style="color: #71852a;"><?=$arParams['ID']?></span></a> <br>
                        <strong>Стоимость заказа:</strong> <?=intval($arResult["PRICE"])?> руб.<br>
                        <strong>ФИО:</strong> <?= $user_name ?><br>
                        <strong>E-mail:</strong>
                        <a style="color: #000 !important; text-decoration: none !important;"href="mailto:<?= $user_email ?>"><?= $user_email ?></a><br>
                        <strong>Телефон:</strong> <?= $user_phone ?>
                        <?//=$arParams['~PASSWORD']?>
                    </span>
                </td>
                <td width="250" align="left" valign="top" style="padding-bottom: 8px; padding-top: 12px; padding-right: 50px;">
                    <span style="font-size: 15px; line-height: 20px; color: #000000; font-family: Tahoma, Arial;">
                        <strong>Тип доставки:</strong> <?=htmlspecialcharsbx($arResult["DELIVERY_NAME"])?><br>
                        <?foreach ($arResult["ORDER_PROPS"] as $prop) {
                            if ($prop["CODE"] == 'LOCATION')
                                $location = $prop['VALUE'];

                            if ($prop["CODE"] == 'ADDRESS')
                                $address = $prop['VALUE'];

                            if ($prop["CODE"] == 'ADDRESS')
                                $address = $prop['VALUE'];
                        }

                        $addressValue =  $location . ', ' . $address;
                        ?><strong>Адрес доставки:</strong> <?=$addressValue?><br>
                        <?if (!empty($arResult['SHIPMENT'][0]["PARAMS"]['DELIVERY_TIME'])){?>
                        <strong>Срок доставки:</strong> <?=$arResult['SHIPMENT'][0]["PARAMS"]['DELIVERY_TIME']?>,<br>
                            не включая день размещения заказа, а также выходные и праздничные дни.<br>
                        <?}?>
                        <strong>Оплата:</strong> <?=htmlspecialcharsbx($arResult["PAY_SYSTEM"]["NAME"])?>
                    </span>
                </td>
            </tr>
            <?if (!empty($arResult["USER_DESCRIPTION"])) {?>
                <tr>
                    <td width="500" align="left" valign="top" colspan="2" style="padding-bottom: 18px; padding-top: 12px; padding-left: 50px; padding-right: 50px;">
                    <span style="font-size: 15px; line-height: 20px; color: #000000; font-family: Tahoma, Arial; text-align: justify; display: block;">
                        <strong>Комментарий:</strong> <?=$arResult["USER_DESCRIPTION"]?>
                    </span>
                    </td>
                </tr>
            <?}?>
            </tbody>
        </table>
        <?if($arParams["SHOW_ORDER_BASKET"]=='Y'):?>
            <table cellpadding="0" cellspacing="0" width="600" align="center">
                <tbody><tr>
                    <td width="600" height="40" align="left" style="padding-left: 50px; background-color: #e8e8e8;"><span style="font-size: 18px; line-height: 40px; color: #000000; font-family: Tahoma, Arial; font-weight: bold;">Состав заказа:</span></td>
                </tr>
                </tbody>
            </table>
            <table cellpadding="0" cellspacing="0" width="600" align="center">
                <tbody><tr>
                    <td width="600" colspan="2" height="14"></td>
                </tr>
                <?foreach($arResult["BASKET"] as $prod):?>
                    <?$hasLink = !empty($prod["DETAIL_PAGE_URL"]);
                    $actuallyHasProps = is_array($prod["PROPS"]) && !empty($prod["PROPS"]);
                    ?>
                    <tr>
                        <td width="180" align="left" valign="top" style="padding-left: 50px; padding-right: 28px;">
                            <?if($prod['PICTURE']['SRC']):?>
                                <a target="_blank" href="<?=$prod["DETAIL_PAGE_URL"]?>"><img style="display: block; border-color: #e8e8e8;" width="180" border="1" src="https://<?=SITE_SERVER_NAME . $prod['PICTURE']['SRC']?>" alt="<?=$prod['NAME']?>"></a>
                            <?endif;?>
                        </td>
                        <td width="300" align="left" valign="top" style="padding-right: 42px;">
                            <span style="display: block; padding-bottom: 12px; font-size: 15px; line-height: 20px; color: #000000; font-family: Tahoma, Arial;">
                                <b>Наименование товара:</b> <?=$prod['PROPERTY_CML2_ARTICLE_VALUE']?> <br>
                                <a target="_blank" href="<?=$prod["DETAIL_PAGE_URL"]?>" style="color: #71852a; text-decoration: underline;">
                                    <span style="color: #71852a;"><?=$prod['NAME']?></span>
                                </a>
                            </span>
                            <span style="display: block; padding-bottom: 12px; font-size: 15px; line-height: 20px; color: #000000; font-family: Tahoma, Arial;">
                                <b>Цена:</b> <span style="display: inline-block; position: relative;"><?=intval($prod["PRICE"])?> руб.
                                    <?if ($prod['BASE_PRICE'] != $prod['PRICE']) {?>
                                        <strike style="display: inline-block; position: absolute; left: 0; top: -14px; color: #ff0000; font-size: 12px;"><?=intval($prod['BASE_PRICE'])?> руб.</strike>
                                    <?}?>
                                </span>
                            </span>
                            <span style="display: block; padding-bottom: 12px; font-size: 15px; line-height: 20px; color: #000000; font-family: Tahoma, Arial;">
                                <b>Количество:</b> <?=$prod["QUANTITY"]?></span>
                            <?/*<span style="display: block; padding-bottom: 12px; font-size: 15px; line-height: 20px; color: #000000; font-family: Tahoma, Arial;"><b>Сумма:</b>
                                <span style="display: inline-block; position: relative;"><?=intval($prod['PRICE']*$prod['QUANTITY'])?> р
                                    <?if ($prod['BASE_PRICE'] != $prod['PRICE']) {?>
                                        <strike style="display: inline-block; position: absolute; left: 0; top: -14px; color: #ff0000; font-size: 12px;"><?=intval($prod['BASE_PRICE']*$prod['QUANTITY'])?> р</strike>
                                    <?}?>
                                </span>
                            </span>*/?>
                        </td>
                    </tr>
                    <tr>
                        <td width="600" colspan="2" height="11"></td>
                    </tr>
                <?endforeach;?>
                </tbody>
            </table>

            <table cellpadding="0" cellspacing="0" width="600" align="center">
                <tbody><tr>
                    <td width="240"></td>
                    <td width="360">
                        <table cellpadding="0" cellspacing="0" width="360" align="center">
                            <tbody><tr>
                                <td width="360" align="left" valign="top">
                                    <span style="display: block; height: 27px; background-color: #71852a; color: #fff; font-size: 14px; line-height: 27px; padding-left: 20px; font-family: Tahoma, Arial;">ИТОГО:</span>
                                </td>
                            </tr>
                            </tbody></table>
                        <table cellpadding="0" cellspacing="0" width="360" align="center">
                            <tbody><tr>
                                <td width="170" align="left" valign="top" style="padding-left: 17px; padding-top: 12px;">
                                    <span style="color: #000; font-size: 15px; line-height: 20px; font-family: Tahoma, Arial; font-weight: bold;">Сумма:</span>
                                </td>
                                <td width="115" align="right" valign="top" style="padding-right: 58px; padding-top: 12px;">
                                    <span style="color: #000; font-size: 15px; line-height: 20px; font-family: Tahoma, Arial; display: inline-block; position: relative;"><?=$arResult['PRODUCT_SUM']?> руб.</span>
                                </td>
                            </tr>
                            <tr>
                                <td width="170" align="left" valign="top" style="padding-left: 17px; padding-bottom: 8px;">
                                    <span style="color: #000; font-size: 15px; line-height: 20px; font-family: Tahoma, Arial; font-weight: bold;">Доставка:</span>
                                </td>
                                <td width="115" align="right" valign="top" style="padding-right: 58px; padding-bottom: 8px;">
                                    <span style="color: #000; font-size: 15px; line-height: 20px; font-family: Tahoma, Arial;"><?=intval($arResult["PRICE_DELIVERY"])?> руб.</span>
                                </td>
                            </tr>
                            <tr>
                                <td width="170" height="1" colspan="2" style="background-color: #71852a;"></td>
                            </tr>
                            <tr>
                                <td width="170" align="left" valign="top" style="padding-left: 17px; padding-top: 4px; padding-bottom: 14px;">
                                    <span style="color: #000; font-size: 15px; line-height: 20px; font-family: Tahoma, Arial; font-weight: bold;">Всего к оплате:</span>
                                </td>
                                <td width="115" align="right" valign="top" style="padding-right: 58px; padding-top: 4px; padding-bottom: 14px;">
                                    <span style="color: #000; font-size: 15px; line-height: 20px; font-family: Tahoma, Arial;"><?=intval($arResult["PRICE"])?> руб.</span>
                                </td>
                            </tr>
                            </tbody></table>
                    </td>
                </tr>
                </tbody>
            </table>
        <?endif?>
    <?endif?>
<?endif?>
