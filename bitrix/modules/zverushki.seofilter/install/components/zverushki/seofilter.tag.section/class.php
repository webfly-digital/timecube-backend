<?
use \Zverushki\Seofilter\Components\tagSection,
	\Bitrix\Main\Loader,
	\Bitrix\Main\Localization\Loc;
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global CIntranetToolbar $INTRANET_TOOLBAR
 */

Loc::loadMessages(__FILE__);

if (!Loader::includeModule('iblock'))
{
	ShowError(Loc::getMessage('IBLOCK_MODULE_NOT_INSTALLED'));
	return;
}
if (!Loader::includeModule('zverushki.seofilter'))
{
	ShowError(Loc::getMessage('SEOFILTER_MODULE_NOT_INSTALLED'));
	return;
}

class SeoFilterTagSectionComponent extends tagSection
{
	public function __construct($component = null)
	{
		parent::__construct($component);
	}
}?>