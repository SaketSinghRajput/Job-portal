<?php

namespace Noo_Elementor_Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Job_Indeed extends Widget_Base
{
    public function get_name()
    {
        return 'noo_job_indeed';
    }

    public function get_title()
    {
        return esc_html__('Noo Jobs Indeed', 'noo');
    }

    public function get_icon()
    {
        return 'fa fa-book';
    }

    public function get_categories()
    {
        return ['noo-element-widgets'];
    }

    private function get_job_type($taxonomy = 'job_type')
    {
        if (!empty($taxonomy)) {
            // Get categories for post type.
            $options = array(
                '' => __('None', 'noo'),
            );
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
            'noo_job_indeed',
            [
                'label' => esc_html__('Noo Job Indeed ', 'noo'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'public_id',
            [
                'label' => esc_html__('Indeed ID', 'noo'),
                'type' => Controls_Manager::TEXT,
                'description' => __('To show search results from Indeed you will need a publish account. Obtain this <a href="https://ads.indeed.com/jobroll/signup">Here</a>'),
            ]
        );
        $this->add_control(
            'key_query',
            [
                'label' => esc_html__('Query', 'noo'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Design', 'noo'),
                'placeholder' => esc_html__('Enter terms', 'noo'),
                'description' => esc_html__('Enter terms to search for by default. By default terms are ANDed. Search for multiple terms at once by using the "or" keyword between each keyword.', 'noo')
            ]
        );
        $this->add_control(
            'location',
            [
                'label' => esc_html__('Location', 'noo'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('new york', 'noo'),
                'placeholder' => esc_html__('Enter Job Location', 'noo'),
            ]
        );
        $this->add_control(
            'job_type',
            [
                'label' => esc_html__('Job Type', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => $this->get_job_type('job_type'),
            ]
        );
        $this->add_control(
            'country',
            [
                'label' => esc_html__('Country', 'noo'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('us', 'noo'),
                'placeholder' => esc_html__('Enter country', 'noo'),
            ]
        );
        $this->add_control(
            'job_per_page',
            [
                'label' => esc_html__('Job Per Page', 'noo'),
                'type' => Controls_Manager::NUMBER,
                'min' => 5,
                'max' => 20,
                'step' => 1,
                'default' => 5,

            ]
        );
        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings();

        $type = get_term_by('slug', $settings['job_type'], 'job_type');
        if ($type) {
            $type->color = jm_get_job_type_color($type->term_id);
        } else {
            $type = new \stdClass();
            $type->name = $settings['job_type'];
            $type->color = '#f14e3b';
        }
        $args = array(
            'q' => $settings['key_query'],
            'l' => $settings['location'],
            'jt' => $settings['job_type'],
            'start' => 0,
            'limit' => absint($settings['job_per_page']),
            'co' => $settings['country'],
            'publisher' => $settings['public_id'],
        );
        $public_id = $settings['public_id'];
        $list_job = \Noo_Elementor_Job_Indeed::_get_indeed_jobs($args);
        if (!empty($list_job)) : ?>
            <div class="jobs post-loop">
                <div class="posts-loop-content">
                    <?php foreach ($list_job as $container) {
                        \Noo_Elementor_Job_Indeed::_indeed_one_job($container, $type);
                        continue;
                    } ?>
                    <div class="list_loadmore_job"></div>
                    <div class="loadmore-action">
                        <div class="btn btn-default btn-block btn-loadmore loadmore_job_indeed"
                             data-public-id="<?php echo $public_id; ?>"
                             data-query="<?php echo $settings['key_query']; ?>"
                             data-localtion="<?php echo $settings['location']; ?>"
                             data-job-type="<?php echo $settings['job_type']; ?>"
                             data-country="<?php echo $settings['country']; ?>"
                             data-limit="<?php echo $settings['job_per_page']; ?>"
                             data-max="<?php echo $settings['job_per_page']; ?>"><?php esc_html_e('Load More', 'noo'); ?>
                        </div>
                        <div class="noo-loader loadmore-loading">
                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p><?php _e('No results found, please try again later.', 'noo'); ?></p>
        <?php endif;

    }
}