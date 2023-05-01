<?php
/**
 * Created by PhpStorm.
 * User: cuong
 * Date: 7/20/2018
 * Time: 10:51 AM
 */

class Noo_Walker_TaxonomyDropdown extends Walker_CategoryDropdown{
    function start_el(&$output, $category, $depth = 0, $args = array(), $id = 0 ) {

        $pad = str_repeat('', $depth * 3);

        /** This filter is documented in wp-includes/category-template.php */
        $cat_name = apply_filters( 'list_cats', $category->name, $category );

        if ( isset( $args['value_field'] ) && isset( $category->{$args['value_field']} ) ) {
            $value_field = $args['value_field'];
        } else {
            $value_field = 'term_id';
        }

        $output .= "\t<option class=\"level-$depth\" value=\"" . esc_attr( $category->{$value_field} ) . "\"";

        // Type-juggling causes false matches, so we force everything to a string.
        if ( in_array($category->{$value_field}, (array) $args['selected']) ){
            $output .= ' selected="selected"';
        }
        $output .= '>';
        $output .= $pad.$cat_name;
        if ( $args['show_count'] )
            $output .= '('. number_format_i18n( $category->count ) .')';
        $output .= "</option>\n";
    }
}