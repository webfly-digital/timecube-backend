function JCSmartFilter(ajaxURL, viewMode, params) {
    this.ajaxURL = ajaxURL;
    this.form = null;
    this.timer = null;
    this.cacheKey = '';
    this.cache = [];
    this.popups = [];
    this.viewMode = viewMode;
    if (params && params.SEF_SET_FILTER_URL) {
        let isChecked = this.isAvailableChecked();
        if (isChecked) params.SEF_SET_FILTER_URL += '?available=y';//значение при загрузке страницы
        this.bindUrlToButton('set_filter', params.SEF_SET_FILTER_URL);
        this.sef = true;
    }
    if (params && params.SEF_DEL_FILTER_URL) {
        this.bindUrlToButton('del_filter', params.SEF_DEL_FILTER_URL);
    }
    this.bindAvailableChecked();
}

JCSmartFilter.prototype.isAvailableChecked = function () {
    let availableCheckbox = BX('products_available');
    if (availableCheckbox.checked) {
        return true;
    } else {
        return false;
    }
};

JCSmartFilter.prototype.bindAvailableChecked = function () {
    let availableCheckbox = BX('products_available');
    BX.bind(availableCheckbox, 'change', BX.proxy(this.changeFilterLink, this));
};

JCSmartFilter.prototype.changeFilterLink = function () {
    let target = BX.proxy_context;
    if(target)
    {//имитируем нажатие на кнопку Показать
       let checkSetFilter = document.getElementById("set_filter");
       if(checkSetFilter)
       {
           checkSetFilter.click();
       }
    }
    let modef = document.getElementById("modef");
    if (modef) modef.style.display = 'none';
};

JCSmartFilter.prototype.keyup = function (input) {
    if (!!this.timer) {
        clearTimeout(this.timer);
    }
    this.timer = setTimeout(BX.delegate(function () {
        this.reload(input);
    }, this), 500);
};

JCSmartFilter.prototype.click = function (checkbox) {
    if (!!this.timer) {
        clearTimeout(this.timer);
    }

    this.timer = setTimeout(BX.delegate(function () {
        this.reload(checkbox);
    }, this), 500);
};

JCSmartFilter.prototype.reload = function (input) {
    if (this.cacheKey !== '') {
        //Postprone backend query
        if (!!this.timer) {
            clearTimeout(this.timer);
        }
        this.timer = setTimeout(BX.delegate(function () {
            this.reload(input);
        }, this), 1000);
        return;
    }
    this.cacheKey = '|';

    this.position = BX.pos(input, true);
    this.form = BX.findParent(input, {'tag': 'form'});
    if (this.form) {
        var values = [];
        values[0] = {name: 'ajax', value: 'y'};
        this.gatherInputsValues(values, BX.findChildren(this.form, {'tag': new RegExp('^(input|select)$', 'i')}, true));

        for (var i = 0; i < values.length; i++)
            this.cacheKey += values[i].name + ':' + values[i].value + '|';

        if (this.cache[this.cacheKey]) {
            this.curFilterinput = input;
            this.postHandler(this.cache[this.cacheKey], true);
        } else {
            if (this.sef) {
                var set_filter = BX('set_filter');
                set_filter.disabled = true;
            }

            this.curFilterinput = input;
            BX.ajax.loadJSON(
                this.ajaxURL,
                this.values2post(values),
                BX.delegate(this.postHandler, this)
            );
        }
    }
};

JCSmartFilter.prototype.updateItem = function (PID, arItem) {
    if (arItem.PROPERTY_TYPE === 'N' || arItem.PRICE) {
        var trackBar = window['trackBar' + PID];
        if (!trackBar && arItem.ENCODED_ID)
            trackBar = window['trackBar' + arItem.ENCODED_ID];

        if (trackBar && arItem.VALUES) {
            if (arItem.VALUES.MIN) {
                if (arItem.VALUES.MIN.FILTERED_VALUE)
                    trackBar.setMinFilteredValue(arItem.VALUES.MIN.FILTERED_VALUE);
                else
                    trackBar.setMinFilteredValue(arItem.VALUES.MIN.VALUE);
            }

            if (arItem.VALUES.MAX) {
                if (arItem.VALUES.MAX.FILTERED_VALUE)
                    trackBar.setMaxFilteredValue(arItem.VALUES.MAX.FILTERED_VALUE);
                else
                    trackBar.setMaxFilteredValue(arItem.VALUES.MAX.VALUE);
            }
        }
    } else if (arItem.VALUES) {
        for (var i in arItem.VALUES) {
            if (arItem.VALUES.hasOwnProperty(i)) {
                var value = arItem.VALUES[i];
                var control = BX(value.CONTROL_ID);

                if (!!control) {
                    var label = document.querySelector('[data-role="label_' + value.CONTROL_ID + '"]');
                    if (value.DISABLED) {
                        BX.adjust(control, {props: {disabled: true}});
                        if (label)
                            BX.addClass(label, 'disabled');
                        else
                            BX.addClass(control.parentNode, 'disabled');
                    } else {
                        BX.adjust(control, {props: {disabled: false}});
                        if (label)
                            BX.removeClass(label, 'disabled');
                        else
                            BX.removeClass(control.parentNode, 'disabled');
                    }

                    if (value.hasOwnProperty('ELEMENT_COUNT')) {
                        label = document.querySelector('[data-role="count_' + value.CONTROL_ID + '"]');
                        if (label)
                            label.innerHTML = value.ELEMENT_COUNT;
                    }
                }
            }
        }
    }
};

JCSmartFilter.prototype.postHandler = function (result, fromCache) {
    var hrefFILTER, url, curProp;
    var modef = BX('modef');
    var modef_num = BX('modef_num');

    if (!!result && !!result.ITEMS) {
        for (var popupId in this.popups) {
            if (this.popups.hasOwnProperty(popupId)) {
                this.popups[popupId].destroy();
            }
        }
        this.popups = [];

        for (var PID in result.ITEMS) {
            if (result.ITEMS.hasOwnProperty(PID)) {
                this.updateItem(PID, result.ITEMS[PID]);
            }
        }

        if (!!modef && !!modef_num) {
            modef_num.innerHTML = result.ELEMENT_COUNT;
            hrefFILTER = BX.findChildren(modef, {tag: 'A'}, true);

            if (result.FILTER_URL && hrefFILTER) {
                hrefFILTER[0].href = BX.util.htmlspecialcharsback(result.FILTER_URL);
                let isChecked = this.isAvailableChecked();
                if (isChecked) hrefFILTER[0].href += '?available=y';
            }

            if (result.FILTER_AJAX_URL && result.COMPONENT_CONTAINER_ID) {
                BX.unbindAll(hrefFILTER[0]);
                BX.bind(hrefFILTER[0], 'click', function (e) {
                    url = BX.util.htmlspecialcharsback(result.FILTER_AJAX_URL);
                    BX.ajax.insertToNode(url, result.COMPONENT_CONTAINER_ID);
                    return BX.PreventDefault(e);
                });
            }

            if (result.INSTANT_RELOAD && result.COMPONENT_CONTAINER_ID) {
                url = BX.util.htmlspecialcharsback(result.FILTER_AJAX_URL);
                BX.ajax.insertToNode(url, result.COMPONENT_CONTAINER_ID);
            } else {
                if (modef.style.display === 'none') {
                    modef.style.display = 'inline-block';
                }

                if (this.viewMode == "VERTICAL") {
                    switch (this.curFilterinput.type) {
                        case 'number':
                        case 'checkbox':
                            curProp = this.curFilterinput.parentNode;
                            break;
                        default:
                            curProp = BX.findChild(BX.findParent(this.curFilterinput, {'class': 'smart-filter-parameters-box'}), {'class': 'smart-filter-container-modef'}, true, false);
                            break;
                    }
                    //switch(this.curFilterInput.tagName)
                    curProp.appendChild(modef);
                }

                if (result.SEF_SET_FILTER_URL) {
                    let isChecked = this.isAvailableChecked();
                    if (isChecked) result.SEF_SET_FILTER_URL += '?available=y';
                    this.bindUrlToButton('set_filter', result.SEF_SET_FILTER_URL);
                }
            }
        }
    }

    if (this.sef) {
        var set_filter = BX('set_filter');
        set_filter.disabled = false;
    }

    if (!fromCache && this.cacheKey !== '') {
        this.cache[this.cacheKey] = result;
    }
    this.cacheKey = '';
};

JCSmartFilter.prototype.bindUrlToButton = function (buttonId, url) {
    var button = BX(buttonId);
    if (button) {
        var proxy = function (j, func) {
            return function () {
                return func(j);
            }
        };

        if (button.type == 'submit')
            button.type = 'button';

        BX.bind(button, 'click', proxy(url, function (url) {
            let availableCheckbox = BX('products_available');
            let urlClass = new URL(url, window.location.origin);
            if (availableCheckbox.checked) {
                if (!urlClass.searchParams.has('available')) {
                    urlClass.searchParams.set('available', 'y');
                    url = urlClass.href;
                }
            } else {
                if (urlClass.searchParams.has('available')) {
                    urlClass.searchParams.delete('available');
                    url = urlClass.href;
                }
            }
            window.location.href = url;
            return false;
        }));
    }
};

JCSmartFilter.prototype.gatherInputsValues = function (values, elements) {
    if (elements) {
        for (var i = 0; i < elements.length; i++) {
            var el = elements[i];
            if (el.disabled || !el.type)
                continue;

            switch (el.type.toLowerCase()) {
                case 'text':
                case 'number':
                case 'textarea':
                case 'password':
                case 'hidden':
                case 'select-one':
                    if (el.value.length)
                        values[values.length] = {name: el.name, value: el.value};
                    break;
                case 'radio':
                case 'checkbox':
                    if (el.checked)
                        values[values.length] = {name: el.name, value: el.value};
                    break;
                case 'select-multiple':
                    for (var j = 0; j < el.options.length; j++) {
                        if (el.options[j].selected)
                            values[values.length] = {name: el.name, value: el.options[j].value};
                    }
                    break;
                default:
                    break;
            }
        }
    }
};

JCSmartFilter.prototype.values2post = function (values) {
    var post = [];
    var current = post;
    var i = 0;

    while (i < values.length) {
        var p = values[i].name.indexOf('[');
        if (p == -1) {
            current[values[i].name] = values[i].value;
            current = post;
            i++;
        } else {
            var name = values[i].name.substring(0, p);
            var rest = values[i].name.substring(p + 1);
            if (!current[name])
                current[name] = [];

            var pp = rest.indexOf(']');
            if (pp == -1) {
                //Error - not balanced brackets
                current = post;
                i++;
            } else if (pp == 0) {
                //No index specified - so take the next integer
                current = current[name];
                values[i].name = '' + current.length;
            } else {
                //Now index name becomes and name and we go deeper into the array
                current = current[name];
                values[i].name = rest.substring(0, pp) + rest.substring(pp + 1);
            }
        }
    }
    return post;
};

JCSmartFilter.prototype.hideFilterProps = function (element) {
    var obj = element.parentNode,
        filterBlock = obj.querySelector("[data-role='bx_filter_block']"),
        propAngle = obj.querySelector("[data-role='prop_angle']");

    if (BX.hasClass(obj, "bx-active")) {
        filterBlock.style.overflow = "hidden";
        new BX.easing({
            duration: 300,
            start: {opacity: 100, height: filterBlock.offsetHeight},
            finish: {opacity: 0, height: 0},
            transition: BX.easing.transitions.quart,
            step: function (state) {
                filterBlock.style.opacity = state.opacity / 100;
                filterBlock.style.height = state.height + "px";
            },
            complete: function () {
                filterBlock.setAttribute("style", "");
                BX.removeClass(obj, "bx-active");
                BX.addClass(propAngle, "smart-filter-angle-down");
                BX.removeClass(propAngle, "smart-filter-angle-up");
            }
        }).animate();

    } else {
        filterBlock.style.display = "block";
        filterBlock.style.opacity = 0;
        filterBlock.style.height = "auto";
        filterBlock.style.overflow = "hidden";

        var obj_children_height = filterBlock.offsetHeight;
        filterBlock.style.height = 0;

        new BX.easing({
            duration: 300,
            start: {opacity: 0, height: 0},
            finish: {opacity: 100, height: obj_children_height},
            transition: BX.easing.transitions.quart,
            step: function (state) {
                filterBlock.style.opacity = state.opacity / 100;
                filterBlock.style.height = state.height + "px";
            },
            complete: function () {
                filterBlock.style.overflow = "";
                BX.addClass(obj, "bx-active");
                BX.removeClass(propAngle, "smart-filter-angle-down");
                BX.addClass(propAngle, "smart-filter-angle-up");
            }
        }).animate();

    }
};

JCSmartFilter.prototype.showDropDownPopup = function (element, popupId) {
    var contentNode = element.querySelector('[data-role="dropdownContent"]');
    this.popups["smartFilterDropDown" + popupId] = BX.PopupWindowManager.create("smartFilterDropDown" + popupId, element, {
        autoHide: true,
        offsetLeft: 0,
        offsetTop: 3,
        overlay: false,
        draggable: {restrict: true},
        closeByEsc: true,
        content: BX.clone(contentNode)
    });
    this.popups["smartFilterDropDown" + popupId].show();
};

JCSmartFilter.prototype.selectDropDownItem = function (element, controlId) {
    this.keyup(BX(controlId));

    var wrapContainer = BX.findParent(BX(controlId), {className: "smart-filter-input-group-dropdown"}, false);

    var currentOption = wrapContainer.querySelector('[data-role="currentOption"]');
    currentOption.innerHTML = element.innerHTML;
    BX.PopupWindowManager.getCurrentPopup().close();
};

BX.namespace("BX.Iblock.SmartFilter");
BX.Iblock.SmartFilter = (function () {
    /** @param {{
			leftSlider: string,
			rightSlider: string,
			tracker: string,
			trackerWrap: string,
			minInputId: string,
			maxInputId: string,
			minPrice: float|int|string,
			maxPrice: float|int|string,
			curMinPrice: float|int|string,
			curMaxPrice: float|int|string,
			fltMinPrice: float|int|string|null,
			fltMaxPrice: float|int|string|null,
			precision: int|null,
			colorUnavailableActive: string,
			colorAvailableActive: string,
			colorAvailableInactive: string
		}} arParams
     */
    var SmartFilter = function (arParams) {
        if (typeof arParams === 'object') {
            this.leftSlider = BX(arParams.leftSlider);
            this.rightSlider = BX(arParams.rightSlider);
            this.tracker = BX(arParams.tracker);
            this.trackerWrap = BX(arParams.trackerWrap);
            this.priceSlider = null;
            this.minInput = BX(arParams.minInputId);
            this.maxInput = BX(arParams.maxInputId);

            this.minPrice = parseFloat(arParams.minPrice);
            this.maxPrice = parseFloat(arParams.maxPrice);

            this.curMinPrice = parseFloat(arParams.curMinPrice);
            this.curMaxPrice = parseFloat(arParams.curMaxPrice);

            this.fltMinPrice = arParams.fltMinPrice ? parseFloat(arParams.fltMinPrice) : parseFloat(arParams.curMinPrice);
            this.fltMaxPrice = arParams.fltMaxPrice ? parseFloat(arParams.fltMaxPrice) : parseFloat(arParams.curMaxPrice);

            this.precision = arParams.precision || 0;

            this.priceDiff = this.maxPrice - this.minPrice;

            this.leftPercent = 0;
            this.rightPercent = 0;

            this.fltMinPercent = 0;
            this.fltMaxPercent = 0;

            this.colorUnavailableActive = BX(arParams.colorUnavailableActive);//gray
            this.colorAvailableActive = BX(arParams.colorAvailableActive);//blue
            this.colorAvailableInactive = BX(arParams.colorAvailableInactive);//light blue

            this.isTouch = false;

            this.init();

            if ('ontouchstart' in document.documentElement) {
                this.isTouch = true;

                BX.bind(this.leftSlider, "touchstart", BX.proxy(function (event) {
                    this.onMoveLeftSlider(event)
                }, this));

                BX.bind(this.rightSlider, "touchstart", BX.proxy(function (event) {
                    this.onMoveRightSlider(event)
                }, this));
            } else {
                BX.bind(this.leftSlider, "mousedown", BX.proxy(function (event) {
                    this.onMoveLeftSlider(event)
                }, this));

                BX.bind(this.rightSlider, "mousedown", BX.proxy(function (event) {
                    this.onMoveRightSlider(event)
                }, this));
            }

            BX.bind(this.minInput, "input", BX.proxy(function (event) {
                this.onInputChange();
            }, this));

            BX.bind(this.maxInput, "input", BX.proxy(function (event) {
                this.onInputChange();
            }, this));
        }
    };

    SmartFilter.prototype.slider = noUiSlider;

    SmartFilter.prototype.init = function () {
        var priceDiff;
        if (this.curMinPrice > this.minPrice) {
            priceDiff = this.curMinPrice - this.minPrice;
            this.leftPercent = (priceDiff * 100) / this.priceDiff;
        }

        if (this.curMaxPrice < this.maxPrice) {
            priceDiff = this.maxPrice - this.curMaxPrice;
            this.rightPercent = (priceDiff * 100) / this.priceDiff;
        }
        this.initPriceSlider();
    };

    SmartFilter.prototype.initPriceSlider = function () {
        var minPrice = parseInt(this.minPrice),
            maxPrice = parseInt(this.maxPrice),
            curMinPrice = parseInt(this.curMinPrice),
            curMaxPrice = parseInt(this.curMaxPrice),
            minInput = this.minInput,
            maxInput = this.maxInput;
        minInput.addEventListener('updated', this.keyup);
        maxInput.addEventListener('updated', this.keyup);
        if (isNaN(curMinPrice))
            curMinPrice = minPrice;
        if (isNaN(curMaxPrice))
            curMaxPrice = maxPrice;

        minInput.value = curMinPrice;
        maxInput.value = curMaxPrice;
        var sliderEl = this.trackerWrap;
        this.priceSlider = this.slider.create(sliderEl, {
            range: {
                'min': minPrice,
                'max': maxPrice
            },
            step: 150,
            connect: true,
            start: [curMinPrice, curMaxPrice]
        });

        this.priceSlider.on('slide', function (values, handle) {
            if (typeof values !== "undefined") {
                var newVal = parseInt(values[handle]),
                    input = minInput;
                if (handle === 1)
                    input = maxInput;
                input.value = newVal;
            }
        });

        this.priceSlider.on('change', function (values, handle) {
            var input = minInput;
            if (handle === 0) {
                this.curMinPrice = parseInt(input.value);
            }
            if (handle === 1) {
                input = maxInput;
                this.curMaxPrice = parseInt(input.value);
            }


            var event = new CustomEvent('keyup');
            input.dispatchEvent(event);

        }.bind(this));
    };

    SmartFilter.prototype.setMinFilteredValue = function (fltMinPrice) {
        /*this.fltMinPrice = parseFloat(fltMinPrice);
         if (this.fltMinPrice >= this.minPrice)
         {
         var priceDiff = this.fltMinPrice - this.minPrice;
         this.fltMinPercent = (priceDiff*100)/this.priceDiff;

         if (this.leftPercent > this.fltMinPercent)
         this.colorAvailableActive.style.left = this.leftPercent + "%";
         else
         this.colorAvailableActive.style.left = this.fltMinPercent + "%";

         this.colorAvailableInactive.style.left = this.fltMinPercent + "%";
         }
         else
         {
         this.colorAvailableActive.style.left = "0%";
         this.colorAvailableInactive.style.left = "0%";
         }*/
    };

    SmartFilter.prototype.setMaxFilteredValue = function (fltMaxPrice) {
        /*	this.fltMaxPrice = parseFloat(fltMaxPrice);
         if (this.fltMaxPrice <= this.maxPrice)
         {
         var priceDiff = this.maxPrice - this.fltMaxPrice;
         this.fltMaxPercent = (priceDiff*100)/this.priceDiff;

         if (this.rightPercent > this.fltMaxPercent)
         this.colorAvailableActive.style.right = this.rightPercent + "%";
         else
         this.colorAvailableActive.style.right = this.fltMaxPercent + "%";

         this.colorAvailableInactive.style.right = this.fltMaxPercent + "%";
         }
         else
         {
         this.colorAvailableActive.style.right = "0%";
         this.colorAvailableInactive.style.right = "0%";
         }*/
    };

    SmartFilter.prototype.recountMinPrice = function () {
        var newMinPrice = (this.priceDiff * this.leftPercent) / 100;
        newMinPrice = (this.minPrice + newMinPrice).toFixed(this.precision);

        if (newMinPrice != this.minPrice)
            this.minInput.value = newMinPrice;
        else
            this.minInput.value = "";
        /** @global JCSmartFilter smartFilter */
        smartFilter.keyup(this.minInput);
    };

    SmartFilter.prototype.recountMaxPrice = function () {
        var newMaxPrice = (this.priceDiff * this.rightPercent) / 100;
        newMaxPrice = (this.maxPrice - newMaxPrice).toFixed(this.precision);

        if (newMaxPrice != this.maxPrice)
            this.maxInput.value = newMaxPrice;
        else
            this.maxInput.value = "";
        /** @global JCSmartFilter smartFilter */
        smartFilter.keyup(this.maxInput);
    };

    SmartFilter.prototype.onInputChange = function () {
        var priceDiff;
        if (this.minInput.value) {
            var leftInputValue = this.minInput.value;
            if (leftInputValue < this.minPrice)
                leftInputValue = this.minPrice;

            if (leftInputValue > this.maxPrice)
                leftInputValue = this.maxPrice;

            priceDiff = leftInputValue - this.minPrice;
            this.leftPercent = (priceDiff * 100) / this.priceDiff;

            this.makeLeftSliderMove(leftInputValue);
        }

        if (this.maxInput.value) {
            var rightInputValue = this.maxInput.value;
            if (rightInputValue < this.minPrice)
                rightInputValue = this.minPrice;

            if (rightInputValue > this.maxPrice)
                rightInputValue = this.maxPrice;

            priceDiff = this.maxPrice - rightInputValue;
            this.rightPercent = (priceDiff * 100) / this.priceDiff;
            this.makeRightSliderMove(rightInputValue);
        }
    };

    SmartFilter.prototype.makeLeftSliderMove = function (recountPrice) {
        this.priceSlider.set([recountPrice, null]);
    };

    SmartFilter.prototype.makeRightSliderMove = function (recountPrice) {
        //  recountPrice = (recountPrice !== false);
        this.priceSlider.set([null, recountPrice]);

    };

    return SmartFilter;
})();
