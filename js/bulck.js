function get_menu(link) {
		$('#content').load(link.attr('href'), function() {
			if($(document).height()>$(window).height()) {
				$("#arrow_down").css('visibility','visible');
			} else {
				$("#arrow_down").css('visibility','hidden');
			}
		});
		$(".menu-list").removeClass('current');
		$("a[href='"+link.attr('href')+"']").parent().addClass('current');
		$('html, body').animate( { scrollTop: $('html').offset().top }, 100 );
}