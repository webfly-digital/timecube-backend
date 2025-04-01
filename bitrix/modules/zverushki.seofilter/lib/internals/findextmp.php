<?php
namespace Zverushki\Seofilter\Internals;

use Bitrix\Main,
	Bitrix\Main\Entity,
	Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class SettingsTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * </ul>
 *
 * @package Zverushki\Seofilter\Internals
 **/

class FindexTmpTable extends Entity\DataManager {
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName () {
		return 'zverushki_seofilter_findex_tmp';
	}

	public static function add (array $fields) {

		return parent::add($fields);
	}

	public static function update ($id, array $fields) {
		return parent::update($id, $fields);
	}

	static function clearIndex($settingId){
		global $DB;

		$DB->Query("DELETE FROM `".static::getTableName()."` WHERE `SETTING_ID` = ".$settingId);
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap () {
		return array(
			new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('SEOFILTER_ENTITY_ID_FIELD'),
			)),
			new Entity\IntegerField('ELEMENT_ID', array(
				'required' => true,
				'title' => Loc::getMessage('SEOFILTER_ENTITY_ELEMENT_ID_FIELD'),
			)),
			new Entity\IntegerField('OFFER_ID', array(
				'required' => false,
				'title' => Loc::getMessage('SEOFILTER_ENTITY_OFFER_ID_FIELD'),
			)),
			new Entity\IntegerField('IBLOCK_ID', array(
				'required' => true,
				'title' => Loc::getMessage('SEOFILTER_ENTITY_IBLOCK_ID_FIELD'),
			)),
			new Entity\IntegerField('SECTION_ID', array(
				'required' => false,
				'default_value' => 0,
				'title' => Loc::getMessage('SEOFILTER_ENTITY_SECTION_ID_FIELD'),
			)),
			new Entity\IntegerField('SETTING_ID', array(
				'required' => true,
				'title' => Loc::getMessage('SEOFILTER_ENTITY_SETTING_ID_FIELD'),
			)),
			new Entity\IntegerField('LANDING_ID', array(
				'required' => true,
				'title' => Loc::getMessage('SEOFILTER_ENTITY_LANDING_ID_FIELD'),
			)),
			new Entity\BooleanField('TYPE', array(
				'values' => array('H', 'A'),
				'default_value' => 'H',
				'title' => Loc::getMessage('SEOFILTER_ENTITY_TYPE_FIELD'),
			)),
			new Entity\IntegerField('SORT', array(
				'required' => false,
				'default_value' => 100,
				'title' => Loc::getMessage('SEOFILTER_ENTITY_SORT_FIELD'),
			)),
			new Entity\DatetimeField('TIMESTAMP_X', array(
				'default_value' => new Main\Type\DateTime
			)),
			new Entity\DatetimeField('DATE_ELEMENT', array(
				'default_value' => new Main\Type\DateTime
			)),
			new Entity\TextField('DESCRIPTION', array(
				'required' => false,
				'default_value' => '',
				'title' => Loc::getMessage('SEOFILTER_ENTITY_DESCRIPTION_FIELD'),
			)),
			new Entity\StringField('PAGE_TITLE', array(
				'required' => false,
				'default_value' => '',
				'title' => Loc::getMessage('SEOFILTER_ENTITY_PAGE_TITLE_FIELD'),
			)),
			new Entity\StringField('PAGE_SECTION_TITLE', array(
				'required' => false,
				'default_value' => '',
				'title' => Loc::getMessage('SEOFILTER_ENTITY_PAGE_SECTION_TITLE_FIELD'),
			)),
			new Main\Entity\StringField('URL_CPU', array(
				'required' => true,
				'title' => Loc::getMessage('SEOFILTER_ENTITY_URL_CPU_FIELD'),
			)),
		);
	}

}