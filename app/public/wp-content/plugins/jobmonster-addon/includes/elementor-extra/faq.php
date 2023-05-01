<?php

namespace Noo_Elementor_Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Background;

class FAQ extends Widget_Base
{
    /**
     * Get widget name.
     */
    public function get_name()
    {
        return 'noo-faq';
    }

    /**
     * Get widget title.
     */
    public function get_title()
    {
        return esc_html__('Noo FAQ', 'noo');
    }

    /**
     * Get widget icon.
     */
    public function get_icon()
    {
        return 'fa fa-code';
    }

    /**
     * Get widget categories.
     *
     */
    public function get_categories()
    {
        return ['noo-element-widgets'];
    }

    /*
    * Depend Style
    */
    public function get_style_depends()
    {
        return [
            'owl-carousel',
        ];
    }

    /*
    * Depend Script
    */
    public function get_script_depends()
    {
        return [
            'owl-carousel',
            'noo-elementor',
        ];
    }

    /**
     * Register Widget controls.
     */
    protected function register_controls()
    {
        // Tab Content
        $this->noo_faq_option();

    }

    /*
    * Config
    */
    private function noo_faq_option()
    {
        $this->start_controls_section(
            'noo-element-widgets',
            [
                'label' => esc_html__('General Options', 'noo')
            ]
        );
        $this->add_control(
            'noo_faq_group',
            [
                'label' => esc_html__('Client Item', 'noo'),
                'type' => Controls_Manager::REPEATER,
                'default' => [
                    [
                        'client_url' => ''
                    ],
                    [
                        'client_url' => ''
                    ],
                    [
                        'client_url' => ''
                    ],
                    [
                        'client_url' => ''
                    ],
                ],
                'fields' => [
                    [
                        'name' => 'open',
                        'label' => esc_html__('Open FAQ', 'noo'),
                        'type' => Controls_Manager::SELECT,
                        'options' => [
                            'open_faq' => __('Show', 'noo'),
                            'hide_faq' => __('Hidden', 'noo'),
                        ],
                        'default' => 'open_faq',
                    ],
                    [
                        'label' => esc_html__('Title', 'noo'),
                        'type' => Controls_Manager::TEXT,
                        'default' => __('FAQ', 'noo'),
                        'name' => 'title',
                    ],
                    [
                        'name' => 'description',
                        'type' => Controls_Manager::TEXTAREA,
                        'rows' => 10,
                        'default' => __('Default description', 'noo'),
                        'placeholder' => __('Type your description here', 'noo'),
                    ]
                ],
                'title_field' => esc_html__('FAQ Item', 'noo'),
            ]
        );


        $this->end_controls_section();
    }

    protected function render()
    {
        // Get settings.
        $settings = $this->get_settings();
        echo '<div class="noo-faq-elementor-widget noo-faq">';
        echo '<div class="noo_faq_group">';
        foreach ($settings['noo_faq_group'] as $value) {
            echo '<div class="noo_faq_item ' . $value['open'] . '">';
            echo '<h4 class="noo_faq_control">';
            echo $value['title'];
            echo '</h4>';
            echo '<div class="noo_faq_content">' . $value['description'] . '</div>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
    }
}