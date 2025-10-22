<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * init.php (Plugin bootstrap)
 * - Loads textdomain on 'init'
 * - Includes CPT (stories)
 * - Registers Swiper + plugin assets (CSS/JS)
 * - Adds Elementor category 'shopido'
 * - Registers widgets from /includes/widgets
 * - Includes Ajax handlers
 *
 * Place: wp-content/plugins/shopido-elements/includes/init.php
 */

# -------------------------------------------------
# 0) Resolve base DIR/URL (plugin root)
# -------------------------------------------------
$SHOPIDO_BASE_DIR = plugin_dir_path( dirname(__FILE__) ); // /plugin-root/
$SHOPIDO_BASE_URL = plugin_dir_url(  dirname(__FILE__) ); // /plugin-root/ URL

# Optional constants (only if not defined elsewhere)
if ( ! defined('SHOPIDO_WP_DIR') ) define('SHOPIDO_WP_DIR', $SHOPIDO_BASE_DIR);
if ( ! defined('SHOPIDO_WP_URL') ) define('SHOPIDO_WP_URL', $SHOPIDO_BASE_URL);

# -------------------------------------------------
# 0.1) Textdomain (avoid JIT notice)
# -------------------------------------------------
add_action('init', function(){
    load_plugin_textdomain(
        'shopido-widgets-pack',
        false,
        dirname( plugin_basename( SHOPIDO_WP_DIR . 'shopido-widgets.php' ) ) . '/languages'
    );
});

# -------------------------------------------------
# 0.5) Include CPTs (Stories)
# -------------------------------------------------
$stories_cpt = SHOPIDO_WP_DIR . 'includes/stories-cpt.php';
if ( file_exists($stories_cpt) ) {
    require_once $stories_cpt;
}

# -------------------------------------------------
# 0.6) Ajax handlers
# -------------------------------------------------
$ajax_file = SHOPIDO_WP_DIR . 'includes/ajax/ajax-search.php';
if ( file_exists($ajax_file) ) {
    require_once $ajax_file;
}

# -------------------------------------------------
# 1) Assets (front): Swiper + plugin assets
# -------------------------------------------------
add_action('wp_enqueue_scripts', function(){

    // Swiper (register if missing)
    $swiper_ver = '8.4.5';
    if ( ! wp_style_is('swiper', 'registered') ) {
        wp_register_style ('swiper', "https://cdn.jsdelivr.net/npm/swiper@{$swiper_ver}/swiper-bundle.min.css", [], $swiper_ver);
    }
    if ( ! wp_script_is('swiper', 'registered') ) {
        wp_register_script('swiper', "https://cdn.jsdelivr.net/npm/swiper@{$swiper_ver}/swiper-bundle.min.js", [], $swiper_ver, true);
    }

    // CSS/JS directories
    $css_dir = SHOPIDO_WP_DIR . 'assets/css/';
    $js_dir  = SHOPIDO_WP_DIR . 'assets/js/';

    // Helper to register by file name
    $reg_css = function($handle, $file, $deps = []) use ($css_dir){
        $abs = $css_dir . $file;
        if ( file_exists($abs) ) {
            wp_register_style($handle, SHOPIDO_WP_URL . 'assets/css/' . $file, $deps, filemtime($abs));
        }
    };
    $reg_js = function($handle, $file, $deps = []) use ($js_dir){
        $abs = $js_dir . $file;
        if ( file_exists($abs) ) {
            wp_register_script($handle, SHOPIDO_WP_URL . 'assets/js/' . $file, $deps, filemtime($abs), true);
        }
    };

    // Stories (اینستاگرامی)
    $reg_css('shopido-story',        'story.css', ['swiper']);
    $reg_js ('shopido-story-player', 'story.js',  ['jquery']);

    // Read More (نمونه قدیمی)
    $reg_css('shopido-readmore',   'read-more.css');
    $reg_js ('shopido-readmore-js','read-more.js', ['jquery']);

    // Breadcrumb
    $reg_css('shopido-breadcrumb', 'breadcrumb.css');

    // Product Carousel
    $reg_css('shopido-product-carousel',    'product-carousel.css', ['swiper']);
    $reg_js ('shopido-product-carousel-js', 'product-carousel.js',  ['jquery','elementor-frontend','swiper']);

    // Tabbed Product Carousel
    $reg_css('shopido-tabbed-carousel',     'tabbed-carousel.css', ['swiper']);
    $reg_js ('shopido-tabbed-carousel-js',  'tabbed-carousel.js',  ['jquery','elementor-frontend','swiper']);

    // Ajax Search
    $reg_css('shopido-ajax-search', 'ajax-search.css');
    $reg_js ('shopido-ajax-search', 'ajax-search.js', ['jquery']);

    // Ticker Carousel (جدید)
    $reg_css('shopido-ticker-carousel', 'ticker-carousel.css');
    $reg_js ('shopido-ticker-carousel', 'ticker-carousel.js', ['jquery']);
    
    // Counter
    $reg_css('shopido-counter', 'counter.css');
    $reg_js ('shopido-counter', 'counter.js', ['jquery']);
});

# -------------------------------------------------
# 2) Elementor: add category
# -------------------------------------------------
add_action('elementor/elements/categories_registered', function($elements_manager){
    $elements_manager->add_category('shopido', [
        'title' => 'Shopido',
        'icon'  => 'fa fa-plug'
    ]);
});

# -------------------------------------------------
# 3) Elementor: register widgets
# -------------------------------------------------
add_action('elementor/widgets/register', function($widgets_manager){

    $widgets_dir = SHOPIDO_WP_DIR . 'includes/widgets/';

    // فایل‌ها را هر کدام اگر وجود داشتند لود کن
    $files = [
        'class-shopido-read-more.php',
        'class-shopido-breadcrumb.php',
        'class-shopido-product-carousel.php',
        'class-shopido-tabbed-product-carousel.php',
        'class-stories.php',                 // Stories (اینستاگرامی)
        'class-ajax-search.php',             // Ajax Search
        'class-shopido-ticker-carousel.php', // Ticker Carousel (جدید)
        'class-shopido-counter.php'
    ];

    foreach ($files as $file) {
        $path = $widgets_dir . $file;
        if ( file_exists($path) ) require_once $path;
    }

    // ثبت کلاس‌ها اگر موجودند
    if ( class_exists('\Shopido_Read_More') ) {
        $widgets_manager->register( new \Shopido_Read_More() );
    }
    if ( class_exists('\Shopido_Breadcrumb') ) {
        $widgets_manager->register( new \Shopido_Breadcrumb() );
    }
    if ( class_exists('\Shopido_Product_Carousel') ) {
        $widgets_manager->register( new \Shopido_Product_Carousel() );
    }
    if ( class_exists('\Shopido_Tabbed_Product_Carousel') ) {
        $widgets_manager->register( new \Shopido_Tabbed_Product_Carousel() );
    }
    if ( class_exists('\SEA\Widgets\Stories') ) {
        $widgets_manager->register( new \SEA\Widgets\Stories() );
    }
    if ( class_exists('\Shopido_Ajax_Search') ) {
        $widgets_manager->register( new \Shopido_Ajax_Search() );
    }
    if ( class_exists('\Shopido_Ticker_Carousel') ) {
        $widgets_manager->register( new \Shopido_Ticker_Carousel() );
    }
    if ( class_exists('\Shopido_Counter') ) {
        $widgets_manager->register( new \Shopido_Counter() );
    }
});
