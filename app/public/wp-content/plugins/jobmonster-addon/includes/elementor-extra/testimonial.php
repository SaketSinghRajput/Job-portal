<?php

namespace Noo_Elementor_Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Noo_Company;


class Testimonial extends Widget_Base
{
    public function get_name()
    {
        return 'noo_testimonial';
    }

    public function get_title()
    {
        return esc_html__('Noo Testimonial', 'noo');
    }

    public function get_icon()
    {
        return 'fa fa-code';
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
        $this->noo_testimonial_options();
        $this->noo_testimonial_style();
    }

    private function get_post_type_categories($taxonomy = 'testimonial_category')
    {
        $options = array('all' => esc_html__('All', 'noo'));
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

    private function noo_testimonial_options()
    {
        $this->start_controls_section(
            'noo_testimonial',
            [
                'label' => esc_html__('Testimonial Option', 'noo'),
                'tab' => Controls_Manager::TAB_CONTENT,

            ]
        );
        $this->add_control(
            'style',
            [
                'label' => esc_html__('Style', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    'style-1' => esc_html__('Style 1', 'noo'),
                    'style-2' => esc_html__('Style 2', 'noo'),
                    'style-3' => esc_html__('Style 3', 'noo'),
                    'style-4' => esc_html__('Style 4', 'noo'),
                    'style-5' => esc_html__('style 5', 'noo'),
                ),
                'default' => 'style-1',
            ]
        );
        $this->add_control(
            'testimonial_category',
            [
                'label' => esc_html__('Category', 'noo'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $this->get_post_type_categories('testimonial_category'),
                'default' => 'all',
            ]
        );

        // Columns.
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
                    'style' => 'style-1',
                ]
            ]
        );
        $this->add_control(
            'auto_play',
            [
                'label' => esc_html__('Auto Play', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'noo'),
                'label_of' => esc_html__('No', 'noo'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'auto_height',
            [
                'label' => esc_html__('Auto Height', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'noo'),
                'label_of' => esc_html__('No', 'noo'),
                'return_value' => 'yes',
                'default' => 'yes',
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
        $this->end_controls_section();
    }

    private function noo_testimonial_style()
    {
        $this->start_controls_section(

            'noo_testimonial_style',
            [
                'label' => esc_html__('Testimonial Style', 'noo'),
                'tab' => Controls_Manager::TAB_STYLE,
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
                    '{{WRAPPER}} .noo-testimonial-item-wrap' => 'margin: -{{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .noo-testimonial-item-wrap' => 'padding: {{SIZE}}{{UNIT}}',
                ],
            ]
        );
        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings();
        $data_slide = array(
            'items' => $settings['columns'],
            'mobilecol' => !empty($settings['columns_mobile']) ? $settings['columns_mobile'] : 1,
            'tabletcol' => !empty($settings['columns_tablet']) ? $settings['columns_tablet'] : 2,
            'loop' => true,
            'auto_height' => $settings['auto_height'] == 'yes' ? true : false,
            'autoplay' => $settings['auto_play'] == 'yes' ? true : false,
            'speed' => $settings['slider_speed']['size'],
            'show_nav' => $settings['show_navigation'],
            'dot' => $settings['show_pagination'],
        );
        if ($settings['style'] != 'style-1') {
            $data_slide['items'] = $data_slide['mobilecol'] = $data_slide['tabletcol'] = 1;
        }
        $data_slider = ' data-slide="' . esc_attr(json_encode($data_slide)) . '"';
        ob_start();
        $args = array(
            'post_type' => 'testimonial',
            'posts_per_page' => '-1',
        );
        $testiominal_category = (is_array($settings['testimonial_category'])) ? $settings['testimonial_category'] : explode(",", $settings['testimonial_category']);

        $args['tax_query'] = array('relation' => 'AND');

        if (!in_array('all', $testiominal_category)) {
            $args['tax_query'][] = array(
                'taxonomy' => 'testimonial_category',
                'field' => 'slug',
                'terms' => $testiominal_category,
            );
        }
        $query = new \WP_Query($args);
        if ($query->have_posts()):?>
			<div class=" testimonial-widget-elementor featured_slider <?php echo $settings['style'] ?>">
			<?php
			$id = uniqid() . '_show_slider';
			echo '<div id="slider_' . $id . '" class = "owl-carousel" '.$data_slider.'>';
				while ( $query->have_posts() ): $query->the_post();
						$name = get_post_meta( get_the_ID(), '_noo_wp_post_name', true );
						$position = get_post_meta( get_the_ID(), '_noo_wp_post_position', true );
						$url      = get_post_meta( get_the_ID(), '_noo_wp_post_image', true );
						?>
						<?php if($settings['style'] == 'style-1'): ?>
                        <div class="box_testimonial">
                            <div class="box-content">
								<?php the_content(); ?>
                            </div>
                            <div class="icon"></div>
                            <div class="box-info">
                                <div class="box-info-image">
                                    <?php echo wp_get_attachment_image($url,'thumbnail-logo');?>
                                </div>
                                <div class="box-info-entry">
                                    <h4><?php echo $name; ?></h4>
                                    <h5><?php echo $position ?></h5>
                                </div>
                            </div>
                        </div>
                        <?php elseif ($settings['style'] == 'style-3'): ?>
                         <div class="box_testimonial_single2">
                                <div class="box-info">
                                    <div class="box-info-image">
                                        <?php echo wp_get_attachment_image($url,'thumbnail-logo');?>
                                     </div>
                                </div>
                                <div class="box-content">
								    <?php the_content(); ?>
                                    <div class="box-info-entry">
                                        <h4><?php echo $name;?><span><?php echo ' - '.$position ?></span></h4>
                                    </div>
                                </div>
                             </div>
                        <?php else: ?>
                         <div class="box_testimonial_single">
                            <div class="box-info">
                                <div class="box-info-image">
                                    <?php echo wp_get_attachment_image($url,'thumbnail-logo');?>
                                </div>
                                <div class="box-info-entry">
                                    <h4><?php echo $name; ?></h4>
                                    <h5><?php echo $position ?></h5>
                                </div>
                            </div>
                            <div class="box-content">
								<?php the_content(); ?>
                            </div>
                        </div>
						<?php endif;

				endwhile;
			wp_reset_query();
			echo '</div>
	 			<div class="clearfix"></div>
	 		</div>';
        endif;
        $content = ob_get_contents();
        ob_clean();
        ob_end_flush();
        echo $content;
    }
}