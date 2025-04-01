<?

$_SERVER['HTTP_X_ORIGINAL_QUERY'] = $_SERVER['QUERY_STRING'];
include_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/ammina.optimizer/mbfunc.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/ammina.optimizer/lib/ext/mobiledetect/Mobile_Detect.php");
$oDetector = new AMOPT_Mobile_Detect();
$bSupportWebP = false;
$bMainSupportWebp = false;
if (strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false) {
	$bMainSupportWebp = true;
}
$bIsLightHouse = (strpos($_SERVER['USER_AGENT'], 'Chrome-Lighthouse') !== false);
$iOsVersion = str_replace("_", ".", $oDetector->version("iOS"));
$imacOsVersion = str_replace("_", ".", $oDetector->version("macOS"));
$arBrowsers = array(
	'IE',
	'Edge',
	'Firefox',
	'Chrome',
	'Safari',
	'Opera',
	'Opera Mini',
	'UCBrowser',
	'SamsungBrowser',
);
foreach ($arBrowsers as $browser) {
	$strVersion = $oDetector->version($browser);
	if ($browser == "Safari") {
		if (($oDetector->isiOS() || $oDetector->ismacOS()) && strlen($strVersion) > 0) {
			$strBrowser = $browser;
			$strBrowserVersion = $strVersion;
			break;
		}
	} elseif (strlen($strVersion) > 0) {
		$strBrowser = $browser;
		$strBrowserVersion = $strVersion;
		break;
	}
}
switch ($strBrowser) {
	case "Edge":
		if (version_compare($strBrowserVersion, '18', '>=')) {
			$bSupportWebP = true;
		}
		break;
	case "Firefox":
		if (version_compare($strBrowserVersion, '65', '>=')) {
			$bSupportWebP = true;
		}
		break;
	case "Chrome":
		if (version_compare($strBrowserVersion, '32', '>=')) {
			$bSupportWebP = true;
		}
		break;
	case "Safari":
		$bMainSupportWebp = false;
		if (version_compare($strBrowserVersion, '14.0.1', '>=')) {
			$bSupportWebP = true;
		}
	case "Opera":
		if (version_compare($strBrowserVersion, '19', '>=')) {
			$bSupportWebP = true;
		}
		break;
	case "Opera Mini":
		if (version_compare($strBrowserVersion, '1', '>=')) {
			$bSupportWebP = true;
		}
		break;
	case "UCBrowser":
		if (version_compare($strBrowserVersion, '11.8', '>=')) {
			$bSupportWebP = true;
		}
		break;
	case "SamsungBrowser":
		if (version_compare($strBrowserVersion, '4', '>=')) {
			$bSupportWebP = true;
		}
		break;
}
if ($strBrowser == "Safari") {
	if ($oDetector->ismacOS() && version_compare($imacOsVersion, '11', '<')) {
		$bSupportWebP = false;
	}
} else {
	if ($oDetector->isiOS() && version_compare($iOsVersion, '14', '<')) {
		$bSupportWebP = false;
	}
}
if ($bMainSupportWebp) {
	$bSupportWebP = true;
}
if ($bIsLightHouse) {
	$bSupportWebP = true;
}
if ($bSupportWebP) {
	$strWebP = "iswebp";
	if (strpos($_SERVER["REQUEST_URI"], '?') === false) {
		$_SERVER["REQUEST_URI"] .= "?" . $strWebP;
	} else {
		$_SERVER["REQUEST_URI"] .= "&" . $strWebP;
	}
}
