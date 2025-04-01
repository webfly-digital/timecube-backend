<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

use \Bitrix\Main;

try {

    $arComponentParameters = array(
    );
} catch (Main\LoaderException $e) {
    ShowError($e->getMessage());
}