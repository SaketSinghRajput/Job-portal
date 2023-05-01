<?php
/**
 * Noo Service.
 *
 * @since 1.0.0
 */
namespace Noo_Elementor_Widgets;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Utils;
use \Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;

class Service extends Widget_Base {
	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'noo-service';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Noo Service', 'noo' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'fa fa-server';
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
		$this->noo_service_option();

		// Tab Style
		$this->noo_service_style();
	}
	/*
	* Config
	*/
	private function noo_service_option(){
		$this->start_controls_section(
			'noo_service_section',
			[
				'label' => esc_html__( 'General Options', 'noo' )
			]
		);
		$this->add_control(
			'noo_services',
			[
				'label'       => esc_html__( 'Services', 'noo' ),
				'type'        => Controls_Manager::REPEATER,
				'default'     => [
					[
						'title' => esc_html__( 'CROSS BROWSERS', 'noo' ),
						'text'  => esc_html__( 'malesuada, laoreet lacus non, lacinia felis. Phasellus pretium enim tellus, et aliquet mi fringilla non. Aenean lorem libero, adipiscing.', 'noo' ),
					],
					[
						'title' => esc_html__( 'EASY CUSTOMIZATION', 'noo' ),
						'text'  => esc_html__( 'malesuada, laoreet lacus non, lacinia felis. Phasellus pretium enim tellus, et aliquet mi fringilla non. Aenean lorem libero, adipiscing.', 'noo' ),
					],
					[
						'title' => esc_html__( 'MODERN DESIGN', 'noo' ),
						'text'  => esc_html__( 'malesuada, laoreet lacus non, lacinia felis. Phasellus pretium enim tellus, et aliquet mi fringilla non. Aenean lorem libero, adipiscing.', 'noo' ),
					],
					[
						'title' => esc_html__( 'QUICK SUPPORT', 'noo' ),
						'text'  => esc_html__( 'malesuada, laoreet lacus non, lacinia felis. Phasellus pretium enim tellus, et aliquet mi fringilla non. Aenean lorem libero, adipiscing.', 'noo' ),
					],
					[
						'title' => esc_html__( 'CROSS BROWSERS', 'noo' ),
						'text'  => esc_html__( 'malesuada, laoreet lacus non, lacinia felis. Phasellus pretium enim tellus, et aliquet mi fringilla non. Aenean lorem libero, adipiscing.', 'noo' ),
					],
					[
						'title' => esc_html__( 'EASY CUSTOMIZATION', 'noo' ),
						'text'  => esc_html__( 'malesuada, laoreet lacus non, lacinia felis. Phasellus pretium enim tellus, et aliquet mi fringilla non. Aenean lorem libero, adipiscing.', 'noo' ),
					],
				],
				
				'fields'      => [
					[
						'label' 	=> esc_html__( 'Icons', 'noo' ),
						'name'		=> 'set_icon',
						'type' 		=> Controls_Manager::ICON,
					],
					[
						'type'    => Controls_Manager::TEXT,
						'name'    => 'title',
						'label_block' => true,
						'label'   => esc_html__( 'Title & Description', 'noo' ),
						'default' => esc_html__( 'Service Title', 'noo' ),
					],
					[
						'type'        => Controls_Manager::TEXTAREA,
						'name'        => 'text',
						'placeholder' => esc_html__( 'Plan Features', 'noo' ),
						'default'     => esc_html__( 'Feature', 'noo' ),
					],
					[
						'label'     => esc_html__( 'Button', 'noo' ),
						'type'      => Controls_Manager::HEADING,
						'name' 		=> 'button_heading',
						'separator' => 'before',
					],
					[
						'label'   => '<i class="fa fa-minus-circle"></i> ' . esc_html__( 'Hide', 'noo' ),
						'type'    => Controls_Manager::SWITCHER,
						'name'	  => 'button_hide',
						'default' => false,
					],
					[
						'type'        => Controls_Manager::TEXT,
						'name'		  => 'button_text',
						'label'       => esc_html__( 'Button text', 'noo' ),
						'placeholder' => esc_html__( 'Enter text', 'noo' ),
						'default'     => esc_html__( 'Add text', 'noo' ),
						'condition'   => [
							'button_hide'    => '',
						],
					],
					[
						'label' => esc_html__( 'Button Link', 'noo' ),
						'type' 	=> Controls_Manager::URL,
						'name' 	=> 'button_link',
						'placeholder' => esc_html__( 'https://your-link.com', 'noo' ),
						'condition'   => [
							'button_hide' => '',
						]
					],
					[
						'label'   =>  esc_html__( 'Hover Active', 'noo' ),
						'type'    => Controls_Manager::SWITCHER,
						'name'	  => 'hover_active',
						'default' => false,
					],
				],
				'title_field' => '{{title}}',
			]
		);
		// Columns.
		$this->add_responsive_control(
			'columns',
			[
				'type'           => Controls_Manager::SELECT,
				'label'          => '<i class="fa fa-columns"></i> ' . esc_html__( 'Columns', 'noo' ),
				'default'        => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'options'        => [
				    1 => 1,
					2 => 2,
					3 => 3,
					4 => 4,
				],
			]
		);


		$this->add_responsive_control(
			'item_spacing',
			[
				'label'     => esc_html__( 'Item Spacing', 'noo' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 15,
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .noo-service-widget'   => 'margin: -{{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .noo-service-widget .noo-service-item-wrap'   => 'padding: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}
	/*
	* Service Widget Style
	*/
	private function noo_service_style(){
		/*------------ Default style -----------------------*/

		$this->start_controls_section(
			'noo_icon_style_section',
			[
				'label' => esc_html__( 'Icon style', 'noo' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);


		// Text Content
		$this->add_control(
			'i_content_heading',
			[
				'label' => esc_html__( 'Text Content', 'noo' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'i_size',
			[
				'label' => esc_html__( 'Icon Size', 'noo' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 15,
						'max' => 300,
					],
				],
				'default' => [
					'size' => 28,
				],
				'selectors' => [
					'{{WRAPPER}} .noo-service-widget .noo-service-box i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs( 'i_content_options' );

		$this->start_controls_tab(
			'i_content_normal',
			[
				'label' => esc_html__( 'Normal', 'noo' ),
			]
		);
		$this->add_control(
			'i_icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'noo' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}  .noo-service-box i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'i_content_hover',
			[
				'label' => esc_html__( 'Hover', 'noo' ),
			]
		);
		$this->add_control(
			'i_icon_color_hover',
			[
				'label' => esc_html__( 'Icon Color', 'noo' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .noo-service-box:hover  i' => 'color: {{VALUE}};',
				],
			]
		);
	}
	/*
	* Render Widget
	*/
	protected function render() {
		// Get settings.
		$settings = $this->get_settings();
		$mobile_class = ( ! empty( $settings['columns_mobile'] ) ? 'noo-mobile-' . $settings['columns_mobile'] : '' );
		$tablet_class = ( ! empty( $settings['columns_tablet'] ) ? 'noo-tablet-' . $settings['columns_tablet'] : '' );
		$desktop_class = ( ! empty( $settings['columns'] ) ? 'noo-desktop-' . $settings['columns'] : '' );
		$this->add_render_attribute( 'service-grid', 'class', ['noo-grid-col',$desktop_class,$tablet_class,$mobile_class] );
		$service_grid_class = $this->get_render_attribute_string( 'service-grid' );
		?>
		<div class="noo-service-widget">
			<div <?php echo implode('',[$service_grid_class]);?>>
				<?php 
					foreach ( $settings['noo_services'] as $service ) {
						echo '<div class="noo-service-item-wrap noo-grid-item">'
						?>
							<div class="noo-service-box br4 <?php echo ($service['hover_active'])? 'hover_active': ''; ?>">
								<?php
								if ( ! empty( $service['title'] ) || ! empty( $service['text'] ) ) { ?>
									<div class="noo-service-box-content">
										<div>
											<?php
												if(!empty($service['set_icon'])){
													echo '<i class="'. $service['set_icon'] .'"></i>';
												}
											?>
											
											<?php if ( ! empty( $service['title'] ) ) { ?>
												<h6 class="title"><?php echo esc_attr( $service['title'] ); ?></h6>
										</div>
											<?php
										}
										if ( ! empty( $service['text'] ) ) { ?>
											<p class="desc"><?php echo ( $service['text'] ); ?></p>
										<?php } ?>

										<?php 
											if ( '' === $service['button_hide']) {
												$link_props = ' href="' . esc_url( $service['button_link']['url'] ) . '" ';
												if ( $service['button_link']['is_external'] === 'on' ) {
													$link_props .= ' target="_blank" ';
												}
												if ( $service['button_link']['nofollow'] === 'on' ) {
													$link_props .= ' rel="nofollow" ';
												} ?>
												<a class="link" <?php echo esc_attr($link_props);?>><?php echo esc_html($service['button_text']);?></a>
										<?php } ?>
										
									</div><!-- /.obfx-service-box-content -->
								<?php } ?>
							</div><!-- /.obfx-service-box -->
							<?php
							if ( ! empty( $service['link']['url'] ) ) {
								echo '</a>';
							} ?>
						</div><!-- /.obfx-grid-wrapper -->
						<?php
					}// End foreach().
				?>
			</div>
		</div>
	<?php	
	}
}