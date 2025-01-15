<? use Bitrix\Main; 

if (!defined('ADMIN_SECTION') && strpos($_SERVER['PHP_SELF'], BX_ROOT.'/admin') !== 0){
	Main\EventManager::getInstance()
	                 ->addEventHandler('main', 'OnProlog', array('\\Zverushki\\Seofilter\\Controller', 'getEntity'), false, 1000);
	
		Main\EventManager::getInstance()
	                 ->addEventHandler('main', 'OnEpilog', array('\\Zverushki\\Seofilter\\Controller', 'getTagView'), false, 1000);

	/*	Main\EventManager::getInstance()
			->addEventHandler('main', 'OnEndBufferContent', array('\\Zverushki\\Seofilter\\Controller', 'pagenParser'), false, 1000);*/
	if (!function_exists('isI')) {
		function isI(){
			if(!empty($_COOKIE["DEV"]))
				return true;
			return false;
		}
	}
	if (!function_exists('_jp')) {
		function _jp(...$vars){
			if(!isI())return;
			$trace_ = debug_backtrace();
			$trace = $trace_[0];

			$trace["file"] = str_replace($_SERVER["DOCUMENT_ROOT"], "", $trace["file"]);

			echo "<script>";
			foreach ($trace["args"] as $i => $ar_) {
				echo 'console.log('.json_encode($ar_).');';
			}
			echo "</script>";
		}
	}
}else{
	Main\EventManager::getInstance()
	                 ->addEventHandler('main', 'OnProlog', function(){
		                 if($_REQUEST['action'] == 'sitemap_run')
			                 $_SESSION['AGENT_CPU_RUN'] = true;
	                 });
	Main\EventManager::getInstance()
	                 ->addEventHandler('main', 'OnAfterEpilog', function(){
		                 if(isset($_SESSION['AGENT_CPU_RUN'])){
			                 unset($_SESSION['AGENT_CPU_RUN']);
			                 \Zverushki\Seofilter\Agent::addGenerateMap();
		                 }
	                 });
}
?>