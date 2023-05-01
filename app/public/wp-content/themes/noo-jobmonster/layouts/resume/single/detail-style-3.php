<?php
wp_enqueue_script('noo-timeline-vendor');
wp_enqueue_script('noo-timeline');

wp_enqueue_script('noo-lightgallery');
wp_enqueue_style('noo-lightgallery');

$enable_education = jm_get_resume_setting('enable_education', '1');
$enable_experience = jm_get_resume_setting('enable_experience', '1');
$enable_skill = jm_get_resume_setting('enable_skill', '1');
$enable_portfolio = jm_get_resume_setting('enable_portfolio', '1');
$enable_awards = jm_get_resume_setting('enable_awards', '1');
$enable_job_complete = jm_get_resume_setting('enable_job_complete','1');
$hide_profile = isset( $hide_profile ) ? $hide_profile : false;

$fields = jm_get_resume_custom_fields();

$education					= array();
if( $enable_education ) {
	$education['school']		= noo_json_decode( noo_get_post_meta( $resume_id, '_education_school' ) );
	$education['qualification']	= noo_json_decode( noo_get_post_meta( $resume_id, '_education_qualification' ) );
	$education['date']			= noo_json_decode( noo_get_post_meta( $resume_id, '_education_date' ) );
	$education['note']			= noo_json_decode( noo_get_post_meta( $resume_id, '_education_note' ) );
}

$experience					= array();
if( $enable_experience ) {
	$experience['employer']		= noo_json_decode( noo_get_post_meta( $resume_id, '_experience_employer' ) );
	$experience['job']			= noo_json_decode( noo_get_post_meta( $resume_id, '_experience_job' ) );
	$experience['date']			= noo_json_decode( noo_get_post_meta( $resume_id, '_experience_date' ) );
	$experience['note']			= noo_json_decode( noo_get_post_meta( $resume_id, '_experience_note' ) );
}

$skill						= array();
if( $enable_skill ) {
	$skill['name']				= noo_json_decode( noo_get_post_meta( $resume_id, '_skill_name' ) );
	$skill['percent']			= noo_json_decode( noo_get_post_meta( $resume_id, '_skill_percent' ) );
}

$awards						= array();
if( $enable_awards ) {
	$awards['name']			= noo_json_decode( noo_get_post_meta( $resume_id, '_awards_name' ) );
	$awards['year']			= noo_json_decode( noo_get_post_meta( $resume_id, '_awards_year' ) );
	$awards['content']		= noo_json_decode( noo_get_post_meta( $resume_id, '_awards_content' ) );
}
$job_complete=array();
if($enable_job_complete){
    $job_complete['name']   = noo_json_decode(noo_get_post_meta($resume_id,'_job_complete_name'));
    $job_complete['count']  = noo_json_decode(noo_get_post_meta($resume_id,'_job_complete_counter'));
    $job_complete['icon']   = noo_json_decode(noo_get_post_meta($resume_id,'_job_complete_icon'));
}

global $post;
$candidate_id = isset($_GET['candidate_id']) ? absint($_GET['candidate_id']) : '';
$enable_upload = (bool)jm_get_resume_setting('enable_upload_resume', '1');
$enable_print = (bool)jm_get_resume_setting('enable_print_resume', '1');
if (get_the_ID() == Noo_Member::get_member_page_id() || jm_is_resume_posting_page()) {
    $candidate_id = get_current_user_id();
     $resume_id = 0;
} else {
    $resume_id = isset($_GET['resume_id']) ? $_GET['resume_id'] : get_the_ID();
    if ('noo_resume' == get_post_type($resume_id)) {
        $candidate_id = get_post_field('post_author', $resume_id);
    }
}

$file_cv = noo_json_decode(noo_get_post_meta($post->ID, '_noo_file_cv'));
$slogan = noo_get_post_meta($post->ID, '_slogan');
$candidate = !empty($candidate_id) ? get_userdata($candidate_id) : false;
if ($candidate) :
    $fields_candidate = jm_get_candidate_custom_fields();
    $all_socials = noo_get_social_fields();
    $socials = jm_get_candidate_socials();
    $email = $candidate ? $candidate->user_email : '';
	$user_per = noo_get_user_permission();

	$social_share = noo_get_option('noo_resume_social_share','1');
	// check $action: Remove unnecessary fields from Resume Preview (Review, share)
    $action = isset($_GET['action']) ? $_GET['action'] : '';
?>
<article id="post-<?php the_ID(); ?>" class="resume-style-3">
	<?php //if( !apply_filters( 'jm_resume_hide_candidate_contact', $hide_profile, $resume_id ) ) noo_get_layout('candidate/resume_candidate_profile'); ?>
	<div class="resume-content">
		<div class="row">
			<div class="col-md-12">
				<div class="resume-desc">
					<div class="resume-description row">
						<div class="col-md-2 col-sm-12">
							<h3 class="title-general">
								<span>
									<?php _e('Description','noo');?>
								</span>
							</h3>
						</div>
						<div class="desc-info col-md-10 col-sm-12">
							<div class="icon-general pencil"></div>
							<?php the_content(); ?>
						</div>
					</div>
					<?php if ((!apply_filters('jm_resume_hide_candidate_contact', $hide_profile, $resume_id)) && ( apply_filters( 'jm_resume_show_candidate_contact', true, $resume_id ) )) : ?>
					<div class="resume-about row">
		                <div class="col-md-2 col-sm-12">
							<h3 class="title-general">
		                        <span><?php _e('About Candidate', 'noo'); ?></span>
		                    </h3>
						</div>
	                    <div class="col-md-10 col-sm-12">
	                    	<div class="icon-general user"></div>
		                    <?php //if ((!apply_filters('jm_resume_hide_candidate_contact', $hide_profile, $resume_id)) && ( apply_filters( 'jm_resume_show_candidate_contact', true, $resume_id ) )) : ?>
		                    <?php $view_candidate_contact_package = jm_is_enabled_job_package_view_candidate_contact();
		                        $can_show_candidate_contact = jm_can_show_candidate_contact_with_package($resume_id);
		                    ?>
		                    <?php if($view_candidate_contact_package && (!$can_show_candidate_contact)): ?>
		                        <?php $remain_view_candidate_contact = jm_get_view_candidate_contact_remain();
		                        
		                            if($remain_view_candidate_contact == -1){
		                                $remain_view_candidate_contact = esc_html__('Unlimited views time','noo');
		                            }else{
		                                $remain_view_candidate_contact = sprintf(esc_html__('Remain %s views time.', 'noo'), $remain_view_candidate_contact);
		                            }
		                        ?>
		                             <form method="post">
		                               <span class="show-candidate-contact " data-resume-id = "<?php echo $resume_id ?>" data-id="<?php echo get_current_user_id(); ?>" data-toggle="tooltip" title="<?php echo esc_html__($remain_view_candidate_contact); ?>">
		                                   <a  title="<?php echo esc_attr__( 'show candidate profile', 'noo' ); ?>">
		                                       <?php echo esc_html__('Click here to view candidate profile','noo') ?>
		                                       <i class="fa fa-eye" aria-hidden="true"></i>
		                                   </a>
		                                </span>
		                             </form>
		                    <?php endif; ?>
		                        <?php if( !$view_candidate_contact_package || $can_show_candidate_contact): ?>
		                        <div class="resume-field row">
		                            <?php if (!empty($fields_candidate)) : ?>
		                                <?php foreach ($fields_candidate as $field) :
		                                    if (isset($field['is_default'])) {
		                                    	if (in_array($field['name'], array('first_name', 'last_name', 'full_name', 'email'))){
		                                            continue; // don't display WordPress default user fields
		                                    	}
		                                    }
		                                    
		                                    $field_id = jm_candidate_custom_fields_name($field['name'], $field);
		                                    $value = get_user_meta($candidate->ID, $field_id, true);
		                                    $value = noo_convert_custom_field_value($field, $value);
		                                    $value_date = noo_convert_custom_field_value($field, $value);
		                                    $icon = isset($field['icon']) ? $field['icon'] : '';
		                                    $icon_class = str_replace("|", " ", $icon);

		                                    if (is_array($value)) {
		                                        $value = implode(', ', $value);
		                                    }
		                                    $permission = isset($field['permission']) ? $field['permission'] : '';
		                                    $is_can_view = false;
		                                    if($user_per == 'true'){
		                                        $is_can_view=true;
		                                    }else{
		                                        if (empty($permission) or 'public' == $permission  ) {
		                                            $is_can_view = true;
		                                        } elseif ($permission == $user_per) {
		                                            $is_can_view = true;
		                                        }
		                                    }
		                                if($is_can_view == false){
		                                        continue;
		                                }
		                                    if (!empty($value)) : ?>
		                                        <div class="<?php echo esc_attr($field_id); ?> col-md-4">
		                                            <?php if ($field['type'] == 'datepicker'):
		                                                $date = date('d/M/Y', $value_date);
		                                                ?>
		                                                <div class="<?php echo esc_attr($field_id); ?> ">
		                                                    <span class="candidate-field-icon"><i
		                                                                class="<?php echo esc_attr($icon_class) ?>"></i></span>
		                                                    <?php echo $date; ?>
		                                                </div>
		                                            <?php else : ?>
		                                                <div class="<?php echo esc_attr($field_id); ?>">
		                                                    <span class="candidate-field-icon"><i
		                                                                class="<?php echo esc_attr($icon_class) ?>"></i></span>
		                                                    <?php echo $value; ?>
		                                                </div>
		                                            <?php endif; ?>
		                                        </div>
		                                    <?php endif; ?>
		                                <?php endforeach; ?>
		                            <?php endif; ?>
		                            <?php if (!empty($email)) : ?>
		                                <div class="email col-md-4">
		                                    <a href="mailto:<?php echo esc_attr($email); ?>">
		                                        <span class="candidate-field-icon"><i
		                                                    class="fa fa-envelope text-primary"></i></span><?php echo esc_html($email); ?>
		                                    </a>
		                                </div>
		                            <?php endif; ?>
		                        </div>
		                        <?php //endif; ?>
		                    <?php else: ?>
		                        <?php
		                        list($title, $link) = jm_message_cannot_view_contact_candidate();
		                        echo apply_filters( 'noo_resume_candidate_private_message',$title, $resume_id );
		                        if( !empty( $link ) ) echo $link;
		                        ?>
		                    <?php endif; ?>
		                    <?php do_action( 'noo_resume_candidate_profile_after', $resume_id ); ?>
		                </div>
		            </div>
					<?php endif?>
					<div class="resume-general row">
						<div class="col-md-2 col-sm-12">
						<h3 class="title-general">
						<span><?php _e('General Information','noo');?></span>
						</h3>										
						</div>
						<div class="col-md-10 col-sm-12">
							<div class="icon-general map"></div>
							<ul>
								<?php
                                $field_skipped=0;
                                if(Noo_Member::get_user_role(get_current_user_id()) == 'employer'){
                                    $user_id = get_current_user_id();
                                    $package = (!empty($user_id)) ? get_user_meta($user_id, '_job_package', true) : null;
                                    $package_id = (!empty($package))? $package['product_id'] : '';
                                    $package_resume_cfs = !empty( $package_id ) ? jm_get_job_package_resume_cf( $package_id ) : array();
                                    $resume_cfs_add_to_package = jm_get_resume_custom_fields_option('job_package_resume_fields', array());
                                    $remove_fields = array_diff($resume_cfs_add_to_package,$package_resume_cfs);
                                    if( isset($_GET['application_id'] ) && !empty($_GET['application_id']) ) {
                                        // Employers can view resumes from their applications
                                        $job_id = get_post_field( 'post_parent', $_GET['application_id'] );
                                        $company_id = noo_get_post_meta($job_id,'_company_id');
                                        $id = (!empty($company_id)) ? $company_id : $job_id;
                                        $employer_id = get_post_field('post_author',$id);
                                        if( $employer_id == get_current_user_id() ) {
                                            $remove_fields = array();
                                        }
                                    }
                                    foreach ($fields as $index=>$field){
                                        if(in_array($field['name'],$remove_fields)){
                                            unset($fields[$index]);
                                            $field_skipped++;
                                        }
                                    }
                                }
								if($fields) : foreach ($fields as $field) :
									// if( $field['type'] == 'embed_video' ) // reserve the video field
         //                				continue;
									$label = isset( $field['label_translated'] ) ? $field['label_translated'] : $field['label'];
									$value = jm_get_resume_field_value( $resume_id, $field );
									$field_id = jm_resume_custom_fields_name($field['name'], $field);
									
                                    $icon = isset($field['icon']) ? $field['icon'] : '';
                                    $icon_class = str_replace("|", " ", $icon);
                                    $permission = isset($field['permission']) ? $field['permission'] : '';
                                    $is_can_view = false;
                                    if($user_per == 'true'){
                                        $is_can_view=true;
                                    }else{
                                        if (empty($permission) or 'public' == $permission  ) {
                                            $is_can_view = true;
                                        } elseif ($permission == $user_per) {
                                            $is_can_view = true;
                                        }
                                    }
                                    if($is_can_view == false){
                                        $field_skipped++;
                                        continue;
                                    }
									if( empty($value) || $field['name'] == '_portfolio' ) continue;

									?>
                                    <?php
                                    if(($field['type']=='single_tax_location') || ($field['type']=='single_tax_location_input')){
                                        $field['type'] = 'select';
                                    }
                                    ?>
									<li class="<?php echo esc_attr( $field_id ); ?>">
										<?php noo_display_field( $field, $field_id, $value); ?>
									</li>

								<?php endforeach;
                                    if ($field_skipped >= 1):?>

                                    	<?php 
					                        $package_page_id = Noo_Job_Package::get_setting( 'package_page_id' );
					                        $link = get_permalink($package_page_id);
					                        $link = '<a href="' . esc_url($link) . '" class="upgrade">' . __('Upgrade', 'noo') . '</a>';
					                    ?>
                                        <?php if (Noo_Member::get_user_role(get_current_user_id()) == 'administrator' || Noo_Member::is_employer(get_current_user_id())) { ?>
                                            <div class="noo-message noo-message-error">
                                                <?php echo sprintf(__('Please %s the package to view more fields.', 'noo'), $link) ?>
                                            </div>
                                        <?php } else { ?>
                                            <div class="noo-message noo-message-error">
                                                <?php echo __('Please login with Employer account to view more fields.', 'noo') ?>
                                            </div>
                                        <?php } ?>

                                    <?php endif;
								endif; ?>
							</ul>
						</div>
					</div>

					<?php if( $enable_education ) : ?>
						<?php $education['school'] = isset( $education['school'] ) ? array_filter( $education['school'] ) : array(); ?>
						<?php if( !empty( $education['school'] ) ) : ?>
							<div class="resume-timeline row">
								<div class="col-md-2 col-sm-12">
									<h3 class="title-general">
										<span><?php _e('Education','noo');?></span>
									</h3>
								</div>
								<div class="col-md-10 col-sm-12">
									<div id="education-timeline" class="timeline-container education">
											<?php $education_count = count( $education['school'] );
											for( $index = 0; $index < $education_count; $index++ ) :
												if( empty( $education['school'][$index] ) ) continue;
												$status = empty($education['note'][$index]) ? 'empty' : '';
												?>
												<div class="timeline-wrapper <?php echo ( $index == ( $education_count - 1 ) ) ? 'last' : ''; ?>">
													<div class="timeline-time"><span><?php echo esc_attr( $education['date'][$index] ); ?></span></div>
													<dl class="timeline-series">
														<span class="tick tick-before"></span>
														<dt id="<?php echo 'education'.$index ?>" class="timeline-event"><a class="<?php echo $status; ?>"><?php esc_attr_e( $education['school'][$index] ); ?><span><?php esc_attr_e( $education['qualification'][$index] ); ?></span></a></dt>
														<span class="tick tick-after"></span>
														<dd class="timeline-event-content" id="<?php echo 'education'.$index.'EX' ?>">
															<div><?php echo wpautop( html_entity_decode( $education['note'][$index] ) ); ?></div>
														<br class="clear">
														</dd><!-- /.timeline-event-content -->
													</dl><!-- /.timeline-series -->
												</div><!-- /.timeline-wrapper -->
											<?php endfor; ?>
									</div>
								</div>
							</div>
						<?php endif; ?>
					<?php endif; ?>

					<?php if( $enable_experience ) : ?>
						<?php $experience['employer'] = isset( $experience['employer'] ) ? array_filter( $experience['employer'] ) : array(); ?>
						<?php if( !empty( $experience['employer'] ) ) : ?>
							<div class="resume-timeline row">
								<div class="col-md-2 col-sm-12">
									<h3 class="title-general">
										<span><?php _e('Work Experience','noo');?></span>
									</h3>
								</div>
								<div class="col-md-10 col-sm-12">
									<div id="experience-timeline" class="timeline-container experience">
										<?php $experience_count = count( $experience['employer'] );
											for( $index = 0; $index < $experience_count; $index++ ) : 
												if( empty( $experience['employer'][$index] ) ) continue;
												$status = empty($education['note'][$index]) ? 'empty' : '';
												?>
												<div class="timeline-wrapper <?php echo ( $index == ( $experience_count - 1 ) ) ? 'last' : ''; ?>">
													<div class="timeline-time"><span><?php echo esc_attr( $experience['date'][$index] ); ?></span></div>
													<dl class="timeline-series">
														<span class="tick tick-before"></span>
														<dt id="<?php echo 'experience'.$index ?>" class="timeline-event"><a class="<?php echo $status; ?>"><?php esc_attr_e( $experience['employer'][$index] ); ?><span class="tick tick-after"><?php esc_attr_e( $experience['job'][$index] ); ?></span></a></dt>
														
														<dd class="timeline-event-content" id="<?php echo 'experience'.$index.'EX' ?>">
															<div><?php echo wpautop( html_entity_decode( $experience['note'][$index] ) ); ?></div>
														<br class="clear">
														</dd><!-- /.timeline-event-content -->
													</dl><!-- /.timeline-series -->
												</div><!-- /.timeline-wrapper -->
										<?php endfor; ?>
									</div>
								</div>
							</div>
						<?php endif; ?>
					<?php endif; ?>

					<?php if( $enable_skill ) : ?>
						<?php $skill['name'] = isset( $skill['name'] ) ? array_filter( $skill['name'] ) : array(); ?>
						<?php if( !empty( $skill['name'] ) ) : ?>
							<div class="resume-timeline row">
								<div class="col-md-2 col-sm-12">
									<h3 class="title-general">
										<span><?php _e('Summary of Skills','noo');?></span>
									</h3>
								</div>
								<div class="col-md-10 col-sm-12">
									<div class="icon-general database"></div>
									<div id="skill" class="skill">
										<?php $skill_count = count( $skill['name'] );
											for( $index = 0; $index < $skill_count; $index++ ) : 
												if( empty( $skill['name'][$index] ) ) continue;
												$skill_value = min( intval( $skill['percent'][$index] ), 100 );
												$skill_value = max( $skill_value, 0 );
												?>
											<div class="pregress-bar clearfix">
												<div class="progress_title"><span><?php echo esc_attr( $skill['name'][$index] ); ?></span></div>
												<div class="progress">
													<div aria-valuemax="100" aria-valuemin="0" aria-valuenow="70" class="progress-bar progress-bar-bg" data-valuenow="<?php esc_attr_e( $skill_value ); ?>" role="progressbar" style="width: <?php esc_attr_e( $skill_value ); ?>%;">
														
													</div>
													<div class="progress_label" style="opacity: 1;"><span><?php echo esc_attr( $skill_value ); ?></span><?php _e('%', 'noo'); ?></div>
												</div>
											</div>
										<?php endfor; ?>
									</div>
								</div>
							</div>
						<?php endif; ?>
					<?php endif; ?>

					<?php if( $enable_portfolio ) : ?>
						<?php
						$portfolio_arr = noo_get_post_meta($resume_id, "_portfolio", '');
						if(!empty($portfolio_arr)) :
							if ( !is_array( $portfolio_arr ) ) {
								$portfolio_arr = explode(',', $portfolio_arr);
							}
							?>
                            <div class="resume-timeline row">
                                <div class="col-md-2 col-sm-12">
                                    <h3 class="title-general">
                                        <span><?php _e( 'Portfolio', 'noo' ); ?></span>
                                    </h3>
                                </div>
                                <div class="col-md-10 col-sm-12">
                                	<div class="icon-general image"></div>
                                    <div id="portfolio" class="portfolio row is-flex">
                                        <?php
                                            foreach ( $portfolio_arr as $image_id ) :
                                                if ( empty( $image_id ) )
                                                    continue;

	                                            $image = wp_get_attachment_image_src( $image_id, 'portfolio-image');
	                                            $image_full = wp_get_attachment_image_src( $image_id, 'full');
	                                            if(isset($image[0]) && !empty($image[0])){
	                                           	 	echo '<a class="col-md-4 col-sm-4 col-xs-6" href="' . $image_full[0] . '"><img src="' . esc_url( $image[0] ) . '" alt="*" /></a>';
	                                           	}

                                            endforeach;
                                        ?>
                                    </div>
                                </div>
                            </div>
					    <?php endif; ?>
					<?php endif; ?>

					<?php if( $enable_awards ) : ?>
						<?php $awards['name'] = isset( $awards['name'] ) ? array_filter( $awards['name'] ) : array(); ?>
						<?php if( !empty( $awards['name'] ) ) : ?>
                            <div class="resume-timeline row">
                                <div class="col-md-2 col-sm-12">
                                    <h3 class="title-general">
                                        <span><?php _e('AWARDS','noo');?></span>
                                    </h3>
                                </div>
                                <div class="col-md-10 col-sm-12">
                                	<div class="icon-general medal"></div>
                                    <div id="awards" class="awards">
	                                    <?php $awards_count = count( $awards['name'] );
	                                    for( $index = 0; $index < $awards_count; $index++ ) :
		                                    if( empty( $awards['name'][$index] ) ) continue;
		                                    $status = empty($awards['content'][$index]) ? 'empty' : '';
		                                    ?>
                                            <div class="timeline-wrapper <?php echo ( $index == ( $awards_count - 1 ) ) ? 'last' : ''; ?>">
                                                <dl class="timeline-series">
                                                    <span class="tick tick-before"></span>
                                                    <dt id="<?php echo 'awards'.$index ?>" class="timeline-event">
                                                        <a class="<?php echo $status; ?>">
                                                             <span class="tick tick-after">
                                                                 <?php echo esc_attr( $awards['name'][$index] ); ?>
                                                             </span>
                                                            <span class="awards-year">(<?php echo esc_attr( $awards['year'][$index] ); ?>)</span>
                                                        </a>
                                                    </dt>

                                                    <dd class="timeline-event-content" id="<?php echo 'awards'.$index.'EX' ?>">
                                                        <div><?php echo wpautop( html_entity_decode( $awards['content'][$index] ) ); ?></div>
                                                        <br class="clear">
                                                    </dd><!-- /.timeline-event-content -->
                                                </dl><!-- /.timeline-series -->
                                            </div><!-- /.timeline-wrapper -->
	                                    <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
						<?php endif; ?>
					<?php endif; ?>

                    <?php if ($enable_job_complete) : ?>
                        <?php $job_complete['name'] = isset($job_complete['name']) ? array_filter($job_complete['name']) : array(); ?>
                        <?php if (!empty($job_complete['name'])) : ?>
                            <div class="resume-timeline row">
                                <div class="col-md-2 col-sm-12">
                                    <h3 class="title-general">
                                        <span><?php _e('JOB COMPLETE', 'noo'); ?></span>
                                    </h3>
                                </div>
                                <div class="col-md-10 col-sm-12">
                                	<div class="icon-general success"></div>
                                    <div id="job-complete" class="noo-counter-job row">
                                        <?php $count = count($job_complete['name']);
                                        $icon = (isset($job_complete['icon'])) ? $job_complete['icon'] : 'fas|fa-pencil-alt';
                                        for ($index = 0; $index < $count; $index++) :
                                            if (empty($job_complete['name'][$index])) continue;
                                            $icon_class =(!empty($icon[$index]))? str_replace("|", " ", $icon[$index]) : 'fas fa-pencil-alt';
                                            $job_count =(!empty($job_complete['count'][$index])) ?$job_complete['count'][$index] : '1';
                                            ?>
                                            <div class="noo-counter-item col-md-4">
                                                <div class="noo-counter-font-icon pull-left">
                                                    <i class="<?php echo esc_attr( $icon_class ) ?>"></i>
                                                </div>
                                                <div class="noo-counter-icon-content pull-left">
                                                    <div class="noo-counter"> <?php esc_attr_e($job_count); ?></div>
                                                    <span class="noo-counter-text"> <?php esc_attr_e($job_complete['name'][$index]); ?></span>
                                                </div>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

				</div>
			</div>
		</div>
		<?php if($social_share && $action != 'resume_preview'):?>
			<div class="candidate-social resume-share">
				<span class="social-share"><?php echo esc_html('Share:','noo');?></span>
				<?php jm_the_resume_social( $resume_id );?>
			</div>
		<?php endif;?>
	</div>
</article> <!-- /#post- -->
<?php 
	if($action != 'resume_preview'):?>
		<div class="row resume-details-3">
		    <?php
		    $enable_post_review = jm_get_resume_setting('post_review_resume','1');
		    if((isset($_POST['total'])) && ($_POST['total'])== 0){
		        $enable_post_review = false;
		    }
		    if($enable_post_review){
		        noo_get_layout("resume/list_comment");
		    }

		    ?>
		</div>	
		<?php add_action( 'wp_footer', function() { ?>
		<script>
			jQuery(document).ready(function($) {
				if($('.resume-timeline').length > 0){
					jQuery.timeliner({
						timelineContainer:'.resume-timeline .timeline-container'
					});
				}
				if($('.venobox').length > 0){
					jQuery('.venobox').venobox();
				}
				if($('#portfolio').length > 0){
					lightGallery(document.getElementById('portfolio'), {
						thumbnail:true
					});
				}
			});
		</script>
		<?php }, 999);
	endif;

endif;