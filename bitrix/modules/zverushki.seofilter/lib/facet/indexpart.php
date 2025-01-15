<?
namespace Zverushki\Seofilter\Facet;

use Bitrix\Main,
	Bitrix\Main\Loader,
	Zverushki\Seofilter\configuration,
	Zverushki\Seofilter\Internals,
	Zverushki\Seofilter\Cpu\Url,
	Zverushki\Seofilter\Agent,
	Zverushki\Seofilter\emoji,
	Zverushki\Seofilter\Filter\result,
	Zverushki\Seofilter\Sections\Section;

/**
 *
 */
class IndexPart
{
	private $arSettings = array();
	private $arElements = array();
	private $arSections = array();
	private $settingId = false;
	private $lastId = 0;
	private $lastCId = 0;
	private $Landing = null;
	private $arConfig = array(
		'limit' => 1,
		'limitStep' => 1000,
		'timeLimit' => 40,
		'notActive' => "N"
	);
	public function __construct($settingId){
		$this->settingId = $settingId;
		$this->Landing = new Landing($this->settingId);
		$limitStep = configuration::getOption('limit_step', '-');
		$this->arConfig['notActive'] = configuration::getOption('not_active', '-');
		if(empty($this->arConfig['notActive']))
			$this->arConfig['notActive'] = "N";
		if(intval($limitStep) > 0)
			$this->arConfig['limitStep'] = $limitStep;


	}
	public function getPullFilter(){
		if(!$this->settingId)
			return;

		$arF = Internals\FTmpTable::getList(array(
			'filter' => array('SETTING_ID' => $this->settingId),
			'select' => array('ID', 'LID', 'SETTING'),
			'order' => array('LID' => 'ASC'),
			'limit' => $this->arConfig['limitStep']
		));
		while($aField = $arF->fetch())
			$this->arSettings[$aField['LID']] = $aField['SETTING'];


		if(empty($this->arSettings)){
			$__objSettings = Internals\SettingsTable::getList(
				[
					'filter' => [ 'ACTIVE' => 'Y', '=ID' => $this->settingId ],
					'select' => [ 'ID', 'SORT', 'IBLOCK_ID', 'SECTION_ID', 'GROUP_ID', 'URL_FILTER', 'URL_CPU', 'TAG_NAME', 'TAG_SECTION_NAME', 'PARAMS', 'SETTING.PAGE_TITLE' ],
					'order' => [ 'ID' => 'ASC', 'SORT' => 'ASC' ],
					'limit' => $this->arConfig['limit']
				]
			);
			while( $setting = $__objSettings->fetch() ){
				$setting['TYPE'] = 'H';

				Section::replace( $setting );
				if( preg_match( '/\#PROP_(.+?)\#/i', $setting['URL_CPU'] ) ){
					$setting['TYPE'] = 'A';
					$arFIlter = [ $setting ];
					$arFIlter = Url::getEntity()
					               ->getPropsSelection( $arFIlter );

					if( $arFIlter ){
						foreach( $arFIlter as $k => $v ){
							$lurl = Url::getEntity()
							           ->shuffle( $v );
							if( $lurl ){
								foreach( $lurl as $n => $url ){
									$r = $v;

									$r['URL_FILTER'] = $url['url'];
									$r['PARAMS'] = $url['params'];
									$r['VARIABLE'] = $url['variable'];
									foreach( $url['variable'] as $code => $val ){
										$r["TAG_NAME"] = preg_replace( '/\#' . $code . '\#/i', implode( ', ', $val ), emoji::decode( htmlspecialcharsback( $r["TAG_NAME"] ) ) );
										$r["TAG_SECTION_NAME"] = preg_replace( '/\#' . $code . '\#/i', implode( ', ', $val ), emoji::decode( htmlspecialcharsback( $r["TAG_SECTION_NAME"] ) ) );
										$r["ZVERUSHKI_SEOFILTER_INTERNALS_SETTINGS_SETTING_PAGE_TITLE"] = preg_replace( '/\#' . $code . '\#/i', implode( ', ', $val ), emoji::decode( htmlspecialcharsback( $r["ZVERUSHKI_SEOFILTER_INTERNALS_SETTINGS_SETTING_PAGE_TITLE"] ) ) );
									}
									unset($r['FRES']);
									$this->arSettings[] = $r;
								}
							}

						}
					}
				}else{
					$this->arSettings[] = $setting;
				}

				foreach($this->arSettings as $lid => $item){
					$f = [
						'LID' => $lid,
						'SETTING_ID' => $this->settingId,
						'SETTING' => $item
					];

					Internals\FTmpTable::add($f);
				}

				$this->lastId = $setting['ID'];
			}
		}

		return !empty( $this->arSettings);
	}

	public function getIdList($lastCId = 0){
		Loader::includeModule('catalog');
		Loader::includeModule('iblock');

		if($this->arSettings) {
			$settings = $this->arSettings;
			$this->lastCId = 0;
			for ($i = $lastCId; $i < $lastCId+count($settings); $i++) {
				$setting = $settings[$i];
				if(empty($setting['IBLOCK_ID']))
					continue;
				$variable = new result();
				$variable->setIblockId($setting['IBLOCK_ID']);
				$variable->setSectionId($setting['SECTION_ID']);
				$variable->setSettingId($setting['ID']);

				$arFilter = $variable->makeFilter($setting['PARAMS']);

				$offerElement = [];
				if($arFilter['OFFER_QUERY'])
					foreach ($arFilter['OFFER_QUERY'] as $propID => $filter){
						$res = \CIBlockElement::GetList(array("ID" => "ASC"), $filter, false, false, array("ID", 'PROPERTY_'.$propID));
						while($arFieds = $res->Fetch())
							$offerElement[$arFieds['PROPERTY_'.$propID.'_VALUE']][] = $arFieds['ID'];
					}

				if($arFilter['OFFERS']){
					$arFilter['OFFERS']['IBLOCK_ID'] = $variable->SKU_IBLOCK_ID;
					$res = \CIBlockElement::GetList(array("ID" => "ASC"), $arFilter['OFFERS'], false, false, array("ID", "PROPERTY_".$variable->SKU_PROPERTY_ID));
					while($arFieds = $res->Fetch()){
						$arFilter['=ID'][] = $arFieds['PROPERTY_'.$variable->SKU_PROPERTY_ID.'_VALUE'];
						$offerElement[$arFieds['PROPERTY_'.$variable->SKU_PROPERTY_ID.'_VALUE']][] = $arFieds['ID'];
					}
				}
				unset($arFieds, $arFilter['OFFER_QUERY'], $arFilter['OFFERS']);

				$newElemDate = 0;
				$cntElem = 0;
				$arItem = [];
				$res = \CIBlockElement::GetList(array("ID" => "ASC"), $arFilter, false, false, array("ID", "IBLOCK_ID", "TIMESTAMP_X"));
				while($arFieds = $res->Fetch()){
					$item = array(
						'ELEMENT_ID' => $arFieds['ID'],
						'OFFER_ID' => '',
						'IBLOCK_ID' => $arFieds["IBLOCK_ID"],
						'SECTION_ID' => $setting["SECTION_ID"],
						'SETTING_ID' => $setting["ID"],
						'TYPE' => $setting["TYPE"],
						'SORT' => $setting["SORT"],
						'DATE_ELEMENT' => new Main\Type\DateTime($arFieds["TIMESTAMP_X"]),
						'DESCRIPTION' => '',//$setting["DESCRIPTION"],
						'URL_CPU' => $setting["URL_FILTER"] != 'NULL' ? $setting["URL_FILTER"] : $setting["URL_CPU"],
						'PAGE_TITLE' => trim($setting["TAG_NAME"] ? $setting["TAG_NAME"] : $setting["ZVERUSHKI_SEOFILTER_INTERNALS_SETTINGS_SETTING_PAGE_TITLE"]),
						'PAGE_SECTION_TITLE' => $setting["TAG_SECTION_NAME"] ? trim($setting["TAG_SECTION_NAME"]) : ''
					);
					if($newElemDate < $item['DATE_ELEMENT']->getTimestamp())
						$newElemDate = $item['DATE_ELEMENT']->getTimestamp();

					if($offerElement[$arFieds['ID']]){
						foreach($offerElement[$arFieds['ID']] as $offerId){
							$item['OFFER_ID'] = $offerId;
							unset($item['ID']);
							if($id = $this->findExists($item))
								$item['ID'] = $id;

							$this->arElements[$arFieds['ID']][] = $item;
						}
					}else{
						if($id = $this->findExists($item))
							$item['ID'] = $id;

						$arItem[$arFieds['ID']] = $item;
					}
					$cntElem++;
				}
				if($cntElem && $setting){
					$section = [
						'IBLOCK_ID' => $setting["IBLOCK_ID"],
						'SECTION_ID' => $setting["SECTION_ID"],
						'GROUP_ID' => $setting["GROUP_ID"],
						'SETTING_ID' => $setting["ID"],
						'TYPE' => $setting["TYPE"],
						'SORT' => $setting["SORT"] ? $setting["SORT"] : 100,
						'COUNT' => $cntElem,
						'PAGE_TITLE' => trim($setting["TAG_NAME"] ? $setting["TAG_NAME"] : $setting["ZVERUSHKI_SEOFILTER_INTERNALS_SETTINGS_SETTING_PAGE_TITLE"]),
						'PAGE_SECTION_TITLE' => $setting["TAG_SECTION_NAME"] ? trim($setting["TAG_SECTION_NAME"]) : '',
						'URL_CPU' => $setting["URL_FILTER"] != 'NULL' ? $setting["URL_FILTER"] : $setting["URL_CPU"],
						'PARAMS' => $setting["PARAMS"],
						'PROPS' => $setting["VARIABLE"]
					];
					if($newElemDate)
						$section['DATE_ELEMENT'] = Main\Type\DateTime::createFromTimestamp($newElemDate);
					$gFilter = $variable->getLastGFilter();
					if($gFilter){
						$section['VAR'] = [];
						foreach($gFilter as $f){
							if($f['PRICE']){
								$section['VAR']['MIN_'.$f['CODE']] = (
									(
										$f['VALUES']['MIN']['HTML_VALUE'] && floatval($f['VALUES']['MIN']['HTML_VALUE']) > floatval($f['VALUES']['MIN']['FILTERED_VALUE'])
									) ? $f['VALUES']['MIN']['HTML_VALUE'] : ($f['VALUES']['MIN']['FILTERED_VALUE'] ? $f['VALUES']['MIN']['FILTERED_VALUE'] : 0)
								);
								$section['VAR']['MAX_'.$f['CODE']] = (
								(
									$f['VALUES']['MAX']['HTML_VALUE'] && floatval($f['VALUES']['MAX']['HTML_VALUE']) < floatval($f['VALUES']['MAX']['FILTERED_VALUE'])
								) ? $f['VALUES']['MAX']['HTML_VALUE'] : ($f['VALUES']['MAX']['FILTERED_VALUE'] ? $f['VALUES']['MAX']['FILTERED_VALUE'] : 0)
								);
							}

						}
					}

					$r = $this->Landing->setLanding($section, true);
					if($r->isSuccess()){
						foreach($arItem as $elId => $item){
							$item['LANDING_ID'] = $r->getId();
							$this->arElements[$elId][] = $item;
						}
					}
				}
				unset($arFilter, $variable, $this->arSettings[$i]);

				if(Agent::$time < time()){
					$this->lastCId = $i+1;
					$this->lastId = $setting['ID'] - 1;
					break;
				}
			}

			$lastCId = 0;
		}

		return !empty($this->arElements);
	}

	public static function endAction(){
		$cacheManager = Main\Application::getInstance()
		                                ->getTaggedCache();
		$cacheManager->ClearByTag("zverushki_seofilter_cpu_all");
	}

	private function setTmp(){
		global $DB;
		if($this->arSettings){
			$DB->Query("DELETE FROM `".Internals\FTmpTable::getTableName()."` WHERE `SETTING_ID` = ".$this->settingId.' and `LID` < '
				.$this->lastCId);
		}else{
			$DB->Query("DELETE FROM `".Internals\FTmpTable::getTableName()."` WHERE `SETTING_ID` = ".$this->settingId);
		}
	}

	public function setIndex($fields){
		$Res = Internals\FindexTable::add($fields);

		return $Res->isSuccess();
	}

	public function getIndexElement($elementId, $limit = 30){
		if(empty($elementId))
			return false;
		$__objSettings = Internals\FindexTable::getList(array(
			'filter' => array('ELEMENT_ID' => $elementId),
			'select' => array('*'),
			'order' => array('ID' => 'ASC'),
			'limit' => $limit
		));
		while($findex = $__objSettings->fetch()){
			$arFindex[$findex['ID']] = $findex;
		}

		return $arFindex;
	}
	public function clearIndexTmp(){
		global $DB;
		$sqlQuery = "TRUNCATE TABLE `".Internals\FindexTmpTable::getTableName()."`";

		return $DB->Query($sqlQuery, false, $err_mess.__LINE__);
	}
	public function clearIndex(){
		global $DB;
		$sqlQuery = "TRUNCATE TABLE `".Internals\FindexTable::getTableName()."`";

		return $DB->Query($sqlQuery, false, $err_mess.__LINE__);
	}
	public function copyIndex(){
		$this->clearIndex();
		global $DB;

		$sqlQuery = "INSERT INTO `".Internals\FindexTable::getTableName()."` SELECT * FROM `".Internals\FindexTmpTable::getTableName()."`";

		return $DB->Query($sqlQuery, false, $err_mess.__LINE__);
	}
	public function deleteIndex($elementId){
		global $DB;
		$sqlQuery = "DELETE FROM `".Internals\FindexTable::getTableName()."` WHERE `ELEMENT_ID` = ".$elementId;

		return $DB->Query($sqlQuery, false, $err_mess.__LINE__);
	}

	/*-------------------------------------------*/

	public function getSettingId () {
		return $this->settingId;
	}

	public function getLastId () {
		return $this->lastId;
	}

	public function getLastCId () {
		return $this->lastCId;
	}

	public function checkOld(){
		if(!$this->settingId)
			return;

		return $this->Landing->landingSetFlag('D', "ACTIVE != 'N' and MARK = 'R'");
	}

	public function partPreparationIndex () {
		if(!$this->settingId)
			return;
		$this->setTmp();
		if($this->partClearIndex('tmp')){
			$this->partCopyIndex('prodToTmp');
			$this->partSetFlag('D');
			$this->Landing->landingSetFlag('R');
		}
	}

	public function partPreparationCopy () {
		if(!$this->settingId)
			return;

		if($this->partDeleteFlag('D') && $this->partClearIndex('prod') && $this->partCopyIndex('tmpToProd')){
			return $this->partClearIndex('tmp');
		}
	}

	public function partClearIndex ($type = 'tmp') {
		if(!$this->settingId)
			return;
		global $DB;
		if($type == 'tmp')
			$sqlQuery = "DELETE FROM `".Internals\FindexTmpTable::getTableName()."` WHERE `SETTING_ID` = ".$this->settingId;
		elseif($type == 'prod')
			$sqlQuery = "DELETE FROM `".Internals\FindexTable::getTableName()."` WHERE `SETTING_ID` = ".$this->settingId;

		if($sqlQuery)
			return $DB->Query($sqlQuery, false, $err_mess.__LINE__);
	}

	private function partDeleteFlag ($flag = 'D') {
		if(!$this->settingId)
			return;
		global $DB;
		$sqlQuery = "DELETE FROM `".Internals\FindexTmpTable::getTableName()."` WHERE `TYPE` = '".$flag."' AND `SETTING_ID` = ".$this->settingId;

		return $DB->Query($sqlQuery, false, $err_mess.__LINE__);
	}

	private function partSetFlag($flag = 'D'){
		if(!$this->settingId)
			return;
		global $DB;
		$sqlQuery = "UPDATE `".Internals\FindexTmpTable::getTableName()."` SET TYPE = '".$flag."' WHERE `SETTING_ID` = ".$this->settingId;

		return $DB->Query($sqlQuery, false, $err_mess.__LINE__);
	}

	public function partCopyIndex ($in = 'tmpToProd'){
		if(!$this->settingId)
			return;
		global $DB;
		if($in == 'tmpToProd')
			$sqlQuery = "INSERT INTO `".Internals\FindexTable::getTableName()."` SELECT null, `ELEMENT_ID`, `OFFER_ID`, `IBLOCK_ID`, `SECTION_ID`, `SETTING_ID`, `LANDING_ID`,`TYPE`, `SORT`, `TIMESTAMP_X`, `DATE_ELEMENT`, `DESCRIPTION`, `PAGE_TITLE`,	`PAGE_SECTION_TITLE`, `URL_CPU` FROM `".Internals\FindexTmpTable::getTableName()."` WHERE `SETTING_ID` = ".$this->settingId;
		elseif($in == 'prodToTmp')
			$sqlQuery = "INSERT INTO `".Internals\FindexTmpTable::getTableName()."` SELECT null, `ELEMENT_ID`, `OFFER_ID`, `IBLOCK_ID`, `SECTION_ID`, `SETTING_ID`, `LANDING_ID`, `TYPE`, `SORT`, `TIMESTAMP_X`, `DATE_ELEMENT`, `DESCRIPTION`, `PAGE_TITLE`,	`PAGE_SECTION_TITLE`, `URL_CPU` FROM `".Internals\FindexTable::getTableName()."` WHERE `SETTING_ID` = ".$this->settingId;

		return $DB->Query($sqlQuery, false, $err_mess.__LINE__);
	}

	private function findExists ($field) {
		if(empty($field))
			return false;

		$filter = array(
			'ELEMENT_ID' => $field['ELEMENT_ID'],
			'OFFER_ID' => $field['OFFER_ID'],
			'IBLOCK_ID' => $field["IBLOCK_ID"],
			'SECTION_ID' => $field["SECTION_ID"],
			'SETTING_ID' => $field["SETTING_ID"],
			'URL_CPU' => $field["URL_CPU"]
		);

		$__objSettings = Internals\FindexTmpTable::getList(array(
			'filter' => $filter,
			'select' => array('*'),
			'order' => array('ID' => 'ASC'),
			'limit' => 1
		));
		if($findex = $__objSettings->fetch()){
			return $findex['ID'];

			return false;
		}
	}

	public function updateBufferIndex(){
		if($this->arElements)
			foreach ($this->arElements as $id => $arrIndex) {
				$this->updateIndex($id, $arrIndex);
			}
		$this->setTmp();
	}

	public function updateIndex($elementId, $arrIndex){
		// $this->deleteIndex($elementId);
		if($arrIndex)
			foreach ($arrIndex as $key => $fields) {
				$this->setIndexTmp($fields);
			}
	}

	public function setIndexTmp($fields){
		if(intval($fields['ID']) > 0){
			$ID = $fields['ID'];
			unset($fields['ID']);

			$Res = Internals\FindexTmpTable::update($ID, $fields);
		}else
			$Res = Internals\FindexTmpTable::add($fields);

		return $Res->isSuccess();
	}

}
?>
