<?php
/**
 * Noo Call to Action Widget.
 *
 * @since 1.0.0
 */
namespace Noo_Elementor_Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;

class Call_To_Action extends Widget_Base {
	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'noo-call-to-action';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Noo Call To Action', 'noo' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'fa fa-plus';
	}

	/**
	 * Get widget categories.
	 *
	 */
	public function get_categories() {
		return [ 'noo-element-widgets' ];
	}

    /**
	 * Register Widget controls.
	 */
	protected function register_controls() {
		// Tab Content
		$this->noo_call_to_action_option();

		// Tab Style
		$this->noo_call_to_action_style();
	}
	/*
	* Config
	*/
	private function noo_call_to_action_option(){
		$this->start_controls_section(
			'noo_call_to_action_section',
			[
				'label' => esc_html__( 'General Options', 'noo' )
			]
		);
		$this->add_control(
			'text_align',
			[
				'label' => esc_html__( 'Alignment', 'noo' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'noo' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'noo' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'noo' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'left',
				'selectors'      => [
					'{{WRAPPER}} .noo-register-company' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'action_icon',
			[
				'label' => esc_html__( 'Select icon', 'noo' ),
				'type' => Controls_Manager::ICON,
				'default' => 'fa fa-book',
			]
		);
		$this->add_control(
			'heading',
			[
				'label' 		=> esc_html__( 'Title and Description', 'noo' ),
				'type' 			=> Controls_Manager::HEADING,
				'separator' 	=> 'before',
			]
		);
		$this->add_control(
			'title',
			[
				'label' 		=> esc_html__( 'Title & Description', 'noo' ),
				'type' 			=> Controls_Manager::TEXT,
				'show_label' 	=> false,
				'label_block'	=> true,
				'default' 		=> esc_html__( 'Default title', 'noo' ),
				'placeholder' 	=> esc_html__( 'Type your title here', 'noo' ),
			]
		);
		$this->add_control(
			'desc',
			[
				'label' 		=> esc_html__( 'Description', 'noo' ),
				'type' 			=> Controls_Manager::CODE,
				'row'			=> 5,
				'show_label' 	=> false,
				'default' 		=> esc_html__( 'Default description', 'noo' ),
				'placeholder' 	=> esc_html__( 'Type your description here', 'noo' ),
			]
		);
		$this->add_control(
			'buton_heading',
			[
				'label' 	=> esc_html__( 'Button', 'noo' ),
				'type' 		=> Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'button_text',
			[
				'label' 		=> esc_html__( 'Button Text', 'noo' ),
				'type' 			=> Controls_Manager::TEXT,
				'default' 		=> esc_html__( 'Post a Job', 'noo' ),
				'placeholder' 	=> esc_html__( 'Type your title here', 'noo' ),
			]
		);
		$this->add_control(
			'button_link',
			[
				'label' => esc_html__( 'Link', 'noo' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'noo' ),
				'show_external' => true,
				'default' => [
					'url' => 'https://your-link.com',
					'is_external' => true,
					'nofollow' => true,
				],
			]
		);

		$this->end_controls_section();
	}
	/*
	* Style
	*/
	private function noo_call_to_action_style(){
		$this->start_controls_section(
			'noo_call_to_action_general_section',
			[
				'label' => esc_html__( 'General', 'noo' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'padding',
			[
				'label' => esc_html__( 'Padding', 'noo' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .noo-register-company' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'noo' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .noo-register-company' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background',
				'label' => esc_html__( 'Background', 'noo' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .noo-register-company',
			]
		);
		$this->add_control(
			'icon_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Icon Color', 'noo' ),
				'selectors' => [
					'{{WRAPPER}} .noo-register-company .noo-icon-register'       => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'fitsica' ),
				'type' => Controls_Manager::SLIDER,
				'default'   => [
					'size' 	=> 60,
				],
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .noo-register-company .noo-icon-register' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'noo_call_to_action_title_section',
			[
				'label' => esc_html__( 'Title', 'noo' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'title_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'noo' ),
				'selectors' => [
					'{{WRAPPER}} .noo-register-company .register-title'       => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_group_control(
	        Group_Control_Typography::get_type(),
	        [
	            'name' => 'title_typography',
	            'label' => esc_html__( 'Typography for Title', 'noo' ),
	            'selector' => '{{WRAPPER}} .register-title',
	        ]
	      );

		$this->end_controls_section();
		// Description
		$this->start_controls_section(
			'noo_call_to_action_desc_section',
			[
				'label' => esc_html__( 'Description', 'noo' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'desc_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'noo' ),
				'selectors' => [
					'{{WRAPPER}} .noo-register-company .register-sub-title'       => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_group_control(
	        Group_Control_Typography::get_type(),
	        [
	            'name' => 'desc_typography',
	            'label' => esc_html__( 'Typography for Description', 'noo' ),
	            'selector' => '{{WRAPPER}} .register-sub-title',
	        ]
	      );
		$this->end_controls_section();
		// Button
		$this->start_controls_section(
			'noo_call_to_action_button_section',
			[
				'label' => esc_html__( 'Button', 'noo' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->start_controls_tabs( 'noo_job_location_style' );

		$this->start_controls_tab(
			'button_normal',
			[
				'label' => esc_html__( 'Normal', 'noo' ),
			]
		);
		$this->add_control(
			'button_bg_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background Color', 'noo' ),
				'selectors' => [
					'{{WRAPPER}} .noo-register-company .button-link'       => 'background-color: {{VALUE}};border-color:{{VALUE}};',
				]
			]
		);
		$this->add_control(
			'button_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'noo' ),
				'selectors' => [
					'{{WRAPPER}} .noo-register-company .button-link'       => 'color: {{VALUE}};',
				]
			]
		);
		

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_hover',
			[
				'label' => esc_html__( 'Hover', 'noo' ),
			]
		);
		$this->add_control(
			'button_hover_bg_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background Color', 'noo' ),
				'selectors' => [
					'{{WRAPPER}} .noo-register-company .button-link:hover'       => 'background-color: {{VALUE}};border-color:{{VALUE}};',
				]
			]
		);
		$this->add_control(
			'button_hover_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'noo' ),
				'selectors' => [
					'{{WRAPPER}} .noo-register-company .button-link:hover'       => 'color: {{VALUE}};',
				]
			]
		);
		

		$this->end_controls_tab();

		$this->end_controls_tabs();
		
		$this->end_controls_section();
	}
	/*
	* Render Widget
	*/
	protected function render(){
		// Get settings.
		$settings = $this->get_settings();

		
		?>
		
			<div class="noo-register-company register-elementor">
				<?php
				$action_icon = !empty( $settings['action_icon'] );
				if ( $action_icon ) {
					$this->add_render_attribute( 'action-icon', 'class', [$settings['action_icon'],'noo-icon-register'] );
					$this->add_render_attribute( 'action-icon', 'aria-hidden', 'true' );
					echo '<span ' . $this->get_render_attribute_string( 'action-icon' ) . '></span>';
				}

				echo '<h4 class="register-title">' . $settings['title'] . '</h4>';				
				echo '<div class="register-sub-title">'. $settings['desc'] . '</div>';
				
				if ( ! empty( $settings['button_link']['url'] ) ) {
					$link_props = ' href="' . $settings['button_link']['url'] . '" ';
					if ( $settings['button_link']['is_external'] === 'on' ) {
						$link_props .= ' target="_blank" ';
					}
					if ( $settings['button_link']['nofollow'] === 'on' ) {
						$link_props .= ' rel="nofollow" ';
					}
					echo '<a' . $link_props . ' class="button-link btn-register">';

					 	echo esc_html($settings['button_text']);

					echo '</a>';
				} ?>
			</div>
		
	<?php
	}
}