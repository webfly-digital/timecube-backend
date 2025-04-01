<?

namespace Ammina\Optimizer\Core2\Optimizer\Image;

class Base
{
	protected $arOptions = false;

	public function setOptions($arOptions)
	{
		$this->arOptions = $arOptions;
	}

	public function doOptimizeImage($strFilePath)
	{
		return $strFilePath;
	}

	public function doConvertAndOptimizeImage($strFilePath, $arConvertOptions = array())
	{
		return $strFilePath;
	}
}