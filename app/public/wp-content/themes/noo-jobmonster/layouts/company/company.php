<div class=" noo-company-style2" role="main">
    <div class="noo-company-profile">
        <div class="<?php noo_main_class(); ?>">
            <div class="job-listing" data-agent-id="<?php the_ID() ?>">
				<?php
				$status = noo_get_option('noo_jobs_show_expired', false) ? array('publish', 'expired') : 'publish';
				$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
				$job_ids = Noo_Company::get_company_jobs(get_the_ID(), array(), -1, $status);
				$args = array(
					'paged' => $paged,
					'post_type' => 'noo_job',
					'post__in' => array_merge($job_ids, array(0)),
					'post_status' => $status,
				);

				$r = new WP_Query($args);
				jm_job_loop(array(
					'query' => $r,
					'title' => sprintf( _n( '%s has posted %s job', '%s has posted %s jobs', $r->found_posts, 'noo' ), get_the_title(), '<span class="text-primary">' . $r->found_posts . '</span>' ),
					'no_content' => __('This company has no active jobs', 'noo'),
					'is_shortcode' => true
				));

				?>
            </div>

			<?php include( locate_template( "layouts/company/list-comment.php" ) ); ?>

        </div>
        <div class="<?php noo_sidebar_class(); ?> hidden-print">
			<?php Noo_Company::display_sidebar_two(get_the_ID(), false, $r->found_posts); ?>
        </div>
    </div>
</div>