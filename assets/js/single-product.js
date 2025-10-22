jQuery(function($){

  /* +/− برای تمام اینپوت‌های تعداد در فرم سبد محصول */
  function enhanceQty($scope){
    $scope.find('form.cart .quantity').each(function(){
      var $q = $(this);
      if ($q.data('shopido-enhanced')) return; // فقط یک‌بار
      var $input = $q.find('input.qty');
      if (!$input.length) return;

      $q.data('shopido-enhanced', true);
      var min = parseFloat($input.attr('min')) || 1;
      var step = parseFloat($input.attr('step')) || 1;
      var max = parseFloat($input.attr('max')) || Infinity;

      $q.addClass('shopido-qty');
      var $minus = $('<button type="button" class="shopido-qty__btn" aria-label="کم کردن">−</button>');
      var $plus  = $('<button type="button" class="shopido-qty__btn" aria-label="زیاد کردن">+</button>');

      // در RTL می‌ذاریم: [ − ][ input ][ + ]
      $q.prepend($minus);
      $q.append($plus);

      $minus.on('click', function(){
        var val = parseFloat($input.val()) || min;
        val = Math.max(min, val - step);
        $input.val(val).trigger('change');
      });
      $plus.on('click', function(){
        var val = parseFloat($input.val()) || min;
        val = Math.min(max, val + step);
        $input.val(val).trigger('change');
      });
    });
  }
  enhanceQty($(document));

  // اگر محصول متغیره و المنت‌ها بعد از انتخاب ایجاد می‌شن:
  $(document).on('found_variation wc_cart_button_updated', function(){ enhanceQty($(document)); });

  /* کپی لینک اشتراک */
  $(document).on('click', '.shopido-share__copy', function(){
    var url = $(this).data('url') || window.location.href;
    navigator.clipboard.writeText(url).then(()=>{
      $(this).text('✔'); // فیدبک کوچیک
      var self = this;
      setTimeout(function(){ $(self).text('⧉'); }, 1200);
    });
  });
  
  
    $('.woocommerce div.product .product_description .tabs li').on('click', function () {
        var $this = $(this);
        var $tabContent = $this.closest('.product_description').find('.tabs_content');
        
        // بستن تمامی محتوای تب‌ها
        $tabContent.removeClass('active');

        // فعال کردن محتوا
        $this.siblings().removeClass('active');
        $this.addClass('active');

        // پیدا کردن محتوای مربوط به تب و نمایش آن
        var target = $this.data('target');
        $(target).addClass('active');
    });

});
