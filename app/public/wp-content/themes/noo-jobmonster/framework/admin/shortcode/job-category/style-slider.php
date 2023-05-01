<?php
/**
 * style-1.php
 *
 * @author  : NooTheme
 * @since   : 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
wp_enqueue_script( 'vendor-carousel' );
?>
<div<?php echo( $id . $class . $custom_style ); ?>>
	<div class="noo-job-category-wrap <?php echo esc_attr( $style ); ?>">

		<div class="noo-job-category" id="<?php echo $id_job_cat = uniqid( 'job-cat-' ) ?>">
			<?php
            $i = 0;
			if ( $list_job_category == 'all' or $list_job_category == '' ) {
				$categories    = get_terms( 'job_category', array(
					'orderby'    => 'NAME',
					'order'      => 'ASC',
					'hide_empty' => ('true' == $hide_empty) ? true : false,
				) );
				foreach ( $categories as $key => $cat ) :
					if ( $i >= $limit_category )
						break;
					$cate_name = $cat->name;
					$job_count = $cat->count;
					$cate_link = get_term_link( $cat );
					$cate_link = apply_filters('noo_job_widget_shortcode_category_link', $cate_link, $cat);
					$icon_markers   = get_term_meta( $cat->term_id, 'icon_type', true );
					if ( empty( $icon_markers ) ) {
						$icon_markers = 'fa-home';
					}
					?>
					<div class="category-item">
						<a href="<?php echo esc_url( $cate_link ); ?>">
                            <span class="icon">
                                <i class="fa <?php echo esc_attr( $icon_markers ) ?>"></i>
                            </span>
							<h3 class="title">
                                <?php echo esc_html( $cate_name ); ?>
                            </h3>
							<?php if ( 'true' == $show_job_count ) : ?>
								<span class="job-count">
                                    (<?php echo sprintf( _n( '%s Job', '%s Jobs', $job_count, 'noo' ), $job_count ); ?>)
                                </span>
							<?php endif; ?>
						</a>
					</div>
				<?php $i++; endforeach;
			} else {
				$list_cat          = explode( ',', $list_job_category );
				foreach ( $list_cat as $key => $cat ) :
					$cate = get_term_by( 'id', absint( $cat ), 'job_category' );
					if ( ! empty( $cate ) ):
						if ( $i >= $limit_category )
							break;
						$cate_name = $cate->name;
						$job_count = $cate->count;
						$cate_link = get_term_link( $cate );
						$icon_markers   = get_term_meta( $cate->term_id, 'icon_type', true );
						if ( empty( $icon_markers ) ) {
							$icon_markers = 'fa-home';
						}
						?>
						<div class="category-item">
							<a href="<?php echo esc_url( $cate_link ); ?>">
                                <span class="icon">
                                    <i class="fa <?php echo esc_attr( $icon_markers ) ?>"></i>
                                </span>
								<h3 class="title">
                                    <?php echo esc_html( $cate_name ); ?>
                                </h3>
								<?php if ( 'true' == $show_job_count ) : ?>
									<span class="job-count">
                                        (<?php echo sprintf( _n( '%s Job', '%s Jobs', $job_count, 'noo' ), $job_count ); ?> )
                                    </span>
								<?php endif; ?>
							</a>
						</div>
						<?php
					endif;
				$i++; endforeach;
			}
			?>
		</div>
		<?php
		$url_more = isset( $url_more ) ? vc_build_link( $url_more ) : '';
		if ( !empty( $url_more[ 'url' ] ) ) {
			echo '<div class="view-more"><a class="btn btn-primary" href="' . esc_url( $url_more[ 'url' ] ) . '">' . esc_html( $url_more[ 'title' ] ) . '</a></div>';
		}
		?>
	</div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $("#<?php echo esc_attr( $id_job_cat ) ?>").owlCarousel({
            loop: true,
            margin: 10,
            responsiveClass: true,
            navigation: true,
            navigationText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>'],
            items: <?php echo esc_attr( $list_column ) ?>,
            pagination: false,
            responsive:{
                0:{
                    items:1,
                    nav:true
                },
                600:{
                    items:3,
                    nav:false
                },
                1000:{
                    items:5,
                    nav:true,
                    loop:false
                }
            }
        });
    });
</script>