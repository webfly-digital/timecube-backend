<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;


$arTemplateParameters['BXR_MOBILE_SHOW_SEARCH_FORM'] = array(
	'NAME' => GetMessage('BXR_MOBILE_SHOW_SEARCH_FORM'),
	'PARENT' => 'VISUAL',
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
);

$arTemplateParameters['BXR_MOBILE_SHOW_ANSWER_FORM'] = array(
	'NAME' => GetMessage('BXR_MOBILE_SHOW_ANSWER_FORM'),
	'PARENT' => 'VISUAL',
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
);

$arTemplateParameters['BXR_MOBILE_SHOW_PHONE_FORM'] = array(
	'NAME' => GetMessage('BXR_MOBILE_SHOW_PHONE_FORM'),
	'PARENT' => 'VISUAL',
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
);

$arTemplateParameters['BXR_MOBILE_SHOW_USER_FORM'] = array(
	'NAME' => GetMessage('BXR_MOBILE_SHOW_USER_FORM'),
	'PARENT' => 'VISUAL',
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
);

?>