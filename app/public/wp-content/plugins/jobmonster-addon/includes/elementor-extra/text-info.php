<?php

namespace Noo_Elementor_Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;

class Text_Info extends Widget_Base
{
    public function get_name()
    {
        return 'noo_text_info';
    }

    public function get_icon()
    {
        return 'fa fa-info';
    }

    public function get_title()
    {
        return esc_html__('Noo Text Info', 'noo');
    }

    public function get_categories()
    {
        return ['noo-element-widgets'];
    }

    protected function register_controls()
    {
        // Tab Content
        $this->noo_text_info_option();

        // Tab Style
        $this->noo_text_info_style();
    }

    private function noo_text_info_option()
    {
        $this->start_controls_section(
            'noo_text_info',
            [
                'label' => esc_html__('Noo Text Info', 'noo'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'align',
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
                    'right' => [
                        'title' => esc_html__('Right', 'noo'),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'default' => 'left',
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .noo_text_info' => 'text-align: {{VALUE}};',
                ],
            ]

        );

        $this->add_control(
            'title',
            [
                'label' => esc_html__('Title', 'noo'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Noo Title', 'noo'),
                'placeholder' => esc_html__('Type your title here', 'noo'),
            ]
        );
        $this->add_control(
            'text_editor',
            [
                'label' => esc_html__('Description', 'noo'),
                'type' => Controls_Manager::WYSIWYG,
                'default' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'noo'),
                'placeholder' => esc_html__('type your description here', 'noo'),
            ]
        );
        $this->end_controls_section();
    }

    private function noo_text_info_style()
    {
        $this->start_controls_section(
            'noo_general_text_info',
            [
                'label' => esc_html__('General', 'noo'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'padding',
            [
                'label' => esc_html__('Padding', 'noo'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .noo-heading-sc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'noo_title_text_info',
            [
                'label' => esc_html__('Title', 'noo'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => esc_html__('Typography for Title', 'noo'),
                'selector' => '{{WRAPPER}} .noo-heading-sc .noo-title-sc',
            ]
        );
        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Title Color', 'noo'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .noo-title-sc' => 'color: {{VALUE}} !important',
                ],
            ]
        );
        $this->add_control(
            'title_space',
            [
                'label' => esc_html__('Space', 'noo'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .noo-title-sc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'noo_desc_text_info',
            [
                'label' => esc_html__('Description', 'noo'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'desc_typography',
                'label' => esc_html__('Typography for Title', 'noo'),
                'selector' => '{{WRAPPER}} .noo-subtitle-sc',
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => esc_html__('Description Color', 'noo'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .noo-subtitle-sc' => 'color: {{VALUE}}',
                ],
            ]
        );
    }

    protected function render()
    {
        $settings = $this->get_settings();
        ?>
        <div class="noo-heading-sc noo_text_info noo-text-info-widget">
            <?php if (!empty($settings['title'])) : ?>
                <h3 class="noo-title-sc">
                    <?php echo $settings['title'] ?>
                </h3>
            <?php endif; ?>

            <?php if (!empty($settings['text_editor'])) : ?>
                <div class="noo-subtitle-sc <?php echo !empty($settings['bottom_border']) ? 'border-left' : '' ?>">
                    <?php echo $settings['text_editor']; ?>
                </div>
            <?php endif; ?>

        </div>
        <?php
    }
}