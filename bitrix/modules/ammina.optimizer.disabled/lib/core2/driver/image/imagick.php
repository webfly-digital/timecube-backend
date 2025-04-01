<?

namespace Ammina\Optimizer\Core2\Driver\Image;

class Imagick extends Base
{

	public function optimizeImageJpg($strSourceFilePath, $strResultFilePath, $strResultInfoFilePath, $iQuality, $arOptions = array())
	{
		CheckDirPath(dirname($_SERVER['DOCUMENT_ROOT'] . $strResultFilePath) . "/");
		if (!class_exists('\\Imagick')) {
			return false;
		}
		try {
			$obImg = new \Imagick($_SERVER['DOCUMENT_ROOT'] . $strSourceFilePath);
			$obImg->optimizeImageLayers();
			$obImg->setImageCompression(\Imagick::COMPRESSION_JPEG);
			$obImg->setImageCompressionQuality($iQuality);
			$obImg->setImageFormat('jpg');
			$obImg->setSamplingFactors(array('2x2', '1x1', '1x1'));
			$arProfiles = $obImg->getImageProfiles("icc", true);
			if (!empty($arProfiles)) {
				$obImg->profileImage('icc', $arProfiles['icc']);
			}
			$obImg->setInterlaceScheme(\Imagick::INTERLACE_PLANE);
			$obImg->setColorspace(\Imagick::COLORSPACE_RGB);
			$obImg->writeImage($_SERVER['DOCUMENT_ROOT'] . $strResultFilePath);
			$obImg->destroy();
			unset($obImg);
			$arInfo = array(
				"SOURCE" => $strSourceFilePath,
				"RESULT" => $strResultFilePath,
				"SOURCE_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strSourceFilePath),
				"RESULT_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strResultFilePath),
			);
			\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strResultInfoFilePath, serialize($arInfo));
			return $strResultFilePath;
		} catch (\Exception $e) {
		}
		return false;
	}

	public function optimizeImagePng($strSourceFilePath, $strResultFilePath, $strResultInfoFilePath, $iQuality, $arOptions = array())
	{
		CheckDirPath(dirname($_SERVER['DOCUMENT_ROOT'] . $strResultFilePath) . "/");

		try {
			$obImg = new \Imagick($_SERVER['DOCUMENT_ROOT'] . $strSourceFilePath);
			$obImg->optimizeImageLayers();
			$obImg->setImageCompression(\Imagick::COMPRESSION_ZIP);
			$obImg->setImageCompressionQuality($iQuality);
			$obImg->setImageFormat('png');
			//$obImg->setSamplingFactors(array('2x2', '1x1', '1x1'));
			/*$arProfiles = $obImg->getImageProfiles("icc", true);
			if (!empty($arProfiles)) {
				$obImg->profileImage('icc', $arProfiles['icc']);
			}
			$obImg->setInterlaceScheme(\Imagick::INTERLACE_PLANE);
			$obImg->setColorspace(\Imagick::COLORSPACE_RGB);
			*/
			$obImg->writeImage($_SERVER['DOCUMENT_ROOT'] . $strResultFilePath);
			$obImg->destroy();
			unset($obImg);
			$arInfo = array(
				"SOURCE" => $strSourceFilePath,
				"RESULT" => $strResultFilePath,
				"SOURCE_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strSourceFilePath),
				"RESULT_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strResultFilePath),
			);
			\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strResultInfoFilePath, serialize($arInfo));
			return $strResultFilePath;
		} catch (\Exception $e) {
		}
		return false;
	}

	public function optimizeImageGif($strSourceFilePath, $strResultFilePath, $strResultInfoFilePath, $iQuality, $arOptions = array())
	{
		CheckDirPath(dirname($_SERVER['DOCUMENT_ROOT'] . $strResultFilePath) . "/");

		try {
			$obImg = new \Imagick($_SERVER['DOCUMENT_ROOT'] . $strSourceFilePath);
			$obImg->coalesceImages();
			$obImg = $obImg->deconstructImages();
			/*$obImg->optimizeImageLayers();
			$obImg->setImageCompression(\Imagick::COMPRESSION_JPEG);
			$obImg->setImageCompressionQuality($iQuality);
			*/
			$obImg->setImageFormat('gif');
			/*$obImg->setSamplingFactors(array('2x2', '1x1', '1x1'));
			$arProfiles = $obImg->getImageProfiles("icc", true);
			if (!empty($arProfiles)) {
				$obImg->profileImage('icc', $arProfiles['icc']);
			}
			$obImg->setInterlaceScheme(\Imagick::INTERLACE_PLANE);
			$obImg->setColorspace(\Imagick::COLORSPACE_RGB);
			*/
			$obImg->writeImage($_SERVER['DOCUMENT_ROOT'] . $strResultFilePath);
			$obImg->destroy();
			unset($obImg);
			$arInfo = array(
				"SOURCE" => $strSourceFilePath,
				"RESULT" => $strResultFilePath,
				"SOURCE_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strSourceFilePath),
				"RESULT_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strResultFilePath),
			);
			\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strResultInfoFilePath, serialize($arInfo));
			return $strResultFilePath;
		} catch (\Exception $e) {
		}
		return false;
	}

	public function doMakeLazyFileBlur($strFileName, $strLazyFile, $strLazyFileInfo)
	{
		CheckDirPath(dirname($_SERVER['DOCUMENT_ROOT'] . $strLazyFile) . "/");
		$arBasePathInfo = ammina_pathinfo($strFileName);
		$arBasePathInfo['extension'] = amopt_strtolower($arBasePathInfo['extension']);
		try {
			$obImg = new \Imagick($_SERVER['DOCUMENT_ROOT'] . $strFileName);
			$obImg->blurImage(10, 10);
			$obImg->optimizeImageLayers();
			if ($arBasePathInfo['extension'] == "png") {
				$obImg->setImageCompression(\Imagick::COMPRESSION_ZIP);
				$obImg->setImageCompressionQuality(95);
				$obImg->setImageFormat('png');
			} else {
				$obImg->setImageCompression(\Imagick::COMPRESSION_JPEG);
				$obImg->setImageCompressionQuality(30);
				$obImg->setImageFormat('jpg');
				$obImg->setSamplingFactors(array('2x2', '1x1', '1x1'));
				$arProfiles = $obImg->getImageProfiles("icc", true);
				if (!empty($arProfiles)) {
					$obImg->profileImage('icc', $arProfiles['icc']);
				}
				$obImg->setInterlaceScheme(\Imagick::INTERLACE_PLANE);
				$obImg->setColorspace(\Imagick::COLORSPACE_RGB);
			}
			$obImg->writeImage($_SERVER['DOCUMENT_ROOT'] . $strLazyFile);
			$obImg->destroy();
			unset($obImg);
			$arInfo = array(
				"SOURCE" => $strFileName,
				"RESULT" => $strLazyFile,
				"SOURCE_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strFileName),
				"RESULT_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strLazyFile),
			);
			\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strLazyFileInfo, serialize($arInfo));
			return $strLazyFile;
		} catch (\Exception $e) {
		}
		return false;
	}

	public function doMakeLazyFileBlurGrey($strFileName, $strLazyFile, $strLazyFileInfo)
	{
		CheckDirPath(dirname($_SERVER['DOCUMENT_ROOT'] . $strLazyFile) . "/");
		$arBasePathInfo = ammina_pathinfo($strFileName);
		$arBasePathInfo['extension'] = amopt_strtolower($arBasePathInfo['extension']);
		try {
			$obImg = new \Imagick($_SERVER['DOCUMENT_ROOT'] . $strFileName);
			$obImg->blurImage(10, 10);
			$obImg->transformImageColorspace(\Imagick::COLORSPACE_GRAY);
			$obImg->optimizeImageLayers();
			if ($arBasePathInfo['extension'] == "png") {
				$obImg->setImageCompression(\Imagick::COMPRESSION_ZIP);
				$obImg->setImageCompressionQuality(95);
				$obImg->setImageFormat('png');
			} else {
				$obImg->setImageCompression(\Imagick::COMPRESSION_JPEG);
				$obImg->setImageCompressionQuality(30);
				$obImg->setImageFormat('jpg');
				$obImg->setSamplingFactors(array('2x2', '1x1', '1x1'));
				$arProfiles = $obImg->getImageProfiles("icc", true);
				if (!empty($arProfiles)) {
					$obImg->profileImage('icc', $arProfiles['icc']);
				}
				$obImg->setInterlaceScheme(\Imagick::INTERLACE_PLANE);
			}
			$obImg->writeImage($_SERVER['DOCUMENT_ROOT'] . $strLazyFile);
			$obImg->destroy();
			unset($obImg);
			$arInfo = array(
				"SOURCE" => $strFileName,
				"RESULT" => $strLazyFile,
				"SOURCE_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strFileName),
				"RESULT_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strLazyFile),
			);
			\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strLazyFileInfo, serialize($arInfo));
			return $strLazyFile;
		} catch (\Exception $e) {
		}
		return false;
	}
}