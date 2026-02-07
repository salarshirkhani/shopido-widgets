(function($){
  const SLIDE_DURATION = 10000; // 30s

  let raf = null, tmo = null, closing = false, keyBound = false;

  function htmlDecode(s){ const d=document.createElement('div'); d.innerHTML=s||''; return d.textContent||d.innerText||''; }
  function safeParse(raw){ try{ const a=JSON.parse(htmlDecode(raw||'[]')); return Array.isArray(a)?a:[]; }catch(e){ return []; } }

  function openViewer(data){
    const $root = $('#shopido-story-viewer');
    const medias = Array.isArray(data.mediaList) ? data.mediaList : [];
    if(!medias.length) return;

    let idx = 0, currentVideo = null;
    let endTime = 0, remaining = 0, paused = false;

    buildStage();
    show(idx);
    bindKeys();

    function buildStage(){
      $root.html(
        '<div class="shopido-story-stage" role="dialog" aria-modal="true" aria-label="نمایش استوری">'+
          // لایه گرادیان بالا
          '<div class="shopido-topfade"></div>'+
          '<div class="shopido-story-top">'+
            '<div class="shopido-progress"></div>'+
            '<div class="shopido-head" dir="rtl">'+
              '<span class="av"><img src="'+(data.avatar||'')+'" alt=""></span>'+
              '<span class="ttl">'+(data.title||'استوری')+'</span>'+
              '<span class="shopido-counter" aria-live="polite"></span>'+
              '<button type="button" class="shopido-close" aria-label="بستن">×</button>'+
            '</div>'+
          '</div>'+
          '<div class="shopido-media" tabindex="0"></div>'+
          '<div class="shopido-actions">'+
            '<button type="button" class="shopido-like"><span class="heart">❤</span><span class="txt">پسندیدم</span></button>'+
          '</div>'+
          // اینستاگرامی: راست=بعدی، چپ=قبلی
          '<button class="shopido-tapzone right" aria-label="بعدی"></button>'+
          '<button class="shopido-tapzone left" aria-label="قبلی"></button>'+
          '<button class="shopido-nav next" aria-label="بعدی">›</button>'+   // راست
          '<button class="shopido-nav prev" aria-label="قبلی">‹</button>'+   // چپ
        '</div>'
      );

      const $prog = $root.find('.shopido-progress');
      $prog.html(medias.map(() => '<div class="shopido-bar"><i></i></div>').join(''));

      $root.off('click.bg').on('click.bg', (e)=>{ if(e.target.id==='shopido-story-viewer') close(); });
      $root.find('.shopido-close').on('click', close);
      $root.find('.shopido-like').on('click', function(){ $(this).toggleClass('liked'); });

      // تاپ‌زون/فلش: راست=next ، چپ=prev
      $root.find('.shopido-nav.next, .shopido-tapzone.right').on('click', (e)=>{ e.preventDefault(); next(); });
      $root.find('.shopido-nav.prev, .shopido-tapzone.left' ).on('click', (e)=>{ e.preventDefault(); prev(); });

      // pause-on-hold
      const $stage = $root.find('.shopido-story-stage');
      $stage.on('mousedown touchstart', pause);
      $stage.on('mouseup mouseleave touchend touchcancel', resume);

      $(window).on('blur.shopido', pause);
      $(window).on('focus.shopido', resume);

      $root.addClass('open').attr('aria-hidden','false');
    }

    function show(i){
      const m = medias[i];
      if(!m) return close();

      cancelTimers();
      setCounter(i); setBars(i);

      const $wrap = $root.find('.shopido-media').empty();
      const $bar  = $root.find('.shopido-bar').eq(i).find('i');
      const isVideo = String(m.type) === 'video';

      if(isVideo){
        const $v = $('<video playsinline muted autoplay></video>').attr('src', m.url);
        $wrap.append($v); currentVideo = $v[0];
        currentVideo.addEventListener('ended', next);
        currentVideo.play().catch(()=>{});
      } else {
        $wrap.append($('<img alt="">').attr('src', m.url));
      }

      startProgress($bar, SLIDE_DURATION, next); // همیشه ۳۰ ثانیه
    }

    function startProgress($bar, dur, done){
      const start = Date.now();
      endTime = start + dur;
      remaining = dur;
      tick();
      function tick(){
        if(paused) return;
        const now = Date.now();
        const p = Math.min(1, 1 - (endTime - now) / remaining);
        $bar.css('width', (p*100)+'%');
        if(p < 1){ raf = requestAnimationFrame(tick); } else { done(); }
      }
      raf = requestAnimationFrame(tick);
      tmo = setTimeout(done, dur + 150); // fallback
    }

    function pause(){
      if(paused) return;
      paused = true; $root.addClass('paused');
      if(raf){ cancelAnimationFrame(raf); raf = null; }
      if(tmo){ clearTimeout(tmo); tmo = null; }
      if(currentVideo){ try{ currentVideo.pause(); }catch(e){} }
      remaining = Math.max(0, endTime - Date.now());
    }
    function resume(){
      if(!paused) return;
      paused = false; $root.removeClass('paused');
      if(currentVideo){ try{ currentVideo.play().catch(()=>{}); }catch(e){} }
      const $bar = $root.find('.shopido-bar').eq(idx).find('i');
      startProgress($bar, remaining || 0, next);
    }

    function next(){ idx++; (idx < medias.length) ? show(idx) : close(); }
    function prev(){ if(idx > 0){ idx--; show(idx); } }

    function setCounter(i){ $root.find('.shopido-counter').text((i+1)+' / '+medias.length); }
    function setBars(i){
      $root.find('.shopido-bar i').each(function(k){
        $(this).css('width', k < i ? '100%' : '0%');
      });
    }

    function cancelTimers(){
      if(raf){ cancelAnimationFrame(raf); raf = null; }
      if(tmo){ clearTimeout(tmo); tmo = null; }
      if(currentVideo){ try{ currentVideo.pause(); currentVideo.src=''; currentVideo.load(); }catch(e){} currentVideo=null; }
    }

    function close(){
      if(closing) return; closing = true;
      cancelTimers(); $(window).off('.shopido');
      $root.removeClass('open').attr('aria-hidden','true').off('click.bg').empty();
      unbindKeys(); closing = false;
    }

    function bindKeys(){
      if(keyBound) return; keyBound = true;
      $(document).on('keydown.shopidoStory', function(e){
        if(e.key === 'Escape'){ e.preventDefault(); return close(); }
        if(e.key === 'ArrowRight'){ e.preventDefault(); return next(); } // اینستاگرامی
        if(e.key === 'ArrowLeft'){  e.preventDefault(); return prev(); }
      });
    }
    function unbindKeys(){ if(!keyBound) return; keyBound=false; $(document).off('keydown.shopidoStory'); }
  }

  $(document).on('click', '.shopido-story-item', function(e){
    e.preventDefault();
    const mediaList = safeParse($(this).attr('data-media'));
    if(!mediaList.length) return;
    openViewer({ title: $(this).data('title')||'', avatar: $(this).data('avatar')||'', mediaList });
  });

  if (window.elementorFrontend && window.elementorFrontend.hooks) {
    elementorFrontend.hooks.addAction('frontend/element_ready/shopido-stories.default', function(){});
  }
})(jQuery);
