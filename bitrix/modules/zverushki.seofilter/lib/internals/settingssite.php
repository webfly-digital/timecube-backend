<?php
namespace Zverushki\Seofilter\Internals;

use Bitrix\Main,
	Bitrix\Main\Entity,
	Bitrix\Main\Localization\Loc,
	Zverushki\Seofilter\Internals\Validator as HlValidator;

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

class SettingsSiteTable extends Entity\DataManager {
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName () {
		return 'zverushki_seofilter_settings_site';
	}

	public static function add (array $fields) {
		return parent::add($fields);
	}

	public static function update ($id, array $fields) {

		return parent::update($id, $fields);
	}

	public static function delete ($id) {
		return parent::delete($id);
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
			new Entity\IntegerField('SETTING_ID', array(
				'title' => Loc::getMessage('SEOFILTER_ENTITY_SETTING_ID_FIELD'),
			)),
			new Entity\StringField('SITE_ID', array(
				'title' => Loc::getMessage('SEOFILTER_ENTITY_SITE_ID_FIELD'),
			)),
		);
	}

}