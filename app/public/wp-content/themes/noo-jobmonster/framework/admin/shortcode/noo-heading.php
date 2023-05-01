<?php
/**
 * noo-job-category.php
 *
 * @author  : NooTheme
 * @since   : 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'noo_heading_shortcode' ) ) :
	function noo_heading_shortcode( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'title'              => '',
			'sub_title'          => '',
			'align'              => 'text-center',
		), $atts ) );
		
		ob_start();

		noo_the_heading_title( $title, $sub_title, $align );

		return ob_get_clean();
	}

	add_shortcode( 'noo_heading', 'noo_heading_shortcode' );

endif;