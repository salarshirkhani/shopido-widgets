(function($){
  let timer = null, closing = false;

  function openViewer(data){
    const $v = $('#shopido-story-viewer');
    const isVideo = data.type === 'video';
    // ساخت DOM
    $v.html(
      '<div class="shopido-story-stage">'+
        '<div class="shopido-story-top">'+
          '<div class="shopido-progress"><div class="shopido-bar"><i></i></div></div>'+
          '<div class="shopido-head">'+
            '<span class="av"><img src="'+(data.avatar||'')+'" alt=""></span>'+
            '<span class="ttl">'+(data.title||'استوری')+'</span>'+
            '<span class="shopido-close" aria-label="بستن">×</span>'+
          '</div>'+
        '</div>'+
        '<div class="shopido-media">'+
          (isVideo ? '<video id="story-media" playsinline muted autoplay></video>' : '<img id="story-media" alt="">')+
        '</div>'+
        '<div class="shopido-actions">'+
          '<button type="button" class="shopido-like"><span class="heart">❤</span><span class="txt">پسندیدم</span></button>'+
        '</div>'+
      '</div>'
    );
    const $bar = $v.find('.shopido-bar i');
    const $close = $v.find('.shopido-close');
    const $like = $v.find('.shopido-like');
    const $media = $('#story-media');

    // تنظیم منبع مدیا
    if(isVideo){
      $media.attr('src', data.media).prop('muted', true).attr('playsinline', true)[0].play().catch(()=>{});
      bindVideo($media[0], $bar, close);
    }else{
      $media.attr('src', data.media);
      startImageTimer($bar, close, 20000); // 20s
    }

    // اکشن‌ها
    $close.on('click', close);
    $v.off('click.bg').on('click.bg', function(e){ if(e.target.id==='shopido-story-viewer') close(); });
    $like.on('click', function(){ $(this).toggleClass('liked'); });

    function close(){
      if(closing) return; closing = true;
      clearTimer();
      $v.removeClass('open').off('click.bg').empty();
      closing = false;
    }
    function clearTimer(){ if(timer){ clearTimeout(timer); timer = null; } }
    $v.addClass('open');
  }

  function startImageTimer($bar, close, dur){
    // انیمیشن ساده با requestAnimationFrame
    let start = null;
    function step(ts){
      if(!start) start = ts;
      const p = Math.min(1, (ts - start) / dur);
      $bar.css('width', (p*100)+'%');
      if(p < 1){ timer = requestAnimationFrame(step); } else { close(); }
    }
    timer = requestAnimationFrame(step);
  }

  function bindVideo(video, $bar, close){
    function update(){
      if(!video.duration || !isFinite(video.duration)) return;
      const p = Math.min(1, video.currentTime / video.duration);
      $bar.css('width', (p*100)+'%');
    }
    video.addEventListener('timeupdate', update);
    video.addEventListener('loadedmetadata', function(){
      // اگر متادیتا نیامد، fallback 20s
      if(!video.duration || !isFinite(video.duration)){
        startImageTimer($bar, close, 20000);
      }
    });
    video.addEventListener('ended', close);
  }

  // کلیک روی آیتم‌های استوری
  $(document).on('click', '.shopido-story-item', function(e){
    e.preventDefault();
    const $it = $(this);
    openViewer({
      title:  $it.data('title') || '',
      avatar: $it.data('avatar') || '',
      media:  $it.data('media') || '',
      type:   $it.data('type') || 'image'
    });
  });

})(jQuery);
