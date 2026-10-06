(function () {
  'use strict';

  function initHomeSlider() {
    var sliderElement = document.querySelector('.banner__slider');
    var desktopViewport = window.matchMedia('(min-width: 1200px)');

    // Mobile uses native horizontal scrolling with CSS scroll-snap.
    if (!desktopViewport.matches || !sliderElement || typeof window.Swiper !== 'function') {
      return;
    }

    var thumbsElement = document.querySelector('.banner__thumbs');
    var thumbElements = Array.prototype.slice.call(document.querySelectorAll('.banner-thumb'));
    var thumbsSlider = null;

    thumbElements.forEach(function (thumb, index) {
      thumb.dataset.index = String(index);
    });

    if (thumbsElement) {
      thumbsSlider = new window.Swiper(thumbsElement, {
        slidesPerView: 3,
        direction: 'vertical',
        mousewheel: true
      });
    }

    var slider = new window.Swiper(sliderElement, {
      init: false,
      pagination: {
        el: document.querySelector('.banner__pagination .swiper-pagination'),
        clickable: true
      },
      navigation: {
        prevEl: sliderElement.querySelector('.swiper-button-prev'),
        nextEl: sliderElement.querySelector('.swiper-button-next')
      }
    });

    function syncThumbs() {
      thumbElements.forEach(function (thumb, index) {
        var isActive = index === slider.realIndex;
        if (isActive) {
          thumb.classList.add('active');
          thumb.setAttribute('aria-current', 'true');
        } else {
          thumb.classList.remove('active');
          thumb.removeAttribute('aria-current');
        }
      });

      if (thumbsSlider) {
        thumbsSlider.slideTo(slider.realIndex);
      }
    }

    thumbElements.forEach(function (thumb) {
      thumb.addEventListener('click', function (event) {
        event.preventDefault();
        slider.slideTo(Number(thumb.dataset.index));
      });
    });

    slider.on('init', syncThumbs);
    slider.on('slideChange', syncThumbs);
    slider.init();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initHomeSlider);
  } else {
    initHomeSlider();
  }
}());
