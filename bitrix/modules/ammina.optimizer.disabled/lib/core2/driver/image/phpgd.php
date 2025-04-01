<?

namespace Ammina\Optimizer\Core2\Driver\Image;

class PhpGD extends Base
{
	public function optimizeImageWebP($strSourceFilePath, $strResultFilePath, $strResultInfoFilePath, $iQuality, $arOptions = array())
	{
		CheckDirPath(dirname($_SERVER['DOCUMENT_ROOT'] . $strResultFilePath) . "/");
		$arPath = ammina_pathinfo($_SERVER['DOCUMENT_ROOT'] . $strSourceFilePath);
		if (in_array(amopt_strtolower($arPath['extension']), array("jpg", "jpeg"))) {
			$oImg = imagecreatefromstring(file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strSourceFilePath));
			if ($oImg) {
				imagewebp($oImg, $_SERVER['DOCUMENT_ROOT'] . $strResultFilePath, $iQuality);
				imagedestroy($oImg);
				$arInfo = array(
					"SOURCE" => $strSourceFilePath,
					"RESULT" => $strResultFilePath,
					"SOURCE_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strSourceFilePath),
					"RESULT_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strResultFilePath),
				);
				\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strResultInfoFilePath, serialize($arInfo));
				return $strResultFilePath;
			}
		} elseif (in_array(amopt_strtolower($arPath['extension']), array("png"))) {
			$oImg = imagecreatefromstring(file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strSourceFilePath));
			if ($oImg) {
				imagesavealpha($oImg, true);
				imagewebp($oImg, $_SERVER['DOCUMENT_ROOT'] . $strResultFilePath, $iQuality);
				imagedestroy($oImg);
				$arInfo = array(
					"SOURCE" => $strSourceFilePath,
					"RESULT" => $strResultFilePath,
					"SOURCE_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strSourceFilePath),
					"RESULT_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strResultFilePath),
				);
				\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strResultInfoFilePath, serialize($arInfo));
				return $strResultFilePath;
			}
		} elseif (in_array(amopt_strtolower($arPath['extension']), array("gif"))) {
			$oImg = imagecreatefromstring(file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strSourceFilePath));
			if ($oImg) {
				imagesavealpha($oImg, true);
				imagewebp($oImg, $_SERVER['DOCUMENT_ROOT'] . $strResultFilePath, $iQuality);
				imagedestroy($oImg);
				$arInfo = array(
					"SOURCE" => $strSourceFilePath,
					"RESULT" => $strResultFilePath,
					"SOURCE_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strSourceFilePath),
					"RESULT_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strResultFilePath),
				);
				\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strResultInfoFilePath, serialize($arInfo));
				return $strResultFilePath;
			}
		}
		return false;
	}

	public function convertToJpg($strSourceFilePath, $strResultFilePath, $strResultInfoFilePath, $arOptions = array())
	{
		CheckDirPath(dirname($_SERVER['DOCUMENT_ROOT'] . $strResultFilePath) . "/");
		$arPath = ammina_pathinfo($_SERVER['DOCUMENT_ROOT'] . $strSourceFilePath);
		if (in_array(amopt_strtolower($arPath['extension']), array("jpg", "jpeg", "png", "gif"))) {
			$arColorTo = array();
			if (!isset($arOptions['BG_COLOR'])) {
				$arColorTo = array(255, 255, 255);
			} else {
				$arColorTo = array(
					hexdec(amopt_substr($arOptions['BG_COLOR'], 0, 2)),
					hexdec(amopt_substr($arOptions['BG_COLOR'], 2, 2)),
					hexdec(amopt_substr($arOptions['BG_COLOR'], 4, 2)),
				);
				foreach ($arColorTo as &$iCol) {
					if ($iCol < 0 || $iCol > 255) {
						$iCol = 255;
					}
				}
			}
			$oImg = imagecreatefromstring(file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strSourceFilePath));
			if ($oImg) {
				imagesavealpha($oImg, true);
				$w = imagesx($oImg);
				$h = imagesy($oImg);
				$oImgTo = imagecreatetruecolor($w, $h);
				if ($oImgTo) {
					$color = imagecolorallocate($oImgTo, $arColorTo[0], $arColorTo[1], $arColorTo[2]);
					imagefill($oImgTo, 0, 0, $color);
					imagecopyresampled($oImgTo, $oImg, 0, 0, 0, 0, $w, $h, $w, $h);
					imagejpeg($oImgTo, $_SERVER['DOCUMENT_ROOT'] . $strResultFilePath, 100);
					imagedestroy($oImgTo);
					imagedestroy($oImg);
					$arInfo = array(
						"SOURCE" => $strSourceFilePath,
						"RESULT" => $strResultFilePath,
						"SOURCE_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strSourceFilePath),
						"RESULT_SIZE" => filesize($_SERVER['DOCUMENT_ROOT'] . $strResultFilePath),
					);
					\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strResultInfoFilePath, serialize($arInfo));
					return $strResultFilePath;
				}
				imagedestroy($oImg);
			}
		}
		return false;
	}

	public function doMakeLazyFileBlur($strFileName, $strLazyFile, $strLazyFileInfo)
	{
		CheckDirPath(dirname($_SERVER['DOCUMENT_ROOT'] . $strLazyFile) . "/");
		$arBasePathInfo = ammina_pathinfo($strFileName);
		$arBasePathInfo['extension'] = amopt_strtolower($arBasePathInfo['extension']);
		try {
			$obImg = imagecreatefromstring(file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strFileName));
			imagesavealpha($obImg, true);
			imagefilter($obImg, IMG_FILTER_SELECTIVE_BLUR);
			if ($arBasePathInfo['extension'] == "png") {
				imagepng($obImg, $_SERVER['DOCUMENT_ROOT'] . $strLazyFile, 9);
			} else {
				imagejpeg($obImg, $_SERVER['DOCUMENT_ROOT'] . $strLazyFile, 30);
			}
			imagedestroy($obImg);
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
			$obImg = imagecreatefromstring(file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strFileName));
			imagesavealpha($obImg, true);
			imagefilter($obImg, IMG_FILTER_GRAYSCALE);
			imagefilter($obImg, IMG_FILTER_SELECTIVE_BLUR);
			if ($arBasePathInfo['extension'] == "png") {
				imagepng($obImg, $_SERVER['DOCUMENT_ROOT'] . $strLazyFile, 9);
			} else {
				imagejpeg($obImg, $_SERVER['DOCUMENT_ROOT'] . $strLazyFile, 30);
			}
			imagedestroy($obImg);
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
