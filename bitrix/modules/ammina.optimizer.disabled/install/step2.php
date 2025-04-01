<? if (!check_bitrix_sessid()) return; ?>
<?
global $errors;

if (amopt_strlen($errors) <= 0):
	echo CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("MOD_INST_OK"), "DETAILS" => GetMessage("ammina.optimizer_INSTALLOK_NOTE"), "TYPE" => "OK", "HTML" => true));
else:
	for ($i = 0; $i < count($errors); $i++)
		$alErrors .= $errors[$i] . "<br>";
	echo CAdminMessage::ShowMessage(Array("TYPE" => "ERROR", "MESSAGE" => GetMessage("MOD_INST_ERR"), "DETAILS" => $alErrors, "HTML" => true));
endif;
if ($ex = $APPLICATION->GetException()) {
	echo CAdminMessage::ShowMessage(Array("TYPE" => "ERROR", "MESSAGE" => GetMessage("MOD_INST_ERR"), "HTML" => true, "DETAILS" => $ex->GetString()));
}

?>
<form action="<? echo $APPLICATION->GetCurPage() ?>">
    <input type="hidden" name="lang" value="<? echo LANG ?>"/>
    <input type="submit" name="" value="<? echo GetMessage("MOD_BACK") ?>"/>
</form>