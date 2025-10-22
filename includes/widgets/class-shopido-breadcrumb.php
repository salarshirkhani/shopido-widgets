<?php
if ( ! defined('ABSPATH') ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class Shopido_Breadcrumb extends Widget_Base {

	public function get_name() { return 'shopido-breadcrumb'; }
	public function get_title() { return 'Shopido – Breadcrumb'; }
	public function get_icon() { return 'eicon-breadcrumbs'; }
	public function get_categories() { return [ 'shopido' ]; }

	public function get_style_depends() { return [ 'shopido-breadcrumb' ]; }

	protected function register_controls() {

		$this->start_controls_section('section_content', [
			'label' => 'تنظیمات مسیر‌نما',
		]);

		$this->add_control('use_yoast', [
			'label'        => 'اگر Yoast/Rank Math فعال است از همان استفاده شود؟',
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => 'بله',
			'label_off'    => 'خیر',
			'return_value' => 'yes',
			'default'      => 'yes',
		]);

		$this->add_control('home_label', [
			'label'   => 'متن خانه',
			'type'    => Controls_Manager::TEXT,
			'default' => 'خانه',
		]);

		$this->add_control('separator', [
			'label'   => 'جداکننده',
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'›' => '›',
				'/' => '/',
				'–' => '–',
				'»' => '»',
			],
			'default' => '›',
		]);

		$this->add_control('show_current', [
			'label'        => 'نمایش آیتم فعلی (بدون لینک)',
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		]);

		$this->end_controls_section();

		$this->start_controls_section('section_style', [
			'label' => 'استایل',
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control('bg_color', [
			'label' => 'پس‌زمینه',
			'type'  => Controls_Manager::COLOR,
			'default' => '#f8fafb',
			'selectors' => [
				'{{WRAPPER}} .shopido-bc' => 'background-color: {{VALUE}}',
			],
		]);

		$this->add_control('border_color', [
			'label' => 'خط دور',
			'type'  => Controls_Manager::COLOR,
			'default' => '#e5e7eb',
			'selectors' => [
				'{{WRAPPER}} .shopido-bc' => 'border-color: {{VALUE}}',
			],
		]);

		$this->add_control('text_color', [
			'label' => 'رنگ متن',
			'type'  => Controls_Manager::COLOR,
			'default' => '#111827',
			'selectors' => [
				'{{WRAPPER}} .shopido-bc' => 'color: {{VALUE}}',
			],
		]);

		$this->add_control('link_color', [
			'label' => 'رنگ لینک‌ها',
			'type'  => Controls_Manager::COLOR,
			'default' => '#0f172a',
			'selectors' => [
				'{{WRAPPER}} .shopido-bc a' => 'color: {{VALUE}}',
			],
		]);

		$this->add_control('link_hover', [
			'label' => 'هاور لینک‌ها',
			'type'  => Controls_Manager::COLOR,
			'default' => '#16a34a',
			'selectors' => [
				'{{WRAPPER}} .shopido-bc a:hover' => 'color: {{VALUE}}',
			],
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'typography',
			'selector' => '{{WRAPPER}} .shopido-bc',
		]);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		// 1) اگر کاربر خواسته و توابع پلاگین موجودند، خروجی همان‌ها
		if ( 'yes' === $s['use_yoast'] && function_exists('yoast_breadcrumb') ) {
			echo '<nav class="shopido-bc" aria-label="Breadcrumb">';
			yoast_breadcrumb('<span class="yoast">','</span>');
			echo '</nav>';
			return;
		}
		if ( 'yes' === $s['use_yoast'] && function_exists('rank_math_the_breadcrumbs') ) {
			echo '<nav class="shopido-bc" aria-label="Breadcrumb">';
			rank_math_the_breadcrumbs();
			echo '</nav>';
			return;
		}

		// 2) تولید دستی
		$items = $this->build_items($s['home_label']);
		if ( empty($items) ) return;

		$sep = esc_html( $s['separator'] ?: '›' );
		echo '<nav class="shopido-bc" aria-label="Breadcrumb"><ol>';
		$last = count($items) - 1;

		foreach ($items as $i => $it) {
			$is_last = $i === $last && ('yes' === $s['show_current']);
			echo '<li>';
			if ( ! $is_last && ! empty($it['url']) ) {
				printf('<a href="%s">%s</a>', esc_url($it['url']), esc_html($it['label']));
			} else {
				printf('<span class="current">%s</span>', esc_html($it['label']));
			}
			if ( $i !== $last ) {
				printf('<span class="sep" aria-hidden="true">%s</span>', $sep);
			}
			echo '</li>';
		}
		echo '</ol></nav>';
	}

	// -------- Helpers --------

	private function build_items($home_label = 'خانه') {
		$items = [];
		$home_url = home_url('/');
		$items[] = [ 'label' => $home_label ?: 'خانه', 'url' => $home_url ];

		// فروشگاه / ووکامرس
		if ( function_exists('is_shop') && is_shop() ) {
			$items[] = [ 'label' => get_the_title( wc_get_page_id('shop') ), 'url' => '' ];
			return $items;
		}
		if ( function_exists('is_product') && is_product() ) {
			$shop_id = wc_get_page_id('shop');
			if ( $shop_id && $shop_id > 0 ) {
				$items[] = [ 'label' => get_the_title($shop_id), 'url' => get_permalink($shop_id) ];
			}
			// دسته والد تا فرزند
			$terms = wc_get_product_terms(get_the_ID(), 'product_cat', ['orderby' => 'parent', 'order'=>'ASC']);
			if ( ! is_wp_error($terms) && ! empty($terms) ) {
				$primary = $terms[0];
				$ancestors = array_reverse( get_ancestors($primary->term_id, 'product_cat') );
				foreach ($ancestors as $aid) {
					$t = get_term($aid, 'product_cat');
					if ($t && ! is_wp_error($t)) {
						$items[] = [ 'label' => $t->name, 'url' => get_term_link($t) ];
					}
				}
				$items[] = [ 'label' => $primary->name, 'url' => get_term_link($primary) ];
			}
			$items[] = [ 'label' => get_the_title(), 'url' => '' ];
			return $items;
		}
		if ( function_exists('is_product_category') && is_product_category() ) {
			$shop_id = wc_get_page_id('shop');
			if ( $shop_id && $shop_id > 0 ) {
				$items[] = [ 'label' => get_the_title($shop_id), 'url' => get_permalink($shop_id) ];
			}
			$term = get_queried_object();
			if ( $term && ! is_wp_error($term) ) {
				$ancestors = array_reverse( get_ancestors($term->term_id, 'product_cat') );
				foreach ($ancestors as $aid) {
					$t = get_term($aid, 'product_cat');
					if ($t && ! is_wp_error($t)) {
						$items[] = [ 'label' => $t->name, 'url' => get_term_link($t) ];
					}
				}
				$items[] = [ 'label' => $term->name, 'url' => '' ];
			}
			return $items;
		}

		// بلاگ/پست
		if ( is_single() && ! is_singular( 'product' ) ) {
			$cat = get_the_category();
			if ( $cat ) {
				$cat = $cat[0];
				$anc = array_reverse( get_ancestors($cat->term_id, 'category') );
				foreach ($anc as $cid) {
					$c = get_category($cid);
					$items[] = [ 'label' => $c->name, 'url' => get_category_link($c) ];
				}
				$items[] = [ 'label' => $cat->name, 'url' => get_category_link($cat) ];
			}
			$items[] = [ 'label' => get_the_title(), 'url' => '' ];
			return $items;
		}

		// برگه‌ها با والد
		if ( is_page() ) {
			global $post;
			if ( $post->post_parent ) {
				$parents = array_reverse( get_post_ancestors( $post->ID ) );
				foreach ( $parents as $pid ) {
					$items[] = [ 'label' => get_the_title($pid), 'url' => get_permalink($pid) ];
				}
			}
			$items[] = [ 'label' => get_the_title(), 'url' => '' ];
			return $items;
		}

		// آرشیوها
		if ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();
			if ( $term && ! is_wp_error($term) ) {
				$anc = array_reverse( get_ancestors($term->term_id, $term->taxonomy) );
				foreach ($anc as $tid) {
					$t = get_term($tid, $term->taxonomy);
					$items[] = [ 'label' => $t->name, 'url' => get_term_link($t) ];
				}
				$items[] = [ 'label' => $term->name, 'url' => '' ];
			}
			return $items;
		}

		if ( is_search() ) {
			$items[] = [ 'label' => sprintf('جستجو: %s', get_search_query()), 'url' => '' ];
			return $items;
		}

		if ( is_404() ) {
			$items[] = [ 'label' => 'خطای ۴۰۴', 'url' => '' ];
			return $items;
		}

		// پیشفرض: فقط خانه
		return $items;
	}
}
