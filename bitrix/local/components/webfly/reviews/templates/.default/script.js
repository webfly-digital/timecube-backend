(function () {
    'use strict';

    if (!!window.WFReviewsComponent) return;

    window.WFReviewsComponent = function (container, signedTemplate, signedParams) {
        this.signedTemplate = signedTemplate;
        this.signedParams = signedParams;

        this.container = document.getElementById(container);
        this.form = this.container.querySelector('[data-entity=form-add]');
        this.btnAdd = this.container.querySelector('[data-entity=btn-add]');
        this.elementInput = this.container.querySelector('[name=element_id]');
        if (this.elementInput) this.elementId = this.elementInput.value;

        if (this.form && this.btnAdd && this.elementInput) {
            this.init();
        }
        BX.ready(BX.delegate(this.initBX, this));
    }

    window.WFReviewsComponent.prototype = {
        init: function () {
            var component = this;

            var popupButtons = [
                new BX.PopupWindowButton({
                    text: "OK",
                    className: "btn btn-xs btn-primary",
                    events: {click: function(){
                            this.popupWindow.close();
                        }}
                })
            ];

            this.form.onsubmit = function (e) {
                e.preventDefault();
                var form = e.currentTarget;
                var wait = BX.showWait(form);
                BX.ajax.runComponentAction('webfly:reviews', 'addReview', { // Вызывается без постфикса Action
                    mode: 'class', data: {
                        signedParams: component.signedParams,
                        message: form.message.value,
                        rate: form.rate.value,
                    }, // ключи объекта data соответствуют параметрам метода
                }).then(function (response) {
                    component.ajaxReload();

                    component.popup.setContent('<p>Отзыв будет опубликован после проверки модератором</p>');
                    component.popup.setButtons(popupButtons);

                    console.log(response);
                    BX.closeWait(form, wait);
                }, function (response) {
                    component.popup.setContent('<p>Не удалось добавить отзыв, попробуйте позже</p>');
                    component.popup.setButtons(popupButtons);

                    console.log(response);
                    BX.closeWait(form, wait);
                });
            }
        },

        ajaxReload: function() {
            var ajaxReload = this.container.querySelector('a[data-entity=a-reload]');
            ajaxReload.click();
        },

        initBX: function () {
            this.popup = new BX.PopupWindow(this.btnAdd, window.body, {
                autoHide: true,
                closeIcon: true,
                closeByEsc: true,
                overlay: {backgroundColor: 'gray', opacity: '70'}
            });
            this.popup.setContent(BX(this.form));
            BX.bind(this.btnAdd, 'click', BX.proxy(this.showPopup, this));
        },

        showPopup: function () {
            this.popup.show();
        },

        ajaxAction: function () {
            BX.ajax.runComponentAction('webfly:reviews', 'ajax', {
                mode: 'class',
                data: {
                    pid: this.pid,
                    signedTemplate: this.signedTemplate,
                    signedParams: this.signedParams
                },
            }).then(function (response) {
                console.log(response);
            }, function (response) {
                console.log(response);
            });
        }
    }

})();