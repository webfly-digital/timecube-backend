(function (
    window, document,
    load_event,
    dequeue, func_queue,
    dom_loaded
) {
    dom_loaded = (/p/).test(document.readyState);

    function defer(func, delay) {
        var default_delay = 32;

        if (dom_loaded) {
            dequeue(func, delay || default_delay);
        } else {
            func_queue.push(func, delay);
        }
    }

    function flushqueue() {
        for (dom_loaded = 1; func_queue[0];) {
            defer(func_queue.shift(), func_queue.shift());
        }
    }

    function dom(tag, id, callback, dom) {
        if (!id || !document.getElementById(id)) {
            dom = document.createElement(tag || 'SCRIPT');

            if (id) {
                dom.id = id;
            }

            if (callback) {
                dom.onload = callback;
            }

            document.head.appendChild(dom);
        }

        return dom || {};
    }

    function deferscript(src, id, delay, callback) {
        defer(function () {
            dom('', id, callback).src = src
        }, delay);
    }

    window.addEventListener('on' + load_event in window ? load_event : 'load', flushqueue);

    defer._ = dom;
    window.defer = defer;
    window.deferscript = deferscript;

})(this, document, 'pageshow', setTimeout, []);


(function (window, document) {
    var ATTR_SRC = 'src';
    var ATTR_TYPE = 'type';

    var LOAD = 'load';
    var FOR_EACH = 'forEach';
    var GET_ATTRIBUTE = 'getAttribute';
    var SET_ATTRIBUTE = 'setAttribute';
    var HAS_ATTRIBUTE = 'hasAttribute';
    var REMOVE_ATTRIBUTE = 'removeAttribute';
    var NODE_NAME = 'nodeName';

    var NOOP = Function();
    var FALSE = false;
    var defer = window.defer || NOOP;
    var dom = defer._ || NOOP;

    function query(selector, parent) {
        return [].slice.call((parent || document).querySelectorAll(selector));
    }

    function defersmart() {
        function loadscript(scripts, tag, base, attr, value) {
            base = '[type=deferjs]';
            attr = '[async]';
            scripts = query(base + ':not(' + attr + ')').concat(query(base + attr));

            (function appendtag() {
                if (scripts == FALSE) {
                    return
                }

                base = scripts.shift();
                base.parentNode.removeChild(base);
                base[REMOVE_ATTRIBUTE](ATTR_TYPE);
                tag = dom(base[NODE_NAME]);

                for (attr in base) {
                    value = base[attr];
                    if (typeof value == 'string' && tag[attr] != value) {
                        tag[attr] = value;
                    }
                }

                if (tag[ATTR_SRC] && !tag[HAS_ATTRIBUTE]('async')) {
                    tag.onload = tag.onerror = appendtag;
                } else {
                    defer(appendtag, 0.1);
                }
            })();
        }
        defer(loadscript, 4);
    }

    defersmart();

    defer.all = defersmart;

})(this, document);

