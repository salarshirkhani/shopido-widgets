;(function($){
  function init($scope){
    const $wrap = $scope.find('.shopido-pc');
    const $cont = $wrap.find('.shopido-pc__swiper');
    if(!$cont.length) return;

    if ($cont.data('shopido-initialized')) return;

    const data = $cont.data('shopido-swiper') || {};

    // عناصر اختیاری را پیدا کن
    const nextEl = $wrap.find('.shopido-pc__next')[0] || null;
    const prevEl = $wrap.find('.shopido-pc__prev')[0] || null;
    const pagEl  = $wrap.find('.shopido-pc__dots')[0]  || null;

    // اگر درخواست navigation/pagination شده اما المنت‌ها نیستند، صراحتاً false بده
    const wantNav = !!data.navigation && nextEl && prevEl;
    const wantPag = !!data.pagination && pagEl;

    const opts = {
      slidesPerView: data.slidesPerView || 4,
      spaceBetween:  data.spaceBetween  || 16,
      loop: !!data.loop,
      speed: data.speed || 500,
      autoplay: data.autoplay || false,
      breakpoints: data.breakpoints || {},

      // خیلی مهم: اگر نمی‌خواهیم، false (نه undefined)
      navigation: wantNav ? { nextEl, prevEl } : false,
      pagination: wantPag ? { el: pagEl, clickable: true } : false
    };

    // فقط از رَپر المنتور استفاده کن
    const EU = elementorFrontend && elementorFrontend.utils && elementorFrontend.utils.swiper;
    if (!EU) { console.warn('Elementor swiper util not found'); return; }

    const node = $cont[0];
    if (typeof EU === 'object' && typeof EU.init === 'function') {
      EU.init(node, opts);
    } else {
      new EU(node, opts);
    }

    $cont.data('shopido-initialized', true);
  }

  $(window).on('elementor/frontend/init', function(){
    elementorFrontend.hooks.addAction('frontend/element_ready/shopido-product-carousel.default', init);
  });
})(jQuery);
