<?

namespace Ammina\Optimizer\Core;

use ImageOptimizer\OptimizerFactory;

class Img extends Base
{
	protected $arAllowFormatsToWebP = array(
		"jpg",
		"jpeg",
	);

	public function doOptimize(&$strContent)
	{
		global $APPLICATION;
		if (\COption::GetOptionString("ammina.optimizer", "img_active", "N") == "Y" && (!defined("AOZR_IMG_ACTIVE_DISABLE") || AOZR_IMG_ACTIVE_DISABLE !== true)) {
			if (\COption::GetOptionString("ammina.optimizer", "img_src_active", "Y") == "Y") {
				$arMatches = array();
				$regexp = '<img\s[^>]* src="([^\" >]*)"([^\>]*)([^\>]*)(\/>|>)';
				preg_match_all("/" . $regexp . "/im", $strContent, $arMatches, PREG_SET_ORDER);

				foreach ($arMatches as $k => $arMatch) {
					$strNewFile = $this->doOptimizeFile($arMatch[1]);
					$arMatches[$k]['REPLACE'] = false;
					if ($strNewFile !== false) {
						$arMatches[$k]['REPLACE'] = true;
						$arMatches[$k]['NEW_FILE'] = $strNewFile;
						$arIdent[] = $strNewFile;
					}
				}
				foreach ($arMatches as $arMatch) {
					if ($arMatch['REPLACE']) {
						$strNew = str_replace($arMatch[1], $arMatch['NEW_FILE'], $arMatch[0]);
						$strContent = str_replace($arMatch[0], $strNew, $strContent);
					}
				}
			}
			if (\COption::GetOptionString("ammina.optimizer", "img_datasrc_active", "N") == "Y") {
				$arMatches = array();
				$regexp = '<img\s[^>]* data-src="([^\" >]*)"([^\>]*)([^\>]*)(\/>|>)';
				preg_match_all("/" . $regexp . "/im", $strContent, $arMatches, PREG_SET_ORDER);

				foreach ($arMatches as $k => $arMatch) {
					$strNewFile = $this->doOptimizeFile($arMatch[1]);
					$arMatches[$k]['REPLACE'] = false;
					if ($strNewFile !== false) {
						$arMatches[$k]['REPLACE'] = true;
						$arMatches[$k]['NEW_FILE'] = $strNewFile;
						$arIdent[] = $strNewFile;
					}
				}
				foreach ($arMatches as $arMatch) {
					if ($arMatch['REPLACE']) {
						$strNew = str_replace($arMatch[1], $arMatch['NEW_FILE'], $arMatch[0]);
						$strContent = str_replace($arMatch[0], $strNew, $strContent);
					}
				}
			}

			if (\COption::GetOptionString("ammina.optimizer", "img_background_active", "Y") == "Y") {
				$arMatches = array();
				$regexp = 'background[-image].*:[\s]*url\(["|\'|' . preg_quote('&#039;') . '|' . preg_quote('&quot;') . '|' . preg_quote('&apos;') . ']*(.+?)["|\'|' . preg_quote('&#039;') . '|' . preg_quote('&quot;') . '|' . preg_quote('&apos;') . ']*\)';
				preg_match_all("/" . $regexp . "/im", $strContent, $arMatches, PREG_SET_ORDER);
				foreach ($arMatches as $k => $arMatch) {
					if (amopt_strpos($arMatch[1], 'data:') !== 0) {
						$strNewFile = $this->doOptimizeFile($arMatch[1]);
						$arMatches[$k]['REPLACE'] = false;
						if ($strNewFile !== false) {
							$arMatches[$k]['REPLACE'] = true;
							$arMatches[$k]['NEW_FILE'] = $strNewFile;
							$arIdent[] = $strNewFile;
						}
					}
				}

				foreach ($arMatches as $arMatch) {
					if ($arMatch['REPLACE']) {
						$strNew = str_replace($arMatch[1], $arMatch['NEW_FILE'], $arMatch[0]);
						$strContent = str_replace($arMatch[0], $strNew, $strContent);
					}
				}
			}

			if (\COption::GetOptionString("ammina.optimizer", "img_all_links_active", "Y") == "Y") {
				//»щем просто ссылки на папку /upload, заказчивающиес€ на ' или "
				$arMatches = array();
				$regexp = '["|\\\']+(\/upload\/*.*)["|\\\']+';
				preg_match_all("/" . $regexp . "/imU", $strContent, $arMatches, PREG_SET_ORDER);
				foreach ($arMatches as $k => $arMatch) {
					if (amopt_strlen($arMatch[1]) > 0 && amopt_substr($arMatch[1], amopt_strlen($arMatch[1]) - 1) == '\\') {
						$arMatches[$k][1] = amopt_substr($arMatch[1], 0, amopt_strlen($arMatch[1]) - 1);
					}
					$strNewFile = $this->doOptimizeFile($arMatch[1]);
					$arMatches[$k]['REPLACE'] = false;
					if ($strNewFile !== false) {
						$arMatches[$k]['REPLACE'] = true;
						$arMatches[$k]['NEW_FILE'] = $strNewFile;
						$arIdent[] = $strNewFile;
					}
				}

				foreach ($arMatches as $arMatch) {
					if ($arMatch['REPLACE']) {
						$strNew = str_replace($arMatch[1], $arMatch['NEW_FILE'], $arMatch[0]);
						$strContent = str_replace($arMatch[0], $strNew, $strContent);
					}
				}
			}
		}
	}

	public function doOptimizeFile($strFileName, $bAllowWebP = true)
	{
		global $APPLICATION;
		if (amopt_strpos($strFileName, '://') !== false || amopt_strpos($strFileName, '//') === 0) {
			if (\COption::GetOptionString("ammina.optimizer", "img_remote_active", "Y") == "Y") {
				if (\CAmminaOptimizer::doMathPageToRules(\COption::GetOptionString("ammina.optimizer", "img_remote_disable_links", ""), $strFileName)) {
					return false;
				}
				return $this->doOptimizeRemoteFile($strFileName, $bAllowWebP);
			}
			return false;
		}
		$strFileName = Rel2Abs(dirname($APPLICATION->GetCurPage(true)), $strFileName);
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strFileName)) {
			return false;
		}
		if (amopt_strpos($strFileName, "ammina.optimizer") !== false) {
			return false;
		}
		if (\CAmminaOptimizer::doMathPageToRules(\COption::GetOptionString("ammina.optimizer", "img_files_no_optimize", ""), $strFileName)) {
			return false;
		}
		$arPathInfo = ammina_pathinfo($_SERVER['DOCUMENT_ROOT'] . $strFileName);
		$arBasePathInfo = ammina_pathinfo($strFileName);
		$isAllowPngConversion = false;
		$isAllowGifConversion = false;
		$isAllowDirectory = false;
		if (amopt_strpos($arBasePathInfo['dirname'] . "/", "/upload/") === 0) {
			$isAllowDirectory = true;
		}
		if (!$isAllowDirectory) {
			$arExtDir = explode("\n", \COption::GetOptionString("ammina.optimizer", "img_dir_2jpg", ""));
			foreach ($arExtDir as $strExtDir) {
				$strExtDir = trim($strExtDir);
				if (amopt_strlen($strExtDir) > 0) {
					if (amopt_strpos($arBasePathInfo['dirname'] . "/", $strExtDir) === 0) {
						$isAllowDirectory = true;
						break;
					}
				}
			}
		}
		if (amopt_strtolower($arPathInfo['extension']) == "png" && $isAllowDirectory && \COption::GetOptionString("ammina.optimizer", "img_upload_png2jpg_active", "N") == "Y") {
			$isAllowPngConversion = true;
		}
		if (amopt_strtolower($arPathInfo['extension']) == "gif" && $isAllowDirectory && \COption::GetOptionString("ammina.optimizer", "img_upload_gif2jpg_active", "N") == "Y") {
			$isAllowGifConversion = true;
		}
		if (!in_array(amopt_strtolower($arPathInfo['extension']), $this->arAllowFormatsToWebP) && !$isAllowPngConversion && !$isAllowGifConversion) {
			return false;
		}
		if ($bAllowWebP && (in_array(amopt_strtolower($arPathInfo['extension']), $this->arAllowFormatsToWebP) || $isAllowPngConversion || $isAllowGifConversion) && $this->isSupportedWebP()) {
			$strNewFile = "/upload/ammina.optimizer/webp/q" . intval(\COption::GetOptionString("ammina.optimizer", "img_quality", 85)) . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".webp";

			if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strNewFile) || $_REQUEST['amopt_clear_cache'] == "Y") {
				CheckDirPath(dirname($_SERVER['DOCUMENT_ROOT'] . $strNewFile) . "/");
				$obImg = imagecreatefromstring(file_get_contents($_SERVER['DOCUMENT_ROOT'] . $strFileName));
				imagewebp($obImg, $_SERVER['DOCUMENT_ROOT'] . $strNewFile, \COption::GetOptionString("ammina.optimizer", "img_quality", 85));
				imagedestroy($obImg);
				unset($obImg);
			}

			return $strNewFile;
		} elseif ($this->isImagick()) {
			if (in_array(amopt_strtolower($arPathInfo['extension']), array("jpg", "jpeg")) || $isAllowPngConversion || $isAllowGifConversion) {
				$strNewFile = "/upload/ammina.optimizer/opt/q" . intval(\COption::GetOptionString("ammina.optimizer", "img_quality", 85)) . $arBasePathInfo['dirname'] . "/" . $arBasePathInfo['filename'] . ".jpg";

				if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strNewFile) || $_REQUEST['amopt_clear_cache'] == "Y") {
					CheckDirPath(dirname($_SERVER['DOCUMENT_ROOT'] . $strNewFile) . "/");

					try {
						$obImg = new \Imagick($_SERVER['DOCUMENT_ROOT'] . $strFileName);
						$obImg->optimizeImageLayers();
						$obImg->setImageCompression(\Imagick::COMPRESSION_JPEG);
						$obImg->setImageCompressionQuality(\COption::GetOptionString("ammina.optimizer", "img_quality", 85));
						$obImg->setImageFormat('jpg');
						$obImg->setSamplingFactors(array('2x2', '1x1', '1x1'));
						$arProfiles = $obImg->getImageProfiles("icc", true);
						if (!empty($arProfiles)) {
							$obImg->profileImage('icc', $arProfiles['icc']);
						}
						$obImg->setInterlaceScheme(\Imagick::INTERLACE_JPEG);
						$obImg->setColorspace(\Imagick::COLORSPACE_RGB);
						$obImg->writeImage($_SERVER['DOCUMENT_ROOT'] . $strNewFile);
						//\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strNewFile, $obImg->getImageBlob());
						$obImg->destroy();
						unset($obImg);
					} catch (\Exception $e) {

					}
				}
			}
			if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strNewFile)) {
				return $strNewFile;
			}
		}

		return false;
	}

	protected function isSupportedWebP()
	{
		if (!function_exists("imagewebp")) {
			return false;
		}
		if (\COption::GetOptionString("ammina.optimizer", "img_webp_active", "Y") != "Y") {
			return false;
		}
		if (amopt_strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false) {
			return true;
		}
		$iPos = amopt_strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox/');
		if ($iPos !== false) {
			$ver = intval(amopt_substr($_SERVER['HTTP_USER_AGENT'], $iPos + 8, 3));
			if ($ver >= 65) {
				return true;
			}
		}
		return false;
	}

	protected function isImagick()
	{
		return (extension_loaded('imagick') || class_exists("Imagick"));
	}

	protected function doOptimizeRemoteFile($strFileName, $bAllowWebP = true)
	{
		global $APPLICATION;
		$strContent = "";
		$strIdent = md5($strFileName);
		$arUrl = parse_url($strFileName);
		$arPath = ammina_pathinfo($arUrl['path']);
		if (amopt_strlen($arPath['extension']) <= 0) {
			return false;
		}
		$strCacheFile = "/upload/ammina.optremote/" . SITE_ID . "/remotefiles/" . amopt_substr($strIdent, 0, 2) . "/" . amopt_substr($strIdent, 0, 6) . "/" . $strIdent . "." . $arPath['extension'];
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile) || $_REQUEST['amopt_clear_cache'] == "Y") {
			$strContent = \CAmminaOptimizer::doRequestPageRemote($strFileName, '', 10, 5);
			if (amopt_strlen($strContent) > 0) {
				\CAmminaOptimizer::SaveFileContent($_SERVER['DOCUMENT_ROOT'] . $strCacheFile, $strContent);
			}
		}
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strCacheFile)) {
			$res = $this->doOptimizeFile($strCacheFile, $bAllowWebP);
			if ($res !== false) {
				return $res;
			} else {
				return $strCacheFile;
			}
		}
		return false;
	}
}
