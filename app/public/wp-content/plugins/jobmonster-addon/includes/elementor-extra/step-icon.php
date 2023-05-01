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

class Step_Icon extends Widget_Base {
	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'noo-step-icon';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Noo Step Icon', 'noo' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-icon-box';
	}

	/**
	 * Get widget categories.
	 *
	 */
	public function get_categories() {
		return [ 'noo-element-widgets' ];
	}
	/*
	 * Depend Style
	 */
	public function get_style_depends() {
        return [
            'owl-carousel',
        ];
    }
	/*
	 * Depend Script
	 */
	public function get_script_depends() {
        return [
            'imageloaded',
            'owl-carousel',
            'noo-elementor',
        ];
    }
    /**
	 * Register Widget controls.
	 */
	protected function register_controls() {
		// Tab Content
		$this->noo_step_icon_option();
	}
	/*
	* Base Widget Config
	*/
	private function noo_step_icon_option(){
		$this->start_controls_section(
			'noo_step_icon_section',
			[
				'label' => esc_html__( 'General Options', 'noo' )
			]
		);
		$this->add_control(
			'layout_style',
			[
				'type' 		=> Controls_Manager::SELECT,
				'label_block' => true,
				'label'   	=> esc_html__('Style', 'noo' ),
				'default' 	=> 'simple',
				'options' 	=> [
					'simple'  => esc_html__( 'Simple', 'noo' ),
					'advanced' 	=> esc_html__( 'Advanced', 'noo' ),
				],
			]
		);
		$this->add_control(
        'text_editor',
	        [
	            'label' => esc_html__( 'Text', 'noo' ),
				'type' 	=> Controls_Manager::TEXTAREA,
				'name' 	=> 'text',
				'default' 		=> esc_html__( 'Text 1', 'noo' ),
				'placeholder' => esc_html__( 'Enter your text', 'noo' ),
				'rows' 		=> 10,
	        ]
      	);
      	$this->add_control(
			'noo_button_advanced',
			[
				'label'       => esc_html__( 'List Button', 'noo' ),
				'type'        => Controls_Manager::REPEATER,
				'default'     => [
					[
						'button_advanced_title' => esc_html__( 'Text 1', 'noo' ),
					],
					[
						'button_advanced_title' => esc_html__( 'Text 1', 'noo' ),
					],
					[
						'button_advanced_title' => esc_html__( 'Text 1', 'noo' ),
					],
				],
				'fields'      => [
					[
						'label' => esc_html__( 'Text', 'noo' ),
						'type' 	=> Controls_Manager::TEXT,
						'name' 	=> 'button_advanced_title',
						'dynamic' => [
							'active' => true,
						],
						'default' 		=> esc_html__( 'Text 1', 'noo' ),
						'placeholder' 	=> esc_html__( 'Enter your text', 'noo' ),
						'label_block' 	=> true,
					],
					[
						'label' => esc_html__( 'Link', 'noo' ),
						'type' 	=> Controls_Manager::URL,
						'name' 	=> 'button_advanced_link',
						'placeholder' => esc_html__( 'https://your-link.com', 'noo' ),
					],
					[
		               'label'  => esc_html__('Button Advenced Color', 'noo'),
		               'type'   => Controls_Manager::COLOR,
		               'name'   => 'button_advanced_color',
		           ]
				],
				'title_field' => '{{button_advanced_title}}',
				'condition' => [
                    'layout_style' => 'advanced',
                ]
			]
		);
      	$this->add_control(
           'background_advanced_image',
            [
                'label' => esc_html__('Background Image', 'noo'),
                'type'  => Controls_Manager::MEDIA,
                'condition' => [
                    'layout_style' => 'advanced',
                ]
            ]
       	);
		$this->add_control(
			'noo_step_icon',
			[
				'label'       => esc_html__( 'Step Icon', 'noo' ),
				'type'        => Controls_Manager::REPEATER,
				'default'     => [
					[
						'title' 	 => esc_html__( '1. Register an account to start', 'noo' ),
						'icon'		 => 'fa fa-home',
					],
					[
						'title' 	 => esc_html__( '1. Register an account to start', 'noo' ),
						'icon'		 => 'fa fa-home',
					],
					[
						'title' 	 => esc_html__( '1. Register an account to start', 'noo' ),
						'icon'		 => 'fa fa-home',
					],
				],
				'fields'      => [
					[
						'label' => esc_html__( 'Select icon', 'noo' ),
						'type' 	=> Controls_Manager::ICON,
						'name'	=> 'icon',
						'default' 	=> 'fa fa-star',
					],
					[
						'label' => esc_html__( 'Text', 'noo' ),
						'type' 	=> Controls_Manager::TEXT,
						'name' 	=> 'title',
						'dynamic' => [
							'active' => true,
						],
						'default' 		=> esc_html__( 'This is the heading', 'noo' ),
						'placeholder' 	=> esc_html__( 'Enter your title', 'noo' ),
						'label_block' 	=> true,
					],
					[
						'label' => esc_html__( 'Link', 'noo' ),
						'type' 	=> Controls_Manager::URL,
						'name' 	=> 'title_link',
						'placeholder' => esc_html__( 'https://your-link.com', 'noo' ),
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
					5 => 5,
				],
			]
		);

		$this->end_controls_section();
	}
	/*
	* Render Widget
	*/
	protected function render() {
		// Get settings.
		$settings = $this->get_settings();

		$layout_style = $settings['layout_style'];
		$mobile_class = ( ! empty( $settings['columns_mobile'] ) ? 'noo-mobile-' . $settings['columns_mobile'] : 'noo-mobile-1' );
		$tablet_class = ( ! empty( $settings['columns_tablet'] ) ? 'noo-tablet-' . $settings['columns_tablet'] : '' );
		$desktop_class = ( ! empty( $settings['columns'] ) ? 'noo-desktop-' . $settings['columns'] : '' );

		$this->add_render_attribute( 'step-grid', 'class', ['noo-step-icon-wrap',$desktop_class,$tablet_class,$mobile_class]);
		$step_grid_class = $this->get_render_attribute_string( 'step-grid' );
		$this->add_render_attribute( 'button', 'class', [ 'icon-button' ] );
		?>
		<div class="noo-step-icon <?php if('advanced' == $layout_style ) { echo 'noo-step-icon-advanced'; }  ?>" style="<?php if('advanced' == $layout_style) { echo 'background: url('.esc_url($settings['background_advanced_image']['url']).') no-repeat center'; }else{echo 'background: transparent';}  ?>">
			<?php if(('simple' == $layout_style )): ?>	
				<ul <?php echo implode(' advanced_search.php',[$step_grid_class]);?>>
					<?php
					$i = 0;

					foreach ( $settings['noo_step_icon'] as $value ) {

						?>
						<li class="noo-step-icon-info">
								
							<div class="noo-step-icon-item">
								<?php
									$link_props = ' href="' . esc_url( $value['title_link']['url'] ) . '" ';
									if ( $value['title_link']['is_external'] === 'on' ) {
										$link_props .= ' target="_blank" ';
									}
									if ( $value['title_link']['nofollow'] === 'on' ) {
										$link_props .= ' rel="nofollow" ';
									}
								?>
								<a <?php echo esc_attr($link_props);?> >
									<span class="<?php echo esc_attr($value['icon']);?> noo-step-icon-class"></span>	
									<?php if ( $value['title'] !== '' ) { ?>
										<span class="noo-step-icon-title">
											<?php echo wp_kses_post($value['title']); ?>
										</span>
									<?php } ?>		
								</a>

							</div>

						</li>

					<?php } ?>

				</ul><!-- . noo-step-icon-wrap -->

			<?php endif; ?>

			<?php if(('advanced' == $layout_style )): ?>

				<?php 
					if ($settings['text_editor'] != '') {
						?>
							<div class="noo-step-icon-content container-boxed max">
								 <?php echo $settings['text_editor'] ?>
							</div>
						<?php
					}
				 ?>

				 <?php 
				 	?>
					<div class="noo-step-icon-button container-boxed max">
				 	<?php
				 	$i = 0;
					foreach ($settings['noo_button_advanced'] as  $value) {
					 	
				 	?>
						<div class="noo-step-icon-btn">
							<?php
								$link_button = ' href="' . esc_url( $value['button_advanced_link']['url'] ) . '" ';
								if ( $value['button_advanced_link']['is_external'] === 'on' ) {
									$link_button .= ' target="_blank" ';
								}
								if ( $value['button_advanced_link']['nofollow'] === 'on' ) {
									$link_button .= ' rel="nofollow" ';
								}
							?>
							<a <?php echo esc_attr($link_button);?> class="btn btn-primary" <?php if($value['button_advanced_color'] != ''){ ?>style="background-color: <?php echo esc_attr($value['button_advanced_color']); ?> ;" <?php } ?>>
								<?php if ( $value['button_advanced_title'] !== '' ) { ?>
									<span><?php echo wp_kses_post($value['button_advanced_title']); ?></span>
								<?php } ?>
							</a>
						</div>
				 	<?php
				 	}
				 	?>
					</div>
				 	<?php
				 ?>

				<div class="noo-step-icon-advanced-list">

					<ul <?php echo implode(' advanced_search.php',[$step_grid_class]);?>>
						<?php
						$i = 0;

						foreach ( $settings['noo_step_icon'] as $value ) {

							?>
							<li class="noo-step-icon-info">

							
								<div class="noo-step-icon-item">
									<?php
										$link_props = ' href="' . esc_url( $value['title_link']['url'] ) . '" ';
										if ( $value['title_link']['is_external'] === 'on' ) {
											$link_props .= ' target="_blank" ';
										}
										if ( $value['title_link']['nofollow'] === 'on' ) {
											$link_props .= ' rel="nofollow" ';
										}
									?>
									<a <?php echo esc_attr($link_props);?> >
										<span class="<?php echo esc_attr($value['icon']);?> noo-step-icon-class"></span>	
										<?php if ( $value['title'] !== '' ) { ?>
											<span class="noo-step-icon-title">
												<?php echo wp_kses_post($value['title']); ?>
											</span>
										<?php } ?>
									</a>
																	
								</div>
										
							</li>

						<?php } ?>

					</ul><!-- . noo-step-icon-wrap -->

				</div>	

			<?php endif; ?>

		</div><!-- .noo-step-icon -->

<?php	
	
	}
}