<?php
/**
 * noo-company.php
 *
 * @author  : NooTheme
 * @since   : 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'noo_company_shortcode' ) ) :
	function noo_company_shortcode( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'style'          => 'style-grid',
			'rows'           => '2',
			'column'         => '3',
			'posts_per_page' => '-1',
			'autoplay'       => 'true',
			'slider_speed'   => '800',
			'featured_company' => '',
		), $atts ) );
		ob_start();
		wp_enqueue_script( 'noo-swiper' );
		wp_enqueue_style( 'noo-swiper' );

		include locate_template('framework/admin/shortcode/noo-company/' . $style . '.php');

		return ob_get_clean();
	}

	add_shortcode( 'noo_company', 'noo_company_shortcode' );

endif;