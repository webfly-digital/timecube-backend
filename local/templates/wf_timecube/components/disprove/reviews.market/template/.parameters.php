<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentParameters = array(
	"GROUPS" => array(
		"ACTION_SETTINGS" => array(
			"NAME" => GetMessage('IBLOCK_ACTIONS')
		),
	),
);
$arComponentParameters["PARAMETERS"]['GIFTS_MESS_BTN_BUY'] = array(
	'PARENT' => 'ACTION_SETTINGS',
	'NAME' => GetMessage('CVP_MESS_BTN_BUY_GIFT'),
	'TYPE' => 'STRING',
	'DEFAULT' => ""
);
$arTemplateParameters = array(
);
$arThemes['orange'] = GetMessage('DRM_BC_TPL_THEME_ORANGE');
$arThemes['blue'] = GetMessage('DRM_BC_TPL_THEME_BLUE');
$arThemes['purpur'] = GetMessage('DRM_BC_TPL_THEME_PURPUR');
$arThemes['green'] = GetMessage('DRM_BC_TPL_THEME_GREEN');
$arThemes['red'] = GetMessage('DRM_BC_TPL_THEME_RED');
$arThemes['black'] = GetMessage('DRM_BC_TPL_THEME_BLACK');
$arTemplateParameters['TEMPLATE_THEME'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage("DRM_BC_TPL_TEMPLATE_THEME"),
	'TYPE' => 'LIST',
	'VALUES' => $arThemes,
	'DEFAULT' => 'blue',
	'ADDITIONAL_VALUES' => 'Y'
);
?>