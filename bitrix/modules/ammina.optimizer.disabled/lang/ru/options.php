<?

use Bitrix\Main\Localization\Loc;

$MESS['ammina.optimizer_PAGE_TITLE'] = "Ammina Optimizer: Оптимизация сайта (CSS, JS, HTML, изображения)";
$MESS['ammina.optimizer_TAB_SETTINGS_TITLE'] = "Настройки";
$MESS['ammina.optimizer_TAB_SETTINGS_DESC'] = "Настройки модуля";
$MESS['ammina.optimizer_TAB_SUPPORT_TITLE'] = "Техподдержка, развитие модуля";
$MESS['ammina.optimizer_TAB_SUPPORT_DESC'] = "Техническая поддержка и пожелания по функционалу модуля Ammina Optimizer: Оптимизация сайта (CSS, JS, HTML, изображения)";
$MESS['ammina.optimizer_TAB_SUPPORT_CONTENT'] = '
	<h3>Техническая поддержка</h3>
	<p>Техническая поддержка модуля осуществляется по электронной почте <a href="mailto:support@ammina.ru">support@ammina.ru</a></p>
	<h3>Развитие модуля, новый функционал</h3>
	<p>Если вы обнаружили что какого-то функционала модуля не хватает лично для вас - напишите нам.</p>
	<hr/>
	<h3>Наши контакты:</h3>
	<p>Электронная почта: <a href="mailto:support@ammina.ru">support@ammina.ru</a></p>
	<div style="clear:both;"></div>
';
$MESS['ammina.optimizer_TAB_RIGHTS_TITLE'] = "Права на доступ";
$MESS['ammina.optimizer_TAB_RIGHTS_DESC'] = "Настройка прав на доступ к модулю";

$MESS['ammina.optimizer_OPTION_DISABLED_PAGES'] = "Не использовать модуль на страницах";
$MESS['ammina.optimizer_OPTION_DISABLED_EDIT'] = "Не использовать модуль в режиме правки в публичной части";
$MESS['ammina.optimizer_OPTION_USE_AMMINA_PATHINFO'] = "Использовать функцию ammina_pathinfo вместо стандартной (при наличии файлов, содержащих русские символы)";
//$MESS['ammina.optimizer_OPTION_AJAX_ACTIVE'] = "Использовать оптимизацию на страницах AJAX запросов";
$MESS['ammina.optimizer_OPTION_GOOGLE_PAGESPEED_APIKEY'] = "API ключ к Google PageSpeed Insights";
$MESS['ammina.optimizer_OPTION_GOOGLE_PAGESPEED_APIKEY_GET'] = "Получить API ключ";
$MESS['ammina.optimizer_OPTION_AMMINA_APIKEY'] = "API ключ к сервису оптимизации файлов Ammina";
$MESS['ammina.optimizer_OPTION_AMMINA_APIKEY_GET'] = "Получить API ключ";
$MESS['ammina.optimizer_OPTION_SHOW_SUPPORT_FORM'] = "Показывать форму технической поддержки на административых страницах модуля";
$MESS['ammina.optimizer_OPTION_DEFAULT_DOMAIN'] = "Домен по умолчанию (при использовании фоновой оптимизации и сервера Ammina). При многосайтовой конфигурации в случае, если папка /upload/ является разной для разных сайтов - укажите основные домены через запятую";
$MESS['ammina.optimizer_OPTION_DEFAULT_ISHTTPS'] = "Протокол HTTPS по умолчанию (при использовании фоновой оптимизации и сервера Ammina)";
$MESS['ammina.optimizer_OPTION_USE_JSON_DECODE'] = "Использовать стандартную функцию json_decode с нормализацией вместо \\CUtil::JsObjectToPhp(). Связано с медленной работой стандартной функции в некоторых случаях";
$MESS['ammina.optimizer_OPTION_LOG_ERROR_REQUESTS'] = "Логировать ошибочные запросы к внешним ресурсам (/bitrix/ammina/ammina.optimizer/log/error_*)";
$MESS['ammina.optimizer_OPTION_LOG_SLOW_REQUESTS'] = "Логировать медленные запросы (>2 сек) к внешним ресурсам (/bitrix/ammina/ammina.optimizer/log/slow_*)";
$MESS['ammina.optimizer_OPTION_AUTOREDIRECT_WEBP'] = "Авторедирект на корректную страницу при наличии в строке запроса параметра iswebp (обратите внимание, что если вы используете для композитного режима управление на уровне Nginx, то перед включением опции необходимо обновить файл <a href='https://www.ammina.ru/upload/sysfiles/ammina_composite.conf'>ammina_composite.conf</a> с нашего сервера в конфигурации вашего Nginx.)";

$MESS['ammina.optimizer_OPTION_SEPARATOR_LIBRARY'] = "Настройки оптимизации CSS";
$MESS['ammina.optimizer_OPTION_LIB_PATH_YUICOMPRESSOR'] = "Путь к исполняемому файлу YUI Compressor";
$MESS['ammina.optimizer_OPTION_LIB_PATH_UGLIFYCSS'] = "Путь к исполняемому файлу Uglify CSS";
$MESS['ammina.optimizer_OPTION_LIB_PATH_UGLIFYJS'] = "Путь к исполняемому файлу UglifyJS";
$MESS['ammina.optimizer_OPTION_LIB_PATH_UGLIFYJS2'] = "Путь к исполняемому файлу UglifyJS 2";
$MESS['ammina.optimizer_OPTION_LIB_PATH_TERSERJS'] = "Путь к исполняемому файлу Terser JS";
$MESS['ammina.optimizer_OPTION_LIB_PATH_BABELMINIFY'] = "Путь к исполняемому файлу Babel Minify";
$MESS['ammina.optimizer_OPTION_LIB_PATH_OPTIPNG'] = "Путь к исполняемому файлу OptiPNG";
$MESS['ammina.optimizer_OPTION_LIB_PATH_PNGQUANT'] = "Путь к исполняемому файлу PNGQuant";
$MESS['ammina.optimizer_OPTION_LIB_PATH_JPEGOPTIM'] = "Путь к исполняемому файлу JpegOptim";
$MESS['ammina.optimizer_OPTION_LIB_PATH_GIFSICLE'] = "Путь к исполняемому файлу GIFSicle";
$MESS['ammina.optimizer_OPTION_LIB_PATH_SVGO'] = "Путь к исполняемому файлу SVGO";
$MESS['ammina.optimizer_OPTION_LIB_PATH_CWEBP'] = "Путь к исполняемому файлу CWebP";
$MESS['ammina.optimizer_OPTION_LIB_PATH_GIF2WEBP'] = "Путь к исполняемому файлу Gif2WebP (пакет CWebP)";

$MESS['ammina.optimizer_OPTION_SEPARATOR_PREOPT'] = "Предоптимизация изображений по событиям";
$MESS['ammina.optimizer_OPTION_PREOPT_SAVE_ACTIVE'] = "Предоптимизация изображений при сохранении изображения";
$MESS['ammina.optimizer_OPTION_PREOPT_RESIZE_ACTIVE'] = "Предоптимизация изображений при изменении размера изображения";

$MESS['ammina.optimizer_OPTION_SEPARATOR_PREOPTAGENT'] = "Агент фоновой оптимизации изображений";
$MESS['ammina.optimizer_OPTION_PREOPTAGENT_ACTIVE'] = "Активировать агента";
$MESS['ammina.optimizer_OPTION_PREOPTAGENT_ONLYCRON'] = "Исполнять агента фоновой оптимизации только в кроне за 1 цикл (иначе пошаговое исполнение на хитах)";
$MESS['ammina.optimizer_OPTION_PREOPTAGENT_PERIOD'] = "Период запуска агента фоновой оптимизации (мин.)";
$MESS['ammina.optimizer_OPTION_PREOPTAGENT_MAXTIME_STEP'] = "Длительность 1 шага при запуске агента фоновой оптимизации на хитах (сек.)";
$MESS['ammina.optimizer_OPTION_PREOPTAGENT_PERIOD_STEPS'] = "Время между шагами при запуске агента фоновой оптимизации на хитах (сек.)";
$MESS['ammina.optimizer_OPTION_PREOPTAGENT_MEMORYLIMIT'] = "Лимит памяти для агента (Мбайт). Если не установлен, то не менять системное значение";
$MESS['ammina.optimizer_OPTION_PREOPTAGENT_DIRS'] = "Какие каталоги проверяет агент";
$MESS['ammina.optimizer_PREOPTAGENT_STATUS'] = "Статус";
$MESS['ammina.optimizer_PREOPTAGENT_STATUS_NEXT'] = "В процессе.<br>Последний проверенный файл: #FILE#";
$MESS['ammina.optimizer_PREOPTAGENT_STATUS_OK'] = "Ожидание следующего цикла проверки";

$MESS['ammina.optimizer_OPTION_SEPARATOR_RESULTAGENT'] = "Агент фоновой проверки результатов оптимизации на AmminaServer";
$MESS['ammina.optimizer_OPTION_RESULTAGENT_ACTIVE'] = "Активировать агента";
$MESS['ammina.optimizer_OPTION_RESULTAGENT_ONLYCRON'] = "Исполнять агента фоновой проверки результатов оптимизации только в кроне за 1 цикл (иначе пошаговое исполнение на хитах)";
$MESS['ammina.optimizer_OPTION_RESULTAGENT_PERIOD'] = "Период запуска агента фоновой проверки результатов оптимизации (мин.)";
$MESS['ammina.optimizer_OPTION_RESULTAGENT_MAXTIME_STEP'] = "Длительность 1 шага при запуске агента фоновой проверки результатов оптимизации на хитах (сек.)";
$MESS['ammina.optimizer_OPTION_RESULTAGENT_PERIOD_STEPS'] = "Время между шагами при запуске агента фоновой проверки результатов оптимизации на хитах (сек.)";
$MESS['ammina.optimizer_OPTION_RESULTAGENT_MEMORYLIMIT'] = "Лимит памяти для агента (Мбайт). Если не установлен, то не менять системное значение";

$MESS['ammina.optimizer_OPTION_SEPARATOR_CLEARCACHEAGENT'] = "Агент фоновой очистки неиспользуемого кеша модуля";
$MESS['ammina.optimizer_OPTION_CLEARCACHEAGENT_ACTIVE'] = "Активировать агента";
$MESS['ammina.optimizer_OPTION_CLEARCACHEAGENT_ONLYCRON'] = "Исполнять агента фоновой очистки неиспользуемого кеша только в кроне за 1 цикл (иначе пошаговое исполнение на хитах)";
$MESS['ammina.optimizer_OPTION_CLEARCACHEAGENT_PERIOD'] = "Период запуска агента фоновой очистки неиспользуемого кеша (мин.)";
$MESS['ammina.optimizer_OPTION_CLEARCACHEAGENT_MAXTIME_STEP'] = "Длительность 1 шага при запуске агента очистки неиспользуемого кеша на хитах (сек.)";
$MESS['ammina.optimizer_OPTION_CLEARCACHEAGENT_PERIOD_STEPS'] = "Время между шагами при запуске агента очистки неиспользуемого кеша на хитах (сек.)";
$MESS['ammina.optimizer_OPTION_CLEARCACHEAGENT_MEMORYLIMIT'] = "Лимит памяти для агента (Мбайт). Если не установлен, то не менять системное значение";
$MESS['ammina.optimizer_OPTION_CLEARCACHEAGENT_TTL_CSS'] = "Время жизни CSS кеша с последнего использования (дней)";
$MESS['ammina.optimizer_OPTION_CLEARCACHEAGENT_TTL_JS'] = "Время жизни JS кеша с последнего использования (дней)";
$MESS['ammina.optimizer_OPTION_CLEARCACHEAGENT_TTL_IMAGE'] = "Время жизни кеша изображений с последнего использования (дней)";
