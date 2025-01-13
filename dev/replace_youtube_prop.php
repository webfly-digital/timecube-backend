<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
global $USER;
if ($USER->isAdmin()) {


    $page = intval($_GET['iNumPage']);
    if (empty($_GET['iNumPage'])) $page = 1;

    $res = CIBlockElement::GetList(['ID' => 'DESC'],
        ["IBLOCK_ID" => WF_CATALOG_IBLOCK_ID,
            [
                'PREVIEW_PICTURE' => false,'DETAIL_PICTURE' => false
            ]
            //['LOGIC'=>'AND',['!PROPERTY_VIDEO' => false],['!PROPERTY_VIDEO' => '%youtu.be%']]
        ],
        false, ["iNumPage" => $page, "nPageSize" => 500],
        ["ID", 'PREVIEW_PICTURE', 'DETAIL_PICTURE']
    );
    $id = '';
    while ($ob = $res->fetch()) {
        $id = $ob['ID'];
        //CIBlockElement::SetPropertyValuesEx($ob['ID'], 8, ['OLD_ID' => $ob['ID']]);
        if ($_GET['del']=='y') {
            CIBlockElement::Delete($ob['ID']);
        }
        global $USER;
        if ($USER->isAdmin()) {
            echo '<pre>';
            var_dump($ob['ID']);
            echo '</pre>';
        }
    }


}
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
