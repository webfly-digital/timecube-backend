<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>

<?if (!empty($arResult)):?>
<?
    $menu_arrow_top = "";
    
    if($arParams['VARIANT_MENU'] == "version_v1" && $arParams['STYLE_MENU'] == "colored_light" )
        $menu_arrow_top = "menu_arrow_top";

?>
<?$compound = $arParams["COMPOUND"] == "Y"? " compound pull-right":""?>
    <?if($arParams["FULL_WIDTH"] == "Y"):?>
        <div class="v-line_menu <?=$arParams["STYLE_MENU"];?>"><div class="container">
    <?else:?>
        <div class="container v-line_menu <?=$arParams["STYLE_MENU"];?>">
    <?endif;?>
<div class="row"><div class="col-sm-12">
<ul class="flex-menu top-menu-v2 <?=$menu_arrow_top?>  hidden-sm hidden-xs <?=$compound?>">
<?
    $previousLevel = 0;
    $flagFirst = true;
    $i = 0;
 
global $USER;
if($USER->GetID() == "4") {
   /* echo "<pre>";
print_r($arResult);
echo "</pre>";*/
} 
    
foreach($arResult as $arItem):?>

	<?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
		<?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
	<?endif?>

	<?$addFirst = "";
		$addClass=array();
	if ($flagFirst){
		$addClass[] = 'first';
		$flagFirst = false;
	}
	if ($arItem["SELECTED"]){
		$addClass[] = 'selected';
	}
	if (count($addClass)>0){
		$addFirst = ' class="'.implode(" ",$addClass).'"';
	}
	?>
    
        <?  
            $glyphicon = "";        
            if($arItem["DEPTH_LEVEL"]==1 && $arItem["IS_PARENT"])
                 $glyphicon = '<span class="glyphicon glyphicon-chevron-down"></span>';   
        ?>
    
        <?if($i == 1):?>
        <li><a href="<?=$arItem["LINK"]?>">
                Каталог<?=$glyphicon?>
                <?$APPLICATION->IncludeComponent(
                    "alexkova.market:top.menu.hover2", 
                    ".default", 
                    array(
                            "COMPONENT_TEMPLATE" => "tree",
                            "FIXED_MENU" => "Y",
                            "COMPACT_MODE_MENU" => "N",
                            "STYLE_MENU_CATALOG" => "test",
                            "SEARCH_FORM" => "Y",
                            "CACHE_TYPE" => "A",
                            "CACHE_TIME" => "3600",
                            "ROOT_MENU_TYPE" => "catalog",
                            "MENU_CACHE_TYPE" => "N",
                            "MENU_CACHE_TIME" => "3600",
                            "MENU_CACHE_USE_GROUPS" => "Y",
                            "MENU_CACHE_GET_VARS" => "",
                            "MAX_LEVEL" => "2",
                            "CHILD_MENU_TYPE" => "",
                            "USE_EXT" => "Y",
                            "DELAY" => "N",
                            "ALLOW_MULTI_SELECT" => "N",
                            "IBLOCK_TYPE" => "catalog",
                            "IBLOCK_ID" => "1",
                            "SECTION_ID" => $_REQUEST["SECTION_ID"],
                            "SECTION_CODE" => "",
                            "COUNT_ELEMENTS" => "Y",
                            "TOP_DEPTH" => "3",
                            "SECTION_FIELDS" => array(
                                    0 => "",
                                    1 => "",
                            ),
                            "SECTION_USER_FIELDS" => array(
                                    0 => "",
                                    1 => "",
                            ),
                            "SECTION_URL" => "",
                            "CACHE_GROUPS" => "Y",
                            "ADD_SECTIONS_CHAIN" => "Y"
                    ),
                    false
                );?>    
            </a>
        <?endif;?>

	<li<?=$addFirst?>><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?><?=$glyphicon?></a>
            
        <? ++$i?> 

	<?if (!$arItem["IS_PARENT"]):?>
		</li>
	<?else:?>
		<ul>
	<?endif;?>

	<?$previousLevel = $arItem["DEPTH_LEVEL"];?>

<?endforeach?>

<?if ($previousLevel > 1)://close last item tags?>
	<?=str_repeat("</ul></li>", ($previousLevel-1) );?>
<?endif?>
    <?if($arParams['SEARCH_FORM'] == "Y"):?>
        <li class="other" id="flex-menu-li">&nbsp;</li>
        <li class="last li-visible" ><a href="#"><span class="glyphicon glyphicon-search"></span></a></li>
    <?else:?>
        <li class="other pull-right" id="flex-menu-li">&nbsp;</li>
    <?endif;?>
    <div class="clearfix"></div>
</ul>
</div></div>
    <?if($arParams["FULL_WIDTH"] == "Y"):?>
        </div></div>
    <?else:?>
        </div>
    <?endif;?>
<?endif?>