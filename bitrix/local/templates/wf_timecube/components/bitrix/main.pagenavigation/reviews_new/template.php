<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var array $arResult
 * @var array $arParam
 * @var CBitrixComponentTemplate $this
 */

/** @var PageNavigationComponent $component */
$component = $this->getComponent();

$this->setFrameMode(true);

?>
<div class="pagination-container">
    <ul class="list-inline">
        <?
        if ($arResult["REVERSED_PAGES"] === true):
            $first = true;
            if ($arResult["CURRENT_PAGE"] < $arResult["PAGE_COUNT"]):
                if (($arResult["CURRENT_PAGE"] + 1) == $arResult["PAGE_COUNT"]):
                    ?>
                    <!--                <a class="modern-page-previous" href="--><?//=htmlspecialcharsbx($arResult["URL"])
                    ?><!--">--><?//=GetMessage("nav_prev")
                    ?><!--</a>-->
                <?
                else:
                    ?>
                    <!--                <a class="modern-page-previous" href="--><?//=htmlspecialcharsbx($component->replaceUrlTemplate($arResult["CURRENT_PAGE"]+1))
                    ?><!--">--><?//=GetMessage("nav_prev")
                    ?><!--</a>-->
                <?
                endif;

                if ($arResult["START_PAGE"] < $arResult["PAGE_COUNT"]):
                    $first = false;
                    ?>
                    <li class="page-item"><a class="page-link"
                                             href="<?= htmlspecialcharsbx($arResult["URL"]) ?>">1</a></li>
                    <?
                    if ($arResult["START_PAGE"] < ($arResult["PAGE_COUNT"] - 1)):
                        ?>
                        <li class="page-item"><a class="page-link"
                                                 href="<?= htmlspecialcharsbx($component->replaceUrlTemplate($arResult["START_PAGE"] + ($arResult["PAGE_COUNT"] - $arResult["START_PAGE"]) / 2)) ?>">...</a>
                        </li>
                    <?
                    endif;
                endif;
            endif;

            $page = $arResult["START_PAGE"];
            do {
                $pageNumber = $arResult["PAGE_COUNT"] - $page + 1;

                if ($page == $arResult["CURRENT_PAGE"]):
                    ?>
                    <li class="page-item"><span
                                class="<?= ($first ? "page-link " : "") ?>modern-page-current"><?= $pageNumber ?></span>
                    </li>
                <?
                elseif ($page == $arResult["PAGE_COUNT"]):
                    ?>
                    <li class="page-item"><a href="<?= htmlspecialcharsbx($arResult["URL"]) ?>"
                                             class="<?= ($first ? "page-link" : "") ?>"><?= $pageNumber ?></a>
                    </li>
                <?
                else:
                    ?>
                    <a href="<?= htmlspecialcharsbx($component->replaceUrlTemplate($page)) ?>"
                       class="<?= ($first ? "page-link" : "") ?>"><?= $pageNumber ?></a>
                <?
                endif;

                $page--;
                $first = false;
            } while ($page >= $arResult["END_PAGE"]);

            if ($arResult["CURRENT_PAGE"] > 1):
                if ($arResult["END_PAGE"] > 1):
                    if ($arResult["END_PAGE"] > 2):
                        ?>
                        <li class="page-item"><a class="page-link"
                                                 href="<?= htmlspecialcharsbx($component->replaceUrlTemplate(round($arResult["END_PAGE"] / 2))) ?>">...</a>
                        </li>
                    <?
                    endif;
                    ?>
                    <li class="page-item"><a
                                href="<?= htmlspecialcharsbx($component->replaceUrlTemplate(1)) ?>"><?= $arResult["PAGE_COUNT"] ?></a>
                    </li>
                <?
                endif;

                ?>
                <!--            <a class="modern-page-next" href="--><?//=htmlspecialcharsbx($component->replaceUrlTemplate($arResult["CURRENT_PAGE"]-1))
                ?><!--">--><?//=GetMessage("nav_next")
                ?><!--</a>-->
            <?
            endif;

        else:
            $first = true;

            if ($arResult["CURRENT_PAGE"] > 1):
                if ($arResult["CURRENT_PAGE"] > 2):
                    ?>
                    <!--                <a class="modern-page-previous" href="--><?//=htmlspecialcharsbx($component->replaceUrlTemplate($arResult["CURRENT_PAGE"]-1))
                    ?><!--">--><?//=GetMessage("nav_prev")
                    ?><!--</a>-->
                <?
                else:
                    ?>
                    <!--                <a class="modern-page-previous" href="--><?//=htmlspecialcharsbx($arResult["URL"])
                    ?><!--">--><?//=GetMessage("nav_prev")
                    ?><!--</a>-->
                <?
                endif;

                if ($arResult["START_PAGE"] > 1):
                    $first = false;
                    ?>
                    <li class="page-item"><a class="page-link"
                                             href="<?= htmlspecialcharsbx($arResult["URL"]) ?>">1</a></li>
                    <?
                    if ($arResult["START_PAGE"] > 2):
                        ?>
                        <li class="page-item"><a class="page-link"
                                                 href="<?= htmlspecialcharsbx($component->replaceUrlTemplate(round($arResult["START_PAGE"] / 2))) ?>">...</a>
                        </li>
                    <?
                    endif;
                endif;
            endif;

            $page = $arResult["START_PAGE"];
            do {
                if ($page == $arResult["CURRENT_PAGE"]):
                    ?>
                    <li class="page-item"><span
                                class="<?= ($first ? "page-link " : "") ?>modern-page-current"><?= $page ?></span>
                    </li>
                <?
                elseif ($page == 1):
                    ?>
                    <li class="page-item"><a href="<?= htmlspecialcharsbx($arResult["URL"]) ?>"
                                             class="<?= ($first ? "page-link" : "") ?>">1</a></li>
                <?
                else:
                    ?>
                    <li class="page-item"><a href="<?= htmlspecialcharsbx($component->replaceUrlTemplate($page)) ?>"
                                             class="<?= ($first ? "page-link" : "") ?>"><?= $page ?></a></li>
                <?
                endif;

                $page++;
                $first = false;
            } while ($page <= $arResult["END_PAGE"]);

            if ($arResult["CURRENT_PAGE"] < $arResult["PAGE_COUNT"]):
                if ($arResult["END_PAGE"] < $arResult["PAGE_COUNT"]):
                    if ($arResult["END_PAGE"] < ($arResult["PAGE_COUNT"] - 1)):
                        ?>
                        <li class="page-item"><a class="page-link"
                                                 href="<?= htmlspecialcharsbx($component->replaceUrlTemplate(round($arResult["END_PAGE"] + ($arResult["PAGE_COUNT"] - $arResult["END_PAGE"]) / 2))) ?>">...</a>
                        </li>
                    <?
                    endif;
                    ?>
                    <li class="page-item"><a
                                href="<?= htmlspecialcharsbx($component->replaceUrlTemplate($arResult["PAGE_COUNT"])) ?>"><?= $arResult["PAGE_COUNT"] ?></a>
                    </li>
                <?
                endif;
                ?>
                <!--            <a class="modern-page-next" href="--><?//=htmlspecialcharsbx($component->replaceUrlTemplate($arResult["CURRENT_PAGE"]+1))
                ?><!--">--><?//=GetMessage("nav_next")
                ?><!--</a>-->
            <?
            endif;
        endif;

        if ($arResult["SHOW_ALL"]):
            if ($arResult["ALL_RECORDS"]):
                ?>
                <!--            <a class="modern-page-pagen" href="--><?//=htmlspecialcharsbx($arResult["URL"])
                ?><!--">--><?//=GetMessage("nav_paged")
                ?><!--</a>-->
            <?
            else:
                ?>
                <!--            <a class="modern-page-all" href="--><?//=htmlspecialcharsbx($component->replaceUrlTemplate("all"))
                ?><!--">--><?//=GetMessage("nav_all")
                ?><!--</a>-->
            <?
            endif;
        endif
        ?>
    </ul>
</div>
