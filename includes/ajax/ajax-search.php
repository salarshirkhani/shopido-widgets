<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * AJAX: shopido_ajax_search
 * returns JSON: { ok:1, html:'...', count: N }
 */
add_action('wp_ajax_shopido_ajax_search', 'shopido_ajax_search_handler');
add_action('wp_ajax_nopriv_shopido_ajax_search', 'shopido_ajax_search_handler');

function shopido_ajax_search_handler() {
    // Basic guards
    if ( ! isset($_POST['_wpnonce']) || ! wp_verify_nonce($_POST['_wpnonce'], 'shopido_ajax_search') ) {
        wp_send_json_error(['message'=>'bad nonce'], 403);
    }

    $q        = isset($_POST['q']) ? wp_strip_all_tags((string)$_POST['q']) : '';
    $q        = trim(mb_substr($q, 0, 80));
    $minchars = max(1, intval($_POST['min_chars'] ?? 2));
    if ( mb_strlen($q) < $minchars ) {
        wp_send_json_success(['ok'=>1,'html'=>'','count'=>0]);
    }

    $post_types  = array_filter(array_map('sanitize_text_field', explode(',', (string)($_POST['post_types'] ?? 'product'))));
    if ( empty($post_types) ) $post_types = ['product'];

    $taxonomy    = sanitize_text_field($_POST['taxonomy'] ?? '');
    $term_ids    = array_filter(array_map('absint', explode(',', (string)($_POST['term_ids'] ?? ''))));
    $limit       = max(1, min(50, intval($_POST['limit'] ?? 8)));
    $orderby     = sanitize_text_field($_POST['orderby'] ?? 'date');
    $order       = in_array(($_POST['order'] ?? 'DESC'), ['ASC','DESC'], true) ? $_POST['order'] : 'DESC';
    $only_stock  = !empty($_POST['only_instock']) && $_POST['only_instock']=='1';

    // Build WP_Query
    $args = [
        'post_type'      => $post_types,
        'post_status'    => 'publish',
        's'              => $q,
        'posts_per_page' => $limit,
        'orderby'        => $orderby,
        'order'          => $order,
        'no_found_rows'  => true,
        'ignore_sticky_posts' => true,
    ];

    // taxonomy filter
    if ( $taxonomy && !empty($term_ids) ) {
        $args['tax_query'] = [[
            'taxonomy' => $taxonomy,
            'field'    => 'term_id',
            'terms'    => $term_ids,
            'operator' => 'IN',
        ]];
    }

    // only in-stock (WooCommerce)
    if ( $only_stock && class_exists('WooCommerce') && in_array('product', $post_types, true) ) {
        $args['meta_query'][] = [
            'key'     => '_stock_status',
            'value'   => 'instock',
            'compare' => '=',
        ];
    }

    // price order (for products)
    if ( $orderby === 'meta_value_num' ) {
        $args['meta_key'] = '_price';
    }

    $query = new WP_Query($args);

    // Render items
    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();

            $post_id   = get_the_ID();
            $pt        = get_post_type($post_id);
            $permalink = get_permalink($post_id);
            $title     = get_the_title($post_id);
            $excerpt   = wp_trim_words( wp_strip_all_tags(get_the_excerpt($post_id)), 20 );

            $is_product = ( $pt === 'product' && class_exists('WooCommerce') );
            $thumb_url  = get_the_post_thumbnail_url($post_id, 'thumbnail') ?: wc_placeholder_img_src();

            $price_html = '';
            if ( $is_product ) {
                $product = wc_get_product($post_id);
                if ( $product ) $price_html = $product->get_price_html();
            }

            // layout will be decided on front via root class (cards | list) — HTML مشترک:
            ?>
            <a class="sas-item <?php echo $is_product ? 'is-product' : 'is-post'; ?>" href="<?php echo esc_url($permalink); ?>" role="option">
                <div class="sas-thumb">
                    <?php if ( has_post_thumbnail($post_id) ) : ?>
                        <?php echo get_the_post_thumbnail($post_id, 'thumbnail', ['loading'=>'lazy','decoding'=>'async']); ?>
                    <?php else: ?>
                        <img src="<?php echo esc_url($thumb_url); ?>" alt="" loading="lazy" decoding="async" />
                    <?php endif; ?>
                </div>
                <div class="sas-body">
                    <div class="sas-title"><?php echo esc_html($title); ?></div>
                    <?php if ( $is_product && $price_html ) : ?>
                        <div class="sas-meta">
                            <span class="sas-price"><?php echo wp_kses_post($price_html); ?></span>
                        </div>
                    <?php else: ?>
                        <div class="sas-meta">
                            <span class="sas-excerpt"><?php echo esc_html($excerpt); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </a>
            <?php
        }
        wp_reset_postdata();
    } else {
        // no results (html خالی برمی‌گردونیم و متن در فرانت handled میشه)
    }

    $html = trim(ob_get_clean());
    wp_send_json_success(['ok'=>1, 'html'=>$html, 'count'=> $query->post_count ]);
}
