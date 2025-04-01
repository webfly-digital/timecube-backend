<?
$MESS['AMMINA_OPTIMIZER_PAGE_TITLE'] = "Проверка доступности библиотек";
$MESS['AMMINA_OPTIMIZER_CHECK_GROUP_PACKAGES_TITLE'] = "Програмные пакеты";
$MESS['AMMINA_OPTIMIZER_CHECK_GROUP_PACKAGES_DESCRIPTION'] = "Програмные пакеты операционной системы, предназначенные для различных видов оптимизаций сайта.";
$MESS['AMMINA_OPTIMIZER_CHECK_GROUP_PACKAGES_HELP'] = '
<p>Програмные пакеты операционной системы, предназначенные для различных видов оптимизаций сайта.</p>
<p>В зависимости от используемой операционной системы сервера могут быть RPM (для операционных систем CenOS) или DEB (Debian или Ubuntu)</p>
<p>Установка части пакетов (в частности модулей PHP) обычно осуществляется через панель управления хостингов.</p>
<p>Если установка через панель управления хостингом невозможна, то необходимо иметь доступ к серверу хостинга по SSH протоколу пользователя с административными правами (root или с правами sudo).</p>
<p>Полная установка всех нижеуказанных пакетов (за исключением PHP модулей curl, dom, gd), а также программных пакетов Node.js для использования следующей группы (Node.js модули).</p>
<p><strong>Для операционных систем на базе CentOS (VMBitrix).</strong></p>
<p>sudo yum install npm nodejs jre jpegoptim optipng pngquant gifsicle php-imagick libwebp-tools</p>
<p><strong>Для операционных систем на базе Ubuntu/Debian.</strong></p>
<p>sudo apt install npm nodejs-legacy default-jre jpegoptim optipng pngquant gifsicle php-imagick webp</p>
';
$MESS['AMMINA_OPTIMIZER_CHECK_GROUP_NPM_TITLE'] = "Node.js модули";
$MESS['AMMINA_OPTIMIZER_CHECK_GROUP_NPM_DESCRIPTION'] = "Node.js модули, необходимые для минификации CSS, JS файлов а так же SVG изображений";
$MESS['AMMINA_OPTIMIZER_CHECK_GROUP_NPM_HELP'] = '
<p>Данные модули предназначены для минификации CSS, JS и SVG файлов.</p>
<p>Для использования данных модулей необходима установка на сервере хостинга Node.JS и NPM программных пакетов (см. предыдущую группу).</p>
<p><strong>Полная установка модулей:</strong></p>
<p> sudo npm install uglify-js uglify-js2 terser babel-minify html-minifier svgo -g </p>
';
$MESS['AMMINA_OPTIMIZER_CHECK_GROUP_OTHER_TITLE'] = "Прочие возможности";
$MESS['AMMINA_OPTIMIZER_CHECK_GROUP_OTHER_DESCRIPTION'] = "Прочие возможности модуля для оптимизации и аудита сайта";
$MESS['AMMINA_OPTIMIZER_CHECK_GROUP_OTHER_HELP'] = '
<p>В данном блоке проверок можно увидеть доступность дополнительного функционала модуля:</p>
<p>1. Ключ Google API - позволяет проводить аудит производительности страниц сайта.</p>
<p>2. Ключ Ammina API - позволяет воспользоваться всеми библиотеками оптимизации на любом хостинге. Оптимизация осуществляется на серверах Ammina.</p>
<p>Для получения ключей вам необходимо перейти в настройки модуля и получить ключи по соответствующим ссылкам.</p>
';

$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_DOM_TITLE'] = "PHP DOM";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_DOM_DESCRIPTION'] = "Пакет DOM является расширение PHP. Предназначен для парсинга HTML кода.";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_DOM_HELP'] = "";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_CURL_TITLE'] = "PHP Curl";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_CURL_DESCRIPTION'] = "Пакет CURL является расширение PHP. Предназначен для запросов по HTTP/HTTPS протоколам удаленных сайтов (например загрузка Google Font)";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_CURL_HELP'] = "";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_PHPIMAGICK_TITLE'] = "PHP Imagick";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_PHPIMAGICK_DESCRIPTION'] = "Пакет PHP Imagick является расширение PHP. Предназначен для оптимизации изображений сайта.";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_PHPIMAGICK_HELP'] = "";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_PHPIMAGICKWEBP_TITLE'] = "PHP Imagick (поддержка WebP)";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_PHPIMAGICKWEBP_DESCRIPTION'] = "Пакет PHP Imagick является расширение PHP. Предназначен для оптимизации изображений сайта. Проверка поддержки PHPImagick формата WebP";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_PHPIMAGICKWEBP_HELP'] = "";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_PHPGD_TITLE'] = "PHP GD";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_PHPGD_DESCRIPTION'] = "Пакет PHP GD является расширение PHP. Необходим для преобразования изображений в WebP формат";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_PHPGD_HELP'] = "";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_JPEGOPTIM_TITLE'] = "JPEG Optim";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_JPEGOPTIM_DESCRIPTION'] = "Пакет JPEGOptim позволяет оптимизировать JPEG изображения";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_JPEGOPTIM_HELP'] = "";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_OPTIPNG_TITLE'] = "Opti PNG";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_OPTIPNG_DESCRIPTION'] = "Пакет OptimPNG позволяет оптимизировать PNG изображения без потери качества";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_OPTIPNG_HELP'] = "";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_PNGQUANT_TITLE'] = "PNG Quant";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_PNGQUANT_DESCRIPTION'] = "Пакет PNGQuant позволяет оптимизировать PNG изображения с потерей качества";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_PNGQUANT_HELP'] = "";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_GIFSICLE_TITLE'] = "GIFSicle";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_GIFSICLE_DESCRIPTION'] = "Пакет GIFSicle позволяет оптимизировать GIF изображения";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_GIFSICLE_HELP'] = "";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_SVGO_TITLE'] = "SVGO";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_SVGO_DESCRIPTION'] = "Пакет SVGO позволяет оптимизировать SVG изображения";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_SVGO_HELP'] = "";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_CWEBP_TITLE'] = "CWebP";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_CWEBP_DESCRIPTION'] = "Пакет CWebP позволяет оптимизировать WebP изображения";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_CWEBP_HELP'] = "";

$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_NPM_YUICOMPRESSOR_TITLE'] = "YUI Compressor";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_NPM_YUICOMPRESSOR_DESCRIPTION'] = "Позволяет минифицировать CSS и JS файлы";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_NPM_YUICOMPRESSOR_HELP'] = "";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_NPM_UGLIFYCSS_TITLE'] = "Uglify CSS";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_NPM_UGLIFYCSS_DESCRIPTION'] = "Позволяет минифицировать CSS файлы";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_NPM_UGLIFYCSS_HELP'] = "";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_NPM_UGLIFYJS_TITLE'] = "Uglify JS";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_NPM_UGLIFYJS_DESCRIPTION'] = "Позволяет минифицировать JS файлы. (<strong>Рекомендуется разработчиком модуля Ammina.Optimizer для минификации JS</strong>)";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_NPM_UGLIFYJS_HELP'] = "";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_NPM_UGLIFYJS2_TITLE'] = "Uglify JS 2";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_NPM_UGLIFYJS2_DESCRIPTION'] = "Позволяет минифицировать JS файлы.";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_NPM_UGLIFYJS2_HELP'] = "";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_NPM_TERSERJS_TITLE'] = "Terser JS";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_NPM_TERSERJS_DESCRIPTION'] = "Позволяет минифицировать JS файлы.";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_NPM_TERSERJS_HELP'] = "";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_NPM_BABELMINIFY_TITLE'] = "Babel minify";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_NPM_BABELMINIFY_DESCRIPTION'] = "Позволяет минифицировать JS файлы. ";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_NPM_BABELMINIFY_HELP'] = "";

$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_OTHER_GOOGLEAPI_TITLE'] = "Ключ Google API";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_OTHER_GOOGLEAPI_DESCRIPTION'] = "Необходим для проведения аудита производительности сайта";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_OTHER_GOOGLEAPI_HELP'] = "";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_OTHER_AMMINAAPI_TITLE'] = "Ключ Ammina API";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_OTHER_AMMINAAPI_DESCRIPTION'] = "Позволяет проводить минификацию файлов и оптимизацию изображений на серверах оптимизации Ammina. Рекомендуется использовать при невозможности установить Node.JS модули и програмные пакеты на имеющемся хостинге. Оптимизация ресурсов происходит с задержкой, но позволяет воспользоваться всеми возможностями модуля даже на виртуальном хостинге";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_OTHER_AMMINAAPI_HELP'] = "";

$MESS['AMMINA_OPTIMIZER_CHECK_GROUP_BXOPTIONS_TITLE'] = "Настройки 1С-Битрикс";
$MESS['AMMINA_OPTIMIZER_CHECK_GROUP_BXOPTIONS_DESCRIPTION'] = "Проверка реомендуемых настроек 1С-Битрикс для максимальной производительности сайта";
$MESS['AMMINA_OPTIMIZER_CHECK_GROUP_BXOPTIONS_HELP'] = "";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_OPTIMIZE_CSS_FILES_TITLE'] = "Объединять CSS файлы - выключено";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_OPTIMIZE_CSS_FILES_DESCRIPTION'] = "Настройка <a href=\"/bitrix/admin/settings.php?lang=ru&mid=main&tabControl_active_tab=edit1\" target='_blank'>главного модуля</a> (группа Оптимизация CSS). Необходимое состояние - выключено";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_OPTIMIZE_CSS_FILES_HELP'] = "";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_OPTIMIZE_JS_FILES_TITLE'] = "Объединять JS файлы - выключено";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_OPTIMIZE_JS_FILES_DESCRIPTION'] = "Настройка <a href=\"/bitrix/admin/settings.php?lang=ru&mid=main&tabControl_active_tab=edit1\" target='_blank'>главного модуля</a> (группа Оптимизация CSS). Необходимое состояние - выключено";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_OPTIMIZE_JS_FILES_HELP'] = "";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_USE_MINIFIED_ASSETS_TITLE'] = "Подключать минифицированные версии CSS и JS файлов - выключено";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_USE_MINIFIED_ASSETS_DESCRIPTION'] = "Настройка <a href=\"/bitrix/admin/settings.php?lang=ru&mid=main&tabControl_active_tab=edit1\" target='_blank'>главного модуля</a> (группа Оптимизация CSS). Необходимое состояние - выключено";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_USE_MINIFIED_ASSETS_HELP'] = "";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_MOVE_JS_TO_BODY_TITLE'] = "Переместить весь Javascript в конец страницы - выключено";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_MOVE_JS_TO_BODY_DESCRIPTION'] = "Настройка <a href=\"/bitrix/admin/settings.php?lang=ru&mid=main&tabControl_active_tab=edit1\" target='_blank'>главного модуля</a> (группа Оптимизация CSS). Рекомендуемое состояние - выключено. Более быстрым аналогом (при больших объемах кода страницы) данной настройки является соответствующий параметр оптимизации в модуле.";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_MOVE_JS_TO_BODY_HELP'] = "";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_COMPRES_CSS_JS_FILES_TITLE'] = "Создавать сжатую копию объединенных CSS и JS файлов - выключено";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_COMPRES_CSS_JS_FILES_DESCRIPTION'] = "Настройка <a href=\"/bitrix/admin/settings.php?lang=ru&mid=main&tabControl_active_tab=edit1\" target='_blank'>главного модуля</a> (группа Оптимизация CSS). Необходимое состояние - выключено";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_COMPRES_CSS_JS_FILES_HELP'] = "";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_CDN_TITLE'] = "CDN - выключен";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_CDN_DESCRIPTION'] = "Настройка <a href=\"/bitrix/admin/bitrixcloud_cdn.php?lang=ru\" target='_blank'>ускорение сайта CDN</a>. Необходимое состояние на период настройки оптимизации - выключено";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_CDN_HELP'] = "";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_COMPOSITE_TITLE'] = "Композит - выключен";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_COMPOSITE_DESCRIPTION'] = "Активность композитного/автокомпозитного <a href=\"/bitrix/admin/composite.php?lang=ru\" target='_blank'>режима</a>. Необходимое состояние на период настройки оптимизации - выключено. После оптимизации страниц сайта при выключенном композитном режиме, его можно включить и повторно проверить корректность оптимизации сайта";
$MESS['AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_COMPOSITE_HELP'] = "";
$MESS[''] = "";
$MESS[''] = "";
$MESS[''] = "";

