<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");?>
<?
$APPLICATION->IncludeComponent(
	"bitrix:rest.marketplace.localapp",
	".default",
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"APPLICATION_URL" => "/marketplace/app/#id#/",
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/local/rest/",
		"SEF_URL_TEMPLATES" => array(
			"index" => "",
			"list" => "list/",
			"edit" => "edite/#id#/",
		)
	),
	false
);


		?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
