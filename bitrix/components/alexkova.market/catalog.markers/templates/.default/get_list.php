<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

define("BX_AJAX_PARAM_ID", "ID, IBLOCK_ID");

if (is_array($_SESSION["BXR_MARKERS_SETTINGS"])
	&& !empty($_SESSION["BXR_MARKERS_SETTINGS"])):

	if (isset($_REQUEST["ID"])) {

		$arFilter = array();

		switch (strval($_REQUEST["ID"])) {

			case 'RECOMMENDED':
				$arFilter = array("!PROPERTY_RECOMMENDED"=>false);
				break;

			case 'NEWPRODUCT':
				$arFilter = array("!PROPERTY_NEWPRODUCT"=>false);
				break;

			case 'SPECIALOFFER':
				$arFilter = array("!PROPERTY_SPECIALOFFER"=>false);
				break;

			case 'SALELEADER':
				$arFilter = array("!PROPERTY_SALELEADER"=>false);
				break;

			default: ;
		}
		if (is_array($arFilter) && !empty($arFilter)) {
			global $arrFilter;
			$arrFilter = $arFilter;
                        $arParams['PRODUCT_DISPLAY_MODE'] = 'Y';
                        
			$APPLICATION->IncludeComponent(
				"bitrix:catalog.section",
				"",
				$arParams,
				$component,
				array('HIDE_ICONS' => 'Y')
			);
		}
	}

endif;