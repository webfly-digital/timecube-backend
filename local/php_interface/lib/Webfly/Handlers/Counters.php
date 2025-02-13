<?php

namespace Webfly\Handlers;

class Counters
{
    const TIME_OUT = 3500;

    public static function insert()
    {
        global $USER;
        echo "<script>" . PHP_EOL;
        echo "(function () {
                var analyticCountersLoaded = false,                    
                    timerId;
                if (navigator.userAgent.indexOf('YandexMetrika') > -1) {
                    loadAnalyticCounters();
                } else {
                    document.addEventListener( 'scroll', loadAnalyticCounters);
                    document.addEventListener( 'touchstart', loadAnalyticCounters);
                    document.addEventListener( 'mousemove', loadAnalyticCounters);
                    document.addEventListener( 'click', loadAnalyticCounters);
                    //document.addEventListener( 'DOMContentLoaded', loadAnalyticCountersByTimeout);
		        }

                function loadAnalyticCountersByTimeout() {
                    timerId = setTimeout(loadAnalyticCounters, " . self::TIME_OUT . ");
                }

                function loadAnalyticCounters(e) {
                    if (e && e.type) {
                        console.log(e.type);
                    } else {
                        console.log('DOMContentLoaded');
                    }

                    if (analyticCountersLoaded) {
                        return;
                    }

                    setTimeout(
                        function () {" . PHP_EOL;
        self::gtm();
        self::googleAnalytics();
        self::yaMetrika();
        if(!$USER->isAdmin()) self::jivo();
        echo "},
                        100
                    );

                    analyticCountersLoaded = true;
                    clearTimeout(timerId);
                    document.removeEventListener('scroll', loadAnalyticCounters);
                    document.removeEventListener('touchstart', loadAnalyticCounters);
                    document.removeEventListener('mousemove', loadAnalyticCounters);
                    document.removeEventListener('click', loadAnalyticCounters);
                    //document.removeEventListener('DOMContentLoaded', loadAnalyticCountersByTimeout);
                }
            })()" . PHP_EOL;
        echo "</script>" . PHP_EOL;
    }


    protected function gtm()
    {

        echo <<<GTM
                //Global Tag Manager
                (function (w, d, s, l, i) {
        w[l] = w[l] || [];
        w[l].push({
            'gtm.start':
                new Date().getTime(), event: 'gtm.js'
        });
        var f = d.getElementsByTagName(s)[0],
            j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
        j.async = true;
        j.src =
            'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
        f.parentNode.insertBefore(j, f);
          let iframe = document.createElement("iframe");
                (iframe.src = "https://www.googletagmanager.com/ns.html?id=GTM-55TJDZD"),
                (iframe.style.display = "none"),
                 (iframe.style.visibility = "hidden"),
                document.head.appendChild(iframe);
           
    })(window, document, 'script', 'dataLayer', 'GTM-55TJDZD');
GTM;

    }

    protected function googleAnalytics()
    {

        echo <<<GA
                //Global site tag (gtag.js) - Google Analytics
              window.dataLayer = window.dataLayer || [];
    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', 'UA-27153558-1');
     let script = document.createElement("script");
                (script.src = "https://www.googletagmanager.com/gtag/js?id=UA-27153558-1"),
                (script.async = !0),
                document.head.appendChild(script);
GA;
    }


    protected function yaMetrika()
    {

        echo <<<YA_METRIKA
             (function (m, e, t, r, i, k, a) {
            m[i] = m[i] || function () {
                (m[i].a = m[i].a || []).push(arguments)
            };
            m[i].l = 1 * new Date();
            k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
        })
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(2121766, "init", {
            clickmap: true,
            trackLinks: true,
            accurateTrackBounce: true,
            webvisor: true,
            ecommerce: "dataLayer"
        });
YA_METRIKA;

    }

    protected function jivo()
    {
        echo <<<JIVO
            (function () {
            var widget_id = 'sQTM4Cg616';
            var d = document;
            var w = window;

            function l() {
                var s = document.createElement('script');
                s.type = 'text/javascript';
                s.async = true;
                s.src = '//code.jivosite.com/script/widget/' + widget_id;
                var ss = document.getElementsByTagName('script')[0];
                ss.parentNode.insertBefore(s, ss);
            }

            if (d.readyState == 'complete') {
                l();
            } else {
                if (w.attachEvent) {
                    w.attachEvent('onload', l);
                } else {
                    w.addEventListener('load', l, false);
                }
            }
               let script = document.createElement("script");
                (script.src = "//code.jivosite.com/widget/sQTM4Cg616"),
                (script.async = !0),
                document.head.appendChild(script);
           
        })();
JIVO;
    }
}