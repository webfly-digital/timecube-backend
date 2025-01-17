<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */
/** @global CUserTypeManager $USER_FIELD_MANAGER */
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Bitrix\Iblock;
use Bitrix\Currency;

global $USER_FIELD_MANAGER;

if (!Loader::includeModule('iblock'))
	return;
$boolCatalog = Loader::includeModule('catalog');
$iblockExists = (!empty($arCurrentValues['IBLOCK_ID']) && (int)$arCurrentValues['IBLOCK_ID'] > 0);

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$iblockFilter = (
	!empty($arCurrentValues['IBLOCK_TYPE'])
	? array('TYPE' => $arCurrentValues['IBLOCK_TYPE'], 'ACTIVE' => 'Y')
	: array('ACTIVE' => 'Y')
);
$rsIBlock = CIBlock::GetList(array('SORT' => 'ASC'), $iblockFilter);
while ($arr = $rsIBlock->Fetch())
	$arIBlock[$arr['ID']] = '['.$arr['ID'].'] '.$arr['NAME'];
unset($arr, $rsIBlock, $iblockFilter);
$arRCM = array(
	'bestsell' => GetMessage('CP_BC_TPL_RCM_BESTSELLERS'),
	'personal' => GetMessage('CP_BC_TPL_RCM_PERSONAL'),
	'similar_sell' => GetMessage('CP_BC_TPL_RCM_SOLD_WITH'),
	'similar_view' => GetMessage('CP_BC_TPL_RCM_VIEWED_WITH'),
	'similar' => GetMessage('CP_BC_TPL_RCM_SIMILAR'),
	'any_similar' => GetMessage('CP_BC_TPL_RCM_SIMILAR_ANY'),
	'any_personal' => GetMessage('CP_BC_TPL_RCM_PERSONAL_WBEST'),
	'any' => GetMessage('CP_BC_TPL_RCM_RAND')
);
$arProperty = array();
$arProperty_N = array();
$arProperty_X = array();
$arProperty_F = array();
if ($iblockExists)
{
	$propertyIterator = Iblock\PropertyTable::getList(array(
		'select' => array('ID', 'IBLOCK_ID', 'NAME', 'CODE', 'PROPERTY_TYPE', 'MULTIPLE', 'LINK_IBLOCK_ID', 'USER_TYPE'),
		'filter' => array('=IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'], '=ACTIVE' => 'Y'),
		'order' => array('NAME' => 'ASC', 'SORT' => 'ASC')
	));
	while ($property = $propertyIterator->fetch())
	{
		$propertyCode = (string)$property['CODE'];

		if ($propertyCode == '') continue;
		if ($propertyCode == 'MINIMUM_PRICE') continue;

		$propertyName = '['.$propertyCode.'] '.$property['NAME'];
		$propertyCode = "PROPERTY_".$propertyCode;
		if ($property['PROPERTY_TYPE'] != Iblock\PropertyTable::TYPE_FILE)
		{
			$arProperty[$propertyCode] = $propertyName;

			if ($property['MULTIPLE'] == 'Y')
				$arProperty_X[$propertyCode] = $propertyName;
			elseif ($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_LIST)
				$arProperty_X[$propertyCode] = $propertyName;
			elseif ($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_ELEMENT && (int)$property['LINK_IBLOCK_ID'] > 0)
				$arProperty_X[$propertyCode] = $propertyName;
		}
		else
		{
			if ($property['MULTIPLE'] == 'N')
				$arProperty_F[$propertyCode] = $propertyName;
		}

		if ($property['PROPERTY_TYPE'] == Iblock\PropertyTable::TYPE_NUMBER)
			$arProperty_N[$propertyCode] = $propertyName;
	}
	unset($propertyCode, $propertyName, $property, $propertyIterator);
}
$arProperty_LNS = $arProperty;

$arSKU = false;
$boolSKU = false;
if ($boolCatalog && (isset($arCurrentValues['IBLOCK_ID']) && (int)$arCurrentValues['IBLOCK_ID']) > 0)
{
	$arSKU = CCatalogSKU::GetInfoByProductIBlock($arCurrentValues['IBLOCK_ID']);
	$boolSKU = !empty($arSKU) && is_array($arSKU);
}

$arViewModeList = array(
	'LIST' => GetMessage('CPT_BC_SECTIONS_VIEW_MODE_LIST'),
	'LINE' => GetMessage('CPT_BC_SECTIONS_VIEW_MODE_LINE'),
	'TEXT' => GetMessage('CPT_BC_SECTIONS_VIEW_MODE_TEXT'),
	'TILE' => GetMessage('CPT_BC_SECTIONS_VIEW_MODE_TILE')
);

$arFilterViewModeList = array(
	"VERTICAL" => GetMessage("CPT_BC_FILTER_VIEW_MODE_VERTICAL"),
	"HORIZONTAL" => GetMessage("CPT_BC_FILTER_VIEW_MODE_HORIZONTAL")
);

$arTemplateParameters = array(
	"SECTIONS_VIEW_MODE" => array(
		"PARENT" => "SECTIONS_SETTINGS",
		"NAME" => GetMessage('CPT_BC_SECTIONS_VIEW_MODE'),
		"TYPE" => "LIST",
		"VALUES" => $arViewModeList,
		"MULTIPLE" => "N",
		"DEFAULT" => "LIST"
	),
	"SECTIONS_MAIN_VIEW_MODE" => array(
		"PARENT" => "SECTIONS_SETTINGS",
		"NAME" => GetMessage('CPT_BC_SECTIONS_MAIN_VIEW_MODE'),
		"TYPE" => "LIST",
		"VALUES" => $arViewModeList,
		"MULTIPLE" => "N",
		"DEFAULT" => "LIST"
	)
);

$arTemplateParameters["SECTIONS_SHOW_DESCRIPTION"] = array(
	"PARENT" => "SECTIONS_SETTINGS",
	"NAME" => GetMessage('CPT_BC_SECTIONS_SHOW_DESCRIPTION'),
	"TYPE" => "CHECKBOX",
        "DEFAULT" => "N"
);


if (isset($arCurrentValues['USE_FILTER']) && $arCurrentValues['USE_FILTER'] == 'Y') {
$arTemplateParameters["FILTER_VIEW_MODE"] = array(
	"PARENT" => "FILTER_SETTINGS",
	"NAME" => GetMessage('CPT_BC_FILTER_VIEW_MODE'),
	"TYPE" => "LIST",
	"VALUES" => $arFilterViewModeList,
	"DEFAULT" => "VERTICAL",
	"HIDDEN" => (!isset($arCurrentValues['USE_FILTER']) || 'N' == $arCurrentValues['USE_FILTER'])
);

$arTemplateParameters["DISPLAY_ELEMENT_COUNT"] = array(
	"PARENT" => "FILTER_SETTINGS",
	"NAME" => GetMessage('KZNC_DISPLAY_ELEMENT_COUNT_FILTER'),
	"TYPE" => "CHECKBOX",
        "DEFAULT" => "N"
);
$arTemplateParameters["HIDE_FILTER_MOBILE"] = array(
	"PARENT" => "FILTER_SETTINGS",
	"NAME" => GetMessage('KZNC_HIDE_FILTER_MOBILE'),
	"TYPE" => "CHECKBOX",
        "DEFAULT" => "Y"
);
}


if (isset($arCurrentValues['IBLOCK_ID']) && (int)$arCurrentValues['IBLOCK_ID'] > 0)
{
	$arAllPropList = array();
	$arFilePropList = array(
		'-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
	);
	$arListPropList = array(
		'-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
	);
	$arHighloadPropList = array(
		'-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
	);
	$rsProps = CIBlockProperty::GetList(
		array('SORT' => 'ASC', 'ID' => 'ASC'),
		array('IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'], 'ACTIVE' => 'Y')
	);
	while ($arProp = $rsProps->Fetch())
	{
		$strPropName = '['.$arProp['ID'].']'.('' != $arProp['CODE'] ? '['.$arProp['CODE'].']' : '').' '.$arProp['NAME'];
		if ('' == $arProp['CODE'])
			$arProp['CODE'] = $arProp['ID'];
		$arAllPropList[$arProp['CODE']] = $strPropName;
		if ('F' == $arProp['PROPERTY_TYPE'])
			$arFilePropList[$arProp['CODE']] = $strPropName;
		if ('L' == $arProp['PROPERTY_TYPE'])
			$arListPropList[$arProp['CODE']] = $strPropName;
		if ('S' == $arProp['PROPERTY_TYPE'] && 'directory' == $arProp['USER_TYPE'] && CIBlockPriceTools::checkPropDirectory($arProp))
			$arHighloadPropList[$arProp['CODE']] = $strPropName;
	}

	$arTemplateParameters['ADD_PICT_PROP'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BC_TPL_ADD_PICT_PROP'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'N',
		'ADDITIONAL_VALUES' => 'N',
		'REFRESH' => 'N',
		'DEFAULT' => '-',
		'VALUES' => $arFilePropList
	);

	if ($boolSKU)
	{
		$arDisplayModeList = array(
			'N' => GetMessage('CP_BC_TPL_DML_SIMPLE'),
			'Y' => GetMessage('CP_BC_TPL_DML_EXT')
		);
		$arTemplateParameters['PRODUCT_DISPLAY_MODE'] = array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('CP_BC_TPL_PRODUCT_DISPLAY_MODE'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'N',
			'ADDITIONAL_VALUES' => 'N',
			'REFRESH' => 'Y',
			'DEFAULT' => 'N',
			'VALUES' => $arDisplayModeList
		);
		$arAllOfferPropList = array();
		$arFileOfferPropList = array(
			'-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
		);
		$arTreeOfferPropList = array(
			'-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
		);
		$rsProps = CIBlockProperty::GetList(
			array('SORT' => 'ASC', 'ID' => 'ASC'),
			array('IBLOCK_ID' => $arSKU['IBLOCK_ID'], 'ACTIVE' => 'Y')
		);
		while ($arProp = $rsProps->Fetch())
		{
			if ($arProp['ID'] == $arSKU['SKU_PROPERTY_ID'])
				continue;
			$arProp['USER_TYPE'] = (string)$arProp['USER_TYPE'];
			$strPropName = '['.$arProp['ID'].']'.('' != $arProp['CODE'] ? '['.$arProp['CODE'].']' : '').' '.$arProp['NAME'];
			if ('' == $arProp['CODE'])
				$arProp['CODE'] = $arProp['ID'];
			$arAllOfferPropList[$arProp['CODE']] = $strPropName;
			if ('F' == $arProp['PROPERTY_TYPE'])
				$arFileOfferPropList[$arProp['CODE']] = $strPropName;
			if ('N' != $arProp['MULTIPLE'])
				continue;
			if (
				'L' == $arProp['PROPERTY_TYPE']
				|| 'E' == $arProp['PROPERTY_TYPE']
				|| ('S' == $arProp['PROPERTY_TYPE'] && 'directory' == $arProp['USER_TYPE'] && CIBlockPriceTools::checkPropDirectory($arProp))
			)
				$arTreeOfferPropList[$arProp['CODE']] = $strPropName;
		}
		$arTemplateParameters['OFFER_ADD_PICT_PROP'] = array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('CP_BC_TPL_OFFER_ADD_PICT_PROP'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'N',
			'ADDITIONAL_VALUES' => 'N',
			'REFRESH' => 'N',
			'DEFAULT' => '-',
			'VALUES' => $arFileOfferPropList
		);
		$arTemplateParameters['OFFER_TREE_PROPS'] = array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('CP_BC_TPL_OFFER_TREE_PROPS'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'Y',
			'ADDITIONAL_VALUES' => 'N',
			'REFRESH' => 'N',
			'DEFAULT' => '-',
			'VALUES' => $arTreeOfferPropList
		);
	}
}

$arTemplateParameters['NO_TABS'] = array(
	'PARENT' => 'DETAIL_SETTINGS',
	'NAME' => GetMessage('NO_TABS'),
	"TYPE" => "CHECKBOX",
        "DEFAULT" => "N"
);

$arTemplateParameters['ADDITIONAL_TAB_SHOW'] = array(
	'PARENT' => 'DETAIL_SETTINGS',
	'NAME' => GetMessage('KZNC_ADDITIONAL_TAB_SHOW'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'N',
	'SORT' => 800,
	'REFRESH' => 'Y'
);

if (isset($arCurrentValues['ADDITIONAL_TAB_SHOW']) && 'Y' == $arCurrentValues['ADDITIONAL_TAB_SHOW']) {
	$arTemplateParameters['ADDITIONAL_TAB_PATH'] = array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => GetMessage('KZNC_ADDITIONAL_TAB_PATH'),
		'MULTIPLE' => 'N',
		'TYPE' => 'STRING',
		'SORT' => 800
	);
	$arTemplateParameters['ADDITIONAL_TAB_NAME'] = array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => GetMessage('KZNC_ADDITIONAL_TAB_NAME'),
		'DEFAULT' => GetMessage('KZNC_ADDITIONAL_TAB_NAME_DEFAULT'),
		'TYPE' => 'STRING',
		'SORT' => 800
	);
}

$arTemplateParameters['ADDITIONAL_DETAIL_INFO'] = array(
	'PARENT' => 'DETAIL_SETTINGS',
	'NAME' => GetMessage('KZNC_ADDITIONAL_DETAIL_INFO'),
	'TYPE' => 'STRING',
	'DEFAULT' => '/include/additional_detail_info.php',
	'SORT' => 1000
);

$arTemplateParameters['DETAIL_DISPLAY_NAME'] = array(
	'PARENT' => 'DETAIL_SETTINGS',
	'NAME' => GetMessage('CP_BC_TPL_DETAIL_DISPLAY_NAME'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y'
);

$detailPictMode = array(
	'IMG' => GetMessage('DETAIL_DETAIL_PICTURE_MODE_IMG'),
	'POPUP' => GetMessage('DETAIL_DETAIL_PICTURE_MODE_POPUP'),
	'MAGNIFIER' => GetMessage('DETAIL_DETAIL_PICTURE_MODE_MAGNIFIER'),
/*	'GALLERY' => GetMessage('DETAIL_DETAIL_PICTURE_MODE_GALLERY') */
);

$displayPreviewTextMode = array(
	'H' => GetMessage('CP_BC_TPL_DETAIL_DISPLAY_PREVIEW_TEXT_MODE_HIDE'),
	'E' => GetMessage('CP_BC_TPL_DETAIL_DISPLAY_PREVIEW_TEXT_MODE_EMPTY_DETAIL'),
	'S' => GetMessage('CP_BC_TPL_DETAIL_DISPLAY_PREVIEW_TEXT_MODE_SHOW')
);

$arTemplateParameters['DETAIL_DISPLAY_PREVIEW_TEXT_MODE'] = array(
	'PARENT' => 'DETAIL_SETTINGS',
	'NAME' => GetMessage('CP_BC_TPL_DETAIL_DISPLAY_PREVIEW_TEXT_MODE'),
	'TYPE' => 'LIST',
	'VALUES' => $displayPreviewTextMode,
	'DEFAULT' => 'E'
);

$arTemplateParameters['HIDE_PREVIEW_PROPS_INLIST'] = array(
	'PARENT' => 'DETAIL_SETTINGS',
	'NAME' => GetMessage('CP_BCE_TPL_HIDE_PREVIEW_PROPS_INLIST'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y'
);

$props_view = array(
	'LIST' => GetMessage('PROPS_TAB_VIEW_LIST'),
	'TABLE' => GetMessage('PROPS_TAB_VIEW_TABLE')
);

$arTemplateParameters["PROPS_TAB_VIEW"] = array(
        "PARENT" => "DETAIL_SETTINGS",
        "NAME" => GetMessage("PROPS_TAB_VIEW"),
        "TYPE" => "LIST",
        "VALUES" => $props_view,
        'DEFAULT' => 'TABLE',
);

$arTemplateParameters["PREVIEW_DETAIL_PROPERTY_CODE"] = array(
        "PARENT" => "DETAIL_SETTINGS",
        "NAME" => GetMessage("PREVIEW_DETAIL_PROPERTY_CODE"),
        "TYPE" => "LIST",
        "MULTIPLE" => "Y",
        "ADDITIONAL_VALUES" => "Y",
        "VALUES" => $arProperty_LNS,
);

$arTemplateParameters['SHOW_MEASURE'] = array(
	'PARENT' => 'PRICES',
	'NAME' => GetMessage('KZNC_SHOW_MEASURE'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'N',
);

$arTemplateParameters['DETAIL_DISPLAY_SHOW_FILES'] = array(
	'PARENT' => 'DETAIL_SETTINGS',
	'NAME' => GetMessage('DETAIL_DISPLAY_SHOW_FILES'),
	"TYPE" => "CHECKBOX",
        "DEFAULT" => "N",
        "REFRESH" => "Y"
);

    if (isset($arCurrentValues['DETAIL_DISPLAY_SHOW_FILES']) && 'Y' == $arCurrentValues['DETAIL_DISPLAY_SHOW_FILES'])
    {
	$arTemplateParameters['DISPLAY_SHOW_FILES_TYPE'] = array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => GetMessage('DISPLAY_SHOW_FILES_TYPE'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'N',
                'VALUES' => array(
			'load' => GetMessage('CP_BC_TPL_SHOW_FILES_TYPE_LOAD'),
			'new_window' => GetMessage('CP_BC_TPL_SHOW_FILES_TYPE_NEW')
                ),
                "DEFAULT" => "load"
	);
    }

$arTemplateParameters['DETAIL_DISPLAY_SHOW_VIDEO'] = array(
	'PARENT' => 'DETAIL_SETTINGS',
	'NAME' => GetMessage('DETAIL_DISPLAY_SHOW_VIDEO'),
	"TYPE" => "CHECKBOX",
        "DEFAULT" => "N",
        'REFRESH' => 'Y',
);

if(isset($arCurrentValues['DETAIL_DISPLAY_SHOW_VIDEO']) && $arCurrentValues['DETAIL_DISPLAY_SHOW_VIDEO'] == "Y") {
    $arVideoType = GetMessage('VIDEO_TYPE_ARRAY');
    $arTemplateParameters['VIDEO_TYPE'] = array(
	'PARENT' => 'DETAIL_SETTINGS',
	'NAME' => GetMessage('VIDEO_TYPE'),
	"TYPE" => "LIST",
        "DEFAULT" => "GRID",
        "VALUES" => $arVideoType,
        'REFRESH' => 'Y',
    );
    
    $arVideoPlayer = GetMessage('VIDEO_PLAYER_ARRAY');
    $arTemplateParameters['VIDEO_PLAYER'] = array(
	'PARENT' => 'DETAIL_SETTINGS',
	'NAME' => GetMessage('VIDEO_PLAYER'),
	"TYPE" => "LIST",
        "DEFAULT" => "MEJ",
        "VALUES" => $arVideoPlayer,
        'REFRESH' => 'Y',
    );
    
    if(isset($arCurrentValues['VIDEO_PLAYER']) && $arCurrentValues['VIDEO_PLAYER'] == "MEJ") {
    
        $arTemplateParameters['VIDEO_PLAYER_FULLSCREEN'] = array(
            'PARENT' => 'DETAIL_SETTINGS',
            'NAME' => GetMessage('VIDEO_PLAYER_FULLSCREEN'),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ); 
    }
    
}



if ($boolCatalog)
{
	$arTemplateParameters['USE_COMMON_SETTINGS_BASKET_POPUP'] = array(
		'PARENT' => 'BASKET',
		'NAME' => GetMessage('CP_BC_TPL_USE_COMMON_SETTINGS_BASKET_POPUP'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y'
	);
	$useCommonSettingsBasketPopup = (
		isset($arCurrentValues['USE_COMMON_SETTINGS_BASKET_POPUP'])
		&& $arCurrentValues['USE_COMMON_SETTINGS_BASKET_POPUP'] == 'Y'
	);
	$addToBasketActions = array(
		'BUY' => GetMessage('ADD_TO_BASKET_ACTION_BUY'),
		'ADD' => GetMessage('ADD_TO_BASKET_ACTION_ADD')
	);
	$arTemplateParameters['COMMON_ADD_TO_BASKET_ACTION'] = array(
		'PARENT' => 'BASKET',
		'NAME' => GetMessage('CP_BC_TPL_COMMON_ADD_TO_BASKET_ACTION'),
		'TYPE' => 'LIST',
		'VALUES' => $addToBasketActions,
		'DEFAULT' => 'ADD',
		'REFRESH' => 'N',
		'HIDDEN' => ($useCommonSettingsBasketPopup ? 'N' : 'Y')
	);
	$arTemplateParameters['TOP_ADD_TO_BASKET_ACTION'] = array(
		'PARENT' => 'BASKET',
		'NAME' => GetMessage('CP_BC_TPL_TOP_ADD_TO_BASKET_ACTION'),
		'TYPE' => 'LIST',
		'VALUES' => $addToBasketActions,
		'DEFAULT' => 'ADD',
		'REFRESH' => 'N',
		'HIDDEN' => (!$useCommonSettingsBasketPopup ? 'N' : 'Y')
	);
	$arTemplateParameters['SECTION_ADD_TO_BASKET_ACTION'] = array(
		'PARENT' => 'BASKET',
		'NAME' => GetMessage('CP_BC_TPL_SECTION_ADD_TO_BASKET_ACTION'),
		'TYPE' => 'LIST',
		'VALUES' => $addToBasketActions,
		'DEFAULT' => 'ADD',
		'REFRESH' => 'N',
		'HIDDEN' => (!$useCommonSettingsBasketPopup ? 'N' : 'Y')
	);
	$arTemplateParameters['DETAIL_ADD_TO_BASKET_ACTION'] = array(
		'PARENT' => 'BASKET',
		'NAME' => GetMessage('CP_BC_TPL_DETAIL_ADD_TO_BASKET_ACTION'),
		'TYPE' => 'LIST',
		'VALUES' => $addToBasketActions,
		'DEFAULT' => 'BUY',
		'REFRESH' => 'N',
		'MULTIPLE' => 'Y',
		'HIDDEN' => (!$useCommonSettingsBasketPopup ? 'N' : 'Y')
	);
	/*	$arTemplateParameters['PRODUCT_SUBSCRIPTION'] = array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('CP_BC_TPL_PRODUCT_SUBSCRIPTION'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
		); */
	$arTemplateParameters['SHOW_DISCOUNT_PERCENT'] = array(
		'PARENT' => 'PRICES',
		'NAME' => GetMessage('CP_BC_TPL_SHOW_DISCOUNT_PERCENT'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y',
		'SORT' => 800
	);
	$arTemplateParameters['SHOW_OLD_PRICE'] = array(
		'PARENT' => 'PRICES',
		'NAME' => GetMessage('CP_BC_TPL_SHOW_OLD_PRICE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		'SORT' => 800
	);
	$arTemplateParameters['DETAIL_SHOW_MAX_QUANTITY'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BC_TPL_DETAIL_SHOW_MAX_QUANTITY'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);
	if (isset($arCurrentValues['USE_PRODUCT_QUANTITY']) && $arCurrentValues['USE_PRODUCT_QUANTITY'] === 'Y')
	{
		$arTemplateParameters['DETAIL_SHOW_BASIS_PRICE'] = array(
			"PARENT" => "BASKET",
			"NAME" => GetMessage("CP_BC_TPL_DETAIL_SHOW_BASIS_PRICE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"REFRESH" => "N",
		);
	}
}
$arTemplateParameters['USE_FAVORITES'] = array(
	'PARENT' => 'KZNC_BUTTON_BLOCK',
	'NAME' => GetMessage('KZNC_USE_FAVORITES'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
	'REFRESH' => 'Y',
);
if (isset($arCurrentValues['USE_FAVORITES']) && 'Y' == $arCurrentValues['USE_FAVORITES']) {
	$arTemplateParameters['USE_FAVORITES_TEXT'] = array(
		'PARENT' => 'KZNC_BUTTON_BLOCK',
		'NAME' => GetMessage('KZNC_USE_FAVORITES_TEXT'),
		'TYPE' => 'STRING',
		'DEFAULT' => GetMessage('KZNC_USE_FAVORITES_TEXT_DEFAULT')
	);
}
$arTemplateParameters['USE_SHARE'] = array(
	'PARENT' => 'KZNC_BUTTON_BLOCK',
	'NAME' => GetMessage('KZNC_USE_SHARE'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
	'REFRESH' => 'Y',
);
if (isset($arCurrentValues['USE_SHARE']) && 'Y' == $arCurrentValues['USE_SHARE']) {
	$arTemplateParameters['USE_SHARE_TEXT'] = array(
		'PARENT' => 'KZNC_BUTTON_BLOCK',
		'NAME' => GetMessage('KZNC_USE_SHARE_TEXT'),
		'TYPE' => 'STRING',
		'DEFAULT' => GetMessage('KZNC_USE_SHARE_TEXT_DEFAULT')
	);
}
$arTemplateParameters['USE_ONE_CLICK'] = array(
	'PARENT' => 'KZNC_BUTTON_BLOCK',
	'NAME' => GetMessage('KZNC_USE_ONE_CLICK'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
	'REFRESH' => 'Y',
);
if (isset($arCurrentValues['USE_ONE_CLICK']) && 'Y' == $arCurrentValues['USE_ONE_CLICK']) {
	$arTemplateParameters['USE_ONE_CLICK_TEXT'] = array(
		'PARENT' => 'KZNC_BUTTON_BLOCK',
		'NAME' => GetMessage('KZNC_USE_ONE_CLICK_TEXT'),
		'TYPE' => 'STRING',
		'DEFAULT' => GetMessage('KZNC_USE_ONE_CLICK_TEXT_DEFAULT')
	);
}
$arTemplateParameters['MESS_BTN_BUY'] = array(
	'PARENT' => 'KZNC_BUTTON_BLOCK',
	'NAME' => GetMessage('CP_BC_TPL_MESS_BTN_BUY'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('CP_BCE_TPL_MESS_BTN_BUY_DEFAULT')
);
$arTemplateParameters['MESS_BTN_REQUEST'] = array(
	'PARENT' => 'KZNC_BUTTON_BLOCK',
	'NAME' => GetMessage('MESS_BTN_REQUEST'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('MESS_BTN_REQUEST_DEFAULT')
);
if (isset($arCurrentValues['USE_COMPARE']) && 'Y' == $arCurrentValues['USE_COMPARE'])
{
	$arTemplateParameters['MESS_BTN_COMPARE'] = array(
		'PARENT' => 'KZNC_BUTTON_BLOCK',
		'NAME' => GetMessage('CP_BC_TPL_MESS_BTN_COMPARE'),
		'TYPE' => 'STRING',
		'DEFAULT' => GetMessage('CP_BC_TPL_MESS_BTN_COMPARE_DEFAULT')
	);
}
$arTemplateParameters['DETAIL_USE_VOTE_RATING'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BC_TPL_DETAIL_USE_VOTE_RATING'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'N',
	'REFRESH' => 'Y'
);
if (isset($arCurrentValues['DETAIL_USE_VOTE_RATING']) && 'Y' == $arCurrentValues['DETAIL_USE_VOTE_RATING'])
{
	$arTemplateParameters['DETAIL_VOTE_DISPLAY_AS_RATING'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BC_TPL_DETAIL_VOTE_DISPLAY_AS_RATING'),
		'TYPE' => 'LIST',
		'VALUES' => array(
			'rating' => GetMessage('CP_BC_TPL_DVDAR_RATING'),
			'vote_avg' => GetMessage('CP_BC_TPL_DVDAR_AVERAGE'),
		),
		'DEFAULT' => 'rating'
	);
}

$arTemplateParameters['DETAIL_USE_COMMENTS'] = array(
	'PARENT' => 'REVIEW_SETTINGS',
	'NAME' => GetMessage('CP_BC_TPL_DETAIL_USE_COMMENTS'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'N',
	'REFRESH' => 'Y'
);
if (isset($arCurrentValues['DETAIL_USE_COMMENTS']) && 'Y' == $arCurrentValues['DETAIL_USE_COMMENTS'])
{
	if (ModuleManager::isModuleInstalled("blog"))
	{
		$arTemplateParameters['DETAIL_BLOG_USE'] = array(
			'PARENT' => 'REVIEW_SETTINGS',
			'NAME' => GetMessage('CP_BC_TPL_DETAIL_BLOG_USE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y'
		);
		if (isset($arCurrentValues['DETAIL_BLOG_USE']) && $arCurrentValues['DETAIL_BLOG_USE'] == 'Y')
		{
			$arTemplateParameters['DETAIL_BLOG_URL'] = array(
				'PARENT' => 'REVIEW_SETTINGS',
				'NAME' => GetMessage('CP_BCE_DETAIL_TPL_BLOG_URL'),
				'TYPE' => 'STRING',
				'DEFAULT' => 'catalog_comments'
			);
			$arTemplateParameters['DETAIL_BLOG_EMAIL_NOTIFY'] = array(
				'PARENT' => 'REVIEW_SETTINGS',
				'NAME' => GetMessage('CP_BCE_TPL_DETAIL_BLOG_EMAIL_NOTIFY'),
				'TYPE' => 'CHECKBOX',
				'DEFAULT' => 'N'
			);
		}
	}

	$boolRus = false;
	$langBy = "id";
	$langOrder = "asc";
	$rsLangs = CLanguage::GetList($langBy, $langOrder, array('ID' => 'ru',"ACTIVE" => "Y"));
	if ($arLang = $rsLangs->Fetch())
	{
		$boolRus = true;
	}

	if ($boolRus)
	{
		$arTemplateParameters['DETAIL_VK_USE'] = array(
			'PARENT' => 'REVIEW_SETTINGS',
			'NAME' => GetMessage('CP_BC_TPL_DETAIL_VK_USE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y'
		);

		if (isset($arCurrentValues['DETAIL_VK_USE']) && 'Y' == $arCurrentValues['DETAIL_VK_USE'])
		{
			$arTemplateParameters['DETAIL_VK_API_ID'] = array(
				'PARENT' => 'REVIEW_SETTINGS',
				'NAME' => GetMessage('CP_BC_TPL_DETAIL_VK_API_ID'),
				'TYPE' => 'STRING',
				'DEFAULT' => 'API_ID'
			);
		}
	}

	$arTemplateParameters['DETAIL_FB_USE'] = array(
		'PARENT' => 'REVIEW_SETTINGS',
		'NAME' => GetMessage('CP_BC_TPL_DETAIL_FB_USE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y'
	);

	if (isset($arCurrentValues['DETAIL_FB_USE']) && 'Y' == $arCurrentValues['DETAIL_FB_USE'])
	{
		$arTemplateParameters['DETAIL_FB_APP_ID'] = array(
			'PARENT' => 'REVIEW_SETTINGS',
			'NAME' => GetMessage('CP_BC_TPL_DETAIL_FB_APP_ID'),
			'TYPE' => 'STRING',
			'DEFAULT' => ''
		);
	}
}

if (ModuleManager::isModuleInstalled("highloadblock"))
{
	$arTemplateParameters['DETAIL_BRAND_USE'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BC_TPL_DETAIL_BRAND_USE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y'
	);

	if (isset($arCurrentValues['DETAIL_BRAND_USE']) && 'Y' == $arCurrentValues['DETAIL_BRAND_USE'])
	{
		$arTemplateParameters['DETAIL_BRAND_PROP_CODE'] = array(
			'PARENT' => 'VISUAL',
			"NAME" => GetMessage("CP_BC_TPL_DETAIL_PROP_CODE"),
			"TYPE" => "LIST",
			"VALUES" => $arHighloadPropList,
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "Y"
		);
	}
}

if (ModuleManager::isModuleInstalled("sale"))
{
	$arTemplateParameters['USE_SALE_BESTSELLERS'] = array(
		'NAME' => GetMessage('CP_BC_TPL_USE_SALE_BESTSELLERS'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
		'REFRESH' => 'Y'
	);

	$arTemplateParameters['USE_BIG_DATA'] = array(
		'PARENT' => 'BIG_DATA_SETTINGS',
		'NAME' => GetMessage('CP_BC_TPL_USE_BIG_DATA'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
		'REFRESH' => 'Y'
	);
	if (!isset($arCurrentValues['USE_BIG_DATA']) || $arCurrentValues['USE_BIG_DATA'] == 'Y')
	{
		$rcmTypeList = array(
			'bestsell' => GetMessage('CP_BC_TPL_RCM_BESTSELLERS'),
			'personal' => GetMessage('CP_BC_TPL_RCM_PERSONAL'),
			'similar_sell' => GetMessage('CP_BC_TPL_RCM_SOLD_WITH'),
			'similar_view' => GetMessage('CP_BC_TPL_RCM_VIEWED_WITH'),
			'similar' => GetMessage('CP_BC_TPL_RCM_SIMILAR'),
			'any_similar' => GetMessage('CP_BC_TPL_RCM_SIMILAR_ANY'),
			'any_personal' => GetMessage('CP_BC_TPL_RCM_PERSONAL_WBEST'),
			'any' => GetMessage('CP_BC_TPL_RCM_RAND')
		);
		$arTemplateParameters['BIG_DATA_RCM_TYPE'] = array(
			'PARENT' => 'BIG_DATA_SETTINGS',
			'NAME' => GetMessage('CP_BC_TPL_BIG_DATA_RCM_TYPE'),
			'TYPE' => 'LIST',
			'VALUES' => $rcmTypeList
		);
		unset($rcmTypeList);
	}
}

if (isset($arCurrentValues['USE_COMPARE']) && $arCurrentValues['USE_COMPARE'] == 'Y')
{
	$arTemplateParameters['COMPARE_POSITION_FIXED'] = array(
		'PARENT' => 'COMPARE_SETTINGS',
		'NAME' => GetMessage('CPT_BC_TPL_COMPARE_POSITION_FIXED'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
		'REFRESH' => 'Y'
	);
	if (!isset($arCurrentValues['COMPARE_POSITION_FIXED']) || $arCurrentValues['COMPARE_POSITION_FIXED'] == 'Y')
	{
		$positionList = array(
			'top left' => GetMessage('CPT_BC_TPL_PARAM_COMPARE_POSITION_TOP_LEFT'),
			'top right' => GetMessage('CPT_BC_TPL_PARAM_COMPARE_POSITION_TOP_RIGHT'),
			'bottom left' => GetMessage('CPT_BC_TPL_PARAM_COMPARE_POSITION_BOTTOM_LEFT'),
			'bottom right' => GetMessage('CPT_BC_TPL_PARAM_COMPARE_POSITION_BOTTOM_RIGHT')
		);
		$arTemplateParameters['COMPARE_POSITION'] = array(
			'PARENT' => 'COMPARE_SETTINGS',
			'NAME' => GetMessage('CPT_BC_TPL_COMPARE_POSITION'),
			'TYPE' => 'LIST',
			'VALUES' => $positionList,
			'DEFAULT' => 'top left'
		);
		unset($positionList);
	}
}

$arTemplateParameters['SIDEBAR_SECTION_SHOW'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CPT_SIDEBAR_SECTION_SHOW'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
	'SORT' => 800
);
$arTemplateParameters['SIDEBAR_DETAIL_SHOW'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CPT_SIDEBAR_DETAIL_SHOW'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
	'SORT' => 800
);
$arTemplateParameters['SIDEBAR_PATH'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CPT_SIDEBAR_PATH'),
	'TYPE' => 'STRING',
	'SORT' => 800
);

$arTemplateParameters['SHOW_CATALOG_QUANTITY'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('SHOW_CATALOG_QUANTITY'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'N',
	'REFRESH' => 'Y'
);

if ($arCurrentValues['SHOW_CATALOG_QUANTITY'] == 'Y') {
	$arTemplateParameters['IN_STOCK'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('IN_STOCK_TEXT'),
		'TYPE' => 'STRING',
	        'DEFAULT' => GetMessage('IN_STOCK'),
	);
	
	$arTemplateParameters['NOT_IN_STOCK'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('NOT_IN_STOCK_TEXT'),
		'TYPE' => 'STRING',
	        'DEFAULT' => GetMessage('NOT_IN_STOCK'),
	);
}

$arTemplateParameters['SHOW_CATALOG_QUANTITY_CNT'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('SHOW_CATALOG_QUANTITY_CNT'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'N',
	'REFRESH' => 'Y'
);

$arOffersViewModeList = array(
//	'LIST' => GetMessage('OFFERS_LIST_VIEW'),
        'SELECT' => GetMessage('OFFERS_SELECT_VIEW'),
        'CHOISE' => GetMessage('OFFERS_CHOISE_VIEW'),
        'ICONS' => GetMessage('OFFERS_ICONS_VIEW')
);

$arTemplateParameters['HIDE_OFFERS_LIST'] = array(
	'PARENT' => 'OFFERS_SETTINGS',
	'NAME' => GetMessage('HIDE_OFFERS_LIST'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'N',
	'REFRESH' => 'Y'
);

//if ($arCurrentValues['HIDE_OFFERS_LIST'] == 'N') {
//$arTemplateParameters['USE_LINKS_SKU'] = array(
//        'PARENT' => 'OFFERS_SETTINGS',
//        'NAME' => GetMessage('USE_LINKS_SKU'),
//        'TYPE' => 'CHECKBOX',
//        'DEFAULT' => 'Y',
//);
//
//$arTemplateParameters['SELECT_FIRST_SKU'] = array(
//        'PARENT' => 'OFFERS_SETTINGS',
//        'NAME' => GetMessage('SELECT_FIRST_SKU'),
//        'TYPE' => 'CHECKBOX',
//        'DEFAULT' => 'Y',
//);

$arTemplateParameters['CHANGE_TITLE_SKU'] = array(
        'PARENT' => 'OFFERS_SETTINGS',
        'NAME' => GetMessage('CHANGE_TITLE_SKU'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
);

$arTemplateParameters['SKU_SORT_PARAMS'] = array(
        'PARENT' => 'OFFERS_SETTINGS',
        'NAME' => GetMessage('SKU_SORT_PARAMS'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
);

$arTemplateParameters['SHOW_OFFER_PIC_BYCLICK'] = array(
        'PARENT' => 'OFFERS_SETTINGS',
        'NAME' => GetMessage('KZNC_SHOW_OFFER_PIC_BYCLICK'),
        'TYPE' => 'CHECKBOX',
        'REFRESH' => 'N',
        'DEFAULT' => 'Y'
);

//}

$arTemplateParameters['OFFERS_VIEW'] = array(
        'PARENT' => 'OFFERS_SETTINGS',
        'NAME' => GetMessage('OFFERS_VIEW'),
        'TYPE' => 'LIST',
        'VALUES' => $arOffersViewModeList,
        'MULTIPLE' => 'N',
        'DEFAULT' => 'LIST',
        'REFRESH' => 'Y'
);

if ($arCurrentValues['OFFERS_VIEW'] == 'CHOISE') {
    $arTemplateParameters['FILTER_SKU_PHOTO_FLEX'] = array(
	'PARENT' => 'OFFERS_SETTINGS',
	'NAME' => GetMessage('FILTER_SKU_PHOTO_FLEX'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'N'
    );
}

$arTemplateParameters['DETAIL_DETAIL_PICTURE_MODE'] = array(
	'PARENT' => 'SLIDER_SETTINGS',
	'NAME' => GetMessage('CP_BC_TPL_DETAIL_DETAIL_PICTURE_MODE'),
	'TYPE' => 'LIST',
	'DEFAULT' => 'IMG',
	'VALUES' => $detailPictMode
);

$arTemplateParameters['DETAIL_ADD_DETAIL_TO_SLIDER'] = array(
	'PARENT' => 'SLIDER_SETTINGS',
	'NAME' => GetMessage('CP_BC_TPL_DETAIL_ADD_DETAIL_TO_SLIDER'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'N'
);

$arTemplateParameters['DETAIL_ADD_DETAIL_TO_SLIDER_SKU'] = array(
	'PARENT' => 'SLIDER_SETTINGS',
	'NAME' => GetMessage('CP_BC_TPL_DETAIL_ADD_DETAIL_TO_SLIDER_SKU'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
	'REFRESH' => 'N'
);

$arTemplateParameters['ADDITIONAL_SKU_PIC_2_SLIDER'] = array(
    'PARENT' => 'SLIDER_SETTINGS',
    'NAME' => GetMessage('ADDITIONAL_SKU_PIC_2_SLIDER'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
);

$arTemplateParameters['FILTER_SKU_PHOTO'] = array(
        'PARENT' => 'SLIDER_SETTINGS',
        'NAME' => GetMessage('FILTER_SKU_PHOTO'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
);

$arTemplateParameters['ZOOM_ON'] = array(
	'PARENT' => 'SLIDER_SETTINGS',
	'NAME' => GetMessage('ZOOM_ON'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
);

$arTemplateParameters["SHOW_MAIN_INSTEAD_NF_SKU"] = array(
        "PARENT" => "SLIDER_SETTINGS",
        "NAME" => GetMessage('SHOW_MAIN_INSTEAD_NF_SKU'),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N",
);

	$skuPropsShowType = array(
	    "square" => GetMessage('square'),
	    "rounded" => GetMessage('rounded')
	);
	
	$arTemplateParameters["SKU_PROPS_SHOW_TYPE"] = array(
	        "PARENT" => "OFFERS_SETTINGS",
	        "NAME" => GetMessage("SKU_PROPS_SHOW_TYPE"),
		'TYPE' => 'LIST',
	        'VALUES' => $skuPropsShowType,
	        'DEFAULT' => "square",
	);

if ($arCurrentValues['SHOW_CATALOG_QUANTITY_CNT'] == 'Y') {
	$arQtyShowType = array(
	    'NUM' => GetMessage('QTY_SHOW_TYPE_NUM'),
	    'TEXT' => GetMessage('QTY_SHOW_TYPE_TEXT'),
	);
	
	$arTemplateParameters['QTY_SHOW_TYPE'] = array(
	        'PARENT' => 'VISUAL',
	        'NAME' => GetMessage('QTY_SHOW_TYPE'),
	        'TYPE' => 'LIST',
	        'VALUES' => $arQtyShowType,
	        'MULTIPLE' => 'N',
	        'DEFAULT' => 'NUM',
	        'REFRESH' => 'Y'
	);
	
	if ($arCurrentValues['QTY_SHOW_TYPE'] == 'TEXT') {
		$arTemplateParameters['QTY_MANY_GOODS_INT'] = array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('QTY_MANY_GOODS_INT'),
			'TYPE' => 'STRING',
		        'DEFAULT' => '5',
		);
		$arTemplateParameters['QTY_MANY_GOODS_TEXT'] = array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('QTY_MANY_GOODS_TEXT'),
			'TYPE' => 'STRING',
		        'DEFAULT' => GetMessage('MANY'),
		);
		
		$arTemplateParameters['QTY_LESS_GOODS_TEXT'] = array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('QTY_LESS_GOODS_TEXT'),
			'TYPE' => 'STRING',
		        'DEFAULT' => GetMessage('LESS'),
		);
	}
}	
	
$arTemplateParameters["ALSO_BUY_TITLE"] = array(
        "PARENT" => "ALSO_BUY_SETTINGS",
        "NAME" => GetMessage("ALSO_BUY_TITLE"),
	'TYPE' => 'STRING',
        'DEFAULT' => GetMessage('ALSO_BUY_TITLE_TEXT'),
);

$arShowType = array(
    "left" => GetMessage('SHOW_LEFT'),
    "bottom" => GetMessage('SHOW_BOTTOM')
);

if ($arCurrentValues['USE_SALE_BESTSELLERS'] == 'Y') {
	$arTemplateParameters["BESTSALLERS_WERE_SHOW"] = array(
	        "PARENT" => "ADDITIONAL_SETTINGS",
	        "NAME" => GetMessage("BESTSALLERS_WERE_SHOW"),
		'TYPE' => 'LIST',
	        'VALUES' => $arShowType,
	);
	
	$arTemplateParameters["BESTSALLERS_SORT"] = array(
	        "PARENT" => "ADDITIONAL_SETTINGS",
	        "NAME" => GetMessage("BESTSALLERS_SORT"),
		'TYPE' => 'STRING',
	        'DEFAULT' => 50
	);
	
	$arTemplateParameters["BESTSALLERS_TITLE"] = array(
	        "PARENT" => "ADDITIONAL_SETTINGS",
	        "NAME" => GetMessage("BESTSALLERS_TITLE"),
		'TYPE' => 'STRING',
	        'DEFAULT' => GetMessage('BESTSALLERS_TITLE_TEXT'),
	);
	
	$arTemplateParameters["BESTSALLERS_CNT"] = array(
	        "PARENT" => "ADDITIONAL_SETTINGS",
	        "NAME" => GetMessage("BESTSALLERS_CNT"),
		'TYPE' => 'STRING',
	        'DEFAULT' => 4,
	);
}	
$arTemplateParameters['BIG_DATA_TITLE'] = array(
		'PARENT' => 'BIG_DATA_SETTINGS',
		'NAME' => GetMessage('BIG_DATA_TITLE'),
		'TYPE' => 'STRING',
                'DEFAULT' => GetMessage('BIG_DATA_TITLE_TEXT'),
	);

$arTemplateParameters['BIG_DATA_CNT'] = array(
		'PARENT' => 'BIG_DATA_SETTINGS',
		"NAME" => GetMessage("BIG_DATA_CNT"),
                'TYPE' => 'STRING',
                'DEFAULT' => 4,
	);
////////////////////////////////////////////////////////////////////////////////////
$arTemplateParameters["SHOWS_BIGDATA_DETAIL"] = array(
			"PARENT" => "BIG_DATA_SETTINGS",
			"NAME" => GetMessage('CP_BC_TPL_USE_BIG_DATA_DETAIL'),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		);
$arTemplateParameters["RCM_TYPE_DETAIL"] = array(
			"PARENT" => "BIG_DATA_SETTINGS",
			"NAME" => GetMessage('CP_BC_TPL_BIG_DATA_RCM_TYPE_DETAIL'),
			"TYPE" => "LIST",
			"VALUES" => $arRCM,
			"DEFAULT" => "similar_sell",
			"HIDDEN" => (isset($arCurrentValues['SHOWS_BIGDATA_DETAIL']) && $arCurrentValues['SHOWS_BIGDATA_DETAIL'] == 'Y' ? 'N' : 'Y')
		);
$arTemplateParameters["RCM_NAME_DETAIL"] = array(
		'PARENT' => 'BIG_DATA_SETTINGS',
		'NAME' => GetMessage('BIG_DATA_TITLE_DETAIL'),
		'TYPE' => 'STRING',
        'DEFAULT' => GetMessage('BIG_DATA_TITLE_TEXT'),
		"HIDDEN" => (isset($arCurrentValues['SHOWS_BIGDATA_DETAIL']) && $arCurrentValues['SHOWS_BIGDATA_DETAIL'] == 'Y' ? 'N' : 'Y')
);
$arTemplateParameters["RCM_COUNT_DETAIL"] = array(
		'PARENT' => 'BIG_DATA_SETTINGS',
		"NAME" => GetMessage("BIG_DATA_CNT_DETAIL"),
        'TYPE' => 'TEXT',
        'DEFAULT' => 4,
		"HIDDEN" => (isset($arCurrentValues['SHOWS_BIGDATA_DETAIL']) && $arCurrentValues['SHOWS_BIGDATA_DETAIL'] == 'Y' ? 'N' : 'Y')
		);
////////////////////////////////////////////////////////////////////////////////////
$arTemplateParameters["VIEWED_PRODUCTS_SHOW"] = array(
        "PARENT" => "ADDITIONAL_SETTINGS",
        "NAME" => GetMessage("VIEWED_PRODUCTS_SHOW"),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
	'REFRESH' => 'Y'
);

if ($arCurrentValues['VIEWED_PRODUCTS_SHOW'] == 'Y') {
	$arTemplateParameters["VIEWED_PRODUCTS_WERE_SHOW"] = array(
	        "PARENT" => "ADDITIONAL_SETTINGS",
	        "NAME" => GetMessage("VIEWED_PRODUCTS_WERE_SHOW"),
		'TYPE' => 'LIST',
	        'VALUES' => $arShowType,
	);
	
	$arTemplateParameters["VIEWED_PRODUCTS_SORT"] = array(
	        "PARENT" => "ADDITIONAL_SETTINGS",
	        "NAME" => GetMessage("VIEWED_PRODUCTS_SORT"),
		'TYPE' => 'STRING',
	        'DEFAULT' => 100
	);
	
	$arTemplateParameters["VIEWED_PRODUCTS_BLOCK_TITLE"] = array(
	        "PARENT" => "ADDITIONAL_SETTINGS",
	        "NAME" => GetMessage("VIEWED_PRODUCTS_BLOCK_TITLE"),
		'TYPE' => 'STRING',
	        'DEFAULT' => GetMessage('VIEWED_PRODUCTS_BLOCK_TITLE_TEXT'),
	);
	
	$arTemplateParameters["VIEWED_PRODUCTS_CNT"] = array(
	        "PARENT" => "ADDITIONAL_SETTINGS",
	        "NAME" => GetMessage("VIEWED_PRODUCTS_CNT"),
		'TYPE' => 'STRING',
	        'DEFAULT' => 4,
	);
}
	
$arShowDescType = array(
    "none" => GetMessage("SHOW_NONE"),
    "top" => GetMessage("SHOW_TOP"),
    "bottom" => GetMessage("SHOW_BOTTOM"),
);

$arTemplateParameters["SHOW_SECTION_DESC"] = array(
        "PARENT" => "LIST_SETTINGS",
        "NAME" => GetMessage("SHOW_SECTION_DESC"),
	'TYPE' => 'LIST',
        'VALUES' => $arShowDescType,
);

$arTemplateParameters["SHOW_SECTION_SEO"] = array(
        "PARENT" => "LIST_SETTINGS",
        "NAME" => GetMessage("SHOW_SECTION_SEO"),
		'TYPE' => 'CHECKBOX',
);

/* SORT_PANEL */

$arCatalogView = array("TITLE" => GetMessage("KZNC_VIEW_TITLE"), "LIST" => GetMessage("KZNC_VIEW_LIST"), "TABLE" => GetMessage("KZNC_VIEW_TABLE"));
$arPageCount = array(8 => 8, 16 => 16, 32 => 32);

$arSort = CIBlockParameters::GetElementSortFields(
	array('SHOWS', 'SORT', 'TIMESTAMP_X', 'NAME', 'ID', 'ACTIVE_FROM', 'ACTIVE_TO'),
	array('KEY_LOWERCASE' => 'Y')
);

if ($boolCatalog)
{
    $arSort = array_merge($arSort, CCatalogIBlockParameters::GetCatalogSortFields());
}

$arSort["PROPERTY_MINIMUM_PRICE"] = GetMessage("KZNC_SORT_PRICE_NAME");

$arThemes = array(
    "default" => GetMessage("KZNC_THEME_DEFAULT"), 
    "solid" => GetMessage("KZNC_THEME_SOLID"), 
);

$arProperty = array_merge($arProperty_LNS, $arSort);

foreach ($arProperty_X as $k => $v) {
    $arProperty[str_replace("PROPERTY_", "PROPERTYSORT_", $k)] = $arProperty[$k] . " " . GetMessage("KZNC_PROPERTYSORT");
}

$arCurrentSortFields = array();
foreach ($arCurrentValues["ELEMENT_SORT_FIELD"] as $val):
	if(array_key_exists($val, $arProperty))
		$arCurrentSortFields[$val] = $arProperty[$val];
endforeach;

$arTemplateParameters["THEME"] = array(
    "PARENT" => "SORT_PANEL_SETTINGS",
    "NAME" => GetMessage("KZNC_THEME_NAME"),
    "TYPE" => "LIST",
    "VALUES" => $arThemes,
    "MULTIPLE" => "N",
    "DEFAULT" => "default",
);

$arTemplateParameters["ELEMENT_SORT_FIELD"] = array(
    "PARENT" => "SORT_PANEL_SETTINGS",
    "NAME" => GetMessage("IBLOCK_ELEMENT_SORT_FIELD"),
    "TYPE" => "LIST",
    "VALUES" => $arProperty,
    "MULTIPLE" => "Y",
    "DEFAULT" => "sort",
    "REFRESH" => "Y",
    "SIZE" => 10,
);

$arTemplateParameters["CATALOG_DEFAULT_SORT"] = array(
    "PARENT" => "SORT_PANEL_SETTINGS",
    "NAME" => GetMessage("KZNC_CATALOG_DEFAULT_SORT"),
    "TYPE" => "LIST",
    "DEFAULT" => "sort",
    "VALUES" => $arCurrentSortFields,
);

$arTemplateParameters["CATALOG_DEFAULT_SORT_ORDER"] = array(
    "PARENT" => "SORT_PANEL_SETTINGS",
    "NAME" => GetMessage("KZNC_CATALOG_DEFAULT_SORT_ORDER"),
    "TYPE" => "LIST",
    "DEFAULT" => "desc",
    "VALUES" => array("asc" => GetMessage("KZNC_CATALOG_DEFAULT_SORT_ORDER_ASC"), "desc" => GetMessage("KZNC_CATALOG_DEFAULT_SORT_ORDER_DESC")),
);

$arTemplateParameters["PAGE_ELEMENT_COUNT_SHOW"] = array(
    "PARENT" => "SORT_PANEL_SETTINGS",
    "NAME" => GetMessage("KZNC_PAGE_ELEMENT_COUNT_SHOW"),
    "TYPE" => "CHECKBOX",
    "DEFAULT" => "Y",
    "REFRESH" => "Y",
);

$arTemplateParameters["PAGE_ELEMENT_COUNT"] = array(
    "PARENT" => "SORT_PANEL_SETTINGS",
    "NAME" => GetMessage("IBLOCK_PAGE_ELEMENT_COUNT"),
    "TYPE" => "STRING",
    "DEFAULT" => "16",
);

if($arCurrentValues["PAGE_ELEMENT_COUNT_SHOW"]=="Y") {
	
    $arTemplateParameters["PAGE_ELEMENT_COUNT_LIST"] = array(
        "PARENT" => "SORT_PANEL_SETTINGS",
        "NAME" => GetMessage("KZNC_PAGE_ELEMENT_COUNT_LIST"),
        "TYPE" => "LIST",
        "MULTIPLE" => "Y",
        "ADDITIONAL_VALUES" => "Y",
        "VALUES" => $arPageCount,
    );
}

$arTemplateParameters["CATALOG_VIEW_SHOW"] = array(
    "PARENT" => "SORT_PANEL_SETTINGS",
    "NAME" => GetMessage("KZNC_CATALOG_VIEW_SHOW"),
    "TYPE" => "CHECKBOX",
    "DEFAULT" => "Y",
    "REFRESH" => "Y",
);

if($arCurrentValues["CATALOG_VIEW_SHOW"]=="Y") {
    $arTemplateParameters["DEFAULT_CATALOG_VIEW"] = array(
        "PARENT" => "SORT_PANEL_SETTINGS",
        "NAME" => GetMessage("KZNC_DEFAULT_CATALOG_VIEW"),
        "TYPE" => "LIST",
        "VALUES" => $arCatalogView,
        "DEFAULT" => "TITLE",
    );
}

$arTemplateParameters['ARTICLE_POSITION'] = array(
	'PARENT' => 'DETAIL_SETTINGS',
	'NAME' => GetMessage('CP_BCE_TPL_ARTICLE_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => array(
                'after_name' => GetMessage('CP_BCE_TPL_ARTICLE_POSITION_AFTER_NAME'),
                'right' => GetMessage('CP_BCE_TPL_ARTICLE_POSITION_RIGHT'),
                'none' => GetMessage('CP_BCE_TPL_NO_ARTICLE'),
        ),
        'DEFAULT' => 'none',
	'REFRESH' => 'Y'
);
if (isset($arCurrentValues['ARTICLE_POSITION']) && ('after_name' == $arCurrentValues['ARTICLE_POSITION'] || 'right' == $arCurrentValues['ARTICLE_POSITION']))
{
	$arTemplateParameters['ARTICLE_PROP_CODE'] = array(
                'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => GetMessage('CP_BCE_TPL_ARTICLE_PROP_CODE'),
		'TYPE' => 'STRING',
		'DEFAULT' => 'CML2_ARTICLE'
	);
}
?>