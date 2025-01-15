<?
namespace Bitrix\Sale\Cashbox;
use Bitrix\Main\Type\DateTime,
    Bitrix\Sale\Cashbox\Internals,
    Bitrix\Sale\Result,
    Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

//Переход на php8.2
Loader::includeModule('sale');

class cashboxCreativeKkmserver extends Cashbox implements IPrintImmediately
{
    const CODE_VAT_0 = 0;
    const CODE_VAT_10 = 10;
    const CODE_VAT_20 = 20;
    const CODE_CALC_VAT_10 = 110;
    const CODE_CALC_VAT_20 = 120;

    public static $defaultSettings = array(
        'TAX' => 0,
        'VAT' => array(
            '0' => -1,
            '10' => 10,
            '18' => 20,
            '20' => 20
        ),
        'Z_REPORT' => array(
            'TIME' => array(
                'H' => '00',
                'M' => '00',
            )
        ),
        'CLIENT' => array(
            'INFO' => 'EMAIL'
        ),
        'CHECK' => array(
            'PRINT' => '1',
            'COPY' => '0',
            'TEXT_BEFORE' => '',
            'TEXT_AFTER' => '',
        ),
        'COMPANY' => array(
            'CASHIER_NAME' => '',
            'CASHIER_INN' => '',
            'PLACE' => '',
        ),
    );

    public static function getGeneralRequiredFields()
    {
        $generalRequiredFields = parent::getGeneralRequiredFields();

        $map = Internals\CashboxTable::getMap();
        $generalRequiredFields['NUMBER_KKM'] = $map['NUMBER_KKM']['title'];
        return $generalRequiredFields;
    }

    public function buildCheckQuery(Check $check)
    {
        $dataCheck = $check->getDataForCheck();
        $UUID = self::buildUuid(self::UUID_TYPE_CHECK, $dataCheck['unique_id']);
        Internals\CashboxCheckTable::update(
            $dataCheck['unique_id'],
            array(
                'EXTERNAL_UUID' => $UUID,
                'DATE_PRINT_START' => new DateTime(),
                'LINK_PARAMS' => '',
            )
        );
        $checkTypeMap = $this->getCheckTypeMap();
        $signMethodMap = $this->getSignMethodMap();
        $signCalculationMap = $this->getSignCalculationMap();
        $settings = $this->getField('SETTINGS');
        $phone = \NormalizePhone($dataCheck['client_phone']);
        if (is_string($phone))
        {
            if ($phone[0] === '7' && $phone[1] === '9')
                $phone = '+'.$phone;
        }
        else
        {
            $phone = '';
        }
        $client_address = $dataCheck['client_email'] ?: '';
        if($settings['CLIENT']['INFO'] == 'PHONE' && !empty($phone)){
            $client_address = $phone;
        }
        $receiptCommand = array(
            'Command' => "RegisterCheck",
            'IdCommand' => $UUID,
            'KktNumber' => $this->getField('NUMBER_KKM'),
            'IsFiscalCheck' => true,
            'TypeCheck' => $checkTypeMap[$dataCheck['type']],
            'NotPrint' => empty($settings['CHECK']['PRINT']),
            'NumberCopies' => $settings['CHECK']['COPY'],
            'CashierName' => $settings['COMPANY']['CASHIER_NAME'],
            'CashierVATIN' => $settings['COMPANY']['CASHIER_INN'],
            'ClientAddress' => $client_address,
            'SenderEmail' => $this->getField('EMAIL'),
            'PlaceMarket' => $settings['COMPANY']['PLACE'],
            'TaxVariant' => $settings['TAX']['SNO'],
            'CheckStrings' => array(),
            'Cash' => 0,
            'ElectronicPayment' => 0,
            'AdvancePayment' => 0,
            'Credit' => 0,
            'CashProvision' => 0
        );
        if(!empty($settings['CHECK']['TEXT_BEFORE'])){
            $receiptCommand['CheckStrings'][] = array(
                'PrintText' => array(
                    'Text' => $settings['CHECK']['TEXT_BEFORE'],
                )
            );
        }
        foreach ($dataCheck['items'] as $item) {
            $CheckStrings = array(
                'Register' => array(
                    'Name' => $item['name'],
                    'Quantity' => $item['quantity'],
                    'Price' => round($item['base_price'], 2),
                    'Amount' => round($item['sum'], 2),
                    'Tax' => $this->mapVatValue($check::getType(),isset($settings['VAT'][$item['vat']]) ? $settings['VAT'][$item['vat']] : -1),
                    'SignMethodCalculation' => $signMethodMap[$dataCheck['type']],
                    'SignCalculationObject' => $signCalculationMap[$item['payment_object']],
                )
            );
            if(!empty($item['nomenclature_code'])){
                $nomenclature_code = str_replace(' ','', $item['nomenclature_code']);
                $GoodCodeData = array(
                    'StampType' => substr($nomenclature_code, 0, 2) == '00' ? substr($nomenclature_code, 2, 2) : substr($nomenclature_code, 0, 4),
                    'GTIN' => hexdec(substr($nomenclature_code, 4, 12)),
                    'SerialNumber' => $this->hexToStr(substr($nomenclature_code, 16)),
                );
                $CheckStrings['Register']['GoodCodeData'] = $GoodCodeData;
            }
            $receiptCommand['CheckStrings'][] = $CheckStrings;
        }
        foreach($dataCheck['payments'] as $payment) {
            switch($payment['type']){
                case Check::PAYMENT_TYPE_CASH:
                    $receiptCommand['Cash'] += $payment['sum'];
                    break;
                case Check::PAYMENT_TYPE_ADVANCE:
                    $receiptCommand['AdvancePayment'] += $payment['sum'];
                    break;
                case Check::PAYMENT_TYPE_CASHLESS:
                    $receiptCommand['ElectronicPayment'] += $payment['sum'];
                    break;
                case Check::PAYMENT_TYPE_CREDIT:
                    $receiptCommand['Credit'] += $payment['sum'];
                    break;
            }
        }


        if(!empty($settings['CHECK']['TEXT_AFTER'])){
            $receiptCommand['CheckStrings'][] = array(
                'PrintText' => array(
                    'Text' => $settings['CHECK']['TEXT_AFTER'],
                )
            );
        }
        return $receiptCommand;
    }

    public function buildZReportQuery($id) {}

    public function printImmediately(Check $check)
    {
        $printResult = new Result();
        return $printResult;
    }

    public static function getName()
    {
        return Loc::getMessage('CREATIVE_KKMSERVER_HANDLER_NAME');
    }

    protected static function extractCheckData(array $data)
    {
        // ���������� ������ �� ���� ����������� ����������
    }

    public static function getCashboxList($select = array('*'), $filter = array('%HANDLER' => 'cashboxCreativeKkmserver', 'ACTIVE' => 'Y'), $order = array('SORT' => 'ASC'))
    {
        $dbRes = Internals\CashboxTable::getList(array(
            'select' => $select,
            'filter' => $filter,
            'order' => $order
        ));
        return $dbRes->fetchAll();
    }

    public static function updateCashboxInfo($cashbox)
    {
        $_cashbox = self::getCashboxList(array('ID'), array('%HANDLER' => 'cashboxCreativeKkmserver', 'ACTIVE' => 'Y', 'NUMBER_KKM' => $cashbox['KktNumber']));
        if (!empty($_cashbox)) {
            foreach ($_cashbox as $_c) {
                Internals\CashboxTable::update(
                    $_c['ID'],
                    array(
                        'DATE_LAST_CHECK' => new DateTime(),
                        'ENABLED' => (!empty($cashbox['OnOff'] && !empty($cashbox['Active']))) ? 'Y' : 'N',
                    )
                );
            }
        }
    }

    public static function deleteCashboxList(){
        $cashboxes = self::getCashboxList(array('ID'), array('%HANDLER' => 'cashboxCreativeKkmserver'));
        if (!empty($cashboxes)) {
            foreach ($cashboxes as $cashbox) {
                Manager::delete($cashbox['ID']);
            }
        }
    }

    public static function getSettings($modelId = 0)
    {
        $settings = array();
        $settings['VAT'] = array(
            'LABEL' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_VAT_LABEL'),
            'REQUIRED' => 'Y',
            'ITEMS' => array(
                '0' => array(
                    'TYPE' => 'STRING',
                    'LABEL' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_VAT_ITEM_LABEL'),
                    'VALUE' => self::$defaultSettings['VAT']['0'],
                )
            )
        );

        if (Loader::includeModule('catalog')) {
            $dbRes = \Bitrix\Catalog\VatTable::getList(array('filter' => array('ACTIVE' => 'Y'), 'order' => array('SORT' => 'ASC')));
            $vatList = $dbRes->fetchAll();
            if ($vatList) {
                foreach ($vatList as $vat) {
                    $settings['VAT']['ITEMS'][(int)$vat['ID']] = array(
                        'TYPE' => 'STRING',
                        'LABEL' => $vat['NAME'] . ' [' . (int)$vat['RATE'] . '%]',
                        'VALUE' => isset(self::$defaultSettings['VAT'][(int)$vat['RATE']]) ? self::$defaultSettings['VAT'][(int)$vat['RATE']] : '',
                    );
                }
            }
        }

        $settings['TAX'] = array(
            'LABEL' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_TAX_LABEL'),
            'ITEMS' => array(
                'SNO' => array(
                    'TYPE' => 'ENUM',
                    'LABEL' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_TAX_ITEM_LABEL'),
                    'VALUE' => self::$defaultSettings['TAX'],
                    'OPTIONS' => array(
                        0 => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_TAX_OPTION_0'),
                        1 => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_TAX_OPTION_1'),
                        2 => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_TAX_OPTION_2'),
                        3 => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_TAX_OPTION_3'),
                        4 => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_TAX_OPTION_4'),
                        5 => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_TAX_OPTION_5'),
                    )
                )
            )
        );

        $hours = array('-' => '-');
        for ($i = 0; $i < 24; $i++) {
            $value = ($i < 10) ? '0' . $i : $i;
            $hours[$value] = $value;
        }

        $minutes = array('-' => '-');
        for ($i = 0; $i < 60; $i += 5) {
            $value = ($i < 10) ? '0' . $i : $i;
            $minutes[$value] = $value;
        }

        $settings['Z_REPORT'] = array(
            'LABEL' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_Z_REPORT_LABEL'),
            'ITEMS' => array(
                'TIME' => array(
                    'TYPE' => 'DELIVERY_MULTI_CONTROL_STRING',
                    'LABEL' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_Z_REPORT_ITEMS_LABEL'),
                    'ITEMS' => array(
                        'H' => array(
                            'TYPE' => 'ENUM',
                            'LABEL' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_Z_REPORT_ITEM_H'),
                            'VALUE' => self::$defaultSettings['Z_REPORT']['TIME']['H'],
                            'OPTIONS' => $hours
                        ),
                        'M' => array(
                            'TYPE' => 'ENUM',
                            'LABEL' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_Z_REPORT_ITEM_M'),
                            'VALUE' => self::$defaultSettings['Z_REPORT']['TIME']['M'],
                            'OPTIONS' => $minutes
                        ),
                    )
                )
            )
        );

        $settings['CLIENT'] = [
            'LABEL' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_CLIENT_LABEL'),
            'ITEMS' => array(
                'INFO' => array(
                    'TYPE' => 'ENUM',
                    'VALUE' => self::$defaultSettings['CLIENT']['INFO'],
                    'LABEL' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_CLIENT_INFO_LABEL'),
                    'OPTIONS' => array(
                        'EMAIL' => 'Email',
                        'PHONE' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_CLIENT_INFO_PHONE'),
                    )
                ),
            )
        ];
        $settings['CHECK'] = [
            'LABEL' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_CHECK_LABEL'),
            'ITEMS' => array(
                'PRINT' => array(
                    'TYPE' => 'ENUM',
                    'VALUE' => self::$defaultSettings['CHECK']['PRINT'],
                    'LABEL' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_CHECK_PRINT_LABEL'),
                    'OPTIONS' => array(
                        '0' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_CHECK_PRINT_0'),
                        '1' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_CHECK_PRINT_1'),
                    )
                ),
                'COPY' => array(
                    'TYPE' => 'ENUM',
                    'VALUE' => self::$defaultSettings['CHECK']['COPY'],
                    'LABEL' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_CHECK_COPY_LABEL'),
                    'OPTIONS' => array(
                        '0' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_CHECK_COPY_0'),
                        '1' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_CHECK_COPY_1'),
                        '2' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_CHECK_COPY_2'),
                        '3' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_CHECK_COPY_3'),
                        '4' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_CHECK_COPY_4'),
                        '5' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_CHECK_COPY_5'),
                    )
                ),
                'TEXT_BEFORE' => array(
                    'TYPE' => 'STRING',
                    'VALUE' => self::$defaultSettings['CHECK']['TEXT_BEFORE'],
                    'LABEL' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_CHECK_TEXT_BEFORE_LABEL'),
                ),
                'TEXT_AFTER' => array(
                    'TYPE' => 'STRING',
                    'VALUE' => self::$defaultSettings['CHECK']['TEXT_AFTER'],
                    'LABEL' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_CHECK_TEXT_AFTER_LABEL'),
                ),
            )
        ];
        $settings['COMPANY'] = [
            'LABEL' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_COMPANY_LABEL'),
            'ITEMS' => array(
                'CASHIER_NAME' => array(
                    'REQUIRED' => 'Y',
                    'TYPE' => 'STRING',
                    'VALUE' => self::$defaultSettings['COMPANY']['CASHIER_NAME'],
                    'LABEL' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_COMPANY_CASHIER_NAME'),
                ),
                'CASHIER_INN' => array(
                    'REQUIRED' => 'Y',
                    'TYPE' => 'STRING',
                    'VALUE' => self::$defaultSettings['COMPANY']['CASHIER_INN'],
                    'LABEL' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_COMPANY_CASHIER_INN'),
                ),
                'PLACE' => array(
                    'TYPE' => 'STRING',
                    'VALUE' => self::$defaultSettings['COMPANY']['PLACE'],
                    'LABEL' => Loc::getMessage('CREATIVE_KKMSERVER_SETTINGS_COMPANY_PLACE'),
                ),
            )
        ];
        return $settings;
    }

    public static function getZReportList($select = array('*'), $filter = array('STATUS' => 'Y'), $order = array('ID' => 'ASC'))
    {
        $dbRes = Internals\CashboxZReportTable::getList(array(
            'select' => $select,
            'filter' => $filter,
            'order' => $order
        ));
        return $dbRes->fetchAll();
    }

    public static function updateCheckInfo($commandResult){
        $uuid = self::parseUuid($commandResult['IdCommand']);
        $data = array();
        if($commandResult['Status'] == 0){
            $data['STATUS'] = 'Y';
            $data['DATE_PRINT_END'] = new DateTime();
            parse_str($commandResult['QRCode'], $receipt_data);
            $data['LINK_PARAMS']['doc_time'] = strtotime($receipt_data['t']);
            $data['LINK_PARAMS']['fn_number'] = $receipt_data['fn'];
            $data['LINK_PARAMS']['fiscal_doc_number'] = $receipt_data['i'];
            $data['LINK_PARAMS']['fiscal_doc_attribute'] = $receipt_data['fp'];
            $data['LINK_PARAMS']['doc_sum'] = $receipt_data['sum'];
            $data['LINK_PARAMS']['session'] = $commandResult['SessionNumber'];
            $data['LINK_PARAMS']['check_num'] = $commandResult['CheckNumber'];
            $data['LINK_PARAMS']['check_session_num'] = $commandResult['SessionCheckNumber'];
            $data['LINK_PARAMS']['url'] = $commandResult['URL'];
        }
        if($commandResult['Status'] == 2){
            $data['STATUS'] = 'E';
            $data['DATE_PRINT_END'] = new DateTime();
            $data['LINK_PARAMS']['error'] = $commandResult['Error'];
        }
        if($commandResult['Status'] == 5){
            $data['STATUS'] = 'E';
            $data['DATE_PRINT_END'] = new DateTime();
            $data['LINK_PARAMS']['error'] = Loc::getMessage('CREATIVE_KKMSERVER_ERROR_COMMAND_BE_SUCCESS');
        }
        Internals\CashboxCheckTable::update(
            $uuid['id'],
            $data
        );
    }

    public static function updateZReportInfo($commandResult)
    {
        $reports = self::getZReportList(array('*'), array('%LINK_PARAMS' => 's:9:"IdCommand";s:36:"' . $commandResult['IdCommand'] . '";'));
        foreach ($reports as $report) {
            $data = array(
                'LINK_PARAMS' => $report['LINK_PARAMS']
            );
            if ($commandResult['Status'] == 0) {
                $data['STATUS'] = 'Y';
                parse_str($commandResult['QRCode'], $receipt_data);
                $data['LINK_PARAMS']['doc_time'] = strtotime($receipt_data['t']);
                $data['LINK_PARAMS']['fn_number'] = $receipt_data['fn'];
                $data['LINK_PARAMS']['fiscal_doc_number'] = $receipt_data['i'];
                $data['LINK_PARAMS']['fiscal_doc_attribute'] = $receipt_data['fp'];
                $data['LINK_PARAMS']['session'] = $commandResult['SessionNumber'];
                $data['LINK_PARAMS']['url'] = $commandResult['URL'];
            } else if ($commandResult['Status'] > 1) {
                $data['STATUS'] = 'E';
                $data['LINK_PARAMS']['error'] = $commandResult['Error'];
            }
            if (!empty($data['STATUS'])) {
                $data['DATE_PRINT_END'] = new DateTime();
                Internals\CashboxZReportTable::update(
                    $report['ID'],
                    $data
                );
            }
        }
    }

    public static function cancelZReportByCommand($IdCommand)
    {
        $reports = self::getZReportList(array('*'), array('STATUS' => 'P', '%LINK_PARAMS' => 's:9:"IdCommand";s:36:"' . $IdCommand . '";'));
        foreach ($reports as $report) {
            $data = array(
                'DATE_PRINT_END' => new DateTime(),
                'STATUS' => 'E'
            );
            Internals\CashboxZReportTable::update(
                $report['ID'],
                $data
            );
        }
    }

    public static function cancelCheckByCommand($IdCommand)
    {
        $checks = self::getCheckList(array('*'), array('STATUS' => 'P', 'EXTERNAL_UUID' => $IdCommand));
        foreach ($checks as $check) {
            $data = array(
                'DATE_PRINT_END' => new DateTime(),
                'STATUS' => 'E'
            );
            Internals\CashboxCheckTable::update(
                $check['ID'],
                $data
            );
        }
    }

    public static function getCheckList($select = array('*'), $filter = array('STATUS' => 'N'), $order = array('ID' => 'ASC'))
    {
        $dbRes = Internals\CashboxCheckTable::getList(array(
            'select' => $select,
            'filter' => $filter,
            'order' => $order
        ));
        return $dbRes->fetchAll();
    }



    protected function getCheckTypeMap()
    {
        return array(
            SellCheck::getType() => 0,
            SellReturnCheck::getType() => 1,
            SellReturnCashCheck::getType() => 1,

            AdvancePaymentCheck::getType() => 0,
            AdvanceReturnCheck::getType() => 1,
            AdvanceReturnCashCheck::getType() => 1,

            CreditCheck::getType() => 0,
            CreditPaymentCheck::getType() => 0,
            CreditReturnCheck::getType() => 1,

            FullPrepaymentCheck::getType() => 0,
            FullPrepaymentReturnCheck::getType() => 1,
            FullPrepaymentReturnCashCheck::getType() => 1,

            PrepaymentCheck::getType() => 0,
            PrepaymentReturnCheck::getType() => 1,
            PrepaymentReturnCashCheck::getType() => 1,
        );
    }

    protected function getSignMethodMap()
    {
        return array(
            SellCheck::getType() => 4,
            SellReturnCheck::getType() => 4,
            SellReturnCashCheck::getType() => 4,

            AdvancePaymentCheck::getType() => 3,
            AdvanceReturnCheck::getType() => 3,
            AdvanceReturnCashCheck::getType() => 3,

            CreditCheck::getType() => 6,
            CreditPaymentCheck::getType() => 7,
            CreditReturnCheck::getType() => 6,

            FullPrepaymentCheck::getType() => 1,
            FullPrepaymentReturnCheck::getType() => 1,
            FullPrepaymentReturnCashCheck::getType() => 1,

            PrepaymentCheck::getType() => 2,
            PrepaymentReturnCheck::getType() => 2,
            PrepaymentReturnCashCheck::getType() => 2,
        );
    }

    protected function getSignCalculationMap()
    {
        return array(
            Check::PAYMENT_OBJECT_COMMODITY => 1,
            Check::PAYMENT_OBJECT_SERVICE => 4,
            Check::PAYMENT_OBJECT_JOB => 3,
            Check::PAYMENT_OBJECT_EXCISE => 2,
            Check::PAYMENT_OBJECT_PAYMENT => 10,
        );
    }

    public function getCheckLink(array $linkParams)
    {
        if(!empty($linkParams['url'])){
            return $linkParams['url'];
        }
        return '';
    }

    public function hexToStr($hex){
        $string='';
        for ($i=0; $i < strlen($hex)-1; $i+=2){
            $string .= chr(hexdec($hex[$i].$hex[$i+1]));
        }
        return $string;
    }

    public static function isSupportedFFD105()
    {
        return true;
    }

    private function mapVatValue($checkType, $vat)
    {
        $map = [
            self::CODE_VAT_10 => [
                PrepaymentCheck::getType() => self::CODE_CALC_VAT_10,
                PrepaymentReturnCheck::getType() => self::CODE_CALC_VAT_10,
                PrepaymentReturnCashCheck::getType() => self::CODE_CALC_VAT_10,
                FullPrepaymentCheck::getType() => self::CODE_CALC_VAT_10,
                FullPrepaymentReturnCheck::getType() => self::CODE_CALC_VAT_10,
                FullPrepaymentReturnCashCheck::getType() => self::CODE_CALC_VAT_10,
            ],
            self::CODE_VAT_20 => [
                PrepaymentCheck::getType() => self::CODE_CALC_VAT_20,
                PrepaymentReturnCheck::getType() => self::CODE_CALC_VAT_20,
                PrepaymentReturnCashCheck::getType() => self::CODE_CALC_VAT_20,
                FullPrepaymentCheck::getType() => self::CODE_CALC_VAT_20,
                FullPrepaymentReturnCheck::getType() => self::CODE_CALC_VAT_20,
                FullPrepaymentReturnCashCheck::getType() => self::CODE_CALC_VAT_20,
            ],
        ];

        return $map[$vat][$checkType] ?? $vat;
    }
}
?>