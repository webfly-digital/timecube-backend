<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/** @var array $arCurrentValues */
/** @global CUserTypeManager $USER_FIELD_MANAGER */

use Bitrix\Main\Loader,
	Bitrix\Main\ModuleManager,
	Bitrix\Iblock,
	Bitrix\Catalog,
	Bitrix\Currency;

global $USER_FIELD_MANAGER;

if (!Loader::includeModule('iblock')) return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = [];
$iblockFilter = (
!empty($arCurrentValues['IBLOCK_TYPE'])
    ? ['TYPE' => $arCurrentValues['IBLOCK_TYPE'], 'ACTIVE' => 'Y']
    : ['ACTIVE' => 'Y']
);
$rsIBlock = CIBlock::GetList(['SORT' => 'ASC'], $iblockFilter);
while ($arr = $rsIBlock->Fetch())
{
    $id = (int)$arr['ID'];
    $arIBlock[$id] = '['.$id.'] '.$arr['NAME'];
}
unset($id, $arr, $rsIBlock, $iblockFilter);

$arComponentParameters = [
	"GROUPS" => [
		"BASE" => [
			"NAME" => 'BASE',
        ],
    ],
	"PARAMETERS" => [
        'AJAX_MODE' => [],
		"IBLOCK_TYPE" => [
			"PARENT" => "BASE",
			"NAME" => "IBLOCK_TYPE",
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
        ],
		"IBLOCK_ID" => [
			"PARENT" => "BASE",
			"NAME" => "IBLOCK_ID",
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
        ],
        "DEPTH_LEVEL" => [
            "PARENT" => "BASE",
            "NAME" => "DEPTH_LEVEL",
            "TYPE" => "STRING"
        ]
    ],
];
