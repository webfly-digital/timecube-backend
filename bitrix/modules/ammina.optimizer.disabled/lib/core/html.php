<?

namespace Ammina\Optimizer\Core;

use PHPMinifier\HtmlMinifier;
use TinyHtmlMinifier\TinyMinify;

class Html extends Base
{

	public function doOptimize(&$strContent)
	{
		global $APPLICATION;
		$strType = \COption::GetOptionString("ammina.optimizer", 'html_minify_type', "type_phpwee");
		if ($strType == "type_phpwee" && (!class_exists("DOMDocument") || !class_exists("DOMXPath"))) {
			return;
		}
		if (\COption::GetOptionString("ammina.optimizer", "html_active", "N") == "Y" && (!defined("AOZR_HTML_ACTIVE_DISABLE") || AOZR_HTML_ACTIVE_DISABLE !== true)) {
			if ($strType == "type_htmlminifier") {
				$strIdent = md5($strContent);
				$strCacheFileOrig = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/html/ammina.optimizer/" . SITE_ID . "/" . amopt_substr($strIdent, 0, 2) . "/" . $strIdent . ".orig.html";
				$strCacheFileTmp = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/ammina.cache/html/ammina.optimizer/" . SITE_ID . "/" . amopt_substr($strIdent, 0, 2) . "/" . $strIdent . ".tmp.html";
				CheckDirPath(dirname($strCacheFileOrig) . "/");
				\CAmminaOptimizer::SaveFileContent($strCacheFileOrig, $strContent);
				$strCommand = \COption::GetOptionString("ammina.optimizer", "html_path_htmlminifier", "html-minifier") . " --collapse-whitespace --minify-css " . (\COption::GetOptionString("ammina.optimizer", "html_phpwee_css_active", "Y") == "Y" ? 'true' : 'false') . " --minify-js " . (\COption::GetOptionString("ammina.optimizer", "html_phpwee_js_active", "N") == "Y" ? 'true' : 'false') . " -o " . $strCacheFileTmp . " " . $strCacheFileOrig;
				@exec($strCommand);
				@unlink($strCacheFileOrig);
				if (file_exists($strCacheFileTmp)) {
					$strContent = file_get_contents($strCacheFileTmp);
					@unlink($strCacheFileTmp);
				}

			} elseif ($strType == "type_phpwee") {
				$strContent = \PHPWee\Minify::html($strContent, \COption::GetOptionString("ammina.optimizer", "html_phpwee_js_active", "N") == "Y", \COption::GetOptionString("ammina.optimizer", "html_phpwee_css_active", "Y") == "Y");
			} elseif ($strType == "type_phpminifier") {
				$oHtml = new HtmlMinifier();
				$strContent = $oHtml->Minify($strContent);
				unset($oHtml);
			} elseif ($strType == "type_tiny") {
				$strContent = TinyMinify::html($strContent);
			}
		}
	}

}