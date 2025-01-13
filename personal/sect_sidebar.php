<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<aside class="three-columns__sidebar bg-white">
    <div class="three-columns__sticky">
        <div class="sidebar-menu">
            <div class="sidebar-menu__header">
                <p class="sidebar-menu__title"><a data-toggle="collapse" href="#sidebar-menu">Личный кабинет</a></p>
            </div>
            <div class="sidebar-menu__body collapse show" id="sidebar-menu">
                <?$APPLICATION->IncludeComponent("bitrix:menu", "sidebar", array(
                    "ROOT_MENU_TYPE" => "personal",
                    "MAX_LEVEL" => "1",
                    "MENU_CACHE_TYPE" => "A",
                    "CACHE_SELECTED_ITEMS" => "N",
                    "MENU_CACHE_TIME" => "36000000",
                    "MENU_CACHE_USE_GROUPS" => "Y",
                    "MENU_CACHE_GET_VARS" => array(),
                ),
                    false
                );?>
            </div>
        </div>
    </div>
</aside>



