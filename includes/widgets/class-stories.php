<?php
namespace SEA\Widgets;

if (!defined('ABSPATH')) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use WP_Query;

class Stories extends Widget_Base {
  public function get_name(){ return 'shopido_stories'; }
  public function get_title(){ return 'استوری‌ها (Shopido)'; }
  public function get_icon(){ return 'eicon-slider-push'; }
  public function get_categories(){ return ['shopido']; }

  public function get_style_depends(){ return ['shopido-story']; }
  public function get_script_depends(){ return ['shopido-story']; }

  protected function register_controls(){
    $this->start_controls_section('content',['label'=>'محتوا','tab'=>Controls_Manager::TAB_CONTENT]);
    $this->add_control('count',['label'=>'تعداد','type'=>Controls_Manager::NUMBER,'default'=>12,'min'=>1,'max'=>50]);
    $this->add_control('order',['label'=>'مرتب‌سازی','type'=>Controls_Manager::SELECT,'default'=>'DESC','options'=>['DESC'=>'جدیدترین','ASC'=>'قدیمی‌تر']]);
    $this->end_controls_section();
  }

  protected function render(){
    $s = $this->get_settings_for_display();
    $q = new WP_Query([
      'post_type'=>'shopido_story','posts_per_page'=>intval($s['count']??12),
      'orderby'=>'date','order'=>($s['order']??'DESC'),'no_found_rows'=>true
    ]);
    echo '<div class="shopido-stories">';
    if($q->have_posts()){
      while($q->have_posts()){ $q->the_post();
        $pid = get_the_ID();
        $title = get_the_title() ?: 'استوری';
        $avatar = get_the_post_thumbnail_url($pid,'thumbnail') ?: wc_placeholder_img_src('thumbnail');

        $mid = (int) get_post_meta($pid,'_story_media_id',true);
        $media_url = $mid ? wp_get_attachment_url($mid) : '';
        $mime = $mid ? get_post_mime_type($mid) : '';
        $type = (strpos((string)$mime,'video/')===0) ? 'video' : 'image';

        // اگر مدیا انتخاب نشده بود، از آواتار استفاده کن
        if(!$media_url){ $media_url = $avatar; $type='image'; }

        printf(
          '<a href="#" class="shopido-story-item" data-title="%s" data-avatar="%s" data-media="%s" data-type="%s">
            <span class="shopido-story-avatar"><img src="%s" alt="%s"></span>
            <div class="shopido-story-title">%s</div>
          </a>',
          esc_attr($title), esc_url($avatar), esc_url($media_url), esc_attr($type),
          esc_url($avatar), esc_attr($title), esc_html($title)
        );
      }
      wp_reset_postdata();
    } else {
      echo '<em>هنوز استوری‌ای ثبت نشده.</em>';
    }
    echo '</div>';

    // ویوِر (یک‌بار)
    static $once=false;
    if(!$once){ $once=true;
      echo '<div id="shopido-story-viewer" aria-hidden="true"></div>';
    }
  }
}
