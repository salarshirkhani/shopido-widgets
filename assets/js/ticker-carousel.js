(function(){
  function rebuild(root){
    if (!root) return;

    // اگر لاین نداریم، بسازیم و Track اصلی را داخلش ببریم
    let line = root.querySelector('.shopido-ticker-line');
    let firstTrack = root.querySelector('.shopido-ticker-track');

    if (!line){
      line = document.createElement('div');
      line.className = 'shopido-ticker-line';
      if (firstTrack) {
        root.appendChild(line);
        line.appendChild(firstTrack);
      } else {
        // اگر هیچ Trackی نیست، خروج
        return;
      }
    } else {
      // اگر لاین هست ولی هنوز Track اصلی خارج از لاین است، منتقلش کن
      if (firstTrack && firstTrack.parentNode !== line){
        line.appendChild(firstTrack);
      }
    }

    // تنظیمات از data-*
    const speed = parseFloat(root.getAttribute('data-speed') || '25');
    const gap   = parseFloat(root.getAttribute('data-gap')   || '16');

    root.style.setProperty('--speed', speed + 's');
    root.style.setProperty('--gap',   gap + 'px');

    // کلون‌های قبلی را پاک کن
    line.querySelectorAll('.shopido-ticker-track.__clone').forEach(n => n.remove());

    // حالا به قدری Track کلون کنیم تا مجموع عرض ≥ 2× ویوپورت شود
    const vw         = root.clientWidth || root.offsetWidth;
    const baseWidth  = firstTrack.scrollWidth;
    let totalWidth   = baseWidth;

    while (totalWidth < vw * 2){
      const c = firstTrack.cloneNode(true);
      c.classList.add('__clone');
      line.appendChild(c);
      totalWidth += c.scrollWidth;
    }

    // مدت زمان انیمیشن را روی خود لاین اعمال کن (اگر در ادیتور تغییر کند)
    line.style.animationDuration = speed + 's';
  }

  function boot(scope){
    (scope || document).querySelectorAll('.shopido-ticker-carousel').forEach(rebuild);
  }

  // Front
  document.addEventListener('DOMContentLoaded', function(){ boot(document); });

  // Elementor editor
  if (window.elementorFrontend && window.elementorFrontend.hooks){
    elementorFrontend.hooks.addAction(
      'frontend/element_ready/shopido-ticker-carousel.default',
      function($scope){ boot($scope[0]); }
    );
  }

  // Resize → rebuild
  let rAF;
  window.addEventListener('resize', function(){
    cancelAnimationFrame(rAF);
    rAF = requestAnimationFrame(function(){ boot(document); });
  });

  // اگر در ادیتور data-* عوض شد → rebuild
  const mo = new MutationObserver(function(muts){
    let need = false;
    for (const m of muts){
      if (m.type === 'attributes' && /^data-(speed|gap|direction)$/.test(m.attributeName)){
        need = true; break;
      }
    }
    if (need) boot(document);
  });
  mo.observe(document.documentElement, { attributes:true, subtree:true });
})();
