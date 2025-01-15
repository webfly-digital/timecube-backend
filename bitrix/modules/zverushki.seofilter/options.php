<?
use Bitrix\Main\Localization\Loc,
	Bitrix\Main\Application,
	Bitrix\Main\Loader,
    Zverushki\Seofilter\Configure\site,
    Zverushki\Seofilter\Agent,
    Bitrix\Main\Config\Option,
    Zverushki\Seofilter\Configure\form;

$moduleId = 'zverushki.seofilter';
$moduleForm = 'seofilter'.md5($moduleId);

Loader::includeSharewareModule($moduleId);
Loc::loadMessages($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
Loc::loadMessages(__FILE__);
\CAdminNotify::DeleteByTag('ZS_INSTALLNOTIFY');
$arSetting = array();
$siteParams = array();
$newSite = new site();
$siteParams = $newSite->GetLang();
$newSite->GetLang();

define("HL_SITE_ID", $siteParams["LID"]);

$POST_RIGHT = $APPLICATION->GetGroupRight($moduleId);
if($POST_RIGHT>="R") {
    $request = \Bitrix\Main\HttpApplication::GetInstance()->GetContext()->GetRequest();
    $form = new form();
    $form->setData($request);

    $arSetting["siteList"] = array();

    $aTabs = array();
    $rsSites = \CSite::GetList($by = "sort", $order = "desc");
    while ($arSite = $rsSites->Fetch()) {
        /*if($arSite['ACTIVE'] != 'Y')
            continue;*/
        $arSetting["siteList"][$arSite["LID"]] = array("NAME" => "[" . $arSite["LID"] . "] " .$arSite["NAME"], "SELECT" => (HL_SITE_ID == $arSite["LID"] ? "Y" : "N"));
        $aTabs[] = array(
            "DIV" => "edit" . $arSite["LID"],
            "LID" => $arSite["LID"],
            "TAB" => "[" . $arSite["LID"] . "] " . $arSite["NAME"],
            "TITLE" => Loc::getMessage($moduleId . "_TAB_TITLE"),
        );
        // break;
    }
    if(count($aTabs) > 1){
        $aTabsTmp = $aTabs;
        $aTabs = array();
        $aTabs[] = $form->getDefaltTab();
        foreach ($aTabsTmp as $tab) {
            $aTabs[] = $tab;
        }
    }

    $form->Init($arSetting);
    if ($request->isPost() && $request["Update"] && check_bitrix_sessid()):
        if(!$form->setPostValues($request->getPost($form->prefix)))
            foreach ($aTabs as $aTab){
                if(!empty($form->error[$aTab["LID"]])){
                    CAdminMessage::ShowMessage(array(
                        "MESSAGE" => $aTab["TAB"],
                        "DETAILS" => implode("<br>", $form->error[$aTab["LID"]]),
                        "HTML" => true,
                        "TYPE" => "ERROR",
                     ));
                }
            }
            // CAdminMessage::ShowMessage(implode("\n", $form->error));
        else
            LocalRedirect($APPLICATION->GetCurPageParam("lang=".$request["lang"]."&mid=".$request["mid"]."&tabControl_active_tab=".$request["tabControl_active_tab"]."&okSave=Y", array("lang", "mid", "okSave", "tabControl_active_tab")));
    endif;

    if($request["okSave"] === "Y"){
        Agent::addGenerateMap();
        CAdminMessage::ShowNote(LOC::getMessage("SEOFILTER_SYSTEM_FIELDSAVE_OK"));
    }


    $tabControl = new CAdminTabControl('tabControl', $aTabs);
    echo BeginNote();
    echo Loc::getMessage("SEOFILTER_OPTION_NOTE", array("#moduleId#" => $moduleId, "#lang#" => $request["lang"]));
    echo EndNote();
    ?><form id="form_<? echo $moduleForm;?>" method="post" action='<? echo $APPLICATION->GetCurPageParam("lang=".$request["lang"]."&mid=".$request["mid"], array("lang", "mid", "okSave")) ?>' name="<?=$moduleForm?>_settings"><?
        $tabControl->Begin();

        foreach ($aTabs as $key => $tab):
            $tabControl->BeginNextTab();
            echo $form->getTab($tab["LID"]);
        endforeach;

        $tabControl->Buttons();
        ?>
        <input type="submit" name="Update" value="<? echo Loc::getMessage("SEOFILTER_OPTION_BUTTON_UPD_NAME"); ?>" />
        <input type="reset" name="reset" value="<? echo Loc::getMessage("SEOFILTER_OPTION_BUTTON_CANCEL_NAME"); ?>" /><?
        echo bitrix_sessid_post();
        $tabControl->End();
    ?></form><?
}else{
    CAdminMessage::ShowMessage(Loc::getMessage($moduleId . "_SYSTEM_ERROR_PERMITION_DENIED"));
    return;
}