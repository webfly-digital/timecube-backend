<?

namespace Ammina\Optimizer\Core;

use PHPMinifier\JavascriptMinifier;

class Js extends Base
{

	public function doOptimize(&$strContent)
	{
		global $APPLICATION;
		if (\COption::GetOptionString("ammina.optimizer", "js_active", "N") == "Y" && (!defined("AOZR_JS_ACTIVE_DISABLE") || AOZR_JS_ACTIVE_DISABLE !== true)) {
			$strExtContentCore = '';
			if (\COption::GetOptionString("ammina.optimizer", "js_bxcore_files_active", "Y") == "Y") {
				$start = amopt_strpos($strContent, '<script type="text/javascript">if(!window.BX)window.BX={};');
				if ($start !== false) {
					$end = $start;
					$complete = false;
					while (!$complete) {
						$st1 = amopt_strpos($strContent, '<script', $end + 1);
						if ($st1 !== false) {
							$st2 = amopt_strpos($strContent, '<script type="text/javascript">', $st1);
							if ($st1 != $st2) {
								$complete = true;
							} else {
								$end = amopt_strpos($strContent, '</script>', $st1) + 9;
							}
						} else {
							$complete = true;
						}
					}
					$strExtContentCore = amopt_substr($strContent, $start, $end - $start);
					$strContent = amopt_substr($strContent, 0, $start) . amopt_substr($strContent, $end);
				}
			}

			$arMatches = array();
			$arMatches2 = array();
			$regexp = '<script\s[^>]*src="([^\" >]*)"([^\>]*)([^\>]*)(\/>|>)';
			preg_match_all("/" . $regexp . "/m", $strContent, $arMatches, PREG_SET_ORDER);
			$strFullJs = "/* Created in ammina.optimizer */\n\n";
			$arIdent = array();
			foreach ($arMatches as $k => $arMatch) {
				$strNewFile = $this->doOptimizeFile($arMatch[1]);
				$arMatches[$k]['REPLACE'] = false;
				if ($strNewFile !== false) {
					$arMatches[$k]['REPLACE'] = true;
					$arMatches[$k]['NEW_FILE'] = $strNewFile;
					$arIdent[] = $strNewFile;
				}
			}
			$arAdditionalFilesToFull = array();
			if (\COption::GetOptionString("ammina.optimizer", "js_require_active", "N") == "Y") {
				$strRequireBasePath = \COption::GetOptionString("ammina.optimizer", "js_require_base_template_path", "js");
				$arRequirePathTmp = explode("\n", \COption::GetOptionString("ammina.optimizer", "js_require_paths", implode("\n", array('custom:custom-scripts', 'libs:3rd-party-libs', 'util:custom-scripts/utils', 'init:custom-scripts/inits', 'um:custom-scripts/libs', 'async:3rd-party-libs/async'))));
				$arRequirePath = array();
				foreach ($arRequirePathTmp as $v) {
					$v = trim($v);
					if (amopt_strlen($v) > 0) {
						$arV = explode(":", $v);
						$arRequirePath[$arV[0]] = $arV[1];
					}
				}
				$regexp = 'require\(\[([^\]]*)]';
				preg_match_all("/" . $regexp . "/m", $strContent, $arMatches2, PREG_SET_ORDER);
				foreach ($arMatches2 as $k => $arMatch) {
					$arAllFiles = explode(",", $arMatch[1]);
					foreach ($arAllFiles as $k1 => $strFile) {
						$strFile = trim($strFile);
						if (amopt_substr($strFile, 0, 1) == "'" || amopt_substr($strFile, 0, 1) == '"') {
							$strFile = amopt_substr($strFile, 1);
						}
						if (amopt_substr($strFile, -1, 1) == "'" || amopt_substr($strFile, -1, 1) == '"') {
							$strFile = amopt_substr($strFile, 0, amopt_strlen($strFile) - 1);
						}
						if (amopt_strlen($strFile) > 0) {
							$arCurFile = explode("/", $strFile);
							if (isset($arRequirePath[$arCurFile[0]])) {
								$arCurFile[0] = $arRequirePath[$arCurFile[0]];
								$strFile = implode("/", $arCurFile);
							}
							if (amopt_substr($strFile, 0, 1) != '/') {
								$strFile = SITE_TEMPLATE_PATH . "/" . $strRequireBasePath . "/" . $strFile;
							}
							if (amopt_substr($strFile, -3, 3) != ".js") {
								$strFile .= ".js";
							}
							if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strFile)) {
								$strNewFile = $this->doOptimizeFile($strFile);
								if (!in_array($strNewFile, $arIdent)) {
									$arIdent[] = $strNewFile;
									$arAdditionalFilesToFull[] = $strNewFile;
								}
								unset($arAllFiles[$k1]);
							}
						} else {
							unset($arAllFiles[$k1]);
						}
					}
					foreach ($arAllFiles as $k1 => $strFile) {
						$arAllFiles[$k1] = "'" . $strFile . "'";
					}
					$strAllFiles = implode(",", $arAllFiles);
					$strNewReplace = str_replace($arMatch[1], $strAllFiles, $arMatch[0]);
					$strContent = str_replace($arMatch[0], $strNewReplace, $strContent);
				}
			}

			asort($arIdent);
			$arIdent = array_values($arIdent);
			$strIdent = md5(serialize($arIdent));
			$strCacheFile = "/bitrix/ammina.cache/js/ammina.optimizer/" . SITE_ID . "/full/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".js";
			if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) || $_REQUEST['amopt_clear_cache'] == "Y") {
				foreach ($arMatches as $k => $arMatch) {
					if ($arMatch['REPLACE']) {
						$strFullJs .= "/* Start:" . $arMatch['NEW_FILE'] . "*/\n" . file_get_contents($_SERVER['DOCUMENT_ROOT'] . $arMatch['NEW_FILE']) . "\n/* End */\n\n";
					}
				}
				foreach ($arAdditionalFilesToFull as $strFileAdd) {
					$strFullJs .= "/* Start:" . $strFileAdd . "*/\n" . file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strFileAdd) . "\n/* End */\n\n";
				}
				CheckDirPath(dirname($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) . "/");
				\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, $strFullJs);
			}

			$bIsReplaceMain = false;
			foreach ($arMatches as $k => $arMatch) {
				if ($arMatch['REPLACE']) {
					if (!$bIsReplaceMain) {
						if (\COption::GetOptionString("ammina.optimizer", "js_bxcore_files_active", "Y") == "Y") {
							$strContent = str_replace($arMatch[0], $strExtContentCore . '<script src="' . $strCacheFile . '?' . filemtime($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) . '" type="text/javascript">', $strContent);
						} else {
							$strContent = str_replace($arMatch[0], '<script src="' . $strCacheFile . '?' . filemtime($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) . '" type="text/javascript">', $strContent);
						}
						if (\COption::GetOptionString("ammina.optimizer", "js_header_link_active", "Y") == "Y") {
							\CAmminaOptimizer::doPreLoadHeaders("<" . $strCacheFile . "?" . filemtime($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) . ">; rel=preload; as=script", $strContent);
							//header("Link: " . $strLink, false);
						}
						$bIsReplaceMain = true;
					} else {
						$strContent = str_replace($arMatch[0] . '</script>', "", $strContent);
						$strContent = str_replace($arMatch[0], "", $strContent);
						/*
						while (($iStart = amopt_strpos($strContent, $arMatch[0])) !== false) {
							$iEnd = amopt_strpos($strContent, '</script>', $iStart);
							if ($iEnd === false) {
								$strContent = str_replace($arMatch[0], "", $strContent);
							} else {
								$strContent = amopt_substr($strContent, 0, $iStart) . amopt_substr($strContent, $iEnd + 9);
							}
							break;
						}
						*/
					}
				}
			}
		}
	}

	protected function doOptimizeFile($strFileName)
	{
		global $APPLICATION;
		if (amopt_strpos($strFileName, '://') !== false || amopt_strpos($strFileName, '//') === 0) {
			if (\COption::GetOptionString("ammina.optimizer", "js_remote_active", "Y") == "Y") {
				if (\CAmminaOptimizer::doMathPageToRules(\COption::GetOptionString("ammina.optimizer", "js_remote_disable_links", implode("\n", array("https://www.googletagmanager.com/"))), $strFileName)) {
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
		if (\CAmminaOptimizer::doMathPageToRules(\COption::GetOptionString("ammina.optimizer", "js_files_no_optimize", ""), $strFileName)) {
			return false;
		}
		$strIdent = md5(
			serialize(
				array(
					"fileName" => $strFileName,
					"mtime" => filemtime($_SERVER['DOCUMENT_ROOT'] . $strFileName),
					"extCache" => \CAmminaOptimizer::getExtOptionsForFiles("js"),
				)
			)
		);
		$strCacheFile = "/bitrix/ammina.cache/js/ammina.optimizer/" . SITE_ID . "/atom/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".js";
		$extTmp = microtime(true);
		$extTmp = str_replace(".", "_", $extTmp) . "_" . rand(1, 1000000);
		$strCacheFileOrig = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/js/ammina.optimizer/" . SITE_ID . "/atom/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".orig." . $extTmp . ".js";
		$strCacheFileTmp = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/js/ammina.optimizer/" . SITE_ID . "/atom/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".tmp." . $extTmp . ".js";
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) || $_REQUEST['amopt_clear_cache'] == "Y") {
			CheckDirPath(dirname($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) . "/");
			$strContent = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strFileName);
			if (!\CAmminaOptimizer::doMathPageToRules(\COption::GetOptionString("ammina.optimizer", "js_files_no_minimize", ""), $strFileName)) {
				if (\COption::GetOptionString("ammina.optimizer", "js_minify_active", "Y") == "Y") {
					$strType = \COption::GetOptionString("ammina.optimizer", 'js_minify_type', "type_phpwee");
					if ($strType == "type_uglifyjs") {
						\CAmminaOptimizer::SaveFileContent($strCacheFileOrig, $strContent);
						$strCommand = \COption::GetOptionString("ammina.optimizer", "js_path_uglifyjs", "uglifyjs") . " " . $strCacheFileOrig . " -m -o " . $strCacheFileTmp;
						@exec($strCommand);
						@unlink($strCacheFileOrig);
						if (file_exists($strCacheFileTmp)) {
							$strTmpContent = file_get_contents($strCacheFileTmp);
							if (amopt_strlen(trim($strTmpContent)) > 0) {
								$strContent = $strTmpContent;
							}
							@unlink($strCacheFileTmp);
						}
					} elseif ($strType == "type_uglifyjs2") {
						\CAmminaOptimizer::SaveFileContent($strCacheFileOrig, $strContent);
						$strCommand = \COption::GetOptionString("ammina.optimizer", "js_path_uglifyjs2", "uglifyjs2") . " " . $strCacheFileOrig . " -m -o " . $strCacheFileTmp;
						@exec($strCommand);
						@unlink($strCacheFileOrig);
						if (file_exists($strCacheFileTmp)) {
							$strTmpContent = file_get_contents($strCacheFileTmp);
							if (amopt_strlen(trim($strTmpContent)) > 0) {
								$strContent = $strTmpContent;
							}
							@unlink($strCacheFileTmp);
						}
					} elseif ($strType == "type_terserjs") {
						\CAmminaOptimizer::SaveFileContent($strCacheFileOrig, $strContent);
						$strCommand = \COption::GetOptionString("ammina.optimizer", "js_path_terserjs", "terser") . " -m -o " . $strCacheFileTmp . " " . $strCacheFileOrig;
						@exec($strCommand);
						@unlink($strCacheFileOrig);
						if (file_exists($strCacheFileTmp)) {
							$strTmpContent = file_get_contents($strCacheFileTmp);
							if (amopt_strlen(trim($strTmpContent)) > 0) {
								$strContent = $strTmpContent;
							}
							@unlink($strCacheFileTmp);
						}
					} elseif ($strType == "type_babelminify") {
						\CAmminaOptimizer::SaveFileContent($strCacheFileOrig, $strContent);
						$strCommand = \COption::GetOptionString("ammina.optimizer", "js_path_babelminify", "babel-minify") . " " . $strCacheFileOrig . " --mangle -o " . $strCacheFileTmp;
						@exec($strCommand);
						@unlink($strCacheFileOrig);
						if (file_exists($strCacheFileTmp)) {
							$strTmpContent = file_get_contents($strCacheFileTmp);
							if (amopt_strlen(trim($strTmpContent)) > 0) {
								$strContent = $strTmpContent;
							}
							@unlink($strCacheFileTmp);
						}
					} elseif ($strType == "type_yuicompressor") {
						\CAmminaOptimizer::SaveFileContent($strCacheFileOrig, $strContent);
						$strCommand = \COption::GetOptionString("ammina.optimizer", "js_path_yuicompressor", "yuicompressor") . " --type js --nomunge -o " . $strCacheFileTmp . " " . $strCacheFileOrig;
						@exec($strCommand);
						@unlink($strCacheFileOrig);
						if (file_exists($strCacheFileTmp)) {
							$strTmpContent = file_get_contents($strCacheFileTmp);
							if (amopt_strlen(trim($strTmpContent)) > 0) {
								$strContent = $strTmpContent;
							}
							@unlink($strCacheFileTmp);
						}
					} elseif ($strType == "type_phpwee") {
						$strTmpContent = \PHPWee\Minify::js($strContent);
						if (amopt_strlen(trim($strTmpContent)) > 0) {
							$strContent = $strTmpContent;
						}
					}/* elseif ($strType == "type_phpminifier") {
						$oJs=new JavascriptMinifier();
						$strContent = $oJs->Minify($strContent);
						unset($oJs);
					}*/ elseif ($strType == "type_matthiasmullie") {
						$oMin = new \MatthiasMullie\Minify\JS();
						$oMin->add($strContent);
						$strTmpContent = $oMin->minify();
						if (amopt_strlen(trim($strTmpContent)) > 0) {
							$strContent = $strTmpContent;
						}
					}
				}
			}
			\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, "/* FROM FILE " . $strFileName . " */\n;" . $strContent . ';');
		}
		return $strCacheFile;
	}

	protected function doOptimizeRemoteFile($strFileName)
	{
		global $APPLICATION;
		$strContent = "";
		$strIdent = md5($strFileName);
		$strCacheFile = "/bitrix/ammina.cache/js/ammina.optimizer/" . SITE_ID . "/remote/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . ".js";
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) || $_REQUEST['amopt_clear_cache'] == "Y") {
			$strContent = \CAmminaOptimizer::doRequestPageRemote($strFileName, '', 10, 5);
			if (amopt_strlen($strContent) > 0) {
				\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, $strContent);
			}
		}
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile)) {
			return $this->doOptimizeFile($strCacheFile);
		}
		return false;
	}
}
