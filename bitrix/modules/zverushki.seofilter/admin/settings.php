<?
require_once ($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

use Bitrix\Main\Application,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\Loader,
	Bitrix\Main\Config\Option,
	Zverushki\Seofilter\Sections\Section,
    Zverushki\Seofilter\Internals;

$moduleId = 'zverushki.seofilter';
if(!in_array(Loader::includeSharewareModule($moduleId), [Loader::MODULE_INSTALLED, Loader::MODULE_DEMO])){
	throw new \Exception("Required module `{$moduleId}` was not found");
}

$seofilterModulePermissions = $APPLICATION->GetGroupRight($moduleId);

if ($seofilterModulePermissions < 'W')
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));

IncludeModuleLangFile(__FILE__);
CUtil::InitJSCore(array('popup', 'window'));

$APPLICATION->SetTitle(Loc::getMessage('SEOFILTER_SETTINGS_SECTION_TITLE'));

$Context = Application::getInstance()->getContext();
$Request = $Context->getRequest();

$sTableID = 'seofilter_settings'.md5($moduleId);

$order = !isset($order) ? 'asc' : $order;
$by = !isset($by) ? 'ID' : strtoupper($by);

$oSort = new CAdminSorting($sTableID, $by, $order);
$lAdmin = new CAdminList($sTableID, $oSort);

if ($lAdmin->EditAction()) {
	$update = 0;
	$err = 0;
	$checkCpu = Option::get($moduleId, 'check_cpu', 'N');
	foreach ($Request->get('FIELDS') as $id => $fields) {
		Zverushki\Seofilter\Agent::addGenerateIndexPart( $id );
		if (!$lAdmin->IsUpdated($id))
			continue;
		$update++;

		if($fields['URL_CPU'])
			$fields['URL_CPU'] = trim($fields['URL_CPU']);
		$checkUnique = false;
		if($checkCpu != 'Y' && $fields['URL_CPU']){
			$f = Internals\SettingsTable::getList(array(
				'filter' => array('ID' => $id, 'ACTIVE' => 'Y'),
				'select' => array('ID', 'IBLOCK_ID', 'SECTION_ID')
			))->fetch();

			$fieldsSearch = ['IBLOCK_ID' => $f ['IBLOCK_ID'], 'SECTION_ID' => $f['SECTION_ID'], 'URL_CPU' => $fields['URL_CPU']];
			Section::replace($fieldsSearch);

			if(Internals\SettingsTable::getList([
				'filter' => [ 'URL_CPU' => $fieldsSearch['URL_CPU'], 'ACTIVE' => 'Y', '!ID' => $id ],
				'select' => [ 'ID' ]
			])
				->fetch())
				$checkUnique = true;
			else{
                $resSearch = Internals\SettingsTable::getList([
                    'filter' => [ 'URL_CPU' => "%#SECTION_%", 'ACTIVE' => 'Y', '!ID' => $id ],
                    'select' => [ 'ID', 'URL_CPU', 'IBLOCK_ID', 'SECTION_ID' ]
                ]);
                while($asSearch = $resSearch->fetch()){
                    Section::replace($asSearch);
                    if($asSearch['URL_CPU'] == $fieldsSearch['URL_CPU'])
	                    $checkUnique = true;
                }
            }
		}

		if($checkUnique){
			$lAdmin->AddGroupError(Loc::getMessage("SEOFILTER_SETTINGS_SPLIST_ERROR_UPDATE", array("#ID#" => $id)).' ['.Loc::getMessage('SEOFILTER_TABLE_ERROR_EXIST').']');
			$err++;
		}else{
		    $seoFields = [];
			foreach (Internals\SeotagTable::getMap() as $Field) {
				if ($Field instanceof \Bitrix\Main\Entity\ReferenceField || $Field instanceof \Bitrix\Main\Entity\ExpressionField)
					continue;

				if ((method_exists($Field, 'isAutocomplete') && $Field->isAutocomplete()) || strpos($Field->getColumnName(), 'TIMESTAMP') === 0 || strpos($Field->getColumnName(), 'SETTING_ID') === 0)
					continue;

				$value = trim($fields[$Field->getColumnName()]);
				unset($fields[$Field->getColumnName()]);
				if ($Field instanceof \Bitrix\Main\Entity\BooleanField && !$value)
					$value = 'N';
				if($value)
					$seoFields[$Field->getColumnName()] = $value;
			}

			$Res = Internals\SettingsTable::update($id, $fields);
			if (!$Res->isSuccess()){
				$err++;
				$lAdmin->AddGroupError(Loc::getMessage("SEOFILTER_SETTINGS_SPLIST_ERROR_UPDATE", array("#ID#" => $id)).' ['.implode('; ', $Res->getErrorMessages()).']');
			}else{
			    if($seoFields){
			    	$fields = [ 'SETTING_ID' => $Res->getId() ];
				    $seotagClass = Internals\SeotagTable::getList(
					    [
						    'filter' => [ 'SETTING_ID' => $fields['SETTING_ID'] ],
						    'select' => [ 'ID' ]
					    ]
				    )->fetch();

				    if( $seotagClass['ID'] > 0 )
					    Internals\SeotagTable::update( $seotagClass['ID'], $seoFields );
			    }
				$cacheManager = \Bitrix\Main\Application::getInstance()
				                                        ->getTaggedCache();
				$cacheManager->ClearByTag("zverushki_seofilter_cpu_all");
			}
		}

	}
}

if (($arID = $lAdmin->GroupAction())) {
	$action = $Request->get('action') ? $Request->get('action') : $Request->get('action_button');

	foreach ($arID as $ID) {
		if (strlen($ID) <= 0)
			continue;

		switch ($action) {
			case 'delete':
				$Res = Internals\SettingsTable::delete($ID);

				if (!$Res->isSuccess())
					$lAdmin->AddGroupError(Loc::getMessage("SEOFILTER_SETTINGS_SPLIST_ERROR_DELETE", array("#ID#" => $ID)).' ['.implode('; ', $Res->getErrorMessages()).']');
				break;
		}
	}

	// LocalRedirect($moduleId.'_settings.php?lang='.LANGUAGE_ID);
}
if (Loader::includeModule('catalog')) {
	$Db = \CCatalog::getList(array(), $arIblockFilter);
	while (($a = $Db->fetch()) !== false){
		if($a['PRODUCT_IBLOCK_ID'] == 0)
			$catalogIb[$a['IBLOCK_ID']] = $a['NAME'];
	}

	if(!empty($catalogIb)){
		asort($catalogIb);
		$sectionIb =  array();
		$tree = CIBlockSection::GetTreeList(
		    $arFilter=Array('IBLOCK_ID' => array_keys($catalogIb)),
		    $arSelect=Array('ID', 'NAME', 'IBLOCK_ID', 'DEPTH_LEVEL')
		);
		while($section = $tree->GetNext()) {
			$sectionIb[$section['ID']] = $section['NAME'];

			$sectionIbStr[$section['ID']] = str_repeat(". ", $section['DEPTH_LEVEL']-1).$section['NAME'];
			$sectionIbAr[$section['IBLOCK_ID']]['name'] = $catalogIb[$section['IBLOCK_ID']];
			$sectionIbAr[$section['IBLOCK_ID']]['item'][$section['ID']] = str_repeat(". ", $section['DEPTH_LEVEL']-1).$section['NAME'];
		}
	}
}
$listSite = array();
$rsSites = \CSite::GetList($bySID = "ID", $orderSID = "asc");
while ($arSite = $rsSites->Fetch()) {
	$listSite[$arSite['LID']] = '['.$arSite['LID'].'] '.$arSite['NAME'];
}

/* - - - - - - - - - -*/
$arFilterFields = array(
	"filter_url_cpu",
	"filter_list_cpu",
	"filter_url_cpu_mask",
	"filter_tag_name",
	"filter_iblock_id",
	"filter_section_id",
	"filter_active",
	"filter_eview",
	"filter_sview",
	"filter_site_id"
);
$lAdmin->InitFilter($arFilterFields);

$filter = array();

foreach ($arFilterFields as $code) {
	$filed = strtoupper(str_replace('filter_', '', $code));


	if (${$code}){
		if($code== 'filter_url_cpu_mask')
			$filter[(${$code} == 'N' ? '!' : '').'URL_CPU'] = "%#PROP_%";
		elseif($code== 'filter_list_cpu') {
			$url = ${$code};
			if(strpos($url, '//')){
				$urlTmp = explode('//', $url);
				if($urlTmp[1]) {
					$dir = explode( '/', $urlTmp[ 1 ] );
					unset($dir[0]);
					$url = implode('/', $dir);
				}
			}
			$filter['ID'] = false;
			$obLandig = Internals\LandingTable::getList([
				'order' => ['SETTING_ID' => 'ASC'],
				'filter' => ['URL_CPU' => '%'.$url.'%'],
				'select' => ['SETTING_ID'],
				'group' => ['SETTING_ID']
			]);
			while ($landig = $obLandig->fetch()){
				$filter['ID'][] = $landig['SETTING_ID'];
			}
		}else
			$filter[$filed == "SITE_ID" ? 'SITE_ID.SITE_ID' : $filed] = $filed == 'NAME' ? '%'.${$code}.'%' : ${$code};
	}
}

/* - - - - - - - - - -*/
$Db = Internals\SettingsTable::getList(array(
	'order' => array($by => $order),
	'filter' => $filter,
	'select' => array('*')
));
$dbRes = new CDBResult();
$dbRes->InitFromArray($Db->fetchAll());

$dbRes = new CAdminResult($dbRes, $sTableID);
$dbRes->NavStart();

$lAdmin->NavText($dbRes->GetNavPrint(Loc::getMessage('SALE_PRLIST')));

$lAdmin->AddHeaders(array(
	array('id' => 'ID', 'content' => 'ID', 	'sort' => 'ID', 'default' => true),
	array('id' => 'ACTIVE', 'content' => Loc::getMessage('SEOFILTER_SETTINGS_TABLE_COL_ACTIVE'),  'sort' => 'ACTIVE', 'default' => true),
	array('id' => 'EVIEW', 'content' => Loc::getMessage('SEOFILTER_SETTINGS_TABLE_COL_EVIEW'),  'sort' => 'EVIEW', 'default' => true),
	array('id' => 'SVIEW', 'content' => Loc::getMessage('SEOFILTER_SETTINGS_TABLE_COL_SVIEW'),  'sort' => 'SVIEW', 'default' => true),
	array('id' => 'IBLOCK_ID', 'content' => Loc::getMessage('SEOFILTER_SETTINGS_TABLE_COL_IBLOCK_ID'), 'default' => true),
	array('id' => 'SECTION_ID', 'content' => Loc::getMessage('SEOFILTER_SETTINGS_TABLE_COL_SECTION_ID'), 'default' => true),
	array('id' => 'SORT', 'content' => Loc::getMessage('SEOFILTER_SETTINGS_TABLE_COL_SORT'), 'sort' => 'SORT', 'default' => true),
	array('id' => 'SITE_ID', 'content' => Loc::getMessage('SEOFILTER_SETTINGS_TABLE_COL_SITE_ID'), 'default' => true),
	array('id' => 'URL_CPU', 'content' => Loc::getMessage('SEOFILTER_SETTINGS_TABLE_COL_URL_CPU'), 'sort' => 'URL_CPU', 'default' => true),
	array('id' => 'TAG_NAME', 'content' => Loc::getMessage('SEOFILTER_SETTINGS_TABLE_COL_TAG_NAME'), 'sort' => 'TAG_NAME', 'default' => true),
	array('id' => 'TAG_SECTION_NAME', 'content' => Loc::getMessage('SEOFILTER_SETTINGS_TABLE_COL_TAG_SECTION_NAME'), 'sort' => 'TAG_SECTION_NAME', 'default' => true),
	array('id' => 'DESCRIPTION', 'content' => Loc::getMessage('SEOFILTER_SETTINGS_TABLE_COL_DESCRIPTION'), 'sort' => 'DESCRIPTION', 'default' => true),

	array('id' => 'PAGE_TITLE', 'content' => Loc::getMessage('SEOFILTER_SETTINGS_TABLE_COL_PAGE_TITLE')),
	array('id' => 'META_TITLE', 'content' => Loc::getMessage('SEOFILTER_SETTINGS_TABLE_COL_META_TITLE')),
	array('id' => 'META_KEYWORDS', 'content' => Loc::getMessage('SEOFILTER_SETTINGS_TABLE_COL_META_KEYWORDS')),
	array('id' => 'META_DESCRIPTION', 'content' => Loc::getMessage('SEOFILTER_SETTINGS_TABLE_COL_META_DESCRIPTION')),
	array('id' => 'SEO_DESCRIPTION_TOP', 'content' => Loc::getMessage('SEOFILTER_SETTINGS_TABLE_COL_SEO_DESCRIPTION_TOP')),
	array('id' => 'SEO_DESCRIPTION', 'content' => Loc::getMessage('SEOFILTER_SETTINGS_TABLE_COL_SEO_DESCRIPTION')),
));

$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();
$not = true;
while ($a = $dbRes->fetch()) {
	$not = false;
	$resSite = Internals\SettingsSiteTable::getList(array(
		'order' => array('SITE_ID' => 'ASC'),
		'filter' => array('SETTING_ID' => $a['ID']),
		'select' => array('ID', 'SITE_ID')
	));
	while($arSite = $resSite->fetch()){
		$a['SITE_ID'][$arSite['SITE_ID']] = $arSite['SITE_ID'];
	}

	$resSeo = Internals\SeotagTable::getList(array(
		'order' => array('ID' => 'ASC'),
		'filter' => array('SETTING_ID' => $a['ID']),
		'select' => array('PAGE_TITLE', 'META_TITLE', 'META_KEYWORDS', 'META_DESCRIPTION', 'SEO_DESCRIPTION_TOP', 'SEO_DESCRIPTION')
	));
	if($arSeo = $resSeo->fetch())
		$a = array_merge($a, $arSeo);

	$Row =& $lAdmin->AddRow($a['ID'], $a, $moduleId."_setting_edit.php?ID=".$a['ID']."&lang=".LANG);

	$Row->AddViewField('ID', "<a href=\"".$moduleId."_setting_edit.php?ID=".$a['ID']."&lang=".LANG."\">".$a['ID']."</a>");
	$Row->AddViewField('IBLOCK_ID', $catalogIb[$a['IBLOCK_ID']]);
	$Row->AddViewField('SECTION_ID', $sectionIb[$a['SECTION_ID']]);
	$Row->AddInputField("SORT", Array('size'=>'5'));
	$Row->AddViewField('SITE_ID', implode(", ", $a['SITE_ID'] ? $a['SITE_ID'] : []));

	if(preg_match('/\#PROP_(.+?)\#/i', $a['URL_CPU']))
		$Row->AddViewField('URL_CPU', $a['URL_CPU']);
	else
		$Row->AddViewField('URL_CPU', '<a href="'.$a['URL_CPU'].'" target="_blank">'.$a['URL_CPU'].'</a>');
	$Row->AddInputField("URL_CPU", Array('size'=>'35'));
	$Row->AddCheckField('ACTIVE', true);
	$Row->AddCheckField('EVIEW', true);
	$Row->AddCheckField('SVIEW', true);
	$Row->AddViewField('DESCRIPTION', $a['DESCRIPTION'] ? htmlspecialcharsback($a['DESCRIPTION']) : " ");
	$Row->AddInputField("DESCRIPTION", Array('size'=>'50'));
	$Row->AddInputField("TAG_NAME", Array('size'=>'50'));
	$Row->AddInputField("TAG_SECTION_NAME", Array('size'=>'50'));


	$Row->AddViewField('PAGE_TITLE', $a['PAGE_TITLE'] ? htmlspecialcharsback($a['PAGE_TITLE']) : " ");
	$sHTML = '<textarea rows="3" cols="80" name="FIELDS['.$a['ID'].'][PAGE_TITLE]">'.htmlspecialcharsex($a["PAGE_TITLE"]).'</textarea>';
	$Row->AddEditField('PAGE_TITLE', $sHTML);

	$Row->AddViewField('META_TITLE', $a['META_TITLE'] ? htmlspecialcharsback($a['META_TITLE']) : " ");
	$sHTML = '<textarea rows="3" cols="80" name="FIELDS['.$a['ID'].'][META_TITLE]">'.htmlspecialcharsex($a["META_TITLE"]).'</textarea>';
	$Row->AddEditField('META_TITLE', $sHTML);

	$Row->AddViewField('META_KEYWORDS', $a['META_KEYWORDS'] ? htmlspecialcharsback($a['META_KEYWORDS']) : " ");
	$sHTML = '<textarea rows="3" cols="80" name="FIELDS['.$a['ID'].'][META_KEYWORDS]">'.htmlspecialcharsex($a["META_KEYWORDS"]).'</textarea>';
	$Row->AddEditField('META_KEYWORDS', $sHTML);

	$Row->AddViewField('META_DESCRIPTION', $a['META_DESCRIPTION'] ? htmlspecialcharsback($a['META_DESCRIPTION']) : " ");
	$sHTML = '<textarea rows="3" cols="80" name="FIELDS['.$a['ID'].'][META_DESCRIPTION]">'.htmlspecialcharsex($a["META_DESCRIPTION"]).'</textarea>';
	$Row->AddEditField('META_DESCRIPTION', $sHTML);

	$Row->AddViewField('SEO_DESCRIPTION_TOP', $a['SEO_DESCRIPTION_TOP'] ? htmlspecialcharsback($a['SEO_DESCRIPTION_TOP']) : " ");
	$sHTML = '<textarea rows="3" cols="80" name="FIELDS['.$a['ID'].'][SEO_DESCRIPTION_TOP]">'.htmlspecialcharsex($a["SEO_DESCRIPTION_TOP"])
        .'</textarea>';
	$Row->AddEditField('SEO_DESCRIPTION_TOP', $sHTML);

	$Row->AddViewField('SEO_DESCRIPTION', $a['SEO_DESCRIPTION'] ? htmlspecialcharsback($a['SEO_DESCRIPTION']) : " ");
	$sHTML = '<textarea rows="3" cols="80" name="FIELDS['.$a['ID'].'][SEO_DESCRIPTION]">'.htmlspecialcharsex($a["SEO_DESCRIPTION"]).'</textarea>';
	$Row->AddEditField('SEO_DESCRIPTION', $sHTML);

	$arActions = array(
		array(
			'ICON' => 'edit',
			'TEXT' => Loc::getMessage('SEOFILTER_SETTINGS_TABLE_ROWACT_EDIT_TEXT'),
			'TITLE' => Loc::getMessage('SEOFILTER_SETTINGS_TABLE_ROWACT_EDIT_TITLE'),
			'ACTION' => $lAdmin->ActionRedirect($moduleId.'_setting_edit.php?ID='.$a['ID'].'&lang='.LANGUAGE_ID),
			'DEFAULT' => true,
		),
		array(
			'ICON' => 'copy',
			'TEXT' => Loc::getMessage('SEOFILTER_SETTINGS_TABLE_ROWACT_COPY_TEXT'),
			'TITLE' => Loc::getMessage('SEOFILTER_SETTINGS_TABLE_ROWACT_COPY_TITLE'),
			'ACTION' => $lAdmin->ActionRedirect($moduleId.'_setting_edit.php?CID='.$a['ID'].'&action=copy&lang='.LANGUAGE_ID),
			'DEFAULT' => true,
		),
	);

	if ($seofilterModulePermissions >= "W") {
		$arActions[] = array('SEPARATOR' => true);
		$arActions[] = array(
			"ICON" => "delete",
			"TEXT" => Loc::getMessage('SEOFILTER_SETTINGS_TABLE_ROWACT_DELETE_TEXT'),
			"TITLE" => Loc::getMessage('SEOFILTER_SETTINGS_TABLE_ROWACT_DELETE_TITLE'),
			"ACTION" => "if(confirm('".Loc::getMessage('SEOFILTER_SETTINGS_TABLE_ROWACT_DELETE_CONFIRMJS', array('#NAME#' => $a['PROFILE_NAME']))."')) ".$lAdmin->ActionDoGroup($a['ID'], "delete")
		);
	}

	$Row->AddActions($arActions);
}


$aContext = array(
	array(
		'TEXT' => Loc::getMessage('SEOFILTER_SETTINGS_TABLE_CONTEXT_ADDNEW'),
		'ICON' => 'btn_new',
		"LINK" => $moduleId.'_setting_edit.php?lang='.LANGUAGE_ID,
		'MENU' => $profileMenu
	)
);

$lAdmin->AddGroupActionTable(array(
	'delete' => Loc::getMessage('SEOFILTER_SETTINGS_AGROUP_DELETE')
));

$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->CheckListMode();

require ($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');


?><form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?"><?

$LFilter = new CAdminFilter(
	$sTableID.'_filter',
	array(
		Loc::getMessage("SEOFILTER_CATTABLE_ACTIVE"),
		Loc::getMessage("SEOFILTER_CATTABLE_EVIEW"),
		Loc::getMessage("SEOFILTER_CATTABLE_SVIEW"),
		Loc::getMessage("SEOFILTER_CATTABLE_IBLOCK_ID"),
		Loc::getMessage("SEOFILTER_CATTABLE_SECTION_ID"),
		Loc::getMessage("SEOFILTER_CATTABLE_SITE_ID"),
		Loc::getMessage("SEOFILTER_CATTABLE_URL_CPU_MASK"),
		Loc::getMessage("SEOFILTER_CATTABLE_URL_CPU"),
		Loc::getMessage("SEOFILTER_CATTABLE_URL_LANDING"),
	)
);

$LFilter->Begin();

$reess = array(
	'ACTIVE' => array(
		"Y" => GetMessage("SEOFILTER_CATTABLE_YES"),
		"N" => GetMessage("SEOFILTER_CATTABLE_NO")
	),
	'EVIEW' => array(
		"Y" => GetMessage("SEOFILTER_CATTABLE_YES"),
		"N" => GetMessage("SEOFILTER_CATTABLE_NO")
	),
	'SVIEW' => array(
		"Y" => GetMessage("SEOFILTER_CATTABLE_YES"),
		"N" => GetMessage("SEOFILTER_CATTABLE_NO")
	),
	'IBLOCK_ID' => $catalogIb,
	'SECTION_ID' => count($catalogIb) > 1 ? $sectionIbAr : $sectionIbStr,
	'SITE_ID' => $listSite,
	'URL_CPU_MASK' => array(
		"Y" => GetMessage("SEOFILTER_URL_CPU_MASK_YES"),
		"N" => GetMessage("SEOFILTER_URL_CPU_MASK_NO")
	),
);
function SelectBoxFromArrayGroup(
	$strBoxName,
	$db_array,
	$strSelectedVal = "",
	$strDetText = "",
	$field1="class='typeselect'",
	$go = false,
	$form="form1"
	)
{
	$boxName = htmlspecialcharsbx($strBoxName);
	if($go)
	{
		$funName = preg_replace("/[^a-z0-9_]/i", "", $strBoxName);
		$jsName = CUtil::JSEscape($strBoxName);

		$strReturnBox = "<script type=\"text/javascript\">\n".
			"function ".$funName."LinkUp()\n".
			"{var number = document.".$form."['".$jsName."'].selectedIndex;\n".
			"if(document.".$form."['".$jsName."'].options[number].value!=\"0\"){ \n".
			"document.".$form."['".$jsName."_SELECTED'].value=\"yes\";\n".
			"document.".$form.".submit();\n".
			"}}\n".
			"</script>\n";
		$strReturnBox .= '<input type="hidden" name="'.$boxName.'_SELECTED" id="'.$boxName.'_SELECTED" value="">';
		$strReturnBox .= '<select '.$field1.' name="'.$boxName.'" id="'.$boxName.'" onchange="'.$funName.'LinkUp()" class="typeselect">';
	}
	else
	{
		$strReturnBox = '<select '.$field1.' name="'.$boxName.'" id="'.$boxName.'">';
	}
	if($strDetText <> '')
		$strReturnBox .= '<option value="">'.$strDetText.'</option>';
	foreach ($db_array as $iblId => $selects) {
		$strReturnBox .= '<optgroup label="'.$selects['name'].'">';
		foreach ($selects['item'] as $secId => $select) {
			$strReturnBox .= '<option';
			if(strcasecmp($secId, $strSelectedVal) == 0)
				$strReturnBox .= ' selected';
			$strReturnBox .= ' value="'.$secId.'">'.htmlspecialcharsbx($select).'</option>';
		}
		$strReturnBox .= '</optgroup>';
	}

	return $strReturnBox.'</select>';
}
	?><tr>
		<td valign="top"><?=Loc::getMessage("SEOFILTER_CATTABLE_ACTIVE");?>:</td>
		<td><?
			echo SelectBoxFromArray("filter_active", array('REFERENCE' => array_values($reess['ACTIVE']), 'REFERENCE_ID' => array_keys($reess['ACTIVE'])), $filter_active, Loc::getMessage('SEOFILTER_FILTER_SELECT'), "");
		?></td>
	</tr><?
	?><tr>
		<td valign="top"><?=Loc::getMessage("SEOFILTER_CATTABLE_EVIEW");?>:</td>
		<td><?
			echo SelectBoxFromArray("filter_eview", array('REFERENCE' => array_values($reess['EVIEW']), 'REFERENCE_ID' => array_keys($reess['EVIEW'])), $filter_eview, Loc::getMessage('SEOFILTER_FILTER_SELECT'), "");
		?></td>
	</tr><?
	?><tr>
		<td valign="top"><?=Loc::getMessage("SEOFILTER_CATTABLE_SVIEW");?>:</td>
		<td><?
			echo SelectBoxFromArray("filter_sview", array('REFERENCE' => array_values($reess['SVIEW']), 'REFERENCE_ID' => array_keys($reess['SVIEW'])), $filter_sview, Loc::getMessage('SEOFILTER_FILTER_SELECT'), "");
		?></td>
	</tr><?
	?><td valign="top"><?=Loc::getMessage("SEOFILTER_CATTABLE_IBLOCK_ID");?>:</td>
		<td><?
			echo SelectBoxFromArray("filter_iblock_id", array('REFERENCE' => array_values($reess['IBLOCK_ID']), 'REFERENCE_ID' => array_keys($reess['IBLOCK_ID'])), $filter_iblock_id, Loc::getMessage('SEOFILTER_FILTER_SELECT'), "");
		?></td>
	</tr><?
	?><td valign="top"><?=Loc::getMessage("SEOFILTER_CATTABLE_SECTION_ID");?>:</td>
		<td><?
			if(count($catalogIb) > 1)
				echo SelectBoxFromArrayGroup("filter_section_id", $reess['SECTION_ID'], $filter_section_id, Loc::getMessage('SEOFILTER_FILTER_SELECT'), "");
			else
				echo SelectBoxFromArray("filter_section_id", array('REFERENCE' => array_values($reess['SECTION_ID']), 'REFERENCE_ID' => array_keys($reess['SECTION_ID'])), $filter_section_id, Loc::getMessage('SEOFILTER_FILTER_SELECT'), "");
		?></td>
	</tr><?
	?><tr>
		<td valign="top"><?=Loc::getMessage("SEOFILTER_CATTABLE_SITE_ID");?>:</td>
		<td><?
			echo SelectBoxFromArray("filter_site_id", array('REFERENCE' => array_values($reess['SITE_ID']), 'REFERENCE_ID' => array_keys($reess['SITE_ID'])), $filter_site_id, Loc::getMessage('SEOFILTER_FILTER_SELECT'), "");
		?></td><?
	?></tr><?
	?><tr>
		<td valign="top"><?=Loc::getMessage("SEOFILTER_CATTABLE_URL_CPU_MASK");?>:</td>
		<td><?
			echo SelectBoxFromArray("filter_url_cpu_mask", array('REFERENCE' => array_values($reess['URL_CPU_MASK']), 'REFERENCE_ID' => array_keys($reess['URL_CPU_MASK'])), $filter_url_cpu_mask, Loc::getMessage('SEOFILTER_FILTER_SELECT'), "");
		?></td>
	</tr><?
	?><tr>
		<td valign="top"><?=Loc::getMessage("SEOFILTER_CATTABLE_URL_CPU");?>:</td>
		<td><input name=filter_url_cpu value="<?=$filter_url_cpu;?>"/></td><?
	?></tr><?
	?><tr>
		<td valign="top"><?=Loc::getMessage("SEOFILTER_CATTABLE_URL_LANDING");?>:</td>
		<td><input name=filter_list_cpu value="<?=$filter_list_cpu;?>"/></td><?
	?></tr><?
$LFilter->Buttons(array(
	'table_id' => $sTableID,
	'url' => $APPLICATION->GetCurPage(),
	'form' => 'find_form'
));

$LFilter->End();

?></form><?
$lAdmin->DisplayList();

if($not && Loader::includeSharewareModule('zverushki.seofilter') == Loader::MODULE_DEMO){
	?><style type="text/css">
		.bx-core-adm-dialog div.feedback-body{font-size: 15px;}
		.feedback-body li{margin-bottom: 5px}
	</style><script type="text/javascript">
		BX.ready(function(){
			var addAnswer = new BX.CDialog({
				title: '<?=Loc::getMessage('ZVEROI_CATTABLE_FEEDBACK_TEXT_TITLE')?>',
				content: '<?=Loc::getMessage('ZVEROI_CATTABLE_FEEDBACK_TEXT')?>',
				closeIcon: {right: "20px", top: "10px"},
				zIndex: 0,
				resizable: false,
				width: 500,
				height: 250,
				draggable: true
			});
			setTimeout(function(){
				addAnswer.Show();
			}, 1000*60);

		});
	</script><?
}
require ($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');