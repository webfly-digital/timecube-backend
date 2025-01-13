<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$APPLICATION->IncludeComponent(
  "bitrix:sale.order.payment.receive",
  "",
  Array(
    "PAY_SYSTEM_ID" => "17",
    "PAY_SYSTEM_ID_NEW" => "17",
    "COMPOSITE_FRAME_MODE" => "A",
    "COMPOSITE_FRAME_TYPE" => "AUTO",
  )
);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>