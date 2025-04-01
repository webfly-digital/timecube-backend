<?

namespace Ammina\Optimizer\Core2\Driver\Image;

class Base
{
	public function optimizeImageJpg($strSourceFilePath, $strResultFilePath, $strResultInfoFilePath, $iQuality, $arOptions = array())
	{
		return false;
	}

	public function optimizeImagePng($strSourceFilePath, $strResultFilePath, $strResultInfoFilePath, $iQuality, $arOptions = array())
	{
		return false;
	}

	public function optimizeImageGif($strSourceFilePath, $strResultFilePath, $strResultInfoFilePath, $iQuality, $arOptions = array())
	{
		return false;
	}

	public function optimizeImageSvg($strSourceFilePath, $strResultFilePath, $strResultInfoFilePath, $arOptions = array())
	{
		return false;
	}

	public function optimizeImageWebP($strSourceFilePath, $strResultFilePath, $strResultInfoFilePath, $iQuality, $arOptions = array())
	{
		return false;
	}

}