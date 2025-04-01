<?
namespace Zverushki\Seofilter\Cpu;

use Bitrix\Main,
	Bitrix\Main\Application,
	Bitrix\Main\Config\Option,
	Bitrix\Main\Loader,
	Zverushki\Seofilter\configuration,
	Zverushki\Seofilter\Internals,
	Zverushki\Seofilter\Sections\Section;
use Bitrix\Main\Diag\Debug;
Loader::includeModule('iblock');

/**
* class Controller
*
*
* @package Zverushki\Seofilter
*/
class Url extends Base
{
	private static $Entity = [];

	public static function getEntity ($siteId = false, $langId = false) {
		$langId = $langId ? $langId : LANGUAGE_ID;
		$k = strtolower($siteId.$langId);

		return array_key_exists($k, static::$Entity)
			? static::$Entity[$k]
			: (static::$Entity[$k] = new self($siteId, $langId));
	}

	public function getQueryString($arr){
		$filterVar = configuration::getOption('filtervar', SITE_ID);
		$queryAr = [];
		foreach($arr as $code => $val)
			if(!preg_match('/' . $filterVar . '/i', strtoupper($code)))
				$queryAr[] = $code.'='.trim($val);

		return (count($queryAr) ? '?'.implode('&', $queryAr) : '');
	}

	/**
	 * Поиск ссылки по параметрам
	 * @param  array $arr Массив параметров нужных для поиска ссылки
	 * @return $url
	 */
	public function clearParams(&$arr){
		unset($arr['ajax'], $arr['set_filter'],  $arr['clear_cache'], $arr['DIRECTORY'], $arr['SECTION_CODE'], $arr['SUB_SECTION_CODE']);
		$filterVar = configuration::getOption('filtervar', SITE_ID);
		foreach ($arr as $code => $v) {
			unset($arr[$code]);
			$code = str_replace($filterVar, 'arrPager', $code);

			if(!preg_match('/PAGEN_([0-9]{1,3})/', $code))
				$arr[$code] = $v;
		}
	}
	public function genUrl($arr){
		$this->clearParams($arr['PARAMS']);
		if(empty($arr['PARAMS']))
			return;
		ksort($arr['PARAMS']);
		$cache_id = md5(serialize(array($this->lifeTime, $this->siteId, $this->languageId, $arr)));
		$urlС = '/'.$this->cacheTag.$this->siteId.'/'.$cache_id.'/';
		$сache = Main\Data\Cache::createInstance();

		$cacheManager = Application::getInstance()
							->getTaggedCache();

		if ($сache->initCache($this->lifeTime, $cache_id, $urlС)){
			$url = $сache->getVars();
		}elseif ($сache->startDataCache()){
			$cacheManager->startTagCache($urlС);

			$url = $this->maskLandingSearch($arr);

			if(!$url)
				$url = $this->quickSearch($arr);

			if(!$url)
				$url = $this->maskSearch($arr);

			$cacheManager->registerTag($this->cacheTag.$this->siteId);
			$cacheManager->registerTag($this->cacheTag.'all');

			$cacheManager->endTagCache();

			$сache->endDataCache($url);
		}

		return $url;
	}
	/**
	 * Поиск по чистой ссылке
	 * @param  string $urlDir искомая ссылка
	 * @return array|bool $params массиыв параметров по ссылке
	 */
	private function quickSearch($par){
		$dbRes = Internals\SettingsTable::getList(array(
			'order' => array("SORT" => "ASC", "ID" => "DESC"),
			'filter' => array('IBLOCK_ID' => $par['IBLOCK_ID'], 'SECTION_ID' => $par['SECTION_ID'], "!URL_CPU" => "%#PROP_%", "PARAMS" => serialize($par['PARAMS']), "ACTIVE" => "Y", 'SITE_ID.SITE_ID' => $this->siteId),
			'select' => array('ID', 'IBLOCK_ID', 'SECTION_ID', 'TIMESTAMP_X', 'ACTIVE', 'DESCRIPTION', 'URL_CPU', 'PARAMS')
		));
		while ($params = $dbRes->fetch()){
			if(!preg_match('/\#PROP_(.+?)\#/i', $params['URL_CPU'], $cpu) && count($params['PARAMS']) == count($par['PARAMS'])){
				$cnt = 0;
				foreach ($par['PARAMS'] as $code => $val) {
					if($params['PARAMS'][$code])
						$cnt++;
				}

				if($cnt == count($par['PARAMS'])){
					Section::replace($params);
					return $params['URL_CPU'];
				}
			}
		}

		return;
	}
	/**
	 * Поиск по ссылкам лендингам
	 * @param  string $urlDir искомая ссылка
	 * @return array|bool $params массиыв параметров по ссылке
	 */
	private function maskLandingSearch($par){
		$arParams = $this->getFilterId($this->siteId);

		$sIds = [];
		foreach ($arParams as $item){
			$sIds[] = $item['ID'];
		}
		if(empty($sIds))
			return;
		$dbRes = Internals\LandingTable::getList(array(
			'order' => ["TYPE" => "DESC", "SORT" => "ASC", 'DATE_ELEMENT' => 'DESC', 'SETTING_ID' => 'ASC'],
			'filter' => [
				'SETTING_ID' => $sIds,
				'IBLOCK_ID' => $par['IBLOCK_ID'],
				'SECTION_ID' => $par['SECTION_ID'],
				'TYPE' => ["A", "H"],
				'ACTIVE' => 'Y',
				'ENABLE' => 'N',
				'PARAMS_HASH' => md5(serialize($par["PARAMS"]))
			],
			'select' => ['ID', 'IBLOCK_ID', 'SECTION_ID', 'SETTING_ID', 'TYPE', 'SORT', 'URL_CPU', 'PARAMS'],
			'limit' => 1
		));
		if ($params = $dbRes->fetch()){
			Section::replace($params);
			return $params['URL_CPU'];
		}
		return;
	}
	/**
	 * Поиск ссылки по маске
	 * @param  string $urlDir искомая ссылка
	 * @return array|bool $arr массиыв параметров по ссылке
	 */
	private function maskSearch($par){
		$arr = false;

		$arParams = $this->getGenUrl($this->siteId, $par);
		if($arParams)
			foreach ($arParams as $key => $arrp)
				if($arr = $this->searchMaskInUrl($par, $arrp))
					break;

		return $arr;
	}
	private function getGenUrl($siteId, $par){
		$arParams = $this->getFilterMask($siteId, []);
		foreach($arParams as $k => $item){
			if($item['IBLOCK_ID'] != $par['IBLOCK_ID'] || $par['SECTION_ID'] != $item['SECTION_ID'])
				unset($arParams[$k]);
		}
		if(empty($arParams))
			return;

		$this->propsSelection($arParams);

		usort($arParams, function($a, $b){
		    return (strlen($a['URL_CPU']) == strlen($b['URL_CPU']) ? 0 : (strlen($a['URL_CPU']) > strlen($b['URL_CPU']) ? -1 : 1));
		});

		return $arParams;
	}
	public function getPropsSelection($par){
		return $this->propsSelection($par);
	}

	/**
	 * Поиск подходящей url  в маске
	 * @param  string $sUrl
	 * @param  array &$arr
	 * @return bool $arr
	 */
	private function searchMaskInUrl($par, &$arr){
		foreach ($par['PARAMS'] as $code => $v) {
			if(!empty($arr['PARAMS'][$code]))
				unset($par['PARAMS'][$code], $arr['PARAMS'][$code]);
		}
		if(!empty($arr['PARAMS']))
			return false;


		foreach ($arr['AR_CODE'] as $n => $code) {
			foreach ($arr['FRES'][$code] as $vId => $p) {
				if($par['PARAMS'][$p['CONTROL_ID']] == $p['HTML_VALUE']){
					$arr['URL_CPU'] = preg_replace("/\#PROP_{$code}\#/i", $p['TVALUE'], $arr['URL_CPU'], 1);
					unset($par['PARAMS'][$p['CONTROL_ID']]);
					$arr['IN_CODE'][$code]--;

					if($arr['IN_CODE'][$code] < 1)
						break;
				}
			}
		}
		unset($arr['FRES']);

		if(!preg_match('/\#PROP_(.+?)\#/i', $arr['URL_CPU'], $cpu) && empty($par['PARAMS']))
			return $arr['URL_CPU'];

		return false;
	}

	public function shuffle($par){

		$lurl = [['url' => $par['URL_CPU'], 'params' => $par['PARAMS'] ? $par['PARAMS'] : []]];
		if($par['AR_CODE'])
			foreach ($par['AR_CODE'] as $n => $code) {
				$lurl = $this->getListUrl($lurl, $code, $par['FRES'][$code]);
			}

		foreach ($lurl as &$aurl) {
			$aurl['url'] = str_replace('#', '', $aurl['url']);
		}

		return $lurl;
	}
	private function getListUrl($lurl, $code, $par){
		$arrList = array();
		if(!empty($lurl)){
			foreach ($lurl as $url) {
				if(!empty($par))
					foreach ($par as $p) {
						$cpu = preg_replace("/\#PROP_{$code}\#/i", "#".$p['TVALUE']."#", $url['url'], 1);
						if(preg_match_all("/\#{$p['TVALUE']}\#/i", $cpu, $cnt) && count($cnt[0]) < 2){
							$tmpP = [];
							$tmpV = [];
							$tmpP = $url['params'];
							$tmpV = $url['variable'];
							$tmpP[$p['CONTROL_ID']] = $p['HTML_VALUE'];
							$tmpV['PROP_'.$code][] = $p['VALUE'];
							$arrList[] = ['url' => $cpu, 'params' => $tmpP, 'variable' => $tmpV];
						}
					}

			}
		}
		unset($lurl);
		return $arrList;
	}
}