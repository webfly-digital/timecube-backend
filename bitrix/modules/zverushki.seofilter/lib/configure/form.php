<?
namespace Zverushki\Seofilter\Configure;

use Bitrix\Main\Config\Option,
    Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Application,
    Bitrix\Main\Page\Asset,
	Zverushki\Seofilter\Agent;

Loc::loadMessages(__FILE__);
Loader::includeSharewareModule("zverushki.seofilter");
Loader::includeSharewareModule("sale");
Loader::includeModule('catalog');

/**
*
*/
class form
{
    public $error = array();
    public $prefix = "zverushki_seofilter";
    private $prefixDef = "def";

    private $module_id = "zverushki.seofilter";
    private $arField = array();
    private $option = array();
    private $siteList = array();
    private $_arField = array();
    private $arSiteDef = array();
    private $codeDef = "-";
    private $isDef = false;
    private $Uniq = false;
    private $arDef = array();

    function __construct()
    {
        $this->option = Config::getFormParams();
	    $this->initPriceType($this->option);
        $this->viewAdvanced($this->option);
        $this->arSiteDef = array(
                    "LID" => $this->codeDef,
                    "NAME" => Loc::getMessage("SEOFILTER_SETTING_DEF_TAB"),
                    "TITLE" => Loc::getMessage("SEOFILTER_SETTING_DEF_TAB_TITLE")
                );
    }

    private function viewAdvanced(&$option){
        $view = Option::get($this->module_id, 'view_advanced', "N", '-');
        if($view != 'Y'){
            foreach($option['form'] as $code => $config){
                if($config['advanced'])
                    unset($option['form'][$code]);
            }
        }
    }

    public function setData($request){
        $this->request = $request;
    }

	public function initPriceType(&$option){
		if(Loader::includeSharewareModule("sale")){
			$rsGroup = \Bitrix\Catalog\GroupTable::getList( [ 'order' => [ 'SORT' => 'ASC', 'NAME' => 'ASC' ] ] );
			while( $arGroup = $rsGroup->fetch() ){
				$option['form']['price_active']['values'][$arGroup['NAME']] = '[' . $arGroup['ID'] . '] ' . $arGroup['NAME'];
			}
		}else{
			unset($option['form']['price_active']);
		}
	}

    public function Init($arParams){
        Asset::getInstance()->addString('<style>
    .fieldlist{background-color: #fff; padding: 5px; border: 1px solid #333 !important;}
    .fieldlist td{padding: 5px;}.required-red{margin-left:1px;color:red;}

</style><script>
function changeDef(_ths){
    let $parentInput = BX.findParent(_ths, {
        tag: "td",
        class: "adm-detail-content-cell-r"
    }, true);

    let $textInput = BX.findChild(
        $parentInput, {
        tag: "input",
        attribute: {
            type: "text"
        }
    }, true);

    BX.adjust($textInput, {props: {disabled: BX(_ths).checked}});
    updateDef(BX.findParent($parentInput, {tag: "tr"}, true));
};
function changeDefCheck(_ths){
    let $parentInput = BX.findParent(_ths, {
        tag: "td",
        class: "adm-detail-content-cell-r"
    }, true);
    let $textInput = BX.findChild(
        $parentInput, {
        tag: "input",
        attribute: {
            type: "checkbox"
        }
    }, true);

    BX.adjust($textInput, {props: {disabled: BX(_ths).checked}});
    updateDef(BX.findParent($parentInput, {tag: "tr"}, true));
};
function changeDefSelect(_ths){
    let $parentInput = BX.findParent(_ths, {
        tag: "td",
        class: "adm-detail-content-cell-r"
    }, true);

    let $textInput = BX.findChild(
        $parentInput, {
        tag: "select"
    }, true);

    BX.adjust($textInput, {props: {disabled: BX(_ths).checked}});
    updateDef(BX.findParent($parentInput, {tag: "tr"}, true));
};
function updateDef(parents){
    let $parents = !!parents ? parents : BX("tabControl_layout");
    let $defLine = BX.findChild(BX("edit-"), {
        prop: "data-name",
    }, true, true);

    if(!$defLine)
        return;

    let defVal = [];
    $defLine.forEach(function($input){
        if(!!$input.getAttribute("data-name")){
            defVal[$input.getAttribute("data-name")] = $input.value;
        }
    });

    let $checks = BX.findChild($parents, {
        tag: "input",
        class: "def",
        attribute: {
            type: "checkbox"
        }
    }, true, true);
    $checks.forEach(function($element){
        if($element.checked){
            let $textInput = BX.findChild(
                BX.findParent($element, {
                    tag: "td",
                    class: "adm-detail-content-cell-r"
                }, true), {
                tag: "input",
                attribute: {
                    type: "text"
                }
            }, true);

            if(!$textInput){
                $textInput = BX.findChild(
                    BX.findParent($element, {
                        tag: "td",
                        class: "adm-detail-content-cell-r"
                    }, true), {
                    tag: "input",
                    attribute: {
                        type: "checkbox"/*,
                        prop: "data-name"*/
                    }
                });

                if(!$textInput){
                    $textInput = BX.findChild(
                        BX.findParent($element, {
                            tag: "td",
                            class: "adm-detail-content-cell-r"
                        }, true), {
                        tag: "select",
                    }, true);

                    if(!!$textInput)
                        BX.adjust($textInput, {props: {disabled: true, value: defVal[$textInput.getAttribute("data-name")]}});
                }else
                    BX.adjust($textInput, {props: {disabled: true, checked: defVal[$textInput.getAttribute("data-name")] == "Y" }});
            }else
                BX.adjust($textInput, {props: {disabled: true, value: defVal[$textInput.getAttribute("data-name")]}});

        }
    });
};
BX.ready(function(){
    updateDef();
    BX.findChild(
            BX("tabControl_layout"), {
            class: "adm-detail-tab",
            tag: "span"
        }, true, true).forEach(function($tab){
            BX.bind(
                $tab, "click", function() {
                    let id = BX(this).getAttribute("id");
                    id = id.replace(/tab_cont_/g, "");
                    if(id !== "-")
                        updateDef(BX(id));
            });
        });
});
</script>', true);

        $this->siteList = $arParams["siteList"];

        if(count($this->siteList) > 1){
            $this->siteList[$this->arSiteDef["LID"]] = array("NAME" => $this->arSiteDef["NAME"], "SELECT" => "N");
            $this->isDef = true;
        }else
            $this->Uniq = true;

        $this->getData();
    }

    public function getDefaltTab(){
        return array(
            "DIV" => "edit" . $this->arSiteDef["LID"],
            "LID" => $this->arSiteDef["LID"],
            "TAB" => $this->arSiteDef["NAME"],
            "TITLE" => $this->arSiteDef["TITLE"]
        );
    }
    public function getTab($siteId){
        $_table = "";
        $this->siteId = $siteId;

        foreach ($this->option["form"] as $code => $field) {
            $field["result"] = $this->getFieldVal((isset($field['altsite']) ? $field['altsite'] : $siteId), $code);
            $field[$this->prefixDef] = $this->getFieldVal((isset($field['altsite']) ? $field['altsite'] : $siteId), $this->prefixDef.'_'.$code);

            if(
                $code != "avail_active" && $code != "groupline"
                || ($code == "avail_active" && ($this->Uniq || ($this->isDef && $this->siteId == $this->codeDef)))
                || ($code == "groupline" && ($this->Uniq || ($this->isDef && $this->siteId == $this->codeDef)))
            ){
                if(!$field['main'] || ($field['main'] && ($this->Uniq || ($this->isDef && $this->siteId == $this->codeDef))))
                    $_table .= $this->getField($code, $field);
            }
        }
        return $_table;
    }

    private function getField($code, $field){
        switch ($field["type"]) {
            case 'list':
                return $this->getSelect($code, $field);
                break;
            case 'checkbox':
                return $this->getCheck($code, $field);
                break;
            case 'radio':
                return $this->getRadio($code, $field);
                break;
            case 'titleline':
                return $this->getTitleLine($field);
                break;
            case 'notes':
                return $this->notes($field);
                break;
            case 'fieldlist':
                return $this->fieldList($code, $field);
                break;
            default:
            case 'text':
                return $this->getText($code, $field);
                break;
        }
    }
    private function getSelect($code, $field){
        $viewCustom = true;
        if($this->isDef){
             if($this->siteId == $this->codeDef)
                $viewCustom = false;
        }elseif($this->Uniq){
            $viewCustom = false;
        }
        $_table = '<tr>
            <td width="50%" class="adm-detail-content-cell-l">';
                if($field["description"]):
                    $_table .= '<span id="hint_'.$code.'"></span>
                    <script type="text/javascript"> BX.hint_replace(BX(\'hint_'.$code.'\'), \''.$field["description"].'\');</script>&nbsp;';
                endif;
                $_table .= '<label for="'.$this->prefix.'['.(isset($field['altsite']) ? $field['altsite'] : $this->siteId).'][values]['.$code.']">'.$field["name"].''.($field["required"] ? '<span class="required-red">*</span>' : '').':</label>
            </td>
            <td width="50%" class="adm-detail-content-cell-r">

                <select name="'.$this->prefix.'['.(isset($field['altsite']) ? $field['altsite'] : $this->siteId).'][values]['.$code.']'.($field['multy'] ? '[]': '').'"'.($field['multy'] ? ' multiple': '').''.(!empty($field['rows']) ? ' size="'.$field['rows'].'"': '').' class="'.$this->prefix.'_'.$code.'" data-name="'.$code.'">';
                if(!$field["required"])
                    $_table .= '<option value="">'.Loc::getMessage("SEOFILTER_SETTING_SELECT_EMPTY").'</option>';
                foreach ($field["values"] as $k => $v)
                    $_table .= '<option value="'.$k.'"'.($field["result"] == $k || (is_array($field["result"]) && in_array($k, $field["result"]))? ' selected="selected"' : '').'>'.$v.'</option>';
            $_table .= '</select>';

        if($viewCustom)
            $_table .= '
                    <label>&nbsp;'.Loc::getMessage("SEOFILTER_SETTING_DEF_TAB").' <input type="checkbox" value="y"
                        id="def_'.$this->siteId.'_'.$code.'"
                        name="'.$this->prefix.'['.$this->siteId.']['.$this->prefixDef.']['.$code.']"
                        class="def '.$this->prefix.'_'.$this->prefixDef.'_'.$code.'"
                        '.(empty($field[$this->prefixDef]) || $field[$this->prefixDef] == "y"  ? ' checked="checked"' : '').'
                         onclick="changeDefSelect(this);"
                        />
                    </label>';
        $_table .= '</td>
        </tr>';
        return $_table;
    }
    private function getCheck($code, $field){
        $viewCustom = true;
        if($this->isDef){
             if($this->siteId == $this->codeDef)
                $viewCustom = false;
        }elseif($this->Uniq){
            $viewCustom = false;
        }
        $_table = '<tr id="'.$this->prefix.'_'.$this->siteId.'_field_'.$code.'">
            <td width="50%" class="adm-detail-content-cell-l">';
                if($field["description"]):
                    $_table .= '<span id="hint_'.$code.'"></span>
                    <script type="text/javascript"> BX.hint_replace(BX(\'hint_'.$code.'\'), \''.$field["description"].'\');</script>&nbsp;';
                endif;
                $_table .= '<label for="'.$this->prefix.'['.$this->siteId.'][values]['.$code.']">'.$field["name"].''.($field["required"] ? '<span class="required-red">*</span>' : '').':</label>
            </td>
            <td width="50%" class="adm-detail-content-cell-r">
                <input id="'.(isset($field['altsite']) ? $field['altsite'] : $this->siteId).'_'.$code.'" type="checkbox" name="'.$this->prefix.'['.(isset($field['altsite']) ? $field['altsite'] : $this->siteId).'][values]['.$code.']" class="'.$this->prefix.'_'.$code.'" value="'.$field["values"].'" '.($field["result"] == $field["values"] ? ' checked="checked"' : '').' data-name="'.$code.'">';
        if($viewCustom)
            $_table .= '
                    <label>&nbsp;'.Loc::getMessage("SEOFILTER_SETTING_DEF_TAB").' <input type="checkbox" value="y"
                        id="def_'.$this->siteId.'_'.$code.'"
                        name="'.$this->prefix.'['.$this->siteId.']['.$this->prefixDef.']['.$code.']"
                        class="def '.$this->prefix.'_'.$this->prefixDef.'_'.$code.'"
                        '.(empty($field[$this->prefixDef]) || $field[$this->prefixDef] == "y"  ? ' checked="checked"' : '').'
                         onclick="changeDefCheck(this);"
                        />
                    </label>';
        $_table .= '</td>
        </tr>';
        return $_table;
    }
    private function getRadio($code, $field){
        $_table = '<tr id="'.$this->prefix.'_'.$this->siteId.'_field_'.$code.'">
            <td width="50%" class="adm-detail-content-cell-l">';
                if($field["description"]):
                    $_table .= '<span id="hint_'.$code.'"></span>
                    <script type="text/javascript"> BX.hint_replace(BX(\'hint_'.$code.'\'), \''.$field["description"].'\');</script>&nbsp;';
                endif;
                $_table .= '<label>'.$field["name"].''.($field["required"] ? '<span class="required-red">*</span>' : '').':</label>
            </td>
            <td width="50%" class="adm-detail-content-cell-r">';
                $arKey = array_keys($field["values"]);
                $first = $arKey[0];
                foreach ($field["values"] as $k => $v)
                    $_table .= '<label><input type="radio" name="'.$this->prefix.'['.$this->siteId.'][values]['.$code.']" class="'.$this->prefix.'_'.$code.'" value="'.$k.'" '.(
                        ($field[$code] == $k || (empty($field["result"]) && $first == $k)) ? ' checked="checked"' : '').'>'.$v.'</label>';

            $_table .= '</td></tr>';

        return $_table;
    }
    private function getText($code, $field){
        $width = "50";
        $viewCustom = true;
        if($this->isDef){
             if($this->siteId == $this->codeDef)
                $viewCustom = false;
        }elseif($this->Uniq){
            $viewCustom = false;
        }
        $_table = '<tr id="'.$this->prefix.'_'.$this->siteId.'_field_'.$code.'"><td width="'.$width.'%" class="adm-detail-content-cell-l">';
        if($field["description"]):
            $_table .= '<span id="hint_'.$code.'"></span>
                    <script type="text/javascript"> BX.hint_replace(BX(\'hint_'.$code.'\'), \''.$field["description"].'\');</script>&nbsp;';
        endif;
        $_table .= '<label>'.$field["name"].''.($field["required"] && $viewCustom ? '<span class="required-red">*</span>' : '').':</label></td><td width="'.$width.'%" class="adm-detail-content-cell-r" valign="top">';

        if(empty($field["row"]) || intval($field["row"]) == 1):
            $_table .= '<input type="text" name="'.$this->prefix.'['.(isset($field['altsite']) ? $field['altsite'] : $this->siteId).'][values]['.$code.']" class="'.$this->prefix.'_'.$code.'" value="'.($field["result"]).'" data-name="'.$code.'" style="'.$field['style'].'"/>';
        else:
            $_table .= '<textarea name="'.$this->prefix.'['.(isset($field['altsite']) ? $field['altsite'] : $this->siteId).'][values]['.$code.']" class="'.$this->prefix.'_'.$code.'">'.($field["result"]).'</textarea>';
        endif;
        if($viewCustom)
            $_table .= '
                    <label>&nbsp;'.Loc::getMessage("SEOFILTER_SETTING_DEF_TAB").' <input type="checkbox" value="y"
                        id="'.$this->siteId.'_'.$code.'"
                        name="'.$this->prefix.'['.$this->siteId.']['.$this->prefixDef.']['.$code.']"
                        class="def '.$this->prefix.'_'.$this->prefixDef.'_'.$code.'"
                        '.(empty($field[$this->prefixDef]) || $field[$this->prefixDef] == "y"  ? ' checked="checked"' : '').'
                         onclick="changeDef(this);"
                        />
                    </label>';

        $_table .= '</td>
                </tr>';

        return $_table;
    }
    private function getTitleLine($field){
        $_table = '<tr class="heading"><td colspan="2">'.$field["name"].'</td></tr>';

        return $_table;
    }
    private function notes($field){
        $_table = '<tr><td colspan="2" align="center"><div class="adm-info-message-wrap"><div class="adm-info-message">'.$field["name"].'</div></div></td></tr>';

        return $_table;
    }
    private function fieldList($code, $field){
        $_table = '<tr><td colspan="2" align="center">
                    <table class="fieldlist"><tr>
                        <th>'.Loc::getMessage("SEOFILTER_SETTING_FIELD_TD_TITLE").'</th>
                        <th>'.Loc::getMessage("SEOFILTER_SETTING_FIELD_TD_ACTIVE").'</th>
                        <th>'.Loc::getMessage("SEOFILTER_SETTING_FIELD_TD_REQUIRED").'</th>
                    </tr>';
        foreach ($field["values"] as $_field)
            $_table .= '<tr>
                            <td>'.$_field["name"].'</td>
                            <td align="center"><input type="checkbox" name="'.$this->prefix.'['.$this->siteId.'][values]['.$code.']['.$_field["code"].'][active]" class="'.$this->prefix.'_'.$code.'" value="Y" '.(!empty($field["result"][$_field["code"]]["active"]) && $field["result"][$_field["code"]]["active"] == "Y" ? ' checked="checked"' : '').'></td>
                            <td align="center"><input type="checkbox" name="'.$this->prefix.'['.$this->siteId.'][values]['.$code.']['.$_field["code"].'][required]" class="'.$this->prefix.'_'.$code.'" value="Y" '.(!empty($field["result"][$_field["code"]]["required"]) && $field["result"][$_field["code"]]["required"] == "Y" ? ' checked="checked"' : '').'></td>
                        </tr>';
        $_table .= '</table></td></tr>';

        return $_table;
    }

    /**
     * Записываем значения формы с POST
     * @param post значение полей формы $field
     *
     * @return bool
     */
    public function setPostValues($field){
        if($this->isDef)
            $this->arDef = $field[$this->codeDef]["values"];

        if($this->verifyFields($field))
            return false;
        $this->setValues();

        if(!empty($this->error))
            return false;

        Application::getInstance()->getTaggedCache()->ClearByTag($this->module_id);
	    Agent::setClearLandingIndex($field[$this->codeDef]['values']['purification'] == 'delete');
        return true;
    }
    private function setValues(){
        foreach ($this->arField as $siteId => $value) {
            foreach ($value["system"] as $code => $val){
                $this->organizationVal($code, $val);
                $field = $this->option["form"][$code];
                    Option::set($this->module_id, $code, $val, (isset($field['altsite']) ? $field['altsite'] : $siteId));
                /*if($code == 'avail_active')
                    mp($code, $val, (isset($field['altsite']) ? $field['altsite'] : $siteId));*/
            }
            if(!empty($value[$this->prefixDef])){
                foreach ($value[$this->prefixDef] as $code => $val){
                    $field = $this->option["form"][$code];
                    Option::set($this->module_id, $this->prefixDef."_".$code, $val, (isset($field['altsite']) ? $field['altsite'] : $siteId));
                }
            }
        }
        // die;

    }
    private function organizationVal($code, &$val){
        switch ($code) {
            case 'search':
            case 'basket':
            case 'order':
                $request = \Bitrix\Main\HttpApplication::GetInstance()->GetContext()->GetRequest();
                $vals = explode(";", $val);
                foreach ($vals as $i => $url){
                    if(strpos($url, $request->getHttpHost()) !== false){
                        $valList = explode($request->getHttpHost(), trim($url));
                        $vals[$i] = trim($valList[1]);
                    }
                }
                $val = implode(";", $vals);
                break;
            case 'price_active':
            case 'skip_exec_USER_GROUP':
                $val = serialize($val);
                break;
        }
    }
    /**
     * Проверяем поля формы
     * @param значение полей формы $field
     *
     * @return bool
     */
	private function verifyFields($field){
		$this->error = array();
		$isError = false;

		foreach ($field as $siteId => $values):
			$_value = $values["values"];
			$_def = $values[$this->prefixDef];

			foreach ($this->option["form"] as $code => $param):
				if($param['main'] && !$this->Uniq && $siteId !== $this->codeDef)
					continue;

				if($this->Uniq && $param['altsite'] && $siteId !== $param['altsite'])
					$_value[$code] = $field[$param['altsite']]["values"][$code];

				if($param["type"] === "checkbox" && empty($_value[$code]))
					$_value[$code] = "N";
				elseif($param["type"] === "notes")
					continue;
				elseif($param["type"] === "titleline")
					continue;
				$vv = $_value[$code];
				$this->arField[$siteId][($param['system'] ? "system" : "values")][$code] = empty($vv) ? "" : $vv;

				if( $siteId !== $this->codeDef)
					$this->arField[$siteId][$this->prefixDef][$code] = empty($_def[$code]) ? "n" : $_def[$code];

				if(!empty($_def) && $_def[$code] == "y")
					$vv = $this->arDef[$code];

				if( $siteId !== $this->codeDef && $_res = $this->verifyVal($param, $vv) ){
					$isError = true;
					$this->error[$siteId][] = Loc::getMessage("SEOFILTER_VERIFY_FILED_ERROR_".$_res."_".$param["type"])." <b>".$param["name"]."</b>";
				}

			endforeach;
		endforeach;

		return $isError;
	}
    private function verifyVal($param, $val){
        if($param["required"] && empty($val))
            return "required";
        return false;
    }
    private function getFieldVal($siteId, $code){
        if($this->request->isPost()){
            $form = $this->request->getPost($this->prefix);
            $space = "values";
            if(strpos($code, $this->prefixDef) !== false){
                $space = $this->prefixDef;
                $codeList = explode($this->prefixDef.'_', $code);
                $code = $codeList[1];
                if(empty($form[$siteId][$space][$code]) )
                    $form[$siteId][$space][$code] = "n";
            }
            return $form[$siteId][$space][$code];
        }elseif (isset($this->_arField[$siteId])){
            return $this->_arField[$siteId][$code];
        }

        return $this->option["form"][$code]["default"];
    }
    /**
     * Возращает значение всех сохраненных полей артикуля для инфоблока
     * @return array $_arr Массив всех полей
     */
    private function getData(){
        if(!empty($this->_arField))return $this->_arField;

        $_arr = array();
        foreach ($this->siteList as $siteid => $val):
            foreach ($this->option["form"] as $code => $param):
                if($param["system"]){
                    $_arr[$siteid][$code] = Option::get($this->module_id, $code, "", $siteid);
                    $_arr[$siteid][$this->prefixDef."_".$code] = Option::get($this->module_id, $this->prefixDef."_".$code, "", $siteid);
                }

            endforeach;
        endforeach;
        foreach ($this->option["form"] as $code => $param){
            if($param["system"]){
	            $_arr['-'][$code] = Option::get($this->module_id, $code, "", "-");
	            $_arr['-'][$this->prefixDef . "_" . $code] = Option::get($this->module_id, $this->prefixDef . "_" . $code, "", "-");
	            if($param['multy']){
		            $_arr['-'][$code] = unserialize($_arr['-'][$code]);
		            $_arr['-'][$this->prefixDef . "_" . $code] = unserialize($_arr['-'][$this->prefixDef . "_" . $code]);
	            }
            }
        }
        foreach ($this->option["form"] as $code => $param){
            if(isset($param['altsite']) && $param["system"]){
                $_arr[$param['altsite']][$code] = Option::get($this->module_id, $code, "", $param['altsite']);
                $_arr[$param['altsite']][$this->prefixDef."_".$code] = Option::get($this->module_id, $this->prefixDef."_".$code, "", $param['altsite']);

	            if($param['multy']){
		            $_arr[$param['altsite']][$code] = unserialize($_arr[$param['altsite']][$code]);
		            $_arr[$param['altsite']][$this->prefixDef."_".$code] = unserialize($_arr[$param['altsite']][$this->prefixDef."_".$code]);
	            }
            }
        }
        $this->_arField = $_arr;

        return $_arr;
    }
    public function getOptions($siteIds = array(), $reDef = false){
        $arr = $this->getData();

        $def = $arr['-'];
        unset($arr['-']);
        foreach ($arr as $siteId => $vals) {
            foreach ($vals as $code => $val) {
                if($this->prefixDef != substr($code, 0, strlen($this->prefixDef))){
                    if($vals[$this->prefixDef."_".$code] == 'y'){
                        $arr[$siteId][$code] = $def[$code];
                    }
                }

            }
        }

        if(!empty($siteIds))
            foreach ($arr as $sid => $vals) {
                if(!in_array($sid, $siteIds))
                    unset($arr[$sid]);
            }

        if($reDef)
            $arr['-'] = $def;
        return $arr;
    }
}