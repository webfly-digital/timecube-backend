<? die();
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
include_once 'class.php';
$POST_RIGHT = $APPLICATION->GetGroupRight("main");
if ($POST_RIGHT == "D")
    $APPLICATION->AuthForm("Доступ запрещен");

Bitrix\Main\Loader::includeModule('iblock');
$IBLOCK_ID = 11;
$SECTION_ID = 'offers';
//$sections = [164, 158, 327, 326, 181, 264, 287, 263, 269, 268, 271, 281, 275, 290, 286, 161, 262];
//163 - Основной раздел - берем без подсекций

if ($_REQUEST['work'] == 'y') {
    $z = new XMLReader;
    $z->open($_SERVER['DOCUMENT_ROOT'] . '/parse/xml/catalog-' . $SECTION_ID . '.xml');
    $doc = new DOMDocument;
    while ($z->read() && $z->name !== 'element') {
    }

    while ($z->name == 'element') {
        $node = new SimpleXMLElement($z->readOuterXML());
        $curId = $node->fields->ID->__toString();
        if ($curId <= $_REQUEST['lastID']) {
            $lastID = 0;
            $z->next('element');
        } else {
            $lastID = $curId;
            $parse = new WFBuildCatalogXmlParse($IBLOCK_ID, $node);
            try {
                $parse->updateElement();
            } catch (\Exception $e) {
                AddMessage2log($e->getMessage());
                AddMessage2log($node);
            }
            break;
        }
    }
    $z->close();
    if (!empty($lastID)) {
        echo json_encode(['result' => 'continue', 'lastID' => $lastID]);
        die();
    } else {
        echo json_encode(['result' => 'end']);
        die();
    }
}

?>
<button type="button" class="go">GO</button>

<script src="https://yastatic.net/jquery/3.1.0/jquery.min.js"></script>
<script type="text/javascript">
    $(function () {
        function ajaxParse(lastID) {
            $.post('<?=$APPLICATION->getCurPage()?>', {work: "y", lastID: lastID}, function (response) {
                var res = JSON.parse(response);
                if (res.result == 'continue') {
                    console.log('lastID: ' + res.lastID);
                    ajaxParse(res.lastID);
                } else {
                    console.log('finish');
                }
            });
        }

        $('.go').on('click', function () {
            ajaxParse(0);
        });
    });
</script>

