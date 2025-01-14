$(function(){
	$(document).on("click", ".dr-show_link", function(e){ 
		$(this).toggleClass("active");
		$(this).next().toggleClass("d-hidden");
	});
	$(document).on("click", ".dr-show_item_comments", function(e){
		$(this).toggleClass("active");
		$(this).parents(".dr-item").find(".dr-comments").toggleClass("d-hidden");
	});
	$(document).on("click", ".dr-show_item_facts", function(e){
		$(this).toggleClass("active");
		$(this).parents(".dr-item").find(".dr-facts").toggleClass("d-hidden");
	});
	$(document).on("click", ".dr-select-selection-input", function(e){
		$(this).parents(".dr-select-selection").toggleClass("ss-active");
	});
	$(document).on("click", ".dreview_block .d-filters .dr-select_drop_down div", function(e){
		if($(this).hasClass("active")) return false;
		if($(this).parents(".dr-select_drop_down").hasClass("dr-sort-date")){
			$(this).parents(".dr-select-selection").find(".dr-select-selection-input")
				.removeClass("down").removeClass("up")
					.addClass($(this).attr("class"));
		}		
		$(this).parents(".dr-select_drop_down").find("div").removeClass("active");
		$(this).toggleClass("active");
		$(this).parents(".dr-select-selection").toggleClass("ss-active");
		$(this).parents(".dr-select-selection").find(".dr-select-selection-input").text($(this).text());
	});
	$(document).on("click", ".dr-ajax-sort-star, .dr-ajax-sort div", function(e){
		e.preventDefault();
		$(".dr-wait").show();
		var star = $(this).data("stars");
		if($(this).parents(".dr-ajax-sort").length > 0){
			var i = $(".dr-ajax-sort-star.active").data("value");
			if(i > 0)
				star = star + '&STAR=' + $(".dr-ajax-sort-star.active").data("value");
		}
		$(".dreview_ajax").load(star+" .dreview_ajax_inner", "",function(){
			$(".dr-wait").hide();
			window.history.replaceState("", "", star);
		});
	});
	$(document).on("click", ".vote-action", function(e){
		e.preventDefault();
		var like = 1; var sum = 0;
		if($(this).hasClass("voted"))return false;
		if($(this).parents(".vote-widget-container").hasClass("voted"))return false;
		var msg = [];
		var id = $(this).parents(".dr-item").data("id");
		var voteSum = $(this).parents(".vote-widget-container").find(".vote-sum");
		$(this).addClass("voted");
		msg.push({name:"AJAX",value:'Y'});
		msg.push({name:"id",value:id});
		if($(this).hasClass("positive")){
			sum = parseInt(voteSum.text())+1;
			msg.push({name:"type",value:1});
		}else{
			sum = parseInt(voteSum.text())-1;
			msg.push({name:"type",value:0});
		}
		if(sum > 0){
			voteSum.removeClass("negative").addClass("positive");	
			$(this).parents(".vote-widget-container").addClass("voted");
		}else {
			voteSum.removeClass("positive").addClass("negative");
			$(this).parents(".vote-widget-container").addClass("voted");
			sum = sum * -1;
		}
		voteSum.text(sum);
		$.ajax({
			type: 'POST',
			url: '/bitrix/components/disprove/reviews.market/templates/.default/like.php',
			data: msg,
			cache: false,
			success: function(data){
				//console.log(data);
			}
		});
	});

	$(document).on("click", ".new-opinion-cancel", function(){ 
		$("#form-send").toggleClass("d-hidden");
	});
	$(document).on("click", ".btn-show-from", function(){ 
		$("#form-send").toggleClass("d-hidden");
	});
	$(document).on("click", ".sources-container .d-btn", function(){ 
		$(".sources-container .d-btn").removeClass("d-active");
		$(this).addClass("d-active");
		$(".sources-container input").prop('checked', false);
		$(this).find("input").prop('checked', true);
	});
	$(document).on("click", ".marks-list .star-item", function(){ 
		var i = $(this).index();
		$(".marks-list .star-item").removeClass("active");
		$(".marks-list .star-item").each(function(){
			if($(this).index() <= i){
				$(this).addClass("active");
				$("#addopinionform-grade").val(i+1);
			}
		});
	});
	$(document).on("click", ".d-btn.new-send", function(e){
		e.preventDefault();
		var error = false;
		$("#addopinionform-author").removeClass("error");
		if($("#addopinionform-author").val().length === 0)
		{
			$("#addopinionform-author").addClass("error");
			error = true;
		}
		$('#addopinionform-city').removeClass("error");
		if($('#addopinionform-city').val().length === 0)
		{
			$('#addopinionform-city').addClass("error");
			error = true;
		}
		if(error == true){
			return false;
		}
		console.log(AJAX_DYMARKET.PATH + '/ajax.php');
		var form = document.forms.dymarketform;
		var formData = new FormData(form);
		var xhr = new XMLHttpRequest();
		xhr.open("POST", AJAX_DYMARKET.PATH + '/ajax.php' + (document.location.href.indexOf('clear_cache=Y') !== -1 ? '?clear_cache=Y' : ''));
		xhr.onreadystatechange = function() {
			if (xhr.readyState == 4) {
				if(xhr.status == 200) {
					data = jQuery.parseJSON(xhr.responseText);
					if(data.STATUS == "SUCCESS"){
						$(".dymarket_add").empty();
						$(".dymarket_add").append("<div class='success_text'>"+AJAX_DYMARKET.SUCCESS_TEXT+"</div>");
					}
				}
			}
		};
		xhr.send(formData);
	});
	$(document).on("keyup", ".cityDrop input", function(e)
	{ 
		var leng = $(this).val().length;
		var box = $(this).parents(".cityDrop");
		$(".DropCityBox").empty();
		if(leng < 3) return false;
		BX.ajax({
			method: 'POST',
			data:
			{val:$(this).val()},
			dataType: 'html',
			url: AJAX_DYMARKET.CITY + '/ajax.php',
			onsuccess: function(data)
			{
				if(data.length > 0)
				{
					box.find(".DropCityBox").show();
					box.find(".DropCityBox").append("<ul>"+data+"</ul>");
				}
			}
		});
	});
	$(document).on("click", ".cityDrop li", function(e)
	{ 
		var box = $(this).parents(".cityDrop");
		var li = $(this).html();
		box.find(".DropCityBox").hide();
		$(".DropCityBox").empty();
		box.find("input").val(li);
	});
});