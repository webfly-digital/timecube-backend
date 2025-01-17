<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if ('html' == $arResult['DETAIL_TEXT_TYPE']){
    echo $arResult['DETAIL_TEXT'];
}else{?>
    <p><?=$arResult['DETAIL_TEXT'];?></p>
<?}