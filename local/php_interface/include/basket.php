<?php
\Bitrix\Main\EventManager::getInstance()->addEventHandler('sale', 'OnSaleBasketBeforeSaved', 'OnSaleBasketBeforeSavedHandler');

define('ACTION_MASK_ENABLED', false);

function OnSaleBasketBeforeSavedHandler(\Bitrix\Main\Event $event)
{
    /** @var \Bitrix\Sale\Basket $basket */
    $basket = $event->getParameter("ENTITY");
    /** @var \Bitrix\Sale\BasketItemCollection $basketItems */
    $basketItems = $basket->getBasketItems();

    $basketPositions = 0;
    $lowestPrice = PHP_INT_MAX;
    $highestPrice = 0;
    $lowestPriceItemId = null;
    $highestPriceItemId = null;
    $penItemId = null;
    $moetItemId = null;
    $penItemsCount = 0;
    $moetItemsCount = 0;
    // fields for disable discount prop
    $discountPropFields = [
        'NAME' => 'Скидка на второй товар',
        'CODE' => 'MORE2_DISCOUNT',
        'VALUE' => 'N',
        'SORT' => 1,
    ];
    \Bitrix\Main\Loader::includeModule('iblock');


    // check actions iblock
    $actionMoetEnabled = false; // action id 10324
    $actionPenEnabled = false; // action id 10327
    $actionMaskEnabled = false; // action id 10333

    $res = \Bitrix\Iblock\ElementTable::getList([
        'select'=>['ID','NAME','ACTIVE'],
        'filter'=>[ 'IBLOCK_ID'=>WF_ACTIONS_IBLOCK_ID, 'ID'=> ['10324','10327','10333']]
    ]);
    $actions = [];
    while ($a = $res->fetch()) {
        if ($a['ID'] == '10324') $actionMoetEnabled = ($a["ACTIVE"] == 'Y');
        if ($a['ID'] == '10327') $actionPenEnabled = ($a["ACTIVE"] == 'Y');
        if ($a['ID'] == '10333') $actionMaskEnabled = ($a["ACTIVE"] == 'Y');
    }


    $maskItemExist = false;
    if (!$actionMaskEnabled) $maskItemExist = true;

    /** @var \Bitrix\Sale\BasketItem $basketItem */

    foreach ($basketItems as $basketItem) {
        $basketItemPC = $basketItem->getPropertyCollection();
        $itemProps = $basketItemPC->getPropertyValues();
        $xmlId = $basketItem->getField('XML_ID');

        $context = \Bitrix\Main\Context::getCurrent();
        $site = $context->getSite();

        // delete mask, if more then one in basket
        if ($xmlId == 'MASK') {
            if ($maskItemExist) {
                $basketItem->delete();
            } else {
                $maskItemExist = true;
            }
            continue;
        }

        if ($xmlId == 'PACK') {
            // pack item. check related product, delete if 404
            $productForBasketID = $itemProps['PRODUCT_FOR']['VALUE'];
            $productFor = $basket->getItemById($productForBasketID);
            if (empty($productFor)) {
                $basketItem->delete();
            } else {
                // restrict user change quantity
                // see also basket template:
                // local/templates/wf_timecube/components/bitrix/sale.basket.basket/bootstrap_v5/mutator.php:81
                if ($basketItem->getField('QUANTITY') != 1)
                    $basketItem->setField('QUANTITY', 1);
            }
        } else if ($xmlId == 'PEN') {
            // save item ID
            $penItemId = $basketItem->getId();
        } else if ($xmlId == 'MOET') {
            // save item ID
            $moetItemId = $basketItem->getId();
        } else {
            // count basket positions. do not count gifts and other
            $basketPositions++;

            $pid = $basketItem->getField('PRODUCT_ID');
            // get PEN property value, if not empty, increase pen gift quantity
            $prop = CIBlockElement::GetProperty(WF_CATALOG_IBLOCK_ID, $pid,[],['CODE'=>'PEN_IN_CASE'])->fetch();
            if ($actionPenEnabled && $prop['VALUE_ENUM'] == 'Да')
                $penItemsCount += $basketItem->getField('QUANTITY');

            // get MOET property value, if not empty, increase MOET quantity
            $prop = CIBlockElement::GetProperty(WF_CATALOG_IBLOCK_ID, $pid,[],['CODE'=>'MOET_IN_CASE'])->fetch();
            if ($actionMoetEnabled && $prop['VALUE_ENUM'] == 'Да')
                $moetItemsCount += $basketItem->getField('QUANTITY');

            // product. check price, mark lowest price for discount
            $itemPrice = $basketItem->getFinalPrice();
            if ($lowestPrice > $itemPrice) {
                $lowestPrice = $itemPrice;
                $lowestPriceItemId = $basketItem->getId();
            }
            // mark highest price
            if ($highestPrice < $itemPrice) {
                $highestPrice = $itemPrice;
                $highestPriceItemId = $basketItem->getId();
            }
        }
    }

    // 2 foreach because U MUST DO ONLY ONE ITEM PROP SAVE ACTION in this event
    foreach ($basketItems as $basketItem) {
        $basketItemPC = $basketItem->getPropertyCollection();
        $itemProps = $basketItemPC->getPropertyValues();

        $xmlId = $basketItem->getField('XML_ID');
        if ($xmlId == 'MASK') {
            if ($basketPositions == 0) {
                $basketItem->delete();
                continue;
            }
            if ($basketItem->getField('QUANTITY') != 1)
                $basketItem->setField('QUANTITY', 1);
        }

        if ($xmlId == 'PEN' && $penItemsCount == 0) {
            $basketItem->delete();
        }
        if ($xmlId == 'MOET' && $moetItemsCount == 0) {
            $basketItem->delete();
        }

        // disable discount props for all products
        if (!empty($itemProps['MORE2_DISCOUNT'])) {
            $itemProps['MORE2_DISCOUNT']['VALUE'] = 'N';
        } else {
            $itemProps['MORE2_DISCOUNT'] = $discountPropFields;
        }

        if ($basketPositions == 1 || $xmlId == 'PEN' || $xmlId == 'MASK'|| $xmlId == 'MOET') {
            unset($itemProps['MORE2_DISCOUNT']);
        } else if (!empty($highestPriceItemId)) {
            // enable discount prop
            if ($highestPriceItemId != $basketItem->getId()) {
                if ($basketPositions > 1)
                    $itemProps['MORE2_DISCOUNT']['VALUE'] = 'Y';
            }
        }

        $basketItemPC->redefine($itemProps);
        $basketItemPC->save();
    }

    // 10 MASK ACTION item
    if (!$maskItemExist && $basketPositions > 0) {
        $maskItem = $basket->createItem('catalog', '0'); // on production, replace by item ID from catalog
        $maskItem->setFields([
            'NAME' => '10 масок в подарок',
            'QUANTITY' => 1,
            'CURRENCY' => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
            'LID' => $site,
            'PRICE' => 0,
            'CUSTOM_PRICE' => 'Y',
            'XML_ID' => 'MASK',
            'SORT' => '999'
        ]);
    }

    // PEN Item.
    if ($penItemsCount > 0 && $basketPositions > 0) {
        if (empty($penItemId)) {
            // Add, if not exist in basket
            $item = $basket->createItem('catalog', '5222'); // item ID from catalog
            $item->setFields([
                'NAME' => 'Ручка в подарок',
                'QUANTITY' => $penItemsCount, // Sync quantity to related products quantity
                'CURRENCY' => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
                'LID' => $site,
                'PRICE' => 0,
                'CUSTOM_PRICE' => 'Y',
                'XML_ID' => 'PEN',
                'SORT' => '999'
            ]);
        } else {
            // Sync quantity to related products quantity
            $item = $basket->getItemById($penItemId);
            $item->setField('QUANTITY', $penItemsCount);
        }
    }

    // MOET Item.
    if ($moetItemsCount > 0 && $basketPositions > 0) {
        if (empty($moetItemId)) {
            // Add, if not exist in basket
            $item = $basket->createItem('catalog', '5221'); // item ID from catalog
            $item->setFields([
                'NAME' => 'Шампанское MOET в подарок',
                'QUANTITY' => $moetItemsCount, // Sync quantity to related products quantity
                'CURRENCY' => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
                'LID' => $site,
                'PRICE' => 0,
                'CUSTOM_PRICE' => 'Y',
                'XML_ID' => 'MOET',
                'SORT' => '999'
            ]);
        } else {
            // Sync quantity to related products quantity
            $item = $basket->getItemById($moetItemId);
            $item->setField('QUANTITY', $moetItemsCount);
        }
    }


    return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS);
}