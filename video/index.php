<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Видео");
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
                    <h4>Зачем нужна шкатулка для часов с автоподзаводом:</h4>

                    <div class="vid" align="center"><iframe width="560" height="315" src="https://www.youtube.com/embed/vnE4E8Ls9Zk" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>

                    <h4>Как правильно подобрать шкатулку для часов с автоподзаводом:</h4>

                    <div class="vid" align="center"><iframe width="560" height="315" src="https://www.youtube.com/embed/nqaoSBTRg44" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>

                    <h4>Шкатулка для часов с автоподзаводом Fancy Brick:</h4>

                    <div class="vid" align="center"> <iframe width="560" height="315" src="https://www.youtube.com/embed/ae69DkybKjA" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>

                    <h4>Шкатулки для часов с автоподзаводом MODALO серия III:</h4>

                    <div class="vid" align="center"> <iframe width="560" height="315" src="https://www.youtube.com/embed/QZLgpyoR5Gc" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>

                </div>
            </div>

    </section>
        <?$APPLICATION->IncludeComponent("bitrix:main.include","",
            ["AREA_FILE_SHOW" => "file","PATH" => SITE_DIR."include/inner_aside.php"], false, ['HIDE_ICONS' => 'Y']
        );?>
    </div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>