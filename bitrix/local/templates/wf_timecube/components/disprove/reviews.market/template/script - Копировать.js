

document.addEventListener("readystatechange", function (e) {
    "complete" === e.target.readyState && setTimeout(function () {
      
    function activeLinksTab() {
        var showProducts = document.querySelectorAll('.dr-show_link');
        if(!!showProducts){
          [].forEach.call( showProducts, function(splink) {
              splink.onclick = function(){
                  this.classList.toggle("active");
                  this.nextElementSibling.classList.toggle("d-hidden");
                  var allimg = this.nextElementSibling.querySelectorAll("img");
                  iMageloaded(allimg);
              }
          });
        }
        var showComments = document.querySelectorAll('.dr-show_item_comments');
        if(!!showComments){
          [].forEach.call( showComments, function(sclink) {
              sclink.onclick = function(){
                  this.classList.toggle("active");
                  this.closest('.dr-item').querySelector(".dr-comments").classList.toggle("d-hidden");
              }
          });
        }
        var showFacts = document.querySelectorAll('.dr-show_item_facts');
        if(!!showFacts){
          [].forEach.call( showFacts, function(sflink) {
              sflink.onclick = function(){
                  this.classList.toggle("active");
                  this.closest('.dr-item').querySelector(".dr-facts").classList.toggle("d-hidden");
              }
          });
        }
    }
    function activeVote() {
        var voteActions = document.querySelectorAll('.vote-action');
        if(!!voteActions){
          [].forEach.call( voteActions, function(vlink) {
              vlink.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                var like = 1, sum = 0, msg = {},
                    container = this.closest(".vote-widget-container");
                  
                if(this.classList.contains("voted")) { return false; }  
                if(container.classList.contains("voted")) { return false; }  
                var id = this.closest(".dr-item").dataset.id,
                    voteSum = container.querySelector(".vote-sum");
                this.classList.add("voted");
                msg = { "AJAX":'Y', "id": id };
                  
                if(this.classList.contains("positive")) {
                    sum = Number(voteSum.innerHTML)+1; //parseInt
                    msg.type = 1;
                }else{
                    sum = Number(voteSum.innerHTML)-1;
                    msg.type = 0;
                }
                if(sum > 0) {
                    voteSum.classList.remove("negative");
                    voteSum.classList.add("positive");	
                }else {
                    voteSum.classList.remove("positive");
                    voteSum.classList.add("negative");
                    sum = sum * -1;
                }
                container.classList.add("voted");
                voteSum.innerHTML = sum;
                  
                  
                  
                
                $.ajax({
                    type: 'POST',
                    url: '/bitrix/components/disprove/reviews.market/ajax.php',
                    data: msg,
                    cache: false,
                    success: function(data) {
                        activeLinksTab();
                    }
                });
                  
              }
          });
        }
        
    }
     
    activeLinksTab();
    activeVote();
      

    var optionCancel = document.querySelectorAll('.new-opinion-cancel, .btn-show-from');
    if(!!optionCancel){
      [].forEach.call( optionCancel, function(opCancel) {
          opCancel.onclick = function(){
              document.getElementById('form-send').classList.toggle("d-hidden");
          }
      });
    }
    var btnSources = document.querySelectorAll('.sources-container .d-btn');
    if(!!btnSources){
      [].forEach.call( btnSources, function(btn) {
          btn.onclick = function() {
              for (var i = 0; i < btnSources.length; i++) {
                  btnSources[i].classList.remove("d-active");
              }
              this.classList.add("active");
              var allInput = document.querySelectorAll('.sources-container input');
              for (var i = 0; i < allInput.length; i++) {
                  allInput[i].checked = false;
              }
              this.querySelector("input").checked = true;
          }
      });
    }
    var btnStars = document.querySelectorAll('.marks .marks-list .star-item'); 
    if(!!btnStars){
      [].forEach.call( btnStars, function(star) {
          star.onclick = function() {
              var pos = getIndex(star), n;
              for (var i = 0; i < btnStars.length; i++) {
                  if(getIndex(btnStars[i]) <= pos) {
                    btnStars[i].classList.add("active");
                    n = i + 1;
                    document.getElementById('addopinionform-grade').value = i+1;
                  }else{
                    btnStars[i].classList.remove("active");
                  }
              }
          }
      });
    }
    function selectedCity() {
      var cityLi = document.querySelectorAll('.cityDrop li'); 
      if(!!cityLi){
          [].forEach.call( cityLi, function(city) {
              city.onclick = function() {
                var box = this.closest('.cityDrop');
                var li = this.innerHTML; 
                box.querySelector(".DropCityBox").style.display = 'none';
                box.querySelector(".DropCityBox").empty(); 
                box.querySelector("input").value = li; 
              }
          });
      }
    }
    var ratingSelected = document.querySelectorAll('.dr-select-selection-input');
    if(!!ratingSelected){
      [].forEach.call( ratingSelected, function(rslink) {
          rslink.onclick = function() {
              this.closest('.dr-select-selection').classList.toggle("active");
          }
      });
    }
    var dropDown = document.querySelectorAll('.dr-select_drop_down div'); 
    if(!!dropDown){
      [].forEach.call( dropDown, function(drop) {
          drop.onclick = function(e) {
              e.preventDefault(); 
              e.stopPropagation();
              if(this.classList.contains("active")) return false;
              var parentDrop = this.closest(".dr-select_drop_down"),
                  selection = this.closest(".dr-select-selection"),
                  selectInput = selection.querySelector(".dr-select-selection-input");
              if(parentDrop.classList.contains("dr-sort-date")) {
                selectInput.classList.remove("down");
                selectInput.classList.remove("up");
                selectInput.classList.add(this.getAttribute("class"));
              }
              var div = parentDrop.querySelectorAll("div");
              for (var i = 0; i < div.length; i++) {
                  div[i].classList.remove("active")
              }
              this.classList.add("active");
              selection.classList.toggle("active");
              selection.querySelector(".dr-select-selection-input").innerHTML = this.innerHTML;

              document.querySelector('.dr-wait').style.display = '';
              var star = this.dataset.stars;
              var ajaxSort = this.closest(".dr-ajax-sort");
              if(!!ajaxSort) {
                    var i = ajaxSort.querySelector(".active").dataset.value;
                    if(i > 0) {
                        star = star + '&STAR=' + i; 
                    } 
              }
              /*
                var defaultData = {
				siteId: this.siteId, template: this.template, parameters: this.parameters
			};
			if (this.ajaxId) {
				defaultData.AJAX_ID = this.ajaxId;
			}
            var idata = Object.assign(defaultData, data),
                formData = new FormData(), thisData = this;
            for ( var key in idata ) {
                formData.append(key, idata[key]);
            }
            // this.componentPath
            var requestURL = '/bitrix/components/disprove/reviews.market/templates/.default/ajax.php' + (document.location.href.indexOf('clear_cache=Y') !== -1 ? '?clear_cache=Y' : ''),
                xhr = new XMLHttpRequest(),
                btn = this.showMoreButton;
            xhr.open("POST", requestURL);
            xhr.onreadystatechange = function(e) {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var result = xhr.responseText;
                    console.log(result);
                    var container = document.implementation.createHTMLDocument().documentElement;
                    container.innerHTML = xhr.responseText;
                    var pre = container.querySelectorAll('pre');
                    var productList = container.querySelectorAll('.products-list .list-item');
                    var pagination = container.querySelector('.pagination');
                    
                    var data = { items: productList, pagin: pagination }
                    thisData.showAction(data);
                    
                    addProducts();
                    imageItemSldier();
                    favoritesAdd();
                }
            };
            xhr.send(formData);
                */
              $(".dreview_ajax").load(star+" .dreview_ajax_inner", "",function() {
                    document.querySelector('.dr-wait').style.display = 'none';
                    window.history.replaceState("", "", star);
                    activeLinksTab();
              }); 

          }
      });
    }
    function getIndex(node) {
      var childs = node.parentNode.querySelectorAll('.star-item');
      for (t = 0; t < childs.length; t++) {
        if (node == childs[t]) break;
      }
      return t;
    }
    function iMageloaded(images) {
        var currentSrc;
        [].forEach.call( images, function(img) {
            if(!img.classList.contains("lazyloaded")) {
                currentSrc = img.getAttribute('data-src');
                img.setAttribute('src', currentSrc);
                img.setAttribute('data-src', '');
                img.classList.remove("lazy");
                img.classList.add("lazyloaded");
            }
        });
    }
  }, 30)
}, {passive: !0});


$(function(){
    

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
		var form = document.forms.dymarketform;
		var formData = new FormData(form);
		var xhr = new XMLHttpRequest();
		xhr.open("POST", AJAX_DYMARKET.PATH + '/ajax.add.php' + (document.location.href.indexOf('clear_cache=Y') !== -1 ? '?clear_cache=Y' : ''));
		xhr.onreadystatechange = function() {
			if (xhr.readyState == 4) {
				if(xhr.status == 200) {
					data = jQuery.parseJSON(xhr.responseText);
					if(data.STATUS == "SUCCESS"){
						$(".dymarket_add").empty();
						$(".dymarket_add").append("<div class='success_text'>"+AJAX_DYMARKET.SUCCESS_TEXT+"</div>");
                        activeLinksTab();
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
});