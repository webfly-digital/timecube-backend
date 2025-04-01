<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule("iblock"))
	return;

if($arCurrentValues["IBLOCK_ID"] > 0)
{
	$arIBlock = CIBlock::GetArrayByID($arCurrentValues["IBLOCK_ID"]);

	$bWorkflowIncluded = ($arIBlock["WORKFLOW"] == "Y") && CModule::IncludeModule("workflow");
	$bBizproc = ($arIBlock["BIZPROC"] == "Y") && CModule::IncludeModule("bizproc");
}
else
{
	$bWorkflowIncluded = CModule::IncludeModule("workflow");
	$bBizproc = false;
}

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock=array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
{
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}

$arProperty_LNSF = array();
$arVirtualProperties = $arProperty_LNSF;

$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"]));
while ($arr=$rsProp->Fetch())
{
	$arProperty[$arr["ID"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	if (in_array($arr["PROPERTY_TYPE"], array("L", "N", "S", "F")))
	{
		$arProperty_LNSF[$arr["ID"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}
}

$arGroups = array();
$rsGroups = CGroup::GetList($by="c_sort", $order="asc", Array("ACTIVE" => "Y"));
while ($arGroup = $rsGroups->Fetch())
{
	$arGroups[$arGroup["ID"]] = $arGroup["NAME"];
}

if ($bWorkflowIncluded)
{
	$rsWFStatus = CWorkflowStatus::GetList($by="c_sort", $order="asc", Array("ACTIVE" => "Y"), $is_filtered);
	$arWFStatus = array();
	while ($arWFS = $rsWFStatus->Fetch())
	{
		$arWFStatus[$arWFS["ID"]] = $arWFS["TITLE"];
	}
}
else
{
	$arActive = array("ANY" => GetMessage("KZNC_IBLOCK_STATUS_ANY"), "INACTIVE" => GetMessage("KZNC_IBLOCK_STATUS_INCATIVE"));
	$arActiveNew = array("N" => GetMessage("KZNC_IBLOCK_ALLOW_N"), "NEW" => GetMessage("KZNC_IBLOCK_ACTIVE_NEW_NEW"));
}

$arAllowEdit = array("CREATED_BY" => GetMessage("KZNC_IBLOCK_CREATED_BY"), "PROPERTY_ID" => GetMessage("KZNC_IBLOCK_PROPERTY_ID"));
$arComponentParameters = array(
	"GROUPS" => array(
		"PARAMS" => array(
			"NAME" => GetMessage("KZNC_IBLOCK_PARAMS"),
			"SORT" => "200"
		),
		"ACCESS" => array(
			"NAME" => GetMessage("KZNC_IBLOCK_ACCESS"),
			"SORT" => "400",
		),
		"FIELDS" => array(
			"NAME" => GetMessage("KZNC_IBLOCK_FIELDS"),
			"SORT" => "300",
		),
		"PERSONAL_DATA" => array(
			"NAME" => GetMessage("KZNC_IBLOCK_PERSONAL_DATA"),
			"SORT" => "400",
                ),
	),

	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("KZNC_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("KZNC_IBLOCK_IBLOCK"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"PROPERTY_CODES" => array(
			"PARENT" => "FIELDS",
			"NAME" => GetMessage("KZNC_IBLOCK_PROPERTY"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty_LNSF,
			"SIZE" => 8
		),
		"NAME_FROM_PROPERTY" => array(
			"PARENT" => "FIELDS",
			"NAME" => GetMessage("KZNC_IBLOCK_NAME_FROM_PROPERTY"),
			"TYPE" => "LIST",
			"VALUES" => $arProperty_LNSF,
		),
		"GROUPS" => array(
			"PARENT" => "ACCESS",
			"NAME" => GetMessage("KZNC_IBLOCK_GROUPS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => $arGroups,
		),
		"STATUS_NEW" => array(
			"PARENT" => "PARAMS",
			"NAME" => $bWorkflowIncluded? GetMessage("KZNC_IBLOCK_STATUS_NEW"): ($bBizproc? GetMessage("KZNC_IBLOCK_BP_NEW"): GetMessage("KZNC_IBLOCK_ACTIVE_NEW")),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => $bWorkflowIncluded ? $arWFStatus : $arActiveNew,
		),

	),
);

$arComponentParameters["PARAMETERS"]["USE_CAPTCHA"] = array(
	"PARENT" => "PARAMS",
	"NAME" => GetMessage("KZNC_IBLOCK_USE_CAPTCHA"),
	"TYPE" => "CHECKBOX",
);

$arComponentParameters["PARAMETERS"]["USER_MESSAGE_ADD"] = array(
	"PARENT" => "PARAMS",
	"NAME" => GetMessage("KZNC_IBLOCK_USER_MESSAGE_ADD"),
	"TYPE" => "TEXT",
);

$arComponentParameters["PARAMETERS"]["RESIZE_IMAGES"] = array(
	"PARENT" => "PARAMS",
	"NAME" => GetMessage("CP_BIEAF_RESIZE_IMAGES"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N",
);

$arComponentParameters["PARAMETERS"]["MAX_FILE_SIZE"] = array(
	"PARENT" => "ACCESS",
	"NAME" => GetMessage("KZNC_IBLOCK_MAX_FILE_SIZE"),
	"TYPE" => "TEXT",
	"DEFAULT" => "0",
);


$arComponentParameters["PARAMETERS"]["MODE"] = array(
	"PARENT" => "PARAMS",
	"NAME" => GetMessage("KZNC_IBLOCK_POPUP_MODE"),
	"TYPE" => "LIST",
	"MULTIPLE" => "N",
	"VALUES" => array(
		'link' => GetMessage("KZNC_IBLOCK_POPUP_MODE_LINK"),
		'static' => GetMessage("KZNC_IBLOCK_POPUP_MODE_STATIC"),
	),
	"DEFAULT" => "static",
	"REFRESH" => "Y",
);
if($arCurrentValues["MODE"] == 'link')
{
	$arComponentParameters["PARAMETERS"]["EVENT_CLASS"] = array(
		"PARENT" => "PARAMS",
		"NAME" => GetMessage("KZNC_IBLOCK_EVENT_CLASS"),
		"TYPE" => "TEXT",
		"DEFAULT" => "open-form",
	);

	$arComponentParameters["PARAMETERS"]["BUTTON_TEXT"] = array(
		"PARENT" => "PARAMS",
		"NAME" => GetMessage("KZNC_IBLOCK_BUTTON_TEXT"),
		"TYPE" => "TEXT",
		"DEFAULT" => GetMessage("KZNC_IBLOCK_POPUP_BUTTON_OPEN"),
	);

	$arComponentParameters["PARAMETERS"]["POPUP_TITLE"] = array(
		"PARENT" => "PARAMS",
		"NAME" => GetMessage("KZNC_IBLOCK_POPUP_TITLE"),
		"TYPE" => "TEXT",
		"DEFAULT" => GetMessage("KZNC_IBLOCK_POPUP_DEFAULT_TITLE"),
	);
	$arComponentParameters["PARAMETERS"]["SEND_EVENT"] = array(
		"NAME" => GetMessage("KZNC_IBLOCK_POPUP_SEND_EVENT"),
		"TYPE" => "TEXT",
		"DEFAULT" => 'KZNC_NEW_FORM_RESULT',
	);
}

$arComponentParameters["PARAMETERS"]["PERSONAL_DATA"] = array(
	"PARENT" => "PERSONAL_DATA",
	"NAME" => GetMessage("KZNC_PERSONAL_DATA"),
	"TYPE" => "CHECKBOX",
);

$arComponentParameters["PARAMETERS"]["PERSONAL_DATA_TEXT"] = array(
	"PARENT" => "PERSONAL_DATA",
	"NAME" => GetMessage("KZNC_PERSONAL_DATA_TEXT"),
	"TYPE" => "STRING",
	"DEFAULT" => GetMessage("KZNC_PERSONAL_DATA_TEXT_DEFAULT")
);

$arComponentParameters["PARAMETERS"]["PERSONAL_DATA_CAPTION"] = array(
	"PARENT" => "PERSONAL_DATA",
	"NAME" => GetMessage("KZNC_PERSONAL_DATA_CAPTION"),
	"TYPE" => "STRING",
	"DEFAULT" => GetMessage("KZNC_PERSONAL_DATA_CAPTION_DEFAULT")
);

$arComponentParameters["PARAMETERS"]["PERSONAL_DATA_URL"] = array(
	"PARENT" => "PERSONAL_DATA",
	"NAME" => GetMessage("KZNC_PERSONAL_DATA_URL"),
	"TYPE" => "STRING",
	"DEFAULT" => ""
);

$arComponentParameters["PARAMETERS"]["PERSONAL_DATA_ERROR"] = array(
	"PARENT" => "PERSONAL_DATA",
	"NAME" => GetMessage("KZNC_PERSONAL_DATA_ERROR"),
	"TYPE" => "STRING",
	"DEFAULT" => GetMessage("KZNC_PERSONAL_DATA_ERROR_DEFAULT")
);