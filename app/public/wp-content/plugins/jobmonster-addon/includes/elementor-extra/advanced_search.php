<?php

namespace Noo_Elementor_Widgets;

use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

class Advanced_Search extends Widget_Base
{
    public function get_name()
    {
        return 'noo_advanced_search';
    }

    public function get_title()
    {
        return esc_html__('Noo Advanced Search', 'noo');
    }

    public function get_icon()
    {
        return 'fa fa-search';
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
            'noo-elementor',
            'owl-carousel',
        ];
    }

    private function get_resume_search_field()
    {
        $resume_custom_fields = jm_get_resume_search_custom_fields();
        $resume_search_field = array(
            'no' => esc_html__('None', 'noo'),
        );
        if ($resume_custom_fields) {
            foreach ($resume_custom_fields as $k => $field) {
                if (isset($field['is_default'])) {
                    $label = (isset($field['label_translated']) ? $field['label_translated'] : (isset($field['label']) ? $field['label'] : $k));
                    $id = $field['name'];
                    $resume_search_field[$id] = $label;
                } else {
                    $label = esc_html__('Custom Field: ', 'noo') . (isset($field['label_translated']) ? $field['label_translated'] : (isset($field['label']) ? $field['label'] : $k));

                    $id = jm_resume_custom_fields_name($field['name'], $field);
                    $resume_search_field[$id] = $label;
                }
            }
        }
        return $resume_search_field;
    }

    private function get_job_search_field()
    {
        $search_field = array(
            'no' => esc_html__('None', 'noo'),
        );
        $custom_fields = jm_get_job_search_custom_fields();
        if ($custom_fields) {
            foreach ($custom_fields as $k => $field) {
                if (isset($field['is_default'])) {
                    $label = isset($field['label']) ? $field['label'] : $k;
                    $id = $field['name'];
                    $search_field[$id] = $label;
                } else {
                    $label = esc_html__('Custom Field: ', 'noo') . (isset($field['label_translated']) ? $field['label_translated'] : (isset($field['label']) ? $field['label'] : $k));
                    $id = jm_job_custom_fields_name($field['name'], $field);
                    $search_field[$id] = $label;
                }
            }
        }
        return $search_field;
    }

    private function get_list_all_revolution_sliders_aliases()
    {
        $my_sliders_array = array();

        if (class_exists('RevSlider')) {
            $rev_slider = new \RevSlider();
            $sliders = $rev_slider->getArrSliders();
            foreach ($sliders as $slider) {
                $my_sliders_array[$slider->getAlias()] = $slider->getTitle();
            }
            return $my_sliders_array;
        } else {
            $sliders = array();
        }

        return $sliders;


    }

    private function get_map_style()
    {
        $map_type = jm_get_location_setting('map_type', 'google');
        if ($map_type == 'google') {
            $map_style = array(
                'none' => esc_html__('None', 'noo'),
                'dark' => esc_html__('Style Dark', 'noo'),
                'apple' => esc_html__('Style Apple', 'noo'),
                'nature' => esc_html__('Style Nature', 'noo'),
                'light' => esc_html__('Style Light', 'noo'),
            );
        } else {
            $map_style = array(
                'none' => esc_html__('None', 'noo'),
                'road' => esc_html__('Style Road', 'noo'),
                'aerial' => esc_html__('Style Aerial', 'noo'),
                'grayscale' => esc_html__('Style GrayScale', 'noo'),
                'birdseye' => esc_html__('Style Birdseye', 'noo'),
                'canvasLight' => esc_html__('Style CanvasLight', 'noo'),
                'canvasDark' => esc_html__('Style CanvasDark', 'noo'),
            );
        }
        return $map_style;
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'noo_advanced_search',
            [
                'label' => esc_html__('Noo Advanced Search', 'noo'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'title',
            [
                'label' => esc_html__('Main Title', 'noo'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Find Jobs', 'noo'),
                'placeholder' => esc_html__('type your title here', 'noo'),
            ]
        );
        $this->add_control(
            'sub_title',
            [
                'label' => esc_html__('Sub Title', 'noo'),
                'type' => Controls_Manager::TEXTAREA,
                'rows' => 5,
                'default' => esc_html__('Join us & Explore thousands of Jobs', 'noo'),
                'placeholder' => esc_html__('Type your description here', 'noo'),
            ]
        );
        $this->add_control(
            'content',
            [
                'label' => esc_html__(' HTML Content', 'noo'),
                'type' => Controls_Manager::CODE,
                'language' => 'html',
                'rows' => 20,
            ]
        );

        $this->add_control(
            'align_title',
            [
                'label' => esc_html__('Align', 'noo'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'noo'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'noo'),
                        'icon' => 'fa fa-align-center',
                    ],
                    'text-right' => [
                        'title' => esc_html__('Right', 'noo'),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'default' => 'center',
            ]

        );
        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Color Title', 'noo'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .search-main-title' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'label' => esc_html__('Typography Title', 'noo'),
                'selector' => '{{WRAPPER}} .search-main-title',
            ]
        );
        $this->add_control(
            'sub_title_color',
            [
                'label' => esc_html__('Color Sub Title', 'noo'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .search-sub-title' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'sub_title_typography',
                'label' => esc_html__('Typography Sub Title', 'noo'),
                'selector' => '{{WRAPPER}} .search-sub-title',
            ]
        );
        $this->add_control(
            'search_mode',
            [
                'label' => esc_html__('Search Form Layout', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'noo_horizontal' => esc_html__('Horizontal', 'noo'),
                    'noo_vertical' => esc_html__('Vertical', 'noo'),
                ],
                'default' => 'noo_horizontal',
            ]
        );
        $this->add_control(
            'style_horizontal',
            [
                'label' => esc_html__('Horizontal Style', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'style-1' => esc_html__('Border', 'noo'),
                    'style-2' => esc_html__('No Border', 'noo'),
                    'style-3' => esc_html__('Background', 'noo'),
                ],
                'default' => 'style-1',
                'condition' => [
                    'search_mode' => 'noo_horizontal',
                ],
            ]
        );
        $this->add_control(
            'background_type',
            [
                'label' => esc_html__('Background Type', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'revo_slider' => esc_html__('Revolution Slider', 'noo'),
                    'slider' => esc_html__('Image Slider', 'noo'),
                    'image' => esc_html__('One Image', 'noo'),
                    'map' => esc_html__('Map', 'noo'),
                    'no_background' => esc_html__('None', 'noo'),
                ],
                'default' => 'image',
            ]
        );
        $this->add_control(
            'map_post_type',
            [
                'label' => esc_html__('Select Post Type Map', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'resume_map' => esc_html__('Resume Map', 'noo'),
                    'job_map' => esc_html__('Job Map', 'noo'),
                ],
                'default' => 'job_map',
                'condition' => [
                    'background_type' => 'map',
                ]
            ]
        );
        $this->add_responsive_control(
            'map_height_custom',
            [
                'label' => esc_html__('Map Max Height (px)', 'noo'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 400,
                        'max' => 1200,
                        'step' => 10,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 800,
                ],
                'condition' => [
                    'background_type' => 'map',
                ],
                'selectors' => [
                    '{{WRAPPER}} #gmap ,#bmap' => 'height:{{SIZE}}{{UNIT}};;',
                ],
            ]
        );
        $this->add_control(
            'map_style',
            [
                'label' => esc_html__('Style Map', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => $this->get_map_style(),
                'default' => 'none',
                'condition' => [
                    'background_type' => 'map',
                ]
            ]
        );
        $this->add_control(
            'revo_slider_id',
            [
                'label' => esc_html__('Revolution Slider', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => $this->get_list_all_revolution_sliders_aliases(),
                'default' => 'slider',
                'condition' => [
                    'background_type' => 'revo_slider',
                ]
            ]
        );

        $this->add_responsive_control(
            'search_position',
            [
                'label' => esc_html__('Content bottom Position', 'noo'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => -500,
                        'max' => 1000,
                        'step' => 10,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 330,
                ],
                'selectors' => [
                    '{{WRAPPER}} .noo-job-search-wrapper .job-advanced-search' => 'top: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'slider_images',
            [
                'label' => esc_html__('Slider Images', 'noo'),
                'type' => Controls_Manager::GALLERY,
                'default' => [],
                'condition' => [
                    'background_type' => 'slider',
                ],
            ]
        );
        $this->add_control(
            'slider_time',
            [
                'label' => esc_html__('Slider Time(ms)', 'noo'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['ms'],
                'range' => [
                    'ms' => [
                        'min' => 500,
                        'max' => 8000,
                        'step' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'ms',
                    'size' => 3000,
                ],
                'condition' => [
                    'background_type' => 'slider',
                ]
            ]
        );
        $this->add_control(
            'slider_speed',
            [
                'label' => esc_html__('Slider Speed(ms)', 'noo'),
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
                    'size' => 600,
                ],
                'condition' => [
                    'background_type' => 'slider',
                ]
            ]
        );
        $this->add_control(
            'slider_height',
            [
                'label' => esc_html__('Slider Max Height(px)', 'noo'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 400,
                        'max' => 1200,
                        'step' => 20,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 600,
                ],
                'condition' => [
                    'background_type' => 'slider',
                ]
            ]
        );
        $this->add_control(
            'background_image',
            [
                'label' => esc_html__('Background Image', 'noo'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'background_type' => 'image',
                ],
                'selectors' => [
                    '{{WRAPPER}} .job-search-bg-image' => 'background-image:url({{URL}});',
                ],
            ]
        );
        $this->add_responsive_control(
            'image_height_custom',
            [
                'label' => esc_html__('Image Max Height (px)', 'noo'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 400,
                        'max' => 1200,
                        'step' => 20,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 840,
                ],
                'condition' => [
                    'background_type' => 'image',
                ],
                'selectors' => [
                    '{{WRAPPER}} .job-search-bg-image' => 'height:{{SIZE}}{{UNIT}};;',
                ],
            ]
        );

        $this->add_responsive_control(
            'no_image_height_custom',
            [
                'label' => esc_html__('Content Max Height (px)', 'noo'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 400,
                        'max' => 1200,
                        'step' => 20,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 840,
                ],
                'condition' => [
                    'background_type' => 'no_background',
                ],
                'selectors' => [
                    '{{WRAPPER}} .job-search-bg-image' => 'height:{{SIZE}}{{UNIT}};;',
                ],
            ]
        );
        $this->add_control(
            'search_type',
            [
                'label' => esc_html__('Search_type', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'noo_job' => esc_html__('Job', 'noo'),
                    'noo_resume' => esc_html__('Resume', 'noo'),
                ],
                'default' => 'noo_job',
            ]
        );
        $this->add_control(
            'show_keyword',
            [
                'label' => esc_html__('Enable keyword search', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'noo'),
                'label_off' => esc_html__('Hide', 'noo'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        $this->add_responsive_control(
            'columns',
            [
                'label' => '<i class="fa fa-columns"></i> ' . esc_html__('Columns', 'noo'),
                'type' => Controls_Manager::SELECT,
                'default' => 3,
                'tablet_default' => 2,
                'mobile_default' => 1,
                'options' => [
                    1 => 1,
                    2 => 2,
                    3 => 3,
                ],
                'condition' => [
                    'enable_more_advanced' => 'yes',
                ]
            ]
        );
        $this->add_control(
            'pos2',
            [
                'label' => esc_html__('Search Position 2', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => $this->get_job_search_field(),
                'default' => 'no',
                'condition' => [
                    'search_type' => 'noo_job',
                ],
            ]
        );
        $this->add_control(
            'pos3',
            [
                'label' => esc_html__('Search Position 3', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => $this->get_job_search_field(),
                'default' => 'no',
                'condition' => [
                    'search_type' => 'noo_job',
                ],
            ]
        );
        $this->add_control(
            'r_pos2',
            [
                'label' => esc_html__('Search Position 2', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => $this->get_resume_search_field(),
                'default' => 'no',
                'condition' => [
                    'search_type' => 'noo_resume',
                ],
            ]
        );
        $this->add_control(
            'r_pos3',
            [
                'label' => esc_html__('Search Position 3', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => $this->get_resume_search_field(),
                'default' => 'no',
                'condition' => [
                    'search_type' => 'noo_resume',
                ],
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

        $this->end_controls_section();
    }

    private function noo_visibility_class($visibility = '')
    {
        switch ($visibility) {
            case 'hidden-phone':
                return ' hidden-xs';
            case 'hidden-tablet':
                return ' hidden-sm';
            case 'hidden-pc':
                return ' hidden-md hidden-lg';
            case 'visible-phone':
                return ' visible-xs';
            case 'visible-tablet':
                return ' visible-sm';
            case 'visible-pc':
                return ' visible-md visible-lg';
            default:
                return '';
        }
    }

    private function noo_vc_elements_id_increment()
    {
        static $count = 0;
        $count++;

        return $count;
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $fields_count = 1;
        $sub_fields_count = 1;
        $content = $settings['content'];
        if ($settings['search_type'] == 'noo_resume') {
            $disable_multiple = noo_get_option('noo_resume_search_field_type', 0);
        } else {
            $disable_multiple = noo_get_option('noo_job_search_field_type', 0);
        }
        if ($settings['search_mode'] == 'noo_horizontal') {
            if ($settings['show_keyword'] == 'yes') {
                $fields_count++;
            }
            if ($settings['search_type'] == 'noo_resume') {
                if ($settings['r_pos2'] !== 'no') {
                    $fields_count++;
                }
                if ($settings['r_pos3'] !== 'no') {
                    $fields_count++;
                }
            } elseif ($settings['search_type'] == 'noo_job') {
                if ($settings['pos2'] !== 'no') {
                    $fields_count++;
                }
                if ($settings['pos3'] !== 'no') {
                    $fields_count++;
                }
            }
        }
        $visibility = ($settings['visibility'] != '') && ($settings['visibility'] != 'all') ? esc_attr($settings['visibility']) : '';
        $class = $this->noo_visibility_class($visibility);
        $id = 'job-search-slider-' . $this->noo_vc_elements_id_increment();
        $id_out = 'id="' . esc_attr($id) . ' "';
        $custom_style = '';
        if (($settings['background_type'] == 'slider') && !empty($settings['slider_images'])) {
            $custom_style .= 'height:' . $settings['slider_height']['size'] . 'px;';
        }
        $custom_style = ($custom_style != '') ? 'style = "' . $custom_style . '"' : '';

        if ($settings['background_type'] == '' || $settings['background_type'] == 'no_background') {
            $class .= 'no-background';
        }
        ?>
        <div class="noo-job-search-wrapper noo-job-search-elementor-widget <?php echo esc_attr($class); ?>" <?php echo($id_out . ' ' . $custom_style); ?>>
            <?php
            if ($settings['background_type'] == 'revo_slider') :?>
                <div class="job-search-bg-revo-slider">
                    <?php echo do_shortcode('[rev_slider  alias=' . $settings['revo_slider_id'] . ']'); ?>
                </div>
                <div class="container">
                    <div class="job-advanced-search job-revo-slider <?php echo $settings['align_title'] ?> <?php echo($settings['search_mode'] == 'noo_vertical' ? 'vertical' : 'horizontal'); ?> column-<?php echo esc_attr($fields_count); ?> <?php echo $settings['style_horizontal'] ?>">
                        <div class="job-search-info <?php echo $settings['align_title'] ?>">
                            <?php if (!empty($settings['title'])): ?>
                                <h2 class="search-main-title title" style="color:<?php echo $settings['title_color'] ?>">
                                    <?php echo $settings['title']; ?>
                                </h2>
                            <?php endif; ?>
                            <?php if (!empty($settings['sub_title'])): ?>
                                <p class="search-sub-title">
                                    <?php echo $settings['sub_title']; ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="job-advanced-search-wrap">
                            <form action="<?php echo esc_url(get_post_type_archive_link($settings['search_type'])); ?>">
                                <div class="job-advanced-search-form <?php echo $settings['align_title'] ?> <?php echo (is_rtl()) ? 'chosen-rtl' : '' ?>">
                                    <?php if ($settings['show_keyword'] == 'yes'): ?>
                                        <div class="form-group">
                                            <label class="sr-only"
                                                   for="search-keyword"> <?php esc_html_e('Keyword', 'noo') ?></label>
                                            <input type="text" class="form-control" id="search-keyword" name="s"
                                                   placeholder="<?php esc_html_e('Keyword', 'noo') ?>"
                                                   value="<?php echo(isset($_GET['s']) ? esc_attr($_GET['s']) : ''); ?>">
                                        </div>
                                    <?php else: ?>
                                        <input type="hidden" value="" name="s">
                                    <?php endif; ?>
                                    <?php
                                    if ($settings['search_type'] == 'noo_job') {
                                        jm_job_advanced_search_field($settings['pos2'], $disable_multiple);
                                        jm_job_advanced_search_field($settings['pos3'], $disable_multiple);
                                    } elseif ($settings['search_type'] == 'noo_resume') {
                                        jm_resume_advanced_search_field($settings['r_pos2'], $disable_multiple);
                                        jm_resume_advanced_search_field($settings['r_pos3'], $disable_multiple);
                                    }
                                    ?>
                                    <div class="form-action">
                                        <button type="submit" class="btn btn-search-submit btn-primary">
                                            <?php esc_html_e('Search', 'noo'); ?>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <?php if ($content != ''): ?>
                            <div class="job-search-info <?php echo $settings['align_title'] ?>">
                                <?php echo $content ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php elseif ($settings['background_type'] == 'map') :
                $map_type = jm_get_location_setting('map_type', '');
                $lat_default = jm_get_location_setting('latitude', '');
                $lon_default = jm_get_location_setting('longitude', '');
                $zoom_default = jm_get_location_setting('zoom', '');
                ?>
                <div class="map-info">
                    <div class="<?php echo ($settings['map_post_type'] == 'job_map') ? 'noo-job-map' : 'noo-resume-map' ?> ">
                        <div class="<?php echo ($settings['map_post_type'] == 'job_map') ? 'job-map' : 'resume-map' ?> heading-map">
                            <div class="gmap-loading"><?php esc_html_e('Loading Maps', 'fitsica'); ?>
                                <div class="gmap-loader">
                                    <div class="rect1"></div>
                                    <div class="rect2"></div>
                                    <div class="rect3"></div>
                                    <div class="rect4"></div>
                                    <div class="rect5"></div>
                                </div>
                            </div>
                            <?php if ($map_type == 'google'): ?>
                                <?php
                                if ($settings['map_post_type'] == 'job_map') {
                                    jm_job_enqueue_map_script('no');
                                    wp_enqueue_script('noo-job-map');
                                    wp_enqueue_script('location-picker');
                                } else {
                                    jm_resume_enqueue_map_script();
                                    wp_enqueue_script('noo-resume-map');
                                }
                                ?>
                                <div id="gmap" class="gmap" data-map_style="<?php echo $settings['map_style'] ?>"
                                     data-latitude="<?php echo esc_attr($lat_default); ?>"
                                     data-longitude="<?php echo esc_attr($lon_default); ?>"
                                     data-zoom="<?php echo esc_attr($zoom_default); ?>"
                                     data-fit_bounds="yes">
                                </div>
                                <div class="container-map-location-search">
                                    <i class="fa fa-search"></i>
                                    <input type="text" class="form-control" id="map-location-search"
                                           placeholder="<?php echo esc_attr__('Search for a location...', 'fitsica'); ?>"
                                           autocomplete="off">
                                </div>
                            <?php else: ?>
                                <?php
                                if ($settings['map_post_type'] == 'job_map') {
                                    jm_job_enqueue_map_script('no');
                                    wp_enqueue_script('bing-map');
                                    wp_enqueue_script('bing-map-api');
                                } else {
                                    jm_resume_enqueue_map_script();
                                    wp_enqueue_script('bing-map');
                                    wp_enqueue_script('bing-map-api');
                                }
                                ?>
                                <div id="bmap"
                                     data-latitude="<?php echo esc_attr($lat_default); ?>"
                                     data-longitude="<?php echo esc_attr($lon_default); ?>"
                                     data-zoom="<?php echo esc_attr($zoom_default); ?>"
                                     data-map_style="<?php echo $settings['map_style'] ?>"
                                     data-id="bmap"
                                     class="<?php echo ($settings['map_post_type'] == 'job_map') ? 'bmap' : 'resume_bmap' ?>">
                                </div>
                            <?php endif ?>
                        </div>
                        <div class="job-advanced-search job-map-elementor container <?php echo($settings['search_mode'] == 'noo_vertical' ? 'vertical' : 'horizontal'); ?> column-<?php echo esc_attr($fields_count); ?> <?php echo $settings['style_horizontal'] ?>">
                            <div class="job-search-info <?php echo $settings['align_title'] ?>">
                                <?php if (!empty($settings['title'])): ?>
                                    <h2 class="search-main-title" style="color: <?php echo $settings['title_color'] ?>">
                                        <?php echo($settings['title']); ?>
                                    </h2>
                                <?php endif; ?>
                                <?php if (!empty($settings['sub_title'])): ?>
                                    <p class="search-sub-title">
                                        <?php echo $settings['sub_title']; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="job-advanced-search-wrap">
                                <form method="get" class="form-inline"
                                      action="<?php echo esc_url(get_post_type_archive_link($settings['search_type'])); ?>">
                                    <div class="job-advanced-search-form <?php echo $settings['align_title'] ?> <?php echo (is_rtl()) ? 'chosen-rtl' : '' ?>">
                                        <?php if ($settings['show_keyword'] == 'yes'): ?>
                                            <div class="form-group">
                                                <label class="sr-only"
                                                       for="search-keyword "> <?php esc_html_e('Keyword', 'noo') ?></label>
                                                <input type="text" class="form-control" id="search-keyword" name="s"
                                                       placeholder="<?php esc_html_e('Enter job title', 'noo') ?>"
                                                       value="<?php echo(isset($_GET['s']) ? esc_attr($_GET['s']) : ''); ?>">
                                            </div>
                                        <?php else: ?>
                                            <input type="hidden" value="" name="s">
                                        <?php endif; ?>
                                        <?php
                                        if ($settings['search_type'] == 'noo_job') {
                                            jm_job_advanced_search_field($settings['pos2'], $disable_multiple);
                                            jm_job_advanced_search_field($settings['pos3'], $disable_multiple);
                                        } elseif ($settings['search_type'] == 'noo_resume') {
                                            jm_resume_advanced_search_field($settings['r_pos2'], $disable_multiple);
                                            jm_resume_advanced_search_field($settings['r_pos3'], $disable_multiple);
                                        }
                                        ?>
                                        <div class="form-action">
                                            <button type="submit" class="btn btn-primary btn-search-submit">
                                                <?php echo ($settings['style_horizontal'] == 'style-1') ? esc_html__('Search Now', 'noo') : esc_html__('Finds Job', 'noo') ?>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <?php if ($content != ''): ?>
                                <div class="job-search-info <?php echo $settings['align_title'] ?>">
                                    <?php echo $content ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php if ($settings['background_type'] == 'image'): ?>
                    <!-- <div class="job-search-bg-image"></div> -->
                <?php endif; ?>
                <?php if ($settings['background_type'] == 'no_background'): ?>
                    <!-- <div class="job-search-bg-image"></div> -->
                <?php endif; ?>
                <?php if ($settings['background_type'] == 'slider' && !empty($settings['slider_images'])) : ?>
                    <div class="job-search-bg-slider">
                        <?php
                        $data_slide = array(
                            'speed' => $settings['slider_speed']['size'],
                            'time' => $settings['slider_time']['size'],
                        );
                        $data_slide = 'data-slide="' . esc_attr(json_encode($data_slide)) . '"';
                        $html = array();
                        $html[] = '<div class="sliders owl-carousel" ' . $data_slide . ' style="display:flex; flex-wrap:wrap" >';
                        $images = $settings['slider_images'];
                        foreach ($images as $image) {
                            $html[] = '<div><img class="slide-image" src="' . $image['url'] . '"></div>';
                        }
                        $html[] = '</div>';
                        $html[] = '<div class="clearfix"></div>';
                        echo implode("\n", $html);
                        ?>
                    </div>
                    <?php
                    if (!empty($settings['slider_height'])) {
                        $html[] = '<style type="text/css" media="screen">';
                        $html[] = "  #{$id}.noo-slider .caroufredsel_wrapper .sliders  .slide-item.noo-property-slide { max-height: {$settings['slider_height']['size']}px; }";
                        $html[] = '</style>';
                    }
                    ?>
                <?php endif; ?>
                <div class="job-search-bg-image">
                    <div class="container job-advanced-search-container">
                        <div class="job-advanced-search <?php echo $settings['align_title'] ?> <?php echo($settings['search_mode'] == 'noo_vertical' ? 'vertical' : 'horizontal'); ?> column-<?php echo esc_attr($fields_count); ?> <?php echo $settings['style_horizontal'] ?>">
                            <div class="job-search-info <?php echo $settings['align_title'] ?>">
                                <?php if (!empty($settings['title'])): ?>
                                    <h2 class="search-main-title" style="color: <?php echo $settings['title_color'] ?>">
                                        <?php echo($settings['title']); ?>
                                    </h2>
                                <?php endif; ?>
                                <?php if (!empty($settings['sub_title'])): ?>
                                    <p class="search-sub-title">
                                        <?php echo $settings['sub_title']; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="job-advanced-search-wrap">
                                <form method="get" class="form-inline"
                                      action="<?php echo esc_url(get_post_type_archive_link($settings['search_type'])); ?>">
                                    <div class="job-advanced-search-form <?php echo (is_rtl()) ? 'chosen-rtl' : '' ?>">
                                        <?php if ($settings['show_keyword'] == 'yes'): ?>
                                            <div class="form-group">
                                                <label class="sr-only" for="search-keyword ">
                                                    <?php esc_html_e('Keyword', 'noo') ?>   
                                                </label>
                                                <input type="text" class="form-control" id="search-keyword" name="s"
                                                       placeholder="<?php esc_html_e('Enter job title', 'noo') ?>"
                                                       value="<?php echo(isset($_GET['s']) ? esc_attr($_GET['s']) : ''); ?>">
                                            </div>
                                        <?php else: ?>
                                            <input type="hidden" value="" name="s">
                                        <?php endif; ?>
                                        <?php
                                        if ($settings['search_type'] == 'noo_job') {
                                            jm_job_advanced_search_field($settings['pos2'], $disable_multiple);
                                            jm_job_advanced_search_field($settings['pos3'], $disable_multiple);
                                        } elseif ($settings['search_type'] == 'noo_resume') {
                                            jm_resume_advanced_search_field($settings['r_pos2'], $disable_multiple);
                                            jm_resume_advanced_search_field($settings['r_pos3'], $disable_multiple);
                                        }
                                        ?>
                                        <div class="form-action">
                                            <button type="submit" class="btn btn-primary btn-search-submit">
                                                <?php echo ($settings['style_horizontal'] == 'style-1') ? esc_html__('Search Now', 'noo') : esc_html__('Finds Job', 'noo') ?>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <?php if ($content != ''): ?>
                                <div class="job-search-info <?php echo $settings['align_title'] ?>">
                                    <?php echo $content ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php
    }
}