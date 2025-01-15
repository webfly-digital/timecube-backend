<?
namespace Zverushki\Seofilter\Cpu;

use Bitrix\Main,
	Bitrix\Main\Application,
	Bitrix\Main\Loader,
	Zverushki\Seofilter\configuration,
	Zverushki\Seofilter\Filter\variable,
	Zverushki\Seofilter\Internals,
	Zverushki\Seofilter\Sections\Section;

Loader::includeModule('iblock');

/**
* class Base
*
*
* @package Zverushki\Seofilter
*/
class Base
{
	protected $siteId = false;
	protected $languageId = false;
	protected $lifeTime = 10800;
	protected $cacheTag = "zverushki_seofilter_cpu_";
	protected $listFilter = [];

	protected function __construct ($siteId, $langId = false) {
		$this->siteId = $siteId ? $siteId : SITE_ID;
		$this->languageId = $langId ? $langId : LANGUAGE_ID;
	}

	/**
	 * Убираем все ссылки которые не подходят по маске
	 * @param  string $sUrl
	 * @param  array &$arr
	 * @return array $arr
	 */
	protected function wildcardSelection($sUrl, &$arr){
		foreach ($arr as $key => $item) {
			$urlCpu = str_replace('/', '\/', preg_replace('/\#PROP_(.+?)\#/i', '(.+)', $item['URL_CPU']));
			if(!preg_match('/'.$urlCpu.'/', $sUrl, $cpu))
				unset($arr[$key]);
		}

		return $arr;
	}

	/**
	 * Ищем все варинты для переменных в ссылке
	 * @param  array &$arr
	 * @return array $arr
	 */
	protected function propsSelection(&$arr){
		$listSection = array();
		if($arr)
			foreach ($arr as $key => $item) {
				$listSection[$item['IBLOCK_ID'].'_'.$item['SECTION_ID']] = ['IBLOCK_ID' => $item['IBLOCK_ID'], 'SECTION_ID' => $item['SECTION_ID']];
			}

		if($listSection)
			foreach ($listSection as $key => $item) {
				if(!empty($this->listFilter[$key]))
					continue;

				$variable = new variable();
				$variable->setIblockId($item['IBLOCK_ID']);

				$variable->setSectionId($item['SECTION_ID']);
				$this->listFilter[$key] = $variable->getVariable();
			}
		// mp($this->listFilter);

		unset($listSection);

		if($arr)
			foreach ($arr as $key => &$item) {
				$item['IN_CODE'] = [];
				$item['AR_CODE'] = [];
				$l = $this->listFilter[$item['IBLOCK_ID'].'_'.$item['SECTION_ID']];
				if(!empty($l) && (!empty($l['PROPERTY_ID_LIST']) || !empty($l['SKU_PROPERTY_ID_LIST']))){
					if(preg_match_all('/\#PROP_(.+?)\#/i', $item['URL_CPU'], $chp)){
						if($chp[1])
							foreach ($chp[1] as $code) {
								$item['IN_CODE'][$code]++;
								$item['AR_CODE'][] = $code;
							}
						$propCodeT = array_unique($chp[1]);
						$propCode = array();
						if($propCodeT)
							foreach ($propCodeT as $code)
								$propCode[$code] = $code;

						unset($propCodeT, $chp, $code);

						if(empty($propCode))
							$propCode;
						if($l['ITEMS'])
							foreach ($l['ITEMS'] as $pId => $prop) {
								if(!$propCode[$prop['CODE']] && $pitem['PROPERTY_TYPE'] != "L" && $pitem['PROPERTY_TYPE'] != "E")
									continue;

								$prefix = $prop['VALUES'][key($prop['VALUES'])]['CONTROL_NAME_ALT'];

								$all = true;
								if($item['PARAMS'])
									foreach ($item['PARAMS'] as $code => $v) {
										if(preg_match('/'.$prefix.'/', $code)){
											$all = false;
											break;
										}
									}
								if($all){
									if($prop['VALUES'])
										foreach ($prop['VALUES'] as $vId=> $val) {
											$item['FRES'][$prop['CODE']][$vId] = array(
																					'CONTROL_ID' => $val['CONTROL_ID'],
																					'HTML_VALUE' => $val['HTML_VALUE'],
																					'VALUE' => $val['VALUE'],
																					'TVALUE' => configuration::getTranslit($val['VALUE']),
																				);
										}
								}else{
									if($prop['VALUES'])
										foreach ($prop['VALUES'] as $vId=> $val) {
											if($item['PARAMS'][$val['CONTROL_ID']]){
												$item['FRES'][$prop['CODE']][$vId] = array(
																					'CONTROL_ID' => $val['CONTROL_ID'],
																					'HTML_VALUE' => $val['HTML_VALUE'],
																					'VALUE' => $val['VALUE'],
																					'TVALUE' => configuration::getTranslit($val['VALUE']),
																				);
												unset($item['PARAMS'][$val['CONTROL_ID']]);
											}
										}
								}
								unset($code, $prefix, $val);
							}
					}
				}
				unset($l);
			}
		unset($item);

		return $arr;
	}

	/**
	 * Список всех ссылок с шаблонами
	 * @param  int $siteId [description]
	 * @return array $arParams [description]
	 */
	protected function getFilterMask($siteId, $par = array()){
		$par["URL_CPU"] = ["%#PROP_%", "%#SECTION_%"];
		$arParams = $this->getFilterId($siteId, $par);

		return $arParams;
	}
	/**
	 * Список всех ссылок с шаблонами
	 * @param  int $siteId [description]
	 * @return array $arParams [description]
	 */
	public function getFilterId($siteId, $par = array()){
		$arParams = array();

		$cache_id = md5(serialize(array($this->lifeTime, $siteId, $par)));
		$url = '/'.$this->cacheTag.$siteId.'/'.$cache_id.'/';
		$сache = Main\Data\Cache::createInstance();

		$cacheManager = Application::getInstance()
							->getTaggedCache();

		if ($сache->initCache($this->lifeTime, $cache_id, $url)){
			$arParams = $сache->getVars();
		}elseif ($сache->startDataCache()){
			$cacheManager->startTagCache($url);

			$params = array();
			$filter = array("ACTIVE" => "Y", 'SITE_ID.SITE_ID' => $siteId);
			if(!empty($par))
				$filter = array_merge($filter, $par);

			$dbRes = Internals\SettingsTable::getList(array(
				'order' => array("SORT" => "ASC", "ID" => "DESC"),
				'filter' => $filter,
				'select' => array('ID', 'IBLOCK_ID', 'SECTION_ID', 'TIMESTAMP_X', 'ACTIVE', 'DESCRIPTION', 'URL_CPU', 'URL_FILTER', 'PARAMS')
			));
			while ($params = $dbRes->fetch()){
				Section::replace($params);
				$arParams[$params['ID']] = $params;

				$cacheManager->registerTag($this->cacheTag.$params['ID']);
			}
			$cacheManager->registerTag($this->cacheTag.$siteId);
			$cacheManager->registerTag($this->cacheTag.'all');

			$cacheManager->endTagCache();

			$сache->endDataCache($arParams);
		}

		return $arParams;
	}
}