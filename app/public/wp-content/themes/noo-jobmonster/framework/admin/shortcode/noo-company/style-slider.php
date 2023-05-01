<?php
/**
 * style-grid.php
 *
 * @author  : NooTheme
 * @since   : 1.0.0
 */
wp_enqueue_script( 'vendor-carousel' );
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$args     = array(
	'post_type'      => 'noo_company',
	'post_status'    => 'publish',
	'posts_per_page' => $posts_per_page,
);
if ($featured_company == true) {
    $args[ 'meta_query' ][] = array(
                'key'   => '_company_featured',
                'value' => 'yes',
                );
}
$wp_query = new WP_Query( $args );
?>
<div class="noo-company-sc <?php echo esc_attr( $style ); ?>" id="<?php echo ( $id_company = uniqid( 'company-id-' ) ); ?>">
    <?php
    while ( $wp_query->have_posts() ) : $wp_query->the_post();
        global $post;

        $company_name = $post->post_title;
        $count        = noo_company_job_count( $post->ID );
        ?>
        <div class="company-item-wrap">
            <div class="company-item">
                <div class="company-thumbnail">
                    <a href="<?php the_permalink(); ?>">
                        <?php echo Noo_Company::get_company_logo( $post->ID, 'company-logo' ); ?>
                    </a>
                </div>
                <div class="company-meta">
                    <a class="company-name" href="<?php the_permalink(); ?>">
                        <?php echo noo_get_the_company_name($post); ?>
                    </a>
                    <p>
                        <span class="job-count"><?php echo sprintf( _n( '%s Job', '%s Jobs', $count, 'noo' ), $count ); ?></span>
                        <span class="company-address"><?php echo noo_get_company_address( $post->ID ); ?></span>
                    </p>
                </div>
            </div>
        </div>
    <?php endwhile; ?>

</div>

<script type="text/javascript">
	jQuery(document).ready(function ($) {
		$("#<?php echo esc_attr( $id_company ) ?>").owlCarousel({
			items: <?php echo $column?>,
			itemsDesktop: false,
			itemsDesktopSmall: [1200, 3],
			itemsTablet: [768, 2],
			itemsMobile: [479, 1],
			navigation: true,
			pagination: false,
			autoPlay: <?php echo esc_attr( $autoplay ) ?>,
			autoHeight: false,
			slideSpeed: <?php echo $slider_speed; ?>,
			navigationText: ["", ""]
		});
	});
</script>