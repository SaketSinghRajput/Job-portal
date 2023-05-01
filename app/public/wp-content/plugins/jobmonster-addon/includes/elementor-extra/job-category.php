<?php

namespace noo_Elementor_Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Job_Category extends Widget_Base
{
    public function get_name()
    {
        return 'job_category';
    }

    public function get_title()
    {
        return esc_html__('Job Category', 'noo');
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

    private function get_job_categories($taxonomy = 'job_category')
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
            'job_category',
            [
                'label' => esc_html__('Job Category Options', 'noo'),
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
            'list_job_category',
            [
                'label' => esc_html__('Select Job Category', 'noo'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $this->get_job_categories('job_category'),
            ]
        );
        $this->start_controls_tabs( 'noo_advanced_style' );
        $this->start_controls_tab(
            'button_normal',
            [
                'label' => esc_html__( 'Normal', 'noo' ),
            ]
        );

        $this->add_control(
           'color_title',
           [
               'label'  => esc_html__('Text and Icon Color', 'noo'),
               'type'   => Controls_Manager::COLOR,
               'selectors' => [
                    '{{WRAPPER}} .noo-job-category .category-item .icon' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .noo-job-category .category-item .title' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .noo-job-category .category-item .job-count' => 'color: {{VALUE}}',
               ],
           ]
        );
        $this->end_controls_tab();

        $this->start_controls_tab(
            'button_hover',
            [
                'label' => esc_html__( 'Hover', 'noo' ),
            ]
        );

        $this->add_control(
           'color_title_hover',
           [
               'label'  => esc_html__('Text and Icon Color', 'noo'),
               'type'   => Controls_Manager::COLOR,
               'selectors' => [
                    '{{WRAPPER}} .noo-job-category .category-item:hover .icon' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .noo-job-category .category-item:hover .title' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .noo-job-category .category-item:hover .job-count' => 'color: {{VALUE}}',
               ],
           ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

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
            'limit_category',
            [
                'label' => esc_html__('Limit Category', 'noo'),
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
                    '{{WRAPPER}} .noo-job-category-widget .noo-grid-item ' => 'padding: {{SIZE}}{{UNIT}}',
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
        $class = 'noo-job-category-elementor noo-job-category-widget noo-job-category clearfix' . " {$settings['style']}";
        extract(array(

            'style' => $settings['style'],
            'show_job_count' => $settings['show_job_count'],
            'hide_empty' => $settings['hide_empty'],
            'limit_category' => $settings['limit_category'],
            'list_job_category' => $settings['list_job_category'],
            'desktop_class' => (!empty($settings['columns_mobile']) ? 'noo-mobile-' . $settings['columns_mobile'] : ''),
            'mobile_class' => (!empty($settings['columns_tablet']) ? 'noo-tablet-' . $settings['columns_tablet'] : ''),
            'tablet_class' => (!empty($settings['columns']) ? 'noo-desktop-' . $settings['columns'] : ''),
        ));

        $class = ($class != '') ? 'noo-job-category clearfix' . esc_attr($class) . " {$settings['style']}" : 'noo-job-category clearfix' . " {$settings['style']}";
        $class = ' class="' . $class . '"';
        $link_style = jm_addon_get_elementor_template("job-category/" . $settings['style'] . '.php');
        include $link_style;
    }
}