<?php
namespace SEA\Widgets;

if ( ! defined( 'ABSPATH' ) ) { exit; }

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use WP_Query;

class Stories extends Widget_Base {

    public function get_name() { return 'shopido-stories'; }
    public function get_title() { return __( 'استوری‌های شاپیدو', 'shopido-widgets-pack' ); }
    public function get_icon() { return 'eicon-instagram-stories'; }
    public function get_categories() { return ['shopido']; }
    public function get_style_depends() { return ['shopido-story']; }
    public function get_script_depends() { return ['shopido-story']; }

    protected function register_controls() {
        $this->start_controls_section( 'section_content', [
            'label' => __( 'محتوا', 'shopido-widgets-pack' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control( 'posts_per_page', [
            'label'   => __( 'تعداد استوری‌ها', 'shopido-widgets-pack' ),
            'type'    => Controls_Manager::NUMBER,
            'default' => 12,
            'min'     => 1,
            'max'     => 100,
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $q = new WP_Query([
            'post_type'      => 'shopido_story',
            'posts_per_page' => intval( $settings['posts_per_page'] ?? 12 ),
            'post_status'    => 'publish',
            'no_found_rows'  => true,
        ]);

        echo '<div class="shopido-stories" role="list" dir="rtl">';
        while ( $q->have_posts() ) : $q->the_post();
            $title  = get_the_title();
            $avatar = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' );
            $items  = get_post_meta( get_the_ID(), '_story_media_items', true );
            if ( ! is_array( $items ) ) { $items = []; }

            // فقط url/type را برای فرانت می‌فرستیم
            $payload = [];
            foreach ( $items as $it ) {
                $url  = isset($it['url']) ? esc_url( $it['url'] ) : '';
                $type = isset($it['type']) ? sanitize_key( $it['type'] ) : 'image';
                if ( $url ) {
                    $payload[] = [ 'url' => $url, 'type' => in_array($type, ['image','video'], true) ? $type : 'image' ];
                }
            }
            $count = count( $payload );
            $badge = $count > 1 ? '<span class="shopido-story-count">' . intval( $count ) . '</span>' : '';
            if ( empty( $payload ) ) { continue; }

            $json = wp_json_encode( $payload );


printf(
    '<a href="#" class="shopido-story-item" role="listitem"
        data-title="%s" data-avatar="%s" data-media="%s" aria-label="%s">
        <span class="shopido-story-avatar">%s%s</span>
        <span class="shopido-story-title">%s</span>
    </a>',
    esc_attr( $title ),
    esc_url( $avatar ?: '' ),
    esc_attr( $json ),
    esc_attr( sprintf( __( 'باز کردن استوری: %s', 'shopido-widgets-pack' ), $title ) ),
    $avatar ? '<img src="'.esc_url($avatar).'" alt="">' : '',
    $badge, //  ← badge روی خود آواتار
    esc_html( $title )
);
        endwhile; wp_reset_postdata();
        echo '</div><div id="shopido-story-viewer" aria-hidden="true"></div>';
    }
}
