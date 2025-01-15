<?
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php';

use Bitrix\Main;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Entity;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Zverushki\Seofilter\configuration;
use Zverushki\Seofilter\Filter;
use Zverushki\Seofilter\Internals;
use Zverushki\Seofilter\Sections\Section;


$moduleId = 'zverushki.seofilter';
if(!in_array(Loader::includeSharewareModule($moduleId), [Loader::MODULE_INSTALLED, Loader::MODULE_DEMO])){
	throw new \Exception("Required module `{$moduleId}` was not found");
}

$seofilterModulePermissions = $APPLICATION->GetGroupRight($moduleId);

if ($seofilterModulePermissions < 'W')
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));

Loc::loadMessages(__FILE__);

$Context = Application::getInstance()->getContext();
$Request = $Context->getRequest();
$Server = $Context->getServer();
$documentRoot = Application::getDocumentRoot();

$id = (int)$Request->get('ID');
if($Request->get('action') == 'copy')
	$cid = (int)$Request->get('CID');
$showAdditionalTabs = false;
$seofilterSetting = array();

$errorMessage = '';
$isSaving = false;


$aTabs = array(
	array(
		'DIV' => 'edit1',
		'TAB' => Loc::getMessage('SEOFILTER_TABE1_TAB'),
		'ICON' => 'sale',
		'TITLE' => Loc::getMessage('SEOFILTER_TABE1_TITLE')
	)
);
if($id){
	$isList = false;
	if(Internals\LandingTable::getList([
				'filter' => ['SETTING_ID' => $id],
				'select' => ['ID'],
				'order'  => ['ID' => "ASC"],
                'limit'  => 1
			])->fetch())
		$isList = true;

	if($isList)
		$aTabs[] = array(
					'DIV' => 'edit2',
					'TAB' => Loc::getMessage('SEOFILTER_TABE2_TAB'),
					'ICON' => 'sale',
					'TITLE' => Loc::getMessage('SEOFILTER_TABE2_TITLE')
				);
}
$tabControl = new CAdminTabControl('tabControl', $aTabs);
$isCheckAll = Option::get($moduleId, 'view_checkall', 'N');

if ($Context->getServer()->getRequestMethod() == 'POST'
	&& ($Request->get('save') !== null || $Request->get('apply') !== null)
		&& $seofilterModulePermissions == 'W'
			&& check_bitrix_sessid()) {
	$checkCpu = Option::get($moduleId, 'check_cpu', 'N');
	$isSaving = true;
	$fields = array();

	foreach (Internals\SettingsTable::getMap() as $Field) {
		if ($Field instanceof \Bitrix\Main\Entity\ReferenceField || $Field instanceof \Bitrix\Main\Entity\ExpressionField)
			continue;

		if ((method_exists($Field, 'isAutocomplete') && $Field->isAutocomplete()) || strpos($Field->getColumnName(), 'TIMESTAMP') === 0)
			continue;

		$value = $Request->get($Field->getColumnName());
		if ($Field instanceof \Bitrix\Main\Entity\BooleanField && !$value)
			$value = 'N';

		if ($Field instanceof \Bitrix\Main\Entity\StringField && !$value)
			$value = trim($Field->getDefaultValue());

		$fields[$Field->getColumnName()] = $value;
	}
	if(!$fields['IBLOCK_ID'])
		$errorMessage .= Loc::getMessage('SEOFILTER_TABLE_ERROR_IBLOCK_EXIST') . '<br>';
	if($fields['PARAMS'])
		foreach ($fields['PARAMS'] as $k => $value) {
			if(empty($value))
				unset($fields['PARAMS'][$k]);
		}

	if($checkCpu != 'Y'){

		$fieldsSearch = [ 'IBLOCK_ID' => $fields['IBLOCK_ID'], 'SECTION_ID' => $fields['SECTION_ID'], 'URL_CPU' => $fields['URL_CPU'] ];
		Section::replace($fieldsSearch);
		if(Internals\SettingsTable::getList([
				'filter' => [ 'URL_CPU' => $fieldsSearch['URL_CPU'], 'ACTIVE' => 'Y', '!ID' => $id ],
				'select' => [ 'ID' ]
			])
				->fetch())
			$errorMessage .= Loc::getMessage('SEOFILTER_TABLE_ERROR_EXIST') . '<br>';
		else{
			$resSearch = Internals\SettingsTable::getList([
				'filter' => [ 'URL_CPU' => "%#SECTION_%", 'ACTIVE' => 'Y', '!ID' => $id ],
				'select' => [ 'ID', 'URL_CPU', 'IBLOCK_ID', 'SECTION_ID' ]
			]);
			while($asSearch = $resSearch->fetch()){
				Section::replace($asSearch);
				if($asSearch['URL_CPU'] == $fieldsSearch['URL_CPU'])
					$errorMessage .= Loc::getMessage('SEOFILTER_TABLE_ERROR_EXIST') . '<br>';
            }
        }
	}
	if($errorMessage === '') {
		if($fields['PARAMS'])
			ksort($fields['PARAMS']);
		if ($id > 0)
			$ResSetting = Internals\SettingsTable::update($id, $fields);
		else
			$ResSetting = Internals\SettingsTable::add($fields);
		if (!$ResSetting->isSuccess()) {
			foreach ($ResSetting->getErrors() as $Er)
				$errorMessage .= $Er->getMessage().'<br>';
		}else{
			if($ResSetting->getId() > 0){

				$fields = array('SETTING_ID' => $ResSetting->getId());
				foreach (Internals\SettingsSiteTable::getMap() as $Field) {
					if ($Field instanceof \Bitrix\Main\Entity\ReferenceField || $Field instanceof \Bitrix\Main\Entity\ExpressionField)
						continue;

					if ((method_exists($Field, 'isAutocomplete') && $Field->isAutocomplete()) || strpos($Field->getColumnName(), 'TIMESTAMP') === 0 || strpos($Field->getColumnName(), 'SETTING_ID') === 0)
						continue;

					$value = $Request->get($Field->getColumnName());

					if ($Field instanceof \Bitrix\Main\Entity\StringField && !$value)
						$value = $Field->getDefaultValue();

					$fields[$Field->getColumnName()] = $value;
				}
				$resSite = Internals\SettingsSiteTable::getList(array(
						'filter' => array('SETTING_ID' => $fields['SETTING_ID']),
						'select' => array('ID', 'SITE_ID')
					));
				while($arSite = $resSite->fetch()){
					if($fields['SITE_ID'][$arSite['SITE_ID']]){
						unset($fields['SITE_ID'][$arSite['SITE_ID']]);
					}else{
						Internals\SettingsSiteTable::delete($arSite['ID']);
					}
				}
				if($fields['SITE_ID'])
					foreach ($fields['SITE_ID'] as $key => $site) {
						$arSiteField = array(
							'SETTING_ID' => $fields['SETTING_ID'],
							'SITE_ID' => $site,
						);
						$Res = Internals\SettingsSiteTable::add($arSiteField);
						if (!$Res->isSuccess())
							foreach ($Res->getErrors() as $Er)
									$errorMessage .= $Er->getMessage().'<br>';

					}


				$fields = array('SETTING_ID' => $ResSetting->getId());
				foreach (Internals\SeotagTable::getMap() as $Field) {
					if ($Field instanceof \Bitrix\Main\Entity\ReferenceField || $Field instanceof \Bitrix\Main\Entity\ExpressionField)
						continue;

					if ((method_exists($Field, 'isAutocomplete') && $Field->isAutocomplete()) || strpos($Field->getColumnName(), 'TIMESTAMP') === 0 || strpos($Field->getColumnName(), 'SETTING_ID') === 0)
						continue;

					if($Field->getColumnName() == 'DESCRIPTION'){
						$value = $Request->get("SEO_".$Field->getColumnName());
					}else{
						$value = $Request->get($Field->getColumnName());
					}
					if ($Field instanceof \Bitrix\Main\Entity\BooleanField && !$value)
						$value = 'N';

					if ($Field instanceof \Bitrix\Main\Entity\StringField && !$value)
						$value = $Field->getDefaultValue();

					$fields[$Field->getColumnName()] = trim($value);
				}

				// mp($fields);die;
				$seotagClass = Internals\SeotagTable::getList(array(
						'filter' => array('SETTING_ID' => $fields['SETTING_ID']),
						'select' => array('ID')
					))
					->fetch();

				if ($seotagClass['ID'] > 0)
					$Res = Internals\SeotagTable::update($seotagClass['ID'], $fields);
				else
					$Res = Internals\SeotagTable::add($fields);

				if (!$Res->isSuccess()) {
					foreach ($Res->getErrors() as $Er)
						$errorMessage .= $Er->getMessage().'<br>';
				}

				$cacheManager = \Bitrix\Main\Application::getInstance()
							->getTaggedCache();
				$cacheManager->ClearByTag("zverushki_seofilter_cpu_all");

				Zverushki\Seofilter\Agent::addGenerateIndexPart($ResSetting->getId());
			}
		}
	}
	if ($errorMessage === '') {
		if (strlen($Request->get('apply')) > 0)
			LocalRedirect($moduleId.'_setting_edit.php?ID='.$ResSetting->getId().'&lang='.$Context->getLanguage().'&'.$tabControl->ActiveTabParam());
		else
			LocalRedirect($moduleId.'_settings.php?lang='.$Context->getLanguage());
	}else{
		$seofilterSetting['PARAMS'] = $_POST['PARAMS'];
    }
}

$listSite = array();
$rsSites = \CSite::GetList($bys = "ID", $orders = "asc");
while ($arSite = $rsSites->Fetch()) {
	$listSite[$arSite['LID']] = $arSite;
}
$arOptionPrice = configuration::getOption('price_active', '-');

$arIblockFilter = array();
if ($id > 0 || $cid > 0){
	$seofilterSetting =
		Internals\SettingsTable::getList(array(
			'filter' => array('ID' => $cid ? $cid : $id),
			'select' => array('*', 'SETTING')
		))
		->fetch();
	$seofilterSetting['SITE_ID'] = array();

		$resSite = Internals\SettingsSiteTable::getList(array(
				'filter' => array('SETTING_ID' => $seofilterSetting['ID']),
				'select' => array('ID', 'SITE_ID')
			));
		while($arSite = $resSite->fetch()){
			$seofilterSetting['SITE_ID'][$arSite['SITE_ID']] = $arSite['SITE_ID'];
		}

		$arIblockFilter['IBLOCK_ID'] = $seofilterSetting['IBLOCK_ID'];
}

if (Loader::includeModule('catalog')) {
	$Db = \CCatalog::getList(array(), $arIblockFilter);
	while (($a = $Db->fetch()) !== false){
		if($a['PRODUCT_IBLOCK_ID'] == 0)
			$catalogIb[$a['IBLOCK_ID']] = $a['NAME'];
	}
	if($catalogIb)
		asort($catalogIb);
}elseif (Loader::includeModule('iblock') && empty($catalogIb)) {
	$res = CIBlock::GetList(
	    Array(),
	    Array(
	        // 'TYPE'=>'catalog',
	        'ACTIVE'=>'Y',
	        "CNT_ACTIVE"=>"Y"
	    ), true
	);
	while($a = $res->Fetch())
		$catalogIb[$a['ID']] = $a['NAME'];

}
$iblockId =  $Request->get('IBLOCK_ID') ? $Request->get('IBLOCK_ID') : $seofilterSetting['IBLOCK_ID'];
$sectionId =  intval($Request->get('SECTION_ID') ? intval($Request->get('SECTION_ID')) : $seofilterSetting['SECTION_ID']);

$arResult = [];
$sectionFields = [];
if(!empty($iblockId)){
	$sectionIb =  array();
	$tree = CIBlockSection::GetTreeList(
	    $arFilter=Array('IBLOCK_ID' => $iblockId),
	    $arSelect=Array('ID', 'NAME', 'DEPTH_LEVEL')
	);
	while($section = $tree->GetNext()) {
		$sectionIb[$section['ID']] = str_repeat(". ", $section['DEPTH_LEVEL']-1).$section['NAME'];
	}

	$arSection = CIBlockSection::GetList(['ID' => 'ASC'], ['IBLOCK_ID' => $iblockId, 'ID' => $sectionId], false, ['SECTION_PAGE_URL'], ['nTopCount' => 1])->GetNext(false, false);
	$sectionFields = Section::initFields($iblockId);

	$variable = new Filter\variable();
	$variable->setIblockId($iblockId);

	if($sectionId){
		$variable->setSectionId($sectionId);
	}

	$arResult = $variable->getVariable();
}
if($isList ){
	ob_start();
	require_once(__DIR__."/list.php");
	$linkContent = ob_get_contents();
	ob_end_clean();
}

$APPLICATION->SetTitle($id > 0 ? Loc::getMessage('SEOFILTER_EDIT_RECORD', array('#ID#' => $id)) : Loc::getMessage('SEOFILTER_NEW_RECORD'));

require $documentRoot.'/bitrix/modules/main/include/prolog_admin_after.php';
 $arParamsEdit = [
		    'bUseOnlyDefinedStyles' => true,
		    'bFromTextarea' => true,
		    'bDisplay' => true,
		    'bWithoutPHP' => true,
		    'arTaskbars' => ["BXPropertiesTaskbar"],
		    'height' => '450',
		    'site' => LANG,
		    'width' => '100%',
		    'arAdditionalParams' => ['hideTypeSelector' => true],
		    'setFocusAfterShow' => false
		];
$aMenu = array(
	array(
		'TEXT' => Loc::getMessage('SEOFILTER_MENU_LIST_TEXT'),
		'LINK' => '/bitrix/admin/'.$moduleId.'_settings.php?lang='.$Context->getLanguage(),
		'ICON' => 'btn_list'
	)
);

if ($id > 0 && $seofilterModulePermissions >= 'W') {
	$aMenu[] = array('SEPARATOR' => 'Y');

	$aMenu[] = array(
		'TEXT' => Loc::getMessage('SEOFILTER_MENU_ACTION_TEXT'),
		'TITLE' => Loc::getMessage('SEOFILTER_MENU_ACTION_TEXT'),
		'MENU' => array(
			array(
				'TEXT' => Loc::getMessage('SEOFILTER_MENU_ADDNEW_TEXT'),
				'LINK' => '/bitrix/admin/'.$moduleId.'_setting_edit.php?lang='.$Context->getLanguage(),
				'ICON' => 'edit'
			),
			array(
				'TEXT' => Loc::getMessage('SEOFILTER_MENU_COPY_TEXT'),
				'LINK' => '/bitrix/admin/'.$moduleId.'_setting_edit.php?CID='.$id.'&action=copy&lang='.$Context->getLanguage(),
				'ICON' => 'copy'
			),
			array(
				'TEXT' => Loc::getMessage('SEOFILTER_MENU_DELETE_TEXT'),
				'LINK' => 'javascript:if(confirm("'.Loc::getMessage('SEOFILTER_MENU_DELETE_CONFIRMJS').'")) window.location="/bitrix/admin/'.$moduleId.'_settings.php?action=delete&ID='.$id.'&lang='.$Context->getLanguage().'&'.bitrix_sessid_get().'#tb";',
				'WARNING' => 'Y',
				'ICON' => 'delete'
			)
		),
		'ICON' => 'btn_new'
	);
}

$ContextMenu = new CAdminContextMenu($aMenu);
$ContextMenu->Show();

if($iblockId && !empty($arResult['ITEMS'])){
	$arParams = array("replace_space"=>"-","replace_other"=>"-");

	$arPop = array();
	$arPop['CPU']['iblock'] = array(
	        'TEXT' => $catalogIb[$iblockId],
	        'TITLE' => $catalogIb[$iblockId],
	    );
	$arPop['SEO_DESCRIPTION_TOP']['iblock'] = array(
	        'TEXT' => $catalogIb[$iblockId],
	        'TITLE' => $catalogIb[$iblockId],
	    );
	$arPop['SEO_DESCRIPTION']['iblock'] = array(
	        'TEXT' => $catalogIb[$iblockId],
	        'TITLE' => $catalogIb[$iblockId],
	    );
	$arPop['TAG_NAME']['iblock'] = array(
	        'TEXT' => $catalogIb[$iblockId],
	        'TITLE' => $catalogIb[$iblockId],
	    );
	$arPop['TAG_SECTION_NAME']['iblock'] = array(
	        'TEXT' => $catalogIb[$iblockId],
	        'TITLE' => $catalogIb[$iblockId],
	    );
	$arPop['PAGE_TITLE']['iblock'] = array(
	        'TEXT' => $catalogIb[$iblockId],
	        'TITLE' => $catalogIb[$iblockId],
	    );
	$arPop['META_TITLE']['iblock'] = array(
	        'TEXT' => $catalogIb[$iblockId],
	        'TITLE' => $catalogIb[$iblockId],
	    );
	$arPop['META_KEYWORDS']['iblock'] = array(
	        'TEXT' => $catalogIb[$iblockId],
	        'TITLE' => $catalogIb[$iblockId],
	    );
	$arPop['META_DESCRIPTION']['iblock'] = array(
	        'TEXT' => $catalogIb[$iblockId],
	        'TITLE' => $catalogIb[$iblockId],
	    );


	foreach($arResult['PROPERTY_ID_LIST'] as $pid){
		if($pitem = $arResult['ITEMS'][$pid]){
			if(empty($pitem['VALUES']))
				continue;
			if(
				$pitem['DISPLAY_TYPE'] != "G"
				&& $pitem['DISPLAY_TYPE'] != "F"
				&& $pitem['DISPLAY_TYPE'] != "H"
				&& $pitem['DISPLAY_TYPE'] != "K"
				&& $pitem['DISPLAY_TYPE'] != "P"
				&& $pitem['DISPLAY_TYPE'] != "R"
			)
				continue;
			$arPop['CPU']['iblock']['MENU'][$pid] = array(
		        'TEXT' => $pitem['NAME'],
		        'TITLE' => "#{$pitem['CODE']}# {$pitem['NAME']}",
		        'ONCLICK' => "__SetUrlVar('#PROP_{$pitem['CODE']}#', 'mnu_URL_CPU', 'URL_CPU')"
			);
			$arPop['SEO_DESCRIPTION_TOP']['iblock']['MENU'][$pid] = array(
		        'TEXT' => $pitem['NAME'],
		        'TITLE' => "#{$pitem['CODE']}# {$pitem['NAME']}",
		        'ONCLICK' => "__SetUrlVarTD('#PROP_{$pitem['CODE']}#', 'mnu_SEO_DESCRIPTION_TOP', 'SEO_DESCRIPTION_TOP')"
			);
			$arPop['SEO_DESCRIPTION']['iblock']['MENU'][$pid] = array(
		        'TEXT' => $pitem['NAME'],
		        'TITLE' => "#{$pitem['CODE']}# {$pitem['NAME']}",
		        'ONCLICK' => "__SetUrlVarTD('#PROP_{$pitem['CODE']}#', 'mnu_SEO_DESCRIPTION', 'SEO_DESCRIPTION')"
			);
			$arPop['TAG_NAME']['iblock']['MENU'][$pid] = array(
		        'TEXT' => $pitem['NAME'],
		        'TITLE' => "#{$pitem['CODE']}# {$pitem['NAME']}",
		        'ONCLICK' => "__SetUrlVar('#PROP_{$pitem['CODE']}#', 'mnu_TAG_NAME', 'TAG_NAME')"
			);
			$arPop['TAG_SECTION_NAME']['iblock']['MENU'][$pid] = array(
		        'TEXT' => $pitem['NAME'],
		        'TITLE' => "#{$pitem['CODE']}# {$pitem['NAME']}",
		        'ONCLICK' => "__SetUrlVar('#PROP_{$pitem['CODE']}#', 'mnu_TAG_SECTION_NAME', 'TAG_SECTION_NAME')"
			);
			$arPop['PAGE_TITLE']['iblock']['MENU'][$pid] = array(
		        'TEXT' => $pitem['NAME'],
		        'TITLE' => "#{$pitem['CODE']}# {$pitem['NAME']}",
		        'ONCLICK' => "__SetUrlVar('#PROP_{$pitem['CODE']}#', 'mnu_PAGE_TITLE', 'PAGE_TITLE')"
			);
			$arPop['META_TITLE']['iblock']['MENU'][$pid] = array(
		        'TEXT' => $pitem['NAME'],
		        'TITLE' => "#{$pitem['CODE']}# {$pitem['NAME']}",
		        'ONCLICK' => "__SetUrlVar('#PROP_{$pitem['CODE']}#', 'mnu_META_TITLE', 'META_TITLE')"
			);
			$arPop['META_KEYWORDS']['iblock']['MENU'][$pid] = array(
		        'TEXT' => $pitem['NAME'],
		        'TITLE' => "#{$pitem['CODE']}# {$pitem['NAME']}",
		        'ONCLICK' => "__SetUrlVar('#PROP_{$pitem['CODE']}#', 'mnu_META_KEYWORDS', 'META_KEYWORDS')"
			);
			$arPop['META_DESCRIPTION']['iblock']['MENU'][$pid] = array(
		        'TEXT' => $pitem['NAME'],
		        'TITLE' => "#{$pitem['CODE']}# {$pitem['NAME']}",
		        'ONCLICK' => "__SetUrlVar('#PROP_{$pitem['CODE']}#', 'mnu_META_DESCRIPTION', 'META_DESCRIPTION')"
			);
			/*foreach($pitem['VALUES'] as $prop){
				$trans = Cutil::translit($prop['VALUE'], "ru", $arParams);
				$arPop['iblock']['MENU'][$pid]["MENU"][] = array(
			        'TEXT' => $prop['VALUE'],
			        'TITLE' => $prop['VALUE'],
			        'ONCLICK' => "__SetUrlVar('{$trans}', 'mnu_URL_CPU', 'URL_CPU')"
				);
			}*/
		}
	}
	$offerProp = array();
	foreach($arResult['SKU_PROPERTY_ID_LIST'] as $pid){
		if($pitem = $arResult['ITEMS'][$pid]){
			if(empty($pitem['VALUES']))
				continue;
			if(
				$pitem['DISPLAY_TYPE'] != "G"
				&& $pitem['DISPLAY_TYPE'] != "F"
				&& $pitem['DISPLAY_TYPE'] != "H"
				&& $pitem['DISPLAY_TYPE'] != "K"
				&& $pitem['DISPLAY_TYPE'] != "P"
				&& $pitem['DISPLAY_TYPE'] != "R"
			)
				continue;
			$offerProp['CPU'][] = array(
		        'TEXT' => $pitem['NAME'],
		        'TITLE' => "#{$pitem['CODE']}# {$pitem['NAME']}",
		        'ONCLICK' => "__SetUrlVar('#PROP_{$pitem['CODE']}#', 'mnu_URL_CPU', 'URL_CPU')"
			);
			$offerProp['SEO_DESCRIPTION_TOP'][] = array(
		        'TEXT' => $pitem['NAME'],
		        'TITLE' => "#{$pitem['CODE']}# {$pitem['NAME']}",
		        'ONCLICK' => "__SetUrlVarTD('#PROP_{$pitem['CODE']}#', 'mnu_SEO_DESCRIPTION_TOP', 'SEO_DESCRIPTION_TOP')"
			);
			$offerProp['SEO_DESCRIPTION'][] = array(
		        'TEXT' => $pitem['NAME'],
		        'TITLE' => "#{$pitem['CODE']}# {$pitem['NAME']}",
		        'ONCLICK' => "__SetUrlVarTD('#PROP_{$pitem['CODE']}#', 'mnu_SEO_DESCRIPTION', 'SEO_DESCRIPTION')"
			);
			$offerProp['TAG_NAME'][] = array(
		        'TEXT' => $pitem['NAME'],
		        'TITLE' => "#{$pitem['CODE']}# {$pitem['NAME']}",
		        'ONCLICK' => "__SetUrlVar('#PROP_{$pitem['CODE']}#', 'mnu_TAG_NAME', 'TAG_NAME')"
			);
			$offerProp['TAG_SECTION_NAME'][] = array(
		        'TEXT' => $pitem['NAME'],
		        'TITLE' => "#{$pitem['CODE']}# {$pitem['NAME']}",
		        'ONCLICK' => "__SetUrlVar('#PROP_{$pitem['CODE']}#', 'mnu_TAG_SECTION_NAME', 'TAG_SECTION_NAME')"
			);
			$offerProp['PAGE_TITLE'][] = array(
		        'TEXT' => $pitem['NAME'],
		        'TITLE' => "#{$pitem['CODE']}# {$pitem['NAME']}",
		        'ONCLICK' => "__SetUrlVar('#PROP_{$pitem['CODE']}#', 'mnu_PAGE_TITLE', 'PAGE_TITLE')"
			);
			$offerProp['META_TITLE'][] = array(
		        'TEXT' => $pitem['NAME'],
		        'TITLE' => "#{$pitem['CODE']}# {$pitem['NAME']}",
		        'ONCLICK' => "__SetUrlVar('#PROP_{$pitem['CODE']}#', 'mnu_META_TITLE', 'META_TITLE')"
			);
			$offerProp['META_KEYWORDS'][] = array(
		        'TEXT' => $pitem['NAME'],
		        'TITLE' => "#{$pitem['CODE']}# {$pitem['NAME']}",
		        'ONCLICK' => "__SetUrlVar('#PROP_{$pitem['CODE']}#', 'mnu_META_KEYWORDS', 'META_KEYWORDS')"
			);
			$offerProp['META_DESCRIPTION'][] = array(
		        'TEXT' => $pitem['NAME'],
		        'TITLE' => "#{$pitem['CODE']}# {$pitem['NAME']}",
		        'ONCLICK' => "__SetUrlVar('#PROP_{$pitem['CODE']}#', 'mnu_META_DESCRIPTION', 'META_DESCRIPTION')"
			);
		}
	}
	if(!empty($offerProp['CPU'])){
		$arPop['CPU']['offer'] = array(
		        'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_OFFER"),
		        'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_OFFER"),
		        'MENU' => $offerProp['CPU']
		    );
		$arPop['SEO_DESCRIPTION_TOP']['offer'] = array(
		        'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_OFFER"),
		        'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_OFFER"),
		        'MENU' => $offerProp['SEO_DESCRIPTION_TOP']
		    );
		$arPop['SEO_DESCRIPTION']['offer'] = array(
		        'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_OFFER"),
		        'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_OFFER"),
		        'MENU' => $offerProp['SEO_DESCRIPTION']
		    );
		$arPop['TAG_NAME']['offer'] = array(
		        'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_OFFER"),
		        'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_OFFER"),
		        'MENU' => $offerProp['TAG_NAME']
		    );
		$arPop['TAG_SECTION_NAME']['offer'] = array(
		        'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_OFFER"),
		        'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_OFFER"),
		        'MENU' => $offerProp['TAG_SECTION_NAME']
		    );
		$arPop['PAGE_TITLE']['offer'] = array(
		        'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_OFFER"),
		        'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_OFFER"),
		        'MENU' => $offerProp['PAGE_TITLE']
		    );
		$arPop['META_TITLE']['offer'] = array(
		        'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_OFFER"),
		        'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_OFFER"),
		        'MENU' => $offerProp['META_TITLE']
		    );
		$arPop['META_KEYWORDS']['offer'] = array(
		        'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_OFFER"),
		        'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_OFFER"),
		        'MENU' => $offerProp['META_KEYWORDS']
		    );
		$arPop['META_DESCRIPTION']['offer'] = array(
		        'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_OFFER"),
		        'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_OFFER"),
		        'MENU' => $offerProp['META_DESCRIPTION']
		    );

	}
	if($sectionFields){
		$sectionProp = array();
		$fielsSectionMain = section::initMainFields();
		unset($fielsSectionMain['NAME']);
		foreach($fielsSectionMain as $code => $name)
            $sectionProp['CPU'][] = array(
                'TEXT' => $name,
                'TITLE' => "#{$code}# {$name}",
                'ONCLICK' => "__SetUrlVar('#SECTION_{$code}#', 'mnu_URL_CPU', 'URL_CPU')"
            );

		foreach($sectionFields as $code => $name){
				$sectionProp['SEO_DESCRIPTION_TOP'][] = array(
					'TEXT' => $name,
					'TITLE' => "#{$code}# {$name}",
					'ONCLICK' => "__SetUrlVarTD('#SECTION_{$code}#', 'mnu_SEO_DESCRIPTION_TOP', 'SEO_DESCRIPTION_TOP')"
				);
				$sectionProp['SEO_DESCRIPTION'][] = array(
					'TEXT' => $name,
					'TITLE' => "#{$code}# {$name}",
					'ONCLICK' => "__SetUrlVarTD('#SECTION_{$code}#', 'mnu_SEO_DESCRIPTION', 'SEO_DESCRIPTION')"
				);
				$sectionProp['TAG_NAME'][] = array(
					'TEXT' => $name,
					'TITLE' => "#{$code}# {$name}",
					'ONCLICK' => "__SetUrlVar('#SECTION_{$code}#', 'mnu_TAG_NAME', 'TAG_NAME')"
				);
				$sectionProp['TAG_SECTION_NAME'][] = array(
					'TEXT' => $name,
					'TITLE' => "#{$code}# {$name}",
					'ONCLICK' => "__SetUrlVar('#SECTION_{$code}#', 'mnu_TAG_SECTION_NAME', 'TAG_SECTION_NAME')"
				);
				$sectionProp['PAGE_TITLE'][] = array(
					'TEXT' => $name,
					'TITLE' => "#{$code}# {$name}",
					'ONCLICK' => "__SetUrlVar('#SECTION_{$code}#', 'mnu_PAGE_TITLE', 'PAGE_TITLE')"
				);
				$sectionProp['META_TITLE'][] = array(
					'TEXT' => $name,
					'TITLE' => "#{$code}# {$name}",
					'ONCLICK' => "__SetUrlVar('#SECTION_{$code}#', 'mnu_META_TITLE', 'META_TITLE')"
				);
				$sectionProp['META_KEYWORDS'][] = array(
					'TEXT' => $name,
					'TITLE' => "#{$code}# {$name}",
					'ONCLICK' => "__SetUrlVar('#SECTION_{$code}#', 'mnu_META_KEYWORDS', 'META_KEYWORDS')"
				);
				$sectionProp['META_DESCRIPTION'][] = array(
					'TEXT' => $name,
					'TITLE' => "#{$code}# {$name}",
					'ONCLICK' => "__SetUrlVar('#SECTION_{$code}#', 'mnu_META_DESCRIPTION', 'META_DESCRIPTION')"
				);
		}

		$arPop['CPU']['section'] = array(
			'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_SECTION"),
			'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_SECTION"),
			'MENU' => $sectionProp['CPU']
		);
		$arPop['SEO_DESCRIPTION_TOP']['section'] = array(
			'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_SECTION"),
			'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_SECTION"),
			'MENU' => $sectionProp['SEO_DESCRIPTION_TOP']
		);
		$arPop['SEO_DESCRIPTION']['section'] = array(
			'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_SECTION"),
			'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_SECTION"),
			'MENU' => $sectionProp['SEO_DESCRIPTION']
		);
		$arPop['TAG_NAME']['section'] = array(
			'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_SECTION"),
			'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_SECTION"),
			'MENU' => $sectionProp['TAG_NAME']
		);
		$arPop['TAG_SECTION_NAME']['section'] = array(
			'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_SECTION"),
			'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_SECTION"),
			'MENU' => $sectionProp['TAG_SECTION_NAME']
		);
		$arPop['PAGE_TITLE']['section'] = array(
			'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_SECTION"),
			'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_SECTION"),
			'MENU' => $sectionProp['PAGE_TITLE']
		);
		$arPop['META_TITLE']['section'] = array(
			'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_SECTION"),
			'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_SECTION"),
			'MENU' => $sectionProp['META_TITLE']
		);
		$arPop['META_KEYWORDS']['section'] = array(
			'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_SECTION"),
			'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_SECTION"),
			'MENU' => $sectionProp['META_KEYWORDS']
		);
		$arPop['META_DESCRIPTION']['section'] = array(
			'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_SECTION"),
			'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_SECTION"),
			'MENU' => $sectionProp['META_DESCRIPTION']
		);
	}

    $dopProp = array();
	foreach($arOptionPrice as $priceName)
	{
		/*$dopProp['CPU']['price']['TEXT'] = Loc::GetMessage("SEOFILTER_TABLE_PRICE");
        $dopProp['CPU']['price']['TITLE'] = Loc::GetMessage("SEOFILTER_TABLE_PRICE");

		$dopProp['CPU']['price']['MENU'][] = array(
			'TEXT' => Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE').' '.$priceName.' '.Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE_MIN'),
			'TITLE' => "#VAR_MIN_{$pitem['CODE']}# {$priceName}",
			'ONCLICK' => "__SetUrlVar('#VAR_MIN_{$pitem['CODE']}#', 'mnu_URL_CPU', 'URL_CPU')"
		);
		$dopProp['CPU']['price']['MENU'][] = array(
			'TEXT' => Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE').' '.$priceName.' '.Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE_MAX'),
			'TITLE' => "#VAR_MIN_{$pitem['CODE']}# {$priceName}",
			'ONCLICK' => "__SetUrlVar('#VAR_MAX_{$pitem['CODE']}#', 'mnu_URL_CPU', 'URL_CPU')"
		);*/
		$dopProp['SEO_DESCRIPTION_TOP']['price']['TEXT'] = Loc::GetMessage("SEOFILTER_TABLE_PRICE");
		$dopProp['SEO_DESCRIPTION_TOP']['price']['TITLE'] = Loc::GetMessage("SEOFILTER_TABLE_PRICE");
		$dopProp['SEO_DESCRIPTION_TOP']['price']['MENU'][] = array(
			'TEXT' => Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE').' '.$priceName.' '.Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE_MIN'),
			'TITLE' => "#VAR_{$pitem['CODE']}# {$priceName}",
			'ONCLICK' => "__SetUrlVarTD('#VAR_MIN_{$priceName}#', 'mnu_SEO_DESCRIPTION_TOP', 'SEO_DESCRIPTION_TOP')"
		);
		$dopProp['SEO_DESCRIPTION_TOP']['price']['MENU'][] = array(
			'TEXT' => Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE').' '.$priceName.' '.Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE_MAX'),
			'TITLE' => "#VAR_{$priceName}# {$priceName}",
			'ONCLICK' => "__SetUrlVarTD('#VAR_MAX_{$priceName}#', 'mnu_SEO_DESCRIPTION_TOP', 'SEO_DESCRIPTION_TOP')"
		);
		$dopProp['SEO_DESCRIPTION']['price']['TEXT'] = Loc::GetMessage("SEOFILTER_TABLE_PRICE");
		$dopProp['SEO_DESCRIPTION']['price']['TITLE'] = Loc::GetMessage("SEOFILTER_TABLE_PRICE");
		$dopProp['SEO_DESCRIPTION']['price']['MENU'][] = array(
			'TEXT' => Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE').' '.$priceName.' '.Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE_MIN'),
			'TITLE' => "#VAR_MIN_{$priceName}# {$priceName}",
			'ONCLICK' => "__SetUrlVarTD('#VAR_MIN_{$priceName}#', 'mnu_SEO_DESCRIPTION', 'SEO_DESCRIPTION')"
		);
		$dopProp['SEO_DESCRIPTION']['price']['MENU'][] = array(
			'TEXT' => Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE').' '.$priceName.' '.Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE_MAX'),
			'TITLE' => "#VAR_MIN_{$priceName}# {$priceName}",
			'ONCLICK' => "__SetUrlVarTD('#VAR_MAX_{$priceName}#', 'mnu_SEO_DESCRIPTION', 'SEO_DESCRIPTION')"
		);
		$dopProp['TAG_NAME']['price']['TEXT'] = Loc::GetMessage("SEOFILTER_TABLE_PRICE");
		$dopProp['TAG_NAME']['price']['TITLE'] = Loc::GetMessage("SEOFILTER_TABLE_PRICE");
		$dopProp['TAG_NAME']['price']['MENU'][] = array(
			'TEXT' => Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE').' '.$priceName.' '.Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE_MIN'),
			'TITLE' => "#VAR_MIN_{$priceName}# {$priceName}",
			'ONCLICK' => "__SetUrlVar('#VAR_MIN_{$priceName}#', 'mnu_TAG_NAME', 'TAG_NAME')"
		);
		$dopProp['TAG_NAME']['price']['MENU'][] = array(
			'TEXT' => Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE').' '.$priceName.' '.Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE_MAX'),
			'TITLE' => "#VAR_MIN_{$priceName}# {$priceName}",
			'ONCLICK' => "__SetUrlVar('#VAR_MAX_{$priceName}#', 'mnu_TAG_NAME', 'TAG_NAME')"
		);
		$dopProp['TAG_SECTION_NAME']['price']['TEXT'] = Loc::GetMessage("SEOFILTER_TABLE_PRICE");
		$dopProp['TAG_SECTION_NAME']['price']['TITLE'] = Loc::GetMessage("SEOFILTER_TABLE_PRICE");
		$dopProp['TAG_SECTION_NAME']['price']['MENU'][] = array(
			'TEXT' => Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE').' '.$priceName.' '.Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE_MIN'),
			'TITLE' => "#VAR_MIN_{$priceName}# {$priceName}",
			'ONCLICK' => "__SetUrlVar('#VAR_MIN_{$priceName}#', 'mnu_TAG_SECTION_NAME', 'TAG_SECTION_NAME')"
		);
		$dopProp['TAG_SECTION_NAME']['price']['MENU'][] = array(
			'TEXT' => Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE').' '.$priceName.' '.Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE_MAX'),
			'TITLE' => "#VAR_MIN_{$priceName}# {$priceName}",
			'ONCLICK' => "__SetUrlVar('#VAR_MAX_{$priceName}#', 'mnu_TAG_SECTION_NAME', 'TAG_SECTION_NAME')"
		);
		$dopProp['PAGE_TITLE']['price']['TEXT'] = Loc::GetMessage("SEOFILTER_TABLE_PRICE");
		$dopProp['PAGE_TITLE']['price']['TITLE'] = Loc::GetMessage("SEOFILTER_TABLE_PRICE");
		$dopProp['PAGE_TITLE']['price']['MENU'][] = array(
			'TEXT' => Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE').' '.$priceName.' '.Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE_MIN'),
			'TITLE' => "#VAR_MIN_{$priceName}# {$priceName}",
			'ONCLICK' => "__SetUrlVar('#VAR_MIN_{$priceName}#', 'mnu_PAGE_TITLE', 'PAGE_TITLE')"
		);
		$dopProp['PAGE_TITLE']['price']['MENU'][] = array(
			'TEXT' => Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE').' '.$priceName.' '.Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE_MAX'),
			'TITLE' => "#VAR_MIN_{$priceName}# {$priceName}",
			'ONCLICK' => "__SetUrlVar('#VAR_MAX_{$priceName}#', 'mnu_PAGE_TITLE', 'PAGE_TITLE')"
		);
		$dopProp['META_TITLE']['price']['TEXT'] = Loc::GetMessage("SEOFILTER_TABLE_PRICE");
		$dopProp['META_TITLE']['price']['TITLE'] = Loc::GetMessage("SEOFILTER_TABLE_PRICE");
		$dopProp['META_TITLE']['price']['MENU'][] = array(
			'TEXT' => Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE').' '.$priceName.' '.Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE_MIN'),
			'TITLE' => "#VAR_MIN_{$priceName}# {$priceName}",
			'ONCLICK' => "__SetUrlVar('#VAR_MIN_{$priceName}#', 'mnu_META_TITLE', 'META_TITLE')"
		);
		$dopProp['META_TITLE']['price']['MENU'][] = array(
			'TEXT' => Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE').' '.$priceName.' '.Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE_MAX'),
			'TITLE' => "#VAR_MIN_{$priceName}# {$priceName}",
			'ONCLICK' => "__SetUrlVar('#VAR_MAX_{$priceName}#', 'mnu_META_TITLE', 'META_TITLE')"
		);
		$dopProp['META_KEYWORDS']['price']['TEXT'] = Loc::GetMessage("SEOFILTER_TABLE_PRICE");
		$dopProp['META_KEYWORDS']['price']['TITLE'] = Loc::GetMessage("SEOFILTER_TABLE_PRICE");
		$dopProp['META_KEYWORDS']['price']['MENU'][] = array(
			'TEXT' => Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE').' '.$priceName.' '.Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE_MIN'),
			'TITLE' => "#VAR_MIN_{$priceName}# {$priceName}",
			'ONCLICK' => "__SetUrlVar('#VAR_MIN_{$priceName}#', 'mnu_META_KEYWORDS', 'META_KEYWORDS')"
		);
		$dopProp['META_KEYWORDS']['price']['MENU'][] = array(
			'TEXT' => Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE').' '.$priceName.' '.Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE_MAX'),
			'TITLE' => "#VAR_MIN_{$priceName}# {$priceName}",
			'ONCLICK' => "__SetUrlVar('#VAR_MAX_{$priceName}#', 'mnu_META_KEYWORDS', 'META_KEYWORDS')"
		);
		$dopProp['META_DESCRIPTION']['price']['TEXT'] = Loc::GetMessage("SEOFILTER_TABLE_PRICE");
		$dopProp['META_DESCRIPTION']['price']['TITLE'] = Loc::GetMessage("SEOFILTER_TABLE_PRICE");
		$dopProp['META_DESCRIPTION']['price']['MENU'][] = array(
			'TEXT' => Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE').' '.$priceName.' '.Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE_MIN'),
			'TITLE' => "#VAR_MIN_{$priceName}# {$priceName}",
			'ONCLICK' => "__SetUrlVar('#VAR_MIN_{$priceName}#', 'mnu_META_DESCRIPTION', 'META_DESCRIPTION')"
		);
		$dopProp['META_DESCRIPTION']['price']['MENU'][] = array(
			'TEXT' => Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE').' '.$priceName.' '.Loc::GetMessage('SEOFILTER_TABLE_TYPE_PRICE_MAX'),
			'TITLE' => "#VAR_MIN_{$priceName}# {$priceName}",
			'ONCLICK' => "__SetUrlVar('#VAR_MAX_{$priceName}#', 'mnu_META_DESCRIPTION', 'META_DESCRIPTION')"
		);
	}
	foreach(['PAGEN' => Loc::GetMessage('SEOFILTER_TABLE_IBLOCK_ID_DOP_PAGEN')] as $code => $name){
        $dopProp['PAGE_TITLE'][] = array(
            'TEXT' => $name,
            'TITLE' => "#{$code}# {$name}",
            'ONCLICK' => "__SetUrlVar('#DOP_{$code}#', 'mnu_PAGE_TITLE', 'PAGE_TITLE')"
        );
        $dopProp['META_TITLE'][] = array(
            'TEXT' => $name,
            'TITLE' => "#{$code}# {$name}",
            'ONCLICK' => "__SetUrlVar('#DOP_{$code}#', 'mnu_META_TITLE', 'META_TITLE')"
        );
        $dopProp['META_DESCRIPTION'][] = array(
            'TEXT' => $name,
            'TITLE' => "#{$code}# {$name}",
            'ONCLICK' => "__SetUrlVar('#DOP_{$code}#', 'mnu_META_DESCRIPTION', 'META_DESCRIPTION')"
        );
    }

	/*$arPop['CPU']['dop'] = array(
		'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_DOP"),
		'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_DOP"),
		'MENU' => $dopProp['CPU']
	);*/
	$arPop['SEO_DESCRIPTION_TOP']['dop'] = array(
		'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_DOP"),
		'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_DOP"),
		'MENU' => $dopProp['SEO_DESCRIPTION_TOP']
	);
	$arPop['SEO_DESCRIPTION']['dop'] = array(
		'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_DOP"),
		'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_DOP"),
		'MENU' => $dopProp['SEO_DESCRIPTION']
	);
	$arPop['TAG_NAME']['dop'] = array(
		'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_DOP"),
		'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_DOP"),
		'MENU' => $dopProp['TAG_NAME']
	);
	$arPop['TAG_SECTION_NAME']['dop'] = array(
		'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_DOP"),
		'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_DOP"),
		'MENU' => $dopProp['TAG_SECTION_NAME']
	);
	$arPop['PAGE_TITLE']['dop'] = array(
		'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_DOP"),
		'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_DOP"),
		'MENU' => $dopProp['PAGE_TITLE']
	);
	$arPop['META_TITLE']['dop'] = array(
		'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_DOP"),
		'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_DOP"),
		'MENU' => $dopProp['META_TITLE']
	);
	$arPop['META_KEYWORDS']['dop'] = array(
		'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_DOP"),
		'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_DOP"),
		'MENU' => $dopProp['META_KEYWORDS']
	);
	$arPop['META_DESCRIPTION']['dop'] = array(
		'TEXT' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_DOP"),
		'TITLE' => Loc::GetMessage("SEOFILTER_TABLE_IBLOCK_ID_DOP"),
		'MENU' => $dopProp['META_DESCRIPTION']
	);

	$u = new CAdminPopupEx(
		"mnu_URL_CPU",
		$arPop['CPU'],
		array("zIndex" => 2000)
	);
	$u->Show();

	$sdt = new CAdminPopupEx(
		"mnu_SEO_DESCRIPTION_TOP",
		$arPop['SEO_DESCRIPTION_TOP'],
		array("zIndex" => 2000)
	);
	$sdt->Show();
	$sd = new CAdminPopupEx(
		"mnu_SEO_DESCRIPTION",
		$arPop['SEO_DESCRIPTION'],
		array("zIndex" => 2000)
	);
	$sd->Show();
	$tn = new CAdminPopupEx(
		"mnu_TAG_NAME",
		$arPop['TAG_NAME'],
		array("zIndex" => 2000)
	);
	$tn->Show();
	$tn = new CAdminPopupEx(
		"mnu_TAG_SECTION_NAME",
		$arPop['TAG_SECTION_NAME'],
		array("zIndex" => 2000)
	);
	$tn->Show();
	$pt = new CAdminPopupEx(
		"mnu_PAGE_TITLE",
		$arPop['PAGE_TITLE'],
		array("zIndex" => 2000)
	);
	$pt->Show();
	$mt = new CAdminPopupEx(
		"mnu_META_TITLE",
		$arPop['META_TITLE'],
		array("zIndex" => 2000)
	);
	$mt->Show();
	$kw = new CAdminPopupEx(
		"mnu_META_KEYWORDS",
		$arPop['META_KEYWORDS'],
		array("zIndex" => 2000)
	);
	$kw->Show();
	$md = new CAdminPopupEx(
		"mnu_META_DESCRIPTION",
		$arPop['META_DESCRIPTION'],
		array("zIndex" => 2000)
	);
	$md->Show();
}

if ($errorMessage !== '')
	CAdminMessage::ShowMessage(array(
		'DETAILS' => $errorMessage,
		'TYPE' => 'ERROR',
		'MESSAGE' => Loc::getMessage('SPSN_ERROR'),
		'HTML' => true
	));


?><script language="JavaScript">
function setLHEClass (lheDivId) {
	BX.ready(
		function(){
			var lheDivObj = BX(lheDivId);

			if (lheDivObj)
				BX.addClass(lheDivObj, 'bxlhe_frame_hndl_dscr');
	});
}
function setIblock(_this){
	obSelect = BX.findChild(_this, {
	        "tag" : "option",
	        "property" : "selected"
	    },
	    true
	);

	let params = window.location.search;
	let url = window.location.href+(!!params ? "&" : "")+'IBLOCK_ID='+obSelect.value;
	window.location.href = url;
}
function setSection(_this){
	obSelect = BX.findChild(_this, {
	        "tag" : "option",
	        "property" : "selected"
	    },
	    true
	);

	let params = window.location.search;
	let url = removeURLParameter(window.location.href+(!!params ? "&" : ""), 'SECTION_ID')+'&SECTION_ID='+obSelect.value;
	window.location.href = url;
}
function removeURLParameter(url, parameter) {
    //prefer to use l.search if you have a location/link object
    var urlparts = url.split('?');
    if (urlparts.length >= 2) {

        var prefix = encodeURIComponent(parameter) + '=';
        var pars = urlparts[1].split(/[&;]/g);

        //reverse iteration as may be destructive
        for (var i = pars.length; i-- > 0;) {
            //idiom for string.startsWith
            if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                pars.splice(i, 1);
            }
        }
		var parsFilt = pars.filter(function (el) {
		  return el != '';
		});

        return urlparts[0] + (parsFilt.length > 0 ? '?' + parsFilt.join('&') : '');
    }
    return url;
}
function __SetUrlVar(id, mnu_id, el_id){
	var obj_ta = BX(el_id);
	//IE
	if (document.selection)
	{
		obj_ta.focus();
		var sel = document.selection.createRange();
		sel.text = id;
		//var range = obj_ta.createTextRange();
		//range.move('character', caretPos);
		//range.select();
	}
	//FF
	else if (obj_ta.selectionStart || obj_ta.selectionStart == '0')
	{
		var startPos = obj_ta.selectionStart;
		var endPos = obj_ta.selectionEnd;
		var caretPos = startPos + id.length;
		obj_ta.value = obj_ta.value.substring(0, startPos) + id + obj_ta.value.substring(endPos, obj_ta.value.length);
		obj_ta.setSelectionRange(caretPos, caretPos);
		obj_ta.focus();
	}
	else
	{
		obj_ta.value += id;
		obj_ta.focus();
	}

	BX.fireEvent(obj_ta, 'change');
	obj_ta.focus();
}
function __SetUrlVarTD(id, mnu_id, el_id)
	{
		var obj_tas = window.BXHtmlEditor.editors[el_id];
		var rand = obj_tas.selection.GetRange();
		if(!!rand.startOffset)
			obj_tas.InsertHtml(id, obj_tas.selection.GetRange());
		else{
			var obj_ta = BX.findChild(BX('bx-html-editor-ta-cnt-'+el_id), {
			        "tag" : "textarea"
			    },
			    true
			);

			if (obj_ta.selectionStart || obj_ta.selectionStart == '0'){
				var startPos = obj_ta.selectionStart;
				var endPos = obj_ta.selectionEnd;
				var caretPos = startPos + id.length;
				obj_ta.value = obj_ta.value.substring(0, startPos) + id + obj_ta.value.substring(endPos, obj_ta.value.length);
				obj_ta.setSelectionRange(caretPos, caretPos);
				obj_ta.focus();
			}
			else
			{
				obj_ta.value += id;
				obj_ta.focus();
			}
		}
	}
function setLinkCpu(_this){
	var parent = BX.findParent(_this, {className : 'url-cpu'});
	var child = BX.findChild(parent, {tag: 'a'});
	child.setAttribute('href', _this.value );
}
function setAllCheck(_this){
	var parent = BX.findParent(_this, {className : 'bx-filter-parameters-box'}),
		childs = parent.querySelectorAll('.bx-filter-block input[type="checkbox"]');
		console.log(childs);
	for (var i = childs.length - 1; i >= 0; i--) {
		childs[i].checked = true;
	}
	return false;
}
</script><style type="text/css">
	.grouped_block {
	    background-color: #fff;
	    border: 1px solid #c4ced2;
	    margin: 0 20px;
	    padding: 15px;
	}
	.bx-filter-parameters-box-title {
	    font-weight: bold;
	    padding: 10px 5px 7px;
	    display: block;
	    background-color: #e0dede;
	    border: 1px solid #999;
	    border-bottom-width: 0;
	    position: relative;
	}
	.flex-it{
		display: flex;
		flex-wrap: wrap;
	}
	.bx-filter-parameters-box-container-block.flex-it{
		align-items: center;
	}
	.flex-it .bx-filter-parameters-box{
		width: 210px;
		padding: 5px;
	}
	.flex-it .bx-filter-parameters-box .bx-filter-block{
		border: 1px solid #999;
		-webkit-box-shadow: 1px 5px 10px -5px rgba(0,0,0,0.75);
		-moz-box-shadow: 1px 5px 10px -5px rgba(0,0,0,0.75);
		box-shadow: 1px 5px 10px -5px rgba(0,0,0,0.75);
	}
	.flex-it .bx-filter-parameters-box .bx-ft-sub{
		width: 35px;
	}
	.bx-filter-parameters-box-container .bx-filter-parameters-box-container-block,
	.bx-filter-parameters-box-container .checkbox{
		padding: 5px 5px 5px 10px;
	}
	.checkbox-with-picture .bx-filter-param-label{
		display: inline-block;
		width: 50px;
		height: 50px;
		background-repeat: no-repeat;
		background-size: contain;
		margin-right: 3px;
		margin-bottom: 5px;
		margin-top: 5px;
		margin-left: 10px;
	}
	.bx-filter-parameters-box-container.dtype-F,
	.bx-filter-parameters-box-container.dtype-P{
		overflow-y: auto;
		max-height: 300px;
	}
	.bx-filter-parameters-box-title .count-title{
		position: absolute;
		right: 0px;
		top: 0px;
		box-sizing: border-box;
		padding: 2px 4px;
		line-height: 1.1em;
		font-size: 12px;
		background-color: #ff4b1b;
		color: #fff;
		border-left: 1px solid #ff4b1b;
		border-bottom: 1px solid #ff4b1b;
	}
	.bx-filter-checkbox-with-label{
		display: flex;
		align-items: center;
		padding: 5px 5px 5px 10px;
	}
	.bx-filter-checkbox-with-label label{
		display: flex;
		align-items: center;
	}
	.bx-filter-checkbox-with-label .bx-filter-param-text{margin-left: 4px}
	.bx-filter-checkbox-with-label .bx-filter-btn-color-icon{
		width:  15px;
		height:  15px;
		display: inline-block;
		margin-left: 4px
	}
	.checklist{
		list-style: none;
		padding: 0;
		margin: 0;
	}
	.checklist li{margin-bottom: 7px;}
	.fbd-top{display: flex;align-items: flex-start;}
	.fbd-top input[type="button"]{margin-left: 10px;}
	.mpd10{padding-left: 10px;}
	.adm-cpu-message{font-style: italic;padding:2px 0;color: #666;}
	.adm-detail-content-cell-l[valign="top"]{padding-top: 10px;}
	.url-cpu{display: flex;align-items: center;}
	.url-cpu a{padding-left: 10px;}
</style><?

?><form method="POST" action="<?=$APPLICATION->GetCurPage();?>" name="pay_sys_form" enctype="multipart/form-data"><?
	echo GetFilterHiddens("filter_");

	?><input type="hidden" name="Update" value="Y"><?
	?><input type="hidden" name="lang" value="<?=$Context->getLanguage();?>"><?
	?><input type="hidden" name="ID" value="<?=$id;?>" id="ID"><?

	echo bitrix_sessid_post();

$tabControl->Begin();
$tabControl->BeginNextTab();

if ($id > 0):
	?><tr><?
		?><td width="40%">ID:</td><?
		?><td width="60%"><?=$id;?></td><?
	?></tr><?
endif;
	?><tr class="adm-detail-required-field"><?
		?><td width="40%"><?=Loc::getMessage('SEOFILTER_TABLE_IBLOCK_ID');?>:</td><?
		?><td width="60%"><?
			if(empty($id)){
				?><select name="IBLOCK_ID" onchange="setIblock(this)"><?
					?><option value=""></option><?
					if($catalogIb)
						foreach ($catalogIb as $id => $name) {
							?><option value="<?=$id?>"<?=($iblockId == $id ? ' selected' : '');?>><?=$name;?></option><?
						}
				?></select><?
			}else{
				echo $catalogIb[$iblockId];
				?><input type="hidden" name="IBLOCK_ID" value="<?=$iblockId?>"><?
			}
		?></td><?
	?></tr><?
	?><tr class="adm-detail-required-field"><?
		?><td width="40%"><?=Loc::getMessage('SEOFILTER_TABLE_SECTION_ID');?>:</td><?
		?><td width="60%"><?
			?><select name="SECTION_ID" onchange="setSection(this)"><?
				?><option value="0"><?=Loc::getMessage('SEOFILTER_SECTION_UPPER_LEVEL')?></option><?
				if($sectionIb)
					foreach ($sectionIb as $id => $name) {
						?><option value="<?=$id?>"<?=($sectionId == $id ? ' selected' : '');?>><?=$name;?></option><?
					}
			?></select><?
		?></td><?
	?></tr><?
	?><tr class="adm-detail-required-field"><?
		?><td width="40%" valign="top"><?=Loc::getMessage('SEOFILTER_TABLE_SITE_ID');?>:</td><?
		?><td width="60%"><?
			if($listSite){
				?><ul class="checklist"><?
				$siteIDs = $isSaving ? $Request->get('SITE_ID') : $seofilterSetting['SITE_ID'];
				foreach ($listSite as $lid => $site) {
					?><li><?
						?><label>
							<input type="checkbox" name="SITE_ID[<?=$lid?>]" id="SITE_ID_<?=$lid?>" value="<?=$lid?>"<?=($siteIDs && in_array($lid, $siteIDs) ? ' checked' : '')?>> [<?=$site['LID']?>] <?=$site['NAME']?>
						</label><?
					?></li><?
				}
				?></ul><?
			}
		?></td><?
	?></tr><?
	?><tr class="adm-detail-required-field"><?
		?><td width="40%" valign="top"><?=Loc::getMessage('SEOFILTER_TABLE_URL_CPU');?>:</td><?
		?><td width="60%"><?
			$urlCpu = $isSaving ? $Request->get('URL_CPU') : $seofilterSetting['URL_CPU'];
			if($iblockId && $sectionId){
				$urlLink = ['URL_CPU' => $urlCpu, 'IBLOCK_ID' => $iblockId, 'SECTION_ID' => $sectionId];
				Section::replace($urlLink);
            }

			$placeHolder = $arSection['SECTION_PAGE_URL'] ? $arSection['SECTION_PAGE_URL'] : configuration::getOption('cpu_catalog', SITE_ID);

			$parUrl = configuration::getRandomVal($arResult['ITEMS']);

			if(!$parUrl)
				$parUrl = 'sinie_sapogi';

			?><div class="url-cpu"><input type="text" name="URL_CPU" id="URL_CPU" value="<?=htmlspecialcharsbx($urlCpu);?>" size="70" placeholder='<?=$placeHolder?>...'><?if(!empty($arResult['ITEMS'])){?> <div class="mpd10"><input type="button" id="mnu_URL_CPU" value='...'></div><?}
			?><?
			if($urlLink['URL_CPU'] && !preg_match('/\#PROP_(.+?)\#/i', $urlCpu)){
				?><a href="<?=$urlLink['URL_CPU']?>" target="_blank"><?=Loc::getMessage('SEOFILTER_URL_VIEW');?></a><?
			}
			?></div><div class="adm-cpu-message"><?=Loc::getMessage('SEOFILTER_TABLE_CPU_DESC', ['#SECTION_URL#' => $placeHolder.$parUrl.'/']);?></div><?
		?></td><?
	?></tr><?

	?><tr>
		<td width="40%"><label for="ACTIVE"><?=Loc::getMessage('SEOFILTER_TABLE_ACTIVE');?>:</label></td>
		<td width="60%"><?
			if ($isSaving)
				$active = $Request->get('ACTIVE') ? $Request->get('ACTIVE') : '';
			else
				$active = isset($seofilterSetting['ACTIVE']) ? $seofilterSetting['ACTIVE'] : 'Y';

			?><input type="hidden" name="ACTIVE" value="N"><?
			?><input type="checkbox" name="ACTIVE" id="ACTIVE" value="Y"<?=($active == 'Y' ? ' checked' : '')?>>
		</td>
	</tr><?
	?><tr class="adm-detail-required-field"><?
		?><td width="40%"><?=Loc::getMessage('SEOFILTER_TABLE_SORT');?>:</td><?
		?><td width="60%"><?
			if ($isSaving)
				$sort = $isSaving ? $Request->get('SORT') : '';
			else
				$sort = isset($seofilterSetting['SORT']) ? $seofilterSetting['SORT'] : 100;

			?><input type="text" name="SORT" id="SORT" value="<?=htmlspecialcharsbx($sort);?>" size="20"><?
		?></td><?
	?></tr><?
	?><tr>
		<td width="40%" valign="top"><?=Loc::getMessage('SEOFILTER_TABLE_DESCRIPTION');?>:</td>
		<td width="60%" valign="top"><?
			$description = $isSaving ? $Request->get('DESCRIPTION') : $seofilterSetting['DESCRIPTION'];
			?><textarea name="DESCRIPTION" id="DESCRIPTION" cols="60" rows="3"><?=htmlspecialcharsback($description);?></textarea><?
			?><br><div class="adm-cpu-message"><?=Loc::getMessage('SEOFILTER_TABLE_DESCRIPTION_DESC');?></div><?
		?></td>
	</tr><?
	?><tr class="heading">
		<td colspan="2"><?=Loc::getMessage('SEOFILTER_TABLE_TAG_ELEMENT');?>:</label></td>
	</tr><?
	?><tr>
		<td width="40%"><label for="EVIEW"><?=Loc::getMessage('SEOFILTER_TABLE_EVIEW');?>:</label></td>
		<td width="60%"><?
			if ($isSaving)
				$eview = $Request->get('EVIEW') ? $Request->get('EVIEW') : '';
			else
				$eview = isset($seofilterSetting['EVIEW']) ? $seofilterSetting['EVIEW'] : 'Y';
			?><input type="hidden" name="EVIEW" value="N"><?
			?><input type="checkbox" name="EVIEW" id="EVIEW" value="Y"<?=($eview == 'Y' ? ' checked' : '')?>>
		</td>
	</tr><?
	?><tr>
		<td width="40%" valign="top"><?=Loc::getMessage('SEOFILTER_TABLE_TAG_NAME');?>:</td>
		<td width="60%" valign="top"><div class="fbd-top"><?
			$tag = $isSaving ? $Request->get('TAG_NAME') : $seofilterSetting['TAG_NAME'];
				?><input type="text" name="TAG_NAME" id="TAG_NAME" value="<?=htmlspecialcharsback($tag);?>" size="70"><?if(!empty($arResult['ITEMS'])){?> <input type="button" id="mnu_TAG_NAME" value='...'><?}
			?></div><?
			?><div class="adm-cpu-message"><?=Loc::getMessage('SEOFILTER_TABLE_TAG_NAME_DESC');?></div><?
		?></td>
	</tr><?

	?><tr class="heading">
		<td colspan="2"><?=Loc::getMessage('SEOFILTER_TABLE_TAG_SECTION');?>:</label></td>
	</tr><?
	?><tr>
		<td width="40%"><label for="SVIEW"><?=Loc::getMessage('SEOFILTER_TABLE_SVIEW');?>:</label></td>
		<td width="60%"><?
			if ($isSaving)
				$sview = $Request->get('SVIEW') ? $Request->get('SVIEW') : '';
			else
				$sview = isset($seofilterSetting['SVIEW']) ? $seofilterSetting['SVIEW'] : 'Y';
			?><input type="hidden" name="SVIEW" value="N"><?
			?><input type="checkbox" name="SVIEW" id="SVIEW" value="Y"<?=($sview == 'Y' ? ' checked' : '')?>>
		</td>
	</tr><?
	?><tr>
		<td width="40%" valign="top"><?=Loc::getMessage('SEOFILTER_TABLE_TAG_SECTION_NAME');?>:</td>
		<td width="60%" valign="top"><div class="fbd-top"><?
			$tag = $isSaving ? $Request->get('TAG_SECTION_NAME') : $seofilterSetting['TAG_SECTION_NAME'];
				?><input type="text" name="TAG_SECTION_NAME" id="TAG_SECTION_NAME" value="<?=htmlspecialcharsback($tag);?>" size="70"><?if(!empty($arResult['ITEMS'])){?> <input type="button" id="mnu_TAG_SECTION_NAME" value='...'><?}
			?></div><?
			?><div class="adm-cpu-message"><?=Loc::getMessage('SEOFILTER_TABLE_TAG_SECTION_NAME_DESC');?></div><?
		?></td>
	</tr><?
	// Тег в каталоге
	?><tr class="heading">
		<td colspan="2"><?=Loc::getMessage('SEOFILTER_TABLE_TAG');?>:</label></td>
	</tr><?
	?><tr>
		<td width="40%" valign="top"><?=Loc::getMessage('SEOFILTER_TABLE_PAGE_TITLE');?>:</td>
		<td width="60%" valign="top"><div class="fbd-top"><?
			$pageTitle = $isSaving ? $Request->get('PAGE_TITLE') : $seofilterSetting['ZVERUSHKI_SEOFILTER_INTERNALS_SETTINGS_SETTING_PAGE_TITLE'];
			?><textarea name="PAGE_TITLE" id="PAGE_TITLE" cols="60" rows="3"><?=htmlspecialcharsback($pageTitle);?></textarea><?if(!empty($arResult['ITEMS'])){?> <input type="button" id="mnu_PAGE_TITLE" value='...'><?}
			?></div><?
		?></td>
	</tr><?
	?><tr>
		<td width="40%" valign="top"><?=Loc::getMessage('SEOFILTER_TABLE_META_TITLE');?>:</td>
		<td width="60%" valign="top"><div class="fbd-top"><?
			$metaTitle = $isSaving ? $Request->get('META_TITLE') : $seofilterSetting['ZVERUSHKI_SEOFILTER_INTERNALS_SETTINGS_SETTING_META_TITLE'];
				?><textarea name="META_TITLE" id="META_TITLE" cols="60" rows="3"><?=htmlspecialcharsback($metaTitle);?></textarea><?if(!empty($arResult['ITEMS'])){?> <input type="button" id="mnu_META_TITLE" value='...'><?}
			?></div><?
		?></td>
	</tr><?

	?><tr>
		<td width="40%" valign="top"><?=Loc::getMessage('SEOFILTER_TABLE_META_KEYWORDS');?>:</td>
		<td width="60%" valign="top"><div class="fbd-top"><?
			$metaKeywords = $isSaving ? $Request->get('META_KEYWORDS') : $seofilterSetting['ZVERUSHKI_SEOFILTER_INTERNALS_SETTINGS_SETTING_META_KEYWORDS'];
			?><textarea name="META_KEYWORDS" id="META_KEYWORDS" cols="60" rows="3"><?=htmlspecialcharsback($metaKeywords);?></textarea><?if(!empty($arResult['ITEMS'])){?> <input type="button" id="mnu_META_KEYWORDS" value='...'><?}
		?></div><?
		?></td>
	</tr><?
	?><tr>
		<td width="40%" valign="top"><?=Loc::getMessage('SEOFILTER_TABLE_META_DESCRIPTION');?>:</td>
		<td width="60%" valign="top"><div class="fbd-top"><?
			$metaDescription = $isSaving ? $Request->get('META_DESCRIPTION') : $seofilterSetting['ZVERUSHKI_SEOFILTER_INTERNALS_SETTINGS_SETTING_META_DESCRIPTION'];
			?><textarea name="META_DESCRIPTION" id="META_DESCRIPTION" cols="60" rows="3"><?=htmlspecialcharsback($metaDescription);?></textarea><?if(!empty($arResult['ITEMS'])){?> <input type="button" id="mnu_META_DESCRIPTION" value='...'><?}
		?></div><?
		?></td>
	</tr><?
	?><tr>
		<td width="40%" valign="top"><?=Loc::getMessage('SEOFILTER_TABLE_SEO_DESCRIPTION_TOP');?>:</td>
		<td width="60%" valign="top"><div class="fbd-top"><?
			$descriptionTop = $isSaving ? $Request->get('SEO_DESCRIPTION_TOP') : $seofilterSetting['ZVERUSHKI_SEOFILTER_INTERNALS_SETTINGS_SETTING_SEO_DESCRIPTION_TOP'];

			if (CModule::IncludeModule("fileman")) {
			    \CFileman::ShowHTMLEditControl("SEO_DESCRIPTION_TOP", $descriptionTop, $arParamsEdit);
			    if(!empty($arResult['ITEMS'])){?> <input type="button" id="mnu_SEO_DESCRIPTION_TOP" value='...'><?}
			} else {
				echo wrapDescrLHE("SEO_DESCRIPTION_TOP", $descriptionTop, "hndl_dscr_seo_".$id);

				?><script language="JavaScript">setLHEClass('bxlhe_frame_hndl_dscr_seo_<?=$id;?>');</script><?
			}
		?></div></td>
	</tr><?
	?><tr>
		<td width="40%" valign="top"><?=Loc::getMessage('SEOFILTER_TABLE_SEO_DESCRIPTION');?>:</td>
		<td width="60%" valign="top"><div class="fbd-top"><?
			$description = $isSaving ? $Request->get('SEO_DESCRIPTION') : $seofilterSetting['ZVERUSHKI_SEOFILTER_INTERNALS_SETTINGS_SETTING_SEO_DESCRIPTION'];

			if (CModule::IncludeModule("fileman")) {
			    \CFileman::ShowHTMLEditControl("SEO_DESCRIPTION", $description, $arParamsEdit);
			    if(!empty($arResult['ITEMS'])){?> <input type="button" id="mnu_SEO_DESCRIPTION" value='...'><?}
			} else {
				echo wrapDescrLHE("SEO_DESCRIPTION", $description, "hndl_dscr_seo_".$id);

				?><script language="JavaScript">setLHEClass('bxlhe_frame_hndl_dscr_seo_<?=$id;?>');</script><?
			}
		?></div></td>
	</tr><?
	?><tr class="heading">
		<td colspan="2"><?=Loc::getMessage('SEOFILTER_TABLE_PARAMS');?>:</label></td>
	</tr>
	<tr>
		<td width="100%" colspan="2"><div id="group_params" class="grouped_block">
			<?include_once "smart.php";?>
		</div>
		</td>
	</tr><?
	if($isList ){
		$tabControl->EndTab();
		$tabControl->BeginNextTab();
		?><tr><td id="setting-link"><?
			echo $linkContent;
		?></td></tr><?
	}

$tabControl->EndTab();


$tabControl->Buttons(
	array(
		'disabled' => ($seofilterModulePermissions < 'W'),
		'back_url' => '/bitrix/admin/'.$moduleId.'_settings.php?lang='.$Context->getLanguage()
	)
);

$tabControl->End();

?></form><?


require $documentRoot.'/bitrix/modules/main/include/epilog_admin.php';


function wrapDescrLHE ($inputName, $content = '', $divId = false) {
	ob_start();
	$ar = array(
		'inputName' => $inputName,
		'height' => '160',
		'width' => '100%',
		'content' => $content,
		'bResizable' => true,
		'bManualResize' => true,
		'bUseFileDialogs' => false,
		'bFloatingToolbar' => false,
		'bArisingToolbar' => false,
		'bAutoResize' => true,
		'bSaveOnBlur' => true,
		'toolbarConfig' => array(
			'Bold', 'Italic', 'Underline', 'Strike',
			'CreateLink', 'DeleteLink',
			'Source', 'BackColor', 'ForeColor'
		)
	);

	if ($divId)
		$ar['id'] = $divId;

	$LHE = new CLightHTMLEditor;
	$LHE->Show($ar);
	$sVal = ob_get_contents();
	ob_end_clean();

	return $sVal;
}