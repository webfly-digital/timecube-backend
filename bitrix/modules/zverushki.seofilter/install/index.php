<?php
/**
 * ReMarketing
 *
 * @package Zverushki
 * @subpackage ReMarketing
 * @copyright 2001-2018 Zverushki
 */

use Bitrix\Main,
	Bitrix\Main\Entity,
	Bitrix\Main\Config\Option,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\Application,
	Bitrix\Main\IO\Directory;

Loc::loadMessages(__FILE__);

if (class_exists('zverushki_seofilter'))
	return;


class zverushki_seofilter extends CModule {
	var $MODULE_ID = 'zverushki.seofilter';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_ICON = '/bitrix/images/zverushki.seofilter/logo.jpg';
	var $MODULE_SORT = 1;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = 'Y';
	var $PARTNER_NAME;
	var $PARTNER_URI;
	var $componentPath;


	public function __construct () {
		$arModuleVersion = array();

		$path = str_replace('\\', '/', __FILE__);
		$path = substr($path, 0, strlen($path) - strlen('/index.php'));

		include $path.'/version.php';

		if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}

		// $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS='Y';
		$this->MODULE_NAME = Loc::getMessage('SEOFILTER_MODULE_NAME');
		$this->MODULE_DESCRIPTION = Loc::getMessage('SEOFILTER_MODULE_DESCRIPTION');
		$this->PARTNER_NAME = 'Zverushki';
        $this->PARTNER_URI = 'http://zverushki.ru';
        $this->componentPath = '/bitrix/components/zverushki';
	}

	function DoInstall () {
		global $DB, $APPLICATION;

		$this->installFiles();
		$this->installDB();
		$this->installEvents();

		\CAdminNotify::Add(array(
		   'MESSAGE' => Loc::getMessage('SEOFILTER_PUBLIC_NOTIFY'),
		   'TAG' => 'ZS_INSTALLNOTIFY',
		   'MODULE_ID' => $this->MODULE_ID,
		   'ENABLE_CLOSE' => "N"
		));
	}

	function installDB () {
		global $DB;

		$this->errors = false;

		foreach ($this->getTableList() as $table)
			if (!$DB->query('SELECT \'x\' FROM '.$table['tableName'], true)) {
				$Base = Entity\Base::getInstance($table['className']);
				$Base->createDbTable();

				foreach ($table['className']::getMap() as $fiels) {
					if (intval($fiels->getParameter('size')) > 0 && $fiels->getDataType() == 'string')
						$DB->query('ALTER TABLE ' . $table['tableName'] . '  MODIFY ' . $fiels->getName() . ' VARCHAR(' . $fiels->getParameter('size') . ')'.($fiels->isRequired() ? ' NOT NULL' : ''), true);
				}
				if(method_exists($table['className'], 'addIndex'))
					$table['className']::addIndex();
			}

		if ($this->errors !== false) {
			$GLOBALS['APPLICATION']->throwException(implode('', $this->errors));
			return false;
		}

		registerModule($this->MODULE_ID);

		\CAgent::removeModuleAgents($this->MODULE_ID);

		if (class_exists('CTimeZone'))
			\CTimeZone::disable();

		\CAgent::Add(array(
			'NAME' => '\Zverushki\Seofilter\Agent::updateIndex();',
			'MODULE_ID' => $this->MODULE_ID,
			'ACTIVE' => 'Y',
			'NEXT_EXEC' => date('d.m.Y H:i:s', strtotime('+2 hour')),
			'AGENT_INTERVAL' => 60,
			'IS_PERIOD' => 'N'
		));

		if (class_exists('CTimeZone'))
			\CTimeZone::enable();

		return true;
	}

	function installEvents () {
		$EventManager = Bitrix\Main\EventManager::getInstance();
		$EventManager->registerEventHandler('main', 'OnBeforeProlog', $this->MODULE_ID);

		foreach ($this->getEvents() as $event)
			$EventManager->registerEventHandler($event['0'], $event['1'], $this->MODULE_ID, $event['2'], $event['3']);

		return true;
	}

	function installFiles () {
		copyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/zverushki.seofilter/install/components/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/components/', true, true);
		copyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/zverushki.seofilter/install/images/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/images/', true, true);

		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/admin'))
			if ($dir = opendir($p)) {
				while (false !== $item = readdir($dir)) {
					if ($item == '..' || $item == '.' || $item == 'menu.php')
						continue;

					file_put_contents($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.$this->MODULE_ID.'_'.$item,
					'<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.$this->MODULE_ID.'/admin/'.$item.'");?'.'>');
				}

				closedir($dir);
			}

		CheckDirPath($_SERVER['DOCUMENT_ROOT'].'/bitrix/tools/'.$this->MODULE_ID.'/');
		file_put_contents($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/tools/'.$this->MODULE_ID.'/argument.php',
				'<'.'? $argument["time"] = '.time().';?'.'>');

		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/tools'))
			if ($dir = opendir($p)) {
				CheckDirPath($_SERVER['DOCUMENT_ROOT'].'/bitrix/tools/'.$this->MODULE_ID.'/');

				while (false !== $item = readdir($dir)) {
					if ($item == '..' || $item == '.')
						continue;

					file_put_contents($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/tools/'.$this->MODULE_ID.'/'.$item,
					'<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.$this->MODULE_ID.'/tools/'.$item.'");?'.'>');
				}

				closedir($dir);
			}

		return true;
	}

	function DoUninstall()
	{
		global $APPLICATION, $step;
		$step = intval($step);
		if($step<2)
			$APPLICATION->IncludeAdminFile(Loc::getMessage("SEOFILTER_MODULE_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/zverushki.seofilter/install/unstep1.php");
		elseif($step==2)
		{
			$this->UnInstallDB(array(
				"savedata" => $_REQUEST["savedata"],
			));

			$this->uninstallFiles();
			$this->uninstallEvents();
		}
	}

	function uninstallDB ($arParams = array()) {
		if ($arParams['savedata'] != 'Y') {
			Option::delete($this->MODULE_ID);
			$Connection = Application::getConnection();

			foreach ($this->getTableList() as $table)
				$Connection->query('DROP TABLE IF EXISTS `'.$table['tableName'].'`');
		}

		unregisterModule($this->MODULE_ID);

		return true;
	}

	function uninstallEvents () {
		$EventManager = Bitrix\Main\EventManager::getInstance();

		foreach ($this->getEvents() as $event)
			$EventManager->unregisterEventHandler($event['0'], $event['1'], $this->MODULE_ID, $event['2'], $event['3']);

		$EventManager->unregisterEventHandler('main', 'OnBeforeProlog', $this->MODULE_ID);

		return true;
	}

	function uninstallFiles () {
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/admin'))
			if ($dir = opendir($p)) {
				while (false !== $item = readdir($dir)) {
					if ($item == '..' || $item == '.' || $item == 'menu.php')
						continue;

					unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.$this->MODULE_ID.'_'.$item);
				}

				closedir($dir);
			}
		Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . '/bitrix/images/'.$this->MODULE_ID);
		Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . '/bitrix/tools/'.$this->MODULE_ID);
		Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . $this->componentPath.'/seofilter.tag');
		Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . $this->componentPath.'/seofilter.tag.section');
		$is_empty=count(glob($_SERVER['DOCUMENT_ROOT'] . $this->componentPath.'/*')) ? false : true;
		if($is_empty)
			Directory::deleteDirectory($_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/zverushki');

		return true;
	}

	private function getTableList () {
		$Directory = Directory::createDirectory(__DIR__.'/../lib/internals');
		$tables = array();

		if ($Directory->isExists()) {
			foreach ($Directory->getChildren() as $File)
				if ($File->isFile() && $File->getExtension() == 'php') {
					require_once $File->getPath();

					$fileName = substr($File->getName(), 0, strlen($File->getName()) - 4);
					$className = '\Zverushki\Seofilter\Internals\\'.$fileName.'table';

					$tables[] = array(
						'className' => $className,
						'tableName' => $className::getTableName(),
						'File' => $File
					);
				}
		}

		return $tables;
	}

	private function getEvents () {
		$events = array();

		/*foreach (array(
			array('mid' => 'main', 'method' => 'OnProlog'),
			array('mid' => 'sale', 'method' => 'OnSaleBasketSaved'),
			array('mid' => 'sale', 'method' => 'OnSaleOrderSaved'),
			array('mid' => 'sale', 'method' => 'OnOrderSave'),
			array('mid' => 'sale', 'method' => 'OnSaleBasketBeforeSaved'),
			array('mid' => 'sale', 'method' => 'OnBasketDelete'),
			array('mid' => 'sale', 'method' => 'OnBeforeBasketDelete')
		) as $E)
			$events[] = array(
				'0' => $E['mid'],
				'1' => $E['method'],
				'2' => '\Zverushki\Remarketing\Event\Base',
				'3' => 'sendEventHandler'
			);*/

		return $events;
	}
}
?>