<?php

namespace Noo_Elementor_Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Careerjet_API;

class Job_Careerjet extends Widget_Base
{
    public function get_name()
    {
        return 'noo_job_careerjet';
    }

    public function get_title()
    {
        return esc_html__('Noo Job CareerJet', 'noo');
    }

    public function get_icon()
    {
        return 'fa fa-briefcase';
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

    protected function register_controls()
    {
        $this->start_controls_section(
            'noo_job_careerjet',
            [
                'label' => esc_html__('Noo Job CareerJet', 'noo'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'keywords',
            [
                'label' => esc_html__('Keywords', 'noo'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __('Type keyword here', 'noo'),
                'default' => 'IT',
            ]
        );
        $this->add_control(
            'location',
            [
                'label' => esc_html__('Location', 'noo'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __('Enter location. ex:United State', 'noo'),
                'default' => 'United State',
            ]
        );
        $this->add_control(
            'type',
            [
                'label' => esc_html__('Job Type', 'noo'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __('Enter Job Type. ex:marketing', 'noo'),
                'default' => 'Backend'
            ]
        );
        $this->add_control(
            'number',
            [
                'label' => esc_html__('Job Per Page', 'noo'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 100,
                'step' => 1,
                'default' => 8,
            ]
        );
        $this->add_control(
            'search_form',
            [
                'label' => esc_html__('Show Search Form', 'noo'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'noo'),
                'label_off' => __('No', 'noo'),
                'return_value' => true,
                'default' => true,
            ]
        );
        $this->add_control(
            'aff_id',
            [
                'label' => esc_html__('Affiliate ID', 'noo'),
                'type' => Controls_Manager::TEXT,
                'description' => '<a target="_blank" href="http://www.careerjet.vn/partners/?ak=8cf0102af68c848437da3f877babe47a">Become a Careerjet affiliate & get Affililate ID</a>'
            ]
        );
        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings();
        $keywords = (!empty($settings['keywords'])) ? $settings['keywords'] : '';
        $location = (!empty($settings['location'])) ? $settings['location'] : '';
        $page = 1;
        $number = $settings['number'];
        $type = (!empty($settings['type'])) ? $settings['type'] : 'none';
        $search_form = ($settings['search_form']) ? true : false;
        $aff_id = (!empty($settings['aff_id'])) ? $settings['aff_id'] : '8cf0102af68c848437da3f877babe47a';

        ob_start();

        $page = isset($_GET['current_page']) ? $_GET['current_page'] : $page;

        $location = isset($_GET['location']) && !empty($_GET['location']) ? $_GET['location'] : $location;
        $keywords = isset($_GET['keywords']) && !empty($_GET['keywords']) ? $_GET['keywords'] : $keywords;
        $job_type = isset($_GET['type']) ? $_GET['type'] : $type;

        $api = new Careerjet_API('en_GB');

        $result = $api->search(array(
            'keywords' => $keywords,
            'location' => $location,
            'contractperiod' => $job_type,
            'pagesize' => $number,
            'page' => $page,
            'affid' => $aff_id,
        ));

        ob_start();

        if ($result->type == 'JOBS') {
            $total_jobs = $result->hits;
            $total_pages = $result->pages;

            ?>
            <?php if ($search_form): ?>

                <form action="" class="search-form job-careerjet-form">
                    <div class="advance-search-form-control">
                        <select name="type" class="form-control noo-select form-control-chosen">
                            <option value="none"><?php _e('All Types', 'noo'); ?></option>
                            <option value="f"><?php _e('Full Time', 'noo'); ?></option>
                            <option value="p"><?php _e('Part Time', 'noo'); ?></option>
                        </select>
                    </div>
                    <input type="text" name="keywords" class="form-control"
                           value="<?php echo $keywords; ?>"
                           placeholder="<?php _e('Enter your keyword', 'noo'); ?>"/>

                    <input type="text" name="location" class="form-control"
                           value="<?php echo $location; ?>"
                           placeholder="<?php _e('Enter your location', 'noo'); ?>"/>
                    <button type="submit" class="btn btn-primary btn-search">
                        <span><?php _e('Search', 'noo'); ?></span>
                    </button>
                </form>
            <?php endif; ?>

            <div class="jobs posts-loop job-careerjet">
                <div class="post-loop-title">
                    <h3><?php echo sprintf(_n('%s Job', '%s Jobs', $total_jobs, 'noo'), $total_jobs) ?></h3>
                </div>
                <div class="posts-loop-content">

                    <?php

                    if ($total_jobs > 0):
                        $jobs = $result->jobs;

                        foreach ($jobs as $job) {
                            ?>
                            <article class="noo_job job-careerjet-item">
                                <div class="loop-item-wrap">
                                    <div class="loop-item-content" style="width: 73%; float: left; padding-left:25px">
                                        <h2 class="loop-item-title">
                                            <a target="_blank"
                                               href="<?php echo $job->url; ?>"><?php echo $job->title; ?></a>
                                        </h2>
                                        <p class="content-meta">
                                            <span><?php _e('Salary:', 'noo'); ?><?php echo $job->salary; ?></span>
                                            <span class="job-company"><?php echo $job->company; ?></span>
                                            <span class="job-location">
										<i class="fa fa-map-marker"></i>
                                                <?php echo $job->locations; ?>
									</span>

                                            <span class="job-date">
										<time class="entry-date" datetime="<?php echo $job->date; ?>">
											<i class="fa fa-calendar"></i>
											<span itemprop="datePosted">
												<?php echo $job->date; ?>
											</span>
										</time>
									</span>
                                        </p>
                                    </div>
                                    <div class="show-view-more" style="float: right;">
                                        <a target="_blank" href="<?php echo $job->url; ?>"
                                           class="btn btn-primary"><?php _e('View more', 'noo'); ?></a>
                                    </div>
                                </div>

                            </article>
                            <?php
                        }

                    else: ?>

                        <p><?php _e('No results found, please try again later.', 'noo'); ?></p>

                    <?php endif; ?>
                </div>
                <div class="pagination list-center">
                    <?php echo $this->pagination($page, $total_pages); ?>
                </div>
            </div>
            <?php
        } else {
            ?>

            <p><?php _e('No results found, please try again later.', 'noo'); ?></p>

            <?php
        }

    }

    private function pagination($current, $total_pages)
    {

        $defaults = array(
            'base' => add_query_arg('current_page', '%#%'),
            'format' => '',
            'total' => $total_pages,
            'current' => $current,
            'prev_next' => true,
            'prev_text' => '<i class="fa fa-long-arrow-left"></i>',
            'next_text' => '<i class="fa fa-long-arrow-right"></i>',
            'show_all' => false,
            'end_size' => 1,
            'mid_size' => 1,
            'add_fragment' => '',
            //			'type'                   => 'list',
            'before' => '',
            'after' => '',
            'echo' => false,
            'use_search_permastruct' => true,
        );
        $page_links = paginate_links($defaults);

        $page_links = $defaults['before'] . $page_links . $defaults['after'];

        return $page_links;
    }

}