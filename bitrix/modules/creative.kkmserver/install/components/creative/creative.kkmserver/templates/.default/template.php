<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\IO,
    Bitrix\Main\Application,
    Bitrix\Main\Type\DateTime,
    Bitrix\Sale\Cashbox\cashboxCreativeKkmserver,
    Bitrix\Sale\Cashbox\CheckManager;

/**Ведем лог поступающих ответов ККМ-сервера**/
if (isset($_GET['debug'])) {
    $file = new IO\File(Application::getDocumentRoot() . "/kkmserver/debug-" . date('Y-m-d') . ".log");
    $file->putContents(date('d.m.Y H:i:s') . "\n", IO\File::APPEND);
    $file->putContents(print_r($arResult, true), IO\File::APPEND);
    $file->putContents("\n\n\n", IO\File::APPEND);
}

$kkmserver = new creativeKkmserver();
if(!empty($arResult['ListRezult'])) {
    foreach ($arResult['ListRezult'] as $commandResult) {
        /**Отработка бага ККМ-сервера**/
        /**есть баг в ККМ-сервере с самовложенностью ответа при множественном запросе статуса одного запроса - разработчик уведомлен**/
        while ($commandResult['Command'] == 'GetRezult' && isset($commandResult['Rezult']['Rezult'])) {
            $commandResult = $commandResult['Rezult'];
        }
        /**Обработка результатов повторных запросов**/
        if ($commandResult['Command'] == 'GetRezult' && $commandResult['Status'] == 3 && !empty($commandResult['Rezult']['IdCommand'])) { //Команда с таким IdCommand не найдена
            cashboxCreativeKkmserver::cancelZReportByCommand($commandResult['Rezult']['IdCommand']);
            cashboxCreativeKkmserver::cancelCheckByCommand($commandResult['Rezult']['IdCommand']);
        }
        /**Обработка результатов закрытия смены**/
        if ($commandResult['Command'] == 'ZReport') {
            cashboxCreativeKkmserver::updateZReportInfo($commandResult);
        }
        /**Обработка результата текущего состояния ККТ**/
        if ($commandResult['Command'] == 'GetDataKKT' && $commandResult['Status'] == 0) {
            /**Закрываем смену, если касса сообщила, что сессия истекла**/
            if ($commandResult['Info']['SessionState'] == 3) {
                $kkmserver->apiReport(array('NumDevice' => $commandResult['NumDevice'], 'KktNumber' => $commandResult['Info']['KktNumber']));
            }
        }
        /**Обработка результатов регистрации чека*/
        if ($commandResult['Command'] == 'RegisterCheck') {
            if ($commandResult['Status'] == 0 || $commandResult['Status'] == 2 || $commandResult['Status'] == 5) {
                cashboxCreativeKkmserver::updateCheckInfo($commandResult);
            }
        }

        if ($commandResult['Command'] == 'List' && $commandResult['Status'] == 0) {
            /**Обработка действий со списком текущим **/
            foreach ($commandResult['ListUnit'] as $cashbox) {
                /**Ищем среди касс ККМ-сервера те, что есть в Битрикс**/
                $_cashbox = cashboxCreativeKkmserver::getCashboxList(array('*'), array('%HANDLER' => 'cashboxCreativeKkmserver', 'ACTIVE' => 'Y', 'NUMBER_KKM' => $cashbox['KktNumber']));
                if (!empty($_cashbox)) {
                    /**Запросим актуальные данные по данной кассе**/
                    $kkmserver->apiGetDataKKT($cashbox);
                    /**Обновляем время последнего обращения к кассе**/
                    cashboxCreativeKkmserver::updateCashboxInfo($cashbox);
                    /**Если касса включена и исправна**/
                    if (!empty($cashbox['OnOff']) && !empty($cashbox['Active'])) {
                        $datetime = new DateTime();
                        $datetime->add('-T5M');
                        /**Отберем Z-отчеты по данной кассе, которые созданы более 5 минут назад**/
                        $frozen_report = cashboxCreativeKkmserver::getZReportList(array('*'), array(
                            '<DATE_CREATE' => $datetime,
                            '<DATE_PRINT_START' => $datetime,
                            'STATUS' => 'P',
                            array(
                                "LOGIC" => "OR",
                                '%LINK_PARAMS' => 's:8:"IdDevice";s:36:"' . $cashbox['IdDevice'] . '";',
                                '%LINK_PARAMS' => 's:9:"KktNumber";s:14:"' . $cashbox['KktNumber'] . '";',
                            )
                        ));
                        if (!empty($frozen_report)) {
                            foreach ($frozen_report as $report) {
                                /**Повторный запрос результата по запросу на закрытие смены**/
                                $kkmserver->apiGetRezult($report['LINK_PARAMS']['IdCommand']);
                            }
                        }

                        foreach ($_cashbox as $c) {
                            /**Отберем чеки по данной кассе, которые созданы более 5 минут назад**/
                            $frozen_check = cashboxCreativeKkmserver::getCheckList(array('*'), array(
                                '<DATE_CREATE' => $datetime,
                                '<DATE_PRINT_START' => $datetime,
                                'STATUS' => 'P',
                                'CASHBOX_ID' => $c['ID'],
                            ));
                            if (!empty($frozen_check)) {
                                foreach ($frozen_check as $check) {
                                    /**Повторный запрос результата по запросу на закрытие смены**/
                                    $kkmserver->apiGetRezult($check['EXTERNAL_UUID']);
                                }
                            }
                            /**Получаем список Z-отчетов, которые нужно отправить на кассу**/
                            $wait_report = cashboxCreativeKkmserver::getZReportList(array('*'), array('STATUS' => 'N', 'CASHBOX_ID' => $c['ID']));
                            if (!empty($wait_report)) {
                                /**Формируем команду на формирование Z-отчета, переводим Z-отчет в статус "Печатается"**/
                                $kkmserver->apiReport($cashbox, true, $wait_report[0]['ID']);
                            }
                            /**Если в настройках кассы в Битрикс установлено время закрытия смены**/
                            if (isset($c['SETTINGS']['Z_REPORT']['TIME']['H']) && isset($c['SETTINGS']['Z_REPORT']['TIME']['M']) && $c['SETTINGS']['Z_REPORT']['TIME']['H'] != '-' && $c['SETTINGS']['Z_REPORT']['TIME']['M'] != '-') {
                                $datestamp = date('d.m.Y') . ' ' . $c['SETTINGS']['Z_REPORT']['TIME']['H'] . ':' . $c['SETTINGS']['Z_REPORT']['TIME']['M'] . ':00';
                                $timestamp = strtotime($datestamp);
                                /**Если наступил новый день, а настройки на 23 часа, то временную метку рассчитываем на вчера**/
                                if (date('H') == 0 && $c['SETTINGS']['Z_REPORT']['TIME']['H'] == 23) {
                                    $timestamp -= 86400;
                                }
                                /**Если текущее время равно времени настройки + 15 минут**/
                                if (time() - $timestamp >= 0 && time() - $timestamp <= 900) {
                                    $datetime = new DateTime();
                                    $datetime->setDate(date('Y', $timestamp), date('m', $timestamp), date('d', $timestamp));
                                    $datetime->setTime(date('H', $timestamp), date('i', $timestamp), date('s', $timestamp));
                                    /**Проверяем наличие других Z-отчетов позже сегодняшней временной метки на закрытие смены**/
                                    $report_by_time = cashboxCreativeKkmserver::getZReportList(array('*'), array(
                                        '>DATE_CREATE' => $datetime,
                                        array(
                                            "LOGIC" => "OR",
                                            '%LINK_PARAMS' => 's:8:"IdDevice";s:36:"' . $cashbox['IdDevice'] . '";',
                                            '%LINK_PARAMS' => 's:9:"KktNumber";s:14:"' . $cashbox['KktNumber'] . '";',
                                        )
                                    ));
                                    if (empty($report_by_time)) {
                                        /**Закрываем смену на кассе**/
                                        $kkmserver->apiReport($cashbox, true, 0, $c);
                                    }
                                }
                            }
                            /**Получаем список чеков, которые нужно отправить на кассу**/
                            $checks = \Bitrix\Sale\Cashbox\cashboxCreativeKkmserver::getCheckList(array('*'), array(
                                array(
                                    'LOGIC' => 'OR',
                                    'STATUS' => 'N',
                                    'STATUS' => 'P',
                                ),
                                'LINK_PARAMS' => NULL,
                                'CASHBOX_ID' => $c['ID']
                            ));
                            foreach ($checks as $check) {
                                $_check = CheckManager::getObjectById($check['ID']);
                                $kkmserver_cashbox = \Bitrix\Sale\Cashbox\cashboxCreativeKkmserver::create($c);
                                $kkmserver->listCommand['ListCommand'][] = $kkmserver_cashbox->buildCheckQuery($_check);
                            }
                        }
                    }
                }
            }
        }
    }
}
/**Запрашиваем список устройств на ККМ-сервере и выводим запросы ККМ-серверу**/
$kkmserver->apiList();
$kkmserver->sendQuery();

/**Ведем лог запросов к ККМ-серверу**/
if (isset($_GET['debug'])) {
    $file = new IO\File(Application::getDocumentRoot() . "/kkmserver/debug-" . date('Y-m-d') . ".log");
    $file->putContents(date('d.m.Y H:i:s') . "\n", IO\File::APPEND);
    $file->putContents(print_r($kkmserver->listCommand, true), IO\File::APPEND);
    $file->putContents("\n\n\n", IO\File::APPEND);
}
?>