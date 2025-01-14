<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */
/** @global CMain $APPLICATION */
if ($arParams["SECTION_CODE"] != WF_PACK_SECTION_CODE) {
    $f_params["ALL"] = [
        ["URL_CPU" => "dsc", "PAGE_SECTION_TITLE" => "Со скидкой"],
        ["URL_CPU" => "dlvr", "PAGE_SECTION_TITLE" => "С бесплатной доставкой"],
        ["URL_CPU" => "pack", "PAGE_SECTION_TITLE" => "С бесплатной упаковкой"],
        ["URL_CPU" => "gft", "PAGE_SECTION_TITLE" => "С подарком"],
    ];
    var_dump($f_params);
    $res = CIBlockSection::GetList([], ['IBLOCK_ID' => $arParams["IBLOCK_ID"], 'CODE' => $arParams["SECTION_CODE"]]);
    $section = $res->Fetch();
    $chain = CIBlockSection::GetNavChain($arParams["IBLOCK_ID"], $section["ID"], array());
    $arSectionPath = [];
    while ($arSection = $chain->GetNext()) {
        $arSectionPath[] = $arSection["CODE"];
    }
    array_shift($arSectionPath); //remove catalog-root

    $base_section_code = "";
    if (in_array($arParams["SECTION_CODE"], $arSectionPath)) {
        $base_section_code = $arSectionPath[0];
    }

    if ($base_section_code == "shkatulki-dlya-chasov-s-avtopodzavodom") {
        $f_params[$base_section_code] = [
            ["URL_CPU" => "dop", "PAGE_SECTION_TITLE" => "С отсеком для хранения", "PROP" => "DOP_OTSEK"],
            ["URL_CPU" => "lcd", "PAGE_SECTION_TITLE" => "С дисплеем", "PROP" => "LCD"],
            ["URL_CPU" => "lock", "PAGE_SECTION_TITLE" => "С замочком", "PROP" => "ZAMOCHEK"],
            ["URL_CPU" => "led", "PAGE_SECTION_TITLE" => "С подсветкой", "PROP" => "PODSVETKA"],
            ["URL_CPU" => "bat", "PAGE_SECTION_TITLE" => "С батарейками", "PROP" => "BATTERY"],
        ];
    }
    if ($base_section_code == "shkatulki_dlya_chasov") {
        $f_params[$base_section_code] = [
            ["URL_CPU" => "glsc", "PAGE_SECTION_TITLE" => "Со стеклянной крышкой", "PROP" => "GLASS_COVER"],
            ["URL_CPU" => "lock", "PAGE_SECTION_TITLE" => "С замочком", "PROP" => "ZAMOCHEK"],
            ["URL_CPU" => "hhld", "PAGE_SECTION_TITLE" => "С увеличенной подушкой", "PROP" => "XXX_HIGH_HOLDER"],
            ["URL_CPU" => "trvl", "PAGE_SECTION_TITLE" => "Для путешествий", "PROP" => "XXX_MOBILE_HUMIDOR"],
        ];
    }
    if ($base_section_code == "shkatulki_dlya_ukrasheniy") {
        $f_params[$base_section_code] = [
            ["URL_CPU" => "lock", "PAGE_SECTION_TITLE" => "С замочком", "PROP" => "ZAMOCHEK"],
            ["URL_CPU" => "mirr", "PAGE_SECTION_TITLE" => "С зеркальцем", "PROP" => "BOX_WITH_MIRROR"],
        ];
        if ($arParams["SECTION_CODE"] != "shkatulki_dlya_ukrasheniy_derevyannye")
            $f_params[$base_section_code][] = ["URL_CPU" => "trvl", "PAGE_SECTION_TITLE" => "Для путешествий", "PROP" => "XXX_MOBILE_HUMIDOR"];
        $f_params[$base_section_code][] = ["URL_CPU" => "glsc", "PAGE_SECTION_TITLE" => "Со стеклянной крышкой", "PROP" => "GLASS_COVER"];
    }
    if ($base_section_code == "khyumidory") {
        $f_params[$base_section_code] = [
            ["URL_CPU" => "hmdf", "PAGE_SECTION_TITLE" => "Увлажнитель", "PROP" => "XXX_CIGAR_HUMIDIFIER"],
            ["URL_CPU" => "hdrm", "PAGE_SECTION_TITLE" => "Гидрометр", "PROP" => "XXX_CIGAR_HYGROMETER"],
            ["URL_CPU" => "lock", "PAGE_SECTION_TITLE" => "С замочком", "PROP" => "ZAMOCHEK"],
            ["URL_CPU" => "trvl", "PAGE_SECTION_TITLE" => "Для путешествий", "PROP" => "XXX_MOBILE_HUMIDOR"],
            ["URL_CPU" => "glsc", "PAGE_SECTION_TITLE" => "Со стеклянной крышкой", "PROP" => "GLASS_COVER"],
        ];
    }
    $tagsParams = array_merge($f_params["ALL"], $f_params[$base_section_code]);
    if ($tagsParams) {
        foreach ($tagsParams as $key => $tagParam) {
            if (isset($_GET[$tagParam["URL_CPU"]])) unset($tagsParams[$key]);
            else $tagsParams[$key]['URL_CPU'] = '?' . $tagParam['URL_CPU'] . '=on';
        }
        $arResult['ITEMS'] = array_merge($tagsParams, $arResult['ITEMS']);
    }
}
if (!empty($arResult['ITEMS'])) { ?>
    <div<?= $arParams['IDENTIFIER'] ? ' id="' . $arParams['IDENTIFIER'] . '"' : '' ?>
            class="catalog-top zverushki-tags">
        <ul class="tags-list">
            <? foreach ($arResult['ITEMS'] as $key => $item) { ?>
                <li class="tag-item">
                    <label class="inline-checkbox">
                        <a href="<? echo $item['URL_CPU'] ?>" title="<? echo $item['PAGE_SECTION_TITLE'] ?>">
                            <span class="inline-checkbox__content"><?= $item['PAGE_SECTION_TITLE'] ?></span>
                        </a>
                    </label>
                </li>
            <? } ?>
        </ul>
    </div>
    <?
} ?>
