<?php
/**
 * Created by PhpStorm.
 * User: luk
 * Date: 09.05.2021
 * Time: 19:41
 */

namespace Zverushki\Seofilter\Facet;
use Bitrix\Main,
	Zverushki\Seofilter\Internals;

class Landing
{
	private $settingId = false;

	public function __construct($settingId){
		$this->settingId = $settingId;
	}

	public function setLanding($ar, $a = false){
		if(!$ar['SETTING_ID'] || !$ar['IBLOCK_ID'] || !$ar['URL_CPU'])
			return;

		if($a){
			$ar['MARK'] = 'Y';
//			$ar['ACTIVE'] = intval(['COUNT']) ? 'Y' : 'N';
			$ar['ACTIVE'] = 'Y';
			$ar['TIMESTAMP_X'] = new Main\Type\DateTime;
			$ar['PARAMS_HASH'] = md5(serialize($ar["PARAMS"]));
			if($ar['PAGE_TITLE'] && !$ar['PAGE_SECTION_TITLE'])
				$ar['PAGE_SECTION_TITLE'] = $ar['PAGE_TITLE'];
			$ar['PARAMS_HASH'] = md5(serialize($ar["PARAMS"]));
		}
		$var = [];
		$props = [];
		if($ar['VAR'])
			$var = $ar['VAR'];
		if($ar['PROPS'])
			$props = $ar['PROPS'];
		unset($ar['VAR'], $ar['PROPS']);

		$r = Internals\LandingTable::getList([
			'filter' => [
				'SETTING_ID' => $ar['SETTING_ID'],
				'IBLOCK_ID' => $ar['IBLOCK_ID'],
				'URL_CPU' => $ar['URL_CPU']
			],
			'select' => ['ID', 'SORT', 'USORT'],
			'limit' => 1
		])->fetch();
		if($r){
			$res = Internals\LandingTable::update($r['ID'], $ar);
		}else
			$res = Internals\LandingTable::add($ar);
		if($res->isSuccess()){
			$this->deleteVar($res->getId());
			if($var){
				foreach($var as $code => $v){
					$f = [
						'LANDING_ID' => $res->getId(),
						'TYPE' => 'V',
						'CODE' => $code,
						'VALUE' => $v,
					];
					$rs = Internals\LandingVarTable::add($f);
				}
			}
			if($props){
				foreach($props as $code => $val){
					foreach($val as $v){
						$f = [
							'LANDING_ID' => $res->getId(),
							'TYPE' => 'P',
							'CODE' => $code,
							'VALUE' => $v,
						];
						$rs = Internals\LandingVarTable::add($f);
					}
				}
			}
		}
		unset($ar, $var, $f);

		return $res;
	}

	public function landingSetFlag($flag = 'D', $subquery = ''){
		$date = '';
		if($flag == 'D'){
			$dt = new Main\Type\DateTime;
			$date = "ACTIVE = 'N', DATE_DEACTIVE = '".$dt->format('Y-m-d H:i:s')."', ";
		}
		$sqlQuery = "UPDATE `".Internals\LandingTable::getTableName()."` SET ".$date."MARK = '".$flag."' WHERE `SETTING_ID` = "
			.$this->settingId.($subquery ? ' and '.$subquery : '');

		$Connection = Main\Application::getConnection();
		return $Connection->Query($sqlQuery);
	}

	public function deleteVar($landId, $type = false){
		$sqlQuery = "DELETE FROM `".Internals\LandingVarTable::getTableName()."` WHERE `LANDING_ID` = ".$landId.( $type ? " and `TYPE` = '".
				$type."'" : "" );
		$Connection = Main\Application::getConnection();
		return $Connection->Query($sqlQuery);
	}
}