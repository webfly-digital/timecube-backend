<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @global CMain $APPLICATION
 * @var array    $arParams
 * @var array    $arResult
 */

$this->setFrameMode(true);

$excludeProps = ['PRICE', 'OLD_PRICE', "MORE_URLS", "VIDEO"];

$QueryTitle = COption::GetOptionString('alexkova.corporate', 'query_button_title', GetMessage('QUERY_BUTTON_TITLE'));
$SaleTitle = COption::GetOptionString('alexkova.corporate', 'query_button_title', GetMessage('SALE_BUTTON_TITLE'));
?>
<?php if (!empty($arParams["DETAIL_FIELD_CODE"]) && is_array($arParams["DETAIL_FIELD_CODE"]) &&
    (in_array("DATE_ACTIVE_FROM", $arParams["DETAIL_FIELD_CODE"]) || in_array("DATE_ACTIVE_TO", $arParams["DETAIL_FIELD_CODE"]))): ?>
    <div class="date-news">
        <?php if (in_array("DATE_ACTIVE_FROM", $arParams["DETAIL_FIELD_CODE"])): ?>

            <?= $arResult["ACTIVE_FROM"] ?>

        <?php endif; ?>
        <?php if (in_array("DATE_ACTIVE_TO", $arParams["DETAIL_FIELD_CODE"])): ?>

            / <?= $arResult["DATE_ACTIVE_TO"] ?>

        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if (is_array($arResult["DETAIL_PICTURE"])): ?>
    <div class="bxr-news-image">
        <img src="<?= $arResult["DETAIL_PICTURE"]["SRC"] ?>">
    </div>
<?php endif; ?>


<?php if (count($arResult["FILES"]) > 0
    || count($arResult["LINKS"]) > 0
    || count($arResult["VIDEO"]) > 0): ?>


    <ul class="nav nav-tabs" role="tablist" id="details">

        <li role="presentation" class="active"><a href="#description" aria-controls="description" role="tab" data-toggle="tab"><?= GetMessage("DETAIL_TEXT_DESC") ?></a></li>

        <?php if (count($arResult["VIDEO"]) > 0): ?>
            <li role="presentation"><a href="#video" aria-controls="video" role="tab" data-toggle="tab"><?= GetMessage("VIDEO_TAB_DESC") ?></a></li>
        <?php endif; ?>
        <?php if (count($arResult["FILES"]) > 0): ?>
            <li role="presentation"><a href="#files" aria-controls="files" role="tab" data-toggle="tab"><?= GetMessage("CATALOG_FILES") ?></a></li>
        <?php endif; ?>
        <?php if (count($arResult["LINKS"]) > 0): ?>
            <li role="presentation"><a href="#links" aria-controls="video" role="tab" data-toggle="tab"><?= GetMessage("LINKS_TAB_DESC") ?></a></li>
        <?php endif; ?>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade in active" id="description">
            <hr/><?php echo $arResult["DETAIL_TEXT"]; ?>
        </div>

        <?php if (count($arResult["FILES"]) > 0): ?>
            <div id="files" class="element-files tb20 tab-pane fade" role="tabpanel">
                <hr/>
                <?php foreach ($arResult["FILES"] as $val): ?>

                    <?php $template = "file_element";
                    $arElementDrawParams = [
                        "DISPLAY_VARIANT" => $template,
                        "ELEMENT" => [
                            "NAME" => $val["ORIGINAL_NAME"],
                            "LINK" => $val["SRC"],
                            "CLASS_NAME" => $val["EXTENTION"]
                        ]
                    ];
                    ?>
                    <?php
                    $APPLICATION->IncludeComponent(
                        "alexkova.corporate:element.draw",
                        ".default",
                        $arElementDrawParams,
                        false
                    )
                    ?>

                <?php endforeach; ?>

            </div>
            <div class="clearfix"></div>
        <?php endif; ?>

        <?php if (count($arResult["LINKS"]) > 0): ?>
            <div id="links" class="element-files tb20 tab-pane fade" role="tabpanel">
                <hr/>
                <?php foreach ($arResult["LINKS"] as $val): ?>

                    <?php $template = "glyph_links";
                    $arElementDrawParams = [
                        "DISPLAY_VARIANT" => $template,
                        "ELEMENT" => [
                            "NAME" => $val["TITLE"],
                            "LINK" => $val["LINK"],
                            "GLYPH" => ["GLYPH_CLASS" => "glyphicon-chevron-right"],
                            "TARGET" => "_blank"
                        ]
                    ];
                    ?>
                    <?php
                    $APPLICATION->IncludeComponent(
                        "alexkova.corporate:element.draw",
                        ".default",
                        $arElementDrawParams,
                        false
                    )
                    ?>

                <?php endforeach; ?>

            </div>
            <div class="clearfix"></div>
        <?php endif; ?>

        <?php if (count($arResult["VIDEO"]) > 0): ?>
            <div id="video" class="element-files tb20 tab-pane fade" role="tabpanel">
                <hr/>
                <?php foreach ($arResult["VIDEO"] as $val): ?>

                    <?php $template = "video_card";
                    $arElementDrawParams = [
                        "DISPLAY_VARIANT" => $template,
                        "ELEMENT" => [
                            "VIDEO" => $val["LINK"],                  //ссылка на видео
                            "VIDEO_IMG" => '',               //ссылка на картинку
                            "VIDEO_IMG_WIDTH" => '150',         //ширина картинки для видео
                            "NAME" => $val["TITLE"]
                        ]
                    ];


                    ?>
                    <div class="col-lg-3">
                        <?php
                        $APPLICATION->IncludeComponent(
                            "alexkova.corporate:element.draw",
                            ".default",
                            $arElementDrawParams,
                            false
                        )
                        ?>
                    </div>

                <?php endforeach; ?>

            </div>
            <div class="clearfix"></div>
        <?php endif; ?>


    </div>

    <script>
        $(function () {
            $('#details a').click(function (e) {
                e.preventDefault();
                $(this).tab('show')
            })
        })
    </script>
<?php else: ?>
    <?php echo $arResult["DETAIL_TEXT"]; ?>
<?php endif; ?>