$(function () {

    var packData = {
        sessid: BX.bitrix_sessid(),
        productid: undefined,
        packid: undefined,
        basketid: undefined
    };

    /*Попап с упаковкой*/
    $(document).on('click', '.select-pack', function (e) {
        e.preventDefault();
        packData.productid = e.target.dataset.productid; //id товара, к которому упаковка
        packData.basketid = e.target.dataset.basketid; //id корзины

        var freePack = e.target.dataset.freepack;
        /*Попап открывается*/
        $.magnificPopup.open({
            items: {
                src: '#products-popup'
            },
            type: 'inline',
            removalDelay: 350,
            callbacks: {
                open: function(){

                    var content = this.content;

                    if (typeof content !== 'undefined') {
                        if (freePack === 'true')
                            content[0].classList.add('free-price');
                        else
                            content[0].classList.remove('free-price');
                    }
                },
                afterClose: function(){
                    location.reload(); //перезагрузка страницы для обновления корзины
                }
            }
        });

    });

    /*Выбор упаковки*/
    $(document).on('click', '.wf-product-select-pack', function (e) {
        packData.packid = e.target.dataset.productid;
        if (typeof packData.productid !== "undefined" && typeof packData.packid !== "undefined")
            wfSelectProduct(packData);
    });

});

/** wfSelectProduct - отправка запроса на выбор упаковки
 * @param {string} data.sessid
 * @param {numbe|string} data.productid - id товара, к которому привязывать упаковку
 * @param {number|string} data.packid - id выбранной упаковки
 * @param {number|string} basketid - id корзины
 * */
function wfSelectProduct(data) {
    if (typeof data.productid === "undefined" || typeof data.packid === "undefined" || typeof data.basketid === "undefined")
        return false;

    $.ajax({
        url: 'select.pack.php',
        method: 'post',
        data: data,
        success: function (res) {
            if(res.success) {
                $.magnificPopup.close();
            }
        },
        error: function () {

        }
    });
}
