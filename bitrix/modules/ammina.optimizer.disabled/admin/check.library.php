<?

use Bitrix\Main\Localization\Loc;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
Bitrix\Main\Loader::includeModule('ammina.optimizer');
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/ammina.optimizer/prolog.php");

Loc::loadMessages(__FILE__);

$arUserGroups = $USER->GetUserGroupArray();
$modulePermissions = $APPLICATION->GetGroupRight("ammina.optimizer");

if ($modulePermissions < "W") {
	$APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
}

if (CAmminaOptimizer::getTestPeriodInfo() == \Bitrix\Main\Loader::MODULE_DEMO_EXPIRED) {
	require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
	CAdminMessage::ShowMessage(array("MESSAGE" => Loc::getMessage("AMMINA_OPTIMIZER_SYS_MODULE_IS_DEMO_EXPIRED"), "HTML" => true));
	require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
	die();
}
$APPLICATION->SetTitle(Loc::getMessage("AMMINA_OPTIMIZER_PAGE_TITLE"));
CUtil::InitJSCore();
CJSCore::Init(array("jquery3"));
$APPLICATION->SetAdditionalCSS("/bitrix/themes/.default/ammina.optimizer/css/regular.css");
$APPLICATION->SetAdditionalCSS("/bitrix/themes/.default/ammina.optimizer/css/solid.css");
$APPLICATION->SetAdditionalCSS("/bitrix/themes/.default/ammina.optimizer/css/fontawesome.css");
\Bitrix\Main\Page\Asset::getInstance()->addString('<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />');
\Bitrix\Main\Page\Asset::getInstance()->addString('<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>');
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

set_time_limit(600);
$arCheckData = \Ammina\Optimizer\Core2\LibAvailable::doCheckLibrary();
//$arCheckData = unserialize(\COption::GetOptionString("ammina.optimizer", "libs_checked_data", ""));
$arCheckDataInfo = array(
	"bxoptions" => array(
		"TITLE" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_GROUP_BXOPTIONS_TITLE"),
		"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_GROUP_BXOPTIONS_DESCRIPTION"),
		"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_GROUP_BXOPTIONS_HELP"),
		"CHECK" => array(
			/*"optimize_css_files" => array(
				"NAME" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_OPTIMIZE_CSS_FILES_TITLE"),
				"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_OPTIMIZE_CSS_FILES_DESCRIPTION"),
				"TYPE" => "boolean",
				"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_OPTIMIZE_CSS_FILES_HELP"),
			),
			"optimize_js_files" => array(
				"NAME" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_OPTIMIZE_JS_FILES_TITLE"),
				"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_OPTIMIZE_JS_FILES_DESCRIPTION"),
				"TYPE" => "boolean",
				"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_OPTIMIZE_JS_FILES_HELP"),
			),
			"use_minified_assets" => array(
				"NAME" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_USE_MINIFIED_ASSETS_TITLE"),
				"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_USE_MINIFIED_ASSETS_DESCRIPTION"),
				"TYPE" => "boolean",
				"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_USE_MINIFIED_ASSETS_HELP"),
			),
			"move_js_to_body" => array(
				"NAME" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_MOVE_JS_TO_BODY_TITLE"),
				"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_MOVE_JS_TO_BODY_DESCRIPTION"),
				"TYPE" => "boolean",
				"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_MOVE_JS_TO_BODY_HELP"),
			),
			"compres_css_js_files" => array(
				"NAME" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_COMPRES_CSS_JS_FILES_TITLE"),
				"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_COMPRES_CSS_JS_FILES_DESCRIPTION"),
				"TYPE" => "boolean",
				"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_COMPRES_CSS_JS_FILES_HELP"),
			),*/
			"cdn" => array(
				"NAME" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_CDN_TITLE"),
				"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_CDN_DESCRIPTION"),
				"TYPE" => "boolean",
				"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_CDN_HELP"),
			),
			"composite" => array(
				"NAME" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_COMPOSITE_TITLE"),
				"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_COMPOSITE_DESCRIPTION"),
				"TYPE" => "boolean",
				"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_BXOPTION_COMPOSITE_HELP"),
			),
        )
    ),
	"packages" => array(
		"TITLE" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_GROUP_PACKAGES_TITLE"),
		"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_GROUP_PACKAGES_DESCRIPTION"),
		"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_GROUP_PACKAGES_HELP"),
		"CHECK" => array(
			"dom" => array(
				"NAME" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_DOM_TITLE"),
				"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_DOM_DESCRIPTION"),
				"TYPE" => "boolean",
				"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_DOM_HELP"),
			),
			"curl" => array(
				"NAME" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_CURL_TITLE"),
				"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_CURL_DESCRIPTION"),
				"TYPE" => "boolean",
				"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_CURL_HELP"),
			),
			"phpimagick" => array(
				"NAME" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_PHPIMAGICK_TITLE"),
				"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_PHPIMAGICK_DESCRIPTION"),
				"TYPE" => "boolean",
				"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_PHPIMAGICK_HELP"),
			),/*
			"phpimagickwebp" => array(
				"NAME" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_PHPIMAGICKWEBP_TITLE"),
				"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_PHPIMAGICKWEBP_DESCRIPTION"),
				"TYPE" => "boolean",
				"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_PHPIMAGICKWEBP_HELP"),
			),*/
			"phpgd" => array(
				"NAME" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_PHPGD_TITLE"),
				"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_PHPGD_DESCRIPTION"),
				"TYPE" => "boolean",
				"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_PHPGD_HELP"),
			),
			"jpegoptim" => array(
				"NAME" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_JPEGOPTIM_TITLE"),
				"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_JPEGOPTIM_DESCRIPTION"),
				"TYPE" => "boolean",
				"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_JPEGOPTIM_HELP"),
			),
			"optipng" => array(
				"NAME" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_OPTIPNG_TITLE"),
				"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_OPTIPNG_DESCRIPTION"),
				"TYPE" => "boolean",
				"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_OPTIPNG_HELP"),
			),
			"pngquant" => array(
				"NAME" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_PNGQUANT_TITLE"),
				"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_PNGQUANT_DESCRIPTION"),
				"TYPE" => "boolean",
				"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_PNGQUANT_HELP"),
			),
			"gifsicle" => array(
				"NAME" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_GIFSICLE_TITLE"),
				"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_GIFSICLE_DESCRIPTION"),
				"TYPE" => "boolean",
				"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_GIFSICLE_HELP"),
			),
			"cwebp" => array(
				"NAME" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_CWEBP_TITLE"),
				"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_CWEBP_DESCRIPTION"),
				"TYPE" => "boolean",
				"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_CWEBP_HELP"),
			),
		),
	),
	"npm" => array(
		"TITLE" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_GROUP_NPM_TITLE"),
		"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_GROUP_NPM_DESCRIPTION"),
		"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_GROUP_NPM_HELP"),
		"CHECK" => array(
			"svgo" => array(
				"NAME" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_SVGO_TITLE"),
				"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_SVGO_DESCRIPTION"),
				"TYPE" => "boolean",
				"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_PACKAGE_SVGO_HELP"),
			),
			/*"yuicompressor" => array(
				"NAME" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_NPM_YUICOMPRESSOR_TITLE"),
				"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_NPM_YUICOMPRESSOR_DESCRIPTION"),
				"TYPE" => "boolean",
				"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_NPM_YUICOMPRESSOR_HELP"),
			),
			"uglifycss" => array(
				"NAME" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_NPM_UGLIFYCSS_TITLE"),
				"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_NPM_UGLIFYCSS_DESCRIPTION"),
				"TYPE" => "boolean",
				"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_NPM_UGLIFYCSS_HELP"),
			),*/
			"uglifyjs" => array(
				"NAME" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_NPM_UGLIFYJS_TITLE"),
				"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_NPM_UGLIFYJS_DESCRIPTION"),
				"TYPE" => "boolean",
				"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_NPM_UGLIFYJS_HELP"),
			),
			"uglifyjs2" => array(
				"NAME" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_NPM_UGLIFYJS2_TITLE"),
				"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_NPM_UGLIFYJS2_DESCRIPTION"),
				"TYPE" => "boolean",
				"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_NPM_UGLIFYJS2_HELP"),
			),
			"terserjs" => array(
				"NAME" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_NPM_TERSERJS_TITLE"),
				"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_NPM_TERSERJS_DESCRIPTION"),
				"TYPE" => "boolean",
				"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_NPM_TERSERJS_HELP"),
			),
			"babelminify" => array(
				"NAME" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_NPM_BABELMINIFY_TITLE"),
				"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_NPM_BABELMINIFY_DESCRIPTION"),
				"TYPE" => "boolean",
				"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_NPM_BABELMINIFY_HELP"),
			),
		),
	),
	"other" => array(
		"TITLE" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_GROUP_OTHER_TITLE"),
		"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_GROUP_OTHER_DESCRIPTION"),
		"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_GROUP_OTHER_HELP"),
		"CHECK" => array(
			"googleapi" => array(
				"NAME" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_OTHER_GOOGLEAPI_TITLE"),
				"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_OTHER_GOOGLEAPI_DESCRIPTION"),
				"TYPE" => "boolean",
				"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_OTHER_GOOGLEAPI_HELP"),
			),
			"amminaapi" => array(
				"NAME" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_OTHER_AMMINAAPI_TITLE"),
				"DESCRIPTION" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_OTHER_AMMINAAPI_DESCRIPTION"),
				"TYPE" => "boolean",
				"HELP" => Loc::getMessage("AMMINA_OPTIMIZER_CHECK_ITEM_OTHER_AMMINAAPI_HELP"),
			),
		),
	),
);
?>
	<div class="aopt-checklib-area">
		<table class="aopt-table" width="100%" cellpadding="0" cellspacing="0">
			<tbody>
			<?
			foreach ($arCheckDataInfo as $groupId => $arGroup) {
				?>
				<tr class="aopt-table__header">
					<td colspan="2" class="aopt-table__header-group">
						<?= $arGroup['TITLE'] ?>
						<p class="aopt-table__description"><?= $arGroup['DESCRIPTION'] ?></p>
					</td>
					<td class="aopt-table__header-help">
						<?
						if (amopt_strlen($arGroup['HELP']) > 0) {
							?>
							<a href="javascript:void(0)" title="<?= $arGroup['TITLE'] ?>" class="aopt-table__header-help-link" data-fancybox="aopt-help" data-src="#aopt-help-<?= $groupId ?>" data-caption="<?= $arGroup['TITLE'] ?>"><i class="fa fa-question-circle"></i></a>
							<div class="aopt-table__help-content" id="aopt-help-<?= $groupId ?>"><?= $arGroup['HELP'] ?></div>
							<?
						}
						?>
					</td>
				</tr>
				<?
				foreach ($arGroup['CHECK'] as $itemId => $arItem) {
					?>
					<tr class="aopt-table__body">
						<td class="aopt-table__status aopt-table__status_<?= ($arCheckData[$groupId][$itemId] ? "ok" : "fail") ?>">
							<i class="fa <?= ($arCheckData[$groupId][$itemId] ? "fa-check-circle" : "fa-exclamation-circle") ?>"></i>
						</td>
						<td class="aopt-table__param"><?= $arItem['NAME'] ?>
							<p class="aopt-table__description"><?= $arItem['DESCRIPTION'] ?></p>
						</td>
						<td class="aopt-table__help">
							<?
							if (amopt_strlen($arItem['HELP']) > 0) {
								?>
								<a href="javascript:void(0)" title="<?= $arItem['NAME'] ?>" class="aopt-table__help-link" data-fancybox="aopt-help" data-src="#aopt-help-<?= $groupId ?>" data-caption="<?= $arItem['NAME'] ?>"><i class="fa fa-question-circle"></i></a>
								<div class="aopt-table__help-content" id="aopt-help-<?= $groupId ?>"><?= $arItem['HELP'] ?></div>
								<?
							}
							?>
						</td>
					</tr>
					<?
				}
			}
			?>
			</tbody>
		</table>
	</div>
<?

CAmminaOptimizer::showSupportForm();
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");