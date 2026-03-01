/**
 * Patrocínio – Swiper init (vanilla JS).
 */
document.addEventListener('DOMContentLoaded', function () {
  var el = document.querySelector('.ec-swiper');
  if (!el) return;

  new Swiper(el, {
    loop: true,
    autoplay: {
      delay: 3500,
      disableOnInteraction: false,
      pauseOnMouseEnter: true,
    },
    slidesPerView: 2,
    spaceBetween: 16,
    grabCursor: true,

    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },

    breakpoints: {
      640: {
        slidesPerView: 3,
        spaceBetween: 20,
      },
      1024: {
        slidesPerView: 5,
        spaceBetween: 24,
      },
    },

    a11y: {
      prevSlideMessage: 'Slide anterior',
      nextSlideMessage: 'Próximo slide',
      firstSlideMessage: 'Primeiro slide',
      lastSlideMessage: 'Último slide',
      paginationBulletMessage: 'Ir para slide {{index}}',
    },
  });
});
