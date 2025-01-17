<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**	@var $this CBitrixComponentTemplate **/
$this->setFrameMode(true);
$path = $_SERVER["DOCUMENT_ROOT"].$this->GetFolder()."/".$arParams["MODE"].".php";
if(file_exists($path))
	include $path;