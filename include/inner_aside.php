<aside class="three-columns__sidebar bg-white">
    <div class="three-columns__sticky">
        <div class="sidebar-menu">
            <div class="sidebar-menu__header">
                <p class="sidebar-menu__title"><a data-toggle="collapse" href="#sidebar-menu">Покупателю</a></p>
            </div>
            <div class="sidebar-menu__body collapse show" id="sidebar-menu">
                <?$APPLICATION->IncludeComponent(
                    "bitrix:menu",
                    "sidebar",
                    array(
                        "ROOT_MENU_TYPE" => "top",
                        "MENU_CACHE_TYPE" => "A",
                        "MENU_CACHE_TIME" => "3600",
                        "MENU_CACHE_USE_GROUPS" => "N",
                        "MENU_CACHE_GET_VARS" => array(
                        ),
                        "MAX_LEVEL" => "1",
                        "CHILD_MENU_TYPE" => "",
                        "USE_EXT" => "N",
                        "DELAY" => "N",
                        "ALLOW_MULTI_SELECT" => "N"
                    ),
                    false
                );?>
            </div>
        </div>
    </div>
</aside>