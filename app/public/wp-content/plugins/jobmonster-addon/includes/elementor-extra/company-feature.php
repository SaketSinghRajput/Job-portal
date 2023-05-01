<?php

namespace Noo_Elementor_Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Noo_Company;

class Company_Feature extends Widget_Base
{
    public function get_name()
    {
        return 'noo_company_feature';
    }

    public function get_title()
    {
        return esc_html__('Noo Company Feature', 'noo');
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
        ];
    }

    public function get_script_depends()
    {
        return [
            'owl-carousel',
            'noo-elementor',
        ];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'noo_company_feature',
            [
                'label' => esc_html__('Noo Company Feature', 'noo'),
                'tab' => Controls_Manager::TAB_CONTENT,
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
            'show_nav' => false,
            'dot' => ($settings['show_pagination'] == 'yes') ? true : false,
        );
        $data_slider = ' data-slide="' . esc_attr(json_encode($data_slide)) . '"';
        $args = array(
            'post_type' => 'noo_company',
            'post_status' => 'publish',
            'posts_per_page' => $settings['posts_per_page'],
        );
        $args['meta_query'][] = array(
            'key' => '_company_featured',
            'value' => 'yes',
        );
        $query = new \WP_Query($args);
        if ($query->have_posts()):
            ?>
            <div class="noo-company-feature-elementor-widget">
                <div class="owl-carousel featured_slider" <?php echo $data_slider ?>>
                    <?php while ($query->have_posts()) : $query->the_post();
                        global $post;
                        $logo_company = Noo_Company::get_company_logo($post->ID);
                        echo "<div class='bg_images'><a href='" . get_permalink($post->ID) . "' >{$logo_company}</a></div>";
                    endwhile; ?>
                </div>
                <?php wp_reset_query(); ?>
            </div>
        <?php else: ?>
            <h3 class="text-center"><?php _e('Nothing Found', 'noo'); ?></h3>
        <?php endif; ?>
    <?php }


}