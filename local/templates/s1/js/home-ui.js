(function () {
  'use strict';

  function ready(callback) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', callback, {once: true});
    } else {
      callback();
    }
  }

  ready(function () {
    var menu = document.getElementById('menu');
    var menuToggle = document.querySelector('.header__toggle');
    var search = document.getElementById('search');
    var searchButtons = document.querySelectorAll('.search');

    function setMenu(open) {
      if (!menu || !menuToggle) return;
      menu.hidden = !open;
      menu.style.display = open ? 'block' : 'none';
      menuToggle.classList.toggle('active', open);
      menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      document.body.style.overflow = open ? 'hidden' : '';
    }

    if (menu && menuToggle) {
      menu.hidden = true;
      menuToggle.setAttribute('aria-controls', 'menu');
      menuToggle.setAttribute('aria-expanded', 'false');
      menuToggle.addEventListener('click', function () {
        setMenu(menu.hidden);
      });
    }

    Array.prototype.forEach.call(searchButtons, function (button) {
      button.addEventListener('click', function () {
        if (search) search.classList.toggle('dn');
      });
    });

    window.addEventListener('load', function () {
      var header = document.querySelector('.header__wrapper');
      if (header) header.classList.add('fixed');
    }, {once: true});
  });
}());
