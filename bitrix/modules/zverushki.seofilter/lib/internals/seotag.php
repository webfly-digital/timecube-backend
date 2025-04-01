<?php
namespace Zverushki\Seofilter\Internals;

use Bitrix\Main,
	Bitrix\Main\Entity,
	Bitrix\Main\Localization\Loc,
	Zverushki\Seofilter\emoji,
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

class SeotagTable extends Entity\DataManager {
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName () {
		return 'zverushki_seofilter_seotag';
	}

	public static function add (array $fields) {

		$fields = static::cleanEmoji($fields);

		return parent::add($fields);
	}

	public static function update ($id, array $fields) {

		$fields = static::cleanEmoji($fields);

		return parent::update($id, $fields);
	}

	private static function cleanEmoji($fields){

		foreach($fields as $code => $value){
			if($code != 'ID' && $code != 'SETTING_ID')
				$fields[$code] = emoji::encode($value);
		}
		return $fields;
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
				'required' => true,
				'title' => Loc::getMessage('SEOFILTER_ENTITY_SETTING_ID_FIELD'),
			)),
			new Entity\DatetimeField('TIMESTAMP_X', array(
				'default_value' => new Main\Type\DateTime
			)),
			new Entity\TextField('SEO_DESCRIPTION_TOP', array(
				'required' => false,
				'default_value' => '',
				'title' => Loc::getMessage('SEOFILTER_ENTITY_SEO_DESCRIPTION_TOP_FIELD'),
			)),
			new Entity\TextField('SEO_DESCRIPTION', array(
				'required' => false,
				'default_value' => '',
				'title' => Loc::getMessage('SEOFILTER_ENTITY_SEO_DESCRIPTION_FIELD'),
			)),
			new Entity\StringField('PAGE_TITLE', array(
				'required' => false,
				'default_value' => '',
				'title' => Loc::getMessage('SEOFILTER_ENTITY_PAGE_TITLE_FIELD'),
			)),
			new Entity\StringField('META_TITLE', array(
				'required' => false,
				'default_value' => '',
				'title' => Loc::getMessage('SEOFILTER_ENTITY_META_TITLE_FIELD'),
			)),
			new Entity\StringField('META_KEYWORDS', array(
				'required' => false,
				'default_value' => '',
				'title' => Loc::getMessage('SEOFILTER_ENTITY_META_KEYWORDS_FIELD'),
			)),
			new Entity\StringField('META_DESCRIPTION', array(
				'required' => false,
				'default_value' => '',
				'title' => Loc::getMessage('SEOFILTER_ENTITY_META_DESCRIPTION_FIELD'),
				'size' => 500,
			)),
		);
	}

}