<?
namespace Zverushki\Seofilter\Sitemap;

use Zverushki\Seofilter\Internals,
	Zverushki\Seofilter\Cpu\Url,
	Zverushki\Seofilter\Filter\result,
	Bitrix\Main\Context;

/**
 *
 */
class Generate
{
	protected $arSettings = array();
	protected $serverName;
	protected $siteMapUrl;
	protected $server;
	protected $request;
	protected $siteId;
	protected $siteName;
	protected $documentRoot;

	function __construct($siteId){
		$this->siteId = $siteId;

		$server = \Bitrix\Main\Context::getCurrent()->getServer();
		$this->server = \Bitrix\Main\Context::getCurrent()->getServer();
		$this->request = Context::getCurrent()->getRequest();

		$this->siteName = $this->server->getServerName();
		$this->documentRoot = $this->server->getDocumentRoot();
		$rsSites = \CSite::GetList($by = "sort", $order = "desc", Array('ID' => $this->siteId));
		if ($arSite = $rsSites->Fetch()){
			if(!empty($arSite['SERVER_NAME']));
				$this->siteName = $arSite['SERVER_NAME'];

			if(!empty($arSite['ABS_DOC_ROOT']));
				$this->documentRoot = $arSite['ABS_DOC_ROOT'];
		}
		// mp($this->documentRoot);die;

		$robots = file_get_contents($this->documentRoot.'/robots.txt');
		preg_match('/Sitemap: (.+)\/(.+)/', $robots, $robotSitmap);

		if($robotSitmap[1])
			$this->serverName = $robotSitmap[1];
		else
			$this->serverName = ($this->request->isHttps() ? "https://" : "http://").$this->siteName;

		$this->siteMapUrl = $robotSitmap[2] ? '/'.$robotSitmap[2] : '/sitemap.xml';
	}
	protected function get()
	{
		if(empty($this->siteId))
			return;

		$__objSettings = Internals\SettingsTable::getList(array(
			'filter' => array('ACTIVE' => "Y", 'SITE_ID.SITE_ID' => $this->siteId),
			'select' => array('ID', 'IBLOCK_ID', 'SECTION_ID', 'TIMESTAMP_X', 'URL_CPU'),
			'order' => array('SORT' => 'ASC', 'ID' => 'ASC'),
		));
		while($setting = $__objSettings->fetch())
			$arFIlter[$setting['ID']] = $setting;

		if($arFIlter){
			$__objSettings = Internals\LandingTable::getList([
				'filter' => [
					'ACTIVE'     => 'Y',
					'ENABLE'     => 'N',
					'SETTING_ID' => array_keys($arFIlter)
				],
				'select' => ['ID', 'SETTING_ID', 'URL_CPU', 'DATE_ELEMENT'],
				'order' => ['TYPE' => 'DESC', 'SORT' => 'ASC', 'DATE_ELEMENT' => 'DESC', 'SETTING_ID' => 'ASC']
			]);
			while($findex = $__objSettings->fetch()){
				if(empty($findex['DATE_ELEMENT']))
					$findex['DATE_ELEMENT'] = new Main\Type\DateTime;

				$setting['URL_CPU'] = iconv("windows-1251", "utf-8", $findex['URL_CPU']);
				if(empty($this->arSettings[$setting['URL_CPU']]))
					$this->arSettings[$findex['URL_CPU']] = array(
						"URL_CPU" =>  preg_replace("/\/\//", "/", $findex["URL_CPU"]),
						"LASTMOD"	=> $findex['DATE_ELEMENT'],
					);
			}
		}

		unset($arFIlter);
		return !empty($this->arSettings);
	}
}

?>