<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "О нас. Подробная информация об интернет-магазине шкатулок для часов Timecube. Звоните: 8 800 775-25-76!");
$APPLICATION->SetPageProperty("title", "О нас | TimeCube");
$APPLICATION->SetTitle("О нас");
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
			 Мы рады приветствовать Вас на сайте <a href="/">Timecube.ru!</a>
		</p>
	</div>
	</section>
</div>
<blockquote>
	<div class="three-columns">
		<section class="three-columns__body">
		<div class="container-fluid">
			<p>
				<img width="314" alt="Сертификат соответствия 2021 год" src="/upload/medialibrary/1b9/11z07zpu87gbpyf5ul5lnvteucoapgx0.png" height="449" title="Сертификат соответствия 2021 год">
			</p>
		</div>
		</section>
	</div>
</blockquote>
<div class="three-columns">
	<section class="three-columns__body">
	<div class="container-fluid">
		<p>
			 Наша команда работает на рынке шкатулок для часов вот уже&nbsp;11 лет. За это время мы узнали все о шкатулках для часов с автоподзаводом и просто хранения. Мы выбрали лучших поставщиков, у которых закупаем товар напрямую с завода, сами его таможим и привозим на наш склад в Москве. Вы можете быть уверены, что покупаете товар из первых рук по самой выгодной цене.
		</p>
		<p>
			 Мы сформировали широкую карту ассортимента и географию поставщиков. Наша компания является эксклюзивным дистрибьютором таких брендов, как Benson (Голландия), Modalo и Luxwinder (Германия), Boxy (Тайвань), Timecube (США). Мы сотрудничаем с крупнейшими заводами Китая (Leader, Twing-Pak, H&amp;S, Viilways), а так же представляем бренды Jebely (Швейцария), Luxojovanni (Италия) и Avante (Гонконг), Prestige (США), MTE (Германия). Наша основная философия - никогда не останавливаться на достигнутом. Поэтому, несмотря на значительные успехи (а ведь наша компания - безусловный лидер на рынке шкатулок), мы продолжаем постоянно развиваться.
		</p>
		<p>
			 Мы следим за новинками рынка и ежеквартально пополняем ассортимент новыми товарами. Мы ищем новых перспективных поставщиков, постоянно участвуем в международных мероприятиях, посвященных часовому бизнесу. Наши сотрудники лично проверяют качество закупаемой продукции на заводах наших поставщиков, чтобы Вы, наши Клиенты, всегда получали только самый лучший товар высочайшего качества.
		</p>
		<p>
			 Мы работаем и развиваемся для Вас!
		</p>
	</div>
 </section>
	<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => SITE_DIR."include/inner_aside.php"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?>
</div>
 <br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>