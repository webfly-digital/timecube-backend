<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
global $USER;
?>
    <button class="btn-inline" onclick="buyOneClick(event)"
            id="wf_buy_one_click"
            data-pid="<?= $arParams['PID'] ?>">Купить в один клик
    </button>
    <div style="display: none">
        <form action="" method="post" id="wf_b1c_form">
            <?= bitrix_sessid_post() ?>
            <? if ($arParams['CAPTCHA'] == 'Y') : ?>
                <input id="token" name="token" value="" hidden>
                <input id="action" name="action" value="" hidden>
            <? endif; ?>
            <div class="review-form">
                <div class="wf-popup-header">
                    <p class="wf-popup__title">Покупка в 1 клик</p>
                </div>
                <div class="review-form__body">
                    <input type="hidden" name="element_id" value="<?= $arParams['PID'] ?>">

                    <div class="form-group">
                        <label for="b1c_phone">Телефон<b style="color: red;">*</b> </label>
                        <input value="" class="input-normally" id="b1c_phone" type="tel" name="phone" required><br>
                    </div>
                    <div class="form-group">
                        <label for="b1c_name">Имя</label>
                        <input value="" class="input-normally" id="b1c_name" type="text" name="name">
                    </div>
                    <div class="form-group">
                        <label for="b1c_email">Email</label>
                        <input value="" class="input-normally" id="b1c_email" type="email" name="email"><br>
                    </div>
                    <div class="form-group">
                        <label for="b1c_msg">Комментарий</label>
                        <textarea id="b1c_msg" name="msg" cols="40" rows="6" style="resize: none"></textarea><br>
                    </div>
                </div>
                <div class="wf-popup-footer">
                    <button type="submit" class="btn btn-primary mx-auto mt-3" id="wf_b1c_submit">Подтвердить</button>
                </div>
            </div>
        </form>
    </div>

<? if ($arParams['CAPTCHA'] == 'Y') : ?>
    <script src="https://www.google.com/recaptcha/api.js?render=6LcO9B8qAAAAAGAEXXYmIjnutmOatWAlWC2DVv3d"></script>
    <script>
        grecaptcha.ready(function () {
            grecaptcha.execute('6LcO9B8qAAAAAGAEXXYmIjnutmOatWAlWC2DVv3d', {action: 'buy_one_click'})
                .then(function (token) {
                    if (BX('token')) BX('token').value = token;
                    if (BX('action')) BX('action').value = 'buy_one_click';
                });
        });
    </script>
<? endif; ?>