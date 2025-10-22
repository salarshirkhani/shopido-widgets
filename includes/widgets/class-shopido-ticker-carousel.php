<?php
if ( ! defined('ABSPATH') ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

class Shopido_Ticker_Carousel extends Widget_Base {

    public function get_name() { return 'shopido-ticker-carousel'; }
    public function get_title() { return 'Shopido – Ticker Carousel'; }
    public function get_icon() { return 'eicon-carousel'; }
    public function get_categories() { return ['shopido']; }
    public function get_keywords() { return ['ticker','marquee','loop','carousel','tag','badge','shopido']; }

    public function get_style_depends() {
        return ['shopido-ticker-carousel'];
    }
    public function get_script_depends() {
        return ['shopido-ticker-carousel'];
    }

    protected function register_controls() {

        /* ---------- CONTENT ---------- */
        $this->start_controls_section('section_content', [
            'label' => 'آیتم‌ها',
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $rep = new Repeater();

        $rep->add_control('text', [
            'label'       => 'متن آیتم',
            'type'        => Controls_Manager::TEXT,
            'default'     => 'نمونه آیتم',
            'placeholder' => 'مثلاً: کاملاً تمیزه',
            'label_block' => true,
        ]);

        $rep->add_control('bg_color', [
            'label'   => 'رنگ پس‌زمینه',
            'type'    => Controls_Manager::COLOR,
            'default' => '#EEF2FF', // indigo-50
        ]);

        $rep->add_control('text_color', [
            'label'   => 'رنگ متن',
            'type'    => Controls_Manager::COLOR,
            'default' => '#111827', // gray-900
        ]);

        $rep->add_control('border_color', [
            'label'   => 'رنگ بوردر',
            'type'    => Controls_Manager::COLOR,
            'default' => 'transparent',
        ]);

        $rep->add_control('radius', [
            'label'   => 'گردی گوشه‌ها (px)',
            'type'    => Controls_Manager::NUMBER,
            'default' => 14,
            'min'     => 0,
            'max'     => 64,
        ]);

        $rep->add_control('link', [
            'label'       => 'لینک (اختیاری)',
            'type'        => Controls_Manager::URL,
            'placeholder' => 'https://',
            'default'     => ['url'=>'', 'is_external'=>false, 'nofollow'=>false],
        ]);

        $this->add_control('items', [
            'label'       => 'آیتم‌ها',
            'type'        => Controls_Manager::REPEATER,
            'fields'      => $rep->get_controls(),
            'title_field' => '{{{ text }}}',
            'default'     => [
                ['text'=>'کاملاً تمیزه'],
                ['text'=>'راحت قد می‌کشه'],
                ['text'=>'بدون قاطی شدن'],
                ['text'=>'رضایت مشتری‌ها عالیه'],
            ],
        ]);

        $this->add_control('gap', [
            'label'   => 'فاصله بین آیتم‌ها (px)',
            'type'    => Controls_Manager::NUMBER,
            'default' => 16,
            'min'     => 0,
            'max'     => 64,
        ]);

        $this->add_control('speed', [
            'label'   => 'سرعت حرکت (ثانیه برای یک دور)',
            'type'    => Controls_Manager::NUMBER,
            'default' => 25,
            'min'     => 5,
            'max'     => 180,
        ]);

        $this->add_control('direction', [
            'label'   => 'جهت حرکت',
            'type'    => Controls_Manager::SELECT,
            'default' => 'rtl',
            'options' => [
                'rtl' => 'راست ← چپ',
                'ltr' => 'چپ ← راست',
            ],
        ]);

        $this->add_control('pause_on_hover', [
            'label'        => 'توقف با هاور',
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => 'بله',
            'label_off'    => 'خیر',
            'return_value' => 'yes',
            'default'      => 'yes',
        ]);

        $this->end_controls_section();

        /* ---------- STYLE ---------- */
        $this->start_controls_section('section_style', [
            'label' => 'استایل متن',
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'     => 'typo',
            'selector' => '{{WRAPPER}} .shopido-ticker-item',
        ]);

        $this->add_control('pad_item', [
            'label' => 'Padding آیتم',
            'type'  => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','em','rem'],
            'default' => [
                'top' => '8', 'right' => '16', 'bottom' => '8', 'left' => '16', 'unit' => 'px'
            ],
            'selectors' => [
                '{{WRAPPER}} .shopido-ticker-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_control('height_wrap', [
            'label' => 'ارتفاع نوار (px)',
            'type'  => Controls_Manager::NUMBER,
            'min'   => 0,
            'max'   => 200,
            'default' => 0, // auto
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();
        $items = $s['items'] ?? [];
        if (empty($items)) return;

        $dir          = esc_attr($s['direction'] ?? 'rtl');
        $speed        = intval($s['speed'] ?? 25);
        $gap          = intval($s['gap'] ?? 16);
        $pause_class  = (!empty($s['pause_on_hover']) && $s['pause_on_hover']==='yes') ? 'pause-on-hover' : '';
        $wrap_height  = intval($s['height_wrap'] ?? 0);
        $wrap_style   = $wrap_height > 0 ? 'style="height:'.$wrap_height.'px"' : '';

        echo '<div class="shopido-ticker-carousel '.$pause_class.'" data-direction="'.$dir.'" data-speed="'.$speed.'" data-gap="'.$gap.'" '.$wrap_style.'>';
        echo '  <div class="shopido-ticker-track">';
        foreach ($items as $it) {
            $text  = trim($it['text'] ?? '');
            if ($text === '') continue;

            $bg    = $it['bg_color'] ?? '#EEF2FF';
            $tc    = $it['text_color'] ?? '#111827';
            $bc    = $it['border_color'] ?? 'transparent';
            $rad   = isset($it['radius']) ? intval($it['radius']) : 14;
            $style = 'background:'.$bg.';color:'.$tc.';border-color:'.$bc.';border-radius:'.$rad.'px;';

            $link  = $it['link']['url'] ?? '';
            $target = (!empty($it['link']['is_external'])) ? ' target="_blank"' : '';
            $rel    = (!empty($it['link']['nofollow']))    ? ' rel="nofollow"' : '';

            echo '<div class="shopido-ticker-item" style="'.esc_attr($style).'">';
            if ($link) {
                echo '<a href="'.esc_url($link).'"'.$target.$rel.'>'.esc_html($text).'</a>';
            } else {
                echo esc_html($text);
            }
            echo '</div>';
        }
        echo '  </div>';
        echo '</div>';
    }
}
