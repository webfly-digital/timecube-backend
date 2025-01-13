<? use Bitrix\Main\Application;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
$request = Application::getInstance()->getContext()->getRequest();
?>
<nav>
    <div class="wrapper">
        <? if (0 < $arResult["SECTIONS_COUNT"]) { ?>
            <ul class="nav-main" id="nav-main"><?
                foreach ($arResult['SECTIONS'] as $arSection){
                $active = false !== strripos($request->getRequestUri(), $arSection["SECTION_PAGE_URL"]);
                ?>
                <li class="nav-main__item <? if ($active) echo 'active'?>">
                    <a class="nav-main__link <?if (!empty($arSection['SUBS'])) echo 'dd '. ($active ? '' : 'collapsed')?>" <?if (!empty($arSection['SUBS'])) echo 'data-toggle="collapse"'?>
                       href="<?= empty($arSection['SUBS']) ? $arSection['SECTION_PAGE_URL'] : '#sm-'.$arSection["ID"]?>">
                        <span><?= $arSection["NAME"]; ?></span>
                    </a>
                    <?if (!empty($arSection['SUBS'])) {?>
                        <ul class="nav-main__submenu <?= $active ? 'show' : 'collapse'?>" data-parent="#nav-main" id="sm-<?= $arSection["ID"] ?>">
                            <li class="<? if ($active) echo 'active'?>">
                                <a href="<?=$arSection['SECTION_PAGE_URL']?>">Все</a>
                            </li>
                        <?foreach ($arSection['SUBS'] as $subSection){
                            $active = false !== strripos($request->getRequestUri(), $subSection["SECTION_PAGE_URL"]);
                            ?>
                            <li class="<? if ($active) echo 'active'?>">
                                <a href="<?=$subSection['SECTION_PAGE_URL']?>"><?=$subSection['NAME']?></a>
                            </li>
                        <?}?>
                        </ul>
                    <?}?>
                </li>
                <? } ?>
            </ul>
        <? } ?>
    </div>
</nav>
<? return ?>
<nav>
    <div class="wrapper">
        <ul class="nav-main">
            <li class="nav-main__item">
                <a class="nav-main__link dd collapsed" href="#sm-0" data-toggle="collapse"><span>Шкатулки для часов с автоподзаводом</span></a>
                <ul class="nav-main__submenu collapse" id="sm-0">
                    <li><a href="/catalog-section.html">Все</a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
