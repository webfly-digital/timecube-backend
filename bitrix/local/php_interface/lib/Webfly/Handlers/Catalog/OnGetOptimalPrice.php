<?php

namespace Webfly\Handlers\Catalog;

class OnGetOptimalPrice
{
    /**
     * Выставляет базовую (розничную) цену
     * @param $productID
     * @param int $quantity
     * @param array $arUserGroups
     * @param string $renewal
     * @param array $arPrices
     * @param false $siteID
     * @param false $arDiscountCoupons
     * @return array[]|bool
     * @throws \Bitrix\Main\ArgumentException
     */
    public function SetCatalogGroupId($productID, $quantity = 1, $arUserGroups = array(), $renewal = "N", $arPrices = array(), $siteID = false, $arDiscountCoupons = false)
    {
        if (SITE_ID != 's1') return true;//только timecube

        $baseProductPrice = \Bitrix\Catalog\PriceTable::getList([
            "select" => ["*"],
            "filter" => [
                "=PRODUCT_ID" => $productID,
                "=CATALOG_GROUP_ID" => 2
            ],
            'limit' => 1,
        ])->fetch();

        return array(
            'PRICE' => array(
                "ID" => $baseProductPrice['ID'],
                'CATALOG_GROUP_ID' => $baseProductPrice['CATALOG_GROUP_ID'],
                'PRICE' => $baseProductPrice['PRICE'],
                'CURRENCY' => $baseProductPrice['CURRENCY'],
                'ELEMENT_IBLOCK_ID' => $productID,
                'VAT_INCLUDED' => "Y",
            ),
//            'DISCOUNT' => array(
//                'VALUE' => $discount,
//                'CURRENCY' => "RUB",
//            ),
        );
    }
}