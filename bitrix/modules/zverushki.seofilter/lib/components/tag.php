<?
namespace Zverushki\Seofilter\Components;
use \Bitrix\Main,
	\Bitrix\Main\Entity,
	\Bitrix\Main\Loader,
	\Bitrix\Main\Error,
	\Zverushki\Seofilter\Internals,
	Zverushki\Seofilter\Cpu\ParamUrl;

/**
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global CIntranetToolbar $INTRANET_TOOLBAR
 */

class tag extends \CBitrixComponent
{
	public function __construct($component = null)
	{
		parent::__construct($component);
	}

	public function onPrepareComponentParams($params)
	{
		$params['IBLOCK_TYPE'] = isset($params['IBLOCK_TYPE']) ? trim($params['IBLOCK_TYPE']) : '';

		$params['IDENTIFIER'] = isset($params['IDENTIFIER']) ? trim($params['IDENTIFIER']) : '';
		$params['IBLOCK_TYPE'] = isset($params['IBLOCK_TYPE']) ? trim($params['IBLOCK_TYPE']) : '';
		$params['IBLOCK_ID'] = isset($params['IBLOCK_ID']) ? intval($params['IBLOCK_ID']) : 0;
		$params['ELEMENT_ID'] = isset($params['ELEMENT_ID']) ? intval($params['ELEMENT_ID']) : '';
		$params['SECTION_ID'] = isset($params['SECTION_ID']) ? intval($params['SECTION_ID']) : '';
		$params['OFFER_ID'] = isset($params['OFFER_ID']) ? intval($params['OFFER_ID']) : '';
		$params['NEWS_COUNT'] = isset($params['NEWS_COUNT']) ? intval($params['NEWS_COUNT']) : 10;
		$params['ITEMS_VISIBLE'] = isset($params['ITEMS_VISIBLE']) ? intval($params['ITEMS_VISIBLE']) : 10;
		$params['SORT_BY1'] = isset($params['SORT_BY1']) ? trim($params['SORT_BY1']) : 'SORT';
		$params['SORT_ORDER1'] = isset($params['SORT_ORDER1']) ? trim($params['SORT_ORDER1']) : 'ASC';
		$params['SETTING_IDS'] = empty($params['SETTING_IDS']) ? [] : (is_array($params['SETTING_IDS']) ? $params['SETTING_IDS'] : [$params['SETTING_IDS']]);

		return $params;
	}
	public function executeComponent()
	{
		if(
			in_array(Loader::includeSharewareModule('zverushki.seofilter'), [Loader::MODULE_INSTALLED, Loader::MODULE_DEMO]) &&
			$this->getElementList()
		)
			$this->includeComponentTemplate();
	}

	protected function getElementList()
	{
		if(!$this->arParams['ELEMENT_ID'])
			return;
		$settingIds = [];

		$setFilter = ['IBLOCK_ID' => $this->arParams['IBLOCK_ID'], 'ACTIVE' => 'Y', 'SITE_ID.SITE_ID' => SITE_ID, 'EVIEW' => 'Y'];
		if(!empty($this->arParams['SETTING_IDS']))
			$setFilter ['ID'] = $this->arParams['SETTING_IDS'];

		$settingIds = array_keys(ParamUrl::getEntity(SITE_ID)->getFilterId(SITE_ID, $setFilter));

		if(!$settingIds)
			return;


		$filterStr = "`ELEMENT_ID` = ".$this->arParams['ELEMENT_ID']." and `IBLOCK_ID` = ".$this->arParams['IBLOCK_ID']." and `SETTING_ID` IN (".implode(',', $settingIds).")";
		if($this->arParams['OFFER_ID'])
			$filterStr .= " and `OFFER_ID` IN (".$this->arParams['OFFER_ID'].", 0)";

		if($this->arParams['SECTION_ID'])
			$filterStr .= " and `SECTION_ID` = ".$this->arParams['SECTION_ID'];

		$id = [];
		$Connection = Main\Application::getConnection();
		$BaseStack = Entity\Base::getInstance('\Zverushki\Seofilter\Internals\FindexTable');
		$__objSettings = $Connection->query("SELECT `ID`, `SETTING_ID`, `URL_CPU`, `LANDING_ID`
							FROM ".$Connection->getSqlHelper()->quote($BaseStack->getDBTableName())."
							WHERE ".$filterStr."
							GROUP BY `URL_CPU`
							ORDER BY `TYPE` DESC, `".$this->arParams['SORT_BY1']."` ".$this->arParams['SORT_ORDER1']."
							LIMIT ".$this->arParams['NEWS_COUNT']);

		while($findex = $__objSettings->fetch())
			$id[$findex['LANDING_ID']] = $findex['SETTING_ID'];

		unset($__objSettings);

		if($params['SORT_BY1'] == 'SORT')
			$params['SORT_BY1'] = 'CSORT';

		$filter =[
			'ACTIVE'     => 'Y',
			'ENABLE'     => 'N',
			'IBLOCK_ID'  => $this->arParams['IBLOCK_ID'],
			'SETTING_ID' => array_values($id),
			'ID' => array_keys($id)
		];
		$artSelect = ['ID', 'SETTING_ID', 'URL_CPU', 'PAGE_TITLE', 'COUNT'];

		if($this->arParams['SORT_BY1'] == 'CSORT'){
			$artSelect[] = 'SORT';
			$artSelect[] = 'USORT';
			$artSelect[] = 'CSORT';
			$arRuntime = [new Entity\ExpressionField('CSORT', 'SORT+USORT')];
		}
		$__objSettings = Internals\LandingTable::getList([
			'filter' => $filter,
			'select' => ['ID', 'SETTING_ID', 'URL_CPU', 'PAGE_TITLE', 'COUNT'],
			'order' => ['TYPE' => 'DESC', $this->arParams['SORT_BY1'] => $this->arParams['SORT_ORDER1']],
			'runtime' => $arRuntime ? $arRuntime : [],
			'limit' => $this->arParams['NEWS_COUNT'],
		]);
		while($sindex = $__objSettings->fetch()){
			if(empty($this->arResult['ITEMS'][$sindex['URL_CPU']])){
				if(empty($sindex['PAGE_TITLE']))
					continue;
				$this->arResult['ITEMS'][$sindex['URL_CPU']] = $sindex;
			}
		}

		$this->arResult['CNT'] = $this->arResult['ITEMS'] ? count($this->arResult['ITEMS']) : 0;
		$this->arResult['HIDE'] = $this->arResult['CNT'] > $this->arParams['ITEMS_VISIBLE'];

		if($this->arResult['ITEMS']){
			\Zverushki\Seofilter\configuration::setSort( $this->arParams['SORT_BY1'], $this->arParams['SORT_ORDER1'] );
			usort( $this->arResult['ITEMS'], "Zverushki\Seofilter\configuration::sort" );
		}

		return !empty($this->arResult['ITEMS']);
	}
}