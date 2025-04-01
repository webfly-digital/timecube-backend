<?
use Bitrix\Main\Localization\Loc;

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';

\Bitrix\Main\Page\Asset::getInstance()->addString('<style>.adm-submenu-item-link-icon.zverushki_seo_menu_icon{background-image:url(/bitrix/images/zverushki.seofilter/filtre.png);background-position: center;}</style>', true);
$moduleId = 'zverushki.seofilter';
$aMenu[] = array(
	'parent_menu' => 'global_menu_marketing',
	'text' => Loc::getMessage('SEOFILTER_MENU_MAIN'),
	'title' => Loc::getMessage('SEOFILTER_MENU_MAIN'),
	'items_id' => 'global_menu_hl'.md5($moduleId),
	'icon' => 'zverushki_seo_menu_icon',
	// 'url' => $moduleId.'.php?lang='.LANGUAGE_ID,
	'more_url' => array(),
	'items' => array(
		array(
			'text' => Loc::getMessage('SEOFILTER_MENU_SET'),
			'title' => Loc::getMessage('SEOFILTER_MENU_SET'),
			'url' => $moduleId.'_settings.php?lang='.LANGUAGE_ID,
			'more_url' => array(
				$moduleId.'_setting_edit.php'
			)
		)
	)
);

return $aMenu;