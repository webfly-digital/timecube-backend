function WFCallback(event) {

    var btn = event.currentTarget,
        form = document.getElementById('wf_callback_form');

    window.WF_CALLBACK_PAGE = btn.dataset.page; //id продукта глобально

    if (!window.WFCallbackPopup) {
        //создаём попап
        window.WFCallbackPopup = new BX.PopupWindow(btn, window.body, {
            autoHide: true,
            closeIcon: true,
            closeByEsc: true,
            overlay: {backgroundColor: 'gray', opacity: '70'}
        });

        //форму в попап
        WFCallbackPopup.setContent(BX(form));

        //слушаем событие отправки и вызываем свой обработчик
        form.addEventListener('submit', callbackFormSubmit);
    }

    WFCallbackPopup.show();
}

function callbackFormSubmit(e) {
    e.preventDefault();
    var form = e.target,
        wait = BX.showWait(form);

    BX.ajax.runComponentAction('webfly:callback', 'callback', {
        mode: 'class',
        data: {
            page: WF_CALLBACK_PAGE,
            name: form.name.value,
            phone: form.phone.value,
            msg: form.msg.value
        },
    }).then(function (response) {
        window.WFCallbackPopup.setContent('<div class="wf-popup-header"><p class="wf-popup__title">Успех</p></div>' +
            '<div class="wf-popup-body"><p>Заявка отправлена, ожидайте звонка.</p></div>');
        BX.closeWait(form, wait);
        setTimeout(function () {
            WFCallbackPopup.close();
        }, 3500);
    }, function (response) {
        console.log(response);
        window.WFCallbackPopup.setContent('<div class="wf-popup-header"><p class="wf-popup__title">Ошибка</p></div>' +
            '<div class="wf-popup-body"><p>Попробуйте позже.</p></div>');
        BX.closeWait(form, wait);
        setTimeout(function () {
            WFCallbackPopup.close();
        }, 3500);
    });

}