<?php
/**
 * noo-client.php
 *
 * @author  : NooTheme
 * @since   : 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'noo_client_shortcode' ) ) :
	function noo_client_shortcode( $atts, $content = null ) {
		extract(shortcode_atts(array(
			'image_per_page'    => '5',
			'autoplay'          => 'true',
			'autoheight'        => 'true',
			'slider_speed'      => '800',
			'hidden_pagination' => 'false',
			'noo_client_group' =>  ''
		), $atts));

		$noo_client_new = vc_param_group_parse_atts( $noo_client_group );

		ob_start();

		wp_enqueue_script( 'vendor-carousel' );
		?>
		<div id="<?php echo $id = uniqid('noo-client-group-'); ?>" class="noo-client">
            <?php
            if( isset($noo_client_new) && !empty($noo_client_new) ):
                foreach( $noo_client_new as $item ){
	                if ( ! empty( $item['logo'] ) ) {
		                $img = wp_get_attachment_image_src( $item['logo'], "full" );
		                if ( !empty( $img[ 0 ] ) ) {
		                    ?>
                            <a href="<?php echo esc_url( $item['url'] ) ?>">
                                <img src="<?php echo esc_url( $img[0] ) ?>" alt="*" />
                            </a>
                            <?php
                        }
	                }
                }
            endif;
            ?>
		</div>
		<script>
			jQuery(document).ready(function($) {
				$("#<?php echo $id ?>").owlCarousel({
					items : <?php echo esc_attr( $image_per_page ) ?>,
					itemsDesktop : false,
					itemsDesktopSmall : false,
					itemsTablet: false,
					itemsTabletSmall : false,
					itemsMobile : false,
					pagination: <?php echo esc_attr( $hidden_pagination ) ?>,
					navigation: false,
					autoPlay : <?php echo esc_attr( $autoplay ) ?>,
					slideSpeed : <?php echo esc_attr( $slider_speed ) ?>
				});
			});
		</script>
		<?php
		$faq = ob_get_contents();
		ob_end_clean();
		return $faq;
	}

	add_shortcode( 'noo_client', 'noo_client_shortcode' );

endif;