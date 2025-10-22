;(function($){
  // یک اینیت برای هر پنل سوایپر
  function initSwiperInPanel($panel){
    const $wrap = $panel; // خود پنل
    const $cont = $wrap.find('.shopido-tpc__wrap'); // .swiper container
    if(!$cont.length) return;

    if ($cont.data('shopido-initialized')) return;

    const data = $cont.data('shopido-swiper') || {};

    const nextEl = $wrap.find('.shopido-tpc__arrow.next')[0] || null;
    const prevEl = $wrap.find('.shopido-tpc__arrow.prev')[0] || null;
    const pagEl  = $wrap.find('.shopido-tpc__dots')[0]         || null;

    const wantNav = !!data.navigation && nextEl && prevEl;
    const wantPag = !!data.pagination && pagEl;

    const opts = {
      slidesPerView: data.slidesPerView || 4,
      spaceBetween:  data.spaceBetween  || 16,
      loop: !!data.loop,
      speed: data.speed || 500,
      autoplay: data.autoplay || false,
      breakpoints: data.breakpoints || {},
      navigation: wantNav ? { nextEl, prevEl } : false,
      pagination: wantPag ? { el: pagEl, clickable: true } : false,
    };

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

  // کا운ت‌دان برای کارت‌های تب «ویژه»
  function tickCountdowns($scope){
    const $items = $scope.find('.shopido-card__countdown[data-countdown]');
    if(!$items.length) return;

    function update(){
      const now = Math.floor(Date.now()/1000);
      $items.each(function(){
        const $cd = $(this);
        const end = parseInt($cd.data('countdown'),10) || 0;
        let diff = end - now;
        if(diff < 0) diff = 0;

        const d = Math.floor(diff / 86400); diff %= 86400;
        const h = Math.floor(diff / 3600);  diff %= 3600;
        const m = Math.floor(diff / 60);
        const s = diff % 60;

        $cd.find('.d').text(String(d).padStart(2,'0'));
        $cd.find('.h').text(String(h).padStart(2,'0'));
        $cd.find('.m').text(String(m).padStart(2,'0'));
        $cd.find('.s').text(String(s).padStart(2,'0'));
      });
    }
    update();
    // هر 1 ثانیه آپدیت
    const timerId = setInterval(update, 1000);
    // اگر پنل عوض شد، تایمر رو قطع کن
    $scope.data('countdown-timer', timerId);
  }
  function stopCountdowns($scope){
    const id = $scope.data('countdown-timer');
    if(id){ clearInterval(id); $scope.removeData('countdown-timer'); }
  }

  function init($scope){
    const $root = $scope.find('.shopido-tpc');
    if(!$root.length) return;

    // تب‌ها
    const $tabs   = $root.find('.shopido-tpc__tab');
    const $panels = $root.find('.shopido-tpc__panel');

    // پنل فعال اولیه
    const $activePanel = $panels.filter('.is-active').first();
    initSwiperInPanel($activePanel);
    // اگر پنل ویژه بود، کاونت‌دان رو بزن
    if($activePanel.is('[data-panel="feat"]')) tickCountdowns($activePanel);

    // سوییچ تب‌ها
    $tabs.on('click', function(e){
      e.preventDefault();
      const $btn = $(this);
      const key  = $btn.data('tab');

      // تب‌ها
      $tabs.removeClass('is-active').attr('aria-selected', 'false');
      $btn.addClass('is-active').attr('aria-selected','true');

      // پنل‌ها
      $panels.removeClass('is-active');
      stopCountdowns($panels); // تایمر قبلی رو اگر بود متوقف کن
      const $target = $panels.filter('[data-panel="'+key+'"]');
      $target.addClass('is-active');

      // سوایپر پنل جدید
      initSwiperInPanel($target);

      // اگر پنل جدید ویژه است، کاونت‌دان را فعال کن
      if(key === 'feat') tickCountdowns($target);
    });
  }

  $(window).on('elementor/frontend/init', function(){
    elementorFrontend.hooks.addAction('frontend/element_ready/shopido-tabbed-product-carousel.default', init);
  });
})(jQuery);
