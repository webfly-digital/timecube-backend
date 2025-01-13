<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
if ($USER->IsAdmin()) {
//if ($_GET['pw'] == 'MlCRoLab') {
//
//    $connection = \Bitrix\Main\Application::getConnection();
//    $sqlHelper = $connection->getSqlHelper();
//    $cib = new CIBlockElement();
//
//    //$res = \CIBlockElement::GetList([], ['IBLOCK_ID' => '10'], false, ['nTopCount' => 100], ['CODE', 'XML_ID', 'ID']);
//    $res = \CIBlockElement::GetList([],['IBLOCK_ID'=>'10'],false,false,['CODE', 'XML_ID', 'ID']);
//    $updatedCount = 0;
//    while ($el = $res->Fetch()) {
//
//        $xml_id = $el['XML_ID'];
//        if (empty($xml_id)) continue;
//        $sql = "SELECT * FROM element_codes WHERE XML_ID = '" . $xml_id . "';";
//
//        $recordset = $connection->query($sql);
//        if (empty($recordset)) continue;
//        $record = $recordset->fetch();
//
//        if (!empty($record)) {
//            if ($el['CODE'] != $record['CODE']) {
//                $cib->Update($el['ID'], ['CODE'=>$record['CODE']]);
//                $updatedCount++;
//            }
//        }
//    }
//    echo $updatedCount;

    \Bitrix\Main\Loader::includeModule('sale');
    //$order = \Bitrix\Sale\Order::load(52048);
    //$order = \Bitrix\Sale\Order::load(52050);
    $order = \Bitrix\Sale\Order::load(52060);
    $deliveryList = $order->getDeliveryIdList();
    $sc = $order->getShipmentCollection();
    /** @var \Bitrix\Sale\Shipment $shipment */
    foreach ($sc as $shipment)
    {
        if (!$shipment->isSystem())
        {
            global $USER;
            if ($USER->IsAdmin()) {
                echo '<pre>';
//                $ds = \Bitrix\Sale\Delivery\Services\Manager::getById($shipment->getDeliveryId());
//                if ($ds['CODE'] == 'sdek:pickup') {
//
//                }
//                if ($ds['ID'] == '20') {
//                    $pFrom = $ds['CONFIG']['MAIN']['PERIOD']['FROM'];
//                    $pTo = $ds['CONFIG']['MAIN']['PERIOD']['TO'];
//                    $period = 'до ' .$pTo . ' дней';
//                }
                $params = $shipment->getField('PARAMS');
                var_dump($params["DELIVERY_TIME"]);
                //var_dump($shipment->getAvailableFields());
                echo '</pre>';
            }
        }
    }
}
