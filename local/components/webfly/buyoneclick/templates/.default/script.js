function buyOneClick(event) {

    var btn = event.currentTarget,
        form = document.getElementById('wf_b1c_form');

    window.PRODUCT_ID = btn.dataset.pid; //id продукта глобально

    if (!window.WFB1CPopup) {
        //создаём попап
        window.WFB1CPopup = new BX.PopupWindow(btn, window.body, {
            autoHide: true,
            closeIcon: true,
            closeByEsc: true,
            overlay: {backgroundColor: 'gray', opacity: '70'}
        });

        //форму в попап
        WFB1CPopup.setContent(BX(form));

        //слушаем событие отправки и вызываем свой обработчик
        form.addEventListener('submit', b1cFormSubmit);
    }

    WFB1CPopup.show();
}

function b1cFormSubmit(e) {
    e.preventDefault();
    var form = e.target,
        wait = BX.showWait(form);
    let data = {};

    data = {
        pid: PRODUCT_ID,
        name: form.name.value,
        email: form.email.value,
        phone: form.phone.value,
        msg: form.msg.value
    };

    if (form.token && form.action && form.action.value && form.token.value) {
        data["action"] = form.action.value;
        data["token"] = form.token.value;
    }

    console.log(data);
    BX.ajax.runComponentAction('webfly:buyoneclick', 'buyOneClick', {
        mode: 'class',
        data: data,
    }).then(function (response) {
        window.WFB1CPopup.setContent('<div class="wf-popup-header"><p class="wf-popup__title">Успех</p></div>' +
            '<div class="wf-popup-body"><p>Заказ успешно создан. Ожидайте уведомления.</p></div>');
        BX.closeWait(form, wait);
        setTimeout(function () {
            WFB1CPopup.close();
        }, 3500);
    }, function (response) {
        console.log(response);
        window.WFB1CPopup.setContent('<div class="wf-popup-header"><p class="wf-popup__title">Ошибка</p></div>' +
            '<div class="wf-popup-body"><p>Попробуйте позже или напишите нам.</p></div>');
        window.WFB1CPopup.setButtons([okBtn]);
        BX.closeWait(form, wait);
    });

}

