<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Loader;

/**
 * Компонент выбирает элементы инфоблока по ее ID
 */
class SectionElementListComponent extends CBitrixComponent
{
    /**
     * List of errors
     * @var array
     */
    protected $errors = [];

    /**
     * Write errors in arResult
     */
    protected function writeErrors()
    {
        $errors = $this->errors;
        if ($errors)
            $this->arResult['ERRORS'] = $errors;
    }

    /**
     * Проверяет обязательные параметры
     */
    protected function checkMandatory()
    {
        $this->initModule();
        $this->checkParams();
    }

    /**
     * Загрузка модуля
     */
    protected function initModule()
    {
        if (!Loader::includeModule('iblock')) {
            $this->errors[] = 'Модуль "Инфоблоки" не установлен';
        }
    }

    /**
     * Проверят arParams
     */
    protected function checkParams()
    {
        if (!$this->arParams['IBLOCK_ID']) {
            $this->errors[] = 'Не передан IBLOCK_ID!';
        }
    }

    protected function getFilter(){
        if($this->arParams["FILTER_NAME"] == '' || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $this->arParams["FILTER_NAME"]))
        {
            $arrFilter = [];
        }
        else
        {
            $arrFilter = $GLOBALS[$this->arParams["FILTER_NAME"]];
            if(!is_array($arrFilter))
                $arrFilter = [];
        }
        return $arrFilter;
    }

    protected function getItems()
    {
        $addFilter = $this->getFilter();
        $sectionData = \CIblockSection::getList(['SORT'=>'asc'], ['IBLOCK_ID'=>$this->arParams['IBLOCK_ID'], 'ACTIVE'=>'Y'], true,['ID', 'NAME', "LIST_PAGE_URL"], false);

        while($ob_s = $sectionData->fetch()){
            $elements = [];
            $items = CIBlockElement::getList(['ACTIVE_FROM'=>'desc', 'ID'=>'desc','SORT'=>'asc'],array_merge(['IBLOCK_SECTION_ID'=>$ob_s['ID'], 'ACTIVE'=>'Y'], $addFilter),false,['nTopCount'=>5],
                    ['ID', 'NAME', 'DETAIL_PAGE_URL', 'PREVIEW_PICTURE', 'DATE_ACTIVE_FROM', 'DATE_CREATE']);
            
            while($ob_item = $items->getNext()){
                if ($ob_item['PREVIEW_PICTURE']){
                    $ob_item['PICTURE'] = CFile::resizeImageGet($ob_item["PREVIEW_PICTURE"], ['width'=>800, 'height'=>500], BX_RESIZE_IMAGE_EXACT, false);
                }
                $elements[] = $ob_item;
            }

            if (count($elements) > 0)
                $res_section[]=['SECTION'=>$ob_s, 'ITEMS'=>$elements];
        }

        $this->arResult['ITEMS'] = $res_section;
    }

    public function executeComponent()
    {
        if ($this->StartResultCache()){
            $this->checkMandatory();
            if ($this->errors) {
                $this->writeErrors();
                $this->abortResultCache();
            } else {
                $this->getItems();
            }
            $this->includeComponentTemplate();
        }

    }
}