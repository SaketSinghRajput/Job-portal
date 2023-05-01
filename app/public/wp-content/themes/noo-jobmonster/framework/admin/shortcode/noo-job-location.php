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

if ( ! function_exists( 'noo_job_location_shortcode' ) ) :
    function noo_job_location_shortcode( $atts, $content = null ) {
        extract( shortcode_atts( array(
            'title'              => '',
            'sub_title'          => '',
            'style'              => 'style-grid',
            'show_job_count'     => 'true',
            'hide_empty'         => 'true',
            'limit_location'     => '12',
            'list_job_location'  => '',
            'list_column'        => '',
            'url_more'           => '',
            'visibility'         => '',
            'class'              => '',
            'id'                 => '',
            'custom_style'       => '',
        ), $atts ) );

        $visibility = ( $visibility != '' ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
        $class      = ( $class != '' ) ? 'noo-job-category clearfix' . esc_attr( $class ) . " {$style}" : 'noo-job-category clearfix'  . " {$style}";
        $class      .= noo_visibility_class( $visibility );

        $id    = ( $id != '' ) ? ' id="' . esc_attr( $id ) . '"' : '';
        $class = ( $class != '' ) ? ' class="' . $class . '"' : '';

        $custom_style = ( $custom_style != '' ) ? ' style="' . $custom_style . '"' : '';
        $link_url     = ( ! empty( $link_url ) ) ? $link_url : '#';

        $list_column = ( ! empty( $list_column ) ) ? $list_column : '4';
        ob_start();
        
        include locate_template('framework/admin/shortcode/job-location/' . $style . '.php');

        return ob_get_clean();
    }

    add_shortcode( 'noo_job_location', 'noo_job_location_shortcode' );

endif;