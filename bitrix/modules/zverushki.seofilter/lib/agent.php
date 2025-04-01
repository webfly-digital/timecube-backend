<?
namespace Zverushki\Seofilter;

use Zverushki\Seofilter\Facet,
	Zverushki\Seofilter\Sitemap,
	Zverushki\Seofilter\Configure\form,
	Zverushki\Seofilter\Internals,
	Bitrix\Main\Loader;

Loader::includeModule('catalog');
Loader::includeModule('iblock');

/**
* class Agent
*
*
* @package Zverushki\Seofilter
*/
class Agent {
	private static $module_id = 'zverushki.seofilter';
	public static $time = 30;
	private static function setAgnetTimeLimit(){
		$timeAgentLimit = configuration::getOption('time_limit', '-');
		if(intval($timeAgentLimit))
			static::$time = $timeAgentLimit;

		return static::$time ? static::$time : 30;
	}
	final public static function updateIndex ($settingId = 0, $lastCId = 0, $event = 'clear') {
		if($agent = \CAgent::GetList(
			array('ID' => 'DESC'),
			array('MODULE_ID' => self::$module_id, 'NAME' => '\Zverushki\Seofilter\Agent::updateIndexPart(%')
		)->fetch()){
			if(defined('SM_VERSION')){
				if(CheckVersion(SM_VERSION, '16.0.0')){
					$GLOBALS['pPERIOD'] = 60*5;
				}
			}
			return '\Zverushki\Seofilter\Agent::updateIndex('.$settingId.', '.$lastCId.', "'.$event.'");';
		}
		static::setAgnetTimeLimit();

		if($settingId == 0 || $event ==  'clear'){
			$__objSettings = Internals\SettingsTable::getList(array(
				'filter' => array('ACTIVE' => 'Y', '>ID' => $settingId),
				'select' => array('ID'),
				'order' => array('ID' => 'ASC', 'SORT' => 'ASC'),
				'limit' => 1
			));
			if($setting = $__objSettings->fetch())
				$settingId = $setting['ID'];
			else{
				self::addGenerateMap();
				Facet\IndexPart::endAction();
				if(defined('SM_VERSION')){
					if(CheckVersion(SM_VERSION, '16.0.0')){
						$period_agent = configuration::getOption('period_agent', '-');
						$GLOBALS['pPERIOD'] = 3600 * ($period_agent ? intval($period_agent) : 12);
						return '\Zverushki\Seofilter\Agent::updateIndex(0);';
					}
				}
				\CAgent::Add(array(
					'NAME' => '\Zverushki\Seofilter\Agent::updateIndex(0);',
					'MODULE_ID' => self::$module_id,
					'ACTIVE' => 'Y',
					'LAST_EXEC' => date('d.m.Y H:i:s', time()),
					'NEXT_EXEC' => date('d.m.Y H:i:s', strtotime('+12 hour')),
					'AGENT_INTERVAL' => 60,
					'IS_PERIOD' => 'N'
				));
				return false;
			}
		}

		$fIndex = new Facet\IndexPart($settingId);

		static::$time += time();
		while(static::$time > time())
			switch ($event) {
				case 'clear':
					$fIndex->partPreparationIndex();
					$settingId = $fIndex->getSettingId();
					$lastCId = $fIndex->getLastCId();
					$event = "update";
					break;

				case 'update':
					if($fIndex->getPullFilter()){
						$fIndex->getIdList($lastCId);
						$fIndex->updateBufferIndex();
						$settingId = $fIndex->getSettingId();
						$lastCId = $fIndex->getLastCId();
						$event = ($fIndex->getLastCId() > 0 ? 'update' : 'copy');
					}else{
						$settingId = $fIndex->getSettingId();
						$lastCId = 0;
						$event = "copy";
					}
					break;

				case 'copy':
					$fIndex->partPreparationCopy();
					$settingId = $fIndex->getSettingId();
					$lastCId = 0;
					$event = 'check';
					break;
				case 'check':
					$fIndex->checkOld();
					$settingId = $fIndex->getSettingId();
					$lastCId = 0;
					static::$time = time()-1;
					$event = 'clear';
					break;
			}

		return '\Zverushki\Seofilter\Agent::updateIndex('.$settingId.', '.$lastCId.', "'.$event.'");';
	}
	final public static function updateIndexPart ($settingId = 1, $lastCId = 0, $event = 'clear') {
		static::setAgnetTimeLimit();
		$fIndex = new Facet\IndexPart($settingId);
		static::$time += time();

		$end = true;
		while(static::$time > time())
			switch ($event) {
				case 'clear':
					$fIndex->partPreparationIndex();
					$settingId = $fIndex->getSettingId();
					$lastCId = $fIndex->getLastCId();
					$event = "update";
					break;

				case 'update':
					if($fIndex->getPullFilter()){
						$fIndex->getIdList($lastCId);
						$fIndex->updateBufferIndex();
						$settingId = $fIndex->getSettingId();
						$lastCId = $fIndex->getLastCId();
						$event = ($fIndex->getLastCId() > 0 ? 'update' : 'copy');
					}else{
						$settingId = $fIndex->getSettingId();
						$lastCId = 0;
						$event = "copy";
					}
					break;

				case 'copy':
					$fIndex->partPreparationCopy();
					$lastCId = 0;
					$event = "check";
					break;
				case 'check':
					if($fIndex->checkOld())
						self::addGenerateMap();
					Facet\IndexPart::endAction();
					static::$time = time()-1;
					$end = false;
					break;
			}
			if($end)
				return '\Zverushki\Seofilter\Agent::updateIndexPart('.$settingId.', '.$lastCId.', "'.$event.'");';

		return false;
	}

	final public static function generateMap ($siteId) {
		$sitemap = new Sitemap\Xml($siteId);
		$sitemap->getFile();

		$url = $sitemap->save('/sitemap_cpu.xml');
		$sitemap->changeMain($url);

		return false;
	}

	final public static function clearIndex () {
		static::setAgnetTimeLimit();
		static::$time += time();
		$agent = '\Zverushki\Seofilter\Agent::clearIndex();';
		$sId = [];
		$obS = Internals\SettingsTable::getList(['select' => ['ID']]);
		while ($arS = $obS->fetch())
			$sId[] = $arS['ID'];

		$tId = [];
		$obS = Internals\FindexTable::getList(['select' =>['SETTING_ID'], 'filter' => ['!SETTING_ID' => $sId]]);
		while ($arS = $obS->fetch())
			$tId[] = $arS['SETTING_ID'];

		if($tId)
			foreach ( $tId as $id ){
				if(static::$time < time())
					return $agent ;
				Internals\SettingsTable::clearSubIndex( $id );
			}
		if(static::$time < time())
			return $agent ;
		$tId = [];
		$obS = Internals\FTmpTable::getList(['select' =>['SETTING_ID'], 'filter' => ['!SETTING_ID' => $sId]]);
		while ($arS = $obS->fetch())
			$tId[] = $arS['SETTING_ID'];

		if($tId)
			foreach ( $tId as $id ){
				if(static::$time < time())
					return $agent ;
				Internals\SettingsTable::clearSubIndex( $id );
			}
		if(static::$time < time())
			return $agent ;

		$tId = [];
		$obS = Internals\LandingTable::getList(['select' =>['SETTING_ID'], 'filter' => ['!SETTING_ID' => $sId]]);
		while ($arS = $obS->fetch())
			$tId[] = $arS['SETTING_ID'];

		if($tId)
			foreach ( $tId as $id ){
				if(static::$time < time())
					return $agent ;
				Internals\SettingsTable::clearSubIndex( $id );
			}


		return false;
	}
	final public static function clearLandingIndex ($settingId = 0) {
		$sec = static::setAgnetTimeLimit();
		static::$time += time();
		$lastId = 0;
		$obS = Internals\SettingsTable::getList([
			'select' => ['ID'],
			'filter' => ['>ID' => $settingId],
			'order' => ['ID' => 'ASC'],
			'limit' => 5 * ($sec ? $sec : 1)
		]);
		while ($arS = $obS->fetch()) {
			Internals\LandingTable::clearIndex($arS[ 'ID' ], true);
			$lastId = $arS[ 'ID' ];
			if(static::$time < time())
				break;
		}

		if(!$lastId && defined('SM_VERSION') && CheckVersion(SM_VERSION, '16.0.0'))
			$GLOBALS['pPERIOD'] = 3600 * 24;

		return '\Zverushki\Seofilter\Agent::clearLandingIndex('.intval($lastId).');';
	}

	final public static function addGenerateMap ($siteID = array()) {
		$rsSites = \CSite::GetList($by = "sort", $order = "desc", ['ACTIVE' => 'Y']);
		while ($arSite = $rsSites->Fetch())
		    	$arSetting["siteList"][$arSite["LID"]] = array("NAME" => "[" . $arSite["LID"] . "] " .$arSite["NAME"], "SELECT" => "N");
		$form = new form();
    	$form->Init($arSetting);
    	$option = $form->getOptions($siteID);

    	if($option)
	    	foreach ($option as $siteId => $vals) {
	    		if($vals['agent_active'] == 'Y'){
	    			if(!\CAgent::GetList(
	    				array('ID' => 'DESC'),
	    				array('MODULE_ID' => self::$module_id, 'NAME' => '\Zverushki\Seofilter\Agent::generateMap("'.$siteId.'");')
	    			)->fetch())
						\CAgent::Add(array(
							'NAME' => '\Zverushki\Seofilter\Agent::generateMap("'.$siteId.'");',
							'MODULE_ID' => self::$module_id,
							'ACTIVE' => 'Y',
							'NEXT_EXEC' => date('d.m.Y H:i:s', strtotime('+1 minutes')),
							'AGENT_INTERVAL' => 1,
							'IS_PERIOD' => 'N',
							'SORT' => 20
						));
				}
	    	}
	}

	final public static function addGenerateIndex () {
		if($agent = \CAgent::GetList(
			array('ID' => 'DESC'),
			array('MODULE_ID' => self::$module_id, 'NAME' => '\Zverushki\Seofilter\Agent::updateIndex(%')
		)->fetch())
			\CAgent::Update($agent['ID'], array('ACTIVE' => 'Y', 'SORT' => 10, 'RETRY_COUNT' => 0, 'NAME' => '\Zverushki\Seofilter\Agent::updateIndex(0);', 'NEXT_EXEC' => date('d.m.Y H:i:s', strtotime('1 minutes'))));
		else
			\CAgent::Add(array(
				'NAME' => '\Zverushki\Seofilter\Agent::updateIndex();',
				'MODULE_ID' => self::$module_id,
				'ACTIVE' => 'Y',
				'NEXT_EXEC' => date('d.m.Y H:i:s', strtotime('-1 minutes')),
				'AGENT_INTERVAL' => 60,
				'IS_PERIOD' => 'N',
				'SORT' => 10
			));
	}

	final public static function addGenerateIndexPart ($settingId) {
		if($agent = \CAgent::GetList(
			array('ID' => 'DESC'),
			array('MODULE_ID' => self::$module_id, 'NAME' => '\Zverushki\Seofilter\Agent::updateIndexPart('.$settingId.'%')
		)->fetch())
			\CAgent::Update($agent['ID'], array('ACTIVE' => 'Y', 'SORT' => 5, 'NAME' => '\Zverushki\Seofilter\Agent::updateIndexPart('.$settingId.', 0, "clear");', 'NEXT_EXEC' => date('d.m.Y H:i:s', strtotime('1 minutes'))));
		else
			\CAgent::Add(array(
				'NAME' => '\Zverushki\Seofilter\Agent::updateIndexPart('.$settingId.', 0, "clear");',
				'MODULE_ID' => self::$module_id,
				'ACTIVE' => 'Y',
				'NEXT_EXEC' => date('d.m.Y H:i:s', strtotime('-1 minutes')),
				'AGENT_INTERVAL' => 60,
				'IS_PERIOD' => 'N',
				'SORT' => 5
			));
	}


	final public static function setClearLandingIndex ($active) {
		if($agent = \CAgent::GetList(
			array('ID' => 'DESC'),
			array('MODULE_ID' => self::$module_id, 'NAME' => '\Zverushki\Seofilter\Agent::clearLandingIndex(%')
		)->fetch())
			\CAgent::Update($agent['ID'], [
											'ACTIVE' => $active ? 'Y' : 'N',
											'SORT' => 15,
											'RETRY_COUNT' => 0,
											'NAME' => '\Zverushki\Seofilter\Agent::clearLandingIndex(0);',
											'NEXT_EXEC' => date('d.m.Y H:i:s', strtotime('1 minutes'))
										]
			);
		else
			\CAgent::Add(array(
				'NAME' => '\Zverushki\Seofilter\Agent::clearLandingIndex(0);',
				'MODULE_ID' => self::$module_id,
				'ACTIVE' => 'Y',
				'NEXT_EXEC' => date('d.m.Y H:i:s', strtotime('-1 minutes')),
				'AGENT_INTERVAL' => 60,
				'IS_PERIOD' => 'N',
				'SORT' => 15
			));
	}

}?>