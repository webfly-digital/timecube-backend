<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var $this \Alexkova\Corporate\FormIblockComponent */

if(CModule::IncludeModule("alexkova.bxready2")) 
    \Alexkova\Bxready2\Component::prepareParams($arParams, "alexkova.market:form.iblock");

global $BXR_FORM_COUNTER;

if (intval($BXR_FORM_COUNTER)<=0)
    $BXR_FORM_COUNTER = 1;
else
    $BXR_FORM_COUNTER ++;

if($arParams["MODE"] == 'link')
{
	CJSCore::Init(array("popup"));
	$_SESSION["ALEXKOVA.MARKET"]["FORMS_PARAM"][$arParams["IBLOCK_ID"]] = serialize($arParams);
}

$this->includeComponentTemplate();