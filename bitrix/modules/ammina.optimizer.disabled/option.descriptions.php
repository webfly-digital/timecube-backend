<?

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arAllowLibrary = \Ammina\Optimizer\Core2\LibAvailable::getCurrentCheckData();

$arLibraryCss = array(
	"sabberworm" => array(),
	/*"phpwee" => array(),
	"matthiasmullie" => array(),
	"yuicompressor" => array(
		"DISABLED" => !$arAllowLibrary['npm']['yuicompressor'],
	),
	"uglifycss" => array(
		"DISABLED" => !$arAllowLibrary['npm']['uglifycss'],
	),
	"amminayuicompressor" => array(
		"DISABLED" => !$arAllowLibrary['other']['amminaapi'],
	),
	"amminauglifycss" => array(
		"DISABLED" => !$arAllowLibrary['other']['amminaapi'],
	),*/
);
$arLibraryJs = array(
	//"phpwee" => array(),
	//"matthiasmullie" => array(),
	/*"yuicompressor" => array(
		"DISABLED" => !$arAllowLibrary['npm']['yuicompressor'],
	),*/
	"uglifyjs" => array(
		"DISABLED" => !$arAllowLibrary['npm']['uglifyjs'],
	),
	"uglifyjs2" => array(
		"DISABLED" => !$arAllowLibrary['npm']['uglifyjs2'],
	),
	"terserjs" => array(
		"DISABLED" => !$arAllowLibrary['npm']['terserjs'],
	),
	"babelminify" => array(
		"DISABLED" => !$arAllowLibrary['npm']['babelminify'],
	),
	/*"amminayuicompressor" => array(
		"DISABLED" => !$arAllowLibrary['other']['amminaapi'],
	),*/
	"amminauglifyjs" => array(
		"DISABLED" => !$arAllowLibrary['other']['amminaapi'],
	),
	"amminauglifyjs2" => array(
		"DISABLED" => !$arAllowLibrary['other']['amminaapi'],
	),
	"amminaterserjs" => array(
		"DISABLED" => !$arAllowLibrary['other']['amminaapi'],
	),
	"amminababelminify" => array(
		"DISABLED" => !$arAllowLibrary['other']['amminaapi'],
	),
);
$strDefaultLibraryJs = "phpwee";
if (!$arLibraryJs['uglifyjs']['DISABLED']) {
	$strDefaultLibraryJs = "uglifyjs";
} elseif (!$arLibraryJs['amminauglifyjs']['DISABLED']) {
	$strDefaultLibraryJs = "amminauglifyjs";
}
$arLibraryImagesJpg = array(
	"phpimagick" => array(
		"DISABLED" => !$arAllowLibrary['packages']['phpimagick'],
	),
	"jpegoptim" => array(
		"DISABLED" => !$arAllowLibrary['packages']['jpegoptim'],
	),
	"amminaphpimagick" => array(
		"DISABLED" => !$arAllowLibrary['other']['amminaapi'],
	),
	"amminajpegoptim" => array(
		"DISABLED" => !$arAllowLibrary['other']['amminaapi'],
	),
);
$strDefaultLibraryImagesJpg = "phpimagick";
if ($arLibraryImagesJpg['phpimagick']['DISABLED']) {
	if (!$arLibraryImagesJpg['jpegoptim']['DISABLED']) {
		$strDefaultLibraryImagesJpg = "jpegoptim";
	} elseif (!$arLibraryImagesJpg['amminaphpimagick']['DISABLED']) {
		$strDefaultLibraryImagesJpg = "amminaphpimagick";
	}
}
$arLibraryImagesPng = array(
	"phpimagick" => array(
		"DISABLED" => !$arAllowLibrary['packages']['phpimagick'],
	),
	"optipng" => array(
		"DISABLED" => !$arAllowLibrary['packages']['optipng'],
	),
	"pngquant" => array(
		"DISABLED" => !$arAllowLibrary['packages']['pngquant'],
	),
	"amminaphpimagick" => array(
		"DISABLED" => !$arAllowLibrary['other']['amminaapi'],
	),
	"amminaoptipng" => array(
		"DISABLED" => !$arAllowLibrary['other']['amminaapi'],
	),
	"amminapngquant" => array(
		"DISABLED" => !$arAllowLibrary['other']['amminaapi'],
	),
);
$strDefaultLibraryImagesPng = "pngquant";
if ($arLibraryImagesPng['pngquant']['DISABLED']) {
	if (!$arLibraryImagesPng['amminapngquant']['DISABLED']) {
		$strDefaultLibraryImagesPng = "amminapngquant";
	} elseif (!$arLibraryImagesPng['phpimagick']['DISABLED']) {
		$strDefaultLibraryImagesPng = "phpimagick";
	}
}
$arLibraryImagesGif = array(
	"phpimagick" => array(
		"DISABLED" => !$arAllowLibrary['packages']['phpimagick'],
	),
	"gifsicle" => array(
		"DISABLED" => !$arAllowLibrary['packages']['gifsicle'],
	),
	"amminaphpimagick" => array(
		"DISABLED" => !$arAllowLibrary['other']['amminaapi'],
	),
	"amminagifsicle" => array(
		"DISABLED" => !$arAllowLibrary['other']['amminaapi'],
	),
);
$arLibraryImagesSvg = array(
	"svgo" => array(
		"DISABLED" => !$arAllowLibrary['npm']['svgo'],
	),
	"amminasvgo" => array(
		"DISABLED" => !$arAllowLibrary['other']['amminaapi'],
	),
);
$strDefaultLibraryImagesSvg = "svgo";
if ($arLibraryImagesSvg['svgo']['DISABLED']) {
	if (!$arLibraryImagesSvg['amminasvgo']['DISABLED']) {
		$strDefaultLibraryImagesSvg = "amminasvgo";
	}
}
$arLibraryHtml = array(
	"amminaoptimizer" => array(),
);

$arLibraryParser = array(
	"domparser" => array(
		"DISABLED" => !$arAllowLibrary['packages']['dom'],
	),
	//"phpparser" => array(),
);
$arLibraryImagesWebP = array(
	"phpgd" => array(
		"DISABLED" => !$arAllowLibrary['packages']['phpgd'],
	),
	/*
	"phpimagick" => array(
		"DISABLED" => !$arAllowLibrary['packages']['phpimagickwebp'],
	),*/
	"cwebp" => array(
		"DISABLED" => !$arAllowLibrary['packages']['cwebp'],
	),
	"amminacwebp" => array(
		"DISABLED" => !$arAllowLibrary['other']['amminaapi'],
	),
);
$strDefaultLibraryImagesWebP = "cwebp";
if ($arLibraryImagesWebP['cwebp']['DISABLED']) {
	if (!$arLibraryImagesWebP['amminacwebp']['DISABLED']) {
		$strDefaultLibraryImagesWebP = "amminacwebp";
	} elseif (!$arLibraryImagesWebP['phpgd']['DISABLED']) {
		$strDefaultLibraryImagesWebP = "phpgd";
	}
}

$defaultParser = "domparser";
if (!$arAllowLibrary['packages']['dom']) {
	$defaultParser = "phpparser";
}
return array(
	"category" => array(
		"main" => array(
			"options" => array(
				"ACTIVE" => array(
					"TYPE" => "checkbox",
					"DEFAULT" => "N",
					"SHORT" => "Y",
				),
			),
			"groups" => array(
				"parse" => array(
					"options" => array(
						//Библиотека
						"LIBRARY" => array(
							"TYPE" => "select",
							"DEFAULT" => "domparser",
							"SHORT" => "Y",
							"VARIANTS" => $arLibraryParser,
						),
						"CHECK_ENCODING_UTF8" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						"CHECK_NOTVALID_START_TAG" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						"CHECK_NOTVALID_UTF8_SYMBOLS" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
					),
				),
				"request" => array(
					"options" => array(
						//Обрабатывать HTML запросы
						"ACTIVE_HTML" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),

						//Обрабатывать AJAX запросы
						"ACTIVE_AJAX" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						//Обрабатывать JSON запросы
						"ACTIVE_JSON" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						//Обрабатывать запросы компонентного ajax
						"ACTIVE_COMPONENT_AJAX" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						//Обрабатывать компонентный кэш
						"ACTIVE_COMPONENT_CACHE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						//Обрабатывать автокомпозит
						"ACTIVE_AUTOCOMPOSITE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),/*
						//Обрабатывать композит
						"ACTIVE_COMPOSITE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),*/
					),
				),
				"delay" => array(
					"options" => array(
						"ACTIVE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"TIME" => array(
							"TYPE" => "text",
							"DEFAULT" => "5000",
							"SHORT" => "N",
						),
						"TIME_BETWEEN_EXECUTE" => array(
							"TYPE" => "text",
							"DEFAULT" => "200",
							"SHORT" => "N",
						),
						"DELAY_YMETRIKA" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"DELAY_GTM" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"DELAY_GANALYTICS" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"DELAY_GRECAPTCHA" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"DELAY_ROISTAT" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"DELAY_BITRIXINFO" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"DELAY_BITRIXSPREAD" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"DELAY_BITRIX24" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"DELAY_JIVOSITE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"DELAY_REGMARKETS" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"DELAY_LIVETEX" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"DELAY_TALKME" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"DELAY_YACHAT" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"DELAY_REDHELPER" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"DELAY_SENDPULSE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"DELAY_MAILRU" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"DELAY_RAMBLERRU" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"DELAY_YANDEXMAPS" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"DELAY_FACEBOOK" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"DELAY_VK" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"OTHER_URL_EXCLUDE" => array(
							"TYPE" => "textarea",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
						"OTHER_URL_INCLUDE" => array(
							"TYPE" => "textarea",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
						"OTHER_JSCONTENT_EXCLUDE" => array(
							"TYPE" => "textarea",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
						"OTHER_JSCONTENT_INCLUDE" => array(
							"TYPE" => "textarea",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
					)
				),
				"other" => array(
					"options" => array(
						"CHECK_BXRAND_SCRIPT" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"CHECK_COMPONENT_AJAX_JSSCRIPT_EXISTS" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						"MOVE_JS_BODY" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"UNLOCK_SKIP_MOVE_JS_ASPRO" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"UNLOCK_SKIP_MOVE_JS_HEAD" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						"UNLOCK_SKIP_MOVE_JS_BODY" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						"REPLACE_BULLET" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						"REPLACE_HTML_ENTITY" => array(
							"TYPE" => "textarea",
							"DEFAULT" => "",
						),
						"DISABLE_MAIN_JOIN_CSS" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"DISABLE_MAIN_JOIN_JS" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"DISABLE_MAIN_MOVE_JS" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"MOVE_JS_BXSTAT_TOP" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"MAKE_STATIC_ASPRO_SETTHEME" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
					)
				)
			),
		),
		"css" => array(
			"options" => array(
				"ACTIVE" => array(
					"TYPE" => "checkbox",
					"DEFAULT" => "N",
					"SHORT" => "Y",
				),
			),
			"groups" => array(
				"minify" => array(
					"options" => array(
						//минифицировать
						"ACTIVE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						//Библиотека
						"LIBRARY" => array(
							"TYPE" => "select.options",
							"DEFAULT" => "sabberworm",
							"SHORT" => "Y",
							"VARIANTS" => $arLibraryCss,
						),
						//Не минифицировать файлы
						"EXCLUDE" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
						//Минифицировать файлы
						"INCLUDE" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
					),
				),
				"external_css" => array(
					"options" => array(
						//оптимизировать сторонние
						"ACTIVE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						//Исключить
						"EXCLUDE" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
						//Включить
						"INCLUDE" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
					),
				),
				"outline_css" => array(
					"options" => array(
						"ACTIVE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"INCLUDE_CONTENT" => array(
							"TYPE" => "textarea",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
						"EXCLUDE_CONTENT" => array(
							"TYPE" => "textarea",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
					)
				),
				"images" => array(
					"options" => array(
						//Обработка изображений из CSS
						"ACTIVE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						//Оптимизировать изображения
						"OPTIMIZE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						//Inline image
						"INLINE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						//Максимальный размер изображений inline
						"MAX_IMAGE_SIZE" => array(
							"TYPE" => "text.bytes",
							"DEFAULT" => "8192",
							"SHORT" => "Y",
						),
					),
				),
				"fonts" => array(
					"options" => array(
						//Оптимизация шрифтов
						"ACTIVE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						//отправлять заголовки
						"LINK" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
						),
						"LINK_ALL_FONTS" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
						),
						"LINK_FONTS" => array(
							"TYPE" => "text",
							"DEFAULT" => "",
						),
						"UNICODE_RANGE" => array(
							"TYPE" => "text",
							"DEFAULT" => "",
						),
						"NORMALIZE_ORDER" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
						),
						//Подставить font-face
						"FONT_FACE" => array(
							"TYPE" => "select",
							"DEFAULT" => "swap",
							"VARIANTS" => array(
								"none" => array(),
								"auto" => array(),
								"swap" => array(),
								"fallback" => array(),
							),
						),
						//Оптимизировать Google шрифты
						"GOOGLE_FONTS" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						//Тип оптимизации Google (с учетом user agent, локализация стандартная)
						"GOOGLE_FONTS_TYPE" => array(
							"TYPE" => "select",
							"DEFAULT" => "ua",
							"VARIANTS" => array(
								"ua" => array(),
								//"standart" => array(),
							),
						),
						//разместить шрифты inline
						"INLINE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
					),
				),
				"critical" => array(
					"options" => array(
						//Проверка критических CSS и включение их inline
						"ACTIVE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"USE_TAG_IDENT" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						"ADD_CLASS" => array(
							"TYPE" => "textarea",
							"DEFAULT" => "",
						),
						"ADD_IDENT" => array(
							"TYPE" => "textarea",
							"DEFAULT" => "",
						),
						"ONLY_CLASS" => array(
							"TYPE" => "textarea",
							"DEFAULT" => "",
						),
						"ONLY_IDENT" => array(
							"TYPE" => "textarea",
							"DEFAULT" => "",
						),
						"IGNORE_CLASS" => array(
							"TYPE" => "textarea",
							"DEFAULT" => "",
						),
						"IGNORE_IDENT" => array(
							"TYPE" => "textarea",
							"DEFAULT" => implode("\n", array("PART:bx_", "PART:sessid_", "PART:arrFilter_")),
						),
						"MAX_CRITICAL_RECORD" => array(
							"TYPE" => "text",
							"DEFAULT" => "5",
						),
						"USE_UNCRITICAL" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						"MODE_FULL" => array(
							"TYPE" => "select",
							"DEFAULT" => "link",
							"SHORT" => "Y",
							"VARIANTS" => array(
								"link" => array(),
								"script" => array(),
								"scriptwait" => array(),
							),
						),
						"WAIT_FULL" => array(
							"TYPE" => "text",
							"DEFAULT" => "100",
						),
					),
				),
				"other" => array(
					"options" => array(
						//Исключить из оптимизации CSS файлы
						"EXCLUDE_FILES" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
						),
						//Включить CSS inline в страницу
						"INLINE_CSS" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						//Максимальный размер CSS для inline
						"MAX_SIZE_INLINE" => array(
							"TYPE" => "text.bytes",
							"DEFAULT" => "65536",
							"SHORT" => "Y",
						),
						//Разместить Inline перед </body>
						"INLINE_BEFORE_BODY" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						"CHECK_BX_CSS" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
						),
						"MOVE_STYLE_BOTTOM" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
						),
						"DOUBLE_CONVERT_UTF8_FOR_WINDOWS_1251" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
						),
						/****
						 * //Разместить в CSS файлы Outline CSS
						 * "OUTLINE_CSS" => array(
						 * "TYPE" => "checkbox",
						 * "DEFAULT" => "Y",
						 * "SHORT" => "Y",
						 * ),
						 * //Минимальный размер ouline css для размещения в файлы
						 * "MIN_SIZE_OUTLINE" => array(
						 * "TYPE" => "text.bytes",
						 * "DEFAULT" => "1024",
						 * ),
						 * //Outline стили в отдельный файл
						 * "OUTLINE_TO_SEPARATE" => array(
						 * "TYPE" => "checkbox",
						 * "DEFAULT" => "N",
						 * "SHORT" => "Y",
						 * ),
						 * //Оптимизировать/минимизировать inline CSS
						 * "OPTIMIZE_INLINE" => array(
						 * "TYPE" => "checkbox",
						 * "DEFAULT" => "N",
						 * "SHORT" => "Y",
						 * ),
						 * "CHECK_BX_CSS" => array(
						 * "TYPE" => "checkbox",
						 * "DEFAULT" => "Y",
						 * ),*//*
						"CHECK_TAGS_IN_SCRIPT" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
						),*/
					),
				),
			),
		),
		"js" => array(
			"options" => array(
				"ACTIVE" => array(
					"TYPE" => "checkbox",
					"DEFAULT" => "N",
					"SHORT" => "Y",
				),
			),
			"groups" => array(
				"minify" => array(
					"options" => array(
						//минифицировать
						"ACTIVE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						//Библиотека
						"LIBRARY" => array(
							"TYPE" => "select.options",
							"DEFAULT" => $strDefaultLibraryJs,
							"SHORT" => "Y",
							"VARIANTS" => $arLibraryJs,
						),
						//Не минифицировать файлы
						"EXCLUDE" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
						//Минифицировать файлы
						"INCLUDE" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
					),
				),
				"external_js" => array(
					"options" => array(
						//оптимизировать сторонние
						"ACTIVE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						//Исключить
						"EXCLUDE" => array(
							"TYPE" => "files.static",
							"DEFAULT" => implode("\n", array("https://www.googletagmanager.com/")),
							"SHORT" => "N",
						),
						//Включить
						"INCLUDE" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
					),
				),
				"outline_js" => array(
					"options" => array(
						"ACTIVE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"INCLUDE_CONTENT" => array(
							"TYPE" => "textarea",
							"DEFAULT" => "window.lazySizesConfig = window.lazySizesConfig\n",
							"SHORT" => "N",
						),
						"EXCLUDE_CONTENT" => array(
							"TYPE" => "textarea",
							"DEFAULT" => "if(!window.BX)window.BX={};\nBX.setCSSList\n(window.BX||top.BX).message\nBX.setJSList\nbxSession\n_ba.push\nMENU_URL\nBXHotKeys.Add\nBX.admin\nBXHotKeys\nwindow.oBXSticker\nBitrixSmallCart\nJCTitleSearch\nBX.Iblock.SmartFilter\nBX.message\nJCSaleProductsGiftSectionComponent\nBX.Currency\nJCCatalogItem\nJCCatalogSectionComponent\nmailSender\nvar phpVars\namminaRegions\nbx_basket\nBX.Main.Menu\nJCSmartFilter",
							"SHORT" => "N",
						),
					)
				),
				"other" => array(
					"options" => array(
						//Включить JS inline в страницу
						"INLINE_JS" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						//Максимальный размер JS для inline
						"MAX_SIZE_INLINE" => array(
							"TYPE" => "text.bytes",
							"DEFAULT" => "65536",
							"SHORT" => "Y",
						),
						"DOUBLE_CONVERT_WIN1251" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						/****
						 * //Разместить в JS файлы Outline JS
						 * "OUTLINE_JS" => array(
						 * "TYPE" => "checkbox",
						 * "DEFAULT" => "Y",
						 * "SHORT" => "Y",
						 * ),
						 * //Минимальный размер ouline js для размещения в файлы
						 * "MIN_SIZE_OUTLINE" => array(
						 * "TYPE" => "text.bytes",
						 * "DEFAULT" => "1024",
						 * ),
						 * //Outline js в отдельный файл
						 * "OUTLINE_TO_SEPARATE" => array(
						 * "TYPE" => "checkbox",
						 * "DEFAULT" => "Y",
						 * "SHORT" => "Y",
						 * ),
						 * //Оптимизировать/минимизировать inline JS
						 * "OPTIMIZE_INLINE" => array(
						 * "TYPE" => "checkbox",
						 * "DEFAULT" => "Y",
						 * "SHORT" => "Y",
						 * ),*/
						//Исключить из оптимизации JS файлы
						"EXCLUDE_FILES" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
						),
					),
				),
				"ext" => array(
					"options" => array(
						//Дополнительная обработка файлов ядра
						"CHECK_CORE_FILES" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"CHECK_ENCODING" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						"JOIN_MODEL" => array(
							"TYPE" => "select.options",
							"DEFAULT" => "keeporder",
							"SHORT" => "Y",
							"VARIANTS" => array(
								"keeporder" => array(),
								"notjoin" => array(),
								"onlypreload" => array(),
								"bundle" => array(),
							)
						),
						"SET_DEFER" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						"EXCLUDE_DEFER" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
						),
						"INCLUDE_DEFER" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
						),
						"COMPOSITE_MOVE_END" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"COMPOSITE_MOVE_TIMEOUT" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"COMPOSITE_MOVE_TIMEOUT_VALUE" => array(
							"TYPE" => "text",
							"DEFAULT" => "1000",
						),
					),
				),
			),
		),
		"images" => array(
			"options" => array(
				"ACTIVE" => array(
					"TYPE" => "checkbox",
					"DEFAULT" => "N",
					"SHORT" => "Y",
				),
			),
			"groups" => array(
				"search_model" => array(
					"options" => array(
						//Обрабатывать тег IMG
						"CHECK_IMG" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"CHECK_SRCSET_IMG" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						//Обрабатывать стиль background[-image]
						"CHECK_BACKGROUND" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						//Обрабатывать data-src
						"CHECK_DATA_SRC" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						//Обрабатывать все аттрибуты
						"CHECK_ATTRIBUTES" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						//Обрабатывать все в кавычках
						"CHECK_ALL_OTHER" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
					)
				),
				"jpg_files" => array(
					"options" => array(
						//Активно
						"ACTIVE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						//Качество
						"QUALITY" => array(
							"TYPE" => "text",
							"DEFAULT" => "80",
							"SHORT" => "N",
						),
						/*
						//Обработка только изображений (для всех) из каталогов
						"IMAGES_FROM_DIR" => array(
							"TYPE" => "files.static",
							"DEFAULT" => array("/upload/", "/local/templates/", "/bitrix/templates/"),
							"SHORT" => "N",
						),
						*/
						//Библиотека оптимизации
						"LIBRARY" => array(
							"TYPE" => "select.options",
							"DEFAULT" => $strDefaultLibraryImagesJpg,
							"SHORT" => "Y",
							"VARIANTS" => $arLibraryImagesJpg,
						),
						//Исключить файлы
						"EXCLUDE_FILES" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
						//Включить файлы
						"INCLUDE_FILES" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
						//Преобразовать в WebP
						"CONVERT_WEBP" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						//Исключить файлы
						"EXCLUDE_WEBP_FILES" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
						//Включить файлы
						"INCLUDE_WEBP_FILES" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
					),
				),
				"png_files" => array(
					"options" => array(
						//Обработка PNG файлов
						"ACTIVE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						//Библиотека оптимизации
						"LIBRARY" => array(
							"TYPE" => "select.options",
							"DEFAULT" => $strDefaultLibraryImagesPng,
							"SHORT" => "Y",
							"VARIANTS" => $arLibraryImagesPng,
						),
						//Исключить файлы
						"EXCLUDE_FILES" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
						//Включить файлы
						"INCLUDE_FILES" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
						//Преобразовать в WebP
						"CONVERT_WEBP" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						//Исключить из конвертации файлы
						"EXCLUDE_WEBP_FILES" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
						//Включить в конвертацию файлы
						"INCLUDE_WEBP_FILES" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
						//преобразовать в JPG
						"CONVERT_JPG" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						//преобразовать в JPG
						"CONVERT_JPG_BG" => array(
							"TYPE" => "text",
							"DEFAULT" => "ffffff",
							"SHORT" => "Y",
							"DISABLED" => !$arAllowLibrary['packages']['phpgd'],
						),
						//Исключить из конвертации файлы
						"EXCLUDE_JPG_FILES" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
						//Включить в конвертацию файлы
						"INCLUDE_JPG_FILES" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
					),
				),
				"gif_files" => array(
					"options" => array(
						//Обработка HIF файлов
						"ACTIVE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						//Качество
						"QUALITY" => array(
							"TYPE" => "text",
							"DEFAULT" => "75",
							"SHORT" => "N",
						),
						//Библиотека оптимизации
						"LIBRARY" => array(
							"TYPE" => "select.options",
							"DEFAULT" => "phpimagick",
							"SHORT" => "Y",
							"VARIANTS" => $arLibraryImagesGif,
						),
						//Исключить файлы
						"EXCLUDE_FILES" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
						//Включить файлы
						"INCLUDE_FILES" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
						//Преобразовать в WebP
						"CONVERT_WEBP" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						//Исключить из конвертации файлы
						"EXCLUDE_WEBP_FILES" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
						//Включить в конвертацию файлы
						"INCLUDE_WEBP_FILES" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
						//преобразовать в JPG
						"CONVERT_JPG" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						//преобразовать в JPG
						"CONVERT_JPG_BG" => array(
							"TYPE" => "text",
							"DEFAULT" => "ffffff",
							"SHORT" => "Y",
							"DISABLED" => !$arAllowLibrary['packages']['phpgd'],
						),
						//Исключить из конвертации файлы
						"EXCLUDE_JPG_FILES" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
						//Включить в конвертацию файлы
						"INCLUDE_JPG_FILES" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
					),
				),
				"svg_files" => array(
					"options" => array(
						//Активно
						"ACTIVE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						/*
						//Обработка только изображений (для всех) из каталогов
						"IMAGES_FROM_DIR" => array(
							"TYPE" => "files.static",
							"DEFAULT" => array("/upload/", "/local/templates/", "/bitrix/templates/"),
							"SHORT" => "N",
						),
						*/
						//Библиотека оптимизации
						"LIBRARY" => array(
							"TYPE" => "select.options",
							"DEFAULT" => $strDefaultLibraryImagesSvg,
							"SHORT" => "Y",
							"VARIANTS" => $arLibraryImagesSvg,
						),
						//Исключить файлы
						"EXCLUDE_FILES" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
						//Включить файлы
						"INCLUDE_FILES" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
					),
				),
				"webp_files" => array(
					"options" => array(
						//Активно
						"ACTIVE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						//Качество
						"QUALITY" => array(
							"TYPE" => "text",
							"DEFAULT" => "80",
							"SHORT" => "N",
						),
						//Библиотека оптимизации
						"LIBRARY" => array(
							"TYPE" => "select.options",
							"DEFAULT" => $strDefaultLibraryImagesWebP,
							"SHORT" => "Y",
							"VARIANTS" => $arLibraryImagesWebP,
						),
						//Исключить файлы
						"EXCLUDE_FILES" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
						//Включить файлы
						"INCLUDE_FILES" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
					),
				),
				/****
				 * "events" => array(
				 * "options" => array(
				 * //оптимизировать сторонние
				 * "ACTIVE" => array(
				 * "TYPE" => "checkbox",
				 * "DEFAULT" => "Y",
				 * "SHORT" => "Y",
				 * ),
				 * //Обрабатывать событие сохранения изображения
				 * "USE_EVENTS_CHANGE" => array(
				 * "TYPE" => "checkbox",
				 * "DEFAULT" => "Y",
				 * "SHORT" => "Y",
				 * ),
				 * //Обрабатывать событие изменения размера
				 * "USE_EVENTS_RESIZE" => array(
				 * "TYPE" => "checkbox",
				 * "DEFAULT" => "Y",
				 * "SHORT" => "Y",
				 * ),
				 * //Исключить
				 * "EXCLUDE" => array(
				 * "TYPE" => "files.static",
				 * "DEFAULT" => "",
				 * "SHORT" => "N",
				 * ),
				 * //Включить
				 * "INCLUDE" => array(
				 * "TYPE" => "files.static",
				 * "DEFAULT" => "",
				 * "SHORT" => "N",
				 * ),
				 * ),
				 * ),*/
				"external_images" => array(
					"options" => array(
						//оптимизировать сторонние
						"ACTIVE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						//Исключить
						"EXCLUDE" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
						//Включить
						"INCLUDE" => array(
							"TYPE" => "files.static",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
					),
				),
				"lazy" => array(
					"options" => array(
						//Активировать LazyLaod
						"ACTIVE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						//Класс для отложенной загрузки
						"LAZY_CLASS" => array(
							"TYPE" => "text",
							"DEFAULT" => "lazyclass",
							"SHORT" => "Y",
						),
						"CHECK_ALL_IMG" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						//Тип lazyLoad
						"TYPE" => array(
							"TYPE" => "select",
							"DEFAULT" => "N",
							"SHORT" => "Y",
							"VARIANTS" => array(
								"blur" => array(),
								"blurgrey" => array(),
								"mainimg" => array(),
							),
						),
						"MAINIMG" => array(
							"TYPE" => "file",
							"DEFAULT" => "/bitrix/",
							"SHORT" => "N",
						),
						//Преобразовать в WebP
						"CONVERT_WEBP" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"INLINE_IMG" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"MAX_SIZE_INLINE" => array(
							"TYPE" => "text.bytes",
							"DEFAULT" => "8192",
						),
						"INCLUDE_JQUERY" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
					),
				),
				"other" => array(
					"options" => array(
						//Разместить изображения Inline
						"INLINE_IMG" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						//Максимальный размер inline изображений
						"MAX_SIZE_INLINE" => array(
							"TYPE" => "text.bytes",
							"DEFAULT" => "2048",
						),
						//Типы файлов для inline изображений
						"INLINE_TYPES" => array(
							"TYPE" => "text",
							"DEFAULT" => "jpg,jpeg,png,gif,svg,webp",
						),
						"PRELOAD_IMG" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						"PRELOAD_IMG_COUNT" => array(
							"TYPE" => "text",
							"DEFAULT" => "30",
						),
						"NOT_CHANGE_ORIGINALS" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						/*//Изменить размер изображений в соответствии с аттрибутами data
						"RESIZE_IMG" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),*/
						/*//Заменить тег IMG на тег Picture для изображений
						"IMG_TO_PICTURE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),*/
					),
				),
			),
		),
		"html" => array(
			"options" => array(
				"ACTIVE" => array(
					"TYPE" => "checkbox",
					"DEFAULT" => "N",
					"SHORT" => "Y",
				),
			),
			"groups" => array(
				/*"html" => array(
					"options" => array(
						"ACTIVE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						//Библиотека
						"LIBRARY" => array(
							"TYPE" => "select.options",
							"DEFAULT" => "amminaoptimizer",
							"SHORT" => "Y",
							"VARIANTS" => $arLibraryHtml,
						),
					),
				),
				*/
				"tags" => array(
					"options" => array(
						"ACTIVE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						//Удалить комментарии
						"REMOVE_COMMENTS" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "N",
						),
						//Удалить тег pre
						"REMOVE_PRE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "N",
						),
						//Удалить лишние аттрибуты тега script
						"REMOVE_ATTR_SCRIPT" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "N",
						),
						//Удалить лишние аттрибуты тега style
						"REMOVE_ATTR_STYLE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "N",
						),
						//Удалить лишние пробелы, табуляции и тд
						"REMOVE_WHITE_SPACE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "N",
						),
					),
				),
			),
		),
		"other" => array(
			"options" => array(
				"ACTIVE" => array(
					"TYPE" => "checkbox",
					"DEFAULT" => "N",
					"SHORT" => "Y",
				),
			),
			"groups" => array(
				"headers" => array(
					"options" => array(
						//Отправлять заголовки preload
						"ACTIVE_PRELOAD" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						//Перечень preload заголовков
						"PRELOAD" => array(
							"TYPE" => "headers.preload",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
						//Отправлять заголовки prefetch
						"ACTIVE_PREFETCH" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						//Перечень prefetch заголовков
						"PREFETCH" => array(
							"TYPE" => "headers.prefetch",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
						//Отправлять заголовки preconnect
						"ACTIVE_PRECONNECT" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "N",
							"SHORT" => "Y",
						),
						//Перечень preconnect заголовков
						"PRECONNECT" => array(
							"TYPE" => "headers.preconnect",
							"DEFAULT" => "",
							"SHORT" => "N",
						),
						//Учитывать UserAgent при отправке prefetch/preconnect заголовков
						"ACTIVE_USERAGENTS" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
					),
				),
				"links" => array(
					"options" => array(
						"DELETE_OLD_LINKS" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						//Установить к HTML также теги link в соответствии с настройками headers
						"SET_LINKS" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						"ONLY_COMPOSITE" => array(
							"TYPE" => "checkbox",
							"DEFAULT" => "Y",
							"SHORT" => "Y",
						),
						//Тип линков по умолчанию (для композита)
						"LINKS_TYPE" => array(
							"TYPE" => "select",
							"DEFAULT" => "preload",
							"SHORT" => "Y",
							"VARIANTS" => array(
								"prefetch" => array(),
								"preload" => array(),
							),
						),
					),
				),
			),
		),
	),
);