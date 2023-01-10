jQuery(document).ready(function($) {
    var swiper = new Swiper(".slide-content", {
        slidesPerView: 4,
        spaceBetween: 18,
        loop: true,
        centerSlide:'true',
        fade: 'true',
        grabCursor: 'true',
        pagination: {
          el: ".swiper-pagination",
          clickable: true,
        },
        navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev",
        },
      });
});