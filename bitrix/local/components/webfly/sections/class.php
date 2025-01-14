<?

use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @global CUser $USER
 * @global CMain $APPLICATION
 */

Loc::loadMessages(__FILE__);

if (!\Bitrix\Main\Loader::includeModule('iblock')) {
    ShowError(Loc::getMessage('IBLOCK_MODULE_NOT_INSTALLED'));
    return;
}

class WFReviews extends CBitrixComponent
{
    /**
     * Component constructor.
     * @param CBitrixComponent | null $component
     */
    private $connection;
    private $sqlHelper;
    private $signer;
    private $navParams;
    private $nav;

    public function __construct($component = null)
    {
        parent::__construct($component);
    }

    /**
     * Подготовка параметров компонента
     * @param $arParams
     * @return mixed
     */
    public function onPrepareComponentParams($arParams)
    {
        // save original parameters for further ajax requests
        $this->arResult['ORIGINAL_PARAMETERS'] = $arParams;

        return $arParams;
    }

    /**
     * Точка входа в компонент
     * Должна содержать только последовательность вызовов вспомогательых ф-ий и минимум логики
     * всю логику стараемся разносить по классам и методам
     */
    public function executeComponent()
    {
        $this->checkModules();

        // some actions
        $this->getList();

        //\Bitrix\Main\Diag\Debug::dump($this->arResult['ITEMS']);

        $this->includeComponentTemplate();
    }

    protected function getList()
    {
        global $APPLICATION;

        $select = ['IBLOCK_ID', 'ID', 'NAME', 'ACTIVE'];
        $filter = [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
            'DEPTH_LEVEL' => $this->arParams['DEPTH_LEVEL'],
        ];
        $order = [];
        $order['SORT'] = 'ASC';

        $res = CIBlockSection::GetList($order, $filter, false, $select, $this->navParams);

        $list = [];
        while ($ob = $res->GetNextElement()) {
            $fields = $ob->GetFields();
            $list[] = $fields;
        }


        $this->arResult['SECTIONS'] = $list;
    }

    /**
     * Проверка наличия модулей требуемых для работы компонента
     * @return bool
     * @throws Exception
     */
    private function checkModules()
    {
        if (!Loader::includeModule('iblock')) {
            throw new \Exception('iblock module not found');
        }
        return true;
    }

}
