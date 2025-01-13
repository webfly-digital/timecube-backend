<?
ini_set('serialize_precision', -1);
ob_start(); 
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
ob_clean();
?>
<?$APPLICATION->IncludeComponent(
    "creative:creative.kkmserver",
    "",
    Array(
        "CACHE_TYPE" => "N",
        "COMPOSITE_FRAME_MODE" => "N",
        "COMPOSITE_FRAME_TYPE" => "DYNAMIC_WITHOUT_STUB",
        "LOGIN" => "kkmserver",
        "PASSWORD" => "026dad56c79203544e7a941b8c5103fc"
    ),
    false
);?>
<?
$output = ob_get_contents();
$o = explode('</script>', $output);
ob_end_clean();
if (count($o) == 1) {
	echo $o[0];
} else {
	echo $o[1];
}
?>
<?/*if($USER->IsAdmin()) {
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
} else {
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
}*/?>