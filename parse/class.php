<?php

use Bitrix\Main\SystemException;

class WFBuildCatalogXmlParse
{

    const SUCCESS_LOG = '/parse/log.txt';

    protected $iblockId;
    protected $node;
    protected $siteItemId = 0;
    protected $filePath = '';
    protected $donorItemId;

    /**
     * @return mixed
     */
    public function getIblockId()
    {
        return $this->iblockId;
    }

    /**
     * @return mixed
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @param mixed $node
     */
    public function setNode($node)
    {
        $this->node = $node;
    }

    /**
     * @param mixed $iblockId
     */
    public function setIblockId($iblockId)
    {
        $this->iblockId = $iblockId;
    }

    public function __construct($iblockId, $node)
    {
        $this->setIblockId($iblockId);
        $this->setNode($node);
    }

    protected function xml2array($xmlObject, $out = array())
    {
        foreach ((array)$xmlObject as $index => $node)
            $out[$index] = (is_object($node)) ? $this->xml2array($node) : $node;

        return $out;
    }

    protected function xmlObjectToString($obj)
    {
        $str = html_entity_decode($obj->__toString());
        return $str;
    }

    public function updateElement()
    {
        if (empty($this->node)) throw new SystemException("ОшибкаСтарт - Пустой узел XML");

        $fields = $this->xml2array($this->node->fields);
        if (empty($fields['EXTERNAL_ID'])) throw new SystemException("ОшибкаСтарт - Пустой EXTERNAL_ID XML узла");

        array_walk($fields, function (&$item, $key) {
            html_entity_decode($item);
        });
        $siteItem = \CIBlockElement::GetList([], ['IBLOCK_ID' => $this->iblockId, 'EXTERNAL_ID' => $fields['EXTERNAL_ID']], false, false, ['ID', 'IBLOCK_ID'])->fetch();
        if (empty($siteItem['ID'])) throw new SystemException("ОшибкаСтарт - Нет товара на сайте");

        $this->siteItemId = $siteItem['ID'];
        $this->donorItemId = $fields['ID'];

        try {
             $this->updateFields($fields);
        } catch (\Exception $e) {
            throw new SystemException("ОшибкаОбновленияПолей - " . $e->getMessage());
        }

        try {
            $this->updateProperties();
        } catch (\Exception $e) {
            throw new SystemException("ОшибкаОбновленияСвойств - " . $e->getMessage());
        }
        return $this->donorItemId;

    }

    protected function updateProperties()
    {
        if (empty($this->node->properties)) throw new SystemException("Пустой массив свойств");

        $updateProp = [];

        foreach ($this->node->properties->xpath('//property') as $property) {
            $propertyCode = $this->xmlObjectToString($property->CODE);
            $updateProp[$propertyCode] = ['CODE' => $this->xmlObjectToString($property->CODE), 'VALUE' => [], 'DESCRIPTION' => []];

            $i = 0;
            foreach ($property->VALUES->VALUE as $value) {

                $updateProp[$propertyCode]['VALUE'][$i] = $this->xmlObjectToString($value);

                if (!empty($property->DESCRIPTIONS->DESCRIPTION[$i]))
                    $updateProp[$propertyCode]['DESCRIPTION'][$i] = $this->xmlObjectToString($property->DESCRIPTIONS->DESCRIPTION[$i]);
                else
                    $updateProp[$propertyCode]['DESCRIPTION'][$i] = '';

                if ($property->PROPERTY_TYPE == 'L') {//список
                    if (!empty($updateProp[$propertyCode]['VALUE'][$i])) {
                        $valList = \CIBlockPropertyEnum::GetList([], ["IBLOCK_ID" => $this->getIblockId(), 'CODE' => $propertyCode, 'VALUE' => $updateProp[$propertyCode]['VALUE'][$i]])->fetch();
                        if (!empty($valList['ID'])) {
                            $updateProp[$propertyCode]['VALUE'][$i] = $valList['ID'];
                        } else {
                            AddMessage2Log("ОшибкаОбновленияСвойств - не найдено значение св-ва типа список. {$propertyCode} -> " . $updateProp[$propertyCode]['VALUE'][$i]);
                            $updateProp[$propertyCode]['VALUE'][$i] = 'WF_REMOVE';
                        }
                    }
                } elseif ($property->PROPERTY_TYPE == 'E') {//привязка к элементам
                    if (!empty($updateProp[$propertyCode]['VALUE'][$i])) {
                        $valElement = \CIBlockElement::getList([], ['IBLOCK_ID' => $this->getIblockId(), 'EXTERNAL_ID' => $updateProp[$propertyCode]['VALUE'][$i]], false, false, ['IBLOCK_ID', 'ID'])->fetch();
                        if (!empty($valElement['ID'])) {
                            $updateProp[$propertyCode]['VALUE'][$i] = $valElement['ID'];
                        } else {
                            AddMessage2Log("ОшибкаОбновленияСвойств - не найдено значение св-ва привязка к элеметам. {$propertyCode} -> " . $updateProp[$propertyCode]['VALUE'][$i]);
                            $updateProp[$propertyCode]['VALUE'][$i] = 'WF_REMOVE';
                        }
                    }
                } elseif ($property->PROPERTY_TYPE == 'F') {//файл
                    if (!empty($updateProp[$propertyCode]['VALUE'][$i])) {
                        $updateProp[$propertyCode]['VALUE'][$i] = CFile::MakeFileArray($updateProp[$propertyCode]['VALUE'][$i]);
                    }
                } elseif ($property->PROPERTY_TYPE == 'S' && $property->USER_TYPE == 'HTML') {//html/текст
                    $updateProp[$propertyCode]['HTML_TEXT'] = 'Y';
                    if (!empty($property->VALUES->TYPE[$i]))
                        $updateProp[$propertyCode]['TEXT_TYPE'][$i] = $this->xmlObjectToString($property->VALUES->TYPE[$i]);
                }
                $i++;
            }
        }
        if (!empty($updateProp)) {
            $propsToUpdate = [];
            foreach ($updateProp as $pCode => $pData) {
                if (in_array('WF_REMOVE', $pData))
                    continue;
                foreach ($pData['VALUE'] as $pKey => $pValue){
                    if (!empty($pData['HTML_TEXT'])){//html/текст
                        $propsToUpdate[$pCode][] = ['VALUE' => ['TYPE'=>$pData['TEXT_TYPE'][$pKey], 'TEXT'=>$pValue], 'DESCRIPTION'=>$pData['DESCRIPTION'][$pKey]];
                    }else{
                        $propsToUpdate[$pCode][] = ['VALUE' => $pValue, 'DESCRIPTION'=>$pData['DESCRIPTION'][$pKey]];
                    }

                }
            }
            if (!empty($propsToUpdate)){
               \CIBlockElement::SetPropertyValuesEx($this->siteItemId, $this->getIblockId(), $propsToUpdate);
               file_put_contents($_SERVER['DOCUMENT_ROOT'] . self::SUCCESS_LOG, "PROPS: {$this->donorItemId} -> $this->siteItemId\r\n", FILE_APPEND);
                CIBlock::clearIblockTagCache($this->getIblockId());
            }
        }
    }

    protected function updateFields($fields)
    {
        if (empty($this->siteItemId)) throw new SystemException("Пустой ID элемента на сайте");
        unset ($fields['ID']);
        unset ($fields['EXTERNAL_ID']);

        if (!empty($fields['PREVIEW_PICTURE']))
            $fields['PREVIEW_PICTURE'] = CFile::MakeFileArray($fields['PREVIEW_PICTURE']);
        else
            $fields['PREVIEW_PICTURE'] = ['del' => 'Y'];

        if (!empty($fields['DETAIL_PICTURE']))
            $fields['DETAIL_PICTURE'] = CFile::MakeFileArray($fields['DETAIL_PICTURE']);
        else
            $fields['DETAIL_PICTURE'] = ['del' => 'Y'];

        if (!empty($fields['PREVIEW_TEXT'])) $fields['PREVIEW_TEXT'] = html_entity_decode($fields['PREVIEW_TEXT']);
        if (!empty($fields['DETAIL_TEXT'])) $fields['DETAIL_TEXT'] = html_entity_decode($fields['DETAIL_TEXT']);


        $el = new \CIBlockElement;
        if (!$el->Update($this->siteItemId, $fields)) {
            throw new SystemException($el->LAST_ERROR);
        } else {
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . self::SUCCESS_LOG, "FIELDS: {$this->donorItemId} -> $this->siteItemId\r\n", FILE_APPEND);
        }

    }


}