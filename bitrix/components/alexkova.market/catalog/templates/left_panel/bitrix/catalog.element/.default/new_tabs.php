<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
if(file_exists($_SERVER["DOCUMENT_ROOT"].$arParams["ADDITIONAL_TAB_PATH"]))
    require_once $_SERVER["DOCUMENT_ROOT"].$arParams["ADDITIONAL_TAB_PATH"];
?>
<?
/*$APPLICATION->IncludeComponent(
        "bitrix:main.include",
        "",
        Array(
                "AREA_FILE_SHOW" => "file",
                "PATH" => $arParams["ADDITIONAL_TAB_PATH"],
                "AREA_FILE_RECURSIVE" => "N",
                "EDIT_MODE" => "html",
        ),
        false,
        Array('HIDE_ICONS' => 'Y')
);*/?>
