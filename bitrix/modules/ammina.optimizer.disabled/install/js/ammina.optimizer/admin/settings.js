$(document).ready(function () {
	$(".amopts-category__main").click(function () {
		var cat = $(this).parents(".amopts-category:first");
		if (cat.is(".active")) {
			cat.find(".amopts-category__content").fadeOut(100);
			cat.find(".amopts-category__content-short").fadeIn(100);
			$(cat).removeClass("active");
		} else {
			cat.find(".amopts-category__content").fadeIn(100);
			cat.find(".amopts-category__content-short").fadeOut(100);
			$(cat).addClass("active");
		}
	});
	$(".amopts-category__group-title-main").click(function () {
		var cat = $(this).parents(".amopts-category__group:first");
		if (cat.is(".active")) {
			cat.find(".amopts-category__group-content").fadeOut(100);
			cat.find(".amopts-category__group-content-short").fadeIn(100);
			$(cat).removeClass("active");
		} else {
			cat.find(".amopts-category__group-content").fadeIn(100);
			cat.find(".amopts-category__group-content-short").fadeOut(100);
			$(cat).addClass("active");
		}
	});
	$(".amopts-field__textbytes-type").change(function () {
		var oTextBytes = $(this).parents(".amopts-field:first").find(".amopts-field__textbytes");
		var oTextBytesType = $(this).parents(".amopts-field:first").find(".amopts-field__textbytes-type");
		var oTextVal = $(this).parents(".amopts-field:first").find(".amopts-field__field");
		var iVal = oTextBytes.val() * 1;
		if (oTextBytesType.val() == 'k') {
			iVal = iVal * 1024;
		} else if (oTextBytesType.val() == 'm') {
			iVal = iVal * 1024 * 1024;
		} else if (oTextBytesType.val() == 'g') {
			iVal = iVal * 1024 * 1024;
		}
		oTextVal.val(iVal);
	});
	$(".amopts-field__textbytes").change(function () {
		var oTextBytes = $(this).parents(".amopts-field:first").find(".amopts-field__textbytes");
		var oTextBytesType = $(this).parents(".amopts-field:first").find(".amopts-field__textbytes-type");
		var oTextVal = $(this).parents(".amopts-field:first").find(".amopts-field__field");
		var iVal = oTextBytes.val() * 1;
		if (oTextBytesType.val() == 'k') {
			iVal = iVal * 1024;
		} else if (oTextBytesType.val() == 'm') {
			iVal = iVal * 1024 * 1024;
		} else if (oTextBytesType.val() == 'g') {
			iVal = iVal * 1024 * 1024;
		}
		oTextVal.val(iVal);
	});
	$(".amopts-field__select").change(function () {
		$(this).parents(".amopts-field:first").find(".amopts-field__description-text_active").removeClass("amopts-field__description-text_active");
		$(this).parents(".amopts-field:first").find(".amopts-field__description-text[data-value='" + $(this).val() + "']").addClass("amopts-field__description-text_active");
	});
	$(".amopts-field__select-options").change(function () {
		$(this).parents(".amopts-field:first").find(".amopts-field__description-text_active").removeClass("amopts-field__description-text_active");
		$(this).parents(".amopts-field:first").find(".amopts-field__description-text[data-value='" + $(this).val() + "']").addClass("amopts-field__description-text_active");
	});
	$(".amopts-category__group-disable").change(function () {
		if ($(this).is(":checked")) {
			$(this).parents(".amopts-category__group:first").find(".amopts-category__option-set .amopts-field-allowdisabled").attr("disabled", "disabled");
		} else {
			$(this).parents(".amopts-category__group:first").find(".amopts-category__option-set .amopts-field-allowdisabled").removeAttr("disabled");
		}
	});
	$(".amopts-category__group-content-short a").click(function () {
		if (!$(this).parents(".amopts-category__group:first").is(".active")) {
			$(this).parents(".amopts-category__group:first").find(".amopts-category__group-title-main").trigger("click");
		}
	});
	$(".amopts-category__content-short a").click(function () {
		if (!$(this).parents(".amopts-category:first").is(".active")) {
			$(this).parents(".amopts-category:first").find(".amopts-category__main").trigger("click");
		}
		$(this).parents(".amopts-category:first").find(".amopts-category__group-content-short a[data-group='" + $(this).data("group") + "']").trigger("click");
	});
});