<?php
/**
 * Created by PhpStorm.
 * User: luk
 * Date: 05.05.2021
 * Time: 19:41
 */

namespace Zverushki\Seofilter\Internals;

use Bitrix\Main,
	Bitrix\Main\Entity,
	Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class LandingVarTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * </ul>
 *
 * @package Zverushki\Seofilter\Internals
 **/

class LandingVarTable extends Entity\DataManager {
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName () {
		return 'zverushki_seofilter_landing_var';
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
			new Entity\IntegerField('LANDING_ID', array(
				'required' => true,
				'title' => Loc::getMessage('SEOFILTER_ENTITY_LANDING_ID_FIELD'),
			)),
			new Entity\BooleanField('TYPE', array(
				'required' => true,
				'values' => array('V', 'P'),
				'default_value' => 'V',
				'title' => Loc::getMessage('SEOFILTER_ENTITY_TYPE_FIELD'),
			)),
			new Entity\StringField('CODE', array(
				'title' => Loc::getMessage('SEOFILTER_ENTITY_CODE_FIELD'),
			)),
			new Entity\StringField('VALUE', array(
				'required' => false,
				'default_value' => '',
				'title' => Loc::getMessage('SEOFILTER_ENTITY_VALUE_FIELD'),
			))
		);
	}
}
?>