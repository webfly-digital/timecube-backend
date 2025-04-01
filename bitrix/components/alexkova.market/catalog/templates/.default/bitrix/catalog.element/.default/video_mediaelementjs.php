<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(!isset($arParams["VIDEO_TYPE"]) || $arParams["VIDEO_TYPE"]=="GRID"):?><div class="row"><?endif;?>
   <?foreach ($arResult["PROPERTIES"]["VIDEO"]["VALUE"] as $val):?>
       <?if(isset($arParams["VIDEO_TYPE"]) && $arParams["VIDEO_TYPE"]=="LIST"):?>
           <div class="row">
               <div class="element-video-card-mej col-lg-3 col-md-4 col-sm-5 col-xs-6 "> 
       <?else:?>
               <div class="element-video-card-mej col-lg-4 col-md-6 col-sm-6 col-xs-12 ">    
       <?endif;?>
           <div class="mej <?if(isset($arParams["VIDEO_PLAYER_FULLSCREEN"]) && $arParams["VIDEO_PLAYER_FULLSCREEN"]=="Y") echo "video-fullscreen" ?>" data-MaxW="640" data-MinW="256"  >
               <?
                   $video_type = "video/mp4";

                   if(strripos($val["path"], "youtu"))
                       $video_type = "video/youtube";

                   if(strripos($val["path"], ".webm"))
                       $video_type = "video/webm";  

                   if(strripos($val["path"], ".ogv"))
                       $video_type = "video/ogg";  
               ?>
               <video style="width:100%;height:100%;" width="100%" height="100%" id="player1" preload="none">
                   <source type="<?=$video_type?>" src="<?=$val["path"];?>" />
               </video>
           </div>
           <?if(!isset($arParams["VIDEO_TYPE"]) || $arParams["VIDEO_TYPE"]=="GRID"):?>
               <div class="element-video-card-mej-content">
                   <p class="bxr-font-color element-video-card-title" itemprop="name"><?=$val["title"];?></p>
                   <span class="element-video-card-desc" itemprop="name"><?=$val["desc"];?></span>
               </div>
           <?endif;?>
       </div>
       <?if(isset($arParams["VIDEO_TYPE"]) && $arParams["VIDEO_TYPE"]=="LIST"):?>
           <div class="element-video-card-mej-content col-lg-9 col-md-8 col-sm-7 col-xs-6 ">
               <p class="bxr-font-color element-video-card-title element-video-card-title-row" itemprop="name"><?=$val["title"];?></p>
               <span class="element-video-card-desc" itemprop="name"><?=$val["desc"];?></span>
           </div>
           </div>
         <?endif;?>
   <?endforeach;?>
<?if(!isset($arParams["VIDEO_TYPE"]) || $arParams["VIDEO_TYPE"]=="GRID"):?></div><?endif;?>