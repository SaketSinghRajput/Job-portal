<?php

namespace noo_Elementor_Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Job_Category_Listing extends Widget_Base
{
    public function get_name()
    {
        return 'job_category_listing';
    }

    public function get_title()
    {
        return esc_html__('Job Category Listing', 'noo');
    }

    public function get_icon()
    {
        return 'fa fa-tag';
    }

    public function get_categories()
    {
        return ['noo-element-widgets'];
    }

    public function get_script_depends()
    {
        return [
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
            'job_category_listing',
            [
                'label' => esc_html__('Job Category Options', 'noo'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'style_job_category',
            [
                'label' => esc_html__('Style', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'style-1' => esc_html__('Style 1', 'noo'),
                    'style-2' => esc_html__('Style 2', 'noo'),
                    'style-3' => esc_html__('Style 3', 'noo'),
                ],
                'default' => 'style-1',
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
            'viewmore_url',
            [
                'label' => __('Link', 'noo'),
                'type' => Controls_Manager::URL,
                'placeholder' => __('https://your-link.com', 'noo'),
                'show_external' => true,
                'default' => [
                    'url' => '',
                    'is_external' => true,
                    'nofollow' => true,
                ],
            ]
        );
        $this->add_control(
            'url_title',
            [
                'label' => __('url title', 'noo'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __('Type label button here', 'noo'),
                'default' => __('View More', 'noo'),
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
                    '{{WRAPPER}} .noo-job-category-listing-widget' => 'margin: -{{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .noo-job-category-listing-widget .noo-grid-item ' => 'padding: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render()
    {
        $settings = $this->get_settings();
        $class = 'noo-list-job-category clearfix noo-job-category-listing-widget';
        $class = ($class != '') ? ' class="' . $class . '"' : '';
        $list_column = (!empty($list_column)) ? $list_column : '4';
        $desktop_class = (!empty($settings['columns_mobile']) ? 'noo-mobile-' . $settings['columns_mobile'] : '');
        $mobile_class = (!empty($settings['columns_tablet']) ? 'noo-tablet-' . $settings['columns_tablet'] : '');
        $tablet_class = (!empty($settings['columns']) ? 'noo-desktop-' . $settings['columns'] : '');

        ?>
        <div<?php echo($class); ?>>
            <div class="noo-list-job-category-content noo-list-job-category-col-<?php echo esc_attr($list_column) . ' ' . $settings['style_job_category']; ?> <?php echo esc_attr($mobile_class . ' ' . $tablet_class . ' ' . $desktop_class); ?> ">
                <ul class="noo-grid-col">
                    <?php
                    $i = 0;

                    if ($settings['list_job_category'] == 'all' or $settings['list_job_category'] == '') {
                        $categories = get_terms('job_category', array(
                            'orderby' => 'NAME',
                            'order' => 'ASC',
                            'hide_empty' => ('true' == $settings['hide_empty']) ? false : true,
                        ));
                        foreach ($categories as $key => $cat) :
                            if ($i >= $settings['limit_category']) {
                                break;
                            }

                            $cate_name = $cat->name;
                            $job_count = $cat->count;
                            $cate_link = get_term_link($cat);
                            ?>
                            <li class="noo-grid-item">
                                <a href="<?php echo esc_url($cate_link); ?>"><?php echo esc_html($cate_name); ?>
                                    <?php if ('true' == $settings['show_job_count']) : ?>
                                        <span class="job-count">(<?php echo sprintf(_n('%s Job', '%s Jobs', $job_count, 'noo'), $job_count); ?>
                                            )</span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <?php $i++; endforeach;
                    } else {
                        $list_cat = explode(',', $settings['list_job_category']);
                        foreach ($list_cat as $key => $cat) :
                            $cate = get_term_by('id', absint($cat), 'job_category');
                            if (!empty($cate)):
                                if ($i >= $settings['limit_category']) {
                                    continue;
                                }

                                $cate_name = $cate->name;
                                $job_count = $cate->count;
                                $cate_link = get_term_link($cate);
                                ?>
                                <li class="noo-grid-item">
                                    <a href="<?php echo esc_url($cate_link); ?>"><?php echo esc_html($cate_name); ?>
                                        <?php if ('true' == $settings['show_job_count']) : ?>
                                            <span class="job-count">(<?php echo sprintf(_n('%s Job', '%s Jobs', $job_count, 'noo'), $job_count); ?>
                                                )</span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            <?php
                            endif;
                            $i++; endforeach;
                    }
                    ?>
                </ul>
            </div>
            <?php
            $link_url = (!empty($settings['viewmore_url']['url'])) ? $settings['viewmore_url']['url'] : '';
            if (!empty($link_url)) {
                echo '<div class="view-more"><a class="btn btn-primary" href="' . esc_url($link_url) . '">' . esc_html($settings['url_title']) . '</a></div>';
            }
            ?>
        </div>
        <?php
    }
}