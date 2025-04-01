<?
namespace Zverushki\Seofilter\Sitemap;

use Zverushki\Seofilter\configuration;
/**
 *
 */
class Xml extends Generate
{
	private $xml = '';

	public function getFile()
	{
		$this->xml = '';
		if($this->get()){
			$arr = array(
				"maintag" => "urlset",
				"inctag" => "url",
				"keys" => "_files",
				"list" => $this->arSettings
			);
			$this->xml = $this->getXml($arr);
		}


		return $this->xml;
	}

	private function getXml($arr){
		$xml = '<?xml version="1.0" encoding="UTF-8"?><'.$arr["maintag"].' xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
			foreach ($arr["list"] as $val):
				$urls = explode("?", $val["URL_CPU"]);
                $nowDate = new \DateTime();
				$xml .= '<'.$arr["inctag"].'>
							    <loc>'.$this->serverName.$urls[0].'</loc>
							    <lastmod>'.date("c", $nowDate->getTimestamp()) /*date("c", $val["LASTMOD"]->getTimestamp())*/.'</lastmod>
							  </'.$arr["inctag"].'>';
			endforeach;
		$xml .= '</'.$arr["maintag"].'>';
		return $xml;
	}
	public function save($nameXml){
		if(empty($this->siteId))
			return;

		$nameXml = str_replace('//', '/', $nameXml);
		$file = fopen($this->documentRoot.$nameXml, "w+");
		$echo = fwrite($file, $this->xml);
		fclose($file);


		return array("DETAIL_PAGE_URL" => $nameXml, "LASTMOD" => time());
	}

	public function changeMain($urlXml){
		$notIntegrate = configuration::getOption('integrate_notactive', $this->siteId) == 'Y';
		if($notIntegrate)
			return;

		$filename = trim($this->documentRoot.$this->siteMapUrl);
		$xml = simplexml_load_file($filename);
		$array = json_decode(json_encode($xml), true);

		if($array && empty($array['sitemap'][0])){
			$tmp = $array['sitemap'];
			unset($array['sitemap']);
			$array['sitemap'][] = $tmp;
		}

		$infile = false;
		foreach ($array['sitemap'] as $key => &$sitemap) {
			if($sitemap['loc'] == $this->serverName.$urlXml['DETAIL_PAGE_URL']){
				$sitemap['lastmod'] = date("c", $urlXml['LASTMOD']);
				$infile = true;
				break;
			}
		}
		if(!$infile)
			$array['sitemap'][] = array('loc' => $this->serverName.$urlXml['DETAIL_PAGE_URL'], 'lastmod' => date("c", $urlXml['LASTMOD']));


		$xml = '<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
			foreach ($array['sitemap'] as $smap):
				$xml .= '<sitemap><loc>'.$smap['loc'].'</loc>'.($smap['lastmod'] ? '<lastmod>'.$smap['lastmod'].'</lastmod>' : '').'</sitemap>';
			endforeach;
		$xml .= '</sitemapindex>';

		$file = fopen($filename, "w+");
		if(!$file)
			return;
		$echo = fwrite($file, $xml);
		fclose($file);
	}

}
?>