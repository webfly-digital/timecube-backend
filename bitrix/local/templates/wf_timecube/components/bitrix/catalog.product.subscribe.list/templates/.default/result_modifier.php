<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */
if (!isset($arParams['LINE_ELEMENT_COUNT']))
	$arParams['LINE_ELEMENT_COUNT'] = 3;
$arParams['LINE_ELEMENT_COUNT'] = intval($arParams['LINE_ELEMENT_COUNT']);
if (2 > $arParams['LINE_ELEMENT_COUNT'] || 5 < $arParams['LINE_ELEMENT_COUNT'])
	$arParams['LINE_ELEMENT_COUNT'] = 3;

$arParams['TEMPLATE_THEME'] = (string)($arParams['TEMPLATE_THEME']);
if ('' != $arParams['TEMPLATE_THEME'])
{
	$arParams['TEMPLATE_THEME'] = preg_replace('/[^a-zA-Z0-9_\-\(\)\!]/', '', $arParams['TEMPLATE_THEME']);
	if ('site' == $arParams['TEMPLATE_THEME'])
	{
		$templateId = COption::GetOptionString("main", "wizard_template_id", "eshop_bootstrap", SITE_ID);
		$templateId = (preg_match("/^eshop_adapt/", $templateId)) ? "eshop_adapt" : $templateId;
		$arParams['TEMPLATE_THEME'] = COption::GetOptionString('main', 'wizard_'.$templateId.'_theme_id', 'blue', SITE_ID);
	}
	if ('' != $arParams['TEMPLATE_THEME'])
	{
		if (!is_file($_SERVER['DOCUMENT_ROOT'].$this->GetFolder().'/themes/'.$arParams['TEMPLATE_THEME'].'/style.css'))
			$arParams['TEMPLATE_THEME'] = '';
	}
}
if ('' == $arParams['TEMPLATE_THEME'])
	$arParams['TEMPLATE_THEME'] = 'blue';

if (!empty($arResult['ITEMS']))
{
	$arEmptyPreview = false;
	$strEmptyPreview = $this->GetFolder() . '/images/no_photo.png';
	if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strEmptyPreview))
	{
		$arSizes = getimagesize($_SERVER['DOCUMENT_ROOT'] . $strEmptyPreview);
		if (!empty($arSizes))
		{
			$arEmptyPreview = array(
				'SRC' => $strEmptyPreview,
				'WIDTH' => intval($arSizes[0]),
				'HEIGHT' => intval($arSizes[1])
			);
		}
		unset($arSizes);
	}
	unset($strEmptyPrev);

	$skuPropList = array();
	$skuPropIds = array();
	$skuPropKeys = array();
	$catalogs = array();
	$arNewItemsList = array();
	foreach ($arResult['ITEMS'] as $key => $arItem)
	{
		$itemId = $arItem['ID'];

		foreach($arResult['CATALOGS'] as $catalog)
		{
			$offersCatalogId = (int)$catalog['OFFERS_IBLOCK_ID'];
			$offersPropId = (int)$catalog['OFFERS_PROPERTY_ID'];
			$catalogId = (int)$catalog['IBLOCK_ID'];
			$sku = false;
			if($offersCatalogId > 0 && $offersPropId > 0)
			{
				$sku = array(
					'IBLOCK_ID' => $offersCatalogId,
					'SKU_PROPERTY_ID' => $offersPropId,
					'PRODUCT_IBLOCK_ID' => $catalogId
				);
			}
			if(!empty($sku) && is_array($sku))
			{
				if (empty($skuPropList[$itemId]))
				{
					$skuPropList[$itemId] = CIBlockPriceTools::getTreeProperties(
						$sku,
						$arParams['OFFER_TREE_PROPS'][$itemId],
						array('PICT' => $arEmptyPreview, 'NAME' => '-')
					);
				}
				CIBlockPriceTools::getTreePropertyValues($skuPropList[$itemId], $arParams['NEED_VALUES'][$itemId]);

				$skuPropIds[$itemId] = array_keys($skuPropList[$itemId]);
				if (!empty($skuPropIds[$arItem['ID']]))
					$skuPropKeys[$itemId] = array_fill_keys($skuPropIds[$itemId], true);

				foreach($skuPropList[$itemId] as $propertyCode => &$propertyValue)
				{
					foreach($propertyValue['VALUES'] as $keyProperty => $value)
					{
						if($propertyValue['SHOW_MODE'] == 'PICT')
							$desiredValue = $value['XML_ID'];
						else
							$desiredValue = $value['NAME'];
						if(!in_array($desiredValue, $arParams['PROPERTY_VALUE'][$itemId][$propertyCode]))
							unset($propertyValue['VALUES'][$keyProperty]);
					}
				}
			}
		}

		$arItem['CATALOG_QUANTITY'] = (
		0 < $arItem['CATALOG_QUANTITY'] && is_float($arItem['CATALOG_MEASURE_RATIO'])
			? floatval($arItem['CATALOG_QUANTITY'])
			: intval($arItem['CATALOG_QUANTITY'])
		);
		$arItem['CATALOG'] = false;
		$arItem['LABEL'] = false;
		if (!isset($arItem['CATALOG_SUBSCRIPTION']) || 'Y' != $arItem['CATALOG_SUBSCRIPTION'])
			$arItem['CATALOG_SUBSCRIPTION'] = 'N';

		// Item Label Properties
		$itemIblockId = $arItem['IBLOCK_ID'];
		$propertyName = isset($arParams['LABEL_PROP'][$itemIblockId]) ? $arParams['LABEL_PROP'][$itemIblockId] : false;

		if ($propertyName && isset($arItem['PROPERTIES'][$propertyName]))
		{
			$property = $arItem['PROPERTIES'][$propertyName];

			if (!empty($property['VALUE']))
			{
				if ('N' == $property['MULTIPLE'] && 'L' == $property['PROPERTY_TYPE'] && 'C' == $property['LIST_TYPE'])
				{
					$arItem['LABEL_VALUE'] = $property['NAME'];
				}
				else
				{
					$arItem['LABEL_VALUE'] = (is_array($property['VALUE'])
						? implode(' / ', $property['VALUE'])
						: $property['VALUE']
					);
				}
				$arItem['LABEL'] = true;

				if (isset($arItem['DISPLAY_PROPERTIES'][$propertyName]))
					unset($arItem['DISPLAY_PROPERTIES'][$propertyName]);
			}
			unset($property);
		}
		// !Item Label Properties

		// item double images
		$productPictures = array(
			"PICT" => false,
			"SECOND_PICT" => false
		);

		if (isset($arParams['ADDITIONAL_PICT_PROP'][$itemIblockId]))
		{
			$productPictures = CIBlockPriceTools::getDoublePicturesForItem($arItem, $arParams['ADDITIONAL_PICT_PROP'][$itemIblockId]);
		}
		else
		{
			$productPictures = CIBlockPriceTools::getDoublePicturesForItem($arItem, false);
		}
		if (empty($productPictures['PICT']))
			$productPictures['PICT'] = $arEmptyPreview;
		if (empty($productPictures['SECOND_PICT']))
			$productPictures['SECOND_PICT'] = $productPictures['PICT'];
		$arItem['PREVIEW_PICTURE'] = $productPictures['PICT'];
		$arItem['PREVIEW_PICTURE_SECOND'] = $productPictures['SECOND_PICT'];
		$arItem['SECOND_PICT'] = true;
		$arItem['PRODUCT_PREVIEW'] = $productPictures['PICT'];
		$arItem['PRODUCT_PREVIEW_SECOND'] = $productPictures['SECOND_PICT'];
		// !item double images

		$arItem['CATALOG'] = true;
		if (!isset($arItem['CATALOG_TYPE']))
			$arItem['CATALOG_TYPE'] = CCatalogProduct::TYPE_PRODUCT;
		if (
			(CCatalogProduct::TYPE_PRODUCT == $arItem['CATALOG_TYPE'] || CCatalogProduct::TYPE_SKU == $arItem['CATALOG_TYPE'])
			&& !empty($arItem['OFFERS'])
		)
		{
			$arItem['CATALOG_TYPE'] = CCatalogProduct::TYPE_SKU;
		}
		switch ($arItem['CATALOG_TYPE'])
		{
			case CCatalogProduct::TYPE_SET:
				$arItem['OFFERS'] = array();
				$arItem['CATALOG_MEASURE_RATIO'] = 1;
				$arItem['CATALOG_QUANTITY'] = 0;
				$arItem['CHECK_QUANTITY'] = false;
				break;
			case CCatalogProduct::TYPE_SKU:
				break;
			case CCatalogProduct::TYPE_PRODUCT:
			default:
				$arItem['CHECK_QUANTITY'] = ('Y' == $arItem['CATALOG_QUANTITY_TRACE'] && 'N' == $arItem['CATALOG_CAN_BUY_ZERO']);
				break;
		}

		// Offers
		if ($arItem['CATALOG'] && isset($arItem['OFFERS']) && !empty($arItem['OFFERS']))
		{
			$arMatrixFields = $skuPropKeys[$itemId];
			$arMatrix = array();

			$arNewOffers = array();
			$boolSKUDisplayProperties = false;
			$arItem['OFFERS_PROP'] = false;

			foreach ($arItem['OFFERS'] as $keyOffer => $arOffer)
			{
				if(!array_key_exists($arOffer['ID'], $arParams['LIST_SUBSCRIPTIONS']))
					continue;

				$arRow = array();
				foreach ($skuPropIds[$itemId] as $propkey => $strOneCode)
				{
					$arCell = array(
						'VALUE' => 0,
						'SORT' => PHP_INT_MAX,
						'NA' => true
					);
					$arCell['NA'] = false;

					if (isset($arOffer['DISPLAY_PROPERTIES'][$strOneCode]))
					{
						if('directory' == $skuPropList[$itemId][$strOneCode]['USER_TYPE'])
						{
							$intValue = $skuPropList[$itemId][$strOneCode]['XML_MAP'][$arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE']];
							$arCell['VALUE'] = $intValue;
						}
						elseif('L' == $skuPropList[$itemId][$strOneCode]['PROPERTY_TYPE'])
						{
							$arCell['VALUE'] = intval($arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE_ENUM_ID']);
						}
						elseif('E' == $skuPropList[$itemId][$strOneCode]['PROPERTY_TYPE'])
						{
							$arCell['VALUE'] = intval($arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE']);
						}
						$arCell['SORT'] = $skuPropList[$itemId][$strOneCode]['VALUES'][$arCell['VALUE']]['SORT'];
					}

					$arRow[$strOneCode] = $arCell;
				}
				$arMatrix[$keyOffer] = $arRow;

				$newOfferProps = array();
				if(!empty($arParams['PROPERTY_CODE'][$arOffer['IBLOCK_ID']]))
				{
					foreach($arParams['PROPERTY_CODE'][$arOffer['IBLOCK_ID']] as $propName)
						$newOfferProps[$propName] = $arOffer['DISPLAY_PROPERTIES'][$propName];
				}
				$arOffer['DISPLAY_PROPERTIES'] = $newOfferProps;

				$arOffer['CHECK_QUANTITY'] = ('Y' == $arOffer['CATALOG_QUANTITY_TRACE'] && 'N' == $arOffer['CATALOG_CAN_BUY_ZERO']);
				if (!isset($arOffer['CATALOG_MEASURE_RATIO']))
					$arOffer['CATALOG_MEASURE_RATIO'] = 1;
				if (!isset($arOffer['CATALOG_QUANTITY']))
					$arOffer['CATALOG_QUANTITY'] = 0;
				$arOffer['CATALOG_QUANTITY'] = (
				0 < $arOffer['CATALOG_QUANTITY'] && is_float($arOffer['CATALOG_MEASURE_RATIO'])
					? floatval($arOffer['CATALOG_QUANTITY'])
					: intval($arOffer['CATALOG_QUANTITY'])
				);
				$arOffer['CATALOG_TYPE'] = CCatalogProduct::TYPE_OFFER;
				CIBlockPriceTools::setRatioMinPrice($arOffer);

				$offerPictures = CIBlockPriceTools::getDoublePicturesForItem($arOffer, $arParams['ADDITIONAL_PICT_PROP'][$arOffer['IBLOCK_ID']]);
				$arOffer['OWNER_PICT'] = empty($offerPictures['PICT']);
				$arOffer['PREVIEW_PICTURE'] = false;
				$arOffer['PREVIEW_PICTURE_SECOND'] = false;
				$arOffer['SECOND_PICT'] = true;
				if (!$arOffer['OWNER_PICT'])
				{
					if (empty($offerPictures['SECOND_PICT']))
						$offerPictures['SECOND_PICT'] = $offerPictures['PICT'];
					$arOffer['PREVIEW_PICTURE'] = $offerPictures['PICT'];
					$arOffer['PREVIEW_PICTURE_SECOND'] = $offerPictures['SECOND_PICT'];
				}
				if ('' != $arParams['OFFER_ADD_PICT_PROP'] && isset($arOffer['DISPLAY_PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]))
					unset($arOffer['DISPLAY_PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]);
				$arNewOffers[$keyOffer] = $arOffer;
			}
			$arItem['OFFERS'] = $arNewOffers;

			$arUsedFields = array();
			$arSortFields = array();

			$matrixKeys = array_keys($arMatrix);
			foreach ($skuPropIds[$itemId] as $propkey => $propCode)
			{
				foreach ($matrixKeys as $keyOffer)
				{
					if (!isset($arItem['OFFERS'][$keyOffer]['TREE']))
						$arItem['OFFERS'][$keyOffer]['TREE'] = array();
					$propId = $skuPropList[$itemId][$propCode]['ID'];
					$value = $arMatrix[$keyOffer][$propCode]['VALUE'];
					if (!isset($arItem['SKU_TREE_VALUES'][$propId]))
						$arItem['SKU_TREE_VALUES'][$propId] = array();
					$arItem['SKU_TREE_VALUES'][$propId][$value] = true;
					$arItem['OFFERS'][$keyOffer]['TREE']['PROP_'.$propId] = $value;
					$arItem['OFFERS'][$keyOffer]['SKU_SORT_'.$propCode] = $arMatrix[$keyOffer][$propCode]['SORT'];
					$arUsedFields[$propCode] = true;
					$arSortFields['SKU_SORT_'.$propCode] = SORT_NUMERIC;
					unset($value, $propId);
				}
				unset($keyOffer);
			}
			unset($propkey, $propCode);
			unset($matrixKeys);
			$arItem['OFFERS_PROP'] = $arUsedFields;

			\Bitrix\Main\Type\Collection::sortByColumn($arItem['OFFERS'], $arSortFields);

			// Find Selected offer
			foreach($arItem['OFFERS']  as $ind => $offer)
				if($offer['SELECTED'])
				{
					$arItem['OFFERS_SELECTED'] = $ind;
					break;
				}

			$arMatrix = array();
			$intSelected = -1;
			$arItem['MIN_PRICE'] = false;
			foreach ($arItem['OFFERS'] as $keyOffer => $arOffer)
			{
				if (empty($arItem['MIN_PRICE']) && $arOffer['CAN_BUY'])
				{
					$intSelected = $keyOffer;
					$arItem['MIN_PRICE'] = (isset($arOffer['RATIO_PRICE']) ? $arOffer['RATIO_PRICE'] : $arOffer['MIN_PRICE']);
				}
				$arSKUProps = false;
				if (!empty($arOffer['DISPLAY_PROPERTIES']))
				{
					$boolSKUDisplayProperties = true;
					$arSKUProps = array();
					foreach ($arOffer['DISPLAY_PROPERTIES'] as &$arOneProp)
					{
						if ('F' == $arOneProp['PROPERTY_TYPE'])
							continue;
						$arSKUProps[] = array(
							'NAME' => $arOneProp['NAME'],
							'VALUE' => $arOneProp['DISPLAY_VALUE']
						);
					}
					unset($arOneProp);
				}

				$arOneRow = array(
					'ID' => $arOffer['ID'],
					'NAME' => $arOffer['~NAME'],
					'TREE' => $arOffer['TREE'],
					'DISPLAY_PROPERTIES' => $arSKUProps,
					'PRICE' => (isset($arOffer['RATIO_PRICE']) ? $arOffer['RATIO_PRICE'] : $arOffer['MIN_PRICE']),
					'SECOND_PICT' => $arOffer['SECOND_PICT'],
					'OWNER_PICT' => $arOffer['OWNER_PICT'],
					'PREVIEW_PICTURE' => $arOffer['PREVIEW_PICTURE'],
					'PREVIEW_PICTURE_SECOND' => $arOffer['PREVIEW_PICTURE_SECOND'],
					'CHECK_QUANTITY' => $arOffer['CHECK_QUANTITY'],
					'MAX_QUANTITY' => $arOffer['CATALOG_QUANTITY'],
					'STEP_QUANTITY' => $arOffer['CATALOG_MEASURE_RATIO'],
					'QUANTITY_FLOAT' => is_double($arOffer['CATALOG_MEASURE_RATIO']),
					'MEASURE' => $arOffer['~CATALOG_MEASURE_NAME'],
					'CAN_BUY' => $arOffer['CAN_BUY'],
					'BUY_URL' => $arOffer['~BUY_URL'],
					'ADD_URL' => $arOffer['~ADD_URL'],
				);
				$arMatrix[$keyOffer] = $arOneRow;
			}

			if (-1 == $intSelected)
				$intSelected = 0;
			if (!$arMatrix[$intSelected]['OWNER_PICT'] && !empty($arItem['OFFERS']))
			{
				$arItem['PREVIEW_PICTURE'] = $arMatrix[$intSelected]['PREVIEW_PICTURE'];
				$arItem['PREVIEW_PICTURE_SECOND'] = $arMatrix[$intSelected]['PREVIEW_PICTURE_SECOND'];
			}
			$arItem['JS_OFFERS'] = $arMatrix;
			$arItem['OFFERS_SELECTED'] = $intSelected;
			$arItem['OFFERS_PROPS_DISPLAY'] = $boolSKUDisplayProperties;
		}

		global $USER;
		if($USER->IsAdmin())
		{
		echo "<pre>";
		var_dump($arItem['DISPLAY_PROPERTIES']);
		echo "</pre>";
		}


		if (!empty($arItem['DISPLAY_PROPERTIES']))
		{
			foreach ($arItem['DISPLAY_PROPERTIES'] as $propKey => $arDispProp)
			{
				if ('F' == $arDispProp['PROPERTY_TYPE'])
					unset($arItem['DISPLAY_PROPERTIES'][$propKey]);
			}
		}
		$arItem['LAST_ELEMENT'] = 'N';
		$arNewItemsList[$key] = $arItem;
	}

	$arNewItemsList[$key]['LAST_ELEMENT'] = 'Y';
	$arResult['ITEMS'] = $arNewItemsList;
	$arResult['SKU_PROPS'] = $skuPropList;
	$arResult['DEFAULT_PICTURE'] = $arEmptyPreview;
}
?>