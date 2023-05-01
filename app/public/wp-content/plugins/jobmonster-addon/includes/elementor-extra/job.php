<?php

namespace Noo_Elementor_Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Job extends Widget_Base
{
    public function get_name()
    {
        return 'noo_job';
    }

    public function get_title()
    {
        return esc_html__('Noo Jobs', 'noo');
    }

    public function get_icon()
    {
        return 'fa fa-briefcase';
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

    private function get_job_taxonomy($taxonomy = 'job_category')
    {
        $options = array();
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
                        if (isset($term->slug) && isset($term->name)) {
                            $options[$term->slug] = $term->name;
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
            'noo_job',
            [
                'label' => esc_html__('Noo Job', 'noo'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'show_job',
            [
                'label' => esc_html__('Show', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'recent' => esc_html__('Recent', 'noo'),
                    'featured' => esc_html__('Featured', 'noo'),
                ],
                'default' => 'recent',
            ]
        );
        $this->add_control(
            'display_style',
            [
                'label' => esc_html__('Layout', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'grid' => esc_html__('Grid', 'noo'),
                    'list' => esc_html__('List', 'noo'),
                    'slider' => esc_html__('Slider', 'noo'),
                    'list2' => esc_html__('List Column', 'noo'),
                ],
                'default' => 'list',
            ]
        );

        $this->add_control(
            'slider_style',
            [
                'label' => esc_html__('Style', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'style-1' => esc_html__('Style 1', 'noo'),
                    'style-2' => esc_html__('Style 2', 'noo'),
                ],
                'default' => 'style-1',
                'condition' => [
                    'display_style' => array('slider'),
                ]
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
                ],
                'condition' => [
                    'display_style' => ['grid', 'slider']
                ]
            ]
        );
        $this->add_control(
            'posts_per_page',
            [
                'label' => esc_html__('Posts Per Page', 'noo'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 100,
                'step' => 1,
                'default' => 6

            ]
        );
        $this->add_control(
            'job_category',
            [
                'label' => esc_html__('Job Category', 'noo'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $this->get_job_taxonomy('job_category'),
            ]
        );
        $this->add_control(
            'job_type',
            [
                'label' => esc_html__('Job Type', 'noo'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $this->get_job_taxonomy('job_type'),
            ]
        );
        $this->add_control(
            'job_location',
            [
                'label' => esc_html__('Job Location', 'noo'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $this->get_job_taxonomy('job_location'),
            ]
        );
        $this->add_control(
            'auto_play',
            [
                'label' => esc_html__('Show Auto Play', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'noo'),
                'label_off' => esc_html__('Hide', 'noo'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'display_style' => 'slider',
                ]
            ]
        );
        $this->add_control(
            'slide_speed',
            [
                'label' => esc_html__('Slide Speed(ms)', 'noo'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['ms'],
                'range' => [
                    'ms' => [
                        'min' => 100,
                        'max' => 3000,
                        'step' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'ms',
                    'size' => 500,
                ],
                'condition' => [
                    'display_style' => 'slider',
                ]
            ]
        );
        $this->add_control(
            'orderby',
            [
                'label' => esc_html__('Order by', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'date' => esc_html__('Date', 'noo'),
                    'view' => esc_html__('View', 'noo'),
                    'featured' => esc_html__('Featured', 'noo'),
                    'rand' => esc_html__('Random', 'noo'),
                ],
                'default' => 'date',
            ]
        );
        $this->add_control(
            'order',
            [
                'label' => esc_html__('Sort By', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'desc' => esc_html__('Recent', 'noo'),
                    'asc' => esc_html__('Older', 'noo'),
                ],
                'default' => 'desc',
            ]
        );
        $this->add_control(
            'show_pagination',
            [
                'label' => esc_html__('Show Pagination', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'noo'),
                'label_off' => esc_html__('None', 'noo'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'show_navigation',
            [
                'label' => esc_html__('Show Navigation', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'noo'),
                'label_off' => esc_html__('None', 'noo'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'display_style' => 'slider',
                ]
            ]
        );
        $this->add_control(
            'choice_paginate',
            [
                'label' => esc_html__('Choice Type Paginate', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'nextajax' => __('Style1', 'noo'),
                    '' => __('Style2', 'noo')
                ],
                'default' => '',
                'condition' => [
                    'show_pagination' => 'yes',
                ]
            ]
        );
        $this->add_control(
            'show_view_more',
            [
                'label' => esc_html__('Show View More', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'noo'),
                'label_off' => esc_html__('Hide', 'noo'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'visibility',
            [
                'label' => esc_html__('Visibility', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'all' => esc_html__('All Devices', 'noo'),
                    'hidden-phone' => esc_html__('Hidden phone', 'noo'),
                    'hidden-tablet' => esc_html__('Hidden Tablet', 'noo'),
                    'hidden-pc' => esc_html__('Hidden PC', 'noo'),
                    'visible-phone' => esc_html__('Visible Phone', 'noo'),
                    'visible-tablet' => esc_html__('Visible Tablet', 'noo'),
                    'visible-pc' => esc_html__('Visible PC', 'noo'),
                ],
                'default' => 'all',
            ]
        );
        $this->add_responsive_control(
            'item_spacing',
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
                    '{{WRAPPER}} .job-elementor-widget' => 'margin: -{{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .job-elementor-widget .noo-grid-item' => 'padding: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'display_style' => 'grid',
                ]
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'noo_job_style',
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
                    '{{WRAPPER}} .loop-item-content .content-meta .job-location i,{{WRAPPER}} .job-location a:hover,{{WRAPPER}} .job-company a:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .noo-job-item:hover' => 'border-color: {{VALUE}};',
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
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $posts_per_page = $settings['posts_per_page'];
        $show_job = $settings['show_job'];
        $display_style = $settings['display_style'];
        $job_category = $settings['job_category'];
        $job_location = $settings['job_location'];
        $job_type = $settings['job_type'];
        $orderby = $settings['orderby'];
        $order = $settings['order'];
        $show_view_more = $settings['show_view_more'];
//      $show_job_tool   =  $settings['show_job_tool'];
        $atts = array(
            'mobile_class' => $mobile_class,
            'tablet_class' => $tablet_class,
            'desktop_class' => $desktop_class,
        );
        $data_slide = array(
            'items' => $settings['columns'],
            'mobilecol' => !empty($settings['columns_mobile']) ? $settings['columns_mobile'] : 1,
            'tabletcol' => !empty($settings['columns_tablet']) ? $settings['columns_tablet'] : 2,
            'loop' => true,
            'auto_height' => false,
            'autoplay' => $settings['auto_play'],
            'speed' => $settings['slide_speed']['size'],
            'show_nav' => ($settings['show_navigation'] == 'yes') ? true : false,
            'dot' => ($settings['show_pagination'] == 'yes') ? true : false,
        );

        $args = array(
            'post_type' => 'noo_job',
            'post_status' => 'publish',
            'paged' => $paged,
            'posts_per_page' => $posts_per_page,
            'ignore_sticky_posts' => true,
        );
        //-- tax_query
        $args['tax_query'] = array('relation' => 'AND');
        if (!empty($job_category)) {
            $args['tax_query'][] = array(
                'taxonomy' => 'job_category',
                'field' => 'slug',
                'terms' => $job_category,
            );
        }

        if (!empty($job_type)) {
            $args['tax_query'][] = array(
                'taxonomy' => 'job_type',
                'field' => 'slug',
                'terms' => $job_type,
            );
        }
        if (!empty($job_location)) {
            $args['tax_query'][] = array(
                'taxonomy' => 'job_location',
                'field' => 'slug',
                'terms' => $job_location,
            );
        }
        //check order by
        if ($orderby == 'view') {
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = '_noo_views_count';
        } elseif ($orderby == 'date') {
            $args['orderby'] = 'date';
        } elseif ($orderby == 'featured') {
            $args['orderby'] = 'meta_value_post_date';
            $args['meta_key'] = '_featured';
        } else {
            $args['orderby'] = 'rand';
        }
        // check order
        if ($orderby != 'rand') {
            if ($order == 'asc') {
                $args['order'] = 'ASC';
            } else {
                $args['order'] = 'DESC';
            }
        }
        if ($show_job == 'featured') {
            $args['meta_query'][] = array(
                'key' => '_featured',
                'value' => 'yes'
            );
        }
        $r = new \WP_Query($args);
        $atts['ajax_item'] = defined('DOING_AJAX') && DOING_AJAX && isset($_POST['action']) && $_POST['action'] === 'noo_nextajax';
        $atts['query'] = $r;
        $atts['data_slider'] = $data_slide;
        $atts['show'] = $show_job;
        $atts['item_class'] = 'nextajax-item';
        $atts['pagination'] = $settings['show_pagination'] == 'yes' ? 1 : 0;
        $atts['paginate_data'] = array(
            'posts_per_page' => $posts_per_page,
            'job_category' => $job_category,
            'job_type' => $job_type,
            'job_location' => $job_location,
            'orderby' => $orderby,
            'order' => $order,
        );
        $atts['paginate'] = ($settings['show_pagination'] == 'yes') ? $settings['choice_paginate'] : '';
        $atts['show_view_more'] = $show_view_more;
        $atts['show_autoplay'] = ($settings['auto_play'] == 'yes') ? true : false;
        $atts['slider_speed'] = $settings['slide_speed']['size'];
        $atts['style'] = $settings['slider_style'];
        $atts['list_column'] = $settings['columns'];
        $atts['display_style'] = $display_style;
        $atts['featured'] = $show_job;
        $atts['class'] = 'jobs_shortcode noo-job-widget ';
        $atts['no_content'] = 'text';
        $atts['is_elementor'] = true;
        echo '<div class="job-elementor-widget">';
        jm_addon_job_loop($atts);
        echo '</div>';

    }
}