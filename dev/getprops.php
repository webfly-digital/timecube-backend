<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
global $USER;
if ($USER->isAdmin()) {

    $filter = array (
        'ID' =>
            array (
                0 => 8696,
            ),
        'IBLOCK_LID' => 's1',
        'IBLOCK_ACTIVE' => 'Y',
        'ACTIVE_DATE' => 'Y',
        'ACTIVE' => 'Y',
        'CHECK_PERMISSIONS' => 'Y',
        'IBLOCK_ID' =>
            array (
                0 => 10,
                1 => 11,
            ),
        'CATALOG_SHOP_QUANTITY_2' => 1,
    );
    $select = array (
        0 => 'ID',
        1 => 'IBLOCK_ID',
        2 => 'IBLOCK_SECTION_ID',
        3 => 'DETAIL_PAGE_URL',
        4 => 'PROPERTY_*',
        5 => 'CATALOG_GROUP_2',
        6 => 'PREVIEW_PICTURE',
        7 => 'DETAIL_PICTURE',
        8 => 'NAME',
    );

$rsElements = CIBlockElement::GetList([], $filter, false, false, $select);
$rsElements->SetUrlTemplates($arParams['DETAIL_URL']);
while($obElement = $rsElements->GetNextElement()) {

        echo '<pre>';
        var_dump($obElement->GetProperties());
        echo '</pre>';
}
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");