<?php 
global $post;
$user_per = noo_get_user_permission();
$candidate_id = isset($_GET['candidate_id']) ? absint($_GET['candidate_id']) : '';
$enable_upload = (bool) jm_get_resume_setting('enable_upload_resume', '1');
$enable_print = (bool) jm_get_resume_setting('enable_print_resume', '1');
if( get_the_ID() == Noo_Member::get_member_page_id() || jm_is_resume_posting_page() ) {
	$candidate_id = get_current_user_id();
	$resume_id = 0;
} else {
	$resume_id = isset( $_GET['resume_id'] ) ? $_GET['resume_id'] : get_the_ID();
	if( 'noo_resume' == get_post_type( $resume_id ) ) {
		$candidate_id = get_post_field( 'post_author', $resume_id);
	}
}

$file_cv = noo_json_decode( noo_get_post_meta( $post->ID, '_noo_file_cv' ) );
$slogan = noo_get_post_meta( $post->ID, '_slogan' );

$candidate = !empty($candidate_id) ? get_userdata($candidate_id) : false;

if( $candidate ) :
	$fields = jm_get_candidate_custom_fields();
	$all_socials = noo_get_social_fields();
	$socials = jm_get_candidate_socials();
	$email = $candidate ? $candidate->user_email : '';

?>
	<div class="resume-candidate-profile">
		<div class="row">
			<div class="col-sm-3 profile-avatar">
				<?php echo noo_get_avatar( $candidate_id, 160); ?>
			</div>
			<div class="col-sm-9 candidate-detail">
				<div class="candidate-title clearfix">
					<div class="pull-left">
                    <h2>
                        <?php echo esc_html( $candidate->display_name ); ?>
						<?php if( $candidate_id == get_current_user_id() ) : ?>
                            <a class="pull-right resume-action" href="<?php echo esc_url( Noo_Member::get_candidate_profile_url('candidate-profile') ); ?>" title="<?php echo esc_attr__('Edit Profile', 'noo'); ?>">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
						<?php endif; ?>
                    </h2>
                    <?php if ( !empty( $slogan ) ) : ?>
                        <h3 class="resume-slogan">
                            <?php echo esc_html( $slogan ); ?>
                        </h3>
                    <?php endif; ?>
                    </div>
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

                            <?php elseif($can_download_resume_setting == 'package'): ?>
                                <form  method="POST">
                                    <span class="btn-download-cv pull-right"
                                          data-resume-id = "<?php echo $resume_id; ?>"
                                          data-id="<?php echo get_current_user_id(); ?>"
                                          data-download-count="<?php echo $remain_download_cv ?>"
                                          data-toggle="tooltip"
                                          data-link-download = "<?php echo $file_cv[0];?>"
                                          title="<?php echo sprintf(esc_html__('Remain %s download times.', 'noo'), $remain_download_cv); ?>">
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
				</div>
				<?php do_action( 'noo_resume_candidate_profile_before', $resume_id ); ?>
                <?php if( apply_filters( 'jm_resume_show_candidate_contact', true, $resume_id ) ) : ?>
				<?php
				// Job's social info
				$socials = jm_get_resume_socials();
        		$enable_socials =noo_get_option('noo_resume_social','1');
				$html = array();

				foreach ($socials as $social) {
					if (!isset($all_socials[$social])) continue;
					$data = $all_socials[$social];
					$value = get_post_meta($resume_id, $social, true);
					if (!empty($value)) {
						$url = esc_url($value);
            if($data['icon'] == 'fa-link' || $data['icon'] == 'fa-envelope'){
              $html[] = '<a title="' . sprintf(esc_attr__('Connect with us on %s', 'noo'), $data['label']) . '" class="noo-icon fa ' . $data['icon'] . '" href="' . $url . '" target="_blank"></a>';
            }else{
              $html[] = '<a title="' . sprintf(esc_attr__('Connect with us on %s', 'noo'), $data['label']) . '" class="noo-icon fab ' . $data['icon'] . '" href="' . $url . '" target="_blank"></a>';
            }
					}
				}

			 ?>
          <div class="candidate-social">
          <?php 	if ( $enable_socials && !empty($html) && count($html) > 0) : ?>
	       <?php echo implode("\n", $html); ?>
          <?php endif; ?>
              <?php if ( $enable_print ) : ?>
                  <a data-resume="<?php echo esc_attr($post->ID); ?>"
                     data-total-review="<?php echo (noo_get_total_review($post->ID)) ?>"
                     data-layout ="style-1"
                     data-post-review = "disable"
                     class=" btn-print-resume print-resume noo-icon" href="#"
                     title="<?php echo esc_attr__('Print', 'noo'); ?>">
                      <i class="fa fa-print"></i>
                  </a>
              <?php endif; ?>
          </div>
          <?php $view_candidate_contact_package = jm_is_enabled_job_package_view_candidate_contact();
          $can_show_candidate_contact = jm_can_show_candidate_contact_with_package($resume_id);
          ?>
          <?php if($view_candidate_contact_package && (!$can_show_candidate_contact)): ?>
              <?php $remain_view_candidate_contact = jm_get_view_candidate_contact_remain(); ?>
                  <form method="post">
                     <span class="show-candidate-contact "
                           data-resume-id = "<?php echo $resume_id ?>"
                           data-id="<?php echo get_current_user_id(); ?>"
                           data-toggle="tooltip"
                           title="<?php echo sprintf(esc_html__('Remain %s views time.', 'noo'), $remain_view_candidate_contact); ?>">
                         <a  title="<?php echo esc_attr__( 'show candidate profile', 'noo' ); ?>">
                             <?php echo esc_html__('Click here to view candidate profile','noo') ?>
                             <i class="fa fa-eye" aria-hidden="true"></i>
                         </a>
                      </span>
                  </form>
          <?php endif; ?>
          <?php if(!$view_candidate_contact_package ||$can_show_candidate_contact): ?>
					<div class="candidate-info">
						<div class="row">
							<?php if( !empty( $fields ) ) : ?>
								<?php foreach ( $fields as $field ) :
									if( isset( $field['is_default'] ) ) {
										if( in_array( $field['name'], array( 'first_name', 'last_name', 'full_name', 'email') ) )
											continue; // don't display WordPress default user fields
									}
									$field_id = jm_candidate_custom_fields_name( $field['name'], $field );
									
									$value = get_user_meta( $candidate->ID, $field_id, true );									
                  $icon = isset($field['icon']) ? $field['icon'] : '';
                  $icon_class = str_replace("|", " ", $icon);


									if( is_array( $value ) ) {
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

									if( !empty( $value ) ) : ?>

										<div class="<?php echo esc_attr( $field_id ); ?> col-sm-6">
											<?php if($field['type'] == 'datepicker' ):
                        $value_date = noo_convert_custom_field_value( $field, $value );
                        if(is_numeric($value_date)){
                          $date = date_i18n('d/M/Y', $value_date);
                        }else{
                          $date = date_i18n('d/M/Y', strtotime($value_date));
                        }
											  
											?>
												<div class="<?php echo esc_attr( $field_id ); ?>">
													<span class="candidate-field-icon"><i class="<?php echo esc_attr($icon_class) ?>"></i></span>
													 <?php echo $date;?>
												</div>
											<?php else : ?>
												<div class="<?php echo esc_attr( $field_id ); ?>">
													<span class="candidate-field-icon"><i class="<?php echo esc_attr($icon_class) ?>"></i></span>
													 <?php echo $value;  ?>
												</div>
											<?php endif; ?>
										</div>
									<?php endif; ?>

								<?php endforeach; ?>
							<?php endif; ?>
							<?php if( !empty( $email ) ) : ?>
								<div class="email col-sm-6">
                  <a href="mailto:<?php echo esc_attr($email); ?>">
                        <span class="candidate-field-icon"><i class="fa fa-envelope text-primary"></i></span><?php echo esc_html($email); ?>
                  </a>

								</div>
							<?php endif; ?>
						</div>
					</div>
                     <?php endif; ?>
					<?php if( !empty( $candidate->description ) ) : ?>
						<div class="candidate-desc">
							<?php echo $candidate->description; ?>
						</div>
					<?php endif; ?>
				<?php else : ?>
					<?php
                         list($title, $link) = jm_message_cannot_view_contact_candidate();
                         echo apply_filters( 'noo_resume_candidate_private_message',$title, $resume_id );
                         if( !empty( $link ) ) echo $link;
					?>
				<?php endif; ?>
				<?php do_action( 'noo_resume_candidate_profile_after', $resume_id ); ?>
			</div>
		</div>
	</div>
<?php else: 
	echo '<h2 class="text-center" style="min-height:200px">'.__('Can not find this Candidate !','noo').'</h2>';
endif; ?>
<hr/>