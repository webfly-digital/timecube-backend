<?
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php")){
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
}
if(CModule::IncludeModuleEx("disprove.reviewsmarket")):
?>
<?
$AJAX = htmlspecialcharsbx($_POST["AJAX"]);
$type = (int)htmlspecialcharsbx($_POST["type"]);
$id = (int)htmlspecialcharsbx($_POST["id"]);
if($AJAX && $AJAX == 'Y' && $id > 0 && $_SERVER["REMOTE_ADDR"]){
	$list = DRM::likeID($id,$_SERVER["REMOTE_ADDR"]);
	if(!$list) DRM::likeIDAdd($id,$_SERVER["REMOTE_ADDR"],$type);
}
endif;
?>