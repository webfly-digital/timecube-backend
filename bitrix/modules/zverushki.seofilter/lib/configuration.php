<?
namespace Zverushki\Seofilter;
use Bitrix\Main\Config\Option,
    Bitrix\Main\Localization\Loc;
;

/**
* class Controller
*
*
* @package Zverushki\Seofilter
*/
class configuration
{
	private static $config = array();
	private static $option = array();
	private static $module_id = "zverushki.seofilter";
	private static $sort = ['ID' => 'DESC'];

	public static function set($name='', $value='')
	{
		self::$config[$name] = $value;
	}

	public static function get($name='')
	{
		if(!empty(self::$config[$name]))
			return self::$config[$name];
		return false;
	}

	public static function getOptions($siteId){
		if(!empty(self::$option[$siteId]))
			return self::$option[$siteId];
		$option = \Zverushki\Seofilter\Configure\Config::getFormParams();
		$_arr = array();
		foreach ($option["form"] as $c => $param):
            if($param["system"]){
                if(Option::get(self::$module_id, "def_".$c, "", $siteId) == 'n')
                	$_arr[$c] = Option::get(self::$module_id, $c, "", $siteId);
                else
                	$_arr[$c] = Option::get(self::$module_id, $c, "", '-');

	            if($param['multy'])
		            $_arr[$c] = unserialize($_arr[$c]);
            }
        endforeach;
        self::$option[$siteId] = $_arr;

        return self::$option[$siteId];
	}

    public static function getOption($code, $siteId){
        if(!empty(self::$option[$siteId]))
            return self::$option[$siteId][$code];

        self::getOptions($siteId);

        return self::$option[$siteId][$code];
    }
	public static function setSort($by, $order){
		static::$sort = [$by => $order];
	}

	public static function sort($a, $b){
		$by = key(static::$sort);
		$order = static::$sort[$by];

		if ($a[$by] == $b[$by])
			return 0;

		return ($order == "ASC" ? ($a[$by] < $b[$by] ? -1 : 1)  :  ($a[$by] > $b[$by] ? -1 : 1 ));
	}

    public static function getRandomVal($arItem){
         if(empty($arItem))
            return;

        foreach ($arItem as $kid => $item){
            if(empty($item['VALUES']))
                unset($arItem[$kid]);
            elseif($item['VALUES']['MIN'] || $item['VALUES']['MAX'])
                unset($arItem[$kid]);
        }
        if(empty($arItem))
            return;

        $parKey1 = array_rand($arItem);
        if($parKey1 && $arItem[$parKey1]['VALUES'])
            $parKey2 = array_rand($arItem[$parKey1]['VALUES']);
        else
            return static::getRandomVal($arItem);

        if($parKey2)
            $parUrl = $arItem[$parKey1]['VALUES'][$parKey2]['VALUE'];
        else
            return static::getRandomVal($arItem);

        if(!$parUrl)
            return static::getRandomVal($arItem);

        return static::getTranslit($parUrl);
    }

	public static function getTranslit($str){
		$replace = Option::get(self::$module_id, 'space_replace', '_', '-');
		if(empty($replace))
			$replace = '_';
        //TODO Подсчет количества мягких знаков
        // $str = 'Россия "Молодая семья"';
        $str = trim(trim(htmlspecialcharsback(htmlspecialcharsback($str))));
        $calc = str_replace(Loc::getMessage("SEOFILTER_SYMBOL_SOLID"), '', str_replace(Loc::getMessage("SEOFILTER_SYMBOL_SOFT"), '', $str));
        $calcCnt = strlen($calc);

		$arParams = array("replace_space" => $replace, "replace_other" => $replace);
        if(CheckVersion(SM_VERSION, '20.200.0'))
            $res = \Cutil::translit(trim($str), "ru", $arParams);
        else{
    		$res = static::translit($str, "ru", $arParams);
            $isDiff = strlen($res) < $calcCnt;
            $tmp = str_replace($replace, '', $res);

    		if((empty($tmp) || $isDiff) && function_exists('mb_substr')){
    			$arParams = array("replace_space" => $replace, "replace_other" => $replace, 'mb' => true);
    			$res = static::translit($str, "ru", $arParams);
    		}
            $isDiff = strlen($res) < $calcCnt;
            $tmp = str_replace($replace, '', $res);
            if(empty($tmp) || $isDiff){
                $arParams = array("replace_space" => $replace, "replace_other" => $replace);
                $res = static::translitSplit($str, "ru", $arParams);
            }

    		$res = trim($res, $replace);
        }
		return $res;
	}

	public static function translit($str, $lang, $params = array()){
        static $search = array();

        if(!isset($search[$lang]))
        {
            $mess = IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/js_core_translit.php", $lang, true);

            $trans_from = explode(",", $mess["TRANS_FROM"]);
            $trans_to = explode(",", $mess["TRANS_TO"]);
            foreach($trans_from as $i => $from)
                $search[$lang][$from] = $trans_to[$i];
        }

        $defaultParams = array(
            "max_len" => 100,
            "change_case" => 'L', // 'L' - toLower, 'U' - toUpper, false - do not change
            "replace_space" => '_',
            "replace_other" => '_',
            "delete_repeat_replace" => true,
            "safe_chars" => '',
            'mb' => false
        );
        foreach($defaultParams as $key => $value)
            if(!array_key_exists($key, $params))
                $params[$key] = $value;

        $len = strlen($str);
        $str_new = '';
        $last_chr_new = '';


        for($i = 0; $i < $len; $i++)
        {
        	if($params['mb'])
            	$chr = mb_substr($str, $i, 1);
            else
            	$chr = substr($str, $i, 1);

            if($chr || $chr == 0)
                if(preg_match("/[a-zA-Z0-9]/".BX_UTF_PCRE_MODIFIER, $chr) || ($params["safe_chars"] && strpos($params["safe_chars"], $chr)!==false))
                {
                    $chr_new = $chr;
                }
                elseif(preg_match("/\\s/".BX_UTF_PCRE_MODIFIER, $chr))
                {
                    if (
                        !$params["delete_repeat_replace"]
                        ||
                        ($i > 0 && $last_chr_new != $params["replace_space"])
                    )
                        $chr_new = $params["replace_space"];
                    else
                        $chr_new = '';
                }
                else
                {
                    if(array_key_exists($chr, $search[$lang]))
                    {
                        $chr_new = $search[$lang][$chr];
                    }
                    else
                    {
                        if (
                            !$params["delete_repeat_replace"]
                            ||
                            ($i > 0 && $i != $len-1 && $last_chr_new != $params["replace_other"])
                        )
                            $chr_new = $params["replace_other"];
                        else
                            $chr_new = '';
                    }
                }

            if(strlen($chr_new))
            {
                if($params["change_case"] == "L" || $params["change_case"] == "l")
                    $chr_new = ToLower($chr_new);
                elseif($params["change_case"] == "U" || $params["change_case"] == "u")
                    $chr_new = ToUpper($chr_new);

                $str_new .= $chr_new;
                $last_chr_new = $chr_new;
            }

            if (strlen($str_new) >= $params["max_len"])
                break;
        }

        return $str_new;
    }

    public static function translitSplit($str, $lang, $params = array()){
        static $search = array();

        if(!isset($search[$lang]))
        {
            $mess = IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/js_core_translit.php", $lang, true);

            $trans_from = explode(",", $mess["TRANS_FROM"]);
            $trans_to = explode(",", $mess["TRANS_TO"]);
            foreach($trans_from as $i => $from)
                $search[$lang][$from] = $trans_to[$i];
        }

        $defaultParams = array(
            "max_len" => 100,
            "change_case" => 'L', // 'L' - toLower, 'U' - toUpper, false - do not change
            "replace_space" => '_',
            "replace_other" => '_',
            "delete_repeat_replace" => true,
            "safe_chars" => '',
            'mb' => false
        );
        foreach($defaultParams as $key => $value)
            if(!array_key_exists($key, $params))
                $params[$key] = $value;

        $len = strlen($str);
        $str_new = '';
        $last_chr_new = '';
        if($str)
            foreach (str_split($str) as $i => $chr) {
                if(preg_match("/[a-zA-Z0-9]/".BX_UTF_PCRE_MODIFIER, $chr) || ($params["safe_chars"] && strpos($params["safe_chars"], $chr)!==false))
                {
                    $chr_new = $chr;
                }
                elseif(preg_match("/\\s/".BX_UTF_PCRE_MODIFIER, $chr))
                {
                    if (
                        !$params["delete_repeat_replace"]
                        ||
                        ($i > 0 && $last_chr_new != $params["replace_space"])
                    )
                        $chr_new = $params["replace_space"];
                    else
                        $chr_new = '';
                }
                else
                {
                    if(array_key_exists($chr, $search[$lang]))
                    {
                        $chr_new = $search[$lang][$chr];
                    }
                    else
                    {
                        if (
                            !$params["delete_repeat_replace"]
                            ||
                            ($i > 0 && $i != $len-1 && $last_chr_new != $params["replace_other"])
                        )
                            $chr_new = $params["replace_other"];
                        else
                            $chr_new = '';
                    }
                }

                if(strlen($chr_new))
                {
                    if($params["change_case"] == "L" || $params["change_case"] == "l")
                        $chr_new = ToLower($chr_new);
                    elseif($params["change_case"] == "U" || $params["change_case"] == "u")
                        $chr_new = ToUpper($chr_new);

                    $str_new .= $chr_new;
                    $last_chr_new = $chr_new;
                }

                if (strlen($str_new) >= $params["max_len"])
                    break;
            }

        return $str_new;
    }
}
?>