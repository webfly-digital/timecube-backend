<?

namespace Ammina\Optimizer\Core2;

use Bitrix\Main\Composite\Helper;

class LibAvailable
{
	static public function doCheckLibrary()
	{
		$arResult = array(
			"bxoptions" => array(
				/*"optimize_css_files" => false,
				"optimize_js_files" => false,
				"use_minified_assets" => false,
				"move_js_to_body" => false,
				"compres_css_js_files" => false,*/
				"cdn" => false,
				"composite" => false,
			),
			"packages" => array(
				"dom" => false,
				"curl" => false,
				"phpimagick" => false,
				//"phpimagickwebp" => false,
				"phpgd" => false,
				"jpegoptim" => false,
				"optipng" => false,
				"pngquant" => false,
				"gifsicle" => false,
				"svgo" => false,
				"cwebp" => false,
			),
			"npm" => array(
				/*"yuicompressor" => false,
				"uglifycss" => false,*/
				"uglifyjs" => false,
				"uglifyjs2" => false,
				"terserjs" => false,
				"babelminify" => false,
			),
			"other" => array(
				"googleapi" => false,
				"amminaapi" => false,
			),
		);
		/*$arResult['bxoptions']['optimize_css_files'] = self::doCheckBxOptionsOptimizeCssFiles();
		$arResult['bxoptions']['optimize_js_files'] = self::doCheckBxOptionsOptimizeJsFiles();
		$arResult['bxoptions']['use_minified_assets'] = self::doCheckBxOptionsUseMinifiedAssets();
		$arResult['bxoptions']['move_js_to_body'] = self::doCheckBxOptionsMoveJsToBody();
		$arResult['bxoptions']['compres_css_js_files'] = self::doCheckBxOptionsCompresCssJsFiles();*/
		$arResult['bxoptions']['cdn'] = self::doCheckBxOptionsCdn();
		$arResult['bxoptions']['composite'] = self::doCheckBxOptionsComposite();

		$arResult['packages']['dom'] = self::doCheckDom();
		$arResult['packages']['curl'] = self::doCheckCurl();
		$arResult['packages']['phpimagick'] = self::doCheckIMagick();
		//$arResult['packages']['phpimagickwebp'] = self::doCheckIMagickWebP();
		$arResult['packages']['phpgd'] = self::doCheckGD();
		$arResult['packages']['jpegoptim'] = self::doCheckJPEGOptim();
		$arResult['packages']['optipng'] = self::doCheckOptiPNG();
		$arResult['packages']['pngquant'] = self::doCheckPNGQuant();
		$arResult['packages']['gifsicle'] = self::doCheckGIFSicle();
		$arResult['npm']['svgo'] = self::doCheckSVGO();
		$arResult['packages']['cwebp'] = self::doCheckCWebP();

		/*$arResult['npm']['yuicompressor'] = self::doCheckYUICompressor();
		$arResult['npm']['uglifycss'] = self::doCheckUglifyCSS();*/
		$arResult['npm']['uglifyjs'] = self::doCheckUglifyJS();
		$arResult['npm']['uglifyjs2'] = self::doCheckUglifyJS2();
		$arResult['npm']['terserjs'] = self::doCheckTerserJS();
		$arResult['npm']['babelminify'] = self::doCheckBabelMinify();

		$arResult['other']['googleapi'] = self::doCheckGoogleAPI();
		$arResult['other']['amminaapi'] = self::doCheckAmminaAPI();

		\COption::SetOptionString("ammina.optimizer", "libs_checked", "Y");
		\COption::SetOptionString("ammina.optimizer", "libs_checked_data", serialize($arResult));

		return $arResult;
	}

	static public function doCheckDom()
	{
		return class_exists("DOMDocument");
	}

	static public function doCheckCurl()
	{
		return function_exists("curl_init");
	}

	static public function doCheckIMagick()
	{
		return class_exists("Imagick");
	}

	/*
		static public function doCheckIMagickWebP()
		{
			if (class_exists("Imagick"))
			{
				try {
					$oImg = new \Imagick($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/ammina.optimizer/test/ammina.optimizer.test.2.png");
					$oImg->optimizeImageLayers();
					$oImg->setImageFormat("webp");
				} catch (\Exception $e)
				{
					$oImg->destroy();
					unset($oImg);
					return false;
				}
				$oImg->destroy();
				unset($oImg);
				return true;
			}
			return false;
		}
	*/
	static public function doCheckGD()
	{
		return function_exists("imagejpeg");
	}

	static public function doCheckJPEGOptim()
	{
		$strToPath = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/tmp/ammina.optimizer.test.1.jpg";
		$strFromPath = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/ammina.optimizer/test/ammina.optimizer.test.1.jpg";
		CheckDirPath($_SERVER['DOCUMENT_ROOT'] . "/bitrix/tmp/");
		if (file_exists($strToPath)) {
			@unlink($strToPath);
		}
		$strCommand = \COption::GetOptionString("ammina.optimizer", "lib_path_jpegoptim", "jpegoptim") . ' --dest=' . dirname($strToPath) . '/ -m75 -s --all-progressive ' . $strFromPath;
		@exec($strCommand);
		if (file_exists($strToPath) && filesize($strToPath) > 0) {
			return true;
		}
		return false;
	}

	static public function doCheckOptiPNG()
	{
		$strToPath = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/tmp/ammina.optimizer.test.2.png";
		$strFromPath = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/ammina.optimizer/test/ammina.optimizer.test.2.png";
		CheckDirPath($_SERVER['DOCUMENT_ROOT'] . "/bitrix/tmp/");
		if (file_exists($strToPath)) {
			@unlink($strToPath);
		}
		$strCommand = \COption::GetOptionString("ammina.optimizer", "lib_path_optipng", "optipng") . ' -o2 -out=' . $strToPath . ' ' . $strFromPath;
		@exec($strCommand);
		if (file_exists($strToPath) && filesize($strToPath) > 0) {
			return true;
		}
		return false;
	}

	static public function doCheckPNGQuant()
	{
		$strToPath = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/tmp/ammina.optimizer.test.3.png";
		$strFromPath = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/ammina.optimizer/test/ammina.optimizer.test.2.png";
		CheckDirPath($_SERVER['DOCUMENT_ROOT'] . "/bitrix/tmp/");
		if (file_exists($strToPath)) {
			@unlink($strToPath);
		}
		$strCommand = \COption::GetOptionString("ammina.optimizer", "lib_path_pngquant", "pngquant") . ' --output ' . $strToPath . ' -- ' . $strFromPath;
		@exec($strCommand);
		if (file_exists($strToPath) && filesize($strToPath) > 0) {
			return true;
		}
		return false;
	}

	static public function doCheckGIFSicle()
	{
		$strToPath = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/tmp/ammina.optimizer.test.5.gif";
		$strFromPath = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/ammina.optimizer/test/ammina.optimizer.test.5.gif";
		CheckDirPath($_SERVER['DOCUMENT_ROOT'] . "/bitrix/tmp/");
		if (file_exists($strToPath)) {
			@unlink($strToPath);
		}
		$strCommand = \COption::GetOptionString("ammina.optimizer", "lib_path_gifsicle", "gifsicle") . ' -o ' . $strToPath . ' --optimize=9 ' . $strFromPath;
		@exec($strCommand);
		if (file_exists($strToPath) && filesize($strToPath) > 0) {
			return true;
		}
		return false;
	}

	static public function doCheckSVGO()
	{
		$strToPath = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/tmp/ammina.optimizer.test.6.svg";
		$strFromPath = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/ammina.optimizer/test/ammina.optimizer.test.6.svg";
		CheckDirPath($_SERVER['DOCUMENT_ROOT'] . "/bitrix/tmp/");
		if (file_exists($strToPath)) {
			@unlink($strToPath);
		}
		$strCommand = \COption::GetOptionString("ammina.optimizer", "lib_path_svgo", "svgo") . ' -o ' . $strToPath . ' -i ' . $strFromPath;
		@exec($strCommand);
		if (file_exists($strToPath) && filesize($strToPath) > 0) {
			return true;
		}
		return false;
	}

	static public function doCheckCWebP()
	{
		$strToPath = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/tmp/ammina.optimizer.test.2.webp";
		$strFromPath = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/ammina.optimizer/test/ammina.optimizer.test.2.png";
		CheckDirPath($_SERVER['DOCUMENT_ROOT'] . "/bitrix/tmp/");
		if (file_exists($strToPath)) {
			@unlink($strToPath);
		}
		$strCommand = \COption::GetOptionString("ammina.optimizer", "lib_path_cwebp", "cwebp") . ' -q 75 ' . $strFromPath . ' -o ' . $strToPath;
		@exec($strCommand);
		if (file_exists($strToPath) && filesize($strToPath) > 0) {
			return true;
		}
		return false;
	}

	static public function doCheckYUICompressor()
	{
		$strToPath = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/tmp/ammina.optimizer.test.4.min.css";
		$strFromPath = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/ammina.optimizer/test/ammina.optimizer.test.3.css";
		CheckDirPath($_SERVER['DOCUMENT_ROOT'] . "/bitrix/tmp/");
		if (file_exists($strToPath)) {
			@unlink($strToPath);
		}
		$strCommand = \COption::GetOptionString("ammina.optimizer", "lib_path_yuicompressor", "yuicompressor") . ' --type css -o ' . $strToPath . ' ' . $strFromPath;
		@exec($strCommand);
		if (file_exists($strToPath) && filesize($strToPath) > 0) {
			return true;
		}
		return false;
	}

	static public function doCheckUglifyCSS()
	{
		$strToPath = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/tmp/ammina.optimizer.test.5.min.css";
		$strFromPath = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/ammina.optimizer/test/ammina.optimizer.test.3.css";
		CheckDirPath($_SERVER['DOCUMENT_ROOT'] . "/bitrix/tmp/");
		if (file_exists($strToPath)) {
			@unlink($strToPath);
		}
		$strCommand = \COption::GetOptionString("ammina.optimizer", "lib_path_uglifycss", "uglifycss") . ' --output ' . $strToPath . ' ' . $strFromPath;
		@exec($strCommand);
		if (file_exists($strToPath) && filesize($strToPath) > 0) {
			return true;
		}
		return false;
	}

	static public function doCheckUglifyJS()
	{
		$strToPath = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/tmp/ammina.optimizer.test.6.min.js";
		$strFromPath = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/ammina.optimizer/test/ammina.optimizer.test.4.js";
		CheckDirPath($_SERVER['DOCUMENT_ROOT'] . "/bitrix/tmp/");
		if (file_exists($strToPath)) {
			@unlink($strToPath);
		}
		$strCommand = \COption::GetOptionString("ammina.optimizer", "lib_path_uglifyjs", "uglifyjs") . ' -m -o ' . $strToPath . ' ' . $strFromPath;
		@exec($strCommand);
		if (file_exists($strToPath) && filesize($strToPath) > 0) {
			return true;
		}
		return false;
	}

	static public function doCheckUglifyJS2()
	{
		$strToPath = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/tmp/ammina.optimizer.test.7.min.js";
		$strFromPath = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/ammina.optimizer/test/ammina.optimizer.test.4.js";
		CheckDirPath($_SERVER['DOCUMENT_ROOT'] . "/bitrix/tmp/");
		if (file_exists($strToPath)) {
			@unlink($strToPath);
		}
		$strCommand = \COption::GetOptionString("ammina.optimizer", "lib_path_uglifyjs2", "uglifyjs2") . ' -m -o ' . $strToPath . ' ' . $strFromPath;
		@exec($strCommand);
		if (file_exists($strToPath) && filesize($strToPath) > 0) {
			return true;
		}
		return false;
	}

	static public function doCheckTerserJS()
	{
		$strToPath = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/tmp/ammina.optimizer.test.8.min.js";
		$strFromPath = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/ammina.optimizer/test/ammina.optimizer.test.4.js";
		CheckDirPath($_SERVER['DOCUMENT_ROOT'] . "/bitrix/tmp/");
		if (file_exists($strToPath)) {
			@unlink($strToPath);
		}
		$strCommand = \COption::GetOptionString("ammina.optimizer", "lib_path_terserjs", "terser") . ' -m -o ' . $strToPath . ' ' . $strFromPath;
		@exec($strCommand);
		if (file_exists($strToPath) && filesize($strToPath) > 0) {
			return true;
		}
		return false;
	}

	static public function doCheckBabelMinify()
	{
		$strToPath = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/tmp/ammina.optimizer.test.9.min.js";
		$strFromPath = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/ammina.optimizer/test/ammina.optimizer.test.4.js";
		CheckDirPath($_SERVER['DOCUMENT_ROOT'] . "/bitrix/tmp/");
		if (file_exists($strToPath)) {
			@unlink($strToPath);
		}
		$strCommand = \COption::GetOptionString("ammina.optimizer", "lib_path_babelminify", "babel-minify") . ' ' . $strFromPath . ' --mangle -o' . $strToPath;
		@exec($strCommand);
		if (file_exists($strToPath) && filesize($strToPath) > 0) {
			return true;
		}
		return false;
	}

	static public function doCheckGoogleAPI()
	{
		return amopt_strlen(\COption::GetOptionString("ammina.optimizer", "google_pagespeed_apikey", "")) > 0;
	}

	static public function doCheckAmminaAPI()
	{
		$str = \COption::GetOptionString("ammina.optimizer", "ammina_apikey", "");
		if (amopt_strlen($str) > 0) {
			return true;
			//@todo Добавить проверку на сервере
		}
		return false;
	}

	static public function getCurrentCheckData()
	{
		$arResult = \unserialize(\COption::GetOptionString("ammina.optimizer", "libs_checked_data", serialize(array())));
		if (empty($arResult)) {
			return self::doCheckLibrary();
		}
		return $arResult;
	}

	static public function doCheckBxOptionsOptimizeCssFiles()
	{
		if (\COption::GetOptionString("main", "optimize_css_files", "N") == "Y") {
			return false;
		}
		return true;
	}

	static public function doCheckBxOptionsOptimizeJsFiles()
	{
		if (\COption::GetOptionString("main", "optimize_js_files", "N") == "Y") {
			return false;
		}
		return true;
	}

	static public function doCheckBxOptionsUseMinifiedAssets()
	{
		if (\COption::GetOptionString("main", "use_minified_assets", "N") == "Y") {
			return false;
		}
		return true;
	}

	static public function doCheckBxOptionsMoveJsToBody()
	{
		if (\COption::GetOptionString("main", "move_js_to_body", "N") == "Y") {
			return false;
		}
		return true;
	}

	static public function doCheckBxOptionsCompresCssJsFiles()
	{
		if (\COption::GetOptionString("main", "compres_css_js_files", "N") == "Y") {
			return false;
		}
		return true;
	}

	static public function doCheckBxOptionsCdn()
	{
		if (\CModule::IncludeModule("bitrixcloud")) {
			if (\CBitrixCloudCDN::IsActive()) {
				return false;
			}
		}
		return true;
	}

	static public function doCheckBxOptionsComposite()
	{
		if (class_exists('\\Bitrix\\Main\\Composite\\Helper')) {
			if (Helper::isOn()) {
				return false;
			}
		}
		return true;
	}

}