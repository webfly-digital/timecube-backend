<?php
/**
 * Created by PhpStorm.
 * User: luk
 * Date: 21.02.2021
 * Time: 21:06
 */
namespace Zverushki\Seofilter\Sections;

use Bitrix\Main\Loader,
	Bitrix\Main\Localization\Loc;

Loader::includeModule('iblock');

class Section
{
	private static $fields = [];
	private static $sections = [];
	public static function initMainFields(){
		static::$fields = [
			'ID' => Loc::getMessage('SEOFILTER_SECTION_TITLE_ID'),
			'NAME' => Loc::getMessage('SEOFILTER_SECTION_TITLE_NAME'),
			'CODE' => Loc::getMessage('SEOFILTER_SECTION_TITLE_CODE'),
			'SECTION_PAGE_URL' => Loc::getMessage('SEOFILTER_SECTION_TITLE_SECTION_PAGE_URL')
		];
		return static::$fields;
	}

	/**
	 * Получить список полей для раздела
	 * @param Ид Инфоблока $iblockId
	 * @param bool $all
	 *
	 * @return array
	 */
	public static function initFields($iblockId, $all = true){
		$fields = static::initMainFields();
		if($all){
			$rs = \CUserTypeEntity::GetList(['SORT' => 'ASC', 'NAME' => 'ASC'], ['ENTITY_ID' => 'IBLOCK_'.$iblockId.'_SECTION', 'LANG' => LANGUAGE_ID]);
			while($arUserField = $rs->Fetch())
			{
				if(in_array($arUserField['USER_TYPE_ID'], ['file', 'video', 'boolean', 'iblock_section', 'iblock_element', 'hlblock']))
					continue;

				$fields[$arUserField['FIELD_NAME']] = $arUserField['LIST_COLUMN_LABEL'] ? $arUserField['LIST_COLUMN_LABEL'] : $arUserField['FIELD_NAME'];
			}
		}
		return $fields;
	}

	/**
	 * Получить значения полей раздела для подмены
	 * @param Ид Инфоблока $iblockId
	 * @param Ид раздела   $sectionId
	 * @param bool $all
	 *
	 * @return mixed
	 */
	public static function initSectionFields($iblockId, $sectionId, $all = true){
		$code = ($all ? 'all' : 'not');
		if(static::$sections[$code][$iblockId][$sectionId])
			return static::$sections[$code][$iblockId][$sectionId];

		$fields = static::initFields($iblockId, $all);
		return static::$sections[$code][$iblockId][$sectionId] = \CIBlockSection::GetList(['ID' => 'ASC'], ['IBLOCK_ID' => $iblockId, 'ID' => $sectionId], false, array_keys($fields), ['nTopCount' => 1])->GetNext(false, false);
	}

	public static function replace(&$setting, &$seo = []){
		if(
			!empty($seo) ||
			preg_match('/\#SECTION_(.+?)\#/i', $setting['TAG_NAME']) ||
			preg_match('/\#SECTION_(.+?)\#/i', $setting['TAG_SECTION_NAME'])
		)
			$section = Section::initSectionFields($setting['IBLOCK_ID'], $setting['SECTION_ID']);
		elseif(preg_match('/\#SECTION_(.+?)\#/i', $setting['URL_CPU'])){
			$section = Section::initSectionFields($setting['IBLOCK_ID'], $setting['SECTION_ID'], false);
		}

		if($section)
			foreach($section as $code => $val){
				if($setting['URL_CPU'])
					$setting['URL_CPU'] = str_replace('//', '/', str_replace('#SECTION_' . $code . '#', $val, $setting['URL_CPU']));

				if($setting['TAG_NAME'])
					$setting['TAG_NAME'] = str_replace('#SECTION_' . $code . '#', $val, $setting['TAG_NAME']);

				if($setting['TAG_SECTION_NAME'])
					$setting['TAG_SECTION_NAME'] = str_replace('#SECTION_' . $code . '#', $val, $setting['TAG_SECTION_NAME']);

				if($setting['ZVERUSHKI_SEOFILTER_INTERNALS_SETTINGS_SETTING_PAGE_TITLE'])
					$setting['ZVERUSHKI_SEOFILTER_INTERNALS_SETTINGS_SETTING_PAGE_TITLE'] = str_replace('#SECTION_' . $code . '#', $val, $setting['ZVERUSHKI_SEOFILTER_INTERNALS_SETTINGS_SETTING_PAGE_TITLE']);

				if($seo){
					if(!is_array($val))
						$val = [ $val ];

					foreach($seo as $c => $l)
						$seo[$c] = preg_replace('/\#SECTION_' . $code . '\#/i', implode(', ', $val), $l);
				}
			}
	}
}