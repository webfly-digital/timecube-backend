<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?
    $location = "right";
    if(isset($arParams["LOCATION_HORIZONTALLY"]) && $arParams["LOCATION_HORIZONTALLY"] == "left")
        $location = "left";
    
    $paddingH = "";
    if(isset($arParams["BUTTON_UP_HORIZONTALLY_INDENT"]) && is_numeric($arParams["BUTTON_UP_HORIZONTALLY_INDENT"]))
        $paddingH = $location . ":" .  $arParams["BUTTON_UP_HORIZONTALLY_INDENT"] . "px;";
    
    $paddingV = "";
    if(isset($arParams["BUTTON_UP_VERTICAL_INDENT"]) && is_numeric($arParams["BUTTON_UP_VERTICAL_INDENT"]))
        $paddingV = "bottom:" .  $arParams["BUTTON_UP_VERTICAL_INDENT"] . "px;";
            
            
?>
<button type="button" class="bxr-button-up <?=$location;?> bxr-color-flat bxr-bg-hover-dark-flat" style="<?=$paddingH;?> <?=$paddingV;?>">
    <i class="fa fa-angle-up"></i>
</button>
<script>
    $(document).ready(function(){
        window.BXReady.Market.buttonUp.init(
            "<?if(isset($arParams["BUTTON_UP_TOP_SHOW"]) && is_numeric($arParams["BUTTON_UP_TOP_SHOW"])) echo $arParams["BUTTON_UP_TOP_SHOW"]; else echo "150";?>",
            "<?if(isset($arParams["BUTTON_UP_SPEED"]) && is_numeric($arParams["BUTTON_UP_SPEED"])) echo $arParams["BUTTON_UP_SPEED"]; else echo "1000";?>");
    });
</script>