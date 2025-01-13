(function (window) {
    'use strict';

    if (window.JCCatalogElement)
        return;

    var BasketButton = function (params) {
        BasketButton.superclass.constructor.apply(this, arguments);
        this.buttonNode = BX.create('SPAN', {
            props: {className: 'btn btn-primary btn-buy btn-sm', id: this.id},
            style: typeof params.style === 'object' ? params.style : {},
            text: params.text,
            events: this.contextEvents
        });

        if (BX.browser.IsIE()) {
            this.buttonNode.setAttribute('hideFocus', 'hidefocus');
        }
    };
    BX.extend(BasketButton, BX.PopupWindowButton);

    window.JCCatalogElement = function (arParams) {

        this.productType = 0;

        this.config = {
            useCatalog: true,
            showQuantity: true,
            showPrice: true,
            showAbsent: true,
            showOldPrice: false,
            showPercent: false,
            showSkuProps: false,
            showOfferGroup: false,
            useCompare: false,
            useStickers: false,
            useSubscribe: false,
            usePopup: false,
            useMagnifier: false,
            usePriceRanges: false,
            basketAction: ['BUY'],
            showClosePopup: false,
            templateTheme: '',
            showSlider: false,
            sliderInterval: 5000,
            useEnhancedEcommerce: false,
            dataLayerName: 'dataLayer',
            brandProperty: false,
            alt: '',
            title: '',
            magnifierZoomPercent: 200
        };

        this.checkQuantity = false;
        this.maxQuantity = 0;
        this.minQuantity = 0;
        this.stepQuantity = 1;
        this.isDblQuantity = false;
        this.canBuy = true;
        this.isGift = false;
        this.canSubscription = true;
        this.currentIsSet = false;
        this.updateViewedCount = false;

        this.currentPriceMode = '';
        this.currentPrices = [];
        this.currentPriceSelected = 0;
        this.currentQuantityRanges = [];
        this.currentQuantityRangeSelected = 0;

        this.precision = 6;
        this.precisionFactor = Math.pow(10, this.precision);

        this.visual = {};
        this.basketMode = '';
        this.product = {
            checkQuantity: false,
            maxQuantity: 0,
            stepQuantity: 1,
            startQuantity: 1,
            isDblQuantity: false,
            canBuy: true,
            canSubscription: true,
            name: '',
            pict: {},
            id: 0,
            addUrl: '',
            buyUrl: '',
            slider: {},
            sliderCount: 0,
            useSlider: false,
            sliderPict: []
        };
        this.mess = {};

        this.basketData = {
            useProps: false,
            emptyProps: false,
            quantity: 'quantity',
            props: 'prop',
            basketUrl: '',
            sku_props: '',
            sku_props_var: 'basket_props',
            add_url: '',
            buy_url: ''
        };
        this.compareData = {
            compareUrl: '',
            compareDeleteUrl: '',
            comparePath: ''
        };

        this.defaultPict = {
            preview: null,
            detail: null
        };

        this.offers = [];
        this.offerNum = 0;
        this.treeProps = [];
        this.selectedValues = {};

        this.mouseTimer = null;
        this.isTouchDevice = BX.hasClass(document.documentElement, 'bx-touch');
        this.touch = null;
        this.slider = {
            interval: null,
            progress: null,
            paused: null,
            controls: []
        };

        this.quantityDelay = null;
        this.quantityTimer = null;

        this.obProduct = null;
        this.obQuantity = null;
        this.obQuantityUp = null;
        this.obQuantityDown = null;
        this.obPrice = {
            price: null,
            full: null,
            discount: null,
            percent: null,
            total: null
        };
        this.obTree = null;
        this.obPriceRanges = null;
        this.obBuyBtn = null;
        this.obAddToBasketBtn = null;
        this.obBasketActions = null;
        this.obNotAvail = null;
        this.obSubscribe = null;
        this.obSkuProps = null;
        this.obMainSkuProps = null;
        this.obBigSlider = null;
        this.obMeasure = null;
        this.obQuantityLimit = {
            all: null,
            value: null
        };
        this.obCompare = null;
        this.obTabsPanel = null;

        this.node = {};
        // top panel small card
        this.smallCardNodes = {};

        this.magnify = {
            enabled: false,
            obBigImg: null,
            obBigSlider: null,
            height: 0,
            width: 0,
            timer: 0
        };
        this.currentImg = {
            id: 0,
            src: '',
            width: 0,
            height: 0
        };
        this.viewedCounter = {
            path: '/bitrix/components/bitrix/catalog.element/ajax.php',
            params: {
                AJAX: 'Y',
                SITE_ID: '',
                PRODUCT_ID: 0,
                PARENT_ID: 0
            }
        };

        this.obPopupWin = null;
        this.basketUrl = '';
        this.basketParams = {};
        this.errorCode = 0;

        if (typeof arParams === 'object') {
            this.params = arParams;
            this.initConfig();

            if (this.params.MESS) {
                this.mess = this.params.MESS;
            }

            switch (this.productType) {
                case 0: // no catalog
                case 1: // product
                case 2: // set
                    this.initProductData();
                    break;
                case 3: // sku
                    this.initOffersData();
                    break;
                default:
                    this.errorCode = -1;
            }

            this.initBasketData();
            this.initCompareData();
        }

        if (this.errorCode === 0) {
            BX.ready(BX.delegate(this.init, this));
        }

        this.params = {};

        //BX.addCustomEvent('onSaleProductIsGift', BX.delegate(this.onSaleProductIsGift, this));
        //BX.addCustomEvent('onSaleProductIsNotGift', BX.delegate(this.onSaleProductIsNotGift, this));
    };

    window.JCCatalogElement.prototype = {
        getEntity: function (parent, entity, additionalFilter) {
            if (!parent || !entity)
                return null;

            additionalFilter = additionalFilter || '';

            return parent.querySelector(additionalFilter + '[data-entity="' + entity + '"]');
        },

        getEntities: function (parent, entity, additionalFilter) {
            if (!parent || !entity)
                return {length: 0};

            additionalFilter = additionalFilter || '';

            return parent.querySelectorAll(additionalFilter + '[data-entity="' + entity + '"]');
        },

        onSaleProductIsGift: function (productId, offerId) {
            if (offerId && this.offers && this.offers[this.offerNum].ID == offerId) {
                this.setGift();
            }
        },

        onSaleProductIsNotGift: function (productId, offerId) {
            if (offerId && this.offers && this.offers[this.offerNum].ID == offerId) {
                this.restoreSticker();
                this.isGift = false;
                this.setPrice();
            }
        },

        reloadGiftInfo: function () {
            if (this.productType === 3) {
                this.checkQuantity = true;
                this.maxQuantity = 1;

                this.setPrice();
                this.redrawSticker({text: BX.message('PRODUCT_GIFT_LABEL')});
            }
        },

        setGift: function () {
            if (this.productType === 3) {
                // sku
                this.isGift = true;
            }

            if (this.productType === 1 || this.productType === 2) {
                // simple
                this.isGift = true;
            }

            if (this.productType === 0) {
                this.isGift = false;
            }

            this.reloadGiftInfo();
        },

        setOffer: function (offerNum) {
            this.offerNum = parseInt(offerNum);
            this.setCurrent();
        },

        init: function () {
            var i = 0,
                j = 0,
                treeItems = null;

            this.obProduct = BX(this.visual.ID);
            if (!this.obProduct) {
                this.errorCode = -1;
            }

            this.obBigSlider = BX(this.visual.BIG_SLIDER_ID);
            this.node.imageContainer = this.getEntity(this.obProduct, 'images-container');
            this.node.imageSliderBlock = this.getEntity(this.obProduct, 'images-slider-block');

            if (!this.obBigSlider || !this.node.imageContainer || !this.node.imageContainer) {
                this.errorCode = -2;
            }

            if (this.config.showPrice) {
                this.obPrice.price = BX(this.visual.PRICE_ID);
                if (!this.obPrice.price && this.config.useCatalog) {
                    this.errorCode = -16;
                } else {
                    this.obPrice.total = BX(this.visual.PRICE_TOTAL);

                    if (this.config.showOldPrice) {
                        this.obPrice.full = BX(this.visual.OLD_PRICE_ID);
                        this.obPrice.discount = BX(this.visual.DISCOUNT_PRICE_ID);

                        if (!this.obPrice.full || !this.obPrice.discount) {
                            this.config.showOldPrice = false;
                        }
                    }

                    if (this.config.showPercent) {
                        this.obPrice.percent = BX(this.visual.DISCOUNT_PERCENT_ID);
                        if (!this.obPrice.percent) {
                            this.config.showPercent = false;
                        }
                    }
                }

                this.obBasketActions = BX(this.visual.BASKET_ACTIONS_ID);
                if (this.obBasketActions) {
                    if (BX.util.in_array('BUY', this.config.basketAction)) {
                        this.obBuyBtn = BX(this.visual.BUY_LINK);
                    }

                    if (BX.util.in_array('ADD', this.config.basketAction)) {
                        this.obAddToBasketBtn = BX(this.visual.ADD_BASKET_LINK);
                    }
                }
                this.obNotAvail = BX(this.visual.NOT_AVAILABLE_MESS);
            }

            if (this.config.showQuantity) {
                this.obQuantity = BX(this.visual.QUANTITY_ID);
                this.node.quantity = this.getEntity(this.obProduct, 'quantity-block');
                if (this.visual.QUANTITY_UP_ID) {
                    this.obQuantityUp = BX(this.visual.QUANTITY_UP_ID);
                }

                if (this.visual.QUANTITY_DOWN_ID) {
                    this.obQuantityDown = BX(this.visual.QUANTITY_DOWN_ID);
                }
            }

            if (this.productType === 3) {
                if (this.visual.TREE_ID) {
                    this.obTree = BX(this.visual.TREE_ID);
                    if (!this.obTree) {
                        this.errorCode = -256;
                    }
                }

                if (this.visual.QUANTITY_MEASURE) {
                    this.obMeasure = BX(this.visual.QUANTITY_MEASURE);
                }

                if (this.visual.QUANTITY_LIMIT && this.config.showMaxQuantity !== 'N') {
                    this.obQuantityLimit.all = BX(this.visual.QUANTITY_LIMIT);
                    //   console.log(this.visual);
                    if (this.obQuantityLimit.all) {
                        this.obQuantityLimit.value = this.getEntity(this.obQuantityLimit.all, 'quantity-limit-value');
                        if (!this.obQuantityLimit.value) {
                            this.obQuantityLimit.all = null;
                        }
                    }
                }

                if (this.config.usePriceRanges) {
                    this.obPriceRanges = this.getEntity(this.obProduct, 'price-ranges-block');
                }
            }

            if (this.config.showSkuProps) {
                this.obSkuProps = BX(this.visual.DISPLAY_PROP_DIV);
                this.obMainSkuProps = BX(this.visual.DISPLAY_MAIN_PROP_DIV);
            }

            if (this.config.useCompare) {
                this.obCompare = BX(this.visual.COMPARE_LINK);
            }

            if (this.config.useSubscribe) {
                this.obSubscribe = BX(this.visual.SUBSCRIBE_LINK);
            }

            this.obTabs = BX(this.visual.TABS_ID);
            this.obTabContainers = BX(this.visual.TAB_CONTAINERS_ID);
            this.obTabsPanel = BX(this.visual.TABS_PANEL_ID);

            this.initPopup();
            this.initTabs();

            if (this.errorCode !== 0) console.log('catalog.element errorCode:', this.errorCode);
            if (this.errorCode === 0) {

                if (this.config.showQuantity) {
                    var startEventName = this.isTouchDevice ? 'touchstart' : 'mousedown';
                    var endEventName = this.isTouchDevice ? 'touchend' : 'mouseup';

                    if (this.obQuantityUp) {
                        BX.bind(this.obQuantityUp, startEventName, BX.proxy(this.startQuantityInterval, this));
                        BX.bind(this.obQuantityUp, endEventName, BX.proxy(this.clearQuantityInterval, this));
                        BX.bind(this.obQuantityUp, 'mouseout', BX.proxy(this.clearQuantityInterval, this));
                        BX.bind(this.obQuantityUp, 'click', BX.delegate(this.quantityUp, this));
                    }

                    if (this.obQuantityDown) {
                        BX.bind(this.obQuantityDown, startEventName, BX.proxy(this.startQuantityInterval, this));
                        BX.bind(this.obQuantityDown, endEventName, BX.proxy(this.clearQuantityInterval, this));
                        BX.bind(this.obQuantityDown, 'mouseout', BX.proxy(this.clearQuantityInterval, this));
                        BX.bind(this.obQuantityDown, 'click', BX.delegate(this.quantityDown, this));
                    }

                    if (this.obQuantity) {
                        BX.bind(this.obQuantity, 'change', BX.delegate(this.quantityChange, this));
                    }
                }

                switch (this.productType) {
                    case 0: // no catalog
                    case 1: // product
                    case 2: // set
                        this.checkQuantityControls();
                        this.fixFontCheck();
                        this.setAnalyticsDataLayer('showDetail');
                        break;
                    case 3: // sku
                        treeItems = this.obTree.querySelectorAll('li');
                        for (i = 0; i < treeItems.length; i++) {
                            BX.bind(treeItems[i], 'click', BX.delegate(this.selectOfferProp, this));
                        }

                        this.setCurrent();
                        break;
                }

                this.obBuyBtn && BX.bind(this.obBuyBtn, 'click', BX.proxy(this.buyBasket, this));

                this.obAddToBasketBtn && BX.bind(this.obAddToBasketBtn, 'click', BX.proxy(this.add2Basket, this));

                if (this.obCompare) {
                    BX.bind(this.obCompare, 'click', BX.proxy(this.compare, this));
                    BX.addCustomEvent('onCatalogDeleteCompare', BX.proxy(this.checkDeletedCompare, this));
                }
            }
        },

        initConfig: function () {
            if (this.params.PRODUCT_TYPE) {
                this.productType = parseInt(this.params.PRODUCT_TYPE, 10);
            }

            if (this.params.CONFIG.USE_CATALOG !== 'undefined' && BX.type.isBoolean(this.params.CONFIG.USE_CATALOG)) {
                this.config.useCatalog = this.params.CONFIG.USE_CATALOG;
            }

            this.config.showQuantity = this.params.CONFIG.SHOW_QUANTITY;
            this.config.showPrice = this.params.CONFIG.SHOW_PRICE;
            this.config.showPercent = this.params.CONFIG.SHOW_DISCOUNT_PERCENT;
            this.config.showOldPrice = this.params.CONFIG.SHOW_OLD_PRICE;
            this.config.showSkuProps = this.params.CONFIG.SHOW_SKU_PROPS;
            this.config.showOfferGroup = this.params.CONFIG.OFFER_GROUP;
            this.config.useCompare = this.params.CONFIG.DISPLAY_COMPARE;
            this.config.useStickers = this.params.CONFIG.USE_STICKERS;
            this.config.useSubscribe = this.params.CONFIG.USE_SUBSCRIBE;
            this.config.showMaxQuantity = this.params.CONFIG.SHOW_MAX_QUANTITY;
            this.config.relativeQuantityFactor = parseInt(this.params.CONFIG.RELATIVE_QUANTITY_FACTOR);
            this.config.usePriceRanges = this.params.CONFIG.USE_PRICE_COUNT;

            if (this.params.CONFIG.ADD_TO_BASKET_ACTION) {
                this.config.basketAction = this.params.CONFIG.ADD_TO_BASKET_ACTION;
            }

            this.config.showClosePopup = this.params.CONFIG.SHOW_CLOSE_POPUP;
            this.config.templateTheme = this.params.CONFIG.TEMPLATE_THEME || '';
            this.config.showSlider = this.params.CONFIG.SHOW_SLIDER === 'Y';
            this.config.useEnhancedEcommerce = this.params.CONFIG.USE_ENHANCED_ECOMMERCE === 'Y';
            this.config.dataLayerName = this.params.CONFIG.DATA_LAYER_NAME;
            this.config.brandProperty = this.params.CONFIG.BRAND_PROPERTY;
            this.config.alt = this.params.CONFIG.ALT || '';
            this.config.title = this.params.CONFIG.TITLE || '';
            this.config.magnifierZoomPercent = parseInt(this.params.CONFIG.MAGNIFIER_ZOOM_PERCENT) || 200;
            if (!this.params.VISUAL || typeof this.params.VISUAL !== 'object' || !this.params.VISUAL.ID) {
                this.errorCode = -1;
                return;
            }

            this.visual = this.params.VISUAL;
        },

        initProductData: function () {
            var j = 0;

            if (this.params.PRODUCT && typeof this.params.PRODUCT === 'object') {
                if (this.config.showPrice) {
                    this.currentPriceMode = this.params.PRODUCT.ITEM_PRICE_MODE;
                    this.currentPrices = this.params.PRODUCT.ITEM_PRICES;
                    this.currentPriceSelected = this.params.PRODUCT.ITEM_PRICE_SELECTED;
                    this.currentQuantityRanges = this.params.PRODUCT.ITEM_QUANTITY_RANGES;
                    this.currentQuantityRangeSelected = this.params.PRODUCT.ITEM_QUANTITY_RANGE_SELECTED;
                }

                if (this.config.showQuantity) {
                    this.product.checkQuantity = this.params.PRODUCT.CHECK_QUANTITY;
                    this.product.isDblQuantity = this.params.PRODUCT.QUANTITY_FLOAT;

                    if (this.product.checkQuantity) {
                        this.product.maxQuantity = this.product.isDblQuantity
                            ? parseFloat(this.params.PRODUCT.MAX_QUANTITY)
                            : parseInt(this.params.PRODUCT.MAX_QUANTITY, 10);
                    }

                    this.product.stepQuantity = this.product.isDblQuantity
                        ? parseFloat(this.params.PRODUCT.STEP_QUANTITY)
                        : parseInt(this.params.PRODUCT.STEP_QUANTITY, 10);
                    this.checkQuantity = this.product.checkQuantity;
                    this.isDblQuantity = this.product.isDblQuantity;
                    this.stepQuantity = this.product.stepQuantity;
                    this.maxQuantity = this.product.maxQuantity;
                    this.minQuantity = this.currentPriceMode === 'Q' ? parseFloat(this.currentPrices[this.currentPriceSelected].MIN_QUANTITY) : this.stepQuantity;

                    if (this.isDblQuantity) {
                        this.stepQuantity = Math.round(this.stepQuantity * this.precisionFactor) / this.precisionFactor;
                    }
                }

                this.product.canBuy = this.params.PRODUCT.CAN_BUY;
                this.canSubscription = this.product.canSubscription = this.params.PRODUCT.SUBSCRIPTION;

                this.product.name = this.params.PRODUCT.NAME;
                this.product.pict = this.params.PRODUCT.PICT;
                this.product.id = this.params.PRODUCT.ID;
                this.product.category = this.params.PRODUCT.CATEGORY;

                if (this.params.PRODUCT.ADD_URL) {
                    this.product.addUrl = this.params.PRODUCT.ADD_URL;
                }

                if (this.params.PRODUCT.BUY_URL) {
                    this.product.buyUrl = this.params.PRODUCT.BUY_URL;
                }

                this.currentIsSet = true;
            } else {
                this.errorCode = -1;
            }
        },

        initOffersData: function () {
            if (this.params.OFFERS && BX.type.isArray(this.params.OFFERS)) {
                this.offers = this.params.OFFERS;
                this.offerNum = 0;

                if (this.params.OFFER_SELECTED) {
                    this.offerNum = parseInt(this.params.OFFER_SELECTED, 10) || 0;
                }

                if (this.params.TREE_PROPS) {
                    this.treeProps = this.params.TREE_PROPS;
                }

                if (this.params.DEFAULT_PICTURE) {
                    this.defaultPict.preview = this.params.DEFAULT_PICTURE.PREVIEW_PICTURE;
                    this.defaultPict.detail = this.params.DEFAULT_PICTURE.DETAIL_PICTURE;
                }
                if (this.params.PRODUCT && typeof this.params.PRODUCT === 'object') {
                    this.product.id = parseInt(this.params.PRODUCT.ID, 10);
                    this.product.name = this.params.PRODUCT.NAME;
                    this.product.category = this.params.PRODUCT.CATEGORY;
                }
            } else {
                this.errorCode = -1;
            }
        },

        initBasketData: function () {
            if (this.params.BASKET && typeof this.params.BASKET === 'object') {
                if (this.productType === 1 || this.productType === 2) {
                    this.basketData.useProps = this.params.BASKET.ADD_PROPS;
                    this.basketData.emptyProps = this.params.BASKET.EMPTY_PROPS;
                }

                if (this.params.BASKET.QUANTITY) {
                    this.basketData.quantity = this.params.BASKET.QUANTITY;
                }

                if (this.params.BASKET.PROPS) {
                    this.basketData.props = this.params.BASKET.PROPS;
                }

                if (this.params.BASKET.BASKET_URL) {
                    this.basketData.basketUrl = this.params.BASKET.BASKET_URL;
                }

                if (this.productType === 3) {
                    if (this.params.BASKET.SKU_PROPS) {
                        this.basketData.sku_props = this.params.BASKET.SKU_PROPS;
                    }
                }

                if (this.params.BASKET.ADD_URL_TEMPLATE) {
                    this.basketData.add_url = this.params.BASKET.ADD_URL_TEMPLATE;
                }

                if (this.params.BASKET.BUY_URL_TEMPLATE) {
                    this.basketData.buy_url = this.params.BASKET.BUY_URL_TEMPLATE;
                }

                if (this.basketData.add_url === '' && this.basketData.buy_url === '') {
                    this.errorCode = -1024;
                }
            }
        },

        initCompareData: function () {
            if (this.config.useCompare) {
                if (this.params.COMPARE && typeof this.params.COMPARE === 'object') {
                    if (this.params.COMPARE.COMPARE_PATH) {
                        this.compareData.comparePath = this.params.COMPARE.COMPARE_PATH;
                    }

                    if (this.params.COMPARE.COMPARE_URL_TEMPLATE) {
                        this.compareData.compareUrl = this.params.COMPARE.COMPARE_URL_TEMPLATE;
                    } else {
                        this.config.useCompare = false;
                    }

                    if (this.params.COMPARE.COMPARE_DELETE_URL_TEMPLATE) {
                        this.compareData.compareDeleteUrl = this.params.COMPARE.COMPARE_DELETE_URL_TEMPLATE;
                    } else {
                        this.config.useCompare = false;
                    }
                } else {
                    this.config.useCompare = false;
                }
            }
        },

        initSlider: function () {
        },

        setAnalyticsDataLayer: function (action) {
            if (!this.config.useEnhancedEcommerce || !this.config.dataLayerName)
                return;

            var item = {},
                info = {},
                variants = [],
                i, k, j, propId, skuId, propValues;

            switch (this.productType) {
                case 0: //no catalog
                case 1: //product
                case 2: //set
                    item = {
                        'id': this.product.id,
                        'name': this.product.name,
                        'price': this.currentPrices[this.currentPriceSelected] && this.currentPrices[this.currentPriceSelected].PRICE,
                        'category': this.product.category,
                        'brand': BX.type.isArray(this.config.brandProperty) ? this.config.brandProperty.join('/') : this.config.brandProperty
                    };
                    break;
                case 3: //sku
                    for (i in this.offers[this.offerNum].TREE) {
                        if (this.offers[this.offerNum].TREE.hasOwnProperty(i)) {
                            propId = i.substring(5);
                            skuId = this.offers[this.offerNum].TREE[i];

                            for (k in this.treeProps) {
                                if (this.treeProps.hasOwnProperty(k) && this.treeProps[k].ID == propId) {
                                    for (j in this.treeProps[k].VALUES) {
                                        propValues = this.treeProps[k].VALUES[j];
                                        if (propValues.ID == skuId) {
                                            variants.push(propValues.NAME);
                                            break;
                                        }
                                    }

                                }
                            }
                        }
                    }
                    item = {
                        'id': this.offers[this.offerNum].ID,
                        'name': this.offers[this.offerNum].NAME,
                        'price': this.currentPrices[this.currentPriceSelected] && this.currentPrices[this.currentPriceSelected].PRICE,
                        'category': this.product.category,
                        'brand': BX.type.isArray(this.config.brandProperty) ? this.config.brandProperty.join('/') : this.config.brandProperty,
                        'variant': variants.join('/')
                    };
                    break;
            }

            switch (action) {
                case 'showDetail':
                    info = {
                        'event': 'showDetail',
                        'ecommerce': {
                            'currencyCode': this.currentPrices[this.currentPriceSelected] && this.currentPrices[this.currentPriceSelected].CURRENCY || '',
                            'detail': {
                                'products': [{
                                    'name': item.name || '',
                                    'id': item.id || '',
                                    'price': item.price || 0,
                                    'brand': item.brand || '',
                                    'category': item.category || '',
                                    'variant': item.variant || ''
                                }]
                            }
                        }
                    };
                    break;
                case 'addToCart':
                    info = {
                        'event': 'addToCart',
                        'ecommerce': {
                            'currencyCode': this.currentPrices[this.currentPriceSelected] && this.currentPrices[this.currentPriceSelected].CURRENCY || '',
                            'add': {
                                'products': [{
                                    'name': item.name || '',
                                    'id': item.id || '',
                                    'price': item.price || 0,
                                    'brand': item.brand || '',
                                    'category': item.category || '',
                                    'variant': item.variant || '',
                                    'quantity': this.config.showQuantity && this.obQuantity ? this.obQuantity.value : 1
                                }]
                            }
                        }
                    };
                    break;
            }
            window[this.config.dataLayerName] = window[this.config.dataLayerName] || [];
            window[this.config.dataLayerName].push(info);
        },

        initTabs: function () {
            var tabs = this.getEntities(this.obTabs, 'tab'),
                panelTabs = this.getEntities(this.obTabsPanel, 'tab');

            var tabValue, targetTab, haveActive = false;

            if (tabs.length !== panelTabs.length)
                return;

            for (var i in tabs) {
                if (tabs.hasOwnProperty(i) && BX.type.isDomNode(tabs[i])) {
                    tabValue = tabs[i].getAttribute('data-value');
                    if (tabValue) {
                        targetTab = this.obTabContainers.querySelector('[data-value="' + tabValue + '"]');
                        if (BX.type.isDomNode(targetTab)) {
                            BX.bind(tabs[i], 'click', BX.proxy(this.changeTab, this));
                            BX.bind(panelTabs[i], 'click', BX.proxy(this.changeTab, this));

                            if (!haveActive) {
                                BX.addClass(tabs[i], 'active');
                                BX.addClass(panelTabs[i], 'active');
                                BX.show(targetTab);
                                haveActive = true;
                            } else {
                                BX.removeClass(tabs[i], 'active');
                                BX.removeClass(panelTabs[i], 'active');
                                BX.hide(targetTab);
                            }
                        }
                    }
                }
            }
        },

        checkTouch: function (event) {
            if (!event || !event.changedTouches)
                return false;

            return event.changedTouches[0].identifier === this.touch.identifier;
        },

        touchStartEvent: function (event) {
            if (event.touches.length != 1)
                return;

            this.touch = event.changedTouches[0];
        },

        touchEndEvent: function (event) {
        },

        cycleSlider: function (event) {
        },

        stopSlider: function (event) {
        },

        resetProgress: function () {
        },

        slideNext: function () {
        },

        slidePrev: function () {
        },

        slide: function (type) {
        },

        getItemForDirection: function (direction, active) {
        },

        getItemIndex: function (item) {
        },

        eq: function (obj, i) {
            var len = obj.length,
                j = +i + (i < 0 ? len : 0);

            return j >= 0 && j < len ? obj[j] : {};
        },

        scrollToProduct: function () {
            var scrollTop = BX.GetWindowScrollPos().scrollTop,
                containerTop = BX.pos(this.obProduct).top - 30;

            if (scrollTop > containerTop) {
                new BX.easing({
                    duration: 500,
                    start: {scroll: scrollTop},
                    finish: {scroll: containerTop},
                    transition: BX.easing.makeEaseOut(BX.easing.transitions.quint),
                    step: BX.delegate(function (state) {
                        window.scrollTo(0, state.scroll);
                    }, this)
                }).animate();
            }
        },

        checkTopPanels: function () {
        },

        changeTab: function (event) {
            BX.PreventDefault(event);

            var targetTabValue = BX.proxy_context && BX.proxy_context.getAttribute('data-value'),
                containers, tabs, panelTabs;

            if (!BX.hasClass(BX.proxy_context, 'active') && targetTabValue) {
                containers = this.getEntities(this.obTabContainers, 'tab-container');
                for (var i in containers) {
                    if (containers.hasOwnProperty(i) && BX.type.isDomNode(containers[i])) {
                        if (containers[i].getAttribute('data-value') === targetTabValue) {
                            BX.show(containers[i]);
                        } else {
                            BX.hide(containers[i]);
                        }
                    }
                }

                tabs = this.getEntities(this.obTabs, 'tab');
                panelTabs = this.getEntities(this.obTabsPanel, 'tab');

                for (i in tabs) {
                    if (tabs.hasOwnProperty(i) && BX.type.isDomNode(tabs[i])) {
                        if (tabs[i].getAttribute('data-value') === targetTabValue) {
                            BX.addClass(tabs[i], 'active');
                            BX.addClass(panelTabs[i], 'active');
                        } else {
                            BX.removeClass(tabs[i], 'active');
                            BX.removeClass(panelTabs[i], 'active');
                        }
                    }
                }
            }

            var scrollTop = BX.GetWindowScrollPos().scrollTop,
                containerTop = BX.pos(this.obTabContainers).top;

            if (scrollTop + 150 > containerTop) {
                new BX.easing({
                    duration: 500,
                    start: {scroll: scrollTop},
                    finish: {scroll: containerTop - 150},
                    transition: BX.easing.makeEaseOut(BX.easing.transitions.quint),
                    step: BX.delegate(function (state) {
                        window.scrollTo(0, state.scroll);
                    }, this)
                }).animate();
            }
        },

        initPopup: function () {
        },

        checkSliderControls: function (count) {
        },

        setCurrentImg: function (img, showImage, showPanelImage) {
            var images, l;

            this.currentImg.id = img.ID;
            this.currentImg.src = img.SRC;
            this.currentImg.width = img.WIDTH;
            this.currentImg.height = img.HEIGHT;

            if (showImage && this.node.imageContainer) {
                images = this.getEntities(this.node.imageContainer, 'image');
                l = images.length;
                while (l--) {
                    if (images[l].getAttribute('data-id') == img.ID) {

                        BX.addClass(images[l], 'active');
                    } else if (BX.hasClass(images[l], 'active')) {
                        BX.removeClass(images[l], 'active');
                    }
                }
            }

        },

        setMagnifierParams: function () {
        },

        enableMagnifier: function () {
        },

        disableMagnifier: function (animateSize) {
        },

        moveMagnifierArea: function (e) {
        },

        inBound: function (rect, point) {
            return (
                (point.Y >= 0 && rect.height >= point.Y)
                && (point.X >= 0 && rect.width >= point.X)
            );
        },

        inRect: function (e, rect) {
            var wndSize = BX.GetWindowSize(),
                currentPos = {
                    X: 0,
                    Y: 0,
                    globalX: 0,
                    globalY: 0
                };

            currentPos.globalX = e.clientX + wndSize.scrollLeft;

            if (e.offsetX && e.offsetX < 0) {
                currentPos.globalX -= e.offsetX;
            }

            currentPos.X = currentPos.globalX - rect.left;
            currentPos.globalY = e.clientY + wndSize.scrollTop;

            if (e.offsetY && e.offsetY < 0) {
                currentPos.globalY -= e.offsetY;
            }

            currentPos.Y = currentPos.globalY - rect.top;

            return currentPos;
        },

        setProductMainPict: function (intPict) {
        },

        onSliderControlHover: function () {
        },

        onSliderControlLeave: function () {
        },

        selectSliderImg: function (target) {
        },

        setMainPict: function (intSlider, intPict, changePanelPict) {
        },

        setMainPictFromItem: function (index) {
            if (this.node.imageContainer) {
                var boolSet = false,
                    obNewPict = {};

                if (this.offers[index]) {
                    if (this.offers[index].DETAIL_PICTURE) {
                        obNewPict = this.offers[index].DETAIL_PICTURE;
                        boolSet = true;
                    } else if (this.offers[index].PREVIEW_PICTURE) {
                        obNewPict = this.offers[index].PREVIEW_PICTURE;
                        boolSet = true;
                    }
                }

                if (!boolSet) {
                    if (this.defaultPict.detail) {
                        obNewPict = this.defaultPict.detail;
                        boolSet = true;
                    } else if (this.defaultPict.preview) {
                        obNewPict = this.defaultPict.preview;
                        boolSet = true;
                    }
                }

                if (boolSet) {
                    this.setCurrentImg(obNewPict, true, true);
                }
            }
        },

        toggleMainPictPopup: function () {
        },

        showMainPictPopup: function () {
        },

        hideMainPictPopup: function () {
        },

        closeByEscape: function (event) {
            event = event || window.event;

            if (event.keyCode == 27) {
                this.hideMainPictPopup();
            }
        },

        startQuantityInterval: function () {
            var target = BX.proxy_context;
            var func = target.id === this.visual.QUANTITY_DOWN_ID
                ? BX.proxy(this.quantityDown, this)
                : BX.proxy(this.quantityUp, this);

            this.quantityDelay = setTimeout(
                BX.delegate(function () {
                    this.quantityTimer = setInterval(func, 150);
                }, this),
                300
            );
        },

        clearQuantityInterval: function () {
            clearTimeout(this.quantityDelay);
            clearInterval(this.quantityTimer);
        },

        quantityUp: function () {
            var curValue = 0,
                boolSet = true;

            if (this.errorCode === 0 && this.config.showQuantity && this.canBuy && !this.isGift) {
                curValue = this.isDblQuantity ? parseFloat(this.obQuantity.value) : parseInt(this.obQuantity.value, 10);
                if (!isNaN(curValue)) {
                    curValue += this.stepQuantity;

                    curValue = this.checkQuantityRange(curValue, 'up');

                    if (this.checkQuantity && curValue > this.maxQuantity) {
                        boolSet = false;
                    }

                    if (boolSet) {
                        if (this.isDblQuantity) {
                            curValue = Math.round(curValue * this.precisionFactor) / this.precisionFactor;
                        }

                        this.obQuantity.value = curValue;

                        this.setPrice();
                    }
                }
            }
        },

        quantityDown: function () {
            var curValue = 0,
                boolSet = true;

            if (this.errorCode === 0 && this.config.showQuantity && this.canBuy && !this.isGift) {
                curValue = (this.isDblQuantity ? parseFloat(this.obQuantity.value) : parseInt(this.obQuantity.value, 10));
                if (!isNaN(curValue)) {
                    curValue -= this.stepQuantity;

                    curValue = this.checkQuantityRange(curValue, 'down');

                    if (curValue < this.minQuantity) {
                        boolSet = false;
                    }

                    if (boolSet) {
                        if (this.isDblQuantity) {
                            curValue = Math.round(curValue * this.precisionFactor) / this.precisionFactor;
                        }

                        this.obQuantity.value = curValue;

                        this.setPrice();
                    }
                }
            }
        },

        quantityChange: function () {
            var curValue = 0,
                intCount;

            if (this.errorCode === 0 && this.config.showQuantity) {
                if (this.canBuy) {
                    curValue = this.isDblQuantity ? parseFloat(this.obQuantity.value) : Math.round(this.obQuantity.value);
                    if (!isNaN(curValue)) {
                        curValue = this.checkQuantityRange(curValue);

                        if (this.checkQuantity) {
                            if (curValue > this.maxQuantity) {
                                curValue = this.maxQuantity;
                            }
                        }

                        this.checkPriceRange(curValue);

                        intCount = Math.floor(
                            Math.round(curValue * this.precisionFactor / this.stepQuantity) / this.precisionFactor
                        ) || 1;
                        curValue = (intCount <= 1 ? this.stepQuantity : intCount * this.stepQuantity);
                        curValue = Math.round(curValue * this.precisionFactor) / this.precisionFactor;

                        if (curValue < this.minQuantity) {
                            curValue = this.minQuantity;
                        }

                        this.obQuantity.value = curValue;
                    } else {
                        this.obQuantity.value = this.minQuantity;
                    }
                } else {
                    this.obQuantity.value = this.minQuantity;
                }

                this.setPrice();
            }
        },

        quantitySet: function (index) {
            var strLimit, resetQuantity;

            var newOffer = this.offers[index],
                oldOffer = this.offers[this.offerNum];

            if (this.errorCode === 0) {
                this.canBuy = newOffer.CAN_BUY;

                this.currentPriceMode = newOffer.ITEM_PRICE_MODE;
                this.currentPrices = newOffer.ITEM_PRICES;
                this.currentPriceSelected = newOffer.ITEM_PRICE_SELECTED;
                this.currentQuantityRanges = newOffer.ITEM_QUANTITY_RANGES;
                this.currentQuantityRangeSelected = newOffer.ITEM_QUANTITY_RANGE_SELECTED;

                if (this.canBuy) {
                    this.node.quantity && BX.style(this.node.quantity, 'display', '');

                    this.obBasketActions && BX.style(this.obBasketActions, 'display', '');

                    this.obNotAvail && BX.style(this.obNotAvail, 'display', 'none');

                    this.obSubscribe && BX.style(this.obSubscribe, 'display', 'none');
                } else {
                    this.node.quantity && BX.style(this.node.quantity, 'display', 'none');

                    this.obBasketActions && BX.style(this.obBasketActions, 'display', 'none');

                    this.obNotAvail && BX.style(this.obNotAvail, 'display', '');

                    if (this.obSubscribe) {
                        if (newOffer.CATALOG_SUBSCRIBE === 'Y') {
                            BX.style(this.obSubscribe, 'display', '');
                            this.obSubscribe.setAttribute('data-item', newOffer.ID);
                            BX(this.visual.SUBSCRIBE_LINK + '_hidden').click();
                        } else {
                            BX.style(this.obSubscribe, 'display', 'none');
                        }
                    }
                }

                this.isDblQuantity = newOffer.QUANTITY_FLOAT;
                this.checkQuantity = newOffer.CHECK_QUANTITY;

                if (this.isDblQuantity) {
                    this.stepQuantity = Math.round(parseFloat(newOffer.STEP_QUANTITY) * this.precisionFactor) / this.precisionFactor;
                    this.maxQuantity = parseFloat(newOffer.MAX_QUANTITY);
                    this.minQuantity = this.currentPriceMode === 'Q' ? parseFloat(this.currentPrices[this.currentPriceSelected].MIN_QUANTITY) : this.stepQuantity;
                } else {
                    this.stepQuantity = parseInt(newOffer.STEP_QUANTITY, 10);
                    this.maxQuantity = parseInt(newOffer.MAX_QUANTITY, 10);
                    this.minQuantity = this.currentPriceMode === 'Q' ? parseInt(this.currentPrices[this.currentPriceSelected].MIN_QUANTITY) : this.stepQuantity;
                }

                if (this.config.showQuantity) {
                    var isDifferentMinQuantity = oldOffer.ITEM_PRICES.length
                        && oldOffer.ITEM_PRICES[oldOffer.ITEM_PRICE_SELECTED]
                        && oldOffer.ITEM_PRICES[oldOffer.ITEM_PRICE_SELECTED].MIN_QUANTITY != this.minQuantity;

                    if (this.isDblQuantity) {
                        resetQuantity = Math.round(parseFloat(oldOffer.STEP_QUANTITY) * this.precisionFactor) / this.precisionFactor !== this.stepQuantity
                            || isDifferentMinQuantity
                            || oldOffer.MEASURE !== newOffer.MEASURE
                            || (
                                this.checkQuantity
                                && parseFloat(oldOffer.MAX_QUANTITY) > this.maxQuantity
                                && parseFloat(this.obQuantity.value) > this.maxQuantity
                            );
                    } else {
                        resetQuantity = parseInt(oldOffer.STEP_QUANTITY, 10) !== this.stepQuantity
                            || isDifferentMinQuantity
                            || oldOffer.MEASURE !== newOffer.MEASURE
                            || (
                                this.checkQuantity
                                && parseInt(oldOffer.MAX_QUANTITY, 10) > this.maxQuantity
                                && parseInt(this.obQuantity.value, 10) > this.maxQuantity
                            );
                    }

                    this.obQuantity.disabled = !this.canBuy;

                    if (resetQuantity) {
                        this.obQuantity.value = this.minQuantity;
                    }

                    if (this.obMeasure) {
                        if (newOffer.MEASURE) {
                            BX.adjust(this.obMeasure, {html: newOffer.MEASURE});
                        } else {
                            BX.adjust(this.obMeasure, {html: ''});
                        }
                    }
                }

                if (this.obQuantityLimit.all) {
                    if (!this.checkQuantity || this.maxQuantity == 0) {
                        BX.adjust(this.obQuantityLimit.value, {html: BX.message('RELATIVE_QUANTITY_EMPTY')});
                        BX.adjust(this.obQuantityLimit.all, {style: {display: ''}});
                    } else {
                        if (this.config.showMaxQuantity === 'M') {
                            strLimit = (this.maxQuantity / this.stepQuantity >= this.config.relativeQuantityFactor)
                                ? BX.message('RELATIVE_QUANTITY_MANY')
                                : BX.message('RELATIVE_QUANTITY_FEW');
                        } else {
                            strLimit = this.maxQuantity;

                            if (newOffer.MEASURE) {
                                strLimit += (' ' + newOffer.MEASURE);
                            }
                        }

                        BX.adjust(this.obQuantityLimit.value, {html: strLimit});
                        BX.adjust(this.obQuantityLimit.all, {style: {display: ''}});
                    }
                }

                if (this.config.usePriceRanges && this.obPriceRanges) {
                    if (
                        this.currentPriceMode === 'Q'
                        && newOffer.PRICE_RANGES_HTML
                    ) {
                        var rangesBody = this.getEntity(this.obPriceRanges, 'price-ranges-body'),
                            rangesRatioHeader = this.getEntity(this.obPriceRanges, 'price-ranges-ratio-header');

                        if (rangesBody) {
                            rangesBody.innerHTML = newOffer.PRICE_RANGES_HTML;
                        }

                        if (rangesRatioHeader) {
                            rangesRatioHeader.innerHTML = newOffer.PRICE_RANGES_RATIO_HTML;
                        }

                        this.obPriceRanges.style.display = '';
                    } else {
                        this.obPriceRanges.style.display = 'none';
                    }

                }
            }
        },

        selectOfferProp: function () {
            var i = 0,
                strTreeValue = '',
                arTreeItem = [],
                rowItems = null,
                target = BX.proxy_context,
                smallCardItem;
            if (target && target.hasAttribute('data-treevalue')) {
                if (BX.hasClass(target, 'selected'))
                    return;

                if (typeof document.activeElement === 'object') {
                    document.activeElement.blur();
                }

                strTreeValue = target.getAttribute('data-treevalue');
                arTreeItem = strTreeValue.split('_');
                this.searchOfferPropIndex(arTreeItem[0], arTreeItem[1]);
                rowItems = BX.findChildren(target.parentNode, {tagName: 'li'}, false);

                if (rowItems && rowItems.length) {
                    for (i = 0; i < rowItems.length; i++) {
                        BX.removeClass(rowItems[i], 'selected');
                    }
                }

                BX.addClass(target, 'selected');
            }
        },

        searchOfferPropIndex: function (strPropID, strPropValue) {
            var strName = '',
                arShowValues = false,
                arCanBuyValues = [],
                allValues = [],
                index = -1,
                i, j,
                arFilter = {},
                tmpFilter = [];

            for (i = 0; i < this.treeProps.length; i++) {
                if (this.treeProps[i].ID === strPropID) {
                    index = i;
                    break;
                }
            }

            if (index > -1) {
                for (i = 0; i < index; i++) {
                    strName = 'PROP_' + this.treeProps[i].ID;
                    arFilter[strName] = this.selectedValues[strName];
                }

                strName = 'PROP_' + this.treeProps[index].ID;
                arFilter[strName] = strPropValue;

                for (i = index + 1; i < this.treeProps.length; i++) {
                    strName = 'PROP_' + this.treeProps[i].ID;
                    arShowValues = this.getRowValues(arFilter, strName);

                    if (!arShowValues)
                        break;

                    allValues = [];

                    if (this.config.showAbsent) {
                        arCanBuyValues = [];
                        tmpFilter = [];
                        tmpFilter = BX.clone(arFilter, true);

                        for (j = 0; j < arShowValues.length; j++) {
                            tmpFilter[strName] = arShowValues[j];
                            allValues[allValues.length] = arShowValues[j];
                            if (this.getCanBuy(tmpFilter))
                                arCanBuyValues[arCanBuyValues.length] = arShowValues[j];
                        }
                    } else {
                        arCanBuyValues = arShowValues;
                    }

                    if (this.selectedValues[strName] && BX.util.in_array(this.selectedValues[strName], arCanBuyValues)) {
                        arFilter[strName] = this.selectedValues[strName];
                    } else {
                        if (this.config.showAbsent) {
                            arFilter[strName] = (arCanBuyValues.length ? arCanBuyValues[0] : allValues[0]);
                        } else {
                            arFilter[strName] = arCanBuyValues[0];
                        }
                    }

                    this.updateRow(i, arFilter[strName], arShowValues, arCanBuyValues);
                }

                this.selectedValues = arFilter;
                this.changeInfo();
            }
        },

        updateRow: function (intNumber, activeId, showId, canBuyId) {
            var i = 0,
                value = '',
                isCurrent = false,
                rowItems = null;

            var lineContainer = this.getEntities(this.obTree, 'sku-line-block');

            if (intNumber > -1 && intNumber < lineContainer.length) {
                rowItems = lineContainer[intNumber].querySelectorAll('li');
                for (i = 0; i < rowItems.length; i++) {
                    value = rowItems[i].getAttribute('data-onevalue');
                    isCurrent = value === activeId;

                    if (isCurrent) {
                        BX.addClass(rowItems[i], 'selected');
                    } else {
                        BX.removeClass(rowItems[i], 'selected');
                    }

                    if (BX.util.in_array(value, canBuyId)) {
                        BX.removeClass(rowItems[i], 'notallowed');
                    } else {
                        BX.addClass(rowItems[i], 'notallowed');
                    }

                    rowItems[i].style.display = BX.util.in_array(value, showId) ? '' : 'none';

                    if (isCurrent) {
                        lineContainer[intNumber].style.display = (value == 0 && canBuyId.length == 1) ? 'none' : '';
                    }
                }

            }
        },

        getRowValues: function (arFilter, index) {
            var arValues = [],
                i = 0,
                j = 0,
                boolSearch = false,
                boolOneSearch = true;

            if (arFilter.length === 0) {
                for (i = 0; i < this.offers.length; i++) {
                    if (!BX.util.in_array(this.offers[i].TREE[index], arValues)) {
                        arValues[arValues.length] = this.offers[i].TREE[index];
                    }
                }
                boolSearch = true;
            } else {
                for (i = 0; i < this.offers.length; i++) {
                    boolOneSearch = true;

                    for (j in arFilter) {
                        if (arFilter[j] !== this.offers[i].TREE[j]) {
                            boolOneSearch = false;
                            break;
                        }
                    }

                    if (boolOneSearch) {
                        if (!BX.util.in_array(this.offers[i].TREE[index], arValues)) {
                            arValues[arValues.length] = this.offers[i].TREE[index];
                        }

                        boolSearch = true;
                    }
                }
            }

            return (boolSearch ? arValues : false);
        },

        getCanBuy: function (arFilter) {
            var i,
                j = 0,
                boolOneSearch = true,
                boolSearch = false;

            for (i = 0; i < this.offers.length; i++) {
                boolOneSearch = true;

                for (j in arFilter) {
                    if (arFilter[j] !== this.offers[i].TREE[j]) {
                        boolOneSearch = false;
                        break;
                    }
                }

                if (boolOneSearch) {
                    if (this.offers[i].CAN_BUY) {
                        boolSearch = true;
                        break;
                    }
                }
            }

            return boolSearch;
        },

        setCurrent: function () {
            var i,
                j = 0,
                strName = '',
                arShowValues = false,
                arCanBuyValues = [],
                arFilter = {},
                tmpFilter = [],
                current = this.offers[this.offerNum].TREE;

            for (i = 0; i < this.treeProps.length; i++) {
                strName = 'PROP_' + this.treeProps[i].ID;
                arShowValues = this.getRowValues(arFilter, strName);

                if (!arShowValues)
                    break;

                if (BX.util.in_array(current[strName], arShowValues)) {
                    arFilter[strName] = current[strName];
                } else {
                    arFilter[strName] = arShowValues[0];
                    this.offerNum = 0;
                }

                if (this.config.showAbsent) {
                    arCanBuyValues = [];
                    tmpFilter = [];
                    tmpFilter = BX.clone(arFilter, true);

                    for (j = 0; j < arShowValues.length; j++) {
                        tmpFilter[strName] = arShowValues[j];

                        if (this.getCanBuy(tmpFilter)) {
                            arCanBuyValues[arCanBuyValues.length] = arShowValues[j];
                        }
                    }
                } else {
                    arCanBuyValues = arShowValues;
                }

                this.updateRow(i, arFilter[strName], arShowValues, arCanBuyValues);
            }

            this.selectedValues = arFilter;
            this.changeInfo();
        },

        changeInfo: function () {
            var index = -1,
                j = 0,
                boolOneSearch = true,
                eventData = {
                    currentId: (this.offerNum > -1 ? this.offers[this.offerNum].ID : 0),
                    newId: 0
                };

            var i, offerGroupNode;

            for (i = 0; i < this.offers.length; i++) {
                boolOneSearch = true;

                for (j in this.selectedValues) {
                    if (this.selectedValues[j] !== this.offers[i].TREE[j]) {
                        boolOneSearch = false;
                        break;
                    }
                }

                if (boolOneSearch) {
                    index = i;
                    break;
                }
            }

            if (index > -1) {
                if (index != this.offerNum) {
                    this.isGift = false;
                }

                this.drawImages(this.offers[index].SLIDER);
                this.drawBigImage(this.offers[index].SLIDER);
                this.updateAvailabilityLabel(this.offers[index]);

                for (i = 0; i < this.offers.length; i++) {
                    if (this.config.showOfferGroup && this.offers[i].OFFER_GROUP) {
                        if (offerGroupNode = BX(this.visual.OFFER_GROUP + this.offers[i].ID)) {
                            offerGroupNode.style.display = (i == index ? '' : 'none');
                        }
                    }

                }

                if (this.config.showSkuProps) {
                    if (this.obSkuProps) {
                        if (!this.offers[index].DISPLAY_PROPERTIES) {
                            BX.adjust(this.obSkuProps, {style: {display: 'none'}, html: ''});
                        } else {
                            BX.adjust(this.obSkuProps, {
                                style: {display: ''},
                                html: this.offers[index].DISPLAY_PROPERTIES
                            });
                        }
                    }

                    if (this.obMainSkuProps) {
                        if (!this.offers[index].DISPLAY_PROPERTIES_MAIN_BLOCK) {
                            BX.adjust(this.obMainSkuProps, {style: {display: 'none'}, html: ''});
                        } else {
                            BX.adjust(this.obMainSkuProps, {
                                style: {display: ''},
                                html: this.offers[index].DISPLAY_PROPERTIES_MAIN_BLOCK
                            });
                        }
                    }
                }

                this.quantitySet(index);
                this.setPrice();
                this.setCompared(this.offers[index].COMPARED);

                this.offerNum = index;
                this.fixFontCheck();
                this.setAnalyticsDataLayer('showDetail');
                this.incViewedCounter();

                eventData.newId = this.offers[this.offerNum].ID;
                // only for compatible catalog.store.amount custom templates

                //проставляем id офера в функционал купить в 1 клик
                if (this.offers[this.offerNum].ID != 0) {
                    let buttonBuyOneClick = document.getElementById('wf_buy_one_click');
                    buttonBuyOneClick.setAttribute('data-pid', this.offers[this.offerNum].ID);
                    let formElementId = document.querySelector("#wf_b1c_form div.review-form__body input[name=element_id]").value = this.offers[this.offerNum].ID;
                }
                BX.onCustomEvent('onCatalogStoreProductChange', [this.offers[this.offerNum].ID]);
                // new event
                BX.onCustomEvent('onCatalogElementChangeOffer', [eventData]);
                eventData = null;
            }
        },

        drawImages: function (images) {
            if (!this.node.imageContainer) return;
            var beginEvent = new CustomEvent('drawImagesBegin'),
                endEvent = new CustomEvent('drawImagesEnd');

            document.dispatchEvent(beginEvent);

            var i, img, entities = this.getEntities(this.node.imageContainer, 'image');

            for (i in entities) {
                if (entities.hasOwnProperty(i) && BX.type.isDomNode(entities[i])) {
                    BX.remove(entities[i]);
                }
            }

            for (i = 0; i < images.length; i++) {
                img = BX.create('IMG', {
                    props: {
                        src: images[i].SRC,
                        alt: this.config.alt,
                        title: this.config.title
                    }
                });

                if (i == 0) {
                    img.setAttribute('itemprop', 'image');
                }

                var ia = BX.create('a', {
                    attrs: {
                        'data-zoom-id': 'big-preview',
                        'href': images[i].SRC,
                        'data-image': images[i].SRC
                    },
                    props: {
                        className: 'mz-thumb'
                    },
                    children: [img]
                });
                var div = BX.create('DIV', {
                    props: {
                        className: 'pic-ratio pic-ratio-6by5'
                    },
                    children: [ia]
                });
                this.node.imageContainer.appendChild(
                    BX.create('DIV', {
                        attrs: {
                            'data-entity': 'image',
                            'data-id': images[i].ID
                        },
                        props: {
                            className: 'product-detail__thumb-item'
                        },
                        children: [div]
                    })
                );
            }

            document.dispatchEvent(endEvent);
        },

        /*Обновление главной картинки товара*/
        drawBigImage: function (images) {
            if (images) {
                var bigPreviewLink = this.obBigSlider.querySelector('.big-zoom-preview'),
                    bigPreviewFig = bigPreviewLink.querySelector('.mz-figure'),
                    bigPreviewPic = this.obBigSlider.querySelectorAll('.big-zoom-preview img');
                var updatedEvent = new Event('sku-pics-updated');

                var startPicPreview = images[0].SRC;
                var starPicFullsize = startPicPreview;

                if (startPicPreview !== '' && typeof startPicPreview !== 'undefined') {
                    bigPreviewLink.setAttribute('href', starPicFullsize);
                    for (var i = 0; i < bigPreviewPic.length; i++) {
                        bigPreviewPic[i].setAttribute('src', startPicPreview);
                    }

                    if (window.MZ_READY)
                        MagicZoom.refresh(bigPreviewLink);

                    setTimeout(function () {
                        MagicZoom.update(bigPreviewLink, startPicPreview, startPicPreview);
                    }, 500);

                    setTimeout(function () {
                        document.dispatchEvent(updatedEvent);
                    }, 700);
                }
            }

        },

        /*Обновление лейбла с количеством остатков*/
        updateAvailabilityLabel: function (offer) {
            var canBy = offer.CAN_BUY,
                maxQuantity = offer.MAX_QUANTITY,
                qFactor = this.config.relativeQuantityFactor;

            var type = 'empty';
            if (canBy) {
                if (maxQuantity > qFactor)
                    type = 'high';
                else if (maxQuantity > 1)
                    type = 'medium';
                else if (maxQuantity == 1)
                    type = "low";
                else
                    type = 'empty';
            }

            var avLabel = document.querySelector('.product-detail__metasection .availability__scale');
            if (avLabel) {
                var img = document.createElement('img');
                img.setAttribute('src', "/assets/img/a-" + type + '.svg');
                img.classList.add('sprite-icon');

                avLabel.innerHTML = '';
                avLabel.appendChild(img);
            }

        },

        restoreSticker: function () {
            if (this.previousStickerText) {
                this.redrawSticker({text: this.previousStickerText});
            } else {
                this.hideSticker();
            }
        },

        hideSticker: function () {
            BX.hide(BX(this.visual.STICKER_ID));
        },

        redrawSticker: function (stickerData) {
            stickerData = stickerData || {};
            var text = stickerData.text || '';

            var sticker = BX(this.visual.STICKER_ID);
            if (!sticker)
                return;

            BX.show(sticker);

            var previousStickerText = sticker.getAttribute('title');
            if (previousStickerText && previousStickerText != text) {
                this.previousStickerText = previousStickerText;
            }

            BX.adjust(sticker, {text: text, attrs: {title: text}});
        },

        checkQuantityRange: function (quantity, direction) {
            if (typeof quantity === 'undefined' || this.currentPriceMode !== 'Q') {
                return quantity;
            }

            quantity = parseFloat(quantity);

            var nearestQuantity = quantity;
            var range, diffFrom, absDiffFrom, diffTo, absDiffTo, shortestDiff;

            for (var i in this.currentQuantityRanges) {
                if (this.currentQuantityRanges.hasOwnProperty(i)) {
                    range = this.currentQuantityRanges[i];

                    if (
                        parseFloat(quantity) >= parseFloat(range.SORT_FROM)
                        && (
                            range.SORT_TO === 'INF'
                            || parseFloat(quantity) <= parseFloat(range.SORT_TO)
                        )
                    ) {
                        nearestQuantity = quantity;
                        break;
                    } else {
                        diffFrom = parseFloat(range.SORT_FROM) - quantity;
                        absDiffFrom = Math.abs(diffFrom);
                        diffTo = parseFloat(range.SORT_TO) - quantity;
                        absDiffTo = Math.abs(diffTo);

                        if (shortestDiff === undefined || shortestDiff > absDiffFrom) {
                            if (
                                direction === undefined
                                || (direction === 'up' && diffFrom > 0)
                                || (direction === 'down' && diffFrom < 0)
                            ) {
                                shortestDiff = absDiffFrom;
                                nearestQuantity = parseFloat(range.SORT_FROM);
                            }
                        }

                        if (shortestDiff === undefined || shortestDiff > absDiffTo) {
                            if (
                                direction === undefined
                                || (direction === 'up' && diffFrom > 0)
                                || (direction === 'down' && diffFrom < 0)
                            ) {
                                shortestDiff = absDiffTo;
                                nearestQuantity = parseFloat(range.SORT_TO);
                            }
                        }
                    }
                }
            }

            return nearestQuantity;
        },

        checkPriceRange: function (quantity) {
            if (typeof quantity === 'undefined' || this.currentPriceMode !== 'Q') {
                return;
            }

            var range, found = false;

            for (var i in this.currentQuantityRanges) {
                if (this.currentQuantityRanges.hasOwnProperty(i)) {
                    range = this.currentQuantityRanges[i];

                    if (
                        parseFloat(quantity) >= parseFloat(range.SORT_FROM)
                        && (
                            range.SORT_TO === 'INF'
                            || parseFloat(quantity) <= parseFloat(range.SORT_TO)
                        )
                    ) {
                        found = true;
                        this.currentQuantityRangeSelected = range.HASH;
                        break;
                    }
                }
            }

            if (!found && (range = this.getMinPriceRange())) {
                this.currentQuantityRangeSelected = range.HASH;
            }

            for (var k in this.currentPrices) {
                if (this.currentPrices.hasOwnProperty(k)) {
                    if (this.currentPrices[k].QUANTITY_HASH == this.currentQuantityRangeSelected) {
                        this.currentPriceSelected = k;
                        break;
                    }
                }
            }
        },

        getMinPriceRange: function () {
            var range;

            for (var i in this.currentQuantityRanges) {
                if (this.currentQuantityRanges.hasOwnProperty(i)) {
                    if (
                        !range
                        || parseInt(this.currentQuantityRanges[i].SORT_FROM) < parseInt(range.SORT_FROM)
                    ) {
                        range = this.currentQuantityRanges[i];
                    }
                }
            }

            return range;
        },

        checkQuantityControls: function () {
            if (!this.obQuantity)
                return;

            var reachedTopLimit = this.checkQuantity && parseFloat(this.obQuantity.value) + this.stepQuantity > this.maxQuantity,
                reachedBottomLimit = parseFloat(this.obQuantity.value) - this.stepQuantity < this.minQuantity;

            if (reachedTopLimit) {
                BX.addClass(this.obQuantityUp, 'product-item-amount-field-btn-disabled');
            } else if (BX.hasClass(this.obQuantityUp, 'product-item-amount-field-btn-disabled')) {
                BX.removeClass(this.obQuantityUp, 'product-item-amount-field-btn-disabled');
            }

            if (reachedBottomLimit) {
                BX.addClass(this.obQuantityDown, 'product-item-amount-field-btn-disabled');
            } else if (BX.hasClass(this.obQuantityDown, 'product-item-amount-field-btn-disabled')) {
                BX.removeClass(this.obQuantityDown, 'product-item-amount-field-btn-disabled');
            }

            if (reachedTopLimit && reachedBottomLimit) {
                this.obQuantity.setAttribute('disabled', 'disabled');
            } else {
                this.obQuantity.removeAttribute('disabled');
            }
        },

        setPrice: function () {
            var economyInfo = '', price;

            if (this.obQuantity) {
                this.checkPriceRange(this.obQuantity.value);
            }

            this.checkQuantityControls();

            price = this.currentPrices[this.currentPriceSelected];

            if (this.isGift) {
                price.PRICE = 0;
                price.DISCOUNT = price.BASE_PRICE;
                price.PERCENT = 100;
            }

            if (this.obPrice.price) {
                if (price) {
                    BX.adjust(this.obPrice.price, {html: BX.Currency.currencyFormat(price.RATIO_PRICE, price.CURRENCY, true)});
                } else {
                    BX.adjust(this.obPrice.price, {html: ''});
                }

                if (price && price.RATIO_PRICE !== price.RATIO_BASE_PRICE) {
                    if (this.config.showOldPrice) {
                        this.obPrice.full && BX.adjust(this.obPrice.full, {
                            style: {display: ''},
                            html: BX.Currency.currencyFormat(price.RATIO_BASE_PRICE, price.CURRENCY, true)
                        });

                        if (this.obPrice.discount) {
                            economyInfo = BX.message('ECONOMY_INFO_MESSAGE');
                            economyInfo = economyInfo.replace('#ECONOMY#', BX.Currency.currencyFormat(price.RATIO_DISCOUNT, price.CURRENCY, true));
                            BX.adjust(this.obPrice.discount, {style: {display: ''}, html: economyInfo});
                        }
                    }

                    if (this.config.showPercent) {

                        if (this.obPrice.percent.parentElement) {
                            if (price.PERCENT > 0) this.obPrice.percent.parentElement.style.display = '';
                            if (price.PERCENT <= 0) this.obPrice.percent.parentElement.style.display = 'none';
                        }
                        this.obPrice.percent && BX.adjust(this.obPrice.percent, {
                            style: {display: ''},
                            html: -price.PERCENT + '%'
                        });
                    }
                } else {
                    if (this.config.showOldPrice) {
                        this.obPrice.full && BX.adjust(this.obPrice.full, {style: {display: 'none'}, html: ''});
                        this.obPrice.discount && BX.adjust(this.obPrice.discount, {style: {display: 'none'}, html: ''});
                    }

                    if (this.config.showPercent) {
                        this.obPrice.percent && BX.adjust(this.obPrice.percent, {style: {display: 'none'}, html: ''});
                    }
                }

                if (this.obPrice.total) {
                    if (price && this.obQuantity && this.obQuantity.value != this.stepQuantity) {
                        BX.adjust(this.obPrice.total, {
                            html: BX.message('PRICE_TOTAL_PREFIX') + ' <strong>'
                                + BX.Currency.currencyFormat(price.PRICE * this.obQuantity.value, price.CURRENCY, true)
                                + '</strong>',
                            style: {display: ''}
                        });
                    } else {
                        BX.adjust(this.obPrice.total, {
                            html: '',
                            style: {display: 'none'}
                        });
                    }
                }
            }
        },

        compare: function (event) {
            var checkbox = this.obCompare.querySelector('[data-entity="compare-checkbox"]'),
                target = BX.getEventTarget(event),
                checked = true;

            if (checkbox) {
                checked = target === checkbox ? checkbox.checked : !checkbox.checked;
            }

            var url = checked ? this.compareData.compareUrl : this.compareData.compareDeleteUrl,
                compareLink;

            if (url) {
                if (target !== checkbox) {
                    BX.PreventDefault(event);
                    this.setCompared(checked);
                }

                switch (this.productType) {
                    case 0: // no catalog
                    case 1: // product
                    case 2: // set
                        compareLink = url.replace('#ID#', this.product.id.toString());
                        break;
                    case 3: // sku
                        compareLink = url.replace('#ID#', this.offers[this.offerNum].ID);
                        break;
                }

                BX.ajax({
                    method: 'POST',
                    dataType: checked ? 'json' : 'html',
                    url: compareLink + (compareLink.indexOf('?') !== -1 ? '&' : '?') + 'ajax_action=Y',
                    onsuccess: checked
                        ? BX.proxy(this.compareResult, this)
                        : BX.proxy(this.compareDeleteResult, this)
                });
            }
        },

        compareResult: function (result) {
            var popupContent, popupButtons;

            if (this.obPopupWin) {
                this.obPopupWin.close();
            }

            if (!BX.type.isPlainObject(result))
                return;

            this.initPopupWindow();

            if (this.offers.length > 0) {
                this.offers[this.offerNum].COMPARED = result.STATUS === 'OK';
            }

            if (result.STATUS === 'OK') {
                BX.onCustomEvent('OnCompareChange');

                popupContent = '<div style="width: 100%; margin: 0; text-align: center;"><p>'
                    + BX.message('COMPARE_MESSAGE_OK')
                    + '</p></div>';

                if (this.config.showClosePopup) {
                    popupButtons = [
                        new BasketButton({
                            text: BX.message('BTN_MESSAGE_COMPARE_REDIRECT'),
                            events: {
                                click: BX.delegate(this.compareRedirect, this)
                            },
                            style: {marginRight: '10px'}
                        }),
                        new BasketButton({
                            text: BX.message('BTN_MESSAGE_CLOSE_POPUP'),
                            events: {
                                click: BX.delegate(this.obPopupWin.close, this.obPopupWin)
                            }
                        })
                    ];
                } else {
                    popupButtons = [
                        new BasketButton({
                            text: BX.message('BTN_MESSAGE_COMPARE_REDIRECT'),
                            events: {
                                click: BX.delegate(this.compareRedirect, this)
                            }
                        })
                    ];
                }
            } else {
                popupContent = '<div style="width: 100%; margin: 0; text-align: center;"><p>'
                    + (result.MESSAGE ? result.MESSAGE : BX.message('COMPARE_UNKNOWN_ERROR'))
                    + '</p></div>';
                popupButtons = [
                    new BasketButton({
                        text: BX.message('BTN_MESSAGE_CLOSE'),
                        events: {
                            click: BX.delegate(this.obPopupWin.close, this.obPopupWin)
                        }
                    })
                ];
            }

            this.obPopupWin.setTitleBar(BX.message('COMPARE_TITLE'));
            this.obPopupWin.setContent(popupContent);
            this.obPopupWin.setButtons(popupButtons);
            this.obPopupWin.show();
        },

        compareDeleteResult: function () {
            BX.onCustomEvent('OnCompareChange');

            if (this.offers && this.offers.length) {
                this.offers[this.offerNum].COMPARED = false;
            }
        },

        setCompared: function (state) {
            if (!this.obCompare)
                return;

            var checkbox = this.getEntity(this.obCompare, 'compare-checkbox');
            if (checkbox) {
                checkbox.checked = state;
            }
        },

        setCompareInfo: function (comparedIds) {
            if (!BX.type.isArray(comparedIds))
                return;

            for (var i in this.offers) {
                if (this.offers.hasOwnProperty(i)) {
                    this.offers[i].COMPARED = BX.util.in_array(this.offers[i].ID, comparedIds);
                }
            }
        },

        compareRedirect: function () {
            if (this.compareData.comparePath) {
                location.href = this.compareData.comparePath;
            } else {
                this.obPopupWin.close();
            }
        },

        checkDeletedCompare: function (id) {
            switch (this.productType) {
                case 0: // no catalog
                case 1: // product
                case 2: // set
                    if (this.product.id == id) {
                        this.setCompared(false);
                    }

                    break;
                case 3: // sku
                    var i = this.offers.length;
                    while (i--) {
                        if (this.offers[i].ID == id) {
                            this.offers[i].COMPARED = false;

                            if (this.offerNum == i) {
                                this.setCompared(false);
                            }

                            break;
                        }
                    }
            }
        },

        initBasketUrl: function () {
            this.basketUrl = (this.basketMode === 'ADD' ? this.basketData.add_url : this.basketData.buy_url);
            switch (this.productType) {
                case 1: // product
                case 2: // set
                    this.basketUrl = this.basketUrl.replace('#ID#', this.product.id.toString());
                    break;
                case 3: // sku
                    this.basketUrl = this.basketUrl.replace('#ID#', this.offers[this.offerNum].ID);
                    break;
            }

            this.basketParams = {
                'ajax_basket': 'Y'
            };

            if (this.config.showQuantity) {
                this.basketParams[this.basketData.quantity] = this.obQuantity.value;
            }

            if (this.basketData.sku_props) {
                this.basketParams[this.basketData.sku_props_var] = this.basketData.sku_props;
            }
        },

        fillBasketProps: function () {
            if (!this.visual.BASKET_PROP_DIV)
                return;

            var
                i = 0,
                propCollection = null,
                foundValues = false,
                obBasketProps = null;

            if (this.basketData.useProps && !this.basketData.emptyProps) {
                if (this.obPopupWin && this.obPopupWin.contentContainer) {
                    obBasketProps = this.obPopupWin.contentContainer;
                }
            } else {
                obBasketProps = BX(this.visual.BASKET_PROP_DIV);
            }

            if (obBasketProps) {
                propCollection = obBasketProps.getElementsByTagName('select');
                if (propCollection && propCollection.length) {
                    for (i = 0; i < propCollection.length; i++) {
                        if (!propCollection[i].disabled) {
                            switch (propCollection[i].type.toLowerCase()) {
                                case 'select-one':
                                    this.basketParams[propCollection[i].name] = propCollection[i].value;
                                    foundValues = true;
                                    break;
                                default:
                                    break;
                            }
                        }
                    }
                }

                propCollection = obBasketProps.getElementsByTagName('input');
                if (propCollection && propCollection.length) {
                    for (i = 0; i < propCollection.length; i++) {
                        if (!propCollection[i].disabled) {
                            switch (propCollection[i].type.toLowerCase()) {
                                case 'hidden':
                                    this.basketParams[propCollection[i].name] = propCollection[i].value;
                                    foundValues = true;
                                    break;
                                case 'radio':
                                    if (propCollection[i].checked) {
                                        this.basketParams[propCollection[i].name] = propCollection[i].value;
                                        foundValues = true;
                                    }
                                    break;
                                default:
                                    break;
                            }
                        }
                    }
                }
            }

            if (!foundValues) {
                this.basketParams[this.basketData.props] = [];
                this.basketParams[this.basketData.props][0] = 0;
            }
        },

        sendToBasket: function () {
            if (!this.canBuy)
                return;

            this.initBasketUrl();
            this.fillBasketProps();
            BX.ajax({
                method: 'POST',
                dataType: 'json',
                url: this.basketUrl,
                data: this.basketParams,
                onsuccess: BX.proxy(this.basketResult, this)
            });
        },

        add2Basket: function () {
            this.basketMode = 'ADD';
            this.basket();
        },

        buyBasket: function () {
            this.basketMode = 'BUY';
            this.basket();
        },

        basket: function () {
            var contentBasketProps = '';

            if (!this.canBuy)
                return;

            switch (this.productType) {
                case 1: // product
                case 2: // set
                    if (this.basketData.useProps && !this.basketData.emptyProps) {
                        this.initPopupWindow();
                        this.obPopupWin.setTitleBar(BX.message('TITLE_BASKET_PROPS'));

                        if (BX(this.visual.BASKET_PROP_DIV)) {
                            contentBasketProps = BX(this.visual.BASKET_PROP_DIV).innerHTML;
                        }

                        this.obPopupWin.setContent(contentBasketProps);
                        this.obPopupWin.setButtons([
                            new BasketButton({
                                text: BX.message('BTN_SEND_PROPS'),
                                events: {
                                    click: BX.delegate(this.sendToBasket, this)
                                }
                            })
                        ]);
                        this.obPopupWin.show();
                    } else {
                        this.sendToBasket();
                    }
                    break;
                case 3: // sku
                    this.sendToBasket();
                    break;
            }
        },

        basketResult: function (arResult) {
            var popupContent, popupButtons, productPict;

            if (this.obPopupWin) {
                this.obPopupWin.close();
            }

            if (!BX.type.isPlainObject(arResult))
                return;

            if (arResult.STATUS === 'OK') {
                this.setAnalyticsDataLayer('addToCart');
            }

            if (arResult.STATUS === 'OK' && this.basketMode === 'BUY') {
                this.basketRedirect();
            } else {
                this.initPopupWindow();

                if (arResult.STATUS === 'OK') {
                    BX.onCustomEvent('OnBasketChange');
                    switch (this.productType) {
                        case 1: // product
                        case 2: // set
                            productPict = this.product.pict.SRC;
                            break;
                        case 3: // sku
                            productPict = this.offers[this.offerNum].PREVIEW_PICTURE
                                ? this.offers[this.offerNum].PREVIEW_PICTURE.SRC
                                : this.defaultPict.pict.SRC;
                            break;
                    }

                    popupContent = '<div style="width: 100%; margin: 0; text-align: center;">'
                        + '<img src="' + productPict + '" height="130" style="max-height:130px"><p>'
                        + this.product.name + '</p></div>';

                    if (this.config.showClosePopup) {
                        popupButtons = [
                            new BasketButton({
                                text: BX.message('BTN_MESSAGE_BASKET_REDIRECT'),
                                events: {
                                    click: BX.delegate(this.basketRedirect, this)
                                },
                                style: {marginRight: '10px'}
                            }),
                            new BasketButton({
                                text: BX.message('BTN_MESSAGE_CLOSE_POPUP'),
                                events: {
                                    click: BX.delegate(this.obPopupWin.close, this.obPopupWin)
                                }
                            })
                        ];
                    } else {
                        popupButtons = [
                            new BasketButton({
                                text: BX.message('BTN_MESSAGE_BASKET_REDIRECT'),
                                events: {
                                    click: BX.delegate(this.basketRedirect, this)
                                }
                            })
                        ];
                    }
                } else {
                    popupContent = '<div style="width: 100%; margin: 0; text-align: center;"><p>'
                        + (arResult.MESSAGE ? arResult.MESSAGE : BX.message('BASKET_UNKNOWN_ERROR'))
                        + '</p></div>';
                    popupButtons = [
                        new BasketButton({
                            text: BX.message('BTN_MESSAGE_CLOSE'),
                            events: {
                                click: BX.delegate(this.obPopupWin.close, this.obPopupWin)
                            }
                        })
                    ];
                }

                this.obPopupWin.setTitleBar(arResult.STATUS === 'OK' ? BX.message('TITLE_SUCCESSFUL') : BX.message('TITLE_ERROR'));
                this.obPopupWin.setContent(popupContent);
                this.obPopupWin.setButtons(popupButtons);
                this.obPopupWin.show();
            }
        },

        basketRedirect: function () {
            location.href = (this.basketData.basketUrl ? this.basketData.basketUrl : BX.message('BASKET_URL'));
        },

        initPopupWindow: function () {
            if (this.obPopupWin)
                return;

            this.obPopupWin = BX.PopupWindowManager.create('CatalogElementBasket_' + this.visual.ID, null, {
                autoHide: false,
                offsetLeft: 0,
                offsetTop: 0,
                overlay: true,
                closeByEsc: true,
                titleBar: true,
                closeIcon: true,
                contentColor: 'white',
                className: this.config.templateTheme ? 'bx-' + this.config.templateTheme : ''
            });
        },

        incViewedCounter: function () {
            if (this.currentIsSet && !this.updateViewedCount) {
                switch (this.productType) {
                    case 1:
                    case 2:
                        this.viewedCounter.params.PRODUCT_ID = this.product.id;
                        this.viewedCounter.params.PARENT_ID = this.product.id;
                        break;
                    case 3:
                        this.viewedCounter.params.PARENT_ID = this.product.id;
                        this.viewedCounter.params.PRODUCT_ID = this.offers[this.offerNum].ID;
                        break;
                    default:
                        return;
                }

                this.viewedCounter.params.SITE_ID = BX.message('SITE_ID');
                this.updateViewedCount = true;
                BX.ajax.post(
                    this.viewedCounter.path,
                    this.viewedCounter.params,
                    BX.delegate(function () {
                        this.updateViewedCount = false;
                    }, this)
                );
            }
        },

        allowViewedCount: function (update) {
            this.currentIsSet = true;

            if (update) {
                this.incViewedCounter();
            }
        },

        fixFontCheck: function () {
        }
    }
})(window);

function compareSize(num) {
    var img = document.getElementById('compare-size-img'),
        src = img.getAttribute('src');

    if (num && src) {
        var newSrc = src.substring(0, src.length - 1) + num;
        img.setAttribute('src', newSrc);
    } else {
        console.log('Что-то пошло не так');
    }
}

//виджет сдека и вырисовываем плеер ютуб
window.addEventListener('scroll', function () {
    let elVideo = document.getElementById("video-youtube");
    if (elVideo) {
        let srcVideo = elVideo.dataset.video;
        let elIframe = elVideo.querySelector('.embed-responsive-item')
        elIframe.src = srcVideo + '?rel=0';
    }

    new ISDEKWidjet({
        showWarns: false,
        showErrors: true,
        showLogs: false,
        hideMessages: false,
        path: 'https://widget.cdek.ru/widget/scripts/',
        servicepath: 'https://timecube.ru/assets/widget/scripts/service.php',
        templatepath: 'https://timecube.ru/assets/widget/scripts/template.php',
        choose: false,
        popup: false,
        country: 'Россия',
        defaultCity: 'Москва',
        cityFrom: 'Москва',
        link: "forpvz",
        hidedress: true,
        hidecash: false,
        hidedelt: true,
        detailAddress: true,
        region: false,
        apikey: '778b5d13-4b60-42d4-b077-d060dc46da43',
        goods: [{
            length: parseInt(document.getElementById('showSdek').dataset.length),
            width: parseInt(document.getElementById('showSdek').dataset.width),
            height: parseInt(document.getElementById('showSdek').dataset.height),
            weight: parseInt( document.getElementById('showSdek').dataset.weight)
        }],
        onReady: onReady,
        onChoose: onChoose,
        onChooseProfile: onChooseProfile,
        onChooseAddress: onChooseAddress,
        onCalculate: onCalculate
    });
}, {once: true});


function onReady() {
    // alert('Виджет загружен');
}

function onChoose(wat) {
    alert(
        'Выбран пункт выдачи заказа ' + wat.id + "\n" +
        'цена ' + wat.price + "\n" +
        'срок ' + wat.term + " дн.\n" +
        'город ' + wat.cityName + ', код города ' + wat.city
    );
}

function onChooseProfile(wat) {
    alert(
        'Выбрана доставка курьером в город ' + wat.cityName + ', код города ' + wat.city + "\n" +
        'цена ' + wat.price + "\n" +
        'срок ' + wat.term + ' дн.'
    );
}

function onChooseAddress(wat) {
    alert(
        'Выбрана доставка курьером по адресу ' + wat.address + ', \n ' +
        'цена ' + wat.price + "\n" +
        'срок ' + wat.term + ' дн.'
    );
}

function onCalculate(params) {
    ipjq('#delPrice').html(+params.profiles.pickup.price + (+params.profiles.pickup.price * 0.1));
    ipjq('#delPriceCour').html(+params.profiles.courier.price + (+params.profiles.courier.price * 0.1));
    ipjq('#delTime').html(params.profiles.pickup.term);
}