<?
namespace Zverushki\Seofilter;

use Bitrix\Main,
	Bitrix\Main\Config\Option,
	Bitrix\Main\Loader,
	Zverushki\Seofilter\Filter\Seo,
	Zverushki\Seofilter\Cpu\ParamUrl,
	Zverushki\Seofilter\Cpu\Url,
	Zverushki\Seofilter\Configure\Version;

Loader::includeModule('iblock');

/**
* class Controller
*
*
* @package Zverushki\Seofilter
*/
class Controller {
	private static $Entity = null;
	const SEOFILTER_BEFORE_SEARCH_BY_URL = 'onBeforeSearchByUrl';
	const SEOFILTER_BEFORE_CUSTOM_LP = 'onBeforeCustomSeoLandingPage';
	const SEOFILTER_CUSTOM_LP = 'onCustomSeoLandingPage';
	const SEOFILTER_CUSTOM_REDIRECT = 'onCustomSeoRdirect';

	protected function __construct () {
		// $this->Config = Config::getEntity();

		if ($this->isContinue()) {
			$this->initConfigurateFilter();
		}

	}

	public static function getEntity () { return static::$Entity !== null ? static::$Entity : (static::$Entity = new self);}

	public function isContinue () {
		if (defined('PUBLIC_AJAX_MODE') && PUBLIC_AJAX_MODE)
			return false;

		/*$Request = Main\Context::getCurrent()->getRequest();
		if ($Request->isAjaxRequest())
			return false;*/

		if (!(!defined('ADMIN_SECTION') && strpos($_SERVER['PHP_SELF'], BX_ROOT.'/admin') !== 0))
			return false;
		/**
		 * Когда закрыт публичный доступ
		 */
		if (Option::get('main', 'site_stopped', 'N') == 'Y' && !$GLOBALS['USER']->CanDoOperation('edit_other_settings'))
			return false;

		return true;
	}
	public static function getTagView(){
		Seo::viewSeoTag();
		Version::get();
	}
	private function replaceSmart($urlCatalog, $urlSmart){
		return str_replace('//', '/', preg_replace('/#(SECTION_CODE|SECTION_ID|SECTION_CODE_PATH)#/', $urlCatalog, $urlSmart));
	}
	private function replaceSIteDir($urlCatalog){
		return str_replace('//', '/', preg_replace('/#(SITE_DIR)#/', SITE_DIR, $urlCatalog));
	}

	/** Получения полной ссылки, с учетом специфических окончаний
	 *
	 * @return string|null
	 */
	private function specialEndings() :?string{
		$uri = explode('?', Main\Context::getCurrent()->getRequest()->getDecodedUri())[0];
		return preg_replace('/index\.(php|html|htm)/', '', $uri);
	}
	private function initConfigurateFilter () {
		$urlDir = $this->specialEndings();
		$selfRULE = configuration::getOption('cpu_catalog', SITE_ID);

		if(!empty($urlDir) && strpos($urlDir, $selfRULE) !== false){
			$eventManager = Main\EventManager::getInstance();
			if ($eventsList = $eventManager->findEventHandlers('zverushki.seofilter', self::SEOFILTER_BEFORE_SEARCH_BY_URL))
			{
				$event = new Main\Event('zverushki.seofilter', self::SEOFILTER_BEFORE_SEARCH_BY_URL, ['URL' => $urlDir]);
				$event->send();

				if ($event->getResults())
				{
					/** @var Main\EventResult $eventResult */
					foreach ($event->getResults() as $eventResult)
					{
						if ($eventResult->getType() == Main\EventResult::SUCCESS)
							$urlDir = $eventResult->getParameters()['URL'];
					}
				}
			}

			configuration::set('requestUri', $urlDir);
			$a = ParamUrl::getEntity(SITE_ID)->searchUrl($urlDir);

			$variable = new Filter\variable();

			if ($a){
				$eventManager = Main\EventManager::getInstance();
				if ($eventsList = $eventManager->findEventHandlers('zverushki.seofilter', self::SEOFILTER_BEFORE_CUSTOM_LP))
				{
					$event = new Main\Event('zverushki.seofilter', self::SEOFILTER_BEFORE_CUSTOM_LP, $a);
					$event->send();

					if ($event->getResults())
					{
						/** @var Main\EventResult $eventResult */
						foreach ($event->getResults() as $eventResult)
						{
							if ($eventResult->getType() == Main\EventResult::SUCCESS)
								$a = $eventResult->getParameters();
						}
					}
				}

				configuration::set('VARIABLE', $a['VARIABLE']);
				configuration::set('VAR', $a['VAR']);
				$cpuActive = configuration::getOption('cpu_active', SITE_ID);
				$cpuUrl = configuration::getOption('cpu_url', SITE_ID);

				$url = $selfRULE;
				if ($a['IBLOCK_ID'] > 0 && $a['SECTION_ID'] > 0)
				{
					$section = false;
					$sectionList = \CIBlockSection::GetList(array(), array(
						"=ID" => $a['SECTION_ID'],
						"IBLOCK_ID" => $a['IBLOCK_ID'],
					), false, array("ID", "IBLOCK_ID", "SECTION_PAGE_URL"));

					if($cpuActive == "Y"){
						if(preg_match('/SECTION_CODE_PATH/', $cpuUrl)){
							if(
								($iblock = \Bitrix\Iblock\IblockTable::getList([
									'filter' => ['ID' => $a['IBLOCK_ID']],
									'select' => ['SECTION_PAGE_URL'],
									'limit' => 1
								])->fetch())
								&& !preg_match('/SECTION_CODE_PATH/', $iblock['SECTION_PAGE_URL'])
								&& !preg_match('/IBLOCK_CODE/', $iblock['SECTION_PAGE_URL'])
							)
							$sectionList->SetUrlTemplates('', $selfRULE . '#SECTION_CODE_PATH#', $selfRULE);
						}
					}

					$section = $sectionList->GetNext();
					if ($section)
						$url = $section["SECTION_PAGE_URL"];
				}elseif ($a['IBLOCK_ID'] > 0 && $a['SECTION_ID'] == 0){
					$res = \CIBlock::GetByID($a['IBLOCK_ID']);
					if($ar_res = $res->GetNext())
						$url = $this->replaceSIteDir($ar_res['LIST_PAGE_URL']);
				}

				if($cpuActive == "Y"){
					$variable->setIblockId($a['IBLOCK_ID']);
					$variable->setSectionId($a['SECTION_ID']);
					$a['REAL_URL'] = $variable->makeSmartUrl($this->replaceSmart($url, $cpuUrl), $a['PARAMS']);
				}else{
					$filterVar = configuration::getOption('filtervar', SITE_ID);
					foreach ($a['PARAMS'] as $code => $val) {
						$code = str_replace('arrPager', $filterVar, $code);
						$_GET[$code] = $val;
						$_REQUEST[$code] = $val;
					}
					$_GET['set_filter'] = 'y';
					$_REQUEST['set_filter'] = 'y';
					$a['REAL_URL'] = $url;
				}

				$eventManager = Main\EventManager::getInstance();
				if ($eventsList = $eventManager->findEventHandlers('zverushki.seofilter', self::SEOFILTER_CUSTOM_LP))
				{
					$event = new Main\Event('zverushki.seofilter', self::SEOFILTER_CUSTOM_LP, $a);
					$event->send();

					if ($event->getResults())
					{
						/** @var Main\EventResult $eventResult */
						foreach ($event->getResults() as $eventResult)
						{
							if ($eventResult->getType() == Main\EventResult::SUCCESS)
								$a = $eventResult->getParameters();
						}
					}
				}

			    $context = Main\Context::getCurrent();
			    $server = $context->getServer();
			    $server_array = $server->toArray();
			    configuration::set('setting', $a);
			    Seo::initSeoTag($a["ID"]);
			    _jp($a);
			    if(!empty($a['REAL_URL'])){
			    	Seo::setCanonical($a['URL_CPU']);
			        $_SERVER['REQUEST_URI'] = $a['REAL_URL'];
					$_SERVER["REDIRECT_URL"] = $urlDir;
			        $server_array['REQUEST_URI'] = $a['REAL_URL'];
			        $server_array['REDIRECT_URL'] = $a['REAL_URL'];
			        $server->set($server_array);
			        configuration::set('realUrl', $a['REAL_URL']);

			        global $APPLICATION;
			        if (mb_substr($a['REAL_URL'], -1) == "/")
			        	$a['REAL_URL'] .= "index.php";
			        $APPLICATION->SetCurPage($a['REAL_URL']);

			        $context->initialize(new Main\HttpRequest($server, $_GET, array(), array(), $_COOKIE), $context->getResponse(), $server);
			    }
			}else{
				$isRedirect = configuration::getOption('redirect_active', SITE_ID, 'N');
				$Request = Main\Context::getCurrent()->getRequest();
				if($isRedirect == 'Y' && !$Request->isAjaxRequest()){
					$arr = $variable->parseUrl($urlDir) ?: [];
					_jp($arr);
					$eventManager = Main\EventManager::getInstance();
					if ($eventsList = $eventManager->findEventHandlers('zverushki.seofilter', self::SEOFILTER_CUSTOM_REDIRECT))
					{
						$event = new Main\Event('zverushki.seofilter', self::SEOFILTER_CUSTOM_REDIRECT, $arr);
						$event->send();

						if ($event->getResults())
						{
							/** @var Main\EventResult $eventResult */
							foreach ($event->getResults() as $eventResult)
							{
								if ($eventResult->getType() == Main\EventResult::SUCCESS)
									$arr = $eventResult->getParameters();
							}
						}
					}

					if($arr)
						$url = Url::getEntity(SITE_ID)->genUrl($arr);

					if($url){
						if(method_exists($Request, 'getValues'))
							$strQuery = Url::getEntity(SITE_ID)->getQueryString($Request->getValues());
						elseif (method_exists($Request, 'toArray')) {
							$strQuery = Url::getEntity(SITE_ID)->getQueryString($Request->toArray());
						}

						$url .= $strQuery;
						LocalRedirect($url, $skip_security_check = false, $status = "301 Moved Permanently");
					}
				}
			}
		}
	}
	public function pagenParser(&$content){
		$realUrlOrigin = configuration::get('realUrl');
		$requestUri = configuration::get('requestUri');
		$realUrl = str_replace('/', '\/', $realUrlOrigin);
		// preg_match_all('/'+$requestUr+'(.+?)/i', $content, $matches);
		$rule = '/(<a.+href=["\']('.$realUrl.'(.*?))["\'].+?>)/i';
		preg_match_all($rule, $content, $matches);
		foreach ($matches[2] as $k => $url) {
			if(preg_match('/PAGEN/i', strtoupper($url)) || preg_match('/^'.$realUrl.'$/i', strtoupper($url))){
				$newRe = str_replace($realUrlOrigin, $requestUri, $matches[1][$k]);
				$content = str_replace($matches[1][$k], $newRe, $content);
			}
		}
		return $content;
	}
	private function getCategory($id){
		if (Loader::includeModule('catalog')) {
			$Db = \CIBlock::GetList(
			    Array(),
			    Array(
			        'ID' => $id,
			        'SITE_ID'=>SITE_ID,
			        'ACTIVE'=>'Y',
			        "CNT_ACTIVE"=>"Y",
			        "!CODE"=>'my_products'
			    ), true
			);
			if (($a = $Db->fetch()) !== false){
				return $a["CODE"];
			}

			return false;
		}
	}
}
?>