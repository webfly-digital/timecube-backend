<?php
/**
 * Created by PhpStorm.
 * User: luk
 * Email: oleh.holovkin@hotlab.com.ua
 * Date: 26.09.2021
 * Time: 19:38
 */

namespace Zverushki\Seofilter\Internals;

use Bitrix\Main,
	Bitrix\Main\Entity,
	Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class GroupTable extends Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName () {
		return 'zverushki_seofilter_group';
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
			new Entity\DatetimeField('TIMESTAMP_X', array(
				'default_value' => new Main\Type\DateTime
			)),
			new Entity\BooleanField('ACTIVE', array(
				'values' => array('N', 'Y'),
				'default_value' => 'Y',
				'title' => Loc::getMessage('SEOFILTER_ENTITY_ACTIVE_FIELD'),
			)),
			new Entity\StringField('TITLE', array(
				'required' => false,
				'default_value' => '',
				'title' => Loc::getMessage('SEOFILTER_ENTITY_TITLE_FIELD'),
			))
		);
	}

}