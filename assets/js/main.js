/* Sticky helper: add .is-stuck when bar touches top + admin bar offset */
(function(){
  const bar = document.querySelector('.shec-shop-filters');
  if(!bar) return;

  // جبران ادمین‌بار وردپرس
  const adminBar = document.getElementById('wpadminbar');
  const adminOffset = adminBar ? adminBar.offsetHeight : 0;
  document.documentElement.style.setProperty('--shec-sticky-top', adminOffset + 'px');

  // سایه وقتی چسبید
  const sentry = document.createElement('div');
  sentry.style.position = 'absolute';
  sentry.style.top = '0';
  sentry.style.height = '1px';
  bar.parentNode.insertBefore(sentry, bar);

  const io = new IntersectionObserver((entries)=>{
    const e = entries[0];
    // اگر sentry دیده نمی‌شه یعنی بار چسبیده
    if(!e.isIntersecting) bar.classList.add('is-stuck');
    else bar.classList.remove('is-stuck');
  }, {rootMargin: `-${adminOffset}px 0px 0px 0px`, threshold: [1]});
  io.observe(sentry);
  // ESC
  doc.addEventListener('keydown', function(e){
    if(e.key === 'Escape' && root.classList.contains('is-open')) closeMM(e);
  });
})();


