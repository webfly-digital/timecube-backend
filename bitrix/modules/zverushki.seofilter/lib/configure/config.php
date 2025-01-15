<?

namespace Zverushki\Seofilter\Configure;

use Bitrix\Main\Loader, Bitrix\Main\Localization\Loc;

Loc::loadMessages( __FILE__ );
Loader::includeSharewareModule( 'zverushki.seofilter' );

class Config
{
	static function getFormParams ()
	{
		return [
			'form' => [
				'groupsitemap'        => [
					'type'        => 'titleline',
					'name'        => Loc::getMessage( 'SEOFILTER_SETTING_SITEMAP_GROUP_LINE' ),
					'description' => Loc::getMessage( 'SEOFILTER_SETTING_FILTER_GROUP_LINE_DESC' ),
					'default'     => ''
				],
				"agent_active"        => [
					"type"        => "checkbox",
					"name"        => Loc::getMessage( "SEOFILTER_SETTING_AGENT_ACTIVE" ),
					"description" => Loc::getMessage( "SEOFILTER_SETTING_AGENT_ACTIVE_DESC" ),
					"values"      => "Y",
					"required"    => false,
					"default"     => "N",
					"system"      => true
				],
				"integrate_notactive" => [
					"type"        => "checkbox",
					"name"        => Loc::getMessage( "SEOFILTER_SETTING_INTEGRATE_NOTACTIVE" ),
					"description" => Loc::getMessage( "SEOFILTER_SETTING_INTEGRATE_NOTACTIVE_DESC" ),
					"values"      => "Y",
					"required"    => false,
					"default"     => "N",
					"system"      => true
				],
				"period_agent"        => [
					"type"        => "list",
					"name"        => Loc::getMessage( "SEOFILTER_SETTING_PERIOD_AGENT" ),
					"description" => Loc::getMessage( "SEOFILTER_SETTING_PERIOD_AGENT_DESC" ),
					"values"      => [
						2   => Loc::getMessage( 'SEOFILTER_SETTING_PERIOD_IS_2' ),
						4   => Loc::getMessage( 'SEOFILTER_SETTING_PERIOD_IS_4' ),
						6   => Loc::getMessage( 'SEOFILTER_SETTING_PERIOD_IS_6' ),
						12  => Loc::getMessage( 'SEOFILTER_SETTING_PERIOD_IS_12' ),
						24  => Loc::getMessage( 'SEOFILTER_SETTING_PERIOD_IS_24' ),
						36  => Loc::getMessage( 'SEOFILTER_SETTING_PERIOD_IS_36' ),
						48  => Loc::getMessage( 'SEOFILTER_SETTING_PERIOD_IS_48' ),
						72  => Loc::getMessage( 'SEOFILTER_SETTING_PERIOD_IS_72' ),
						96  => Loc::getMessage( 'SEOFILTER_SETTING_PERIOD_IS_96' ),
						120 => Loc::getMessage( 'SEOFILTER_SETTING_PERIOD_IS_120' ),
						240 => Loc::getMessage( 'SEOFILTER_SETTING_PERIOD_IS_240' ),
						480 => Loc::getMessage( 'SEOFILTER_SETTING_PERIOD_IS_480' )
					],
					"required"    => true,
					"default"     => 12,
					"main"        => true,
					"system"      => true,
					"altsite"     => '-'
				],
				'groupfilter'         => [
					'type'        => 'titleline',
					'name'        => Loc::getMessage( 'SEOFILTER_SETTING_FILTER_GROUP_LINE' ),
					'description' => Loc::getMessage( 'SEOFILTER_SETTING_FILTER_GROUP_LINE_DESC' ),
					'default'     => ''
				],
				'cpu_catalog'         => [
					'type'        => 'text',
					'name'        => Loc::getMessage( 'SEOFILTER_SETTING_CPU_CATALOG' ),
					'description' => Loc::getMessage( 'SEOFILTER_SETTING_CPU_CATALOG_DESC' ),
					'values'      => [],
					'required'    => true,
					'system'      => true,
					'default'     => '',
					'style'       => 'max-width: 100%;width: 200px;',
					'relate'      => []
				],
				'filtervar'           => [
					'type'        => 'text',
					'name'        => Loc::getMessage( 'SEOFILTER_SETTING_FILTER_VAR' ),
					'description' => Loc::getMessage( 'SEOFILTER_SETTING_FILTER_VAR_DESC' ),
					'values'      => [],
					'required'    => false,
					'system'      => true,
					'default'     => '',
					'style'       => 'max-width: 100%;width: 200px;',
					'relate'      => []
				],
				"cpu_active"          => [
					"type"        => "checkbox",
					"name"        => Loc::getMessage( "SEOFILTER_SETTING_CPU_ACTIVE" ),
					"description" => Loc::getMessage( "SEOFILTER_SETTING_CPU_URL_DESC" ),
					"values"      => "Y",
					"required"    => false,
					"default"     => "N",
					"system"      => true
				],
				'cpu_url'             => [
					'type'        => 'text',
					'name'        => Loc::getMessage( 'SEOFILTER_SETTING_CPU_URL' ),
					'description' => Loc::getMessage( 'SEOFILTER_SETTING_CPU_URL_DESC' ),
					'values'      => [],
					'required'    => false,
					'system'      => true,
					'default'     => '',
					'style'       => 'max-width: 100%;width: 400px;',
					'relate'      => []
				],
				'groupline'           => [
					'type'        => 'titleline',
					'name'        => Loc::getMessage( 'SEOFILTER_SETTING_CUSTOM_GROUP_LINE' ),
					'description' => Loc::getMessage( 'SEOFILTER_SETTING_CUSTOM_GROUP_LINE_DESC' ),
					'default'     => ''
				],
				"price_active"        => [
					"type"        => "list",
					"name"        => Loc::getMessage( "SEOFILTER_SETTING_PRICE_ACTIVE" ),
					"description" => Loc::getMessage( "SEOFILTER_SETTING_PRICE_ACTIVE_DESC" ),
					"values"      => [],
					"required"    => false,
					"default"     => "",
					"multy"       => true,
					"system"      => true,
					"main"        => true,
					"altsite"     => '-'
				],
				"avail_active"        => [
					"type"        => "checkbox",
					"name"        => Loc::getMessage( "SEOFILTER_SETTING_AVAIL_ACTIVE" ),
					"description" => Loc::getMessage( "SEOFILTER_SETTING_AVAIL_ACTIVE_DESC" ),
					"values"      => "Y",
					"required"    => false,
					"default"     => "Y",
					"system"      => true,
					"main"        => true,
					"altsite"     => '-'
				],
				"check_cpu"           => [
					"type"        => "checkbox",
					"name"        => Loc::getMessage( "SEOFILTER_SETTING_CHECK_CPU" ),
					"description" => Loc::getMessage( "SEOFILTER_SETTING_CHECK_CPU_DESC" ),
					"values"      => "Y",
					"required"    => false,
					"default"     => "Y",
					"system"      => true,
					"main"        => true,
					"altsite"     => '-'
				],
				'groupglobal'         => [
					'type'        => 'titleline',
					'name'        => Loc::getMessage( 'SEOFILTER_SETTING_CUSTOM_GROUP_GLOBAL' ),
					'description' => Loc::getMessage( 'SEOFILTER_SETTING_CUSTOM_GROUP_GLOBAL_DESC' ),
					'default'     => ''
				],
				"not_accelerated_search"       => [
					"type"        => "checkbox",
					"name"        => Loc::getMessage( "SEOFILTER_SETTING_VIEW_NOT_ACCELERATED_SEARCH" ),
					"description" => Loc::getMessage( "SEOFILTER_SETTING_VIEW_NOT_ACCELERATED_SEARCH_DESC" ),
					"values"      => "Y",
					'required'    => false,
					"main"        => true,
					"system"      => true,
					"altsite"     => '-'
				],
				"purification"        => [
					"type"        => "list",
					"name"        => Loc::getMessage( "SEOFILTER_SETTING_PURIFICATION" ),
					"description" => Loc::getMessage( "SEOFILTER_SETTING_PURIFICATION_DESC" ),
					"values"      => [
						'hide'   => Loc::getMessage( "SEOFILTER_SETTING_PURIFICATION_VAL_HIDE" ),
						'delete' => Loc::getMessage( "SEOFILTER_SETTING_PURIFICATION_VAL_DELETE" )
					],
					"required"    => false,
					"default"     => "N",
					"system"      => true,
					"main"        => true,
					"altsite"     => '-'
				],
				"view_checkall"       => [
					"type"        => "checkbox",
					"name"        => Loc::getMessage( "SEOFILTER_SETTING_VIEW_CHECKALL" ),
					"description" => Loc::getMessage( "SEOFILTER_SETTING_VIEW_CHECKALL_DESC" ),
					"values"      => "Y",
					'required'    => false,
					"main"        => true,
					"system"      => true,
				],
				"redirect_active"     => [
					"type"        => "checkbox",
					"name"        => Loc::getMessage( "SEOFILTER_SETTING_REDIRECT_ACTIVE" ),
					"description" => Loc::getMessage( "SEOFILTER_SETTING_REDIRECT_ACTIVE_DESC" ),
					"values"      => "Y",
					"required"    => false,
					"default"     => "N",
					"system"      => true
				],
				"canonical_active"    => [
					"type"        => "checkbox",
					"name"        => Loc::getMessage( "SEOFILTER_SETTING_CANONICAL_ACTIVE" ),
					"description" => Loc::getMessage( "SEOFILTER_SETTING_CANONICAL_URL_DESC" ),
					"values"      => "Y",
					"required"    => false,
					"default"     => "N",
					"system"      => true
				],
				"space_replace"       => [
					"type"        => "text",
					"name"        => Loc::getMessage( "SEOFILTER_SETTING_SPACE_REPLACE" ),
					"description" => Loc::getMessage( "SEOFILTER_SETTING_SPACE_REPLACE_DESC" ),
					'values'      => [],
					'required'    => false,
					'default'     => '_',
					"system"      => true,
					"main"        => true,
					'style'       => 'max-width: 100%;width: 40px;',
					"altsite"     => '-'
				],
				"template_active"     => [
					"type"        => "list",
					"name"        => Loc::getMessage( "SEOFILTER_SETTING_TEMPLATE_SELECT" ),
					"description" => Loc::getMessage( "SEOFILTER_SETTING_TEMPLATE_SELECT_DESC" ),
					"values"      => [ 'aspronext' => Loc::getMessage( "SEOFILTER_SETTING_TEMPLATE_SELECT_VAL" ) ],
					"required"    => false,
					"default"     => "N",
					"system"      => true
				],
				'advanced_settings'   => [
					'type'        => 'titleline',
					'name'        => Loc::getMessage( 'SEOFILTER_SETTING_CUSTOM_ADVACED_SETTINGS' ),
					'description' => Loc::getMessage( 'SEOFILTER_SETTING_CUSTOM_ADVACED_SETTINGS_DESC' ),
					'default'     => '',
					"main"        => true,
					"advanced"    => true
				],
				"not_active"          => [
					"type"        => "checkbox",
					"name"        => Loc::getMessage( "SEOFILTER_SETTING_NOT_ACTIVE" ),
					"description" => Loc::getMessage( "SEOFILTER_SETTING_NOT_ACTIVE_DESC" ),
					"values"      => "Y",
					"required"    => false,
					"default"     => "N",
					"main"        => true,
					"system"      => true,
					"advanced"    => true,
					"altsite"     => '-'

				],
				"limit_step"          => [
					"type"        => "text",
					"name"        => Loc::getMessage( "SEOFILTER_SETTING_LIMIT_STEP" ),
					"description" => Loc::getMessage( "SEOFILTER_SETTING_LIMIT_STEP_DESC" ),
					"values"      => "Y",
					"required"    => false,
					"default"     => "1000",
					"main"        => true,
					"system"      => true,
					"advanced"    => true,
					"altsite"     => '-'
				],
				"time_limit"          => [
					"type"        => "text",
					"name"        => Loc::getMessage( "SEOFILTER_SETTING_TIME_LIMIT" ),
					"description" => Loc::getMessage( "SEOFILTER_SETTING_TIME_LIMIT_DESC" ),
					"values"      => "Y",
					"required"    => false,
					"default"     => "40",
					"main"        => true,
					"system"      => true,
					"advanced"    => true,
					"altsite"     => '-'
				],
			]
		];
	}
}

?>