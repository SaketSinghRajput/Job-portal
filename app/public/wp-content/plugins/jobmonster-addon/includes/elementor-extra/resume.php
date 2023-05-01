<?php

namespace Noo_Elementor_Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Resume extends Widget_Base
{
    public function get_name()
    {
        return 'noo_resume';
    }

    public function get_title()
    {
        return esc_html__('Noo Resumes', 'noo');
    }

    public function get_icon()
    {
        return 'fa fa-graduation-cap';
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

    private function get_taxonomy($taxonomy = 'job_category')
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
            'noo_resume',
            [
                'label' => esc_html__('Noo Resume', 'noo'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'resume_layout',
            [
                'label' => esc_html__('Layout', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'list' => esc_html__('List', 'noo'),
                    'grid' => esc_html__('Grid', 'noo'),
                ],
                'default' => 'list',
            ]
        );
        $this->add_control(
            'is_slider',
            [
                'label' => esc_html__('Slide Mode', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'noo'),
                'label_off' => esc_html__('No', 'noo'),
                'return_value' => 'yes',
                'default' => 'no',
                'condition' => [
                    'resume_layout' => 'grid',
                ],
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
                'condition' => [
                    'resume_layout' => ['grid']
                ]
            ]
        );
        $this->add_control(
            'rows',
            [
                'label' => esc_html__('Rows', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '2' => __('2 Rows', 'noo'),
                    '3' => __('3 Rows', 'noo'),
                    '4' => __('4 Rows', 'noo'),
                ],
                'default' => '2',
                'condition' => [
                    'is_slider' => 'yes',
                    'resume_layout' => 'grid',
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
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'resume_layout' => 'grid',
                    'is_slider' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'slider_speed',
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
            'show_pagination',
            [
                'label' => esc_html__('Show Pagination', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('show', 'noo'),
                'label_off' => esc_html__('Hide', 'noo'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'resume_layout' => 'grid',
                    'is_slider' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'posts_per_page',
            [
                'label' => esc_html__('Posts per page', 'noo'),
                'type' => Controls_Manager::NUMBER,
                'min' => -1,
                'max' => 100,
                'step' => 1,
                'default' => 6,
            ]
        );
        $this->add_control(
            'job_category',
            [
                'label' => esc_html__('Job Category', 'noo'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $this->get_taxonomy('job_category'),
            ]
        );
        $this->add_control(
            'job_location',
            [
                'label' => esc_html__('Job Location', 'noo'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $this->get_taxonomy('job_location'),
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
                    '{{WRAPPER}} .noo-resume-elementor-widget' => 'margin: -{{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .noo-resume-elementor-widget .noo-grid-item' => 'padding: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .noo-resume-elementor-widget .noo-resume-item' => 'padding: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'noo_resume_style',
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
                    '{{WRAPPER}} .noo-resume-grid .noo-resume-item .total-point-grid' => 'background: {{VALUE}}',
                    '{{WRAPPER}} .noo-resume-grid .noo-resume-item:after' => 'background-color: {{VALUE}}',
                ]
            ]
        );
        $this->end_controls_section();

    }

    protected function render()
    {
        $settings = $this->get_settings();
        // Grid
        $mobile_class = (!empty($settings['columns_mobile']) ? 'noo-mobile-' . $settings['columns_mobile'] : '');
        $tablet_class = (!empty($settings['columns_tablet']) ? 'noo-tablet-' . $settings['columns_tablet'] : '');
        $desktop_class = (!empty($settings['columns']) ? 'noo-desktop-' . $settings['columns'] : '');
        // slider
        $data_slide = array(
            'items' => absint($settings['columns']),
            'mobilecol' => !empty($settings['columns_mobile']) ? absint($settings['columns_mobile']) : 1,
            'tabletcol' => !empty($settings['columns_tablet']) ? absint($settings['columns_tablet']) : 2,
            'autoplay' => $settings['auto_play'],
            'speed' => $settings['slider_speed']['size'],
            'show_nav' => ($settings['show_pagination'] == 'yes') ? true : false,
            'rows' => absint($settings['rows']),
        );

        $paged = 1;
        $posts_per_page = $settings['posts_per_page'];
        $display_style = $settings['resume_layout'];
        $job_category = $settings['job_category'];
        $job_location = $settings['job_location'];
        $orderby = $settings['orderby'];
        $order = $settings['order'];
        if (defined('DOING_AJAX') && DOING_AJAX && isset($_POST['action']) && $_POST['action'] === 'noo_resume_nextajax') {
            $paged = isset($_POST['page']) ? absint($_POST['page']) : 1;
            $posts_per_page = isset($_POST['posts_per_page']) ? absint($_POST['posts_per_page']) : $posts_per_page;
            $display_style = isset($_POST['resume_layout']) ? $_POST['resume_layout'] : $display_style;
            $job_category = isset($_POST['job_category']) ? $_POST['job_category'] : $job_category;
            $job_location = isset($_POST['job_location']) ? $_POST['job_location'] : $settings['job_location'];
            $orderby = isset($_POST['orderby']) ? $_POST['orderby'] : $orderby;
            $order = isset($_POST['order']) ? $_POST['order'] : $order;
        } else {
            if (is_front_page() || is_home()) {
                $paged = (get_query_var('paged')) ? get_query_var('paged') : ((get_query_var('page')) ? get_query_var('page') : 1);
            } else {
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            }
        }
        $args = array(
            'post_type' => 'noo_resume',
            'post_status' => 'publish',
            'paged' => $paged,
            'posts_per_page' => $posts_per_page,
        );
        $args['meta_query'] = array();
        $temp_meta_query = array('relation' => 'OR');
        if (!empty($job_category)) {
            foreach ($job_category as $cat) {
                if (empty($cat)) continue;
                $temp_meta_query[] = array(
                    'key' => 'job_category',
                    'value' => '"' . $cat . '"',
                    'compare' => 'LIKE'
                );
            }
        }
        if (!empty($job_location)) {
            foreach ($job_location as $loc) {
                $temp_meta_query[] = array(
                    'key' => 'job_location',
                    'value' => '"' . $loc . '"',
                    'compare' => 'LIKE'
                );
            }
        }
        $args['meta_query'] = $temp_meta_query;

        //  -- Check order by......

        if ($orderby == 'view') {
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = '_noo_views_count';
        } else {
            $args['orderby'] = 'date';
        }

        //  -- Check order

        if ($order == 'asc') {
            $args['order'] = 'ASC';
        } else {
            $args['order'] = 'DESC';
        }
        $can_view_resume_list = jm_can_view_resumes_list();
        if ($can_view_resume_list) {
            $r = new \WP_Query($args); ?>
            <div class="noo-resumes noo-resume-elementor-widget">
                <?php
                jm_addon_resume_loop(array(
                    'query' => $r,
                    'column' => $settings['columns'],
                    'autoplay' => $settings['auto_play'] == 'yes' ? true : false,
                    'slider_speed' => $settings['slider_speed']['size'],
                    'show_pagination' => ($settings['show_pagination'] == 'yes') ? true : false,
                    'paginate' => 'resume_nextajax',
                    'resume_style' => $settings['resume_layout'],
                    'posts_per_page' => $posts_per_page,
                    'job_category' => $job_category,
                    'job_location' => $job_location,
                    'orderby' => $orderby,
                    'order' => $order,
                    'is_elementor' => true,
                    'is_slider' => ($settings['is_slider'] == 'yes') ? true : false,
                    'data_slider' => $data_slide,
                    'mobile_class' => $mobile_class,
                    'tablet_class' => $tablet_class,
                    'desktop_class' => $desktop_class,
                ));
                ?>
            </div>
            <?php
        } else {
            list($title, $link) = jm_cannot_view_list_resume();
            ?>
            <article class="resume">
                <h3><?php echo $title; ?></h3>
                <?php if (!empty($link)) echo $link; ?>
            </article>
            <?php
        }
        wp_reset_query();
    }
}