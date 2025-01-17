<?
    global $BXRMarketPresentsSettings;
    
    if(!is_array($BXRMarketPresentsSettings))
        $BXRMarketPresentsSettings = array();
    
    if(strlen($arResult["INCLUDE"]["CSS"]) > 0)
        $BXRMarketPresentsSettings["CSS"][] = $arResult["INCLUDE"]["CSS"];
    
     if(strlen($arResult["INCLUDE"]["JS"]) > 0)
        $BXRMarketPresentsSettings["JS"][] = $arResult["INCLUDE"]["JS"];
     
?>