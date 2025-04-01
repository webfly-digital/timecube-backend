<?

namespace Ammina\Optimizer\Core2\Optimizer;

use Ammina\Optimizer\Core2\Application;

class Delay
{
	protected $arOptions = false;
	protected $arAllJSScript = array();
	protected $moveJsBxstatTop = false;

	public function __construct($arOptions, $moveJsBxstatTop = false)
	{
		$this->setOptions($arOptions);
		$this->setMoveJsBxstatTop($moveJsBxstatTop);
	}

	public function setOptions($arOptions)
	{
		$this->arOptions = $arOptions;
	}

	public function setMoveJsBxstatTop($moveJsBxstatTop)
	{
		$this->moveJsBxstatTop = $moveJsBxstatTop;
	}

	public function doOptimize()
	{
		global $APPLICATION;
		if ($this->arOptions['ACTIVE'] == "Y") {
			$this->arAllJSScript = Application::getInstance()->getParser()->getAllJsScript(true);
			$startTimeDelay = intval($this->arOptions['TIME']);
			if ($startTimeDelay < 100) {
				$startTimeDelay = 100;
			}
			$stepTimeDelay = intval($this->arOptions['TIME_BETWEEN_EXECUTE']);
			if ($stepTimeDelay < 50) {
				$stepTimeDelay = 50;
			}
			$arUrlExclude = explode("\n", $this->arOptions['OTHER_URL_EXCLUDE']);
			$arUrlInclude = explode("\n", $this->arOptions['OTHER_URL_INCLUDE']);
			$arContentExclude = explode("\n", $this->arOptions['OTHER_JSCONTENT_EXCLUDE']);
			$arContentInclude = explode("\n", $this->arOptions['OTHER_JSCONTENT_INCLUDE']);
			foreach ($arUrlExclude as $k => $v) {
				$arUrlExclude[$k] = trim($v);
				if (amopt_strlen($arUrlExclude[$k]) <= 0) {
					unset($arUrlExclude[$k]);
				}
			}
			foreach ($arUrlInclude as $k => $v) {
				$arUrlInclude[$k] = trim($v);
				if (amopt_strlen($arUrlInclude[$k]) <= 0) {
					unset($arUrlInclude[$k]);
				}
			}
			foreach ($arContentExclude as $k => $v) {
				$arContentExclude[$k] = trim($v);
				if (amopt_strlen($arContentExclude[$k]) <= 0) {
					unset($arContentExclude[$k]);
				}
			}
			foreach ($arContentInclude as $k => $v) {
				$arContentInclude[$k] = trim($v);
				if (amopt_strlen($arContentInclude[$k]) <= 0) {
					unset($arContentInclude[$k]);
				}
			}
			$oParser = Application::getInstance()->getParser();
			foreach ($this->arAllJSScript as $k => $v) {
				if (!empty($arUrlExclude)) {
					foreach ($arUrlExclude as $k1 => $v1) {
						if (amopt_stripos($v['src'], $v1) !== false) {
							continue(2);
						}
					}
				}
				if (!empty($arUrlInclude)) {
					foreach ($arUrlInclude as $k1 => $v1) {
						if (amopt_stripos($v['src'], $v1) !== false) {
							$oParser->setJsDelayScript($k, '', $v['src'], $startTimeDelay);
							$startTimeDelay += $stepTimeDelay;
							continue(2);
						}
					}
				}
				if (!empty($arContentExclude)) {
					foreach ($arContentExclude as $k1 => $v1) {
						if (amopt_stripos($v['CONTENT'], $v1) !== false) {
							continue(2);
						}
					}
				}
				if (!empty($arContentInclude)) {
					foreach ($arContentInclude as $k1 => $v1) {
						if (amopt_stripos($v['CONTENT'], $v1) !== false) {
							$oParser->setJsDelayScript($k, $v['CONTENT'], '', $startTimeDelay);
							$startTimeDelay += $stepTimeDelay;
							continue(2);
						}
					}
				}
				if ($this->arOptions['DELAY_YMETRIKA'] == "Y" && amopt_stripos($_SERVER['HTTP_USER_AGENT'], 'YandexMetrika') === false) {
					if (amopt_strpos($v['CONTENT'], 'mc.yandex.ru/metrika/tag.js') !== false || amopt_strpos($v['CONTENT'], '/npm/yandex-metrica-watch/tag.js') !== false || amopt_strpos($v['CONTENT'], '/metrika/watch.js') !== false) {
						$oParser->setJsDelayScript($k, $v['CONTENT'], '', $startTimeDelay);
						$startTimeDelay += $stepTimeDelay;
						continue;
					}
				}
				if ($this->arOptions['DELAY_GTM'] == "Y") {
					if (amopt_strpos($v['src'], 'googletagmanager.com/gtag/') !== false) {
						$oParser->setJsDelayScript($k, '', $v['src'], $startTimeDelay);
						$startTimeDelay += $stepTimeDelay;
						continue;
					}
					if (amopt_strpos($v['CONTENT'], 'googletagmanager.com/gtm.js') !== false) {
						$oParser->setJsDelayScript($k, $v['CONTENT'], '', $startTimeDelay);
						$startTimeDelay += $stepTimeDelay;
						continue;
					}
				}
				if ($this->arOptions['DELAY_GANALYTICS'] == "Y") {
					if (amopt_strpos($v['CONTENT'], 'google-analytics.com/analytics.js') !== false) {
						$oParser->setJsDelayScript($k, $v['CONTENT'], '', $startTimeDelay);
						$startTimeDelay += $stepTimeDelay;
						continue;
					}
				}
				if ($this->arOptions['DELAY_GRECAPTCHA'] == "Y") {
					if (amopt_strpos($v['CONTENT'], 'google.com/recaptcha/api.js') !== false || amopt_stripos($v['CONTENT'], 'asprorecaptcha') !== false) {
						$oParser->setJsDelayScript($k, $v['CONTENT'], '', $startTimeDelay);
						$startTimeDelay += $stepTimeDelay;
						continue;
					}
					if (amopt_strpos($v['src'], 'google.com/recaptcha/api.js') !== false) {
						$oParser->setJsDelayScript($k, '', $v['src'], $startTimeDelay);
						$startTimeDelay += $stepTimeDelay;
						continue;
					}
				}
				if ($this->arOptions['DELAY_ROISTAT'] == "Y") {
					if (amopt_strpos($v['CONTENT'], 'cloud.roistat.com') !== false) {
						$oParser->setJsDelayScript($k, $v['CONTENT'], '', $startTimeDelay);
						$startTimeDelay += $stepTimeDelay;
						continue;
					}
				}
				if ($this->arOptions['DELAY_BITRIXSPREAD'] == "Y") {
					if (amopt_strpos($v['CONTENT'], '/bitrix/spread.php') !== false) {
						$oParser->setJsDelayScript($k, $v['CONTENT'], '', $startTimeDelay);
						$startTimeDelay += $stepTimeDelay;
						continue;
					}
				}

				if ($this->moveJsBxstatTop) {
					if (amopt_strpos($v['CONTENT'], 'bitrix.info/ba.js') !== false) {
						$oParser->moveJsToHeadStart($k);
						continue;
					}
				} elseif ($this->arOptions['DELAY_BITRIXINFO'] == "Y") {
					if (amopt_strpos($v['CONTENT'], 'bitrix.info/ba.js') !== false) {
						$oParser->setJsDelayScript($k, $v['CONTENT'], '', $startTimeDelay);
						$startTimeDelay += $stepTimeDelay;
						continue;
					}
				}
				if ($this->arOptions['DELAY_BITRIX24'] == "Y") {
					if (amopt_strpos($v['CONTENT'], '/crm/site_button/') !== false || amopt_strpos($v['CONTENT'], '/upload/crm/') !== false) {
						$oParser->setJsDelayScript($k, $v['CONTENT'], '', $startTimeDelay);
						$startTimeDelay += $stepTimeDelay;
						continue;
					}
				}
				if ($this->arOptions['DELAY_REGMARKETS'] == "Y") {
					if (amopt_strpos($v['src'], 'regmarkets.ru/js/') !== false) {
						$oParser->setJsDelayScript($k, '', $v['src'], $startTimeDelay);
						$startTimeDelay += $stepTimeDelay;
						continue;
					}
				}
				if ($this->arOptions['DELAY_JIVOSITE'] == "Y") {
					if (amopt_strpos($v['src'], 'code.jivosite.com/widget/') !== false) {
						$oParser->setJsDelayScript($k, '', $v['src'], $startTimeDelay);
						$startTimeDelay += $stepTimeDelay;
						continue;
					}
					if (amopt_strpos($v['CONTENT'], 'code.jivosite.com/script') !== false) {
						$oParser->setJsDelayScript($k, $v['CONTENT'], '', $startTimeDelay);
						$startTimeDelay += $stepTimeDelay;
						continue;
					}
				}
				if ($this->arOptions['DELAY_LIVETEX'] == "Y") {
					$tmpContent = str_replace("'+'", '', $v['CONTENT']);
					if (amopt_strpos($tmpContent, 'livetex.ru/js/') !== false) {
						$oParser->setJsDelayScript($k, $v['CONTENT'], '', $startTimeDelay);
						$startTimeDelay += $stepTimeDelay;
						continue;
					}
				}
				if ($this->arOptions['DELAY_TALKME'] == "Y") {
					if (amopt_strpos($v['CONTENT'], 'talk-me.ru/support') !== false) {
						$oParser->setJsDelayScript($k, $v['CONTENT'], '', $startTimeDelay);
						$startTimeDelay += $stepTimeDelay;
						continue;
					}
				}
				if ($this->arOptions['DELAY_YACHAT'] == "Y") {
					if (amopt_stripos($v['CONTENT'], 'yandexChatWidget') !== false) {
						$oParser->setJsDelayScript($k, $v['CONTENT'], '', $startTimeDelay);
						$startTimeDelay += $stepTimeDelay;
						continue;
					}
				}
				if ($this->arOptions['DELAY_REDHELPER'] == "Y") {
					if (amopt_stripos($v['CONTENT'], 'web.redhelper.ru/service/') !== false) {
						$oParser->setJsDelayScript($k, $v['CONTENT'], '', $startTimeDelay);
						$startTimeDelay += $stepTimeDelay;
						continue;
					}
				}
				if ($this->arOptions['DELAY_SENDPULSE'] == "Y") {
					if (amopt_strpos($v['src'], 'webpushs.com/js/push/') !== false) {
						$oParser->setJsDelayScript($k, '', $v['src'], $startTimeDelay);
						$startTimeDelay += $stepTimeDelay;
						continue;
					}
				}
				if ($this->arOptions['DELAY_MAILRU'] == "Y") {
					if (amopt_strpos($v['CONTENT'], 'top-fwz1.mail.ru/js/') !== false) {
						$oParser->setJsDelayScript($k, $v['CONTENT'], '', $startTimeDelay);
						$startTimeDelay += $stepTimeDelay;
						continue;
					}
				}
				if ($this->arOptions['DELAY_RAMBLERRU'] == "Y") {
					if (amopt_strpos($v['CONTENT'], 'st.top100.ru/top100/top100') !== false) {
						$oParser->setJsDelayScript($k, $v['CONTENT'], '', $startTimeDelay);
						$startTimeDelay += $stepTimeDelay;
						continue;
					}
				}
				if ($this->arOptions['DELAY_YANDEXMAPS'] == "Y") {
					if (amopt_strpos($v['src'], 'api-maps.yandex.ru') !== false) {
						$oParser->setJsDelayScript($k, '', $v['src'], $startTimeDelay);
						$startTimeDelay += $stepTimeDelay;
						continue;
					}
					if (amopt_strpos($v['CONTENT'], 'api-maps.yandex.ru') !== false || amopt_strpos($v['CONTENT'], 'window.ymaps') !== false || amopt_strpos($v['CONTENT'], 'ymaps.') !== false) {
						$oParser->setJsDelayScript($k, $v['CONTENT'], '', $startTimeDelay);
						$startTimeDelay += $stepTimeDelay;
						continue;
					}
				}
				if ($this->arOptions['DELAY_FACEBOOK'] == "Y") {
					if (amopt_strpos($v['CONTENT'], 'fbevents.js') !== false) {
						$oParser->setJsDelayScript($k, $v['CONTENT'], '', $startTimeDelay);
						$startTimeDelay += $stepTimeDelay;
						continue;
					}
				}
				if ($this->arOptions['DELAY_VK'] == "Y") {
					if (amopt_strpos($v['src'], 'vk.com/js/api/') !== false) {
						$oParser->setJsDelayScript($k, '', $v['src'], $startTimeDelay);
						$startTimeDelay += $stepTimeDelay;
						continue;
					}
					if (amopt_strpos($v['CONTENT'], 'vk.com/js/api/') !== false) {
						$oParser->setJsDelayScript($k, $v['CONTENT'], '', $startTimeDelay);
						$startTimeDelay += $stepTimeDelay;
						continue;
					}
				}
			}
			/*			$strIncludeContent = trim($this->arOptions['outline_js']['options']['INCLUDE_CONTENT']);
						$strExcludeContent = trim($this->arOptions['outline_js']['options']['EXCLUDE_CONTENT']);
						$this->arAllJSScript = Application::getInstance()->getParser()->getAllJsScript();
						foreach ($this->arAllJSScript as $iScript => $arScript) {
							$strContent = trim($arScript['CONTENT']);
							if (amopt_strlen($strContent) > 0) {
								$bAllowOutline = true;
								if (amopt_strlen($strIncludeContent) > 0) {
									$bAllowOutline = \CAmminaOptimizer::doMathContentToRules($strIncludeContent, $strContent);
								}
								if ($bAllowOutline && amopt_strlen($strExcludeContent) > 0) {
									$bAllowOutline = !\CAmminaOptimizer::doMathContentToRules($strExcludeContent, $strContent);
								}
								if ($bAllowOutline) {
									$strIdent = md5($strContent);
									$strCacheFile = "/bitrix/ammina.cache/js.outline/" . SITE_ID . "/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".js";
									if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) || Application::getInstance()->isClearCache("js")) {
									}
									if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile)) {
										if (filemtime($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) < (time() - 3600)) {
											@touch($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, time());
										}
										Application::getInstance()->getParser()->setSrcForJsOutlineScript($iScript, $strCacheFile);
									}
								}
							}
						}*/
		}
	}
}

