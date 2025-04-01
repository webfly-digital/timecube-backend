<?php
/**
 * Created by PhpStorm.
 * User: luk
 * Date: 05.05.2021
 * Time: 19:00
 */

namespace Zverushki\Seofilter\Internals;

use Bitrix\Main, Bitrix\Main\Entity, Bitrix\Main\Localization\Loc;

Loc::loadMessages( __FILE__ );

/**
 * Class LandingTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * </ul>
 *
 * @package Zverushki\Seofilter\Internals
 **/
class LandingTable extends Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName ()
	{
		return 'zverushki_seofilter_landing';
	}

	public static function add ( array $fields )
	{
		if ( !isset( $fields[ 'ACTIVE' ] ) ) $fields[ 'ACTIVE' ] = 'N';

		return parent::add( $fields );
	}

	public static function update ( $id, array $fields )
	{

		return parent::update( $id, $fields );
	}

	public static function delete ( $id )
	{
		$seofilterSetting = self::getList(
			[
				'filter' => [ 'ID' => $id ],
				'select' => [ 'VAR.ID' ]
			]
		)
		                        ->fetch();
		if ( $seofilterSetting[ "ZVERUSHKI_SEOFILTER_INTERNALS_LANDING_VAR_LANDING_ID" ] > 0 ) LandingVarTable::delete(
			$seofilterSetting[ "ZVERUSHKI_SEOFILTER_INTERNALS_LANDING_VAR_LANDING_ID" ]
		);

		return parent::delete( $id );
	}

	static function clearIndex ( $settingId, $old = false )
	{
		global $DB;
		$filter = [ 'SETTING_ID' => $settingId ];
		if ( $old ) {
			$dt = new Main\Type\DateTime;
			$dt->add('-10 day');
			$filter[ 'ACTIVE' ] = "N";
			$filter[ 'ENABLE' ] = "N";
			$filter[ '<DATE_DEACTIVE' ] = $dt;

			$filterStr = " and ACTIVE = 'N' and ENABLE = 'N' and DATE_DEACTIVE < ".$DB->CharToDateFunction($dt->toString());
		}
		$obLanding = LandingTable::getList(
			[
				'filter' => $filter,
				'select' => [ 'ID', 'SETTING_ID', 'DATE_DEACTIVE']
			]
		);
		while ( $arLanding = $obLanding->fetch() ) {
			$DB->Query( "DELETE FROM `".LandingVarTable::getTableName()."` WHERE `LANDING_ID` = ".$arLanding[ 'ID' ] );
		}

		$DB->Query( "DELETE FROM `".static::getTableName()."` WHERE `SETTING_ID` = ".$settingId.$filterStr);
	}

	public static function addIndex()
	{
		global $DB;
		$DB->Query(
			"ALTER TABLE `".static::getTableName()."`
			ADD INDEX `IBLOCK_ID` (`IBLOCK_ID`),
			ADD INDEX `SECTION_ID` (`SECTION_ID`),
			ADD INDEX `SETTING_ID` (`SETTING_ID`);"
		);
	}
	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap ()
	{
		return [
			new Entity\IntegerField(
				'ID', [
				'primary'      => true,
				'autocomplete' => true,
				'title'        => Loc::getMessage( 'SEOFILTER_ENTITY_ID_FIELD' ),
			]
			),
			new Entity\IntegerField(
				'SETTING_ID', [
				'required' => true,
				'title'    => Loc::getMessage( 'SEOFILTER_ENTITY_SETTING_ID_FIELD' ),
			]
			),
			new Entity\IntegerField(
				'IBLOCK_ID', [
				'required' => true,
				'title'    => Loc::getMessage( 'SEOFILTER_ENTITY_IBLOCK_ID_FIELD' ),
			]
			),
			new Entity\IntegerField(
				'SECTION_ID', [
				'required'      => false,
				'default_value' => 0,
				'title'         => Loc::getMessage( 'SEOFILTER_ENTITY_SECTION_ID_FIELD' ),
			]
			),
			new Entity\IntegerField('GROUP_ID', array(
				'required'      => false,
				'default_value' => '',
				'title' => Loc::getMessage('SEOFILTER_ENTITY_GROUP_ID_FIELD'),
			)),
			new Entity\IntegerField(
				'IMG_ID', [
				'required'      => false,
				'default_value' => 0,
				'title'         => Loc::getMessage( 'SEOFILTER_ENTITY_IMG_ID_FIELD' ),
			]
			),
			new Entity\BooleanField(
				'MARK', [
				'values'        => [
					'D', // Deleting
					'R', // Updating
					'Y', // Active
				],
				'default_value' => 'R',
				'title'         => Loc::getMessage( 'SEOFILTER_ENTITY_TYPE_FIELD' ),
			]
			),
			new Entity\BooleanField(
				'TYPE', [
				'values'        => [
					'D', // Deleting
					'R', // Updating
					'H', // Static link
					'A'  // Template link
				],
				'default_value' => 'H',
				'title'         => Loc::getMessage( 'SEOFILTER_ENTITY_TYPE_FIELD' ),
			]
			),
			new Entity\IntegerField(
				'SORT', [
				'required' => false,
				'title'    => Loc::getMessage( 'SEOFILTER_ENTITY_SORT_FIELD' ),
			]
			),
			new Entity\IntegerField(
				'USORT', [
				'required'      => false,
				'default_value' => 0,
				'title'         => Loc::getMessage( 'SEOFILTER_ENTITY_USORT_FIELD' ),
			]
			),
			new Entity\IntegerField(
				'COUNT', [
				'required'      => false,
				'default_value' => 0,
				'title'         => Loc::getMessage( 'SEOFILTER_ENTITY_COUNT_FIELD' ),
			]
			),
			new Entity\DatetimeField(
				'TIMESTAMP_X', [
				'default_value' => new Main\Type\DateTime
			]
			),
			new Entity\DatetimeField(
				'DATE_ELEMENT', [
				'default_value' => new Main\Type\DateTime
			]
			),
			new Entity\DatetimeField( 'DATE_DEACTIVE', [
				'default_value' => new Main\Type\DateTime,
				'required'      => false
			] ),
			new Entity\BooleanField(
				'ACTIVE', [
				'values'        => [ 'N', 'Y' ],
				'default_value' => 'Y',
				'title'         => Loc::getMessage( 'SEOFILTER_ENTITY_ACTIVE_FIELD' ),
			]
			),
			new Entity\BooleanField(
				'ENABLE', [
				'values'        => [ 'N', 'Y' ],
				'default_value' => 'N',
				'title'         => Loc::getMessage( 'SEOFILTER_ENTITY_ENABLE_FIELD' ),
			]
			),
			new Entity\StringField(
				'PAGE_TITLE', [
				'required'      => false,
				'default_value' => '',
				'title'         => Loc::getMessage( 'SEOFILTER_ENTITY_PAGE_TITLE_FIELD' ),
			]
			),
			new Entity\StringField(
				'PAGE_SECTION_TITLE', [
				'required'      => false,
				'default_value' => '',
				'title'         => Loc::getMessage( 'SEOFILTER_ENTITY_PAGE_SECTION_TITLE_FIELD' ),
			]
			),
			new Entity\StringField(
				'URL_CPU', [
				'required' => true,
				'title'    => Loc::getMessage( 'SEOFILTER_ENTITY_URL_CPU_FIELD' ),
			]
			),
			new Entity\StringField(
				'PARAMS_HASH', [
				'required' => true,
				'title'    => Loc::getMessage( 'SEOFILTER_ENTITY_PARAMS_HASH_FIELD' ),
			]
			),
			new Entity\TextField(
				'PARAMS', [
				'serialized' => true,
				'title'      => Loc::getMessage( 'SEOFILTER_ENTITY_PARAMS_FIELD' )
			]
			),
			new Entity\ReferenceField(
				'VAR',
				'Zverushki\Seofilter\Internals\LandingVarTable',
				[ '=this.ID' => 'ref.LANDING_ID', 'ref.TYPE' => new Main\DB\SqlExpression( '?i', 'V' ) ]
			),
			new Entity\ReferenceField(
				'PROPS',
				'Zverushki\Seofilter\Internals\LandingVarTable',
				[ '=this.ID' => 'ref.LANDING_ID', 'ref.TYPE' => new Main\DB\SqlExpression( '?i', 'P' ) ]
			)
		];
	}
}

?>