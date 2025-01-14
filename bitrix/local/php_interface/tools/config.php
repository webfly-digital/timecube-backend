<?
//Лог
define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/bitrix/log.txt" );
//автозагружаемые классы
if (file_exists(TOOLS_FOLDER . "/autoload.php"))
    include_once(TOOLS_FOLDER . "/autoload.php");
//регистрация обработчиков
if (file_exists(TOOLS_FOLDER . "/handlers.php"))
    include_once(TOOLS_FOLDER . "/handlers.php");
