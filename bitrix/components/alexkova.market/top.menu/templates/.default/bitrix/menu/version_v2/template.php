<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>

<?if (!empty($arResult)):?>
<?$compound = $arParams["COMPOUND"] == "Y"? " compound pull-right":""?>
    <?$full_width_menu = "";?>
    <?if($arParams["FULL_WIDTH"] == "Y"):?>
        <div class="v-line_menu <?=$arParams["STYLE_MENU"];?>"><div class="container">
            <?$full_width_menu = "full-width-menu";?>
    <?else:?>
        <div class="container v-line_menu <?=$arParams["STYLE_MENU"];?>">
    <?endif;?>
<div class="row" style="margin-right: -30px;"><div class="col-sm-12" >
<ul class="flex-menu top-menu-v2 menu-level2 <?=$full_width_menu;?>  hidden-sm hidden-xs <?=$compound?>">
<?
$previousLevel = 0;
$flagFirst = true;
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
	if (count($addClass)>0   && $arItem["IS_PARENT"]){
		$addFirst = ' class="'.implode(" ",$addClass).'"';
	}
	?>
    
        <?  
            $glyphicon = "";        
            if($arItem["DEPTH_LEVEL"]==1 || $arItem["IS_PARENT"])
                 $glyphicon = '<span class="glyphicon glyphicon-chevron-down"></span>';   
        ?>

	<li<?=$addFirst?>><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?><?=$glyphicon?></a>

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

