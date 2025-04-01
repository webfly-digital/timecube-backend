<?

namespace Webfly\Handlers\Catalog;

use Bitrix\Main\Loader,
    Bitrix\Main\EventManager;

class SetDimensions
{
    const weight = "PROPERTY_XXX_PACK_GROSS_KG";
    const length = "PROPERTY_PACK_DEPTH";
    const width = "PROPERTY_PACK_WIDTH";
    const height = "PROPERTY_PACK_HEIGHT";
    const IBLOCKS = array(10);

    static $handlerDisallow = array();

    /**
     * Проставление размеров и веса товара из свойств при добавлении нового элемента
     * @param \Bitrix\Main\Event $e
     */
    public static function SetCatalogData(\Bitrix\Main\Event $event)
    {
        $params = $event->getParameters();
        $arFields = $params['primary'] ? $params['primary'] : $params['fields'];

        if (empty($arFields["ID"])) return;
        $arProductFields = $event->getParameter("fields");

        Loader::includeModule("iblock");

        /*  if (in_array($arFields["ID"], self::$handlerDisallow))
              return;
          self::$handlerDisallow[] = $arFields["ID"];

          //IBLOCKS = array(6, 8, 13, 14, 15);
          //Get iblock_id
          /* if ($arFields["ID"]) {
               $iblck = \CIBlockElement::GetList(array(), array("ID" => $arFields["ID"]), false, false, array("ID", "IBLOCK_ID"))->fetch();
               $arFields["IBLOCK_ID"] = $iblck["IBLOCK_ID"];
           }

           if (in_array($arFields["IBLOCK_ID"], self::IBLOCKS))*/
        {
            $props = [];
            $elt = \CIBlockElement::GetList(array(), array('ID' => $arFields["ID"], "IBLOCK_ID" => self::IBLOCKS), false, false, array('ID', 'IBLOCK_ID', self::weight, self::height, self::length, self::width));
            while ($ob = $elt->Fetch()) {
                $props[self::weight] = $ob[self::weight . '_VALUE'];
                $props[self::length] = $ob[self::length . '_VALUE'];
                $props[self::width] = $ob[self::width . '_VALUE'];
                $props[self::height] = $ob[self::height . '_VALUE'];
            }

            if (is_array($props)) {
                foreach ($props as $propCode => $propVal) {
                    //дефолтные значения веса и габаритов
                    if (empty($propVal) or $propVal === 0) {
                        if ($propCode == self::weight) {
                            $arProductFields["WEIGHT"] = 500;
                        } else {
                            $arProductFields["LENGTH"] = 300;
                            $arProductFields["WIDTH"] = 300;
                            $arProductFields["HEIGHT"] = 300;
                        }
                    } else {//проставление значений из свойств
                        switch ($propCode) {
                            case self::weight:
                                //преобразование в граммы
                                if (substr_count($propVal, ",") > 0)
                                    $propVal = str_replace(",", ".", $propVal);

                                if (substr_count($propVal, ".") > 0)
                                    intval($propVal);
                                $arProductFields["WEIGHT"] = $propVal * 1000;
                                break;
                            case self::length:
                                $arProductFields["LENGTH"] = $propVal * 10;
                                break;
                            case self::width:
                                $arProductFields["WIDTH"] = $propVal * 10;
                                break;
                            case self::height:
                                $arProductFields["HEIGHT"] = $propVal * 10;
                                break;
                        }
                    }
                }

                $result = new \Bitrix\Main\Entity\EventResult();
                //модификация данных
                $result->modifyFields($arProductFields);
                return $result;
            }
        }
    }
}