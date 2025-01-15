<?
namespace Zverushki\Seofilter\Filter;

use Bitrix\Main,
	Bitrix\Main\Entity,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\Loader,
	Zverushki\Seofilter\Internals,
	Zverushki\Seofilter\emoji,
	Zverushki\Seofilter\configuration,
	Zverushki\Seofilter\Sections\Section;

/**
 *
 */
class Seo
{
	static private $arSeo = array();
	static private $isCPUSeo = false;
	static private $canonicalCPU;
	static public function initSeoTag($id = false){
		if(empty($id))return false;
		self::$isCPUSeo = true;
		configuration::set('isCPUSeo', true);

		self::$arSeo = Internals\SeotagTable::getList(array(
			'order' => array('ID' => "DESC"),
			'filter' => array('SETTING_ID' => $id),
			'select' => array('ID', 'SEO_DESCRIPTION_TOP', 'SEO_DESCRIPTION', 'PAGE_TITLE', 'META_TITLE', 'META_KEYWORDS', 'META_DESCRIPTION')
		))
		->fetch();

		$variable = configuration::get('VARIABLE');
		$var = configuration::get('VAR');
		$setting = configuration::get('setting');

		self::$arSeo['PAGE_TITLE'] = emoji::decode(htmlspecialcharsback(self::$arSeo['PAGE_TITLE']));
		self::$arSeo['META_TITLE'] = emoji::decode(htmlspecialcharsback(self::$arSeo['META_TITLE']));
		self::$arSeo['META_DESCRIPTION'] = emoji::decode(htmlspecialcharsback(self::$arSeo['META_DESCRIPTION']));
		self::$arSeo['META_KEYWORDS'] = emoji::decode(htmlspecialcharsback(self::$arSeo['META_KEYWORDS']));

		self::$arSeo['SEO_DESCRIPTION_TOP'] = emoji::decode(htmlspecialcharsback(self::$arSeo['SEO_DESCRIPTION_TOP']));
		self::$arSeo['SEO_DESCRIPTION'] = emoji::decode(htmlspecialcharsback(self::$arSeo['SEO_DESCRIPTION']));

		if(!empty($variable)){
			foreach ($variable as $code => $val) {
				sort($val);
				self::$arSeo['PAGE_TITLE'] = preg_replace('/\#'.$code.'\#/i', implode(', ', $val), self::$arSeo['PAGE_TITLE']);
				self::$arSeo['META_TITLE'] = preg_replace('/\#'.$code.'\#/i', implode(', ', $val), self::$arSeo['META_TITLE']);
				self::$arSeo['META_DESCRIPTION'] = preg_replace('/\#'.$code.'\#/i', implode(', ', $val), self::$arSeo['META_DESCRIPTION']);
				self::$arSeo['META_KEYWORDS'] = preg_replace('/#'.$code.'#/i', implode(', ', $val), self::$arSeo['META_KEYWORDS']);

				self::$arSeo['SEO_DESCRIPTION_TOP'] = preg_replace('/\#'.$code.'\#/i', implode(', ', $val), self::$arSeo['SEO_DESCRIPTION_TOP']);
				self::$arSeo['SEO_DESCRIPTION'] = preg_replace('/\#'.$code.'\#/i', implode(', ', $val), self::$arSeo['SEO_DESCRIPTION']);
			}


		}

		if($var){
			foreach($var as $code => $val){
				self::$arSeo['PAGE_TITLE'] = preg_replace('/\#VAR_' . $code . '\#/i', $val, self::$arSeo['PAGE_TITLE']);
				self::$arSeo['META_TITLE'] = preg_replace('/\#VAR_' . $code . '\#/i', $val, self::$arSeo['META_TITLE']);
				self::$arSeo['META_DESCRIPTION'] = preg_replace('/\#VAR_' . $code . '\#/i', $val, self::$arSeo['META_DESCRIPTION']);
				self::$arSeo['META_KEYWORDS'] = preg_replace('/#VAR_' . $code . '#/i', $val, self::$arSeo['META_KEYWORDS']);

				self::$arSeo['SEO_DESCRIPTION_TOP'] = preg_replace('/\#VAR_' . $code . '\#/i', $val, self::$arSeo['SEO_DESCRIPTION_TOP']);
				self::$arSeo['SEO_DESCRIPTION'] = preg_replace('/\#VAR_' . $code . '\#/i', $val, self::$arSeo['SEO_DESCRIPTION']);
			}
		}
		self::$arSeo['PAGE_TITLE'] = preg_replace('/\#VAR_(.*?)\#/i', '', self::$arSeo['PAGE_TITLE']);
		self::$arSeo['META_TITLE'] = preg_replace('/\#VAR_(.*?)\#/i', '', self::$arSeo['META_TITLE']);
		self::$arSeo['META_DESCRIPTION'] = preg_replace('/\#VAR_(.*?)\#/i', '', self::$arSeo['META_DESCRIPTION']);
		self::$arSeo['META_KEYWORDS'] = preg_replace('/\#VAR_(.*?)\#/i', '', self::$arSeo['META_KEYWORDS']);

		self::$arSeo['SEO_DESCRIPTION_TOP'] = preg_replace('/\#VAR_(.*?)\#/i','', self::$arSeo['SEO_DESCRIPTION_TOP']);
		self::$arSeo['SEO_DESCRIPTION'] = preg_replace('/\#VAR_(.*?)\#/i', '', self::$arSeo['SEO_DESCRIPTION']);
		if($setting)
			Section::replace($setting, self::$arSeo);

		$pagen = static::isPagen();
		if(!empty($pagen)){
			$pagen = Loc::getMessage('SEOFILTER_SECTION_PAGEN_NAME', array('#N#' => $pagen));
		}

		self::$arSeo['PAGE_TITLE'] = trim(preg_replace('/\#DOP_PAGEN\#/i', $pagen, self::$arSeo['PAGE_TITLE']));
		self::$arSeo['META_TITLE'] = trim(preg_replace('/\#DOP_PAGEN\#/i', $pagen, self::$arSeo['META_TITLE']));
		self::$arSeo['META_DESCRIPTION'] = trim(preg_replace('/\#DOP_PAGEN\#/i', $pagen, self::$arSeo['META_DESCRIPTION']));
		self::$arSeo['META_KEYWORDS'] = trim(preg_replace('/#DOP_PAGEN#/i', $pagen, self::$arSeo['META_KEYWORDS']));
		self::$arSeo['SEO_DESCRIPTION_TOP'] = trim(preg_replace('/\#DOP_PAGEN\#/i', $pagen, self::$arSeo['SEO_DESCRIPTION_TOP']));
		self::$arSeo['SEO_DESCRIPTION'] = trim(preg_replace('/\#DOP_PAGEN\#/i', $pagen, self::$arSeo['SEO_DESCRIPTION']));
	}

	static public function getSection($iblockId, $sectionId, &$sectionIds, $t = 'asc'){
		$arFilter = Array('IBLOCK_ID' => $iblockId, 'GLOBAL_ACTIVE'=>'Y');
		if($t == 'desc')
			$arFilter['IBLOCK_SECTION_ID'] = $sectionId;
		else
			$arFilter['ID'] = $sectionId;

		$dbList = \Bitrix\Iblock\sectionTable::GetList([
			'order' => ['ID' => 'ASC'],
			'filter' => $arFilter,
			'select' => ['ID', 'IBLOCK_SECTION_ID', 'NAME']
		]);
		while($arResult = $dbList->fetch())
		{
			$sectionIds[] = $arResult['ID'];
			static::getSection($iblockId, ($t == 'desc' ? $arResult['ID'] : $arResult['IBLOCK_SECTION_ID']), $sectionIds, $t);
		}
	}

	static public function setCanonical($url = ""){
		self::$canonicalCPU = $url;
	}
	static public function viewSeoTag(){
		if(self::isCPUSeo()){

			if(\Bitrix\Main\Loader::includeModule("aristov.vregions")){
				self::$arSeo['PAGE_TITLE'] = \Aristov\Vregions\Tools::makeText(self::$arSeo['PAGE_TITLE']);
				self::$arSeo['META_TITLE'] = \Aristov\Vregions\Tools::makeText(self::$arSeo['META_TITLE']);
				self::$arSeo['META_DESCRIPTION'] = \Aristov\Vregions\Tools::makeText(self::$arSeo['META_DESCRIPTION']);
				self::$arSeo['META_KEYWORDS'] = \Aristov\Vregions\Tools::makeText(self::$arSeo['META_KEYWORDS']);
				self::$arSeo['SEO_DESCRIPTION_TOP'] = \Aristov\Vregions\Tools::makeText(self::$arSeo['SEO_DESCRIPTION_TOP']);
				self::$arSeo['SEO_DESCRIPTION'] = \Aristov\Vregions\Tools::makeText(self::$arSeo['SEO_DESCRIPTION']);
			}

			global $APPLICATION;
			$APPLICATION->SetTitle(self::$arSeo['PAGE_TITLE']);
			$APPLICATION->SetPageProperty('title', self::$arSeo['META_TITLE'] ?: "");
			$APPLICATION->SetPageProperty('description',  self::$arSeo['META_DESCRIPTION'] ?: "");
			$APPLICATION->SetPageProperty('keywords', self::$arSeo['META_KEYWORDS'] ?: "");

			$APPLICATION->AddChainItem(self::$arSeo['PAGE_TITLE'], "");

			$isViewCanonical = true;
			if(!empty(static::isPagen()))
				$isViewCanonical = false;

			if($isViewCanonical){
				$APPLICATION->AddViewContent('seo_text_top', self::$arSeo['SEO_DESCRIPTION_TOP'] ? self::$arSeo['SEO_DESCRIPTION_TOP'] : " ");
				$APPLICATION->AddViewContent('seo_text', self::$arSeo['SEO_DESCRIPTION'] ? self::$arSeo['SEO_DESCRIPTION'] : " ");
			}

			switch (configuration::getOption("template_active", SITE_ID)) {
				case 'aspronext':
					if (Loader::includeModule('aspro.next'))
					{
						unset($APPLICATION->__view['top_desc'], $APPLICATION->__view['bottom_desc']);

						if($isViewCanonical){
						    $APPLICATION->AddViewContent('top_desc', self::$arSeo['SEO_DESCRIPTION_TOP'] ? self::$arSeo['SEO_DESCRIPTION_TOP'] : " ");
						    $APPLICATION->AddViewContent('bottom_desc', self::$arSeo['SEO_DESCRIPTION'] ? self::$arSeo['SEO_DESCRIPTION'] : " ");
						}
					}
					break;
			}

			if(self::$canonicalCPU && configuration::getOption('canonical_active', SITE_ID) == 'Y')
				$APPLICATION->SetPageProperty('canonical', 'https://'.$_SERVER["SERVER_NAME"].''.self::$canonicalCPU);
		}
	}
	static function isPagen(){
		$pagen = '';
		foreach(array_keys($_REQUEST) as $par) {
			if(($pos = strpos($par, "PAGEN_")) !== false){
				$pagen = $_REQUEST[$par];
				break;
			}
		}
		return $pagen;
	}
	static public function isCPUSeo(){
		return configuration::get("isCPUSeo");
	}
	static public function getSeoTag($tag = ""){
		if(empty($tag) || empty(self::$arSeo[$tag]))return "";

		return self::$arSeo[$tag];
	}
}