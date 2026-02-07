(function(){
  function animateCounter(el){
    if (el.__played && el.dataset.play_once === '1') return;

    const numEl   = el.querySelector('.sc-number');
    if (!numEl) return;

    const start   = parseFloat(el.dataset.start || '0');
    const end     = parseFloat(el.dataset.end   || '0');
    const dur     = Math.max(0.1, parseFloat(el.dataset.duration || '2')) * 1000;
    const grouping= el.dataset.grouping === '1';
    const locale  = (el.dataset.locale || '').trim() || document.documentElement.lang || 'fa-IR';

    const formatter = grouping ? new Intl.NumberFormat(locale) : { format: v => (''+v) };
    const t0 = performance.now();
    const delta = end - start;

    function step(now){
      const p = Math.min(1, (now - t0) / dur);
      const current = start + delta * p;
      const display = formatter.format(Math.round(current));
      numEl.textContent = display;

      if (p < 1) {
        el.__raf = requestAnimationFrame(step);
      } else {
        numEl.textContent = formatter.format(Math.round(end));
        el.__played = true;
      }
    }
    cancelAnimationFrame(el.__raf || 0);
    el.__raf = requestAnimationFrame(step);
  }

  function observe(el){
    if (el.__obs) return;
    const obs = new IntersectionObserver((entries)=>{
      entries.forEach(e=>{
        if (e.isIntersecting) animateCounter(el);
      });
    }, {threshold: .3});
    obs.observe(el);
    el.__obs = obs;
  }

  function boot(scope){
    (scope || document).querySelectorAll('.shopido-counter').forEach(observe);
  }

  document.addEventListener('DOMContentLoaded', function(){ boot(document); });

  if (window.elementorFrontend && window.elementorFrontend.hooks) {
    elementorFrontend.hooks.addAction('frontend/element_ready/shopido-counter.default', function($scope){
      boot($scope[0]);
    });
  }
})();
