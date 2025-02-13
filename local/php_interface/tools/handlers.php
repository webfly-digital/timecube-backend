<?php
use \Bitrix\Main\EventManager;
$eventManager = EventManager::getInstance();
//sale
//$eventManager->addEventHandler('sale', 'onSaleDeliveryServiceCalculate', ["\Webfly\Handlers\Sale", "setFreeDelivery"]);//бесплатная доставка по лейблу
//$eventManager->addEventHandler('sale', 'onSaleDeliveryServiceCalculate', ["\Webfly\Handlers\Sale", "setFreeDeliveryCoupon"]);//бесплатная доставка по купону
//catalog
//проставление веса и количества из свойств в поля товара
$eventManager->addEventHandler('catalog', '\Bitrix\Catalog\Product::OnBeforeUpdate', ["\Webfly\Handlers\Catalog\SetDimensions", "SetCatalogData"]);
$eventManager->addEventHandler('catalog', '\Bitrix\Catalog\Product::OnBeforeAdd', ["\Webfly\Handlers\Catalog\SetDimensions", "SetCatalogData"]);
$eventManager->addEventHandler('catalog', 'OnGetOptimalPrice', ["\Webfly\Handlers\Catalog\OnGetOptimalPrice", "SetCatalogGroupId"]);

//$eventManager->addEventHandler('sale', 'OnSaleComponentOrderResultPrepared',["\Webfly\Handlers\Sale", 'OnSaleComponentOrderJsDataHeandler']);