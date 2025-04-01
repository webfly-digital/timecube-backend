<?php
/**
 * Created by PhpStorm.
 * User: luk
 * Date: 30.05.2021
 * Time: 16:44
 */

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

class FTmpTable extends Entity\DataManager {
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName () {
		return 'zverushki_seofilter_ftmp';
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
			new Entity\IntegerField('LID', array(
				'required' => true,
				'title' => Loc::getMessage('SEOFILTER_ENTITY_LID_FIELD'),
			)),
			new Entity\IntegerField('SETTING_ID', array(
				'required' => true,
				'title' => Loc::getMessage('SEOFILTER_ENTITY_SETTING_ID_FIELD'),
			)),
			new Entity\TextField('SETTING', array(
				'default_value' => '',
				'serialized' => true,
				'title' => Loc::getMessage('SEOFILTER_ENTITY_DESCRIPTION_FIELD'),
			))
		);
	}

}