<?php
if ( ! defined('ABSPATH') ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Shopido_Product_Carousel extends Widget_Base {
  public function get_name() { return 'shopido-product-carousel'; }
  public function get_title() { return 'Shopido – Product Carousel'; }
  public function get_icon() { return 'eicon-products'; }
  public function get_categories() { return ['shopido']; }

  // FIX: اطمینان از بارگذاری CSS خود Swiper
  public function get_style_depends(){
    return [
      'swiper',                  // CSS Swiper
      'shopido-product-carousel' // CSS خودت
    ];
  }

  // FIX: اطمینان از بارگذاری JS Swiper و المنتور
  public function get_script_depends(){
    return [
      'elementor-frontend',       // برای utils.swiper
      'swiper',                   // JS Swiper (هندل استاندارد المنتور/وردپرس)
      'shopido-product-carousel'  // JS خودت
    ];
  }

  protected function register_controls(){
    $this->start_controls_section('sec_query', ['label'=>'منبع محصولات']);
    $this->add_control('source', [
      'label'=>'نوع منبع','type'=>Controls_Manager::SELECT,'default'=>'recent',
      'options'=>[
        'recent'=>'جدیدترین','sale'=>'تخفیف‌دار','featured'=>'ویژه',
        'best_selling'=>'پرفروش‌ترین','top_rated'=>'بالاترین امتیاز',
        'by_cat'=>'بر اساس دسته','ids'=>'شناسه‌ها (دستی)'
      ]
    ]);
    $this->add_control('product_cat', [
      'label'=>'دسته‌ها','type'=>Controls_Manager::SELECT2,'multiple'=>true,'label_block'=>true,
      'options'=>$this->cats(), 'condition'=>['source'=>'by_cat']
    ]);
    $this->add_control('ids', [
      'label'=>'IDs (کاما جدا)','type'=>Controls_Manager::TEXT,'placeholder'=>'12,34,56',
      'condition'=>['source'=>'ids']
    ]);
    $this->add_control('only_instock', [
      'label'=>'فقط موجودها','type'=>Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes'
    ]);
    $this->add_control('per_page', [
      'label'=>'تعداد','type'=>Controls_Manager::NUMBER,'default'=>8,'min'=>1,'max'=>48
    ]);
    $this->add_control('orderby', [
      'label'=>'مرتب‌سازی','type'=>Controls_Manager::SELECT,'default'=>'date',
      'options'=>['date'=>'تاریخ','title'=>'عنوان','price'=>'قیمت','popularity'=>'محبوبیت','rating'=>'امتیاز','rand'=>'تصادفی'],
      'condition'=>['source!'=>['best_selling','top_rated','sale','featured']]
    ]);
    $this->add_control('order', [
      'label'=>'جهت','type'=>Controls_Manager::SELECT,'default'=>'DESC',
      'options'=>['DESC'=>'نزولی','ASC'=>'صعودی'],
      'condition'=>['source!'=>['best_selling','top_rated','sale','featured']]
    ]);
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

  private function cats(){
    $out=[]; $terms=get_terms(['taxonomy'=>'product_cat','hide_empty'=>false,'number'=>200]);
    if(is_wp_error($terms)) return $out; foreach($terms as $t){ $out[$t->slug]=$t->name; } return $out;
  }

  protected function render(){
    if ( ! class_exists('WooCommerce') ) return;
    $s   = $this->get_settings_for_display();
    $uid = 'shopido-pc-'. $this->get_id();

    // --- Query
    $args = [
      'post_type'=>'product','post_status'=>'publish',
      'posts_per_page'=>intval($s['per_page']),
      'orderby'=>$s['orderby']??'date','order'=>$s['order']??'DESC',
      'meta_query'=>[],'tax_query'=>[]
    ];
    if ( 'yes' === ($s['only_instock']??'') ) {
      $args['meta_query'][] = ['key'=>'_stock_status','value'=>'instock'];
    }
    switch($s['source']){
      case 'sale': $args['post__in']=wc_get_product_ids_on_sale(); break;
      case 'featured': $args['tax_query'][]=['taxonomy'=>'product_visibility','field'=>'name','terms'=>'featured']; break;
      case 'best_selling': $args['meta_key']='total_sales'; $args['orderby']='meta_value_num'; $args['order']='DESC'; break;
      case 'top_rated': add_filter('posts_clauses',['WC_Shortcodes','order_by_rating_post_clauses']); break;
      case 'by_cat': if(!empty($s['product_cat'])) $args['tax_query'][]=['taxonomy'=>'product_cat','field'=>'slug','terms'=>(array)$s['product_cat']]; break;
      case 'ids': $ids=array_filter(array_map('intval',explode(',',$s['ids']??''))); if($ids) $args['post__in']=$ids; break;
    }
    $q = new WP_Query($args);

    // --- Swiper options (در data-* می‌ریزیم)
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
    ?>

    <div id="<?php echo esc_attr($uid); ?>" class="shopido-pc">
      <?php if('yes'===$s['show_arrows']): ?>
        <button class="shopido-pc__arrow shopido-pc__prev" type="button" aria-label="قبلی"></button>
        <button class="shopido-pc__arrow shopido-pc__next" type="button" aria-label="بعدی"></button>
      <?php endif; ?>
    
      <div class="shopido-pc__swiper swiper" data-shopido-swiper='<?php echo wp_json_encode($opts); ?>' dir="rtl">
        <div class="shopido-pc__list swiper-wrapper"><!-- ایزوله؛ دیگه ul.products نیست -->
    
          <?php if($q->have_posts()): while($q->have_posts()): $q->the_post(); global $product; ?>
            <div class="shopido-pc__item swiper-slide"><!-- ایزوله؛ دیگه li.product نیست -->
              <div class="shopido-card">
                <a class="shopido-card__thumb" href="<?php the_permalink(); ?>">
                  <?php if (has_post_thumbnail()) {
                    the_post_thumbnail('woocommerce_thumbnail',['class'=>'shopido-card__img']);
                  } else {
                    echo wc_placeholder_img('woocommerce_thumbnail',['class'=>'shopido-card__img']);
                  } ?>
                </a>
    
                <div class="shopido-card__body">
                  <h3 class="shopido-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                  <div class="shopido-card__price"><?php echo $product ? wp_kses_post($product->get_price_html()) : ''; ?></div>
    
                  <div class="shopido-card__actions">
                    <?php if($product && $product->is_purchasable()){
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
            </div>
          <?php endwhile; wp_reset_postdata(); else: ?>
            <div class="shopido-pc__item swiper-slide"><p><?php esc_html_e('محصولی یافت نشد.','shopido'); ?></p></div>
          <?php endif; ?>
    
        </div>
      </div>
    
      <?php if('yes'===$s['show_dots']): ?>
        <div class="shopido-pc__dots"></div>
      <?php endif; ?>
    </div>

    <?php
    if('top_rated'===$s['source']) remove_filter('posts_clauses',['WC_Shortcodes','order_by_rating_post_clauses']);
  }
}
