<?
namespace Zverushki\Seofilter\Facet;

use Bitrix\Main,
	Bitrix\Main\Entity,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\Loader,
	Zverushki\Seofilter\Internals,
	Zverushki\Seofilter\Cpu\Url,
	Zverushki\Seofilter\emoji,
	Zverushki\Seofilter\Filter\result,
	Zverushki\Seofilter\Sections\Section;
	use Bitrix\Main\Diag\Debug;
/**
 *
 */
class Index
{
	private $arSettings = array();
	private $arElements = array();
	private $lastId = 0;
	private $lastCId = 0;
	private $arConfig = array(
		'limit' => 25,
		'timeLimit' => 40
	);

	public function getPullFilter($id = 0){
		$timeLimit = $this->arConfig['timeLimit'] + time();
		$__objSettings = Internals\SettingsTable::getList(array(
			'filter' => array('ACTIVE' => 'Y', '>ID' => $id ? $id : 0),
			'select' => array('*', 'SETTING'),
			'order' => array('ID' => 'ASC', 'SORT' => 'ASC'),
			'limit' => $this->arConfig['limit']
		));
		while($setting = $__objSettings->fetch()){
			$setting['TYPE'] = 'H';
			Section::replace($setting);

			if(preg_match('/\#PROP_(.+?)\#/i', $setting['URL_CPU'])){
				$setting['TYPE'] = 'A';
				$arFIlter = [$setting];
				$arFIlter = Url::getEntity()->getPropsSelection($arFIlter);

				if($arFIlter){
					foreach ($arFIlter as $k => $v) {
						$lurl = Url::getEntity()->shuffle($v);
						// mp($lurl);
						if($lurl){
							foreach ($lurl as $n => $url) {
								$r = $v;

								$r['URL_FILTER'] = $url['url'];
								$r['PARAMS'] = $url['params'];
								foreach ($url['variable'] as $code => $val) {
									$r["TAG_NAME"]  = preg_replace('/\#'.$code.'\#/i', implode(', ', $val), emoji::decode(htmlspecialcharsback($r["TAG_NAME"])));
									$r["TAG_SECTION_NAME"]  = preg_replace('/\#'.$code.'\#/i', implode(', ', $val), emoji::decode(htmlspecialcharsback($r["TAG_SECTION_NAME"])));
									$r["ZVERUSHKI_SEOFILTER_INTERNALS_SETTINGS_SETTING_PAGE_TITLE"]  = preg_replace('/\#'.$code.'\#/i', implode(', ', $val), emoji::decode(htmlspecialcharsback($r["ZVERUSHKI_SEOFILTER_INTERNALS_SETTINGS_SETTING_PAGE_TITLE"])));
								}

								$this->arSettings[$setting['ID']][] = $r;
							}
						}

					}
				}
			}else{
				$this->arSettings[$setting['ID']][] = $setting;
			}

			$this->lastId = $setting['ID'];
			if($timeLimit < time())
				break;
		}

		return !empty($this->arSettings);
	}
	public function getLastId(){
		return $this->lastId;
	}

	public function getLastCId(){
		return $this->lastCId;
	}

	public function getIdList($lastCId = 0){
		Loader::includeModule('catalog');
		Loader::includeModule('iblock');
		$timeLimit = $this->arConfig['timeLimit'] + time();

		$break = false;

		if($this->arSettings)
			foreach ($this->arSettings as $settings) {
				$this->lastCId = 0;
				for ($i = $lastCId; $i < count($settings); $i++) {
					$setting = $settings[$i];
					if(empty($setting['IBLOCK_ID']))
						continue;
					$variable = new result();
					$variable->setIblockId($setting['IBLOCK_ID']);
					$variable->setSectionId($setting['SECTION_ID']);

					$arFilter = $variable->makeFilter($setting['PARAMS']);

					$offerElement = [];
					if($arFilter['OFFER_QUERY'])
						foreach ($arFilter['OFFER_QUERY'] as $propID => $filter){
							$res = \CIBlockElement::GetList(array("ID" => "ASC"), $filter, false, false, array("ID", 'PROPERTY_'.$propID));
							while($arFieds = $res->Fetch())
								$offerElement[$arFieds['PROPERTY_'.$propID.'_VALUE']][] = $arFieds['ID'];
						}

					unset($arFieds, $arFilter['OFFER_QUERY']);
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

						if($offerElement[$arFieds['ID']]){
							foreach($offerElement[$arFieds['ID']] as $offerId){
								$item['OFFER_ID'] = $offerId;
								$this->arElements[$arFieds['ID']][] = $item;
							}
						}else
							$this->arElements[$arFieds['ID']][] = $item;
					}
					unset($arFilter, $variable);

					if($timeLimit < time()){
						$this->lastCId = $i+1;
						$this->lastId = $setting['ID'] - 1;
						$break = true;
						break;
					}
				}
				$lastCId = 0;
				if($break)
					break;
			}

		return !empty($this->arElements);
	}
	public function updateBufferIndex(){
		if($this->arElements)
			foreach ($this->arElements as $id => $arrIndex) {
				$this->updateIndex($id, $arrIndex);
			}
	}
	public function updateIndex($elementId, $arrIndex){
		// $this->deleteIndex($elementId);
		if($arrIndex)
			foreach ($arrIndex as $key => $fields) {
				$this->setIndexTmp($fields);
			}
	}
	public function setIndexTmp($fields){
		$Res = Internals\FindexTmpTable::add($fields);

		return $Res->isSuccess();
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

}
?>
