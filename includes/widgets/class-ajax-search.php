<?php
if ( ! defined('ABSPATH') ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Shopido_Ajax_Search extends Widget_Base {

    public function get_name() { return 'shopido-ajax-search'; }
    public function get_title() { return 'Shopido – Ajax Search'; }
    public function get_icon() { return 'eicon-site-search'; }
    public function get_categories() { return ['shopido']; }
    public function get_keywords() { return ['search','ajax','shopido','woocommerce','product']; }

    public function get_style_depends() {
        return ['shopido-ajax-search'];
    }
    public function get_script_depends() {
        return ['shopido-ajax-search'];
    }

    protected function register_controls() {

        /* ------ Content: Data ------ */
        $this->start_controls_section('section_data', [
            'label' => 'دیتا و منبع',
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $post_types = get_post_types(['public'=>true], 'objects');
        $choices = [];
        foreach ($post_types as $pt) {
            $choices[$pt->name] = $pt->labels->singular_name . " ({$pt->name})";
        }

        $this->add_control('post_types', [
            'label'       => 'Post Types',
            'type'        => Controls_Manager::SELECT2,
            'multiple'    => true,
            'label_block' => true,
            'options'     => $choices,
            'default'     => ['product'],
        ]);

        $this->add_control('taxonomy', [
            'label'       => 'Taxonomy (اختیاری)',
            'type'        => Controls_Manager::TEXT,
            'placeholder' => 'مثلاً: product_cat یا category',
            'default'     => '',
        ]);

        $this->add_control('term_ids', [
            'label'       => 'Term IDs (اختیاری، کاما جدا)',
            'type'        => Controls_Manager::TEXT,
            'placeholder' => 'مثلاً: 12,34,56',
            'default'     => '',
        ]);

        $this->add_control('min_chars', [
            'label'   => 'حداقل کاراکتر برای جستجو',
            'type'    => Controls_Manager::NUMBER,
            'min'     => 1,
            'max'     => 10,
            'default' => 2,
        ]);

        $this->add_control('limit', [
            'label'   => 'تعداد نتایج لایو',
            'type'    => Controls_Manager::NUMBER,
            'min'     => 1,
            'max'     => 50,
            'default' => 8,
        ]);

        $this->add_control('orderby', [
            'label'   => 'ترتیب',
            'type'    => Controls_Manager::SELECT,
            'default' => 'date',
            'options' => [
                'date'       => 'جدیدترین',
                'title'      => 'عنوان',
                'relevance'  => 'مرتبط‌ترین',
                'meta_value_num' => 'قیمت (محصول)',
            ],
        ]);

        $this->add_control('order', [
            'label'   => 'جهت',
            'type'    => Controls_Manager::SELECT,
            'default' => 'DESC',
            'options' => [
                'DESC' => 'نزولی',
                'ASC'  => 'صعودی',
            ],
        ]);

        $this->add_control('only_instock', [
            'label'        => 'فقط موجود (ووکامرس)',
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => 'بله',
            'label_off'    => 'خیر',
            'return_value' => 'yes',
            'default'      => '',
        ]);

        $this->end_controls_section();

        /* ------ Content: Behavior ------ */
        $this->start_controls_section('section_behavior', [
            'label' => 'رفتار و UX',
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('placeholder', [
            'label'       => 'Placeholder',
            'type'        => Controls_Manager::TEXT,
            'default'     => 'جستجوی محصول یا مطلب…',
        ]);

        $this->add_control('debounce', [
            'label'   => 'تاخیر تایپ (ms)',
            'type'    => Controls_Manager::NUMBER,
            'min'     => 0,
            'max'     => 1000,
            'default' => 250,
        ]);

        $this->add_control('no_result_text', [
            'label'       => 'متن «چیزی پیدا نشد»',
            'type'        => Controls_Manager::TEXT,
            'default'     => 'نتیجه‌ای یافت نشد.',
        ]);

        $this->add_control('show_view_all', [
            'label'        => 'نمایش «همه نتایج»',
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => 'بله',
            'label_off'    => 'خیر',
            'return_value' => 'yes',
            'default'      => 'yes',
        ]);

        $this->end_controls_section();

        /* ------ Content: Display ------ */
        $this->start_controls_section('section_display', [
            'label' => 'نمایش',
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('layout', [
            'label'   => 'Layout',
            'type'    => Controls_Manager::CHOOSE,
            'options' => [
                'cards' => [
                    'title' => 'کارتـی',
                    'icon'  => 'eicon-gallery-grid',
                ],
                'list' => [
                    'title' => 'ستونی',
                    'icon'  => 'eicon-bullet-list',
                ],
            ],
            'default' => 'cards',
            'toggle'  => false,
        ]);

        $this->add_control('show_thumb', [
            'label'        => 'نمایش تصویر شاخص',
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => 'بله',
            'label_off'    => 'خیر',
            'return_value' => 'yes',
            'default'      => 'yes',
        ]);

        $this->add_control('show_price', [
            'label'        => 'نمایش قیمت (محصول)',
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => 'بله',
            'label_off'    => 'خیر',
            'return_value' => 'yes',
            'default'      => 'yes',
        ]);

        $this->add_control('columns_desktop', [
            'label'   => 'ستون (دسکتاپ/کارتـی)',
            'type'    => Controls_Manager::NUMBER,
            'min'     => 1,
            'max'     => 6,
            'default' => 3,
            'condition' => ['layout' => 'cards']
        ]);

        $this->add_control('columns_tablet', [
            'label'   => 'ستون (تبلت/کارتـی)',
            'type'    => Controls_Manager::NUMBER,
            'min'     => 1,
            'max'     => 6,
            'default' => 2,
            'condition' => ['layout' => 'cards']
        ]);

        $this->add_control('columns_mobile', [
            'label'   => 'ستون (موبایل/کارتـی)',
            'type'    => Controls_Manager::NUMBER,
            'min'     => 1,
            'max'     => 6,
            'default' => 1,
            'condition' => ['layout' => 'cards']
        ]);

        $this->end_controls_section();

        /* ------ Style: Input ------ */
        $this->start_controls_section('style_input', [
            'label' => 'استایل ورودی',
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'     => 'input_typo',
            'selector' => '{{WRAPPER}} .sas-input input',
        ]);

        $this->add_control('input_padding', [
            'label' => 'Padding',
            'type'  => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors'  => [
                '{{WRAPPER}} .sas-input input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Border::get_type(), [
            'name'     => 'input_border',
            'selector' => '{{WRAPPER}} .sas-input input',
        ]);

        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name'     => 'input_shadow',
            'selector' => '{{WRAPPER}} .sas-input input',
        ]);

        $this->end_controls_section();

        /* ------ Style: Panel ------ */
        $this->start_controls_section('style_panel', [
            'label' => 'استایل پنل نتایج',
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(Group_Control_Border::get_type(), [
            'name'     => 'panel_border',
            'selector' => '{{WRAPPER}} .sas-panel',
        ]);

        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name'     => 'panel_shadow',
            'selector' => '{{WRAPPER}} .sas-panel',
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();
    
        $nonce = wp_create_nonce('shopido_ajax_search');
        $data  = [
            'post_types'     => implode(',', (array)($s['post_types'] ?? [])),
            'taxonomy'       => (string)($s['taxonomy'] ?? ''),
            'term_ids'       => (string)($s['term_ids'] ?? ''),
            'min_chars'      => max(1, intval($s['min_chars'] ?? 2)),
            'limit'          => max(1, intval($s['limit'] ?? 8)),
            'orderby'        => (string)($s['orderby'] ?? 'date'),
            'order'          => (string)($s['order'] ?? 'DESC'),
            'only_instock'   => !empty($s['only_instock']) ? '1' : '0',
            'layout'         => (string)($s['layout'] ?? 'cards'),
            'show_thumb'     => !empty($s['show_thumb']) ? '1' : '0',
            'show_price'     => !empty($s['show_price']) ? '1' : '0',
            'cols_d'         => intval($s['columns_desktop'] ?? 3),
            'cols_t'         => intval($s['columns_tablet'] ?? 2),
            'cols_m'         => intval($s['columns_mobile'] ?? 1),
            'debounce'       => intval($s['debounce'] ?? 250),
            'placeholder'    => (string)($s['placeholder'] ?? 'جستجو...'),
            'no_result_text' => (string)($s['no_result_text'] ?? 'نتیجه‌ای یافت نشد.'),
            'show_view_all'  => !empty($s['show_view_all']) ? '1' : '0',
        ];
        $attrs = [];
        foreach ($data as $k=>$v) {
            $attrs[] = 'data-'.$k.'="'.esc_attr($v).'"';
        }
        $attrs[] = 'data-nonce="'.esc_attr($nonce).'"';
        $attrs[] = 'data-ajax-url="'.esc_url(admin_url('admin-ajax.php')).'"';
    
        $root_classes = [
            'shopido-ajax-search',
            'is-layout-'.$data['layout'],
        ];
        if ($data['layout']==='cards') {
            $root_classes[] = 'cols-d-'.$data['cols_d'];
            $root_classes[] = 'cols-t-'.$data['cols_t'];
            $root_classes[] = 'cols-m-'.$data['cols_m'];
        }
        ?>
        <div class="<?php echo esc_attr(implode(' ', $root_classes)); ?>" <?php echo implode(' ', $attrs); ?>>
            <div class="shopido-ajax-search__field">
                <span class="shopido-ajax-search__clear" aria-label="پاک کردن">×</span>
                <input type="search" class="shopido-ajax-search__input" placeholder="<?php echo esc_attr($data['placeholder']); ?>" autocomplete="off" aria-autocomplete="list" aria-expanded="false" />
                <span class="shopido-ajax-search__icon" aria-hidden="true">🔎</span>
            </div>
    
            <div class="shopido-ajax-search__results" role="listbox" aria-label="نتایج"></div>
    
            <?php if ($data['show_view_all']==='1'): ?>
                <a class="sas-view-all" href="<?php echo esc_url(home_url('/?s=')); ?>" target="_self" rel="nofollow">مشاهده همه نتایج</a>
            <?php endif; ?>
        </div>
        <?php
    }

}
