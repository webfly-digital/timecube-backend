<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Шкатулки для часов с автоподзаводом, шкатулки для часов, шкатулки для украшений");
$APPLICATION->SetTitle("Видео: как выбрать автоподзаводчик?");
?> 
<div class="videolist">
					<div class="videoitem">
						<h2>Видео 1.</h2>
						<h3>Что такое шкатулка для часов с автоподзаводом?</h3>
						<p>Выбрать шкатулку для часов с автоподзаводом несложно, особенно если Вы знаете, как это сделать правильно. </br>
Именно для этого мы сделали два видео клипа, которые быстро введут Вас в курс дела.<br>
 Первое видео рассказывает о том, что же такое шкатулка для часов с автоподзаводом (она же виндер и тайммувер)</br>
 и для чего она нужна.</p>
<div align="center"><?$APPLICATION->IncludeComponent(
	"bitrix:player",
	".default",
	Array(
		"PLAYER_TYPE" => "auto",
		"USE_PLAYLIST" => "N",
		"PATH" => "http://www.youtube.com/watch?v=vnE4E8Ls9Zk",
		"PROVIDER" => "",
		"STREAMER" => "",
		"WIDTH" => "600",
		"HEIGHT" => "450",
		"PREVIEW" => "",
		"FILE_TITLE" => "",
		"FILE_DURATION" => "",
		"FILE_AUTHOR" => "",
		"FILE_DATE" => "",
		"FILE_DESCRIPTION" => "",
		"SKIN_PATH" => "/bitrix/components/bitrix/player/mediaplayer/skins",
		"SKIN" => "",
		"CONTROLBAR" => "bottom",
		"WMODE" => "opaque",
		"LOGO" => "",
		"LOGO_LINK" => "",
		"LOGO_POSITION" => "none",
		"PLUGINS" => array(),
		"ADDITIONAL_FLASHVARS" => "",
		"AUTOSTART" => "N",
		"REPEAT" => "none",
		"VOLUME" => "90",
		"MUTE" => "N",
		"ADVANCED_MODE_SETTINGS" => "N",
		"PLAYER_ID" => "",
		"BUFFER_LENGTH" => "10",
		"ALLOW_SWF" => "N"
	)
);?><br /></div>
					</div>
					<div class="videoitem">
						<h2>Видео 2.</h2>
						<h3>Как правильно выбрать шкатулку для часов с автоподзаводом?</h3>
						<p>Второе видео расскажет о том, как правильно выбрать шкатулку для часов с автоподзаводом.<br> Если после просмотра у Вас все-таки останутся вопросы, мы с удовольствием на них ответим по телефону.</p>
<div align="center"><?$APPLICATION->IncludeComponent(
	"bitrix:player",
	".default",
	Array(
		"PLAYER_TYPE" => "auto",
		"USE_PLAYLIST" => "N",
		"PATH" => "http://www.youtube.com/watch?v=nqaoSBTRg44",
		"PROVIDER" => "",
		"STREAMER" => "",
		"WIDTH" => "600",
		"HEIGHT" => "450",
		"PREVIEW" => "",
		"FILE_TITLE" => "",
		"FILE_DURATION" => "",
		"FILE_AUTHOR" => "",
		"FILE_DATE" => "",
		"FILE_DESCRIPTION" => "",
		"SKIN_PATH" => "/bitrix/components/bitrix/player/mediaplayer/skins",
		"SKIN" => "",
		"CONTROLBAR" => "bottom",
		"WMODE" => "opaque",
		"LOGO" => "",
		"LOGO_LINK" => "",
		"LOGO_POSITION" => "none",
		"PLUGINS" => array(),
		"ADDITIONAL_FLASHVARS" => "",
		"AUTOSTART" => "N",
		"REPEAT" => "none",
		"VOLUME" => "90",
		"MUTE" => "N",
		"ADVANCED_MODE_SETTINGS" => "N",
		"PLAYER_ID" => "",
		"BUFFER_LENGTH" => "10",
		"ALLOW_SWF" => "N"
	)
);?><br /></div>
						<p>Чтобы правильно заводить именно Ваши часы, необходимо узнать параметры их подзавода.<br> Для этого, пожалуйста, перейдите на страницу: <a href="/tablica_podzavoda_chasov/">таблица подзавода часов</a></p>
					</div>
				</div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>