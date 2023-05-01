<?php

namespace Noo_Elementor_Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Company extends Widget_Base
{
    public function get_name()
    {
        return 'noo_company';
    }

    public function get_title()
    {
        return esc_html__('Noo Company', 'noo');
    }

    public function get_icon()
    {
        return 'fa fa-building';
    }

    public function get_categories()
    {
        return ['noo-element-widgets'];
    }

    public function get_style_depends()
    {
        return [
            'owl-carousel',
            'noo-swiper',
        ];
    }

    public function get_script_depends()
    {
        return [
            'owl-carousel',
            'noo-swiper',
            'noo-elementor',
        ];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'noo_company',
            [
                'label' => esc_html__('Noo Company', 'noo'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'featured_company',
            [
                'label' => esc_html__('Featured Company Only', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'noo'),
                'label_off' => __('No', 'noo'),
                'return_value' => true,
                'default' => true,
            ]
        );
        $this->add_control(
            'layout',
            [
                'label' => esc_html__('Layout', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'style-grid' => esc_html__('Grid', 'noo'),
                    'style-slider' => esc_html__('Slider', 'noo'),
                ],
                'default' => 'style-grid',
            ]
        );
        $this->add_control(
            'is_slider',
            [
                'label' => esc_html__('Slide Mode', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'noo'),
                'label_off' => esc_html__('No', 'noo'),
                'return_value' => true,
                'default' => true,
                'condition' => [
                    'layout' => 'style-grid',
                ],
            ]
        );
        $this->add_control(
            'posts_per_page',
            [
                'label' => esc_html__('Posts Per Page', 'noo'),
                'type' => Controls_Manager::NUMBER,
                'min' => -1,
                'max' => 100,
                'step' => 1,
                'default' => 8,
            ]
        );
        $this->add_control(
            'orderby',
            [
                'label' => esc_html__('OrderBy', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'title' => esc_html__('Title', 'noo'),
                    'job_count' => esc_html__('Job Count', 'noo'),
                ],
                'default' => 'title',
            ]
        );
        $this->add_control(
            'order',
            [
                'label' => esc_html__('Order', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'ASC' => esc_html__('ASC', 'noo'),
                    'DESC' => esc_html__('DESC', 'noo'),
                ],
                'default' => 'ASC',
            ]
        );
        $this->add_responsive_control(
            'columns',
            [
                'type' => Controls_Manager::SELECT,
                'label' => '<i class="fa fa-columns"></i> ' . esc_html__('Columns', 'noo'),
                'default' => 3,
                'tablet_default' => 2,
                'mobile_default' => 1,
                'options' => [
                    1 => 1,
                    2 => 2,
                    3 => 3,
                    4 => 4,
                ],
            ]
        );
        $this->add_control(
            'rows',
            [
                'label' => __('Rows', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '2' => __('2 Rows', 'noo'),
                    '3' => __('3 Rows', 'noo'),
                    '4' => __('4 Rows', 'noo'),
                ],
                'default' => '2',
                'condition' => [
                    'layout' => 'style-grid',
                ]
            ]
        );
        $this->add_control(
            'auto_play',
            [
                'label' => esc_html__('Auto Play', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'noo'),
                'label_off' => esc_html__('No', 'noo'),
                'return_value' => true,
                'default' => true,
            ]
        );
        $this->add_control(
            'slider_speed',
            [
                'label' => esc_html__('Slide Speed', 'noo'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['ms'],
                'range' => [
                    'ms' => [
                        'min' => 300,
                        'max' => 3000,
                        'step' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'ms',
                    'size' => 500,
                ],
                'condition' => [
                    'layout' => 'slider',
                ],
            ]
        );
        $this->add_control(
            'show_pagination',
            [
                'label' => esc_html__('Show Pagination', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'noo'),
                'label_of' => esc_html__('Hide', 'noo'),
                'return_value' => 'yes',
            ]
        );

        $this->add_responsive_control(
            'desc_spacing',
            [
                'label' => esc_html__('Item Spacing', 'noo'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 15,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .noo-company-elementor-widget' => 'margin: -{{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .noo-company-elementor-widget .noo-grid-item' => 'padding: {{SIZE}}{{UNIT}}',
                ],
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'noo_company_style',
            [
                'label' => esc_html__('Style', 'noo'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'primary_color',
            [
                'label' => esc_html__('Primary Color', 'noo'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .noo-company-widget .company-address i' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .noo-company-widget .style-1 .company-item:hover' => 'border-color: {{VALUE}}',
                    '{{WRAPPER}} .noo-company-widget .company-item:hover' => 'border-color: {{VALUE}}',

                ]
            ]
        );
        $this->end_controls_section();

    }

    protected function render()
    {
        $settings = $this->get_settings();
        $mobile_class = (!empty($settings['columns_mobile']) ? 'noo-mobile-' . $settings['columns_mobile'] : '');
        $tablet_class = (!empty($settings['columns_tablet']) ? 'noo-tablet-' . $settings['columns_tablet'] : '');
        $desktop_class = (!empty($settings['columns']) ? 'noo-desktop-' . $settings['columns'] : '');

        $data_slide = array(
            'items' => $settings['columns'],
            'mobilecol' => $settings['columns_mobile'],
            'tabletcol' => $settings['columns_tablet'],
            'margin' => 0,
            'loop' => true,
            'autoplay' => $settings['auto_play'],
            'speed' => $settings['slider_speed']['size'],
            'show_nav' => ($settings['show_pagination'] == 'yes') ? true : false,
            'rows' => absint($settings['rows']),
            'dot' => false,
        );
        $atts = array(
            'style' => $settings['layout'],
            'column' => $settings['columns'],
            'posts_per_page' => $settings['posts_per_page'],
            'featured_company' => $settings['featured_company'],
            'paginate' => ($settings['show_pagination'] == 'yes') ? true : false,
            'orderby' => $settings['orderby'],
            'order' => $settings['order'],
            'is_slider' => $settings['is_slider'],
            'data_slider' => $data_slide,
        );

        extract($atts);

        $args = array(
            'post_type' => 'noo_company',
            'post_status' => 'publish',
            'posts_per_page' => $posts_per_page,
        );
        if ($orderby == 'job_count') {
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = '_noo_job_count';
        } else {
            $args['orderby'] = $orderby;
        }
        $args['order'] = $order;
        if ($featured_company == 'yes') {
            $args['meta_query'][] = array(
                'key' => '_company_featured',
                'value' => 'yes',
            );
        }

        if ('style-slider' == $style) {
            $class = array('owl-carousel', $style);
            wp_enqueue_script('vendor-carousel');
        } else {
            $class = (!$is_slider) ? array('noo-grid-col', $style, $mobile_class, $tablet_class, $desktop_class) : '';
            if (is_front_page()) {
                $paged = get_query_var('page') ? intval(get_query_var('page')) : 1;
            } else {
                $paged = get_query_var('paged') ? intval(get_query_var('paged')) : 1;
            }
            $args['paged'] = $paged;
        }

        $c = new \WP_Query($args);
        global $wp_query;
        $wp_query = $c;

        ?>
        <div class="noo-company-elementor-widget">
            <?php
            // Include style
            $display_style = jm_addon_get_elementor_template('noo-company/' . $style . '.php');
            include $display_style;
            ?>
            <?php wp_reset_query(); ?>
            <?php
            if (1 < $wp_query->max_num_pages && $paginate && 'style-grid' == $style && !$is_slider) {
                noo_pagination(array(), $c);
            }
            ?>
        </div>

    <?php }


}