<?php
/*
Plugin Name: [BETA] JobMonster - Ultimate Member integration
Plugin URI: https://www.nootheme.com
Description: This plugin help integrates the chat function of FEP to your JobMonster theme
Version: 0.1.0
Author: NooTheme Team
Author URI: https://www.nootheme.com/
*/

// === << Check class exits

if ( !class_exists( 'JobMonster_UM_Integration' ) ) :

	class JobMonster_UM_Integration {

		function __construct() {
			if( !function_exists( 'is_plugin_active') ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			if(is_admin()){

				add_action('admin_init', array($this,'admin_init'));

				$plugin = plugin_basename( __FILE__ );
				add_filter( "plugin_action_links_$plugin", array( $this, 'plugin_add_settings_link' ) );

				if( !class_exists('Noo_Check_Version_Child') ) {
					require_once( 'includes/noo-check-version-child.php' );
				}
		 
	            $license_manager = new Noo_Check_Version_Child(
	                'jobmonster-um-integration',
	                'JobMonster - Ultimate Member Integration',
	                'noo-jobmonster',
	                'http://update.nootheme.com/api/license-manager/v1',
	                'plugin',
	                __FILE__
	            );
			}

			if( !is_plugin_active( 'ultimate-member/index.php' ) ) {
				// @TODO: add notice message
				return;
			}

			// -- Load script
			add_action( 'wp_enqueue_scripts', array( &$this, 'load_enqueue_script' ) );

			// -- Theme's hooks
			add_action( 'after_setup_theme', array( $this, 'init_after_theme' ) );
		}

		public function admin_init(){
			register_setting( 'jobmonster_um' , 'jobmonster_um' );
		}

		public function plugin_add_settings_link( $links ) {
			if( is_plugin_active( 'jobmonster-um-integration/jobmonster-um-integration.php' ) ) {
			    $settings_link = '<a href="' . admin_url( 'edit.php?post_type=noo_job&page=manage_noo_job&tab=jobmonster_um' ) . '">' . __( 'Settings' ) . '</a>';
			    array_unshift( $links, $settings_link );
			}
		  	return $links;
		}

		public function init_after_theme() {
			if( !class_exists( 'Noo_Member' ) ) {
				// @TODO: add notice message
				return;
			}

			if(is_admin()){
				add_filter('noo_job_settings_tabs_array', array( $this, 'add_seting_jobmonster_um_tab' ));
				add_action('noo_job_setting_jobmonster_um', array( $this, 'setting_page' ));
			}

			// -- JobMonster hooks
			// Endpoint
			add_filter('noo_get_endpoint_url', array( $this, 'member_endpoint' ), 10, 2);
			add_action('noo_new_user_registered', array( $this, 'new_user_registered' ), 10, 2);

			// Profile for Resume/Application
			if( self::get_setting('um_candidate_profile', false) ) {
				add_filter('noo_resume_candidate_link', array( $this, 'resume_candidate_link' ), 10, 3);
				add_filter('noo_application_candidate_link', array( $this, 'application_candidate_link' ), 10, 3);
			}

			// Company profile link
			if( self::get_setting('um_employer_profile', false) ) {
				if( self::get_setting('um_employer_profile_company', false) )
					add_filter('post_type_link', array( $this, 'company_post_link' ), 10, 2);
			}

			// -- UM hooks
			// Member Profile page
			add_filter('um_profile_tabs', array( $this, 'custom_um_tab' ), 1000 );
			add_action('um_profile_content_jobs_default', array( $this, 'employer_jobs_default' ));
			add_action('um_profile_content_resumes_default', array( $this, 'candidate_resumes_default' ));

			// Custom Fields
			add_filter('um_predefined_fields_hook', array( $this, 'predefined_fields' ));

			// Employer
			add_action('um_after_new_user_register', array( $this, 'um_after_edit_profile' ));
			add_action('um_after_user_updated', array( $this, 'um_after_edit_profile' ));
		}

		public function load_enqueue_script() {
			// wp_register_style( 'noo-um', plugin_dir_url( __FILE__ ) . 'assets/css/um.css' );
			// wp_enqueue_style( 'noo-um' );
		}

		public function member_endpoint( $url = '', $endpoint = '') {
			if( 'candidate-profile' == $endpoint && self::get_setting('um_candidate_profile', false) ) {
				if( $this->_is_candidate_form_used() ) {
					$url = um_user_profile_url();
				}
			}
			if( 'company-profile' == $endpoint && self::get_setting('um_employer_profile', false) ) {
				if( $this->_is_employer_form_used() ) {
					$url = um_user_profile_url();
				}
			}

			return $url;
		}

		public function new_user_registered( $user_id = 0, $role = '' ) {
			if( empty( $user_id ) || empty( $role ) ) {
				return;
			}

			if( $role == 'employer' && self::get_setting( 'um_employer_profile', false ) ) {
				$employer_role = self::get_setting( 'um_employer_role', '' );
				if( !empty( $employer_role ) ) {
					update_user_meta( $user_id, 'role', $employer_role );
				}
			} elseif( $role == 'candidate' && self::get_setting( 'um_candidate_profile', false ) ) {
				$candidate_role = self::get_setting( 'um_candidate_role', '' );
				if( !empty( $candidate_role ) ) {
					update_user_meta( $user_id, 'role', $candidate_role );
				}
			}
		}

		public function application_candidate_link( $link = '', $application_id = 0, $candidate_email = '' ) {
			if( self::get_setting( 'um_candidate_profile', false ) ) {
				$candidate = empty( $candidate_email ) ? false : get_user_by( 'email', $candidate_email );
				if( $candidate ) {
					$link = $this->_profile_url( $candidate->ID );
				}
			}

			return $link;
		}

		public function resume_candidate_link( $link = '', $resume_id = 0, $candidate_id = '' ) {
			if( self::get_setting( 'um_candidate_profile', false ) ) {
				$candidate = empty( $candidate_id ) ? false : get_user_by( 'id', $candidate_id );
				if( $candidate ) {
					$link = $this->_profile_url( $candidate->ID );
				}
			}

			return $link;
		}

		public function company_post_link( $link, $post = '' ) {
			if( is_object( $post ) && 'noo_company' == $post->post_type ) {
				$employer_id = Noo_Company::get_employer_id( $post->ID );
				if( !empty( $employer_id ) ) {
					$link = $this->_profile_url( $employer_id );
				}
			}

			return $link;
		}

		public function custom_um_tab( $tabs = array() ) {
			$user_id = um_profile_id();
			if( Noo_Member::is_candidate( $user_id ) ) {
				if( self::get_setting('um_candidate_profile', false) ) {
					if( self::get_setting('um_candidate_profile_hide_post', false) ) {
						unset( $tabs['posts'] );
					}
					if( self::get_setting('um_candidate_profile_hide_comment', false) ) {
						unset( $tabs['comments'] );
					}
					
					if( self::get_setting('um_candidate_profile_resume', false) ) {
						$tabs['resumes'] = array(
							'name' => 'Resume',
							'icon' => 'um-faicon-file-text',
							'custom' => true,
							'subnav' => array(),
							'subnav_default' => ''
						);
					}
				}
			} elseif( Noo_Member::is_employer( $user_id ) ) {
				if( self::get_setting('um_employer_profile', false) ) {
					if( self::get_setting('um_employer_profile_hide_post', false) ) {
						unset( $tabs['posts'] );
					}
					if( self::get_setting('um_employer_profile_hide_comment', false) ) {
						unset( $tabs['comments'] );
					}

					if( self::get_setting('um_employer_profile_job', false) ) {
						$tabs['jobs'] = array(
							'name' => 'Jobs',
							'icon' => 'um-faicon-file-text',
							'custom' => true,
							'subnav' => array(),
							'subnav_default' => ''
						);
					}
				}
			}

			return $tabs;
		}

		public function um_after_edit_profile( $user_id = '' ) {
			if( empty( $user_id ) ) return false;
			if( Noo_Member::is_employer( $user_id ) ) {
				$employer = get_userdata( $user_id );
				$company_id = get_user_meta( $user_id, 'employer_company' , true );

				$company_data = array(
					'post_title'     => $employer->display_name,
					'post_content'   => $employer->description,
					'post_type'      => 'noo_company',
					'comment_status' => 'closed',
					'post_status'	 => 'publish',
				);

				if(!empty($company_id)){
					$company_data['ID'] = $company_id;
					$company_id = wp_update_post($company_data);
				}else{
					$company_id = wp_insert_post($company_data);
				}

				if( !empty( $company_id ) || is_wp_error($company_id) ) {
					update_user_meta($user_id, 'employer_company', $company_id);
					update_post_meta($company_id, '_website', get_user_meta( $user_id, 'website', true ) );
					// update_post_meta($company_id, '_logo', get_user_meta( $user_id, 'company_logo', true ) );
					update_post_meta($company_id, '_googleplus', get_user_meta( $user_id, 'googleplus', true ) );
					update_post_meta($company_id, '_facebook', get_user_meta( $user_id, 'facebook', true ) );
					update_post_meta($company_id, '_twitter', get_user_meta( $user_id, 'twitter', true ) );
					update_post_meta($company_id, '_linkedin', get_user_meta( $user_id, 'linkedin', true ) );
					update_post_meta($company_id, '_instagram', get_user_meta( $user_id, 'instagram', true ) );

					$profile_photo = get_user_meta( $user_id, 'profile_photo', true );
					if ( um_profile('profile_photo') && !empty( $profile_photo ) ) {
						update_post_meta($company_id, '_logo', $profile_photo);
					}

					$cover_photo = get_user_meta( $user_id, 'cover_photo', true );
					if ( um_profile('cover_photo') && !empty( $cover_photo ) ) {
						update_post_meta($company_id, '_cover_image', $cover_photo);
					}
				}
			}

			if( Noo_Member::is_employer( $user_id ) || Noo_Member::is_candidate( $user_id ) ) {
				$profile_photo = get_user_meta( $user_id, 'profile_photo', true );
				if ( um_profile('profile_photo') && !empty( $profile_photo ) ) {
					update_post_meta($user_id, 'profile_image', $profile_photo);
				}
			}
		}

		public function add_seting_jobmonster_um_tab( $tabs ) {
			$tabs['jobmonster_um'] = __( 'Ultimate Member', 'noo' );
			return $tabs;
		}

		public function candidate_resumes_default( $args ) {
			$q_args = array(
				'post_type' => 'noo_resume',
				'author' => um_profile_id(),
				'post_status'=>array('publish')
			);
			$query = new WP_Query( $q_args );

			if( $query->post_count > 1 ) {
				Noo_Resume::loop_display( array( 'query' => $query ) );
			} elseif ( $query->post_count == 1 ) {
				Noo_Resume::display_detail( $query, true );
			} else {
				echo __('No Resume', 'noo');
			}			
		}

		public function employer_jobs_default( $args ) {
			$q_args = array(
				'post_type' => 'noo_job',
				'author' => um_profile_id(),
				'post_status'=>array('publish')
			);
			$query = new WP_Query( $q_args );

			if( $query->post_count > 1 ) {
				Noo_Job::loop_display( array( 'query' => $query ) );
			} else {
				echo __('No Job', 'noo');
			}
		}

		public function predefined_fields( $predefined_fields = array() ) {
			$jm_fields = array(

				'website' => array(
					'title' => __('[JM] Website','noo'),
					'metakey' => 'website',
					'type' => 'text',
					'label' => __('Website','noo'),
					'required' => 0,
					'public' => 1,
					'editable' => 1,
					'validate' => '',
					'url_target' => '_blank',
					'url_rel' => 'nofollow',
				),
				'current_job' => array(
					'title' => __('[JM] Current Job','noo'),
					'metakey' => 'current_job',
					'type' => 'text',
					'label' => __('Current Job','noo'),
					'required' => 0,
					'public' => 1,
					'editable' => 1,
					'validate' => '',
				),
				'current_company' => array(
					'title' => __('[JM] Current Company','noo'),
					'metakey' => 'current_company',
					'type' => 'text',
					'label' => __('Current Company','noo'),
					'required' => 0,
					'public' => 1,
					'editable' => 1,
					'validate' => '',
				),
				'birthday' => array(
					'title' => __('[JM] Birthday','noo'),
					'metakey' => 'birthday',
					'type' => 'date',
					'label' => __('Birthday','noo'),
					'required' => 0,
					'public' => 1,
					'editable' => 1,
					'pretty_format' => 1,
					'years' => 115,
					'years_x' => 'past',
					'icon' => 'um-faicon-calendar'
				),
				'address' => array(
					'title' => __('[JM] Address','noo'),
					'metakey' => 'address',
					'type' => 'text',
					'label' => __('Address','noo'),
					'required' => 0,
					'public' => 1,
					'editable' => 1,
					'validate' => '',
					'icon' => 'um-icon-ios-location',
				),
				'phone' => array(
					'title' => __('[JM] Phone','noo'),
					'metakey' => 'phone',
					'type' => 'text',
					'label' => __('Phone','noo'),
					'required' => 0,
					'public' => 1,
					'editable' => 1,
					'validate' => '',
					'icon' => 'um-faicon-phone',
				),
				'behance' => array(
					'title' => __('[JM] Behance','noo'),
					'metakey' => 'behance',
					'type' => 'url',
					'label' => __('Behance','noo'),
					'required' => 0,
					'public' => 1,
					'editable' => 1,
					'url_target' => '_blank',
					'url_rel' => 'nofollow',
					'validate' => '',
					'url_text' => 'Behance',
					'icon' => 'um-faicon-behance-square',
					'advanced' => 'social',
					'color' => '#1769ff',
				),
			);

			return array_merge( $predefined_fields, $jm_fields );
		}

		public static function get_setting($id = null ,$default = null){
			$settings = get_option('jobmonster_um');
			if(isset($settings[$id]))
				return $settings[$id];
			return $default;
		}

		public static function save_setting($id = null ,$value = null){
			if( empty( $id ) ) {
				return false;
			}

			$settings = get_option('jobmonster_um');
			if( empty( $settings ) ) {
				$settings = array();
			}
			$settings[$id] = $value;

			update_option('jobmonster_um', $settings );
		}

		public function setting_page() {
			global $ultimatemember;
			$um_page_id = $this->_um_page_id();
			
			$new_candidate_role = self::get_setting('um_candidate_role_create_new', null);
			if( $new_candidate_role ) {
				$new_role = $this->_new_um_role( 'candidate' );
				if( !empty( $new_role ) ) {
					$new_role = get_post_field( 'post_name', $new_role );
					self::save_setting( 'um_candidate_role', $new_role );
					self::save_setting( 'um_candidate_role_create_new', null );
					$candidate_form = $this->_get_candidate_form();
					update_post_meta( $candidate_form, '_um_profile_role', $new_role );
				}
			}

			$new_employer_role = self::get_setting('um_employer_role_create_new', null);
			if( isset( $_POST['um_employer_role_create_new'] ) && !empty( $_POST['um_employer_role_create_new'] ) ) {
				$new_role = $this->_new_um_role( 'employer' );
				if( !empty( $new_role ) ) {
					$new_role = get_post_field( 'post_name', $new_role );
					self::save_setting( 'um_employer_role', $new_role );
					self::save_setting( 'um_employer_role_create_new', null );
					$employer_form = $this->_get_employer_form();
					update_post_meta( $employer_form, '_um_profile_role', $new_role );
				}
			}
			?>
				<?php settings_fields('jobmonster_um'); ?>
				<h2><?php echo __('Ultimate Member Settings','noo')?></h2>
				<h3><?php echo __('For Candidate','noo')?></h3>
				<?php if( self::get_setting('um_candidate_profile', false) ) :
					$um_candidate_role = self::get_setting('um_candidate_role', '');
					if( empty( $um_candidate_role ) ) :
					?>
						<div class="update-nag notice">
							<p><strong>Note:</strong> Please select a Comunity role for Candidates.</p>
						</div>
					<?php elseif( $this->_is_candidate_form_used() === false ) :
						$candidate_form = $this->_get_candidate_form();
						$user_page_link = get_edit_post_link( $um_page_id );
						$form_link = get_edit_post_link( $candidate_form );
					?>
						<div class="update-nag notice">
							<p><strong>Note:</strong> We created a <a href="<?php echo $form_link; ?>" target="_blank">UM profile form</a> for the Candidates. Please add below shortcode to the <a href="<?php echo $user_page_link; ?>" target="_blank">Ultimate Member's user page</a>.</p>
							<code>[ultimatemember form_id=<?php echo $candidate_form; ?>]</code>
						</div>
					<?php endif; ?>
				<?php endif; ?>
				<table class="form-table" cellspacing="0">
					<tbody>
						<script type="text/javascript">
							jQuery(document).ready(function($) {
								$('#um_candidate_profile').change(function(event) {
									var $input = $( this );
									if ( $input.prop( "checked" ) ) {
										$('.um_candidate_profile').show().find(':input').change();
									} else {
										$('.um_candidate_profile').hide().find(':input').change();
									}
								}).change();
							});
						</script>
						<tr>
							<th>
								<?php esc_html_e('Enable Candidate profile on Ultimate Member','noo')?>
							</th>
							<td>
								<?php $um_candidate_profile = self::get_setting('um_candidate_profile', false); ?>
								<input type="checkbox" <?php checked( true, $um_candidate_profile ); ?> id="um_candidate_profile" name="jobmonster_um[um_candidate_profile]" value="1" />
								<p><?php _e( 'Candidates will use UM User page as the profile page. Candidate Edit profile link will also be redirected to UM page.', 'noo' ); ?></p>
							</td>
						</tr>
						<tr class="um_candidate_profile">
							<th>
								<?php esc_html_e('Candidate Community role','noo')?>
							</th>
							<td>
								<?php $um_candidate_role = self::get_setting('um_candidate_role', ''); ?>
								<p>
									<select name="jobmonster_um[um_candidate_role]" id="um_candidate_role">
										<?php foreach($ultimatemember->query->get_roles( $add_default = 'Select a role' ) as $key => $value) { ?>
										<option value="<?php echo $key; ?>" <?php selected($key, $um_candidate_role ); ?>><?php echo $value; ?></option>
										<?php } ?>
									</select>
									<?php _e('or','noo'); ?>
									<input type="checkbox" name="jobmonster_um[um_candidate_role_create_new]" value="1" />
									<?php _e('create a new role','noo'); ?>
								</p>
								<p><?php _e('You will need to incoporate Candidate with one of UM\'s roles. Please create one if you don\'t have any.', 'noo'); ?></p>
							</td>
						</tr>

						<tr class="um_candidate_profile">
							<th>
								<?php esc_html_e('Show Resume tab','noo')?>
							</th>
							<td>
								<?php $um_candidate_profile_resume = self::get_setting('um_candidate_profile_resume', false); ?>
								<input type="checkbox" <?php checked( true, $um_candidate_profile_resume ); ?> id="um_candidate_profile_resume" name="jobmonster_um[um_candidate_profile_resume]" value="1" />
								<p><?php _e('Show a tab for resume on the Candidate profile page.', 'noo'); ?></p>
							</td>
						</tr>

						<tr class="um_candidate_profile">
							<th>
								<?php esc_html_e('Hide Posts Tab','noo')?>
							</th>
							<td>
								<?php $um_candidate_profile_hide_post = self::get_setting('um_candidate_profile_hide_post', false); ?>
								<input type="checkbox" <?php checked( true, $um_candidate_profile_hide_post ); ?> id="um_candidate_profile_hide_post" name="jobmonster_um[um_candidate_profile_hide_post]" value="1" />
								<p><?php _e('Hide Posts tab for Candidates.', 'noo'); ?></p>
							</td>
						</tr>

						<tr class="um_candidate_profile">
							<th>
								<?php esc_html_e('Hide Comments Tab','noo')?>
							</th>
							<td>
								<?php $um_candidate_profile_hide_comment = self::get_setting('um_candidate_profile_hide_comment', false); ?>
								<input type="checkbox" <?php checked( true, $um_candidate_profile_hide_comment ); ?> id="um_candidate_profile_hide_comment" name="jobmonster_um[um_candidate_profile_hide_comment]" value="1" />
								<p><?php _e('Hide Comments tab for Candidates.', 'noo'); ?></p>
							</td>
						</tr>

					</tbody>
				</table>
				<hr/>
				<h3><?php echo __('For Employer/Company','noo')?></h3>
				<?php if( self::get_setting('um_employer_profile', false) ) :
					$um_employer_role = self::get_setting('um_employer_role', '');
					if( empty( $um_employer_role ) ) :
					?>
						<div class="update-nag notice">
							<p><strong>Note:</strong> Please select a Comunity role for Employers.</p>
						</div>
					<?php elseif( $this->_is_employer_form_used() === false ) :
						$employer_form = $this->_get_employer_form();
						$user_page_link = get_edit_post_link( $um_page_id );
						$form_link = get_edit_post_link( $employer_form );
					?>
						<div class="update-nag notice">
							<p><strong>Note:</strong> We created a <a href="<?php echo $form_link; ?>" target="_blank">UM profile form</a> for the Employers. Please add below shortcode to the <a href="<?php echo $user_page_link; ?>" target="_blank">Ultimate Member's user page</a>.</p>
							<code>[ultimatemember form_id=<?php echo $employer_form; ?>]</code>
						</div>
					<?php endif; ?>
				<?php endif; ?>
				<table class="form-table" cellspacing="0">
					<tbody>
						<script type="text/javascript">
							jQuery(document).ready(function($) {
								$('#um_employer_profile').change(function(event) {
									var $input = $( this );
									if ( $input.prop( "checked" ) ) {
										$('.um_employer_profile').show().find(':input').change();
									} else {
										$('.um_employer_profile').hide().find(':input').change();
									}
								}).change();
							});
						</script>
						<tr>
							<th>
								<?php esc_html_e('Enable Employer profile on Ultimate Member','noo')?>
							</th>
							<td>
								<?php $um_employer_profile = self::get_setting('um_employer_profile', false); ?>
								<input type="checkbox" <?php checked( true, $um_employer_profile ); ?> id="um_employer_profile" name="jobmonster_um[um_employer_profile]" value="1" />
								<p><?php _e( 'Employers will use UM User page as the profile page.', 'noo' ); ?></p>
							</td>
						</tr>
						<tr class="um_employer_profile">
							<th>
								<?php esc_html_e('Employer Community role','noo')?>
							</th>
							<td>
								<?php $um_employer_role = self::get_setting('um_employer_role', ''); ?>
								<p>
									<select name="jobmonster_um[um_employer_role]" id="um_employer_role">
										<?php foreach($ultimatemember->query->get_roles( $add_default = 'Select a role' ) as $key => $value) { ?>
										<option value="<?php echo $key; ?>" <?php selected($key, $um_employer_role ); ?>><?php echo $value; ?></option>
										<?php } ?>
									</select>
									<?php _e('or','noo'); ?>
									<input type="checkbox" name="jobmonster_um[um_employer_role_create_new]" value="1" />
									<?php _e('create a new role','noo'); ?>
								</p>
								<p><?php _e('You will need to incoporate Employer with one of UM\'s roles. Please create one if you don\'t have any.', 'noo'); ?></p>
							</td>
						</tr>

						<tr class="um_employer_profile">
							<th>
								<?php esc_html_e('Show Jobs tab','noo')?>
							</th>
							<td>
								<?php $um_employer_profile_job = self::get_setting('um_employer_profile_job', false); ?>
								<input type="checkbox" <?php checked( true, $um_employer_profile_job ); ?> id="um_employer_profile_job" name="jobmonster_um[um_employer_profile_job]" value="1" />
								<p><?php _e('Show a tab for jobs on the Employer profile page.', 'noo'); ?></p>
							</td>
						</tr>

						<tr class="um_employer_profile">
							<th>
								<?php esc_html_e('Hide Posts Tab','noo')?>
							</th>
							<td>
								<?php $um_employer_profile_hide_post = self::get_setting('um_employer_profile_hide_post', false); ?>
								<input type="checkbox" <?php checked( true, $um_employer_profile_hide_post ); ?> id="um_employer_profile_hide_post" name="jobmonster_um[um_employer_profile_hide_post]" value="1" />
								<p><?php _e('Hide Posts tab for Employers.', 'noo'); ?></p>
							</td>
						</tr>

						<tr class="um_employer_profile">
							<th>
								<?php esc_html_e('Hide Comments Tab','noo')?>
							</th>
							<td>
								<?php $um_employer_profile_hide_comment = self::get_setting('um_employer_profile_hide_comment', false); ?>
								<input type="checkbox" <?php checked( true, $um_employer_profile_hide_comment ); ?> id="um_employer_profile_hide_comment" name="jobmonster_um[um_employer_profile_hide_comment]" value="1" />
								<p><?php _e('Hide Comments tab for Employers.', 'noo'); ?></p>
							</td>
						</tr>

						<tr class="um_employer_profile">
							<th>
								<?php esc_html_e('Use Employer profile for Company link','noo')?>
							</th>
							<td>
								<?php $um_employer_profile_company = self::get_setting('um_employer_profile_company', false); ?>
								<input type="checkbox" <?php checked( true, $um_employer_profile_company ); ?> id="um_employer_profile_company" name="jobmonster_um[um_employer_profile_company]" value="1" />
								<p><?php _e('Enable this option will redirect Company links to the Ultimate Member page', 'noo'); ?></p>
							</td>
						</tr>

					</tbody>
				</table>
			<?php 
		}

		private function _profile_url( $user_id = '' ) {
			if( empty( $user_id ) ) return um_user_profile_url();

			$um_user = new UM_User();
			$um_user->set( $user_id );
			$userinfo = $um_user->profile;

			$um_page_id = $this->_um_page_id();

			$profile_url = get_permalink( $um_page_id );
			
			if ( function_exists('icl_get_current_language') && icl_get_current_language() != icl_get_default_language() ) {
				if ( get_the_ID() > 0 && get_post_meta( get_the_ID(), '_um_wpml_user', true ) == 1 ) {
					$profile_url = get_permalink( get_the_ID() );
				}
			}
			
			if ( um_get_option('permalink_base') == 'user_login' ) {
				$user_in_url = $userinfo['user_login'];
				
				if ( is_email($user_in_url) ) {
					$user_in_url = str_replace('@','',$user_in_url);
					if( ( $pos = strrpos( $user_in_url , '.' ) ) !== false ) {
						$search_length  = strlen( '.' );
						$user_in_url    = substr_replace( $user_in_url , '-' , $pos , $search_length );
					}
				} else {
					
					$user_in_url = sanitize_title( $user_in_url );
				}
			}
			
			if ( um_get_option('permalink_base') == 'user_id' ) {
				$user_in_url = $user_id;
			}
			
			if ( um_get_option('permalink_base') == 'name' ) {
				$user_in_url = rawurlencode( strtolower( $userinfo['full_name'] ) );
			}
			
			if ( get_option('permalink_structure') ) {
			
				$profile_url = trailingslashit( untrailingslashit( $profile_url ) );
				$profile_url = $profile_url . $user_in_url . '/';
			
			} else {
				
				$profile_url =  add_query_arg( 'um_user', $user_in_url, $profile_url );
				
			}

			return $profile_url;
		}

		private function _um_page_id( ) {
			$um_pages = get_option('um_core_pages');
			return isset( $um_pages['user'] ) ? $um_pages['user'] : '';
		}

		private function _new_um_role( $wp_role = 'candidate' ) {
			if ( current_user_can('manage_options') && um_user('ID') ){
				
				$um_role = array(
					'post_title'		=> ucfirst( $wp_role ),
					'post_name'			=> $wp_role,
					'post_type' 	  	=> 'um_role',
					'post_status'		=> 'publish',
					'post_author'   	=> um_user('ID'),
				);
				
				$post_id = wp_insert_post( $um_role );
				if( is_wp_error( $post_id ) ) return false;

				$um_perms = array(
					'can_access_wpadmin' => 0,
					'can_not_see_adminbar' => 1,
					'can_edit_everyone' => 0,
					'can_delete_everyone' => 0,
					'can_edit_profile' => 1,
					'can_delete_profile' => 0,
					'can_make_private_profile' => 0,
					'can_access_private_profile' => 0,
					'after_login' => 'redirect_profile',
					'synced_role' => $wp_role
				);
				
				foreach( $um_perms as $key => $value ) {
					update_post_meta($post_id, "_um_" . $key, $value);
				}

				return $post_id;
			}

			return false;
		}

		private function _is_candidate_form_used() {
			$um_page_id = $this->_um_page_id();
			if( self::get_setting('um_candidate_profile', false) && !empty( $um_page_id ) ) {
				$candidate_form = $this->_get_candidate_form();
				$user_page = get_post( $um_page_id );
				if( !empty( $candidate_form ) && $user_page ) {
					$checking_str = 'form_id=' . $candidate_form;
					$page_content = $user_page->post_content;

					return false !== strpos( $page_content, $checking_str );
				}
			}

			return null;
		}

		private function _is_employer_form_used() {
			$um_pages = get_option('um_core_pages');
			$um_page_id = isset( $um_pages['user'] ) ? $um_pages['user'] : '';
			if( self::get_setting('um_employer_profile', false) && !empty( $um_page_id ) ) {
				$employer_form = $this->_get_employer_form();
				$user_page = get_post( $um_page_id );
				if( !empty( $employer_form ) && $user_page ) {
					$checking_str = 'form_id=' . $employer_form;
					$page_content = $user_page->post_content;

					return false !== strpos( $page_content, $checking_str );
				}
			}

			return null;
		}

		private function _get_candidate_form() {
			$um_candidate_form = get_option( 'jm_um_candidate_form' );
			if( empty( $um_candidate_form ) ) {
				// add new form
				//'_um_profile_role'
				$form = array(
					'post_type' 	  	=> 'um_form',
					'post_title'		=> 'Candidate Profile',
					'post_status'		=> 'publish',
				);

				$role = self::get_setting( 'um_candidate_role' );
				if( empty( $role ) ) {
					return '';
				}

				$form_id = wp_insert_post( $form );
				if( !is_wp_error( $form_id ) ) {
					$form_meta = array(
						'_um_custom_fields' => 'a:14:{s:13:"user_password";a:16:{s:5:"title";s:8:"Password";s:7:"metakey";s:13:"user_password";s:4:"type";s:8:"password";s:5:"label";s:8:"Password";s:8:"required";i:1;s:6:"public";i:1;s:8:"editable";i:1;s:9:"min_chars";i:8;s:9:"max_chars";i:30;s:15:"force_good_pass";i:1;s:18:"force_confirm_pass";i:1;s:8:"position";s:2:"13";s:6:"in_row";s:9:"_um_row_2";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:8:"in_group";s:0:"";}s:8:"facebook";a:20:{s:5:"title";s:8:"Facebook";s:7:"metakey";s:8:"facebook";s:4:"type";s:3:"url";s:5:"label";s:8:"Facebook";s:8:"required";i:0;s:6:"public";i:1;s:8:"editable";i:1;s:10:"url_target";s:6:"_blank";s:7:"url_rel";s:8:"nofollow";s:4:"icon";s:18:"um-faicon-facebook";s:8:"validate";s:12:"facebook_url";s:8:"url_text";s:8:"Facebook";s:8:"advanced";s:6:"social";s:5:"color";s:7:"#3B5999";s:5:"match";s:21:"https://facebook.com/";s:8:"position";s:1:"8";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:8:"in_group";s:0:"";}s:7:"twitter";a:20:{s:5:"title";s:7:"Twitter";s:7:"metakey";s:7:"twitter";s:4:"type";s:3:"url";s:5:"label";s:7:"Twitter";s:8:"required";i:0;s:6:"public";i:1;s:8:"editable";i:1;s:10:"url_target";s:6:"_blank";s:7:"url_rel";s:8:"nofollow";s:4:"icon";s:17:"um-faicon-twitter";s:8:"validate";s:11:"twitter_url";s:8:"url_text";s:7:"Twitter";s:8:"advanced";s:6:"social";s:5:"color";s:7:"#4099FF";s:5:"match";s:20:"https://twitter.com/";s:8:"position";s:1:"9";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:8:"in_group";s:0:"";}s:8:"linkedin";a:20:{s:5:"title";s:8:"LinkedIn";s:7:"metakey";s:8:"linkedin";s:4:"type";s:3:"url";s:5:"label";s:8:"LinkedIn";s:8:"required";i:0;s:6:"public";i:1;s:8:"editable";i:1;s:10:"url_target";s:6:"_blank";s:7:"url_rel";s:8:"nofollow";s:4:"icon";s:18:"um-faicon-linkedin";s:8:"validate";s:12:"linkedin_url";s:8:"url_text";s:8:"LinkedIn";s:8:"advanced";s:6:"social";s:5:"color";s:7:"#0976b4";s:5:"match";s:24:"https://linkedin.com/in/";s:8:"position";s:2:"10";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:8:"in_group";s:0:"";}s:9:"instagram";a:20:{s:5:"title";s:9:"Instagram";s:7:"metakey";s:9:"instagram";s:4:"type";s:3:"url";s:5:"label";s:9:"Instagram";s:8:"required";i:0;s:6:"public";i:1;s:8:"editable";i:1;s:10:"url_target";s:6:"_blank";s:7:"url_rel";s:8:"nofollow";s:4:"icon";s:19:"um-faicon-instagram";s:8:"validate";s:13:"instagram_url";s:8:"url_text";s:9:"Instagram";s:8:"advanced";s:6:"social";s:5:"color";s:7:"#3f729b";s:5:"match";s:22:"https://instagram.com/";s:8:"position";s:2:"11";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:8:"in_group";s:0:"";}s:12:"display_name";a:14:{s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:4:"type";s:4:"text";s:7:"metakey";s:12:"display_name";s:8:"position";s:1:"1";s:5:"title";s:12:"Display Name";s:10:"visibility";s:3:"all";s:5:"label";s:9:"Full Name";s:6:"public";s:1:"1";s:8:"required";s:1:"0";s:8:"editable";s:1:"1";s:17:"conditional_value";s:1:"0";s:8:"in_group";s:0:"";}s:10:"user_email";a:15:{s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:4:"type";s:4:"text";s:7:"metakey";s:10:"user_email";s:8:"position";s:1:"2";s:5:"title";s:14:"E-mail Address";s:10:"visibility";s:3:"all";s:5:"label";s:6:"E-mail";s:6:"public";s:1:"1";s:8:"validate";s:12:"unique_email";s:8:"required";s:1:"0";s:8:"editable";s:1:"1";s:17:"conditional_value";s:1:"0";s:8:"in_group";s:0:"";}s:11:"current_job";a:13:{s:5:"title";s:16:"[JM] Current Job";s:7:"metakey";s:11:"current_job";s:4:"type";s:4:"text";s:5:"label";s:11:"Current Job";s:8:"required";i:0;s:6:"public";i:1;s:8:"editable";i:1;s:8:"validate";s:0:"";s:8:"position";s:1:"3";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:8:"in_group";s:0:"";}s:15:"current_company";a:13:{s:5:"title";s:20:"[JM] Current Company";s:7:"metakey";s:15:"current_company";s:4:"type";s:4:"text";s:5:"label";s:15:"Current Company";s:8:"required";i:0;s:6:"public";i:1;s:8:"editable";i:1;s:8:"validate";s:0:"";s:8:"position";s:1:"4";s:8:"in_group";s:0:"";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";}s:7:"address";a:14:{s:5:"title";s:12:"[JM] Address";s:7:"metakey";s:7:"address";s:4:"type";s:4:"text";s:5:"label";s:7:"Address";s:8:"required";i:0;s:6:"public";i:1;s:8:"editable";i:1;s:8:"validate";s:0:"";s:4:"icon";s:20:"um-icon-ios-location";s:8:"position";s:1:"5";s:8:"in_group";s:0:"";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";}s:5:"phone";a:14:{s:5:"title";s:10:"[JM] Phone";s:7:"metakey";s:5:"phone";s:4:"type";s:4:"text";s:5:"label";s:5:"Phone";s:8:"required";i:0;s:6:"public";i:1;s:8:"editable";i:1;s:8:"validate";s:0:"";s:4:"icon";s:15:"um-faicon-phone";s:8:"position";s:1:"7";s:8:"in_group";s:0:"";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";}s:8:"birthday";a:16:{s:5:"title";s:13:"[JM] Birthday";s:7:"metakey";s:8:"birthday";s:4:"type";s:4:"date";s:5:"label";s:8:"Birthday";s:8:"required";i:0;s:6:"public";i:1;s:8:"editable";i:1;s:13:"pretty_format";i:1;s:5:"years";i:115;s:7:"years_x";s:4:"past";s:4:"icon";s:18:"um-faicon-calendar";s:8:"position";s:1:"6";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:8:"in_group";s:0:"";}s:7:"behance";a:19:{s:5:"title";s:12:"[JM] Behance";s:7:"metakey";s:7:"behance";s:4:"type";s:3:"url";s:5:"label";s:5:"Phone";s:8:"required";i:0;s:6:"public";i:1;s:8:"editable";i:1;s:10:"url_target";s:6:"_blank";s:7:"url_rel";s:8:"nofollow";s:8:"validate";s:0:"";s:8:"url_text";s:7:"Behance";s:4:"icon";s:24:"um-faicon-behance-square";s:8:"advanced";s:6:"social";s:5:"color";s:7:"#1769ff";s:8:"position";s:2:"12";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:8:"in_group";s:0:"";}s:9:"_um_row_1";a:5:{s:4:"type";s:3:"row";s:2:"id";s:9:"_um_row_1";s:8:"sub_rows";s:1:"1";s:4:"cols";s:1:"1";s:6:"origin";s:9:"_um_row_1";}}',
						'_um_mode' => 'profile',
						'_um_core' => 'profile',
						'_um_profile_use_globals' => 0,
						'_um_profile_role' => $role
					);
					foreach( $form_meta as $key => $value ) {
						if ( $key == '_um_custom_fields' ) {
							$array = unserialize( $value );
							update_post_meta( $form_id, $key, $array );
						} else {
							update_post_meta($form_id, $key, $value);
						}
					}

					$um_candidate_form = $form_id;
					update_option( 'jm_um_candidate_form', $um_candidate_form );
				}
			}

			return $um_candidate_form;
		}

		private function _get_employer_form() {
			$um_emmployer_form = get_option( 'jm_um_emmployer_form' );
			if( empty( $um_emmployer_form ) ) {
				// add new form
				//'_um_profile_role'
				$form = array(
					'post_type' 	  	=> 'um_form',
					'post_title'		=> 'Employer Profile',
					'post_status'		=> 'publish',
				);

				$role = self::get_setting( 'um_employer_role' );
				if( empty( $role ) ) {
					return '';
				}

				$form_id = wp_insert_post( $form );
				if( !is_wp_error( $form_id ) ) {
					$form_meta = array(
						'_um_custom_fields' => 'a:8:{s:8:"facebook";a:20:{s:5:"title";s:8:"Facebook";s:7:"metakey";s:8:"facebook";s:4:"type";s:3:"url";s:5:"label";s:8:"Facebook";s:8:"required";i:0;s:6:"public";i:1;s:8:"editable";i:1;s:10:"url_target";s:6:"_blank";s:7:"url_rel";s:8:"nofollow";s:4:"icon";s:18:"um-faicon-facebook";s:8:"validate";s:12:"facebook_url";s:8:"url_text";s:8:"Facebook";s:8:"advanced";s:6:"social";s:5:"color";s:7:"#3B5999";s:5:"match";s:21:"https://facebook.com/";s:8:"position";s:1:"3";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:8:"in_group";s:0:"";}s:7:"twitter";a:20:{s:5:"title";s:7:"Twitter";s:7:"metakey";s:7:"twitter";s:4:"type";s:3:"url";s:5:"label";s:7:"Twitter";s:8:"required";i:0;s:6:"public";i:1;s:8:"editable";i:1;s:10:"url_target";s:6:"_blank";s:7:"url_rel";s:8:"nofollow";s:4:"icon";s:17:"um-faicon-twitter";s:8:"validate";s:11:"twitter_url";s:8:"url_text";s:7:"Twitter";s:8:"advanced";s:6:"social";s:5:"color";s:7:"#4099FF";s:5:"match";s:20:"https://twitter.com/";s:8:"position";s:1:"4";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:8:"in_group";s:0:"";}s:8:"linkedin";a:20:{s:5:"title";s:8:"LinkedIn";s:7:"metakey";s:8:"linkedin";s:4:"type";s:3:"url";s:5:"label";s:8:"LinkedIn";s:8:"required";i:0;s:6:"public";i:1;s:8:"editable";i:1;s:10:"url_target";s:6:"_blank";s:7:"url_rel";s:8:"nofollow";s:4:"icon";s:18:"um-faicon-linkedin";s:8:"validate";s:12:"linkedin_url";s:8:"url_text";s:8:"LinkedIn";s:8:"advanced";s:6:"social";s:5:"color";s:7:"#0976b4";s:5:"match";s:24:"https://linkedin.com/in/";s:8:"position";s:1:"5";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:8:"in_group";s:0:"";}s:9:"instagram";a:20:{s:5:"title";s:9:"Instagram";s:7:"metakey";s:9:"instagram";s:4:"type";s:3:"url";s:5:"label";s:9:"Instagram";s:8:"required";i:0;s:6:"public";i:1;s:8:"editable";i:1;s:10:"url_target";s:6:"_blank";s:7:"url_rel";s:8:"nofollow";s:4:"icon";s:19:"um-faicon-instagram";s:8:"validate";s:13:"instagram_url";s:8:"url_text";s:9:"Instagram";s:8:"advanced";s:6:"social";s:5:"color";s:7:"#3f729b";s:5:"match";s:22:"https://instagram.com/";s:8:"position";s:1:"6";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:8:"in_group";s:0:"";}s:12:"display_name";a:14:{s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:4:"type";s:4:"text";s:7:"metakey";s:12:"display_name";s:8:"position";s:1:"1";s:5:"title";s:12:"Display Name";s:10:"visibility";s:3:"all";s:5:"label";s:9:"Full Name";s:6:"public";s:1:"1";s:8:"required";s:1:"0";s:8:"editable";s:1:"1";s:17:"conditional_value";s:1:"0";s:8:"in_group";s:0:"";}s:10:"googleplus";a:20:{s:5:"title";s:7:"Google+";s:7:"metakey";s:10:"googleplus";s:4:"type";s:3:"url";s:5:"label";s:7:"Google+";s:8:"required";i:0;s:6:"public";i:1;s:8:"editable";i:1;s:10:"url_target";s:6:"_blank";s:7:"url_rel";s:8:"nofollow";s:4:"icon";s:21:"um-faicon-google-plus";s:8:"validate";s:10:"google_url";s:8:"url_text";s:7:"Google+";s:8:"advanced";s:6:"social";s:5:"color";s:7:"#dd4b39";s:5:"match";s:20:"https://google.com/+";s:8:"position";s:1:"7";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:8:"in_group";s:0:"";}s:7:"website";a:15:{s:5:"title";s:12:"[JM] Website";s:7:"metakey";s:7:"website";s:4:"type";s:4:"text";s:5:"label";s:7:"Website";s:8:"required";i:0;s:6:"public";i:1;s:8:"editable";i:1;s:8:"validate";s:0:"";s:10:"url_target";s:6:"_blank";s:7:"url_rel";s:8:"nofollow";s:8:"position";s:1:"2";s:6:"in_row";s:9:"_um_row_1";s:10:"in_sub_row";s:1:"0";s:9:"in_column";s:1:"1";s:8:"in_group";s:0:"";}s:9:"_um_row_1";a:5:{s:4:"type";s:3:"row";s:2:"id";s:9:"_um_row_1";s:8:"sub_rows";s:1:"1";s:4:"cols";s:1:"1";s:6:"origin";s:9:"_um_row_1";}}',
						'_um_mode' => 'profile',
						'_um_core' => 'profile',
						'_um_profile_use_globals' => 0,
						'_um_profile_role' => $role
					);
					foreach( $form_meta as $key => $value ) {
						if ( $key == '_um_custom_fields' ) {
							$array = unserialize( $value );
							update_post_meta( $form_id, $key, $array );
						} else {
							update_post_meta($form_id, $key, $value);
						}
					}

					$um_emmployer_form = $form_id;
					update_option( 'jm_um_emmployer_form', $um_emmployer_form );
				}
			}

			return $um_emmployer_form;
		}

		private function _get_user_role( $user_id = 0 ) {
			$user_role = '';
			$user = get_userdata( $user_id );

			if( $user ) {
				if(!function_exists('get_editable_roles'))
					include_once( ABSPATH . 'wp-admin/includes/user.php' );
				$editable_roles = array_keys( get_editable_roles() );
				if ( count( $user->roles ) <= 1 ) {
					$user_role = reset( $user->roles );
				} elseif ( $roles = array_intersect( array_values( $user->roles ), $editable_roles ) ) {
					$user_role = reset( $roles );
				} else {
					$user_role = reset( $user->roles );
				}
			}

			if( $user_role == 'administrator' ) {
				$user_role = 'employer';
			}

			return $user_role;
		}

		private function _is_administrator( $user_id = 0 ) {
			if( empty( $user_id ) ) {
				return current_user_can( 'manage_options' );
			} else {
				return user_can( $user_id, 'manage_options' );
			}
		}
	}
	new JobMonster_UM_Integration();
endif;