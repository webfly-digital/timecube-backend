<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if (isset($includeAreaName) && strlen($includeAreaName)>0){

	if (CModule::IncludeModule('alexkova.bxready2')){
		$elementarArea = \Alexkova\Bxready2\Elementars::getArea('catalog.element','element.'.$includeAreaName.'.prolog');
		if (strlen($elementarArea) > 0)
			include($elementarArea);

		$elementarArea = \Alexkova\Bxready2\Elementars::getArea('catalog.element','element.'.$includeAreaName);
		if (strlen($elementarArea) > 0)
			include($elementarArea);
		else
			include $includeAreaName.'.php';

		$elementarArea = \Alexkova\Bxready2\Elementars::getArea('catalog.element','element.'.$includeAreaName.'.epilog');
		if (strlen($elementarArea) > 0)
			include($elementarArea);
	}else{
		include $includeAreaName.'.php';
	}


}
$includeAreaName = '';
?>