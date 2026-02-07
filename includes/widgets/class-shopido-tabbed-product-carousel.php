<?php
if ( ! defined('ABSPATH') ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Shopido_Tabbed_Product_Carousel extends Widget_Base {
  public function get_name(){ return 'shopido-tabbed-product-carousel'; }
  public function get_title(){ return 'Shopido – Tabbed Product Carousel'; }
  public function get_icon(){ return 'eicon-slider-push'; }
  public function get_categories(){ return ['shopido']; }

  public function get_style_depends(){
    return ['swiper','shopido-tabbed-carousel'];
  }

  public function get_script_depends(){
    return ['elementor-frontend','swiper','shopido-tabbed-carousel'];
  }

  protected function register_controls(){
    $this->start_controls_section('sec_titles', ['label'=>'عنوان تب‌ها']);
    $this->add_control('tab_all_title', ['label'=>'تب ۱ (همه)','type'=>Controls_Manager::TEXT,'default'=>'همه']);
    $this->add_control('tab_feat_title',['label'=>'تب ۲ (ویژه زمان‌دار)','type'=>Controls_Manager::TEXT,'default'=>'ویژه']);
    $this->add_control('tab_sale_title',['label'=>'تب ۳ (تخفیف‌دار)','type'=>Controls_Manager::TEXT,'default'=>'تخفیف‌ها']);
    $this->end_controls_section();

    $this->start_controls_section('sec_query', ['label'=>'تنظیمات Query (همه/تخفیف)']);
    $this->add_control('per_page', ['label'=>'تعداد هر تب','type'=>Controls_Manager::NUMBER,'default'=>8,'min'=>1,'max'=>48]);
    $this->add_control('only_instock', ['label'=>'فقط موجودها','type'=>Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes']);
    $this->end_controls_section();

    $this->start_controls_section('sec_carousel', ['label'=>'کاروسل']);
    $this->add_control('slides_desktop', ['label'=>'اسلاید/دسکتاپ','type'=>Controls_Manager::NUMBER,'default'=>4,'min'=>1,'max'=>6]);
    $this->add_control('slides_tablet',  ['label'=>'اسلاید/تبلت','type'=>Controls_Manager::NUMBER,'default'=>3,'min'=>1,'max'=>6]);
    $this->add_control('slides_mobile',  ['label'=>'اسلاید/موبایل','type'=>Controls_Manager::NUMBER,'default'=>1,'min'=>1,'max'=>3]);
    $this->add_control('space_between',  ['label'=>'فاصله','type'=>Controls_Manager::NUMBER,'default'=>16,'min'=>0,'max'=>40]);
    $this->add_control('loop',           ['label'=>'لوپ','type'=>Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes']);
    $this->add_control('autoplay',       ['label'=>'اتوپلی','type'=>Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes']);
    $this->add_control('autoplay_delay', ['label'=>'تاخیر (ms)','type'=>Controls_Manager::NUMBER,'default'=>3500,'min'=>1000,'max'=>10000,'condition'=>['autoplay'=>'yes']]);
    $this->add_control('speed',          ['label'=>'سرعت (ms)','type'=>Controls_Manager::NUMBER,'default'=>500,'min'=>100,'max'=>3000]);
    $this->add_control('show_arrows',    ['label'=>'فلش‌ها','type'=>Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes']);
    $this->add_control('show_dots',      ['label'=>'نقطه‌ها','type'=>Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'no']);
    $this->end_controls_section();
  }

  protected function query_all($s){
    $args = [
      'post_type'=>'product','post_status'=>'publish',
      'posts_per_page'=>intval($s['per_page']??8),
      'orderby'=>'date','order'=>'DESC',
      'meta_query'=>[],'tax_query'=>[],
    ];
    if ( 'yes' === ($s['only_instock']??'') ) {
      $args['meta_query'][] = ['key'=>'_stock_status','value'=>'instock'];
    }
    return new WP_Query($args);
  }

  protected function query_sale($s){
    $args = [
      'post_type'=>'product','post_status'=>'publish',
      'posts_per_page'=>intval($s['per_page']??8),
      'post__in'=>wc_get_product_ids_on_sale(),
      'orderby'=>'date','order'=>'DESC',
      'meta_query'=>[],'tax_query'=>[],
    ];
    if ( 'yes' === ($s['only_instock']??'') ) {
      $args['meta_query'][] = ['key'=>'_stock_status','value'=>'instock'];
    }
    return new WP_Query($args);
  }

  protected function query_featured_timed($s){
    $now = current_time('timestamp'); // وردپرس/لوکال
    $args = [
      'post_type'=>'product','post_status'=>'publish',
      'posts_per_page'=>intval($s['per_page']??8),
      'orderby'=>'date','order'=>'DESC',
      'meta_query'=>[
        ['key'=>'_shopido_featured_enabled','value'=>'yes'],
        ['key'=>'_shopido_featured_start','value'=>$now,'compare'=>'<=','type'=>'NUMERIC'],
        ['key'=>'_shopido_featured_end','value'=>$now,'compare'=>'>=','type'=>'NUMERIC'],
      ],
      'tax_query'=>[],
    ];
    if ( 'yes' === ($s['only_instock']??'') ) {
      $args['meta_query'][] = ['key'=>'_stock_status','value'=>'instock'];
    }
    return new WP_Query($args);
  }

  protected function product_card($product, $show_countdown=false){
    if (!$product instanceof WC_Product) return;
    $end_ts = $show_countdown ? intval(get_post_meta($product->get_id(), '_shopido_featured_end', true)) : 0;
    ?>
    <div class="shopido-card">
      <a class="shopido-card__thumb" href="<?php echo esc_url(get_permalink($product->get_id())); ?>">
        <?php echo $product->get_image('woocommerce_thumbnail', ['class'=>'shopido-card__img']); ?>
      </a>
      <div class="shopido-card__body">
        <h3 class="shopido-card__title">
          <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>"><?php echo esc_html($product->get_name()); ?></a>
        </h3>
        <div class="shopido-card__price"><?php echo wp_kses_post($product->get_price_html()); ?></div>

        <?php if ($show_countdown && $end_ts): ?>
          <div class="shopido-card__countdown" data-countdown="<?php echo esc_attr($end_ts); ?>">
            <span class="d">00</span><b>:</b><span class="h">00</span><b>:</b><span class="m">00</span><b>:</b><span class="s">00</span>
          </div>
        <?php endif; ?>

        <div class="shopido-card__actions">
          <?php if($product->is_purchasable()){
            $args_btn=[
              'class'=>'shopido-card__btn add_to_cart_button ajax_add_to_cart',
              'attributes'=>[
                'data-product_id'=>$product->get_id(),
                'data-product_sku'=>$product->get_sku(),
                'aria-label'=>$product->add_to_cart_description(),
                'rel'=>'nofollow',
              ],
            ];
            echo apply_filters('woocommerce_loop_add_to_cart_link',
              sprintf('<a href="%s" data-quantity="1" class="%s" %s>%s</a>',
                esc_url($product->add_to_cart_url()),
                esc_attr($args_btn['class']),
                wc_implode_html_attributes($args_btn['attributes']),
                esc_html__('افزودن به سبد خرید','shopido')
              ), $product, $args_btn);
          } ?>
        </div>
      </div>
    </div>
    <?php
  }

  protected function render(){
    if ( ! class_exists('WooCommerce') ) return;

    $s   = $this->get_settings_for_display();
    $uid = 'shopido-tpc-'. $this->get_id();

    // Swiper opts اشتراکی
    $opts = [
      'slidesPerView'=>(int)$s['slides_desktop'],
      'spaceBetween'=>(int)$s['space_between'],
      'loop'=>('yes'===$s['loop']),
      'speed'=>(int)$s['speed'],
      'autoplay'=>('yes'===$s['autoplay'])?['delay'=>(int)$s['autoplay_delay']]:false,
      'breakpoints'=>[
        0=>['slidesPerView'=>(int)$s['slides_mobile']],
        640=>['slidesPerView'=>(int)$s['slides_tablet']],
        1024=>['slidesPerView'=>(int)$s['slides_desktop']],
      ],
      'navigation'=>('yes'===$s['show_arrows']),
      'pagination'=>('yes'===$s['show_dots']),
    ];

    // کوئری‌ها
    $q_all  = $this->query_all($s);
    $q_feat = $this->query_featured_timed($s);
    $q_sale = $this->query_sale($s);
    ?>

    <div id="<?php echo esc_attr($uid); ?>" class="shopido-tpc" dir="rtl">
      <!-- Tabs -->
      <div class="shopido-tpc__tabs" role="tablist">
        <button class="shopido-tpc__tab is-active" data-tab="all"  role="tab" aria-selected="true"><?php echo esc_html($s['tab_all_title'] ?? 'همه'); ?></button>
        <button class="shopido-tpc__tab"         data-tab="feat" role="tab" aria-selected="false"><?php echo esc_html($s['tab_feat_title'] ?? 'ویژه'); ?></button>
        <button class="shopido-tpc__tab"         data-tab="sale" role="tab" aria-selected="false"><?php echo esc_html($s['tab_sale_title'] ?? 'تخفیف‌ها'); ?></button>
      </div>

      <!-- Panels -->
      <div class="shopido-tpc__panel is-active" data-panel="all">
        <div class="shopido-tpc__wrap swiper" data-shopido-swiper='<?php echo wp_json_encode($opts); ?>'>
          <div class="swiper-wrapper">
          <?php if($q_all->have_posts()): while($q_all->have_posts()): $q_all->the_post(); $p=wc_get_product(get_the_ID()); ?>
            <div class="swiper-slide"><?php $this->product_card($p,false); ?></div>
          <?php endwhile; else: ?>
            <div class="swiper-slide"><p>محصولی یافت نشد.</p></div>
          <?php endif; wp_reset_postdata(); ?>
          </div>
        </div>
        <?php if('yes'===$s['show_arrows']): ?>
          <button class="shopido-tpc__arrow prev" type="button" aria-label="قبلی"></button>
          <button class="shopido-tpc__arrow next" type="button" aria-label="بعدی"></button>
        <?php endif; ?>
        <?php if('yes'===$s['show_dots']): ?><div class="shopido-tpc__dots"></div><?php endif; ?>
      </div>

      <div class="shopido-tpc__panel" data-panel="feat">
        <div class="shopido-tpc__wrap swiper" data-shopido-swiper='<?php echo wp_json_encode($opts); ?>'>
          <div class="swiper-wrapper">
          <?php if($q_feat->have_posts()): while($q_feat->have_posts()): $q_feat->the_post(); $p=wc_get_product(get_the_ID()); ?>
            <div class="swiper-slide"><?php $this->product_card($p,true); ?></div>
          <?php endwhile; else: ?>
            <div class="swiper-slide"><p>فعلاً آیتم ویژه در بازه‌ی زمانی فعال موجود نیست.</p></div>
          <?php endif; wp_reset_postdata(); ?>
          </div>
        </div>
        <?php if('yes'===$s['show_arrows']): ?>
          <button class="shopido-tpc__arrow prev" type="button" aria-label="قبلی"></button>
          <button class="shopido-tpc__arrow next" type="button" aria-label="بعدی"></button>
        <?php endif; ?>
        <?php if('yes'===$s['show_dots']): ?><div class="shopido-tpc__dots"></div><?php endif; ?>
      </div>

      <div class="shopido-tpc__panel" data-panel="sale">
        <div class="shopido-tpc__wrap swiper" data-shopido-swiper='<?php echo wp_json_encode($opts); ?>'>
          <div class="swiper-wrapper">
          <?php if($q_sale->have_posts()): while($q_sale->have_posts()): $q_sale->the_post(); $p=wc_get_product(get_the_ID()); ?>
            <div class="swiper-slide"><?php $this->product_card($p,false); ?></div>
          <?php endwhile; else: ?>
            <div class="swiper-slide"><p>محصول تخفیف‌دار یافت نشد.</p></div>
          <?php endif; wp_reset_postdata(); ?>
          </div>
        </div>
        <?php if('yes'===$s['show_arrows']): ?>
          <button class="shopido-tpc__arrow prev" type="button" aria-label="قبلی"></button>
          <button class="shopido-tpc__arrow next" type="button" aria-label="بعدی"></button>
        <?php endif; ?>
        <?php if('yes'===$s['show_dots']): ?><div class="shopido-tpc__dots"></div><?php endif; ?>
      </div>
    </div>
    <?php
  }
}
