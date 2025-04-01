<?
namespace Zverushki\Seofilter;

use Bitrix\Main,
	Bitrix\Main\Config\Option,
	Bitrix\Main\Application,
	Bitrix\Main\Loader,
	Zverushki\Seofilter\Internals;


/**
* class Controller
*
*
* @package Zverushki\Seofilter
*/
class Custom {
	static $requestUri = false;
	static public function setParams(&$arParams){
		if(!Filter\Seo::isCPUSeo())
			return;

		self::$requestUri = configuration::get('requestUri');

		if(self::$requestUri){
			$arParams["PAGER_BASE_LINK"] = self::$requestUri;
		    $arParams["PAGER_BASE_LINK_ENABLE"] = "Y";
		    if($arParams['PAGER_PARAMS_NAME'])
				$GLOBALS[$arParams['PAGER_PARAMS_NAME']]['BASE_LINK'] = $arParams["PAGER_BASE_LINK"];
		}
	}
}