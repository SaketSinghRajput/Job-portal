<?php
/**
 * Noo Elementor Base Widget.
 *
 * @since 1.0.0
 */
namespace Noo_Elementor_Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Contact_Form extends Widget_Base {
	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'noo-contact-form';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Noo Contact Form 7', 'noo' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-wordpress';
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
		$this->noo_contact_form_option();
	}
		/**
	 * Get ID Contact Form 7.
	 */
	private function get_wpcf7_id() {
		$options = array();
		if(class_exists('WPCF7')){
			$args = array(
				'post_type' => 'wpcf7_contact_form', 
				'posts_per_page' => -1
			); 
			$cf7Forms = get_posts( $args );
			foreach ($cf7Forms as $value) {
				$options[$value->ID] = $value->post_title;
			}
		}
		return $options;
	}
	/*
	* Config
	*/
	private function noo_contact_form_option(){
		$this->start_controls_section(
			'noo_contact_form_section',
			[
				'label' => esc_html__( 'General Options', 'noo' )
			]
		);
		$this->add_control(
			'title',
			[
				'type'        => Controls_Manager::TEXT,
				'label'       => esc_html__( 'Title & Description', 'noo' ),
				'label_block' => true,
				'placeholder' => esc_html__( 'Enter text heading', 'noo' ),
				'default'     => esc_html__( 'Don\'t miss', 'noo' ),
			]
		);
		$this->add_control(
			'desc',
			[
				'type'        => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Enter description', 'noo' ),
				'default'     => esc_html__( 'the latest tech news from us!', 'noo' ),
			]
		);
		$this->add_control(
			'noo_wpcf7',
			[
				'label' 	=> esc_html__( 'Select Form', 'noo' ),
				'type' 		=> Controls_Manager::SELECT,
				'multiple' 	=> true,
				'options' 	=> $this->get_wpcf7_id(),
			]
		);
		$this->add_control(
			'mailchimp_style',
			[
				'label' => esc_html__( 'Style for MailChimp', 'noo' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'noo' ),
				'label_off' => esc_html__( 'Off', 'noo' ),
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
		$mailchimp_style = !empty($settings['mailchimp_style']) ? ' noo-mailchimp' : '';
		if(!empty($settings['noo_wpcf7'])){
		?>
			<div class="noo-contact-form-widget<?php echo esc_attr($mailchimp_style);?>">
				<div class="noo-contact-head">
					<h3 class=contact-title><?php echo esc_html($settings['title']);?></h3>
					<p class="contact-desc"><?php echo esc_html($settings['desc']);?></p>
				</div>
				<div class="noo-contact-form-content">
					<?php echo do_shortcode('[contact-form-7 id="'.$settings['noo_wpcf7'].'"]');?>
				</div>
			</div>
		<?php }else{?>
			<p class="noo-warring"><?php esc_html_e('Make sure you have enabled Contact Form 7 plugin.', 'noo');?></p>
		<?php }?>
	<?php
	}
}