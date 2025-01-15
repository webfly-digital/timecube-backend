<?
namespace Zverushki\Seofilter\Cpu;

use Bitrix\Main\Loader,
	Zverushki\Seofilter\Internals,
	Zverushki\Seofilter\configuration;

Loader::includeModule('iblock');

/**
* class ParamUrl
*
*
* @package Zverushki\Seofilter
*/
class ParamUrl extends Base
{
	private static $Entity = [];

	public static function getEntity ($siteId, $langId = false) {
		$langId = $langId ? $langId : LANGUAGE_ID;
		$k = strtolower($siteId.$langId);

		return array_key_exists($k, static::$Entity)
			? static::$Entity[$k]
			: (static::$Entity[$k] = new self($siteId, $langId));
	}

	public function searchUrl($urlDir){

		$params = $this->maskLandingSearch($urlDir);

		if(configuration::getOption('not_accelerated_search', '-') == 'Y') {
			if ( !$params )
				$params = $this->quickSearch( $urlDir );

			if ( !$params )
				$params = $this->maskIndexSearch( $urlDir );

			if ( !$params )
				$params = $this->maskSearch( $urlDir );
		}
		if($params['PARAMS'] == 'Y')
			return false;

		return $params;
	}
	/**
	 * Поиск по чистой ссылке
	 * @param  string $urlDir искомая ссылка
	 * @return array|bool $params массиыв параметров по ссылке
	 */
	private function quickSearch($urlDir){
		$dbRes = Internals\SettingsTable::getList(array(
			'order' => array("SORT" => "ASC", "ID" => "DESC"),
			'filter' => array("URL_CPU" => $urlDir, "ACTIVE" => "Y", 'SITE_ID.SITE_ID' => $this->siteId),
			'select' => array('ID', 'IBLOCK_ID', 'SECTION_ID', 'TIMESTAMP_X', 'ACTIVE', 'DESCRIPTION', 'URL_CPU', 'PARAMS')
		));
		if ($params = $dbRes->fetch())
			return $params;

		return;
	}
	/**
	 * Поиск по ссылкам лендингам
	 * @param  string $urlDir искомая ссылка
	 * @return array|bool $params массиыв параметров по ссылке
	 */
	private function maskLandingSearch($urlDir){
		$arParams = $this->getFilterId($this->siteId);

		$sIds = [];
		foreach ($arParams as $item){
			$sIds[] = $item['ID'];
		}
		if(empty($sIds))
			return;

		$dbRes = Internals\LandingTable::getList(array(
			'order' => array("TYPE" => "DESC", "SORT" => "ASC", 'DATE_ELEMENT' => 'DESC', 'SETTING_ID' => 'ASC'),
			'filter' => array("SETTING_ID" => $sIds, "URL_CPU" => $urlDir, 'TYPE' => ["A", "H"], 'ACTIVE' => 'Y', 'ENABLE' => 'N'),
			'select' => array('ID', 'IBLOCK_ID', 'SECTION_ID', 'SETTING_ID', 'TYPE', 'SORT', 'URL_CPU', 'PARAMS'),
			'limit' => 1
		));
		if ($param = $dbRes->fetch()){
			$dbRes = Internals\LandingVarTable::getList(array(
				'order' => array("ID" => "ASC"),
				'filter' => array("LANDING_ID" => $param['ID']),
				'select' => array('TYPE', 'CODE', 'VALUE'),
			));
			while ($p = $dbRes->fetch()){
				if($p['TYPE'] == 'V')
					$param['VAR'][$p['CODE']] = $p['VALUE'];
				elseif($p['TYPE'] == 'P')
					$param['VARIABLE'][$p['CODE']][] = $p['VALUE'];
			}

			$param['ID'] = $param['SETTING_ID'];
			unset($param['SETTING_ID']);
			return $param;
		}

		return;
	}
	/**
	 * Поиск по чистой ссылке
	 * @param  string $urlDir искомая ссылка
	 * @return array|bool $params массиыв параметров по ссылке
	 */
	private function maskIndexSearch($urlDir){
		$arParams = $this->getFilterMask($this->siteId);

		$sIds = [];
		$nParams = [];
		foreach ($arParams as $item){
			$sIds[] = $item['ID'];
			$nParams[$item['ID']] = $item;
		}
		unset($arParams);

		$dbRes = Internals\FindexTable::getList(array(
			'order' => array("TYPE" => "DESC", "SORT" => "ASC", 'DATE_ELEMENT' => 'DESC', 'SETTING_ID' => 'ASC'),
			'filter' => array("SETTING_ID" => $sIds, "URL_CPU" => $urlDir, 'TYPE' => ["A", "H"]),
			'select' => array('ID', 'SETTING_ID', 'TYPE', 'SORT'),
			'limit' => 1
		));
		if ($param = $dbRes->fetch()){
			$arParams = [$nParams[$param['SETTING_ID']]];

			$this->wildcardSelection($urlDir, $arParams);
			$this->propsSelection($arParams);
			$arItem = $arParams[key($arParams)];
			$arFIlter = $lurl = Url::getEntity()->shuffle($arItem);
			foreach ($arFIlter as $item) {
				if($item['url'] == $urlDir){
					$arItem['URL_CPU'] = $item['url'];
					$arItem['PARAMS'] = $item['params'];
					$arItem['VARIABLE'] = $item['variable'];

					return $arItem;
				}
			}
			return $arr;
		}

		return;
	}
	/**
	 * Поиск ссылки по маске
	 * @param  string $urlDir искомая ссылка
	 * @return array|bool $arr массиыв параметров по ссылке
	 */
	private function maskSearch($urlDir){
		$arr = false;
		$arParams = $this->getGenUrl($urlDir, $this->siteId);
		foreach ($arParams as $key => $arrp)
			if($arr = $this->searchMaskInUrl($urlDir, $arrp))
				break;

		$arFIlter = Url::getEntity()->shuffle($arr);

		foreach ($arFIlter as $item) {
			if($item['url'] == $urlDir){
				$arr['URL_CPU'] = $item['url'];
				$arr['PARAMS'] = $item['params'];
				$arr['VARIABLE'] = $item['variable'];
				break;
			}
		}
		return $arr;
	}
	private function getGenUrl($urlDir, $siteId){
		$arParams = $this->getFilterMask($siteId);

		if($arParams){
			$dbRes = Internals\LandingTable::getList(array(
				'order' => array("SETTING_ID" => "DESC"),
				'filter' => array("SETTING_ID" => array_keys($arParams)),
				'select' => array('SETTING_ID'),
				'group' => array('SETTING_ID')
			));
			while ($param = $dbRes->fetch())
				unset($arParams[$param['SETTING_ID']]);

		}

		$this->wildcardSelection($urlDir, $arParams);
		$this->propsSelection($arParams);

		usort($arParams, function($a, $b){
		    return (strlen($a['URL_CPU']) == strlen($b['URL_CPU'])) ? 0 : ((strlen($a['URL_CPU']) > strlen($b['URL_CPU'])) ? -1 : 1);
		});

		return $arParams;
	}



	/**
	 * Поиск подходящей url  в маске
	 * @param  string $sUrl
	 * @param  array &$arr
	 * @return bool $arr
	 */
	private function searchMaskInUrl($sUrl, &$arr){
		preg_match('/(.+?)\#PROP_/', $arr['URL_CPU'], $mthUrl);
		if(empty($mthUrl[1]))
			return false;
		$mthUrltmp = str_replace($mthUrl[1], '', $sUrl);
		if($arr['FRES'])
			foreach ($arr['FRES'] as $code => $item) {
				usort($item, function($a, $b){
				    return (strlen($a['TVALUE']) == strlen($b['TVALUE'])) ? 0 : ((strlen($a['TVALUE']) > strlen($b['TVALUE'])) ? -1 : 1);
				});
				foreach ($item as $vId => $par) {
					if(empty($par['TVALUE']))
						continue;
					$mthUrltmp = preg_replace("/(?!\#PROP_){$par['TVALUE']}(?!.*\#)/i", "#PROP_{$code}#", $mthUrltmp, 3, $count);
					// $mthUrltmp = preg_replace("/{$par['TVALUE']}/i", "#PROP_{$code}#", $mthUrltmp, 3, $count);
					if($count > 0){
						$arr['PARAMS'][$par['CONTROL_ID']] = $par['HTML_VALUE'];
						$arr['VARIABLE']['PROP_'.$code][] = $par['VALUE'];
					}
					/*if($mthUrl[1].$mthUrltmp == $arr['URL_CPU'])
                    	break;*/

				}
			}

		// unset($arr['FRES']);
		if($mthUrl[1].$mthUrltmp == $arr['URL_CPU'])
			return $arr;

		return false;
	}
}