<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if(!isset($arParams["VIDEO_TYPE"]) || $arParams["VIDEO_TYPE"]=="GRID"):?><div class="row"><?endif;?>
<?   
    $user_type_settings_ib = $arResult["PROPERTIES"]["VIDEO"]["USER_TYPE_SETTINGS"];

    $user_type_settings = array(
        "BUFFER_LENGTH" => !empty($user_type_settings_ib["BUFFER_LENGTH"]) ? $user_type_settings_ib["BUFFER_LENGTH"] : 10,
        "AUTOSTART" => !empty($user_type_settings_ib["AUTOSTART"]) ? $user_type_settings_ib["AUTOSTART"] : "N",
        "VOLUME" => !empty($user_type_settings_ib["VOLUME"]) ? $user_type_settings_ib["VOLUME"] : 90,
        "SKIN" => !empty($user_type_settings_ib["SKIN"]) ? $user_type_settings_ib["SKIN"] : "bekle.zip",
        "FLASHVARS" => !empty($user_type_settings_ib["FLASHVARS"]) ? $user_type_settings_ib["FLASHVARS"] : "",
        "WMODE_FLV" => !empty($user_type_settings_ib["WMODE_FLV"]) ? $user_type_settings_ib["WMODE_FLV"] : "window",
        "BGCOLOR" => !empty($user_type_settings_ib["BGCOLOR"]) ? $user_type_settings_ib["BGCOLOR"] : "FFFFFF",
        "COLOR" => !empty($user_type_settings_ib["COLOR"]) ? $user_type_settings_ib["COLOR"] : "000000",
        "OVER_COLOR" => !empty($user_type_settings_ib["OVER_COLOR"]) ? $user_type_settings_ib["OVER_COLOR"] : "000000",
        "SCREEN_COLOR" => !empty($user_type_settings_ib["SCREEN_COLOR"]) ? $user_type_settings_ib["SCREEN_COLOR"] : "000000",
        "WMODE_WMV" => !empty($user_type_settings_ib["WMODE_WMV"]) ? $user_type_settings_ib["WMODE_WMV"] : "window",
    );

?>    
<?     
    foreach ($arResult["PROPERTIES"]["VIDEO"]["VALUE"] as $val):?>
        <?if(isset($arParams["VIDEO_TYPE"]) && $arParams["VIDEO_TYPE"]=="LIST"):?>
            <div class="row">
                <div class="element-video-card col-lg-4 col-md-5 col-sm-6 col-xs-7 ">
        <?else:?>
            <div class="element-video-card col-lg-4 col-md-5 col-sm-6 col-xs-12">   
        <?endif;?>
        <?$APPLICATION->IncludeComponent(
            "bitrix:player", 
            ".default", 
            array(
                    "PLAYER_TYPE" => "auto",
                    "USE_PLAYLIST" => "N",
                    "PATH" => $val["path"],
                    "PLAYLIST_DIALOG" => "",
                    "PROVIDER" => "",
                    "STREAMER" => "",
                    "WIDTH" => "",
                    "HEIGHT" => "",
                    "PREVIEW" => "",
                    "FILE_TITLE" => $val["title"],
                    "FILE_DURATION" => $val["duration"],
                    "FILE_AUTHOR" => $val["author"],
                    "FILE_DATE" => $val["date"],
                    "FILE_DESCRIPTION" => $val["desc"],
                    "SKIN_PATH" => "/bitrix/components/bitrix/player/mediaplayer/skins",
                    "SKIN" => $user_type_settings["SKIN"],
                    "CONTROLBAR" => "over",
                    "WMODE" => $user_type_settings["WMODE_FLV"],
                    "PLAYLIST" => "right",
                    "PLAYLIST_SIZE" => "180",
                    "LOGO" => "",
                    "LOGO_LINK" => "",
                    "LOGO_POSITION" => "none",
                    "PLUGINS" => array(
                            0 => "hd",
                            1 => "",
                    ),
                    "PLUGINS_TWEETIT-1" => "tweetit.link=",
                    "PLUGINS_FBIT-1" => "fbit.link=",
                    "ADDITIONAL_FLASHVARS" => $user_type_settings["FLASHVARS"],
                    "WMODE_WMV" => $user_type_settings["WMODE_WMV"],
                    "SHOW_CONTROLS" => "Y",
                    "PLAYLIST_TYPE" => "xspf",
                    "PLAYLIST_PREVIEW_WIDTH" => "64",
                    "PLAYLIST_PREVIEW_HEIGHT" => "48",
                    "SHOW_DIGITS" => "Y",
                    "CONTROLS_BGCOLOR" => $user_type_settings["BGCOLOR"],
                    "CONTROLS_COLOR" => $user_type_settings["COLOR"],
                    "CONTROLS_OVER_COLOR" => $user_type_settings["OVER_COLOR"],
                    "SCREEN_COLOR" => $user_type_settings["SCREEN_COLOR"],
                    "AUTOSTART" => $user_type_settings["AUTOSTART"],
                    "REPEAT" => "list",
                    "VOLUME" => $user_type_settings["VOLUME"],
                    "MUTE" => "N",
                    "HIGH_QUALITY" => "Y",
                    "SHUFFLE" => "N",
                    "START_ITEM" => "1",
                    "ADVANCED_MODE_SETTINGS" => "Y",
                    "PLAYER_ID" => "",
                    "BUFFER_LENGTH" => $user_type_settings["BUFFER_LENGTH"],
                    "DOWNLOAD_LINK" => "",
                    "DOWNLOAD_LINK_TARGET" => "_self",
                    "ADDITIONAL_WMVVARS" => "",
                    "ALLOW_SWF" => "N",
                    "COMPONENT_TEMPLATE" => ".default",
                    "PLUGINS_HD" => "file=fullscreen=false",
                    "PLUGINS_VIRAL-2" => "viral.onpause=false
                                        viral.oncomplete=true
                                        viral.allowmenu=false
                                        viral.functions=all
                                        viral.link=
                                        viral.email_subject=text
                                        viral.email_footer=text
                                        viral.embed=
                                        ",
                    "PLUGINS_FLOW-1" => "flow.coverheight=100
                                        flow.coverwidth=150
                                        ",
                    "PLUGINS_GAPRO-1" => "gapro.accountid=UA-XXXXXXX-X
                                        gapro.trackstarts=true
                                        gapro.trackpercentage=true
                                        gapro.tracktime=true
                                        ",
                    "PLUGINS_DRELATED-1" => "",
                    "PLUGINS_REVOLT-1" => "",
                    "PLUGINS_YOUSEARCH-1" => "",
                    "PLUGINS_SPECTRUMVISUALIZER-1" => ""
            ),
            false
        );?>
        <?if(!isset($arParams["VIDEO_TYPE"]) || $arParams["VIDEO_TYPE"]=="GRID"):?>
            <div class="element-video-card-grid">
                <p class="bxr-font-color element-video-card-title" itemprop="name"><?=$val["title"];?></p>
                <span class="element-video-card-desc" itemprop="name"><?=$val["desc"];?></span>
            </div>    
        <?endif;?>
    </div>
    <?if(isset($arParams["VIDEO_TYPE"]) && $arParams["VIDEO_TYPE"]=="LIST"):?>
        <div class=" col-lg-8 col-md-7 col-sm-6 col-xs-5 ">
            <p class="bxr-font-color element-video-card-title" itemprop="name"><?=$val["title"];?></p>
            <span class="element-video-card-desc" itemprop="name"><?=$val["desc"];?></span>
        </div>    
    </div>
    <?endif;?>
<?endforeach;?>
<?if(!isset($arParams["VIDEO_TYPE"]) || $arParams["VIDEO_TYPE"]=="GRID"):?></div><?endif;?>