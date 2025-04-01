<?

namespace Ammina\Optimizer\Core2\Optimizer;

use Ammina\Optimizer\Core2\Application;
use Bitrix\Main\Composite\Engine;
use Bitrix\Main\Composite\Helper;

class Html
{
	protected $arOptions = false;

	public function __construct($arOptions)
	{
		$this->setOptions($arOptions);
	}

	public function setOptions($arOptions)
	{
		$this->arOptions = $arOptions;
		/*
		$this->strBaseIdent = $arOptions;
		unset($this->strBaseIdent['jpg_files']['DEFAULT']);
		unset($this->strBaseIdent['png_files']['DEFAULT']);
		unset($this->strBaseIdent['gif_files']['DEFAULT']);
		unset($this->strBaseIdent['svg_files']['DEFAULT']);
		unset($this->strBaseIdent['external_images']['DEFAULT']);
		unset($this->strBaseIdent['lazy']['DEFAULT']);
		unset($this->strBaseIdent['other']['DEFAULT']);
		unset($this->strBaseIdent['jpg_files']['options']['EXCLUDE_FILES']);
		unset($this->strBaseIdent['jpg_files']['options']['INCLUDE_FILES']);
		unset($this->strBaseIdent['jpg_files']['options']['USE_EVENTS_CHANGE']);
		unset($this->strBaseIdent['jpg_files']['options']['USE_EVENTS_CHANGE']);

		unset($this->strBaseIdent['png_files']['options']['EXCLUDE_FILES']);
		unset($this->strBaseIdent['png_files']['options']['INCLUDE_FILES']);
		unset($this->strBaseIdent['png_files']['options']['USE_EVENTS_CHANGE']);
		unset($this->strBaseIdent['png_files']['options']['USE_EVENTS_CHANGE']);
		unset($this->strBaseIdent['png_files']['options']['EXCLUDE_CONVERT_FILES']);
		unset($this->strBaseIdent['png_files']['options']['INCLUDE_CONVERT_FILES']);

		unset($this->strBaseIdent['gif_files']['options']['EXCLUDE_FILES']);
		unset($this->strBaseIdent['gif_files']['options']['INCLUDE_FILES']);
		unset($this->strBaseIdent['gif_files']['options']['USE_EVENTS_CHANGE']);
		unset($this->strBaseIdent['gif_files']['options']['USE_EVENTS_CHANGE']);
		unset($this->strBaseIdent['gif_files']['options']['EXCLUDE_CONVERT_FILES']);
		unset($this->strBaseIdent['gif_files']['options']['INCLUDE_CONVERT_FILES']);

		unset($this->strBaseIdent['svg_files']['options']['EXCLUDE_FILES']);
		unset($this->strBaseIdent['svg_files']['options']['INCLUDE_FILES']);

		unset($this->strBaseIdent['external_images']['options']['EXCLUDE']);
		unset($this->strBaseIdent['external_images']['options']['INCLUDE']);

		$this->strBaseIdent = md5(serialize($this->strBaseIdent));
		*/
	}

	public function doOptimize()
	{
		if ($this->arOptions['tags']['options']['ACTIVE'] == "Y") {
			if ($this->arOptions['tags']['options']['REMOVE_PRE'] == "Y") {
				Application::getInstance()->getParser()->removePreTag();
			}
			if (class_exists('\\Bitrix\\Main\\Composite\\Helper')) {
				if (!Helper::isOn() && $this->arOptions['tags']['options']['REMOVE_COMMENTS'] == "Y") {
					Application::getInstance()->getParser()->removeComments();
				}
			}
			if ($this->arOptions['tags']['options']['REMOVE_ATTR_SCRIPT'] == "Y") {
				Application::getInstance()->getParser()->removeScriptAttr();
			}
			if ($this->arOptions['tags']['options']['REMOVE_ATTR_STYLE'] == "Y") {
				Application::getInstance()->getParser()->removeStyleAttr();
			}
			if ($this->arOptions['tags']['options']['REMOVE_WHITE_SPACE'] == "Y") {
				Application::getInstance()->getParser()->setOutputMinified(true);
				Application::getInstance()->getParser()->removeWhiteSpace();
			} else {
				Application::getInstance()->getParser()->setOutputMinified(false);
			}
		}
	}
}

