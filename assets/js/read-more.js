(function($){
  function resolveOverlayColor(el){
    // اگر --srm-overlay ست نشده بود، از بک‌گراند صفحه استفاده کن
    var hasCustom = el.style.getPropertyValue('--srm-overlay');
    if(!hasCustom){
      var pageBg = getComputedStyle(document.body).backgroundColor || '#ffffff';
      el.style.setProperty('--srm-overlay', pageBg);
    }
  }

  $(function(){
    $('.shopido-rm').each(function(){
      var $wrap = $(this);
      var $btn = $wrap.find('.shopido-rm__toggle');
      var $label = $wrap.find('.shopido-rm__label')[0];

      resolveOverlayColor(this);

      $btn.on('click', function(){
        var isOpen = $wrap.toggleClass('is-open').hasClass('is-open');
        var more = $btn.data('more');
        var less = $btn.data('less');
        if($label){ $label.textContent = isOpen ? less : more; }
      });
    });
  });
})(jQuery);
