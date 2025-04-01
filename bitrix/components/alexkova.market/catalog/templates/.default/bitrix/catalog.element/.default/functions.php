<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if (!function_exists('getSliderForItem'))
{
	function getSliderForItem(&$item, $propertyCode, $addDetailToSlider, $skuProp = false, $addSku2Slider = false, $addAdditSku2Slider = false)
	{
		$result = array();
                if ($skuProp) {
                    foreach ($item['PROPERTIES'] as $pCode => $arProp) {
                        if (!in_array($pCode, $skuProp)) continue;
                        if ($arProp["VALUE"])
                            $group[$pCode] = $arProp["VALUE"];
                    }
                } else 
                    $group["MAIN"] = 'default';
		if (!empty($item) && is_array($item))
		{
			if (
				'' != $propertyCode &&
				isset($item['PROPERTIES'][$propertyCode]) &&
				'F' == $item['PROPERTIES'][$propertyCode]['PROPERTY_TYPE']
			)
			{
                            if ($skuProp && $addAdditSku2Slider && $addSku2Slider) $group["MAIN"] = 'default';
				if ('MORE_PHOTO' == $propertyCode && isset($item['MORE_PHOTO']) && !empty($item['MORE_PHOTO']))
				{
					foreach ($item['MORE_PHOTO'] as &$onePhoto)
					{
						$result[] = array(
							'ID' => intval($onePhoto['ID']),
							'SRC' => $onePhoto['SRC'],
							'WIDTH' => intval($onePhoto['WIDTH']),
							'HEIGHT' => intval($onePhoto['HEIGHT']),
                                                        'ITEM_ID' => $item["ID"],
                                                        'GROUP' => $group
						);
					}
					unset($onePhoto);
				}
				else
				{
					if (
						isset($item['DISPLAY_PROPERTIES'][$propertyCode]['FILE_VALUE']) &&
						!empty($item['DISPLAY_PROPERTIES'][$propertyCode]['FILE_VALUE'])
					)
					{
						$fileValues = (
							isset($item['DISPLAY_PROPERTIES'][$propertyCode]['FILE_VALUE']['ID']) ?
							array(0 => $item['DISPLAY_PROPERTIES'][$propertyCode]['FILE_VALUE']) :
							$item['DISPLAY_PROPERTIES'][$propertyCode]['FILE_VALUE']
						);
						foreach ($fileValues as &$oneFileValue)
						{
							$result[] = array(
								'ID' => intval($oneFileValue['ID']),
								'SRC' => $oneFileValue['SRC'],
								'WIDTH' => intval($oneFileValue['WIDTH']),
								'HEIGHT' => intval($oneFileValue['HEIGHT']),
                                                                'ITEM_ID' => $item["ID"],
                                                                'GROUP' => $group
							);
						}
						if (isset($oneFileValue))
							unset($oneFileValue);
					}
					else
					{
						$propValues = $item['PROPERTIES'][$propertyCode]['VALUE'];
						if (!is_array($propValues))
							$propValues = array($propValues);

						foreach ($propValues as &$oneValue)
						{
							$oneFileValue = CFile::GetFileArray($oneValue);
							if (isset($oneFileValue['ID']))
							{
								$result[] = array(
									'ID' => intval($oneFileValue['ID']),
									'SRC' => $oneFileValue['SRC'],
									'WIDTH' => intval($oneFileValue['WIDTH']),
									'HEIGHT' => intval($oneFileValue['HEIGHT']),
                                                                        'ITEM_ID' => $item["ID"],
                                                                        'GROUP' => $group
								);
							}
						}
						if (isset($oneValue))
							unset($oneValue);
					}
				}
			}
			if ($addDetailToSlider || empty($result))
			{
				if (!empty($item['DETAIL_PICTURE']))
				{
					if (!is_array($item['DETAIL_PICTURE']))
						$item['DETAIL_PICTURE'] = CFile::GetFileArray($item['DETAIL_PICTURE']);
					if (isset($item['DETAIL_PICTURE']['ID']))
					{
                                            if ($skuProp && $addSku2Slider) $group["MAIN"] = 'default';
						array_unshift(
							$result,
							array(
								'ID' => intval($item['DETAIL_PICTURE']['ID']),
								'SRC' => $item['DETAIL_PICTURE']['SRC'],
								'WIDTH' => intval($item['DETAIL_PICTURE']['WIDTH']),
								'HEIGHT' => intval($item['DETAIL_PICTURE']['HEIGHT']),
                                                                'ITEM_ID' => $item["ID"],
                                                                'GROUP' => $group
							)
						);
					}
				}
			}
		}
                
//                if (empty($result) && $skuProp) {
                if (empty($result) && ($skuProp || (!$addAdditSku2Slider && !empty($addSku2Slider)))) {
                    if ($skuProp)
                        $group["MAIN"] = 'none';
                    elseif (!$addAdditSku2Slider && !$addSku2Slider) 
                        $group["MAIN"] = 'default';
                    $result[] = array(
                        'ID' => 0,
                        'SRC' => '/bitrix/tools/bxready/.default/no-image.png',
                        'ITEM_ID' => $item["ID"],
                        'GROUP' => $group,
                        'TYPE' => "NO_PHOTO" 
                    );
                }
                
		return $result;
	}
}


if (!function_exists('getRealSectionId'))
{
    function getRealSectionId($requestSid = false, $requestScode = false, $elId, $ibSid) {
        if (!intval($elId) || !intval($ibSid)) return false;
        
        $realSid = false;
        
        $elGroups = CIBlockElement::GetElementGroups($elId, true);
        while($arGroup = $elGroups->Fetch())
            $groups[$arGroup["ID"]] = $arGroup["CODE"];
        
        if (intval($requestSid) && array_key_exists($requestSid, $groups))
            return $requestSid;
        elseif (strlen($requestScode) && in_array($requestScode, $groups))
            return array_search($requestScode, $groups);
        elseif (intval($ibSid) && array_key_exists($ibSid, $groups))
            return $ibSid;
        else
            return key($groups);
    }
}

if (!function_exists('getSectionBasketProps'))
{
    function getSectionBasketProps($sid, $ibId) {
        if (!intval($sid) || !intval($ibId)) return false;
        
        $arBasketProps = array();
        
        $nav = CIBlockSection::GetNavChain($ibId, $sid, array("ID", "LEFT_MARGIN"));
        while($arSectionPath = $nav->GetNext()) {
            $arSPathId[$arSectionPath["LEFT_MARGIN"]] = $arSectionPath["ID"];
        }
        krsort($arSPathId);
        
        foreach ($arSPathId as $cSid) {
            $filter = array("IBLOCK_ID" => $ibId, "ID" => $cSid);
            $select = array("ID", "UF_REQUIRED_BP", "UF_OPTIONAL_BP");
            $res = CIBlockSection::GetList(Array($by=>$order), $filter, false, $select);
            if ($arFields = $res->GetNext())
            {
                if ( (is_array($arFields["UF_REQUIRED_BP"]) && count($arFields["UF_REQUIRED_BP"])) 
                    || (is_array($arFields["UF_OPTIONAL_BP"]) && count($arFields["UF_OPTIONAL_BP"])) ) {
                    $arBasketProps = array(
                        "REQUIRED" => $arFields["UF_REQUIRED_BP"],
                        "OPTIONAL" => $arFields["UF_OPTIONAL_BP"],
                    );
                    break;
                }
            }
        }
        
        return $arBasketProps;
    }
}
