<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("buyer_menu", "Y");
$APPLICATION->SetTitle("Информация для оптовых покупателей");
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
		<div class="heading">
			<div class="heading__item">
				<h1 class="heading__title"><?=$APPLICATION->ShowTitle(true)?></h1>
			</div>
		</div>
	</div>
	<div class="container-fluid">
		<p>
			 Основным видом нашей деятельности является оптовая продажа <a href="https://timecube.ru/shkatulki-dlya-chasov-s-avtopodzavodom/">шкатулок для часов с автоподзаводом</a>, <a href="https://timecube.ru/shkatulki_dlya_chasov/">шкатулок для механических часов</a> и <a href="https://timecube.ru/shkatulki_dlya_ukrasheniy/">шкатулок для украшений</a>.
		</p>
		<p>
			 Наша компания является эксклюзивным дистрибьютором в России <a href="https://timecube.ru/manufacturers/">производителей</a>: <br>
			 - Modalo (Германия) <br>
			 - Luxwinder (Германия)&nbsp; <br>
			 - BOXY (Тайвань) <br>
			 - Benson watchwinders (Голландия)<br>
			 - Timecube (США)<br>
		</p>
		<p>
			 Также мы являемся дистрибьюторами следующих производителей: <br>
			 - Leader (Китай)&nbsp; <br>
			 - T.Wnig-Pak (Китай)&nbsp; <br>
			 - C&amp;S (Китай) <br>
			 - Prestige (США)&nbsp; <br>
			 - MTE (Германия)&nbsp; <br>
			 - Luxojiovanni (Италия)<br>
			 - Zoss (Китай)
			 - Avante (Гонг-Конг)
		</p>
		<p>
 <br>
		</p>
		<p>
 <br>
		</p>
		<p>
			 Оптовым покупателям предоставляются значительные скидки на все товары. Для определения Вашей скидки и заключения договора, пожалуйста, свяжитесь с нами по телефону или email.
		</p>
		<p>
			 Для комфортной работы мы рекомендуем зарегистрироваться на нашем сайте. Мы установим для Вас специальные цены, и Вы сможете видеть не только розничную, но и оптовую цену на сайте. Также Вам будет доступна история заказов.
		</p>
		<p>
			 Мы осуществляем поставки крупным интернет-магазинам, таким как Alltime.ru, Technictime.ru, Clockshop.ru, Luxpodarki.ru. Также работаем с многими магазинами, расположенными на территории России и Белорусии.
		</p>
		<p>
			 Становитесь нашим партнером и получайте удовольствие от работы с профессионалами!
		</p>
	</div>
 </section>
</div>
 <br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>