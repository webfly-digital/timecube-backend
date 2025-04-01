<?
IncludeModuleLangFile(__FILE__);

if ($APPLICATION->GetGroupRight("ammina.optimizer") >= "R") {
	$arAllExistsSetting = array();
	if (class_exists("\\Ammina\\Optimizer\\SettingsTable")) {
		$rSettings = \Ammina\Optimizer\SettingsTable::getList(array(
			"select" => array(
				"ID", "SITE_ID", "TYPE",
			),
		));
		while ($arSetting = $rSettings->fetch()) {
			if ($arSetting['SITE_ID'] == "all") {
				$arAllExistsSetting[] = '.adm-submenu-item-name-link[href*="ammina.optimizer.settings.php?lang=' . LANGUAGE_ID . '&site=all&type=' . $arSetting['TYPE'] . '"]';
			} else {
				$arAllExistsSetting[] = '.adm-submenu-item-name-link[href*="ammina.optimizer.settings.php?lang=' . LANGUAGE_ID . '&site=' . $arSetting['SITE_ID'] . '&type=' . $arSetting['TYPE'] . '"]';
				$arAllExistsSetting[] = '.adm-submenu-item-name-link[onclick*="menu_ammina_site_' . $arSetting['SITE_ID'] . '_settings_page"]';
			}
		}
	}
	if (!empty($arAllExistsSetting)) {
		$strStyle = '<style>' . implode(",", $arAllExistsSetting) . '{color:#006212;} ';
		foreach ($arAllExistsSetting as $k => $v) {
			$arAllExistsSetting[$k] = ".adm-submenu-item-active " . $v;
		}
		$strStyle .= implode(",", $arAllExistsSetting) . '{font-weight:bold;}';
		$strStyle .= '</style>';
		$APPLICATION->AddHeadString($strStyle);
	}
	$arSitesMenu = array();
	$rSites = CSite::GetList($b, $o);
	while ($arSite = $rSites->Fetch()) {
		$arSitesMenu[] = array(
			"text" => "[" . $arSite['LID'] . "] " . $arSite['NAME'],
			"title" => "[" . $arSite['LID'] . "] " . $arSite['NAME'],
			"items_id" => "menu_ammina_site_" . $arSite['LID'] . "_settings_page",
			"items" => array(
				array(
					"text" => GetMessage("AMMINA_OPTIMIZER_MENU_SITE_SETTINGS_ALL_TEXT"),
					"title" => GetMessage("AMMINA_OPTIMIZER_MENU_SITE_SETTINGS_ALL_TITLE"),
					"items_id" => "menu_ammina_" . $arSite['LID'] . "_settings_all_page",
					"url" => "ammina.optimizer.settings.php?lang=" . LANGUAGE_ID . "&site=" . $arSite['LID'] . "&type=a",
				),
				array(
					"text" => GetMessage("AMMINA_OPTIMIZER_MENU_SITE_SETTINGS_MOBILE_TEXT"),
					"title" => GetMessage("AMMINA_OPTIMIZER_MENU_SITE_SETTINGS_MOBILE_TITLE"),
					"items_id" => "menu_ammina_" . $arSite['LID'] . "_settings_mobile_page",
					"url" => "ammina.optimizer.settings.php?lang=" . LANGUAGE_ID . "&site=" . $arSite['LID'] . "&type=m",
				),
				array(
					"text" => GetMessage("AMMINA_OPTIMIZER_MENU_SITE_SETTINGS_DESKTOP_TEXT"),
					"title" => GetMessage("AMMINA_OPTIMIZER_MENU_SITE_SETTINGS_DESKTOP_TITLE"),
					"items_id" => "menu_ammina_" . $arSite['LID'] . "_settings_desktop_page",
					"url" => "ammina.optimizer.settings.php?lang=" . LANGUAGE_ID . "&site=" . $arSite['LID'] . "&type=d",
				),
			),
		);
	}

	$aMenu = array(
		"parent_menu" => "global_menu_services",
		"section" => "ammina.optimizer",
		"sort" => 10000,
		"text" => GetMessage("AMMINA_OPTIMIZER_MENU_TEXT"),
		"title" => GetMessage("AMMINA_OPTIMIZER_MENU_TITLE"),
		"icon" => "AMMINA_OPTIMIZER_menu_icon",
		"page_icon" => "AMMINA_OPTIMIZER_page_icon",
		"items_id" => "menu_ammina_optimizer",
		"items" => array(
			array(
				"text" => GetMessage("AMMINA_OPTIMIZER_MENU_CHECK_LIBRARY_TEXT"),
				"title" => GetMessage("AMMINA_OPTIMIZER_MENU_CHECK_LIBRARY_TITLE"),
				"items_id" => "menu_ammina_check_library_page",
				"url" => "ammina.optimizer.check.library.php?lang=" . LANGUAGE_ID,
			),
			array(
				"text" => GetMessage("AMMINA_OPTIMIZER_MENU_QUICK_SETTING_TEXT"),
				"title" => GetMessage("AMMINA_OPTIMIZER_MENU_QUICK_SETTING_TITLE"),
				"items_id" => "menu_ammina_quick_setting_page",
				"url" => "ammina.optimizer.quick.setting.php?lang=" . LANGUAGE_ID,
			),
			array(
				"text" => GetMessage("AMMINA_OPTIMIZER_MENU_DEFAULT_SETTINGS_TEXT"),
				"title" => GetMessage("AMMINA_OPTIMIZER_MENU_DEFAULT_SETTINGS_TITLE"),
				"items_id" => "menu_ammina_def_settings_page",
				"items" => array(
					array(
						"text" => GetMessage("AMMINA_OPTIMIZER_MENU_DEFAULT_SETTINGS_ALL_TEXT"),
						"title" => GetMessage("AMMINA_OPTIMIZER_MENU_DEFAULT_SETTINGS_ALL_TITLE"),
						"items_id" => "menu_ammina_def_settings_all_page",
						"url" => "ammina.optimizer.settings.php?lang=" . LANGUAGE_ID . "&site=all&type=a",
					),
					array(
						"text" => GetMessage("AMMINA_OPTIMIZER_MENU_DEFAULT_SETTINGS_MOBILE_TEXT"),
						"title" => GetMessage("AMMINA_OPTIMIZER_MENU_DEFAULT_SETTINGS_MOBILE_TITLE"),
						"items_id" => "menu_ammina_def_settings_mobile_page",
						"url" => "ammina.optimizer.settings.php?lang=" . LANGUAGE_ID . "&site=all&type=m",
					),
					array(
						"text" => GetMessage("AMMINA_OPTIMIZER_MENU_DEFAULT_SETTINGS_DESKTOP_TEXT"),
						"title" => GetMessage("AMMINA_OPTIMIZER_MENU_DEFAULT_SETTINGS_DESKTOP_TITLE"),
						"items_id" => "menu_ammina_def_settings_desktop_page",
						"url" => "ammina.optimizer.settings.php?lang=" . LANGUAGE_ID . "&site=all&type=d",
					),
				),
			),
			array(
				"text" => GetMessage("AMMINA_OPTIMIZER_MENU_SITES_SETTINGS_TEXT"),
				"title" => GetMessage("AMMINA_OPTIMIZER_MENU_SITES_SETTINGS_TITLE"),
				"items_id" => "menu_ammina_sites_settings_page",
				"items" => $arSitesMenu,
			),
			array(
				"text" => GetMessage("AMMINA_OPTIMIZER_MENU_AUDIT_TEXT"),
				"title" => GetMessage("AMMINA_OPTIMIZER_MENU_AUDIT_TITLE"),
				"items_id" => "menu_ammina_audit_page",
				"items" => array(
					array(
						"text" => GetMessage("AMMINA_OPTIMIZER_MENU_PAGE_TEXT"),
						"title" => GetMessage("AMMINA_OPTIMIZER_MENU_PAGE_TITLE"),
						"items_id" => "menu_ammina_optimizer_page",
						"url" => "ammina.optimizer.page.php?lang=" . LANGUAGE_ID,
						"more_url" => array(
							"ammina.optimizer.page.edit.php",
						),
					),
					array(
						"text" => GetMessage("AMMINA_OPTIMIZER_MENU_HISTORY_TEXT"),
						"title" => GetMessage("AMMINA_OPTIMIZER_MENU_HISTORY_TITLE"),
						"items_id" => "menu_ammina_optimizer_history",
						"url" => "ammina.optimizer.history.php?lang=" . LANGUAGE_ID,
						"more_url" => array(
							"ammina.optimizer.history.edit.php",
						),
					),
				),
			),

			array(
				"text" => GetMessage("AMMINA_OPTIMIZER_MENU_STAT_TEXT"),
				"title" => GetMessage("AMMINA_OPTIMIZER_MENU_STAT_TITLE"),
				"items_id" => "menu_ammina_stat_page",
				"items" => array(
					array(
						"text" => GetMessage("AMMINA_OPTIMIZER_MENU_STAT_CACHE_TEXT"),
						"title" => GetMessage("AMMINA_OPTIMIZER_MENU_STAT_CACHE_TITLE"),
						"items_id" => "menu_ammina_optimizer_stat_cache",
						"url" => "ammina.optimizer.stat.cache.php?lang=" . LANGUAGE_ID,
						"more_url" => array(),
					),
					array(
						"text" => GetMessage("AMMINA_OPTIMIZER_MENU_STAT_FILES_TEXT"),
						"title" => GetMessage("AMMINA_OPTIMIZER_MENU_STAT_FILES_TITLE"),
						"items_id" => "menu_ammina_optimizer_stat_cache",
						"url" => "ammina.optimizer.stat.files.php?lang=" . LANGUAGE_ID,
						"more_url" => array(
							"ammina.optimizer.stat.files.view.php",
						),
					),
				),
			),
			array(
				"text" => GetMessage("AMMINA_OPTIMIZER_MENU_HELP_TEXT"),
				"title" => GetMessage("AMMINA_OPTIMIZER_MENU_HELP_TITLE"),
				"items_id" => "menu_ammina_optimizer_help",
				"url" => "ammina.optimizer.help.php?lang=" . LANGUAGE_ID,
				"more_url" => array(),
			),
			array(
				"text" => GetMessage("AMMINA_OPTIMIZER_MENU_GETKEY_TEXT"),
				"title" => GetMessage("AMMINA_OPTIMIZER_MENU_GETKEY_TITLE"),
				"items_id" => "menu_ammina_optimizer_getkey",
				"url" => "ammina.optimizer.get.key.php?lang=" . LANGUAGE_ID,
				"more_url" => array(),
			),
		),
	);

	return $aMenu;
}

return false;
