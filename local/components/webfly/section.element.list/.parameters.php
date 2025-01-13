<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
$arComponentParameters = [
    "GROUPS" => [
        "COMMON" => [
            "SORT" => 110,
            "NAME" => 'Общие настройки',
        ],
    ],
    "PARAMETERS"=>[
        "IBLOCK_ID" => [
            "NAME" => 'ID инфоблока',
            "TYPE" => "INT",
            "PARENT" => "COMMON"
        ],
        "BLOCK_TITLE" => [
            "NAME" => 'Заголовок',
            "TYPE" => "STRING",
            "PARENT" => "COMMON"
        ],
        "FILTER_NAME" => [
            "NAME" => 'Переменная фильтра',
            "TYPE" => "STRING",
            "PARENT" => "COMMON"
        ],
        "LIMIT" => [
            "NAME" => 'Количество статей на группу',
            "TYPE" => "INT",
            "PARENT" => "COMMON"
        ],
        "CACHE_TIME" => array("DEFAULT" => 36000000)
    ]
];