<?php
if (!defined('ABSPATH')) exit;

/* ---------- CPT: استوری‌ها ---------- */
add_action('init', function(){
  $labels = [
    'name' => 'استوری‌ها','singular_name'=>'استوری','menu_name'=>'استوری‌ها',
    'add_new'=>'افزودن استوری','add_new_item'=>'استوری جدید','edit_item'=>'ویرایش استوری',
    'all_items'=>'همه استوری‌ها','search_items'=>'جستجوی استوری','not_found'=>'چیزی پیدا نشد'
  ];
  $args = [
    'labels'=>$labels,'public'=>true,'show_ui'=>true,'show_in_menu'=>true,
    'menu_icon'=>'dashicons-format-status','has_archive'=>false,'rewrite'=>['slug'=>'story'],
    'supports'=>['title','thumbnail'],'show_in_rest'=>true
  ];
  register_post_type('shopido_story',$args);
});

/* ---------- متاباکس: انتخاب یک مدیای استوری ---------- */
add_action('add_meta_boxes', function(){
  add_meta_box('shopido_story_media','مدیای استوری (عکس یا ویدیو)','shopido_story_media_mb','shopido_story','normal','high');
});

function shopido_story_media_mb($post){
  wp_nonce_field('shopido_story_media','shopido_story_media_nonce');
  $mid = get_post_meta($post->ID,'_story_media_id',true);
  $url = $mid ? wp_get_attachment_url($mid) : '';
  $mime= $mid ? get_post_mime_type($mid) : '';
  $type= strpos((string)$mime,'video/')===0 ? 'video' : ( $url ? 'image' : '' );
  ?>
  <style>
    .story-row{margin:10px 0}
    .story-row label{font-weight:600;display:block;margin-bottom:6px}
    .story-prev img,.story-prev video{max-width:240px;border-radius:12px;border:1px solid #e5e7eb;display:block}
  </style>
  <div class="story-row">
    <label>مدیای استوری</label>
    <input type="hidden" id="story_media_id" name="story_media_id" value="<?php echo esc_attr($mid); ?>">
    <div class="story-prev" id="story-prev">
      <?php if($url && $type==='image'): ?>
        <img src="<?php echo esc_url($url); ?>" alt="">
      <?php elseif($url && $type==='video'): ?>
        <video src="<?php echo esc_url($url); ?>" controls muted style="max-height:300px"></video>
      <?php else: ?>
        <em>هنوز مدیایی انتخاب نشده.</em>
      <?php endif; ?>
    </div>
    <p style="margin-top:8px">
      <a href="#" class="button button-secondary" id="story-pick">انتخاب/تغییر</a>
      <a href="#" class="button" id="story-clear">حذف</a>
      <span style="color:#666;font-size:12px">یک تصویر یا یک ویدیو انتخاب کنید.</span>
    </p>
  </div>
  <script>
  jQuery(function($){
    let frame, $id=$('#story_media_id'), $prev=$('#story-prev');
    $('#story-pick').on('click', function(e){
      e.preventDefault();
      if(frame){ frame.open(); return; }
      frame = wp.media({ title:'انتخاب مدیای استوری', button:{text:'انتخاب'}, multiple:false });
      frame.on('select', function(){
        const at = frame.state().get('selection').first().toJSON();
        $id.val(at.id);
        const isVideo = (at.mime && at.mime.indexOf('video/')===0);
        $prev.html(isVideo ? '<video src="'+at.url+'" controls muted style="max-height:300px"></video>'
                           : '<img src="'+at.url+'" alt="">');
      });
      frame.open();
    });
    $('#story-clear').on('click', function(e){ e.preventDefault(); $id.val(''); $prev.html('<em>هنوز مدیایی انتخاب نشده.</em>'); });
  });
  </script>
  <?php
}

add_action('save_post_shopido_story', function($post_id){
  if (!isset($_POST['shopido_story_media_nonce']) || !wp_verify_nonce($_POST['shopido_story_media_nonce'],'shopido_story_media')) return;
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if (!current_user_can('edit_post',$post_id)) return;
  $mid = isset($_POST['story_media_id']) ? intval($_POST['story_media_id']) : 0;
  if ($mid>0) update_post_meta($post_id,'_story_media_id',$mid); else delete_post_meta($post_id,'_story_media_id');
});
