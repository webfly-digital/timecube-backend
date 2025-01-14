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

class WFReviews extends CBitrixComponent implements Controllerable
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
        $this->connection = \Bitrix\Main\Application::getConnection();
        $this->sqlHelper = $this->connection->getSqlHelper();
        $this->signer = new \Bitrix\Main\Security\Sign\Signer;
    }

    // Обязательный метод
    public function configureActions()
    {
        // Сбрасываем фильтры по-умолчанию (ActionFilter\Authentication и ActionFilter\HttpMethod)
        // Предустановленные фильтры находятся в папке /bitrix/modules/main/lib/engine/actionfilter/
        return [
            'addReview' => [ // Ajax-метод
                'prefilters' => [
                    new ActionFilter\Authentication(),
                    new ActionFilter\HttpMethod([
                        //ActionFilter\HttpMethod::METHOD_GET,
                        ActionFilter\HttpMethod::METHOD_POST
                    ]),
                    new ActionFilter\Csrf(),
                ]
            ],
        ];
    }

    // Ajax-методы должны быть с постфиксом Action
    public function addReviewAction($signedParams, $message, $rate)
    {
        try {
            $paramString = $this->unsign($signedParams);
        } catch (\Bitrix\Main\Security\Sign\BadSignatureException $e) {
            return ['BadSignatureException'];
        }
        $parameters = unserialize(base64_decode($paramString));

        // add Element
        global $USER;
        $el = new CIBlockElement;
        $props = [
            'ELEMENT_ID' => $parameters['ELEMENT_ID'],
            'USER_ID' => $USER->GetID(),
            'RATE' => $rate,
        ];
        $fields = [
            "MODIFIED_BY" => $USER->GetID(),
            "IBLOCK_ID" => $parameters["REVIEWS_IBLOCK_ID"],
            "IBLOCK_SECTION_ID" => false,
            "PROPERTY_VALUES" => $props,
            "NAME" => $USER->GetFullName(),
            "ACTIVE" => "N",
            "PREVIEW_TEXT" => $message,
        ];

        if ($id = $el->Add($fields))
            $result = "ID:" . $id;
        else
            $result = "Error:" . $el->LAST_ERROR;


        return [$parameters, 'result' => $result];
    }

    protected function unSign($param)
    {
        return $this->signer->unsign($param, 'webfly.reviews');
    }

    public function ajaxAction($pid, $signedTemplate, $signedParams)
    {
        try {
            $template = $this->unsign($signedTemplate);
            $paramString = $this->unsign($signedParams);
        } catch (\Bitrix\Main\Security\Sign\BadSignatureException $e) {
            return ['BadSignatureException'];
        }
        $parameters = unserialize(base64_decode($paramString));

        ob_start();
        global $APPLICATION;
        $APPLICATION->IncludeComponent(
            'webfly:reviews',
            $template,
            $parameters
        );
        $view = ob_get_contents();
        ob_end_clean();

        return [$view, $pid, $template, $parameters];
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
        $this->initNav();
        $this->getReviewsList();


        //\Bitrix\Main\Diag\Debug::dump($this->arResult['ITEMS']);

        $this->includeComponentTemplate();
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

    protected function initNav()
    {

        //https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=2741&LESSON_PATH=3913.5062.5748.2741
        $this->nav = new \Bitrix\Main\UI\PageNavigation("reviews");
        $this->nav->allowAllRecords(false)
            ->setPageSize($this->arParams['PAGE_COUNT'])
            ->initFromUri();

        $this->navParams = [
            'nPageSize' => $this->nav->getLimit(),
            'bShowAll' => $this->nav->allRecordsShown(),
            'iNumPage' => $this->nav->getCurrentPage(),
        ];
    }

    protected function getReviewsList()
    {
        global $USER, $APPLICATION;

        $select = ['IBLOCK_ID', 'ID', 'NAME', 'DATE_CREATE', 'ACTIVE', 'PREVIEW_TEXT', 'PROPERTY_USER_ID', 'PROPERTY_ELEMENT_ID'];
        $filter = [
            'IBLOCK_ID' => $this->arParams['REVIEWS_IBLOCK_ID'],
            'PROPERTY_ELEMENT_ID' => $this->arParams['ELEMENT_ID'],
        ];
        $order = [];
        if ($USER->isAdmin()) {
            $order['ACTIVE'] = 'ASC';
            if (empty($this->arParams['ELEMENT_ID'])) {
                $filter['ACTIVE'] = 'Y';
            }
        } else {
            $filter['ACTIVE'] = 'Y';
        }
        $order['PROPERTY_RATE'] = 'DESC';
        $order['DATE_CREATE'] = 'DESC';

        $res = CIBlockElement::GetList($order, $filter, false, $this->navParams, $select);

        $this->nav->setRecordCount($res->SelectedRowsCount());

        ob_start();
        $APPLICATION->IncludeComponent(
            "bitrix:main.pagenavigation", $this->arParams['PAGER_TEMPLATE'],
            ["NAV_OBJECT" => $this->nav, "SEF_MODE" => "N"], $this
        );
        $navString = ob_get_contents();
        ob_end_clean();

        $this->arResult["NAV_STRING"] = $navString;

        $list = [];
        $arrProduct =[];
        $this->arResult['USER_HAS_REVIEW'] = false;
        while ($ob = $res->GetNextElement()) {
            $fields = $ob->GetFields();
            $props = $ob->GetProperties(false, ['CODE' => ['ELEMENT_ID', 'USER_ID', 'RATE']]);
            if ($props['USER_ID']['VALUE'] == $USER->GetID()) $this->arResult['USER_HAS_REVIEW'] = true;
            $list[] = $fields + $props;
            if (empty($this->arParams['ELEMENT_ID'])) {
                $arrProductID[] = $fields["PROPERTY_ELEMENT_ID_VALUE"];
            }

        }

        if(!empty($arrProductID)){
            $resProduct = CIBlockElement::GetList([], ['ID' => $arrProductID], false, false, ['NAME', 'DETAIL_PAGE_URL']);
            while ($obProduct = $resProduct->GetNext()) {
               $arrProduct[$obProduct['ID']] =  $obProduct;
            }

            foreach ($list as $key =>  $item){
                $list[$key]['PRODUCT'] = $arrProduct[$item["PROPERTY_ELEMENT_ID_VALUE"]];
            }
        }



        $this->arResult['ITEMS'] = $list;

        if ($this->arParams['AJAX_MODE'] == 'Y') {
            $bxajaxid = \CAjax::GetComponentID($this->__name, $this->__template->__name, $this->arParams['AJAX_OPTION_ADDITIONAL']);
            $this->arResult['AJAX_ID'] = $bxajaxid;
        }
    }
}
