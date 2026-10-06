jQuery(document).ready(function($) {

  var scroll_width = window.innerWidth - document.documentElement.clientWidth;

  $('img, a').on('dragstart', function(event) {
    event.preventDefault();
  });

  // Popups

  const magnificPopupArgs = {
    type: 'inline',
    removalDelay: 400,
    mainClass: 'mfp-fade',
    tClose: 'Закрыть',
    callbacks: {
      open: function() {
        $('.header__wrapper.fixed').css('margin-right', scroll_width);
      },
      close: function() {
        if ($('#menu').is(':hidden')) $('.header__wrapper.fixed').css('margin-right', '');
      }
    }
  };

  function popup_open(src, type) {
    let args = $.extend(true, {}, magnificPopupArgs);
    if (type == 'iframe' || type == 'image') args['type'] = type;
    args['items'] = {
      src: src
    };
    $.magnificPopup.open(args);
  }

  $('body').on('click', '[data-popup]', function() {
    var type = $(this).attr('data-popup');
    popup_open($(this).attr('href'), type);
    return false;
  });

  // Popups END

  $('select:not([size])').styler();

  $('[type="tel"]').mask('+7 (999) 999-99-99');

	$('.filter-price__range input').ionRangeSlider({
		type: "double",
		skin: "round",
		hide_min_max: true,
		hide_from_to: true
	});

  $('.js-header-height').matchHeight({
    byRow: false,
  });
  $(window).on('load', function() {
    $('.header__wrapper').addClass('fixed');
  });

  function containerFull() {
    $('.container__full').each(function() {
      var wrap = $(this).closest('.container');
      $(this).outerWidth(($(window).width() - (wrap.offset().left + parseInt(wrap.css('padding-left')))).toFixed());
    });
  }

  $(window).on('load resize', function() {
    containerFull();
  });
  containerFull();

  // Sliders

  $('.banner-thumb').each(function() {
    $(this).attr('data-index', $(this).index());
  });

  var bannerSlider = new Swiper('.banner__slider', {
    init: false,
    pagination: {
      el: '.banner__pagination .swiper-pagination',
      clickable: true,
    },
    navigation: {
      prevEl: '.swiper-button-prev',
      nextEl: '.swiper-button-next',
    },
    breakpoints: {
      1199: {
        autoHeight: true,
      },
    }
  });

  var bannerThumbsSlider = new Swiper('.banner__thumbs', {
    slidesPerView: 3,
    direction: 'vertical',
    mousewheel: true,
  });

  if ($(bannerSlider.params.el).length > 0 && $(bannerThumbsSlider.params.el).length > 0) {
    bannerSlider.on('slideChange init', function() {
      $('.banner-thumb').removeClass('active');
      $('.banner-thumb[data-index="' + bannerSlider.realIndex + '"]').addClass('active');
      bannerThumbsSlider.slideTo(bannerSlider.realIndex);
    }).init();

    $('.banner-thumb').click(function() {
      bannerSlider.slideTo($(this).attr('data-index'));
      return false;
    });
  }

  $('.product-thumbs__item').each(function() {
    $(this).attr('data-index', $(this).index());
  });
  var productThumbsSlider = new Swiper('.product-thumbs', {
    slidesPerView: 'auto',
    direction: 'vertical',
    mousewheel: true,
  });
  var productSlider = new Swiper('.product-gallery', {
    init: false,
    effect: 'fade',
    fadeEffect: {
      crossFade: true
    },
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
  });
  if ($(productThumbsSlider.params.el).length > 0 && $(productSlider.params.el).length > 0) {
    productSlider.on('slideChange init', function() {
      $('.product-thumbs__item').removeClass('active');
      $('.product-thumbs__item[data-index="' + productSlider.realIndex + '"]').addClass('active');
      productThumbsSlider.slideTo(productSlider.realIndex);
    }).init();

    $('.product-thumbs__item').click(function() {
      productSlider.slideTo($(this).attr('data-index'));
      return false;
    });
  }

  $('.similar').each(function(index) {
    $(this).attr('data-index', index);

    new Swiper('[data-index="' + index + '"] .similar__slider', {
      slidesPerView: 4,
      centerInsufficientSlides: true,
      navigation: {
        prevEl: '[data-index="' + index + '"] .similar-nav .swiper-button-prev',
        nextEl: '[data-index="' + index + '"] .similar-nav .swiper-button-next',
      },
      breakpoints: {
        1699: {
          slidesPerView: 3,
        },
        767: {
          freeMode: true,
          slidesPerView: 'auto',
        },
      }
    });
  });

  new Swiper('.news__slider', {
    slidesPerView: 'auto',
    navigation: {
      prevEl: '.news-nav .swiper-button-prev',
      nextEl: '.news-nav .swiper-button-next',
    },
    breakpoints: {
      767: {
        freeMode: true,
      },
    }
  });

  new Swiper('.post-gallery', {
    navigation: {
      prevEl: '.swiper-button-prev',
      nextEl: '.swiper-button-next',
    }
  });

  function swiperNavHidden() {
    $('.swiper-nav').each(function() {
      var wrap = $(this),
        prev = wrap.find('.swiper-button-prev'),
        next = wrap.find('.swiper-button-next');

      if (prev.hasClass('swiper-button-disabled') && next.hasClass('swiper-button-disabled')) {
        wrap.addClass('swiper-nav_hidden');
      } else {
        wrap.removeClass('swiper-nav_hidden');
      }
    });
  }
  $(window).on('load resize', function() {
    swiperNavHidden();
  });

  // Sliders END

  $('.news-item').hover(function() {
    $(this).find('.news-item__line').css('width', $(this).outerHeight());
  }, function() {
    $(this).find('.news-item__line').css('width', '');
  });

  // Menu

  function menuBodyShow() {
    $('body').css({
      'overflow': 'hidden',
      'margin-right': scroll_width
    });

    $('.header__wrapper.fixed').css('margin-right', scroll_width);
  }

  function menuBodyClose() {
    setTimeout(function() {
      if ($('#menu').is(':hidden')) {
        if ($('#cat-menu').length == 0 || $('#cat-menu').is(':hidden')) {
          $('body').css({
            'overflow': '',
            'margin-right': ''
          });

          $('.header__wrapper.fixed').css('margin-right', '');
        }
      }
    }, 405);
  }

  function mobMenuShow() {
    $('.header__toggle').addClass('active');
    $('#menu').css('top', $('.header').outerHeight() - 1).fadeIn(400);

    menuBodyShow();
  }

  function mobMenuClose() {
    $('.header__toggle').removeClass('active');
    $('#menu').fadeOut(400);

    menuBodyClose();
  }

  $('.header__toggle').click(function() {
    if ($('#menu').is(':hidden')) {
      catMenuClose();
      mobMenuShow();
    } else {
      mobMenuClose();
    }
  });

  // Menu END

  // Cat Menu

  function catMenuShow() {
    $(window).scrollTop(0);
    $('.cat-menu-mob__toggle, .cat-menu-mob').addClass('active');
    $('#cat-menu').css('top', $('.cat-menu-mob').offset().top + $('.cat-menu-mob').outerHeight() - 1).fadeIn(400);

    menuBodyShow();
  }

  function catMenuClose() {
    $('.cat-menu-mob__toggle, .cat-menu-mob').removeClass('active');
    $('#cat-menu').fadeOut(400);

    menuBodyClose();
  }

  $('.cat-menu-mob__toggle').click(function() {
    if ($('#cat-menu').is(':hidden')) {
      mobMenuClose();
      catMenuShow();
    } else {
      catMenuClose();
    }
  });

  $('.cat-menu-dropdown').each(function(i) {
    $(this).find('.cat-menu-dropdown__list').before('<button class="cat-menu-dropdown__back"><i class="ic ic-chevron-left"></i> ' + $('.cat-menu-main__list>li').eq(i).find('>a').text() + '</button>');
  });

  $('.cat-menu-main__list>li>a').mouseenter(function() {
    if (window.matchMedia('(max-width: 1200px)').matches) return false;

    var i = $(this).closest('li').index();

    if ($('.cat-menu-dropdown').eq(i).is(':hidden')) {
      $('.cat-menu-main__list>li>a').removeClass('hover')
      $('.cat-menu-main__list>li').eq(i).find('>a').addClass('hover');
      $('.cat-menu-dropdown').hide().eq(i).fadeIn(150);

      $('.cat-menu-dropdowns').css('top', $(this).closest('li').position().top + $(this).outerHeight());
    }
  }).mouseleave(function() {
    if (window.matchMedia('(max-width: 1200px)').matches) return false;

    var i = $(this).closest('li').index(),
      dropdown = $('.cat-menu-dropdown').eq(i);

    if (dropdown.length != 0 && dropdown.is(':hover')) {} else {
      dropdown.fadeOut(150);
      $(this).removeClass('hover');
    }
  });

  $('.cat-menu-dropdown').mouseleave(function() {
    if (window.matchMedia('(max-width: 1200px)').matches) return false;

    var i = $(this).index(),
      link = $('.cat-menu-main__list>li').eq(i).find('>a');

    if (link.hasClass('hover') && !link.is(':hover')) {
      $(this).fadeOut(150);
      link.removeClass('hover');
    }
  });

  $('.cat-menu-main__list>li>a').click(function() {
    if (window.matchMedia('(max-width: 1200px)').matches) {
      var i = $(this).closest('li').index();

      $('.cat-menu-dropdown').hide().eq(i).show();

      $('#cat-menu').animate({
        scrollTop: 0
      }, 100, 'swing');

      setTimeout(function() {
        $('.cat-menu__grid').addClass('active');
      }, 100)

      return false;
    }
  });

  $('.cat-menu-dropdown__back').click(function() {
    $('.cat-menu__grid').removeClass('active');
  });


  // Cat Menu END

  $('.filter__toggle').click(function() {
    $(this).toggleClass('active');
    $(this).closest('.filter').find('.filter__wrap').slideToggle(400);

		setTimeout(function() {
			$('select:not([size])').trigger('refresh');
		}, 400)
  });

  $('.category__specs').each(function() {
    if ($(this).find('tr').length > 4) {
      $(this).after('<button class="category__toggle ic ic-chevron-bottom"></button>');
    }
  });
  $('.category__toggle').on('click', function() {
    $(this).toggleClass('active');
    $(this).siblings('.category__specs').find('tr:nth-child(n+5)').toggle();
  });

  $('.filter-item__toggle').click(function() {
    let item = $(this).closest('.filter-item'),
      toggle = $(this),
      dropdown = item.find('.filter-item__dropdown');

    toggle.toggleClass('active');
    dropdown.toggle().css({
      'max-height': $(window).height() - (toggle.offset().top - $(window).scrollTop() + toggle.outerHeight())
    });

    $(document).on("click", function(e) {
      if (!item.is(e.target) && item.has(e.target).length === 0) {
        toggle.removeClass('active');
        dropdown.hide();
      }
    });
  });

});
