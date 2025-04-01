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

class SettingsTable extends Entity\DataManager {
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName () {
		return 'zverushki_seofilter_settings';
	}

	public static function add (array $fields) {
		if (!isset($fields['ACTIVE']))
			$fields['ACTIVE'] = 'N';
		$fields['GROUP_ID'] = 0;

		if(!$fields['GROUP_ID'])
			$fields['GROUP_ID'] = 0;
		return parent::add($fields);
	}

	public static function update ($id, array $fields) {
		if(!$fields['GROUP_ID'])
			$fields['GROUP_ID'] = 0;

		return parent::update($id, $fields);
	}

	public static function delete ($id) {
		static::clearIndex($id);

		return parent::delete($id);
	}
	public static function clearIndex($id){
		$seofilterSetting =
			self::getList([
				'filter' => array('ID' => $id),
				'select' => array('SETTING.ID')
			])
			    ->fetch();
		if($seofilterSetting["ZVERUSHKI_SEOFILTER_INTERNALS_SETTINGS_SETTING_ID"] > 0)
			SeotagTable::delete($seofilterSetting["ZVERUSHKI_SEOFILTER_INTERNALS_SETTINGS_SETTING_ID"]);

		$seofilterSettings =
			self::getList([
				'filter' => array('ID' => $id),
				'select' => array('SITE_ID.ID')
			]);
		while ($seofilterSetting = $seofilterSettings->fetch())
			if ( $seofilterSetting[ "ZVERUSHKI_SEOFILTER_INTERNALS_SETTINGS_SITE_ID" ] > 0 )
				SettingsSiteTable::delete($seofilterSetting[ "ZVERUSHKI_SEOFILTER_INTERNALS_SETTINGS_SITE_ID" ]);

		static::clearSubIndex($id);
	}
	static function clearSubIndex($id){
		FTmpTable::clearIndex($id);
		FindexTable::clearIndex($id);
		FindexTmpTable::clearIndex($id);
		LandingTable::clearIndex($id);
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
			new Entity\ReferenceField(
				'SETTING',
				'Zverushki\Seofilter\Internals\SeotagTable',
				array('=this.ID' => 'ref.SETTING_ID')
			),
			new Entity\ReferenceField(
				'SITE_ID',
				'Zverushki\Seofilter\Internals\SettingsSiteTable',
				array('=this.ID' => 'ref.SETTING_ID')
			),
			new Entity\IntegerField('SORT', array(
				'required' => true,
				'default_value' => 500,
				'title' => Loc::getMessage('SEOFILTER_ENTITY_SORT_FIELD'),
			)),
			new Entity\IntegerField('IBLOCK_ID', array(
				'required' => true,
				'title' => Loc::getMessage('SEOFILTER_ENTITY_IBLOCK_ID_FIELD'),
			)),
			new Entity\IntegerField('SECTION_ID', array(
				'default_value' => 0,
				'title' => Loc::getMessage('SEOFILTER_ENTITY_SECTION_ID_FIELD'),
			)),
			new Entity\IntegerField('GROUP_ID', array(
				'required'      => false,
				'default_value' => 0,
				'title' => Loc::getMessage('SEOFILTER_ENTITY_GROUP_ID_FIELD'),
			)),
			new Entity\ReferenceField(
				'GROUP_NAME',
				'Zverushki\Seofilter\Internals\GroupTable',
				array('=this.GROUP_ID' => 'ref.ID')
			),
			new Entity\DatetimeField('TIMESTAMP_X', array(
				'default_value' => new Main\Type\DateTime
			)),
			new Entity\BooleanField('ACTIVE', array(
				'values' => array('N', 'Y'),
				'default_value' => 'Y',
				'title' => Loc::getMessage('SEOFILTER_ENTITY_ACTIVE_FIELD'),
			)),
			new Entity\BooleanField('EVIEW', array(
				'values' => array('N', 'Y'),
				'default_value' => 'Y',
				'title' => Loc::getMessage('SEOFILTER_ENTITY_EVIEW_FIELD'),
			)),
			new Entity\BooleanField('SVIEW', array(
				'values' => array('N', 'Y'),
				'default_value' => 'Y',
				'title' => Loc::getMessage('SEOFILTER_ENTITY_SVIEW_FIELD'),
			)),
			new Entity\StringField('DESCRIPTION', array(
				'required' => false,
				'default_value' => '',
				'title' => Loc::getMessage('SEOFILTER_ENTITY_DESCRIPTION_FIELD'),
			)),
			new Entity\StringField('TAG_NAME', array(
				'required' => false,
				'default_value' => '',
				'title' => Loc::getMessage('SEOFILTER_ENTITY_TAG_NAME_FIELD'),
			)),
			new Entity\StringField('TAG_SECTION_NAME', array(
				'required' => false,
				'default_value' => '',
				'title' => Loc::getMessage('SEOFILTER_ENTITY_TAG_SECTION_NAME_FIELD'),
			)),
			new Main\Entity\StringField('URL_CPU', array(
				'required' => true,
				'title' => Loc::getMessage('SEOFILTER_ENTITY_URL_CPU_FIELD'),
			)),
			new Main\Entity\StringField('URL_FILTER', array(
				'required' => false,
				'default_value' => 'NULL',
				'title' => Loc::getMessage('SEOFILTER_ENTITY_URL_FILTER_FIELD'),
			)),
			new Entity\TextField('PARAMS', array(
			    'serialized' => true,
			    'title' => Loc::getMessage('SEOFILTER_ENTITY_PARAMS_FIELD')
			))
		);
	}

}