<?
namespace Zverushki\Seofilter\Filter;

use Bitrix\Main,
	Bitrix\Main\Loader,
    Zverushki\Seofilter\configuration;

/**
 *
 */
class variable
{
	public $IBLOCK_ID = false;
	public $facet = null;
	protected $isFacet = false;
	public $arResult = false;
	protected $arOption = [];
	public $SECTION_ID = 0;
	public $SKU_IBLOCK_ID = 0;
	public $SKU_PROPERTY_ID = 0;
	public $SETTING_ID = 0;
	public $SAFE_FILTER_NAME = "arrPager";
	protected static $catalogIncluded = null;
	protected static $iblockIncluded = null;
	protected static $variableForSection = [];

	const SEOFILTER_CUSTOM_PARSE_CONFIG = 'onCustomParseUrlConfig';


	function __construct(){
		$this->arOption['avail'] = configuration::getOption('avail_active', '-');
		$this->arOption['price'] = configuration::getOption('price_active', '-');
		$this->arOption['not_active'] = configuration::getOption('not_active', '-');
		if(empty($this->arOption['avail']))
			$this->arOption['avail'] = 'N';
		if(empty($this->arOption['not_active']))
			$this->arOption['not_active'] = 'N';

		$this->arParams["PRICE_CODE"] = $this->arOption['price'];
	}

	public function setIblockId($iblockId){
		$this->IBLOCK_ID = $iblockId;
	}
	public function setSectionId($sectionId = 0){
		$this->SECTION_ID = $sectionId;
	}
	public function setSettingId($settingId = 0){
		$this->SETTING_ID = $settingId;
	}
	private function initFacet($iblockId = false){
		if(!empty($iblockId))
			$iblockId = $this->IBLOCK_ID;

		if(!empty($iblockId) && $this->facet === null){
			$this->facet = new \Bitrix\Iblock\PropertyIndex\Facet($iblockId);
			$this->isFacet = $this->isValid($iblockId);
			return true;
		}

		return false;
	}
	private function isValid($iblockId = false){
		if(!empty($iblockId))
			return $this->facet->isValid();
		return false;
	}
	public function getVariable(){
		if($this->SETTING_ID && self::$variableForSection[$this->SETTING_ID]) {
			$this->arResult = self::$variableForSection[ $this->SETTING_ID ]['result'];
			if(self::$variableForSection[ $this->SETTING_ID ]['facet'])
				$this->facet = self::$variableForSection[ $this->SETTING_ID ]['facet'];
			return $this->arResult;
		}
		if (self::$catalogIncluded === null)
			self::$catalogIncluded = Loader::includeModule('catalog');
		if (self::$catalogIncluded)
		{
			$arCatalog = \CCatalogSKU::GetInfoByProductIBlock($this->IBLOCK_ID);
			if (!empty($arCatalog))
			{
				$this->SKU_IBLOCK_ID = $arCatalog["IBLOCK_ID"];
				$this->SKU_PROPERTY_ID = $arCatalog["SKU_PROPERTY_ID"];
			}
		}
		$this->arResult["IBLOCK_ID"] = $this->IBLOCK_ID;
		$this->arResult["ITEMS"] = $this->getResultItems();
		$this->arResult["PRICES"] = \CIBlockPriceTools::GetCatalogPrices($this->IBLOCK_ID, $this->arParams["PRICE_CODE"]);

		$this->initFacet($this->IBLOCK_ID);
		if($this->isFacet)
			$this->getVariableFacet();
		else
			$this->getVariableNotFacet();

		if($this->arResult["ITEMS"])
			foreach($this->arResult["ITEMS"] as $PID => $arItem){
				if($this->arResult["ITEMS"][$PID]["VALUES"])
					uasort($this->arResult["ITEMS"][$PID]["VALUES"], [ $this, "_sort" ]);
			}

		if($this->SETTING_ID)
			self::$variableForSection[ $this->SETTING_ID ] = ['result' => $this->arResult, 'facet' => $this->isFacet ? $this->facet : null];

		return $this->arResult;
	}
	public function _sort($v1, $v2)
	{
		if ($v1["SORT"] < $v2["SORT"])
			return -1;
		elseif ($v1["SORT"] > $v2["SORT"])
			return 1;
		else
			return strcmp($v1["UPPER"], $v2["UPPER"]);
	}
	private function getVariableFacet(){
		$this->facet->setPrices($this->arResult["PRICES"]);
		$this->facet->setSectionId($this->SECTION_ID);

		$this->arResult["FACET_FILTER"] = array(
			"CHECK_PERMISSIONS" => "Y",
		);
		if($this->arOption['not_active'] != 'Y')
			$this->arResult["FACET_FILTER"]["ACTIVE_DATE"] = "Y";

		if($this->arOption['avail'] == "Y")
			$this->arResult["FACET_FILTER"]["CATALOG_AVAILABLE"] = "Y";

		$cntProperty = 0;
		$tmpProperty = array();
		$dictionaryID = array();
		$elementDictionary = array();
		$sectionDictionary = array();
		$directoryPredict = array();

		$res = $this->facet->query($this->arResult["FACET_FILTER"]);
		\CTimeZone::Disable();
		while ($rowData = $res->fetch())
		{
			$facetId = $rowData["FACET_ID"];
			if (\Bitrix\Iblock\PropertyIndex\Storage::isPropertyId($facetId))
			{
				$PID = \Bitrix\Iblock\PropertyIndex\Storage::facetIdToPropertyId($facetId);
				if (!array_key_exists($PID, $this->arResult["ITEMS"]))
					continue;
				++$cntProperty;

				$rowData['PID'] = $PID;
				$tmpProperty[] = $rowData;
				$item = $this->arResult["ITEMS"][$PID];
				$arUserType = \CIBlockProperty::GetUserType($item['USER_TYPE']);

				if ($item["PROPERTY_TYPE"] == "S")
				{
					$dictionaryID[] = $rowData["VALUE"];
				}

				if ($item["PROPERTY_TYPE"] == "E" && $item['USER_TYPE'] == '')
				{
					$elementDictionary[] = $rowData['VALUE'];
				}

				if ($item["PROPERTY_TYPE"] == "G" && $item['USER_TYPE'] == '')
				{
					$sectionDictionary[] = $rowData['VALUE'];
				}

				if ($item['USER_TYPE'] == 'directory' && isset($arUserType['GetExtendedValue']))
				{
					$tableName = $item['USER_TYPE_SETTINGS']['TABLE_NAME'];
					$directoryPredict[$tableName]['PROPERTY'] = array(
						'PID' => $item['ID'],
						'USER_TYPE_SETTINGS' => $item['USER_TYPE_SETTINGS'],
						'GetExtendedValue' => $arUserType['GetExtendedValue'],
					);
					$directoryPredict[$tableName]['VALUE'][] = $rowData["VALUE"];
				}
			}
			else
			{
				$priceId = \Bitrix\Iblock\PropertyIndex\Storage::facetIdToPriceId($facetId);
				if($this->arResult["PRICES"])
					foreach($this->arResult["PRICES"] as $NAME => $arPrice)
					{
						if ($arPrice["ID"] == $priceId && isset($this->arResult["ITEMS"][$NAME]))
						{
							$this->fillItemPrices($this->arResult["ITEMS"][$NAME], $rowData);

							if (isset($this->arResult["ITEMS"][$NAME]["~CURRENCIES"]))
							{
								$this->arResult["CURRENCIES"] += $this->arResult["ITEMS"][$NAME]["~CURRENCIES"];
							}

							if ($rowData["VALUE_FRAC_LEN"] > 0)
							{
								$this->arResult["ITEMS"][$PID]["DECIMALS"] = $rowData["VALUE_FRAC_LEN"];
							}
						}
					}
			}

			if ($cntProperty > 200)
			{
				$this->predictIBElementFetch($elementDictionary);
				$this->predictIBSectionFetch($sectionDictionary);
				$this->processProperties($this->arResult, $tmpProperty, $dictionaryID, $directoryPredict);
				$cntProperty = 0;
				$tmpProperty = array();
				$dictionaryID = array();
				$lookupDictionary = array();
				$directoryPredict = array();
				$elementDictionary = array();
				$sectionDictionary = array();
			}
		}

		$this->predictIBElementFetch($elementDictionary);
		$this->predictIBSectionFetch($sectionDictionary);
		$this->processProperties($this->arResult, $tmpProperty, $dictionaryID, $directoryPredict);

		\CTimeZone::Enable();
	}
	private function getVariableNotFacet(){
			$arElementFilter = array(
				"IBLOCK_ID" => $this->IBLOCK_ID,
				"SUBSECTION" => $this->SECTION_ID,
				"SECTION_SCOPE" => "IBLOCK",
				"CHECK_PERMISSIONS" => "Y",
			);
			if($this->arOption['not_active'] != 'Y'){
				$arElementFilter["ACTIVE"] = "Y";
				$arElementFilter["ACTIVE_DATE"] = "Y";
			}

			if($this->arOption['avail'] == "Y")
				$arElementFilter['CATALOG_AVAILABLE'] = 'Y';

			$arElements = array();

			if (!empty($this->arResult["PROPERTY_ID_LIST"]))
			{
				$rsElements = \CIBlockElement::GetPropertyValues($this->IBLOCK_ID, $arElementFilter, false, array('ID' => $this->arResult["PROPERTY_ID_LIST"]));
				while($arElement = $rsElements->Fetch())
					$arElements[$arElement["IBLOCK_ELEMENT_ID"]] = $arElement;
			}
			else
			{
				$rsElements = \CIBlockElement::GetList(array('ID' => 'ASC'), $arElementFilter, false, false, array('ID', 'IBLOCK_ID'));
				while($arElement = $rsElements->Fetch())
					$arElements[$arElement["ID"]] = array();
			}

			if (!empty($arElements) && $this->SKU_IBLOCK_ID && $this->arResult["SKU_PROPERTY_COUNT"] > 0)
			{
				$arSkuFilter = array(
					"IBLOCK_ID" => $this->SKU_IBLOCK_ID,
					"CHECK_PERMISSIONS" => "Y",
					"=PROPERTY_".$this->SKU_PROPERTY_ID => array_keys($arElements),
				);
				if($this->arOption['not_active'] != 'Y'){
					$arSkuFilter["ACTIVE"] = "Y";
					$arSkuFilter["ACTIVE_DATE"] = "Y";
				}
				if($this->arOption['avail'] == "Y")
					$arSkuFilter['CATALOG_AVAILABLE'] = 'Y';

				$rsElements = \CIBlockElement::GetPropertyValues($this->SKU_IBLOCK_ID, $arSkuFilter, false, array('ID' => $this->arResult["SKU_PROPERTY_ID_LIST"]));
				while($arSku = $rsElements->Fetch())
				{
					if($this->arResult["ITEMS"])
						foreach($this->arResult["ITEMS"] as $PID => $arItem)
						{
							if (isset($arSku[$PID]) && $arSku[$this->SKU_PROPERTY_ID] > 0)
							{
								if (is_array($arSku[$PID]))
								{
									foreach($arSku[$PID] as $value)
										$arElements[$arSku[$this->SKU_PROPERTY_ID]][$PID][] = $value;
								}
								else
								{
									$arElements[$arSku[$this->SKU_PROPERTY_ID]][$PID][] = $arSku[$PID];
								}
							}
						}
				}
			}
// mp($arElements);
			\CTimeZone::Disable();
			$uniqTest = array();
			foreach($arElements as $arElement)
			{
				$propertyValues = $propertyEmptyValuesCombination;
				$uniqStr = '';
				if($this->arResult["ITEMS"])
					foreach($this->arResult["ITEMS"] as $PID => $arItem)
					{
						if (is_array($arElement[$PID]))
						{
							foreach($arElement[$PID] as $value)
							{
								$key = $this->fillItemValues($this->arResult["ITEMS"][$PID], $value);
								$propertyValues[$PID][$key] = $this->arResult["ITEMS"][$PID]["VALUES"][$key]["VALUE"];
								$uniqStr .= '|'.$key.'|'.$propertyValues[$PID][$key];
							}
						}
						elseif ($arElement[$PID] !== false)
						{
							$key = $this->fillItemValues($this->arResult["ITEMS"][$PID], $arElement[$PID]);
							$propertyValues[$PID][$key] = $this->arResult["ITEMS"][$PID]["VALUES"][$key]["VALUE"];
							$uniqStr .= '|'.$key.'|'.$propertyValues[$PID][$key];
						}
					}

				$uniqCheck = md5($uniqStr);
				if (isset($uniqTest[$uniqCheck]))
					continue;
				$uniqTest[$uniqCheck] = true;

				$this->ArrayMultiply($this->arResult["COMBO"], $propertyValues);
			}
			\CTimeZone::Enable();

			$arSelect = array("ID", "IBLOCK_ID");
			if($this->arResult["PRICES"])
				foreach($this->arResult["PRICES"] as &$value)
				{
					if (!$value['CAN_VIEW'] && !$value['CAN_BUY'])
						continue;
					$arSelect[] = $value["SELECT"];
					$arFilter["CATALOG_SHOP_QUANTITY_".$value["ID"]] = 1;
				}
			unset($value);

			$rsElements = \CIBlockElement::GetList(array(), $arElementFilter, false, false, $arSelect);
			while($arElement = $rsElements->Fetch())
			{
				if($this->arResult["PRICES"])
					foreach($this->arResult["PRICES"] as $NAME => $arPrice)
						if(isset($this->arResult["ITEMS"][$NAME]))
							$this->fillItemPrices($this->arResult["ITEMS"][$NAME], $arElement);
			}

			if (isset($arSkuFilter))
			{
				$rsElements = \CIBlockElement::GetList(array(), $arSkuFilter, false, false, $arSelect);
				while($arSku = $rsElements->Fetch())
				{
					if($this->arResult["PRICES"])
						foreach($this->arResult["PRICES"] as $NAME => $arPrice)
							if(isset($this->arResult["ITEMS"][$NAME]))
								$this->fillItemPrices($this->arResult["ITEMS"][$NAME], $arSku);
				}
			}
	}
	public function fillItemPrices(&$resultItem, $arElement)
	{
		if (isset($arElement["MIN_VALUE_NUM"]) && isset($arElement["MAX_VALUE_NUM"]))
		{
			$currency = (string)$arElement["VALUE"];
			$existCurrency = $currency !== '';
			if ($existCurrency)
				$currency = $this->facet->lookupDictionaryValue($currency);

			$priceValue = $this->convertPrice($arElement["MIN_VALUE_NUM"], $currency);
			if (
				!isset($resultItem["VALUES"]["MIN"]["VALUE"])
				|| $resultItem["VALUES"]["MIN"]["VALUE"] > $priceValue
			)
			{
				$resultItem["VALUES"]["MIN"]["VALUE"] = $priceValue;
				if ($existCurrency)
				{
					if ($this->convertCurrencyId)
						$resultItem["VALUES"]["MIN"]["CURRENCY"] = $this->convertCurrencyId;
					else
						$resultItem["VALUES"]["MIN"]["CURRENCY"] = $currency;
				}
			}

			$priceValue = $this->convertPrice($arElement["MAX_VALUE_NUM"], $currency);
			if (
				!isset($resultItem["VALUES"]["MAX"]["VALUE"])
				|| $resultItem["VALUES"]["MAX"]["VALUE"] < $priceValue
			)
			{
				$resultItem["VALUES"]["MAX"]["VALUE"] = $priceValue;
				if ($existCurrency)
				{
					if ($this->convertCurrencyId)
						$resultItem["VALUES"]["MAX"]["CURRENCY"] = $this->convertCurrencyId;
					else
						$resultItem["VALUES"]["MAX"]["CURRENCY"] = $currency;
				}
			}
		}
		else
		{
			$newFormat = array_key_exists("PRICE_".$resultItem["ID"], $arElement);
			if ($newFormat)
			{
				$currency = (string)$arElement["CURRENCY_".$resultItem["ID"]];
				$price = (string)$arElement["PRICE_".$resultItem["ID"]];
			}
			else
			{
				$currency = (string)$arElement["CATALOG_CURRENCY_".$resultItem["ID"]];
				$price = (string)$arElement["CATALOG_PRICE_".$resultItem["ID"]];
			}
			$existCurrency = $currency !== '';
			if($price !== '')
			{
				if ($this->convertCurrencyId && $existCurrency)
				{
					$convertPrice = CCurrencyRates::ConvertCurrency($price, $currency, $this->convertCurrencyId);
					$this->currencyTagList[$currency] = $currency;
				}
				else
				{
					$convertPrice = (float)$price;
				}

				if(
					!isset($resultItem["VALUES"]["MIN"])
					|| !array_key_exists("VALUE", $resultItem["VALUES"]["MIN"])
					|| (float)$resultItem["VALUES"]["MIN"]["VALUE"] > $convertPrice
				)
				{
					$resultItem["VALUES"]["MIN"]["VALUE"] = $convertPrice;
					if ($existCurrency)
					{
						if ($this->convertCurrencyId)
							$resultItem["VALUES"]["MIN"]["CURRENCY"] = $this->convertCurrencyId;
						else
							$resultItem["VALUES"]["MIN"]["CURRENCY"] = $currency;
					}
				}

				if(
					!isset($resultItem["VALUES"]["MAX"])
					|| !array_key_exists("VALUE", $resultItem["VALUES"]["MAX"])
					|| (float)$resultItem["VALUES"]["MAX"]["VALUE"] < $convertPrice
				)
				{
					$resultItem["VALUES"]["MAX"]["VALUE"] = $convertPrice;
					if ($existCurrency)
					{
						if ($this->convertCurrencyId)
							$resultItem["VALUES"]["MAX"]["CURRENCY"] = $this->convertCurrencyId;
						else
							$resultItem["VALUES"]["MAX"]["CURRENCY"] = $currency;
					}
				}
			}
		}

		if ($existCurrency)
		{
			if ($this->convertCurrencyId)
			{
				$resultItem["CURRENCIES"][$this->convertCurrencyId] = (
				isset($this->currencyCache[$this->convertCurrencyId])
					? $this->currencyCache[$this->convertCurrencyId]
					: $this->getCurrencyFullName($this->convertCurrencyId)
				);
				$resultItem["~CURRENCIES"][$currency] = (
				isset($this->currencyCache[$currency])
					? $this->currencyCache[$currency]
					: $this->getCurrencyFullName($currency)
				);
			}
			else
			{
				$resultItem["CURRENCIES"][$currency] = (
				isset($this->currencyCache[$currency])
					? $this->currencyCache[$currency]
					: $this->getCurrencyFullName($currency)
				);
			}
		}
	}

	public function convertPrice($price, $currency)
	{
		if ($this->convertCurrencyId && $currency)
		{
			$priceValue = CCurrencyRates::ConvertCurrency($price, $currency, $this->convertCurrencyId);
			$this->currencyTagList[$currency] = $currency;
		}
		else
		{
			$priceValue = $price;
		}
		return $priceValue;
	}

	public function getCurrencyFullName($currencyId)
	{
		if (!isset($this->currencyCache[$currencyId]))
		{
			$currencyInfo = \CCurrencyLang::GetById($currencyId, LANGUAGE_ID);
			if ($currencyInfo["FULL_NAME"] != "")
				$this->currencyCache[$currencyId] = $currencyInfo["FULL_NAME"];
			else
				$this->currencyCache[$currencyId] = $currencyId;
		}
		return $this->currencyCache[$currencyId];
	}

	public function getResultItems()
	{
		$items = $this->getIBlockItems($this->IBLOCK_ID);
		$this->arResult["PROPERTY_COUNT"] = count($items);
		$this->arResult["PROPERTY_ID_LIST"] = array_keys($items);

		if($this->SKU_IBLOCK_ID)
		{
			$this->arResult["SKU_PROPERTY_ID_LIST"] = array($this->SKU_PROPERTY_ID);
			foreach($this->getIBlockItems($this->SKU_IBLOCK_ID) as $PID => $arItem)
			{
				$items[$PID] = $arItem;
				$this->arResult["SKU_PROPERTY_COUNT"]++;
				$this->arResult["SKU_PROPERTY_ID_LIST"][] = $PID;
			}
		}

		if (!empty($this->arParams["PRICE_CODE"]))
		{
			foreach($this->getPriceItems() as $PID => $arItem)
			{
				$arItem["ENCODED_ID"] = md5($arItem["ID"]);
				$items[$PID] = $arItem;
			}
		}

		return $items;
	}

	public function getPriceItems()
	{
		$items = array();
		if (!empty($this->arParams["PRICE_CODE"]))
		{
			if (self::$catalogIncluded === null)
				self::$catalogIncluded = Loader::includeModule('catalog');
			if (self::$catalogIncluded)
			{
				$rsPrice = \CCatalogGroup::GetList(
					array('SORT' => 'ASC', 'ID' => 'ASC'),
					array('=NAME' => $this->arParams["PRICE_CODE"]),
					false,
					false,
					array('ID', 'NAME', 'NAME_LANG', 'CAN_ACCESS', 'CAN_BUY')
				);
				while($arPrice = $rsPrice->Fetch())
				{
					if($arPrice["CAN_ACCESS"] == "Y" || $arPrice["CAN_BUY"] == "Y")
					{
						$arPrice["NAME_LANG"] = (string)$arPrice["NAME_LANG"];
						if ($arPrice["NAME_LANG"] === '')
							$arPrice["NAME_LANG"] = $arPrice["NAME"];
						$minID = $this->SAFE_FILTER_NAME.'_P'.$arPrice['ID'].'_MIN';
						$maxID = $this->SAFE_FILTER_NAME.'_P'.$arPrice['ID'].'_MAX';
						$error = "";
						$utf_id = \Bitrix\Main\Text\Encoding::convertEncoding(toLower($arPrice["NAME"]), LANG_CHARSET, "utf-8", $error);
						$items[$arPrice["NAME"]] = array(
							"ID" => $arPrice["ID"],
							"CODE" => $arPrice["NAME"],
							"URL_ID" => rawurlencode(str_replace("/", "-", $utf_id)),
							"~NAME" => $arPrice["NAME_LANG"],
							"NAME" => htmlspecialcharsbx($arPrice["NAME_LANG"]),
							"PRICE" => true,
							"VALUES" => array(
								"MIN" => array(
									"CONTROL_ID" => $minID,
									"CONTROL_NAME" => $minID,
								),
								"MAX" => array(
									"CONTROL_ID" => $maxID,
									"CONTROL_NAME" => $maxID,
								),
							),
						);
					}
				}
			}
		}
		return $items;
	}

	public function getIBlockItems($iblockId)
	{
		$items = array();

		foreach(\CIBlockSectionPropertyLink::GetArray($iblockId, $this->SECTION_ID) as $PID => $arLink)
		{
			if ($arLink["SMART_FILTER"] !== "Y")
				continue;

			if ($arLink["ACTIVE"] === "N")
				continue;

			if ($arLink['FILTER_HINT'] <> '')
			{
				$arLink['FILTER_HINT'] = \CTextParser::closeTags($arLink['FILTER_HINT']);
			}

			$rsProperty = \CIBlockProperty::GetByID($PID);
			$arProperty = $rsProperty->Fetch();
			if($arProperty)
			{
				$items[$arProperty["ID"]] = array(
					"ID" => $arProperty["ID"],
					"IBLOCK_ID" => $arProperty["IBLOCK_ID"],
					"CODE" => $arProperty["CODE"],
					"~NAME" => $arProperty["NAME"],
					"NAME" => htmlspecialcharsEx($arProperty["NAME"]),
					"PROPERTY_TYPE" => $arProperty["PROPERTY_TYPE"],
					"USER_TYPE" => $arProperty["USER_TYPE"],
					"USER_TYPE_SETTINGS" => $arProperty["USER_TYPE_SETTINGS"],
					"DISPLAY_TYPE" => $arLink["DISPLAY_TYPE"],
					"DISPLAY_EXPANDED" => $arLink["DISPLAY_EXPANDED"],
					"FILTER_HINT" => $arLink["FILTER_HINT"],
					"VALUES" => array(),
				);

				if (
					$arProperty["PROPERTY_TYPE"] == "N"
					|| $arLink["DISPLAY_TYPE"] == "U"
				)
				{
					$minID = $this->SAFE_FILTER_NAME.'_'.$arProperty['ID'].'_MIN';
					$maxID = $this->SAFE_FILTER_NAME.'_'.$arProperty['ID'].'_MAX';
					$items[$arProperty["ID"]]["VALUES"] = array(
						"MIN" => array(
							"CONTROL_ID" => $minID,
							"CONTROL_NAME" => $minID,
						),
						"MAX" => array(
							"CONTROL_ID" => $maxID,
							"CONTROL_NAME" => $maxID,
						),
					);
				}
			}
		}
		return $items;
	}
	public function predictIBElementFetch($id = array())
	{
		if (!is_array($id) || empty($id))
		{
			return;
		}

		$linkFilter = array (
			"ID" => $id,
			"ACTIVE" => "Y",
			"ACTIVE_DATE" => "Y",
			"CHECK_PERMISSIONS" => "Y",
		);

		$link = \CIBlockElement::GetList(array(), $linkFilter, false, false, array("ID","IBLOCK_ID","NAME","SORT","CODE"));
		while ($el = $link->Fetch())
		{
			$this->cache['E'][$el['ID']] = $el;
		}
		unset($el);
		unset($link);
	}
	public function predictIBSectionFetch($id = array())
	{
		if (!is_array($id) || empty($id))
		{
			return;
		}

		$arLinkFilter = array (
			"ID" => $id,
			"GLOBAL_ACTIVE" => "Y",
			"CHECK_PERMISSIONS" => "Y",
		);

		$link = \CIBlockSection::GetList(array(), $arLinkFilter, false, array("ID","IBLOCK_ID","NAME","LEFT_MARGIN","DEPTH_LEVEL","CODE"));
		while ($sec = $link->Fetch())
		{
			$this->cache['G'][$sec['ID']] = $sec;
			$this->cache['G'][$sec['ID']]['DEPTH_NAME'] = str_repeat(".", $sec["DEPTH_LEVEL"]).$sec["NAME"];
		}
		unset($sec);
		unset($link);
	}
	public function processProperties(array &$resultItem, array $elements, array $dictionaryID, array $directoryPredict = [])
	{
		$lookupDictionary = [];
		if (!empty($dictionaryID))
		{
			$lookupDictionary = $this->facet->getDictionary()->getStringByIds($dictionaryID);
		}

		if (!empty($directoryPredict))
		{
			foreach ($directoryPredict as $directory)
			{
				if (empty($directory['VALUE']) || !is_array($directory['VALUE']))
					continue;
				$values = [];
				foreach ($directory['VALUE'] as $item)
				{
					if (isset($lookupDictionary[$item]))
						$values[] = $lookupDictionary[$item];
				}
				if (!empty($values))
					$this->predictHlFetch($directory['PROPERTY'], $values);
				unset($values);
			}
			unset($directory);
		}

		foreach ($elements as $row)
		{
			$PID = $row['PID'];
			if ($resultItem["ITEMS"][$PID]["PROPERTY_TYPE"] == "N")
			{
				$this->fillItemValues($resultItem["ITEMS"][$PID], $row["MIN_VALUE_NUM"]);
				$this->fillItemValues($resultItem["ITEMS"][$PID], $row["MAX_VALUE_NUM"]);
				if ($row["VALUE_FRAC_LEN"] > 0)
					$resultItem["ITEMS"][$PID]["DECIMALS"] = $row["VALUE_FRAC_LEN"];
			}
			elseif ($resultItem["ITEMS"][$PID]["DISPLAY_TYPE"] == "U")
			{
				$this->fillItemValues($resultItem["ITEMS"][$PID], FormatDate("Y-m-d", $row["MIN_VALUE_NUM"]));
				$this->fillItemValues($resultItem["ITEMS"][$PID], FormatDate("Y-m-d", $row["MAX_VALUE_NUM"]));
			}
			elseif ($resultItem["ITEMS"][$PID]["PROPERTY_TYPE"] == "S")
			{
				$addedKey = $this->fillItemValues($resultItem["ITEMS"][$PID], $lookupDictionary[$row["VALUE"]], true);
				if (strlen($addedKey) > 0)
				{
					$resultItem["ITEMS"][$PID]["VALUES"][$addedKey]["FACET_VALUE"] = $row["VALUE"];
					$resultItem["ITEMS"][$PID]["VALUES"][$addedKey]["ELEMENT_COUNT"] = $row["ELEMENT_COUNT"];
				}
			}
			else
			{
				$addedKey = $this->fillItemValues($resultItem["ITEMS"][$PID], $row["VALUE"], true);
				if (strlen($addedKey) > 0)
				{
					$resultItem["ITEMS"][$PID]["VALUES"][$addedKey]["FACET_VALUE"] = $row["VALUE"];
					$resultItem["ITEMS"][$PID]["VALUES"][$addedKey]["ELEMENT_COUNT"] = $row["ELEMENT_COUNT"];
				}
			}
		}
	}
	public function fillItemValues(&$resultItem, $arProperty, $flag = null)
	{
		if(is_array($arProperty))
		{
			if(isset($arProperty["PRICE"]))
			{
				return null;
			}
			$key = $arProperty["VALUE"];
			$PROPERTY_TYPE = $arProperty["PROPERTY_TYPE"];
			$PROPERTY_USER_TYPE = $arProperty["USER_TYPE"];
			$PROPERTY_ID = $arProperty["ID"];
		}
		else
		{
			$key = $arProperty;
			$PROPERTY_TYPE = $resultItem["PROPERTY_TYPE"];
			$PROPERTY_USER_TYPE = $resultItem["USER_TYPE"];
			$PROPERTY_ID = $resultItem["ID"];
			$arProperty = $resultItem;
		}

		if($PROPERTY_TYPE == "F")
		{
			return null;
		}
		elseif($PROPERTY_TYPE == "N")
		{
			$convertKey = (float)$key;
			if (strlen($key) <= 0)
			{
				return null;
			}

			if (
				!isset($resultItem["VALUES"]["MIN"])
				|| !array_key_exists("VALUE", $resultItem["VALUES"]["MIN"])
				|| doubleval($resultItem["VALUES"]["MIN"]["VALUE"]) > $convertKey
			)
				$resultItem["VALUES"]["MIN"]["VALUE"] = preg_replace("/\\.0+\$/", "", $key);

			if (
				!isset($resultItem["VALUES"]["MAX"])
				|| !array_key_exists("VALUE", $resultItem["VALUES"]["MAX"])
				|| doubleval($resultItem["VALUES"]["MAX"]["VALUE"]) < $convertKey
			)
				$resultItem["VALUES"]["MAX"]["VALUE"] = preg_replace("/\\.0+\$/", "", $key);

			return null;
		}
		elseif($arProperty["DISPLAY_TYPE"] == "U")
		{
			$date = substr($key, 0, 10);
			if (!$date)
			{
				return null;
			}
			$timestamp = MakeTimeStamp($date, "YYYY-MM-DD");
			if (!$timestamp)
			{
				return null;
			}

			if (
				!isset($resultItem["VALUES"]["MIN"])
				|| !array_key_exists("VALUE", $resultItem["VALUES"]["MIN"])
				|| $resultItem["VALUES"]["MIN"]["VALUE"] > $timestamp
			)
				$resultItem["VALUES"]["MIN"]["VALUE"] = $timestamp;

			if (
				!isset($resultItem["VALUES"]["MAX"])
				|| !array_key_exists("VALUE", $resultItem["VALUES"]["MAX"])
				|| $resultItem["VALUES"]["MAX"]["VALUE"] < $timestamp
			)
				$resultItem["VALUES"]["MAX"]["VALUE"] = $timestamp;

			return null;
		}
		elseif($PROPERTY_TYPE == "E" && $key <= 0)
		{
			return null;
		}
		elseif($PROPERTY_TYPE == "G" && $key <= 0)
		{
			return null;
		}
		elseif(strlen($key) <= 0)
		{
			return null;
		}

		$arUserType = array();
		if($PROPERTY_USER_TYPE != "")
		{
			$arUserType = \CIBlockProperty::GetUserType($PROPERTY_USER_TYPE);
			if(isset($arUserType["GetExtendedValue"]))
				$PROPERTY_TYPE = "Ux";
			elseif(isset($arUserType["GetPublicViewHTML"]))
				$PROPERTY_TYPE = "U";
		}

		if ($PROPERTY_USER_TYPE === "DateTime")
		{
			$key = call_user_func_array(
				$arUserType["GetPublicViewHTML"],
				array(
					$arProperty,
					array("VALUE" => $key),
					array("MODE" => "SIMPLE_TEXT", "DATETIME_FORMAT" => "SHORT"),
				)
			);
			$PROPERTY_TYPE = "S";
		}

		$htmlKey = htmlspecialcharsbx($key);
		if (isset($resultItem["VALUES"][$htmlKey]))
		{
			return $htmlKey;
		}

		$file_id = null;
		$url_id = null;

		switch($PROPERTY_TYPE)
		{
		case "L":
			$enum = \CIBlockPropertyEnum::GetByID($key);
			if ($enum)
			{
				$value = $enum["VALUE"];
				$sort  = $enum["SORT"];
				$url_id = toLower($enum["XML_ID"]);
			}
			else
			{
				return null;
			}
			break;
		case "E":
			if(!isset($this->cache[$PROPERTY_TYPE][$key]))
			{
				$this->predictIBElementFetch(array($key));
			}

			if (!$this->cache[$PROPERTY_TYPE][$key])
				return null;

			$value = $this->cache[$PROPERTY_TYPE][$key]["NAME"];
			$sort = $this->cache[$PROPERTY_TYPE][$key]["SORT"];
			if ($this->cache[$PROPERTY_TYPE][$key]["CODE"])
				$url_id = toLower($this->cache[$PROPERTY_TYPE][$key]["CODE"]);
			else
				$url_id = toLower($value);
			break;
		case "G":
			if(!isset($this->cache[$PROPERTY_TYPE][$key]))
			{
				$this->predictIBSectionFetch(array($key));
			}

			if (!$this->cache[$PROPERTY_TYPE][$key])
				return null;

			$value = $this->cache[$PROPERTY_TYPE][$key]['DEPTH_NAME'];
			$sort = $this->cache[$PROPERTY_TYPE][$key]["LEFT_MARGIN"];
			if ($this->cache[$PROPERTY_TYPE][$key]["CODE"])
				$url_id = toLower($this->cache[$PROPERTY_TYPE][$key]["CODE"]);
			else
				$url_id = toLower($value);
			break;
		case "U":
			if(!isset($this->cache[$PROPERTY_ID]))
				$this->cache[$PROPERTY_ID] = array();

			if(!isset($this->cache[$PROPERTY_ID][$key]))
			{
				$this->cache[$PROPERTY_ID][$key] = call_user_func_array(
					$arUserType["GetPublicViewHTML"],
					array(
						$arProperty,
						array("VALUE" => $key),
						array("MODE" => "SIMPLE_TEXT"),
					)
				);
			}

			$value = $this->cache[$PROPERTY_ID][$key];
			$sort = 0;
			$url_id = toLower($value);
			break;
		case "Ux":
			if(!isset($this->cache[$PROPERTY_ID]))
				$this->cache[$PROPERTY_ID] = array();

			if(!isset($this->cache[$PROPERTY_ID][$key]))
			{
				$this->cache[$PROPERTY_ID][$key] = call_user_func_array(
					$arUserType["GetExtendedValue"],
					array(
						$arProperty,
						array("VALUE" => $key),
					)
				);
			}

			if ($this->cache[$PROPERTY_ID][$key])
			{
				$value = $this->cache[$PROPERTY_ID][$key]['VALUE'];
				$file_id = $this->cache[$PROPERTY_ID][$key]['FILE_ID'];
				$sort = (isset($this->cache[$PROPERTY_ID][$key]['SORT']) ? $this->cache[$PROPERTY_ID][$key]['SORT'] : 0);
				$url_id = toLower($this->cache[$PROPERTY_ID][$key]['UF_XML_ID']);
			}
			else
			{
				return null;
			}
			break;
		default:
			$value = $key;
			$sort = 0;
			$url_id = toLower($value);
			break;
		}

		$keyCrc = abs(crc32($htmlKey));
		$safeValue = htmlspecialcharsex($value);
		$sort = (int)$sort;

		$filterPropertyID = $this->SAFE_FILTER_NAME.'_'.$PROPERTY_ID;
		$filterPropertyIDKey = $filterPropertyID.'_'.$keyCrc;
		$resultItem["VALUES"][$htmlKey] = array(
			"CONTROL_ID" => $filterPropertyIDKey,
			"CONTROL_NAME" => $filterPropertyIDKey,
			"CONTROL_NAME_ALT" => $filterPropertyID,
			"HTML_VALUE_ALT" => $keyCrc,
			"HTML_VALUE" => "Y",
			"VALUE" => $safeValue,
			"SORT" => $sort,
			"UPPER" => ToUpper($safeValue),
			"FLAG" => $flag,
		);

		if ($file_id)
		{
			$resultItem["VALUES"][$htmlKey]['FILE'] = \CFile::GetFileArray($file_id);
		}

		if (strlen($url_id))
		{
			$error = "";
			$utf_id = \Bitrix\Main\Text\Encoding::convertEncoding($url_id, LANG_CHARSET, "utf-8", $error);
			$resultItem["VALUES"][$htmlKey]['URL_ID'] = rawurlencode(str_replace("/", "-", $utf_id));
		}

		return $htmlKey;
	}
	public function predictHlFetch($userType, $valueIDs)
	{
		$values = call_user_func_array(
			$userType['GetExtendedValue'],
			array(
				$userType,
				array("VALUE" => $valueIDs),
			)
		);

		foreach ($values as $key => $value)
		{
			$this->cache[$userType['PID']][$key] = $value;
		}
	}

	function ArrayMultiply(&$arResult, $arTuple, $arTemp = array())
	{
		if($arTuple)
		{
			reset($arTuple);
			$key = key($arTuple);
			$head = $arTuple[$key];
			unset($arTuple[$key]);
			$arTemp[$key] = false;
			if(is_array($head))
			{
				if(empty($head))
				{
					if(empty($arTuple))
						$arResult[] = $arTemp;
					else
						$this->ArrayMultiply($arResult, $arTuple, $arTemp);
				}
				else
				{
					foreach($head as $value)
					{
						$arTemp[$key] = $value;
						if(empty($arTuple))
							$arResult[] = $arTemp;
						else
							$this->ArrayMultiply($arResult, $arTuple, $arTemp);
					}
				}
			}
			else
			{
				$arTemp[$key] = $head;
				if(empty($arTuple))
					$arResult[] = $arTemp;
				else
					$this->ArrayMultiply($arResult, $arTuple, $arTemp);
			}
		}
		else
		{
			$arResult[] = $arTemp;
		}
	}

	public function makeSmartUrl($url, $params, $checkedControlId = false)
	{
		$apply = true;

		$this->getVariable();

		$smartParts = array();

		if ($apply)
		{
			foreach($this->arResult["ITEMS"] as $PID => $arItem)
			{
				$smartPart = array();
				//Prices
				if ($arItem["PRICE"])
				{
					if(!empty($params[$arItem["VALUES"]["MIN"]["CONTROL_NAME"]]))
						$arItem["VALUES"]["MIN"]["HTML_VALUE"] = $params[$arItem["VALUES"]["MIN"]["CONTROL_NAME"]];
					if(!empty($params[$arItem["VALUES"]["MAX"]["CONTROL_NAME"]]))
						$arItem["VALUES"]["MAX"]["HTML_VALUE"] = $params[$arItem["VALUES"]["MAX"]["CONTROL_NAME"]];

					if (strlen($arItem["VALUES"]["MIN"]["HTML_VALUE"]) > 0)
						$smartPart["from"] = $arItem["VALUES"]["MIN"]["HTML_VALUE"];
					if (strlen($arItem["VALUES"]["MAX"]["HTML_VALUE"]) > 0)
						$smartPart["to"] = $arItem["VALUES"]["MAX"]["HTML_VALUE"];
				}

				if ($smartPart)
				{
					array_unshift($smartPart, "price-".$arItem["URL_ID"]);

					$smartParts[] = $smartPart;
				}
			}

			foreach($this->arResult["ITEMS"] as $PID => $arItem)
			{
				$smartPart = array();
				if ($arItem["PRICE"])
					continue;

				//Numbers && calendar == ranges
				if (
					$arItem["PROPERTY_TYPE"] == "N"
					|| $arItem["DISPLAY_TYPE"] == "U"
				)
				{
					if(!empty($params[$arItem["VALUES"]["MIN"]["CONTROL_NAME"]]))
						$arItem["VALUES"]["MIN"]["HTML_VALUE"] = $params[$arItem["VALUES"]["MIN"]["CONTROL_NAME"]];
					if(!empty($params[$arItem["VALUES"]["MAX"]["CONTROL_NAME"]]))
						$arItem["VALUES"]["MAX"]["HTML_VALUE"] = $params[$arItem["VALUES"]["MAX"]["CONTROL_NAME"]];

					if (strlen($arItem["VALUES"]["MIN"]["HTML_VALUE"]) > 0)
						$smartPart["from"] = $arItem["VALUES"]["MIN"]["HTML_VALUE"];
					if (strlen($arItem["VALUES"]["MAX"]["HTML_VALUE"]) > 0)
						$smartPart["to"] = $arItem["VALUES"]["MAX"]["HTML_VALUE"];
				}
				else
				{
					if($arItem["VALUES"])
						foreach($arItem["VALUES"] as $key => $ar)
						{
							if(!empty($params[$ar["CONTROL_NAME"]])){
								$ar["CHECKED"] = "Y";
								$cnt++;
							}
							if (
								(
									$ar["CHECKED"]
									|| $ar["CONTROL_ID"] === $checkedControlId
								)
								&& strlen($ar["URL_ID"])
							)
							{
								$smartPart[] = $ar["URL_ID"];
							}
						}
				}

				if ($smartPart)
				{
					if ($arItem["CODE"])
						array_unshift($smartPart, toLower($arItem["CODE"]));
					else
						array_unshift($smartPart, $arItem["ID"]);

					$smartParts[] = $smartPart;
				}
			}
		}

		if (!$smartParts)
			$smartParts[] = array("clear");

		return str_replace("#SMART_FILTER_PATH#", implode("/", $this->encodeSmartParts($smartParts)), $url);
	}

	public function encodeSmartParts($smartParts)
	{
		foreach ($smartParts as &$smartPart)
		{
			$urlPart = "";
			foreach ($smartPart as $i => $smartElement)
			{
				if (!$urlPart)
					$urlPart .= $smartElement;
				elseif ($i == 'from' || $i == 'to')
					$urlPart .= '-'.$i.'-'.$smartElement;
				elseif ($i == 1)
					$urlPart .= '-is-'.$smartElement;
				else
					$urlPart .= '-or-'.$smartElement;
			}
			$smartPart = $urlPart;
		}
		unset($smartPart);
		return $smartParts;
	}

	public function parseUrl($url)
	{
		if (\Bitrix\Main\Loader::includeModule('catalog')){
			$rr = [];
			$filtervar = configuration::getOption('filtervar', SITE_ID);
			$cpuUrl = configuration::getOption('cpu_url', SITE_ID);
			$selfRULE = str_replace('//', '/', SITE_DIR . configuration::getOption('cpu_catalog', SITE_ID));

			$Db = \CCatalog::getList([], $arIblockFilter);
			while(($a = $Db->fetch()) !== false){
				if($a['PRODUCT_IBLOCK_ID'] == 0)
					$catalogIb[$a['IBLOCK_ID']] = $a['NAME'];
			}
			$eventManager = Main\EventManager::getInstance();
			if ($eventsList = $eventManager->findEventHandlers('zverushki.seofilter', self::SEOFILTER_CUSTOM_PARSE_CONFIG))
			{
				$arr = [
					'filtervar' => $filtervar,
					'cpuUrl' => $cpuUrl,
					'selfRULE' => $selfRULE,
					'catalogIb' => $catalogIb,
				];
				$event = new Main\Event('zverushki.seofilter', self::SEOFILTER_CUSTOM_PARSE_CONFIG, $arr);
				$event->send();

				if ($event->getResults())
				{
					/** @var Main\EventResult $eventResult */
					foreach ($event->getResults() as $eventResult)
					{
						if ($eventResult->getType() == Main\EventResult::SUCCESS){
							$arr = $eventResult->getParameters();
							$filtervar = $arr['filtervar'];
							$cpuUrl = $arr['cpuUrl'];
							$selfRULE = $arr['selfRULE'];
							$catalogIb = $arr['catalogIb'];
						}
					}
				}
			}

				$arDefaultUrlTemplates404 = [
					"smart_filter" => $cpuUrl
				];
				$arVariables = $this->getCatalogVariabal($selfRULE, $arDefaultUrlTemplates404);

				if(!$arVariables['page'] && !$arVariables['variable']){
					$iblockList = \CIBlock::GetList(["SORT"=>"ASC"], ['IBLOCK_ID' => array_keys($catalogIb), 'ACTIVE' => 'Y', 'SITE_ID' => SITE_ID]);
					while(!$arVariables['page'] && !$arVariables['variable'] && $iblock = $iblockList->fetch()){
						if(!empty($iblock['SECTION_PAGE_URL'])){
							$template = str_replace('#SITE_DIR#', SITE_DIR, $iblock['SECTION_PAGE_URL']);
							if($selfRULE != '/')
								$template = str_replace($selfRULE, '', $template);
							$template = str_replace('//', '/', $template);

							$pos = strpos($template, '/');
							if($pos  !== false && $pos  == 0){
								$template = substr($template, 1);
							}
							if($template){
								$arDefaultUrlTemplates404['section'] = $template;
								$arVariables = $this->getCatalogVariabal($selfRULE, $arDefaultUrlTemplates404);
							}
						}
					}
				}

				$path = $arVariables['variable']['SECTION_CODE'];
				if(empty($path) && $arVariables['variable']['SECTION_CODE_PATH']){
					$paths = explode('/', $arVariables['variable']['SECTION_CODE_PATH']);
					$path = end($paths);
					if(count($paths) > 1){
						$cnt = count($paths)-2;
						if($paths[$cnt]){
							$filterSectionParent = ["IBLOCK_ID" => array_keys($catalogIb), "CODE" => $paths[$cnt]];
							if($sectionListParent = \CIBlockSection::GetList([], $filterSectionParent, false, [ "ID", "IBLOCK_ID"], [ 'nTopCount' => 1 ])->fetch())
								$parentSectionId = $sectionListParent['ID'];

						}
					}
				}
				$pathID = $arVariables['variable']['SECTION_ID'];

				if(($arVariables['page'] == 'smart_filter' || $arVariables['page'] == 'section') && ($path || $pathID)){
					$filterSection = ["IBLOCK_ID" => array_keys($catalogIb)];

					if($pathID)
						$filterSection["ID"] = $pathID;
					if($path)
						$filterSection["CODE"] = $path;
					if($parentSectionId)
						$filterSection["SECTION_ID"] = $parentSectionId;

					$sectionList = \CIBlockSection::GetList([], $filterSection, false,
						[ "ID", "IBLOCK_ID", "SECTION_PAGE_URL" ], [ 'nTopCount' => 1 ]);

					$sectionList->SetUrlTemplates($selfRULE);
					if($section = $sectionList->fetch()){
						$variable = new variable();
						$variable->setIblockId($section['IBLOCK_ID']);
						$variable->setSectionId($section['ID']);
						$variable->getVariable();

						$rr['IBLOCK_ID'] = $section['IBLOCK_ID'];
						$rr['SECTION_ID'] = $section['ID'];
						if($arVariables['variable']['SMART_FILTER_PATH']){
							$rr['PARAMS'] = $variable->convertUrlToCheck($arVariables['variable']['SMART_FILTER_PATH']);
						}elseif($filtervar){
							$request = Main\Context::getCurrent()->getRequest();
							$queryArr = [];
							if(method_exists($request, 'getValues'))
								$queryArr = $request->getValues();
							elseif (method_exists($request, 'toArray')) {
								$queryArr = $request->toArray();
							}
							foreach($queryArr as $code => $val){
								if(preg_match('/' . $filtervar . '/i', strtoupper($code)))
									$rr['PARAMS'][$code] = trim($val);
							}
						}
						if(!$rr['PARAMS']){
							$rr = false;
						}
					}

				}
		}
		return $rr;
	}

	public function getCatalogVariabal($selfRULE, $arCustomUrlTemplates = [], $arCustomVariableAliases = []){

		$arDefaultUrlTemplates404 = array(
			"smart_filter" => "#SECTION_CODE_PATH#/filter/#SMART_FILTER_PATH#/apply/"
		);


		$arDefaultVariableAliases = array();

		$arDefaultVariableAliases404 = array(
			"SECTION_ID",
			"SECTION_CODE",
			"ELEMENT_ID",
			"ELEMENT_CODE",
			"IBLOCK_CODE",
			"action",
		);


		// Для ЧПУ режима

		// В этой переменной будем накапливать значения истинных переменных
		$arVariables = array();

		$engine = new \CComponentEngine($this);
		if (\Bitrix\Main\Loader::includeModule('iblock'))
		{
			$engine->addGreedyPart("#SECTION_CODE_PATH#");
			$engine->addGreedyPart("#SMART_FILTER_PATH#");
			$engine->setResolveCallback(array("CIBlockFindTools", "resolveComponentEngine"));
		}

		$arUrlTemplates = \CComponentEngine::makeComponentUrlTemplates($arDefaultUrlTemplates404, $arCustomUrlTemplates);
		$arVariableAliases = \CComponentEngine::makeComponentVariableAliases($arDefaultVariableAliases404, $arCustomVariableAliases);

		$componentPage = $engine->guessComponentPath(
			$selfRULE,
			$arUrlTemplates,
			$arVariables
		);

		$component = $componentPage;
		if ($componentPage === "smart_filter")
			$componentPage = "section";

		if(!$componentPage && isset($_REQUEST["q"]))
			$componentPage = "search";

		$b404 = false;
		if(!$componentPage)
		{
			$componentPage = "sections";
			$b404 = true;
		}
//		\CComponentEngine::initComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);

		//-- ищем в настройках ИБ соответствие путям. Если такогопути нет, то возвращаем 404
		return ['page' => $component, 'variable' => $arVariables];
	}

	public function searchPrice($items, $lookupValue)
	{
		$error = "";
		$searchValue = \Bitrix\Main\Text\Encoding::convertEncoding($lookupValue, LANG_CHARSET, "utf-8", $error);
		if (!$error)
		{
			$encodedValue = rawurlencode($searchValue);
			foreach($items as $itemId => $arItem)
			{
				if ($arItem["PRICE"])
				{
					$code = toLower($arItem["CODE"]);
					if ($lookupValue === $code || $encodedValue === $arItem["URL_ID"])
						return $itemId;
				}
			}
		}
		return null;
	}

	public function searchProperty($items, $lookupValue)
	{
		foreach($items as $itemId => $arItem)
		{
			if (!$arItem["PRICE"])
			{
				$code = toLower($arItem["CODE"]);
				if ($lookupValue === $code)
					return $itemId;
				if ($lookupValue == intval($arItem["ID"]))
					return $itemId;
			}
		}
		return null;
	}

	public function searchValue($item, $lookupValue)
	{
		$error = "";
		$searchValue = \Bitrix\Main\Text\Encoding::convertEncoding($lookupValue, LANG_CHARSET, "utf-8", $error);
		if (!$error)
		{
			$encodedValue = rawurlencode($searchValue);
			foreach($item as $itemId => $arValue)
			{
				if ($encodedValue === $arValue["URL_ID"])
					return $itemId;
			}
		}
		return false;
	}

	public function convertUrlToCheck($url)
	{
		$result = array();
		$smartParts = explode("/", $url);
		foreach ($smartParts as $smartPart)
		{
			$item = false;
			$smartPart = preg_split("/-(from|to|is|or)-/", $smartPart, -1, PREG_SPLIT_DELIM_CAPTURE);
			foreach ($smartPart as $i => $smartElement)
			{
				if ($i == 0)
				{
					if (preg_match("/^price-(.+)$/", $smartElement, $match))
						$itemId = $this->searchPrice($this->arResult["ITEMS"], $match[1]);
					else
						$itemId = $this->searchProperty($this->arResult["ITEMS"], $smartElement);

					if (isset($itemId))
						$item = &$this->arResult["ITEMS"][$itemId];
					else
						break;
				}
				elseif ($smartElement === "from")
				{
					$result[$item["VALUES"]["MIN"]["CONTROL_NAME"]] = $smartPart[$i+1];
				}
				elseif ($smartElement === "to")
				{
					$result[$item["VALUES"]["MAX"]["CONTROL_NAME"]] = $smartPart[$i+1];
				}
				elseif ($smartElement === "is" || $smartElement === "or")
				{
					$valueId = $this->searchValue($item["VALUES"], $smartPart[$i+1]);
					if($valueId <> '')
					{
						$result[$item["VALUES"][$valueId]["CONTROL_NAME"]] = $item["VALUES"][$valueId]["HTML_VALUE"];
					}
				}
			}
			unset($item);
		}
		return $result;
	}
	public function getLastGFilter(){
		return $this->gFilter;
	}
}
?>