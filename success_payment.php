<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Спасибо за ваш заказ!"); ?>
    <div class="bx-404-container">
        <div class="bx-404-block"><img src="<?= SITE_DIR ?>images/tick.svg" alt=""></div>
        <div class="bx-404-text-block">Спасибо за ваш заказ! <br>Оплата прошла успешно</div>
        <div class="bx-404-text">Наши менеджеры свяжутся с Вами, если возникнут проблемы при обработке заказа.</div>
    </div>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>