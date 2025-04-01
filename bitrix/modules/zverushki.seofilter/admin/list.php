<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Application,
	Bitrix\Main\Localization\Loc,
	\Zverushki\Seofilter\Internals,
	Zverushki\Seofilter\configuration;
	$Context = Application::getInstance()->getContext();
	$Request = $Context->getRequest();
	$purification = configuration::getOption('purification', '-');

	$sTableID = 'link'.md5($moduleId);
	$order = !isset($order) ? 'asc' : $order;
	$by = !isset($by) ? 'ID' : strtoupper($by);

	$oSort = new CAdminSorting($sTableID, $by, $order);
	$lAdmin = new CAdminList($sTableID, $oSort);

	$filter = Array(
		'IBLOCK_ID' => $iblockId,
		'SETTING_ID' => $id
	);
	if($purification == 'hide')
		$filter['ACTIVE'] = 'Y';

	$arSelect = array(
		'ID',
		'ACTIVE',
		'URL_CPU',
		'PAGE_TITLE',
		'PAGE_SECTION_TITLE',
		'DATE_ELEMENT',
		'COUNT'
	);

	$__objSettings = Internals\LandingTable::getList([
				'filter' => $filter,
				'select' => $arSelect,
				'order' => [$by => $order]
			]);

	$dbRes = new CAdminResult($__objSettings, $sTableID);
	$dbRes->NavStart();

	$lAdmin->NavText($dbRes->GetNavPrint(Loc::getMessage('SALE_PRLIST')));
	$lAdmin->AddHeaders(array(
		array('id' => 'ACTIVE', 'content' => Loc::getMessage('SEOFILTER_TABLE_LINK_ACTIVE'), 'sort' => 'ACTIVE', 'default' => true),
		array('id' => 'URL_CPU', 'content' => Loc::getMessage('SEOFILTER_TABLE_LINK_TITLE_URL_CPU'), 'sort' => 'URL_CPU', 'default' => true),
		array('id' => 'PAGE_TITLE', 'content' => Loc::getMessage('SEOFILTER_TABLE_LINK_TITLE_PAGE_TITLE'), 'sort' => 'PAGE_TITLE', 'default' => true),
		array('id' => 'PAGE_SECTION_TITLE', 'content' => Loc::getMessage('SEOFILTER_TABLE_LINK_TITLE_SECTION_TITLE'), 'sort' => 'PAGE_SECTION_TITLE', 'default' => true),
		array('id' => 'DATE_ELEMENT', 'content' => Loc::getMessage('SEOFILTER_TABLE_LINK_DATE_ELEMENT'), 'sort' => 'DATE_ELEMENT', 'default' => true),
		array('id' => 'COUNT', 'content' => Loc::getMessage('SEOFILTER_TABLE_LINK_TITLE_COUNT'), 'default' => true),
	));
	$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();
	while ($a = $dbRes->fetch()) {
		$Row =& $lAdmin->AddRow($a['ID'], $a, "");

		$Row->AddViewField('ACTIVE', '<img src="/bitrix/images/sale/'.($a['ACTIVE'] == 'Y' ? 'green' : 'red').'.gif" title="'.Loc::getMessage('SEOFILTER_TABLE_LINK_ACTIVE_IS_'.$a['ACTIVE']).'">');
		$Row->AddViewField('PAGE_TITLE', htmlspecialcharsback($a['PAGE_TITLE']));
		$Row->AddViewField('PAGE_SECTION_TITLE', htmlspecialcharsback($a['PAGE_SECTION_TITLE']));
		$Row->AddViewField('URL_CPU', '<a href="'.$a['URL_CPU'].'" target="_blank">'.$a['URL_CPU'].'</a>');
	}

	$aContext = [];
	$lAdmin->AddAdminContextMenu($aContext);
	$lAdmin->CheckListMode();

	?><?
	$lAdmin->bCanBeEdited = false;
	$lAdmin->DisplayList(array("FIX_HEADER" => false, "FIX_FOOTER" => false, "context_menu" => false, "context_ctrl" => false));?>