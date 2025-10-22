<?php
if ( ! defined('ABSPATH') ) exit;

// بدون namespace/use
class Shopido_Read_More extends \Elementor\Widget_Base {

    public function get_name()        { return 'shopido-read-more'; }
    public function get_title()       { return 'بیشتر بخوانید – شاپیدو'; }
    public function get_icon()        { return 'eicon-text'; }
    public function get_categories()  { return ['shopido']; }
    public function get_style_depends(){ return ['shopido-readmore']; }
    public function get_script_depends(){ return ['shopido-readmore']; }

    protected function register_controls() {
    
    	// تب «محتوا»
    	$this->start_controls_section('section_content', [ 'label' => 'محتوا' ]);
    
    	$this->add_control('content', [
    		'label'   => 'متن',
    		'type'    => \Elementor\Controls_Manager::WYSIWYG,
    		'default' => 'اینجا متن طولانی‌ات را قرار بده…'
    	]);
    
    	$this->add_control('height', [
    		'label'      => 'ارتفاع حالت بسته (px)',
    		'type'       => \Elementor\Controls_Manager::SLIDER,
    		'size_units' => ['px'],
    		'range'      => ['px' => ['min' => 80, 'max' => 1200]],
    		'default'    => ['size' => 180, 'unit' => 'px'],
    	]);
    
    	$this->add_control('overlay', [
    		'label'       => 'رنگ محوشدن (اختیاری)',
    		'type'        => \Elementor\Controls_Manager::COLOR,
    		'description' => 'اگر خالی باشد با بک‌گراند سکشن/صفحه هماهنگ می‌شود.'
    	]);
    
    	$this->add_control('more_text', [
    		'label'   => 'متن «مشاهده بیشتر»',
    		'type'    => \Elementor\Controls_Manager::TEXT,
    		'default' => 'مشاهده بیشتر'
    	]);
    
    	$this->add_control('less_text', [
    		'label'   => 'متن «بستن»',
    		'type'    => \Elementor\Controls_Manager::TEXT,
    		'default' => 'بستن'
    	]);
    
    	$this->add_control('align', [
    		'label'   => 'چیدمان دکمه',
    		'type'    => \Elementor\Controls_Manager::CHOOSE,
    		'options' => [
    			'left'   => ['title' => 'چپ',  'icon' => 'eicon-text-align-left'],
    			'center' => ['title' => 'وسط', 'icon' => 'eicon-text-align-center'],
    			'right'  => ['title' => 'راست','icon' => 'eicon-text-align-right'],
    		],
    		'default' => 'center',
    		'toggle'  => false
    	]);
    
    	$this->end_controls_section();
    
    	/* ---------- تب «استایل» > تایپوگرافی متن ---------- */
    	$this->start_controls_section('section_style_content', [
    		'label' => 'تایپوگرافی متن',
    		'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
    	]);
    
    	$this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
    		'name'     => 'content_typo',
    		'selector' => '{{WRAPPER}} .shopido-rm__content',
    	]);
    
    	$this->add_control('content_color', [
    		'label' => 'رنگ متن',
    		'type'  => \Elementor\Controls_Manager::COLOR,
    		'selectors' => [
    			'{{WRAPPER}} .shopido-rm__content' => 'color: {{VALUE}};',
    		],
    	]);
    
    	$this->end_controls_section();
    
    	/* ---------- تب «استایل» > تایپوگرافی دکمه ---------- */
    	$this->start_controls_section('section_style_button', [
    		'label' => 'تایپوگرافی دکمه',
    		'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
    	]);
    
    	$this->add_group_control(\Elementor\Group_Control_Typography::get_type(), [
    		'name'     => 'button_typo',
    		'selector' => '{{WRAPPER}} .shopido-rm__toggle',
    	]);
    
    	$this->add_control('button_color', [
    		'label' => 'رنگ نوشتهٔ دکمه',
    		'type'  => \Elementor\Controls_Manager::COLOR,
    		'selectors' => [
    			'{{WRAPPER}} .shopido-rm__toggle' => 'color: {{VALUE}};',
    		],
    	]);
    
    	$this->add_control('button_color_hover', [
    		'label' => 'رنگ دکمه (هاور)',
    		'type'  => \Elementor\Controls_Manager::COLOR,
    		'selectors' => [
    			'{{WRAPPER}} .shopido-rm__toggle:hover' => 'color: {{VALUE}};',
    		],
    	]);
    
    	$this->add_responsive_control('icon_size', [
    		'label' => 'اندازه آیکن (px)',
    		'type'  => \Elementor\Controls_Manager::SLIDER,
    		'size_units' => ['px'],
    		'range' => ['px' => ['min' => 8, 'max' => 48]],
    		'default' => ['size' => 18, 'unit' => 'px'],
    		'selectors' => [
    			'{{WRAPPER}} .shopido-rm__icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
    		],
    	]);
    
    	$this->add_responsive_control('button_margin_top', [
    		'label' => 'فاصلهٔ بالای دکمه (px)',
    		'type'  => \Elementor\Controls_Manager::SLIDER,
    		'range' => ['px' => ['min' => 0, 'max' => 60]],
    		'selectors' => [
    			'{{WRAPPER}} .shopido-rm__toggle' => 'margin-top: {{SIZE}}{{UNIT}};',
    		],
    	]);
    
    	$this->end_controls_section();
    }


    protected function render() {
        $s       = $this->get_settings_for_display();
        $height  = isset($s['height']['size']) ? intval($s['height']['size']) : 180;
        $overlay = isset($s['overlay']) ? trim((string)$s['overlay']) : '';
        $align   = in_array($s['align'], ['left','center','right'], true) ? $s['align'] : 'center';

        $style = "--srm-h: {$height}px;";
        if ($overlay !== '') $style .= "--srm-overlay: {$overlay};";

        $this->add_render_attribute('wrap', [
            'class' => 'shopido-rm',
            'style' => $style,
            'dir'   => 'rtl'
        ]);

        $content = do_shortcode( wp_kses_post( $s['content'] ) );
        ?>
        <div <?php echo $this->get_render_attribute_string('wrap'); ?>>
            <div class="shopido-rm__content"><?php echo $content; ?></div>
            <div class="shopido-rm__fade" aria-hidden="true"></div>
            <button type="button"
                    class="shopido-rm__toggle align-<?php echo esc_attr($align); ?>"
                    data-more="<?php echo esc_attr($s['more_text']); ?>"
                    data-less="<?php echo esc_attr($s['less_text']); ?>">
                <span class="shopido-rm__label"><?php echo esc_html($s['more_text']); ?></span>
                <svg class="shopido-rm__icon" width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M7 10l5 5 5-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
        <?php
    }
}
