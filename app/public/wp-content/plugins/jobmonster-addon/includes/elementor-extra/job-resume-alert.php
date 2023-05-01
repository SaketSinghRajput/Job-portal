<?php

namespace Noo_Elementor_Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Noo_Resume_Alert;
use Noo_Job_Alert;

class Job_Resume_Alert extends Widget_Base
{
    public function get_name()
    {
        return 'noo_job_resume_alert';
    }

    public function get_title()
    {
        return esc_html__('Noo Job/Resume Alert', 'noo');
    }

    public function get_icon()
    {
        return 'fa fa-building';
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
            'noo_job_resume_alert',
            [
                'label' => esc_html__('Noo Job Resume Alert', 'noo'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'alert_type',
            [
                'label' => esc_html__('Select Alert Type', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'job' => __('Job', 'noo'),
                    'resume' => __('Resume', 'noo'),
                ],
                'default' => 'job',
            ]
        );
        $this->add_control(
            'icon',
            [
                'label' => esc_html__('Icon', 'noo'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-star',
                    'library' => 'solid',
                ]
            ]
        );
        $this->add_control(
            'button_text',
            [
                'label' => esc_html__('Button Text', 'noo'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Create Alert', 'noo'),
                'placeholder' => __('Type your label here', 'noo'),

            ]
        );

        $this->end_controls_section();
        $this->start_controls_section(
            'noo_job_resume_style',
            [
                'label' => esc_html__('Style', 'noo'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'primary_color',
            [
                'label' => esc_html__('Primary Color', 'noo'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .noo-btn-job-alert-form i,span' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .noo-btn-resume-alert-form i,span' => 'color: {{VALUE}}',
                ]
            ]
        );
        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings();
        ?>
        <div class="noo-job-resume-alert-elementor-widget">
            <div class="noo-job-resume-alert">
                <?php if ($settings['alert_type'] == 'job') {
                    if (Noo_Job_Alert::enable_job_alert()) : ?>
                        <div class="noo-btn-job-alert-form shortcode">
                            <?php \Elementor\Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']); ?>
                            <span><?php echo esc_html($settings['button_text']); ?></span>
                        </div>
                    <?php endif;
                    noo_get_layout('forms/job_alert_form_popup');
                } else {
                    if (Noo_Resume_Alert::enable_resume_alert()): ?>
                        <div class="noo-btn-resume-alert-form shortcode">
                            <?php \Elementor\Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']); ?>
                            <span><?php echo esc_html($settings['button_text']); ?></span>
                        </div>
                    <?php endif;
                    noo_get_layout('forms/resume_alert_form_popup');
                } ?>
            </div>
        </div>

    <?php }

}