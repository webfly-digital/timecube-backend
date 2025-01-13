<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Не удалось обработать Ваш платёж"); ?>
    <div class="bx-404-container">
        <div class="bx-404-block"><img src="<?= SITE_DIR ?>images/close.svg" alt=""></div>
        <div class="bx-404-text-block">Что-то пошло не так :(<br>Нам не удалось обработать Ваш платёж</div>
        <div class="bx-404-text">Просим Вас связаться с нами по телефону или email. Контактные данные и режим работы нашего офиса указаны в разделе <a href="/contacts/">контакты</a>.</div>
    </div>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>