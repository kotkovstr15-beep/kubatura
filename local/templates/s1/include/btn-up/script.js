function scrollToSection(id) {
	document.querySelector(id).scrollIntoView({ 
		behavior: 'smooth' 
	});
}

$(window).on('scroll', function() {
	const scrollTop = $(window).scrollTop();

	if (scrollTop > 100) {
		$('.scs-btn-up').addClass('scs-btn-up--active');
	} else {
		$('.scs-btn-up--active').removeClass('scs-btn-up--active');
	}

});