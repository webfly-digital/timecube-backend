 BX.ready(function(){
 	BX.bind(BX("zver-show-list"), 'click', function(event) {
 		var target = event.target || event.srcElement,
	 		parent = BX.findParent(target, {"tag" : "ul", "class" : "zverushki-tags"}),
	 		fields = BX.findChild(parent, {class: 'zver-hide'}, false, true);

	 	BX.remove(target);
	 	fields.forEach(function(element){
	 		BX.removeClass(element, 'zver-hide');
	 	});
 	});
 });