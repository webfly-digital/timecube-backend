<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$itemCount = count($arResult);
$needReload = (isset($_REQUEST["compare_list_reload"]) && $_REQUEST["compare_list_reload"] == "Y");
$idCompareCount = 'compareList'.$this->randString();
$obCompare = 'ob'.$idCompareCount;
$mainClass = 'catalog-compare-list';

?><div id="<?=$idCompareCount; ?>" class="<?=$mainClass; ?> "><?
unset($mainClass);

if ($needReload)
{
	$APPLICATION->RestartBuffer();
}

$frame = $this->createFrame($idCompareCount)->begin('');

	?>
    <a class="icon-button" href="<?=$arParams["COMPARE_URL"]; ?>" title="<?=GetMessage('CP_BCCL_TPL_MESS_COMPARE_PAGE'); ?>">
        <span class="icon-button__content">
            <span class="icon-button__icon">
                <span class="svg-icon icon-compare"></span>
            </span>
            <span class="icon-button__caption">Сравнить</span>
            <span <?if ($itemCount == 0 ) echo 'style="display:none"'?> class="icon-button__counter catalog-compare-count" data-block="count"><?=$itemCount; ?></span>
        </span>
    </a>
	<div class="catalog-compare-form" style="display: none">
		<table class="table table-sm table-striped table-borderless mb-0" data-block="item-list">
			<thead>
				<tr>
					<th  scope="col" class="text-center" colspan="2"><strong><?=GetMessage("CATALOG_COMPARE_ELEMENTS")?></strong></th>
				</tr>
			</thead>
			<tbody><?
				foreach($arResult as $arElement)
				{
					?><tr data-block="item-row" data-row-id="row<?=$arElement['PARENT_ID']; ?>">
						<td class="text-left align-middle">
							<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><?=$arElement["NAME"]?></a>
						</td>
						<td class="text-right align-middle">
							<a class="text-muted" href="javascript:void(0);" data-id="<?=$arElement['PARENT_ID']; ?>" rel="nofollow"><?=GetMessage("CATALOG_DELETE")?></a>
						</td>
					</tr><?
				}
				?>
			</tbody>
		</table>
	</div><?

$frame->end();
if ($needReload)
{
	die();
}
$currentPath = CHTTP::urlDeleteParams(
	$APPLICATION->GetCurPageParam(),
	[
		$arParams['PRODUCT_ID_VARIABLE'],
		$arParams['ACTION_VARIABLE'],
		'ajax_action'
    ],
	["delete_system_params" => true]
);

$jsParams = [
	'VISUAL' => [
		'ID' => $idCompareCount,
    ],
	'AJAX' => [
		'url' => $currentPath,
		'params' => [
			'ajax_action' => 'Y'
        ],
		'reload' => [
			'compare_list_reload' => 'Y'
        ],
		'templates' => [
			'delete' => (strpos($currentPath, '?') === false ? '?' : '&').$arParams['ACTION_VARIABLE'].'=DELETE_FROM_COMPARE_LIST&'.$arParams['PRODUCT_ID_VARIABLE'].'='
        ]
    ],
	'POSITION' => [
		'fixed' => false,
		'align' => [
			'vertical' => '',
			'horizontal' => ''
        ]
    ]
];
?>
	<script type="text/javascript">
		var <?=$obCompare; ?> = new JCCatalogCompareList(<? echo CUtil::PhpToJSObject($jsParams, false, true); ?>)
	</script>
</div>