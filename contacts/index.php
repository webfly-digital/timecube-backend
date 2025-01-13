<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetPageProperty("description", "Контакты интернет-магазина шкатулок для часов и украшений Timecube в Москве. Звоните: 8 800 775-25-76!");
$APPLICATION->SetPageProperty("title", "Контакты магазина Timecube в Москве");
$APPLICATION->SetTitle("Контакты");

?><div class="three-columns" id="inner-page">
 <section class="three-columns__body">
	<div class="container-fluid breadcrumbs-wrapper">
		 <?$APPLICATION->IncludeComponent(
	"bitrix:breadcrumb",
	"catalog",
	Array(
		"PATH" => "",
		"SITE_ID" => "s1",
		"START_FROM" => "0"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?>
	</div>
	<div class="container-fluid">
		<div class="heading__item">
			<h1 class="heading__title"><?= $APPLICATION->ShowTitle(true) ?></h1>
		</div>
		<div class="contacts-grid mt-md-5">
			<div class="contact-group contact-group--wide">
				 <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"EDIT_MODE" => "html",
		"PATH" => SITE_DIR."include/contacts_message.php"
	),
false,
Array(
	'HIDE_ICONS' => 'N'
)
);?>
			</div>
			<div class="contact-group">
				<h4 class="contact-group__title">
				Адрес интернет-магазина </h4>
				<div class="contact-group__content">
					 <!-- Address begin-->
					<div class="address-block">
						 <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => SITE_DIR."include/address.php"
	),
false,
Array(
	'HIDE_ICONS' => 'N'
)
);?>
					</div>
					 <!-- Address end-->
				</div>
			</div>
			<div class="contact-group">
				<h4 class="contact-group__title">
				Телефоны </h4>
				<div class="contact-group__content">
					 <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"EDIT_MODE" => "html",
		"PATH" => SITE_DIR."include/contacts_phones.php"
	),
false,
Array(
	'HIDE_ICONS' => 'N'
)
);?>
				</div>
			</div>
			<div class="contact-group">
				<h4 class="contact-group__title">
				Почта </h4>
				<div class="contact-group__content">
					<p>
						 <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"EDIT_MODE" => "html",
		"PATH" => SITE_DIR."include/email.php"
	),
false,
Array(
	'HIDE_ICONS' => 'N'
)
);?>
					</p>
				</div>
			</div>
			<div class="contact-group">
				<h4 class="contact-group__title">
				Режим работы </h4>
				<div class="contact-group__content">
					 <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => SITE_DIR."include/schedule.php"
	),
false,
Array(
	'HIDE_ICONS' => 'N'
)
);?>
				</div>
			</div>
			<div class="contact-group">
				<h4 class="contact-group__title">
				Социальные сети </h4>
				<div class="contact-group__content">
					 <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"EDIT_MODE" => "html",
		"PATH" => SITE_DIR."include/socnet_sidebar.php"
	),
false,
Array(
	'HIDE_ICONS' => 'N'
)
);?>
				</div>
			</div>
			<div class="contact-group contact-group--wide">
				<h2 class="contact-group__title">
				Магазин на карте </h2>
				<div class="contact-group__content">
					<div class="contact-group__map">
						<div class="map-box" id="map_box">
							 <script type="text/javascript" charset="utf-8" async
                                        src="https://api-maps.yandex.ru/services/constructor/1.0/js/?sid=qND0tujRcuWmfZSg6IWRLzxSDElIHBm0&amp;amp;width=100%&amp;amp;height=513&amp;amp;lang=ru_RU&amp;amp;sourceType=constructor&amp;amp;scroll=false&amp;amp;id=map_box"></script>
						</div>
					</div>
				</div>
			</div>
			<div class="contact-group contact-group--wide">
				<h2 class="contact-group__title">
				Как пройти в магазин? </h2>
				<div class="contact-group__content">
					<div class="photo-group">
						<p class="photo-group__title caption-gray">
							 1. Дойти до конца здания, до одноэтажной проходной с надписью «Чистый город».
						</p>
 <img src="null" class="lozad" data-src="/assets/img/contacts/1.jpg" alt="">
					</div>
					<div class="photo-group">
						<p class="photo-group__title caption-gray">
							 2. Войти на проходную.
						</p>
 <img src="null" class="lozad" data-src="/assets/img/contacts/2.jpg" alt="">
					</div>
					<div class="photo-group">
						<p class="photo-group__title caption-gray">
							 3. После проходной в центральный вход со ступеньками.
						</p>
 <img src="null" class="lozad" data-src="/assets/img/contacts/3.jpg" alt="">
					</div>
					<div class="photo-group">
						<p class="photo-group__title caption-gray">
							 4. Поднимаетесь на второй этаж.
						</p>
 <img src="null" class="lozad" data-src="/assets/img/contacts/4.jpg" alt="">
					</div>
					<div class="photo-group">
						<p class="photo-group__title caption-gray">
							 5. По коридору направо.
						</p>
 <img src="null" class="lozad" data-src="/assets/img/contacts/5.jpg" alt="">
					</div>
					<div class="photo-group">
						<p class="photo-group__title caption-gray">
							 6. Вторая дверь справа.
						</p>
 <img src="null" class="lozad" data-src="/assets/img/contacts/6.jpg" alt="">
					</div>
					<div class="photo-group">
						<p class="photo-group__title caption-gray">
							 7. Офис 209. Добро пожаловать.
						</p>
 <img src="null" class="lozad" data-src="/assets/img/contacts/7.jpg" alt="">
					</div>
					<div class="photo-group">
						<p class="photo-group__title caption-gray">
							 8. Можете обратиться к любому менеджеру. Вам с радостью помогут.
						</p>
 <img src="null" class="lozad" data-src="/assets/img/contacts/8.jpg" alt="">
					</div>
					<div class="photo-group">
						<p class="photo-group__title caption-gray">
							 9. Склад продукции находится тут же. Все товары в наличии.
						</p>
 <img src="null" class="lozad" data-src="/assets/img/contacts/9.jpg" alt="">
					</div>
				</div>
			</div>
			 <?$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"contact.list",
	Array(
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"ADD_SECTIONS_CHAIN" => "N",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"CHECK_DATES" => "Y",
		"COMPONENT_TEMPLATE" => "contact.list",
		"DETAIL_URL" => "",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_DATE" => "N",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"FIELD_CODE" => array(0=>"NAME",1=>"PREVIEW_PICTURE",2=>"",),
		"FILTER_NAME" => "",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"IBLOCK_ID" => "29",
		"IBLOCK_TYPE" => "timegear",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"INCLUDE_SUBSECTIONS" => "N",
		"MESSAGE_404" => "",
		"NEWS_COUNT" => "20",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => ".default",
		"PAGER_TITLE" => "Новости",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"PREVIEW_TRUNCATE_LEN" => "",
		"PROPERTY_CODE" => array(0=>"EMAIL",1=>"POSITION",2=>"DEPT",3=>"PHONE",4=>"",),
		"SET_BROWSER_TITLE" => "N",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_STATUS_404" => "N",
		"SET_TITLE" => "N",
		"SHOW_404" => "N",
		"SORT_BY1" => "SORT",
		"SORT_BY2" => "ACTIVE_FROM",
		"SORT_ORDER1" => "ASC",
		"SORT_ORDER2" => "ASC",
		"STRICT_SECTION_CHECK" => "N"
	)
);?> <!--                <div class="contact-group contact-group--wide">--> <!--                    <p class="contact-group__title">--> <!--                        Сотрудники--> <!--                    </p>--> <!--                    <div class="contact-group__content">--> <!--                        <div class="persons-list">--> <!--                            <div class="person">--> <!--                                <div class="person__photo">--> <!--                                    <img alt="Алексей, Генеральный директор" src="null" class="lozad"--> <!--                                         data-src="/assets/img/team/1.jpg">--> <!--                                </div>--> <!--                                <div class="person__content">--> <!--                                    <p class="person__name">--> <!--                                        Алексей--> <!--                                    </p>--> <!--                                    <p class="person__position">--> <!--                                        Генеральный директор--> <!--                                    </p>--> <!--                                    <p class="person__phone">--> <!--                                        8 495 686-20-36--> <!--                                    </p>--> <!--                                    <p class="person__email">--> <!--                                        <a href="mailto:alex@timecube.ru">alex@timecube.ru</a>--> <!--                                    </p>--> <!--                                </div>--> <!--                            </div>--> <!--                            <div class="person">--> <!--                                <div class="person__photo">--> <!--                                    <img alt="Людмила, Начальник отдела розничных продаж" src="null" class="lozad"--> <!--                                         data-src="/assets/img/team/2.jpg">--> <!--                                </div>--> <!--                                <div class="person__content">--> <!--                                    <p class="person__name">--> <!--                                        Людмила--> <!--                                    </p>--> <!--                                    <p class="person__position">--> <!--                                        Начальник отдела розничных продаж--> <!--                                    </p>--> <!--                                    <p class="person__phone">--> <!--                                        8 495 984-04-83--> <!--                                    </p>--> <!--                                    <p class="person__email">--> <!--                                        <a href="mailto:info@timecube.ru">info@timecube.ru</a>--> <!--                                    </p>--> <!--                                </div>--> <!--                            </div>--> <!--                            <div class="person">--> <!--                                <div class="person__photo">--> <!--                                    <img alt="Светлана, Начальник отдела оптовых продаж" src="null" class="lozad"--> <!--                                         data-src="/assets/img/team/3.jpg">--> <!--                                </div>--> <!--                                <div class="person__content">--> <!--                                    <p class="person__name">--> <!--                                        Светлана--> <!--                                    </p>--> <!--                                    <p class="person__position">--> <!--                                        Начальник отдела оптовых продаж--> <!--                                    </p>--> <!--                                    <p class="person__phone">--> <!--                                        8 495 687-35-18--> <!--                                    </p>--> <!--                                    <p class="person__email">--> <!--                                        <a href="mailto:sveta@timecube.ru">sveta@timecube.ru</a>--> <!--                                    </p>--> <!--                                </div>--> <!--                            </div>--> <!--                            <div class="person">--> <!--                                <div class="person__photo">--> <!--                                    <img alt="Вячеслав, Специалист сервисного центра" src="null" class="lozad"--> <!--                                         data-src="/assets/img/team/4.jpg">--> <!--                                </div>--> <!--                                <div class="person__content">--> <!--                                    <p class="person__name">--> <!--                                        Вячеслав--> <!--                                    </p>--> <!--                                    <p class="person__position">--> <!--                                        Специалист сервисного центра--> <!--                                    </p>--> <!--                                    <p class="person__phone">--> <!--                                        8 495 686-20-36--> <!--                                    </p>--> <!--                                    <p class="person__email">--> <!--                                        <a href="mailto:slava@timecube.ru">slava@timecube.ru</a>--> <!--                                    </p>--> <!--                                </div>--> <!--                            </div>--> <!--                            <div class="person">--> <!--                                <div class="person__photo">--> <!--                                    <img alt="Татьяна, Начальник отдела маркетплейсов" src="null" class="lozad"--> <!--                                         data-src="/assets/img/team/5.jpg">--> <!--                                </div>--> <!--                                <div class="person__content">--> <!--                                    <p class="person__name">--> <!--                                        Татьяна--> <!--                                    </p>--> <!--                                    <p class="person__position">--> <!--                                        Начальник отдела маркетплейсов--> <!--                                    </p>--> <!--                                    <p class="person__phone">--> <!--                                        8 800 775-25-76--> <!--                                    </p>--> <!--                                    <p class="person__email">--> <!--                                        <a href="mailto:tatyana@timecube.ru">tatyana@timecube.ru</a>--> <!--                                    </p>--> <!--                                </div>--> <!--                            </div>--> <!--                            <div class="person">--> <!--                                <div class="person__photo">--> <!--                                    <img alt="Вячеслав, Продавец-консультант" src="null" class="lozad"--> <!--                                         data-src="/assets/img/team/6.jpg">--> <!--                                </div>--> <!--                                <div class="person__content">--> <!--                                    <p class="person__name">--> <!--                                        Вячеслав--> <!--                                    </p>--> <!--                                    <p class="person__position">--> <!--                                        Продавец-консультант--> <!--                                    </p>--> <!--                                    <p class="person__phone">--> <!--                                        8 495 687-44-37--> <!--                                    </p>--> <!--                                    <p class="person__email">--> <!--                                        <a href="mailto:info@timecube.ru">info@timecube.ru</a>--> <!--                                    </p>--> <!--                                </div>--> <!--                            </div>--> <!--                            <div class="person">--> <!--                                <div class="person__photo">--> <!--                                    <img alt="Максим, Продавец-консультант" src="null" class="lozad"--> <!--                                         data-src="/assets/img/team/7.jpg">--> <!--                                </div>--> <!--                                <div class="person__content">--> <!--                                    <p class="person__name">--> <!--                                        Максим--> <!--                                    </p>--> <!--                                    <p class="person__position">--> <!--                                        Продавец-консультант--> <!--                                    </p>--> <!--                                    <p class="person__phone">--> <!--                                        8 495 984-04-83--> <!--                                    </p>--> <!--                                    <p class="person__email">--> <!--                                        <a href="mailto:max@timecube.ru">max@timecube.ru</a>--> <!--                                    </p>--> <!--                                </div>--> <!--                            </div>--> <!--                            <div class="person">--> <!--                                <div class="person__photo">--> <!--                                    <img alt="Андрей, Курьер" src="null" class="lozad"--> <!--                                         data-src="/assets/img/team/8.jpg">--> <!--                                </div>--> <!--                                <div class="person__content">--> <!--                                    <p class="person__name">--> <!--                                        Андрей--> <!--                                    </p>--> <!--                                    <p class="person__position">--> <!--                                        Курьер--> <!--                                    </p>--> <!--                                </div>--> <!--                            </div>--> <!--                            <div class="person">--> <!--                                <div class="person__photo">--> <!--                                    <img alt="Дмитрий, Курьер" src="null" class="lozad"--> <!--                                         data-src="/assets/img/team/9.jpg">--> <!--                                </div>--> <!--                                <div class="person__content">--> <!--                                    <p class="person__name">--> <!--                                        Дмитрий--> <!--                                    </p>--> <!--                                    <p class="person__position">--> <!--                                        Курьер--> <!--                                    </p>--> <!--                                </div>--> <!--                            </div>--> <!--                            <div class="person">--> <!--                                <div class="person__photo">--> <!--                                    <img alt="Анатолий, Курьер" src="null" class="lozad"--> <!--                                         data-src="/assets/img/team/10.jpg">--> <!--                                </div>--> <!--                                <div class="person__content">--> <!--                                    <p class="person__name">--> <!--                                        Анатолий--> <!--                                    </p>--> <!--                                    <p class="person__position">--> <!--                                        Курьер--> <!--                                    </p>--> <!--                                </div>--> <!--                            </div>--> <!--                            <div class="person">--> <!--                                <div class="person__photo">--> <!--                                    <img alt="Денис, Продавец-консультант" src="null" class="lozad"--> <!--                                         data-src="/assets/img/team/11.jpg">--> <!--                                </div>--> <!--                                <div class="person__content">--> <!--                                    <p class="person__name">--> <!--                                        Денис--> <!--                                    </p>--> <!--                                    <p class="person__position">--> <!--                                        Продавец-консультант--> <!--                                    </p>--> <!--                                    <p class="person__phone">--> <!--                                        8 495 984-04-83--> <!--                                    </p>--> <!--                                    <p class="person__email">--> <!--                                        <a href="mailto:denis@timecube.ru">denis@timecube.ru</a>--> <!--                                    </p>--> <!--                                </div>--> <!--                            </div>--> <!--                        </div>--> <!--                    </div>--> <!--                </div>-->
			<div class="contact-group contact-group--wide">
				 <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"EDIT_MODE" => "html",
		"PATH" => SITE_DIR."include/contacts_rec.php"
	),
false,
Array(
	'HIDE_ICONS' => 'N'
)
);?>
			</div>
		</div>
	</div>
 </section>
</div><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php") ?>