<div class="noo-topbar">
	<div class="container-boxed max">
		<?php
		$topbar_layout = noo_get_option( 'noo_top_bar_layout', 'right' );
		$topbar_text   = noo_get_option( 'noo_top_bar_text', '' );
		if ( ! empty( $topbar_text ) ): ?>
			<div class="noo-topbar-text pull-<?php echo esc_attr( $topbar_layout == 'right' ? 'left' : 'right' ); ?>"><?php echo $topbar_text; ?></div>
		<?php endif; ?>
		<div class="pull-<?php echo $topbar_layout; ?>">
			<?php if ( noo_get_option( 'noo_top_bar_social', 1 ) ): ?>
				<div class="noo-topbar-social">
					<?php
					$facebook  = noo_get_option( 'noo_header_top_facebook_url', '' );
					$google    = noo_get_option( 'noo_header_top_google_plus_url', '' );
					$twitter   = noo_get_option( 'noo_header_top_twitter_url', '' );
					$linked    = noo_get_option( 'noo_header_top_linked_url', '' );
					$instagram = noo_get_option( 'noo_header_top_instagram_url', '' );
					$pinterest = noo_get_option( 'noo_header_top_pinterest_url', '' );
					$youtube   = noo_get_option( 'noo_header_top_youtube_url', '' );
					?>
					<ul>

						<?php if ( ! empty( $facebook ) ) : ?>
							<li>
								<a href="<?php echo esc_url( $facebook ); ?>" target="blank_"
								   class="fab fa-facebook"></a>
							</li>
						<?php endif; ?>

						<?php if ( ! empty( $google ) ) : ?>
							<li>
								<a href="<?php echo esc_url( $google ); ?>" target="blank_"
								   class="fab fa-google-plus"></a>
							</li>
						<?php endif; ?>

						<?php if ( ! empty( $twitter ) ) : ?>
							<li>
								<a href="<?php echo esc_url( $twitter ); ?>" target="blank_" class="fab fa-twitter"></a>
							</li>
						<?php endif; ?>

						<?php if ( ! empty( $linked ) ) : ?>
							<li>
								<a href="<?php echo esc_url( $linked ); ?>" target="blank_" class="fab fa-linkedin"></a>
							</li>
						<?php endif; ?>
						<?php if ( ! empty( $instagram ) ) : ?>
							<li>
								<a href="<?php echo esc_url( $instagram ); ?>" target="blank_"
								   class="fab fa-instagram"></a>
							</li>
						<?php endif; ?>

						<?php if ( ! empty( $pinterest ) ) : ?>
							<li>
								<a href="<?php echo esc_url( $pinterest ); ?>" target="blank_" class="fab fa-pinterest"></a>
							</li>
						<?php endif; ?>
						<?php if ( ! empty( $youtube ) ) : ?>
							<li>
								<a href="<?php echo esc_url( $youtube ); ?>" target="blank_" class="fab fa-youtube"></a>
							</li>
						<?php endif; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ( noo_get_option( 'noo_header_top_bar_login_link', 1 ) ): ?>

				<?php if ( is_user_logged_in() ): ?>

					<div class="noo-topbar-user">
						<a id="thumb-info" href="<?php echo Noo_Member::get_member_page_url(); ?>">
							<span class="profile-name"><?php echo Noo_Member::get_display_name(); ?></span>
							<span class="profile-avatar"><?php echo noo_get_avatar( get_current_user_id(), 36 ); ?></span>
							<?php echo user_notifications_number(); ?>
						</a>
						<ul class="sub-menu">
							<?php

							$employer_menu_values  		= jm_get_member_menu( 'employer_menu', array() );
							$candidate_menu_values 		= jm_get_member_menu( 'candidate_menu', array() );
							$enable_block_company  		= jm_get_action_control('enable_block_company');
							$hide_post_job 			  	= jm_get_member_menu( 'hide_post_job', '' );
							$hide_post_resume 		  	= jm_get_member_menu( 'hide_post_resume', '' );

							?>
							<?php if ( Noo_Member::is_employer() ): ?>
								<?php if( empty($hide_post_job) ):?>
									<li class="menu-item">
										<a href="<?php echo Noo_Member::get_post_job_url() ?>">
											<i class="fa fa-edit"></i> <?php _e( 'Post a Job', 'noo' ) ?>
										</a>
									</li>
								<?php endif; ?>
												
								<?php if ( in_array( 'manage-job', $employer_menu_values ) or empty( $employer_menu_values ) ) : ?>
									<li class="menu-item">
										<a href="<?php echo Noo_Member::get_endpoint_url( 'manage-job' ) ?>">
											<i class="far fa-file-alt"></i> <?php _e( 'Manage Jobs', 'noo' ) ?>
										</a>
									</li>
								<?php endif; ?>

								<?php if ( in_array( 'manage-application', $employer_menu_values ) or empty( $employer_menu_values ) ) : ?>
									<li class="menu-item">
										<a href="<?php echo Noo_Member::get_endpoint_url( 'manage-application' ) ?>" style="white-space: nowrap;">
											<i class="far fa-newspaper"></i> <?php _e( 'Manage Applications', 'noo' ) ?>
										</a>
									</li>
								<?php endif; ?>

								<?php if ( in_array( 'viewed-resume', $employer_menu_values ) or empty( $employer_menu_values ) ) : ?>
									<?php if ( jm_is_enabled_job_package_view_resume() ) : ?>
										<li class="menu-item">
											<a href="<?php echo Noo_Member::get_endpoint_url( 'viewed-resume' ) ?>">
												<i class="far fa-file-alt"></i> <?php _e( 'Viewed Resumes', 'noo' ) ?>
											</a>
										</li>
									<?php endif; ?>
								<?php endif; ?>

								<?php do_action( 'noo-member-employer-menu' ); ?>
								
								<li class="divider" role="presentation"></li>

								<?php if ( in_array( 'manage-plan', $employer_menu_values ) or empty( $employer_menu_values ) ) : ?>
									<?php if ( jm_is_woo_job_posting() ) : ?>
										<li class="menu-item">
											<a href="<?php echo Noo_Member::get_endpoint_url( 'manage-plan' ) ?>">
												<i class="far fa-file-alt"></i> <?php _e( 'Manage Plan', 'noo' ) ?>
											</a>
										</li>
									<?php endif; ?>
								<?php endif; ?>
                                <?php $can_follow_company = noo_can_follow_company(); ?>
                                <?php if ($can_follow_company): ?>
                                    <?php if (in_array('manage-follow', $employer_menu_values) or empty($employer_menu_values)) : ?>
                                        <li class="menu-item">
                                            <a href="<?php echo Noo_Member::get_endpoint_url('manage-follow') ?>">
                                                <i class="fa fa-plus"></i> <?php _e('Manage Follow', 'noo') ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (in_array('job-follow', $employer_menu_values) or empty($employer_menu_values)) : ?>
                                        <li class="menu-item">
                                            <a href="<?php echo Noo_Member::get_endpoint_url('job-follow') ?>">
                                                <i class="fa fa-plus"></i> <?php _e('Job Follow', 'noo') ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php $can_shortlist_candidate = noo_can_shortlist_candidate() ?>
                                <?php if ($can_shortlist_candidate): ?>
                                    <?php if (in_array('shortlist', $employer_menu_values) or empty($employer_menu_values)) : ?>
                                        <li class="menu-item">
                                            <a href="<?php echo Noo_Member::get_endpoint_url('shortlist') ?>">
                                                <i class="fa fa-heart"></i> <?php _e('Shortlist', 'noo') ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>
								<?php if ( in_array( 'company_profile', $employer_menu_values ) or empty( $employer_menu_values ) ) : ?>
									<li class="menu-item">
										<a href="<?php echo Noo_Member::get_company_profile_url() ?>">
											<i class="far fa-user"></i> <?php _e( 'Company Profile', 'noo' ) ?>
										</a>
									</li>
								<?php endif; ?>
                                <?php if ( in_array( 'resume-alert', $employer_menu_values  ) or empty( $employer_menu_values  ) ) : ?>
                                    <?php if ( Noo_Resume_Alert::enable_resume_alert() ) : ?>
                                        <li class="menu-item">
                                        	<a href="<?php echo Noo_Member::get_endpoint_url( 'resume-alert' ) ?>">
                                        		<i class="far fa-bell"></i> <?php _e( 'Resume Alerts', 'noo' ) ?>
                                        	</a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>
								<?php if ( in_array( 'signout', $employer_menu_values ) or empty( $employer_menu_values ) ) : ?>
									<li class="menu-item">
										<a href="<?php echo Noo_Member::get_logout_url() ?>">
											<i class="fas fa-sign-out-alt"></i> <?php _e( 'Sign Out', 'noo' ) ?>
										</a>
									</li>
								<?php endif; ?>


							<?php elseif ( Noo_Member::is_candidate() ): ?>
								<?php if ( jm_resume_enabled() ) : ?>
									<?php if ( empty($hide_post_resume) ) : ?>
										<li class="menu-item">
											<a href="<?php echo Noo_Member::get_post_resume_url() ?>">
												<i class="fa fa-edit"></i> <?php _e( 'Post a Resume', 'noo' ) ?>
											</a>
										</li>
									<?php endif; ?>
									<?php if ( in_array( 'manage-resume', $candidate_menu_values ) or empty( $candidate_menu_values ) ) : ?>
										<li class="menu-item">
											<a href="<?php echo Noo_Member::get_endpoint_url( 'manage-resume' ) ?>" style="white-space: nowrap;">
												<i class="far fa-file-alt"></i> <?php _e( 'Manage Resumes', 'noo' ) ?>
											</a>
										</li>
									<?php endif; ?>
								<?php endif; ?>

								<?php if ( in_array( 'manage-job-applied', $candidate_menu_values ) or empty( $candidate_menu_values ) ) : ?>
									<li class="menu-item">
										<a href="<?php echo Noo_Member::get_endpoint_url( 'manage-job-applied' ) ?>" style="white-space: nowrap;">
											<i class="far fa-newspaper"></i> <?php _e( 'Manage Applications', 'noo' ) ?>
										</a>
									</li>
								<?php endif; ?>

								<?php if ( in_array( 'job-alert', $candidate_menu_values ) or empty( $candidate_menu_values ) ) : ?>
									<?php if ( Noo_Job_Alert::enable_job_alert() ) : ?>
										<li class="menu-item">
											<a href="<?php echo Noo_Member::get_endpoint_url( 'job-alert' ) ?>">
												<i class="far fa-bell"></i> <?php _e( 'Jobs Alert', 'noo' ) ?>
											</a>
										</li>
									<?php endif; ?>
								<?php endif; ?>
								<?php do_action( 'noo-member-candidate-menu' ); ?>
								<li class="divider" role="presentation"></li>

								<?php if ( in_array( 'manage-plan', $candidate_menu_values ) or empty( $candidate_menu_values ) ) : ?>
									<?php if ( jm_is_woo_resume_posting() ) : ?>
										<li class="menu-item">
											<a href="<?php echo Noo_Member::get_endpoint_url( 'manage-plan' ) ?>">
												<i class="far fa-file-alt"></i> <?php _e( 'Manage Plan', 'noo' ) ?>
											</a>
										</li>
									<?php endif; ?>
								<?php endif; ?>

                                <?php $can_follow_company = noo_can_follow_company(); ?>
                                <?php if ($can_follow_company): ?>
                                    <?php if (in_array('manage-follow', $candidate_menu_values) or empty($candidate_menu_values)) : ?>
                                        <li class="menu-item">
                                            <a href="<?php echo Noo_Member::get_endpoint_url('manage-follow') ?>">
                                                <i class="fa fa-plus"></i> <?php _e('Manage Follow', 'noo') ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if((in_array('block-company',$candidate_menu_values) or empty($candidate_menu_values)) && ($enable_block_company=='enable')): ?>
							                <li class="<?php echo esc_attr(Noo_Member::get_actice_enpoint_class('block-company')) ?>">
							                    <a href="<?php echo Noo_Member::get_endpoint_url('block-company') ?>">
							                        <i class="fa fa-plus"></i> <?php _e('Block Companies','noo') ?>
							                    </a>
							                </li>
							        <?php endif; ?>

                                    <?php if (in_array('job-follow', $candidate_menu_values) or empty($candidate_menu_values)) : ?>
                                        <li class="menu-item">
                                            <a href="<?php echo Noo_Member::get_endpoint_url('job-follow') ?>">
                                                <i class="fa fa-plus"></i>
                                                <?php _e('Job Follow', 'noo') ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>
								<?php if(in_array('job-suggest',$candidate_menu_values)or empty($candidate_menu_values)): ?>
				                    <li class="menu-item">
				                        <a href="<?php echo Noo_Member::get_endpoint_url('job-suggest') ?>">
				                            <i class="fa fa-plus"></i> <?php _e('Job Suggest', 'noo') ?>
				                        </a>
				                    </li>
				                <?php endif; ?>
								<?php if ( in_array( 'bookmark-job', $candidate_menu_values ) or empty( $candidate_menu_values ) ) : ?>

									<?php if ( jm_is_enabled_job_bookmark() ): ?>
										<li class="menu-item">
											<a href="<?php echo Noo_Member::get_endpoint_url( 'bookmark-job' ) ?>">
												<i class="fa fa-heart"></i> <?php _e( 'Bookmarked', 'noo' ) ?>
											</a>
										</li>
									<?php endif; ?>
								<?php endif; ?>

                                <?php $can_shortlist_candidate = noo_can_shortlist_candidate() ?>
                                <?php if ($can_shortlist_candidate): ?>
                                    <?php if (in_array('shortlist', $candidate_menu_values) or empty($candidate_menu_values)) : ?>
                                        <li class="menu-item">
                                            <a href="<?php echo Noo_Member::get_endpoint_url('shortlist') ?>">
                                                <i class="fa fa-heart"></i> <?php _e('Shortlist', 'noo') ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>

								<?php if ( in_array( 'candidate_profile', $candidate_menu_values ) or empty( $candidate_menu_values ) ) : ?>
									<li class="menu-item">
										<a href="<?php echo Noo_Member::get_candidate_profile_url() ?>">
											<i class="fa fa-user"></i> <?php _e( 'My Profile', 'noo' ) ?>
										</a>
									</li>
								<?php endif; ?>

								<?php if ( in_array( 'signout', $candidate_menu_values ) or empty( $candidate_menu_values ) ) : ?>
									<li class="menu-item">
										<a href="<?php echo Noo_Member::get_logout_url() ?>">
											<i class="fas fa-sign-out-alt"></i> <?php _e( 'Sign Out', 'noo' ) ?>
										</a>
									</li>
								<?php endif; ?>

							<?php endif; ?>
						</ul>
					</div>

				<?php else: ?>

					<ul class="noo-topbar-login-link">
						<li>
							<a href="<?php echo Noo_Member::get_login_url(); ?>" class="member-links member-login-link">
								<i class="fas fa-sign-in-alt"></i>&nbsp;<?php _e( 'Login', 'noo' ) ?>
							</a>
						</li>
                    <?php if ( Noo_Member::can_register() ) : ?>
						<li>
							<a href="<?php echo Noo_Member::get_register_url(); ?>" class="member-links member-register-link">
								<i class="fas fa-key"></i>&nbsp;<?php _e( 'Register', 'noo' ) ?>
							</a>
						</li>
                    <?php endif; ?>
					</ul>

				<?php endif; ?>

			<?php endif; ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>