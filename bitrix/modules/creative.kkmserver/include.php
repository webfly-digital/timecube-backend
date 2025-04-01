<?
use Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Bitrix\Sale\Cashbox\Internals,
    Bitrix\Main\Type\DateTime,
    Bitrix\Sale\Cashbox\cashboxCreativeKkmserver;

class creativeKkmserver
{
    public $listCommand;

    public function __construct()
    {
        $this->listCommand = $this->listCommand();
        require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/creative.kkmserver/lib/cashboxcreativekkmserver.php");
    }

    public static function customCashboxHandlers()
    {
        $data = array('\Bitrix\Sale\Cashbox\cashboxCreativeKkmserver' => '/bitrix/modules/creative.kkmserver/lib/cashboxcreativekkmserver.php');
        $event = new Bitrix\Main\EventResult(Bitrix\Main\EventResult::SUCCESS, $data);
        return $event;
    }

    public static function AddModuleOnAjaxSettings()
    {

        $request = Context::getCurrent()->getRequest()->getRequestedPage();
        if ($request == '/bitrix/admin/sale_cashbox_ajax.php' || $request == '/shop/settings/sale_cashbox_ajax.php') { //��� ����� ��������� �� ������������� ��� �����/������������� ����
            Loader::includeModule('creative.kkmserver');
            Loader::includeModule('sale');
            new creativeKkmserver();
        }
    }

    public function listCommand()
    {
        return array(
            'ListCommand' => array()
        );
    }

    public function apiList()
    {
        $this->listCommand['ListCommand'][] = array(
            'Command' => 'List',
            "IdCommand" => self::getUUID()
        );
    }

    public function apiReport($cashbox = array(), $z = true, $id = 0, $CASHBOX = array())
    {
        if(empty($CASHBOX)) {
            $_cashbox = cashboxCreativeKkmserver::getCashboxList(array('*'), array('%HANDLER' => 'cashboxCreativeKkmserver', 'ACTIVE' => 'Y', 'NUMBER_KKM' => $cashbox['KktNumber']));
        } else {
            $_cashbox = cashboxCreativeKkmserver::getCashboxList(array('*'), array('%HANDLER' => 'cashboxCreativeKkmserver', 'ACTIVE' => 'Y', 'NUMBER_KKM' => $cashbox['KktNumber'], 'ID' => $CASHBOX['ID']));
        }
        foreach ($_cashbox as $_c) {
            $idCommand = self::getUUID();
            $data = array(
                'Command' => ($z ? 'ZReport' : 'XReport'),
                "IdDevice" => empty($cashbox['IdDevice']) ? '' : $cashbox['IdDevice'],
                "NumDevice" => empty($cashbox['IdDevice']) && !empty($cashbox['NumDevice']) ? $cashbox['NumDevice'] : '',
                "IdCommand" => $idCommand
            );
            if($data['IdDevice'] == ''){
                unset($data['IdDevice']);
            }
            if($data['NumDevice'] == ''){
                unset($data['NumDevice']);
            }
            $this->listCommand['ListCommand'][] = $data;
            if ($z) {
                if(empty($id)) {
                    Internals\CashboxZReportTable::add(
                        array(
                            'CASHBOX_ID' => $_c['ID'],
                            'DATE_CREATE' => new DateTime(),
                            'DATE_PRINT_START' => new DateTime(),
                            'STATUS' => 'P',
                            'CURRENCY' => 'RUB',
                            'LINK_PARAMS' => array('IdDevice' => $cashbox['IdDevice'], 'KktNumber' => $cashbox['KktNumber'], "IdCommand" => $idCommand),
                        )
                    );
                } else {
                    Internals\CashboxZReportTable::update(
                        $id,
                        array(
                            'DATE_PRINT_START' => new DateTime(),
                            'STATUS' => 'P',
                            'LINK_PARAMS' => array('IdDevice' => $cashbox['IdDevice'], 'KktNumber' => $cashbox['KktNumber'], "IdCommand" => $idCommand),
                        )
                    );
                }
            }
            break;
        }
    }

    public function apiGetDataKKT($cashbox = array())
    {
        $this->listCommand['ListCommand'][] = array(
            "Command" => "GetDataKKT",
            "IdDevice" => $cashbox['IdDevice'],
            "IdCommand" => self::getUUID()
        );
    }

    public function apiOpenShift($cashbox = array())
    {
        $this->listCommand['ListCommand'][] = array(
            "Command" => "OpenShift",
            "IdDevice" => empty($cashbox['IdDevice']) ? '' : $cashbox['IdDevice'],
            "NumDevice" => empty($cashbox['IdDevice']) && !empty($cashbox['NumDevice']) ? $cashbox['NumDevice'] : '',
            "IdCommand" => self::getUUID()
        );
    }

    public function apiGetRezult($IdCommand)
    {
        $this->listCommand['ListCommand'][] = array(
            "Command" => "GetRezult",
            "IdCommand" => $IdCommand
        );
    }

    public function sendQuery()
    {
        echo json_encode($this->listCommand, JSON_UNESCAPED_UNICODE);
    }

    public static function getUUID()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}

?>