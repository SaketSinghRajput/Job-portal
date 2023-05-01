<?php

namespace Noo_Elementor_Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Background;

class Counter extends Widget_Base
{
    /**
     * Get widget name.
     */
    public function get_name()
    {
        return 'noo_counter';
    }

    /**
     * Get widget title.
     */
    public function get_title()
    {
        return esc_html__('Noo Counter', 'noo');
    }

    /**
     * Get widget icon.
     */
    public function get_icon()
    {
        return 'fa fa-calculator';
    }

    /**
     * Get widget categories.
     *
     */
    public function get_categories()
    {
        return ['noo-element-widgets'];
    }

    public function get_script_depends()
    {
        return [
            'vendor-countTo',
            'vendor-appear',
            'vendor-easing',
            'noo-elementor',
        ];
    }

    protected function register_controls()
    {
        $this->noo_counter_option();

    }

    /*
    * Config
    */
    private function noo_counter_option()
    {
        $this->start_controls_section(
            'noo-element-widgets',
            [
                'label' => esc_html__('General Options', 'noo')
            ]
        );
        $this->add_control(
            'data',
            [
                'label' => esc_html__('Data', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'public_job' => __('Public Job', 'noo'),
                    'public_resume' => __('Public Resumes', 'noo'),
                    'all_job' => __('All Job', 'noo'),
                    'all_resume' => __('All Resume', 'noo'),
                    'all_company' => __('All Companies', 'noo'),
                    'all_application' => __('All Applications', 'noo'),
                    'user_all' => __('All User', 'noo'),
                    'user_candidate' => __('Candidate User', 'noo'),
                    'user_employer' => __('Employer User', 'noo'),
                    'custom' => __('Custom Number', 'noo'),
                ],
                'default' => 'public_job',
            ]
        );
        $this->add_control(
            'number',
            [
                'label' => __('Number', 'noo'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __('Enter Number here', 'noo'),
                'condition' => [
                    'data' => ['custom']
                ],
            ]
        );
        $this->add_control(
            'text',
            [
                'label' => __('Text', 'noo'),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'data' => 'custom',
                ],
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
           'color_text',
           [
               'label'  => esc_html__('Text Color', 'fitsica'),
               'type'   => Controls_Manager::COLOR,
               'selectors' => [
                   '{{WRAPPER}} .noo-counter-item .noo-counter-icon-content .noo-counter' => 'color: {{VALUE}}',
                   '{{WRAPPER}} .noo-counter-item .noo-counter-icon-content .noo-counter-text' => 'color: {{VALUE}}',
               ],
        ]
        );
        $this->add_control(
            'visibility',
            [
                'label' => esc_html__('Visibility', 'noo'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'all' => __('All Devices', 'noo'),
                    'hidden-phone' => __('Hidden Phone', 'noo'),
                    'hidden-tablet' => __('Hidden Tablet', 'noo'),
                    'hidden-pc' => __('Hidden PC', 'noo'),
                    'visible-phone' => __('Visible Phone', 'noo'),
                    'visible-tablet' => __('Visible Tablet', 'noo'),
                    'visible-pc' => __('Visible PC', 'noo'),
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

    protected function render()
    {
        // Get settings.
        $settings = $this->get_settings();
        $visibility = ($settings['visibility'] != '') && ($settings['visibility'] != 'all') ? esc_attr($settings['visibility']) : '';
        $class = 'noo-counter-icon clearfix';
        $class .= $this->noo_visibility_class($visibility);
        $class = ($class != '') ? ' class="' . $class . '"' : '';
        $number = ($settings['number'] != '') ? $settings['number'] : 99;
        $number_show = '';
        $text_show = '';
        $text = $settings['text'];
        if (!empty($settings['data'])) {
            switch ($settings['data']) {
                case 'public_job' :
                    $job_count = wp_count_posts('noo_job');
                    $number_show = $job_count->publish;
                    $text_show = ($text != '') ? $text : __('Public Jobs', 'noo');
                    break;
                case 'public_resume' :
                    $resume_count = wp_count_posts('noo_resume');
                    $number_show = $resume_count->publish;
                    $text_show = ($text != '') ? $text : __('Public Resumes', 'noo');
                    break;
                case 'all_job' :
                    $job_count = wp_count_posts('noo_job');
                    $number_show = ($job_count->publish + $job_count->pending);
                    $text_show = ($text != '') ? $text : __('All Jobs', 'noo');
                    break;
                case 'all_resume' :
                    $resume_count = wp_count_posts('noo_resume');
                    $number_show = ($resume_count->publish + $resume_count->pending);
                    $text_show = ($text != '') ? $text : __('All Resumes', 'noo');
                    break;
                case 'all_company' :
                    $company_count = wp_count_posts('noo_company');
                    $number_show = ($company_count->publish + $company_count->pending);
                    $text_show = ($text != '') ? $text : __('All Companies', 'noo');
                    break;
                case 'all_application' :
                    $application_count = wp_count_posts('noo_application');
                    $number_show = ($application_count->publish + $application_count->pending);
                    $text_show = ($text != '') ? $text : __('All Applications', 'noo');
                    break;
                case 'user_all' :
                    $result = count_users();
                    $number_show = $result['total_users'];
                    $text_show = ($text != '') ? $text : __('All Users', 'noo');
                    break;
                case 'user_candidate' :
                    $result = count_users();
                    $number_show = $result['avail_roles']['candidate'];
                    $text_show = ($text != '') ? $text : __('Candidate', 'noo');
                    break;
                case 'user_employer' :
                    $result = count_users();
                    $number_show = $result['avail_roles']['employer'];
                    $text_show = ($text != '') ? $text : __('Employer', 'noo');
                    break;
                case 'custom' :
                    $number_show = $number;
                    $text_show = ($text != '') ? $text : '';
                    break;
            }
        }
        ?>
        <div<?php echo($class); ?>>
            <div class="noo-counter-item noo-counter-elementor">
                <div class="noo-counter-font-icon pull-left">
                    <?php \Elementor\Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']); ?>
                </div>
                <div class="noo-counter-icon-content pull-left">
                    <div data-number="<?php echo esc_attr($number_show); ?>"
                         class="noo-counter"><?php echo esc_html($number_show); ?></div>
                    <span class="noo-counter-text"><?php echo esc_html($text_show); ?></span>
                </div>
            </div>
        </div>
        <?php

    }
}