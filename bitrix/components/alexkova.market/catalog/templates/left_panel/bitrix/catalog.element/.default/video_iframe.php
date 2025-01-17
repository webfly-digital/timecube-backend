<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if (strlen($arResult["ELEMENT"]["VIDEO_IMG"])<=0){
	$arResult["ELEMENT"]["VIDEO_IMG"] = SITE_TEMPLATE_PATH."/images/video.png";
}
?>
<?if(!isset($arParams["VIDEO_TYPE"]) || $arParams["VIDEO_TYPE"]=="GRID"):?><div class="row"><?endif;?>
   <?foreach ($arResult["PROPERTIES"]["VIDEO"]["VALUE"] as $val):?>
       <?if(isset($arParams["VIDEO_TYPE"]) && $arParams["VIDEO_TYPE"]=="LIST"):?>
           <div class="row">
               <div class="element-video-card-mej element-video-card-iframe col-lg-3 col-md-4 col-sm-4 col-xs-5 "> 
       <?else:?>
               <div class="element-video-card-mej element-video-card-iframe col-lg-4 col-md-6 col-sm-6 col-xs-12 ">    
       <?endif;?>
           <div class="video-card-link" >
               <img class='video-img' data-url="<?=$val["path"];?>" src="<?=$templateFolder;?>/css/play.jpg">
           </div>
           <?if(!isset($arParams["VIDEO_TYPE"]) || $arParams["VIDEO_TYPE"]=="GRID"):?>
               <div class="element-video-card-grid">
                   <p class="bxr-font-color element-video-card-title" itemprop="name"><?=$val["title"];?></p>
                   <span class="element-video-card-desc" itemprop="name"><?=$val["desc"];?></span>
               </div>
           <?endif;?>
       </div>
       <?if(isset($arParams["VIDEO_TYPE"]) && $arParams["VIDEO_TYPE"]=="LIST"):?>
           <div class="col-lg-9 col-md-8 col-sm-6 col-xs-7 ">
               <p class="bxr-font-color element-video-card-title element-video-card-title-row" itemprop="name"><?=$val["title"];?></p>
               <span class="element-video-card-desc" itemprop="name"><?=$val["desc"];?></span>
           </div>
           </div>
         <?endif;?>
   <?endforeach;?>
<?if(!isset($arParams["VIDEO_TYPE"]) || $arParams["VIDEO_TYPE"]=="GRID"):?></div><?endif;?>