<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

//$res = \Bitrix\Iblock\ElementTable::getList([
//    'select'=>['ID','NAME','ACTIVE'],
//    'filter'=>[ 'IBLOCK_ID'=>WF_ACTIONS_IBLOCK_ID, 'ID'=> ['10324','10327','10333']]
//]);
//
//global $USER;
//if ($USER->IsAdmin()) {
//    echo '<pre>';
//    var_dump($res->fetch());
//    var_dump($res->fetch());
//    var_dump($res->fetch());
//    echo '</pre>';
//}