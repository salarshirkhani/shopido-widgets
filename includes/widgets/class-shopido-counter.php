<?php
if ( ! defined('ABSPATH') ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

class Shopido_Counter extends Widget_Base {

    public function get_name() { return 'shopido-counter'; }
    public function get_title() { return 'Shopido – Counter'; }
    public function get_icon() { return 'eicon-counter'; }
    public function get_categories() { return ['shopido']; }
    public function get_keywords() { return ['counter','countup','countdown','number','stat']; }

    public function get_style_depends() { return ['shopido-counter']; }
    public function get_script_depends(){ return ['shopido-counter']; }

    protected function register_controls() {

        /* ------------ Content ------------ */
        $this->start_controls_section('sec_content', [
            'label' => 'محتوا',
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('title', [
            'label'       => 'عنوان',
            'type'        => Controls_Manager::TEXT,
            'default'     => 'عنوان شمارنده',
            'label_block' => true,
        ]);

        $this->add_control('direction', [
            'label'   => 'جهت شمارش',
            'type'    => Controls_Manager::CHOOSE,
            'options' => [
                'up'   => ['title'=>'از کم به زیاد','icon'=>'eicon-caret-up'],
                'down' => ['title'=>'از زیاد به کم','icon'=>'eicon-caret-down'],
            ],
            'toggle'  => false,
            'default' => 'up',
        ]);

        $this->add_control('start', [
            'label'   => 'عدد شروع',
            'type'    => Controls_Manager::NUMBER,
            'default' => 0,
        ]);
        $this->add_control('end', [
            'label'   => 'عدد پایان',
            'type'    => Controls_Manager::NUMBER,
            'default' => 100,
        ]);
        $this->add_control('duration', [
            'label'   => 'مدت انیمیشن (ثانیه)',
            'type'    => Controls_Manager::NUMBER,
            'min'     => 0.2,
            'step'    => 0.1,
            'default' => 2.0,
        ]);
        $this->add_control('thousands', [
            'label'        => 'جداکننده هزارگان',
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => 'بله','label_off'=>'خیر',
            'return_value' => 'yes',
            'default'      => 'yes',
        ]);
        $this->add_control('locale', [
            'label'       => 'Locale فرمت عدد (اختیاری)',
            'type'        => Controls_Manager::TEXT,
            'placeholder' => 'fa-IR یا en-US',
            'default'     => '',
        ]);
        $this->add_control('prefix', ['label'=>'پیشوند','type'=>Controls_Manager::TEXT,'default'=>'']);
        $this->add_control('suffix', ['label'=>'پسوند','type'=>Controls_Manager::TEXT,'default'=>'']);
        $this->add_control('play_once', [
            'label'        => 'فقط یک‌بار هنگام دیده‌شدن پخش شود',
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => 'بله','label_off'=>'خیر',
            'return_value' => 'yes',
            'default'      => 'yes',
        ]);

        $this->end_controls_section();

        /* ------------ Style: Wrapper ------------ */
        $this->start_controls_section('sec_style_wrap', [
            'label' => 'استایل کلی (Wrapper)',
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(Group_Control_Background::get_type(), [
            'name'     => 'wrap_bg',
            'selector' => '{{WRAPPER}} .shopido-counter',
        ]);

        $this->add_group_control(Group_Control_Border::get_type(), [
            'name'     => 'wrap_border',
            'selector' => '{{WRAPPER}} .shopido-counter',
        ]);

        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name'     => 'wrap_shadow',
            'selector' => '{{WRAPPER}} .shopido-counter',
        ]);

        $this->add_control('wrap_radius', [
            'label' => 'گردی گوشه‌های Wrapper',
            'type'  => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','%','em'],
            'selectors'  => [
                '{{WRAPPER}} .shopido-counter' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ]
        ]);

        $this->add_control('wrap_padding', [
            'label' => 'Padding Wrapper',
            'type'  => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','%','em'],
            'selectors'  => [
                '{{WRAPPER}} .shopido-counter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ]
        ]);

        $this->end_controls_section();

        /* ------------ Style: Number ------------ */
        $this->start_controls_section('sec_style_number', [
            'label' => 'استایل عدد',
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('number_color', [
            'label'     => 'رنگ عدد',
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .sc-number' => 'color: {{VALUE}};'],
        ]);
        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'     => 'number_typo',
            'selector' => '{{WRAPPER}} .sc-number',
        ]);

        $this->add_group_control(Group_Control_Background::get_type(), [
            'name'     => 'number_bg',
            'selector' => '{{WRAPPER}} .sc-number-wrap',
        ]);

        $this->add_group_control(Group_Control_Border::get_type(), [
            'name'     => 'number_border',
            'selector' => '{{WRAPPER}} .sc-number-wrap',
        ]);

        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name'     => 'number_shadow',
            'selector' => '{{WRAPPER}} .sc-number-wrap',
        ]);

        $this->add_control('number_radius', [
            'label' => 'Radius عدد',
            'type'  => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','%','em'],
            'selectors'  => [
                '{{WRAPPER}} .sc-number-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ]
        ]);

        $this->add_control('number_padding', [
            'label' => 'Padding عدد',
            'type'  => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','%','em'],
            'selectors'  => [
                '{{WRAPPER}} .sc-number-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ]
        ]);

        $this->end_controls_section();

        /* ------------ Style: Title ------------ */
        $this->start_controls_section('sec_style_title', [
            'label' => 'استایل عنوان',
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('title_color', [
            'label'     => 'رنگ عنوان',
            'type'      => Controls_Manager::COLOR,
            'selectors' => ['{{WRAPPER}} .sc-title' => 'color: {{VALUE}};'],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'     => 'title_typo',
            'selector' => '{{WRAPPER}} .sc-title',
        ]);

        $this->add_control('title_margin', [
            'label' => 'فاصله بیرونی عنوان',
            'type'  => Controls_Manager::DIMENSIONS,
            'size_units' => ['px','%','em'],
            'selectors'  => [
                '{{WRAPPER}} .sc-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ]
        ]);

        $this->add_control('gap', [
            'label' => 'فاصله عدد و عنوان (px)',
            'type'  => Controls_Manager::NUMBER,
            'min'   => 0,
            'default' => 8,
            'selectors' => ['{{WRAPPER}} .shopido-counter' => '--sc-gap: {{VALUE}}px;'],
        ]);

        $this->add_control('align', [
            'label'   => 'چینش',
            'type'    => Controls_Manager::CHOOSE,
            'options' => [
                'start'  => ['title'=>'راست','icon'=>'eicon-text-align-right'],
                'center' => ['title'=>'وسط','icon'=>'eicon-text-align-center'],
                'end'    => ['title'=>'چپ','icon'=>'eicon-text-align-left'],
            ],
            'default'   => 'center',
            'selectors' => ['{{WRAPPER}} .shopido-counter' => 'justify-items: {{VALUE}}; text-align: {{VALUE}};'],
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();

        $start = (float) ($s['direction']==='down' ? ($s['start']!=='' ? $s['start'] : $s['end']) : $s['start']);
        $end   = (float) $s['end'];

        $data = [
            'start'     => $start,
            'end'       => $end,
            'duration'  => max(0.1, (float)($s['duration'] ?: 2)),
            'grouping'  => (!empty($s['thousands']) ? '1' : '0'),
            'locale'    => trim((string)($s['locale'] ?? '')),
            'prefix'    => (string)($s['prefix'] ?? ''),
            'suffix'    => (string)($s['suffix'] ?? ''),
            'play_once' => (!empty($s['play_once']) ? '1' : '0'),
        ];

        $attrs = [];
        foreach ($data as $k=>$v) $attrs[] = 'data-'.$k.'="'.esc_attr($v).'"';
        ?>
        <div class="shopido-counter" <?php echo implode(' ', $attrs); ?>>
            <div class="sc-number-wrap">
                <span class="sc-prefix"><?php echo esc_html($data['prefix']); ?></span>
                <span class="sc-number">0</span>
                <span class="sc-suffix"><?php echo esc_html($data['suffix']); ?></span>
            </div>
            <?php if (!empty($s['title'])): ?>
                <div class="sc-title"><?php echo esc_html($s['title']); ?></div>
            <?php endif; ?>
        </div>
        <?php
    }
}
