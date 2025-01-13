<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<div class="col-auto">
<?
    //region icons
    $productPropsData = [
       'FINGERPRINT_LOCK' => [
            'title' => 'Замок с отпечатком пальца',
            'description' => 'Изделие открывается с помощью отпечатка пальца.',
            "icon" => "/assets/img/product-detail/icons/picto-26.svg",
            "photo" => "/assets/img/product-detail/popup/26.jpg",
        ],
        'ZAMOCHEK' => [
            'title' => 'С замочком',
            'description' => 'От нежелательного проникникновения к вашим часам и украшениям предохраняет встроенный замочек и элегантный ключик. Ключик можно носить с собой или хранить в потайном месте и быть уверенным за сохранность своих дракгоценностей.',
            "icon" => "/assets/img/product-detail/icons/picto-1.png",
            "photo" => "/assets/img/product-detail/popup/1.jpg",
        ],
        'WORK_POWER' => [
            'title' => 'Работа от сети',
            'description' => 'Небольшой адаптер позволяет подключить шкатулку от обычной бытовой розетки 220 вольт.',
            "icon" => "/assets/img/product-detail/icons/picto-2.png",
            "photo" => "/assets/img/product-detail/popup/2.jpg",
        ],
        'BATTERY' => [
            'title' => 'Работа от батареек',
            'description' => 'Уезжая на несколько дней, питание заводчика от батареек позволяет вам не оставлять прибор подключённый к сети без присмотра. Отсек для батареек, как правило, находится в скрытом от взгляда месте и не нарушает эстетики заводчика. Количество и формфактор батарей можно найти в описании товара. Элементы питания в комплект не входят и приобретаются отдельно.',
            "icon" => "/assets/img/product-detail/icons/picto-3.png",
            "photo" => "/assets/img/product-detail/popup/3.jpg",
        ],
        'PODSVETKA' => [
            'title' => 'Подсветка',
            'description' => 'Светодиодная подсветка придаст вашим часам таинственность и заставит их играть множеством ослепляющих искр. В зависимости от модели подзаводчика подсветка может быть разного цвета, а также разной интенсивности.',
            "icon" => "/assets/img/product-detail/icons/picto-4.png",
            "photo" => "/assets/img/product-detail/popup/4.jpg",
        ],
        'REMOTE_CONTROL' => [
            'title' => 'Пульт управления',
            'description' => 'В комплекте поставляется пульт дистанционного управления, делающий управление шкатулкой наиболее комфортным. Вы сможете на расстоянии включать и выключать подсветку часов, а также в полной мере осуществлять управления режимами заводчика.',
            "icon" => "/assets/img/product-detail/icons/picto-5.png",
            "photo" => "/assets/img/product-detail/popup/5.jpg",
        ],
        'KEYCARD' => [
            'title' => 'Подсветка',
            'description' => 'Шкатулка закрывается на электронный замок, который приводится в работу с помощью электронного ключа (карточки), что делает использование шкатулки комфортным, а хранение Ваших часов безопасным.',
            "icon" => "/assets/img/product-detail/icons/picto-6.png",
            "photo" => "/assets/img/product-detail/popup/6.jpg",
        ],
        'PODZAVOD_NUM2' => [
            'title' => 'Количество часов, подзавод',
            'description' => 'Данная характеристика обозначает количество мест для подзавода часов.',
            "icon" => "/assets/img/product-detail/icons/picto-7.png",
            "photo" => "/assets/img/product-detail/popup/7.jpg",
            'value' => $arResult['PROPERTIES']['PODZAVOD_NUM2']['VALUE']
        ],
        'HRAN_NUM2' => [
            'title' => 'Количество часов, хранение',
            'description' => 'Данная характеристика обозначает количество мест для хранения часов без подзавода. Как правило, часы одеваются на подушечку и помещаются в отдельную ячейку.',
            "icon" => "/assets/img/product-detail/icons/picto-8.png",
            "photo" => "/assets/img/product-detail/popup/8.jpg",
            'value' => $arResult['PROPERTIES']['HRAN_NUM2']['VALUE']
        ],
        'LCD' => [
            'title' => 'LCD дисплей',
            'description' => 'LCD дисплей отображает всю информацию о режимах автоподзаводчика. В зависимости от модели подзаводчика, дисплей может отличаться цветом подсветки и отображаемой информацией.',
            "icon" => "/assets/img/product-detail/icons/picto-14.png",
            "photo" => "/assets/img/product-detail/popup/14.jpg",
        ],
        'TOUCHSCREEN' => [
            'title' => 'Тачскрин',
            'description' => 'Управление режимами и программами заводчика осуществляется с помощью тачскрина. Термин «тачскрин» появился в результате слияния слов «Touch» и «Screen», что с английского можно перевести дословно, как «реагирующий на прикосновение экран». В отличие от механического управления тачскрин отличается наглядностью и простотой управления. В зависимости от модели подзаводчика, дисплей тачскрина может отличаться функциональностью и цветом подсветки.',
            "icon" => "/assets/img/product-detail/icons/picto-15.png",
            "photo" => "/assets/img/product-detail/popup/15.jpg",
        ],
        'XXX_CIGAR_QTY' => [
            'title' => 'Количество сигар',
            'description' => 'Данная характеристика указыает на количество сигар, вмещаемое всеми отделами хьюмидора.',
            "icon" => "/assets/img/product-detail/icons/picto-16.png",
            "photo" => "/assets/img/product-detail/popup/16.jpg",
        ],
        'XXX_CIGAR_HUMIDIFIER' => [
            'title' => 'Испаритель',
            'description' => 'Испаритель воды представляет собой пластиковый бокс, содержащий внутри себя пористый вспененый материал. После пропитки его водой он, испаряя воду, поддерживает постоянную влажность и микроклимат внутри хьюмидора, предотвращая пересыхание сигар.',
            "icon" => "/assets/img/product-detail/icons/picto-17.png",
            "photo" => "/assets/img/product-detail/popup/17.jpg",
        ],
        'XXX_CIGAR_HYGROMETER' => [
            'title' => 'Гидрометр',
            'description' => 'Гидрометр представляет собой измерительный прибор, показывающий уровень влажности внутри хьюмидора. Бывает механический и цифровой.',
            "icon" => "/assets/img/product-detail/icons/picto-18.png",
            "photo" => "/assets/img/product-detail/popup/18.jpg",
        ],
        'XXX_CIGAR_CUTTER' => [
            'title' => 'Гильотина',
            'description' => 'В комплект изделия входит стальная гильотина.',
            "icon" => "/assets/img/product-detail/icons/picto-19.png",
            "photo" => "/assets/img/product-detail/popup/19.jpg",
        ],
        'XXX_MOBILE_HUMIDOR' => [
            'title' => 'Мобильность',
            'description' => 'Изделие является мобильным, что позволяет взять его с собой в дорогу, командировку или путешествие.',
            "icon" => "/assets/img/product-detail/icons/picto-20.png",
            "photo" => "/assets/img/product-detail/popup/20.jpg",
        ],
        'XXX_BUILT_IN_BATT' => [
            'title' => 'Встроенный аккумулятор',
            'description' => 'Изделие имеет встроенный аккумулятор, что обеспечивает его работу независимо от наличия электросети или батареек. Зарядка аккумулятора происходит автоматически через сетевой адаптер. Аккумулятор всегда готов к работе.',
            "icon" => "/assets/img/product-detail/icons/picto-21.png",
            "photo" => "/assets/img/product-detail/popup/21.jpg",
        ],
        'STOP_MOTOR' => [
            'title' => 'Автостоп',
            'description' => 'При открывании крышки шкатулки моторы автоматически отключаются. Это позволяет уберечь механизм шкатулки от поломки во время крепления часов. Часы можно устанавливать или извлекать из шкатулки только при остановленных роторах. Функция отключаемая. Для нее предусмотрен отдельный выключатель, расположенный на задней панели виндера для часов.',
            "icon" => "/assets/img/product-detail/icons/picto-22.png",
            "photo" => "/assets/img/product-detail/popup/22.jpg",
        ],
        'XXX_GLASSES_QTY2' => [
            'title' => 'Количество очков',
            'description' => 'Данная характеристика обозначает количество мест для хранения очков.',
            "icon" => "/assets/img/product-detail/icons/picto-23.png",
            "photo" => "/assets/img/product-detail/popup/23.jpg",
            'value' => $arResult['PROPERTIES']['XXX_GLASSES_QTY2']['VALUE']
        ],
        'XXX_ALARM' => [
            'title' => 'Сигналиция',
            'description' => 'При небольшой тряске сейфа, а также при трёхкратном неверном наборе кода для замка, срабатывает громкая звуковая сигналиция. Эта безусловно полезная функция как минимум спугнёт злоумышленников, посягнувших на Ваше драгоценное имущество.',
            "icon" => "/assets/img/product-detail/icons/picto-24.png",
            "photo" => "/assets/img/product-detail/popup/24.jpg",
        ],
        'XXX_WATER_RESISTANCE' => [
            'title' => 'Водонепроницаемость',
            'description' => 'Водонепроницаемость',
            "icon" => "/assets/img/product-detail/icons/picto-25.png",
            "photo" => "/assets/img/product-detail/popup/25.jpg",
        ],
    ];

    $productProps = [];
    foreach ($productPropsData as $propCode => $propData) {
        if (!empty($arResult['PROPERTIES'][$propCode]['VALUE'])) {
            $productProps[] = $propData;
        }
    }

    $materials = [
        'Металл' => [
            'title' => 'Металл',
            'description' => 'Элементы, выполненные из металла',
            "photo" => "/assets/img/product-detail/popup/10.jpg",
            "icon" => "/assets/img/product-detail/icons/picto-10.png"
        ],
        'Полиуретан' => [
            'title' => 'Полиуретан','description' => 'Элементы, выполненные из полиуретана',
            "icon" => "/assets/img/product-detail/icons/poly.png"
        ],
        'Бархат' => [
            'title' => 'Бархат','description' => 'Элементы, выполненные из бархата',
            "icon" => "/assets/img/product-detail/icons/velvet.png"
        ],
        'Акрил' => [
            'title' => 'Акрил','description' => 'Элементы, выполненные из акрила',
            "icon" => "/assets/img/product-detail/icons/acrylic.png"
        ],
        'Пластик' => [
            'title' => 'Пластик','description' => 'Элементы, выполненные из пластика',
            "icon" => "/assets/img/product-detail/icons/plastic.png"
        ],
        'Каучук' => [
            'title' => 'Каучук',
            'description' => 'Элементы, выполненные из каучука',
            "icon" => "/assets/img/product-detail/icons/picto-2715232.png",
            "photo" => "/assets/img/product-detail/popup/2715232.jpg",
        ],
        'Карбон' => [
            'title' => 'Карбон','description' => 'Элементы, выполненные из карбона',
            "icon" => "/assets/img/product-detail/icons/carbon.png"
        ],
        'Экокожа' => [
            'title' => 'Экокожа',
            'description' => 'Элементы, выполненные из экокожи',
            "icon" => "/assets/img/product-detail/icons/picto-13.png"
        ],
        'Дерево' => [
            'title' => 'Дерево',
            'description' => 'Элементы, выполненные из дерева',
            "icon" => "/assets/img/product-detail/icons/picto-11.png"
        ],
        'Искуственный Ротанг' => [
            'title' => 'Искуственный Ротанг',
            'description' => 'Элементы, выполненные из искуственного ротанга',
            "icon" => "/assets/img/product-detail/icons/rattan.png"
        ],
        'Натуральная кожа' => [
            'title' => 'Натуральная кожа',
            'description' => 'Элементы, выполненные из натуральной кожи',
            "icon" => "/assets/img/product-detail/icons/picto-12.png"
        ],
        'Ткань' => [
            'title' => 'Ткань','description' => 'Элементы, выполненные из ткани',
            "icon" => "/assets/img/product-detail/icons/textile.png"
        ],
        'Силикон' => [
            'title' => 'Силикон','description' => 'Элементы, выполненные из силикона',
            "icon" => "/assets/img/product-detail/icons/silicone.png"
        ],
        'Стекло' => [
            'title' => 'Стекло',
            'description' => 'Элементы, выполненные из стекла',
            "icon" => "/assets/img/product-detail/icons/picto-9.png"
        ],
        'Перламутр' => [
            'title' => 'Перламутр','description' => 'Элементы, выполненные из перламутра',
            "icon" => "/assets/img/product-detail/icons/perl.png"
        ],
        'Натур. кожа' => [
            'title' => 'Натуральная кожа',
            'description' => 'Элементы, выполненные из натуральной кожи',
            "icon" => "/assets/img/product-detail/icons/picto-12.png"
        ],
        'Материя' => [
            'title' => 'Материя','description' => 'Элементы, выполненные из материи',
            "icon" => "/assets/img/product-detail/icons/textile.png"
        ],
        'Кожа аллигатора'=> [
            'title' => 'Кожа аллигатора',
            'description' => 'Элементы, выполненные из кожи аллигатора',
            "icon" => "/assets/img/product-detail/icons/picto-2715220.png",
            "photo" => "/assets/img/product-detail/popup/2715220.jpg"
        ],
        'Кожа ската'=> [
            'title' => 'Кожа ската',
            'description' => 'Элементы, выполненные из кожи ската',
            "icon" => "/assets/img/product-detail/icons/picto-2715228.png",
            "photo" => "/assets/img/product-detail/popup/2715228.jpg"
        ],
        'Кожа теленка'=> [
            'title' => 'Кожа теленка',
            'description' => 'Элементы, выполненные из кожи теленка',
            "icon" => "/assets/img/product-detail/icons/picto-2715195.png",
            "photo" => "/assets/img/product-detail/popup/2715195.jpg"
        ],
        'Кожа акулы'=> [
            'title' => 'Кожа акулы',
            'description' => 'Элементы, выполненные из кожи акулы',
            "icon" => "/assets/img/product-detail/icons/picto-2715227.png",
            "photo" => "/assets/img/product-detail/popup/2715227.jpg"
        ],
        'Кожа крокодила'=> [
            'title' => 'Кожа крокодила',
            'description' => 'Элементы, выполненные из кожи крокодила',
            "icon" => "/assets/img/product-detail/icons/picto-2715220.png",
            "photo" => "/assets/img/product-detail/popup/2715220.jpg"
        ],
        'Искусственный шелк'=> [
            'title' => 'Искусственный шелк','description' => 'Элементы, выполненные из искусственного шелка',
            "icon" => "/assets/img/product-detail/icons/silk.png"
        ],
        'Сталь'=> [
            'title' => 'Сталь',
            'description' => 'Элементы, выполненные из стали',
            "icon" => "/assets/img/product-detail/icons/picto-2715240.png",
            "photo" => "/assets/img/product-detail/popup/2715240.jpg"
        ],
    ];

    if (array_key_exists($arResult['PROPERTIES']['IN_OTDELKA']['VALUE'], $materials)) {
        $productProps[] = $materials[$arResult['PROPERTIES']['IN_OTDELKA']['VALUE']];
    }
    if ($arResult['PROPERTIES']['IN_OTDELKA']['VALUE'] !== $arResult['PROPERTIES']['OUT_OTDELKA']['VALUE'] &&
        array_key_exists($arResult['PROPERTIES']['OUT_OTDELKA']['VALUE'], $materials)) {
        $productProps[] = $materials[$arResult['PROPERTIES']['OUT_OTDELKA']['VALUE']];
    }
    if (array_key_exists($arResult['PROPERTIES']['BRC_MATERIAL']['VALUE'], $materials)) {
        $productProps[] = $materials[$arResult['PROPERTIES']['BRC_MATERIAL']['VALUE']];
    }

    switch ($arResult['PROPERTIES']['XXX_STRAP_GENDER']['VALUE']) {
        case 'Мужской':
            $productProps[] = [
                'title' => 'Для мужчин',
                'description' => 'Для мужчин',
                "icon" => "/assets/img/product-detail/icons/picto-2615255.png",
                "photo" => "/assets/img/product-detail/popup/2615255.jpg",
            ];
            break;
        case 'Женский':
            $productProps[] = [
                'title' => 'Для женщин',
                'description' => 'Для женщин',
                "icon" => "/assets/img/product-detail/icons/picto-2615257.png",
                "photo" => "/assets/img/product-detail/popup/2615257.jpg",
            ];
            break;
        case 'Unisex':
            $productProps[] = [
                'title' => 'Унисекс',
                'description' => 'Унисекс',
                "icon" => "/assets/img/product-detail/icons/picto-2615256.png",
                "photo" => "/assets/img/product-detail/popup/2615256.jpg",
            ];
            break;
    }

    /*
     *  Get materials list:
        \CModule::IncludeModule("iblock" );
        $r = CIBlockProperty::GetPropertyEnum("IN_OTDELKA", [], ["IBLOCK_ID"=>9]);
        $in = [];
        while($p = $r->GetNext()) $in[] = $p['VALUE'];
        var_export($in);
    */

    //endregion icons
    ?>
    <script>
        var PRODUCT_PROPS = <?=CUtil::PhpToJSObject($productProps)?>;
    </script>
    <div class="visual-props" id="product-props">
        <!--сюда будут дорисовываться иконки со свойствами товара-->
    </div>
    <!--Product-props-icons end-->
</div>