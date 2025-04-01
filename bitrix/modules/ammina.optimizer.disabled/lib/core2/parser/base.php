<?

namespace Ammina\Optimizer\Core2\Parser;

class Base
{
	protected $strHTML = '';
	protected $bMinifiedOutput = false;

	/**
	 * @param $strParserName
	 *
	 * @return Base
	 */
	public static function createParser($strParserName, $bCheckNotValidOpenTag = false, $arOtherOptions = array())
	{
		$className = 'Ammina\\Optimizer\\Core2\\Parser\\' . $strParserName;
		return new $className($bCheckNotValidOpenTag, $arOtherOptions);
	}

	public function setOutputMinified($bMininfied = true)
	{
		$this->bMinifiedOutput = $bMininfied;
	}

	public function doParse($strHTML)
	{
	}

	public function doParsePart($strHTML, $strEncode = "utf-8")
	{
	}

	public function doSaveHTML()
	{
		return $this->strHTML;
	}

	public function doSaveHTMLPart()
	{
		return $this->strHTML;
	}

	public function getAllClassesAndIdent()
	{
		return array(
			"CLASSES" => array(),
			"IDENT" => array(),
		);
	}

	public function getAllCssLinks()
	{
		return array();
	}

	public function removeCssLink($index)
	{
	}

	public function setHrefForCssLink($index, $href)
	{
	}

	public function getAllCssStyle()
	{
		return array();
	}

	public function replaceCssStyleToLink($index, $strFileName)
	{
	}

	public function removeCssStyle($index)
	{
	}

	/**
	 * @param $strXPathParent
	 * @param $strTag
	 * @param array $arAttributes
	 * @param string $strTagContent
	 * @param bool $bFirstChild
	 */
	public function AddTag($strXPathParent, $strTag, $arAttributes = array(), $strTagContent = '', $bFirstChild = false)
	{
	}

	public function AddTagBeforeCssLink($iCssLinkIndex, $strTag, $arAttributes = array(), $strTagContent = '')
	{
	}

	public function doUnlockMoveBxRandScript()
	{
	}

	public function doMoveBxRandScriptToEndBody()
	{
	}

	public function doMakeStaticAsproSetTheme()
	{

	}

	public function doUnlockSkipMoveJsAspro()
	{
	}

	public function doUnlockSkipMoveJsHead()
	{
	}

	public function doUnlockSkipMoveJsBody()
	{
	}

	public function getAllJsScript($bIgnoreSkip = false, $bReturnAllExtensions = false)
	{
		return array();
	}

	public function removeJsScriptAttribute($index, $strAttribute)
	{
	}

	public function removeJsScript($index)
	{
	}

	public function moveJsScriptBefore($index, $iBeforeIndex)
	{
	}

	public function setSrcForJsScript($index, $src)
	{
	}

	public function setContentForJsScript($index, $content)
	{

	}

	public function setSrcForJsOutlineScript($index, $src)
	{
	}

	public function setDeferForInlineJsScript($index, $content)
	{
	}

	public function setDeferForJsScript($index)
	{
	}

	public function doAppendDeferInitScript()
	{

	}

	public function setJsDelayScript($index, $content = '', $src = '', $iTimeout = 5000)
	{
	}

	public function moveJsToBodyEnd()
	{
	}

	public function moveJsToHeadStart($index)
	{
	}

	public function getAllImages($arCheckTypes)
	{
		return array();
	}

	public function isImage($strImage)
	{
	}

	public function setImageAttribute($index, $strContent)
	{
	}

	public function setImageScriptContent($index, $strContent)
	{
	}

	public function getAllLazyTags($strLazyClasses, $bLazyImg = false)
	{
	}

	public function setLazyAttribute($index, $strContent)
	{
	}

	public function setLazyNodeAttribute($index, $strAttribute, $strContent)
	{
	}

	public function addLazyClassAttribute($index, $strClass)
	{
	}

	public function removeWhiteSpace()
	{
	}

	public function removeComments()
	{
	}

	public function removeScriptAttr()
	{
	}

	public function removeStyleAttr()
	{
	}

	public function removePreTag()
	{
	}

	public function getAllJsScriptWithBxLoadCss()
	{
	}

	public function setJsScriptWithBxLoadCssContent($index, $strContent)
	{
	}

	public function doRemoveLinkPreloadPrefetch()
	{
	}

}