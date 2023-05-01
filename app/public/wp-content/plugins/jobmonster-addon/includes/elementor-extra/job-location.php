<?php

namespace noo_Elementor_Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Job_Location extends Widget_Base
{
    public function get_name()
    {
        return 'job_location';
    }

    public function get_title()
    {
        return esc_html__(' Job Location', 'noo');
    }

    public function get_icon()
    {
        return 'fa fa-tag';
    }

    public function get_categories()
    {
        return ['noo-element-widgets'];
    }

    public function get_style_depends()
    {
        return [
            'owl-carousel',
        ];
    }

    public function get_script_depends()
    {
        return [
            'owl-carousel',
            'noo-elementor',
        ];
    }

    private function get_job_location($taxonomy = 'job_location')
    {
        if (!empty($taxonomy)) {
            // Get categories for post type.
            $terms = get_terms(
                array(
                    'taxonomy' => $taxonomy,
                    'hide_empty' => true,
                )
            );
            if (!empty($terms)) {
                foreach ($terms as $term) {
                    if (isset($term)) {
                        if (isset($term->term_id) && isset($term->name)) {
                            $options[$term->term_id] = $term->name;
                        }
                    }
                }
            }
        }

        return $options;
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'job_location',
            [
                'label' => esc_html__('Job Location Options', 'noo'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'style',
            [
                'label' => esc_html__('Style', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'style-grid' => esc_html__('Style Grid', 'noo'),
                    'style-list' => esc_html__('Style List', 'noo'),
                    'style-slider' => esc_html__('Style Slider', 'noo'),
                ],
                'default' => 'style-grid',
            ]
        );
        $this->add_control(
            'style-list',
            [
                'label' => esc_html__('Select Style list', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'style-1' => esc_html__('Style 1', 'noo'),
                    'style-2' => esc_html__('Style 2', 'noo'),
                    'style-3' => esc_html__('Style 3', 'noo'),
                ],
                'default' => 'style-1',
                'condition' => [
                    'style' => 'style-list',
                ]
            ]
        );
        $this->add_control(
            'list_job_location',
            [
                'label' => esc_html__('Select Job Location', 'noo'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $this->get_job_location('job_location'),
            ]
        );
        $this->add_control(
            'hide_empty',
            [
                'label' => esc_html__('Hide Empty', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Hide', 'noo'),
                'label_off' => esc_html__('Show', 'noo'),
                'return_value' => true,
                'default' => true,

            ]
        );
        $this->add_control(
            'show_job_count',
            [
                'label' => esc_html__('Show Job Count', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'noo'),
                'label_of' => esc_html__('Hide', 'noo'),
                'return_value' => true,
                'default' => true,
            ]
        );
        $this->add_control(
            'limit_location',
            [
                'label' => esc_html__('Limit Location', 'noo'),
                'type' => Controls_Manager::NUMBER,
                'min' => 5,
                'max' => 100,
                'step' => 1,
                'default' => 8,

            ]
        );
        // Columns.
        $this->add_responsive_control(
            'columns',
            [
                'label' => '<i class="fa fa-columns"></i> ' . esc_html__('Columns', 'noo'),
                'type' => Controls_Manager::SELECT,
                'default' => 4,
                'tablet_default' => 2,
                'mobile_default' => 1,
                'options' => [
                    1 => 1,
                    2 => 2,
                    3 => 3,
                    4 => 4,
                    5 => 5,
                ],
            ]
        );
        $this->add_control(
            'auto_play',
            [
                'label' => esc_html__('Auto Play', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_off' => esc_html__('Off', 'noo'),
                'label_on' => esc_html__('On', 'noo'),
                'separator' => 'before',
                'default' => 'yes',
                'condition' => [
                    'style' => 'style-slider',
                ]
            ]
        );
        $this->add_control(
            'slider_loop',
            [
                'label' => esc_html__('Loop', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_off' => esc_html__('Off', 'noo'),
                'label_on' => esc_html__('On', 'noo'),
                'separator' => 'before',
                'default' => 'yes',
                'condition' => [
                    'style' => 'style-slider',
                ]
            ]
        );
        $this->add_control(
            'show_nav',
            [
                'label' => esc_html__('Show Navigation', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_off' => esc_html__('Off', 'noo'),
                'label_on' => esc_html__('On', 'noo'),
                'separator' => 'before',
                'default' => 'yes',
                'condition' => [
                    'style' => 'style-slider',
                ]
            ]
        );
        $this->add_control(
            'show_pagination',
            [
                'label' => esc_html__('Show Pagination', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_off' => esc_html__('Off', 'noo'),
                'label_on' => esc_html__('On', 'noo'),
                'separator' => 'before',
                'condition' => [
                    'style' => 'style-slider',
                ]
            ]
        );
        $this->add_responsive_control(
            'item_spacing',
            [
                'label' => esc_html__('Item Spacing for Grid', 'noo'),
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
                    '{{WRAPPER}} .noo-job-category-widget' => 'margin: -{{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .noo-job-category-widget .category-item' => 'padding: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render()
    {
        $settings = $this->get_settings();


        if ($settings['style'] == 'style-slider') {
            $auto_play = $settings['auto_play'] == 'yes' ? true : false;
            $slider_loop = $settings['slider_loop'] == 'yes' ? true : false;
            $show_nav = $settings['show_nav'] == 'yes' ? true : false;
            $show_pagination = $settings['show_pagination'] == 'yes' ? true : false;
            $data_slide = array(
                'items' => $settings['columns'],
                'autoplay' => $auto_play,
                'loop' => $slider_loop,
                'show_nav' => $show_nav,
                'mobilecol' => !empty($settings['columns_mobile']) ? $settings['columns_mobile'] : 1,
                'tabletcol' => !empty($settings['columns_tablet']) ? $settings['columns_tablet'] : 2,
                'dot' => $show_pagination,

            );
            $data_slide = 'data-slide="' . esc_attr(json_encode($data_slide)) . '"';
        }
        $class = 'noo-job-location-elementor noo-job-category-widget noo-job-category clearfix' . " {$settings['style']}";
        $class = ($class != '') ? ' class="' . $class . '"' : '';
        extract(array(

            'style' => $settings['style'],
            'show_job_count' => $settings['show_job_count'],
            'hide_empty' => $settings['hide_empty'],
            'limit_location' => $settings['limit_location'],
            'list_job_location' => $settings['list_job_location'],
            'desktop_class' => (!empty($settings['columns_mobile']) ? 'noo-mobile-' . $settings['columns_mobile'] : ''),
            'mobile_class' => (!empty($settings['columns_tablet']) ? 'noo-tablet-' . $settings['columns_tablet'] : ''),
            'tablet_class' => (!empty($settings['columns']) ? 'noo-desktop-' . $settings['columns'] : ''),
        ));

        $class = ($class != '') ? 'noo-job-category clearfix' . esc_attr($class) . " {$settings['style']}" : 'noo-job-category clearfix' . " {$settings['style']}";
        $class = ' class="' . $class . '"';
        ob_start();
        $link_style = jm_addon_get_elementor_template("job-location/" . $settings['style'] . '.php');
        include $link_style;
    }
}