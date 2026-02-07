(function(){
  function init(root){
    (root || document).querySelectorAll('.shopido-pricing').forEach(function(wrapper){
      var card = wrapper.querySelector('.sp-card');
      if (!card) return;

      var head = card.querySelector('.sp-head');
      var body = card.querySelector('.sp-body');
      if (!head || !body) return;

      // اگر سوییچ آکاردئون خاموش شده باشد، کلاً کاری نکن
      var useAcc = wrapper.getAttribute('data-mobile-acc') === '1';

      // مدیای موبایل
      var mq = window.matchMedia('(max-width: 640px)');

      function updateState(){
        // اگر آکاردئون خاموش است → همیشه باز
        if (!useAcc){
          card.classList.add('is-open');
          body.style.display = '';
          head.setAttribute('aria-expanded', 'true');
          return;
        }

        // دسکتاپ: همیشه باز، آکاردئون فقط موبایل
        if (!mq.matches){
          card.classList.add('is-open');
          body.style.display = '';
          head.setAttribute('aria-expanded', 'true');
          return;
        }

        // موبایل: بسته/باز بر اساس کلاس is-open
        if (card.classList.contains('is-open')){
          body.style.display = 'block';
          head.setAttribute('aria-expanded', 'true');
        } else {
          body.style.display = 'none';
          head.setAttribute('aria-expanded', 'false');
        }
      }

      // حالت اولیه
      if (!card.classList.contains('is-open')){
        card.classList.add('is-open');
      }
      updateState();

      // کلیک روی هدر → فقط در موبایل و فقط وقتی آکاردئون فعاله
      head.addEventListener('click', function(){
        if (!useAcc) return;
        if (!mq.matches) return; // فقط موبایل

        card.classList.toggle('is-open');
        updateState();
      });

      // تغییر سایز (وقتی موبایل/دسکتاپ عوض می‌شود)
      if (mq.addEventListener){
        mq.addEventListener('change', updateState);
      } else if (mq.addListener){
        mq.addListener(updateState);
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function(){
    init(document);
  });

  if (window.elementorFrontend && window.elementorFrontend.hooks){
    window.elementorFrontend.hooks.addAction(
      'frontend/element_ready/shopido-pricing.default',
      function($scope){ init($scope[0]); }
    );
  }
})();
