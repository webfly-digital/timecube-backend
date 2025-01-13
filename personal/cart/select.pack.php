<?require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

Bitrix\Main\Loader::includeModule("catalog");
Bitrix\Main\Loader::includeModule("sale");

$context = Bitrix\Main\Context::getCurrent();
$site = $context->getSite();
$request = $context->getRequest();
$success = false;

if (check_bitrix_sessid() && $request->isPost()) {
    $productID = $request->getPost('productid');
    $basketID = $request->getPost('basketid');
    $packID = $request->getPost('packid');

    $pack = CIBlockElement::GetByID($packID)->GetNext();
    $freePackProp = CIBlockElement::GetProperty(WF_CATALOG_IBLOCK_ID, $productID, [], ["CODE"=>"IS_PACK_FREE"])->Fetch();

    // get product basket item
    $basket = \Bitrix\Sale\Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(), $site);
    $productItem = $basket->getItemById($basketID);
    $productPC = $productItem->getPropertyCollection();
    $productProps = $productPC->getPropertyValues();

    $packBasketID = $productProps['PACK']['VALUE'];
    if (!empty($packBasketID)) {
        // get existed pack in basket
        $packItem = $basket->getItemById($packBasketID);
    } 
    if (empty($packItem)) {
        // add pack
        $packItem = $basket->createItem('catalog', $packID);
    }

    $freePack = $freePackProp["VALUE_ENUM"] == 'Да' || $freePackProp["VALUE_ENUM"] == 'Y';
    $packPrice = $freePack ? '0' : '500';
    $title = $pack['NAME'] . ' - упаковка для [' . $productItem->getField('NAME') . ']';

    if ($freePack) {
        // add pack with custom price to basket
        $packItem->setFields([
            //'NAME' => $title,
            'QUANTITY' => 1,
            'CURRENCY' => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
            'LID' => $site,
            'PRODUCT_ID' => $packID,
            'CUSTOM_PRICE' => 'Y',
            'PRICE' => $packPrice,
            'SORT' => $productItem->getField('SORT'),
            'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
            'XML_ID' => 'PACK',
        ]);
    } else {
        // add pack with catalog price to basket
        $packItem->setFields([
            //'NAME' => $title,
            'PRODUCT_ID' => $packID,
            'QUANTITY' => 1,
            'CURRENCY' => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
            'LID' => $site,
            'SORT' => $productItem->getField('SORT'),
            'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
            'XML_ID' => 'PACK',
        ]);
    }
    // $packItem->setFieldNoDemand('NAME', $title);
    // set product basketID, for which pack is selected
    $packPC = $packItem->getPropertyCollection();
    $packProps = $packPC->getPropertyValues();
    if (!empty($packProps['PRODUCT_FOR'])) {
        $packProps['PRODUCT_FOR']['VALUE'] = $productItem->getId(); // product basketID
        $packProps['PRODUCT_FOR']['NAME'] = 'Упаковка для ['.$productItem->getField('NAME').']';
    } else {
        $packProps['PRODUCT_FOR'] = [
            'NAME' => 'Упаковка для ['.$productItem->getField('NAME').']',
            'CODE' => 'PRODUCT_FOR',
            'VALUE' => $productItem->getId(), // product basketID
            'SORT' => 100,
        ];
    }
    $packPC->redefine($packProps); // redefine = setProperty
    $packSaveResult = $success = $packItem->save();

    if ($packSaveResult->isSuccess()) {
        // set product basket property to selected pack basketID
        $productProps = $productPC->getPropertyValues();
        if (!empty($productProps['PACK'])) {
            $productProps['PACK']['VALUE'] = $packItem->getId(); // pack basketID
        } else {
            $productProps['PACK'] = [
                'NAME' => 'Упаковка',
                'CODE' => 'PACK',
                'VALUE' => $packItem->getId(), // pack basketID
                'SORT' => 100,
            ];
        }
        $productPC->redefine($productProps);
        $productSaveResult = $productItem->save();

        if ($productSaveResult->isSuccess()) {
            $success = true;
        }
    }

}
header('Content-Type: application/json');
echo json_encode(['success'=>$success]);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");