<?php
if ( ! defined('ABSPATH') ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;

class Shopido_Pricing extends Widget_Base {

    public function get_name()        { return 'shopido-pricing'; }
    public function get_title()       { return 'Shopido – Pricing Plan'; }
    public function get_icon()        { return 'eicon-price-table'; }
    public function get_categories()  { return ['shopido']; }
    public function get_style_depends(){ return ['shopido-pricing']; }
    public function get_script_depends(){ return ['shopido-pricing']; }

    protected function register_controls() {

        /* ---------- CONTENT ---------- */
        $this->start_controls_section('sec_content', [
            'label' => 'پلن',
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('title', [
            'label'       => 'عنوان پلن',
            'type'        => Controls_Manager::TEXT,
            'default'     => 'VIP Gold',
            'label_block' => true,
        ]);

        $this->add_control('price', [
            'label'   => 'قیمت',
            'type'    => Controls_Manager::TEXT,
            'default' => '5,500',
        ]);

        $this->add_control('currency', [
            'label'   => 'واحد / پسوند قیمت',
            'type'    => Controls_Manager::TEXT,
            'default' => 'QR',
        ]);

        $this->add_control('cta_text', [
            'label'   => 'متن دکمه',
            'type'    => Controls_Manager::TEXT,
            'default' => 'Lorem ipsum CTA',
        ]);

        $this->add_control('cta_url', [
            'label'   => 'لینک دکمه',
            'type'    => Controls_Manager::URL,
            'default' => [
                'url'         => '#',
                'is_external' => false,
                'nofollow'    => false,
            ],
        ]);

        $this->add_control('features', [
            'label'       => 'ویژگی‌ها (هر خط یک مورد)',
            'type'        => Controls_Manager::TEXTAREA,
            'rows'        => 8,
            'placeholder' => "feature 1\nfeature 2\nfeature 3",
        ]);

        // آیکن ویژگی‌ها
        $this->add_control('feature_icon', [
            'label'   => 'آیکن ویژگی‌ها',
            'type'    => Controls_Manager::ICONS,
            'default' => [
                'value'   => 'fas fa-check',
                'library' => 'fa-solid',
            ],
        ]);

        $this->add_control('featured', [
            'label'        => 'پلن ویژه (طلایی)',
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => 'بله',
            'label_off'    => 'خیر',
            'return_value' => 'yes',
            'default'      => '',
        ]);

        $this->add_control('featured_bg_image', [
            'label'     => 'تصویر پس‌زمینه ویژه (اختیاری)',
            'type'      => Controls_Manager::MEDIA,
            'condition' => ['featured' => 'yes'],
        ]);

        $this->add_control('mobile_accordion', [
            'label'        => 'آکاردئون در موبایل',
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => 'فعال',
            'label_off'    => 'خاموش',
            'return_value' => 'yes',
            'default'      => 'yes',
        ]);

        $this->end_controls_section();

        /* ---------- STYLE: CARD / TITLE / PRICE / BUTTON ---------- */
        $this->start_controls_section('sec_style_normal', [
            'label' => 'استایل پلن',
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('card_bg', [
            'label'   => 'پس‌زمینه کارت',
            'type'    => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .shopido-pricing .sp-card' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('card_text', [
            'label'   => 'رنگ متن عمومی',
            'type'    => Controls_Manager::COLOR,
            'default' => '#111827',
            'selectors' => [
                '{{WRAPPER}} .shopido-pricing .sp-card' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('accent_color', [
            'label'   => 'رنگ تأکید (تیتر / قیمت)',
            'type'    => Controls_Manager::COLOR,
            'default' => '#B58F58',
            'selectors' => [
                '{{WRAPPER}} .shopido-pricing .title' => 'color: {{VALUE}};',
                '{{WRAPPER}} .shopido-pricing .price' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'     => 'title_typo',
            'label'    => 'تایپوگرافی عنوان',
            'selector' => '{{WRAPPER}} .shopido-pricing .title',
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'     => 'price_typo',
            'label'    => 'تایپوگرافی قیمت',
            'selector' => '{{WRAPPER}} .shopido-pricing .price',
        ]);

        // استایل دکمه
        $this->add_control('btn_bg_color', [
            'label'   => 'رنگ پس‌زمینه دکمه',
            'type'    => Controls_Manager::COLOR,
            'default' => '#f1f5f9',
            'selectors' => [
                '{{WRAPPER}} .shopido-pricing .sp-cta .btn' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('btn_text_color', [
            'label'   => 'رنگ متن دکمه',
            'type'    => Controls_Manager::COLOR,
            'default' => '#111827',
            'selectors' => [
                '{{WRAPPER}} .shopido-pricing .sp-cta .btn, {{WRAPPER}} .shopido-pricing .sp-cta .btn span, {{WRAPPER}} .shopido-pricing .sp-cta .btn i' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'     => 'btn_typo',
            'label'    => 'تایپوگرافی دکمه',
            'selector' => '{{WRAPPER}} .shopido-pricing .sp-cta .btn span',
        ]);

        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name'     => 'card_shadow',
            'selector' => '{{WRAPPER}} .shopido-pricing .sp-card',
        ]);

        $this->add_group_control(Group_Control_Border::get_type(), [
            'name'     => 'card_border',
            'selector' => '{{WRAPPER}} .shopido-pricing .sp-card',
        ]);

        $this->end_controls_section();

        /* ---------- STYLE: FEATURES LIST ---------- */
        $this->start_controls_section('sec_style_features', [
            'label' => 'لیست ویژگی‌ها',
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('features_color', [
            'label'   => 'رنگ متن ویژگی‌ها',
            'type'    => Controls_Manager::COLOR,
            'default' => '#374151',
            'selectors' => [
                '{{WRAPPER}} .shopido-pricing .sp-features li span.sp-text' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'     => 'features_typo',
            'label'    => 'تایپوگرافی ویژگی‌ها',
            'selector' => '{{WRAPPER}} .shopido-pricing .sp-features li span.sp-text',
        ]);

        $this->add_control('feature_icon_color', [
            'label'   => 'رنگ آیکن ویژگی‌ها',
            'type'    => Controls_Manager::COLOR,
            'default' => '#16a34a',
            'selectors' => [
                '{{WRAPPER}} .shopido-pricing .sp-features li .sp-icon' => 'color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();

        /* ---------- STYLE: FEATURED ---------- */
        $this->start_controls_section('sec_style_featured', [
            'label' => 'استایل پلن ویژه',
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('feat_bg_color', [
            'label'   => 'رنگ پس‌زمینه ویژه',
            'type'    => Controls_Manager::COLOR,
            'default' => '#C5A574',
            'selectors' => [
                '{{WRAPPER}} .shopido-pricing .sp-card.is-featured' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('feat_text_color', [
            'label'   => 'رنگ متن ویژه',
            'type'    => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .shopido-pricing .sp-card.is-featured'                               => 'color: {{VALUE}};',
                '{{WRAPPER}} .shopido-pricing .sp-card.is-featured .title'                        => 'color: {{VALUE}};',
                '{{WRAPPER}} .shopido-pricing .sp-card.is-featured .price'                        => 'color: {{VALUE}};',
                '{{WRAPPER}} .shopido-pricing .sp-card.is-featured .sp-features li span.sp-text'  => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('feat_overlay', [
            'label'   => 'شفافیت تصویر پس‌زمینه',
            'type'    => Controls_Manager::SLIDER,
            'size_units' => ['%'],
            'range'  => ['%' => ['min' => 0, 'max' => 100]],
            'default'=> ['size' => 20, 'unit' => '%'],
        ]);

        $this->end_controls_section();
    }

    protected function render(){
        $s = $this->get_settings_for_display();

        $title    = $s['title'] ?? '';
        $price    = $s['price'] ?? '';
        $currency = $s['currency'] ?? '';
        $cta_text = $s['cta_text'] ?? '';
        $cta_url  = !empty($s['cta_url']['url']) ? $s['cta_url']['url'] : '#';
        $target   = !empty($s['cta_url']['is_external']) ? ' target="_blank"' : '';
        $rel      = !empty($s['cta_url']['nofollow']) ? ' rel="nofollow"' : '';

        $features_raw = (string) ($s['features'] ?? '');
        $features = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $features_raw)));

        $is_featured = !empty($s['featured']) && $s['featured'] === 'yes';
        $mobile_acc  = !empty($s['mobile_accordion']) && $s['mobile_accordion'] === 'yes' ? '1' : '0';

        $card_classes = 'sp-card is-open';
        if ( $is_featured ) {
            $card_classes .= ' is-featured';
        }

        $style_attr = '';
        if ( $is_featured && !empty($s['featured_bg_image']['url']) ) {
            $url      = $s['featured_bg_image']['url'];
            $overlay  = isset($s['feat_overlay']['size']) ? floatval($s['feat_overlay']['size']) : 20;
            $overlay  = max(0, min(100, $overlay)) / 100;
            $style_attr = sprintf(
                ' style="--sp-featured-bg:url(%s);--sp-overlay:%s;"',
                esc_url($url),
                esc_attr($overlay)
            );
        }

        echo '<div class="shopido-pricing" data-mobile-acc="' . esc_attr($mobile_acc) . '">';

        echo '<div class="' . esc_attr($card_classes) . '"' . $style_attr . '>';

        // HEAD
        echo '<div class="sp-head" aria-expanded="true">';
        echo '  <div class="title">' . esc_html($title) . '</div>';
        echo '  <div class="price">' . esc_html($price) . ' <span class="cur">' . esc_html($currency) . '</span></div>';
        echo '  <button class="sp-toggle" type="button" aria-label="toggle"></button>';
        echo '</div>';

        // BODY
        echo '<div class="sp-body">';

        echo '  <div class="sp-cta"><a class="btn" href="' . esc_url($cta_url) . '"' . $target . $rel . '>';
        echo '      <span>' . esc_html($cta_text) . '</span><i class="arr">→</i>';
        echo '  </a></div>';

        if ( ! empty($features) ) {
            echo '<ul class="sp-features">';
            foreach ($features as $f) {
                echo '<li>';

                echo '<span class="sp-icon">';
                if ( ! empty($s['feature_icon']['value']) ) {
                    Icons_Manager::render_icon(
                        $s['feature_icon'],
                        [
                            'aria-hidden' => 'true',
                            'class'       => 'sp-icon-inner',
                        ]
                    );
                } else {
                    echo '<span class="sp-icon-fallback">✓</span>';
                }
                echo '</span>';

                echo '<span class="sp-text">' . esc_html($f) . '</span>';
                echo '</li>';
            }
            echo '</ul>';
        }

        echo '</div>'; // .sp-body
        echo '</div>'; // .sp-card
        echo '</div>'; // .shopido-pricing
    }
}
