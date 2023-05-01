<div class="noo-company noo-company-style2" role="main">
    <div class="noo-company-profile">

        <div class="<?php noo_main_class(); ?>">
        	<?php 
        	$content = get_post_field( 'post_content', get_the_ID() );
        	$content = apply_filters('the_content', $content);
        	if(!empty($content)){
        	?>
            <div class="noo-company-content">
                <div class="noo-company-heading"><?php _e('Something About Company', 'noo'); ?></div>
				<?php
				echo $content;
				?>
            </div>
			<?php } ?>
	        <?php
	        $photo_arr = noo_get_post_meta(get_the_ID(), "_portfolio", '');
	        if(!empty($photo_arr)) :
                wp_enqueue_script('prettyphoto');
	            wp_enqueue_style('prettyphoto');
		        if ( !is_array( $photo_arr ) ) {
			        $photo_arr = explode(',', $photo_arr);
		        }
		        ?>
                <div class="noo-company-content">
                    <div class="noo-company-heading">
                        <?php _e( 'Office Photos', 'noo' ); ?>
                    </div>
                    <div id="company-photo" class="company-photo row is-flex">
				        <?php
				        foreach ( $photo_arr as $image_id ) :
					        if ( empty( $image_id ) )
						        continue;

					        $image = wp_get_attachment_image_src( $image_id, array(145,180));
					        $image_full = wp_get_attachment_image_src( $image_id, 'full');
					        if(!empty($image) && !empty($image_full)){
					        	echo '<a data-rel="prettyPhoto[pp_gal_'. get_the_ID().']" class="col-md-4" href="' . $image_full[0] . '" ><img src="' . esc_url( $image[0] ) . '" alt="*" data-rel="prettyPhoto[pp_gal_'. get_the_ID().']"/></a>';
					        }

				        endforeach;
				        ?>
                    </div>
                </div>
	        <?php endif; ?>

	        <?php include( locate_template( "layouts/company/list-comment.php" ) ); ?>

            <div class="clearfix"></div>
            <div class="noo-company-profile-line"></div>
            <div class="job-listing" data-agent-id="<?php the_ID() ?>">
				<?php
				$status = noo_get_option('noo_jobs_show_expired', false) ? array('publish', 'expired') : 'publish';
				$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
				$job_ids = Noo_Company::get_company_jobs(get_the_ID(), array(), -1, $status );
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

        </div>

        <div class="<?php noo_sidebar_class(); ?> hidden-print">
			<?php Noo_Company::display_sidebar(get_the_ID(), true, $r->found_posts); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var prettyPhoto = $("a[data-rel^='prettyPhoto']");
        if(prettyPhoto.length > 0){
            prettyPhoto.prettyPhoto({
                hook:'data-rel',
                social_tools:'',
                animation_speed:'normal',
                theme:'light_square'
            });
        }
    });
</script>