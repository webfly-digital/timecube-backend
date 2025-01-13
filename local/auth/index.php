<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");?>
<?

$APPLICATION->IncludeComponent(
    "bitrix:rest.authorize",
    ".default",
    array());
?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
