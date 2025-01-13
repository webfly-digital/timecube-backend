<div class="collapse-group" id="customers">
	<div class="collapse-card">
		<div class="collapse-card__header" id="heading-customers-0">
			<h4 class="collapse-card__title"><a href="#collapse-customers-0" data-toggle="collapse"><span class="svg-icon icon-delivery-truck"></span><span class="collapse-card__name">Доставка</span></a> </h4>
		</div>
		<div class="collapse show" id="collapse-customers-0" aria-labelledby="heading-customers-0" data-parent="#customers">
			<div class="collapse-card__body">
				 Все в наличии. Отгрузка сегодня.<br>
				Доставка по Москве завтра.<br>
                <a class="btn btn-xs btn-secondary mt-2" href="#anchorSdek">Расчёт доставки</a>
			</div>
		</div>
	</div>
	<div class="collapse-card">
		<div class="collapse-card__header" id="heading-customers-1">
			<h4 class="collapse-card__title"><a href="#collapse-customers-1" data-toggle="collapse"><span class="svg-icon icon-payment"></span><span class="collapse-card__name">Оплата</span></a> </h4>
		</div>
		<div class="collapse show" id="collapse-customers-1" aria-labelledby="heading-customers-1" data-parent="#customers">
			<div class="collapse-card__body">
				<ul class="list-unstyled">
					<li>Наличный расчет</li>
					<li>Картой при получении</li>
					<li>Онлайн оплата</li>
				</ul>
			</div>
		</div>
	</div>
	<div class="collapse-card">
		<div class="collapse-card__header" id="heading-customers-2">
			<h4 class="collapse-card__title"><a href="#collapse-customers-2" data-toggle="collapse"><span class="svg-icon icon-security"></span><span class="collapse-card__name">Гарантия и возврат</span></a> </h4>
		</div>
		<div class="collapse show" id="collapse-customers-2" aria-labelledby="heading-customers-2" data-parent="#customers">
			<div class="collapse-card__body">
				Если по каким-либо причинам Ваша покупка Вас не устроит, то в течение 7 дней с момента получения заказа Вы можете его вернуть. <a href="/delivery/#garanty">Подробнее</a>
			</div>
		</div>
	</div>
	<div class="collapse-card">
		<div class="collapse-card__header" id="heading-customers-3">
			<h4 class="collapse-card__title"><a href="#collapse-customers-3" data-toggle="collapse"><span class="svg-icon icon-delivery-truck"></span> <span class="collapse-card__name">Самовывоз</span></a> </h4>
		</div>
		<div class="collapse show" id="collapse-customers-3" aria-labelledby="heading-customers-3" data-parent="#customers">
			<div class="collapse-card__body">
				 <?
                include $_SERVER['DOCUMENT_ROOT']."/include/address.php";
                ?> Показываем на выбор не более трех наименований.
			</div>
		</div>
	</div>
</div>
<br>