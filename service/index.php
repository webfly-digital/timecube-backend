<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Собственный сервисный центр");
?>
    <div class="three-columns" id="inner-page">
    <section class="three-columns__body">
        <div class="container-fluid breadcrumbs-wrapper">
            <? $APPLICATION->IncludeComponent(
                "bitrix:breadcrumb",
                "catalog",
                array(
                    "PATH" => "",
                    "SITE_ID" => "s1",
                    "START_FROM" => "0"
                ),
                false,
                array(
                    'HIDE_ICONS' => 'Y'
                )
            ); ?>

            <div class="container-fluid">
                <div class="heading">
                    <div class="heading__item">
                        <h1 class="heading__title">Собственный сервисный центр</h1>
                    </div>
                </div>
                <div class="text-content">
                    <p>
                        Наши покупатели не брошены на произвол судьбы после покупки товара. В собственном сервисном
                        центре профессиональные мастера осуществят гарантийное и послегарантийное сервисное обслуживание
                        <a href="https://timecube.ru/shkatulki_dlya_chasov/">шкатулок</a> и <a
                                href="https://timecube.ru/shkatulki-dlya-chasov-s-avtopodzavodom/">подзаводчиков</a>.
                    </p>
                    <div class="media-card-h">
                        <div class="media-card-h__pic">
                            <a href="/service/polirovka/"><img src="null" class="lozad"
                                                               data-src="images/serv-1.jpg"></a>
                        </div>
                        <div class="media-card-h__content">
                            <h3 class="media-card-h__title">
                                <a href="/service/polirovka/">Полировочные работы</a>
                            </h3>
                            <p>
                                Полируем лаковые поверхности изделий
                            </p>
                            <p>
                                <a href="/service/polirovka/">Подробнее</a>
                            </p>
                        </div>
                    </div>
                    <div class="media-card-h">
                        <div class="media-card-h__pic">
                            <a href="/service/komplektuyuschie/"><img src="null" class="lozad"
                                                                      data-src="images/serv-2.jpg"></a>
                        </div>
                        <div class="media-card-h__content">
                            <h3 class="media-card-h__title">
                                <a href="/service/komplektuyuschie/">Оригинальные комплектующие</a>
                            </h3>
                            <p>
                                Фирменные запчасти в наличии и на заказ
                            </p>
                            <p>
                                <a href="/service/komplektuyuschie/">Подробнее</a>
                            </p>
                        </div>
                    </div>
                    <h2 class="h3">
                        Отдел сервиса и ремонта
                    </h2>
                    <p>
                        г. Москва, Маломосковская ул. 22с1, 2-й этаж, офис 209.
                    </p>
                    <div class="persons-list">
                        <div class="person">
                            <div class="person__photo">
                                <img alt="Вячеслав, Специалист сервисного центра" src="null" class="lozad"
                                     data-src="images/serviceman.jpg">
                            </div>
                            <div class="person__content">
                                <p class="person__name">
                                    Вячеслав
                                </p>
                                <p class="person__position">
                                    Специалист сервисного центра
                                </p>
                                <p class="person__phone">
                                    8 495 686-20-36
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>
<? $APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "",
    array(
        "AREA_FILE_SHOW" => "file",
        "PATH" => SITE_DIR . "include/inner_aside.php"
    ),
    false,
    array(
        'HIDE_ICONS' => 'Y'
    )
); ?>
    </div>
    <br><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>