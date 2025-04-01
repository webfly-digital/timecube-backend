<?

namespace Ammina\Optimizer\Core2\Parser;

use Ammina\Optimizer\Core2\Application;

class DOMParser extends Base
{
	/**
	 * @var \DOMDocument
	 */
	protected $DOM = null;
	/**
	 * @var \DOMXPath
	 */
	protected $XPath = null;
	protected $arAllCssLinks = array();
	protected $arAllCssNodeLinks = array();
	protected $arAllCssStyle = array();
	protected $arAllCssNodeStyle = array();
	protected $arAllJsScript = array();
	protected $arAllJsNodeLinks = array();
	protected $arAllJsScriptWithBxLoadCss = array();
	protected $arAllJsNodeLinksWithBxLoadCss = array();
	protected $arAllImages = array();
	protected $arAllImagesNodeLinks = array();
	protected $arAllLazy = array();
	protected $arAllLazyAttrLinks = array();
	protected $arImageTypes = array(
		"jpg" => "jpg",
		"jpeg" => "jpeg",
		"png" => "png",
		"gif" => "gif",
		"svg" => "svg",
	);
	protected $bPartIsInBodyAdd = false;
	protected $bCheckNotValidOpenTag = false;
	protected $arOtherOptions = array();

	public function __construct($bCheckNotValidOpenTag = false, $arOtherOptions = array())
	{
		$this->bCheckNotValidOpenTag = $bCheckNotValidOpenTag;
		$this->arOtherOptions = $arOtherOptions;
	}

	public function doParse($strHTML)
	{
		global $APPLICATION;
		//Делаем контент тегов script безопасным
		$arScriptContent = array();
		if ($this->arOtherOptions['CHECK_NOTVALID_UTF8_SYMBOLS']) {
			$strHTML = preg_replace(
				'/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]' .
				'|[\x00-\x7F][\x80-\xBF]+' .
				'|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*' .
				'|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})' .
				'|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
				'?',
				$strHTML
			);
		}

		$arHtml = explode("</script>", $strHTML);
		$index = 1;
		foreach ($arHtml as $k => $v) {
			$iStart = amopt_strpos($v, '<script');
			if ($iStart !== false) {
				$iStartContent = amopt_strpos($v, '>', $iStart + 1);
				$strCont = amopt_substr($v, $iStartContent + 1);
				$arScriptContent[$index] = trim($strCont);
				$arHtml[$k] = amopt_substr($v, 0, $iStartContent + 1) . $index;
				$index++;
			}
		}
		$strHTML = implode("</script>", $arHtml);

		if ($this->bCheckNotValidOpenTag) {
			$strAllowCharsTag = "/'\"\\abcdefghijklmnopqrstuvwxyz-!";
			$arHtmlTagCheck = explode('<', $strHTML);
			$iOldK = 0;
			foreach ($arHtmlTagCheck as $k => $v) {
				$char = amopt_substr($v, 0, 1);
				if (amopt_strpos($strAllowCharsTag, amopt_strtolower($char)) === false && $k > 0) {
					$arHtmlTagCheck[$iOldK] .= '&lt;' . $v;
					unset($arHtmlTagCheck[$k]);
				} else {
					$iOldK = $k;
				}
			}
			$strHTML = implode("<", $arHtmlTagCheck);
		}
		if ($this->arOtherOptions['REPLACE_BULLET']) {
			$strHTML = str_replace('&bullet;', '&bull;', $strHTML);
		}
		$arReplaceRules = explode("\n", $this->arOtherOptions['REPLACE_HTML_ENTITY']);
		$arNewRules = array();
		foreach ($arReplaceRules as $k => $v) {
			$v = trim($v);
			if (amopt_strlen($v) > 0) {
				$arV = explode("=", $v, 2);
				$arV[0] = trim($arV[0]);
				$arV[1] = trim($arV[1]);
				if (amopt_strlen($arV[0]) > 0 && amopt_strlen($arV[1]) > 0) {
					$arNewRules[$arV[0]] = $arV[1];
				}
			}
		}
		if (!empty($arNewRules)) {
			foreach ($arNewRules as $k => $v) {
				$strHTML = str_replace($k, $v, $strHTML);
			}
		}
		$strHTML = $this->doCheckHeadEncode($strHTML, amopt_strlen(LANG_CHARSET) > 0 ? LANG_CHARSET : SITE_CHARSET);
		$this->strHTML = $strHTML;
		if (!defined("BX_UTF") || BX_UTF !== true) {
			$this->DOM = new \DOMDocument("", (amopt_strlen(LANG_CHARSET) > 0 ? LANG_CHARSET : SITE_CHARSET));
		} else {
			$this->DOM = new \DOMDocument("", "UTF-8");
		}
		$this->DOM->preserveWhiteSpace = false;
		$this->DOM->formatOutput = true;
		//$this->strHTML=html_entity_decode($this->strHTML,ENT_QUOTES,"UTF-8");
		@$this->DOM->loadHTML($this->strHTML);
		$this->XPath = new \DOMXPath($this->DOM);
		$this->XPath->registerNamespace("php", "http://php.net/xpath");
		$this->XPath->registerPHPFunctions();
		$oScripts = $this->XPath->query("//script");
		for ($i = 0; $i < $oScripts->length; $i++) {
			$oNode = $oScripts->item($i);
			$index = intval($oNode->nodeValue);
			if ($index > 0) {
				$oNode->nodeValue = "";
				$oCData = $this->DOM->createCDATASection($this->doNormalizeCharsetValue($arScriptContent[$index]));
				$oNode->appendChild($oCData);
			}
		}
	}

	public function doCheckHeadEncode($strHTML, $strEncode)
	{
		$tmpHtml = str_replace('"', "", $strHTML);
		$tmpHtml = str_replace('\'', "", $tmpHtml);
		if (amopt_stripos($tmpHtml, 'http-equiv=Content-Type') === false) {
			$strHTML = str_replace('</head>', '<meta http-equiv="Content-Type" content="text/html; charset=' . $strEncode . '"></head>', $strHTML);
		}
		return $strHTML;
	}

	public function doParsePart($strHTML, $strEncode = "")
	{
		global $APPLICATION;

		$this->bPartIsInBodyAdd = false;
		if (amopt_strlen($strEncode) <= 0) {
			if (!defined("BX_UTF") || BX_UTF !== true) {
				$strEncode = (amopt_strlen(LANG_CHARSET) > 0 ? LANG_CHARSET : SITE_CHARSET);
			} else {
				$strEncode = "UTF-8";
			}
		}
		if ($this->arOtherOptions['CHECK_NOTVALID_UTF8_SYMBOLS']) {
			$strHTML = preg_replace(
				'/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]' .
				'|[\x00-\x7F][\x80-\xBF]+' .
				'|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*' .
				'|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})' .
				'|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
				'?',
				$strHTML
			);
		}
		if (amopt_strpos($strHTML, '<body') === false) {
			$strHTML = '<!DOCTYPE html><html xml:lang="ru" lang="ru"><head><title>test</title><meta http-equiv="Content-Type" content="text/html; charset=' . $strEncode . '"></head><body><div>' . $strHTML . '</div></body></html>';
			$this->bPartIsInBodyAdd = true;
		} else {
			$strHTML = $this->doCheckHeadEncode($strHTML, $strEncode);
		}
		//Делаем контент тегов script безопасным
		$arScriptContent = array();
		$arHtml = explode("</script>", $strHTML);
		$index = 1;
		foreach ($arHtml as $k => $v) {
			$iStart = amopt_strpos($v, '<script');
			if ($iStart !== false) {
				$iStartContent = amopt_strpos($v, '>', $iStart + 1);
				$strCont = amopt_substr($v, $iStartContent + 1);
				$arScriptContent[$index] = trim($strCont);
				$arHtml[$k] = amopt_substr($v, 0, $iStartContent + 1) . $index;
				$index++;
			}
		}
		$strHTML = implode("</script>", $arHtml);

		if ($this->bCheckNotValidOpenTag) {
			$strAllowCharsTag = "/'\"\\abcdefghijklmnopqrstuvwxyz-!";
			$arHtmlTagCheck = explode('<', $strHTML);
			$iOldK = 0;
			foreach ($arHtmlTagCheck as $k => $v) {
				$char = amopt_substr($v, 0, 1);
				if (amopt_strpos($strAllowCharsTag, amopt_strtolower($char)) === false && $k > 0) {
					$arHtmlTagCheck[$iOldK] .= '&lt;' . $v;
					unset($arHtmlTagCheck[$k]);
				} else {
					$iOldK = $k;
				}
			}
			$strHTML = implode("<", $arHtmlTagCheck);
		}
		if ($this->arOtherOptions['REPLACE_BULLET']) {
			$strHTML = str_replace('&bullet;', '&bull;', $strHTML);
		}

		$this->strHTML = $strHTML;
		$this->DOM = new \DOMDocument("", $strEncode);
		$this->DOM->preserveWhiteSpace = false;
		$this->DOM->formatOutput = true;
		@$this->DOM->loadHTML($this->strHTML);
		$this->XPath = new \DOMXPath($this->DOM);
		$this->XPath->registerNamespace("php", "http://php.net/xpath");
		$this->XPath->registerPHPFunctions();
		$oScripts = $this->XPath->query("//script");
		for ($i = 0; $i < $oScripts->length; $i++) {
			$oNode = $oScripts->item($i);
			$index = intval($oNode->nodeValue);
			if ($index > 0) {
				$oNode->nodeValue = "";
				$oCData = $this->DOM->createCDATASection($this->doNormalizeCharsetValue($arScriptContent[$index]));
				$oNode->appendChild($oCData);
			}
		}
	}

	public function doSaveHTML()
	{
		if ($this->bMinifiedOutput) {
			$this->DOM->preserveWhiteSpace = false;
			$this->DOM->formatOutput = false;
		} else {
			$this->DOM->preserveWhiteSpace = false;
			$this->DOM->formatOutput = true;
		}
		$this->DOM->normalizeDocument();
		return $this->DOM->saveHTML();
	}

	public function doSaveHTMLPart()
	{
		if ($this->bMinifiedOutput) {
			$this->DOM->preserveWhiteSpace = false;
			$this->DOM->formatOutput = false;
		} else {
			$this->DOM->preserveWhiteSpace = false;
			$this->DOM->formatOutput = true;
		}
		$this->DOM->normalizeDocument();
		if ($this->bPartIsInBodyAdd) {
			$strResult = trim($this->DOM->saveHTML());
			$ar = explode("<body>", $strResult, 2);
			$strResult = $ar[1];
			$ar = explode("</body>", $strResult, 2);
			$strResult = $ar[0];
			/*$oNode = $this->XPath->query("//body[1]/div[1]")->item(0);
			$strResult = trim($this->DOM->saveHTML($oNode));*/
			$strResult = amopt_substr($strResult, amopt_strpos($strResult, ">") + 1);
			$strResult = amopt_substr($strResult, 0, amopt_strrpos($strResult, "</div>"));
			return $strResult;
		} else {
			return $this->DOM->saveHTML();
		}
	}

	/**
	 * @param $oNode \DOMNode
	 */
	protected function getChildNodes(&$oNode)
	{
		?>
		<ul>
			<?
			$oChild = $oNode->childNodes;

			foreach ($oChild as $oChildNode) {
				/*
			 * var $oChildNode \DOMNode
			 */
				?>
				<li>
					<?= htmlspecialchars($oChildNode->nodeName) ?>
					<?
					if ($oNode->hasChildNodes()) {
						$this->getChildNodes($oChildNode);
					}
					?>
				</li>
				<?
			}
			?>
		</ul>
		<?
	}

	public function getAllClassesAndIdent()
	{
		$arResult = array(
			"CLASSES" => array(),
			"IDENT" => array(),
		);
		$rAllNodes = $this->XPath->query("//*[@class]|//*[@id]");
		for ($i = 0; $i < $rAllNodes->length; $i++) {
			$attrList = $rAllNodes->item($i)->attributes;
			foreach ($attrList as $attr) {
				if ($attr->localName == "class") {
					$arClasses = explode(" ", $attr->nodeValue);
					foreach ($arClasses as $class) {
						$class = amopt_strtolower(trim($class));
						if (amopt_strlen($class) > 0) {
							$arResult['CLASSES'][amopt_strtolower($class)] = 1;
						}
					}
				} elseif ($attr->localName == "id") {
					$arIdent = explode(" ", $attr->nodeValue);
					foreach ($arIdent as $id) {
						if (amopt_strlen($id) > 0) {
							$arResult['IDENT'][amopt_strtolower($id)] = 1;
						}
					}
				}
			}
		}
		ksort($arResult['CLASSES'], SORT_NATURAL | SORT_ASC);
		ksort($arResult['IDENT'], SORT_NATURAL | SORT_ASC);
		return $arResult;
	}

	public function getAllCssLinks()
	{
		global $APPLICATION;
		$this->arAllCssLinks = array();
		$this->arAllCssNodeLinks = array();
		$index = 1;
		$rAllNodes = $this->XPath->query("//link");
		for ($i = 0; $i < $rAllNodes->length; $i++) {
			$this->arAllCssNodeLinks[$index] = $rAllNodes->item($i);
			$attrList = $this->arAllCssNodeLinks[$index]->attributes;
			$this->arAllCssLinks[$index] = array();
			foreach ($attrList as $attr) {
				/**
				 * @var $attr \DOMAttr
				 */
				$this->arAllCssLinks[$index][$attr->localName] = $attr->nodeValue;
			}
			if (isset($this->arAllCssLinks[$index]['href'])) {
				$arUrl = parse_url($this->arAllCssLinks[$index]['href']);
				if (!isset($arUrl['host'])) {
					$strFileName = \CAmminaOptimizer::Rel2AbsUrl($APPLICATION->GetCurPage(true), $arUrl['path']);
					if (strlen($strFileName) > 0 && file_exists($_SERVER['DOCUMENT_ROOT'] . $strFileName)) {
						if (strpos($strFileName, '/bitrix/ammina.cache/css.outline/') === 0 || strpos($strFileName, '/bitrix/ammina.cache/css.remote/') === 0 || strpos($strFileName, '/bitrix/ammina.cache/js.outline/') === 0 || strpos($strFileName, '/bitrix/ammina.cache/js.remote/') === 0) {
							$this->arAllCssLinks[$index]['href'] = $strFileName;
						} else {
							$this->arAllCssLinks[$index]['href'] = $strFileName . "?" . filemtime($_SERVER['DOCUMENT_ROOT'] . $strFileName);
						}
					}
				}
			}
			if ((!isset($this->arAllCssLinks[$index]['data-skip']) || !$this->arAllCssLinks[$index]['data-skip']) && ($this->arAllCssLinks[$index]['rel'] == "stylesheet")) {
				$index++;
			} else {
				unset($this->arAllCssLinks[$index]);
				unset($this->arAllCssNodeLinks[$index]);
			}
		}
		return $this->arAllCssLinks;
	}

	public function removeCssLink($index)
	{
		if (isset($this->arAllCssNodeLinks[$index])) {
			/**
			 * @var $oNode \DOMNode
			 */
			$oNode =& $this->arAllCssNodeLinks[$index];
			$oNode->parentNode->removeChild($oNode);
			unset($this->arAllCssNodeLinks[$index]);
		}
	}

	public function setHrefForCssLink($index, $href)
	{
		if (isset($this->arAllCssNodeLinks[$index])) {
			/**
			 * @var $oNode \DOMNode
			 */
			$oNode =& $this->arAllCssNodeLinks[$index];
			$rAttributes = $oNode->attributes;
			for ($i = 0; $i < $rAttributes->length; $i++) {
				$oAttr = $rAttributes->item($i);
				if ($oAttr->nodeName == "href") {
					$oAttr->nodeValue = $href;
				}
			}
		}
	}

	public function getAllCssStyle()
	{
		$this->arAllCssStyle = array();
		$this->arAllCssNodeStyle = array();
		$index = 1;
		$rAllNodes = $this->XPath->query("//style");
		for ($i = 0; $i < $rAllNodes->length; $i++) {
			$this->arAllCssNodeStyle[$index] = $rAllNodes->item($i);
			$attrList = $this->arAllCssNodeStyle[$index]->attributes;
			$this->arAllCssStyle[$index] = array(
				"CONTENT" => $this->arAllCssNodeStyle[$index]->textContent
			);
			foreach ($attrList as $attr) {
				/**
				 * @var $attr \DOMAttr
				 */
				$this->arAllCssStyle[$index]['attributes'][$attr->localName] = $attr->nodeValue;
			}
			if (!isset($this->arAllCssStyle[$index]['attributes']['data-skip'])) {
				$index++;
			} else {
				unset($this->arAllCssStyle[$index]);
				unset($this->arAllCssNodeStyle[$index]);
			}
		}
		return $this->arAllCssStyle;
	}

	public function replaceCssStyleToLink($index, $strFileName)
	{
		if (isset($this->arAllCssNodeStyle[$index])) {
			/**
			 * @var $oNodeStyle \DOMNode
			 */
			$oNodeStyle =& $this->arAllCssNodeStyle[$index];
			$oNewTagNode = $this->DOM->createElement('link', '');
			$oAttrRel = $this->DOM->createAttribute('rel');
			$oAttrRel->value = 'stylesheet';
			$oNewTagNode->appendChild($oAttrRel);
			$oAttrHref = $this->DOM->createAttribute('href');
			$oAttrHref->value = $strFileName;
			$oNewTagNode->appendChild($oAttrHref);
			$oNodeStyle->parentNode->insertBefore($oNewTagNode, $oNodeStyle);
			$oNodeStyle->parentNode->removeChild($oNodeStyle);
			unset($this->arAllCssNodeStyle[$index]);
		}
	}

	public function removeCssStyle($index)
	{
		if (isset($this->arAllCssNodeStyle[$index])) {
			/**
			 * @var $oNode \DOMNode
			 */
			$oNode =& $this->arAllCssNodeStyle[$index];
			$oNode->parentNode->removeChild($oNode);
			unset($this->arAllCssNodeStyle[$index]);
		}
	}

	public function AddTag($strXPathParent, $strTag, $arAttributes = array(), $strTagContent = '', $bFirstChild = false)
	{
		$oNewTagNode = $this->DOM->createElement($strTag, $strTagContent);
		if (is_array($arAttributes)) {
			foreach ($arAttributes as $k => $v) {
				$oAttr = $this->DOM->createAttribute($k);
				if ($v !== null) {
					$oAttr->value = $v;
				}
				$oNewTagNode->appendChild($oAttr);
			}
		}
		$rParentNodes = $this->XPath->query($strXPathParent);
		if ($rParentNodes->length > 0) {
			if ($bFirstChild) {
				$rParentNodes->item(0)->insertBefore($oNewTagNode, $rParentNodes->item(0)->firstChild);
			} else {
				$rParentNodes->item(0)->appendChild($oNewTagNode);
			}
		}
	}

	public function AddTagBeforeCssLink($iCssLinkIndex, $strTag, $arAttributes = array(), $strTagContent = '')
	{
		$oNewTagNode = $this->DOM->createElement($strTag, $strTagContent);
		foreach ($arAttributes as $k => $v) {
			$oAttr = $this->DOM->createAttribute($k);
			if ($v !== null) {
				$oAttr->value = $v;
			}
			$oNewTagNode->appendChild($oAttr);
		}
		/**
		 * @var $oLink \DOMNode
		 */
		$oLink = $this->arAllCssNodeLinks[$iCssLinkIndex];
		$oLink->parentNode->insertBefore($oNewTagNode, $oLink);
	}

	public function doUnlockMoveBxRandScript()
	{
		$rAllNodes = $this->XPath->query("//script");
		for ($i = 0; $i < $rAllNodes->length; $i++) {
			/**
			 * @var $oNode \DOMNode
			 */
			$oNode = $rAllNodes->item($i);
			$strContent = $oNode->textContent;
			if (amopt_strpos($strContent, '?bxrand=') !== false) {
				$oNode->removeAttribute("data-skip-moving");
				//$oAttr = $this->DOM->createAttribute("async");
				//$oNode->appendChild($oAttr);
				$oAttr = $this->DOM->createAttribute("data-bxrand-script");
				$oAttr->value = 'true';
				$oNode->appendChild($oAttr);
				return;
			}
		}
	}

	public function doMoveBxRandScriptToEndBody()
	{
		$rBody = $this->XPath->query("//body");
		if ($rBody->length == 1) {
			/**
			 * @var $oBody \DOMNode
			 */
			$oBody = $rBody->item(0);
			$rAllNodes = $this->XPath->query('//script[@data-bxrand-script="true"]');
			for ($i = 0; $i < $rAllNodes->length; $i++) {
				/**
				 * @var $oNode \DOMNode
				 */
				$oNode = $rAllNodes->item($i);
				$attrList = $oNode->attributes;
				$arAttr = array();
				foreach ($attrList as $attr) {
					/**
					 * @var $attr \DOMAttr
					 */
					$arAttr[$attr->localName] = $attr->nodeValue;
				}
				if ($arAttr['data-skip-moving'] != 'true' && (amopt_strlen($arAttr['type']) <= 0 || amopt_strtolower($arAttr['type']) == "text/javascript" || amopt_strtolower($arAttr['type']) == "application/javascript")) {
					$oBody->appendChild($oNode);
				}
			}
		}
	}

	public function doMakeStaticAsproSetTheme()
	{
		$rAllNodes = $this->XPath->query("//script");
		for ($i = 0; $i < $rAllNodes->length; $i++) {
			/**
			 * @var $oNode \DOMNode
			 */
			$oNode = $rAllNodes->item($i);
			$src = false;
			$content = false;

			$attrList = $oNode->attributes;
			foreach ($attrList as $attr) {
				if ($attr->localName === "src") {
					$src = $attr->nodeValue;
					break;
				}
			}
			if ($src && strpos($src, 'setTheme.php') !== false) {
				$path = \CAmminaOptimizer::Rel2AbsUrl((\CMain::IsHTTPS() ? "https://" : "http://") . $_SERVER["SERVER_NAME"], $src);
				$oCache = new \CPHPCache();
				$arData = array();
				if ($oCache->InitCache(3600, $path, 'ammina.optimizer')) {
					$res = $oCache->GetVars();
					$arData = $res['DATA'];
				}
				if ($oCache->StartDataCache()) {
					$result = \CAmminaOptimizer::doRequestPageRemote($path, '', 5, 5);
					if ($result === false) {
						$oCache->AbortDataCache();
					} else {
						$arData = $result;
						$oCache->EndDataCache(
							array(
								"DATA" => $arData,
							)
						);
					}
				}
				if ($arData && !empty($arData)) {
					$oNode->removeAttribute("src");
					$oNode->removeAttribute("data-skip-moving");
					$oNode->nodeValue = $arData;
				}
			}
		}
	}

	public function doUnlockSkipMoveJsAspro()
	{
		$rAllNodes = $this->XPath->query("//script");
		for ($i = 0; $i < $rAllNodes->length; $i++) {
			/**
			 * @var $oNode \DOMNode
			 */
			$oNode = $rAllNodes->item($i);
			if ($oNode) {
				$src = false;
				$content = false;

				$attrList = $oNode->attributes;
				foreach ($attrList as $attr) {
					if ($attr->localName === "src") {
						$src = $attr->nodeValue;
						break;
					}
				}
				$content = trim($oNode->textContent);
				if (strlen($content) <= 0) {
					$content = false;
				}
				$removeSkip = false;
				if ($src !== false) {
					if (strpos($src, 'lazysizes') !== false || strpos($src, 'ls.unveilhooks') !== false || strpos($src, 'jquery-') !== false || strpos($src, 'speed.') !== false || strpos($src, 'owl.carousel') !== false) {
						$removeSkip = true;
					}
				}
				if ($content !== false) {
					$content = strtolower($content);
					if (strpos($content, 'window.lazysizesconfig') !== false || strpos($content, 'solutionname') !== false || strpos($content, 'inittopestmenu') !== false || strpos($content, 'checktopmenu') !== false || strpos($content, 'var filter =') !== false || strpos($content, 'var arasprooptions') !== false) {
						$removeSkip = true;
					}
				}
				if ($removeSkip) {
					$oNode->removeAttribute("data-skip-moving");
				}
			}
		}
	}

	public function doUnlockSkipMoveJsHead()
	{
		$rAllNodes = $this->XPath->query("//head//script");
		for ($i = 0; $i < $rAllNodes->length; $i++) {
			/**
			 * @var $oNode \DOMNode
			 */
			$oNode = $rAllNodes->item($i);
			$oNode->removeAttribute("data-skip-moving");
		}
	}

	public function doUnlockSkipMoveJsBody()
	{
		$rAllNodes = $this->XPath->query("//body//script");
		for ($i = 0; $i < $rAllNodes->length; $i++) {
			/**
			 * @var $oNode \DOMNode
			 */
			$oNode = $rAllNodes->item($i);
			$oNode->removeAttribute("data-skip-moving");
		}
	}

	public function getAllJsScript($bIgnoreSkip = false, $bReturnAllExtensions = false)
	{
		global $APPLICATION;
		$this->arAllJsScript = array();
		$this->arAllJsNodeLinks = array();
		$index = 1;
		$rAllNodes = $this->XPath->query("//script");
		for ($i = 0; $i < $rAllNodes->length; $i++) {
			$this->arAllJsNodeLinks[$index] = $rAllNodes->item($i);
			$attrList = $this->arAllJsNodeLinks[$index]->attributes;
			$this->arAllJsScript[$index] = array();
			foreach ($attrList as $attr) {
				/**
				 * @var $attr \DOMAttr
				 */
				$this->arAllJsScript[$index][$attr->localName] = $attr->nodeValue;
			}

			$this->arAllJsScript[$index]['CONTENT'] = $this->arAllJsNodeLinks[$index]->textContent;

			$bAllowScript = true;
			if (isset($this->arAllJsScript[$index]['src'])) {
				$arUrl = parse_url($this->arAllJsScript[$index]['src']);
				if (empty($arUrl['host'])) {
					$arPath = pathinfo($arUrl['path']);
					$arPath['extension'] = amopt_strtolower($arPath['extension']);
					$this->arAllJsScript[$index]['extension'] = $arPath['extension'];
					if (!$bReturnAllExtensions && amopt_strtolower($arPath['extension']) != "js") {
						$bAllowScript = false;
					}
					if ($bAllowScript) {
						$strFileName = \CAmminaOptimizer::Rel2AbsUrl($APPLICATION->GetCurPage(true), $arUrl['path']);
						if (strlen($strFileName) > 0 && file_exists($_SERVER['DOCUMENT_ROOT'] . $strFileName)) {
							if (strpos($strFileName, '/bitrix/ammina.cache/css.outline/') === 0 || strpos($strFileName, '/bitrix/ammina.cache/css.remote/') === 0 || strpos($strFileName, '/bitrix/ammina.cache/js.outline/') === 0 || strpos($strFileName, '/bitrix/ammina.cache/js.remote/') === 0) {
								$this->arAllJsScript[$index]['src'] = $strFileName;
							} else {
								$this->arAllJsScript[$index]['src'] = $strFileName . "?" . filemtime($_SERVER['DOCUMENT_ROOT'] . $strFileName);
							}
						}
					}
				}
			}
			if ($bAllowScript && ($bIgnoreSkip || ((!isset($this->arAllJsScript[$index]['data-skip']) || !$this->arAllJsScript[$index]['data-skip']) && (!isset($this->arAllJsScript[$index]['data-skip-moving']) || !$this->arAllJsScript[$index]['data-skip-moving'])))) {
				$index++;
			} else {
				unset($this->arAllJsScript[$index]);
				unset($this->arAllJsNodeLinks[$index]);
			}
		}
		return $this->arAllJsScript;
	}

	public function removeJsScriptAttribute($index, $strAttribute)
	{
		if (isset($this->arAllJsNodeLinks[$index])) {
			/**
			 * @var $oNode \DOMElement
			 */
			$oNode =& $this->arAllJsNodeLinks[$index];
			$oNode->removeAttribute($strAttribute);
		}
	}

	public function removeJsScript($index)
	{
		if (isset($this->arAllJsNodeLinks[$index])) {
			/**
			 * @var $oNode \DOMNode
			 */
			$oNode =& $this->arAllJsNodeLinks[$index];
			$oNode->parentNode->removeChild($oNode);
			unset($this->arAllJsNodeLinks[$index]);
		}
	}

	public function moveJsScriptBefore($index, $iBeforeIndex)
	{
		if (isset($this->arAllJsNodeLinks[$index]) && isset($this->arAllJsNodeLinks[$iBeforeIndex])) {
			/**
			 * @var $oNode \DOMNode
			 */
			$oNode =& $this->arAllJsNodeLinks[$index];
			/**
			 * @var $oNodeTo \DOMNode
			 */
			$oNodeTo =& $this->arAllJsNodeLinks[$iBeforeIndex];
			$oNodeTo->parentNode->insertBefore($oNode, $oNodeTo);
			/*
			$oNode->parentNode->removeChild($oNode);
			unset($this->arAllJsNodeLinks[$index]);
			*/
		}
	}

	public function setSrcForJsScript($index, $src)
	{
		if (isset($this->arAllJsNodeLinks[$index])) {
			/**
			 * @var $oNode \DOMNode
			 */
			$oNode =& $this->arAllJsNodeLinks[$index];
			$bFinded = false;
			$rAttributes = $oNode->attributes;
			for ($i = 0; $i < $rAttributes->length; $i++) {
				$oAttr = $rAttributes->item($i);
				if ($oAttr->nodeName == "src") {
					$oAttr->nodeValue = $src;
					$bFinded = true;
				}
			}
			if (!$bFinded) {
				$oAttrSrc = $this->DOM->createAttribute('src');
				$oAttrSrc->value = $src;
				$oNode->appendChild($oAttrSrc);
			}
		}
	}

	public function setContentForJsScript($index, $content)
	{
		if (isset($this->arAllJsNodeLinks[$index])) {
			/**
			 * @var $oNode \DOMNode
			 */
			$oNode =& $this->arAllJsNodeLinks[$index];
			$oNode->nodeValue = $content;
		}
	}

	public function setSrcForJsOutlineScript($index, $src)
	{
		if (isset($this->arAllJsNodeLinks[$index])) {
			/**
			 * @var $oNode \DOMNode
			 */
			$oNode =& $this->arAllJsNodeLinks[$index];
			$oNode->nodeValue = '';
			$bFinded = false;
			$rAttributes = $oNode->attributes;
			for ($i = 0; $i < $rAttributes->length; $i++) {
				$oAttr = $rAttributes->item($i);
				if ($oAttr->nodeName == "src") {
					$oAttr->nodeValue = $src;
					$bFinded = true;
				}
			}
			if (!$bFinded) {
				$oAttrSrc = $this->DOM->createAttribute('src');
				$oAttrSrc->value = $src;
				$oNode->appendChild($oAttrSrc);
			}
		}
	}

	public function setDeferForInlineJsScript($index, $content)
	{
		return;
		if (isset($this->arAllJsNodeLinks[$index])) {
			$strSrcContent = 'data:text/javascript;base64,' . base64_encode($content);
			/**
			 * @var $oNode \DOMNode
			 */
			$oNode =& $this->arAllJsNodeLinks[$index];
			$oNode->nodeValue = '';
			$bFinded = false;
			$rAttributes = $oNode->attributes;
			for ($i = 0; $i < $rAttributes->length; $i++) {
				$oAttr = $rAttributes->item($i);
				if ($oAttr->nodeName == "defer") {
					$oAttr->nodeValue = "defer";
					$bFinded = true;
				}
			}
			if (!$bFinded) {
				$oAttrSrc = $this->DOM->createAttribute('defer');
				$oAttrSrc->value = "defer";
				$oNode->appendChild($oAttrSrc);
			}

			$bFinded = false;
			$rAttributes = $oNode->attributes;
			for ($i = 0; $i < $rAttributes->length; $i++) {
				$oAttr = $rAttributes->item($i);
				if ($oAttr->nodeName == "src") {
					$oAttr->nodeValue = $strSrcContent;
					$bFinded = true;
				}
			}
			if (!$bFinded) {
				$oAttrSrc = $this->DOM->createAttribute('src');
				$oAttrSrc->value = $strSrcContent;
				$oNode->appendChild($oAttrSrc);
			}
		}
	}

	public function setDeferForJsScript($index)
	{
		if (isset($this->arAllJsNodeLinks[$index])) {
			/**
			 * @var $oNode \DOMNode
			 */
			$oNode =& $this->arAllJsNodeLinks[$index];
			if (amopt_strpos($oNode->nodeValue, 'BX.admin.moreButton.init') !== false) {
				$oNode->nodeValue = 'BX.admin.panel.Init();' . $oNode->nodeValue;
			}
			$bFinded = false;
			$rAttributes = $oNode->attributes;
			for ($i = 0; $i < $rAttributes->length; $i++) {
				$oAttr = $rAttributes->item($i);
				if ($oAttr->nodeName == "type") {
					if ($oAttr->nodeValue == "text/javascript") {
						$oAttr->nodeValue = "deferjs";
					}
					$bFinded = true;
				}
			}
			if (!$bFinded) {
				$oAttrSrc = $this->DOM->createAttribute('type');
				$oAttrSrc->value = "deferjs";
				$oNode->appendChild($oAttrSrc);
			}
		}
	}

	public function doAppendDeferInitScript()
	{
		$rBody = $this->XPath->query("//body");
		if ($rBody->length == 1) {
			/**
			 * @var $oBody \DOMNode
			 */
			$oBody = $rBody->item(0);
			$oNode = $this->DOM->createElement("script");
			$oAttrDefer = $this->DOM->createAttribute('defer');
			$oNode->appendChild($oAttrDefer);

			$strDeferContent = 'data:text/javascript;base64,' . base64_encode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/ammina.optimizer/system/defer.min.js"));
			$oAttrSrc = $this->DOM->createAttribute('src');
			$oAttrSrc->value = $strDeferContent;
			$oNode->appendChild($oAttrSrc);

			$oBody->appendChild($oNode);
		}
	}

	public function setJsDelayScript($index, $content = '', $src = '', $iTimeout = 5000)
	{
		static $iIndexIdTags = 1;
		if (isset($this->arAllJsNodeLinks[$index])) {
			/**
			 * @var $oNode \DOMNode
			 */
			$oNode =& $this->arAllJsNodeLinks[$index];
			$bFinded = false;
			$rAttributes = $oNode->attributes;
			for ($i = 0; $i < $rAttributes->length; $i++) {
				$oAttr = $rAttributes->item($i);
				if ($oAttr->nodeName == "src") {
					$oAttr->parentNode->removeAttributeNode($oAttr);
				} elseif ($oAttr->nodeName == "data-amoptdelay-id") {
					$oAttr->nodeValue = $iIndexIdTags;
					$bFinded = true;
				}
			}
			if (!$bFinded) {
				$oAttrSrc = $this->DOM->createAttribute('data-amoptdelay-id');
				$oAttrSrc->value = $iIndexIdTags;
				$oNode->appendChild($oAttrSrc);
			}
			$strNewContent = '/*AMOPT DELAY SCRIPT*/window.setTimeout(function () {var curTag = document.querySelector("script[data-amoptdelay-id=\'' . $iIndexIdTags . '\']");var newEl = document.createElement(\'script\');newEl.async = 1;';
			if (amopt_strlen($content) > 0) {
				$strNewContent .= 'var amopttmpcont' . $iIndexIdTags . '=' . \CUtil::PhpToJSObject($content) . ';newEl.textContent = amopttmpcont' . $iIndexIdTags . ';';
			} elseif (amopt_strlen($src) > 0) {
				$strNewContent .= 'var amopttmpsrc' . $iIndexIdTags . '=' . \CUtil::PhpToJSObject($src) . ';newEl.src = amopttmpsrc' . $iIndexIdTags . ';';
			}
			$strNewContent .= 'curTag.parentNode.insertBefore(newEl, curTag);curTag.parentNode.removeChild(curTag);}, ' . $iTimeout . ');';

			$oNode->nodeValue = $strNewContent;
			$iIndexIdTags++;
		}
	}

	public function moveJsToBodyEnd()
	{
		$rBody = $this->XPath->query("//body");
		if ($rBody->length == 1) {
			/**
			 * @var $oBody \DOMNode
			 */
			$oBody = $rBody->item(0);
			$rAllNodes = $this->XPath->query("//script");
			for ($i = 0; $i < $rAllNodes->length; $i++) {
				/**
				 * @var $oNode \DOMNode
				 */
				$oNode = $rAllNodes->item($i);
				$attrList = $oNode->attributes;
				$arAttr = array();
				foreach ($attrList as $attr) {
					/**
					 * @var $attr \DOMAttr
					 */
					$arAttr[$attr->localName] = $attr->nodeValue;
				}
				if ($arAttr['data-amopt-skip-moving'] != 'true' && $arAttr['data-skip-moving'] != 'true' && (amopt_strlen($arAttr['type']) <= 0 || amopt_strtolower($arAttr['type']) == "text/javascript" || amopt_strtolower($arAttr['type']) == "application/javascript")) {
					$oBody->appendChild($oNode);
				}
			}
		}
	}

	public function moveJsToHeadStart($index)
	{
		$rHead = $this->XPath->query("//head");
		if ($rHead->length == 1 && isset($this->arAllJsNodeLinks[$index])) {
			$oHead = $rHead->item(0);
			/**
			 * @var $oNode \DOMNode
			 */
			$oNode =& $this->arAllJsNodeLinks[$index];
			$bFinded = false;
			$rAttributes = $oNode->attributes;
			for ($i = 0; $i < $rAttributes->length; $i++) {
				$oAttr = $rAttributes->item($i);
				if ($oAttr->nodeName === "data-amopt-skip-moving") {
					$oAttr->nodeValue = 'true';
					$bFinded = true;
				}
			}
			if (!$bFinded) {
				$oAttrSrc = $this->DOM->createAttribute('data-amopt-skip-moving');
				$oAttrSrc->value = 'true';
				$oNode->appendChild($oAttrSrc);
			}
			if ($oHead->firstChild) {
				$oHead->insertBefore($oNode, $oHead->firstChild);
			} else {
				$oHead->appendChild($oNode);
			}
		}
	}

	public function getAllImages($arCheckTypes)
	{
		$this->arAllImages = array();
		$this->arAllImagesNodeLinks = array();
		$index = 1;
		$rAllNodes = $this->XPath->query("//*");
		for ($i = 0; $i < $rAllNodes->length; $i++) {
			/**
			 * @var $oNode \DOMNode
			 */
			$oNode = $rAllNodes->item($i);
			if (!$arCheckTypes['CHECK_SRCSET_IMG'] && !$arCheckTypes['CHECK_ALL_OTHER'] && !$arCheckTypes['CHECK_BACKGROUND'] && !$arCheckTypes['CHECK_ATTRIBUTES'] && $oNode->nodeName != "img") {
				continue;
			}
			/**
			 * @todo Улучшить для CHECK_ALL_OTHER производительность
			 */
			if ($arCheckTypes['CHECK_ALL_OTHER'] && $oNode->nodeName == "script") {
				$strNodeValue = amopt_strtolower($oNode->nodeValue);
				foreach ($this->arImageTypes as $k => $v) {
					if (amopt_strpos($strNodeValue, "." . $k) !== false) {
						$this->arAllImages[$index] = array(
							"SCRIPT" => trim($oNode->nodeValue),
							"TYPE" => "SCRIPT",
						);
						$this->arAllImagesNodeLinks[$index] = $oNode;
						$index++;
						break;
					}
				}
			} else {
				$attrList = $oNode->attributes;
				foreach ($attrList as $oAttr) {
					/**
					 * @var $oAttr \DOMAttr
					 */
					if ($oAttr->localName == "src") {
						if ($arCheckTypes['CHECK_IMG'] || $arCheckTypes['CHECK_ATTRIBUTES']) {
							if ($this->isImage(trim($oAttr->nodeValue))) {
								$this->arAllImages[$index] = array(
									"IMAGE" => urldecode(trim($oAttr->nodeValue)),
									"TYPE" => "ATTRIBUTE",
									"ATTR" => "SRC",
								);
								$this->arAllImagesNodeLinks[$index] = $oAttr;
								$index++;
							}
						}
					} elseif ($oAttr->localName == "data-src") {
						if ($arCheckTypes['CHECK_DATA_SRC'] || $arCheckTypes['CHECK_ATTRIBUTES'] || $arCheckTypes['CHECK_ALL_OTHER']) {
							if ($this->isImage(trim($oAttr->nodeValue))) {
								$this->arAllImages[$index] = array(
									"IMAGE" => urldecode(trim($oAttr->nodeValue)),
									"TYPE" => "ATTRIBUTE",
									"ATTR" => "DATA-SRC",
								);
								$this->arAllImagesNodeLinks[$index] = $oAttr;
								$index++;
							}
						}
					} elseif ($oAttr->localName == "srcset") {
						if ($arCheckTypes['CHECK_SRCSET_IMG']) {
							if ($this->isSrcImage(trim($oAttr->nodeValue))) {
								$this->arAllImages[$index] = array(
									"IMAGE" => urldecode(trim($oAttr->nodeValue)),
									"TYPE" => "SRCSET",
									"ATTR" => "SRCSET",
								);
								$this->arAllImagesNodeLinks[$index] = $oAttr;
								$index++;
							}
						}
					} elseif ($oAttr->localName == "data-srcset") {
						if ($arCheckTypes['CHECK_SRCSET_IMG']) {
							if ($this->isSrcImage(trim($oAttr->nodeValue))) {
								$this->arAllImages[$index] = array(
									"IMAGE" => urldecode(trim($oAttr->nodeValue)),
									"TYPE" => "SRCSET",
									"ATTR" => "DATA-SRCSET",
								);
								$this->arAllImagesNodeLinks[$index] = $oAttr;
								$index++;
							}
						}
					} elseif ($oAttr->localName == "style") {
						if ($arCheckTypes['CHECK_BACKGROUND'] || $arCheckTypes['CHECK_ALL_OTHER']) {
							if (amopt_stripos($oAttr->nodeValue, 'url') !== false) {
								$this->arAllImages[$index] = array(
									"STYLE" => trim($oAttr->nodeValue),
									"TYPE" => "STYLE",
								);
								$this->arAllImagesNodeLinks[$index] = $oAttr;
								$index++;
							}
						}
					} elseif ($arCheckTypes['CHECK_ALL_OTHER'] || $arCheckTypes['CHECK_ATTRIBUTES']) {
						if ($this->isImage(trim($oAttr->nodeValue))) {
							$this->arAllImages[$index] = array(
								"IMAGE" => urldecode(trim($oAttr->nodeValue)),
								"TYPE" => "ATTRIBUTE",
								"ATTR" => $oAttr->localName,
							);
							$this->arAllImagesNodeLinks[$index] = $oAttr;
							$index++;
						}
					}
				}
			}
			/*
			$this->arAllCssNodeLinks[$index] = $rAllNodes->item($i);
			$attrList = $this->arAllCssNodeLinks[$index]->attributes;
			$this->arAllCssLinks[$index] = array();
			foreach ($attrList as $attr) {
				/**
				 * @var $attr \DOMAttr
				 *//*
				$this->arAllCssLinks[$index][$attr->localName] = $attr->nodeValue;
			}
			if ((!isset($this->arAllCssLinks[$index]['data-skip']) || !$this->arAllCssLinks[$index]['data-skip']) && ($this->arAllCssLinks[$index]['rel'] == "stylesheet")) {
				$index++;
			} else {
				unset($this->arAllCssLinks[$index]);
				unset($this->arAllCssNodeLinks[$index]);
			}
*/
		}
		return $this->arAllImages;
	}

	public function isImage($strImage)
	{
		$strImage = urldecode($strImage);
		if (amopt_strpos($strImage, '://') !== false || amopt_strpos($strImage, '//') === 0) {
			$arUrl = parse_url($strImage);
			$arPath = ammina_pathinfo($arUrl['path']);
		} else {
			$arPath = ammina_pathinfo($strImage);
		}
		if (isset($arPath['extension'])) {
			if (amopt_strpos($arPath['extension'], '?') !== false) {
				$arPath['extension'] = explode("?", $arPath['extension']);
				$arPath['extension'] = $arPath['extension'][0];
			}
			if (isset($this->arImageTypes[amopt_strtolower($arPath['extension'])])) {
				return true;
			}
		}
		return false;
	}

	protected function isSrcImage($strImage)
	{
		$arImg = explode(", ", $strImage);
		foreach ($arImg as $v) {
			$arV = explode(" ", $v);
			foreach ($arV as $v1) {
				if ($this->isImage($v1)) {
					return true;
				}
			}
		}
		return false;
	}

	public function setImageAttribute($index, $strContent)
	{
		if (isset($this->arAllImagesNodeLinks[$index])) {
			/**
			 * @var $oAttr \DOMAttr
			 */
			$oAttr =& $this->arAllImagesNodeLinks[$index];
			$oAttr->nodeValue = $strContent;
		}
	}

	public function setImageScriptContent($index, $strContent)
	{
		if (isset($this->arAllImagesNodeLinks[$index])) {
			/**
			 * @var $oNode \DOMNode
			 */
			$oNode =& $this->arAllImagesNodeLinks[$index];
			@$oNode->nodeValue = $strContent;
		}
	}

	public function getAllLazyTags($strLazyClasses, $bLazyImg = false)
	{
		$this->arAllLazy = array();
		$this->arAllLazyAttrLinks = array();
		$arAllQuery = array();
		if ($bLazyImg) {
			$arAllQuery[] = '//img';
		}
		$arLazyClass = explode(",", $strLazyClasses);
		foreach ($arLazyClass as $k => &$v) {
			$v = trim($v);
			if (amopt_strlen($v) <= 0) {
				unset($arLazyClass[$k]);
			} else {
				$arAllQuery[] = '//*[php:functionString("amminainclassxpath",@class,"' . $v . '")="Y"]';
			}
		}
		$index = 1;
		$rAllNodes = $this->XPath->query(implode("|", $arAllQuery));
		for ($i = 0; $i < $rAllNodes->length; $i++) {
			/**
			 * @var $oNode \DOMNode
			 */
			$oNode = $rAllNodes->item($i);
			if ($oNode->nodeName == "img") {
				$attrList = $oNode->attributes;
				foreach ($attrList as $oAttr) {
					/**
					 * @var $oAttr \DOMAttr
					 */
					if ($oAttr->localName == "src") {
						$this->arAllLazy[$index] = array(
							"IMAGE" => $oAttr->nodeValue,
							"TYPE" => "img",
							"ATTRIBUTE" => "src",
						);
						$this->arAllLazyAttrLinks[$index] = array(
							"NODE" => $oNode,
							"ATTR" => $oAttr,
						);
						$index++;
						break;
					}
				}
			} else {
				$attrList = $oNode->attributes;
				foreach ($attrList as $oAttr) {
					/**
					 * @var $oAttr \DOMAttr
					 */
					if ($oAttr->localName == "style") {
						$strNodeVal = $oAttr->nodeValue;
						if (amopt_strpos($strNodeVal, 'url') !== false) {
							$this->arAllLazy[$index] = array(
								"STYLE" => $strNodeVal,
								"TYPE" => "style",
								"TAG" => $oNode->nodeName,
							);
							$this->arAllLazyAttrLinks[$index] = array(
								"NODE" => $oNode,
								"ATTR" => $oAttr,
							);
							$index++;
							break;
						}
					}
				}
			}
		}
		return $this->arAllLazy;
	}

	public function setLazyAttribute($index, $strContent)
	{
		if (isset($this->arAllLazyAttrLinks[$index]['ATTR'])) {
			/**
			 * @var $oAttr \DOMAttr
			 */
			$oAttr =& $this->arAllLazyAttrLinks[$index]['ATTR'];
			$oAttr->nodeValue = $strContent;
		}
	}

	public function setLazyNodeAttribute($index, $strAttribute, $strContent)
	{
		if (isset($this->arAllLazyAttrLinks[$index]['NODE'])) {
			/**
			 * @var $oNode \DOMNode
			 */
			$oNode =& $this->arAllLazyAttrLinks[$index]['NODE'];
			$bExistsAttribute = false;
			$attrList = $oNode->attributes;
			foreach ($attrList as $oAttr) {
				/**
				 * @var $oAttr \DOMAttr
				 */
				if ($oAttr->localName == $strAttribute) {
					$bExistsAttribute = true;
					$oAttr->nodeValue = $strContent;
					break;
				}
			}
			if (!$bExistsAttribute) {
				/**
				 * @var $oAttr \DOMAttr
				 */
				$oAttr = $this->DOM->createAttribute($strAttribute);
				$oAttr->value = $strContent;
				$oNode->appendChild($oAttr);
			}
		}
	}

	public function addLazyClassAttribute($index, $strClass)
	{
		if (isset($this->arAllLazyAttrLinks[$index]['NODE'])) {
			/**
			 * @var $oNode \DOMNode
			 */
			$oNode =& $this->arAllLazyAttrLinks[$index]['NODE'];
			$bExistsAttribute = false;
			$attrList = $oNode->attributes;
			foreach ($attrList as $oAttr) {
				/**
				 * @var $oAttr \DOMAttr
				 */
				if ($oAttr->localName == "class") {
					$bExistsAttribute = true;
					$oAttr->nodeValue = $oAttr->nodeValue . " " . $strClass;
					break;
				}
			}
			if (!$bExistsAttribute) {
				/**
				 * @var $oAttr \DOMAttr
				 */
				$oAttr = $this->DOM->createAttribute("class");
				$oAttr->value = $strClass;
				$oNode->appendChild($oAttr);
			}
		}
	}

	protected function doNormalizeCharsetValue($strValue)
	{
		global $APPLICATION;
		if (!defined("BX_UTF") || BX_UTF !== true) {
			return $APPLICATION->ConvertCharset($strValue, (amopt_strlen(LANG_CHARSET) > 0 ? LANG_CHARSET : SITE_CHARSET), "UTF-8");
		}
		return $strValue;
	}

	public function removeWhiteSpace()
	{
		$this->DOM->normalizeDocument();
		$oTextNodes = $this->XPath->query('//text()');
		$arSkip = array("style", "pre", "code", "script", "textarea");
		foreach ($oTextNodes as $oNode) {
			/**
			 * @var $oNode \DOMNode
			 */
			$strNodePath = $oNode->getNodePath();
			$bSkip = false;
			foreach ($arSkip as $strSkip) {
				if (amopt_strpos($strNodePath, "/" . $strSkip) !== false) {
					$bSkip = true;
					break;
				}
			}
			if ($bSkip) {
				continue;
			}
			$oNode->nodeValue = preg_replace("/\s{2,}/", " ", $oNode->nodeValue);
		}
	}

	public function removeComments()
	{
		$this->DOM->normalizeDocument();
		$oNodes = $this->XPath->query('//comment()');
		foreach ($oNodes as $oNode) {
			/**
			 * @var $oNode \DOMNode
			 */
			$strVal = $oNode->nodeValue;
			if (amopt_strpos($strVal, '[') !== 0) {
				$oNode->parentNode->removeChild($oNode);
			}
		}
	}

	public function removeScriptAttr()
	{
		$this->DOM->normalizeDocument();
		$oNodes = $this->XPath->query('//script');
		foreach ($oNodes as $oNode) {
			/**
			 * @var $oNode \DOMElement
			 */
			if ($oNode->hasAttribute("type") && amopt_strtolower($oNode->getAttribute("type")) !== 'text/javascript') {
				continue;
			}
			$oNode->removeAttribute("type");
		}
	}

	public function removeStyleAttr()
	{
		$this->DOM->normalizeDocument();
		$oNodes = $this->XPath->query('//style');
		foreach ($oNodes as $oNode) {
			/**
			 * @var $oNode \DOMElement
			 */
			if ($oNode->hasAttribute("type") && amopt_strtolower($oNode->getAttribute("type")) !== 'text/css') {
				continue;
			}
			$oNode->removeAttribute("type");
		}
	}

	public function removePreTag()
	{
		$oNodes = $this->XPath->query('//pre');
		foreach ($oNodes as $oNode) {
			/**
			 * @var $oNode \DOMNode
			 */
			$oNode->parentNode->removeChild($oNode);
		}
	}

	public function getAllJsScriptWithBxLoadCss()
	{
		$this->arAllJsScriptWithBxLoadCss = array();
		$this->arAllJsNodeLinksWithBxLoadCss = array();
		$index = 1;
		$rAllNodes = $this->XPath->query("//script");
		for ($i = 0; $i < $rAllNodes->length; $i++) {
			/**
			 * @var $oNode \DOMNode
			 */
			$oNode = $rAllNodes->item($i);
			$strValue = $oNode->textContent;
			if (amopt_strpos(amopt_strtolower($strValue), 'bx.loadcss([') !== false) {
				$this->arAllJsNodeLinksWithBxLoadCss[$index] = $rAllNodes->item($i);
				$this->arAllJsScriptWithBxLoadCss[$index]['CONTENT'] = $this->arAllJsNodeLinksWithBxLoadCss[$index]->textContent;
				$index++;
			}
		}
		return $this->arAllJsScriptWithBxLoadCss;
	}

	public function setJsScriptWithBxLoadCssContent($index, $strContent)
	{
		if (isset($this->arAllJsNodeLinksWithBxLoadCss[$index])) {
			/**
			 * @var $oNode \DOMNode
			 */
			$oNode =& $this->arAllJsNodeLinksWithBxLoadCss[$index];
			$oNode->nodeValue = $strContent;
		}
	}

	public function doRemoveLinkPreloadPrefetch()
	{
		$oNodes = $this->XPath->query('//link[@rel="preload"]|//link[@rel="prefetch"]');
		foreach ($oNodes as $oNode) {
			/**
			 * @var $oNode \DOMNode
			 */
			$oNode->parentNode->removeChild($oNode);
		}
	}
}
