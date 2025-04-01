<?
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
if(!check_bitrix_sessid())
    return;
echo CAdminMessage::ShowNote(Loc::getMessage("CREATIVE_KKMSERVER_MODULE_UNINSTALLED"));
?>