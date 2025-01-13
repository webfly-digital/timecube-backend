<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

global $USER;
if ($USER->isAdmin()) {
    $connection = Bitrix\Main\Application::getConnection();
    $sqlHelper = $connection->getSqlHelper();

    $sql = "SELECT * FROM b_askaron_reviews_review;";

    $recordset = $connection->query($sql);
    while ($record = $recordset->fetch()) {
        $ibe = new CIBlockElement();
        $props = [
            'AUTHOR_EMAIL' => $record["AUTHOR_EMAIL"],
            'OLD_ELEMENT_ID' => $record["ELEMENT_ID"],
            'ELEMENT_ID' => $record["ELEMENT_ID"],
            'AUTHOR_IP' => $record["AUTHOR_IP"],
            'RATE' => $record["GRADE"],
            'PRO' => $record["PRO"],
            'CONTRA' => $record["CONTRA"],
        ];

        $text = $record["TEXT"];
        if (!empty($record["PRO"])) $text .= " \r\n" . 'Достоинства: '.$record["PRO"];
        if (!empty($record["CONTRA"])) $text .= " \r\n" . 'Недостатки: '.$record["CONTRA"];
        $messageParams = [
            'IBLOCK_ID'      => 7,
            'NAME'           => empty($record["AUTHOR_NAME"]) ? 'Аноним' : $record["AUTHOR_NAME"] ,
            'ACTIVE'         => $record["ACTIVE"],
            'ACTIVE_FROM'    => $record["DATE"],
            'PREVIEW_TEXT'   => $text,
            'PROPERTY_VALUES' => $props
        ];


        if($qID = $ibe->Add($messageParams))
            echo "New ID: ".$qID;
        else
            echo "Error: ".$ibe->LAST_ERROR;
        echo PHP_EOL;
    }
}
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
