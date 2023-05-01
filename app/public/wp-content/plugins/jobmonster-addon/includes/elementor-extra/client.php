<?php
/**
 * Noo client.
 *
 * @since 1.0.0
 */

namespace Noo_Elementor_Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Background;

class Client extends Widget_Base
{
    /**
     * Get widget name.
     */
    public function get_name()
    {
        return 'noo-client';
    }

    /**
     * Get widget title.
     */
    public function get_title()
    {
        return esc_html__('Noo Client', 'noo');
    }

    /**
     * Get widget icon.
     */
    public function get_icon()
    {
        return 'fa fa-user';
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
        $this->noo_client_option();

    }

    /*
    * Config
    */
    private function noo_client_option()
    {
        $this->start_controls_section(
            'noo_client_section',
            [
                'label' => esc_html__('General Options', 'noo')
            ]
        );
        $this->add_responsive_control(
            'columns',
            [
                'type' => Controls_Manager::SELECT,
                'label' => '<i class="fa fa-columns"></i> ' . esc_html__('Columns', 'noo'),
                'default' => 4,
                'tablet_default' => 2,
                'mobile_default' => 1,
                'options' => [
                    1 => 1,
                    2 => 2,
                    3 => 3,
                    4 => 4,
                    5 => 5,
                    6 => 6,
                ],
            ]
        );
        $this->add_control(
            'loop',
            [
                'label' => esc_html__('Loop', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_off' => esc_html__('Off', 'noo'),
                'label_on' => esc_html__('On', 'noo'),
                'default' => true,
                'return_value' => true,
            ]
        );
        $this->add_control(
            'auto_play',
            [
                'label' => esc_html__('Auto Play', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_off' => esc_html__('Off', 'noo'),
                'label_on' => esc_html__('On', 'noo'),
                'default' => true,
                'return_value' => true,
            ]
        );
        $this->add_control(
            'slider_speed',
            [
                'label' => esc_html__('Slider speed(ms)', 'noo'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['ms'],
                'range' => [
                    'ms' => [
                        'min' => 1000,
                        'max' => 8000,
                        'step' => 100,

                    ]
                ],
                'default' => [
                    'unit' => 'ms',
                    'size' => 1000,
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
                'default' => 'no',
            ]
        );
        $this->add_control(
            'show_pagination',
            [
                'label' => __('Show Dot', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_off' => esc_html__('Off', 'noo'),
                'label_on' => esc_html__('On', 'noo'),
                'default' => 'no',
            ]
        );
        $this->add_control(
            'noo_client_group',
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
                        'label' => esc_html__('Url', 'noo'),
                        'type' => Controls_Manager::URL,
                        'name' => 'client_url',
                        'placeholder' => esc_html__('https://your-link.com', 'noo'),
                        'default' => [
                            'url' => 'https://example.com'
                        ]
                    ],
                    [
                        'type' => Controls_Manager::MEDIA,
                        'name' => 'image',
                        'label' => esc_html__('Choose Image', 'noo'),
                        'dynamic' => [
                            'active' => true,
                        ],
                        'default' => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
                    ]
                ],
                'title_field' => esc_html__('Client Item', 'noo'),
            ]
        );


        $this->end_controls_section();
    }

    protected function render()
    {
        // Get settings.
        $settings = $this->get_settings();
        $client_class = 'owl-carousel noo-client';
        $auto_play = ($settings['auto_play']) ? true : false;
        $loop = ($settings['loop']) ? true : false;
        $nav = $settings['show_nav'] == 'yes' ? true : false;
        $dot = $settings['show_pagination'] == 'yes' ? true : false;
        $speed = $settings['slider_speed']['size'];
        $data_slide = array(
            'items' => $settings['columns'],
            'margin' => 0,
            'loop' => $loop,
            'autoplay' => $auto_play,
            'show_nav' => $nav,
            'dot' => $dot,
            'speed' => $speed,
        );
        $data_slide = 'data-slide="' . esc_attr(json_encode($data_slide)) . '"';


        echo '<div class="noo-client-elementor-widget">';
        echo '<div class="' . $client_class . '" ' . $data_slide . '>';
        foreach ($settings['noo_client_group'] as $value) {
            $image = wp_get_attachment_image_src($value['image']['id'], 'full');
            echo '<div class="noo-client-item">';
            $link_props = ' href="' . esc_url($value['client_url']['url']) . '" ';
            if ($value['client_url']['is_external'] === 'on') {
                $link_props .= ' target="_blank" ';
            }
            if ($value['client_url']['nofollow'] === 'on') {
                $link_props .= ' rel="nofollow" ';
            }

            echo '<a class="client-url" ' . $link_props . '>';
            echo '<img class="client-image " src="' . $image[0] . '" alt="Noo Client">';
            echo '</a>';
            echo '</div>';
        }
        echo '</div><!-- . noo-client-wrap -->';
        echo '</div><!-- .noo-client-widget -->';
    }
}