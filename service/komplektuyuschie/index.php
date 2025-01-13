<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Оригинальные комплектующие");
?>
    <div class="three-columns" id="inner-page">
        <section class="three-columns__body">
            <div class="container-fluid breadcrumbs-wrapper">
                <?
                $APPLICATION->IncludeComponent("bitrix:breadcrumb","catalog",
                    ["START_FROM" => "0","PATH" => "","SITE_ID" => "s1"],
                    false,["HIDE_ICONS" => "Y"]
                );
                ?>
            </div>
            <div class="container-fluid">
                <div class="heading">
                    <div class="heading__item">
                        <h1 class="heading__title"><?=$APPLICATION->ShowTitle(true)?></h1>
                    </div>
                </div>

                <div class="text-content">
                    <p>Прямые поставки от производителей:</p>
                    <div class="media-card-h">
                        <div class="media-card-h__pic">
                            <a href="/aksessuary_dlya_shkatulok_dlya_chasov_s_avtopodzavodom/">
                                <img class="lozad" data-src="/service/images/oa-1.jpg">
                            </a>
                        </div>
                        <div class="media-card-h__content">
                            <p class="media-card-h__title"><a href="/aksessuary_dlya_shkatulok_dlya_chasov_s_avtopodzavodom/">Электромоторы и редукторы</a></p>
                            <p><a href="/aksessuary_dlya_shkatulok_dlya_chasov_s_avtopodzavodom/">Подробнее</a></p>
                        </div>
                    </div>

                    <div class="media-card-h">
                        <div class="media-card-h__pic">
                            <a href="/aksessuary_dlya_shkatulok_dlya_chasov_s_avtopodzavodom/">
                                <img class="lozad" data-src="/service/images/oa-2.jpg">
                            </a>
                        </div>
                        <div class="media-card-h__content">
                            <p class="media-card-h__title"><a href="/aksessuary_dlya_shkatulok_dlya_chasov_s_avtopodzavodom/">Микросхемы и платы управления</a></p>
                            <p><a href="/aksessuary_dlya_shkatulok_dlya_chasov_s_avtopodzavodom/">Подробнее</a></p>
                        </div>
                    </div>

                    <div class="media-card-h">
                        <div class="media-card-h__pic">
                            <a href="/aksessuary_dlya_shkatulok_dlya_chasov_s_avtopodzavodom/">
                                <img class="lozad" data-src="/service/images/oa-3.jpg">
                            </a>
                        </div>
                        <div class="media-card-h__content">
                            <p class="media-card-h__title"><a href="/aksessuary_dlya_shkatulok_dlya_chasov_s_avtopodzavodom/">Подушечки для шкатулок с автоподзаводом</a></p>
                            <p><a href="/aksessuary_dlya_shkatulok_dlya_chasov_s_avtopodzavodom/">Подробнее</a></p>
                        </div>
                    </div>
                </div>
            </div>

    </section>
        <?$APPLICATION->IncludeComponent("bitrix:main.include","",
            ["AREA_FILE_SHOW" => "file","PATH" => SITE_DIR."include/inner_aside.php"], false, ['HIDE_ICONS' => 'Y']
        );?>
    </div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>