<?php

namespace Noo_Elementor_Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Companies_Listing extends Widget_Base
{
    public function get_name()
    {
        return 'noo_companies_listing';
    }

    public function get_title()
    {
        return esc_html__('Companies Listing', 'noo');
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
            'noo-rating',
        ];
    }

    public function get_script_depends()
    {
        return [
            'addon-imageloaded',
            'isotope',
            'noo-rating',
            'noo-elementor',
        ];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'noo_companies_listing',
            [
                'label' => esc_html__('Noo Companies Listing', 'noo'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'style',
            [
                'label' => esc_html__('Display Style', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'alphabet' => __('Alphabet', 'noo'),
                    'grid' => __('Grid', 'noo'),
                ],
                'default' => 'alphabet',
            ]
        );
        $this->add_control(
            'number',
            [
                'label' => __('Number Company', 'noo'),
                'type' => Controls_Manager::NUMBER,
                'min' => 5,
                'max' => 100,
                'step' => 1,
                'default' => 12,
                'condition' => [
                    'style' => 'grid',
                ]
            ]
        );
        $this->add_control(
            'show_filter',
            [
                'label' => esc_html__('Show Filter', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'noo'),
                'label_off' => __('Hidden', 'noo'),
                'return_value' => true,
                'default' => true,
            ]
        );
        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings();
        $number = (!empty($settings['number'])) ? $settings['number'] : '-1';
        if (is_front_page()) {
            $paged = get_query_var('page') ? intval(get_query_var('page')) : 1;
        } else {
            $paged = get_query_var('paged') ? intval(get_query_var('paged')) : 1;
        }
        $args = array(
            'post_type' => 'noo_company',
            'post_status' => 'publish',
            'posts_per_page' => $number,
            'order' => 'ASC',
            'paged' => $paged,
            'is_main_shortcode' => true,
        );

        $r = new \WP_Query($args);
        global $wp_query;
        $wp_query = $r;
        ob_start();
        ?>
        <div class="noo-companies-listing-elementor-widget">
            <?php
            // Include style
            $show_filter = ($settings['show_filter']) ? true : false;
            $display_style = jm_addon_get_elementor_template('noo-company/' . $settings['style'] . '.php');
            include $display_style;
            ?>
            <?php
            wp_reset_postdata();
            wp_reset_query(); ?>
        </div>
        <?php
        wp_reset_query();

    }


}