<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Shopido_FAQ_Accordion extends Widget_Base {

	public function get_name() {
		return 'shopido_faq_accordion';
	}

	public function get_title() {
		return esc_html__( 'Shopido FAQ (Accordion)', 'shopido-widgets-pack' );
	}

	public function get_icon() {
		return 'eicon-accordion';
	}

	public function get_categories() {
		return [ 'shopido' ]; 
	}

	public function get_style_depends() {
		return [ 'shopido-faq-accordion' ];
	}

	public function get_script_depends() {
		return [ 'shopido-faq-accordion' ];
	}

	protected function register_controls() {

		// =========================
		// Content
		// =========================
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'shopido-widgets-pack' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'question',
			[
				'label'       => esc_html__( 'Question', 'shopido-widgets-pack' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'آیا کاشت مو امری دائمی و یکبار برای همیشه است؟', 'shopido-widgets-pack' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'answer',
			[
				'label'   => esc_html__( 'Answer', 'shopido-widgets-pack' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'این یک متن نمونه است. پاسخ سوال را اینجا بنویسید.', 'shopido-widgets-pack' ),
			]
		);

		$repeater->add_control(
			'is_open',
			[
				'label'        => esc_html__( 'Open by default', 'shopido-widgets-pack' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'items',
			[
				'label'       => esc_html__( 'FAQ Items', 'shopido-widgets-pack' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'question' => esc_html__( 'آیا کاشت مو امری دائمی و یکبار برای همیشه است؟', 'shopido-widgets-pack' ),
						'answer'   => esc_html__( 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است...', 'shopido-widgets-pack' ),
						'is_open'  => 'yes',
					],
					[
						'question' => esc_html__( 'آیا کاشت مو امری دائمی و یکبار برای همیشه است؟', 'shopido-widgets-pack' ),
						'answer'   => esc_html__( 'پاسخ نمونه', 'shopido-widgets-pack' ),
						'is_open'  => '',
					],
					[
						'question' => esc_html__( 'آیا کاشت مو امری دائمی و یکبار برای همیشه است؟', 'shopido-widgets-pack' ),
						'answer'   => esc_html__( 'پاسخ نمونه', 'shopido-widgets-pack' ),
						'is_open'  => '',
					],
				],
				'title_field' => '{{{ question }}}',
			]
		);

		$this->add_control(
			'allow_multiple',
			[
				'label'        => esc_html__( 'Allow multiple open', 'shopido-widgets-pack' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'open_first_if_none',
			[
				'label'        => esc_html__( 'If none open, open first', 'shopido-widgets-pack' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'allow_multiple!' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		// =========================
		// Style: Wrapper
		// =========================
		$this->start_controls_section(
			'section_style_wrapper',
			[
				'label' => esc_html__( 'Wrapper', 'shopido-widgets-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'wrapper_bg',
			[
				'label'     => esc_html__( 'Background', 'shopido-widgets-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f4f3f1',
				'selectors' => [
					'{{WRAPPER}} .shopido-faq' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'wrapper_padding',
			[
				'label'      => esc_html__( 'Padding', 'shopido-widgets-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top' => 18, 'right' => 18, 'bottom' => 18, 'left' => 18, 'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .shopido-faq' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'wrapper_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'shopido-widgets-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [ 'size' => 14 ],
				'selectors'  => [
					'{{WRAPPER}} .shopido-faq' => 'border-radius: {{SIZE}}px;',
				],
			]
		);

		$this->end_controls_section();

		// =========================
		// Style: Items
		// =========================
		$this->start_controls_section(
			'section_style_items',
			[
				'label' => esc_html__( 'Items', 'shopido-widgets-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'item_bg',
			[
				'label'     => esc_html__( 'Item Background', 'shopido-widgets-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f7f6f4',
				'selectors' => [
					'{{WRAPPER}} .shopido-faq-item' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'item_border_color',
			[
				'label'     => esc_html__( 'Divider Color', 'shopido-widgets-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .shopido-faq-item + .shopido-faq-item' => 'border-top-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_radius',
			[
				'label'      => esc_html__( 'Item Radius', 'shopido-widgets-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [ 'size' => 12 ],
				'selectors'  => [
					'{{WRAPPER}} .shopido-faq-item' => 'border-radius: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'header_padding',
			[
				'label'      => esc_html__( 'Header Padding', 'shopido-widgets-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'top' => 18, 'right' => 18, 'bottom' => 18, 'left' => 18, 'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .shopido-faq-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'answer_padding',
			[
				'label'      => esc_html__( 'Answer Padding', 'shopido-widgets-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'top' => 0, 'right' => 18, 'bottom' => 18, 'left' => 18, 'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .shopido-faq-answer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// =========================
		// Style: Typography
		// =========================
		$this->start_controls_section(
			'section_style_typo',
			[
				'label' => esc_html__( 'Typography', 'shopido-widgets-pack' ),
				'tab'   =>Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'question_typo',
				'label'    => esc_html__( 'Question', 'shopido-widgets-pack' ),
				'selector' => '{{WRAPPER}} .shopido-faq-question',
			]
		);

		$this->add_control(
			'question_color',
			[
				'label'     => esc_html__( 'Question Color', 'shopido-widgets-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#222223',
				'selectors' => [
					'{{WRAPPER}} .shopido-faq-question' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'answer_typo',
				'label'    => esc_html__( 'Answer', 'shopido-widgets-pack' ),
				'selector' => '{{WRAPPER}} .shopido-faq-answer',
			]
		);

		$this->add_control(
			'answer_color',
			[
				'label'     => esc_html__( 'Answer Color', 'shopido-widgets-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3a3a3a',
				'selectors' => [
					'{{WRAPPER}} .shopido-faq-answer' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// =========================
		// Style: Icon
		// =========================
		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => esc_html__( 'Icon (+/-)', 'shopido-widgets-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'shopido-widgets-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#222223',
				'selectors' => [
					'{{WRAPPER}} .shopido-faq-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'shopido-widgets-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [ 'size' => 18 ],
				'selectors'  => [
					'{{WRAPPER}} .shopido-faq-icon' => 'font-size: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'icon_gap',
			[
				'label'      => esc_html__( 'Icon Gap', 'shopido-widgets-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [ 'size' => 16 ],
				'selectors'  => [
					'{{WRAPPER}} .shopido-faq-header' => 'gap: {{SIZE}}px;',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$items    = isset($settings['items']) ? (array) $settings['items'] : [];

		if ( empty($items) ) return;

		$allow_multiple = ( isset($settings['allow_multiple']) && $settings['allow_multiple'] === 'yes' ) ? '1' : '0';
		$open_first_if_none = ( isset($settings['open_first_if_none']) && $settings['open_first_if_none'] === 'yes' ) ? true : false;

		// اگر چندتا باز نباشه، فقط یکی رو باز نگه داریم
		$has_any_open = false;
		foreach ($items as $it) {
			if ( ! empty($it['is_open']) && $it['is_open'] === 'yes' ) { $has_any_open = true; break; }
		}
		if ( $allow_multiple === '0' ) {
			if ( ! $has_any_open && $open_first_if_none ) {
				$items[0]['is_open'] = 'yes';
				$has_any_open = true;
			}
			if ( $has_any_open ) {
				$seen = false;
				foreach ($items as $k => $it) {
					if ( ! empty($it['is_open']) && $it['is_open'] === 'yes' ) {
						if ( ! $seen ) { $seen = true; }
						else { $items[$k]['is_open'] = ''; }
					}
				}
			}
		}

		$uid = $this->get_id();

		echo '<div class="shopido-faq" data-allow-multiple="'.esc_attr($allow_multiple).'">';

		foreach ( $items as $index => $item ) {
			$question = isset($item['question']) ? $item['question'] : '';
			$answer   = isset($item['answer']) ? $item['answer'] : '';

			$is_open  = ( ! empty($item['is_open']) && $item['is_open'] === 'yes' );
			$item_id  = 'shopido-faq-'.$uid.'-'.$index;
			$panel_id = $item_id.'-panel';

			$btn_attrs = [
				'type'          => 'button',
				'class'         => 'shopido-faq-header',
				'aria-expanded' => $is_open ? 'true' : 'false',
				'aria-controls' => $panel_id,
			];

			echo '<div class="shopido-faq-item'.($is_open ? ' is-open' : '').'">';
				echo '<button';
				foreach ($btn_attrs as $k => $v) {
					echo ' '.esc_attr($k).'="'.esc_attr($v).'"';
				}
				echo '>';
					echo '<span class="shopido-faq-icon" aria-hidden="true"></span>';
					echo '<span class="shopido-faq-question">'.esc_html($question).'</span>';
				echo '</button>';

				echo '<div id="'.esc_attr($panel_id).'" class="shopido-faq-panel" role="region"'.($is_open ? '' : ' hidden').'>';
					echo '<div class="shopido-faq-answer">'.wp_kses_post($answer).'</div>';
				echo '</div>';
			echo '</div>';
		}

		echo '</div>';
	}
}
