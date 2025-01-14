<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use \Bitrix\Main\Engine\Contract\Controllerable;
use \Bitrix\Main\Engine\ActionFilter;
use \Bitrix\Main\Web\Cookie;
use \Bitrix\Main\ORM\Fields\IntegerField;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class WFFavorites extends CBitrixComponent implements Controllerable
{
    /**
     * Component constructor.
     * @param CBitrixComponent | null $component
     */
    var $connection;
    var $sqlHelper;

    public function __construct($component = null)
    {
        parent::__construct($component);
        $this->connection = \Bitrix\Main\Application::getConnection();
        $this->sqlHelper = $this->connection->getSqlHelper();

        //$this->connection->dropTable('wf_favorite');
        if (!$this->connection->isTableExists('wf_favorite')) {

            $this->connection->createTable('wf_favorite', [
                'USER_ID' => new IntegerField('USER_ID', ['primary' => true, 'required' => true]),
                'PRODUCT_ID' => new IntegerField('PRODUCT_ID', ['primary' => true, 'required' => true]),
            ],
                ['USER_ID', 'PRODUCT_ID']
            );
            $this->connection->createIndex('wf_favorite', 'i_wf_favorite', ['USER_ID', 'PRODUCT_ID']);

        }
    }

    // Обязательный метод
    public function configureActions()
    {
        // Сбрасываем фильтры по-умолчанию (ActionFilter\Authentication и ActionFilter\HttpMethod)
        // Предустановленные фильтры находятся в папке /bitrix/modules/main/lib/engine/actionfilter/
        return [
            'addFavorite' => [
                'prefilters' => [
                    //new ActionFilter\Authentication(),
                    new ActionFilter\HttpMethod([
                        //ActionFilter\HttpMethod::METHOD_GET,
                        ActionFilter\HttpMethod::METHOD_POST
                    ]),
                    new ActionFilter\Csrf(),
                ]
            ]
        ];
    }

    public function addFavoriteAction($pid)
    {
        global $USER;
        $pid = preg_replace('/[^0-9]/', '', $pid);
        if ($USER->isAuthorized()) {

            $favorites = $this->getUserValue();
            $guestFavorites = $this->getGuestValue();
            if (!empty($guestFavorites)) {
                $favorites = array_unique(array_merge($favorites, $guestFavorites));
            }

            $sqlPid = $this->sqlHelper->ForSql($pid);
            $sqlUid = $this->sqlHelper->ForSql($USER->GetID());
            if (in_array($pid, $favorites)) {
                // delete fav
                $q = "DELETE FROM wf_favorite WHERE USER_ID = '" . $sqlUid . "' AND PRODUCT_ID = '" . $sqlPid . "';";
                $this->connection->queryExecute($q);
                $favorites = array_diff($favorites, [$pid]);
            } else {
                // add fav
                $q = "INSERT INTO wf_favorite (USER_ID, PRODUCT_ID) VALUES ('" . $sqlUid . "', '" . $sqlPid . "');";
                $this->connection->queryExecute($q);
                $favorites[] = $pid;
            }

            //if (!empty($guestFavorites)) $this->setCookieAjax('');

        } else {

            $favorites = $this->getGuestValue();
            if (in_array($pid, $favorites)) {
                $favorites = array_diff($favorites, [$pid]);
            } else {
                $favorites[] = $pid;
            }

            $this->setCookieAjax($favorites);
        }

        return ['favorites' => $favorites, 'count' => count($favorites)];
    }

    protected function setCookieAjax($value)
    {
        $cookie = new Cookie("FAVORITES", implode(',', $value), 0);
        $cookie->setSecure(false);
        $context = Application::getInstance()->getContext();
        $response = $context->getResponse();
        $response->addCookie($cookie);
        $response->flush('');
    }

    /**
     * @return bool
     * @throws Exception
     */
    private function _checkModules()
    {
        if (!Loader::includeModule('iblock'))
            throw new \Exception('iblock module not found');

        return true;
    }

    /**
     * @param $arParams
     * @return mixed
     */
    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    protected function getUserValue()
    {
        global $USER;
        $sqlUid = $this->sqlHelper->ForSql($USER->GetID());
        $q = "SELECT * FROM wf_favorite WHERE USER_ID = " . $sqlUid . ";";
        $res = $this->connection->query($q, false, "File: " . __FILE__ . " Line: " . __LINE__);
        $favorites = [];
        while ($fav = $res->fetch()) {
            $favorites[] = $fav['PRODUCT_ID'];
        }

        return $favorites;
    }

    protected function getGuestValue()
    {
        $context = Application::getInstance()->getContext();

        $rawData = $context->getRequest()->getCookie("FAVORITES");
        $favorites = empty($rawData) ? [] : explode(',', $rawData);

        return $favorites;
    }

    public function getFavorites()
    {
        $favorites = [];

        global $USER;
        if ($USER->isAuthorized()) {
            $favorites = $this->getUserValue();
        } else {
            $favorites = $this->getGuestValue();
        }
        return $favorites;
    }

    public function executeComponent()
    {
        $this->_checkModules();

        // some actions
        $favorites = $this->getFavorites();
        $this->arResult['FAVORITES'] = $favorites;

        $this->arResult['COUNT'] = 0;
        if (is_array($favorites)) {
            $this->arResult['COUNT'] = count($favorites);
        }

        //\Bitrix\Main\Diag\Debug::dump();

        $this->includeComponentTemplate();
    }
}
