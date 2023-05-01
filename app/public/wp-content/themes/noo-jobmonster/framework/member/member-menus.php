<?php

if ( ! function_exists( 'jm_member_menu_admin_init' ) ) :
	function jm_member_menu_admin_init() {
		register_setting( 'jm_member_menu', 'jm_member_menu' );
	}

	add_filter( 'admin_init', 'jm_member_menu_admin_init' );
endif;

if ( ! function_exists( 'jm_get_member_menu' ) ) :
	function jm_get_member_menu( $action = '', $default = '' ) {

		return jm_get_setting( 'jm_member_menu', $action, $default );
	}
endif;

if ( ! function_exists( 'jm_member_menu_settings_tabs' ) ) :
	function jm_member_menu_settings_tabs( $tabs = array() ) {

		$temp1 = array_slice($tabs, 0, 9);
		$temp2 = array_slice($tabs, 4);

		$member_tab = array( 'member_menu' => __('Member Menu','noo') );
		return array_merge($temp1, $member_tab, $temp2);
	}

	add_filter( 'noo_job_settings_tabs_array', 'jm_member_menu_settings_tabs', 99 );
endif;

if ( ! function_exists( 'jm_member_menu_setting_form' ) ) :
	function jm_member_menu_setting_form() {
		jm_member_menu_correct_settings();

		$other_menus = jm_member_menu_other_items();

		$employer_menus = jm_member_menu_employer_items();
		$employer_menus = array_merge( $employer_menus, $other_menus );

		$candidate_menus = jm_member_menu_candidate_items();
		$candidate_menus = array_merge( $candidate_menus, $other_menus );

		$employer_menu_values    = jm_get_member_menu( 'employer_menu', array() );
		$employer_heading_values = jm_get_member_menu( 'employer_heading', array() );

		$candidate_menu_values    = jm_get_member_menu( 'candidate_menu', array() );
		$candidate_heading_values = jm_get_member_menu( 'candidate_heading', array() );
		$hide_post_job 			  = jm_get_member_menu( 'hide_post_job', '' );
		$hide_post_resume 		  = jm_get_member_menu( 'hide_post_resume', '' );

		?>
		<?php settings_fields( 'jm_member_menu' ); ?>
		<h3><?php echo __( 'Member Menu', 'noo' ); ?></h3>
		<p><?php echo __( 'This page allows you to set up the Member Dashboard Menu and Sub Menu.', 'noo' ); ?></p>
		<table class="form-table" cellspacing="0">
			<tbody>
			<tr>
				<th>
					<?php echo esc_html__( 'Hide Post Job', 'noo' ); ?>
				</th>
				<td>
					<input class="post-a-job" type="checkbox" <?php checked($hide_post_job, 1); ?> name="jm_member_menu[hide_post_job]" value="1">
				</td>
			</tr>
			<tr>
				<th>
					<?php echo __( 'Employer Sub Menu', 'noo' ); ?>
				</th>
				<td>
					<select class="noo-admin-chosen" name="jm_member_menu[employer_menu][]" multiple="multiple"
					        style="width: 500px;max-width: 100%;">
						<?php foreach ( $employer_menus as $key => $text ) : ?>
							<option <?php selected( in_array( $key, $employer_menu_values ), true ); ?>
								value="<?php echo $key; ?>"><?php echo $text; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th>
					<?php echo __( 'Employer Heading Menu', 'noo' ); ?>
				</th>
				<td>
					<select class="noo-admin-chosen" name="jm_member_menu[employer_heading][]" multiple="multiple"
					        style="width: 500px;max-width: 100%;">
						<?php foreach ( $employer_menus as $key => $text ) : ?>
							<option <?php selected( in_array( $key, $employer_heading_values ), true ); ?>
								value="<?php echo $key; ?>"><?php echo $text; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th>
					<?php echo esc_html__( 'Hide Post Resume', 'noo' ); ?>
				</th>
				<td>
					<input class="post-a-resume" type="checkbox" <?php checked($hide_post_resume, 1); ?> name="jm_member_menu[hide_post_resume]" value="1">
				</td>
			</tr>
			<tr>
				<th>
					<?php echo __( 'Candidate Sub Menu', 'noo' ); ?>
				</th>
				<td>
					<select class="noo-admin-chosen" name="jm_member_menu[candidate_menu][]" multiple="multiple"
					        style="width: 500px;max-width: 100%;">
						<?php foreach ( $candidate_menus as $key => $text ) : ?>
							<option <?php selected( in_array( $key, $candidate_menu_values ), true ); ?>
								value="<?php echo $key; ?>"><?php echo $text; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th>
					<?php echo __( 'Candidate Heading Menu', 'noo' ); ?>
				</th>
				<td>
					<select class="noo-admin-chosen" name="jm_member_menu[candidate_heading][]" multiple="multiple"
					        style="width: 500px;max-width: 100%;">
						<?php foreach ( $candidate_menus as $key => $text ) : ?>
							<option <?php selected( in_array( $key, $candidate_heading_values ), true ); ?>
								value="<?php echo $key; ?>"><?php echo $text; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			</tbody>
		</table>
		<?php
	}

	add_action( 'noo_job_setting_member_menu', 'jm_member_menu_setting_form' );
endif;

if ( ! function_exists( 'jm_member_menu_correct_settings' ) ) :
	function jm_member_menu_correct_settings() {
		if ( $action_settings = jm_get_setting( 'jm_member_menu' ) ) {
			return;
		}
		$default_settings = array(
			'manage-job' => 'yes',
		);

		update_option( 'jm_member_menu', $default_settings );
	}
endif;

if ( ! function_exists( 'jm_member_menu_employer_items' ) ) :

	function jm_member_menu_employer_items() {
		$menus = array(
			'manage-job'         => __( 'Manage Jobs', 'noo' ),
			'manage-application' => __( 'Manage Applications', 'noo' ),
			'viewed-resume'      => __( 'Viewed Resumes', 'noo' ),
			'company_profile'    => __( 'Company Profile', 'noo' ),
            'resume-suggest'     => __( 'Resume Suggest', 'noo'),
            'resume-alert'       => __( 'Resume Alert','noo'),
		);

		return $menus;
	}

endif;

if ( ! function_exists( 'jm_member_menu_candidate_items' ) ) :

	function jm_member_menu_candidate_items() {
		$menus = array(
			'manage-resume'      => __( 'Manage Resumes', 'noo' ),
			'manage-job-applied' => __( 'Manage Job Applied', 'noo' ),
			'bookmark-job'       => __( 'Job Applied Bookmarked', 'noo' ),
			'job-alert'          => __( 'Manage Jobs Alert', 'noo' ),
			'candidate_profile'  => __( 'My Profile', 'noo' ),
            'job-suggest'        => __( 'Job Suggest','noo'),
            'block-company'      => __( 'Block Companies','noo'),
		);

		return $menus;
	}

endif;

if ( ! function_exists( 'jm_member_menu_other_items' ) ) :

	function jm_member_menu_other_items() {
		$menus = array(
			'manage-plan'   => __( 'Manage Plan', 'noo' ),
			'manage-follow' => __( 'Manage Follow', 'noo' ),
			'job-follow'    => __( 'Job Follow', 'noo' ),
			'shortlist'     => __( 'Shortlist', 'noo' ),
			'signout'       => __( 'Sign Out', 'noo' ),
		);

		return $menus;
	}

endif;