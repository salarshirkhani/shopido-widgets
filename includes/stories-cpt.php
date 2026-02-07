<?php
/**
 * ثبت CPT استوری و متاباکس چندمدیا (فارسی)
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'init', function () {
    $labels = [
        'name'               => __( 'استوری‌ها', 'shopido-widgets-pack' ),
        'singular_name'      => __( 'استوری', 'shopido-widgets-pack' ),
        'menu_name'          => __( 'استوری‌ها', 'shopido-widgets-pack' ),
        'add_new'            => __( 'افزودن', 'shopido-widgets-pack' ),
        'add_new_item'       => __( 'افزودن استوری', 'shopido-widgets-pack' ),
        'edit_item'          => __( 'ویرایش استوری', 'shopido-widgets-pack' ),
        'new_item'           => __( 'استوری جدید', 'shopido-widgets-pack' ),
        'view_item'          => __( 'مشاهده استوری', 'shopido-widgets-pack' ),
        'search_items'       => __( 'جستجوی استوری‌ها', 'shopido-widgets-pack' ),
        'not_found'          => __( 'استوری‌ای یافت نشد', 'shopido-widgets-pack' ),
        'not_found_in_trash' => __( 'در زباله‌دان چیزی نیست', 'shopido-widgets-pack' ),
    ];
    $args = [
        'labels'             => $labels,
        'public'             => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_icon'          => 'dashicons-format-status',
        'supports'           => ['title','thumbnail'],
        'has_archive'        => false,
        'rewrite'            => ['slug' => 'story'],
        'show_in_rest'       => true,
    ];
    $args = apply_filters( 'shopido_story_cpt_args', $args );
    register_post_type( 'shopido_story', $args );
});

/**
 * متاباکس: چندمدیا برای هر استوری
 */
add_action( 'add_meta_boxes', function() {
    add_meta_box(
        'shopido_story_media_box',
        __( 'مدیاهای استوری (چندتایی)', 'shopido-widgets-pack' ),
        'shopido_story_media_box_cb',
        'shopido_story',
        'normal',
        'high'
    );
});

function shopido_story_media_box_cb( $post ) {
    wp_nonce_field( 'shopido_story_media_nonce', 'shopido_story_media_nonce' );
    wp_enqueue_media();

    $items = get_post_meta( $post->ID, '_story_media_items', true );
    if ( ! is_array( $items ) ) { $items = []; }
    ?>
    <style>
        .shopido-admin-media-list{display:flex;flex-wrap:wrap;gap:10px;margin-top:10px}
        .shopido-admin-media-card{border:1px solid #ccd0d4;border-radius:6px;overflow:hidden;width:120px;background:#fff;position:relative;direction:rtl}
        .shopido-admin-media-card img,.shopido-admin-media-card video{width:120px;height:120px;object-fit:cover;display:block}
        .shopido-admin-media-card .type{position:absolute;left:6px;top:6px;background:#000a;color:#fff;font-size:10px;padding:2px 6px;border-radius:99px}
        .shopido-admin-media-card .remove{position:absolute;right:6px;top:6px;background:#ff4757;color:#fff;border:none;border-radius:3px;cursor:pointer;font-size:12px;line-height:1;padding:0 6px}
        .shopido-admin-actions{margin-top:10px;display:flex;gap:8px;flex-wrap:wrap}
        .shopido-admin-actions .button{height:auto;padding:8px 12px}
        .description{margin-top:8px;color:#555}
    </style>

    <div id="shopido-admin-media" data-field="#shopido_story_media_json">
        <p class="description">
            <?php _e('چند تصویر یا ویدیو را برای این استوری انتخاب کنید. ترتیب نمایش مطابق ترتیب انتخاب/چینش در زیر است.', 'shopido-widgets-pack'); ?>
        </p>

        <div class="shopido-admin-actions">
            <a href="#" class="button button-primary js-shopido-add"><?php _e('افزودن مدیا', 'shopido-widgets-pack'); ?></a>
            <a href="#" class="button js-shopido-clear"><?php _e('پاک‌کردن همه', 'shopido-widgets-pack'); ?></a>
        </div>

        <div class="shopido-admin-media-list" id="shopido-media-list">
            <?php foreach ( $items as $it ) :
                $url  = esc_url( isset($it['url']) ? $it['url'] : '' );
                $type = esc_attr( isset($it['type']) ? $it['type'] : 'image' );
                ?>
                <div class="shopido-admin-media-card" data-url="<?php echo $url; ?>" data-type="<?php echo $type; ?>">
                    <span class="type"><?php echo esc_html( $type === 'video' ? 'ویدیو' : 'تصویر' ); ?></span>
                    <button type="button" class="remove" title="<?php esc_attr_e('حذف', 'shopido-widgets-pack'); ?>">×</button>
                    <?php if ( $type === 'video' ) : ?>
                        <video src="<?php echo $url; ?>" muted></video>
                    <?php else: ?>
                        <img src="<?php echo $url; ?>" alt="">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <input type="hidden" id="shopido_story_media_json" name="shopido_story_media_json" value="<?php echo esc_attr( wp_json_encode( $items ) ); ?>"/>
    </div>

    <script>
    (function($){
        const root = $('#shopido-admin-media');
        const $field = $(root.data('field'));
        const $list = $('#shopido-media-list');

        // ذخیره‌ی آرایه به فیلد hidden
        function save(){
            const arr = [];
            $list.find('.shopido-admin-media-card').each(function(){
                arr.push({
                    url: $(this).attr('data-url'),
                    type: $(this).attr('data-type')
                });
            });
            $field.val(JSON.stringify(arr));
        }

        // افزودن چندمدیا
        root.on('click','.js-shopido-add', function(e){
            e.preventDefault();
            const frame = wp.media({
                title: '<?php echo esc_js(__('انتخاب مدیا', 'shopido-widgets-pack')); ?>',
                multiple: true,
                library: { type: ['image','video'] }
            });
            frame.on('select', function(){
                const sel = frame.state().get('selection');
                sel.each(function(item){
                    const url = item.get('url');
                    const subtype = (item.get('type') || '').toLowerCase().indexOf('video') !== -1 ? 'video' : 'image';
                    const card = $('<div class="shopido-admin-media-card" data-url="'+url+'" data-type="'+subtype+'">' +
                                    '<span class="type">'+(subtype==='video'?'ویدیو':'تصویر')+'</span>' +
                                    '<button type="button" class="remove" title="<?php echo esc_js(__('حذف','shopido-widgets-pack')); ?>">×</button>' +
                                    (subtype === 'video' ? '<video src="'+url+'" muted></video>' : '<img src="'+url+'" alt="">') +
                                   '</div>');
                    $list.append(card);
                });
                save();
            });
            frame.open();
        });

        // حذف کارت
        root.on('click','.remove', function(e){
            e.preventDefault();
            $(this).closest('.shopido-admin-media-card').remove();
            save();
        });

        // پاک‌کردن همه
        root.on('click','.js-shopido-clear', function(e){
            e.preventDefault();
            $list.empty(); save();
        });
    })(jQuery);
    </script>
    <?php
}

/**
 * ذخیره متا
 */
add_action( 'save_post_shopido_story', function( $post_id ){
    if ( ! isset( $_POST['shopido_story_media_nonce'] ) || ! wp_verify_nonce( $_POST['shopido_story_media_nonce'], 'shopido_story_media_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
    if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }

    $json = isset($_POST['shopido_story_media_json']) ? wp_unslash($_POST['shopido_story_media_json']) : '[]';
    $arr = json_decode( $json, true );
    $clean = [];
    if ( is_array( $arr ) ) {
        foreach ( $arr as $it ) {
            $url  = isset($it['url']) ? esc_url_raw( $it['url'] ) : '';
            $type = isset($it['type']) && in_array($it['type'], ['image','video'], true ) ? $it['type'] : 'image';
            if ( $url ) {
                $clean[] = ['url' => $url, 'type' => $type];
            }
        }
    }
    update_post_meta( $post_id, '_story_media_items', $clean );
});
