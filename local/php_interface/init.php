<?php
if ($APPLICATION->GetCurPage() == "/test1.php") {
    die();
}
define('TOOLS_FOLDER', $_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/tools");
if (file_exists(TOOLS_FOLDER . "/config.php"))
    include_once(TOOLS_FOLDER . "/config.php");

mb_internal_encoding('UTF-8');

//const WF_CATALOG_IBLOCK_ID = '2';
//const WF_CATALOG_IBLOCK_TYPE = 'catalog';
//const WF_CATALOG_PRICE_TYPE = 'BASE';
const WF_SETTINGS_IBLOCK_CODE = 'settings';
const WF_CATALOG_IBLOCK_ID = '10';
const WF_CATALOG_IBLOCK_TYPE = '1c_catalog';
const WF_CATALOG_ROOT = 'catalog-root';
const WF_CATALOG_PRICE_TYPE = 'Розничная цена';
const WF_REVIEWS_IBLOCK_ID = '18';
const WF_MANUFACTURERS_IBLOCK_ID = '13';
const WF_ACTIONS_IBLOCK_ID = '16';
const WF_PACK_SECTION_CODE = 'pack';
const WF_PRICE_ID = '2';

const START_SALE = '2024-11-25';
const END_SALE =  '2024-12-01';
const NAME_SALE = 'black-friday';//'new-year'; //black-friday
const LABEL_SALE_TOP = 'BLACK';
const LABEL_SALE_BOTTOM = 'FRIDAY';


//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);


include_once 'include/users.php';
include_once 'include/coupons.php';
include_once 'include/basket.php';
include_once 'include/order.php';
include_once 'include/agents.php';

AddEventHandler('main', 'OnEpilog', 'pagenTitle', 100501);
function pagenTitle()
{
    global $APPLICATION;
    if (isset($_GET["PAGEN_3"])) $page = intval($_GET["PAGEN_3"]);
    if (isset($_GET["PAGEN_2"])) $page = intval($_GET["PAGEN_2"]);
    if (isset($_GET["PAGEN_1"])) $page = intval($_GET["PAGEN_1"]);
    if (!defined('ERROR_404') && $page > 0) {
//        $APPLICATION->SetPageProperty("title", $APPLICATION->GetPageProperty('title') . " | Cтраница №" . $page);
//        $APPLICATION->SetPageProperty("description", $APPLICATION->GetPageProperty('description') . " cтраница №" . $page . '.');
//        if ($page > 1) $APPLICATION->SetTitle($APPLICATION->getTitle(false) . " - Cтраница №" . $page);
    }
}

function logToFile($message, $traceCall = false)
{
    if (!is_string($message)) $message = var_export($message, true);
    // trace call stack option
    if ($traceCall) {
        $e = new Exception;
        $message .= PHP_EOL . $e->getTraceAsString();
    }
    // write to file
    $fd = fopen($_SERVER['DOCUMENT_ROOT'] . '/log.txt', 'a');
    $str = '[' . date('Y/m/d H:i:s', mktime()) . '] ' . $message;
    fwrite($fd, PHP_EOL . $str);
    fclose($fd);
}


$LastModified_unix = strtotime(date("D, d M Y H:i:s", filectime($_SERVER['SCRIPT_FILENAME'])));
$LastModified = gmdate("D, d M Y H:i:s \G\M\T", $LastModified_unix);
$IfModifiedSince = false;

if (isset($_ENV['HTTP_IF_MODIFIED_SINCE']))
    $IfModifiedSince = strtotime(substr($_ENV['HTTP_IF_MODIFIED_SINCE'], 5));

if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
    $IfModifiedSince = strtotime(substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 5));

if ($IfModifiedSince && $IfModifiedSince >= $LastModified_unix) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified');
    exit;
}
header('Last-Modified: ' . $LastModified);


//todo обработчик для редиректов со сторого урл на новый /product/
AddEventHandler('main', 'OnProlog', 'redirectOldProductUrl');
function redirectOldProductUrl()
{
    $context = \Bitrix\Main\Context::getCurrent();
    $request = $context->getRequest();
    $path = $request->getRequestedPageDirectory();
    $substr = explode('/', $path);
    $url = URLTable::getList(['filter' => ['CODE' => $substr[2]]])->fetch();
    if ($url != false && $substr[1] != 'product') {
        LocalRedirect("/product/" . $url["CODE"] . '/', true, "301");

    }
}

$eventManager = \Bitrix\Main\EventManager::getInstance();

// удяляем скрипты ядра при отдаче сайта пользователям
//$eventManager->addEventHandler("main", "OnEndBufferContent", "deleteJs");

// удяляем css ядра при отдаче сайта пользователям
$eventManager->addEventHandler("main", "OnEndBufferContent", "deleteKernelCss");
// удяляем скрипты ядра при отдаче сайта пользователям
function deleteKernelJs(&$content)
{
    global $USER, $APPLICATION;
    if ((is_object($USER) && $USER->IsAuthorized()) || strpos($APPLICATION->GetCurDir(), "/bitrix/") !== false) return;
    if ($APPLICATION->GetProperty("save_kernel") == "Y") return;

    $arPatternsToRemove = array(
        '/<script.+?src=".+?kernel_main\/kernel_main_v1\.js\?\d+"><\/script\>/',
    );

    $content = preg_replace($arPatternsToRemove, "", $content);
    $content = preg_replace("/\n{2,}/", "\n\n", $content);
}
function deleteJs(&$content)
{
    global $USER, $APPLICATION;
    if ((is_object($USER) && $USER->IsAuthorized()) || strpos($APPLICATION->GetCurDir(), "/bitrix/") !== false) return;

    $arPatternsToRemove = array(
        '/<script.+?src=".+?protobuf\/protobuf\.min\.js\?\d+"><\/script\>/',
    );

    $content = preg_replace($arPatternsToRemove, "", $content);
    $content = preg_replace("/\n{2,}/", "\n\n", $content);
}
// удяляем css ядра при отдаче сайта пользователям
function deleteKernelCss(&$content)
{
    global $USER, $APPLICATION;
    if ((is_object($USER) && $USER->IsAuthorized()) || strpos($APPLICATION->GetCurDir(), "/bitrix/") !== false) return;
    if ($APPLICATION->GetProperty("save_kernel") == "Y") return;

    $arPatternsToRemove = array(
        '/<link.+?href=".+?kernel_main\/kernel_main\.css\?\d+"[^>]+>/',
        '/<link.+?href=".+?bitrix\/js\/main\/core\/css\/core[^"]+"[^>]+>/',
        '/<link.+?href=".+?bitrix\/templates\/[\w\d_-]+\/styles.css[^"]+"[^>]+>/',
        '/<link.+?href=".+?bitrix\/templates\/[\w\d_-]+\/template_styles.css[^"]+"[^>]+>/',
        '/<link.+?href=".+?bitrix\/js\/ui\/fonts\/opensans\/ui.font.opensans[^"]+"[^>]+>/',
    );

    $content = preg_replace($arPatternsToRemove, "", $content);
    $content = preg_replace("/\n{2,}/", "\n\n", $content);
}

//todo класс для редиректов со сторого урл на новый /product/
class URLTable extends Bitrix\Main\Entity\DataManager
{
    public static function getTableName()
    {
        return 'wf_url';
    }

    public static function getMap()
    {
        return [
            'ID' => ['primary' => true, 'data_type' => 'integer'],
            'URL' => ['data_type' => 'string'],
            'PRODUCT_ID' => ['data_type' => 'string'],
            'CODE' => ['data_type' => 'string'],
        ];
    }

    public static function getUfId()
    {
        return mb_strtoupper(self::getTableName());
    }

    public static function createTable()
    {
        $connection = Bitrix\Main\Application::getInstance()->getConnection();
        if (!$connection->isTableExists(static::getTableName()))
            static::getEntity()->createDbTable();
    }

    public static function dropTable()
    {
        $connection = Bitrix\Main\Application::getInstance()->getConnection();
        $connection->dropTable(static::getTableName());
    }

    public static function truncateTable()
    {
        $connection = Bitrix\Main\Application::getInstance()->getConnection();
        return $connection->truncateTable(static::getTableName());
    }
}