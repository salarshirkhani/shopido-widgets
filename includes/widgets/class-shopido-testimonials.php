<?php
if ( ! defined('ABSPATH') ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class Shopido_Testimonials extends Widget_Base {

  public function get_name() { return 'shopido_testimonials'; }
  public function get_title() { return esc_html__('Shopido Testimonials', 'shopido-widgets-pack'); }
  public function get_icon() { return 'eicon-testimonial-carousel'; }
  public function get_categories() { return [ 'shopido' ]; }

  public function get_style_depends() { return [ 'shopido-testimonials' ]; }
  public function get_script_depends() { return [ 'shopido-testimonials' ]; }

  protected function register_controls() {

    $this->start_controls_section('content', [
      'label' => esc_html__('Content', 'shopido-widgets-pack'),
      'tab' => Controls_Manager::TAB_CONTENT,
    ]);

    $this->add_control('post_type', [
      'label' => esc_html__('Post Type', 'shopido-widgets-pack'),
      'type' => Controls_Manager::TEXT,
      'default' => 'shopido_testimonial', // بعداً وقتی CPT ساختی همین بمونه
      'description' => 'نام CPT نظرات (مثلاً shopido_testimonial)',
    ]);

    $this->add_control('initial_count', [
      'label' => esc_html__('Items on first page', 'shopido-widgets-pack'),
      'type' => Controls_Manager::NUMBER,
      'min' => 1,
      'default' => 30,
    ]);

    $this->add_control('next_count', [
      'label' => esc_html__('Items on next pages', 'shopido-widgets-pack'),
      'type' => Controls_Manager::NUMBER,
      'min' => 1,
      'default' => 12,
    ]);

    $this->add_control('service_name', [
      'label' => esc_html__('Service name (Schema)', 'shopido-widgets-pack'),
      'type' => Controls_Manager::TEXT,
      'default' => 'Hair Transplant',
      'description' => 'برای schema.org itemReviewed',
    ]);

    $this->end_controls_section();

    // ----------------- Style: Card -----------------
    $this->start_controls_section('style_card', [
      'label' => esc_html__('Card', 'shopido-widgets-pack'),
      'tab' => Controls_Manager::TAB_STYLE,
    ]);

    $this->add_control('bg_wrapper', [
      'label' => esc_html__('Wrapper background', 'shopido-widgets-pack'),
      'type' => Controls_Manager::COLOR,
      'default' => '#f2f0ed',
      'selectors' => [
        '{{WRAPPER}} .shpd-t-wrap' => 'background: {{VALUE}};',
      ],
    ]);

    $this->add_control('bg_card', [
      'label' => esc_html__('Card background', 'shopido-widgets-pack'),
      'type' => Controls_Manager::COLOR,
      'default' => '#ffffff',
      'selectors' => [
        '{{WRAPPER}} .shpd-t-card' => 'background: {{VALUE}};',
      ],
    ]);

    $this->add_responsive_control('card_radius', [
      'label' => esc_html__('Card radius', 'shopido-widgets-pack'),
      'type' => Controls_Manager::SLIDER,
      'size_units' => [ 'px' ],
      'default' => [ 'size' => 24 ],
      'selectors' => [
        '{{WRAPPER}} .shpd-t-card' => 'border-radius: {{SIZE}}px;',
      ],
    ]);

    $this->add_responsive_control('card_padding', [
      'label' => esc_html__('Card padding', 'shopido-widgets-pack'),
      'type' => Controls_Manager::DIMENSIONS,
      'size_units' => ['px'],
      'default' => ['top'=>18,'right'=>18,'bottom'=>18,'left'=>18,'unit'=>'px'],
      'selectors' => [
        '{{WRAPPER}} .shpd-t-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
      ],
    ]);

    $this->end_controls_section();

    // ----------------- Style: Typography -----------------
    $this->start_controls_section('style_typo', [
      'label' => esc_html__('Typography', 'shopido-widgets-pack'),
      'tab' => Controls_Manager::TAB_STYLE,
    ]);

    $this->add_group_control(Group_Control_Typography::get_type(), [
      'name' => 'typo_title',
      'label' => 'Title (Name | Age)',
      'selector' => '{{WRAPPER}} .shpd-t-title',
    ]);

    $this->add_control('color_title', [
      'label' => esc_html__('Title color', 'shopido-widgets-pack'),
      'type' => Controls_Manager::COLOR,
      'default' => '#2b2b2b',
      'selectors' => [
        '{{WRAPPER}} .shpd-t-title' => 'color: {{VALUE}};',
      ],
    ]);

    $this->add_group_control(Group_Control_Typography::get_type(), [
      'name' => 'typo_meta',
      'label' => 'Meta rows',
      'selector' => '{{WRAPPER}} .shpd-t-meta',
    ]);

    $this->add_control('color_meta', [
      'label' => esc_html__('Meta color', 'shopido-widgets-pack'),
      'type' => Controls_Manager::COLOR,
      'default' => '#6b6b6b',
      'selectors' => [
        '{{WRAPPER}} .shpd-t-meta' => 'color: {{VALUE}};',
      ],
    ]);

    $this->add_group_control(Group_Control_Typography::get_type(), [
      'name' => 'typo_link',
      'label' => 'Video link',
      'selector' => '{{WRAPPER}} .shpd-t-video',
    ]);

    $this->add_control('color_link', [
      'label' => esc_html__('Video link color', 'shopido-widgets-pack'),
      'type' => Controls_Manager::COLOR,
      'default' => '#8a6b2f',
      'selectors' => [
        '{{WRAPPER}} .shpd-t-video' => 'color: {{VALUE}};',
      ],
    ]);

    $this->end_controls_section();
  }

  // -------- helpers --------
  private function get_current_page() {
    $paged = (int) get_query_var('paged');
    if ( $paged < 1 ) $paged = 1;

    // fallback برای صفحات غیرآرشیو
    if ( $paged === 1 && isset($_GET['tpage']) ) {
      $tp = (int) $_GET['tpage'];
      if ( $tp > 1 ) $paged = $tp;
    }
    return max(1, $paged);
  }

  private function get_paging_numbers($total, $initial, $next) {
    if ( $total <= $initial ) return 1;
    return 1 + (int) ceil( ($total - $initial) / $next );
  }

  private function build_page_url($page) {
    $page = max(1, (int)$page);

    // اگر آرشیو هستیم: /page/2/ استاندارد
    if ( is_archive() || is_home() ) {
      return get_pagenum_link($page);
    }

    // غیرآرشیو: با پارامتر tpage
    $base = remove_query_arg('tpage');
    return add_query_arg('tpage', $page, $base);
  }

  private function calc_limit_offset($page, $initial, $next) {
    if ( $page <= 1 ) {
      return [ $initial, 0 ];
    }
    $offset = $initial + ($page - 2) * $next;
    return [ $next, $offset ];
  }

  protected function render() {
    $s = $this->get_settings_for_display();

    $post_type = sanitize_key($s['post_type']);
    $initial   = max(1, (int)$s['initial_count']);
    $next      = max(1, (int)$s['next_count']);
    $service   = sanitize_text_field($s['service_name']);

    $page = $this->get_current_page();

    // total
    $total_q = new \WP_Query([
      'post_type'      => $post_type,
      'post_status'    => 'publish',
      'posts_per_page' => 1,
      'fields'         => 'ids',
      'no_found_rows'  => false,
    ]);
    $total = (int) $total_q->found_posts;
    wp_reset_postdata();

    $max_pages = $this->get_paging_numbers($total, $initial, $next);
    if ( $page > $max_pages ) $page = $max_pages;

    [ $limit, $offset ] = $this->calc_limit_offset($page, $initial, $next);

    $q = new \WP_Query([
      'post_type'      => $post_type,
      'post_status'    => 'publish',
      'posts_per_page' => $limit,
      'offset'         => $offset,
      'orderby'        => 'date',
      'order'          => 'DESC',
      'no_found_rows'  => true, // ما total رو جدا گرفتیم
    ]);

    $wid = $this->get_id();

    // برای JS: دیتاها
    $data = [
      'widgetId'   => $wid,
      'page'       => $page,
      'maxPages'   => $max_pages,
      'initial'    => $initial,
      'next'       => $next,
      'baseUrl1'   => $this->build_page_url(1),
    ];

    echo '<section class="shpd-t-wrap" data-shpd-testimonials=\''.esc_attr( wp_json_encode($data) ).'\'>';

    // grid
    echo '<div class="shpd-t-grid" id="shpd-t-grid-'.$wid.'">';

    if ( $q->have_posts() ) :
      while ( $q->have_posts() ) : $q->the_post();

        // ---- meta keys (بعداً مطابق متاباکس خودت دقیق کن) ----
        $name   = get_post_meta(get_the_ID(), 'shpd_name', true);
        $age    = get_post_meta(get_the_ID(), 'shpd_age', true);
        $area   = get_post_meta(get_the_ID(), 'shpd_area', true);
        $method = get_post_meta(get_the_ID(), 'shpd_method', true);
        $grafts = get_post_meta(get_the_ID(), 'shpd_grafts', true);

        $img_id      = get_post_thumbnail_id(get_the_ID()); // عکس اصلی (سمت چپ کارت)
        $avatar_id   = (int) get_post_meta(get_the_ID(), 'shpd_avatar_id', true); // عکس کوچیک بالا
        $video_url   = esc_url_raw( get_post_meta(get_the_ID(), 'shpd_video_url', true) );

        $title = trim($name) . ( $age ? ' | ' . trim($age) . ' سال' : '' );

        echo '<article class="shpd-t-card" itemscope itemtype="https://schema.org/Review">';
          // schema minimal
          echo '<meta itemprop="itemReviewed" content="'.esc_attr($service).'">';
          if ($name) {
            echo '<span itemprop="author" itemscope itemtype="https://schema.org/Person" class="shpd-sr-only">';
              echo '<meta itemprop="name" content="'.esc_attr($name).'">';
            echo '</span>';
          }

          echo '<div class="shpd-t-media">';

            if ( $img_id ) {
              echo wp_get_attachment_image($img_id, 'large', false, [
                'class' => 'shpd-t-img',
                'alt'   => esc_attr($title),
                'loading' => 'lazy',
              ]);
            } else {
              echo '<div class="shpd-t-img shpd-t-img--ph"></div>';
            }

            if ( $avatar_id ) {
              echo '<div class="shpd-t-avatar">';
                echo wp_get_attachment_image($avatar_id, 'thumbnail', false, [
                  'class' => 'shpd-t-avatar-img',
                  'alt'   => esc_attr($name ?: 'avatar'),
                  'loading' => 'lazy',
                ]);
              echo '</div>';
            }

          echo '</div>'; // media

          echo '<div class="shpd-t-body">';
            echo '<h3 class="shpd-t-title">'.esc_html($title).'</h3>';

            echo '<ul class="shpd-t-meta">';
              if ($area)   echo '<li><span class="shpd-t-dot">•</span> <span class="shpd-t-k">ناحیه کاشت:</span> <span class="shpd-t-v">'.esc_html($area).'</span></li>';
              if ($method) echo '<li><span class="shpd-t-dot">•</span> <span class="shpd-t-k">روش کاشت:</span> <span class="shpd-t-v">'.esc_html($method).'</span></li>';
              if ($grafts) echo '<li><span class="shpd-t-dot">•</span> <span class="shpd-t-k">تعداد گرافت:</span> <span class="shpd-t-v">'.esc_html($grafts).'</span></li>';
            echo '</ul>';

            if ( $video_url ) {
              echo '<button type="button" class="shpd-t-video" data-video="'.esc_attr($video_url).'">';
                echo '<span class="shpd-t-play" aria-hidden="true">▶</span> ';
                echo 'مشاهده ویدیو رضایت کاشت';
              echo '</button>';
            }

          echo '</div>'; // body
        echo '</article>';

      endwhile;
      wp_reset_postdata();
    endif;

    echo '</div>'; // grid

    // pagination (real links)
    $prev_page = max(1, $page - 1);
    $next_page = min($max_pages, $page + 1);

    $prev_url = $this->build_page_url($prev_page);
    $next_url = $this->build_page_url($next_page);

    $prev_disabled = ($page <= 1) ? ' aria-disabled="true" data-disabled="1"' : '';
    $next_disabled = ($page >= $max_pages) ? ' aria-disabled="true" data-disabled="1"' : '';

    echo '<nav class="shpd-t-nav" aria-label="Testimonials Pagination">';
      echo '<a class="shpd-t-btn shpd-t-prev" href="'.esc_url($prev_url).'"'.$prev_disabled.'>‹</a>';

      // dots
      echo '<div class="shpd-t-dots" aria-hidden="true">';
        for ($i=1; $i<=min($max_pages, 5); $i++){
          $active = ($i === $page) ? ' is-active' : '';
          echo '<span class="shpd-t-dotp'.$active.'"></span>';
        }
      echo '</div>';

      echo '<a class="shpd-t-btn shpd-t-next" href="'.esc_url($next_url).'"'.$next_disabled.'>›</a>';
    echo '</nav>';

    // loader + modal
    echo '<div class="shpd-t-loader" hidden><span></span></div>';

    echo '<div class="shpd-t-modal" hidden role="dialog" aria-modal="true">';
      echo '<div class="shpd-t-modal-backdrop" data-close="1"></div>';
      echo '<div class="shpd-t-modal-box" role="document">';
        echo '<button type="button" class="shpd-t-modal-close" data-close="1" aria-label="Close">×</button>';
        echo '<div class="shpd-t-modal-content"></div>';
      echo '</div>';
    echo '</div>';

    echo '</section>';
  }
}
