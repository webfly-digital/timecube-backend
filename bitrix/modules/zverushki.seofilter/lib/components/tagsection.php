<?
namespace Zverushki\Seofilter\Components;
use \Bitrix\Main,
	\Bitrix\Main\Entity,
	\Bitrix\Main\Loader,
	\Bitrix\Main\Error,
	\Zverushki\Seofilter\Internals,
	Zverushki\Seofilter\Filter\Seo,
	Zverushki\Seofilter\Cpu\ParamUrl;

/**
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global CIntranetToolbar $INTRANET_TOOLBAR
 */

class tagSection extends \CBitrixComponent
{
	public function __construct($component = null)
	{
		parent::__construct($component);
	}

	public function onPrepareComponentParams($params)
	{
		$params['IBLOCK_TYPE'] = isset($params['IBLOCK_TYPE']) ? trim($params['IBLOCK_TYPE']) : '';

		$params['IDENTIFIER'] = isset($params['IDENTIFIER']) ? trim($params['IDENTIFIER']) : '';
		$params['IBLOCK_ID'] = isset($params['IBLOCK_ID']) ? intval($params['IBLOCK_ID']) : 0;
		$params['SECTION_ID'] = isset($params['SECTION_ID']) ? intval($params['SECTION_ID']) : 0;
		$params['TYPE'] = isset($params['TYPE']) ? trim($params['TYPE']) : 'only';
		$params['NEWS_COUNT'] = isset($params['NEWS_COUNT']) ? intval($params['NEWS_COUNT']) : 10;
		$params['ITEMS_VISIBLE'] = isset($params['ITEMS_VISIBLE']) ? intval($params['ITEMS_VISIBLE']) : 10;
		$params['SORT_BY1'] = isset($params['SORT_BY1']) ? trim($params['SORT_BY1']) : 'SORT';
		$params['SORT_ORDER1'] = isset($params['SORT_ORDER1']) ? trim($params['SORT_ORDER1']) : 'ASC';
		$params['SETTING_IDS'] = empty($params['SETTING_IDS']) ? [] : (is_array($params['SETTING_IDS']) ? $params['SETTING_IDS'] : [$params['SETTING_IDS']]);

		if($params['SORT_BY1'] == 'SORT')
			$params['SORT_BY1'] = 'CSORT';

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
		if(!$this->arParams['SECTION_ID'])
			return;
		$sectionIds = $this->initSectionTag($this->arParams['IBLOCK_ID'], $this->arParams['SECTION_ID']);
		if(!$sectionIds)
			return;

		$setFilter = [ 'IBLOCK_ID' => $this->arParams['IBLOCK_ID'], 'ACTIVE' => 'Y', 'SVIEW' => 'Y' ];
		if(!empty($this->arParams['SETTING_IDS']))
			$setFilter ['ID'] = $this->arParams['SETTING_IDS'];

		$settingIds = array_keys(ParamUrl::getEntity(SITE_ID)->getFilterId(SITE_ID, $setFilter));

		if(!$settingIds)
			return;

		$requestUri = \Zverushki\Seofilter\configuration::get('requestUri');
		$filter =[
			'ACTIVE'     => 'Y',
			'ENABLE'     => 'N',
			'IBLOCK_ID'  => $this->arParams['IBLOCK_ID'],
			'SETTING_ID' => $settingIds,
			'SECTION_ID' => $sectionIds,
			'!URL_CPU'   => $requestUri
		];
		$artSelect = ['ID', 'SETTING_ID', 'URL_CPU', 'PAGE_TITLE', 'PAGE_SECTION_TITLE', 'COUNT'];

		if($this->arParams['SORT_BY1'] == 'CSORT'){
			$artSelect[] = 'SORT';
			$artSelect[] = 'USORT';
			$artSelect[] = 'CSORT';
			$arRuntime = [new Entity\ExpressionField('CSORT', 'SORT+USORT')];
		}
		$__objSettings = Internals\LandingTable::getList([
			'filter' => $filter,
			'select' => $artSelect,
			'order' => ['TYPE' => 'DESC', $this->arParams['SORT_BY1'] => $this->arParams['SORT_ORDER1']],
			'runtime' => $arRuntime ? $arRuntime : [],
			'limit' => $this->arParams['NEWS_COUNT'],
		]);

		while($sindex = $__objSettings->fetch()){
			if(empty($this->arResult['ITEMS'][$sindex['URL_CPU']])){
				$sindex['PAGE_SECTION_TITLE'] = $sindex['PAGE_SECTION_TITLE'] ? $sindex['PAGE_SECTION_TITLE'] : $sindex['PAGE_TITLE'];
				if(empty($sindex['PAGE_SECTION_TITLE']))
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

	protected function initSectionTag($iblockId, $sectionId){
		if(!$iblockId)
			return [];

		if(!$sectionId)
			$sectionId = 0;

		$captureSection = [$sectionId];
		switch ($this->arParams['TYPE']) {
			/* Данного раздела и дочерних */
			case 'only_child':
				Seo::getSection($iblockId, $sectionId, $captureSection, 'desc');
				break;
			/* Данного раздела, если нет - родительский */
			case 'only_parent':
				if(Internals\SettingsTable::getCount([
					'IBLOCK_ID' => $iblockId,
					'SECTION_ID' => $sectionId,
					'ACTIVE' => 'Y',
					'SITE_ID.SITE_ID' => SITE_ID,
					'SVIEW' => 'Y'
				]) > 0)
					$captureSection = [$sectionId];
				else{
					$dbList = \Bitrix\Iblock\sectionTable::GetList([
						'order' => ['ID' => 'ASC'],
						'filter' => ['IBLOCK_ID' => $iblockId, 'GLOBAL_ACTIVE'=>'Y', 'ID' => $sectionId],
						'select' => ['ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'NAME'],
						'limit' => 1
					]);
					if($arResult = $dbList->fetch())
						$captureSection = [$arResult['IBLOCK_SECTION_ID']];
				}
				break;
		}

		return $captureSection;
	}
}