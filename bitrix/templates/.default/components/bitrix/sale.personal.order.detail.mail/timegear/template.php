
<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=600">
</head>

<body style="margin: 0px;">
<? if (\Bitrix\Main\Loader::includeModule('mail')) : ?>
    <?=\Bitrix\Mail\Message::getQuoteStartMarker(true); ?>
<? endif; ?>
<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
    <tr>
        <td width="100%" bgcolor="#ffffff" valign="top" align="center" style="padding: 15px 0px 15px 0px;">
            <table cellpadding="0" cellspacing="0" width="600" align="center">
                <tbody>
                <tr>
                    <td width="600" height="47" align="center" style="padding-bottom: 36px; padding-top: 36px;">
                        <a target="_blank" href="//timegear.ru"><img width="278" src="//timegear.ru/local/templates/timegear_mail/img/lo.png" height="47" border="0" alt=""></a>
                    </td>
                </tr>
                <tr>
                    <td width="600" align="center" style="padding-bottom: 36px; padding-left: 15px; padding-right: 15px;">
 <span style="font-size: 24px; line-height: 30px; color: #000000; font-family: Tahoma, Arial;">Уважаемый покупатель <?=$arResult['FIO']?>, <br>
		 Спасибо за покупку на сайте <a target="_blank" href="//timegear.ru" style="color: #71852a !important; text-decoration: underline;"><span style="font-size: 24px; line-height: 30px; color: #71852a !important; font-family: Tahoma, Arial;">timegear.ru!</span></a></span>
                    </td>
                </tr>
                <tr>
                    <td width="600" height="40" align="left" style="padding-left: 50px; background-color: #e8e8e8;">
                        <span style="font-size: 18px; line-height: 40px; color: #000000; font-family: Tahoma, Arial; font-weight: bold;">Детали заказа:</span>
                    </td>
                </tr>
                </tbody>
            </table>

<?
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
                        <a target="_blank" href="//timegear.ru" style="color: #71852a; text-decoration: none;"><span style="color: #71852a;"><?=$arParams['ID']?></span></a> <br>
                        <strong>Стоимость заказа:</strong> <?=intval($arResult["PRICE"])?> &#8381;<br>
                        <strong>ФИО:</strong> <?= $user_name ?><br>
                        <strong>E-mail:</strong>
                        <a style="color: #000 !important; text-decoration: none !important;"href="mailto:<?= $user_email ?>"><?= $user_email ?></a><br>
                        <strong>Телефон:</strong> <?= $user_phone ?>
                        <?=$arParams['~PASSWORD']?>
                    </span>
                </td>
                <td width="250" align="left" valign="top" style="padding-bottom: 8px; padding-top: 12px; padding-right: 50px;">
                    <span style="font-size: 15px; line-height: 20px; color: #000000; font-family: Tahoma, Arial;">
                        <strong>Тип доставки:</strong> <?=htmlspecialcharsbx($arResult["DELIVERY_NAME"])?><br>
                        <?foreach ($arResult["ORDER_PROPS"] as $prop) {
                            if ($prop["CODE"] == 'LOCATION') {
                                $location = $prop['VALUE'];
                            }
                            if ($prop["CODE"] == 'ADDRESS') {
                                $address = $prop['VALUE'];
                            }
                        }
                        $addressValue =  $location . ', ' . $address;
                        ?><strong>Адрес доставки:</strong> <?=$addressValue?><br>
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
                                <a target="_blank" href="//timegear.ru<?=$prod["DETAIL_PAGE_URL"]?>">
                                    <img style="display: block; border-color: #e8e8e8;" width="180" border="1" src="//timegear.ru<?=$prod['PICTURE']['SRC']?>" alt="<?=$prod['NAME']?>"></a>
                            <?endif;?>
                        </td>
                        <td width="300" align="left" valign="top" style="padding-right: 42px;">
                            <span style="display: block; padding-bottom: 12px; font-size: 15px; line-height: 20px; color: #000000; font-family: Tahoma, Arial;">
                                <b>Наименование товара:</b><br>
                                <a target="_blank" href="//timegear.ru<?=$prod["DETAIL_PAGE_URL"]?>" style="color: #71852a; text-decoration: underline;">
                                    <span style="color: #71852a;"><?=$prod['NAME']?></span>
                                </a>
                            </span>
                            <span style="display: block; padding-bottom: 12px; font-size: 15px; line-height: 20px; color: #000000; font-family: Tahoma, Arial;">
                                <b>Артикул:</b> <span style="display: inline-block; position: relative;"> <?=$prod['PROPERTY_CML2_ARTICLE_VALUE']?> 
                                </span>
                            </span>
                            <span style="display: block; padding-bottom: 12px; font-size: 15px; line-height: 20px; color: #000000; font-family: Tahoma, Arial;">
                                <b>Цена:</b> <span style="display: inline-block; position: relative;"><?=intval($prod["PRICE"])?> &#8381;
                                    <?if ($prod['BASE_PRICE'] != $prod['PRICE']) {?>
                                        <strike style="display: inline-block; position: absolute; left: 0; top: -14px; color: #ff0000; font-size: 12px;"><?=intval($prod['BASE_PRICE'])?> &#8381;</strike>
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
                                    <span style="color: #000; font-size: 15px; line-height: 20px; font-family: Tahoma, Arial; display: inline-block; position: relative;"><?=$arResult['PRODUCT_SUM']?> &#8381;</span>
                                </td>
                            </tr>
                            <tr>
                                <td width="170" align="left" valign="top" style="padding-left: 17px; padding-bottom: 8px;">
                                    <span style="color: #000; font-size: 15px; line-height: 20px; font-family: Tahoma, Arial; font-weight: bold;">Доставка:</span>
                                </td>
                                <td width="115" align="right" valign="top" style="padding-right: 58px; padding-bottom: 8px;">
                                    <span style="color: #000; font-size: 15px; line-height: 20px; font-family: Tahoma, Arial;"><?=intval($arResult["PRICE_DELIVERY"])?> &#8381;</span>
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
                                    <span style="color: #000; font-size: 15px; line-height: 20px; font-family: Tahoma, Arial;"><?=intval($arResult["PRICE"])?> &#8381;</span>
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

            <table cellpadding="0" cellspacing="0" width="600" align="center">
                <tr>
                    <td width="492" style="padding-left: 50px; padding-right: 60px; padding-top: 26px; padding-bottom: 14px; background-color: #e7e5d9;">
                        <span style="display: block; padding-bottom: 14px; font-size: 16px; line-height: 20px;  font-family: Tahoma, Arial; color: #000;">При отказе от заказа клиент оплачивает стоимость доставки. <br>В случае бесплатной доставки по Москве и СПБ, <br>сумма оплаты составит 300 и 500 рублей соответственно.</span>
                        <span style="display: block; padding-bottom: 14px; font-size: 16px; line-height: 20px;  font-family: Tahoma, Arial; color: #000;"><b>ВАЖНО!</b> Проверьте товар при курьере. <br>После проверки (отказа от проверки) претензии по внешнему виду не принимаются!</span>
                    </td>
                </tr>
                <tr>
                    <td width="492" height="21"></td>
                </tr>
                <tr>
                    <td width="492" style="padding-left: 50px; padding-right: 60px; padding-top: 26px; padding-bottom: 14px; background-color: #d4d0b9;">
                        <span style="display: block; padding-bottom: 14px; font-size: 16px; line-height: 20px;  font-family: Tahoma, Arial; color: #000;"><b>ЕСТЬ ВОПРОСЫ?</b></span>
                        <span style="display: block; padding-bottom: 14px; font-size: 16px; line-height: 20px;  font-family: Tahoma, Arial; color: #000;">Мы рады ответить на Ваши вопросы по почте или телефону. Пожалуйста, при обращении в службу поддержки указывайте номер заказа.</span>
                    </td>
                </tr>
                <tr>
                    <td width="492" height="21"></td>
                </tr>
                <tr>
                    <td width="502" style="padding-left: 50px; padding-right: 50px; padding-top: 26px; padding-bottom: 14px; background-color: #eae2b4;">
                        <span style="display: block; padding-bottom: 14px; font-size: 16px; line-height: 20px;  font-family: Tahoma, Arial; color: #000;"><b>РЕЖИМ РАБОТЫ И КОНТАКТЫ:</b></span>
                        <span style="display: block; padding-bottom: 14px; font-size: 16px; line-height: 20px;  font-family: Tahoma, Arial; color: #000;">Заказы, сделанные в нерабочее время или праздничные дни, обрабатываются на следующий рабочий день.</span>
                        <span style="display: block; padding-bottom: 14px; font-size: 16px; line-height: 20px;  font-family: Tahoma, Arial; color: #000;">Магазин открыт в рабочие дни  с 9 до 20 часов; <br>в субботу и воскресенье с 11 до 18 часов.</span>
                        <span style="display: block; padding-bottom: 14px; font-size: 16px; line-height: 20px;  font-family: Tahoma, Arial; color: #000;">Адрес: 129629 Москва, Маломосковская 22-1, 2-й этаж, офис 209 <br>м. Алексеевская, 10 минут пешком.</span>
                        <table cellpadding="0" cellspacing="0" width="256" align="left">
                            <tr>
                                <td width="74" valign="top" style="font-size: 16px; line-height: 20px; font-family: Tahoma, Arial; color: #000;">Телефон:</td>
                                <td width="182" valign="top" style="font-size: 16px; line-height: 20px; font-family: Tahoma, Arial; color: #000;">
                                    8 (495) 687-35-18 <br>8 (495) 686-20-36 <br>8 (495) 687-44-37 <br>8 (495) 984-04-83
                                </td>
                            </tr>
                            <tr>
                                <td width="74" valign="top" style="font-size: 16px; line-height: 20px; font-family: Tahoma, Arial; color: #000; padding-bottom: 14px;">Почта:</td>
                                <td width="182" valign="top" style="font-size: 16px; line-height: 20px; font-family: Tahoma, Arial; color: #000; padding-bottom: 14px;">
                                    <a style="color: #000 !important; text-decoration: none !important;" href="mailto:info@timegear.ru">info@timegear.ru</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width="492" height="21"></td>
                </tr>
                <tr>
                    <td width="492" height="26"></td>
                </tr>
            </table>
            <table cellpadding="0" cellspacing="0" width="600" align="center">
                <tr>
                    <td width="600" align="center" style="padding-bottom: 12px;"><span style="text-transform: uppercase; color: #000;
						font-size: 18px; line-height: 24px; font-family: Tahoma, Arial;">Это письмо написано роботом. <br><span style="color: #71852a;">Отвечать на него не нужно.</span></span></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>

</html>