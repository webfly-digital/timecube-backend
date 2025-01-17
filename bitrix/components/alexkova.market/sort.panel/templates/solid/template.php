<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$this->createFrame()->begin('sortpanel');

function number_key($array, $desired_key)
{
	if (!isset($array[$desired_key])) {
		return false;
	}
	$i = 1;
	foreach ($array as $key => $value) {
		if ($key == $desired_key) {
			return $i;
		}else{
			$i++;
		}
	}
}

global $arSortGlobal;

$arAvailableSort = array(
	"name" => Array("name", "asc", GetMessage("SORT_BY_NAME"), 'hidden-xs'),
	"price" => Array('PROPERTY_MINIMUM_PRICE', "asc",GetMessage("SORT_BY_PRICE"), ),
	"rating" => Array('PROPERTY_RATING', 'desc',GetMessage("SORT_BY_RATING"), 'hidden-sm hidden-xs'),
);

$sort = array_key_exists("sort", $_REQUEST) && array_key_exists(ToLower($_REQUEST["sort"]), $arAvailableSort) ? $arAvailableSort[ToLower($_REQUEST["sort"])][0] : "name";
$sort_order = array_key_exists("order", $_REQUEST) && in_array(ToLower($_REQUEST["order"]), Array("asc", "desc")) ? ToLower($_REQUEST["order"]) : $arAvailableSort[$sort][1];

$arSortGlobal = array(
	"sort" => $sort,
	"sort_order" => $sort_order,
);
?>

	<div class="col-xs-12 bxr-color bxr-sort-panel">
		<div class="col-xs-5 col-sm-7">
			<?foreach ($arAvailableSort as $key => $val):
				$className = ($sort == $val[0]) ? ' active' : '';
				$icon = "";
				if ($className){
					$className .= ($sort_order == 'asc') ? ' asc' : ' desc';
					$icon = ($sort_order == 'asc') ? '<i class="fa fa-arrow-up"></i>' : ' <i class="fa fa-arrow-down"></i>';
				}

				if (strlen($val[3])>0){
					$className .= " ".$val[3];
				}

				$newSort = ($sort == $val[0]) ? ($sort_order == 'desc' ? 'asc' : 'desc') : $arAvailableSort[$key][1];
				?>
				<a href="<?=$APPLICATION->GetCurPageParam('sort='.$key.'&order='.$newSort, 	array('sort', 'order'))?>"
				   class="bxr-sortbutton<?=$className?> <?if(number_key($arAvailableSort,$key)==count($arAvailableSort)) echo "last";?>" rel="nofollow">
					<?=$val[2]?><?if ($sort == $val[0]):?><?=$icon?><?endif?>
				</a>
			<?endforeach;?>
		</div>
		<div class="col-xs-7 col-sm-5 text-right">
			<a href="<?=$APPLICATION->GetCurPageParam('view=grid',array('view'));?>" title="<?=GetMessage('VIEW_PLITKA')?>" class="bxr-view-mode <?=($_REQUEST['view'] == 'grid' || !$_REQUEST['view']) ? 'active' : '';?>">
				<i class="fa fa-th"></i>
			</a>
			<a href="<?=$APPLICATION->GetCurPageParam('view=list',array('view'));?>" title="<?=GetMessage('VIEW_LIST')?>" class="bxr-view-mode <?=($_REQUEST['view'] == 'list') ? 'active' : '';?>">
				<i class="fa fa-th-list"></i>
			</a>
			<a href="<?=$APPLICATION->GetCurPageParam('view=table',array('view'));?>" title="<?=GetMessage('VIEW_TABLE')?>" class="bxr-view-mode <?=($_REQUEST['view'] == 'table') ? 'active' : '';?>">
				<i class="fa fa-align-justify"></i>
			</a>
		</div>
	</div>
