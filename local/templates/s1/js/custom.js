jQuery(document).ready(function($) {
	$('.swiper-button-prev').attr({'role': 'button', 'tabindex': '0', 'aria-label': 'Предыдущий слайд'});
	$('.swiper-button-next').attr({'role': 'button', 'tabindex': '0', 'aria-label': 'Следующий слайд'});
	$('.swiper-pagination').attr('aria-label', 'Навигация по слайдам');
	$('.sandwich').attr({'aria-label': 'Открыть меню', 'aria-controls': 'menu'});
	$('button.pab, input.pab').attr('aria-label', 'Найти');
	$('.scs-btn-up').attr('aria-label', 'Наверх');
	$('.pagination__prev').attr('aria-label', 'Предыдущая страница');
	$('.pagination__next').attr('aria-label', 'Следующая страница');
	$('.social-item.ic-vk').attr('aria-label', 'ВКонтакте');
	$('.social-item.ic-instagram').attr('aria-label', 'Instagram');
	$('.social-item.ic-globe').attr('aria-label', 'Сайт');

	$(document).on('keydown', '.swiper-button-prev[role="button"], .swiper-button-next[role="button"]', function(event) {
		if (event.key === 'Enter' || event.key === ' ') {
			event.preventDefault();
			this.click();
		}
	});

	$(document).on({
		mouseenter: function () {
			$(this).find('.news-item__line').css('width', $(this).outerHeight());
		},
		mouseleave: function () {
			$(this).find('.news-item__line').css('width', '');
		}
	}, ".news-item");

	/*
	new Swiper('.news__slider_custom', {
		slidesPerView: 'auto',
		navigation: {
		  //prevEl: '.news-nav .swiper-button-prev',
		  //nextEl: '.news-nav .swiper-button-next',
		},
		breakpoints: {
		  767: {
			freeMode: true,
		  },
		}
	  });
	*/


});
$(document).ready(function(){
	$('.togler input').click(function(){

		window.location.replace("https://luxkubatura.ru/");

	});

	$('.bx-active').click(function(){
	    $('#plan').toggleClass("on");
	});
	$( ".i.dn" ).click(function() {
		$( '.colorlist').toggleClass( "is-active" );
	});
		});
