<?php

namespace Noo_Elementor_Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Noo_Job_Package;
use Noo_Resume_Package;
use Noo_Member;
use Elementor\Group_Control_Typography;

class Package extends Widget_Base
{
    public function get_name()
    {
        return 'noo_package';
    }

    public function get_title()
    {
        return esc_html__('Noo Package', 'noo');
    }

    public function get_icon()
    {
        return 'fa fa-plus-square';
    }

    public function get_categories()
    {
        return ['noo-element-widgets'];
    }

    public function get_script_depends()
    {
        return [
            'owl-carousel',
            'noo-elementor',
        ];
    }

    public function get_style_depends()
    {
        return [
            'owl-carousel',
            'noo-package',
        ];
    }

    protected function register_controls()
    {
        $this->noo_package_options();
    }

    private function get_product_categories($taxonomy = 'product_cat')
    {
        $categories = array();
        if (!empty($taxonomy)) {
            $terms = get_terms(
                array(
                    'taxonomy' => $taxonomy,
                    'hide_empty' => false,
                )
            );
            if (!empty($terms)) {
                foreach ($terms as $category) {
                    if (isset($category)) {
                        if (isset($category->slug) && isset($category->name)) {
                            $categories[$category->slug] = $category->name;
                        }
                    }
                }
            }
        }
        return $categories;
    }

    private function noo_package_options()
    {
        $this->start_controls_section(
            'noo_package',
            [
                'label' => esc_html__('Packages Options', 'noo'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'package_type',
            [
                'label' => esc_html__('Type Package', 'noo'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'type_job' => [
                        'title' => esc_html__('Job Package', 'noo'),
                        'icon' => 'fa fa-briefcase'
                    ],
                    'type_resume' => [
                        'title' => esc_html__('Resume Package', 'noo'),
                        'icon' => 'fa fa-users'
                    ],
                ],
                'default' => 'type_job',
            ]
        );
        $this->add_control(
            'package_style',
            [
                'label' => esc_html__('Packages Style', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    'style-1' => esc_html__('Style 1', 'noo'),
                    'style-2' => esc_html__('Style 2', 'noo'),
                ),
                'default' => 'style-1',
            ]
        );
        $this->add_control(
            'style_slider',
            [
                'label' => esc_html__('Packages Style Slide', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'noo'),
                'label_off' => __('No', 'noo'),
                'return_value' => true,
                'default' => true,
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
                'label_of' => esc_html__('No', 'noo'),
                'return_value' => true,
                'default' => true,
                'condition' => [
                    'style_slider' => true,
                ]
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
                'condition' => [
                    'style_slider' => true,
                ]
            ]
        );
        $this->add_control(
            'show_pagination',
            [
                'label' => __('Show Pagination', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'noo'),
                'label_off' => __('No', 'noo'),
                'return_value' => true,
                'default' => true,
                'condition' => [
                    'style_slider' => true,
                ]
            ]
        );
        $this->add_control(
            'show_navigation',
            [
                'label' => __('Show Navigation', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'noo'),
                'label_off' => __('No', 'noo'),
                'return_value' => true,
                'default' => true,
                'condition' => [
                    'style_slider' => true,
                ]
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
                ],
                'condition' => [
                    'style_slider' => true,
                ]
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
                    '{{WRAPPER}} .noo-package-widget' => 'margin: -{{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .noo-package-widget .noo-grid-item' => 'padding: {{SIZE}}{{UNIT}}',
                ],
            ]
        );
        $this->end_controls_section();
    }

    protected function render()
    {
        $setting = $this->get_settings();
        $add_to_cart = jm_is_job_posting_page() ? false : true;
        ?>
        <div class="noo-package-widget job-package clearfix">
            <?php

            $user_id = get_current_user_id();
            if ($setting['package_type'] == 'type_job'):
                global $noo_view_job_package;
                $noo_view_job_package = true;
                $product_args = array(
                    'post_type' => 'product',
                    'posts_per_page' => -1,
                    'suppress_filters' => false,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'product_type',
                            'field' => 'slug',
                            'terms' => array('job_package'),
                        )
                    ),
                    'orderby' => 'menu_order title',
                    'order' => 'ASC',

                );

                $packages = get_posts($product_args);
                $noo_view_job_package = false;
                $purchased_free_package = Noo_Job_Package::is_purchased_free_package($user_id);
                $ex = Noo_Job_Package::set_expired_package_schedule($user_id);
                $package_data = (!empty($user_id)) ? get_user_meta($user_id, '_job_package', true) : null;
                $expired_package = (isset($package_data['expired'])) ? $package_data['expired'] : '';
                $time = getdate();
                $current_time = $time[0];
                $mobile_class = (!empty($setting['columns_mobile']) ? 'noo-mobile-' . $setting['columns_mobile'] : '');
                $tablet_class = (!empty($setting['columns_tablet']) ? 'noo-tablet-' . $setting['columns_tablet'] : '');
                $desktop_class = (!empty($setting['columns']) ? 'noo-desktop-' . $setting['columns'] : '');
                $this->add_render_attribute('package-grid', 'class', ['noo-grid-col noo-pricing-table classic package-pricing', $desktop_class, $tablet_class, $mobile_class, $setting['package_style']]);
                $package_grid_class = $this->get_render_attribute_string('package-grid');
                if ($setting['style_slider'] == true) {
                    $data_slide = array(
                        'items' => $setting['columns'],
                        'mobilecol' => !empty($setting['columns_mobile']) ? $setting['columns_mobile'] : 1,
                        'tabletcol' => !empty($setting['columns_tablet']) ? $setting['columns_tablet'] : 2,
                        'loop' => true,
                        'auto_height' => $setting['auto_height'] == 'yes' ? true : false,
                        'autoplay' => $setting['auto_play'] == 'yes' ? true : false,
                        'speed' => $setting['slider_speed']['size'],
                        'show_nav' => $setting['show_navigation'],
                        'dot' => $setting['show_pagination'],

                    );
                    $data_slider = ' data-slide="' . esc_attr(json_encode($data_slide)) . '"';
                    $package_grid_class = 'class="owl-carousel noo-grid-col noo-pricing-table classic package-pricing ' . $setting['package_style'] . '" ' . $data_slider . '';
                }

                $package_style = (($setting['package_style'] == 'style-1') ? 'style-1' : 'style-2');

                ?>
                <?php if ($packages): ?>
                <?php do_action('noo_job_package_before'); ?>
                <div <?php echo (!$setting['style_slider']) ? implode('', [$package_grid_class]) : $package_grid_class; ?>>
                    <?php foreach ($packages as $package): ?>
                        <?php

                        $product = wc_get_product($package->ID);
                        $checkout_url = $add_to_cart ? Noo_Member::get_checkout_url($product->get_id()) : add_query_arg('package_id', $product->get_id());
                        $checkout_url_candidate = add_query_arg('package_id', $product->get_id());

                        $redirect_package_free = $add_to_cart ? Noo_Member::get_endpoint_url('manage-plan') : add_query_arg('package_id', $product->get_id());

                        $package_interval = $product->get_package_interval();
                        $package_interval_unit = $product->get_package_interval_unit();
                        $package_interval_text = Noo_Job_Package::get_package_interval_text($package_interval, $package_interval_unit);

                        $is_unlimited = $product->is_unlimited_job_posting();
                        $job_limit = $product->get_post_job_limit();
                        $job_posting_text = $is_unlimited ? esc_html__('Unlimited job posting', 'noo') : sprintf(_n('%s job posting', '%s jobs posting', $job_limit, 'noo'), $job_limit);

                        $featured_limit = $product->get_job_feature_limit();
                        $job_featured_text = 99999999 == $featured_limit ? esc_html__('Unlimited featured job', 'noo') : sprintf(_n('%s featured job', '%s featured jobs', $featured_limit, 'noo'), $featured_limit);

                        $refresh_limit = $product->get_job_refresh_limit();
                        $job_refresh_text = 99999999 == $refresh_limit ? esc_html__('Unlimited refresh job', 'noo') : sprintf(_n('%s refresh job', '%s refresh jobs', $refresh_limit, 'noo'), $refresh_limit);

                        $download_resume_limit = $product->get_download_resume_limit();
                        $download_resume_limit_text = 99999999 == $download_resume_limit ? esc_html__('Unlimited download resume', 'noo') : sprintf(_n('%s download resume ', '%s download resumes ', $download_resume_limit, 'noo'), $download_resume_limit);

                        $job_duration_text = sprintf(_n('Job displayed for %s day', 'Job displayed for %s days', $product->get_job_display_duration(), 'noo'), $product->get_job_display_duration());

                        $company_featured = $product->get_company_featured();
                        ?>
                        <div
                                class="noo-grid-item noo-pricing-column <?php echo($product->is_featured() ? 'featured' : ''); ?>">
                            <div class="pricing-content transition300 br4 b-all">
                                <div class="pricing-header">
                                    <h2 class="pricing-title"><?php echo esc_html($product->get_title()) ?></h2>
                                    <h3 class="pricing-value"><span
                                                class="noo-price"><?php echo wp_kses_post($product->get_price_html()); ?></span>
                                    </h3>
                                </div>
                                <div class="pricing-info">
                                    <ul class="noo-ul-icon fa-ul">
                                        <?php if (!empty($package_interval_text)) : ?>
                                            <li class="noo-li-icon"><i
                                                        class="fa fa-check-circle"></i> <?php echo sprintf(__('%s Membership', 'noo'), $package_interval_text); ?>
                                            </li>
                                        <?php endif; ?>
                                        <?php if ($is_unlimited || $job_limit > 0) : ?>
                                            <li class="noo-li-icon"><i
                                                        class="fa fa-check-circle"></i> <?php echo esc_html($job_posting_text); ?>
                                            </li>
                                        <?php else : ?>
                                            <li class="noo-li-icon"><i
                                                        class="fa fa-times-circle-o not-good"></i> <?php esc_html_e('No job posting', 'noo'); ?>
                                            </li>
                                        <?php endif; ?>
                                        <?php if ($featured_limit > 0) : ?>
                                            <li class="noo-li-icon"><i
                                                        class="fa fa-check-circle"></i> <?php echo esc_html($job_featured_text); ?>
                                            </li>
                                        <?php else : ?>
                                            <li class="noo-li-icon"><i
                                                        class="fa fa-times-circle-o not-good"></i> <?php esc_html_e('No featured job', 'noo'); ?>
                                            </li>
                                        <?php endif; ?>
                                        <?php if ($refresh_limit > 0) : ?>
                                            <li class="noo-li-icon"><i
                                                        class="fa fa-check-circle"></i> <?php echo esc_html($job_refresh_text); ?>
                                            </li>
                                        <?php else : ?>
                                            <li class="noo-li-icon"><i
                                                        class="fa fa-times-circle-o not-good"></i> <?php esc_html_e('No refresh job', 'noo'); ?>
                                            </li>
                                        <?php endif; ?>
                                        <?php if ($download_resume_limit > 0): ?>
                                            <li class="noo-li-icon"><i
                                                        class="fa fa-check-circle"></i><?php esc_html_e($download_resume_limit_text); ?>
                                            </li>
                                        <?php else: ?>
                                            <li class="noo-li-icon"><i
                                                        class="fa fa-times-circle-o not-good"></i><?php esc_html_e('No Download Resume Attach file', 'noo') ?>
                                            </li>
                                        <?php endif; ?>
                                        <?php if ($is_unlimited || $job_limit > 0) : ?>
                                            <li class="noo-li-icon"><i
                                                        class="fa fa-check-circle"></i> <?php echo esc_html($job_duration_text); ?>
                                            </li>
                                        <?php endif; ?>
                                        <?php if ($company_featured) : ?>
                                            <li class="noo-li-icon"><i
                                                        class="fa fa-check-circle"></i> <?php esc_html_e('Featured Company', 'noo'); ?>
                                            </li>
                                        <?php endif; ?>
                                        <?php do_action('jm_job_package_features_list', $product); ?>
                                    </ul>
                                    <?php if (!empty($package->post_excerpt)) : ?>
                                        <div class="short-desc">
                                            <?php echo apply_filters('woocommerce_short_description', $package->post_excerpt); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($package->post_content)) : ?>
                                        <a href="javascript:void(0)" class="readmore package-modal" data-toggle="modal"
                                           data-target="#package-content-<?php echo $package->ID; ?>"><i
                                                    class="fa fa-arrow-circle-right"></i><?php echo __('More info', 'noo'); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <?php
                                $disable = '';
                                if ($product->get_price() <= 0) {
                                    if ($current_time < $expired_package) {
                                        $disable = 'disabled';
                                    } elseif ($purchased_free_package) {
                                        $disable = 'disabled';
                                    }
                                }
                                ?>
                                <?php
                                if (Noo_Member::is_logged_in()) {
                                    if (Noo_Member::is_employer($user_id)):?>
                                        <div class="pricing-footer pb50 pt20"
                                             <?php if (!empty($disable)): ?>data-toggle="tooltip"
                                             title="<?php echo esc_attr__('You have exhausted the right to purchase this package', 'noo'); ?>"<?php endif;
                                        ?>>
                                            
                                                <a class="btn btn-lg btn-primary <?php echo $disable ?><?php echo ($product->get_price() == 0 && is_user_logged_in() && empty($disable)) ? ' auto_create_order_free' : ''; ?>"
                                                    <?php if (empty($disable)): ?> data-id="<?php echo get_current_user_id(); ?>"<?php echo ($product->get_price() == 0 && is_user_logged_in()) ? ' data-security="' . wp_create_nonce('noo-free-package') . '" data-url-package="' . $redirect_package_free . '"' : ' href="' . esc_url($checkout_url) . '"'; ?>
                                                        data-package="<?php echo $product->get_id() ?>"<?php endif; ?>><?php echo wp_kses_post($product->add_to_cart_text()) ?></a>
                                            
                                        </div>
                                    <?php else : ?>
                                        <div class="pricing-footer pb50 pt20" data-toggle="tooltip"
                                             title="<?php echo esc_html__('You cannot buy the package with a candidate account', 'noo'); ?>">
                                            
                                                <a class="btn btn-lg btn-primary disabled"
                                                   href="#"><?php echo wp_kses_post($product->add_to_cart_text()) ?>

                                                </a>
                                            
                                        </div>
                                    <?php endif; ?>
                                <?php } else {
                                    ?>
                                    <?php $link = Noo_Member::get_login_url(); ?>
                                    <div class="pricing-footer pb50 pt20">
                                        
                                            <a class="btn btn-lg btn-primary" href="<?php echo esc_url($link) ?>">
                                                <?php echo wp_kses_post($product->add_to_cart_text()) ?>
                                            </a>
                                        
                                    </div>
                                <?php } ?>
                                <?php if (!empty($package->post_content)) : ?>
                                    <div id="package-content-<?php echo $package->ID; ?>"
                                         class="package-content modal fade"
                                         tabindex="-1" role="dialog"
                                         aria-labelledby="package-content-<?php echo $package->ID; ?>Label"
                                         aria-hidden="true">
                                        <div class="modal-dialog package-modal">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                        <span aria-hidden="true">&times;</span></button>
                                                    <h2 class="modal-title"><?php echo esc_html($product->get_title()) ?></h2>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-5 pricing-header">
                                                            <h3 class="pricing-value"><span
                                                                        class="noo-price"><?php echo wp_kses_post($product->get_price_html()); ?></span>
                                                            </h3>
                                                        </div>
                                                        <div class="col-md-7 pull-right pricing-info">
                                                            <ul class="noo-ul-icon fa-ul">
                                                                <?php if (!empty($package_interval_text)) : ?>
                                                                    <li class="noo-li-icon"><i
                                                                                class="fa fa-check-circle"></i> <?php echo sprintf(__('%s Membership', 'noo'), $package_interval_text); ?>
                                                                    </li>
                                                                <?php endif; ?>
                                                                <?php if ($is_unlimited || $job_limit > 0) : ?>
                                                                    <li class="noo-li-icon"><i
                                                                                class="fa fa-check-circle"></i> <?php echo $job_posting_text; ?>
                                                                    </li>
                                                                <?php else : ?>
                                                                    <li class="noo-li-icon"><i
                                                                                class="fa fa-times-circle-o not-good"></i> <?php echo __('No job posting', 'noo'); ?>
                                                                    </li>
                                                                <?php endif; ?>
                                                                <?php if ($featured_limit > 0) : ?>
                                                                    <li class="noo-li-icon"><i
                                                                                class="fa fa-check-circle"></i> <?php echo $job_featured_text; ?>
                                                                    </li>
                                                                <?php else : ?>
                                                                    <li class="noo-li-icon"><i
                                                                                class="fa fa-times-circle-o not-good"></i> <?php echo __('No featured job', 'noo'); ?>
                                                                    </li>
                                                                <?php endif; ?>
                                                                <?php if ($is_unlimited || $job_limit > 0) : ?>
                                                                    <li class="noo-li-icon"><i
                                                                                class="fa fa-check-circle"></i> <?php echo $job_duration_text; ?>
                                                                    </li>
                                                                <?php endif; ?>
                                                                <?php if ($company_featured) : ?>
                                                                    <li class="noo-li-icon"><i
                                                                                class="fa fa-check-circle"></i> <?php echo __('Featured Company', 'noo'); ?>
                                                                    </li>
                                                                <?php endif; ?>
                                                                <?php do_action('jm_job_package_features_list', $product); ?>
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-12 package-content">
                                                            <?php echo apply_filters('noo_package_content', $package->post_content); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <a class="btn btn-lg btn-primary <?php echo $disable ?> <?php echo ($product->get_price() == 0 && is_user_logged_in()) ? ' auto_create_order_free' : ''; ?>"
                                                       data-id="<?php echo get_current_user_id(); ?>"<?php echo ($product->get_price() == 0 && is_user_logged_in()) ? ' data-security="' . wp_create_nonce('noo-free-package') . '" data-url-package="' . $redirect_package_free . '"' : ' href="' . esc_url($checkout_url) . '"'; ?>
                                                       data-package="<?php echo $product->get_id() ?>"><?php echo wp_kses_post($product->add_to_cart_text()) ?></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php elseif ($setting['package_type'] == 'type_resume'):
                global $noo_view_resume_package;
                $noo_view_resume_package = true;
                $product_args = array(
                    'post_type' => 'product',
                    'posts_per_page' => -1,
                    'suppress_filters' => false,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'product_type',
                            'field' => 'slug',
                            'terms' => array('resume_package')
                        )
                    ),
                    'orderby' => 'menu_order title',
                    'order' => 'ASC',
                );
                $packages = get_posts($product_args);
                $noo_view_resume_package = false;
                $user_id = get_current_user_id();
                $purchased_free_package = Noo_Resume_Package::is_purchased_free_package($user_id);
                $package_data = jm_get_resume_posting_info($user_id);
                $expired_package = (isset($package_data['expired'])) ? $package_data['expired'] : '';
                $time = getdate();
                $current_time = $time[0];
                $columns = !isset($columns) || empty($columns) ? min(count($packages), 4) : absint($columns);
                $mobile_class = (!empty($setting['columns_mobile']) ? 'noo-mobile-' . $setting['columns_mobile'] : '');
                $tablet_class = (!empty($setting['columns_tablet']) ? 'noo-tablet-' . $setting['columns_tablet'] : '');
                $desktop_class = (!empty($setting['columns']) ? 'noo-desktop-' . $setting['columns'] : '');
                $this->add_render_attribute('package-grid', 'class', [' noo-grid-col noo-pricing-table classic row package-pricing', $desktop_class, $tablet_class, $mobile_class, $setting['package_style']]);
                $package_grid_class = $this->get_render_attribute_string('package-grid');
                if ($setting['style_slider'] == true) {
                    $data_slide = array(
                        'items' => $setting['columns'],
                        'mobilecol' => !empty($setting['columns_mobile']) ? $setting['columns_mobile'] : 1,
                        'tabletcol' => !empty($setting['columns_tablet']) ? $setting['columns_tablet'] : 2,
                        'loop' => true,
                        'auto_height' => $setting['auto_height'] == 'yes' ? true : false,
                        'autoplay' => $setting['auto_play'] == 'yes' ? true : false,
                        'speed' => $setting['slider_speed']['size'],
                        'show_nav' => $setting['show_navigation'],
                        'dot' => $setting['show_pagination'],

                    );
                    $data_slider = ' data-slide="' . esc_attr(json_encode($data_slide)) . '"';
                    $package_grid_class = 'class="owl-carousel noo-grid-col noo-pricing-table classic package-pricing ' . $setting['package_style'] . '" ' . $data_slider . '';
                }
                $package_style = (($setting['package_style'] == 'style-1') ? 'style-1' : 'style-2');
                ?>
                <?php if ($packages): ?>
                <?php do_action('noo_resume_package_before'); ?>
                <div <?php echo (!$setting['style_slider']) ? implode('', [$package_grid_class]) : $package_grid_class; ?>>
                    <?php foreach ($packages as $package): ?>
                        <?php
                        $product = wc_get_product($package->ID);
                        $checkout_url = $add_to_cart ? Noo_Member::get_checkout_url($product->get_id()) : add_query_arg('package_id', $product->get_id());
                        $redirect_package_free = $add_to_cart ? Noo_Member::get_endpoint_url('manage-plan') : add_query_arg('package_id', $product->get_id());

                        $package_interval = $product->get_package_interval();
                        $package_interval_unit = $product->get_package_interval_unit();
                        $package_interval_text = Noo_Job_Package::get_package_interval_text($package_interval, $package_interval_unit);
                        $is_unlimited = $product->is_unlimited_resume_posting();
                        $resume_limit = $product->get_post_resume_limit();
                        $resume_refresh = $product->get_resume_refresh_limit();
                        $resume_feature_limit = $product->get_resume_feature_limit();

                        $resume_limit_text = $is_unlimited ? esc_html__('Unlimited resume posting', 'noo') : sprintf(_n('%s resume posting', '%s resumes posting', $resume_limit, 'noo'), $resume_limit);

                        $columns_class = ($columns == 5) ? 'noo-5' : (12 / $columns);
                        ?>
                        <div
                                class=" noo-pricing-column noo-grid-item <?php echo($product->is_featured() ? 'featured' : ''); ?>">
                            <div class="pricing-content transition300 ">
                                <div class="pricing-header">
                                    <h2 class="pricing-title"><?php echo esc_html($product->get_title()) ?></h2>
                                    <h3 class="pricing-value"><span
                                                class="noo-price"><?php echo wp_kses_post($product->get_price_html()) ?></span>
                                    </h3>
                                </div>
                                <div class="pricing-info">
                                    <ul class="noo-ul-icon fa-ul">
                                        <?php if (!empty($package_interval_text)) : ?>
                                            <li class="noo-li-icon"><i
                                                        class="fa fa-check-circle"></i> <?php echo sprintf(__('%s Membership', 'noo'), $package_interval_text); ?>
                                            </li>
                                        <?php endif; ?>
                                        <?php if (!empty($resume_refresh)) : ?>
                                            <li class="noo-li-icon"><i
                                                        class="fa fa-check-circle"></i> <?php echo sprintf(__('%s Resume Refresh', 'noo'), $resume_refresh); ?>
                                            </li>
                                        <?php endif; ?>
                                        <?php if (!empty($resume_feature_limit)): ?>
                                            <li class="noo-li-icon"><i
                                                        class="fa fa-check-circle"></i> <?php echo sprintf(__('%s Resume Feature', 'noo'), $resume_feature_limit); ?>
                                            </li>
                                        <?php endif; ?>
                                        <?php if ($is_unlimited || $resume_limit > 0) : ?>
                                            <li class="noo-li-icon"><i
                                                        class="fa fa-check-circle"></i> <?php echo $resume_limit_text; ?>
                                            </li>
                                        <?php else : ?>
                                            <li class="noo-li-icon"><i
                                                        class="fa fa-times-circle-o not-good"></i> <?php echo __('No resume posting', 'noo'); ?>
                                            </li>
                                        <?php endif; ?>
                                        <?php do_action('jm_resume_package_features_list', $product); ?>
                                    </ul>
                                    <?php if (!empty($package->post_excerpt)) : ?>
                                        <div class="short-desc">
                                            <?php echo apply_filters('woocommerce_short_description', $package->post_excerpt); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($package->post_content)) : ?>
                                        <a href="javascript:void(0)" class="readmore package-modal" data-toggle="modal"
                                           data-target="#package-content-<?php echo $package->ID; ?>"><i
                                                    class="fa fa-arrow-circle-right"></i><?php echo __('More info', 'noo'); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <?php
                                $disable = '';
                                if ($product->get_price() <= 0) {
                                    if ($current_time < $expired_package) {
                                        $disable = 'disabled';
                                    } elseif ($purchased_free_package) {
                                        $disable = 'disabled';
                                    }
                                }
                                ?>
                                <?php
                                if (Noo_Member::is_logged_in()) {
                                    if (Noo_Member::is_candidate($user_id)): ?>
                                        <div class="pricing-footer pb50 pt20"
                                             <?php if (!empty($disable)): ?>data-toggle="tooltip"
                                             title="<?php echo esc_attr__('You have exhausted the right to purchase this package', 'noo'); ?>"<?php endif; ?>>
                                            
                                                <a class="btn btn-lg btn-primary <?php echo $disable; ?> <?php echo ($product->get_price() == 0 && is_user_logged_in() && empty($disable)) ? ' auto_create_order_free' : ''; ?>"
                                                   <?php if (empty($disable)): ?>data-id="<?php echo get_current_user_id(); ?>"<?php echo ($product->get_price() == 0 && is_user_logged_in()) ? ' data-security="' . wp_create_nonce('noo-free-package') . '" data-url-package="' . $redirect_package_free . '"' : ' href="' . esc_url($checkout_url) . '"'; ?>
                                                   data-package="<?php echo $product->get_id() ?>" <?php endif; ?>><?php echo wp_kses_post($product->add_to_cart_text()) ?></a>
                                            
                                        </div>

                                    <?php else: ?>
                                        <div class="pricing-footer pb50 pt20" data-toggle="tooltip"
                                             title="<?php echo esc_html__('You cannot buy the package with an employer account', 'noo'); ?>">
                                           
                                                <a class="btn btn-lg btn-primary disable">
                                                    <?php echo wp_kses_post($product->add_to_cart_text()) ?>
                                                </a>
                                            
                                        </div>
                                    <?php endif; ?>
                                <?php } else {
                                    ?>
                                    <?php $link = Noo_Member::get_login_url(); ?>
                                    <div class="pricing-footer pb50 pt20">
                                        
                                            <a class="btn btn-lg btn-primary" href="<?php echo esc_url($link); ?>">
                                                <?php echo wp_kses_post($product->add_to_cart_text()) ?>
                                            </a>
                                        
                                    </div>
                                <?php } ?>
                                <?php if (!empty($package->post_content)) : ?>
                                    <div id="package-content-<?php echo $package->ID; ?>"
                                         class="package-content modal fade" tabindex="-1" role="dialog"
                                         aria-labelledby="package-content-<?php echo $package->ID; ?>Label"
                                         aria-hidden="true">
                                        <div class="modal-dialog package-modal">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close"><span aria-hidden="true">&times;</span>
                                                    </button>
                                                    <h2 class="modal-title"><?php echo esc_html($product->get_title()) ?></h2>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-5 pricing-header">
                                                            <h3 class="pricing-value"><span
                                                                        class="noo-price"><?php echo wp_kses_post($product->get_price_html()) ?></span>
                                                            </h3>
                                                        </div>
                                                        <div class="col-md-7 pull-right pricing-info">
                                                            <ul class="noo-ul-icon fa-ul">
                                                                <?php if (!empty($package_interval_text)) : ?>
                                                                    <li class="noo-li-icon"><i
                                                                                class="fa fa-check-circle"></i> <?php echo sprintf(__('%s Membership', 'noo'), $package_interval_text); ?>
                                                                    </li>
                                                                <?php endif; ?>
                                                                <?php if ($is_unlimited || $resume_limit > 0) : ?>
                                                                    <li class="noo-li-icon"><i
                                                                                class="fa fa-check-circle"></i> <?php echo $resume_limit_text; ?>
                                                                    </li>
                                                                <?php else : ?>
                                                                    <li class="noo-li-icon"><i
                                                                                class="fa fa-times-circle-o not-good"></i> <?php echo __('No resume posting', 'noo'); ?>
                                                                    </li>
                                                                <?php endif; ?>
                                                                <?php do_action('jm_resume_package_features_list', $product); ?>
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-12 package-content">
                                                            <?php echo apply_filters('noo_package_content', $package->post_content); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <a class="btn btn-lg btn-primary <?php echo $disable; ?> <?php echo ($product->get_price() == 0 && is_user_logged_in()) ? ' auto_create_order_free' : ''; ?>"
                                                       data-id="<?php echo get_current_user_id(); ?>"<?php echo ($product->get_price() == 0 && is_user_logged_in()) ? ' data-security="' . wp_create_nonce('noo-free-package') . '" data-url-package="' . $redirect_package_free . '"' : ' href="' . esc_url($checkout_url) . '"'; ?>
                                                       data-package="<?php echo $product->get_id() ?>"><?php echo wp_kses_post($product->add_to_cart_text()) ?></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
                <?php wp_reset_postdata(); ?>
            <?php endif; ?>
        </div>
        <?php
    }
}
