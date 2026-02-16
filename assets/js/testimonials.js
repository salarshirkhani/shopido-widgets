(function(){
  function qs(el, s){ return el.querySelector(s); }
  function qsa(el, s){ return Array.from(el.querySelectorAll(s)); }

  function parseHTML(html){
    const doc = new DOMParser().parseFromString(html, "text/html");
    return doc;
  }

  function findWidgetInDoc(doc, widgetId){
    // همون section که data-shpd-testimonials داره
    const nodes = doc.querySelectorAll('[data-shpd-testimonials]');
    for (const n of nodes){
      try{
        const data = JSON.parse(n.getAttribute('data-shpd-testimonials') || '{}');
        if (data.widgetId === widgetId) return n;
      }catch(e){}
    }
    return null;
  }

  function setHeadRel(doc){
    // برای حالت Ajax: rel prev/next رو از doc جدید بردار و روی head فعلی ست کن
    const rels = ['prev','next'];
    rels.forEach(rel=>{
      const cur = document.head.querySelector(`link[rel="${rel}"]`);
      if (cur) cur.remove();
      const incoming = doc.head.querySelector(`link[rel="${rel}"]`);
      if (incoming){
        const l = document.createElement('link');
        l.rel = rel;
        l.href = incoming.href;
        document.head.appendChild(l);
      }
    });
  }

  function openVideoModal(root, url){
    const modal = qs(root, '.shpd-t-modal');
    const content = qs(root, '.shpd-t-modal-content');
    if (!modal || !content) return;

    content.innerHTML = '';

    // اگر یوتیوب/ویimeo → iframe، اگر mp4 → video
    const isMP4 = /\.mp4(\?|$)/i.test(url);
    if (isMP4){
      const v = document.createElement('video');
      v.controls = true;
      v.autoplay = true;
      v.src = url;
      content.appendChild(v);
    } else {
      const iframe = document.createElement('iframe');
      iframe.allow = 'autoplay; fullscreen; picture-in-picture';
      iframe.allowFullscreen = true;
      iframe.src = url;
      content.appendChild(iframe);
    }

    modal.hidden = false;
    document.documentElement.style.overflow = 'hidden';
  }

  function closeModal(root){
    const modal = qs(root, '.shpd-t-modal');
    const content = qs(root, '.shpd-t-modal-content');
    if (!modal || !content) return;

    modal.hidden = true;
    content.innerHTML = '';
    document.documentElement.style.overflow = '';
  }

  async function ajaxNavigate(root, href, push){
    const loader = qs(root, '.shpd-t-loader');
    const data = JSON.parse(root.getAttribute('data-shpd-testimonials') || '{}');
    const grid = qs(root, `.shpd-t-grid`);

    if (!href || !grid || !data.widgetId) return;

    loader && (loader.hidden = false);

    try{
      const res = await fetch(href, { credentials: 'same-origin' });
      const html = await res.text();
      const doc = parseHTML(html);

      const newWidget = findWidgetInDoc(doc, data.widgetId);
      if (!newWidget) throw new Error('Widget not found in response');

      const newGrid = qs(newWidget, '.shpd-t-grid');
      const newNav  = qs(newWidget, '.shpd-t-nav');

      if (!newGrid || !newNav) throw new Error('Invalid response structure');

      // replace
      grid.replaceWith(newGrid);
      qs(root, '.shpd-t-nav').replaceWith(newNav);

      // update dataset (page/maxpages etc.)
      root.setAttribute('data-shpd-testimonials', newWidget.getAttribute('data-shpd-testimonials'));

      // update rel prev/next for SEO helpers
      setHeadRel(doc);

      if (push){
        history.pushState({ shpdWidget: data.widgetId, href }, '', href);
      }
    } catch(e){
      // اگر Ajax شکست خورد، progressive enhancement: برو به صفحه واقعی
      window.location.href = href;
    } finally{
      loader && (loader.hidden = true);
    }
  }

  function bind(root){
    // pagination
    root.addEventListener('click', (ev)=>{
      const a = ev.target.closest('.shpd-t-prev, .shpd-t-next');
      if (a && a.tagName === 'A'){
        // progressive enhancement: اگر disabled بود کاری نکن
        if (a.getAttribute('data-disabled') === '1') return;
        ev.preventDefault();
        ajaxNavigate(root, a.href, true);
      }

      const btn = ev.target.closest('.shpd-t-video');
      if (btn){
        ev.preventDefault();
        const url = btn.getAttribute('data-video');
        if (url) openVideoModal(root, url);
      }

      const close = ev.target.closest('[data-close="1"]');
      if (close) closeModal(root);
    });

    // ESC close modal
    document.addEventListener('keydown', (e)=>{
      if (e.key === 'Escape') closeModal(root);
    });
  }

  function boot(){
    document.querySelectorAll('[data-shpd-testimonials]').forEach(bind);

    // back/forward
    window.addEventListener('popstate', (e)=>{
      // اگر کاربر back/forward زد، ajax sync کنیم
      const st = e.state;
      if (st && st.href){
        document.querySelectorAll('[data-shpd-testimonials]').forEach(root=>{
          const data = JSON.parse(root.getAttribute('data-shpd-testimonials')||'{}');
          if (data.widgetId === st.shpdWidget){
            ajaxNavigate(root, st.href, false);
          }
        });
      }
    });
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot);
  else boot();

  // Elementor Editor support
  if (window.elementorFrontend && window.elementorFrontend.hooks){
    window.elementorFrontend.hooks.addAction(
      'frontend/element_ready/shopido_testimonials.default',
      function($scope){
        const root = $scope[0].querySelector('[data-shpd-testimonials]');
        if (root) bind(root);
      }
    );
  }
})();
