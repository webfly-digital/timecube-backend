<?

namespace Ammina\Optimizer\Core;

use PHPMinifier\CssMinifier;

class Css extends Base
{

	protected $arInlineTypes = array(
		"png" => "image/png",
		"gif" => "image/gif",
		"jpg" => "image/jpeg",
		"svg" => "image/svg+xml",
	);

	public function doOptimize(&$strContent)
	{
		global $APPLICATION;

		if (\COption::GetOptionString("ammina.optimizer", "css_active", "N") == "Y" && (!defined("AOZR_CSS_ACTIVE_DISABLE") || AOZR_CSS_ACTIVE_DISABLE !== true)) {
			$arMatches = array();
			$regexp = '<link\s[^>]*href="([^\" >]*)"([^\>]*)([^\>]*)(\/>|>)';
			preg_match_all("/" . $regexp . "/m", $strContent, $arMatches, PREG_SET_ORDER);
			$strFullCss = "/* Created in ammina.optimizer */\n\n";
			$arIdent = array();
			foreach ($arMatches as $k => $arMatch) {
				if (amopt_strpos($arMatch[0], 'rel="stylesheet"') === false) {
					unset($arMatches[$k]);
					continue;
				}
				$strNewFile = $this->doOptimizeFile($arMatch[1]);
				$arMatches[$k]['REPLACE'] = false;
				if ($strNewFile !== false) {
					$arMatches[$k]['REPLACE'] = true;
					$arMatches[$k]['NEW_FILE'] = $strNewFile;
					if (!is_array($strNewFile)) {
						$arIdent[] = $strNewFile;
					}
				}
			}
			asort($arIdent);
			$arIdent = array_values($arIdent);
			$strIdent = md5(serialize($arIdent));
			$strCacheFile = "/bitrix/ammina.cache/css/ammina.optimizer/" . SITE_ID . "/full/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".css";
			if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) || $_REQUEST['amopt_clear_cache'] == "Y") {
				foreach ($arMatches as $k => $arMatch) {
					if ($arMatch['REPLACE'] && !is_array($arMatch['NEW_FILE'])) {
						$strFullCss .= "/* Start:" . $arMatch['NEW_FILE'] . "*/\n" . file_get_contents($_SERVER['DOCUMENT_ROOT'] . $arMatch['NEW_FILE']) . "\n/* End */\n\n";
					}
				}
				CheckDirPath(dirname($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) . "/");
				\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, $strFullCss);
			}

			if (\COption::GetOptionString("ammina.optimizer", "css_inline_active", "N") == "Y") {
				$strFullCss = '<style>' . file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) . '</style>';
				if (\COption::GetOptionString("ammina.optimizer", "css_set_to_endbody", "Y") == "Y") {
					foreach ($arMatches as $k => $arMatch) {
						if ($arMatch['REPLACE'] && !is_array($arMatch['NEW_FILE'])) {
							$strContent = str_replace($arMatch[0], "", $strContent);
						}
					}
					$strContent = str_replace('</body>', $strFullCss . '</body>', $strContent);
				} else {
					$bIsReplaceMain = false;
					foreach ($arMatches as $k => $arMatch) {
						if ($arMatch['REPLACE'] && !is_array($arMatch['NEW_FILE'])) {
							if (!$bIsReplaceMain) {
								$strContent = str_replace($arMatch[0], $strFullCss, $strContent);
								$bIsReplaceMain = true;
							} else {
								$strContent = str_replace($arMatch[0], "", $strContent);
							}
						}
					}
				}
			} else {
				$bIsReplaceMain = false;
				foreach ($arMatches as $k => $arMatch) {
					if ($arMatch['REPLACE'] && !is_array($arMatch['NEW_FILE'])) {
						if (!$bIsReplaceMain) {
							$strContent = str_replace($arMatch[0], '<link href="' . $strCacheFile . '?' . filemtime($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) . '" type="text/css"  rel="stylesheet" />', $strContent);
							$bIsReplaceMain = true;
							if (\COption::GetOptionString("ammina.optimizer", "css_header_link_active", "Y") == "Y") {
								\CAmminaOptimizer::doPreLoadHeaders("<" . $strCacheFile . "?" . filemtime($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) . ">; rel=preload; as=style", $strContent);
								//header("Link: " . $strLink, false);
							}
						} else {
							$strContent = str_replace($arMatch[0], "", $strContent);
						}
					} else {
						\CAmminaOptimizer::doPreLoadHeaders("<" . $arMatch[1] . ">; rel=preload; as=style", $strContent);
						//header("Link: " . $strLink, false);
					}
				}
			}
			foreach ($arMatches as $k => $arMatch) {
				if ($arMatch['REPLACE'] && is_array($arMatch['NEW_FILE'])) {
					if (\COption::GetOptionString("ammina.optimizer", "css_remote_googlefonts_type", "none") == "inline") {
						$strContent = str_replace($arMatch[0], '<style>' . file_get_contents($_SERVER['DOCUMENT_ROOT'] . $arMatch['NEW_FILE']['FILE']) . '</style>', $strContent);
					} elseif (\COption::GetOptionString("ammina.optimizer", "css_remote_googlefonts_type", "none") == "link") {
						$strContent = str_replace($arMatch[0], '<link href="' . $arMatch['NEW_FILE']['FILE'] . '" rel="stylesheet">', $strContent);
					}
				}
			}

			//Обрабатываем BX.loadCss
			$arMatches = array();
			$regexp = 'BX\.loadCSS\(\[([^\>]*)(\]\))';
			preg_match_all("/" . $regexp . "/im", $strContent, $arMatches, PREG_SET_ORDER);
			foreach ($arMatches as $k => $arMatch) {
				if (isset($arMatch[1]) && amopt_strlen($arMatch[1]) > 0) {
					$arIdent = array();
					$arNewFiles = array();
					$arFiles = explode(",", $arMatch[1]);
					foreach ($arFiles as $k => $strFile) {
						if (amopt_substr($strFile, 0, 1) == "'" || amopt_substr($strFile, 0, 1) == '"') {
							$strFile = amopt_substr($strFile, 1);
						}
						if (amopt_substr($strFile, amopt_strlen($strFile) - 1, 1) == "'" || amopt_substr($strFile, amopt_strlen($strFile) - 1, 1) == '"') {
							$strFile = amopt_substr($strFile, 0, amopt_strlen($strFile) - 1);
						}
						$strNewFile = $this->doOptimizeFile($strFile);
						$arNewFiles[] = $strNewFile;
						$arIdent[] = $strNewFile;
					}
					asort($arIdent);
					$arIdent = array_values($arIdent);
					$strIdent = md5(serialize($arIdent));
					$strCacheFile = "/bitrix/ammina.cache/css/ammina.optimizer/" . SITE_ID . "/full/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".css";
					$strFullCss = "";
					if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) || $_REQUEST['amopt_clear_cache'] == "Y") {
						foreach ($arNewFiles as $strFile) {
							$strFullCss .= "/* Start:" . $strFile . "*/\n" . file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strFile) . "\n/* End */\n\n";
						}
						CheckDirPath(dirname($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) . "/");
						\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, $strFullCss);
					}
					$strContent = str_replace($arMatch[0], "BX.loadCSS(['" . $strCacheFile . "'])", $strContent);
					if (\COption::GetOptionString("ammina.optimizer", "css_header_link_active", "Y") == "Y") {
						\CAmminaOptimizer::doPreLoadHeaders("<" . $strCacheFile . "?" . filemtime($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) . ">; rel=preload; as=style", $strContent);
						//header("Link: " . $strLink, false);
					}
				}
			}

			if (\COption::GetOptionString("ammina.optimizer", "css_fontface", "fallback") == "auto") {
				$strContent = str_replace('@font-face{', '@font-face{display:auto;', $strContent);
				$strContent = str_replace('@font-face {', '@font-face{display:auto;', $strContent);
			} elseif (\COption::GetOptionString("ammina.optimizer", "css_fontface", "fallback") == "swap") {
				$strContent = str_replace('@font-face{', '@font-face{display:swap;', $strContent);
				$strContent = str_replace('@font-face {', '@font-face{display:swap;', $strContent);
			} elseif (\COption::GetOptionString("ammina.optimizer", "css_fontface", "fallback") == "fallback") {
				$strContent = str_replace('@font-face{', '@font-face{display:fallback;', $strContent);
				$strContent = str_replace('@font-face {', '@font-face{display:fallback;', $strContent);
			}
		}
	}

	protected function doOptimizeFile($strFileName)
	{
		global $APPLICATION;
		if (amopt_strpos($strFileName, '://') !== false || amopt_strpos($strFileName, '//') === 0) {
			if (\COption::GetOptionString("ammina.optimizer", "css_remote_active", "Y") == "Y") {
				if (\CAmminaOptimizer::doMathPageToRules(\COption::GetOptionString("ammina.optimizer", "css_remote_disable_links", ""), $strFileName)) {
					return false;
				}
				return $this->doOptimizeRemoteFile($strFileName);
			}
			return false;
		}
		$strFileName = explode("?", $strFileName);
		$strFileName = $strFileName[0];
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strFileName)) {
			return false;
		}
		if (\CAmminaOptimizer::doMathPageToRules(\COption::GetOptionString("ammina.optimizer", "css_files_no_optimize", ""), $strFileName)) {
			return false;
		}
		$strIdent = md5(
			serialize(
				array(
					"fileName" => $strFileName,
					"mtime" => filemtime($_SERVER['DOCUMENT_ROOT'] . $strFileName),
					"extCache" => \CAmminaOptimizer::getExtOptionsForFiles("css"),
				)
			)
		);
		$strCacheFile = "/bitrix/ammina.cache/css/ammina.optimizer/" . SITE_ID . "/atom/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".css";
		$extTmp = microtime(true);
		$extTmp = str_replace(".", "_", $extTmp) . "_" . rand(1, 1000000);
		$strCacheFileOrig = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/css/ammina.optimizer/" . SITE_ID . "/atom/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".orig." . $extTmp . ".css";
		$strCacheFileTmp = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/css/ammina.optimizer/" . SITE_ID . "/atom/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".tmp." . $extTmp . ".css";
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) || $_REQUEST['amopt_clear_cache'] == "Y") {
			CheckDirPath(dirname($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) . "/");
			$strContent = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strFileName);
			$bAllowIncImages = true;
			if (amopt_strpos($strContent, '/* Ammina ignore inline images */') !== false) {
				$bAllowIncImages = false;
			}
			$strContent = preg_replace_callback(
				'/url\s?\([\'"]?(?![\'"]?data:)([^\'")]*)(["\']?\))/m',
				function ($arCssUrl) use ($strFileName, $bAllowIncImages) {
					if (amopt_strpos($arCssUrl[1], '://') !== false || amopt_strpos($arCssUrl[1], '//') === 0) {
						return 'url("' . $arCssUrl[1] . '")';
					}
					$strNewUrl = Rel2Abs(dirname($strFileName), $arCssUrl[1]);
					$obImg = new Img();
					$strNewUrl1 = $obImg->doOptimizeFile($strNewUrl, false);
					if ($strNewUrl1 !== false) {
						$strNewUrl = $strNewUrl1;
					}
					if ($bAllowIncImages && \COption::GetOptionString("ammina.optimizer", "css_incimages", "Y") == "Y") {
						return 'url(' . $this->getImageInline($strNewUrl) . ')';
					} else {
						return 'url("' . $strNewUrl . '")';
					}
				},
				$strContent
			);
			if (!\CAmminaOptimizer::doMathPageToRules(\COption::GetOptionString("ammina.optimizer", "css_files_no_minimize", ""), $strFileName)) {
				if (\COption::GetOptionString("ammina.optimizer", "css_minify_active", "Y") == "Y") {
					$strType = \COption::GetOptionString("ammina.optimizer", 'css_minify_type', "type_matthiasmullie");
					if ($strType == "type_uglifycss") {
						\CAmminaOptimizer::SaveFileContent($strCacheFileOrig, $strContent);
						$strCommand = \COption::GetOptionString("ammina.optimizer", "css_path_uglifycss", "uglifycss") . " --output " . $strCacheFileTmp . " " . $strCacheFileOrig;
						@exec($strCommand);
						@unlink($strCacheFileOrig);
						if (file_exists($strCacheFileTmp)) {
							$strContent = file_get_contents($strCacheFileTmp);
							@unlink($strCacheFileTmp);
						}
					} elseif ($strType == "type_yuiccompressor") {
						\CAmminaOptimizer::SaveFileContent($strCacheFileOrig, $strContent);
						$strCommand = \COption::GetOptionString("ammina.optimizer", "js_path_yuicompressor", "yuicompressor") . " --type css -o " . $strCacheFileTmp . " " . $strCacheFileOrig;
						@exec($strCommand);
						@unlink($strCacheFileOrig);
						if (file_exists($strCacheFileTmp)) {
							$strContent = file_get_contents($strCacheFileTmp);
							@unlink($strCacheFileTmp);
						}
					} elseif ($strType == "type_phpwee") {
						$strContent = \PHPWee\Minify::css($strContent);
					} elseif ($strType == "type_phpminifier") {
						$oCss = new CssMinifier();
						$strContent = $oCss->Minify($strContent);
						unset($oCss);
					} else {
						$oMin = new \MatthiasMullie\Minify\CSS();
						$oMin->add($strContent);
						$strContent = $oMin->minify();
					}
				}
			}
			if (\COption::GetOptionString("ammina.optimizer", "css_fontface", "fallback") == "auto") {
				$strContent = str_replace('@font-face{', '@font-face{display:auto;', $strContent);
				$strContent = str_replace('@font-face {', '@font-face{display:auto;', $strContent);
			} elseif (\COption::GetOptionString("ammina.optimizer", "css_fontface", "fallback") == "swap") {
				$strContent = str_replace('@font-face{', '@font-face{display:swap;', $strContent);
				$strContent = str_replace('@font-face {', '@font-face{display:swap;', $strContent);
			} elseif (\COption::GetOptionString("ammina.optimizer", "css_fontface", "fallback") == "fallback") {
				$strContent = str_replace('@font-face{', '@font-face{display:fallback;', $strContent);
				$strContent = str_replace('@font-face {', '@font-face{display:fallback;', $strContent);
			}
			$strContent = "/* Start original:" . $strFileName . "*/\n" . $strContent . "\n/* End original */\n\n";
			\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, $strContent);
		}
		return $strCacheFile;
	}

	protected function doOptimizeRemoteFile($strFileName)
	{
		global $APPLICATION;
		$strContent = "";
		$bIsInlineCss = false;
		if (amopt_strpos($strFileName, '//fonts.googleapis.com/css') !== false) {
			if (\COption::GetOptionString("ammina.optimizer", "css_remote_googlefonts_type", "none") == "none") {
				return false;
			}
			$strIdent = md5($strFileName . "|" . amopt_strtolower($_SERVER['HTTP_USER_AGENT']));
			$strCacheFile = "/bitrix/ammina.cache/css/ammina.optimizer/" . SITE_ID . "/googlefonts/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".css";
			$bIsInlineCss = true;
		} else {
			$strIdent = md5($strFileName);
			$strCacheFile = "/bitrix/ammina.cache/css/ammina.optimizer/" . SITE_ID . "/remote/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".css";
		}
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) || $_REQUEST['amopt_clear_cache'] == "Y") {
			if (amopt_strpos($strFileName, '//fonts.googleapis.com/css') !== false) {
				$strContent = $this->doLoadGoogleFontCss($strFileName);
			} else {
				$strContent = \CAmminaOptimizer::doRequestPageRemote($strFileName, '', 10, 5);
			}
			if (amopt_strlen($strContent) > 0) {
				$strContent = preg_replace_callback(
					'/url\s?\([\'"]?(?![\'"]?data:)([^\'")]*)(["\']?\))/m',
					function ($arCssUrl) use ($strFileName) {
						global $APPLICATION;
						$strNewUrl = \CAmminaOptimizer::Rel2AbsUrl($strFileName, $arCssUrl[1]);
						$strFileIdent = md5($strNewUrl);
						$arUrlFile = parse_url($strNewUrl);
						$arPathFile = ammina_pathinfo($arUrlFile['path']);
						$strCacheRemoteFile = "/upload/ammina.optimizer/" . SITE_ID . "/remotefiles/" . amopt_substr($strFileIdent, 0, 2) . "/" . amopt_substr($strFileIdent, 0, 6) . "/" . $strFileIdent . "." . $arPathFile['extension'];
						if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheRemoteFile) || $_REQUEST['amopt_clear_cache'] == "Y") {
							$strContentFile = \CAmminaOptimizer::doRequestPageRemote($strNewUrl, '', 10, 5);
							if (amopt_strlen($strContentFile) > 0) {
								\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheRemoteFile, $strContentFile);
							}
						}
						if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheRemoteFile)) {
							return 'url("' . $strCacheRemoteFile . '")';
						}
						return 'url("' . $strFileName . '")';
					},
					$strContent
				);
				\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, $strContent);
			}
		}
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile)) {
			if ($bIsInlineCss) {
				return array("IS_INLINE" => true, "FILE" => $this->doOptimizeFile($strCacheFile));
			} else {
				return $this->doOptimizeFile($strCacheFile);
			}
		}
		return false;
	}

	protected function doLoadGoogleFontCss($strFileName)
	{
		$strResult = \CAmminaOptimizer::doRequestPageRemote($strFileName, $_SERVER['HTTP_USER_AGENT'], 10, 5);
		if (amopt_strlen($strResult) <= 0) {
			return false;
		}
		return $strResult;
	}

	protected function getImageInline($strFileName)
	{
		$strFullName = $_SERVER['DOCUMENT_ROOT'] . $strFileName;
		$iMaxFileSize = \COption::GetOptionString("ammina.optimizer", "css_incimages_maxsize", 4096);
		if (file_exists($strFullName) && filesize($strFullName) <= $iMaxFileSize) {
			$arPathInfo = ammina_pathinfo($strFullName);
			if (isset($this->arInlineTypes[amopt_strtolower($arPathInfo['extension'])])) {
				return 'data:' . $this->arInlineTypes[amopt_strtolower($arPathInfo['extension'])] . ';base64,' . base64_encode(file_get_contents($strFullName));
			}
		}
		return '"' . $strFileName . '"';
	}
}
