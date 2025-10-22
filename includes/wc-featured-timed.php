<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * فیلدهای «ویژه زمان‌دار» در ادیت محصول ووکامرس
 * متاها:
 * _shopido_featured_enabled  = yes/no
 * _shopido_featured_start    = Y-m-d H:i (local)
 * _shopido_featured_end      = Y-m-d H:i (local)
 */
add_action('woocommerce_product_options_pricing', function(){
    echo '<div class="options_group">';

    woocommerce_wp_checkbox([
        'id'          => '_shopido_featured_enabled',
        'label'       => 'ویژه زمان‌دار',
        'description' => 'با فعال کردن، این محصول در تب «ویژه» نمایش داده می‌شود (بین تاریخ شروع/پایان).',
    ]);

    woocommerce_wp_text_input([
        'id'          => '_shopido_featured_start',
        'label'       => 'تاریخ شروع ویژه (local)',
        'placeholder' => 'YYYY-MM-DD HH:MM',
        'desc_tip'    => true,
        'description' => 'مثال: 2025-09-22 09:00',
    ]);

    woocommerce_wp_text_input([
        'id'          => '_shopido_featured_end',
        'label'       => 'تاریخ پایان ویژه (local)',
        'placeholder' => 'YYYY-MM-DD HH:MM',
        'desc_tip'    => true,
        'description' => 'مثال: 2025-09-30 23:59',
    ]);

    echo '</div>';
});

add_action('woocommerce_process_product_meta', function($post_id){
    $enabled = isset($_POST['_shopido_featured_enabled']) ? 'yes' : 'no';
    update_post_meta($post_id, '_shopido_featured_enabled', $enabled);

    // تاریخ‌ها را به timestamp UTC ذخیره می‌کنیم تا مقایسه آسان شود
    $start_raw = isset($_POST['_shopido_featured_start']) ? sanitize_text_field($_POST['_shopido_featured_start']) : '';
    $end_raw   = isset($_POST['_shopido_featured_end'])   ? sanitize_text_field($_POST['_shopido_featured_end'])   : '';

    $start_ts = $start_raw ? strtotime($start_raw) : 0;
    $end_ts   = $end_raw   ? strtotime($end_raw)   : 0;

    update_post_meta($post_id, '_shopido_featured_start', $start_ts);
    update_post_meta($post_id, '_shopido_featured_end',   $end_ts);
});
