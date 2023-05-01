<?php
global $post;

list( $heading, $sub_heading ) = get_page_heading();
$noo_enable_parallax = noo_get_option( 'noo_enable_parallax', 1 );

$can_shortlist_candidate = noo_can_shortlist_candidate();

$layout = "";
if (is_singular('noo_resume')) {
	$layout = noo_get_option( 'noo_resumes_detail_layout', 'style-1' );
} else if(is_singular('noo_job')) {
	$layout = noo_get_option( 'noo_job_detail_layout', 'style-1' );
}

$layout = ! empty( $_GET[ 'layout' ] ) ? sanitize_text_field( $_GET[ 'layout' ] ) : $layout;

if ( ! empty( $heading ) && apply_filters('noo_enable_heading', true) ) :
	$heading_image = get_page_heading_image();?>
	
	<?php if ( is_post_type_archive( 'noo_job' ) or is_tax( get_object_taxonomies( 'noo_job' ) )): ?>
		<?php noo_get_layout( 'job/heading-job' ); ?>
        <?php return;  ?>
    <?php endif; ?>
    <?php if(noo_get_option('resume_search_form',1)): ?>
        <?php if (is_post_type_archive('noo_resume')):?>
        <?php noo_get_layout('resume/heading-resume'); ?>
        <?php return;  ?>
        <?php endif; ?>
    <?php endif; ?>
	<?php if ( ! empty( $heading_image ) ) : ?>
		<?php if ( is_singular( 'noo_company' ) && Noo_Company::get_layout() == 'two' ) : ?>
			<header class="noo-page-heading noo-page-heading-company-2" style="<?php echo ( ! $noo_enable_parallax ) ? 'background: url(' . esc_url( $heading_image ) . ') no-repeat center center; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover;' : ''; ?>">
		<?php else: ?>
			<header class="noo-page-heading <?php 
				if(is_singular('noo_resume')) {
					if ('style-1' == $layout) {
						echo 'noo-page-resume-heading-1';
					} elseif ('style-2' == $layout){
						echo 'noo-page-resume-heading-2';
					} elseif ('style-3' == $layout){
						echo 'noo-page-resume-heading-3';
					} elseif ('style-4' == $layout){
						echo 'noo-page-resume-heading-4';
					}
				} elseif(is_singular('noo_job')){
					if ('style-1' == $layout) {
						echo 'noo-page-job-heading-1';
					} elseif ('style-2' == $layout){
						echo 'noo-page-job-heading-2';
					} elseif ('style-3' == $layout){
						echo 'noo-page-job-heading-3';
					} elseif ('style-4' == $layout){
						echo 'noo-page-job-heading-4';
					}
				} ?>"
	        style="<?php echo ( ! $noo_enable_parallax ) ? 'background: url(' . esc_url( $heading_image ) . ') no-repeat center center; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover;' : ''; ?>">
		<?php endif; ?>
	<?php else : ?>
		<header class="noo-page-heading <?php echo strtolower( preg_replace( '/\s+/', '-', $sub_heading ) ); ?>">
			<?php endif; ?>
			<div class="container-boxed max" style="position: relative; z-index: 1;">
				<?php
				$page_temp = get_page_template_slug();
				?>
				<?php 
				if ( is_singular( 'noo_job' ) ) : 
					if ($layout == 'style-3') :
					$company_id 	= jm_get_job_company($post);
					$company_title 	=  noo_get_the_company_name($company_id);
			        if (!empty($company_id)) {				    
				        $company_logo 	= Noo_Company::get_company_logo($company_id, 'company-logo'); 				    
					}
				?>
					<div class="logo-company">
						<a href="<?php echo get_permalink($company_id); ?>">
							<?php echo $company_logo; ?>
						</a>
						<h5 class="company-title"><?php echo $company_title; ?></h5>
					</div>
				<?php	
					endif;
				endif; 
				?>
				<?php if ( 'page-post-resume.php' === $page_temp || 'page-post-job.php' === $page_temp || ( is_user_logged_in() && get_the_ID() == Noo_Member::get_member_page_id() ) ): ?>
					<div class="member-heading-avatar">
						<?php echo noo_get_avatar( get_current_user_id(), 100 ); ?>
					</div>
					<div class="page-heading-info">
						<?php if ( Noo_Member::is_employer() ):   ?>
							<?php
							$user_employer = get_current_user_id();
							$company_employer = get_user_meta( $user_employer, 'employer_company', true );
							$url_employer = get_permalink($company_employer);
					        $enable_profile_status = Noo_Member::get_setting('enable_profile_status',false);
                            if($enable_profile_status):
                                $profile_percent_employer = noo_get_profile_percent_company(); ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="pregress-bar ">
                                        <div class="progress_title"><span><?php echo __('Profile Percent', 'noo'); ?></span></div>
                                        <div class="progress">
                                            <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="70" class="progress-bar progress-bar-bg" data-valuenow="<?php esc_attr_e($profile_percent_employer ); ?>" role="progressbar" style="width: <?php esc_attr_e($profile_percent_employer); ?>%;">

                                            </div>
                                            <div class="progress_label" style="opacity: 1;"><span><?php echo esc_attr($profile_percent_employer); ?></span><?php _e('%', 'noo'); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
							<h1 class="page-title" ><a href="<?php echo esc_url($url_employer); ?>" style="color:#fff;" ><?php echo ( $heading ); ?></a></h1>
						<?php elseif ( Noo_Member::is_candidate() ): ?>
                            <?php $enable_profile_status = Noo_Member::get_setting('enable_profile_status',false);
                            if($enable_profile_status): ?>
                            <?php $profile_percent_candidate= noo_get_profile_percent_resume(); ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="pregress-bar ">
                                        <div class="progress_title"><span><?php echo __('Profile Percent', 'noo'); ?></span></div>
                                        <div class="progress">
                                            <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="70" class="progress-bar progress-bar-bg" data-valuenow="<?php esc_attr_e( $profile_percent_candidate ); ?>" role="progressbar" style="width: <?php esc_attr_e( $profile_percent_candidate ); ?>%;">

                                            </div>
                                            <div class="progress_label" style="opacity: 1;"><span><?php echo esc_attr($profile_percent_candidate ); ?></span><?php _e('%', 'noo'); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
							<h1 class="page-title"><a href="<?php echo Noo_Member::get_candidate_profile_url();  ?>" style="color:#fff;" ><?php echo ( $heading ); ?></a></h1>
						<?php endif; ?>
					</div>
				<?php elseif ( is_singular( 'noo_company' ) ) : ?>
					<?php
					$logo_company   = Noo_Company::get_company_logo( get_the_ID(), 'company-logo' );					
					$slogan         = noo_get_post_meta( $post->ID, '_slogan' );
					$layout_company = Noo_Company::get_layout();
					?>
					<div class="noo-company-heading">
						<div class="noo-company-info">
							<div class="noo-company-avatar">
								<a href="<?php echo get_permalink(); ?>"><?php echo $logo_company; ?></a>
							</div>
							<div class="noo-company-info">
								<h1 class="noo-company-name">
									<span <?php noo_page_title_schema(); ?>><?php echo noo_get_the_company_name(get_the_ID()); ?></span>
									<?php
									$settings_fields    = get_theme_mod('noo_company_list_fields');
									$content_meta       = !is_array($settings_fields) ? explode(',', $settings_fields) : $settings_fields;
									
									if(!empty($content_meta) && in_array('company_view', $content_meta)){
										$post_view      = noo_get_post_views( get_the_ID() );
										if ( $post_view > 0 ) {
											echo '<span class="count">' . sprintf( _n( '%d view', '%d views', $post_view, 'noo' ), $post_view ) . '</span>';
										}
									}
									?>
								</h1>
								<?php if ( ! empty( $slogan ) ) : ?>
									<div class="slogan">
										<?php echo esc_html( $slogan ); ?>
									</div>
								<?php endif; ?>

								<?php if ( 'two' == $layout_company ) : ?>
									<div class="company-meta">
										<?php
										$all_socials = noo_get_social_fields();
										$socials     = jm_get_company_socials();
										$html        = array();

										foreach ( $socials as $social ) {
											if ( ! isset( $all_socials[ $social ] ) ) {
												continue;
											}
											$data  = $all_socials[ $social ];
											$value = get_post_meta( get_the_ID(), "_{$social}", true );
											if ( ! empty( $value ) ) {
												$url    = $social == 'email_address' ? 'mailto:' . $value : esc_url( $value );
												if($data['icon'] == 'fa-link'){
									              	$html[] = '<a title="' . sprintf(esc_attr__('Connect with us on %s', 'noo'), $data['label']) . '" class="noo-icon fa ' . $data['icon'] . '" href="' . $url . '" target="_blank"></a>';
									            }else{
									              	$html[] = '<a title="' . sprintf(esc_attr__('Connect with us on %s', 'noo'), $data['label']) . '" class="noo-icon fab ' . $data['icon'] . '" href="' . $url . '" target="_blank"></a>';
									            }
											}
										}

										if ( ! empty( $html ) && count( $html ) > 0 ) : ?>
											<div class="company-social">
												<?php echo implode( "\n", $html ); ?>
											</div>
										<?php endif; ?>

										<?php if ( Noo_Company::review_is_enable() ): ?>

											<span class="total-review">
		                                        <?php noo_box_rating( noo_get_total_point_review( get_the_ID() ), true ) ?>
												<span><?php echo '(' . noo_get_total_review( get_the_ID() ) . ')' ?></span>
		                                    </span>

										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
						<div class="noo-company-action">
                            <?php
                                $can_follow_company = noo_can_follow_company();                                
                            ?>
                            <?php if($can_follow_company): 
                            	$follow_count 	    = noo_total_follow( get_the_ID() );?>
			                    <span class="noo-follow-company" data-company-id="<?php echo get_the_ID() ?>"
			                          data-user-id="<?php echo get_current_user_id(); ?>">
			                        <?php echo noo_follow_status( get_the_ID(), get_current_user_id() ) ?>
			                    </span>
			                    <?php if($follow_count > 0):?>
				                    <span class="total-follow">
				                        <?php echo sprintf( _n( '<span>%s</span> Follower', '<span>%s</span> Followers', $follow_count, 'noo' ), $follow_count ); ?>
				                    </span>
				                <?php endif;?>
                            <?php endif; ?>
						</div>
					</div>

				<?php else: ?>
					<div class="page-heading-info">
						<?php 
						//Single Resume
						if ( is_singular( 'noo_resume' ) ) : ?>
							<?php
							$layout      = noo_get_option( 'noo_resumes_detail_layout', 'style-1' );
							$layout = ! empty( $_GET[ 'layout' ] ) ? sanitize_text_field( $_GET[ 'layout' ] ) : $layout;
							if ( 'style-1' == $layout ) {
							?>
								<h1 class="page-title">
									<span <?php noo_page_title_schema(); ?>><?php echo ( $heading ); ?></span>
									
									<?php if( $can_shortlist_candidate ):?>
										<a class="noo-shortlist" 
											href="#"
										   	data-resume-id="<?php echo esc_attr( $post->ID ) ?>"
										   	data-user-id="<?php echo get_current_user_id() ?>" data-type="text">
											<?php echo noo_shortlist_icon( $post->ID, get_current_user_id() ) ?>
											<?php echo noo_shortlist_status( $post->ID, get_current_user_id() ) ?>
										</a>
									<?php endif; ?>
									<?php
									global $post;
									$resume_view    = get_theme_mod('noo_resume_show_fields');
									if($resume_view){
										$post_view = noo_get_post_views( $post->ID );
										if ( $post_view > 0 ) {
											echo '<span class="count">' . sprintf( _n( '%d view', '%d views', $post_view, 'noo' ), $post_view ) . '</span>';
										}
									}
									?>
									
							<?php } elseif ( 'style-2' == $layout ) {
								$candidate_avatar   = '';
								$candidate_name     = '';
									if ( ! empty( $post->post_author ) ) :
										$candidate_avatar = noo_get_avatar( $post->post_author, 85 );
										$candidate      = get_user_by( 'id', $post->post_author );
										$candidate_name = $candidate->display_name;
										$candidate_link = esc_url( apply_filters( 'noo_resume_candidate_link', get_the_permalink(), $post->ID, $post->post_author ) );
										$slogan         = noo_get_post_meta( $post->ID, '_slogan' );
										$enable_upload  = (bool) jm_get_resume_setting( 'enable_upload_resume', '1' );
										$file_cv        = noo_json_decode( noo_get_post_meta( $post->ID, '_noo_file_cv' ) );
										?>
											<div class="noo-resume-info-heading resume-heading-2">
												<div class="resume-avatar">
													<a href="<?php echo $candidate_link; ?>">
														<?php echo $candidate_avatar; ?>
													</a>
												</div>
												<div class="resume-info">
			                                        <?php $resume_id=$post->ID;
			                                            $can_download_resume=jm_can_download_cv_upload($resume_id);
			                                            $user_always_can_download = jm_user_alwasy_download_cv($resume_id);
	                                                    $can_download_resume_setting = jm_get_resume_setting('who_can_download_resume');
			                                            $remain_download_cv = jm_get_download_cv_remain();
			                                        ?>
	                                                <?php if ($enable_upload && !empty($file_cv) && isset($file_cv[0]) && !empty($file_cv[0])) : ?>
	                                                    <?php if ($can_download_resume == true): ?>
	                                                            <?php if($can_download_resume_setting !== 'package' || $user_always_can_download): ?>
	                                                            <a class="btn btn-primary resume-download  pull-right"
	                                                               href="<?php echo noo_get_file_upload( $file_cv[ 0 ] ); ?>"
	                                                               target="_blank"
	                                                               title="<?php echo esc_attr__( 'Download CV', 'noo' ); ?>">
	                                                                <i class="fa fa-download"></i>
	                                                                <?php echo esc_html__( 'Download CV', 'noo' ); ?>
	                                                            </a>

	                                                            <?php elseif($can_download_resume_setting == 'package'): 
	                                                            	$remain_download_cv_text = sprintf(esc_html__('Remain %s download times.', 'noo'), $remain_download_cv);
								                                    // check resume download unlimit -1 <-> 99999999 download times
	                                                            	if(strlen((string)$remain_download_cv) >= 7) $remain_download_cv_text = esc_html__('Unlimited download times','noo'); // check download resume unlimit -1 <-> 99999999 download times
	                                                            ?>
	                                                            <form  method="POST">
	                                                            	<span class="btn-download-cv pull-right"
	                                                                  data-resume-id = "<?php echo esc_attr($resume_id); ?>"
	                                                                  data-id="<?php echo get_current_user_id(); ?>"
	                                                                  data-download-count="<?php echo esc_attr($remain_download_cv); ?>"
	                                                                  data-toggle="tooltip"
	                                                                  data-link-download = "<?php echo $file_cv[0];?>"
	                                                                  title="<?php echo esc_attr($remain_download_cv_text) ?>">
	                                                                     <a class="btn btn-primary resume-download  pull-right"  href="#" title="<?php echo esc_attr__( 'Download CV', 'noo' ); ?>">
	                                                                            <i class="fa fa-download"></i>
	                                                                         <?php echo esc_html__( 'Download CV', 'noo' ); ?>
	                                                                     </a>
	                                                                 </span>
	                                                            </form>
	                                                            <?php endif; ?>
	                                                    <?php else: ?>
	                                                    <div class="pull-right">
	                                                        <?php
	                                                        list($title, $link) = jm_message_cannot_download_cv_candidate();
	                                                        echo apply_filters( 'noo_resume_candidate_private_message',$title, $resume_id );
	                                                        if( !empty( $link ) ) {
	                                                        	echo $link;
	                                                        }
	                                                        ?>
	                                                    </div>
			                                            <?php endif; ?>
													<?php endif; ?>

													<h1 class="item-author">
														<a href="<?php echo $candidate_link; ?>"
														   title="<?php echo esc_html( $candidate_name ); ?>">
															<?php echo esc_html( $candidate_name ); ?>
														</a>
														<?php
														$resume_view    = get_theme_mod('noo_resume_show_fields');
														if($resume_view){
															$post_view = noo_get_post_views( $post->ID );
															if ( $post_view > 0 ) {
																echo '<span class="count">' . sprintf( _n( '(%d views)', '(%d views)', $post_view, 'noo' ), $post_view ) . '</span>';
															}
														}
														?>
													</h1>
													<?php if ( ! empty( $slogan ) ) : ?>
														<h2 class="resume-slogan">
															<?php echo esc_html( $slogan ) ?>
														</h2>
													<?php endif; ?>

													<?php
													// Job's social info
													$all_socials = noo_get_social_fields();
													$socials     = jm_get_resume_socials();
													$enable_socials =noo_get_option('noo_resume_social','1');
													$enable_print = (bool) jm_get_resume_setting('enable_print_resume','1'); 
													$html         = array();

													foreach ( $socials as $social ) {
														if ( ! isset( $all_socials[ $social ] ) ) {
															continue;
														}
														$data  = $all_socials[ $social ];
														$value = get_post_meta( $post->ID, $social, true );
														if ( ! empty( $value ) ) {
															$url    = esc_url( $value );
															if($data['icon'] == 'fa-link'){
												              	$html[] = '<a title="' . sprintf(esc_attr__('Connect with us on %s', 'noo'), $data['label']) . '" class="noo-icon fa ' . $data['icon'] . '" href="' . $url . '" target="_blank"></a>';
												            }else{
												              	$html[] = '<a title="' . sprintf(esc_attr__('Connect with us on %s', 'noo'), $data['label']) . '" class="noo-icon fab ' . $data['icon'] . '" href="' . $url . '" target="_blank"></a>';
												            }
														}
													}
													?>
	                                                <?php if( jm_can_view_candidate_contact( $resume_id ) ): ?>
														<div class="candidate-social">
															<?php if ( $enable_socials && ! empty( $html ) && count( $html ) > 0 ) : ?>
																<?php echo implode( "\n", $html ); ?>
															<?php endif; ?>
															<?php if ( $enable_print && jm_can_view_resume($resume_id)) : ?>
		                                                        <a data-resume="<?php echo esc_attr($post->ID); ?>" data-total-review="<?php echo (noo_get_total_review($post->ID)) ?>"  data-layout ="style-2"  data-post-review = "disable"  class=" btn-print-resume print-resume noo-icon" href="#"  title="<?php echo esc_attr__('Print', 'noo'); ?>"> <i class="fa fa-print"></i>
		                                                        </a>
		                                                        <?php endif; ?>
															<?php if( $can_shortlist_candidate ):?>
																<a class="noo-shortlist" href="#" data-resume-id="<?php echo esc_attr( $post->ID ) ?>" data-user-id="<?php echo get_current_user_id() ?>" data-type="text">
																	<?php echo noo_shortlist_icon( $post->ID, get_current_user_id() ) ?>
																	<?php echo noo_shortlist_status( $post->ID, get_current_user_id() ) ?>
																</a>
															<?php endif; ?>
														</div>
	                                                <?php endif; ?>
												</div>
											</div>
											<?php
									endif;
							?>
							<?php } elseif ( 'style-3' == $layout ) {
								$candidate_avatar   = '';
								$candidate_name     = '';
									if ( ! empty( $post->post_author ) ) :
										$candidate_avatar = noo_get_avatar( $post->post_author, 85 );
										$candidate      = get_user_by( 'id', $post->post_author );
										$candidate_name = $candidate->display_name;
										$candidate_link = esc_url( apply_filters( 'noo_resume_candidate_link', get_the_permalink(), $post->ID, $post->post_author ) );
										$slogan         = noo_get_post_meta( $post->ID, '_slogan' );
										$enable_upload  = (bool) jm_get_resume_setting( 'enable_upload_resume', '1' );
										$file_cv        = noo_json_decode( noo_get_post_meta( $post->ID, '_noo_file_cv' ) );
										?>
											<div class="noo-resume-info-heading resume-heading-3">
												<?php
													$resume_view    = get_theme_mod('noo_resume_show_fields');
													if($resume_view){
														$post_view = noo_get_post_views( $post->ID );
														if ( $post_view > 0 ) {
															echo '<span class="count">' . sprintf( _n( '%d view', '%d views', $post_view, 'noo' ), $post_view ) . '</span>';
														}
													}
												?>
												<h1 class="page-title">
													<span <?php noo_page_title_schema(); ?>><?php echo ( $heading ); ?></span>
												</h1>
												<div class="resume-avatar">
													<a href="<?php echo $candidate_link; ?>">
														<?php echo $candidate_avatar; ?>
													</a>
												</div>
												<div class="resume-info">
			                                        <?php $resume_id=$post->ID;
			                                            $can_download_resume=jm_can_download_cv_upload($resume_id);
			                                            $user_always_can_download = jm_user_alwasy_download_cv($resume_id);
	                                                    $can_download_resume_setting = jm_get_resume_setting('who_can_download_resume');
			                                            $remain_download_cv = jm_get_download_cv_remain();
			                                        ?>

													<h2 class="item-author">
														<a href="<?php echo $candidate_link; ?>"
														   title="<?php echo esc_html( $candidate_name ); ?>">
															<?php echo esc_html( $candidate_name ); ?>
														</a>
													</h2>
													<?php if ( ! empty( $slogan ) ) : ?>
														<h2 class="resume-slogan">
															<?php echo esc_html( $slogan ) ?>
														</h2>
													<?php endif; ?>

													<?php
													// Job's social info
													$all_socials = noo_get_social_fields();
													$socials     = jm_get_resume_socials();
													$enable_socials =noo_get_option('noo_resume_social','1');
													$enable_print = (bool) jm_get_resume_setting('enable_print_resume','1'); 
													$html         = array();

													foreach ( $socials as $social ) {
														if ( ! isset( $all_socials[ $social ] ) ) {
															continue;
														}
														$data  = $all_socials[ $social ];
														$value = get_post_meta( $post->ID, $social, true );
														if ( ! empty( $value ) ) {
															$url    = esc_url( $value );
															if($data['icon'] == 'fa-link'){
												              	$html[] = '<a title="' . sprintf(esc_attr__('Connect with us on %s', 'noo'), $data['label']) . '" class="noo-icon fa ' . $data['icon'] . '" href="' . $url . '" target="_blank"></a>';
												            }else{
												              	$html[] = '<a title="' . sprintf(esc_attr__('Connect with us on %s', 'noo'), $data['label']) . '" class="noo-icon fab ' . $data['icon'] . '" href="' . $url . '" target="_blank"></a>';
												            }
														}
													}
													?>
	                                                <?php if( jm_can_view_candidate_contact( $resume_id ) ): ?>
													<div class="candidate-social">
														<?php if ( $enable_socials && ! empty( $html ) && count( $html ) > 0 ) : ?>
															<?php echo implode( "\n", $html ); ?>
														<?php endif; ?>
														<?php if ( $enable_print && jm_can_view_resume($resume_id)) : ?>
	                                                        <a data-resume="<?php echo esc_attr($post->ID); ?>"
	                                                           data-total-review="<?php echo (noo_get_total_review($post->ID)) ?>"
	                                                           data-layout ="style-2"
	                                                           data-post-review = "disable"
	                                                           class=" btn-print-resume print-resume noo-icon" href="#"
	                                                           title="<?php echo esc_attr__('Print', 'noo'); ?>">
	                                                            <i class="fa fa-print"></i>
	                                                        </a>
	                                                        <?php endif; ?>
													</div>
	                                                <?php endif; ?>

	                                                <?php if ($enable_upload && !empty($file_cv) && isset($file_cv[0]) && !empty($file_cv[0])) : ?>
	                                                    <?php if ($can_download_resume == true): ?>
	                                                            <?php if($can_download_resume_setting !== 'package' || $user_always_can_download): ?>
	                                                            <a class="btn btn-primary resume-download"
	                                                               href="<?php echo noo_get_file_upload( $file_cv[ 0 ] ); ?>"
	                                                               target="_blank"
	                                                               title="<?php echo esc_attr__( 'Download CV', 'noo' ); ?>">
	                                                                <i class="fa fa-download"></i>
	                                                                <?php echo esc_html__( 'Download CV', 'noo' ); ?>
	                                                            </a>

	                                                            <?php elseif($can_download_resume_setting == 'package'): 
	                                                            	$remain_download_cv_text = sprintf(esc_html__('Remain %s download times.', 'noo'), $remain_download_cv);
								                                    // check resume download unlimit -1 <-> 99999999 download times
	                                                            	if(strlen((string)$remain_download_cv) >= 7) $remain_download_cv_text = esc_html__('Unlimited download times','noo'); // check download resume unlimit -1 <-> 99999999 download times
	                                                            ?>
	                                                            <form  method="POST">
	                                                            <span class="btn-download-cv"
	                                                                  data-resume-id = "<?php echo esc_attr($resume_id); ?>"
	                                                                  data-id="<?php echo get_current_user_id(); ?>"
	                                                                  data-download-count="<?php echo esc_attr($remain_download_cv); ?>"
	                                                                  data-toggle="tooltip"
	                                                                  data-link-download = "<?php echo $file_cv[0];?>"
	                                                                  title="<?php echo esc_attr($remain_download_cv_text) ?>">
	                                                                     <a class="btn btn-primary resume-download  pull-right"
	                                                                        href="#"
	                                                                        title="<?php echo esc_attr__( 'Download CV', 'noo' ); ?>">
	                                                                            <i class="fa fa-download"></i>
	                                                                         <?php echo esc_html__( 'Download CV', 'noo' ); ?>
	                                                                     </a>
	                                                                 </span>
	                                                            </form>
	                                                            <?php endif; ?>
	                                                    <?php else: ?>
	                                                    <div class="pull-right">
	                                                        <?php
	                                                        list($title, $link) = jm_message_cannot_download_cv_candidate();
	                                                        echo apply_filters( 'noo_resume_candidate_private_message',$title, $resume_id );
	                                                        if( !empty( $link ) ) echo $link;
	                                                        ?>
	                                                    </div>
			                                            <?php endif; ?>
													<?php endif; ?>

	                                                <?php if( $can_shortlist_candidate ):?>
														<a class="noo-shortlist btn btn-primary" href="#"
														   data-resume-id="<?php echo esc_attr( $post->ID ) ?>"
														   data-user-id="<?php echo get_current_user_id() ?>" data-type="text">
															<?php echo noo_shortlist_icon( $post->ID, get_current_user_id() ) ?>
															<?php echo noo_shortlist_status( $post->ID, get_current_user_id() ) ?>
														</a>
													<?php endif; ?>

												</div>
											</div>
											<?php
									endif;
							?>
							<?php } elseif ( 'style-4' == $layout ) {
								$candidate_avatar   = '';
								$candidate_name     = '';
									if ( ! empty( $post->post_author ) ) :
										$candidate_avatar = noo_get_avatar( $post->post_author, 85 );
										$candidate      = get_user_by( 'id', $post->post_author );
										$candidate_name = $candidate->display_name;
										$candidate_link = esc_url( apply_filters( 'noo_resume_candidate_link', get_the_permalink(), $post->ID, $post->post_author ) );
										$slogan         = noo_get_post_meta( $post->ID, '_slogan' );
										$enable_upload  = (bool) jm_get_resume_setting( 'enable_upload_resume', '1' );
										$file_cv        = noo_json_decode( noo_get_post_meta( $post->ID, '_noo_file_cv' ) );
										?>
											<div class="noo-resume-info-heading resume-heading-4">
												<div class="resume-avatar">
													<a href="<?php echo $candidate_link; ?>">
														<?php echo $candidate_avatar; ?>
													</a>
												</div>
												<div class="resume-info">
			                                        <?php $resume_id=$post->ID;
			                                            $can_download_resume=jm_can_download_cv_upload($resume_id);
			                                            $user_always_can_download = jm_user_alwasy_download_cv($resume_id);
	                                                    $can_download_resume_setting = jm_get_resume_setting('who_can_download_resume');
			                                            $remain_download_cv = jm_get_download_cv_remain();
			                                        ?>

													<?php
													$resume_view    = get_theme_mod('noo_resume_show_fields');
													if($resume_view){
														$post_view = noo_get_post_views( $post->ID );
														if ( $post_view > 0 ) {
															echo '<span class="count">' . sprintf( _n( '%d view', '%d views', $post_view, 'noo' ), $post_view ) . '</span>';
														}
													}
													?>
													<h1 class="item-author">
														<a href="<?php echo $candidate_link; ?>"
														   title="<?php echo esc_html( $candidate_name ); ?>">
															<?php echo esc_html( $candidate_name ); ?>
														</a>
													</h1>
													<?php if ( ! empty( $slogan ) ) : ?>
														<h2 class="resume-slogan">
															<?php echo esc_html( $slogan ) ?>
														</h2>
													<?php endif; ?>

													<?php
													// Job's social info
													$all_socials = noo_get_social_fields();
													$socials     = jm_get_resume_socials();
													$enable_socials =noo_get_option('noo_resume_social','1');
													$enable_print = (bool) jm_get_resume_setting('enable_print_resume','1'); 
													$html         = array();

													foreach ( $socials as $social ) {
														if ( ! isset( $all_socials[ $social ] ) ) {
															continue;
														}
														$data  = $all_socials[ $social ];
														$value = get_post_meta( $post->ID, $social, true );
														if ( ! empty( $value ) ) {
															$url    = esc_url( $value );
															if($data['icon'] == 'fa-link'){
												              	$html[] = '<a title="' . sprintf(esc_attr__('Connect with us on %s', 'noo'), $data['label']) . '" class="noo-icon fa ' . $data['icon'] . '" href="' . $url . '" target="_blank"></a>';
												            }else{
												              	$html[] = '<a title="' . sprintf(esc_attr__('Connect with us on %s', 'noo'), $data['label']) . '" class="noo-icon fab ' . $data['icon'] . '" href="' . $url . '" target="_blank"></a>';
												            }
														}
													}
													?>
	                                                <?php if( jm_can_view_candidate_contact( $resume_id ) ): ?>
													<div class="candidate-social">
														<?php if ( $enable_socials && ! empty( $html ) && count( $html ) > 0 ) : ?>
															<?php echo implode( "\n", $html ); ?>
														<?php endif; ?>
														<?php if ( $enable_print && jm_can_view_resume($resume_id)) : ?>
	                                                        <a data-resume="<?php echo esc_attr($post->ID); ?>"
	                                                           data-total-review="<?php echo (noo_get_total_review($post->ID)) ?>"
	                                                           data-layout ="style-2"
	                                                           data-post-review = "disable"
	                                                           class=" btn-print-resume print-resume noo-icon" href="#"
	                                                           title="<?php echo esc_attr__('Print', 'noo'); ?>">
	                                                            <i class="fa fa-print"></i>
	                                                        </a>
	                                                        <?php endif; ?>
													</div>
	                                                <?php endif; ?>
	                                                
													<?php if ($enable_upload && !empty($file_cv) && isset($file_cv[0]) && !empty($file_cv[0])) : ?>
	                                                    <?php if ($can_download_resume == true): ?>
	                                                            <?php if($can_download_resume_setting !== 'package' || $user_always_can_download): ?>
	                                                            <a class="btn btn-primary resume-download"
	                                                               href="<?php echo noo_get_file_upload( $file_cv[ 0 ] ); ?>"
	                                                               target="_blank"
	                                                               title="<?php echo esc_attr__( 'Download CV', 'noo' ); ?>">
	                                                                <i class="fa fa-download"></i>
	                                                                <?php echo esc_html__( 'Download CV', 'noo' ); ?>
	                                                            </a>

	                                                            <?php elseif($can_download_resume_setting == 'package'): 
	                                                            	$remain_download_cv_text = sprintf(esc_html__('Remain %s download times.', 'noo'), $remain_download_cv);
								                                    // check resume download unlimit -1 <-> 99999999 download times
	                                                            	if(strlen((string)$remain_download_cv) >= 7) $remain_download_cv_text = esc_html__('Unlimited download times','noo'); // check download resume unlimit -1 <-> 99999999 download times
	                                                            ?>
	                                                            <form  method="POST">
	                                                            <span class="btn-download-cv"
	                                                                  data-resume-id = "<?php echo esc_attr($resume_id); ?>"
	                                                                  data-id="<?php echo get_current_user_id(); ?>"
	                                                                  data-download-count="<?php echo esc_attr($remain_download_cv); ?>"
	                                                                  data-toggle="tooltip"
	                                                                  data-link-download = "<?php echo $file_cv[0];?>"
	                                                                  title="<?php echo esc_attr($remain_download_cv_text) ?>">
	                                                                     <a class="btn btn-primary resume-download  pull-right"
	                                                                        href="#"
	                                                                        title="<?php echo esc_attr__( 'Download CV', 'noo' ); ?>">
	                                                                            <i class="fa fa-download"></i>
	                                                                         <?php echo esc_html__( 'Download CV', 'noo' ); ?>
	                                                                     </a>
	                                                                 </span>
	                                                            </form>
	                                                            <?php endif; ?>
	                                                    <?php else: ?>
	                                                    <div class="pull-right">
	                                                        <?php
	                                                        list($title, $link) = jm_message_cannot_download_cv_candidate();
	                                                        echo apply_filters( 'noo_resume_candidate_private_message',$title, $resume_id );
	                                                        if( !empty( $link ) ) echo $link;
	                                                        ?>
	                                                    </div>
			                                            <?php endif; ?>
													<?php endif; ?>
													
													<?php if( $can_shortlist_candidate ):?>
														<a class="btn noo-shortlist" href="#"
														   data-resume-id="<?php echo esc_attr( $post->ID ) ?>"
														   data-user-id="<?php echo get_current_user_id() ?>" data-type="text">
															<?php echo noo_shortlist_icon( $post->ID, get_current_user_id() ) ?>
															<?php echo noo_shortlist_status( $post->ID, get_current_user_id() ) ?>
														</a>
													<?php endif; ?>

												</div>
											</div>
											<?php
									endif;
								}
							?>

						<?php 
						//Single Job
						elseif (is_singular( 'noo_job' )): ?>
							<?php 
								if ( is_singular( 'noo_job' ) ) {
								global $post;
								$post_view = noo_get_post_views( $post->ID );
								$layout =  noo_get_option('noo_job_detail_layout','style-1');
								$layout = isset($_GET['layout']) ? sanitize_text_field( $_GET['layout'] ) : $layout;
								$applications_count = noo_get_job_applications_count( $post->ID );
								$show_count = noo_get_option( 'noo_job_show_count', '' );
							?>
								<?php if ( $layout == 'style-1' ) { ?>
									<h1 class="page-title">
										<span <?php noo_page_title_schema(); ?>><?php echo ( $heading ); ?></span>
										<?php											
											if(!empty($show_count)){
												$show_count = explode(',', $show_count);
												if ( in_array('job_view', $show_count) && $post_view > 0 ) {
													echo '<span class="count views">' . sprintf( _n( '%d view', '%d views', $post_view, 'noo' ), $post_view ) . '</span>';
												}
												if ( in_array('job_apply', $show_count) && $applications_count > 0 ) {
													echo '<span class="count applications">' . sprintf( _n( '%d application', '%d applications', $applications_count, 'noo' ), $applications_count ) . '</span>';
												}
											}
										?>
									</h1>
								<?php } elseif( $layout == 'style-3' || $layout == 'style-4' ) { ?>
									<h1 class="page-title title">
										<div class="count-application">
											<?php
											if(!empty($show_count)){
												$show_count = explode(',', $show_count);
												if ( in_array('job_view', $show_count) && $post_view > 0 ) {
													echo '<span class="count views">' . sprintf( _n( '%d view', '%d views', $post_view, 'noo' ), $post_view ) . '</span>';
												}
												if ( in_array('job_apply', $show_count) && $applications_count > 0 ) {
													echo '<span class="count applications">' . sprintf( _n( '%d application', '%d applications', $applications_count, 'noo' ), $applications_count ) . '</span>';
												}
											}	
											?>
										</div>
										<span class="heading-title" <?php noo_page_title_schema(); ?>><?php echo ( $heading ); ?></span>
									</h1>
								<?php } ?>
							<?php } ?>
						<?php else : ?>
							<h1 class="page-title" <?php noo_page_title_schema(); ?>>
								<?php 
								if(!is_page() ||  !noo_get_post_meta( get_the_ID(), '_noo_wp_page_hide_page_title_text', false )){
									echo $heading; 
								}
								
								if ( is_singular( 'noo_job' ) ) {
									global $post;
									$post_view = noo_get_post_views( $post->ID );
									$layout =  noo_get_option('noo_job_detail_layout','style-1');
									$layout = isset($_GET['layout']) ? sanitize_text_field( $_GET['layout'] ) : $layout;
									if ( $layout == 'style-1' ):
										if ( $post_view > 0 ) {
											echo '<span class="count  views">' . sprintf( _n( '%d view', '%d views', $post_view, 'noo' ), $post_view ) . '</span>';
										}
										$applications_count = noo_get_job_applications_count( $post->ID );
										if ( $applications_count > 0 ) {
											echo '<span class="count applications">' . sprintf( _n( '%d application', '%d applications', $applications_count, 'noo' ), $applications_count ) . '</span>';
										}
									endif;
								}
								?>
							</h1>
						<?php endif; ?>
						<?php if ( is_singular( 'post' ) ): ?>
							<?php noo_content_meta(); ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="page-sub-heading-info">
					<?php if ( is_singular( 'noo_job' ) ) :
						if ($layout == 'style-4') {
						$company_id 	= jm_get_job_company($post);
						$company_title 	=  noo_get_the_company_name($company_id);
					        if (!empty($company_id)) {				    
						        $company_logo 	= Noo_Company::get_company_logo($company_id, 'company-logo'); 				    
							}
							echo '<div class="logo-company"><a href=" ' . get_permalink($company_id) . ' "> ' . $company_logo . ' </a></div>';
							echo '<h2 class="company-title"> '.  $company_title .' </h2>';
						}
						$is_schema = noo_get_option( 'noo_job_schema', false );
						$layout =  noo_get_option('noo_job_detail_layout','style-1');
						$layout = isset($_GET['layout']) ? sanitize_text_field( $_GET['layout'] ) : $layout;
						if ($layout == 'style-1' || $layout == 'style-3' || $layout == 'style-4') {
							jm_the_job_meta( array(
								'show_company' => ($layout == 'style-3' || $layout == 'style-4') ? false : true,
								'fields'       => array(
									'job_type',
									'_full_address',
									'job_location',
									'job_date',
									'_closing',
									'job_category',
								),
								'schema'       => $is_schema ? true : false,
							) );	
						} else {
							jm_the_job_meta( array(
								'show_company' => true,
								'fields'       => false,
								'schema'       => $is_schema ? true : false,
							) );
						}

						
					elseif ( is_singular( 'noo_resume' ) ) :
						echo '';
					elseif ( is_singular( 'noo_company' ) ) :
						echo '';
					elseif ( is_single( 'post' ) ) :
						noo_content_meta();
					elseif ( ! empty( $sub_heading ) ) :
						echo $sub_heading;
					endif; ?>
				</div>
			</div><!-- /.container-boxed -->
			<?php if ( ! empty( $heading_image ) ) : ?>
				<?php if ( $noo_enable_parallax ) : ?>
					<div class=" parallax" data-parallax="1" data-parallax_no_mobile="1" data-velocity="0.1"
					     style="background-image: url(<?php echo esc_url( $heading_image ); ?>); background-position: 50% 0; background-repeat: no-repeat;"></div>
				<?php endif; ?>
			<?php endif; ?>
		</header>
	<?php endif; ?>
	<?php 
	//Member 
	if ( is_user_logged_in() && get_the_ID() == Noo_Member::get_member_page_id() ): ?>
		<div class="member-heading">
			<div class="container-boxed max">

				<div class="member-heading-nav">
					<?php

					$employer_heading_values  = jm_get_member_menu( 'employer_heading', array() );
					$candidate_heading_values = jm_get_member_menu( 'candidate_heading', array() );
					$enable_block_company  	  =    jm_get_action_control('enable_block_company');

					?>
					<ul>
						<?php if ( Noo_Member::is_employer() ) : ?>
				
							<?php if ( in_array( 'manage-job', $employer_heading_values ) or empty( $employer_heading_values ) ) : ?>
								<li class="<?php echo esc_attr( Noo_Member::get_actice_enpoint_class( array(
									'manage-job',
									'preview-job',
									'edit-job',
								) ) ) ?>"><a href="<?php echo Noo_Member::get_endpoint_url( 'manage-job' ) ?>"><i class="far fa-file-alt"></i> <?php _e( 'Jobs', 'noo' ) ?></a>
								</li>
							<?php endif; ?>

							<?php if ( in_array( 'manage-application', $employer_heading_values ) or empty( $employer_heading_values ) ) : ?>
								<li class="<?php echo esc_attr( Noo_Member::get_actice_enpoint_class( 'manage-application' ) ) ?>">
									<a href="<?php echo Noo_Member::get_endpoint_url( 'manage-application' ) ?>"
									   style="white-space: nowrap;">
										<i class="far fa-newspaper"></i>
										<?php _e( 'Applications', 'noo' ) ?>
										<?php echo unseen_applications_number(); ?>
									</a>
								</li>
							<?php endif; ?>
							<?php if ( in_array( 'viewed-resume', $employer_heading_values ) or empty( $employer_heading_values ) ) : ?>
								<?php
								if ( jm_is_enabled_job_package_view_resume() ) : ?>
									<li class="<?php echo esc_attr( Noo_Member::get_actice_enpoint_class( array( 'viewed-resume' ) ) ) ?>">
										<a href="<?php echo Noo_Member::get_endpoint_url( 'viewed-resume' ) ?>"><i class="far fa-file-alt"></i> <?php _e( 'Viewed Resumes', 'noo' ) ?>
										</a>
									</li>
								<?php endif; ?>
							<?php endif; ?>

							<?php do_action( 'noo-member-employer-heading' ); ?>

							<?php if ( in_array( 'manage-plan', $employer_heading_values ) or empty( $employer_heading_values ) ) : ?>
								<?php if ( jm_is_woo_job_posting() ) : ?>
									<li class="<?php echo esc_attr( Noo_Member::get_actice_enpoint_class( 'manage-plan' ) ) ?>">
										<a href="<?php echo Noo_Member::get_endpoint_url( 'manage-plan' ) ?>"><i class="far fa-file-alt"></i> <?php _e( 'Manage Plan', 'noo' ) ?>
										</a>
									</li>
								<?php endif; ?>
							<?php endif; ?>
                        <?php $can_follow_company = noo_can_follow_company(); ?>
                        <?php if($can_follow_company): ?>
							<?php if ( in_array( 'manage-follow', $employer_heading_values ) or empty( $employer_heading_values ) ) : ?>
								<li class="<?php echo esc_attr( Noo_Member::get_actice_enpoint_class( 'manage-follow' ) ) ?>">
									<a href="<?php echo Noo_Member::get_endpoint_url( 'manage-follow' ) ?>">
										<i class="fa fa-plus"></i>
										<?php _e( 'Manage Follow', 'noo' ) ?>
									</a>
								</li>
							<?php endif; ?>
							<?php if ( in_array( 'job-follow', $employer_heading_values ) or empty( $employer_heading_values ) ) : ?>
								<li class="<?php echo esc_attr( Noo_Member::get_actice_enpoint_class( 'job-follow' ) ) ?>">
									<a href="<?php echo Noo_Member::get_endpoint_url( 'job-follow' ) ?>">
										<i class="fa fa-plus"></i>
										<?php _e( 'Job Follow', 'noo' ) ?>
									</a>
								</li>
							<?php endif; ?>
                        <?php endif; ?>
                            <?php if(in_array('resume-suggest',$employer_heading_values)or empty($employer_heading_values)): ?>
                            <li class="<?php echo esc_attr(Noo_Member::get_actice_enpoint_class('resume-suggest')) ?>">
                                <a href="<?php echo Noo_Member::get_endpoint_url('resume-suggest') ?>">
                                    <i class="fa fa-plus"></i>
                                    <?php _e('Resume Suggest','noo') ?>
                                    <?php echo noo_get_resume_suggest_count(); ?>
                                </a>
                            </li>
                           <?php endif; ?>
                           <?php if ( in_array( 'shortlist', $employer_heading_values ) or empty( $employer_heading_values ) ) : ?>
		                        <?php $can_shortlist_candidate=noo_can_shortlist_candidate() ?>
		                        <?php if($can_shortlist_candidate): ?>							
									<li class="<?php echo esc_attr( Noo_Member::get_actice_enpoint_class( 'shortlist' ) ) ?>">
										<a href="<?php echo Noo_Member::get_endpoint_url( 'shortlist' ) ?>">
											<i class="fa fa-heart"></i>
											<?php _e( 'Shortlist', 'noo' ) ?>
										</a>
									</li>
								<?php endif; ?>
                        	<?php endif; ?>
							<?php if ( in_array( 'company_profile', $employer_heading_values ) or empty( $employer_heading_values ) ) : ?>
								<li class="<?php echo esc_attr( Noo_Member::get_actice_enpoint_class( 'company-profile' ) ) ?>">
									<a href="<?php echo Noo_Member::get_company_profile_url() ?>"><i class="far fa-user"></i> <?php _e( 'Company Profile', 'noo' ) ?></a>
								</li>
							<?php endif; ?>
                            <?php if ( in_array( 'resume-alert', $employer_heading_values ) or empty( $employer_heading_values ) ) : ?>
                                <?php if ( Noo_Resume_Alert::enable_resume_alert() ) : ?>
                                    <li class="<?php echo esc_attr( Noo_Member::get_actice_enpoint_class( array(
                                        'resume-alert',
                                        'add-resume-alert',
                                        'edit-resume-alert',
                                    ) ) ) ?>"><a href="<?php echo Noo_Member::get_endpoint_url( 'resume-alert' ) ?>"><i class="far fa-bell"></i> <?php _e( 'Resume Alerts', 'noo' ) ?></a>
                                    </li>
                                <?php endif; ?>

                            <?php endif; ?>
							<?php if ( in_array( 'signout', $employer_heading_values ) or empty( $employer_heading_values ) ) : ?>
								<li>
									<a href="<?php echo Noo_Member::get_logout_url() ?>">
										<i class="fas fa-sign-out-alt"></i> <?php _e( 'Sign Out', 'noo' ) ?>
									</a>
								</li>
							<?php endif; ?>
                        <?php

						// Candidate Menu

						elseif ( Noo_Member::is_candidate() ) : ?>
							<?php if ( jm_resume_enabled() ) : ?>
								<?php if ( in_array( 'manage-resume', $candidate_heading_values ) or empty( $candidate_heading_values ) ) : ?>
									
									<li class="<?php echo esc_attr( Noo_Member::get_actice_enpoint_class( array(
										'manage-resume',
										'preview-resume',
										'edit-resume',
									) ) ) ?>"><a
											href="<?php echo Noo_Member::get_endpoint_url( 'manage-resume' ) ?>"><i class="far fa-file-alt"></i> <?php _e( 'Resumes', 'noo' ) ?></a>
									</li>
								<?php endif; ?>
							<?php endif; ?>

							<?php if ( in_array( 'manage-job-applied', $candidate_heading_values ) or empty( $candidate_heading_values ) ) : ?>
								<li class="<?php echo esc_attr( Noo_Member::get_actice_enpoint_class( 'manage-job-applied' ) ) ?>">
									<a href="<?php echo Noo_Member::get_endpoint_url( 'manage-job-applied' ) ?>"
									   style="white-space: nowrap;"><i class="far fa-newspaper"></i> <?php _e( 'Applications', 'noo' ) ?></a>
								</li>
							<?php endif; ?>

							<?php if ( in_array( 'job-alert', $candidate_heading_values ) or empty( $candidate_heading_values ) ) : ?>
								<?php if ( Noo_Job_Alert::enable_job_alert() ) : ?>
									<li class="<?php echo esc_attr( Noo_Member::get_actice_enpoint_class( array(
										'job-alert',
										'add-job-alert',
										'edit-job-alert',
									) ) ) ?>"><a href="<?php echo Noo_Member::get_endpoint_url( 'job-alert' ) ?>"><i class="far fa-bell"></i> <?php _e( 'Job Alerts', 'noo' ) ?></a>
									</li>
								<?php endif; ?>

							<?php endif; ?>


							<?php do_action( 'noo-member-candidate-heading' ); ?>

							<?php if ( in_array( 'manage-plan', $candidate_heading_values ) or empty( $candidate_heading_values ) ) : ?>
								<?php if ( jm_is_woo_resume_posting() ) : ?>
									<li class="<?php echo esc_attr( Noo_Member::get_actice_enpoint_class( 'manage-plan' ) ) ?>">
										<a href="<?php echo Noo_Member::get_endpoint_url( 'manage-plan' ) ?>"><i class="far fa-file-alt"></i> <?php _e( 'Manage Plan', 'noo' ) ?>
										</a></li>
								<?php endif; ?>
							<?php endif; ?>
                            <?php if ( in_array( 'bookmark-job', $candidate_heading_values ) or empty( $candidate_heading_values ) ) : ?>

                                <?php if ( jm_is_enabled_job_bookmark() ): ?>
                                    <li class="<?php echo esc_attr( Noo_Member::get_actice_enpoint_class( 'bookmark-job' ) ) ?>">
                                        <a href="<?php echo Noo_Member::get_endpoint_url( 'bookmark-job' ) ?>">
                                            <i class="fa fa-heart"></i>
                                            <?php _e( 'Bookmarked', 'noo' ) ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                        <?php $can_follow_company = noo_can_follow_company(); ?>
                        <?php if($can_follow_company): ?>
							<?php if ( in_array( 'manage-follow', $candidate_heading_values ) or empty( $candidate_heading_values ) ) : ?>
								<li class="<?php echo esc_attr( Noo_Member::get_actice_enpoint_class( 'manage-follow' ) ) ?>">
									<a href="<?php echo Noo_Member::get_endpoint_url( 'manage-follow' ) ?>">
										<i class="fa fa-plus"></i>
										<?php _e( 'Manage Follow', 'noo' ) ?>
									</a>
								</li>
							<?php endif; ?>
							<?php endif; ?>
							<?php if ( in_array( 'job-follow', $candidate_heading_values ) or empty( $candidate_heading_values ) ) : ?>
								<li class="<?php echo esc_attr( Noo_Member::get_actice_enpoint_class( 'job-follow' ) ) ?>">
									<a href="<?php echo Noo_Member::get_endpoint_url( 'job-follow' ) ?>">
										<i class="fa fa-plus"></i>
										<?php _e( 'Job Follow', 'noo' ) ?>
									</a>
								</li>
							<?php endif; ?>
                        <?php endif; ?>
                            <?php if(in_array('job-suggest',$candidate_heading_values)or empty($candidate_heading_values)): ?>
                                <li class="<?php echo esc_attr(Noo_Member::get_actice_enpoint_class('job-suggest')) ?>">
                                    <a href="<?php echo Noo_Member::get_endpoint_url('job-suggest') ?>">
                                        <i class="fa fa-plus"></i>
                                        <?php _e('Job Suggest','noo') ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if((in_array('block-company',$candidate_heading_values) or empty($candidate_heading_values)) && ($enable_block_company=='enable')): ?>
                                <li class="<?php echo esc_attr(Noo_Member::get_actice_enpoint_class('block-company')) ?>">
                                    <a href="<?php echo Noo_Member::get_endpoint_url('block-company') ?>">
                                        <i class="fa fa-plus"></i>
                                        <?php _e('Block Companies','noo') ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ( in_array( 'shortlist', $candidate_heading_values ) or empty( $candidate_heading_values ) ) : ?>
		                        <?php $can_shortlist_candidate=noo_can_shortlist_candidate() ?>
		                        <?php if($can_shortlist_candidate): ?>
									<li class="<?php echo esc_attr( Noo_Member::get_actice_enpoint_class( 'shortlist' ) ) ?>">
										<a href="<?php echo Noo_Member::get_endpoint_url( 'shortlist' ) ?>">
											<i class="fa fa-heart"></i>
											<?php _e( 'Shortlist', 'noo' ) ?>
										</a>
									</li>
								<?php endif; ?>
	                        <?php endif; ?>

							<?php if ( in_array( 'candidate_profile', $candidate_heading_values ) or empty( $candidate_heading_values ) ) : ?>
								<li class="<?php echo esc_attr( Noo_Member::get_actice_enpoint_class( 'candidate-profile' ) ) ?>">
									<a href="<?php echo Noo_Member::get_candidate_profile_url() ?>"><i
											class="fa fa-user"></i> <?php _e( 'My Profile', 'noo' ) ?></a>
								</li>
							<?php endif; ?>

							<?php if ( in_array( 'signout', $candidate_heading_values ) or empty( $candidate_heading_values ) ) : ?>
								<li>
									<a href="<?php echo Noo_Member::get_logout_url() ?>">
										<i class="fas fa-sign-out-alt"></i> <?php _e( 'Sign Out', 'noo' ) ?>
									</a>
								</li>
							<?php endif; ?>


						<?php endif; ?>


					</ul>
				</div>
			</div>
		</div>
	<?php endif; ?>
<?php
do_action( 'after_heading' );
?>
