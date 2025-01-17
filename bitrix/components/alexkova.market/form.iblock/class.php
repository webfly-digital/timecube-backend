<?
namespace Alexkova\Corporate;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class FormIblockComponent extends \CBitrixComponent{

	private $iblockId = 0;

	public function onPrepareComponentParams($params)
	{
		$params["MODE"] = in_array($params["MODE"], array('static', 'link')) ? $params["MODE"] : 'static';
		$params["IBLOCK_ID"] = intval($params["IBLOCK_ID"]);
		return $params;
	}

	public function executeComponent()
	{
		$this->iblockId = intval($this->arParams["IBLOCK_ID"]);
		if(!$this->iblockId)
			return false;
		return parent::executeComponent();
	}

}