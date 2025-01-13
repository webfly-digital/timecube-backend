<?php
/**
 * Notification PayAnyWay v3.0.7
 */
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

CModule::IncludeModule("sale");
CModule::IncludeModule("payanyway.payment");

$dbPayList = CSalePaySystemAction::GetList(array(), array(), false, false, array('ID', 'ACTION_FILE', 'PARAMS', 'ENCODING'));
$paysystem_id = null;
while ($arPaysystem = $dbPayList->Fetch()) {
    if (strpos($arPaysystem['ACTION_FILE'], 'payanyway.payment') !== false) {
        $paysystem_id = $arPaysystem['ID'];
        $paysystem_params = unserialize($arPaysystem['PARAMS']);
        break;
    }
}

function pawGetRequestValue($name)
{
    $result = null;
    if (isset($_GET[$name])) {
        $result = $_GET[$name];
    }
    elseif (isset($_POST[$name])) {
        $result = $_POST[$name];
    }

    return $result;
}

if (!is_null($paysystem_id)) {

    /*
     * payment form transaction id
     *

    $MNT_TRANSACTION_ID = $arOrder["ID"];
    if (isset($arOrder["ORDER_PAYMENT_ID"])) {
        $MNT_TRANSACTION_ID .= "_" . $arOrder["ORDER_PAYMENT_ID"];
    }
    else {
        $MNT_TRANSACTION_ID .= "_";
    }

    // custom number of order account
    if (isset($arOrder["ACCOUNT_NUMBER"])) {
        $MNT_TRANSACTION_ID .= "_" . $arOrder["ACCOUNT_NUMBER"];
    }
    else {
        $MNT_TRANSACTION_ID .= "_";
    }

    $MNT_TRANSACTION_ID .= "_" . $sOrderID;
    */

    // workflow variables
    $error = '';
    $useSeparatePay = false;
    $order = null;
    $orderId = null;
    $arOrder = null;
    $order_id = null;
    $kassa_inventory = null;
    $kassa_customer = null;
    $kassa_delivery = null;


    // payanyway settings
    $is_testmode = (trim($paysystem_params['TEST']['VALUE']) != '0');
    $is_demomode = (trim($paysystem_params['DEMO']['VALUE']) != '0');
    $account_id = $paysystem_params['ACCOUNT_ID']['VALUE'];
    $account_code = $paysystem_params['ACCOUNT_CODE']['VALUE'];

    $MNT_ID = pawGetRequestValue('MNT_ID');
    $MNT_TRANSACTION_ID = pawGetRequestValue('MNT_TRANSACTION_ID');
    $MNT_OPERATION_ID = pawGetRequestValue('MNT_OPERATION_ID');
    $MNT_SUBSCRIBER_ID = pawGetRequestValue('MNT_SUBSCRIBER_ID');
    $MNT_CURRENCY_CODE = pawGetRequestValue('MNT_CURRENCY_CODE');
    $MNT_AMOUNT = pawGetRequestValue('MNT_AMOUNT');
    $MNT_TEST_MODE = pawGetRequestValue('MNT_TEST_MODE');
    $MNT_TEST_MODE = ($MNT_TEST_MODE == 1) ? '1' : '0';
    $MNT_SIGNATURE = pawGetRequestValue('MNT_SIGNATURE');
    $usercontact = pawGetRequestValue('usercontact');

    $transactionIdParts = explode('_', $MNT_TRANSACTION_ID);

    if ($MNT_ID && $MNT_TRANSACTION_ID && $MNT_AMOUNT) {

        $signature = md5($MNT_ID . $MNT_TRANSACTION_ID . $MNT_OPERATION_ID . $MNT_AMOUNT . $MNT_CURRENCY_CODE . $MNT_SUBSCRIBER_ID . $MNT_TEST_MODE . $account_code);
        if ($signature == $MNT_SIGNATURE && $account_id == $MNT_ID) {

            if (isset($transactionIdParts[0]) && $transactionIdParts[0]) {
                $order_id = $transactionIdParts[0];
                $arOrder = CSaleOrder::GetByID($order_id);
            }
            if (!$arOrder && isset($transactionIdParts[3]) && $transactionIdParts[3]) {
                $order_id = $transactionIdParts[3];
                $arOrder = CSaleOrder::GetByID($order_id);
            }
            if (!$arOrder && isset($transactionIdParts[2]) && $transactionIdParts[2]) {
                $order_id = $transactionIdParts[2];
                $arOrder = CSaleOrder::GetList(array(), array("ACCOUNT_NUMBER" => intval($order_id)))->arResult[0];
                $order_id = $arOrder['ID'];
            }

            $mntPaymentId = (isset($transactionIdParts[1]) && $transactionIdParts[1]) ? $transactionIdParts[1] : null;

            if ($order_id && $arOrder) {

                // check class \Bitrix\Sale\Order for old versions of sale module
                if ($mntPaymentId > 0 && class_exists('\Bitrix\Sale\Order')) {
                    /** @var \Bitrix\Sale\Order $order */
                    if (isset($transactionIdParts[0]) && $transactionIdParts[0]) {
                        $order = \Bitrix\Sale\Order::load($transactionIdParts[0]);
                        $orderId = $transactionIdParts[0];
                    }
                    if (isset($transactionIdParts[3]) && $transactionIdParts[3]) {
                        $order = \Bitrix\Sale\Order::load($transactionIdParts[3]);
                        $orderId = $transactionIdParts[3];
                    }
                    if (!$order && isset($transactionIdParts[2]) && $transactionIdParts[2]) {
                        $data = \Bitrix\Sale\Internals\OrderTable::getRow(array(
                            'select' => array('ID'),
                            'filter' => array('ACCOUNT_NUMBER' => $transactionIdParts[2])
                        ));
                        $order = \Bitrix\Sale\Order::load($data['ID']);
                        $orderId = $data['ID'];
                    }

                    $kassa_customer = (isset($GLOBALS["SALE_INPUT_PARAMS"]["USER"]["EMAIL"]) && $GLOBALS["SALE_INPUT_PARAMS"]["USER"]["EMAIL"]) ? $GLOBALS["SALE_INPUT_PARAMS"]["USER"]["EMAIL"] : $usercontact;

                    $payment = $order->getPaymentCollection()->getItemById($mntPaymentId);
                    if ($payment) {
                        CSalePaySystemAction::InitParamArrays($arOrder, $arOrder["ID"], '', array(), $payment->getFieldValues());
                        $aDesc = array(
                            "In Process" => array(GetMessage("SASP_IP"), GetMessage("SASPD_IP")),
                            "Delayed" => array(GetMessage("SASP_D"), GetMessage("SASPD_D")),
                            "Approved" => array(GetMessage("SASP_A"), GetMessage("SASPD_A")),
                            "PartialApproved" => array(GetMessage("SASP_PA"), GetMessage("SASPD_PA")),
                            "PartialDelayed" => array(GetMessage("SASP_PD"), GetMessage("SASPD_PD")),
                            "Canceled" => array(GetMessage("SASP_C"), GetMessage("SASPD_C")),
                            "PartialCanceled" => array(GetMessage("SASP_PC"), GetMessage("SASPD_PC")),
                            "Declined" => array(GetMessage("SASP_DEC"), GetMessage("SASPD_DEC")),
                            "Timeout" => array(GetMessage("SASP_T"), GetMessage("SASPD_T")),
                        );

                        // prepare data for kassa.payanyway.ru
                        if (class_exists('\Bitrix\Sale\Order')) {
                            $inventory = array();
                            $basket = \Bitrix\Sale\Order::load($orderId)->getBasket();
                            foreach ($basket as $basketItem) {
                                // in case of CP1251 encding
                                // $resultName = trim(preg_replace("/&?[a-z0-9]+;/i", "", htmlspecialchars(iconv("CP1251", "UTF8",$basketItem->getField('NAME')))));
                                $resultName = trim(preg_replace("/&?[a-z0-9]+;/i", "", htmlspecialchars($basketItem->getField('NAME'))));
                                $inventory[] = array("name" => $resultName, "price" => $basketItem->getPrice(), "quantity" => $basketItem->getQuantity(), "vatTag" => 1105);
                            }
                            $kassa_inventory = json_encode($inventory);
                        }

                        $payment->setField('PAID', 'Y');
                        $order->save();

                        $useSeparatePay = true;
                        // get order data
                        $getArrOrder = CSaleOrder::GetByID($orderId);
                        if (isset($getArrOrder['PRICE_DELIVERY']) && $getArrOrder['PRICE_DELIVERY']) {
                            $kassa_delivery = $getArrOrder['PRICE_DELIVERY'];
                        }
                        // https://dev.1c-bitrix.ru/api_help/sale/classes/csaleorder/csaleorder__getbyid.5cbe0078.php
                        if (is_array($getArrOrder)) {
                            // add a new transaction to Bitrix
                            $arTransaction = array(
                                'USER_ID' => $getArrOrder['USER_ID'],
                                'AMOUNT' => $MNT_AMOUNT,
                                'CURRENCY' => $MNT_CURRENCY_CODE,
                                'DEBIT' => 'Y',
                                'DESCRIPTION' => '(PayAnyWay) moneta.ru operation ID: ' . $MNT_OPERATION_ID,
                                'ORDER_ID' => $getArrOrder['ID'],
                                'EMPLOYEE_ID' => $getArrOrder['RESPONSIBLE_ID'],
                                'TRANSACT_DATE' => Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG)))
                            );
                            // https://dev.1c-bitrix.ru/api_help/sale/classes/csaleusertransact/csaleusertransact.add.php
                            CSaleUserTransact::Add($arTransaction);
                        }
                    }
                }

                // always update the whole order status
                $is_payed = ($arOrder['PAYED'] == 'Y');
                if (!$is_payed && !CSaleOrder::PayOrder($order_id, "Y")) {
                    $error .= ' Set payed flag error; ';
                }
                $arFields = array("STATUS_ID" => "P");
                $arFields["COMMENTS"] = $arOrder['COMMENTS'] . "\n cумма " . $MNT_AMOUNT . ' поступила в счет оплаты заказа';
                if (isset($paysystem_params['PAID_STATUS'], $paysystem_params['PAID_STATUS']['VALUE']) && $paysystem_params['PAID_STATUS']['VALUE']) {
                    $arFields['STATUS_ID'] = $paysystem_params['PAID_STATUS']['VALUE'];
                }
                if (!CSaleOrder::Update($order_id, $arFields)) {
                    $error .= ' Update payed order error ';
                }

                // generate XML answer
                $resultCode = 200;

                $result = '<?xml version="1.0" encoding="UTF-8" ?>';
                $result .= '<MNT_RESPONSE>';
                $result .= '<MNT_ID>' . $MNT_ID . '</MNT_ID>';
                $result .= '<MNT_TRANSACTION_ID>' . $MNT_TRANSACTION_ID . '</MNT_TRANSACTION_ID>';
                $result .= '<MNT_RESULT_CODE>' . $resultCode . '</MNT_RESULT_CODE>';
                $result .= '<MNT_SIGNATURE>' . md5($resultCode . $MNT_ID . $MNT_TRANSACTION_ID . $account_code) . '</MNT_SIGNATURE>';

                if ($kassa_inventory || $kassa_customer || $kassa_delivery) {
                    $result .= '<MNT_ATTRIBUTES>';
                }

                if ($kassa_inventory) {
                    $result .= '<ATTRIBUTE>';
                    $result .= '<KEY>INVENTORY</KEY>';
                    $result .= '<VALUE>' . $kassa_inventory . '</VALUE>';
                    $result .= '</ATTRIBUTE>';
                }

                if ($kassa_customer) {
                    $result .= '<ATTRIBUTE>';
                    $result .= '<KEY>CUSTOMER</KEY>';
                    $result .= '<VALUE>' . $kassa_customer . '</VALUE>';
                    $result .= '</ATTRIBUTE>';
                }

                if ($kassa_delivery) {
                    $result .= '<ATTRIBUTE>';
                    $result .= '<KEY>DELIVERY</KEY>';
                    $result .= '<VALUE>' . $kassa_delivery . '</VALUE>';
                    $result .= '</ATTRIBUTE>';
                }

                if ($kassa_inventory || $kassa_customer || $kassa_delivery) {
                    $result .= '</MNT_ATTRIBUTES>';
                }

                $result .= '</MNT_RESPONSE>';

                echo $result;

            } else {
                echo "FAIL#no_order";
            }

        } else {
            echo "FAIL#signature_is_incorrect";
        }

    } else {
        echo "FAIL#no_params";
    }

}
else {
    echo "FAIL#no_paysystem_id";
}

?>