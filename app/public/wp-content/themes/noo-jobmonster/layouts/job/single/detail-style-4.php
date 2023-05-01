
<?php
$noo_single_jobs_layout = noo_get_option( 'noo_single_jobs_layout', 'right_company' );
$is_candidate           = Noo_Member::is_candidate();
$closing_date           = get_post_meta( $job_id, '_closing', true );
$closing_date           = empty( $closing_date ) || is_numeric( $closing_date ) ? $closing_date : strtotime( $closing_date );

$is_expired = ( 'expired' == get_post_status( $job_id ) ) || ( ! empty( $closing_date ) && $closing_date <= time() );
$status = noo_get_option('noo_jobs_show_expired', false) ? array('publish', 'expired') : 'publish';
$schema_desc = '';
$is_schema = noo_get_option('noo_job_schema',false);
if($is_schema) {
	$schema_desc = 'itemprop="description"';
}

$current_user_role = Noo_Member::get_user_role(get_current_user_id());
?>

<div class="<?php noo_main_class(); ?> job-detail-4" role="main">
    <?php if ( $is_expired ) : ?>
        <div class="job-message-job-status">
       <span class="jm-status-job-expired">
           <i class="far fa-clock"></i>
           <?php esc_html_e('Job Expired','noo'); ?>
       </span>
        </div>
    <?php endif; ?>
    <h3><?php echo esc_html__( 'Job Overview', 'noo' ) ?></h3>
    <?php 
        $fields = jm_get_job_custom_fields();
        $job_overview = get_theme_mod('noo_jobs_list_fields');
        $job_overview = !is_array($job_overview) ? explode(',', $job_overview) : $job_overview;
        $user_per = noo_get_user_permission();
        if(!empty($fields)) {
            $html = array();
            $field_skipped = 0;
            foreach ( $fields as $field ) {
                if( $field['name'] == '_closing' ) // reserve the _closing field
                    continue;
                if( $field['name'] == '_cover_image' ) // reserve the _closing field
                    continue;
                if( $field['name'] == '_full_address' ) // reserve the _closing field
                    continue;
                if( $field['type'] == 'embed_video' ) // reserve the _closing field
                    continue;
                if( $field['type'] == 'image_gallery' ) // reserve the _closing field
                    continue; 
                if( $field['type'] == 'single_image' ) // reserve the _closing field
                    continue;

                if(in_array($field['name'], $job_overview)){
	                $id = jm_job_custom_fields_name($field['name'], $field);
	                if( isset( $field['is_tax'] ) ) {
	                    $value = jm_job_get_tax_value();
	                    $value = implode( ',', $value );
	                } else {
	                    $value = noo_get_post_meta(get_the_ID(), $id, '');
	                }
	                $icon = isset($field['icon']) ? $field['icon'] : '';
	                $icon_class = str_replace("|", " ", $icon);
	                $current_user_id = get_current_user_id();
	                /* $current_user_permission = 'candidate';*/
	                $permission = isset($field['permission']) ? $field['permission'] : '';

	                $is_can_view = false;
	                if (empty($permission) or 'public' == $permission or $user_per == 'candidate_with_package' or $user_per=='true' or 'administrator' == $current_user_role) {
	                    $is_can_view = true;
	                } elseif($permission ==  $user_per){
	                    $is_can_view = true;
	                }

	                if ($is_can_view== false) {
	                    $field_skipped++;
	                    continue;
	                }
	                if( $value != '' ) { 

	                    $html[] = '<li class="job-cf col-sm-4 col-xs-6">' . noo_display_field( $field, $id, $value, array( 'label_tag' => 'strong', 'label_class' => '', 'value_tag' => 'span' ), false) . '</li>';
	                }
	            }
            }
            if( !empty( $html ) && count( $html ) > 0 ) : ?> 
                <div class="job-custom-fields feature-fields">
                    <ul class="row is-flex">
                        <?php echo implode("\n", $html); ?>
                    </ul>
                </div>

            <?php endif;
            if ($field_skipped >= 1):?>

                <?php 
                    $package_page_id = Noo_Resume_Package::get_setting( 'resume_package_page_id' );
                    $link = get_permalink($package_page_id);
                    $link = '<a href="' . esc_url($link) . '" class="upgrade">' . __('Upgrade', 'noo') . '</a>';
                ?>
                <?php if ($current_user_role == 'candidate') { ?>
                     <div class="noo-message noo-message-error">
                        <?php echo sprintf(__('Please %s the package to view more fields.', 'noo'), $link) ?>
                    </div>
                <?php } else { ?>
                    <div class="noo-message noo-message-error">
                        <?php echo sprintf(__('Please login with Candidate account to view more fields.', 'noo'), $link) ?>
                    </div>    
                <?php } ?>

            <?php endif;
        }
    ?>
	<div class="job-desc" <?php echo $schema_desc;?>>
		<?php do_action( 'jm_job_detail_content_before' ); ?>
		<?php the_content(); ?>
		<?php do_action( 'jm_job_detail_content_after' ); ?>
	</div>
	<div class="job-action hidden-print clearfix">
		<?php if ( $is_expired ) : ?>
			<div class="noo-messages noo-message-error">
				<ul>
					<li><?php echo __( 'This job has expired!', 'noo' ); ?></li>
				</ul>
			</div>
		<?php else : ?>
			<?php if ( $is_candidate ) : ?>
				<div class="noo-ajax-result" style="display: none"></div>
			<?php endif; ?>
			<?php $has_applied = $is_candidate ? Noo_Application::has_applied( 0, $job_id ) : false;?>
			<?php if ( $has_applied ) : ?>
				<div class="noo-messages noo-message-notice pull-left">
					<ul>
						<li><?php echo __( 'You have already applied for this job', 'noo' ); ?></li>
					</ul>
				</div>
			<?php else: ?>
				<?php 
				$can_apply = jm_can_apply_job( $job_id );
				if($can_apply === 'none'): // Disale Apply for Job Button
				elseif( !$can_apply ) :?>
					<?php list( $title, $link ) = jm_get_cannot_apply_job_message( $job_id ); ?>
					<?php if ( ! empty( $title ) ) {
						echo "<div><strong>$title</strong></div>";
					} ?>
					<?php if ( ! empty( $link ) ) {
						echo $link;
					} ?>
					<?php do_action( 'jm_job_detail_cannot_apply', $job_id ); ?>
				<?php else :?>
					<?php
                    $custom_apply_link = jm_get_setting( 'noo_job_linkedin', 'custom_apply_link','' );
                    $apply_url         = ! empty( $custom_apply_link ) ? noo_get_post_meta( $job_id, '_custom_application_url', '' ) : '';
                    ?>                        
                    <?php if(!empty($apply_url)) : ?>
                        <a class="btn btn-primary" href="<?php echo esc_url( $apply_url ); ?>" rel="nofollow" target="_blank"><?php echo jm_job_single_apply_text(true); ?></a>
                    <?php else:?>
                    	<a class="btn btn-primary" data-target="#applyJobModal" href="#" data-toggle="modal"><?php echo jm_job_single_apply_text(); ?></a> <?php include( locate_template( "layouts/job/apply/form.php" ) ); ?>
                    <?php endif; ?>
					<?php do_action( 'jm_job_detail_apply', $job_id ); ?>
					<?php
					if(jm_get_setting('noo_job_linkedin','use_apply_with_facebook') == 'yes'):
						noo_get_layout( 'job/apply/facebook' );
					endif;
					?>
					<?php
                    if(jm_get_setting('noo_job_linkedin','use_apply_with_xing') == 'yes'):
                        noo_get_layout('job/apply/via_xing_form');
                    endif;
                    ?>
				<?php endif; ?>
			<?php endif; ?>
			<?php do_action( 'jm_job_detail_actions', $job_id ); ?>
		<?php endif; ?>
	</div>
	<?php jm_the_job_tag(); ?>
	<?php
	//  -- Check display company
	if ( $noo_single_jobs_layout == 'left_sidebar' || $noo_single_jobs_layout == 'fullwidth' || $noo_single_jobs_layout == 'sidebar' ) :

		// -- Job Social Share
		jm_the_job_social( $job_id, __( 'Share this job', 'noo' ) );

		// -- check option turn on/off show company info
		if ( noo_get_option( 'noo_company_info_in_jobs', true ) ) :
			
			$job_ids = Noo_Company::get_company_jobs($company_id, array(), -1, $status);
			Noo_Company::display_sidebar( $company_id, true, count($job_ids) );
		endif;

	endif;
	?>
	<?php if ( noo_get_option( 'noo_job_related', true ) ) : ?>
		<?php jm_related_jobs( $job_id, __( 'Related Jobs', 'noo' ) ); ?>
	<?php endif; ?>
	<?php if ( noo_get_option( 'noo_job_comment', false ) && comments_open() ) : ?>
		<?php comments_template( '', true ); ?>
	<?php endif; ?>
</div> <!-- /.main -->
<?php if ( $noo_single_jobs_layout != 'fullwidth' ) : ?>
	<div class="<?php noo_sidebar_class(); ?> hidden-print">
		<div class="noo-sidebar-wrap">
			<?php
			//  -- Check display company
			if ( $noo_single_jobs_layout != 'left_sidebar' && $noo_single_jobs_layout != 'sidebar' ) :

				// -- Job Social Share
				jm_the_job_social( $job_id, __( 'Share this job', 'noo' ) );

				// -- show company info
				$job_ids = Noo_Company::get_company_jobs($company_id, array(), -1, $status);
				Noo_Company::display_sidebar( $company_id, true, count($job_ids));

			else :
				// -- show siderbar
				if ( ! function_exists( 'dynamic_sidebar' ) || ! dynamic_sidebar() ) :
					$sidebar = get_sidebar_id();
					dynamic_sidebar( $sidebar );
				endif;
			endif;
			?>
		</div>
	</div>
<?php endif; ?>
