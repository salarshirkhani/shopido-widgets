(function($){
  const MIN_CHARS = 2; // کمترین تعداد کاراکتر برای شروع جستجو
  const DEBOUNCE  = 250;

  function debounce(fn, delay){
    let t;
    return function(){
      const ctx = this, args = arguments;
      clearTimeout(t);
      t = setTimeout(function(){ fn.apply(ctx, args); }, delay);
    };
  }

  function openResults($wrap){
    if ($wrap.hasClass('is-open')) return;
    $wrap.addClass('is-open');

    // بک‌دراپ برای بستن با کلیک بیرون
    if (!$wrap.find('.shopido-ajax-search__backdrop').length) {
      const $bd = $('<div class="shopido-ajax-search__backdrop" />');
      $('body').append($bd);
      $bd.on('click', function(){
        closeResults($wrap);
      });
    }
  }

  function closeResults($wrap){
    $wrap.removeClass('is-open has-results');
    $wrap.find('.shopido-ajax-search__results').empty();
    $('.shopido-ajax-search__backdrop').remove();
  }

  function renderItems(items, layout){
    if (!items || !items.length) return '';
    return items.map(function(it){
      const thumb = it.thumb ? `<img src="${it.thumb}" alt="" loading="lazy">` : '';
      if (layout === 'cards') {
        return `
          <a href="${it.link}" class="sr-item sr-item--card">
            <div class="sr-thumb">${thumb}</div>
            <div class="sr-meta">
              <div class="sr-title">${it.title || ''}</div>
              ${it.price ? `<div class="sr-price">${it.price}</div>` : ''}
            </div>
          </a>`;
      }
      // list
      return `
        <a href="${it.link}" class="sr-item sr-item--list">
          <div class="sr-thumb">${thumb}</div>
          <div class="sr-title">${it.title || ''}</div>
          ${it.price ? `<div class="sr-price">${it.price}</div>` : ''}
        </a>`;
    }).join('');
  }

  function initAjaxSearch($root){
    const $input   = $root.find('.shopido-ajax-search__input');
    const $clear   = $root.find('.shopido-ajax-search__clear');
    const $results = $root.find('.shopido-ajax-search__results');

    const endpoint = $root.data('endpoint') || (window.ajaxurl || '');
    const postType = ($root.data('post-type') || 'product').split(',');
    const terms    = ($root.data('terms') || '').split(',').filter(Boolean);
    const maxItems = parseInt($root.data('max') || '8', 10);
    const layout   = $root.data('layout') || 'cards'; // cards | list

    let lastQuery = '';
    let cache     = [];

    const doSearch = debounce(function(q){
      q = (q || '').trim();
      if (q.length < MIN_CHARS) {
        closeResults($root);
        return;
      }
      lastQuery = q;

      $.ajax({
        url: endpoint,
        method: 'POST',
        dataType: 'json',
        data: {
          action: 'shopido_ajax_search',
          s: q,
          post_type: postType,
          terms: terms,
          max: maxItems
        }
      }).done(function(res){
        const items = (res && res.items) ? res.items : [];
        cache = items;

        if (!items.length) {
          closeResults($root);
          return;
        }

        $results.html( renderItems(items, layout) );
        $root.addClass('has-results');
        openResults($root);

      }).fail(function(){
        closeResults($root);
      });
    }, DEBOUNCE);

    // تایپ
    $input.on('input', function(){
      const q = $(this).val();
      if (!q || q.trim().length < MIN_CHARS) {
        closeResults($root);
        return;
      }
      doSearch(q);
    });

    // فوکوس: اگر قبلاً نتیجه داشتیم، پنل باز شود
    $input.on('focus', function(){
      if (cache.length) {
        $results.html( renderItems(cache, layout) );
        $root.addClass('has-results');
        openResults($root);
      }
    });

    // پاک کردن
    $clear.on('click', function(){
      $input.val('');
      closeResults($root);
      $input.trigger('focus');
    });

    // بستن با Esc
    $(document).on('keydown', function(e){
      if (e.key === 'Escape') closeResults($root);
    });

    // جلوگیری از بسته‌شدن با کلیک روی نتایج
    $results.on('mousedown', function(e){ e.preventDefault(); });

    // اگر بیرون روت کلیک شد
    $(document).on('mousedown', function(e){
      if (!$.contains($root.get(0), e.target)) {
        closeResults($root);
      }
    });
  }
  
  // بعد از رندر نتایج، وضعیت نمایش را تنظیم کن
    function shopidoSetSearchState($root, itemsCount){
      if(itemsCount > 0){
        $root.addClass('has-results is-open');
      }else{
        $root.removeClass('has-results is-open');
      }
    }
    
    // نمونه‌ی استفاده بعد از گرفتن نتایج AJAX:
    $.ajax({ /* ... */ }).done(function(res){
      var items = (res && res.success && res.data && res.data.items) ? res.data.items : [];
      // ... رندر آیتم‌ها ...
      shopidoSetSearchState($root, items.length);
    }).fail(function(){
      // خطا = بدون نتیجه
      shopidoSetSearchState($root, 0);
    });


  $(function(){
    $('.shopido-ajax-search').each(function(){
      initAjaxSearch($(this));
    });
  });

})(jQuery);
