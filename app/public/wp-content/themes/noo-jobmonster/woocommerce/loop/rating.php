<?php
/**
 * Loop Rating
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product;

if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' )
	return;
?>
<?php 
$rating = $product->get_average_rating();
$rating = absint($rating);
$rating_html  = '<div class="star-rating" title="' . sprintf( __( 'Rated %s out of 5', 'noo' ), $rating ) . '">';

$rating_html .= '<span style="width:' . ( ( $rating / 5 ) * 100 ) . '%"><strong class="rating">' . $rating . '</strong> ' . __( 'out of 5', 'noo' ) . '</span>';

$rating_html .= '</div>';
echo $rating_html;
?>