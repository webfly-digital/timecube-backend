<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$content = file_get_contents("zodiac/index.php"); // not a file link
$removedPhptags = preg_replace('/<\?(.*?)\?>/s', '', $content);


global $USER;
if ($USER->isAdmin()) {
    echo '<pre>';
    echo $removedPhptags;
    echo '</pre>';
}