<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

global $USER;
if ($USER->isAdmin()) {

    $res = CIBlockElement::GetList(['ID' => 'DESC'],
        ["IBLOCK_ID" => WF_REVIEWS_IBLOCK_ID],
        false, ["nPageSize" => 9999],
        ["*", 'PROPERTY_OLD_ELEMENT_ID','PROPERTY_ELEMENT_ID']
    );

    while ($review = $res->fetch()) {

        $prod = CIBlockElement::GetList(['ID' => 'DESC'],
            ["IBLOCK_ID" => WF_CATALOG_IBLOCK_ID, '=PROPERTY_OLD_ID' => $review['PROPERTY_OLD_ELEMENT_ID_VALUE']],
            false, [],
            ["ID", 'NAME', 'CODE', 'PROPERTY_OLD_ID']
        )->fetch();
        if ($prod) {
            if ($review['PROPERTY_OLD_ELEMENT_ID_VALUE'] == $prod['PROPERTY_OLD_ID_VALUE'] &&
                !empty($review['PROPERTY_OLD_ELEMENT_ID_VALUE']) &&
                !empty($prod['PROPERTY_OLD_ID_VALUE'])) {
                $r = CIBlockElement::SetPropertyValuesEx($review['ID'], WF_REVIEWS_IBLOCK_ID, ['ELEMENT_ID' => $prod['ID']]);
            }
        }
    }
}
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
