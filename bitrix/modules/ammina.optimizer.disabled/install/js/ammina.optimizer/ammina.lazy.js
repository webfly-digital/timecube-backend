$(document).ready(function () {
	$(document).on('scroll', function () {
		amminaLazyLoadCheck();
	});
	amminaLazyLoadCheck();
});

function amminaLazyLoadCheck() {
	var windowHeight = $(window).height();
	$('.ammina-lazy').each(function () {
		var self = $(this), height = self.offset().top - windowHeight - 10;
		if (!self.is(".ammina-lazy-load")) {
			if ($(document).scrollTop() >= height) {
				var datasrc = self.attr("data-src");
				if (typeof datasrc !== typeof undefined && datasrc !== false) {
					self.addClass("ammina-lazy-load");
					$.ajax(self.attr("data-src"), {
						cache: true,
						context: self,
						success: function () {
							$(this).attr("src", $(this).attr("data-src"));
							$(this).removeAttr("data-src");
							$(this).removeClass("ammina-lazy");
							$(this).removeClass("ammina-lazy-load");
						}
					});
				}
				datasrc = self.attr("data-background-image");
				if (typeof datasrc !== typeof undefined && datasrc !== false) {
					self.addClass("ammina-lazy-load");
					$.ajax(self.attr("data-background-image"), {
						cache: true,
						context: self,
						success: function () {
							$(this).css("background-image", $(this).attr("data-background-image"));
							$(this).removeAttr("data-background-image");
							$(this).removeClass("ammina-lazy");
							$(this).removeClass("ammina-lazy-load");
						}
					});
				}
			}
		}
	});
}