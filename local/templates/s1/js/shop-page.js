(function(){
  // Neighboring shops stay a native scroll list — out of scope for this change.
  document.querySelectorAll('[data-shop-scroll]').forEach(function(button){
    button.addEventListener('click',function(){
      var list=document.querySelector('.shop-page__neighbors');
      if(list) list.scrollBy({left: Number(button.getAttribute('data-shop-scroll'))*240,behavior:'smooth'});
    });
  });

  if (typeof Swiper === 'undefined') return;

  // Salon photos: real slider (site's bundled Swiper 4.5, same pattern as .similar__slider elsewhere).
  var photos = document.querySelector('.shop-page__photos');
  if (photos && photos.querySelector('.swiper-slide')) {
    // up to 3 photos on screen at once (fewer if the shop has fewer photos); 2 on narrow phones
    var perView = Math.max(1, Math.min(3, Number(photos.getAttribute('data-per-view')) || 3));
    var photoOptions = {
      speed: 400,
      slidesPerView: perView,
      spaceBetween: 10,
      watchOverflow: true,
      breakpoints: { 575: { slidesPerView: Math.min(2, perView), spaceBetween: 8 } }
    };
    var photoPrev = photos.querySelector('.shop-page__photos-prev');
    var photoNext = photos.querySelector('.shop-page__photos-next');
    if (photoPrev && photoNext) photoOptions.navigation = { prevEl: photoPrev, nextEl: photoNext };
    new Swiper(photos, photoOptions);
  }

  // Shop products: same slider mechanics as the rest of the site's product carousels.
  var productsViewport = document.querySelector('.shop-page__products-viewport');
  if (productsViewport && productsViewport.querySelector('.swiper-slide')) {
    new Swiper(productsViewport, {
      speed: 400,
      slidesPerView: 6,
      spaceBetween: 20,
      watchOverflow: true,
      navigation: {
        prevEl: document.querySelector('[data-product-scroll="-1"]'),
        nextEl: document.querySelector('[data-product-scroll="1"]')
      },
      breakpoints: {
        1199: { slidesPerView: 4, spaceBetween: 20 },
        700: { slidesPerView: 1.3, spaceBetween: 14 }
      }
    });
  }
})();