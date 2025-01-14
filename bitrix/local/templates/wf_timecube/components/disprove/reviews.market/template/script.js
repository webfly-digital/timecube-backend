(function() {
	'use strict';
	if (!!window.JSDisproveReviewsComponent) return;
    window.JSDisproveReviewsComponent = function(params) {
		this.formPosting = false;
		this.siteId = params.siteId || '';
		this.ajaxId = params.ajaxId || '';
		this.template = params.template || '';
		this.componentPath = params.componentPath || '';
		this.parameters = params.parameters || '';
		this.lazyLoad = params.lazyLoad || '';
		this.loadOnScroll = params.loadOnScroll || '';
		this.sendText = params.sendText || '';
		this.filterStar = 0;
		if (params.navParams) 
        {
			this.navParams = {
				NavNum: params.navParams.NavNum || 1,
				NavPageNomer: parseInt(params.navParams.NavPageNomer) || 1,
				NavPageCount: parseInt(params.navParams.NavPageCount) || 1
			};
		}
        if(!!this.lazyLoad) {
            window.addEventListener('scroll', () => JSDisproveReviewsComponent.prototype.loadOnScroll.call(this), {passive: !0});
        }
        JSDisproveReviewsComponent.prototype.activeLinksTab.call();
        JSDisproveReviewsComponent.prototype.activeVote.call(this);
        JSDisproveReviewsComponent.prototype.activeForm.call(this);
		this.container = document.querySelector('[data-entity="' + params.container + '"]');
		this.lazyLoadContainer = document.querySelector('[data-entity="lazy-' + params.container + '"]');
        this.showMoreButton = document.querySelector('[data-use="show-more-' + this.navParams.NavPageNomer + '"]');
        JSDisproveReviewsComponent.prototype.lazyLoadBtn.call(this);
        JSDisproveReviewsComponent.prototype.selectedDrop.call(this);
	};
	window.JSDisproveReviewsComponent.prototype = {
		sendRequest: function(data, reset = "N") {
			var defaultData = {
				siteId: this.siteId, template: this.template, parameters: this.parameters
			};
			if (this.ajaxId) { defaultData.AJAX_ID = this.ajaxId; }
            var idata = Object.assign(defaultData, data),
                formData = new FormData(), thisData = this;
            for ( var key in idata ) {
                formData.append(key, idata[key]);
            }
            if(thisData.filterStar){
                formData.append("STAR", this.filterStar);
            }
            var requestURL = '/bitrix/components/disprove/reviews.market/ajax.php' + (document.location.href.indexOf('clear_cache=Y') !== -1 ? '?clear_cache=Y' : ''),
                xhr = new XMLHttpRequest(),
                btn = this.showMoreButton;
            xhr.open("POST", requestURL);
            xhr.onreadystatechange = function(e) {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var result = xhr.responseText;
                    var container = document.implementation.createHTMLDocument().documentElement;
                    container.innerHTML = xhr.responseText;
                    var reviewsList = container.querySelectorAll('.dr-item');
                    var pagination = container.querySelector('.pagination_block');
                    var data = { items: reviewsList, pagination: pagination }
                    if(reset !== "N"){
                        [].forEach.call( document.querySelectorAll('.dr-item'), function(item) {
                            item.remove();   
                        });
                    }
                    if(!!idata["STAR"]) {
                        window.history.replaceState("", "", "?STAR=" + idata["STAR"]);
                    }
                    thisData.processShowMoreAction(data);
                    thisData.activeLinksTab();
                    thisData.activeVote();
                    thisData.activeForm();
                }
            };
            xhr.send(formData);
		},
		processShowMoreAction: function(result) {
			this.formPosting = false;
			if (result) {
				this.processItems(result.items);
				this.processPagination(result.pagination);
				this.checkButton();
			}
		},
		processItems: function(itemsHtml, position) {
			if (!itemsHtml) return;
			var k, origRows;
			if (itemsHtml.length) {
				for (k in itemsHtml) {
					if (itemsHtml.hasOwnProperty(k)) {
						this.container.querySelector(".dreview_list_new").appendChild(itemsHtml[k]);
					}
				}
			}
		},
		processPagination: function(paginationHtml) {
			//if (!paginationHtml) return;
			var pagination = document.querySelector('[data-pagination-num="' + this.navParams.NavPageNomer + '"]'),
                moreLoad = document.querySelector('.more-load');
                moreLoad.style.display = 'none';
			this.navParams.NavPageNomer++;
            this.navParams.NavPageCount = Number(paginationHtml.dataset.paginationPages);
            if(!!pagination) {
                pagination.remove();
            }
            if(!!paginationHtml) {
                document.querySelector('.dreview_block').appendChild(paginationHtml);
                moreLoad.style.display = 'table';
            }
		},
		checkButton: function() {
			if (this.showMoreButton) {
				if (this.navParams.NavPageNomer == this.navParams.NavPageCount) {
					this.showMoreButton.remove();
					document.querySelector('[data-pagination-num="' + this.navParams.NavPageNomer + '"]').remove();
                    var moreLoad = document.querySelector('.more-load');
                    moreLoad.style.display = 'none';
				}
                return true;
			}else{
                return false;
            }
		},
		loadOnScroll: function() {
            if(this.checkButton()) {
                var scrollTop = window.pageYOffset,
                    containerTop = this.container.querySelector('.pagination_block').offsetTop;
                if (scrollTop + window.innerHeight > containerTop) {
                    this.showMore();
                }
            }
		},
		showMore: function() {
			if (this.navParams.NavPageNomer < this.navParams.NavPageCount) {
				var data = {};
				data['action'] = 'showMore';
				data['PAGEN_' + this.navParams.NavNum] = this.navParams.NavPageNomer + 1;
				if (!this.formPosting) {
					this.formPosting = true;
					this.sendRequest(data); 
				}
			}
		},
        lazyLoadBtn: function() {
            if (this.loadOnScroll) {
                if(!!this.showMoreButton) {
                    this.showMoreButtonMessage = this.showMoreButton.innerHTML;
                    var arg = this;
                    this.showMoreButton.onclick = function(e) {
                        JSDisproveReviewsComponent.prototype.showMore.call(arg);
                    };
                }
            }
		},
        selectedDrop() {
            var dropDown = document.querySelectorAll('.dr-select_drop_down div');
            if(!!dropDown){
              var thisData = this;
              [].forEach.call( dropDown, function(drop) {
                  drop.onclick = function(e) {
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
                      var star = this.dataset.value;
                      thisData.filterStar = star;
                      
                      
                      //document.querySelector('.dr-wait').style.display = '';
                      
                      
                      if(!!star && star > 0) {
                          var data = {};
                          data['STAR'] = star;
                          
                          var pagination = document.querySelector('[data-pagination-num="' + thisData.navParams.NavPageNomer + '"]');
                          if(!!pagination) {
                            pagination.remove();
                          }
                          
                          thisData.navParams.NavPageNomer = 0;
                          
                          data['PAGEN_' + thisData.navParams.NavNum] = 1;
                          data['action'] = 'showMore';
                          if (!thisData.formPosting) {
                            thisData.formPosting = true;
                            thisData.sendRequest(data, "Y"); 
                          }
                      }
                  }
              });
            }
        }, 
        activeVote() {
            var voteActions = document.querySelectorAll('.vote-action');
            if(!!voteActions){
              var thisData = this;
              [].forEach.call( voteActions, function(vlink) {
                  vlink.onclick = function() {
                    var like = 1, sum = 0, msg = {}, formData = new FormData(),
                        container = this.closest(".vote-widget-container"),
                        id = this.closest(".dr-item").dataset.id,
                        voteSum = container.querySelector(".vote-sum");
                    msg = { "AJAX":'Y', "id": id };
                    if(this.classList.contains("voted")) { return false; }  
                    if(container.classList.contains("voted")) { return false; }  
                    this.classList.add("voted");

                    if(this.classList.contains("positive")) {
                        sum = Number(voteSum.innerHTML)+1;
                        msg.type = 1;
                    }else{
                        sum = Number(voteSum.innerHTML)-1;
                        msg.type = 0;
                    }
                    if(sum > 0) {
                        voteSum.classList.remove("negative");
                        voteSum.classList.add("positive");	
                    }else{
                        voteSum.classList.remove("positive");
                        voteSum.classList.add("negative");
                        sum = sum * -1;
                    }
                    container.classList.add("voted");
                    voteSum.innerHTML = sum;
                    for ( var key in msg ) { formData.append(key, msg[key]); }
                    var requestURL = '/bitrix/components/disprove/reviews.market/like.php' + (document.location.href.indexOf('clear_cache=Y') !== -1 ? '?clear_cache=Y' : ''),
                        xhr = new XMLHttpRequest();
                    xhr.open("POST", requestURL);
                    xhr.onreadystatechange = function(e) {};
                    xhr.send(formData);
                  }
              });
            }
        },
        activeForm() {
            var thisData = this,
                optionCancel = document.querySelectorAll('.new-opinion-cancel, .btn-show-from');
            if(!!optionCancel){
              [].forEach.call( optionCancel, function(opCancel) {
                  opCancel.onclick = function(){
                      document.getElementById('form-send').classList.toggle("d-hidden");
                  }
              });
            }
            var btnSources = document.querySelectorAll('.sources-container .d-btn'),
                allInput = document.querySelectorAll('.sources-container input');
            if(!!btnSources){
              [].forEach.call( btnSources, function(btn) {
                  btn.onclick = function() {
                      for (var i = 0; i < allInput.length; i++) {
                          allInput[i].checked = false;
                          btnSources[i].classList.remove("d-active");
                      }
                      this.classList.add("d-active");
                      this.querySelector("input").checked = true;
                  }
              });
            }
            var btnStars = document.querySelectorAll('.marks .marks-list .star-item'); 
            if(!!btnStars){
              [].forEach.call( btnStars, function(star) {
                  star.onclick = function() {
                      var pos = thisData.getIndex(this), n;
                      for (var i = 0; i < btnStars.length; i++) {
                          if(thisData.getIndex(btnStars[i]) <= pos) {
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
            var ratingSelected = document.querySelectorAll('.dr-select-selection-input');
            if(!!ratingSelected){
              [].forEach.call( ratingSelected, function(rslink) {
                  rslink.onclick = function() {
                      this.closest('.dr-select-selection').classList.toggle("active");
                  }
              });
            }
            var btnSendForm = document.querySelector('.d-btn.new-send');
            if(!!btnSendForm) {
                btnSendForm.onclick = function (e) {
                    e.preventDefault(); 
                    e.stopPropagation();
                    var error = false,
                        authorInput = document.getElementById('addopinionform-author'),
                        cityInput = document.getElementById('addopinionform-city'),
                        gradeInput = document.getElementById('addopinionform-grade'),
                        proInput = document.getElementById('addopinionform-pro'),
                        contraInput = document.getElementById('addopinionform-contra'),
                        textInput = document.getElementById('addopinionform-text'),
                        periodInput = document.querySelector("input[name=period]:checked");
                    
                    authorInput.classList.remove("error"),
                    cityInput.classList.remove("error");
                    if(authorInput.value.length === 0)
                    {
                        authorInput.classList.add("error");
                        error = true;
                    }
                    if(cityInput.value.length === 0)
                    {
                        cityInput.classList.add("error");
                        error = true;
                    }
                    if(error == true){
                        return false;
                    }
                    var formData = new FormData();
                    
                    formData.append("text", textInput.value);
                    formData.append("contra", contraInput.value);
                    formData.append("pro", proInput.value);
                    formData.append("rating", gradeInput.value);
                    formData.append("city", cityInput.value);
                    formData.append("userName", authorInput.value);
                    formData.append("period", periodInput.value);
                    
                    var xhr = new XMLHttpRequest();
                    var requestURL = '/bitrix/components/disprove/reviews.market/add_review.php' + (document.location.href.indexOf('clear_cache=Y') !== -1 ? '?clear_cache=Y' : '');
                    xhr.open("POST", requestURL);
                    xhr.onreadystatechange = function() {
                         if (xhr.readyState == 4 && xhr.status == 200) {
                            var result = xhr.responseText;
                            var data = JSON.parse(xhr.responseText);
                            if(data.STATUS == "SUCCESS") {
                                document.querySelector('.dymarket_add').innerHTML = "<div class='success_text'>"+thisData.sendText+"</div>";
                                thisData.activeLinksTab();
                                setTimeout(function(){document.getElementById('form-send').classList.toggle("d-hidden");}, 5000);
                            }
                        }
                    };
                    xhr.send(formData);
                }
            }
            var cityInput = document.querySelector('.cityDrop input');
            if(!!cityInput) {
                cityInput.onkeyup = function () {
                    var leng = this.value.length;
                    var box = this.closest(".cityDrop"),
                        dropCity = box.querySelector(".DropCityBox");
                        dropCity.innerHTML = ''; 
                    if(leng < 3) return false;
                    var formData = new FormData();
                    formData.append("val", this.value);
                    var xhr = new XMLHttpRequest();
                    var requestURL = '/bitrix/components/disprove/reviews.market/city.php' + (document.location.href.indexOf('clear_cache=Y') !== -1 ? '?clear_cache=Y' : '');
                    xhr.open("POST", requestURL);
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            var result = xhr.responseText;
                            if(result.length > 0) {
                                dropCity.style.display = 'block';
                                dropCity.innerHTML = "<ul>"+result+"</ul>";
                                thisData.selectedCity();
                            }
                        }
                    };
                    xhr.send(formData);
                }
            }
        },
        selectedCity() {
          var cityLi = document.querySelectorAll('.cityDrop li'); 
          if(!!cityLi){
              [].forEach.call( cityLi, function(city) {
                  city.onclick = function() {
                    var box = this.closest('.cityDrop');
                    var li = this.innerHTML;
                    box.querySelector(".DropCityBox").style.display = 'none';
                    box.querySelector(".DropCityBox").innerHTML = '';
                    box.querySelector("input").value = li;
                  }
              });
          }
        },
        activeLinksTab() {
            var showProducts = document.querySelectorAll('.dr-show_link');
            if(!!showProducts){
              [].forEach.call( showProducts, function(splink) {
                  splink.onclick = function(){
                      this.classList.toggle("active");
                      this.nextElementSibling.classList.toggle("d-hidden");
                      var images = this.nextElementSibling.querySelectorAll("img");
                      JSDisproveReviewsComponent.prototype.iMageloaded.call(this, images);
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
        },
		getIndex: function(node) {
            var childs = node.parentNode.querySelectorAll('.'+node.classList[0]);
            for (var t = 0; t < childs.length; t++) {
                if (node == childs[t]) break;
            }
            return t;
		},
        iMageloaded: function(images) {
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
		},
        
	};
})();