<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

remove_action('wp_job_board_pro_before_job_archive', array( 'WP_Job_Board_Pro_Job_Listing', 'display_jobs_count_results'), 10 );
$layout_type = superio_get_jobs_layout_type();
if ( $layout_type !== 'half-map' ) {
	add_action('wp_job_board_pro_before_job_archive', array( 'WP_Job_Board_Pro_Job_Listing', 'display_jobs_count_results'), 20 );
}

remove_action( 'wp_job_board_pro_before_job_archive', array( 'WP_Job_Board_Pro_Job_Listing', 'display_job_feed' ), 22 );
remove_action( 'wp_job_board_pro_before_job_archive', array( 'WP_Job_Board_Pro_Job_Listing', 'display_jobs_alert_form' ), 20 );

add_action( 'wp_job_board_pro_before_job_archive', 'superio_job_display_filter_btn', 19 );
add_action( 'wp_job_board_pro_before_job_archive', 'superio_job_display_per_page_form', 26 );


?>
<div class="jobs-listing-wrapper main-items-wrapper" data-display_mode="<?php echo esc_attr($jobs_display_mode); ?>">
	<?php
	/**
	 * wp_job_board_pro_before_job_archive
	 */
	do_action( 'wp_job_board_pro_before_job_archive', $jobs );
	?>

	<?php if ( !empty($jobs) && !empty($jobs->posts) ) : ?>
		<?php
		/**
		 * wp_job_board_pro_before_loop_job
		 */
		do_action( 'wp_job_board_pro_before_loop_job', $jobs );
		?>
		
		<div class="jobs-wrapper items-wrapper">
			<?php 
				$columns = superio_get_jobs_columns();
				$bcol = $columns ? 12/$columns : 4;
				$i = 0;
			?>
			<div class="row items-wrapper-<?php echo esc_attr($job_inner_style); ?>">
				<?php while ( $jobs->have_posts() ) : $jobs->the_post(); ?>
					<div class="item-job <?php echo esc_attr($columns > 1 ? 'col-sm-6' : 'col-sm-12'); ?> col-md-<?php echo esc_attr($bcol); ?> col-xs-12 <?php echo esc_attr(($i%$columns == 0)?'lg-clearfix md-clearfix':'') ?> <?php echo esc_attr($columns > 1 && ($i%2 == 0)?'sm-clearfix':'') ?>">
						<?php echo WP_Job_Board_Pro_Template_Loader::get_template_part( 'jobs-styles/inner-'.$job_inner_style ); ?>
					</div>
				<?php $i++; endwhile; ?>
			</div>
		</div>

		<?php
		/**
		 * wp_job_board_pro_after_loop_job
		 */
		do_action( 'wp_job_board_pro_after_loop_job', $jobs );
		
		wp_reset_postdata();
		?>

	<?php else : ?>
		<div class="not-found"><?php esc_html_e('No job found.', 'superio'); ?></div>
	<?php endif; ?>

	<?php
	/**
	 * wp_job_board_pro_after_job_archive
	 */
	do_action( 'wp_job_board_pro_after_job_archive', $jobs );
	?>
</div>