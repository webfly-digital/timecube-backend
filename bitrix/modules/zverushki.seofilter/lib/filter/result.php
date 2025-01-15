<?
namespace Zverushki\Seofilter\Filter;

use Bitrix\Main\Loader;

// \CBitrixComponent::includeComponentClass("bitrix:catalog.smart.filter");
/**
 *
 */
class result extends variable
{
	protected $gFilter = [];
	public function makeFilter($gFilters = array())
	{
		Loader::includeModule('iblock');
		$gFilter = $this->getfill($gFilters);

		$INCLUDE_SUBSECTIONS = "Y";
		$SHOW_ALL_WO_SECTION = "Y";
		$bOffersIBlockExist = false;
		if ($catalogIncluded === null)
			$catalogIncluded = Loader::includeModule('catalog');
		if ($catalogIncluded)
		{
			$arCatalog = \CCatalogSKU::GetInfoByProductIBlock($this->IBLOCK_ID);
			if (!empty($arCatalog))
			{
				$bOffersIBlockExist = true;
				$this->SKU_IBLOCK_ID = $arCatalog["IBLOCK_ID"];
				$this->SKU_PROPERTY_ID = $arCatalog["SKU_PROPERTY_ID"];
			}
		}

		$arFilter = array(
			"IBLOCK_ID" => $this->IBLOCK_ID,
			"IBLOCK_ACTIVE" => "Y",
			"INCLUDE_SUBSECTIONS" => ($INCLUDE_SUBSECTIONS != 'N' ? 'Y' : 'N'),
		);

		if (($this->SECTION_ID > 0) || ($SHOW_ALL_WO_SECTION !== "Y"))
		{
			$arFilter["SECTION_ID"] = $this->SECTION_ID;
		}

		if($this->arOption['avail'] == "Y")
			$arFilter['AVAILABLE'] = 'Y';
		if($this->arOption['not_active'] != 'Y'){
			$arFilter["ACTIVE"] = "Y";
			$arFilter["ACTIVE_DATE"] = "Y";
		}


		if($catalogIncluded && $bOffersIBlockExist)
		{

			$arPriceFilter = array();
			foreach($gFilter as $key => $value)
			{
				if (\CProductQueryBuilder::isPriceFilterField($key))
				{
					$arPriceFilter[$key] = $value;
					unset($gFilter[$key]);
				}
			}
			if(!empty($gFilter["OFFERS"]))
			{
				if (empty($arPriceFilter))
					$arSubFilter = $gFilter["OFFERS"];
				else
					$arSubFilter = array_merge($gFilter["OFFERS"], $arPriceFilter);

				$arSubFilter["IBLOCK_ID"] = $this->SKU_IBLOCK_ID;
				$arSubFilter["ACTIVE_DATE"] = "Y";
				$arSubFilter["ACTIVE"] = "Y";
				if($this->arOption['avail'] == "Y")
					$arSubFilter['AVAILABLE'] = 'Y';

				$arFilter["=ID"] = \CIBlockElement::SubQuery("PROPERTY_".$this->SKU_PROPERTY_ID, $arSubFilter);
				$arFilter["OFFER_QUERY"][$this->SKU_PROPERTY_ID] = $arSubFilter;
			}
			elseif(!empty($arPriceFilter))
			{
				$arSubFilter = $arPriceFilter;

				$arSubFilter["IBLOCK_ID"] = $this->SKU_IBLOCK_ID;
				$arSubFilter["ACTIVE_DATE"] = "Y";
				$arSubFilter["ACTIVE"] = "Y";
				$arFilter[] = array(
					"LOGIC" => "OR",
					array($arPriceFilter),
					"=ID" => \CIBlockElement::SubQuery("PROPERTY_".$this->SKU_PROPERTY_ID, $arSubFilter),
				);
			}
			unset($gFilter["OFFERS"]);
		}

		return array_merge($gFilter, $arFilter);
	}

	private function getfill($_CHECK){
		if(empty($this->arResult["IBLOCK_ID"]) || $this->arResult["IBLOCK_ID"] != $this->IBLOCK_ID)
			$this->getVariable();
		$arResult = $this->arResult;

		/*Set state of the html controls depending on filter values*/
		$allCHECKED = array();
		/*Faceted filter*/
		$facetIndex = array();
		if($arResult["ITEMS"])
			foreach($arResult["ITEMS"] as $PID => $arItem)
			{
				if($arItem["VALUES"])
					foreach($arItem["VALUES"] as $key => $ar)
					{
						if ($arResult["FACET_FILTER"] && isset($ar["FACET_VALUE"]))
						{
							$facetIndex[$PID][$ar["FACET_VALUE"]] = &$arResult["ITEMS"][$PID]["VALUES"][$key];
						}

						if(
							isset($_CHECK[$ar["CONTROL_NAME"]])
							|| (
								isset($_CHECK[$ar["CONTROL_NAME_ALT"]])
								&& $_CHECK[$ar["CONTROL_NAME_ALT"]] == $ar["HTML_VALUE_ALT"]
							)
						)
						{
							if($arItem["PROPERTY_TYPE"] == "N")
							{
								$arResult["ITEMS"][$PID]["VALUES"][$key]["HTML_VALUE"] = htmlspecialcharsbx($_CHECK[$ar["CONTROL_NAME"]]);
								$arResult["ITEMS"][$PID]["DISPLAY_EXPANDED"] = "Y";
								if ($arResult["FACET_FILTER"] && strlen($_CHECK[$ar["CONTROL_NAME"]]) > 0)
								{
									if ($key == "MIN")
										$this->facet->addNumericPropertyFilter($PID, ">=", $_CHECK[$ar["CONTROL_NAME"]]);
									elseif ($key == "MAX")
										$this->facet->addNumericPropertyFilter($PID, "<=", $_CHECK[$ar["CONTROL_NAME"]]);
								}
							}
							elseif(isset($arItem["PRICE"]))
							{
								$arResult["ITEMS"][$PID]["VALUES"][$key]["HTML_VALUE"] = htmlspecialcharsbx($_CHECK[$ar["CONTROL_NAME"]]);
								$arResult["ITEMS"][$PID]["DISPLAY_EXPANDED"] = "Y";
								if ($arResult["FACET_FILTER"] && strlen($_CHECK[$ar["CONTROL_NAME"]]) > 0)
								{
									if ($key == "MIN")
										$this->facet->addPriceFilter($arResult["PRICES"][$PID]["ID"], ">=", $_CHECK[$ar["CONTROL_NAME"]]);
									elseif ($key == "MAX")
										$this->facet->addPriceFilter($arResult["PRICES"][$PID]["ID"], "<=", $_CHECK[$ar["CONTROL_NAME"]]);
								}
							}
							elseif($arItem["DISPLAY_TYPE"] == "U")
							{
								$arResult["ITEMS"][$PID]["VALUES"][$key]["HTML_VALUE"] = htmlspecialcharsbx($_CHECK[$ar["CONTROL_NAME"]]);
								$arResult["ITEMS"][$PID]["DISPLAY_EXPANDED"] = "Y";
								if ($arResult["FACET_FILTER"] && strlen($_CHECK[$ar["CONTROL_NAME"]]) > 0)
								{
									if ($key == "MIN")
										$this->facet->addDatetimePropertyFilter($PID, ">=", MakeTimeStamp($_CHECK[$ar["CONTROL_NAME"]], FORMAT_DATE));
									elseif ($key == "MAX")
										$this->facet->addDatetimePropertyFilter($PID, "<=", MakeTimeStamp($_CHECK[$ar["CONTROL_NAME"]], FORMAT_DATE) + 23*3600+59*60+59);
								}
							}
							elseif($_CHECK[$ar["CONTROL_NAME"]] == $ar["HTML_VALUE"])
							{
								$arResult["ITEMS"][$PID]["VALUES"][$key]["CHECKED"] = true;
								$arResult["ITEMS"][$PID]["DISPLAY_EXPANDED"] = "Y";
								$allCHECKED[$PID][$ar["VALUE"]] = true;
								if ($arResult["FACET_FILTER"])
								{
									if ($arItem["USER_TYPE"] === "DateTime")
										$this->facet->addDatetimePropertyFilter($PID, "=", MakeTimeStamp($ar["VALUE"], FORMAT_DATE));
									else
										$this->facet->addDictionaryPropertyFilter($PID, "=", $ar["FACET_VALUE"]);
								}
							}
							elseif($_CHECK[$ar["CONTROL_NAME_ALT"]] == $ar["HTML_VALUE_ALT"])
							{
								$arResult["ITEMS"][$PID]["VALUES"][$key]["CHECKED"] = true;
								$arResult["ITEMS"][$PID]["DISPLAY_EXPANDED"] = "Y";
								$allCHECKED[$PID][$ar["VALUE"]] = true;
								if ($arResult["FACET_FILTER"])
								{
									$this->facet->addDictionaryPropertyFilter($PID, "=", $ar["FACET_VALUE"]);
								}
							}
						}
					}
			}


		/**/
		if ($_CHECK)
		{
			if ($arResult["FACET_FILTER"])
			{
				if (!$this->facet->isEmptyWhere())
				{
					foreach ($arResult["ITEMS"] as $PID => &$arItem)
					{
						if ($arItem["PROPERTY_TYPE"] != "N" && !isset($arItem["PRICE"]) && $arItem["VALUES"])
						{
							foreach ($arItem["VALUES"] as $key => &$arValue)
							{
								$arValue["DISABLED"] = true;
								$arValue["ELEMENT_COUNT"] = 0;
							}
							unset($arValue);
						}
					}
					unset($arItem);

					if ($arResult["CURRENCIES"])
						$this->facet->enableCurrencyConversion($this->convertCurrencyId, array_keys($arResult["CURRENCIES"]));

					$res = $this->facet->query($arResult["FACET_FILTER"]);
					while ($row = $res->fetch())
					{
						$facetId = $row["FACET_ID"];
						if (\Bitrix\Iblock\PropertyIndex\Storage::isPropertyId($facetId))
						{
							$pp = \Bitrix\Iblock\PropertyIndex\Storage::facetIdToPropertyId($facetId);
							if ($arResult["ITEMS"][$pp]["PROPERTY_TYPE"] == "N")
							{
								if (is_array($arResult["ITEMS"][$pp]["VALUES"]))
								{
									$arResult["ITEMS"][$pp]["VALUES"]["MIN"]["FILTERED_VALUE"] = $row["MIN_VALUE_NUM"];
									$arResult["ITEMS"][$pp]["VALUES"]["MAX"]["FILTERED_VALUE"] = $row["MAX_VALUE_NUM"];
								}
							}
							else
							{
								if (isset($facetIndex[$pp][$row["VALUE"]]))
								{
									unset($facetIndex[$pp][$row["VALUE"]]["DISABLED"]);
									$facetIndex[$pp][$row["VALUE"]]["ELEMENT_COUNT"] = $row["ELEMENT_COUNT"];
								}
							}
						}
						else
						{
							$priceId = \Bitrix\Iblock\PropertyIndex\Storage::facetIdToPriceId($facetId);
							if($arResult["PRICES"])
								foreach($arResult["PRICES"] as $NAME => $arPrice)
								{
									if (
										$arPrice["ID"] == $priceId
										&& isset($arResult["ITEMS"][$NAME])
										&& is_array($arResult["ITEMS"][$NAME]["VALUES"])
									)
									{
										$currency = $row["VALUE"];
										$existCurrency = strlen($currency) > 0;
										if ($existCurrency)
											$currency = $this->facet->lookupDictionaryValue($currency);

										$priceValue = $this->convertPrice($row["MIN_VALUE_NUM"], $currency);
										if (
											!isset($arResult["ITEMS"][$NAME]["VALUES"]["MIN"]["FILTERED_VALUE"])
											|| $arResult["ITEMS"][$NAME]["VALUES"]["MIN"]["FILTERED_VALUE"] > $priceValue
										)
										{
											$arResult["ITEMS"][$NAME]["VALUES"]["MIN"]["FILTERED_VALUE"] = $priceValue;
										}

										$priceValue = $this->convertPrice($row["MAX_VALUE_NUM"], $currency);
										if (
												!isset($arResult["ITEMS"][$NAME]["VALUES"]["MAX"]["FILTERED_VALUE"])
												|| $arResult["ITEMS"][$NAME]["VALUES"]["MAX"]["FILTERED_VALUE"] > $priceValue
										)
										{
											$arResult["ITEMS"][$NAME]["VALUES"]["MAX"]["FILTERED_VALUE"] = $priceValue;
										}
									}
								}
						}
					}
				}
			}
			else
			{
				$index = array();
				foreach ($arResult["COMBO"] as $id => $combination)
				{
					foreach ($combination as $PID => $value)
					{
						$index[$PID][$value][] = &$arResult["COMBO"][$id];
					}
				}

				/*Handle disabled for checkboxes (TODO: handle number type)*/
				foreach ($arResult["ITEMS"] as $PID => &$arItem)
				{
					if ($arItem["PROPERTY_TYPE"] != "N" && !isset($arItem["PRICE"]))
					{
						//All except current one
						$checked = $allCHECKED;
						unset($checked[$PID]);

						foreach ($arItem["VALUES"] as $key => &$arValue)
						{
							$found = false;
							if (isset($index[$PID][$arValue["VALUE"]]))
							{
								//Check if there are any combinations exists
								foreach ($index[$PID][$arValue["VALUE"]] as $id => $combination)
								{
									//Check if combination fits into the filter
									$isOk = true;
									foreach ($checked as $cPID => $values)
									{
										if (!isset($values[$combination[$cPID]]))
										{
											$isOk = false;
											break;
										}
									}

									if ($isOk)
									{
										$found = true;
										break;
									}
								}
							}
							if (!$found)
								$arValue["DISABLED"] = true;
						}
						unset($arValue);
					}
				}
				unset($arItem);
			}
		}

		/*Make iblock filter*/
		$FILTER = array();

		foreach($arResult["ITEMS"] as $PID => $arItem)
		{
			if(isset($arItem["PRICE"]))
			{
				if(strlen($arItem["VALUES"]["MIN"]["HTML_VALUE"]) && strlen($arItem["VALUES"]["MAX"]["HTML_VALUE"]))
					$FILTER["><CATALOG_PRICE_".$arItem["ID"]] = array($arItem["VALUES"]["MIN"]["HTML_VALUE"], $arItem["VALUES"]["MAX"]["HTML_VALUE"]);
				elseif(strlen($arItem["VALUES"]["MIN"]["HTML_VALUE"]))
					$FILTER[">=CATALOG_PRICE_".$arItem["ID"]] = $arItem["VALUES"]["MIN"]["HTML_VALUE"];
				elseif(strlen($arItem["VALUES"]["MAX"]["HTML_VALUE"]))
					$FILTER["<=CATALOG_PRICE_".$arItem["ID"]] = $arItem["VALUES"]["MAX"]["HTML_VALUE"];
			}
			elseif($arItem["PROPERTY_TYPE"] == "N")
			{
				$existMinValue = (strlen($arItem["VALUES"]["MIN"]["HTML_VALUE"]) > 0);
				$existMaxValue = (strlen($arItem["VALUES"]["MAX"]["HTML_VALUE"]) > 0);
				if ($existMinValue || $existMaxValue)
				{
					$filterKey = '';
					$filterValue = '';
					if ($existMinValue && $existMaxValue)
					{
						$filterKey = "><PROPERTY_".$PID;
						$filterValue = array($arItem["VALUES"]["MIN"]["HTML_VALUE"], $arItem["VALUES"]["MAX"]["HTML_VALUE"]);
					}
					elseif($existMinValue)
					{
						$filterKey = ">=PROPERTY_".$PID;
						$filterValue = $arItem["VALUES"]["MIN"]["HTML_VALUE"];
					}
					elseif($existMaxValue)
					{
						$filterKey = "<=PROPERTY_".$PID;
						$filterValue = $arItem["VALUES"]["MAX"]["HTML_VALUE"];
					}

					if ($arItem["IBLOCK_ID"] == $this->SKU_IBLOCK_ID)
					{
						if (!isset($FILTER["OFFERS"]))
						{
							$FILTER["OFFERS"] = array();
						}
						$FILTER["OFFERS"][$filterKey] = $filterValue;
					}
					else
					{
						$FILTER[$filterKey] = $filterValue;
					}
				}
			}
			elseif($arItem["DISPLAY_TYPE"] == "U")
			{
				$existMinValue = (strlen($arItem["VALUES"]["MIN"]["HTML_VALUE"]) > 0);
				$existMaxValue = (strlen($arItem["VALUES"]["MAX"]["HTML_VALUE"]) > 0);
				if ($existMinValue || $existMaxValue)
				{
					$filterKey = '';
					$filterValue = '';
					if ($existMinValue && $existMaxValue)
					{
						$filterKey = "><PROPERTY_".$PID;
						$timestamp1 = MakeTimeStamp($arItem["VALUES"]["MIN"]["HTML_VALUE"], FORMAT_DATE);
						$timestamp2 = MakeTimeStamp($arItem["VALUES"]["MAX"]["HTML_VALUE"], FORMAT_DATE);
						if ($timestamp1 && $timestamp2)
							$filterValue = array(FormatDate("Y-m-d H:i:s", $timestamp1), FormatDate("Y-m-d H:i:s", $timestamp2 + 23*3600+59*60+59));
					}
					elseif($existMinValue)
					{
						$filterKey = ">=PROPERTY_".$PID;
						$timestamp1 = MakeTimeStamp($arItem["VALUES"]["MIN"]["HTML_VALUE"], FORMAT_DATE);
						if ($timestamp1)
							$filterValue = FormatDate("Y-m-d H:i:s", $timestamp1);
					}
					elseif($existMaxValue)
					{
						$filterKey = "<=PROPERTY_".$PID;
						$timestamp2 = MakeTimeStamp($arItem["VALUES"]["MAX"]["HTML_VALUE"], FORMAT_DATE);
						if ($timestamp2)
							$filterValue = FormatDate("Y-m-d H:i:s", $timestamp2 + 23*3600+59*60+59);
					}

					if ($arItem["IBLOCK_ID"] == $this->SKU_IBLOCK_ID)
					{
						if (!isset($FILTER["OFFERS"]))
						{
							$FILTER["OFFERS"] = array();
						}
						$FILTER["OFFERS"][$filterKey] = $filterValue;
					}
					else
					{
						$FILTER[$filterKey] = $filterValue;
					}
				}
			}
			elseif($arItem["USER_TYPE"] == "DateTime")
			{
				$datetimeFilters = array();
				foreach($arItem["VALUES"] as $key => $ar)
				{
					if ($ar["CHECKED"])
					{
						$filterKey = "><PROPERTY_".$PID;
						$timestamp = MakeTimeStamp($ar["VALUE"], FORMAT_DATE);
						$filterValue = array(
							FormatDate("Y-m-d H:i:s", $timestamp),
							FormatDate("Y-m-d H:i:s", $timestamp + 23 * 3600 + 59 * 60 + 59)
						);
						$datetimeFilters[] = array($filterKey => $filterValue);
					}
				}

				if ($datetimeFilters)
				{
					$datetimeFilters["LOGIC"] = "OR";
					if ($arItem["IBLOCK_ID"] == $this->SKU_IBLOCK_ID)
					{
						if (!isset($FILTER["OFFERS"]))
						{
							$FILTER["OFFERS"] = array();
						}
						$FILTER["OFFERS"][] = $datetimeFilters;
					}
					else
					{
						$FILTER[] = $datetimeFilters;
					}
				}
			}
			else
			{
				if($arItem["VALUES"])
					foreach($arItem["VALUES"] as $key => $ar)
					{
						if($ar["CHECKED"])
						{
							$filterKey = "=PROPERTY_".$PID;
							$backKey = htmlspecialcharsback($key);
							if ($arItem["IBLOCK_ID"] == $this->SKU_IBLOCK_ID)
							{
								if (!isset($FILTER["OFFERS"]))
								{
									$FILTER["OFFERS"] = array();
								}
								if (!isset($FILTER["OFFERS"][$filterKey]))
									$FILTER["OFFERS"][$filterKey] = array($backKey);
								elseif (!is_array($FILTER["OFFERS"][$filterKey]))
									$FILTER["OFFERS"][$filterKey] = array($filter[$filterKey], $backKey);
								elseif (!in_array($backKey, $FILTER["OFFERS"][$filterKey]))
									$FILTER["OFFERS"][$filterKey][] = $backKey;
							}
							else
							{
								if (!isset($FILTER[$filterKey]))
									$FILTER[$filterKey] = array($backKey);
								elseif (!is_array($FILTER[$filterKey]))
									$FILTER[$filterKey] = array($filter[$filterKey], $backKey);
								elseif (!in_array($backKey, $FILTER[$filterKey]))
									$FILTER[$filterKey][] = $backKey;
							}
						}
					}
			}
		}
		$this->gFilter = $arResult["ITEMS"];

		return $FILTER;
	}
	public function getCountElement($setting){
		$this->setIblockId($setting['IBLOCK_ID']);
		$this->setSectionId($setting['SECTION_ID']);
		$arFilter = $this->makeFilter($setting['PARAMS']);
		return intval(\CIBlockElement::GetList(array("ID" => "ASC"), $arFilter, false, false, array("ID", "IBLOCK_ID"))->SelectedRowsCount());
	}

}
?>